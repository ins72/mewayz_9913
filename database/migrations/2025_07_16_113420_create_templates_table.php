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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type'); // email, bio-page, landing-page, course, etc.
            $table->foreignId('category_id')->constrained('template_categories');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Template creator
            $table->json('template_data'); // The actual template content/structure
            $table->string('preview_image')->nullable();
            $table->json('tags')->nullable();
            $table->decimal('price', 8, 2)->default(0.00); // 0 for free templates
            $table->enum('status', ['draft', 'published', 'rejected'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->integer('downloads')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->json('metadata')->nullable(); // Additional template metadata
            $table->timestamps();
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');

};