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
        Schema::create('invoice_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->unique();
            $table->foreignId('sales_invoice_id')->constrained()->onDelete('cascade');
            $table->string('invoice_no');
            $table->string('customer_id');
            $table->date('return_date');
            $table->decimal('total_amount', 10, 2);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('processed_by');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
            
            $table->index(['customer_id', 'return_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_returns');
    }
}; 