<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create base tables first (no foreign key dependencies)
        
        // Subscription Plans Table
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime']);
            $table->integer('max_workspaces')->default(1);
            $table->integer('max_team_members')->default(1);
            $table->integer('max_bio_sites')->default(1);
            $table->integer('max_courses')->default(1);
            $table->integer('max_products')->default(10);
            $table->boolean('analytics_enabled')->default(true);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Affiliates Table
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('unique_code')->unique();
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->json('payment_details')->nullable();
            $table->timestamps();
        });

        // Workspaces Table
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->json('settings')->nullable();
            $table->json('branding')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('preview_video_url')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->json('tags')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free')->default(false);
            $table->integer('enrollment_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
        });

        // Bio Sites Table
        Schema::create('bio_sites', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->json('theme_config')->nullable();
            $table->json('links')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('view_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->timestamps();
        });

        // Escrow Transactions Table
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'funded', 'delivered', 'completed', 'disputed', 'refunded'])->default('pending');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Booking Services Table
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_minutes');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('availability')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('location')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Email Campaigns Table
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('content');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Integration Configurations Table
        Schema::create('integration_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->json('configuration');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });

        // Subscription Addons Table
        Schema::create('subscription_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'one_time']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Gamification Tables
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('badge_icon')->nullable();
            $table->integer('xp_reward')->default(0);
            $table->json('requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->timestamp('earned_at');
            $table->timestamps();
        });

        Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->default(1);
            $table->integer('total_xp')->default(0);
            $table->integer('current_level_xp')->default(0);
            $table->integer('next_level_xp')->default(100);
            $table->timestamps();
        });

        Schema::create('xp_events', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->integer('xp_amount');
            $table->text('description')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();
        });

        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->string('streak_type');
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();
        });

        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('period');
            $table->integer('position');
            $table->integer('score');
            $table->timestamps();
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->integer('xp_reward')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('challenge_participations', function (Blueprint $table) {
            $table->id();
            $table->timestamp('joined_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
        });

        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->json('reward_data')->nullable();
            $table->integer('cost_xp')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->timestamp('redeemed_at');
            $table->timestamp('used_at')->nullable();
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('challenge_participations');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('user_streaks');
        Schema::dropIfExists('xp_events');
        Schema::dropIfExists('user_levels');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('subscription_addons');
        Schema::dropIfExists('integration_configurations');
        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('escrow_transactions');
        Schema::dropIfExists('bio_sites');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('workspaces');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('subscription_plans');
    }
};