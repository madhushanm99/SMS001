<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->bigIncrements('grn_item_id');
            $table->unsignedSmallInteger('grn_id');
            $table->foreign('grn_id')->references('grn_id')->on('grn')->onDelete('cascade');
            $table->string('item_ID');
            $table->string('item_Name');
            $table->integer('qty_received');
            $table->double('price');
            $table->double('line_total');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
