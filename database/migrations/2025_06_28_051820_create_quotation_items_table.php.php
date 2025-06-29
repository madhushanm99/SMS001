<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->unsignedInteger('line_no');

            $table->string('item_type'); // 'spare' or 'job'
            $table->string('item_id');   // refers to either item.item_ID or job_types.jobCustomID

            $table->string('description')->nullable(); // optional item/job name
            $table->integer('qty')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('line_total', 10, 2);

            $table->timestamps();
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
}

