import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  ShieldCheckIcon,
  UsersIcon,
  ServerIcon,
  ChartBarIcon,
  CheckCircleIcon,
  ArrowTrendingUpIcon,
  CurrencyDollarIcon,
  GlobeAltIcon,
  CreditCardIcon,
  BanknotesIcon,
  ShoppingBagIcon,
  DocumentArrowDownIcon,
  PlusIcon,
  Cog6ToothIcon,
  UserPlusIcon,
  CloudIcon,
  CircleStackIcon,
  DocumentTextIcon
} from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


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
    
  const loadDashboardData = async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/dashboard/overview', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setMetrics(data.metrics || {});
        setRecentActivity(data.recent_activity || []);
        setSystemHealth(data.system_health || {});
      } else {
        console.error('Failed to load dashboard data');
      }
    } catch (error) {
      console.error('Error loading dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };


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

  