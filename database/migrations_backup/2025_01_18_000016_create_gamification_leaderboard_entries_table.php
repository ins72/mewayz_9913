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
        Schema::create('gamification_leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leaderboard_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('position')->default(0);
            $table->integer('score')->default(0);
            $table->integer('previous_position')->nullable();
            $table->string('position_change')->default('same'); // up, down, same, new
            $table->json('score_details')->nullable(); // Breakdown of score calculation
            $table->json('achievements_data')->nullable(); // Related achievements
            $table->datetime('last_updated')->nullable();
            $table->timestamps();

            $table->foreign('leaderboard_id')->references('id')->on('gamification_leaderboards')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['leaderboard_id', 'user_id']);
            $table->index(['leaderboard_id', 'position']);
            $table->index(['user_id', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_leaderboard_entries');
    }
};