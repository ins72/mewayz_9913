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
        Schema::create('social_media_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // instagram, facebook, twitter, linkedin, tiktok, youtube
            $table->string('username');
            $table->string('display_name')->nullable();
            $table->text('access_token');
            $table->text('access_token_secret')->nullable(); // For Twitter OAuth 1.0
            $table->string('avatar_url')->nullable();
            $table->integer('followers_count')->nullable();
            $table->integer('following_count')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('connected_at')->nullable();
            $table->json('metadata')->nullable(); // Additional platform-specific data
            $table->timestamps();

            $table->unique(['user_id', 'platform', 'username']);
            $table->index(['user_id', 'platform']);
            $table->index(['user_id', 'is_active']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_accounts');

};