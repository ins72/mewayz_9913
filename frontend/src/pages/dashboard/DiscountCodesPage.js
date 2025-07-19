import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import {
  TicketIcon,
  PlusIcon,
  ClipboardDocumentIcon,
  CalendarIcon,
  ChartBarIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  CheckCircleIcon,
  XCircleIcon
} from '@heroicons/react/24/outline';

const DiscountCodesPage = () => {
  const { user } = useAuth();
  const [discountCodes, setDiscountCodes] = useState([]);
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [loading, setLoading] = useState(false);

  // Mock discount codes data
  const mockDiscountCodes = [
    {
      id: '1',
      code: 'WELCOME20',
      description: 'Welcome discount for new customers',
      type: 'percentage',
      value: 20,
      usageLimit: 100,
      usedCount: 45,
      isActive: true,
      expiresAt: '2024-12-31',
      createdAt: '2024-06-01',
      applicableProducts: ['all']
    },
    {
      id: '2',
      code: 'FLASH50',
      description: 'Flash sale - 50% off premium courses',
      type: 'percentage',
      value: 50,
      usageLimit: 50,
      usedCount: 32,
      isActive: true,
      expiresAt: '2024-07-15',
      createdAt: '2024-06-15',
      applicableProducts: ['courses']
    },
    {
      id: '3',
      code: 'FREESHIP',
      description: 'Free shipping on all orders',
      type: 'fixed',
      value: 10,
      usageLimit: null,
      usedCount: 156,
      isActive: true,
      expiresAt: null,
      createdAt: '2024-05-01',
      applicableProducts: ['physical']
    },
    {
      id: '4',
      code: 'EXPIRED10',
      description: 'Old promotion code',
      type: 'percentage',
      value: 10,
      usageLimit: 200,
      usedCount: 89,
      isActive: false,
      expiresAt: '2024-06-01',
      createdAt: '2024-03-01',
      applicableProducts: ['all']
    }
  ];

  useEffect(() => {
    setDiscountCodes(mockDiscountCodes);
  }, []);

  const getStatusColor = (code) => {
    if (!code.isActive) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    if (code.expiresAt && new Date(code.expiresAt) < new Date()) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    if (code.usageLimit && code.usedCount >= code.usageLimit) return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
  };

  const getStatusText = (code) => {
    if (!code.isActive) return 'Inactive';
    if (code.expiresAt && new Date(code.expiresAt) < new Date()) return 'Expired';
    if (code.usageLimit && code.usedCount >= code.usageLimit) return 'Limit Reached';
    return 'Active';
  };

  const copyToClipboard = (code) => {
    navigator.clipboard.writeText(code);
    // You could add a toast notification here
  };

  const toggleCodeStatus = (id) => {
    setDiscountCodes(codes =>
      codes.map(code =>
        code.id === id ? { ...code, isActive: !code.isActive } : code
      )
    );
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="mb-8"
      >
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold text-primary mb-2">Discount Codes</h1>
            <p className="text-secondary">Create and manage promotional discount codes</p>
          </div>
          <button
            onClick={() => setShowCreateModal(true)}
            className="btn btn-primary flex items-center space-x-2"
          >
            <PlusIcon className="h-5 w-5" />
            <span>Create Code</span>
          </button>
        </div>
      </motion.div>

      {/* Stats Cards */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8"
      >
        {[
          { 
            label: 'Total Codes', 
            value: discountCodes.length.toString(), 
            icon: TicketIcon, 
            color: 'bg-blue-500' 
          },
          { 
            label: 'Active Codes', 
            value: discountCodes.filter(c => c.isActive).length.toString(), 
            icon: CheckCircleIcon, 
            color: 'bg-green-500' 
          },
          { 
            label: 'Total Uses', 
            value: discountCodes.reduce((sum, c) => sum + c.usedCount, 0).toString(), 
            icon: ChartBarIcon, 
            color: 'bg-purple-500' 
          },
          { 
            label: 'Expired', 
            value: discountCodes.filter(c => !c.isActive || (c.expiresAt && new Date(c.expiresAt) < new Date())).length.toString(), 
            icon: XCircleIcon, 
            color: 'bg-red-500' 
          }
        ].map((stat, index) => (
          <div key={index} className="bg-surface p-6 rounded-lg shadow-default">
            <div className="flex items-center">
              <div className={`p-3 rounded-lg ${stat.color} mr-4`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary">{stat.value}</p>
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Discount Codes List */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="bg-surface rounded-lg shadow-default overflow-hidden"
      >
        <div className="p-6 border-b border-default">
          <h2 className="text-xl font-semibold text-primary">Your Discount Codes</h2>
        </div>
        <div className="divide-y divide-default">
          {discountCodes.map((code) => (
            <div key={code.id} className="p-6 hover:bg-surface-hover transition-colors">
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <div className="flex items-center space-x-3 mb-2">
                    <h3 className="text-lg font-semibold text-primary font-mono">
                      {code.code}
                    </h3>
                    <button
                      onClick={() => copyToClipboard(code.code)}
                      className="p-1 text-secondary hover:text-primary"
                      title="Copy code"
                    >
                      <ClipboardDocumentIcon className="h-4 w-4" />
                    </button>
                    <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(code)}`}>
                      {getStatusText(code)}
                    </span>
                  </div>
                  
                  <p className="text-secondary mb-3">{code.description}</p>
                  
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                      <span className="text-secondary">Discount:</span>
                      <p className="font-medium text-primary">
                        {code.type === 'percentage' ? `${code.value}%` : `$${code.value}`}
                      </p>
                    </div>
                    <div>
                      <span className="text-secondary">Used:</span>
                      <p className="font-medium text-primary">
                        {code.usedCount} / {code.usageLimit || '∞'}
                      </p>
                    </div>
                    <div>
                      <span className="text-secondary">Expires:</span>
                      <p className="font-medium text-primary">
                        {code.expiresAt ? new Date(code.expiresAt).toLocaleDateString() : 'Never'}
                      </p>
                    </div>
                    <div>
                      <span className="text-secondary">Created:</span>
                      <p className="font-medium text-primary">
                        {new Date(code.createdAt).toLocaleDateString()}
                      </p>
                    </div>
                  </div>
                  
                  {code.usageLimit && (
                    <div className="mt-3">
                      <div className="flex justify-between text-sm text-secondary mb-1">
                        <span>Usage Progress</span>
                        <span>{Math.round((code.usedCount / code.usageLimit) * 100)}%</span>
                      </div>
                      <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                          className="bg-accent-primary h-2 rounded-full transition-all duration-300"
                          style={{ width: `${Math.min((code.usedCount / code.usageLimit) * 100, 100)}%` }}
                        ></div>
                      </div>
                    </div>
                  )}
                </div>
                
                <div className="flex items-center space-x-2 ml-4">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors" title="View Analytics">
                    <EyeIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors" title="Edit">
                    <PencilIcon className="h-5 w-5" />
                  </button>
                  <button
                    onClick={() => toggleCodeStatus(code.id)}
                    className={`p-2 rounded-lg transition-colors ${
                      code.isActive 
                        ? 'text-orange-500 hover:text-orange-700 hover:bg-orange-50 dark:hover:bg-orange-900'
                        : 'text-green-500 hover:text-green-700 hover:bg-green-50 dark:hover:bg-green-900'
                    }`}
                    title={code.isActive ? 'Deactivate' : 'Activate'}
                  >
                    {code.isActive ? <XCircleIcon className="h-5 w-5" /> : <CheckCircleIcon className="h-5 w-5" />}
                  </button>
                  <button className="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-colors" title="Delete">
                    <TrashIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </motion.div>

      {/* Create Modal would go here */}
      {showCreateModal && (
        <div className="fixed inset-0 z-50 overflow-y-auto">
          <div className="flex items-center justify-center min-h-screen px-4">
            <div className="fixed inset-0 bg-black bg-opacity-25" onClick={() => setShowCreateModal(false)}></div>
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="relative bg-surface p-6 rounded-lg shadow-xl max-w-lg w-full"
            >
              <h3 className="text-lg font-semibold text-primary mb-4">Create Discount Code</h3>
              <p className="text-secondary mb-4">Form fields would go here...</p>
              <div className="flex justify-end space-x-3">
                <button
                  onClick={() => setShowCreateModal(false)}
                  className="btn btn-secondary"
                >
                  Cancel
                </button>
                <button className="btn btn-primary">
                  Create Code
                </button>
              </div>
            </motion.div>
          </div>
        </div>
      )}
    </div>
  );
};

export default DiscountCodesPage;