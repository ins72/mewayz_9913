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
        if (!Schema::hasTable('workspaces')) {
            Schema::create('workspaces', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->json('settings')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'is_primary']);
            });
        } else {
            // If workspaces table already exists, add missing columns
            Schema::table('workspaces', function (Blueprint $table) {
                if (!Schema::hasColumn('workspaces', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
            
                if (!Schema::hasColumn('workspaces', 'is_primary')) {
                    $table->boolean('is_primary')->default(false)->after('description');
            
                
                // Add foreign key if it doesn't exist
                if (!$this->foreignKeyExists('workspaces', 'workspaces_user_id_foreign')) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
                
                // Add index if it doesn't exist
                if (!$this->indexExists('workspaces', 'workspaces_user_id_is_primary_index')) {
                    $table->index(['user_id', 'is_primary']);
            
            });
    

    
    private function foreignKeyExists($table, $constraintName)
    {
        $constraints = \DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?", [$table, $constraintName]);
        return !empty($constraints);

    
    private function indexExists($table, $indexName)
    {
        $indexes = \DB::select("SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?", [$table, $indexName]);
        return !empty($indexes);


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');

}
};
