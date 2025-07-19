import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import toast from 'react-hot-toast';
import {
  ShieldCheckIcon,
  CurrencyDollarIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  DocumentTextIcon,
  UserGroupIcon,
  ChartBarIcon,
  PlusIcon,
  EyeIcon,
  ArrowPathIcon
} from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';

const EscrowSystemPage = () => {
  const { user } = useAuth();
  const [escrowData, setEscrowData] = useState(null);
  const [transactions, setTransactions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedFilter, setSelectedFilter] = useState('all');

  useEffect(() => {
    fetchEscrowData();
  }, []);

  const fetchEscrowData = async () => {
    try {
      setLoading(true);
      // Mock comprehensive escrow data
      const mockData = {
        transaction_overview: {
          total_transactions: 234,
          active_transactions: 45,
          completed_transactions: 189,
          disputed_transactions: 3,
          total_value: 456789.50,
          platform_fees_earned: 12456.78,
          avg_transaction_value: 1950.38
        },
        escrow_metrics: {
          completion_rate: 96.8,
          dispute_rate: 1.3,
          avg_escrow_duration: "7.2 days",
          fastest_completion: "2 hours",
          release_accuracy: 99.2,
          customer_satisfaction: 4.8
        },
        transaction_types: [
          { type: "Service Payment", count: 156, value: 234567, percentage: 66.7 },
          { type: "Product Purchase", count: 45, value: 123456, percentage: 25.3 },
          { type: "Digital Asset", count: 23, value: 67890, percentage: 8.0 }
        ],
        dispute_resolution: {
          total_disputes: 8,
          resolved_disputes: 5,
          pending_disputes: 3,
          avg_resolution_time: "3.2 days",
          customer_favor: 62.5,
          vendor_favor: 37.5
        }
      };
      
      const mockTransactions = [
        {
          id: "ESC001",
          client: "John Doe",
          vendor: "TechService Co",
          amount: 2500.00,
          status: "active",
          created_at: "2025-01-15",
          type: "Service Payment",
          milestone: "50% complete",
          due_date: "2025-01-25"
        },
        {
          id: "ESC002", 
          client: "Sarah Wilson",
          vendor: "DesignStudio",
          amount: 1800.00,
          status: "completed",
          created_at: "2025-01-12",
          type: "Digital Asset",
          milestone: "Delivered",
          due_date: "2025-01-20"
        },
        {
          id: "ESC003",
          client: "Mike Chen",
          vendor: "DevTeam Pro",
          amount: 4500.00,
          status: "disputed",
          created_at: "2025-01-10",
          type: "Service Payment",
          milestone: "Under review",
          due_date: "2025-01-30"
        }
      ];
      
      setEscrowData(mockData);
      setTransactions(mockTransactions);
    } catch (error) {
      console.error('Failed to fetch escrow data:', error);
      toast.error('Failed to load escrow data');
    } finally {
      setLoading(false);
    }
  };

  const getStatusIcon = (status) => {
    switch (status) {
      case 'completed':
        return <CheckCircleIcon className="h-5 w-5 text-green-500" />;
      case 'disputed':
        return <ExclamationTriangleIcon className="h-5 w-5 text-red-500" />;
      case 'active':
        return <ClockIcon className="h-5 w-5 text-yellow-500" />;
      default:
        return <ClockIcon className="h-5 w-5 text-gray-500" />;
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'completed':
        return 'bg-green-100 text-green-800';
      case 'disputed':
        return 'bg-red-100 text-red-800';
      case 'active':
        return 'bg-yellow-100 text-yellow-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-primary"></div>
      </div>
    );
  }

  const overviewCards = [
    {
      title: 'Total Value Secured',
      value: `$${escrowData?.transaction_overview?.total_value?.toLocaleString()}`,
      subtitle: `${escrowData?.transaction_overview?.total_transactions} transactions`,
      icon: ShieldCheckIcon,
      color: 'bg-blue-500'
    },
    {
      title: 'Active Transactions',
      value: escrowData?.transaction_overview?.active_transactions,
      subtitle: 'Currently in escrow',
      icon: ClockIcon,
      color: 'bg-yellow-500'
    },
    {
      title: 'Completion Rate',
      value: `${escrowData?.escrow_metrics?.completion_rate}%`,
      subtitle: 'Successful releases',
      icon: CheckCircleIcon,
      color: 'bg-green-500'
    },
    {
      title: 'Platform Fees',
      value: `$${escrowData?.transaction_overview?.platform_fees_earned?.toLocaleString()}`,
      subtitle: 'Total earned',
      icon: CurrencyDollarIcon,
      color: 'bg-purple-500'
    }
  ];

  const filteredTransactions = selectedFilter === 'all' 
    ? transactions 
    : transactions.filter(t => t.status === selectedFilter);

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="flex items-center justify-between"
      >
        <div>
          <h1 className="text-3xl font-bold text-primary mb-2 flex items-center">
            <ShieldCheckIcon className="h-8 w-8 text-accent-primary mr-3" />
            Escrow System
          </h1>
          <p className="text-secondary">
            Secure transaction management with milestone-based payments.
          </p>
        </div>
        
        <button className="btn-primary flex items-center">
          <PlusIcon className="h-5 w-5 mr-2" />
          Create Escrow
        </button>
      </motion.div>

      {/* Overview Cards */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        {overviewCards.map((card, index) => (
          <div key={card.title} className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary mb-1">{card.title}</p>
                <p className="text-2xl font-bold text-primary">{card.value}</p>
                <p className="text-sm text-secondary mt-1">{card.subtitle}</p>
              </div>
              <div className={`p-3 rounded-lg ${card.color}`}>
                <card.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Metrics Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Transaction Types */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Transaction Types</h3>
          <div className="space-y-4">
            {escrowData?.transaction_types?.map((type, index) => (
              <div key={index} className="flex items-center justify-between">
                <div>
                  <p className="font-medium text-primary">{type.type}</p>
                  <p className="text-sm text-secondary">{type.count} transactions</p>
                </div>
                <div className="text-right">
                  <p className="font-semibold text-accent-primary">${type.value.toLocaleString()}</p>
                  <p className="text-sm text-secondary">{type.percentage}%</p>
                </div>
              </div>
            ))}
          </div>
        </motion.div>

        {/* Performance Metrics */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.3 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Performance Metrics</h3>
          <div className="space-y-4">
            <div className="flex justify-between items-center">
              <span className="text-secondary">Avg Duration</span>
              <span className="font-semibold text-primary">{escrowData?.escrow_metrics?.avg_escrow_duration}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Fastest Completion</span>
              <span className="font-semibold text-green-500">{escrowData?.escrow_metrics?.fastest_completion}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Release Accuracy</span>
              <span className="font-semibold text-blue-500">{escrowData?.escrow_metrics?.release_accuracy}%</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Customer Satisfaction</span>
              <span className="font-semibold text-purple-500">{escrowData?.escrow_metrics?.customer_satisfaction}/5</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Dispute Rate</span>
              <span className="font-semibold text-red-500">{escrowData?.escrow_metrics?.dispute_rate}%</span>
            </div>
          </div>
        </motion.div>

        {/* Dispute Resolution */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Dispute Resolution</h3>
          <div className="space-y-4">
            <div className="flex justify-between items-center">
              <span className="text-secondary">Total Disputes</span>
              <span className="font-semibold text-primary">{escrowData?.dispute_resolution?.total_disputes}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Resolved</span>
              <span className="font-semibold text-green-500">{escrowData?.dispute_resolution?.resolved_disputes}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Pending</span>
              <span className="font-semibold text-yellow-500">{escrowData?.dispute_resolution?.pending_disputes}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-secondary">Avg Resolution Time</span>
              <span className="font-semibold text-primary">{escrowData?.dispute_resolution?.avg_resolution_time}</span>
            </div>
            <div className="mt-4 pt-4 border-t border-default">
              <p className="text-sm text-secondary mb-2">Resolution Outcomes</p>
              <div className="flex justify-between text-sm">
                <span>Customer Favor: {escrowData?.dispute_resolution?.customer_favor}%</span>
                <span>Vendor Favor: {escrowData?.dispute_resolution?.vendor_favor}%</span>
              </div>
            </div>
          </div>
        </motion.div>
      </div>

      {/* Transactions Table */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.5 }}
        className="bg-surface-elevated rounded-lg shadow-default"
      >
        <div className="p-6 border-b border-default">
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-primary">Recent Transactions</h3>
            <div className="flex items-center space-x-4">
              <select
                value={selectedFilter}
                onChange={(e) => setSelectedFilter(e.target.value)}
                className="input rounded-lg focus-ring"
              >
                <option value="all">All Transactions</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="disputed">Disputed</option>
              </select>
            </div>
          </div>
        </div>
        
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-default">
            <thead className="bg-surface">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                  Transaction
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                  Parties
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                  Amount
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                  Status
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                  Progress
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-surface-elevated divide-y divide-default">
              {filteredTransactions.map((transaction) => (
                <tr key={transaction.id}>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div className="text-sm font-medium text-primary">{transaction.id}</div>
                      <div className="text-sm text-secondary">{transaction.type}</div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div className="text-sm text-primary">Client: {transaction.client}</div>
                      <div className="text-sm text-secondary">Vendor: {transaction.vendor}</div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-primary">
                      ${transaction.amount.toLocaleString()}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center">
                      {getStatusIcon(transaction.status)}
                      <span className={`ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(transaction.status)}`}>
                        {transaction.status}
                      </span>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm text-primary">{transaction.milestone}</div>
                    <div className="text-sm text-secondary">Due: {transaction.due_date}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div className="flex items-center space-x-2">
                      <button className="text-blue-600 hover:text-blue-900">
                        <EyeIcon className="h-4 w-4" />
                      </button>
                      <button className="text-green-600 hover:text-green-900">
                        <ArrowPathIcon className="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </motion.div>
    </div>
  );
};

export default EscrowSystemPage;