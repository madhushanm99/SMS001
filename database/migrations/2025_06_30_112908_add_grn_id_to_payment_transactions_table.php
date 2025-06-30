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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedSmallInteger('grn_id')->nullable()->after('purchase_order_id');
            $table->foreign('grn_id')->references('grn_id')->on('grn')->onDelete('set null');
            $table->index('grn_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['grn_id']);
            $table->dropIndex(['grn_id']);
            $table->dropColumn('grn_id');
        });
    }
};
