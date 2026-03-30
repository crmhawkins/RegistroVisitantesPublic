<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCheckInRequest;
use App\Models\Guest;
use App\Services\DocumentExtractionService;
use App\Services\TravelerRegistryService;
use App\Services\CrmCallbackService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CheckInController extends Controller
{
    protected $extractionService;
    protected $registryService;
    protected $crmCallbackService;

    public function __construct(DocumentExtractionService $extractionService, TravelerRegistryService $registryService, CrmCallbackService $crmCallbackService)
    {
        $this->extractionService = $extractionService;
        $this->registryService = $registryService;
        $this->crmCallbackService = $crmCallbackService;
    }

    public function index(Request $request)
    {
        $reservationData = null;
        $tokenError = null;

        if ($request->has('token')) {
            try {
                $reservationData = $this->validateAndDecodeToken($request->get('token'));
                session([
                    'reservation_data'  => $reservationData,
                    'checkin_token'     => $request->get('token'),
                ]);
            } catch (\Exception $e) {
                $tokenError = $e->getMessage();
                Log::warning('CheckInController: Token inválido recibido: ' . $e->getMessage());
            }
        }

        return view('checkin.step1', compact('reservationData', 'tokenError'));
    }

    public function processImages(Request $request)
    {
        $request->validate([
            'dni_front' => 'required|file',
            'dni_back' => 'nullable|file',
        ]);

        $frontPath = $request->file('dni_front')->store('private/dni_uploads');
        $backPath = $request->hasFile('dni_back') 
            ? $request->file('dni_back')->store('private/dni_uploads') 
            : null;

        // Process with AI Service
        $extractedData = $this->extractionService->extract($frontPath, $backPath);

        // Store paths in session to use them later in step 2
        session(['dni_front_path' => $frontPath, 'dni_back_path' => $backPath, 'ai_status' => $extractedData['status'] ?? 'not_processed']);

        return response()->json([
            'success' => true,
            'data' => $extractedData['data'] ?? [],
        ]);
    }

    public function form(Request $request)
    {
        if (!session()->has('dni_front_path')) {
            return redirect()->route('checkin.step1')->with('error', __('Por favor sube tu documento primero.'));
        }

        $reservationData = session('reservation_data', []);

        return view('checkin.step2', compact('reservationData'));
    }

    public function store(StoreCheckInRequest $request)
    {
        // Validation handled by StoreCheckInRequest
        $bookingData = $request->only(['check_in_date', 'check_out_date', 'payment_method']);
        $signatureFileName = null;

        // Handle Signature Base64 image globally for all guests
        if ($request->filled('signature_data')) {
            $imageParts = explode(";base64,", $request->signature_data);
            if (count($imageParts) == 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1];
                $imageBase64 = base64_decode($imageParts[1]);
                $signatureFileName = 'private/signatures/' . uniqid() . '.' . $imageType;
                
                Storage::put($signatureFileName, $imageBase64);
            }
        }

        $guestsData = $request->input('guests', []);

        foreach ($guestsData as $index => $guestData) {
            $guest = new Guest();
            $guest->fill($guestData);

            // Set shared booking details
            $guest->check_in_date = $bookingData['check_in_date'];
            $guest->check_out_date = $bookingData['check_out_date'];
            $guest->payment_method = $bookingData['payment_method'];

            $guest->signature_path = $signatureFileName;

            // Only the first guest gets the DNI image and AI processed status natively from step 1
            if ($index === 0) {
                $guest->dni_front_path = session('dni_front_path');
                $guest->dni_back_path = session('dni_back_path');
                $guest->ai_processed_status = session('ai_status', 'not_processed');
            } else {
                $guest->dni_front_path = null;
                $guest->dni_back_path = null;
                $guest->ai_processed_status = 'not_processed';
            }

            $guest->registry_sync_status = 'pending';
            $guest->save();
        }

        // Optional: Trigger traveler registry dispatch here or in an event/observer
        // $this->registryService->dispatchPendingGuest($guest);

        // Callback al CRM si la sesión tiene token
        $checkinToken = session('checkin_token');
        if ($checkinToken) {
            $guestsPayload = $request->input('guests', []);
            // Enviar de forma no bloqueante — si falla, el registro del huésped ya fue guardado
            $this->crmCallbackService->enviarDatos($checkinToken, $guestsPayload);
        }

        // Clear session data
        session()->forget(['dni_front_path', 'dni_back_path', 'ai_status', 'reservation_data', 'checkin_token']);
        // Flash flag so the success page guard works
        session()->flash('checkin_complete', true);

        return redirect()->route('checkin.success');
    }

    public function success()
    {
        if (!session()->has('checkin_complete')) {
            return redirect()->route('checkin.step1');
        }
        return view('checkin.success');
    }

    private function validateAndDecodeToken(string $token): array
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            throw new \Exception(__('Enlace de reserva malformado.'));
        }

        [$encoded, $signature] = $parts;
        $secret = config('services.crm.checkin_secret');

        if (empty($secret)) {
            throw new \Exception(__('Configuración de integración incompleta.'));
        }

        $expected = hash_hmac('sha256', $encoded, $secret);
        if (!hash_equals($expected, $signature)) {
            throw new \Exception(__('El enlace de reserva no es válido.'));
        }

        $payload = json_decode(base64_decode($encoded), true);
        if (!is_array($payload)) {
            throw new \Exception(__('El enlace de reserva no se puede leer.'));
        }

        if (($payload['exp'] ?? 0) < time()) {
            throw new \Exception(__('El enlace de reserva ha caducado. Contacte con el establecimiento.'));
        }

        return $payload;
    }

    public function setLocale($locale)
    {
        if (in_array($locale, ['es', 'en'])) {
            session(['locale' => $locale]);
        }
        return back();
    }
}
