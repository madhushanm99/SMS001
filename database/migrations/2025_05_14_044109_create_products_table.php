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
        Schema::create('products', function (Blueprint $table) {
            $table->smallIncrements('item_ID_Auto');
            $table->string('item_ID')->unique();
            $table->string('item_Name');
            $table->string('product_Type');
            $table->string('catagory_Name');
            $table->decimal('sales_Price', 15, 2);
            $table->double('units');
            $table->string('unitofMeture');
            $table->string('location');
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
        Schema::dropIfExists('products');
    }
};
