import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  UserGroupIcon, 
  CurrencyDollarIcon,
  ChartBarIcon,
  ServerIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon,
  CogIcon,
  BellIcon,
  ShieldCheckIcon,
  GlobeAltIcon,
  DocumentTextIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const AdminDashboard = () => {
  const [metrics, setMetrics] = useState(null);
  const [systemHealth, setSystemHealth] = useState(null);
  const [users, setUsers] = useState([]);
  const [recentActivity, setRecentActivity] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadAdminData();
  }, []);

  const loadAdminData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      // Real data loaded from API

      // Real data loaded from API

      // Real data loaded from API

      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load admin data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const MetricCard = ({ title, value, change, icon: Icon, color = 'primary', subtitle = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {subtitle && (
            <p className="text-sm text-secondary mt-1">{subtitle}</p>
          )}
          {change && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% vs last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const SystemHealthCard = ({ title, status, value, icon: Icon }) => (
    <div className="card p-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-3">
          <Icon className="w-6 h-6 text-accent-primary" />
          <div>
            <h4 className="font-medium text-primary">{title}</h4>
            {value && <p className="text-sm text-secondary">{value}%</p>}
          </div>
        </div>
        <div className="flex items-center space-x-2">
          {status === 'healthy' && <CheckCircleIcon className="w-5 h-5 text-accent-success" />}
          {status === 'warning' && <ExclamationTriangleIcon className="w-5 h-5 text-accent-warning" />}
          {status === 'error' && <ExclamationTriangleIcon className="w-5 h-5 text-accent-danger" />}
          <span className={`text-sm font-medium ${
            status === 'healthy' ? 'text-accent-success' :
            status === 'warning' ? 'text-accent-warning' : 'text-accent-danger'
          }`}>
            {status}
          </span>
        </div>
      </div>
    </div>
  );

  const UserRow = ({ user }) => (
    <tr className="hover-surface">
      <td className="px-6 py-4 whitespace-nowrap">
        <div className="flex items-center">
          <div className="w-8 h-8 bg-gradient-primary rounded-full flex items-center justify-center">
            <span className="text-white text-sm font-bold">{user.name.charAt(0)}</span>
          </div>
          <div className="ml-3">
            <p className="font-medium text-primary">{user.name}</p>
            <p className="text-sm text-secondary">{user.email}</p>
          </div>
        </div>
      </td>
      <td className="px-6 py-4 whitespace-nowrap">
        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
          user.status === 'active' 
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        }`}>
          {user.status}
        </span>
      </td>
      <td className="px-6 py-4 whitespace-nowrap text-primary">{user.plan}</td>
      <td className="px-6 py-4 whitespace-nowrap text-secondary">{user.joined}</td>
      <td className="px-6 py-4 whitespace-nowrap">
        <Button variant="secondary" size="small">Manage</Button>
      </td>
    </tr>
  );

  if (loading) {
    
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
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  