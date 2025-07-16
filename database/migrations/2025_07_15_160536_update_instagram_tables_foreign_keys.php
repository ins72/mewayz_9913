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
        // Skip foreign key updates for SQLite
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        
        Schema::table('instagram_accounts', function (Blueprint $table) {
            // Drop existing foreign key and add new one pointing to organizations table
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('organizations')->onDelete('cascade');
        });
        
        Schema::table('instagram_posts', function (Blueprint $table) {
            // Drop existing foreign key and add new one pointing to organizations table
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('organizations')->onDelete('cascade');
        });
        
        Schema::table('instagram_hashtags', function (Blueprint $table) {
            // Drop existing foreign key and add new one pointing to organizations table
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instagram_accounts', function (Blueprint $table) {
            // Restore original foreign key
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
        
        Schema::table('instagram_posts', function (Blueprint $table) {
            // Restore original foreign key
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
        
        Schema::table('instagram_hashtags', function (Blueprint $table) {
            // Restore original foreign key
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }
};
