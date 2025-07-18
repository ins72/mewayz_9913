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
            $table->uuid('workspace_id');
            $table->string('hashtag');
            $table->integer('post_count')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0.00);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_trending')->default(false);
            $table->json('related_hashtags')->nullable();
            $table->json('analytics')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->unique(['workspace_id', 'hashtag']);
            $table->index(['workspace_id', 'is_trending']);
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
