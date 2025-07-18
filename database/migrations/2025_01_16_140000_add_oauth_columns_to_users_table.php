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
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('email_verified_at');
        
            if (!Schema::hasColumn('users', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->after('oauth_provider');
        
            // Avatar column already exists, skip adding it
            
            // Add index if it doesn't exist
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('users');
            $indexExists = false;
            foreach ($indexes as $index) {
                if ($index->getColumns() === ['oauth_provider', 'oauth_id']) {
                    $indexExists = true;
                    break;
            
        
            if (!$indexExists) {
                $table->index(['oauth_provider', 'oauth_id']);
        
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['oauth_provider', 'oauth_id']);
            $table->dropColumn(['oauth_provider', 'oauth_id', 'avatar']);
        });

}
};