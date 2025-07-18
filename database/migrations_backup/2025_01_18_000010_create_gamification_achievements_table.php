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
        Schema::create('gamification_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('badge_color')->default('#3B82F6');
            $table->string('type')->default('custom'); // milestone, streak, engagement, revenue, etc.
            $table->string('category')->default('general'); // general, social, business, learning
            $table->string('difficulty')->default('medium'); // easy, medium, hard, legendary
            $table->integer('points')->default(0);
            $table->json('requirements')->nullable(); // Dynamic requirements
            $table->json('rewards')->nullable(); // Points, badges, unlocks
            $table->boolean('is_active')->default(true);
            $table->boolean('is_repeatable')->default(false);
            $table->integer('max_completions')->nullable();
            $table->string('unlock_condition')->nullable(); // Required achievement or level
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['is_active', 'difficulty']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_achievements');
    }
};