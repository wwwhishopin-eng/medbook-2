<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_history_id')->nullable()->constrained('medical_histories')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->date('prescribed_at');
            $table->json('medications');        // array of {name, dose, frequency}
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
            $table->unsignedTinyInteger('refills_allowed')->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['patient_id', 'prescribed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};