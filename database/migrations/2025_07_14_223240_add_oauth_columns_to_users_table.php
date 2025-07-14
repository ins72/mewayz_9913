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
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('email');
            $table->string('provider_id')->nullable()->after('provider');
            $table->string('provider_avatar')->nullable()->after('provider_id');
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->boolean('two_factor_enabled')->default(false)->after('provider_avatar');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->timestamp('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('last_login_at')->nullable()->after('two_factor_recovery_codes');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_id', 
                'provider_avatar',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};
