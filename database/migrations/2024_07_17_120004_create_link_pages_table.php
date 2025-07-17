<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('link_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('custom_domain')->nullable();
            $table->json('theme_settings')->nullable(); // colors, fonts, etc.
            $table->json('content_blocks')->nullable(); // drag-and-drop content
            $table->json('seo_settings')->nullable();
            $table->json('social_settings')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_password_protected')->default(false);
            $table->string('password')->nullable();
            $table->json('analytics_settings')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['workspace_id', 'is_published']);
            $table->index('slug');
            $table->index('custom_domain');
        });
    }

    public function down()
    {
        Schema::dropIfExists('link_pages');
    }
};