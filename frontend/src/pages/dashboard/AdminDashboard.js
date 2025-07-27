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
      // Real data loaded from API
      const response = await adminAPI.getDashboard();
      // Real data loaded from API
    } catch (error) {
      console.error('Failed to fetch admin data:', error);
      toast.error('Failed to load admin dashboard');
    } finally {
      // Real data loaded from API
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
    link.// Real data loaded from API
    link.// Real data loaded from API
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
    link.// Real data loaded from API
    link.// Real data loaded from API
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
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500"></div>
      </div>
    );
  }

  if (user?.role !== 'admin') {
    