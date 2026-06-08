<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();

            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])
                  ->default('scheduled')
                  ->index();

            $table->enum('type', ['checkup', 'follow_up', 'lab', 'consultation', 'emergency'])
                  ->default('checkup');

            $table->text('notes')->nullable();
            $table->boolean('reminder_sent')->default(false);

            $table->timestamps();

            $table->index(['patient_id', 'start_at']);
            $table->index('start_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};