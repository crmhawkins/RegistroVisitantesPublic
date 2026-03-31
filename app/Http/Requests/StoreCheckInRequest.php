<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guests' => 'required|array|min:1|max:10',
            'guests.*.first_name' => 'required|string|max:100',
            'guests.*.last_name' => 'required|string|max:100',
            // El parentesco es obligatorio para los huéspedes extra.
            // Una vez que pase la validación del front, aseguramos que el segundo y posteriores lo tengan.
            'guests.*.relationship' => 'nullable|string|max:100',
            'guests.*.gender' => 'required|string|in:M,F,O',
            'guests.*.birth_date' => 'required|date|before:today',
            'guests.*.nationality' => 'required|string|max:100',
            'guests.*.document_type' => 'required|string|in:DNI,Passport',
            'guests.*.document_number' => 'required|string|max:50',
            'guests.*.document_support_number' => 'nullable|string|max:50',
            'guests.*.exp_date' => 'nullable|date|before_or_equal:today',
            'guests.*.expiry_date' => 'required|date|after:today',
            'guests.*.address' => 'required|string|max:255',
            'guests.*.postal_code' => 'required|string|max:20',
            'guests.*.city' => 'required|string|max:100',
            'guests.*.country' => 'required|string|max:100',
            'guests.*.phone' => 'nullable|string|max:30',
            'guests.*.email' => 'nullable|email|max:100',
            
            // Shared booking fields globally outside array
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'payment_method' => 'required|string|in:Tarjeta,Efectivo,Transferencia',
            'signature_data' => 'required|string', // Base64 data
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $guests = $this->input('guests', []);
            if (is_array($guests)) {
                foreach ($guests as $index => $guest) {
                    if ($index > 0 && empty($guest['relationship'])) {
                        $validator->errors()->add("guests.{$index}.relationship", __('El parentesco es obligatorio para los huéspedes adicionales.'));
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => __('Debes aceptar las condiciones de uso.'),
            'signature_data.required' => __('La firma es obligatoria.'),
        ];
    }
}
