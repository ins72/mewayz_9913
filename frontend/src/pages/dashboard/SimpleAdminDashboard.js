import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
  useEffect(() => {
    loadData();
  }, []);


const SimpleAdminDashboard = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('overview');

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
          <div className="text-6xl mb-4">🛡️</div>
          <h3 className="text-lg font-medium text-primary">Access Denied</h3>
          <p className="text-secondary">Admin privileges required to access this page.</p>
        </div>
      </div>
    );
  }

  