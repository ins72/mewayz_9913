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
        // Achievements table
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('flavor_text')->nullable();
            $table->enum('category', ['social', 'content', 'commerce', 'learning', 'community', 'platform', 'collaboration', 'innovation']);
            $table->enum('tier', ['starter', 'bronze', 'silver', 'gold', 'platinum', 'diamond', 'legendary', 'mythical']);
            $table->json('requirements');
            $table->json('rewards');
            $table->string('icon');
            $table->string('animated_icon')->nullable();
            $table->integer('rarity')->default(1000); // 1-1000
            $table->boolean('is_hidden')->default(false);
            $table->boolean('is_secret')->default(false);
            $table->json('prerequisite_achievements')->nullable();
            $table->json('mutually_exclusive')->nullable();
            $table->json('seasonal_availability')->nullable();
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->string('first_earner')->nullable();
            $table->integer('earned_count')->default(0);
            $table->json('tags')->nullable();
            $table->enum('difficulty', ['trivial', 'easy', 'medium', 'hard', 'expert', 'legendary', 'impossible']);
            $table->integer('estimated_time')->default(0); // hours
            $table->decimal('ai_difficulty', 5, 2)->default(0);
            $table->decimal('community_rating', 3, 2)->default(0);
            $table->json('related_achievements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['category', 'tier']);
            $table->index(['difficulty', 'is_active']);
            $table->index(['rarity', 'is_hidden']);
        });

        // User achievements pivot table
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->integer('progress')->default(100);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
            $table->index(['user_id', 'earned_at']);
        });

        // User progress table
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('current_level')->default(1);
            $table->bigInteger('current_xp')->default(0);
            $table->bigInteger('total_xp')->default(0);
            $table->bigInteger('lifetime_xp')->default(0);
            $table->integer('prestige')->default(0);
            $table->integer('prestige_points')->default(0);
            $table->json('specializations')->nullable();
            $table->json('mastery')->nullable();
            $table->json('level_history')->nullable();
            $table->json('xp_multipliers')->nullable();
            $table->json('streaks')->nullable();
            $table->json('seasonal_bonus')->nullable();
            $table->json('mentorship_bonus')->nullable();
            $table->bigInteger('daily_xp')->default(0);
            $table->bigInteger('weekly_xp')->default(0);
            $table->bigInteger('monthly_xp')->default(0);
            $table->bigInteger('yearly_xp')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['current_level', 'current_xp']);
            $table->index(['total_xp']);
        });

        // Leaderboards table
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['global', 'regional', 'workspace', 'category', 'seasonal', 'realtime', 'historical', 'predictive']);
            $table->string('category');
            $table->enum('timeframe', ['daily', 'weekly', 'monthly', 'yearly', 'alltime', 'custom']);
            $table->string('metric');
            $table->string('calculation_method')->default('sum');
            $table->integer('min_entries')->default(1);
            $table->integer('max_entries')->default(1000);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->string('reset_frequency')->default('never');
            $table->timestamp('last_reset')->nullable();
            $table->timestamp('next_reset')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'category', 'is_active']);
            $table->index(['timeframe', 'reset_frequency']);
        });

        // Leaderboard entries table
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leaderboard_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rank');
            $table->integer('previous_rank')->default(999999);
            $table->decimal('score', 15, 2);
            $table->decimal('previous_score', 15, 2)->default(0);
            $table->json('achievements')->nullable();
            $table->json('badges')->nullable();
            $table->json('specializations')->nullable();
            $table->json('streak_summary')->nullable();
            $table->json('social_metrics')->nullable();
            $table->timestamp('last_active')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['leaderboard_id', 'user_id']);
            $table->index(['leaderboard_id', 'rank']);
            $table->index(['user_id', 'score']);
        });

        // Challenges table
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('story')->nullable();
            $table->enum('type', ['daily', 'weekly', 'monthly', 'seasonal', 'special', 'community', 'personal', 'competitive']);
            $table->enum('difficulty', ['trivial', 'easy', 'medium', 'hard', 'expert', 'legendary', 'impossible']);
            $table->string('category');
            $table->json('requirements');
            $table->json('rewards');
            $table->json('penalties')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('participant_limit')->nullable();
            $table->integer('current_participants')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->decimal('average_time', 8, 2)->default(0);
            $table->boolean('is_team_challenge')->default(false);
            $table->integer('team_size')->nullable();
            $table->boolean('is_ranked')->default(false);
            $table->json('prerequisites')->nullable();
            $table->json('exclusions')->nullable();
            $table->boolean('dynamic_difficulty')->default(false);
            $table->boolean('ai_adaptation')->default(false);
            $table->boolean('community_voting')->default(false);
            $table->boolean('mentorship_required')->default(false);
            $table->boolean('cross_platform_integration')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'category', 'is_active']);
            $table->index(['difficulty', 'start_date']);
            $table->index(['is_featured', 'start_date']);
        });

        // Challenge participants pivot table
        Schema::create('challenge_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('progress')->default(0);
            $table->decimal('score', 10, 2)->default(0);
            $table->integer('rank')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['challenge_id', 'user_id']);
            $table->index(['challenge_id', 'score']);
            $table->index(['user_id', 'joined_at']);
        });

        // Challenge teams table
        Schema::create('challenge_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
            $table->integer('max_members');
            $table->integer('current_members')->default(1);
            $table->decimal('team_score', 10, 2)->default(0);
            $table->integer('team_rank')->nullable();
            $table->boolean('is_recruiting')->default(true);
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['challenge_id', 'team_score']);
            $table->index(['challenge_id', 'is_recruiting']);
        });

        // Challenge team members pivot table
        Schema::create('challenge_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('challenge_teams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['leader', 'member']);
            $table->timestamp('joined_at');
            $table->decimal('individual_score', 10, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
            $table->index(['team_id', 'role']);
        });

        // Guilds table
        Schema::create('guilds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('banner')->nullable();
            $table->string('logo')->nullable();
            $table->timestamp('founded_date');
            $table->foreignId('founder_id')->constrained('users')->onDelete('cascade');
            $table->integer('member_count')->default(1);
            $table->integer('max_members')->default(50);
            $table->integer('level')->default(1);
            $table->bigInteger('xp')->default(0);
            $table->integer('reputation')->default(0);
            $table->bigInteger('treasury')->default(0);
            $table->json('specializations')->nullable();
            $table->json('requirements')->nullable();
            $table->json('benefits')->nullable();
            $table->json('activities')->nullable();
            $table->json('achievements')->nullable();
            $table->json('competitions')->nullable();
            $table->json('alliances')->nullable();
            $table->json('territories')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_recruiting')->default(true);
            $table->string('language', 5)->default('en');
            $table->string('timezone')->nullable();
            $table->json('culture')->nullable();
            $table->json('governance')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_public', 'is_recruiting']);
            $table->index(['level', 'reputation']);
        });

        // Guild members pivot table
        Schema::create('guild_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guild_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['founder', 'leader', 'officer', 'member', 'recruit']);
            $table->timestamp('joined_at');
            $table->bigInteger('contribution_xp')->default(0);
            $table->integer('contribution_points')->default(0);
            $table->json('permissions')->nullable();
            $table->json('achievements')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['guild_id', 'user_id']);
            $table->index(['guild_id', 'role']);
            $table->index(['user_id', 'joined_at']);
        });

        // Rewards table
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['virtual', 'practical', 'monetary', 'physical', 'social', 'educational', 'experiential', 'charitable']);
            $table->enum('category', ['xp', 'credits', 'badge', 'theme', 'premium', 'physical', 'discount', 'access']);
            $table->json('requirements')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('currency', 10)->default('credits');
            $table->integer('stock')->nullable();
            $table->integer('claimed_count')->default(0);
            $table->boolean('is_one_time')->default(false);
            $table->boolean('is_tradeable')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['cost', 'currency']);
            $table->index(['available_from', 'available_until']);
        });

        // User rewards pivot table
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->timestamp('claimed_at');
            $table->enum('status', ['claimed', 'used', 'expired', 'refunded']);
            $table->decimal('cost_paid', 10, 2)->default(0);
            $table->string('currency_paid', 10)->default('credits');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'claimed_at']);
            $table->index(['reward_id', 'status']);
        });

        // XP transactions table for detailed tracking
        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->decimal('multiplier', 5, 2)->default(1.0);
            $table->integer('final_amount');
            $table->string('source');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['source', 'created_at']);
        });

        // User badges table
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_slug');
            $table->string('badge_name');
            $table->text('badge_description')->nullable();
            $table->string('badge_icon')->nullable();
            $table->enum('badge_rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary']);
            $table->timestamp('earned_at');
            $table->string('earned_from')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'badge_slug']);
            $table->index(['user_id', 'earned_at']);
            $table->index(['badge_rarity', 'earned_at']);
        });

        // Daily quests table
        Schema::create('daily_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('quest_type');
            $table->string('title');
            $table->text('description');
            $table->json('requirements');
            $table->json('rewards');
            $table->integer('progress')->default(0);
            $table->integer('target');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->date('quest_date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'quest_type', 'quest_date']);
            $table->index(['user_id', 'quest_date', 'is_completed']);
        });

        // Mentorship relationships table
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled']);
            $table->text('goals')->nullable();
            $table->json('areas_of_focus')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('sessions_completed')->default(0);
            $table->decimal('mentor_rating', 3, 2)->nullable();
            $table->decimal('mentee_rating', 3, 2)->nullable();
            $table->text('mentor_feedback')->nullable();
            $table->text('mentee_feedback')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['mentor_id', 'mentee_id']);
            $table->index(['mentor_id', 'status']);
            $table->index(['mentee_id', 'status']);
        });

        // Collaboration projects table
        Schema::create('collaboration_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['content', 'business', 'learning', 'community', 'innovation']);
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled']);
            $table->integer('max_collaborators')->default(5);
            $table->integer('current_collaborators')->default(1);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('required_skills')->nullable();
            $table->json('rewards_distribution')->nullable();
            $table->json('milestones')->nullable();
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['creator_id', 'status']);
        });

        // Collaboration participants pivot table
        Schema::create('collaboration_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('collaboration_projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['creator', 'co-leader', 'contributor', 'advisor']);
            $table->decimal('contribution_percentage', 5, 2)->default(0);
            $table->decimal('revenue_share', 5, 2)->default(0);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->json('contributions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
            $table->index(['project_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaboration_participants');
        Schema::dropIfExists('collaboration_projects');
        Schema::dropIfExists('mentorships');
        Schema::dropIfExists('daily_quests');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('user_rewards');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('guild_members');
        Schema::dropIfExists('guilds');
        Schema::dropIfExists('challenge_team_members');
        Schema::dropIfExists('challenge_teams');
        Schema::dropIfExists('challenge_participants');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('leaderboard_entries');
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('user_progress');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
    }
};