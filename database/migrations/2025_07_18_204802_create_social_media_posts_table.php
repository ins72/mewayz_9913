<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('platform'); // instagram, facebook, twitter, etc.
            $table->string('post_type')->default('post'); // post, story, reel, etc.
            $table->text('content');
            $table->json('media_urls')->nullable();
            $table->json('hashtags')->nullable();
            $table->string('status')->default('draft'); // draft, scheduled, published, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('platform_post_id')->nullable();
            $table->json('platform_response')->nullable();
            $table->json('analytics')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'platform', 'status']);
            $table->index(['status', 'scheduled_at']);
            $table->index(['platform', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_media_posts');
    }
};
