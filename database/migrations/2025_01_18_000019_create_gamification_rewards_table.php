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
        Schema::create('gamification_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type'); // xp, badge, unlock, discount, premium_feature
            $table->string('rarity')->default('common'); // common, rare, epic, legendary
            $table->json('reward_data')->nullable(); // Specific reward configuration
            $table->integer('xp_value')->default(0);
            $table->integer('point_cost')->nullable(); // Cost to redeem
            $table->string('icon')->nullable();
            $table->string('color')->default('#10B981');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_redeemable')->default(false);
            $table->integer('max_redemptions')->nullable();
            $table->integer('current_redemptions')->default(0);
            $table->datetime('expires_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'rarity']);
            $table->index(['is_active', 'is_redeemable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_rewards');
    }
};