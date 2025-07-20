import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import api from '../../services/api';
import {
  ChartBarIcon,
  TrophyIcon,
  StarIcon,
  FireIcon,
  BoltIcon,
  ShieldCheckIcon,
  EyeIcon,
  HeartIcon,
  FunnelIcon,
  CurrencyDollarIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  RocketLaunchIcon,
  UserGroupIcon,
  LightBulbIcon,
  SparklesIcon,
  ChartPieIcon,
  CalendarIcon,
  ClockIcon,
  GiftIcon,
  BeakerIcon,
  AcademicCapIcon,
  PlayCircleIcon,
  PauseCircleIcon,
  CheckCircleIcon,
  ExclamationCircleIcon
} from '@heroicons/react/24/outline';
import {
  TrophyIcon as TrophyIconSolid,
  FireIcon as FireIconSolid,
  StarIcon as StarIconSolid,
  BoltIcon as BoltIconSolid
} from '@heroicons/react/24/solid';

const GamifiedAnalyticsPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeTab, setActiveTab] = useState('overview');
  const [dateRange, setDateRange] = useState('30d');
  const [loading, setLoading] = useState(true);
  const [analyticsData, setAnalyticsData] = useState(null);
  const [achievementData, setAchievementData] = useState(null);
  const [leaderboard, setLeaderboard] = useState(null);
  const [challenges, setChallenges] = useState(null);

  // Enhanced user gamification profile
  const [userProfile, setUserProfile] = useState({
    level: 1,
    currentXP: 0,
    nextLevelXP: 1000,
    totalPoints: 0,
    rank: 'Rising Entrepreneur',
    streakDays: 0,
    achievements: 0,
    completionRate: 0,
    weeklyGoal: 5000,
    monthlyGoal: 20000,
    badges: [],
    recentActivities: [],
    skillPoints: {
      content_creation: 0,
      social_media: 0,
      analytics: 0,
      sales: 0,
      marketing: 0,
      customer_service: 0
    }
  });

  useEffect(() => {
    loadAnalyticsData();
    loadUserProfile();
    loadAchievements();
    loadLeaderboard();
    loadChallenges();
  }, [dateRange]);

  const loadAnalyticsData = async () => {
    try {
      const response = await api.get('/analytics/overview');
      if (response.data.success) {
        setAnalyticsData(response.data.data);
        calculateGamificationMetrics(response.data.data);
      }
    } catch (err) {
      console.error('Failed to load analytics:', err);
    }
  };

  const loadUserProfile = async () => {
    try {
      // Simulate gamification profile based on real data
      const profileResponse = await api.get('/auth/me');
      if (profileResponse.data.success) {
        const userData = profileResponse.data.user;
        setUserProfile(prev => ({
          ...prev,
          level: calculateUserLevel(userData),
          currentXP: calculateCurrentXP(userData),
          totalPoints: calculateTotalPoints(userData),
          rank: determineUserRank(userData),
          achievements: calculateAchievements(userData)
        }));
      }
    } catch (err) {
      console.error('Failed to load user profile:', err);
    }
  };

  const loadAchievements = async () => {
    try {
      // Generate dynamic achievements based on user activity
      const achievements = [
        {
          id: 'first_login',
          name: 'Welcome Aboard',
          description: 'Successfully logged in for the first time',
          icon: RocketLaunchIcon,
          earned: true,
          earnedAt: new Date().toISOString(),
          rarity: 'common',
          xp: 100,
          category: 'onboarding'
        },
        {
          id: 'social_media_setup',
          name: 'Social Media Pioneer',
          description: 'Connected your first social media account',
          icon: UserGroupIcon,
          earned: true,
          earnedAt: new Date().toISOString(),
          rarity: 'common',
          xp: 200,
          category: 'social'
        },
        {
          id: 'first_ai_request',
          name: 'AI Explorer',
          description: 'Generated your first AI content',
          icon: SparklesIcon,
          earned: false,
          rarity: 'rare',
          xp: 500,
          category: 'ai'
        },
        {
          id: 'analytics_master',
          name: 'Data Detective',
          description: 'Viewed analytics dashboard 10 times',
          icon: ChartBarIcon,
          earned: false,
          rarity: 'epic',
          xp: 750,
          category: 'analytics'
        },
        {
          id: 'revenue_milestone',
          name: 'Revenue Rocket',
          description: 'Generated $1,000 in revenue',
          icon: CurrencyDollarIcon,
          earned: false,
          rarity: 'legendary',
          xp: 2000,
          category: 'business'
        }
      ];

      setAchievementData(achievements);
    } catch (err) {
      console.error('Failed to load achievements:', err);
    }
  };

  const loadLeaderboard = async () => {
    try {
      // Simulate leaderboard data
      const leaderboardData = {
        daily: [
          { rank: 1, user: 'Sarah Chen', points: 2450, avatar: '👑', level: 28 },
          { rank: 2, user: 'Mike Rodriguez', points: 2100, avatar: '🚀', level: 25 },
          { rank: 3, user: 'Emma Watson', points: 1950, avatar: '⭐', level: 24 },
          { rank: 4, user: user?.name || 'You', points: 1200, avatar: '🎯', level: userProfile.level },
          { rank: 5, user: 'David Kim', points: 1050, avatar: '💎', level: 22 }
        ],
        weekly: [
          { rank: 1, user: 'Emma Watson', points: 15200, avatar: '⭐', level: 24 },
          { rank: 2, user: 'Sarah Chen', points: 13800, avatar: '👑', level: 28 },
          { rank: 3, user: user?.name || 'You', points: 8900, avatar: '🎯', level: userProfile.level },
          { rank: 4, user: 'Mike Rodriguez', points: 8200, avatar: '🚀', level: 25 },
          { rank: 5, user: 'David Kim', points: 7500, avatar: '💎', level: 22 }
        ]
      };
      setLeaderboard(leaderboardData);
    } catch (err) {
      console.error('Failed to load leaderboard:', err);
    }
  };

  const loadChallenges = async () => {
    try {
      const currentChallenges = [
        {
          id: 'weekly_content',
          name: 'Content Creator Challenge',
          description: 'Generate 10 AI content pieces this week',
          progress: 3,
          target: 10,
          reward: 1500,
          timeLeft: '4 days',
          type: 'weekly',
          difficulty: 'medium'
        },
        {
          id: 'social_engagement',
          name: 'Social Media Boost',
          description: 'Get 100 total engagements across platforms',
          progress: 67,
          target: 100,
          reward: 2000,
          timeLeft: '2 days',
          type: 'weekly',
          difficulty: 'hard'
        },
        {
          id: 'analytics_streak',
          name: 'Analytics Explorer',
          description: 'Check analytics 7 days in a row',
          progress: 4,
          target: 7,
          reward: 1000,
          timeLeft: '3 days',
          type: 'streak',
          difficulty: 'easy'
        }
      ];

      setChallenges(currentChallenges);
    } catch (err) {
      console.error('Failed to load challenges:', err);
    } finally {
      setLoading(false);
    }
  };

  // Helper functions for gamification calculations
  const calculateUserLevel = (userData) => {
    const createdAt = new Date(userData.created_at);
    const daysSinceCreation = Math.floor((new Date() - createdAt) / (1000 * 60 * 60 * 24));
    return Math.min(Math.floor(daysSinceCreation / 7) + 1, 50); // Level up every week, max level 50
  };

  const calculateCurrentXP = (userData) => {
    const level = calculateUserLevel(userData);
    return Math.floor(Math.random() * (level * 100)); // Random XP based on level
  };

  const calculateTotalPoints = (userData) => {
    const level = calculateUserLevel(userData);
    return level * 1000 + Math.floor(Math.random() * 2000);
  };

  const determineUserRank = (userData) => {
    const level = calculateUserLevel(userData);
    if (level >= 40) return 'Digital Marketing Legend';
    if (level >= 30) return 'Business Growth Expert';
    if (level >= 20) return 'Content Creation Master';
    if (level >= 10) return 'Social Media Specialist';
    if (level >= 5) return 'Rising Entrepreneur';
    return 'Getting Started';
  };

  const calculateAchievements = (userData) => {
    const level = calculateUserLevel(userData);
    return Math.min(Math.floor(level * 1.5), 25);
  };

  const calculateGamificationMetrics = (analyticsData) => {
    // Convert analytics data into gamification points
    // This would be more sophisticated in a real implementation
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  // Advanced analytics data with gamified elements
  const [analytics, setAnalytics] = useState({
    performance: {
      overview: {
        totalReach: 156780,
        reachChange: 23.5,
        engagement: 12450,
        engagementChange: 18.7,
        conversions: 892,
        conversionChange: 31.2,
        revenue: 15680.50,
        revenueChange: 28.9
      },
      goals: [
        {
          id: 'reach_goal',
          title: 'Monthly Reach Goal',
          current: 156780,
          target: 200000,
          progress: 78,
          reward: '500 XP',
          icon: EyeIcon
        },
        {
          id: 'engagement_goal', 
          title: 'Engagement Rate Goal',
          current: 8.4,
          target: 10.0,
          progress: 84,
          reward: '300 XP',
          icon: HeartIcon
        },
        {
          id: 'content_goal',
          title: 'Weekly Content Goal', 
          current: 12,
          target: 15,
          progress: 80,
          reward: '200 XP',
          icon: LightBulbIcon
        }
      ]
    },
    leaderboard: [
      { rank: 1, name: 'Sarah Johnson', avatar: 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=ec4899&color=fff', points: 67890, level: 32, change: 0 },
      { rank: 2, name: 'Mike Chen', avatar: 'https://ui-avatars.com/api/?name=Mike+Chen&background=3b82f6&color=fff', points: 54320, level: 28, change: 0 },
      { rank: 3, name: 'Emily Rodriguez', avatar: 'https://ui-avatars.com/api/?name=Emily+Rodriguez&background=10b981&color=fff', points: 48750, level: 26, change: 1 },
      { rank: 4, name: user?.name || 'You', avatar: 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user?.name || 'You') + '&background=f59e0b&color=fff', points: 45680, level: 24, change: -1, isUser: true },
      { rank: 5, name: 'David Smith', avatar: 'https://ui-avatars.com/api/?name=David+Smith&background=6366f1&color=fff', points: 42100, level: 23, change: 0 }
    ],
    challenges: [
      {
        id: 'viral_week',
        title: 'Viral Week Challenge',
        description: 'Get 50K total reach across all platforms this week',
        progress: 67,
        current: 33500,
        target: 50000,
        timeLeft: '3 days',
        reward: '1000 XP + Viral Master Badge',
        participants: 234,
        icon: FireIcon,
        difficulty: 'Hard'
      },
      {
        id: 'engagement_boost',
        title: 'Engagement Booster',
        description: 'Achieve 15% engagement rate on 5 consecutive posts',
        progress: 40,
        current: 2,
        target: 5,
        timeLeft: '1 week',
        reward: '500 XP + Engagement Expert Badge',
        participants: 567,
        icon: HeartIcon,
        difficulty: 'Medium'
      },
      {
        id: 'content_sprint',
        title: 'Content Creation Sprint',
        description: 'Create and publish 20 pieces of content this month',
        progress: 85,
        current: 17,
        target: 20,
        timeLeft: '5 days',
        reward: '300 XP + Content Creator Pro Badge',
        participants: 1234,
        icon: LightBulbIcon,
        difficulty: 'Easy'
      }
    ],
    achievements: [
      {
        id: 'social_butterfly',
        title: 'Social Butterfly',
        description: 'Connected 5 social media accounts',
        unlocked: true,
        date: '2024-01-10',
        xp: 200,
        rarity: 'common'
      },
      {
        id: 'engagement_king',
        title: 'Engagement King',
        description: 'Achieved 20% engagement rate',
        unlocked: true,
        date: '2024-01-08',
        xp: 500,
        rarity: 'epic'
      },
      {
        id: 'revenue_milestone',
        title: 'First $10K Revenue',
        description: 'Generated $10,000 in revenue',
        unlocked: false,
        xp: 1000,
        rarity: 'legendary'
      }
    ]
  });

  const getProgressColor = (progress) => {
    if (progress >= 80) return 'bg-green-500';
    if (progress >= 60) return 'bg-blue-500';
    if (progress >= 40) return 'bg-yellow-500';
    return 'bg-red-500';
  };

  const getRarityColor = (rarity) => {
    switch (rarity) {
      case 'legendary': return 'from-yellow-400 via-orange-500 to-red-500';
      case 'epic': return 'from-purple-400 via-pink-500 to-red-500';
      case 'rare': return 'from-blue-400 via-blue-500 to-purple-500';
      case 'uncommon': return 'from-green-400 to-blue-500';
      default: return 'from-gray-400 to-gray-500';
    }
  };

  const getDifficultyColor = (difficulty) => {
    switch (difficulty) {
      case 'Hard': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
      case 'Medium': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
      case 'Easy': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
  };

  return (
    <div className="space-y-6">
      {/* Page Header with Gamification */}
      <div className="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold mb-2">Analytics & Performance</h1>
            <p className="text-white/80">Track your performance and climb the leaderboard</p>
          </div>
          <div className="text-right">
            <div className="flex items-center space-x-4">
              <div className="text-center">
                <div className="flex items-center space-x-2 mb-1">
                  <TrophyIconSolid className="h-6 w-6 text-yellow-300" />
                  <span className="text-2xl font-bold">{userProfile.level}</span>
                </div>
                <p className="text-sm text-white/70">Level</p>
              </div>
              <div className="text-center">
                <div className="flex items-center space-x-2 mb-1">
                  <StarIconSolid className="h-6 w-6 text-yellow-300" />
                  <span className="text-2xl font-bold">{userProfile.totalPoints.toLocaleString()}</span>
                </div>
                <p className="text-sm text-white/70">Points</p>
              </div>
              <div className="text-center">
                <div className="flex items-center space-x-2 mb-1">
                  <FireIconSolid className="h-6 w-6 text-orange-400" />
                  <span className="text-2xl font-bold">{userProfile.streakDays}</span>
                </div>
                <p className="text-sm text-white/70">Day Streak</p>
              </div>
            </div>
          </div>
        </div>
        
        {/* Level Progress Bar */}
        <div className="mt-6">
          <div className="flex justify-between items-center mb-2">
            <span className="text-sm text-white/70">Progress to Level {userProfile.level + 1}</span>
            <span className="text-sm text-white/70">{userProfile.currentXP}/{userProfile.nextLevelXP} XP</span>
          </div>
          <div className="w-full bg-white/20 rounded-full h-3">
            <div 
              className="bg-gradient-to-r from-yellow-400 to-orange-500 h-3 rounded-full transition-all duration-300"
              style={{ width: `${(userProfile.currentXP / userProfile.nextLevelXP) * 100}%` }}
            ></div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <div className="bg-surface-elevated rounded-xl shadow-default">
        <div className="border-b border-default">
          <nav className="flex space-x-8 px-6">
            {[
              { id: 'dashboard', name: 'Performance Dashboard', icon: ChartBarIcon },
              { id: 'goals', name: 'Goals & Objectives', icon: TrophyIcon },
              { id: 'leaderboard', name: 'Leaderboard', icon: TrophyIcon },
              { id: 'challenges', name: 'Active Challenges', icon: BoltIcon },
              { id: 'achievements', name: 'Achievements', icon: StarIcon },
              { id: 'badges', name: 'Badge Collection', icon: ShieldCheckIcon }
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-secondary hover:text-primary'
                }`}
              >
                <tab.icon className="h-4 w-4 mr-2" />
                {tab.name}
              </button>
            ))}
          </nav>
        </div>

        <div className="p-6">
          {activeTab === 'dashboard' && (
            <div className="space-y-6">
              {/* Performance Overview */}
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Total Reach</p>
                      <p className="text-2xl font-bold text-primary">{analytics.performance.overview.totalReach.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{analytics.performance.overview.reachChange}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-blue-100 rounded-xl dark:bg-blue-900">
                      <EyeIcon className="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                  </div>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.1 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Engagement</p>
                      <p className="text-2xl font-bold text-primary">{analytics.performance.overview.engagement.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{analytics.performance.overview.engagementChange}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-pink-100 rounded-xl dark:bg-pink-900">
                      <HeartIcon className="h-6 w-6 text-pink-600 dark:text-pink-400" />
                    </div>
                  </div>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.2 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Conversions</p>
                      <p className="text-2xl font-bold text-primary">{analytics.performance.overview.conversions.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{analytics.performance.overview.conversionChange}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-green-100 rounded-xl dark:bg-green-900">
                      <FunnelIcon className="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                  </div>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.3 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Revenue</p>
                      <p className="text-2xl font-bold text-primary">${analytics.performance.overview.revenue.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{analytics.performance.overview.revenueChange}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-yellow-100 rounded-xl dark:bg-yellow-900">
                      <CurrencyDollarIcon className="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                  </div>
                </motion.div>
              </div>

              {/* Chart Placeholder */}
              <div className="bg-surface border border-default rounded-xl p-6">
                <div className="flex items-center justify-between mb-6">
                  <h3 className="text-lg font-semibold text-primary">Performance Trends</h3>
                  <select
                    value={dateRange}
                    onChange={(e) => setDateRange(e.target.value)}
                    className="input text-sm"
                  >
                    <option value="7d">Last 7 days</option>
                    <option value="30d">Last 30 days</option>
                    <option value="90d">Last 90 days</option>
                    <option value="1y">Last year</option>
                  </select>
                </div>
                <div className="h-64 bg-surface-elevated rounded-lg flex items-center justify-center text-secondary">
                  Interactive Chart Component Would Go Here
                </div>
              </div>
            </div>
          )}

          {activeTab === 'goals' && (
            <div className="space-y-6">
              <div className="text-center mb-8">
                <h2 className="text-2xl font-bold text-primary mb-2">Your Performance Goals</h2>
                <p className="text-secondary">Track your progress and earn XP by completing goals</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {analytics.performance.goals.map((goal) => (
                  <motion.div
                    key={goal.id}
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    className="bg-surface border border-default rounded-xl p-6 hover:shadow-lg transition-shadow"
                  >
                    <div className="flex items-center justify-between mb-4">
                      <div className={`p-3 rounded-xl ${getProgressColor(goal.progress)}/10`}>
                        <goal.icon className={`h-6 w-6 ${getProgressColor(goal.progress).replace('bg-', 'text-')}`} />
                      </div>
                      <span className="text-sm font-medium text-green-600">{goal.reward}</span>
                    </div>
                    
                    <h3 className="font-semibold text-primary mb-2">{goal.title}</h3>
                    
                    <div className="mb-4">
                      <div className="flex justify-between items-center mb-2">
                        <span className="text-sm text-secondary">Progress</span>
                        <span className="text-sm font-medium text-primary">{goal.progress}%</span>
                      </div>
                      <div className="w-full bg-surface-elevated rounded-full h-3">
                        <div
                          className={`h-3 rounded-full transition-all duration-300 ${getProgressColor(goal.progress)}`}
                          style={{ width: `${goal.progress}%` }}
                        ></div>
                      </div>
                    </div>
                    
                    <div className="text-sm text-secondary">
                      <span className="font-medium text-primary">{typeof goal.current === 'number' ? goal.current.toLocaleString() : goal.current}</span>
                      {' / '}
                      <span>{typeof goal.target === 'number' ? goal.target.toLocaleString() : goal.target}</span>
                      {goal.id === 'engagement_goal' && <span>%</span>}
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'leaderboard' && (
            <div className="space-y-6">
              <div className="text-center mb-8">
                <h2 className="text-2xl font-bold text-primary mb-2">Global Leaderboard</h2>
                <p className="text-secondary">See how you rank against other creators</p>
              </div>

              <div className="bg-surface border border-default rounded-xl overflow-hidden">
                <div className="p-6 border-b border-default bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20">
                  <h3 className="text-lg font-semibold text-primary mb-4">Top Performers</h3>
                </div>
                
                <div className="divide-y divide-default">
                  {analytics.leaderboard.map((user, index) => (
                    <div key={index} className={`p-6 flex items-center justify-between hover:bg-surface-hover transition-colors ${user.isUser ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500' : ''}`}>
                      <div className="flex items-center space-x-4">
                        <div className={`flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${
                          user.rank === 1 ? 'bg-yellow-100 text-yellow-800' :
                          user.rank === 2 ? 'bg-gray-100 text-gray-800' :
                          user.rank === 3 ? 'bg-orange-100 text-orange-800' :
                          'bg-blue-100 text-blue-800'
                        }`}>
                          {user.rank}
                        </div>
                        
                        <img
                          src={user.avatar}
                          alt={user.name}
                          className="w-12 h-12 rounded-full"
                        />
                        
                        <div>
                          <div className="flex items-center space-x-2">
                            <h4 className="font-semibold text-primary">{user.name}</h4>
                            {user.isUser && <span className="text-sm text-blue-600 font-medium">(You)</span>}
                            {user.rank <= 3 && (
                              <TrophyIconSolid className={`h-4 w-4 ${
                                user.rank === 1 ? 'text-yellow-500' :
                                user.rank === 2 ? 'text-gray-500' :
                                'text-orange-500'
                              }`} />
                            )}
                          </div>
                          <p className="text-sm text-secondary">Level {user.level}</p>
                        </div>
                      </div>
                      
                      <div className="text-right">
                        <p className="font-bold text-primary">{user.points.toLocaleString()}</p>
                        <div className="flex items-center text-sm">
                          {user.change === 0 ? (
                            <span className="text-secondary">No change</span>
                          ) : user.change > 0 ? (
                            <>
                              <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                              <span className="text-green-500">+{user.change}</span>
                            </>
                          ) : (
                            <>
                              <ArrowTrendingDownIcon className="h-4 w-4 text-red-500 mr-1" />
                              <span className="text-red-500">{user.change}</span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {activeTab === 'challenges' && (
            <div className="space-y-6">
              <div className="text-center mb-8">
                <h2 className="text-2xl font-bold text-primary mb-2">Active Challenges</h2>
                <p className="text-secondary">Join challenges to earn XP and unlock exclusive badges</p>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {analytics.challenges.map((challenge) => (
                  <motion.div
                    key={challenge.id}
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    className="bg-surface border border-default rounded-xl p-6 hover:shadow-lg transition-all"
                  >
                    <div className="flex items-start justify-between mb-4">
                      <div className="flex items-center space-x-3">
                        <div className={`p-3 rounded-xl ${getProgressColor(challenge.progress)}/10`}>
                          <challenge.icon className={`h-6 w-6 ${getProgressColor(challenge.progress).replace('bg-', 'text-')}`} />
                        </div>
                        <div>
                          <h3 className="font-semibold text-primary">{challenge.title}</h3>
                          <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getDifficultyColor(challenge.difficulty)}`}>
                            {challenge.difficulty}
                          </span>
                        </div>
                      </div>
                      <div className="text-right text-sm">
                        <p className="text-secondary">{challenge.timeLeft} left</p>
                        <p className="text-primary font-medium">{challenge.participants} joined</p>
                      </div>
                    </div>
                    
                    <p className="text-sm text-secondary mb-4">{challenge.description}</p>
                    
                    <div className="mb-4">
                      <div className="flex justify-between items-center mb-2">
                        <span className="text-sm text-secondary">Progress</span>
                        <span className="text-sm font-medium text-primary">{challenge.current}/{challenge.target}</span>
                      </div>
                      <div className="w-full bg-surface-elevated rounded-full h-3">
                        <div
                          className={`h-3 rounded-full transition-all duration-300 ${getProgressColor(challenge.progress)}`}
                          style={{ width: `${challenge.progress}%` }}
                        ></div>
                      </div>
                      <div className="text-xs text-right mt-1 text-secondary">{challenge.progress}% complete</div>
                    </div>
                    
                    <div className="flex items-center justify-between">
                      <div>
                        <p className="text-sm font-medium text-green-600">{challenge.reward}</p>
                      </div>
                      <button className="btn btn-primary btn-sm">
                        {challenge.progress > 0 ? 'Continue' : 'Join Challenge'}
                      </button>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'achievements' && (
            <div className="space-y-6">
              <div className="text-center mb-8">
                <h2 className="text-2xl font-bold text-primary mb-2">Your Achievements</h2>
                <p className="text-secondary">Milestones you've unlocked on your journey</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {analytics.achievements.map((achievement) => (
                  <motion.div
                    key={achievement.id}
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    className={`border rounded-xl p-6 transition-all ${
                      achievement.unlocked
                        ? 'bg-surface border-default hover:shadow-lg'
                        : 'bg-surface/50 border-dashed border-default/50 opacity-60'
                    }`}
                  >
                    <div className="text-center">
                      <div className={`w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center ${
                        achievement.unlocked
                          ? `bg-gradient-to-r ${getRarityColor(achievement.rarity)}`
                          : 'bg-gray-200 dark:bg-gray-700'
                      }`}>
                        <TrophyIcon className={`h-8 w-8 ${achievement.unlocked ? 'text-white' : 'text-gray-400'}`} />
                      </div>
                      
                      <h3 className={`font-semibold mb-2 ${achievement.unlocked ? 'text-primary' : 'text-secondary'}`}>
                        {achievement.title}
                      </h3>
                      
                      <p className={`text-sm mb-4 ${achievement.unlocked ? 'text-secondary' : 'text-gray-400'}`}>
                        {achievement.description}
                      </p>
                      
                      <div className={`text-sm ${achievement.unlocked ? 'text-green-600' : 'text-gray-400'}`}>
                        {achievement.unlocked ? (
                          <>
                            <p className="font-medium">+{achievement.xp} XP</p>
                            <p>Unlocked {achievement.date}</p>
                          </>
                        ) : (
                          <p>Locked • {achievement.xp} XP when unlocked</p>
                        )}
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'badges' && (
            <div className="space-y-6">
              <div className="text-center mb-8">
                <h2 className="text-2xl font-bold text-primary mb-2">Badge Collection</h2>
                <p className="text-secondary">Show off your expertise with earned badges</p>
              </div>

              <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                {userProfile.badges.map((badge) => (
                  <motion.div
                    key={badge.id}
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    className={`p-6 rounded-xl border text-center transition-all ${
                      badge.earned
                        ? 'bg-surface border-default hover:shadow-lg'
                        : 'bg-surface/50 border-dashed border-default/50 opacity-60'
                    }`}
                  >
                    <div className={`w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center ${
                      badge.earned
                        ? `bg-gradient-to-r ${getRarityColor(badge.rarity)}`
                        : 'bg-gray-200 dark:bg-gray-700'
                    }`}>
                      <badge.icon className={`h-8 w-8 ${badge.earned ? 'text-white' : 'text-gray-400'}`} />
                    </div>
                    
                    <h3 className={`font-semibold text-sm ${badge.earned ? 'text-primary' : 'text-gray-400'}`}>
                      {badge.name}
                    </h3>
                    
                    <div className="mt-2">
                      <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium capitalize ${
                        badge.rarity === 'legendary' ? 'bg-yellow-100 text-yellow-800' :
                        badge.rarity === 'epic' ? 'bg-purple-100 text-purple-800' :
                        badge.rarity === 'rare' ? 'bg-blue-100 text-blue-800' :
                        'bg-gray-100 text-gray-800'
                      }`}>
                        {badge.rarity}
                      </span>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default GamifiedAnalyticsPage;