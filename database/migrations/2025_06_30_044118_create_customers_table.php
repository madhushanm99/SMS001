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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->unique(); // Customer ID like CUST000001
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('nic')->nullable(); // National ID
            $table->string('group_name')->nullable();
            $table->text('address')->nullable();
            $table->decimal('balance_credit', 15, 2)->default(0.00);
            $table->date('last_visit')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->index('custom_id'); // This is what was missing for foreign key
            $table->index(['status', 'name']);
            $table->index('phone');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
