<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('registration_status');
            $table->string('approved_by')->nullable()->after('is_approved');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        // Backfill: mark existing vehicles as approved to avoid blocking operations
        DB::table('vehicles')->update([
            'is_approved' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'approved_by', 'approved_at']);
        });
    }
};
