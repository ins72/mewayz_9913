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
        if (!Schema::hasTable('instagram_posts')) {
            Schema::create('instagram_posts', function (Blueprint $table) {
                $table->id();
                $table->uuid('workspace_id');
                $table->unsignedBigInteger('user_id');
                $table->string('title');
                $table->text('caption');
                $table->json('media_urls'); // Array of image/video URLs
                $table->json('hashtags')->nullable(); // Array of hashtags
                $table->enum('post_type', ['feed', 'story', 'reel'])->default('feed');
                $table->enum('status', ['draft', 'scheduled', 'published', 'failed'])->default('draft');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->string('instagram_post_id')->nullable(); // Instagram API post ID
                $table->json('analytics')->nullable(); // Engagement metrics
                $table->json('metadata')->nullable(); // Additional data
                $table->timestamps();

                $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['workspace_id', 'status']);
                $table->index(['user_id', 'scheduled_at']);
            });



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_posts');

};
