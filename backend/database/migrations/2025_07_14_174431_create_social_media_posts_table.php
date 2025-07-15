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
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->json('media_urls')->nullable(); // Array of media URLs/Base64
            $table->json('hashtags')->nullable(); // Array of hashtags
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'published', 'failed'])->default('draft');
            $table->enum('post_type', ['text', 'media', 'video'])->default('text');
            $table->json('platform_post_ids')->nullable(); // Platform-specific post IDs
            $table->json('engagement_data')->nullable(); // Likes, comments, shares, etc.
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_posts');
    }
};