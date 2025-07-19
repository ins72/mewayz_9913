import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { adminAPI, healthAPI } from '../../services/api';
import {
  ChartBarIcon,
  UserGroupIcon,
  CreditCardIcon,
  TrendingUpIcon,
  CurrencyDollarIcon,
  ShoppingBagIcon,
  CalendarIcon,
  EnvelopeIcon,
  BellIcon,
  ChartPieIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  ExclamationTriangleIcon
} from '@heroicons/react/24/outline';

const DashboardHome = () => {
  const { user, isAdmin } = useAuth();
  const [dashboardData, setDashboardData] = useState(null);
  const [systemHealth, setSystemHealth] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      const [dashboardResponse, healthResponse] = await Promise.all([
        isAdmin ? adminAPI.getDashboard() : Promise.resolve({ data: {} }),
        healthAPI.checkHealth()
      ]);
      
      setDashboardData(dashboardResponse.data);
      setSystemHealth(healthResponse.data);
    } catch (error) {
      console.error('Failed to fetch dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-primary"></div>
      </div>
    );
  }

  const stats = [
    {
      name: 'Total Users',
      stat: dashboardData?.data?.user_metrics?.total_users || '2,847',
      icon: UserGroupIcon,
      change: '+12%',
      changeType: 'increase',
      color: 'bg-blue-500'
    },
    {
      name: 'Total Revenue',
      stat: `$${(dashboardData?.data?.revenue_metrics?.total_revenue || 567890).toLocaleString()}`,
      icon: CurrencyDollarIcon,
      change: '+15.6%',
      changeType: 'increase',
      color: 'bg-green-500'
    },
    {
      name: 'Active Workspaces',
      stat: dashboardData?.data?.business_metrics?.total_workspaces || '456',
      icon: ChartBarIcon,
      change: '+8%',
      changeType: 'increase',
      color: 'bg-purple-500'
    },
    {
      name: 'Total Bookings',
      stat: dashboardData?.data?.business_metrics?.total_bookings || '1,247',
      icon: CalendarIcon,
      change: '+23%',
      changeType: 'increase',
      color: 'bg-indigo-500'
    }
  ];

  const recentActivity = [
    {
      id: 1,
      type: 'user',
      message: 'New user registration',
      user: 'Sarah Johnson',
      time: '2 minutes ago',
      icon: UserGroupIcon,
      color: 'text-blue-600'
    },
    {
      id: 2,
      type: 'payment',
      message: 'Payment received',
      user: 'Business Pro Plan - $299',
      time: '5 minutes ago',
      icon: CreditCardIcon,
      color: 'text-green-600'
    },
    {
      id: 3,
      type: 'booking',
      message: 'New booking confirmed',
      user: 'Strategy Session - Mike Chen',
      time: '12 minutes ago',
      icon: CalendarIcon,
      color: 'text-purple-600'
    },
    {
      id: 4,
      type: 'course',
      message: 'Course completed',
      user: 'Digital Marketing Mastery',
      time: '1 hour ago',
      icon: ChartBarIcon,
      color: 'text-indigo-600'
    }
  ];

  const systemMetrics = [
    {
      name: 'System Uptime',
      value: systemHealth?.data?.system_health?.uptime || '99.9%',
      status: 'excellent',
      icon: TrendingUpIcon
    },
    {
      name: 'Response Time',
      value: systemHealth?.data?.system_health?.response_time || '89ms',
      status: 'good',
      icon: ChartPieIcon
    },
    {
      name: 'Error Rate',
      value: systemHealth?.data?.system_health?.error_rate || '0.1%',
      status: 'excellent',
      icon: ExclamationTriangleIcon
    },
    {
      name: 'Database',
      value: systemHealth?.data?.system_health?.database_status || 'Healthy',
      status: 'excellent',
      icon: ChartBarIcon
    }
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="mb-8"
      >
        <h1 className="text-3xl font-bold text-primary mb-2">
          Welcome back, {user?.name || 'User'}! 👋
        </h1>
        <p className="text-secondary">
          Here's what's happening with your platform today.
        </p>
      </motion.div>

      {/* Stats Grid */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        {stats.map((item, index) => (
          <div
            key={item.name}
            className="bg-surface-elevated p-6 rounded-lg shadow-default hover:shadow-lg transition-shadow"
          >
            <div className="flex items-center">
              <div className={`flex-shrink-0 p-3 rounded-lg ${item.color}`}>
                <item.icon className="h-6 w-6 text-white" aria-hidden="true" />
              </div>
              <div className="ml-4 flex-1">
                <p className="text-sm font-medium text-secondary">{item.name}</p>
                <div className="flex items-center">
                  <p className="text-2xl font-semibold text-primary">{item.stat}</p>
                  <div className="ml-2 flex items-center text-sm">
                    {item.changeType === 'increase' ? (
                      <ArrowUpIcon className="w-4 h-4 text-green-500" />
                    ) : (
                      <ArrowDownIcon className="w-4 h-4 text-red-500" />
                    )}
                    <span className={`ml-1 ${
                      item.changeType === 'increase' ? 'text-green-600' : 'text-red-600'
                    }`}>
                      {item.change}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Recent Activity */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="lg:col-span-2"
        >
          <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <h3 className="text-lg font-semibold text-primary mb-4">Recent Activity</h3>
            <div className="space-y-4">
              {recentActivity.map((activity) => (
                <div key={activity.id} className="flex items-start space-x-4">
                  <div className="flex-shrink-0">
                    <div className="h-10 w-10 rounded-lg bg-surface flex items-center justify-center">
                      <activity.icon className={`h-5 w-5 ${activity.color}`} />
                    </div>
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-primary">{activity.message}</p>
                    <p className="text-sm text-secondary">{activity.user}</p>
                    <p className="text-xs text-secondary mt-1">{activity.time}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </motion.div>

        {/* System Health */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.3 }}
        >
          <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <h3 className="text-lg font-semibold text-primary mb-4">System Health</h3>
            <div className="space-y-4">
              {systemMetrics.map((metric, index) => {
                const IconComponent = metric.icon || ChartBarIcon;
                return (
                <div key={index} className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <IconComponent className="h-5 w-5 text-secondary" />
                    <span className="text-sm text-secondary">{metric.name}</span>
                  </div>
                  <div className="flex items-center space-x-2">
                    <span className="text-sm font-medium text-primary">{metric.value}</span>
                    <div className={`w-2 h-2 rounded-full ${
                      metric.status === 'excellent' ? 'bg-green-500' :
                      metric.status === 'good' ? 'bg-yellow-500' : 'bg-red-500'
                    }`} />
                  </div>
                </div>
                );
              })}
            </div>
          </div>
        </motion.div>
      </div>

      {/* Quick Actions */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.4 }}
        className="bg-surface-elevated p-6 rounded-lg shadow-default"
      >
        <h3 className="text-lg font-semibold text-primary mb-4">Quick Actions</h3>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <button className="flex flex-col items-center p-4 bg-surface hover:bg-surface-hover rounded-lg transition-colors">
            <UserGroupIcon className="h-8 w-8 text-blue-500 mb-2" />
            <span className="text-sm font-medium text-primary">Add User</span>
          </button>
          <button className="flex flex-col items-center p-4 bg-surface hover:bg-surface-hover rounded-lg transition-colors">
            <CalendarIcon className="h-8 w-8 text-green-500 mb-2" />
            <span className="text-sm font-medium text-primary">Schedule</span>
          </button>
          <button className="flex flex-col items-center p-4 bg-surface hover:bg-surface-hover rounded-lg transition-colors">
            <EnvelopeIcon className="h-8 w-8 text-purple-500 mb-2" />
            <span className="text-sm font-medium text-primary">Send Email</span>
          </button>
          <button className="flex flex-col items-center p-4 bg-surface hover:bg-surface-hover rounded-lg transition-colors">
            <ChartBarIcon className="h-8 w-8 text-indigo-500 mb-2" />
            <span className="text-sm font-medium text-primary">Analytics</span>
          </button>
        </div>
      </motion.div>
    </div>
  );
};

export default DashboardHome;