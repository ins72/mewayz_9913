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
        Schema::create('gamification_xp_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('event_type'); // login, post_created, course_completed, etc.
            $table->string('event_category')->default('general'); // engagement, content, learning, business
            $table->integer('xp_amount');
            $table->integer('multiplier')->default(1);
            $table->integer('final_xp');
            $table->string('source_type')->nullable(); // Model type that triggered XP
            $table->unsignedBigInteger('source_id')->nullable(); // Model ID that triggered XP
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional event data
            $table->boolean('is_bonus')->default(false);
            $table->string('bonus_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'event_type']);
            $table->index(['event_category', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_xp_events');
    }
};