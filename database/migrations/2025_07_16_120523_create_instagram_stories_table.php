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
        Schema::create('instagram_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('instagram_account_id')->constrained()->onDelete('cascade');
            $table->string('instagram_story_id')->nullable(); // Set after posting
            $table->text('content')->nullable(); // Story text content
            $table->string('media_url'); // Image or video URL
            $table->enum('media_type', ['photo', 'video'])->default('photo');
            $table->enum('status', ['draft', 'scheduled', 'published', 'expired', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Stories expire after 24 hours
            $table->text('failure_reason')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('exits_count')->default(0);
            $table->integer('taps_forward')->default(0);
            $table->integer('taps_back')->default(0);
            $table->json('interactive_elements')->nullable(); // Polls, questions, stickers
            $table->json('engagement_data')->nullable(); // Detailed engagement metrics
            $table->json('location_data')->nullable(); // Location tagging
            $table->json('user_tags')->nullable(); // Tagged users
            $table->json('metadata')->nullable(); // Additional story data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_stories');
    }
};