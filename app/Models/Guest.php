<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'relationship',
        'gender',
        'birth_date',
        'nationality',
        'document_type',
        'document_number',
        'document_support_number',
        'exp_date',
        'expiry_date',
        'address',
        'postal_code',
        'city',
        'country',
        'phone',
        'email',
        'check_in_date',
        'check_out_date',
        'payment_method',
        'dni_front_path',
        'dni_back_path',
        'signature_path',
        'ai_processed_status',
        'registry_sync_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'exp_date' => 'date',
            'expiry_date' => 'date',
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            // Encrypted fields for data protection
            'document_number' => 'encrypted',
            'document_support_number' => 'encrypted',
            'relationship' => 'encrypted',
            'phone' => 'encrypted',
            'email' => 'encrypted',
            'address' => 'encrypted',
        ];
    }
}
