import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { dashboardAPI, healthAPI } from '../../services/api';
import {
  ChartBarIcon,
  UsersIcon,
  ShoppingBagIcon,
  CurrencyDollarIcon,
  ArrowUpIcon,
  ArrowDownIcon,
} from '@heroicons/react/24/outline';

const DashboardHome = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const [systemHealth, setSystemHealth] = useState(null);

  useEffect(() => {
    loadDashboardData();
    checkSystemHealth();
  }, []);

  const loadDashboardData = async () => {
    try {
      // For now, we'll use mock data since the Laravel endpoints might not be fully implemented
      setStats({
        totalRevenue: 12450,
        revenueGrowth: 12.5,
        totalUsers: 1240,
        userGrowth: 8.2,
        totalOrders: 156,
        orderGrowth: -2.4,
        conversionRate: 3.2,
        conversionGrowth: 5.1,
      });
    } catch (error) {
      console.error('Failed to load dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  const checkSystemHealth = async () => {
    try {
      const response = await healthAPI.checkHealth();
      setSystemHealth(response.data);
    } catch (error) {
      console.error('Failed to check system health:', error);
    }
  };

  const StatCard = ({ title, value, growth, icon: Icon, color = 'accent-primary' }) => {
    const isPositive = growth > 0;
    
    return (
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="card-elevated p-6"
      >
        <div className="flex items-center justify-between">
          <div>
            <p className="text-sm font-medium text-secondary">{title}</p>
            <p className="text-3xl font-bold text-primary mt-2">{value}</p>
            <div className="flex items-center mt-2">
              {isPositive ? (
                <ArrowUpIcon className="w-4 h-4 text-accent-success mr-1" />
              ) : (
                <ArrowDownIcon className="w-4 h-4 text-accent-danger mr-1" />
              )}
              <span className={`text-sm font-medium ${
                isPositive ? 'text-accent-success' : 'text-accent-danger'
              }`}>
                {Math.abs(growth)}%
              </span>
              <span className="text-secondary text-sm ml-1">vs last month</span>
            </div>
          </div>
          <div className={`bg-gradient-${color} p-3 rounded-lg`}>
            <Icon className="w-8 h-8 text-white" />
          </div>
        </div>
      </motion.div>
    );
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Welcome Header */}
      <div className="bg-gradient-primary rounded-lg p-6 text-white">
        <h1 className="text-3xl font-bold mb-2">
          Welcome back, {user?.name || 'User'}!
        </h1>
        <p className="text-blue-100">
          Here's what's happening with your business today.
        </p>
      </div>

      {/* System Health Status */}
      {systemHealth && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="card-elevated p-4"
        >
          <div className="flex items-center justify-between">
            <div>
              <h3 className="text-lg font-semibold text-primary">System Status</h3>
              <p className="text-secondary">All systems operational</p>
            </div>
            <div className="flex items-center space-x-2">
              <div className="w-3 h-3 bg-accent-success rounded-full animate-pulse"></div>
              <span className="text-accent-success font-medium">{systemHealth.data?.status}</span>
            </div>
          </div>
          
          {systemHealth.data?.services && (
            <div className="mt-4 grid grid-cols-3 gap-4">
              {Object.entries(systemHealth.data.services).map(([service, status]) => (
                <div key={service} className="flex items-center space-x-2">
                  <div className={`w-2 h-2 rounded-full ${
                    status === 'healthy' ? 'bg-accent-success' : 'bg-accent-danger'
                  }`}></div>
                  <span className="text-sm text-secondary capitalize">{service}</span>
                </div>
              ))}
            </div>
          )}
        </motion.div>
      )}

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Total Revenue"
          value={`$${stats.totalRevenue.toLocaleString()}`}
          growth={stats.revenueGrowth}
          icon={CurrencyDollarIcon}
          color="primary"
        />
        
        <StatCard
          title="Total Users"
          value={stats.totalUsers.toLocaleString()}
          growth={stats.userGrowth}
          icon={UsersIcon}
          color="success"
        />
        
        <StatCard
          title="Total Orders"
          value={stats.totalOrders.toLocaleString()}
          growth={stats.orderGrowth}
          icon={ShoppingBagIcon}
          color="warning"
        />
        
        <StatCard
          title="Conversion Rate"
          value={`${stats.conversionRate}%`}
          growth={stats.conversionGrowth}
          icon={ChartBarIcon}
          color="primary"
        />
      </div>

      {/* Quick Actions */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.3 }}
        className="card-elevated p-6"
      >
        <h3 className="text-xl font-semibold text-primary mb-4">Quick Actions</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <button className="p-4 border border-default rounded-lg hover-surface transition-colors text-left">
            <ChartBarIcon className="w-8 h-8 text-accent-primary mb-2" />
            <h4 className="font-medium text-primary">Create Post</h4>
            <p className="text-sm text-secondary">Share content on social media</p>
          </button>
          
          <button className="p-4 border border-default rounded-lg hover-surface transition-colors text-left">
            <ShoppingBagIcon className="w-8 h-8 text-accent-primary mb-2" />
            <h4 className="font-medium text-primary">Add Product</h4>
            <p className="text-sm text-secondary">Add new product to store</p>
          </button>
          
          <button className="p-4 border border-default rounded-lg hover-surface transition-colors text-left">
            <UsersIcon className="w-8 h-8 text-accent-primary mb-2" />
            <h4 className="font-medium text-primary">View Analytics</h4>
            <p className="text-sm text-secondary">Check your performance</p>
          </button>
        </div>
      </motion.div>

      {/* Recent Activity */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.4 }}
        className="card-elevated p-6"
      >
        <h3 className="text-xl font-semibold text-primary mb-4">Recent Activity</h3>
        <div className="space-y-4">
          {[
            { action: 'New user registered', time: '2 minutes ago', type: 'user' },
            { action: 'Product order received', time: '15 minutes ago', type: 'order' },
            { action: 'Social media post published', time: '1 hour ago', type: 'social' },
            { action: 'Email campaign sent', time: '3 hours ago', type: 'email' },
          ].map((activity, index) => (
            <div key={index} className="flex items-center space-x-3 p-3 hover-surface rounded-lg transition-colors">
              <div className="w-2 h-2 bg-accent-primary rounded-full"></div>
              <div className="flex-1">
                <p className="text-primary font-medium">{activity.action}</p>
                <p className="text-secondary text-sm">{activity.time}</p>
              </div>
            </div>
          ))}
        </div>
      </motion.div>
    </div>
  );
};

export default DashboardHome;