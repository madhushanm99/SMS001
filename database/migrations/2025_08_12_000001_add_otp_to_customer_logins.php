<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_logins', function (Blueprint $table) {
            $table->string('email_verification_otp')->nullable();
            $table->timestamp('email_verification_otp_expires_at')->nullable();

            $table->string('login_otp')->nullable();
            $table->timestamp('login_otp_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_logins', function (Blueprint $table) {
            $table->dropColumn([
                'email_verification_otp',
                'email_verification_otp_expires_at',
                'login_otp',
                'login_otp_expires_at',
            ]);
        });
    }
};


