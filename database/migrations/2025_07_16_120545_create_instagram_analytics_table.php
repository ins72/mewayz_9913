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
        Schema::create('instagram_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('instagram_account_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Date for the analytics data
            $table->enum('metric_type', ['account', 'post', 'story', 'reel'])->default('account');
            $table->string('metric_name'); // e.g., 'followers', 'reach', 'impressions'
            $table->bigInteger('metric_value')->default(0);
            $table->decimal('metric_percentage', 8, 2)->default(0.00); // For percentage metrics
            $table->integer('followers_count')->default(0);
            $table->integer('following_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->integer('stories_count')->default(0);
            $table->integer('total_reach')->default(0);
            $table->integer('total_impressions')->default(0);
            $table->integer('total_likes')->default(0);
            $table->integer('total_comments')->default(0);
            $table->integer('total_shares')->default(0);
            $table->integer('total_saves')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0.00);
            $table->integer('profile_visits')->default(0);
            $table->integer('website_clicks')->default(0);
            $table->integer('email_contacts')->default(0);
            $table->integer('phone_calls')->default(0);
            $table->integer('direction_clicks')->default(0);
            $table->json('audience_demographics')->nullable(); // Age, gender, location data
            $table->json('top_posts')->nullable(); // Best performing posts
            $table->json('top_stories')->nullable(); // Best performing stories
            $table->json('hashtag_performance')->nullable(); // Hashtag analytics
            $table->json('optimal_times')->nullable(); // Best times to post
            $table->json('metadata')->nullable(); // Additional analytics data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_analytics');
    }
};