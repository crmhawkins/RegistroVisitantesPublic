<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrmCallbackService
{
    /**
     * Envía los datos del huésped completados al CRM.
     */
    public function enviarDatos(string $token, array $guests): bool
    {
        $callbackUrl = config('services.crm.callback_url');

        if (empty($callbackUrl)) {
            Log::warning('CrmCallbackService: CRM_CALLBACK_URL no configurada, omitiendo callback.');
            return false;
        }

        try {
            $response = Http::timeout(15)->post($callbackUrl, [
                'token'  => $token,
                'guests' => $guests,
            ]);

            if ($response->successful()) {
                Log::info('CrmCallbackService: Datos enviados correctamente al CRM.');
                return true;
            }

            Log::error('CrmCallbackService: El CRM respondió con error. Status: ' . $response->status() . ' Body: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('CrmCallbackService: Excepción al enviar datos al CRM: ' . $e->getMessage());
            return false;
        }
    }
}
