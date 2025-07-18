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
        Schema::create('gamification_user_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('challenge_id');
            $table->integer('progress')->default(0);
            $table->integer('target')->default(1);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->json('progress_data')->nullable(); // Detailed progress tracking
            $table->json('rewards_claimed')->nullable(); // Rewards already claimed
            $table->string('status')->default('active'); // active, completed, failed, expired
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('gamification_challenges')->onDelete('cascade');
            
            $table->unique(['user_id', 'challenge_id']);
            $table->index(['user_id', 'status']);
            $table->index(['challenge_id', 'completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_user_challenges');
    }
};