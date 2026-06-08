<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->date('visit_date');
            $table->enum('visit_type', ['first_visit', 'follow_up', 'lab_review', 'consultation', 'emergency'])
                  ->default('follow_up');

            $table->string('chief_complaint')->nullable();   // شکایت اصلی
            $table->text('diagnosis')->nullable();            // تشخیص
            $table->text('treatment')->nullable();            // درمان
            $table->text('prescriptions')->nullable();        // نسخه
            $table->json('lab_results')->nullable();          // نتایج آزمایش
            $table->json('vital_signs')->nullable();          // علائم حیاتی
            $table->text('doctor_notes')->nullable();
            $table->date('follow_up_date')->nullable();

            $table->timestamps();

            $table->index(['patient_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};