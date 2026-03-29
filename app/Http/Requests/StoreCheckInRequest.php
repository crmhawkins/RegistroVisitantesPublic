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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|string|in:M,F,O',
            'birth_date' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'document_type' => 'required|string|in:DNI,Passport',
            'document_number' => 'required|string|max:50',
            'document_support_number' => 'nullable|string|max:50',
            'exp_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'required|date|after:today',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'payment_method' => 'required|string',
            'signature_data' => 'required|string', // Base64 data
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => __('Debes aceptar las condiciones de uso.'),
            'signature_data.required' => __('La firma es obligatoria.'),
        ];
    }
}
