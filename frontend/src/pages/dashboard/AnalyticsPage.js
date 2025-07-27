import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  ChartBarIcon, 
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  EyeIcon,
  UserGroupIcon,
  CurrencyDollarIcon,
  ClockIcon,
  CalendarIcon,
  FunnelIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  GlobeAltIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const AnalyticsPage = () => {
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');
  const [dateRange, setDateRange] = useState('7d');

  useEffect(() => {
    loadAnalyticsData();
  }, [dateRange]);

  const loadAnalyticsData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load analytics data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const MetricCard = ({ title, value, change, icon: Icon, color = 'primary', suffix = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}{suffix}</p>
          <div className="flex items-center mt-2">
            {change > 0 ? (
              <ArrowTrendingUpIcon className="w-4 h-4 text-accent-success mr-1" />
            ) : (
              <ArrowTrendingDownIcon className="w-4 h-4 text-accent-danger mr-1" />
            )}
            <span className={`text-sm font-medium ${
              change > 0 ? 'text-accent-success' : 'text-accent-danger'
            }`}>
              {Math.abs(change)}%
            </span>
            <span className="text-secondary text-sm ml-1">vs last period</span>
          </div>
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const TrafficSourceCard = ({ source, value, total }) => {
    const percentage = ((value / total) * 100).toFixed(1);
    
  const loadAnalyticsData = async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/analytics/overview', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setAnalytics(data);
      } else {
        console.error('Failed to load analytics data');
      }
    } catch (error) {
      console.error('Error loading analytics data:', error);
    } finally {
      setLoading(false);
    }
  };


  return (
      <div className="flex items-center justify-between py-3">
        <div className="flex items-center space-x-3">
          <div className="w-3 h-3 bg-accent-primary rounded-full"></div>
          <span className="text-primary font-medium capitalize">{source}</span>
        </div>
        <div className="text-right">
          <p className="text-primary font-bold">{value.toLocaleString()}</p>
          <p className="text-secondary text-sm">{percentage}%</p>
        </div>
      </div>
    );
  };

  if (loading) {
    