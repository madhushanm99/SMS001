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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_no')->unique();
            $table->string('customer_id'); // References customers.custom_id
            $table->string('vehicle_no');
            $table->enum('service_type', ['NS', 'FS'])->comment('NS = Normal Service, FS = Full Service');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->string('handled_by')->nullable(); // Staff member who handled the appointment
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['customer_id']);
            $table->index(['appointment_date', 'appointment_time']);
            $table->index(['status']);
            $table->unique(['appointment_date', 'appointment_time'], 'unique_datetime_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
