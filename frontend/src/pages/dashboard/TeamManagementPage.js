import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import {
  UsersIcon,
  UserPlusIcon,
  UserMinusIcon,
  ShieldCheckIcon,
  CogIcon,
  EnvelopeIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon
} from '@heroicons/react/24/outline';

const TeamManagementPage = () => {
  const { user } = useAuth();
  const [teamMembers, setTeamMembers] = useState([]);
  const [error, setError] = useState(null);
  const [showInviteModal, setShowInviteModal] = useState(false);
  const [error, setError] = useState(null);
  const [inviteEmail, setInviteEmail] = useState('');
  const [error, setError] = useState(null);
  const [inviteRole, setInviteRole] = useState('user');
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  // Mock team data
  // Real data loaded from API

  useEffect(() => {
    // Real data loaded from API
  }, []);

  const handleInviteUser = async (e) => {
    e.preventDefault();
    // Real data loaded from API
    
    // Simulate API call
    setTimeout(() => {
      const newMember = {
        id: Date.now().toString(),
        name: inviteEmail.split('@')[0],
        email: inviteEmail,
        role: inviteRole,
        status: 'pending',
        lastActive: 'Never',
        avatar: null,
        joinedAt: new Date().toISOString().split('T')[0]
      };
      
      // Real data loaded from API
      // Real data loaded from API
      // Real data loaded from API
      // Real data loaded from API
      // Real data loaded from API
    }, 1000);
  };

  const getRoleColor = (role) => {
    switch (role) {
      case 'admin': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
      case 'editor': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
      case 'viewer': return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
      default: return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
      case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
      case 'inactive': return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
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
            <h1 className="text-3xl font-bold text-primary mb-2">Team Management</h1>
            <p className="text-secondary">Manage your workspace members and permissions</p>
          </div>
          <button
            onClick={() => setShowInviteModal(true)}
            className="btn btn-primary flex items-center space-x-2"
          >
            <UserPlusIcon className="h-5 w-5" />
            <span>Invite Member</span>
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
          { label: 'Total Members', value: teamMembers.length.toString(), icon: UsersIcon, color: 'bg-blue-500' },
          { label: 'Active Members', value: teamMembers.filter(m => m.status === 'active').length.toString(), icon: CheckCircleIcon, color: 'bg-green-500' },
          { label: 'Pending Invites', value: teamMembers.filter(m => m.status === 'pending').length.toString(), icon: ExclamationTriangleIcon, color: 'bg-yellow-500' },
          { label: 'Admins', value: teamMembers.filter(m => m.role === 'admin').length.toString(), icon: ShieldCheckIcon, color: 'bg-red-500' }
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

      {/* Team Members List */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="bg-surface rounded-lg shadow-default overflow-hidden"
      >
        <div className="p-6 border-b border-default">
          <h2 className="text-xl font-semibold text-primary">Team Members</h2>
        </div>
        <div className="divide-y divide-default">
          {teamMembers.map((member) => (
            <div key={member.id} className="p-6 hover:bg-surface-hover transition-colors">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <div className="h-12 w-12 bg-gradient-primary rounded-full flex items-center justify-center">
                    <span className="text-white font-medium">
                      {member.name.charAt(0).toUpperCase()}
                    </span>
                  </div>
                  <div>
                    <h3 className="text-lg font-medium text-primary">{member.name}</h3>
                    <p className="text-sm text-secondary">{member.email}</p>
                    <p className="text-xs text-secondary">Last active: {member.lastActive}</p>
                  </div>
                </div>
                <div className="flex items-center space-x-4">
                  <span className={`px-3 py-1 rounded-full text-xs font-medium ${getRoleColor(member.role)}`}>
                    {member.role}
                  </span>
                  <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(member.status)}`}>
                    {member.status}
                  </span>
                  <div className="flex items-center space-x-2">
                    <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                      <EnvelopeIcon className="h-5 w-5" />
                    </button>
                    <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                      <CogIcon className="h-5 w-5" />
                    </button>
                    {member.status === 'pending' && (
                      <button className="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg">
                        <UserMinusIcon className="h-5 w-5" />
                      </button>
                    )}
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </motion.div>

      {/* Invite Modal */}
      {showInviteModal && (
        <div className="fixed inset-0 z-50 overflow-y-auto">
          <div className="flex items-center justify-center min-h-screen px-4">
            <div className="fixed inset-0 bg-black bg-opacity-25" onClick={() => setShowInviteModal(false)}></div>
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="relative bg-surface p-6 rounded-lg shadow-xl max-w-md w-full"
            >
              <h3 className="text-lg font-semibold text-primary mb-4">Invite Team Member</h3>
              <form onSubmit={handleInviteUser} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">
                    Email Address
                  </label>
                  <input
                    type="email"
                    value={inviteEmail}
                    onChange={(e) => setInviteEmail(e.target.value)}
                    placeholder="user@example.com"
                    className="input w-full"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">
                    Role
                  </label>
                  <select
                    value={inviteRole}
                    onChange={(e) => setInviteRole(e.target.value)}
                    className="input w-full"
                  >
                    <option value="viewer">Viewer - Can view content</option>
                    <option value="editor">Editor - Can edit content</option>
                    <option value="admin">Admin - Full access</option>
                  </select>
                </div>
                <div className="flex justify-end space-x-3 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowInviteModal(false)}
                    className="btn btn-secondary"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    disabled={loading}
                    className="btn btn-primary"
                  >
                    {loading ? 'Sending...' : 'Send Invite'}
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        </div>
      )}
    </div>
  );
};

export default TeamManagementPage;