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
        Schema::create('gamification_user_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('achievement_id');
            $table->integer('progress')->default(0);
            $table->integer('target')->default(1);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('completion_count')->default(0);
            $table->json('progress_data')->nullable(); // Dynamic progress tracking
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('gamification_achievements')->onDelete('cascade');

            $table->unique(['user_id', 'achievement_id']);
            $table->index(['user_id', 'completed']);
            $table->index(['achievement_id', 'completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_user_achievements');
    }
};