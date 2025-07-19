import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import { dashboardAPI, healthAPI } from '../../services/api';
import LoadingSpinner from '../../components/LoadingSpinner';
import Button from '../../components/Button';
import {
  ChartBarIcon,
  UsersIcon,
  CurrencyDollarIcon,
  ShoppingBagIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  EyeIcon,
  PlayIcon,
} from '@heroicons/react/24/outline';

const DashboardHome = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState(null);
  const [healthData, setHealthData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      const [healthResponse] = await Promise.all([
        healthAPI.checkHealth(),
        // dashboardAPI.getStats() // Uncomment when backend implements this
      ]);
      
      setHealthData(healthResponse.data);
      
      // Mock stats for now
      setStats({
        totalRevenue: 12450,
        totalUsers: 1234,
        totalOrders: 89,
        conversionRate: 2.4,
        revenueChange: 12.5,
        usersChange: 8.2,
        ordersChange: -2.1,
        conversionChange: 5.3,
      });
    } catch (error) {
      console.error('Failed to fetch dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  const quickActions = [
    {
      title: 'Create Post',
      description: 'Schedule a new social media post',
      icon: ChartBarIcon,
      href: '/dashboard/social-media',
      color: 'bg-blue-500',
    },
    {
      title: 'Add Product',
      description: 'Add a new product to your store',
      icon: ShoppingBagIcon,
      href: '/dashboard/ecommerce',
      color: 'bg-green-500',
    },
    {
      title: 'Create Course',
      description: 'Start building a new course',
      icon: PlayIcon,
      href: '/dashboard/courses',
      color: 'bg-purple-500',
    },
    {
      title: 'View Analytics',
      description: 'Check your performance metrics',
      icon: EyeIcon,
      href: '/dashboard/analytics',
      color: 'bg-orange-500',
    },
  ];

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <LoadingSpinner size="xl" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Welcome Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
      >
        <div className="bg-gradient-welcome rounded-lg p-6 text-primary border border-gray-200 dark:border-gray-700 shadow-lg">
          <h1 className="text-2xl font-bold mb-2">
            Welcome back, {user?.name || 'Creator'}! 👋
          </h1>
          <p className="text-secondary">
            Here's what's happening with your business today.
          </p>
        </div>
      </motion.div>

      {/* Stats Grid */}
      {stats && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.1 }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
        >
          <div className="bg-surface-elevated rounded-lg p-6 shadow-sm">
            <div className="flex items-center">
              <CurrencyDollarIcon className="w-8 h-8 accent-emerald" />
              <div className="ml-4">
                <p className="text-sm text-secondary">Total Revenue</p>
                <p className="text-2xl font-bold text-primary">
                  ${stats.totalRevenue.toLocaleString()}
                </p>
                <div className="flex items-center mt-1">
                  <ArrowUpIcon className="w-4 h-4 accent-emerald" />
                  <span className="text-sm accent-emerald ml-1">
                    +{stats.revenueChange}%
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-surface-elevated rounded-lg p-6 shadow-sm">
            <div className="flex items-center">
              <UsersIcon className="w-8 h-8 accent-purple" />
              <div className="ml-4">
                <p className="text-sm text-secondary">Total Users</p>
                <p className="text-2xl font-bold text-primary">
                  {stats.totalUsers.toLocaleString()}
                </p>
                <div className="flex items-center mt-1">
                  <ArrowUpIcon className="w-4 h-4 accent-emerald" />
                  <span className="text-sm accent-emerald ml-1">
                    +{stats.usersChange}%
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-surface-elevated rounded-lg p-6 shadow-sm">
            <div className="flex items-center">
              <ShoppingBagIcon className="w-8 h-8 accent-amber" />
              <div className="ml-4">
                <p className="text-sm text-secondary">Total Orders</p>
                <p className="text-2xl font-bold text-primary">
                  {stats.totalOrders}
                </p>
                <div className="flex items-center mt-1">
                  <ArrowDownIcon className="w-4 h-4 accent-red" />
                  <span className="text-sm accent-red ml-1">
                    {stats.ordersChange}%
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-surface-elevated rounded-lg p-6 shadow-sm">
            <div className="flex items-center">
              <ChartBarIcon className="w-8 h-8 accent-amber" />
              <div className="ml-4">
                <p className="text-sm text-secondary">Conversion Rate</p>
                <p className="text-2xl font-bold text-primary">
                  {stats.conversionRate}%
                </p>
                <div className="flex items-center mt-1">
                  <ArrowUpIcon className="w-4 h-4 accent-emerald" />
                  <span className="text-sm accent-emerald ml-1">
                    +{stats.conversionChange}%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      )}

      {/* Quick Actions */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.2 }}
      >
        <h2 className="text-lg font-semibold text-primary mb-4">
          Quick Actions
        </h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {quickActions.map((action, index) => (
            <Link key={action.title} to={action.href}>
              <motion.div
                whileHover={{ scale: 1.02 }}
                whileTap={{ scale: 0.98 }}
                className="bg-surface rounded-lg p-4 shadow-sm hover:shadow-md transition-all cursor-pointer"
              >
                <div className={`${action.color} w-10 h-10 rounded-lg flex items-center justify-center mb-3`}>
                  <action.icon className="w-5 h-5 text-white" />
                </div>
                <h3 className="font-medium text-primary mb-1">
                  {action.title}
                </h3>
                <p className="text-sm text-secondary">
                  {action.description}
                </p>
              </motion.div>
            </Link>
          ))}
        </div>
      </motion.div>

      {/* System Health */}
      {healthData && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.3 }}
        >
          <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            System Status
          </h2>
          <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <div>
                <h3 className="text-lg font-medium text-gray-900 dark:text-white">
                  Platform Health
                </h3>
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  All systems operational
                </p>
              </div>
              <div className="flex items-center">
                <div className="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span className="text-sm text-green-600 dark:text-green-400 font-medium">
                  {healthData.data?.status}
                </span>
              </div>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {healthData.data?.services && Object.entries(healthData.data.services).map(([service, status]) => (
                <div key={service} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <span className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                    {service}
                  </span>
                  <span className={`text-xs px-2 py-1 rounded-full ${
                    status === 'healthy' 
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                      : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                  }`}>
                    {status}
                  </span>
                </div>
              ))}
            </div>

            {healthData.data?.features && (
              <div className="mt-4">
                <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-2">
                  Available Features
                </h4>
                <div className="flex flex-wrap gap-2">
                  {Object.entries(healthData.data.features).map(([feature, enabled]) => (
                    enabled && (
                      <span key={feature} className="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 rounded-full">
                        {feature.replace(/_/g, ' ')}
                      </span>
                    )
                  ))}
                </div>
              </div>
            )}
          </div>
        </motion.div>
      )}
    </div>
  );
};

export default DashboardHome;