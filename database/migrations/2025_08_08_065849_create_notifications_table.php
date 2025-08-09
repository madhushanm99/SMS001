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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // appointment_created, appointment_confirmed, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data like appointment_id, customer_id, etc.
            $table->boolean('is_read')->default(false);
            $table->string('user_id')->nullable(); // For specific user notifications
            $table->string('created_by')->nullable(); // Who created the notification
            $table->timestamps();

            // Indexes
            $table->index(['is_read']);
            $table->index(['type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
