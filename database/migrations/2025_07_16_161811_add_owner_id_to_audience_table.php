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
        Schema::table('audience', function (Blueprint $table) {
            if (!Schema::hasColumn('audience', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->after('user_id')->default(0);
        
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audience', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });

};
