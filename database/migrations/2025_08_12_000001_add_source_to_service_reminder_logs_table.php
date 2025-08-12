<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_reminder_logs', function (Blueprint $table) {
            $table->enum('source', ['auto', 'manual'])->default('auto')->after('attempt');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('service_reminder_logs', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }
};


