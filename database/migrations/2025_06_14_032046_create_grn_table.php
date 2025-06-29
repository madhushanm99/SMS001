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
        Schema::create('grn', function (Blueprint $table) {
            $table->smallIncrements('grn_id');
            $table->string('grn_no')->unique();
            $table->date('grn_date');
            $table->unsignedSmallInteger('po_Auto_ID')->nullable();
            $table->string('po_No')->nullable();
            $table->string('supp_Cus_ID')->nullable();
            $table->foreign('supp_Cus_ID')->references('Supp_CustomID')->on('suppliers')->onDelete('set null');
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('received_by')->nullable();
            $table->string('note')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('grn');
    }
};
