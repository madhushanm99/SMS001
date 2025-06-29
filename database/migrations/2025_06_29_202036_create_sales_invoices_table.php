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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('customer_id');
            $table->date('invoice_date');
            $table->decimal('grand_total', 10, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['hold', 'finalized'])->default('hold');
            $table->string('created_by');
            $table->timestamps();
            
            $table->index(['customer_id', 'invoice_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
