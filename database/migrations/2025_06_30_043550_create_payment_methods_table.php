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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Cash, Bank Transfer, Check, Credit Card, etc.
            $table->string('code', 10)->unique(); // CASH, BANK, CHECK, CARD, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_reference')->default(false); // For checks, bank transfers
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
