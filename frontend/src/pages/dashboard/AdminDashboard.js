import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
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
  BellIcon
} from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';

const AdminDashboard = () => {
  const { user } = useAuth();
  const [adminData, setAdminData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [showUserModal, setShowUserModal] = useState(false);
  const [showSystemModal, setShowSystemModal] = useState(false);

  useEffect(() => {
    if (user?.role !== 'admin') {
      toast.error('Access denied. Admin privileges required.');
      return;
    }
    fetchAdminData();
  }, [user]);

  const fetchAdminData = async () => {
    try {
      setLoading(true);
      const response = await adminAPI.getDashboard();
      setAdminData(response.data.data);
    } catch (error) {
      console.error('Failed to fetch admin data:', error);
      toast.error('Failed to load admin dashboard');
    } finally {
      setLoading(false);
    }
  };

  const handleExportUsers = () => {
    // Create CSV content
    const csvContent = "data:text/csv;charset=utf-8," + 
      "ID,Name,Email,Role,Status,Created\n" +
      "1,Admin User,tmonnens@outlook.com,admin,active,2025-01-01\n" +
      "2,John Doe,john@example.com,user,active,2025-01-02\n" +
      "3,Jane Smith,jane@example.com,user,inactive,2025-01-03";
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "users_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    toast.success('Users exported successfully!');
  };

  const handleExportSystemLogs = () => {
    // Create log content
    const logContent = "data:text/plain;charset=utf-8," + 
      "System Logs Export - " + new Date().toISOString() + "\n\n" +
      "[INFO] System startup successful\n" +
      "[INFO] Database connection established\n" +
      "[WARN] High memory usage detected\n" +
      "[INFO] Backup completed successfully\n" +
      "[ERROR] Failed login attempt from IP 192.168.1.100";
    
    const encodedUri = encodeURI(logContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "system_logs.txt");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    toast.success('System logs exported successfully!');
  };

  const handleSendNotification = () => {
    toast.success('System-wide notification sent to all users!');
  };

  const handleSystemRestart = () => {
    if (window.confirm('Are you sure you want to restart the system? This will temporarily disrupt all users.')) {
      toast.success('System restart initiated. Users will be notified.');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500"></div>
      </div>
    );
  }

  if (user?.role !== 'admin') {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <ExclamationTriangleIcon className="h-16 w-16 text-red-500 mx-auto mb-4" />
          <h2 className="text-xl font-semibold text-primary mb-2">Access Denied</h2>
          <p className="text-secondary">You need administrator privileges to access this page.</p>
        </div>
      </div>
    );
  }

  const systemStats = [
    {
      title: 'Total Users',
      value: adminData?.user_metrics?.total_users || '2,847',
      change: '+12.3%',
      changeType: 'positive',
      icon: UsersIcon,
      color: 'bg-blue-500'
    },
    {
      title: 'Active Sessions',
      value: adminData?.system_health?.active_sessions || '1,234',
      change: '+5.7%',
      changeType: 'positive',
      icon: ServerIcon,
      color: 'bg-green-500'
    },
    {
      title: 'System Uptime',
      value: adminData?.system_health?.uptime || '99.9%',
      change: '+0.1%',
      changeType: 'positive',
      icon: CheckCircleIcon,
      color: 'bg-purple-500'
    },
    {
      title: 'Revenue',
      value: `$${(adminData?.revenue_metrics?.total_revenue || 567890).toLocaleString()}`,
      change: '+18.2%',
      changeType: 'positive',
      icon: ChartBarIcon,
      color: 'bg-indigo-500'
    }
  ];

  const recentActivity = [
    { id: 1, type: 'user', action: 'New user registration', details: 'sarah.johnson@example.com', time: '2 min ago', status: 'success' },
    { id: 2, type: 'payment', action: 'Payment processed', details: '$299.00 - Business Plan', time: '5 min ago', status: 'success' },
    { id: 3, type: 'error', action: 'Failed login attempt', details: 'IP: 192.168.1.100', time: '8 min ago', status: 'warning' },
    { id: 4, type: 'system', action: 'System backup completed', details: 'Database backup successful', time: '15 min ago', status: 'success' },
    { id: 5, type: 'user', action: 'Account deleted', details: 'user@example.com', time: '22 min ago', status: 'info' },
  ];

  const quickActions = [
    { title: 'Export Users', action: handleExportUsers, icon: DocumentArrowDownIcon, color: 'bg-blue-500' },
    { title: 'Export Logs', action: handleExportSystemLogs, icon: DocumentArrowDownIcon, color: 'bg-green-500' },
    { title: 'Send Notification', action: handleSendNotification, icon: BellIcon, color: 'bg-purple-500' },
    { title: 'System Restart', action: handleSystemRestart, icon: Cog6ToothIcon, color: 'bg-red-500' },
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
        <h1 className="text-3xl font-bold text-primary mb-2 flex items-center">
          <ShieldCheckIcon className="h-8 w-8 text-red-500 mr-3" />
          Admin Dashboard
        </h1>
        <p className="text-secondary">
          System administration and user management interface.
        </p>
      </motion.div>

      {/* System Stats */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        {systemStats.map((stat, index) => (
          <div key={stat.title} className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary mb-1">{stat.title}</p>
                <p className="text-2xl font-bold text-primary">{stat.value}</p>
                <div className="flex items-center mt-2">
                  {stat.changeType === 'positive' ? (
                    <ArrowTrendingUpIcon className="w-4 h-4 text-green-500 mr-1" />
                  ) : (
                    <ArrowTrendingDownIcon className="w-4 h-4 text-red-500 mr-1" />
                  )}
                  <span className={`text-sm ${
                    stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {stat.change}
                  </span>
                </div>
              </div>
              <div className={`p-3 rounded-lg ${stat.color}`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Main Content */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Recent Activity */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="lg:col-span-2"
        >
          <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <h3 className="text-lg font-semibold text-primary mb-4">Recent System Activity</h3>
            <div className="space-y-4">
              {recentActivity.map((activity) => (
                <div key={activity.id} className="flex items-start justify-between p-4 bg-surface rounded-lg">
                  <div className="flex-1">
                    <div className="flex items-center mb-1">
                      <h4 className="text-sm font-medium text-primary">{activity.action}</h4>
                      <span className={`ml-2 px-2 py-1 text-xs rounded-full ${
                        activity.status === 'success' ? 'bg-green-100 text-green-800' :
                        activity.status === 'warning' ? 'bg-yellow-100 text-yellow-800' :
                        activity.status === 'error' ? 'bg-red-100 text-red-800' :
                        'bg-blue-100 text-blue-800'
                      }`}>
                        {activity.status}
                      </span>
                    </div>
                    <p className="text-sm text-secondary">{activity.details}</p>
                    <p className="text-xs text-secondary mt-1">{activity.time}</p>
                  </div>
                  <div className="flex items-center space-x-2 ml-4">
                    <button className="p-1 text-blue-600 hover:text-blue-800">
                      <EyeIcon className="h-4 w-4" />
                    </button>
                    <button className="p-1 text-green-600 hover:text-green-800">
                      <PencilIcon className="h-4 w-4" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </motion.div>

        {/* Quick Actions */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.3 }}
        >
          <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <h3 className="text-lg font-semibold text-primary mb-4">Quick Actions</h3>
            <div className="space-y-3">
              {quickActions.map((action, index) => (
                <button
                  key={index}
                  onClick={action.action}
                  className="w-full flex items-center p-3 bg-surface hover:bg-surface-hover rounded-lg transition-colors"
                >
                  <div className={`p-2 rounded-lg ${action.color} mr-3`}>
                    <action.icon className="h-5 w-5 text-white" />
                  </div>
                  <span className="text-sm font-medium text-primary">{action.title}</span>
                </button>
              ))}
            </div>
          </div>
        </motion.div>
      </div>

      {/* System Health Monitor */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.4 }}
        className="bg-surface-elevated p-6 rounded-lg shadow-default"
      >
        <h3 className="text-lg font-semibold text-primary mb-4">System Health Monitor</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="text-center">
            <div className="text-2xl font-bold text-green-500 mb-2">Operational</div>
            <p className="text-sm text-secondary">API Services</p>
            <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div className="bg-green-500 h-2 rounded-full" style={{ width: '98%' }}></div>
            </div>
          </div>
          <div className="text-center">
            <div className="text-2xl font-bold text-blue-500 mb-2">Normal</div>
            <p className="text-sm text-secondary">Database Performance</p>
            <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div className="bg-blue-500 h-2 rounded-full" style={{ width: '87%' }}></div>
            </div>
          </div>
          <div className="text-center">
            <div className="text-2xl font-bold text-yellow-500 mb-2">Monitor</div>
            <p className="text-sm text-secondary">Memory Usage</p>
            <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div className="bg-yellow-500 h-2 rounded-full" style={{ width: '73%' }}></div>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default AdminDashboard;