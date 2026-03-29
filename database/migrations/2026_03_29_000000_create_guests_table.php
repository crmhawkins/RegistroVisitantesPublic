<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            
            // Document Information
            $table->string('document_type')->nullable(); // DNI, Passport
            $table->text('document_number')->nullable(); // Encrypted
            $table->text('document_support_number')->nullable(); // Encrypted
            $table->date('exp_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Address Information
            $table->text('address')->nullable(); // Encrypted
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            
            // Contact Information
            $table->text('phone')->nullable(); // Encrypted
            $table->text('email')->nullable(); // Encrypted
            
            // Booking & Payment
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->string('payment_method')->default('Tarjeta');
            
            // Files / Storage paths (stored securely)
            $table->string('dni_front_path')->nullable();
            $table->string('dni_back_path')->nullable();
            $table->string('signature_path')->nullable();
            
            // Integration States
            $table->string('ai_processed_status')->default('not_processed'); // success, failed, not_processed
            $table->string('registry_sync_status')->default('pending'); // pending, prepared, sent, error
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
