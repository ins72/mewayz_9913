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
        Schema::create('gamification_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type'); // daily, weekly, monthly, event, custom
            $table->string('category')->default('general'); // engagement, content, learning, business
            $table->string('difficulty')->default('medium'); // easy, medium, hard, legendary
            $table->json('objectives')->nullable(); // List of objectives to complete
            $table->json('rewards')->nullable(); // XP, achievements, unlocks
            $table->integer('target_value')->default(1);
            $table->string('target_unit')->default('count'); // count, points, minutes, etc.
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->json('participation_requirements')->nullable(); // Level, achievements required
            $table->json('metadata')->nullable(); // Additional challenge data
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['is_active', 'is_featured']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_challenges');
    }
};