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
        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('instagram_account_id')->constrained()->onDelete('cascade');
            $table->string('instagram_post_id')->nullable(); // Set after posting
            $table->text('caption');
            $table->json('media_urls'); // Array of image/video URLs
            $table->json('hashtags')->nullable(); // Array of hashtags
            $table->enum('post_type', ['photo', 'video', 'carousel', 'reel'])->default('photo');
            $table->enum('status', ['draft', 'scheduled', 'published', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('saves_count')->default(0);
            $table->integer('reach')->default(0);
            $table->integer('impressions')->default(0);
            $table->json('engagement_data')->nullable(); // Detailed engagement metrics
            $table->json('location_data')->nullable(); // Location tagging
            $table->json('user_tags')->nullable(); // Tagged users
            $table->json('metadata')->nullable(); // Additional post data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_posts');
    }
};