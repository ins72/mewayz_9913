import React, { useState, useEffect, useContext } from 'react';
import { motion } from 'framer-motion';
import { 
  UserPlusIcon,
  StarIcon,
  GiftIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  ShareIcon,
  ClipboardDocumentIcon,
  EnvelopeIcon,
  DevicePhoneMobileIcon,
  LinkIcon,
  CheckCircleIcon,
  FireIcon
} from '@heroicons/react/24/outline';
import { AuthContext } from '../../contexts/AuthContext';

const ReferralSystem = () => {
  const { user } = useContext(AuthContext);
  const [referralStats, setReferralStats] = useState({});
  const [referralCode, setReferralCode] = useState('');
  const [referralUrl, setReferralUrl] = useState('');
  const [recentReferrals, setRecentReferrals] = useState([]);
  const [rewards, setRewards] = useState([]);
  const [leaderboard, setLeaderboard] = useState([]);

  // Mock data - in production this would come from API
  useEffect(() => {
    const mockStats = {
      totalReferrals: 23,
      successfulSignups: 18,
      conversionRate: 78.3,
      totalEarnings: 540.00,
      pendingEarnings: 120.00,
      lifetimeValue: 2340.50,
      currentTier: 'Gold',
      nextTierRequirement: 7
    };

    const mockReferrals = [
      { id: 1, name: 'Sarah Johnson', email: 's***@gmail.com', status: 'converted', date: '2024-01-15', reward: 30 },
      { id: 2, name: 'Mike Chen', email: 'm***@outlook.com', status: 'signed_up', date: '2024-01-14', reward: 15 },
      { id: 3, name: 'Emma Davis', email: 'e***@yahoo.com', status: 'pending', date: '2024-01-13', reward: 0 },
      { id: 4, name: 'Alex Brown', email: 'a***@gmail.com', status: 'converted', date: '2024-01-12', reward: 30 },
      { id: 5, name: 'Lisa Wilson', email: 'l***@hotmail.com', status: 'converted', date: '2024-01-11', reward: 30 }
    ];

    const mockRewards = [
      { id: 1, type: 'signup_bonus', title: 'Referral Signup', description: 'Friend signs up with your link', reward: '$15', icon: UserPlusIcon },
      { id: 2, type: 'conversion_bonus', title: 'Subscription Conversion', description: 'Friend upgrades to paid plan', reward: '$30', icon: CurrencyDollarIcon },
      { id: 3, type: 'tier_bonus', title: 'Gold Tier Bonus', description: 'Reach Gold tier (25 referrals)', reward: '$100', icon: StarIcon },
      { id: 4, type: 'monthly_bonus', title: 'Top Referrer Bonus', description: 'Be the top referrer this month', reward: '$500', icon: FireIcon }
    ];

    const mockLeaderboard = [
      { rank: 1, name: 'Jennifer Lee', referrals: 156, earnings: 4680, avatar: 'JL', tier: 'Diamond' },
      { rank: 2, name: 'David Kumar', referrals: 134, earnings: 4020, avatar: 'DK', tier: 'Diamond' },
      { rank: 3, name: 'Maria Garcia', referrals: 98, earnings: 2940, avatar: 'MG', tier: 'Platinum' },
      { rank: 4, name: 'You', referrals: 23, earnings: 540, avatar: user?.name?.charAt(0) || 'Y', tier: 'Gold' },
      { rank: 5, name: 'Robert Smith', referrals: 19, earnings: 465, avatar: 'RS', tier: 'Silver' }
    ];

    setReferralStats(mockStats);
    setReferralCode(user?.id ? `MEWAYZ${user.id.slice(-6).toUpperCase()}` : 'MEWAYZ123ABC');
    setReferralUrl(`https://mewayz.com/join/${referralCode}`);
    setRecentReferrals(mockReferrals);
    setRewards(mockRewards);
    setLeaderboard(mockLeaderboard);
  }, [user, referralCode]);

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    // You could add a toast notification here
  };

  const shareReferral = (platform) => {
    const message = `Join me on Mewayz - the complete creator economy platform! Use my referral link and we both get rewards: ${referralUrl}`;
    
    const urls = {
      twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}`,
      facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralUrl)}`,
      linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(referralUrl)}`,
      email: `mailto:?subject=Join me on Mewayz&body=${encodeURIComponent(message)}`,
      sms: `sms:?body=${encodeURIComponent(message)}`
    };

    if (urls[platform]) {
      window.open(urls[platform], '_blank');
    }
  };

  const getTierColor = (tier) => {
    switch (tier?.toLowerCase()) {
      case 'bronze': return 'text-amber-600 bg-amber-100 dark:bg-amber-900 dark:text-amber-400';
      case 'silver': return 'text-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400';
      case 'gold': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-400';
      case 'platinum': return 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-400';
      case 'diamond': return 'text-purple-600 bg-purple-100 dark:bg-purple-900 dark:text-purple-400';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400';
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'converted': return 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-400';
      case 'signed_up': return 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-400';
      case 'pending': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-400';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400';
    }
  };

  return (
    <div className="max-w-7xl mx-auto p-6 space-y-8">
      {/* Header */}
      <div className="text-center">
        <h1 className="text-4xl font-bold text-gray-900 dark:text-white mb-4">
          Referral Program
        </h1>
        <p className="text-xl text-gray-600 dark:text-gray-300">
          Share Mewayz with friends and earn rewards together
        </p>
      </div>

      {/* Stats Overview */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <motion.div
          whileHover={{ y: -2 }}
          className="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl p-6 shadow-lg"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-blue-100 text-sm">Total Referrals</p>
              <p className="text-3xl font-bold">{referralStats.totalReferrals}</p>
            </div>
            <UserPlusIcon className="h-12 w-12 text-blue-200" />
          </div>
          <div className="mt-4">
            <span className="text-blue-100 text-sm">
              {referralStats.successfulSignups} converted
            </span>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ y: -2 }}
          className="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl p-6 shadow-lg"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-green-100 text-sm">Total Earnings</p>
              <p className="text-3xl font-bold">${referralStats.totalEarnings}</p>
            </div>
            <CurrencyDollarIcon className="h-12 w-12 text-green-200" />
          </div>
          <div className="mt-4">
            <span className="text-green-100 text-sm">
              ${referralStats.pendingEarnings} pending
            </span>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ y: -2 }}
          className="bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl p-6 shadow-lg"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-orange-100 text-sm">Conversion Rate</p>
              <p className="text-3xl font-bold">{referralStats.conversionRate}%</p>
            </div>
            <ChartBarIcon className="h-12 w-12 text-orange-200" />
          </div>
          <div className="mt-4">
            <span className="text-orange-100 text-sm">Above average</span>
          </div>
        </motion.div>

        <motion.div
          whileHover={{ y: -2 }}
          className="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl p-6 shadow-lg"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-purple-100 text-sm">Current Tier</p>
              <p className="text-3xl font-bold">{referralStats.currentTier}</p>
            </div>
            <StarIcon className="h-12 w-12 text-purple-200" />
          </div>
          <div className="mt-4">
            <span className="text-purple-100 text-sm">
              {referralStats.nextTierRequirement} more to Platinum
            </span>
          </div>
        </motion.div>
      </div>

      {/* Referral Tools */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Share Your Link */}
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
          <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
            Share Your Referral Link
          </h2>
          
          {/* Referral Code */}
          <div className="mb-6">
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Your Referral Code
            </label>
            <div className="flex items-center space-x-3">
              <div className="flex-1 p-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg font-mono text-lg">
                {referralCode}
              </div>
              <button
                onClick={() => copyToClipboard(referralCode)}
                className="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                title="Copy Code"
              >
                <ClipboardDocumentIcon className="h-5 w-5" />
              </button>
            </div>
          </div>

          {/* Referral URL */}
          <div className="mb-6">
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Your Referral Link
            </label>
            <div className="flex items-center space-x-3">
              <div className="flex-1 p-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm break-all">
                {referralUrl}
              </div>
              <button
                onClick={() => copyToClipboard(referralUrl)}
                className="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                title="Copy Link"
              >
                <ClipboardDocumentIcon className="h-5 w-5" />
              </button>
            </div>
          </div>

          {/* Share Buttons */}
          <div>
            <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              Share on Social Media
            </h3>
            <div className="grid grid-cols-2 md:grid-cols-5 gap-3">
              <button
                onClick={() => shareReferral('twitter')}
                className="flex items-center justify-center p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
              >
                <ShareIcon className="h-5 w-5 md:mr-2" />
                <span className="hidden md:inline">Twitter</span>
              </button>
              <button
                onClick={() => shareReferral('facebook')}
                className="flex items-center justify-center p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                <ShareIcon className="h-5 w-5 md:mr-2" />
                <span className="hidden md:inline">Facebook</span>
              </button>
              <button
                onClick={() => shareReferral('linkedin')}
                className="flex items-center justify-center p-3 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors"
              >
                <ShareIcon className="h-5 w-5 md:mr-2" />
                <span className="hidden md:inline">LinkedIn</span>
              </button>
              <button
                onClick={() => shareReferral('email')}
                className="flex items-center justify-center p-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
              >
                <EnvelopeIcon className="h-5 w-5 md:mr-2" />
                <span className="hidden md:inline">Email</span>
              </button>
              <button
                onClick={() => shareReferral('sms')}
                className="flex items-center justify-center p-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
              >
                <DevicePhoneMobileIcon className="h-5 w-5 md:mr-2" />
                <span className="hidden md:inline">SMS</span>
              </button>
            </div>
          </div>
        </div>

        {/* Reward Structure */}
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
          <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
            How You Earn
          </h2>
          
          <div className="space-y-4">
            {rewards.map((reward) => (
              <div key={reward.id} className="flex items-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <div className="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg mr-4">
                  <reward.icon className="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div className="flex-1">
                  <h3 className="font-medium text-gray-900 dark:text-white">
                    {reward.title}
                  </h3>
                  <p className="text-sm text-gray-600 dark:text-gray-400">
                    {reward.description}
                  </p>
                </div>
                <div className="text-right">
                  <span className="text-xl font-bold text-green-600 dark:text-green-400">
                    {reward.reward}
                  </span>
                </div>
              </div>
            ))}
          </div>

          {/* Next Tier Progress */}
          <div className="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg">
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                Progress to Platinum Tier
              </span>
              <span className="text-sm font-bold text-purple-600 dark:text-purple-400">
                {referralStats.totalReferrals}/30
              </span>
            </div>
            <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                className="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full transition-all"
                style={{ width: `${(referralStats.totalReferrals / 30) * 100}%` }}
              ></div>
            </div>
            <p className="text-xs text-gray-600 dark:text-gray-400 mt-2">
              {30 - referralStats.totalReferrals} more referrals to unlock Platinum benefits
            </p>
          </div>
        </div>
      </div>

      {/* Recent Referrals */}
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
          Recent Referrals
        </h2>
        
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-gray-200 dark:border-gray-700">
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300 font-medium">Name</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300 font-medium">Email</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300 font-medium">Date</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300 font-medium">Reward</th>
              </tr>
            </thead>
            <tbody>
              {recentReferrals.map((referral) => (
                <tr key={referral.id} className="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td className="py-3 px-4 font-medium text-gray-900 dark:text-white">
                    {referral.name}
                  </td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-400">
                    {referral.email}
                  </td>
                  <td className="py-3 px-4">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(referral.status)}`}>
                      {referral.status.replace('_', ' ')}
                    </span>
                  </td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-400">
                    {new Date(referral.date).toLocaleDateString()}
                  </td>
                  <td className="py-3 px-4">
                    {referral.reward > 0 ? (
                      <span className="text-green-600 dark:text-green-400 font-medium">
                        +${referral.reward}
                      </span>
                    ) : (
                      <span className="text-gray-400">-</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Leaderboard */}
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
          Top Referrers This Month
        </h2>
        
        <div className="space-y-4">
          {leaderboard.map((person, index) => (
            <motion.div
              key={index}
              whileHover={{ x: 5 }}
              className={`flex items-center justify-between p-4 rounded-lg transition-all ${
                person.name === 'You' 
                  ? 'bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border-2 border-blue-200 dark:border-blue-800' 
                  : 'bg-gray-50 dark:bg-gray-900'
              }`}
            >
              <div className="flex items-center space-x-4">
                <div className={`flex items-center justify-center w-10 h-10 rounded-full font-bold ${
                  person.rank === 1 ? 'bg-yellow-500 text-white' :
                  person.rank === 2 ? 'bg-gray-400 text-white' :
                  person.rank === 3 ? 'bg-amber-600 text-white' :
                  'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                }`}>
                  {person.rank <= 3 ? (
                    <StarIcon className="h-5 w-5" />
                  ) : (
                    person.rank
                  )}
                </div>
                
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                    {person.avatar}
                  </div>
                  <div>
                    <h3 className={`font-medium ${person.name === 'You' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white'}`}>
                      {person.name}
                    </h3>
                    <span className={`px-2 py-1 text-xs font-medium rounded ${getTierColor(person.tier)}`}>
                      {person.tier}
                    </span>
                  </div>
                </div>
              </div>
              
              <div className="text-right">
                <div className="text-lg font-bold text-gray-900 dark:text-white">
                  {person.referrals} referrals
                </div>
                <div className="text-sm text-green-600 dark:text-green-400">
                  ${person.earnings} earned
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default ReferralSystem;