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
        if (!Schema::hasTable('instagram_accounts')) {
            Schema::create('instagram_accounts', function (Blueprint $table) {
                $table->id();
                $table->uuid('workspace_id');
                $table->unsignedBigInteger('user_id');
                $table->string('username');
                $table->string('instagram_user_id')->nullable();
                $table->string('profile_picture_url')->nullable();
                $table->text('bio')->nullable();
                $table->integer('followers_count')->default(0);
                $table->integer('following_count')->default(0);
                $table->integer('media_count')->default(0);
                $table->string('access_token')->nullable();
                $table->timestamp('token_expires_at')->nullable();
                $table->boolean('is_connected')->default(false);
                $table->boolean('is_primary')->default(false);
                $table->json('account_info')->nullable();
                $table->timestamps();
                
                $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['workspace_id', 'is_connected']);
                $table->index(['user_id', 'is_primary']);
            });
    


    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');

};
