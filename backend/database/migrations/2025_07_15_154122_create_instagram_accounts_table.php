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
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('instagram_user_id')->nullable(); // Instagram API user ID
            $table->string('profile_picture_url')->nullable();
            $table->text('bio')->nullable();
            $table->integer('followers_count')->default(0);
            $table->integer('following_count')->default(0);
            $table->integer('media_count')->default(0);
            $table->string('access_token')->nullable(); // Instagram API access token
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->json('account_info')->nullable(); // Additional account data
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['workspace_id', 'username']);
            $table->index(['workspace_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');
    }
};
