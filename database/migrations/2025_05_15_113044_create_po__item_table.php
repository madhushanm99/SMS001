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
        Schema::create('po__Item', function (Blueprint $table) {
            $table->smallIncrements('po_Item_Auto_ID');
            $table->smallInteger('po_Auto_ID')->constrained('po','po_Auto_ID')->onDelete('cascade');
            $table->string('po_No')->constrained('po','po_No')->onDelete('cascade');
            $table->smallInteger('list_No');
            $table->string('item_ID');
            $table->smallInteger('qty');
            $table->double('price');
            $table->double('line_Total');
            $table->timestamps();
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po__item');
    }
};
