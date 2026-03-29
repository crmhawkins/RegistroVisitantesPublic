<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCheckInRequest;
use App\Models\Guest;
use App\Services\DocumentExtractionService;
use App\Services\TravelerRegistryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class CheckInController extends Controller
{
    protected $extractionService;
    protected $registryService;

    public function __construct(DocumentExtractionService $extractionService, TravelerRegistryService $registryService)
    {
        $this->extractionService = $extractionService;
        $this->registryService = $registryService;
    }

    public function index()
    {
        return view('checkin.step1');
    }

    public function processImages(Request $request)
    {
        $request->validate([
            'dni_front' => 'required|image|mimes:jpeg,png,jpg|max:10240', // 10MB max
            'dni_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
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

        // We receive the data passed from JS (extracted) or blank if failed
        // The view will populate the fields or leave them blank
        return view('checkin.step2');
    }

    public function store(StoreCheckInRequest $request)
    {
        // Validation handled by StoreCheckInRequest

        $guest = new Guest($request->validated());
        
        // Handle Signature Base64 image
        if ($request->filled('signature_data')) {
            $imageParts = explode(";base64,", $request->signature_data);
            if (count($imageParts) == 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1];
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName = 'private/signatures/' . uniqid() . '.' . $imageType;
                
                Storage::put($fileName, $imageBase64);
                $guest->signature_path = $fileName;
            }
        }

        // Restore DNI paths from session
        $guest->dni_front_path = session('dni_front_path');
        $guest->dni_back_path = session('dni_back_path');
        $guest->ai_processed_status = session('ai_status', 'not_processed');
        $guest->registry_sync_status = 'pending';

        $guest->save();

        // Optional: Trigger traveler registry dispatch here or in an event/observer
        // $this->registryService->dispatchPendingGuest($guest);

        // Clear session data
        session()->forget(['dni_front_path', 'dni_back_path', 'ai_status']);

        return view('checkin.success');
    }

    public function setLocale($locale)
    {
        if (in_array($locale, ['es', 'en'])) {
            session(['locale' => $locale]);
        }
        return back();
    }
}
