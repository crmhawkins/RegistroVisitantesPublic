<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DocumentExtractionService
{
    /**
     * Extracts info from document images.
     * Currently implemented as a Mock Service.
     */
    public function extract($frontImagePath, $backImagePath = null): array
    {
        $isMockMode = config('services.mock_ai_extraction', false);

        if (!$isMockMode) {
            // TODO: Connect to real local AI container API
            Log::info("Real AI Extraction not yet implemented.");
            return ['status' => 'not_processed', 'data' => []];
        }

        // Simulate some delay for realism
        sleep(2);

        // Randomly succeed or fail for testing the fallback gracefully
        $success = rand(1, 100) > 20; // 80% success rate

        if (!$success) {
            return ['status' => 'failed', 'data' => []];
        }

        // Mock Data
        return [
            'status' => 'success',
            'data' => [
                'first_name' => 'GARCIA',
                'last_name' => 'MARTA',
                'gender' => 'F',
                'birth_date' => '1985-05-15',
                'nationality' => 'ESP',
                'document_type' => 'DNI',
                'document_number' => '12345678Z',
                'document_support_number' => 'BAA123456',
                'exp_date' => '2015-05-15',
                'expiry_date' => '2025-05-15',
                'address' => 'CALLE MAYOR 1',
                'postal_code' => '28001',
                'city' => 'MADRID',
                'country' => 'ESP',
            ]
        ];
    }
}
