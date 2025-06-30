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
        Schema::create('payment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique(); // Short code for category
            $table->enum('type', ['income', 'expense']); // Cash in or cash out
            $table->unsignedBigInteger('parent_id')->nullable(); // For sub-categories
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('payment_categories')->onDelete('set null');
            $table->index(['type', 'is_active']);
            $table->index('parent_id');
            $table->unique(['name', 'type']); // Prevent duplicate names within same type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_categories');
    }
};
