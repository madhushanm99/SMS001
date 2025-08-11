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
        Schema::create('service_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->index();
            $table->string('customer_custom_id')->index();
            $table->string('customer_email')->nullable();
            $table->date('next_service_date')->nullable()->index();
            $table->date('week_start')->index();
            $table->date('week_end')->index();
            $table->unsignedTinyInteger('attempt')->default(1)->comment('1 = initial, 2 = follow-up, 3+ = subsequent');
            $table->enum('status', ['sent', 'skipped', 'fulfilled', 'no_email', 'error'])->default('sent');
            $table->timestamp('email_sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['vehicle_no', 'week_start', 'attempt'], 'uniq_vehicle_week_attempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reminder_logs');
    }
};


