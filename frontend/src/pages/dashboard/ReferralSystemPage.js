import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  UserPlusIcon,
  GiftIcon,
  ChartBarIcon,
  LinkIcon,
  ShareIcon,
  CopyIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  TrophyIcon,
  UsersIcon,
  EnvelopeIcon,
  TagIcon
} from '@heroicons/react/24/outline';
import ReferralSystem from '../../components/growth/ReferralSystem';

const ReferralSystemPage = () => {
  const [viewMode, setViewMode] = useState('dashboard'); // dashboard, referrals, rewards, settings
  const [copied, setCopied] = useState(false);

  const referralCode = 'REF-MW-2025';
  const referralLink = `https://app.mewayz.com/register?ref=${referralCode}`;

  const stats = [
    {
      name: 'Total Referrals',
      value: '47',
      change: '+12 this month',
      changeType: 'positive',
      icon: UserPlusIcon
    },
    {
      name: 'Successful Conversions',
      value: '23',
      change: '48.9% rate',
      changeType: 'positive',
      icon: CheckCircleIcon
    },
    {
      name: 'Earnings this month',
      value: '$1,150',
      change: '+$340 vs last month',
      changeType: 'positive',
      icon: CurrencyDollarIcon
    },
    {
      name: 'Tier Status',
      value: 'Gold',
      change: 'Next: Platinum (7 refs)',
      changeType: 'positive',
      icon: TrophyIcon
    }
  ];

  const recentReferrals = [
    {
      id: 1,
      name: 'Sarah Johnson',
      email: 'sarah@example.com',
      date: '2025-01-15',
      status: 'converted',
      reward: '$50',
      plan: 'Pro'
    },
    {
      id: 2,
      name: 'Mike Chen',
      email: 'mike@example.com',
      date: '2025-01-12',
      status: 'pending',
      reward: '$25',
      plan: 'Basic'
    },
    {
      id: 3,
      name: 'Lisa Davis',
      email: 'lisa@example.com', 
      date: '2025-01-10',
      status: 'converted',
      reward: '$100',
      plan: 'Enterprise'
    },
    {
      id: 4,
      name: 'Tom Wilson',
      email: 'tom@example.com',
      date: '2025-01-08',
      status: 'converted',
      reward: '$50',
      plan: 'Pro'
    }
  ];

  const tierBenefits = [
    {
      tier: 'Bronze',
      minReferrals: 0,
      commission: '20%',
      bonuses: ['$25 signup bonus', 'Basic analytics'],
      color: 'bg-orange-100 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400'
    },
    {
      tier: 'Silver',
      minReferrals: 5,
      commission: '25%',
      bonuses: ['$35 signup bonus', 'Priority support', 'Custom landing page'],
      color: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
    },
    {
      tier: 'Gold',
      minReferrals: 15,
      commission: '30%',
      bonuses: ['$50 signup bonus', 'Dedicated manager', 'Advanced analytics'],
      color: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
      current: true
    },
    {
      tier: 'Platinum',
      minReferrals: 30,
      commission: '35%',
      bonuses: ['$75 signup bonus', 'White-label options', 'Custom integrations'],
      color: 'bg-purple-100 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400'
    }
  ];

  const copyReferralLink = () => {
    navigator.clipboard.writeText(referralLink);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const shareOptions = [
    {
      name: 'Email',
      icon: EnvelopeIcon,
      action: () => window.open(`mailto:?subject=Join Mewayz Platform&body=Check out this amazing platform: ${referralLink}`)
    },
    {
      name: 'Twitter',
      icon: ShareIcon,
      action: () => window.open(`https://twitter.com/intent/tweet?text=Check out Mewayz Platform!&url=${referralLink}`)
    },
    {
      name: 'LinkedIn',
      icon: ShareIcon,
      action: () => window.open(`https://linkedin.com/sharing/share-offsite/?url=${referralLink}`)
    }
  ];

  const renderDashboard = () => (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <motion.div
            key={stat.name}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <stat.icon className="h-8 w-8 text-blue-600" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                  {stat.name}
                </p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {stat.value}
                </p>
                <p className="text-sm text-green-600 dark:text-green-400">
                  {stat.change}
                </p>
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Referral Link Section */}
      <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Your Referral Link
        </h3>
        <div className="flex items-center space-x-4 mb-4">
          <div className="flex-1">
            <div className="flex">
              <input
                type="text"
                value={referralLink}
                readOnly
                className="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"
              />
              <button
                onClick={copyReferralLink}
                className={`px-4 py-2 rounded-r-lg transition-colors ${
                  copied
                    ? 'bg-green-600 text-white'
                    : 'bg-blue-600 text-white hover:bg-blue-700'
                }`}
              >
                {copied ? (
                  <CheckCircleIcon className="h-5 w-5" />
                ) : (
                  <CopyIcon className="h-5 w-5" />
                )}
              </button>
            </div>
          </div>
        </div>

        <div className="flex items-center space-x-3">
          <span className="text-sm text-gray-600 dark:text-gray-400">Share via:</span>
          {shareOptions.map((option) => (
            <button
              key={option.name}
              onClick={option.action}
              className="p-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              title={option.name}
            >
              <option.icon className="h-4 w-4 text-gray-600 dark:text-gray-400" />
            </button>
          ))}
        </div>
      </div>

      {/* Current Tier & Progress */}
      <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          Tier Progress
        </h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {tierBenefits.map((tier) => (
            <div
              key={tier.tier}
              className={`p-4 rounded-lg border-2 ${
                tier.current
                  ? 'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/10'
                  : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50'
              }`}
            >
              <div className="text-center">
                <div className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-2 ${tier.color}`}>
                  {tier.current && <TrophyIcon className="h-4 w-4 mr-1" />}
                  {tier.tier}
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-400 mb-1">
                  {tier.minReferrals}+ referrals
                </p>
                <p className="text-lg font-bold text-gray-900 dark:text-white mb-3">
                  {tier.commission} commission
                </p>
                <div className="space-y-1">
                  {tier.bonuses.map((bonus, index) => (
                    <p key={index} className="text-xs text-gray-600 dark:text-gray-400">
                      {bonus}
                    </p>
                  ))}
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="mt-6">
          <div className="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
            <span>Current progress to Platinum</span>
            <span>23/30 referrals</span>
          </div>
          <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div
              className="bg-yellow-400 h-2 rounded-full"
              style={{ width: `${(23/30) * 100}%` }}
            ></div>
          </div>
        </div>
      </div>

      {/* Recent Referrals */}
      <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
              Recent Referrals
            </h3>
            <button
              onClick={() => setViewMode('referrals')}
              className="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
            >
              View all
            </button>
          </div>
        </div>
        <div className="divide-y divide-gray-200 dark:divide-gray-700">
          {recentReferrals.slice(0, 4).map((referral) => (
            <div key={referral.id} className="p-6">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <div className="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                    <UsersIcon className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div>
                    <p className="font-medium text-gray-900 dark:text-white">
                      {referral.name}
                    </p>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      {referral.email} • {referral.date}
                    </p>
                  </div>
                </div>
                <div className="text-right">
                  <div className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                    referral.status === 'converted'
                      ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                      : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                  }`}>
                    {referral.status}
                  </div>
                  <p className="text-sm font-medium text-gray-900 dark:text-white mt-1">
                    {referral.reward}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderReferrals = () => (
    <div className="space-y-6">
      <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            All Referrals
          </h3>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead className="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Contact
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Date Referred
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Status
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Plan
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Reward
                </th>
              </tr>
            </thead>
            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              {recentReferrals.map((referral) => (
                <tr key={referral.id}>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div className="text-sm font-medium text-gray-900 dark:text-white">
                        {referral.name}
                      </div>
                      <div className="text-sm text-gray-500 dark:text-gray-400">
                        {referral.email}
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {referral.date}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                      referral.status === 'converted'
                        ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                    }`}>
                      {referral.status}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {referral.plan}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                    {referral.reward}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );

  const renderRewards = () => (
    <div className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
          <div className="text-center">
            <CurrencyDollarIcon className="h-12 w-12 text-green-600 mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
              Total Earnings
            </h3>
            <p className="text-3xl font-bold text-green-600">$3,425</p>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">
              All-time earnings
            </p>
          </div>
        </div>

        <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
          <div className="text-center">
            <GiftIcon className="h-12 w-12 text-blue-600 mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
              Pending Rewards
            </h3>
            <p className="text-3xl font-bold text-blue-600">$425</p>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">
              Will be paid next cycle
            </p>
          </div>
        </div>

        <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
          <div className="text-center">
            <TrophyIcon className="h-12 w-12 text-yellow-600 mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
              Bonus Earned
            </h3>
            <p className="text-3xl font-bold text-yellow-600">$750</p>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">
              Tier bonuses received
            </p>
          </div>
        </div>
      </div>

      <ReferralSystem />
    </div>
  );

  return (
    <div className="p-6">
      <div className="mb-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
              Referral Program
            </h1>
            <p className="text-gray-600 dark:text-gray-400 mt-1">
              Earn rewards by referring friends and colleagues to Mewayz
            </p>
          </div>

          <div className="flex items-center space-x-3">
            <button className="flex items-center space-x-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <ShareIcon className="h-5 w-5" />
              <span>Share Link</span>
            </button>
            <button className="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <UserPlusIcon className="h-5 w-5" />
              <span>Invite Friends</span>
            </button>
          </div>
        </div>

        {/* Navigation Tabs */}
        <div className="mt-6 border-b border-gray-200 dark:border-gray-700">
          <nav className="-mb-px flex space-x-8">
            {[
              { key: 'dashboard', label: 'Dashboard', icon: ChartBarIcon },
              { key: 'referrals', label: 'My Referrals', icon: UsersIcon },
              { key: 'rewards', label: 'Rewards', icon: GiftIcon },
              { key: 'settings', label: 'Settings', icon: TagIcon }
            ].map((tab) => (
              <button
                key={tab.key}
                onClick={() => setViewMode(tab.key)}
                className={`flex items-center space-x-2 py-2 px-1 border-b-2 font-medium text-sm ${
                  viewMode === tab.key
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300'
                }`}
              >
                <tab.icon className="h-5 w-5" />
                <span>{tab.label}</span>
              </button>
            ))}
          </nav>
        </div>
      </div>

      {/* Content Area */}
      <motion.div
        key={viewMode}
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.2 }}
      >
        {viewMode === 'dashboard' && renderDashboard()}
        {viewMode === 'referrals' && renderReferrals()}
        {viewMode === 'rewards' && renderRewards()}
        {viewMode === 'settings' && (
          <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Referral Settings
            </h3>
            <p className="text-gray-600 dark:text-gray-400">
              Settings and preferences for your referral program
            </p>
          </div>
        )}
      </motion.div>
    </div>
  );
};

export default ReferralSystemPage;