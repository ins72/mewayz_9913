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
        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('instagram_account_id')->after('user_id')->nullable();
            $table->foreign('instagram_account_id')->references('id')->on('instagram_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->dropForeign(['instagram_account_id']);
            $table->dropColumn('instagram_account_id');
        });
    }
};
