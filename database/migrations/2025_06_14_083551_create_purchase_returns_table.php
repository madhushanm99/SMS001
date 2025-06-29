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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->unique(); // e.g., PR000001
            $table->unsignedSmallInteger('grn_id');
            $table->string('grn_no');
            $table->string('supp_Cus_ID');
            $table->foreign('supp_Cus_ID')->references('Supp_CustomID')->on('suppliers')->onDelete('cascade');
            $table->text('note')->nullable(); // General reason / note
            $table->string('returned_by'); // User who performed the return
            $table->boolean('status')->default(true); // Soft delete
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
