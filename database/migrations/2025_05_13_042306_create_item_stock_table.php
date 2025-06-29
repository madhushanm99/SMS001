<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. ID_Auto, Item_ID, Cost, Qty, reorder_Lvl, reorder_Qty, created_at, updated_at, status
     */
    public function up(): void
    {
        Schema::create('item_stock', function (Blueprint $table) {
            $table->smallIncrements('iD_Auto');
            $table->string('item_ID')->unique();
            $table->decimal('sales_Price', 10, 2);
            $table->integer('qty');
            $table->integer('reorder_Lvl');
            $table->integer('reorder_Qty');
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
        Schema::dropIfExists('item_stock');
    }
};
