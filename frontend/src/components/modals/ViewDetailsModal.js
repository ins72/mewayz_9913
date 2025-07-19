import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  XMarkIcon,
  EyeIcon,
  CalendarIcon,
  UserIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
  CurrencyDollarIcon,
  ClockIcon
} from '@heroicons/react/24/outline';

const ViewDetailsModal = ({ isOpen, onClose, title, data, type = 'general' }) => {
  const renderContent = () => {
    if (!data) return null;

    switch (type) {
      case 'user':
        return (
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Full Name</label>
                <div className="flex items-center p-3 bg-surface rounded-lg">
                  <UserIcon className="h-5 w-5 text-secondary mr-2" />
                  <span className="text-primary">{data.name || 'N/A'}</span>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Email</label>
                <div className="flex items-center p-3 bg-surface rounded-lg">
                  <EnvelopeIcon className="h-5 w-5 text-secondary mr-2" />
                  <span className="text-primary">{data.email || 'N/A'}</span>
                </div>
              </div>
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Role</label>
                <div className="p-3 bg-surface rounded-lg">
                  <span className={`px-2 py-1 text-xs font-semibold rounded-full ${
                    data.role === 'admin' ? 'bg-red-100 text-red-800' :
                    data.role === 'moderator' ? 'bg-blue-100 text-blue-800' :
                    'bg-green-100 text-green-800'
                  }`}>
                    {data.role || 'user'}
                  </span>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Status</label>
                <div className="p-3 bg-surface rounded-lg">
                  <span className={`px-2 py-1 text-xs font-semibold rounded-full ${
                    data.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  }`}>
                    {data.status || 'active'}
                  </span>
                </div>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-1">Created At</label>
              <div className="flex items-center p-3 bg-surface rounded-lg">
                <CalendarIcon className="h-5 w-5 text-secondary mr-2" />
                <span className="text-primary">{data.created_at || new Date().toLocaleDateString()}</span>
              </div>
            </div>
          </div>
        );
      
      case 'transaction':
        return (
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Amount</label>
                <div className="flex items-center p-3 bg-surface rounded-lg">
                  <CurrencyDollarIcon className="h-5 w-5 text-secondary mr-2" />
                  <span className="text-primary font-semibold">${data.amount?.toLocaleString() || '0'}</span>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Status</label>
                <div className="p-3 bg-surface rounded-lg">
                  <span className={`px-2 py-1 text-xs font-semibold rounded-full ${
                    data.status === 'completed' ? 'bg-green-100 text-green-800' :
                    data.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    data.status === 'failed' ? 'bg-red-100 text-red-800' :
                    'bg-blue-100 text-blue-800'
                  }`}>
                    {data.status || 'pending'}
                  </span>
                </div>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-1">Description</label>
              <div className="p-3 bg-surface rounded-lg">
                <span className="text-primary">{data.description || 'No description provided'}</span>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-1">Created At</label>
              <div className="flex items-center p-3 bg-surface rounded-lg">
                <CalendarIcon className="h-5 w-5 text-secondary mr-2" />
                <span className="text-primary">{data.created_at || new Date().toLocaleDateString()}</span>
              </div>
            </div>
          </div>
        );

      case 'booking':
        return (
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Client</label>
                <div className="flex items-center p-3 bg-surface rounded-lg">
                  <UserIcon className="h-5 w-5 text-secondary mr-2" />
                  <span className="text-primary">{data.client_name || 'N/A'}</span>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Service</label>
                <div className="p-3 bg-surface rounded-lg">
                  <span className="text-primary">{data.service_name || 'N/A'}</span>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Date & Time</label>
                <div className="flex items-center p-3 bg-surface rounded-lg">
                  <CalendarIcon className="h-5 w-5 text-secondary mr-2" />
                  <span className="text-primary">{data.scheduled_at || 'TBD'}</span>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-1">Duration</label>
                <div className="flex items-center p-3 bg-surface rounded-lg">
                  <ClockIcon className="h-5 w-5 text-secondary mr-2" />
                  <span className="text-primary">{data.duration || '60'} minutes</span>
                </div>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-1">Notes</label>
              <div className="p-3 bg-surface rounded-lg">
                <span className="text-primary">{data.notes || 'No additional notes'}</span>
              </div>
            </div>
          </div>
        );

      default:
        return (
          <div className="space-y-4">
            {Object.entries(data).map(([key, value]) => (
              <div key={key}>
                <label className="block text-sm font-medium text-secondary mb-1 capitalize">
                  {key.replace(/_/g, ' ')}
                </label>
                <div className="p-3 bg-surface rounded-lg">
                  <span className="text-primary">{String(value) || 'N/A'}</span>
                </div>
              </div>
            ))}
          </div>
        );
    }
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-50"
            onClick={onClose}
          />
          
          <motion.div
            initial={{ opacity: 0, scale: 0.9, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.9, y: 20 }}
            className="relative bg-surface-elevated rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto"
          >
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-lg font-semibold text-primary flex items-center">
                <EyeIcon className="h-5 w-5 mr-2 text-accent-primary" />
                {title || 'Details'}
              </h3>
              <button
                onClick={onClose}
                className="text-secondary hover:text-primary transition-colors"
              >
                <XMarkIcon className="h-6 w-6" />
              </button>
            </div>

            {renderContent()}

            <div className="flex justify-end mt-6 pt-4 border-t border-default">
              <button
                onClick={onClose}
                className="btn-secondary"
              >
                Close
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
};

export default ViewDetailsModal;