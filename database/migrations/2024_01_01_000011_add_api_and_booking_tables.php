<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('online_booking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->json('working_hours')->nullable();
            $table->integer('slot_duration')->default(30);
            $table->timestamps();
        });

        Schema::create('online_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_booking_slot_id')->constrained()->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('patient_phone', 20);
            $table->string('patient_national_id', 10)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('start_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_bookings');
        Schema::dropIfExists('online_booking_slots');
        Schema::dropIfExists('personal_access_tokens');
    }
};
