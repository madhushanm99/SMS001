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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no')->unique(); // Auto-generated transaction number
            $table->enum('type', ['cash_in', 'cash_out']); // Transaction direction
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->datetime('transaction_time')->useCurrent();
            $table->text('description');
            $table->string('reference_no')->nullable(); // Check number, transfer reference, etc.
            
            // Payment method and bank account relationships
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('bank_account_id')->nullable(); // For non-cash transactions
            $table->unsignedBigInteger('payment_category_id');
            
            // Related entity relationships (nullable - for standalone transactions)
            $table->string('customer_id')->nullable(); // Link to customers
            $table->string('supplier_id')->nullable(); // Link to suppliers (Supp_CustomID)
            $table->unsignedBigInteger('sales_invoice_id')->nullable(); // Link to sales invoices
            $table->unsignedSmallInteger('purchase_order_id')->nullable(); // Link to PO (po_Auto_ID)
            
            // Transaction status and workflow
            $table->enum('status', ['draft', 'pending', 'approved', 'completed', 'cancelled'])->default('completed');
            $table->string('approved_by')->nullable(); // User who approved
            $table->datetime('approved_at')->nullable();
            
            // Additional tracking
            $table->string('created_by'); // User who created the transaction
            $table->string('updated_by')->nullable(); // User who last updated
            $table->text('notes')->nullable(); // Internal notes
            $table->json('attachments')->nullable(); // File attachments (receipts, etc.)
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
            $table->foreign('payment_category_id')->references('id')->on('payment_categories');
            $table->foreign('customer_id')->references('custom_id')->on('customers')->onDelete('set null');
            $table->foreign('supplier_id')->references('Supp_CustomID')->on('suppliers')->onDelete('set null');
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('set null');
            $table->foreign('purchase_order_id')->references('po_Auto_ID')->on('po')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['type', 'transaction_date']);
            $table->index(['status', 'transaction_date']);
            $table->index('customer_id');
            $table->index('supplier_id');
            $table->index('sales_invoice_id');
            $table->index('purchase_order_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
