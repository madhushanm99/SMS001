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
        Schema::create('service_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_invoice_id');
            $table->integer('line_no');
            $table->enum('item_type', ['job', 'spare']); // job = job type, spare = spare part
            $table->string('item_id'); // Links to job_types.jobCustomID or item.item_ID
            $table->string('item_name');
            $table->integer('qty');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
            
            $table->foreign('service_invoice_id')->references('id')->on('service_invoices')->onDelete('cascade');
            $table->index(['service_invoice_id', 'line_no']);
            $table->index(['item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_invoice_items');
    }
}; 