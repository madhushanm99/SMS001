<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. ID_Auto, location_Name, Description, created_at, updated_at, status
     */
    public function up(): void
    {
        Schema::create('item_location', function (Blueprint $table) {
            $table->smallIncrements('iD_Auto');
            $table->string('location_Name');
            $table->string('description');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_location');
    }
};
