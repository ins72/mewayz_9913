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
        if (!Schema::hasTable('templates')) {
            Schema::create('templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('creator_id');
            $table->uuid('category_id');
            $table->string('name');
            $table->text('description');
            $table->enum('template_type', ['website', 'email', 'social', 'bio', 'course']);
            $table->decimal('price', 8, 2)->default(0);
            $table->text('tags')->nullable();
            $table->json('preview_images')->nullable();
            $table->json('template_data');
            $table->string('demo_url')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('template_categories')->onDelete('cascade');
            
            $table->index(['status', 'is_active']);
            $table->index(['template_type', 'status']);
            $table->index(['creator_id']);
            $table->index(['featured', 'status']);
            $table->index(['average_rating']);
            $table->index(['download_count']);
        });


    /**
     * Reverse the migrations.
     */



    public function down(): void
    {
        Schema::dropIfExists('templates');

};