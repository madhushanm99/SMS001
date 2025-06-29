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
        Schema::create('invoice_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_return_id')->constrained()->onDelete('cascade');
            $table->integer('line_no');
            $table->string('item_id');
            $table->string('item_name');
            $table->integer('qty_returned');
            $table->integer('original_qty');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->text('return_reason')->nullable();
            $table->timestamps();
            
            $table->index(['invoice_return_id', 'line_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_return_items');
    }
}; 