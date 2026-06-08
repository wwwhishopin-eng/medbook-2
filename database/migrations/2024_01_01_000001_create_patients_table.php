<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Personal info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id', 10)->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');

            // Contact
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Medical
            $table->string('blood_type', 5)->nullable();
            $table->json('conditions')->nullable();   // chronic conditions
            $table->json('allergies')->nullable();

            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();

            // App
            $table->enum('status', ['active', 'pending', 'recovered', 'inactive'])
                  ->default('pending')
                  ->index();
            $table->text('notes')->nullable();
            $table->string('avatar_color', 10)->default('#2E5BFF');

            $table->softDeletes();
            $table->timestamps();

            // Indexes for search performance
            $table->index(['first_name', 'last_name']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};