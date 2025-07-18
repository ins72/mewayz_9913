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
        Schema::create('gamification_user_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('level')->default(1);
            $table->integer('total_xp')->default(0);
            $table->integer('current_level_xp')->default(0);
            $table->integer('next_level_xp')->default(100);
            $table->integer('xp_to_next_level')->default(100);
            $table->string('level_name')->default('Beginner');
            $table->string('level_tier')->default('Bronze'); // Bronze, Silver, Gold, Platinum, Diamond
            $table->json('level_benefits')->nullable(); // Unlocked features, bonuses
            $table->timestamp('last_level_up')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique('user_id');
            $table->index(['level', 'level_tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_user_levels');
    }
};