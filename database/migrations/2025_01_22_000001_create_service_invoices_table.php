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
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('customer_id'); // Links to customers.custom_id
            $table->string('vehicle_no')->nullable(); // Links to vehicles.vehicle_no
            $table->integer('mileage')->nullable(); // Current vehicle mileage
            $table->date('invoice_date');
            $table->decimal('job_total', 10, 2)->default(0.00);
            $table->decimal('parts_total', 10, 2)->default(0.00);
            $table->decimal('grand_total', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->enum('status', ['hold', 'finalized'])->default('hold');
            $table->string('created_by');
            $table->timestamps();
            
            $table->index(['customer_id', 'invoice_date']);
            $table->index(['vehicle_no', 'invoice_date']);
            $table->index('status');
            $table->index('invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_invoices');
    }
}; 