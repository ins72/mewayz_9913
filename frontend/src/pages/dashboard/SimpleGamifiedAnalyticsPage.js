import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';

const SimpleGamifiedAnalyticsPage = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('dashboard');

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold mb-2">Advanced Analytics & Gamification</h1>
            <p className="text-white/80">Track your performance, earn XP, and climb the leaderboard</p>
          </div>
          <div className="text-right">
            <div className="flex items-center space-x-4">
              <div className="text-center">
                <div className="text-2xl font-bold">24</div>
                <p className="text-sm text-white/70">Level</p>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold">45,680</div>
                <p className="text-sm text-white/70">Points</p>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold">12</div>
                <p className="text-sm text-white/70">Day Streak</p>
              </div>
            </div>
          </div>
        </div>
        
        {/* Level Progress */}
        <div className="mt-6">
          <div className="flex justify-between items-center mb-2">
            <span className="text-sm text-white/70">Progress to Level 25</span>
            <span className="text-sm text-white/70">3,750/4,000 XP</span>
          </div>
          <div className="w-full bg-white/20 rounded-full h-3">
            <div className="bg-gradient-to-r from-yellow-400 to-orange-500 h-3 rounded-full w-[94%]"></div>
          </div>
        </div>
      </div>

      {/* Performance Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Total Reach</p>
              <p className="text-2xl font-bold text-primary">156.8K</p>
              <div className="flex items-center mt-1">
                <span className="text-sm text-green-500">+23.5%</span>
              </div>
            </div>
            <div className="p-3 bg-blue-100 rounded-xl dark:bg-blue-900">
              <div className="h-6 w-6 text-blue-600 dark:text-blue-400">👁</div>
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Engagement</p>
              <p className="text-2xl font-bold text-primary">12.4K</p>
              <div className="flex items-center mt-1">
                <span className="text-sm text-green-500">+18.7%</span>
              </div>
            </div>
            <div className="p-3 bg-pink-100 rounded-xl dark:bg-pink-900">
              <div className="h-6 w-6 text-pink-600 dark:text-pink-400">❤️</div>
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Conversions</p>
              <p className="text-2xl font-bold text-primary">892</p>
              <div className="flex items-center mt-1">
                <span className="text-sm text-green-500">+31.2%</span>
              </div>
            </div>
            <div className="p-3 bg-green-100 rounded-xl dark:bg-green-900">
              <div className="h-6 w-6 text-green-600 dark:text-green-400">📈</div>
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Revenue</p>
              <p className="text-2xl font-bold text-primary">$15.7K</p>
              <div className="flex items-center mt-1">
                <span className="text-sm text-green-500">+28.9%</span>
              </div>
            </div>
            <div className="p-3 bg-yellow-100 rounded-xl dark:bg-yellow-900">
              <div className="h-6 w-6 text-yellow-600 dark:text-yellow-400">💰</div>
            </div>
          </div>
        </motion.div>
      </div>

      {/* Navigation Tabs */}
      <div className="bg-surface-elevated rounded-xl shadow-default">
        <div className="border-b border-default">
          <nav className="flex space-x-8 px-6">
            {[
              { id: 'dashboard', name: 'Performance Dashboard' },
              { id: 'goals', name: 'Goals & Objectives' },
              { id: 'leaderboard', name: 'Leaderboard' },
              { id: 'challenges', name: 'Active Challenges' },
              { id: 'achievements', name: 'Achievements' },
              { id: 'badges', name: 'Badge Collection' }
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`py-4 px-1 border-b-2 font-medium text-sm ${
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-secondary hover:text-primary'
                }`}
              >
                {tab.name}
              </button>
            ))}
          </nav>
        </div>

        <div className="p-6">
          {activeTab === 'dashboard' && (
            <div className="text-center py-12">
              <h3 className="text-xl font-semibold text-primary mb-4">Advanced Analytics Dashboard</h3>
              <p className="text-secondary mb-6">
                Comprehensive performance tracking with gamification elements including XP, levels, badges, and achievements.
              </p>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div className="p-6 bg-surface border border-default rounded-xl">
                  <h4 className="font-semibold text-primary mb-2">Performance Goals</h4>
                  <p className="text-sm text-secondary">Track progress toward monthly reach, engagement, and content goals with XP rewards.</p>
                </div>
                <div className="p-6 bg-surface border border-default rounded-xl">
                  <h4 className="font-semibold text-primary mb-2">Global Leaderboard</h4>
                  <p className="text-sm text-secondary">See how you rank against other creators and climb the leaderboard.</p>
                </div>
                <div className="p-6 bg-surface border border-default rounded-xl">
                  <h4 className="font-semibold text-primary mb-2">Badge System</h4>
                  <p className="text-sm text-secondary">Earn rare badges and achievements for reaching milestones and completing challenges.</p>
                </div>
              </div>
            </div>
          )}
          
          {activeTab === 'goals' && (
            <div className="text-center py-12">
              <h3 className="text-xl font-semibold text-primary mb-4">Performance Goals</h3>
              <p className="text-secondary mb-8">Complete goals to earn XP and unlock new achievements</p>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div className="p-6 bg-surface border border-default rounded-xl">
                  <div className="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                    <span className="text-2xl">🎯</span>
                  </div>
                  <h4 className="font-semibold text-primary mb-2">Monthly Reach Goal</h4>
                  <div className="text-2xl font-bold text-primary mb-1">78%</div>
                  <p className="text-sm text-secondary">156K / 200K reached</p>
                  <div className="w-full bg-surface-elevated rounded-full h-2 mt-3">
                    <div className="bg-blue-500 h-2 rounded-full w-[78%]"></div>
                  </div>
                </div>
                
                <div className="p-6 bg-surface border border-default rounded-xl">
                  <div className="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                    <span className="text-2xl">❤️</span>
                  </div>
                  <h4 className="font-semibold text-primary mb-2">Engagement Rate Goal</h4>
                  <div className="text-2xl font-bold text-primary mb-1">84%</div>
                  <p className="text-sm text-secondary">8.4% / 10.0% rate</p>
                  <div className="w-full bg-surface-elevated rounded-full h-2 mt-3">
                    <div className="bg-pink-500 h-2 rounded-full w-[84%]"></div>
                  </div>
                </div>
                
                <div className="p-6 bg-surface border border-default rounded-xl">
                  <div className="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                    <span className="text-2xl">📝</span>
                  </div>
                  <h4 className="font-semibold text-primary mb-2">Weekly Content Goal</h4>
                  <div className="text-2xl font-bold text-primary mb-1">80%</div>
                  <p className="text-sm text-secondary">12 / 15 posts</p>
                  <div className="w-full bg-surface-elevated rounded-full h-2 mt-3">
                    <div className="bg-green-500 h-2 rounded-full w-[80%]"></div>
                  </div>
                </div>
              </div>
            </div>
          )}
          
          {activeTab === 'leaderboard' && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-xl font-semibold text-primary mb-2">Global Leaderboard</h3>
                <p className="text-secondary">See how you rank against other creators</p>
              </div>
              <div className="space-y-4">
                {[
                  { rank: 1, name: 'Sarah Johnson', points: '67,890', level: 32, change: 0 },
                  { rank: 2, name: 'Mike Chen', points: '54,320', level: 28, change: 0 },
                  { rank: 3, name: 'Emily Rodriguez', points: '48,750', level: 26, change: 1 },
                  { rank: 4, name: user?.name || 'You', points: '45,680', level: 24, change: -1, isUser: true }
                ].map((player) => (
                  <div key={player.rank} className={`p-4 rounded-lg border flex items-center justify-between ${player.isUser ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-500' : 'border-default'}`}>
                    <div className="flex items-center space-x-4">
                      <div className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold ${
                        player.rank === 1 ? 'bg-yellow-100 text-yellow-800' :
                        player.rank === 2 ? 'bg-gray-100 text-gray-800' :
                        player.rank === 3 ? 'bg-orange-100 text-orange-800' :
                        'bg-blue-100 text-blue-800'
                      }`}>
                        {player.rank}
                      </div>
                      <div>
                        <div className="flex items-center space-x-2">
                          <span className="font-semibold text-primary">{player.name}</span>
                          {player.isUser && <span className="text-sm text-blue-600">(You)</span>}
                          {player.rank <= 3 && <span className="text-yellow-500">🏆</span>}
                        </div>
                        <span className="text-sm text-secondary">Level {player.level}</span>
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="font-bold text-primary">{player.points}</div>
                      <div className="text-sm text-secondary">points</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
          
          {activeTab === 'challenges' && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-xl font-semibold text-primary mb-2">Active Challenges</h3>
                <p className="text-secondary">Join challenges to earn XP and unlock badges</p>
              </div>
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {[
                  { title: 'Viral Week Challenge', description: 'Get 50K total reach this week', progress: 67, reward: '1000 XP + Badge', difficulty: 'Hard' },
                  { title: 'Engagement Booster', description: 'Achieve 15% engagement on 5 posts', progress: 40, reward: '500 XP + Badge', difficulty: 'Medium' },
                  { title: 'Content Sprint', description: 'Create 20 pieces of content this month', progress: 85, reward: '300 XP + Badge', difficulty: 'Easy' }
                ].map((challenge, index) => (
                  <div key={index} className="p-6 bg-surface border border-default rounded-xl">
                    <div className="flex justify-between items-start mb-3">
                      <div>
                        <h4 className="font-semibold text-primary">{challenge.title}</h4>
                        <span className={`text-xs px-2 py-1 rounded-full ${
                          challenge.difficulty === 'Hard' ? 'bg-red-100 text-red-800' :
                          challenge.difficulty === 'Medium' ? 'bg-yellow-100 text-yellow-800' :
                          'bg-green-100 text-green-800'
                        }`}>
                          {challenge.difficulty}
                        </span>
                      </div>
                      <button className="btn btn-primary btn-sm">Join</button>
                    </div>
                    <p className="text-sm text-secondary mb-4">{challenge.description}</p>
                    <div className="mb-3">
                      <div className="flex justify-between text-sm mb-1">
                        <span>Progress</span>
                        <span>{challenge.progress}%</span>
                      </div>
                      <div className="w-full bg-surface-elevated rounded-full h-2">
                        <div className="bg-blue-500 h-2 rounded-full" style={{width: `${challenge.progress}%`}}></div>
                      </div>
                    </div>
                    <p className="text-sm text-green-600 font-medium">{challenge.reward}</p>
                  </div>
                ))}
              </div>
            </div>
          )}
          
          {activeTab === 'achievements' && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-xl font-semibold text-primary mb-2">Your Achievements</h3>
                <p className="text-secondary">Milestones you've unlocked</p>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {[
                  { title: 'Social Butterfly', description: 'Connected 5 social accounts', unlocked: true, xp: 200 },
                  { title: 'Engagement King', description: 'Achieved 20% engagement rate', unlocked: true, xp: 500 },
                  { title: 'First $10K Revenue', description: 'Generated $10,000 in revenue', unlocked: false, xp: 1000 },
                  { title: 'Viral Master', description: 'Post reached 100K views', unlocked: false, xp: 750 },
                  { title: 'Content Creator Pro', description: 'Published 100 posts', unlocked: true, xp: 300 },
                  { title: 'Template Master', description: 'Sold 50 templates', unlocked: false, xp: 600 }
                ].map((achievement, index) => (
                  <div key={index} className={`p-6 border rounded-xl text-center ${achievement.unlocked ? 'bg-surface border-default' : 'bg-surface/50 border-dashed opacity-60'}`}>
                    <div className={`w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center text-2xl ${achievement.unlocked ? 'bg-gradient-to-r from-yellow-400 to-orange-500' : 'bg-gray-200 dark:bg-gray-700'}`}>
                      {achievement.unlocked ? '🏆' : '🔒'}
                    </div>
                    <h4 className={`font-semibold mb-2 ${achievement.unlocked ? 'text-primary' : 'text-secondary'}`}>{achievement.title}</h4>
                    <p className={`text-sm mb-4 ${achievement.unlocked ? 'text-secondary' : 'text-gray-400'}`}>{achievement.description}</p>
                    <div className={`text-sm ${achievement.unlocked ? 'text-green-600' : 'text-gray-400'}`}>
                      {achievement.unlocked ? `Unlocked • +${achievement.xp} XP` : `Locked • ${achievement.xp} XP when unlocked`}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
          
          {activeTab === 'badges' && (
            <div className="space-y-6">
              <div className="text-center">
                <h3 className="text-xl font-semibold text-primary mb-2">Badge Collection</h3>
                <p className="text-secondary">Show off your expertise</p>
              </div>
              <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                {[
                  { name: 'First Post', earned: true, rarity: 'common' },
                  { name: 'Social Maven', earned: true, rarity: 'rare' },
                  { name: 'Engagement Pro', earned: true, rarity: 'epic' },
                  { name: 'Viral Master', earned: false, rarity: 'legendary' },
                  { name: 'Analytics Guru', earned: true, rarity: 'rare' },
                  { name: 'Content Creator', earned: true, rarity: 'common' },
                  { name: 'Revenue King', earned: false, rarity: 'epic' },
                  { name: 'Community Builder', earned: false, rarity: 'rare' }
                ].map((badge, index) => (
                  <div key={index} className={`p-4 rounded-xl text-center ${badge.earned ? 'bg-surface border border-default' : 'bg-surface/50 border-dashed border-default/50 opacity-60'}`}>
                    <div className={`w-16 h-16 mx-auto mb-3 rounded-full flex items-center justify-center text-2xl ${
                      badge.earned 
                        ? badge.rarity === 'legendary' ? 'bg-gradient-to-r from-yellow-400 to-orange-500' :
                          badge.rarity === 'epic' ? 'bg-gradient-to-r from-purple-400 to-pink-500' :
                          badge.rarity === 'rare' ? 'bg-gradient-to-r from-blue-400 to-purple-500' :
                          'bg-gradient-to-r from-gray-400 to-gray-500'
                        : 'bg-gray-200 dark:bg-gray-700'
                    }`}>
                      {badge.earned ? '🏆' : '🔒'}
                    </div>
                    <h4 className={`font-semibold text-sm mb-1 ${badge.earned ? 'text-primary' : 'text-gray-400'}`}>{badge.name}</h4>
                    <span className={`text-xs px-2 py-1 rounded-full capitalize ${
                      badge.rarity === 'legendary' ? 'bg-yellow-100 text-yellow-800' :
                      badge.rarity === 'epic' ? 'bg-purple-100 text-purple-800' :
                      badge.rarity === 'rare' ? 'bg-blue-100 text-blue-800' :
                      'bg-gray-100 text-gray-800'
                    }`}>
                      {badge.rarity}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default SimpleGamifiedAnalyticsPage;