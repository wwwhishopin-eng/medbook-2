<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Subscription plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();
            $table->integer('price_monthly')->default(0);
            $table->integer('price_yearly')->default(0);
            $table->json('features')->nullable();
            $table->integer('max_patients')->nullable();
            $table->integer('max_appointments_per_day')->nullable();
            $table->integer('max_users')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default plans
        DB::table('subscription_plans')->insert([
            [
                'name' => 'پایه',
                'slug' => 'basic',
                'description' => 'پلن مناسب برای مطب‌های کوچک',
                'price_monthly' => 150000,
                'price_yearly' => 1500000,
                'features' => json_encode(['sms_limit' => 100, 'voice_calls' => false, 'custom_reports' => false]),
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'حرفه‌ای',
                'slug' => 'professional',
                'description' => 'پلن پیشنهادی برای مطب‌ها',
                'price_monthly' => 350000,
                'price_yearly' => 3500000,
                'features' => json_encode(['sms_limit' => 500, 'voice_calls' => true, 'custom_reports' => true]),
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سازمانی',
                'slug' => 'enterprise',
                'description' => 'پلن کامل با قابلیت‌های پیشرفته',
                'price_monthly' => 750000,
                'price_yearly' => 7500000,
                'features' => json_encode(['sms_limit' => -1, 'voice_calls' => true, 'custom_reports' => true, 'api_access' => true]),
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->enum('status', ['pending', 'active', 'trial', 'expired', 'cancelled', 'suspended'])->default('pending');
            $table->string('license_key', 64)->unique();
            $table->date('trial_ends_at')->nullable();
            $table->date('starts_at');
            $table->date('expires_at');
            $table->date('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('license_key');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
