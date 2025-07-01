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
            $table->unsignedBigInteger('service_invoice_id')->nullable()->after('purchase_order_id');
            $table->foreign('service_invoice_id')->references('id')->on('service_invoices')->onDelete('set null');
            $table->index('service_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['service_invoice_id']);
            $table->dropIndex(['service_invoice_id']);
            $table->dropColumn('service_invoice_id');
        });
    }
};
