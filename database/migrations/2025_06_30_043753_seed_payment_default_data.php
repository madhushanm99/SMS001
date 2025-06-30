<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default payment methods
        DB::table('payment_methods')->insert([
            [
                'name' => 'Cash',
                'code' => 'CASH',
                'description' => 'Cash payments and receipts',
                'is_active' => true,
                'requires_reference' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'BANK',
                'description' => 'Bank to bank transfers',
                'is_active' => true,
                'requires_reference' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Check',
                'code' => 'CHECK',
                'description' => 'Check payments',
                'is_active' => true,
                'requires_reference' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Credit Card',
                'code' => 'CARD',
                'description' => 'Credit/Debit card payments',
                'is_active' => true,
                'requires_reference' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Digital Wallet',
                'code' => 'WALLET',
                'description' => 'Digital wallet payments (PayPal, etc.)',
                'is_active' => true,
                'requires_reference' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert default payment categories for income (cash_in)
        DB::table('payment_categories')->insert([
            // Income Categories
            [
                'name' => 'Sales Revenue',
                'code' => 'SALES_REV',
                'type' => 'income',
                'parent_id' => null,
                'description' => 'Revenue from sales invoices',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer Payments',
                'code' => 'CUST_PAY',
                'type' => 'income',
                'parent_id' => null,
                'description' => 'Payments received from customers',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other Income',
                'code' => 'OTHER_INC',
                'type' => 'income',
                'parent_id' => null,
                'description' => 'Miscellaneous income',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Interest Income',
                'code' => 'INT_INC',
                'type' => 'income',
                'parent_id' => null,
                'description' => 'Interest earned on bank accounts',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Expense Categories
            [
                'name' => 'Supplier Payments',
                'code' => 'SUPP_PAY',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Payments made to suppliers',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operating Expenses',
                'code' => 'OP_EXP',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Day-to-day operating expenses',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salary & Wages',
                'code' => 'SALARY',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Employee salaries and wages',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilities',
                'code' => 'UTILITIES',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Electricity, water, internet, phone bills',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rent & Lease',
                'code' => 'RENT',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Office rent and equipment lease',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transportation',
                'code' => 'TRANSPORT',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Vehicle fuel, maintenance, transport costs',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Supplies',
                'code' => 'OFFICE_SUP',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Stationery, office equipment, supplies',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Services',
                'code' => 'PROF_SERV',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Legal, accounting, consulting fees',
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing & Advertising',
                'code' => 'MARKETING',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Marketing and advertising expenses',
                'is_active' => true,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Insurance',
                'code' => 'INSURANCE',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Business insurance premiums',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Charges',
                'code' => 'BANK_CHG',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Bank fees and charges',
                'is_active' => true,
                'sort_order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other Expenses',
                'code' => 'OTHER_EXP',
                'type' => 'expense',
                'parent_id' => null,
                'description' => 'Miscellaneous expenses',
                'is_active' => true,
                'sort_order' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove seeded data
        DB::table('payment_categories')->whereIn('code', [
            'SALES_REV', 'CUST_PAY', 'OTHER_INC', 'INT_INC',
            'SUPP_PAY', 'OP_EXP', 'SALARY', 'UTILITIES', 'RENT', 
            'TRANSPORT', 'OFFICE_SUP', 'PROF_SERV', 'MARKETING', 
            'INSURANCE', 'BANK_CHG', 'OTHER_EXP'
        ])->delete();
        
        DB::table('payment_methods')->whereIn('code', [
            'CASH', 'BANK', 'CHECK', 'CARD', 'WALLET'
        ])->delete();
    }
};
