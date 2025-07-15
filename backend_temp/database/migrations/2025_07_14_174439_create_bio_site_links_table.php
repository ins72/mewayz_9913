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
        Schema::create('bio_site_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bio_site_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('url');
            $table->text('description')->nullable();
            $table->enum('type', ['link', 'email', 'phone', 'social', 'custom'])->default('link');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('click_count')->default(0);
            $table->json('metadata')->nullable(); // Additional link data
            $table->timestamps();

            $table->index(['bio_site_id', 'is_active']);
            $table->index(['bio_site_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bio_site_links');
    }
};