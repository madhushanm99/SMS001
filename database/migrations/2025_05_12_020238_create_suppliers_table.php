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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->integerIncrements('Supp_ID')->primary();
            $table->string('Supp_CustomID')->unique();
            $table->text('Supp_Name')->nullable();
            $table->text('Company_Name')->nullable();
            $table->text('Phone')->nullable();
            $table->text('Fax')->nullable();
            $table->text('Email')->nullable();
            $table->text('Web')->nullable();
            $table->text('Address1')->nullable();
            $table->text('Supp_Group_Name')->nullable();
            $table->text('Remark')->nullable();
            $table->text('Last_GRN')->nullable();
            $table->double('Total_Orders')->default(0);
            $table->double('Total_Spent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
