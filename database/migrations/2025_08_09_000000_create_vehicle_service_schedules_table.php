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
        Schema::create('vehicle_service_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->index();
            $table->unsignedBigInteger('last_service_invoice_id')->nullable()->index();
            $table->date('last_service_date')->nullable();
            $table->integer('last_mileage')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_mileage')->nullable();
            $table->integer('days_until_next')->nullable();
            $table->string('last_service_type', 2)->nullable()->comment('NS/FS');
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique('vehicle_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_service_schedules');
    }
};


