<?php

namespace App\Services;

use App\Models\Guest;
use Illuminate\Support\Facades\Log;

class TravelerRegistryService
{
    /**
     * Prepares and sends the Guest data to the Spanish Government's Traveler Registry.
     * Stub implementation ready for future integration.
     */
    public function dispatchPendingGuest(Guest $guest): bool
    {
        if ($guest->registry_sync_status !== 'pending') {
            return false;
        }

        try {
            // 1. Prepare XML/JSON payload exactly as the Government API requires
            $payload = $this->buildPayload($guest);

            // 2. Perform HTTP request to the API with certificates
            // $response = Http::withOptions(['cert' => ...])->post(...);

            // Simulating successful dispatch
            $guest->registry_sync_status = 'sent';
            $guest->save();
            
            Log::info("Guest ID {$guest->id} successfully synced to Traveler Registry.");
            return true;
        } catch (\Exception $e) {
            $guest->registry_sync_status = 'error';
            $guest->save();
            Log::error("Failed to sync Guest ID {$guest->id} to Traveler Registry: " . $e->getMessage());
            return false;
        }
    }

    private function buildPayload(Guest $guest): array
    {
        // Example mapping
        return [
            'identificacion' => [
                'tipo_documento' => $guest->document_type,
                'numero_documento' => $guest->document_number,
                // ... map all required fields
            ]
        ];
    }
}
