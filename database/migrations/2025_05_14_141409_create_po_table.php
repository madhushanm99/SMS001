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
        Schema::create('po', function (Blueprint $table) {
            $table->smallIncrements('po_Auto_ID');
            $table->string('po_No')->unique();
            $table->date('po_date');
            $table->string('supp_Cus_ID');
            $table->foreign('supp_Cus_ID')->references('Supp_CustomID')->on('suppliers')->onDelete('cascade');
            $table->double('grand_Total');
            $table->string('note')->nullable();
            $table->string('Reff_No')->nullable();
            $table->string('emp_Name')->nullable();
            $table->enum('orderStatus', ['draft', 'pending', 'approved', 'received', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po');
    }
};
