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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name'); // Account holder name or company name
            $table->string('account_number')->unique();
            $table->string('bank_name');
            $table->string('bank_branch')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->enum('account_type', ['checking', 'savings', 'current', 'business'])->default('checking');
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->date('opening_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('currency', 3)->default('LKR'); // Assuming Sri Lankan Rupees
            $table->timestamps();
            
            $table->index(['is_active', 'account_type']);
            $table->index('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
