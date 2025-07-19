import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import { adminAPI } from '../../services/api';
import {
  ShieldCheckIcon,
  UsersIcon,
  ServerIcon,
  ChartBarIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  DocumentArrowDownIcon,
  Cog6ToothIcon,
  BellIcon,
  CreditCardIcon,
  BanknotesIcon,
  GlobeAltIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  CurrencyDollarIcon,
  UserPlusIcon,
  UserMinusIcon,
  DocumentTextIcon,
  FolderOpenIcon,
  DatabaseIcon,
  CloudIcon,
  WifiIcon,
  CpuChipIcon,
  CircleStackIcon,
  ShoppingBagIcon,
  AcademicCapIcon,
  EnvelopeIcon,
  ChatBubbleLeftRightIcon,
  PhotoIcon,
  VideoCameraIcon
} from '@heroicons/react/24/outline';

const ComprehensiveAdminDashboard = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeTab, setActiveTab] = useState('overview');
  const [loading, setLoading] = useState(false);

  // Comprehensive admin data
  const [adminData, setAdminData] = useState({
    overview: {
      totalUsers: 15847,
      userGrowth: 12.5,
      activeUsers: 8923,
      activeUserGrowth: 18.7,
      totalRevenue: 284567.89,
      revenueGrowth: 31.2,
      subscriptionRevenue: 156780.45,
      subscriptionGrowth: 28.9,
      systemHealth: 99.8,
      responseTime: 89,
      serverUptime: 99.9,
      apiCalls: 2847593,
      apiCallsGrowth: 15.3
    },
    users: {
      totalUsers: 15847,
      newUsersToday: 147,
      activeUsers24h: 8923,
      premiumUsers: 3456,
      bannedUsers: 12,
      usersByPlan: {
        free: 12391,
        pro: 2134,
        enterprise: 1322
      },
      recentRegistrations: [
        { name: 'Sarah Johnson', email: 'sarah@example.com', plan: 'Pro', registeredAt: '2024-01-15 14:23', country: 'US' },
        { name: 'Mike Chen', email: 'mike@example.com', plan: 'Enterprise', registeredAt: '2024-01-15 13:45', country: 'CA' },
        { name: 'Emily Rodriguez', email: 'emily@example.com', plan: 'Free', registeredAt: '2024-01-15 12:56', country: 'MX' }
      ],
      topUsers: [
        { name: 'Digital Agency Pro', revenue: 12450.89, templates: 67, downloads: 23456 },
        { name: 'Creative Studio XYZ', revenue: 9876.54, templates: 43, downloads: 18932 },
        { name: 'Marketing Experts', revenue: 8765.43, templates: 52, downloads: 16578 }
      ]
    },
    revenue: {
      totalRevenue: 284567.89,
      monthlyRecurring: 156780.45,
      oneTimePayments: 89456.78,
      templateSales: 38330.66,
      averageOrderValue: 89.45,
      revenueByPlan: {
        pro: 134567.89,
        enterprise: 127890.45,
        templates: 38330.66
      },
      recentTransactions: [
        { user: 'Sarah Johnson', amount: 29.99, type: 'Pro Subscription', status: 'completed', date: '2024-01-15' },
        { user: 'TechStart Inc.', amount: 299.99, type: 'Enterprise Plan', status: 'completed', date: '2024-01-15' },
        { user: 'Creative Studio', amount: 89.99, type: 'Template Purchase', status: 'pending', date: '2024-01-15' }
      ]
    },
    system: {
      serverHealth: {
        cpu: 45,
        memory: 62,
        disk: 38,
        network: 23
      },
      apiMetrics: {
        totalCalls: 2847593,
        callsToday: 45782,
        averageResponseTime: 89,
        errorRate: 0.2,
        topEndpoints: [
          { endpoint: '/api/ai/services', calls: 156789, avgTime: 234 },
          { endpoint: '/api/bio-sites', calls: 89456, avgTime: 145 },
          { endpoint: '/api/templates', calls: 67832, avgTime: 198 }
        ]
      },
      errors: [
        { time: '14:23:45', level: 'error', message: 'Database connection timeout', count: 3 },
        { time: '13:56:21', level: 'warning', message: 'High memory usage detected', count: 1 },
        { time: '12:45:33', level: 'info', message: 'Scheduled backup completed', count: 1 }
      ]
    },
    content: {
      totalTemplates: 2847,
      activeTemplates: 2456,
      pendingReview: 23,
      rejectedTemplates: 45,
      totalDownloads: 456789,
      totalUploads: 89456,
      storageUsed: 1.2, // TB
      contentByCategory: {
        website: 1234,
        email: 567,
        social: 432,
        mobile: 298,
        other: 316
      },
      recentUploads: [
        { title: 'Modern SaaS Landing Page', author: 'Design Pro', category: 'Website', status: 'approved' },
        { title: 'Email Marketing Kit', author: 'Marketing Expert', category: 'Email', status: 'pending' },
        { title: 'Social Media Pack', author: 'Creative Studio', category: 'Social', status: 'approved' }
      ]
    },
    features: {
      aiGeneration: {
        totalRequests: 789456,
        successRate: 98.7,
        averageTime: 2.3,
        topModels: ['GPT-4', 'Claude', 'Gemini']
      },
      socialMedia: {
        connectedAccounts: 45789,
        scheduledPosts: 23456,
        publishedPosts: 189456
      },
      analytics: {
        activeReports: 15678,
        dataProcessed: 234, // GB
        insightsGenerated: 67890
      }
    }
  });

  const getHealthColor = (value) => {
    if (value >= 95) return 'text-green-500';
    if (value >= 85) return 'text-yellow-500';
    return 'text-red-500';
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
      case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
      case 'failed': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
  };

  if (!user || user.role !== 'admin') {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <ShieldCheckIcon className="h-16 w-16 text-red-500 mx-auto mb-4" />
          <h3 className="text-lg font-medium text-primary">Access Denied</h3>
          <p className="text-secondary">Admin privileges required to access this page.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Admin Header */}
      <div className="bg-gradient-to-r from-red-600 via-orange-600 to-yellow-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold mb-2">Admin Dashboard</h1>
            <p className="text-white/80">Comprehensive platform management and monitoring</p>
          </div>
          <div className="flex items-center space-x-4">
            <div className="text-center">
              <div className="text-2xl font-bold">{adminData.overview.totalUsers.toLocaleString()}</div>
              <div className="text-sm text-white/70">Total Users</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold">${adminData.overview.totalRevenue.toLocaleString()}</div>
              <div className="text-sm text-white/70">Total Revenue</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold">{adminData.overview.systemHealth}%</div>
              <div className="text-sm text-white/70">System Health</div>
            </div>
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        {[
          { name: 'Manage Users', icon: UsersIcon, color: 'bg-blue-500', action: () => setActiveTab('users') },
          { name: 'Revenue Reports', icon: CurrencyDollarIcon, color: 'bg-green-500', action: () => setActiveTab('revenue') },
          { name: 'System Status', icon: ServerIcon, color: 'bg-purple-500', action: () => setActiveTab('system') },
          { name: 'Content Review', icon: DocumentTextIcon, color: 'bg-orange-500', action: () => setActiveTab('content') },
          { name: 'Feature Usage', icon: ChartBarIcon, color: 'bg-pink-500', action: () => setActiveTab('features') },
          { name: 'Settings', icon: Cog6ToothIcon, color: 'bg-gray-500', action: () => setActiveTab('settings') }
        ].map((action) => (
          <button
            key={action.name}
            onClick={action.action}
            className="p-4 bg-surface-elevated rounded-xl shadow-default hover:shadow-lg transition-all text-left"
          >
            <div className={`w-10 h-10 rounded-lg ${action.color} flex items-center justify-center mb-3`}>
              <action.icon className="h-5 w-5 text-white" />
            </div>
            <p className="text-sm font-medium text-primary">{action.name}</p>
          </button>
        ))}
      </div>

      {/* Main Content Tabs */}
      <div className="bg-surface-elevated rounded-xl shadow-default">
        <div className="border-b border-default">
          <nav className="flex space-x-8 px-6">
            {[
              { id: 'overview', name: 'Platform Overview', icon: ChartBarIcon },
              { id: 'users', name: 'User Management', icon: UsersIcon },
              { id: 'revenue', name: 'Revenue Analytics', icon: CurrencyDollarIcon },
              { id: 'system', name: 'System Monitoring', icon: ServerIcon },
              { id: 'content', name: 'Content Management', icon: DocumentTextIcon },
              { id: 'features', name: 'Feature Analytics', icon: DatabaseIcon }
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                  activeTab === tab.id
                    ? 'border-red-500 text-red-600 dark:text-red-400'
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
          {activeTab === 'overview' && (
            <div className="space-y-6">
              {/* Platform Metrics */}
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Total Users</p>
                      <p className="text-2xl font-bold text-primary">{adminData.overview.totalUsers.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{adminData.overview.userGrowth}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-blue-100 rounded-xl dark:bg-blue-900">
                      <UsersIcon className="h-6 w-6 text-blue-600 dark:text-blue-400" />
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
                      <p className="text-sm font-medium text-secondary">Monthly Revenue</p>
                      <p className="text-2xl font-bold text-primary">${adminData.overview.totalRevenue.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{adminData.overview.revenueGrowth}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-green-100 rounded-xl dark:bg-green-900">
                      <CurrencyDollarIcon className="h-6 w-6 text-green-600 dark:text-green-400" />
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
                      <p className="text-sm font-medium text-secondary">System Health</p>
                      <p className="text-2xl font-bold text-primary">{adminData.overview.systemHealth}%</p>
                      <div className="flex items-center mt-1">
                        <CheckCircleIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">All systems operational</span>
                      </div>
                    </div>
                    <div className="p-3 bg-purple-100 rounded-xl dark:bg-purple-900">
                      <ServerIcon className="h-6 w-6 text-purple-600 dark:text-purple-400" />
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
                      <p className="text-sm font-medium text-secondary">API Calls</p>
                      <p className="text-2xl font-bold text-primary">{adminData.overview.apiCalls.toLocaleString()}</p>
                      <div className="flex items-center mt-1">
                        <ArrowTrendingUpIcon className="h-4 w-4 text-green-500 mr-1" />
                        <span className="text-sm text-green-500">+{adminData.overview.apiCallsGrowth}%</span>
                      </div>
                    </div>
                    <div className="p-3 bg-orange-100 rounded-xl dark:bg-orange-900">
                      <GlobeAltIcon className="h-6 w-6 text-orange-600 dark:text-orange-400" />
                    </div>
                  </div>
                </motion.div>
              </div>

              {/* Platform Activity */}
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Recent Activity</h3>
                  <div className="space-y-4">
                    {[
                      { type: 'user', message: 'New user registration: Sarah Johnson (Pro Plan)', time: '2 minutes ago', icon: UserPlusIcon },
                      { type: 'revenue', message: 'Payment received: $299.99 Enterprise subscription', time: '5 minutes ago', icon: CreditCardIcon },
                      { type: 'content', message: 'Template approved: Modern Dashboard UI Kit', time: '12 minutes ago', icon: CheckCircleIcon },
                      { type: 'system', message: 'System backup completed successfully', time: '1 hour ago', icon: CloudIcon }
                    ].map((activity, index) => (
                      <div key={index} className="flex items-start space-x-3">
                        <div className="p-2 bg-surface-elevated rounded-lg">
                          <activity.icon className="h-4 w-4 text-secondary" />
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="text-sm text-primary">{activity.message}</p>
                          <p className="text-xs text-secondary mt-1">{activity.time}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">System Resources</h3>
                  <div className="space-y-4">
                    {[
                      { name: 'CPU Usage', value: adminData.system.serverHealth.cpu, color: 'blue' },
                      { name: 'Memory Usage', value: adminData.system.serverHealth.memory, color: 'green' },
                      { name: 'Disk Usage', value: adminData.system.serverHealth.disk, color: 'yellow' },
                      { name: 'Network I/O', value: adminData.system.serverHealth.network, color: 'purple' }
                    ].map((resource) => (
                      <div key={resource.name}>
                        <div className="flex justify-between items-center mb-2">
                          <span className="text-sm font-medium text-secondary">{resource.name}</span>
                          <span className="text-sm text-primary">{resource.value}%</span>
                        </div>
                        <div className="w-full bg-surface-elevated rounded-full h-2">
                          <div
                            className={`bg-${resource.color}-500 h-2 rounded-full transition-all duration-300`}
                            style={{ width: `${resource.value}%` }}
                          ></div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'users' && (
            <div className="space-y-6">
              <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold text-primary">User Management</h2>
                <div className="flex space-x-3">
                  <button className="btn btn-secondary">
                    <DocumentArrowDownIcon className="h-4 w-4 mr-2" />
                    Export Users
                  </button>
                  <button className="btn btn-primary">
                    <PlusIcon className="h-4 w-4 mr-2" />
                    Add User
                  </button>
                </div>
              </div>

              {/* User Statistics */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">User Distribution</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-secondary">Free Plan</span>
                      <span className="font-semibold text-primary">{adminData.users.usersByPlan.free.toLocaleString()}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-secondary">Pro Plan</span>
                      <span className="font-semibold text-primary">{adminData.users.usersByPlan.pro.toLocaleString()}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-secondary">Enterprise</span>
                      <span className="font-semibold text-primary">{adminData.users.usersByPlan.enterprise.toLocaleString()}</span>
                    </div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Top Users by Revenue</h3>
                  <div className="space-y-3">
                    {adminData.users.topUsers.map((user, index) => (
                      <div key={index} className="flex justify-between items-center">
                        <div>
                          <p className="text-sm font-medium text-primary">{user.name}</p>
                          <p className="text-xs text-secondary">{user.templates} templates</p>
                        </div>
                        <div className="text-right">
                          <p className="text-sm font-bold text-green-600">${user.revenue.toLocaleString()}</p>
                          <p className="text-xs text-secondary">{user.downloads.toLocaleString()} downloads</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Recent Registrations</h3>
                  <div className="space-y-3">
                    {adminData.users.recentRegistrations.map((user, index) => (
                      <div key={index} className="border-b border-default pb-3 last:border-b-0">
                        <div className="flex justify-between items-start">
                          <div>
                            <p className="text-sm font-medium text-primary">{user.name}</p>
                            <p className="text-xs text-secondary">{user.email}</p>
                            <p className="text-xs text-secondary">{user.registeredAt}</p>
                          </div>
                          <div className="text-right">
                            <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{user.plan}</span>
                            <p className="text-xs text-secondary mt-1">{user.country}</p>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'revenue' && (
            <div className="space-y-6">
              <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold text-primary">Revenue Analytics</h2>
                <button className="btn btn-primary">
                  <DocumentArrowDownIcon className="h-4 w-4 mr-2" />
                  Export Report
                </button>
              </div>

              {/* Revenue Overview */}
              <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Total Revenue</p>
                      <p className="text-2xl font-bold text-primary">${adminData.revenue.totalRevenue.toLocaleString()}</p>
                    </div>
                    <BanknotesIcon className="h-8 w-8 text-green-500" />
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Monthly Recurring</p>
                      <p className="text-2xl font-bold text-primary">${adminData.revenue.monthlyRecurring.toLocaleString()}</p>
                    </div>
                    <CreditCardIcon className="h-8 w-8 text-blue-500" />
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Template Sales</p>
                      <p className="text-2xl font-bold text-primary">${adminData.revenue.templateSales.toLocaleString()}</p>
                    </div>
                    <ShoppingBagIcon className="h-8 w-8 text-purple-500" />
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Avg Order Value</p>
                      <p className="text-2xl font-bold text-primary">${adminData.revenue.averageOrderValue}</p>
                    </div>
                    <ChartBarIcon className="h-8 w-8 text-orange-500" />
                  </div>
                </div>
              </div>

              {/* Recent Transactions */}
              <div className="bg-surface border border-default rounded-xl p-6">
                <h3 className="text-lg font-semibold text-primary mb-4">Recent Transactions</h3>
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b border-default">
                        <th className="text-left py-3 px-4 text-sm font-medium text-secondary">User</th>
                        <th className="text-left py-3 px-4 text-sm font-medium text-secondary">Amount</th>
                        <th className="text-left py-3 px-4 text-sm font-medium text-secondary">Type</th>
                        <th className="text-left py-3 px-4 text-sm font-medium text-secondary">Status</th>
                        <th className="text-left py-3 px-4 text-sm font-medium text-secondary">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      {adminData.revenue.recentTransactions.map((transaction, index) => (
                        <tr key={index} className="border-b border-default hover:bg-surface-hover">
                          <td className="py-3 px-4 text-sm text-primary">{transaction.user}</td>
                          <td className="py-3 px-4 text-sm font-semibold text-green-600">${transaction.amount}</td>
                          <td className="py-3 px-4 text-sm text-secondary">{transaction.type}</td>
                          <td className="py-3 px-4">
                            <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(transaction.status)}`}>
                              {transaction.status}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-sm text-secondary">{transaction.date}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* Other tabs would continue similarly... */}
        </div>
      </div>
    </div>
  );
};

export default ComprehensiveAdminDashboard;