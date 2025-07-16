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
        Schema::create('instagram_hashtags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('hashtag'); // Without the # symbol
            $table->string('category')->nullable(); // Custom categorization
            $table->integer('posts_count')->default(0); // Number of posts with this hashtag
            $table->decimal('engagement_rate', 5, 2)->default(0.00); // Average engagement rate
            $table->integer('difficulty_score')->default(0); // Competition difficulty (0-100)
            $table->enum('trend_status', ['rising', 'stable', 'declining'])->default('stable');
            $table->timestamp('last_used_at')->nullable();
            $table->integer('use_count')->default(0); // How many times user has used this hashtag
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_banned')->default(false); // If hashtag is shadowbanned
            $table->json('related_hashtags')->nullable(); // Array of related hashtags
            $table->json('performance_data')->nullable(); // Historical performance metrics
            $table->json('metadata')->nullable(); // Additional hashtag data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_hashtags');
    }
};