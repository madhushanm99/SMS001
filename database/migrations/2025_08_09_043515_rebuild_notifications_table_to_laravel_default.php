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
        // If the table already exists with a custom schema, drop it and recreate
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            // Default Laravel notifications table schema
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // notifiable_type, notifiable_id
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');

        // Optionally, recreate the previous custom schema
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('user_id')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->index(['is_read']);
            $table->index(['type']);
            $table->index(['created_at']);
        });
    }
};
