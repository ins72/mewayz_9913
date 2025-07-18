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
        Schema::create('instagram_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 255)->unique();
            $table->string('display_name', 255)->nullable();
            $table->text('bio')->nullable();
            $table->bigInteger('follower_count')->default(0);
            $table->bigInteger('following_count')->default(0);
            $table->integer('post_count')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->string('location', 255)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->text('profile_image_url')->nullable();
            $table->boolean('is_business_account')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('language', 10)->default('en');
            $table->timestamp('last_scraped')->nullable();
            $table->uuid('workspace_id')->nullable();
            $table->timestamps();

            $table->index(['username', 'workspace_id']);
            $table->index(['follower_count', 'engagement_rate']);
            $table->index(['category', 'is_business_account']);
            $table->index(['location', 'is_verified']);
            $table->index(['last_scraped', 'workspace_id']);
            $table->fullText(['username', 'display_name', 'bio']);

            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_profiles');

}
};