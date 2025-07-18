<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gamification\Achievement;

class GamificationAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            // Beginner Achievements
            [
                'name' => 'Welcome to Mewayz',
                'slug' => 'welcome-to-mewayz',
                'description' => 'Complete your first login to the Mewayz platform',
                'icon' => '🎉',
                'badge_color' => '#10B981',
                'type' => 'milestone',
                'category' => 'general',
                'difficulty' => 'easy',
                'points' => 10,
                'requirements' => ['target' => 1, 'event_type' => 'login'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 10],
                    ['type' => 'badge', 'badge' => 'newcomer']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'Profile Complete',
                'slug' => 'profile-complete',
                'description' => 'Complete your user profile with all required information',
                'icon' => '👤',
                'badge_color' => '#3B82F6',
                'type' => 'milestone',
                'category' => 'general',
                'difficulty' => 'easy',
                'points' => 50,
                'requirements' => ['target' => 1, 'event_type' => 'profile_complete'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 50],
                    ['type' => 'unlock', 'unlock' => 'advanced_features']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 2
            ],
            [
                'name' => 'First Steps',
                'slug' => 'first-steps',
                'description' => 'Login for 7 consecutive days',
                'icon' => '🚀',
                'badge_color' => '#8B5CF6',
                'type' => 'engagement',
                'category' => 'general',
                'difficulty' => 'easy',
                'points' => 100,
                'requirements' => ['target' => 7, 'event_type' => 'login'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 100],
                    ['type' => 'badge', 'badge' => 'consistent_user']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 3
            ],

            // Content Creation Achievements
            [
                'name' => 'Content Creator',
                'slug' => 'content-creator',
                'description' => 'Create your first bio site',
                'icon' => '🎨',
                'badge_color' => '#F59E0B',
                'type' => 'content',
                'category' => 'content',
                'difficulty' => 'medium',
                'points' => 200,
                'requirements' => ['target' => 1, 'event_type' => 'bio_site_created'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 200],
                    ['type' => 'unlock', 'unlock' => 'premium_themes']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 4
            ],
            [
                'name' => 'Social Media Master',
                'slug' => 'social-media-master',
                'description' => 'Create 10 social media posts',
                'icon' => '📱',
                'badge_color' => '#EF4444',
                'type' => 'content',
                'category' => 'social',
                'difficulty' => 'medium',
                'points' => 300,
                'requirements' => ['target' => 10, 'event_type' => 'post_created'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 300],
                    ['type' => 'badge', 'badge' => 'social_expert']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 5
            ],
            [
                'name' => 'Course Creator',
                'slug' => 'course-creator',
                'description' => 'Create your first online course',
                'icon' => '📚',
                'badge_color' => '#8B5CF6',
                'type' => 'content',
                'category' => 'learning',
                'difficulty' => 'hard',
                'points' => 500,
                'requirements' => ['target' => 1, 'event_type' => 'course_created'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 500],
                    ['type' => 'unlock', 'unlock' => 'course_analytics']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 6
            ],

            // Business Achievements
            [
                'name' => 'Email Marketer',
                'slug' => 'email-marketer',
                'description' => 'Send your first email campaign',
                'icon' => '📧',
                'badge_color' => '#10B981',
                'type' => 'business',
                'category' => 'business',
                'difficulty' => 'medium',
                'points' => 250,
                'requirements' => ['target' => 1, 'event_type' => 'email_campaign_sent'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 250],
                    ['type' => 'unlock', 'unlock' => 'advanced_email_templates']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 7
            ],
            [
                'name' => 'Revenue Generator',
                'slug' => 'revenue-generator',
                'description' => 'Generate your first $100 in revenue',
                'icon' => '💰',
                'badge_color' => '#F59E0B',
                'type' => 'revenue',
                'category' => 'business',
                'difficulty' => 'hard',
                'points' => 1000,
                'requirements' => ['target' => 100, 'event_type' => 'revenue_milestone'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 1000],
                    ['type' => 'badge', 'badge' => 'entrepreneur']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 8
            ],

            // Advanced Achievements
            [
                'name' => 'Platform Expert',
                'slug' => 'platform-expert',
                'description' => 'Reach level 10 on the platform',
                'icon' => '🏆',
                'badge_color' => '#8B5CF6',
                'type' => 'milestone',
                'category' => 'general',
                'difficulty' => 'hard',
                'points' => 1500,
                'requirements' => ['target' => 1, 'event_type' => 'level_milestone', 'level' => 10],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 1500],
                    ['type' => 'unlock', 'unlock' => 'expert_features']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 9
            ],
            [
                'name' => 'Legendary Creator',
                'slug' => 'legendary-creator',
                'description' => 'Complete 50 different activities on the platform',
                'icon' => '⭐',
                'badge_color' => '#EF4444',
                'type' => 'milestone',
                'category' => 'general',
                'difficulty' => 'legendary',
                'points' => 5000,
                'requirements' => ['target' => 50, 'event_type' => 'activity_milestone'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 5000],
                    ['type' => 'badge', 'badge' => 'legendary_creator'],
                    ['type' => 'unlock', 'unlock' => 'vip_features']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 10
            ],

            // Engagement Achievements
            [
                'name' => 'Streak Champion',
                'slug' => 'streak-champion',
                'description' => 'Maintain a 30-day login streak',
                'icon' => '🔥',
                'badge_color' => '#EF4444',
                'type' => 'engagement',
                'category' => 'general',
                'difficulty' => 'hard',
                'points' => 750,
                'requirements' => ['target' => 30, 'event_type' => 'streak_milestone'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 750],
                    ['type' => 'badge', 'badge' => 'streak_master']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 11
            ],
            [
                'name' => 'Community Builder',
                'slug' => 'community-builder',
                'description' => 'Refer 5 new users to the platform',
                'icon' => '👥',
                'badge_color' => '#10B981',
                'type' => 'social',
                'category' => 'social',
                'difficulty' => 'medium',
                'points' => 400,
                'requirements' => ['target' => 5, 'event_type' => 'referral_signup'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 400],
                    ['type' => 'unlock', 'unlock' => 'referral_dashboard']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 12
            ],

            // Subscription Achievements
            [
                'name' => 'Premium Member',
                'slug' => 'premium-member',
                'description' => 'Upgrade to any premium subscription',
                'icon' => '💎',
                'badge_color' => '#8B5CF6',
                'type' => 'business',
                'category' => 'business',
                'difficulty' => 'medium',
                'points' => 300,
                'requirements' => ['target' => 1, 'event_type' => 'subscription_upgrade'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 300],
                    ['type' => 'badge', 'badge' => 'premium_member']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 13
            ],

            // Learning Achievements
            [
                'name' => 'Knowledge Seeker',
                'slug' => 'knowledge-seeker',
                'description' => 'Complete 5 online courses',
                'icon' => '🎓',
                'badge_color' => '#3B82F6',
                'type' => 'learning',
                'category' => 'learning',
                'difficulty' => 'medium',
                'points' => 600,
                'requirements' => ['target' => 5, 'event_type' => 'course_completed'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 600],
                    ['type' => 'unlock', 'unlock' => 'advanced_courses']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 14
            ],
            [
                'name' => 'Master Learner',
                'slug' => 'master-learner',
                'description' => 'Complete 20 online courses',
                'icon' => '🧠',
                'badge_color' => '#8B5CF6',
                'type' => 'learning',
                'category' => 'learning',
                'difficulty' => 'hard',
                'points' => 1200,
                'requirements' => ['target' => 20, 'event_type' => 'course_completed'],
                'rewards' => [
                    ['type' => 'xp', 'amount' => 1200],
                    ['type' => 'badge', 'badge' => 'master_learner'],
                    ['type' => 'unlock', 'unlock' => 'certification_program']
                ],
                'is_active' => true,
                'is_repeatable' => false,
                'sort_order' => 15
            ]
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}