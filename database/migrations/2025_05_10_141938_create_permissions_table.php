<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('usertype')->unique(); // e.g., "admin", "user"
            $table->boolean('suppliers')->default(false);
            $table->boolean('products')->default(false);
            $table->boolean('purchaseOrder')->default(false);
            $table->boolean('recevingGRN')->default(false);
            $table->boolean('purchaseReturn')->default(false);
            // Add other actions as columns (e.g., create_posts, etc.)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}

