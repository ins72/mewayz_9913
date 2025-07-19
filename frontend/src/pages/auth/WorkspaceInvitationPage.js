import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CheckCircleIcon,
  XCircleIcon,
  UserGroupIcon,
  BuildingOfficeIcon,
  SparklesIcon,
  ArrowRightIcon,
  ShieldCheckIcon,
  ClockIcon,
  UserIcon,
  CogIcon
} from '@heroicons/react/24/outline';

const WorkspaceInvitationPage = () => {
  const { token } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [invitation, setInvitation] = useState(null);
  const [loading, setLoading] = useState(true);
  const [accepting, setAccepting] = useState(false);
  const [declining, setDeclining] = useState(false);

  useEffect(() => {
    fetchInvitation();
  }, [token]);

  const fetchInvitation = async () => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/invitations/${token}`);
      if (response.ok) {
        const data = await response.json();
        setInvitation(data);
      } else if (response.status === 404) {
        setInvitation({ error: 'Invitation not found or expired' });
      } else {
        throw new Error('Failed to fetch invitation');
      }
    } catch (err) {
      setInvitation({ error: 'Failed to load invitation' });
    } finally {
      setLoading(false);
    }
  };

  const handleAcceptInvitation = async () => {
    setAccepting(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/invitations/${token}/accept`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });

      if (response.ok) {
        success(`Successfully joined ${invitation.workspace.name}!`);
        
        // Show joining animation
        setTimeout(() => {
          navigate('/dashboard');
        }, 2000);
      } else {
        throw new Error('Failed to accept invitation');
      }
    } catch (err) {
      error('Failed to accept invitation. Please try again.');
      setAccepting(false);
    }
  };

  const handleDeclineInvitation = async () => {
    setDeclining(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/invitations/${token}/decline`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });

      if (response.ok) {
        success('Invitation declined.');
        navigate('/');
      } else {
        throw new Error('Failed to decline invitation');
      }
    } catch (err) {
      error('Failed to decline invitation. Please try again.');
      setDeclining(false);
    }
  };

  const getRoleIcon = (role) => {
    switch (role) {
      case 'owner': return ShieldCheckIcon;
      case 'admin': return CogIcon;
      case 'editor': return UserIcon;
      case 'viewer': return UserIcon;
      default: return UserIcon;
    }
  };

  const getRoleColor = (role) => {
    switch (role) {
      case 'owner': return 'text-yellow-500';
      case 'admin': return 'text-red-500';
      case 'editor': return 'text-blue-500';
      case 'viewer': return 'text-green-500';
      default: return 'text-gray-500';
    }
  };

  const getRoleDescription = (role) => {
    switch (role) {
      case 'owner': return 'Full access to all workspace features and settings';
      case 'admin': return 'Manage team members, settings, and most features';
      case 'editor': return 'Create and edit content, limited access to settings';
      case 'viewer': return 'View-only access to workspace content';
      default: return 'Basic workspace access';
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-surface flex items-center justify-center">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-surface-elevated rounded-xl shadow-default p-8 max-w-md w-full mx-4 text-center"
        >
          <div className="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4" />
          <h2 className="text-xl font-semibold text-primary mb-2">Loading Invitation</h2>
          <p className="text-secondary">Please wait while we fetch your invitation details...</p>
        </motion.div>
      </div>
    );
  }

  if (accepting) {
    return (
      <div className="min-h-screen bg-surface flex items-center justify-center">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-surface-elevated rounded-xl shadow-default p-8 max-w-md w-full mx-4 text-center"
        >
          <motion.div
            animate={{ rotate: 360 }}
            transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
            className="w-16 h-16 mx-auto mb-6"
          >
            <div className="w-full h-full border-4 border-blue-500 border-t-transparent rounded-full" />
          </motion.div>
          
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3 }}
          >
            <h2 className="text-2xl font-bold text-primary mb-2">Joining Workspace</h2>
            <p className="text-secondary mb-4">
              Please wait while we add you to <span className="font-semibold text-primary">{invitation?.workspace?.name}</span>
            </p>
            
            <motion.div
              initial={{ width: 0 }}
              animate={{ width: "100%" }}
              transition={{ duration: 1.5, ease: "easeInOut" }}
              className="w-full bg-surface-elevated rounded-full h-2 mb-6"
            >
              <div className="bg-blue-500 h-2 rounded-full" />
            </motion.div>
            
            <div className="flex items-center justify-center space-x-2 text-blue-500">
              <SparklesIcon className="h-5 w-5" />
              <span className="text-sm">Setting up your access...</span>
            </div>
          </motion.div>
        </motion.div>
      </div>
    );
  }

  if (!invitation || invitation.error) {
    return (
      <div className="min-h-screen bg-surface flex items-center justify-center">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-surface-elevated rounded-xl shadow-default p-8 max-w-md w-full mx-4 text-center"
        >
          <XCircleIcon className="h-16 w-16 text-red-500 mx-auto mb-6" />
          <h2 className="text-xl font-semibold text-primary mb-2">Invalid Invitation</h2>
          <p className="text-secondary mb-6">
            {invitation?.error || 'This invitation link is invalid or has expired.'}
          </p>
          <button
            onClick={() => navigate('/')}
            className="btn btn-primary w-full"
          >
            Go to Homepage
          </button>
        </motion.div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-surface flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0, scale: 0.9 }}
        animate={{ opacity: 1, scale: 1 }}
        className="bg-surface-elevated rounded-xl shadow-default p-8 max-w-2xl w-full"
      >
        {/* Header */}
        <div className="text-center mb-8">
          <motion.div
            initial={{ scale: 0 }}
            animate={{ scale: 1 }}
            transition={{ delay: 0.2, type: "spring", stiffness: 200 }}
            className="w-20 h-20 mx-auto mb-6 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center"
          >
            <UserGroupIcon className="h-10 w-10 text-white" />
          </motion.div>
          
          <h1 className="text-2xl font-bold text-primary mb-2">You're Invited!</h1>
          <p className="text-secondary">
            <span className="font-semibold text-primary">{invitation.invitedBy.name}</span> has invited you to join their workspace
          </p>
        </div>

        {/* Workspace Details */}
        <div className="space-y-6 mb-8">
          {/* Workspace Info */}
          <div className="bg-surface p-6 rounded-xl border border-default">
            <div className="flex items-start space-x-4">
              <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                <BuildingOfficeIcon className="h-6 w-6 text-white" />
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-semibold text-primary mb-1">{invitation.workspace.name}</h3>
                {invitation.workspace.description && (
                  <p className="text-secondary text-sm mb-3">{invitation.workspace.description}</p>
                )}
                <div className="flex items-center space-x-4 text-sm text-secondary">
                  <div className="flex items-center space-x-1">
                    <UserGroupIcon className="h-4 w-4" />
                    <span>{invitation.workspace.memberCount} members</span>
                  </div>
                  <div className="flex items-center space-x-1">
                    <ClockIcon className="h-4 w-4" />
                    <span>Created {new Date(invitation.workspace.createdAt).toLocaleDateString()}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Role Information */}
          <div className="bg-surface p-6 rounded-xl border border-default">
            <div className="flex items-start space-x-4">
              <div className={`p-3 rounded-xl bg-surface-elevated`}>
                {React.createElement(getRoleIcon(invitation.role), {
                  className: `h-6 w-6 ${getRoleColor(invitation.role)}`
                })}
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-semibold text-primary mb-1 capitalize">
                  {invitation.role} Access
                </h3>
                <p className="text-secondary text-sm mb-3">
                  {getRoleDescription(invitation.role)}
                </p>
                
                {/* Role Permissions */}
                <div className="space-y-2">
                  {invitation.permissions && invitation.permissions.length > 0 && (
                    <div>
                      <h4 className="font-medium text-primary text-sm mb-2">Permissions:</h4>
                      <div className="grid grid-cols-2 gap-2">
                        {invitation.permissions.map((permission, index) => (
                          <div key={index} className="flex items-center space-x-2 text-sm text-secondary">
                            <CheckCircleIcon className="h-4 w-4 text-green-500" />
                            <span className="capitalize">{permission.replace('_', ' ')}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>

          {/* Workspace Features */}
          {invitation.workspace.features && invitation.workspace.features.length > 0 && (
            <div className="bg-surface p-6 rounded-xl border border-default">
              <h3 className="text-lg font-semibold text-primary mb-4">Available Features</h3>
              <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                {invitation.workspace.features.slice(0, 9).map((feature, index) => (
                  <div key={index} className="flex items-center space-x-2 text-sm">
                    <CheckCircleIcon className="h-4 w-4 text-green-500 flex-shrink-0" />
                    <span className="text-secondary capitalize">{feature.replace('_', ' ')}</span>
                  </div>
                ))}
                {invitation.workspace.features.length > 9 && (
                  <div className="text-sm text-secondary">
                    +{invitation.workspace.features.length - 9} more features
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Invitation Details */}
          <div className="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
            <div className="flex items-center space-x-3">
              <div className="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <img
                  src={invitation.invitedBy.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(invitation.invitedBy.name)}&background=3B82F6&color=fff`}
                  alt={invitation.invitedBy.name}
                  className="w-8 h-8 rounded-full"
                />
              </div>
              <div>
                <p className="text-sm font-medium text-primary">
                  Invited by {invitation.invitedBy.name}
                </p>
                <p className="text-xs text-secondary">
                  {invitation.invitedBy.email} • {invitation.invitedBy.role}
                </p>
              </div>
            </div>
          </div>

          {/* Expiration Warning */}
          {invitation.expiresAt && (
            <div className="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-xl border border-orange-200 dark:border-orange-800">
              <div className="flex items-center space-x-2">
                <ClockIcon className="h-5 w-5 text-orange-500" />
                <div>
                  <p className="text-sm font-medium text-orange-800 dark:text-orange-200">
                    Invitation expires soon
                  </p>
                  <p className="text-xs text-orange-600 dark:text-orange-300">
                    This invitation will expire on {new Date(invitation.expiresAt).toLocaleString()}
                  </p>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Actions */}
        <div className="flex flex-col sm:flex-row gap-4">
          <button
            onClick={handleDeclineInvitation}
            disabled={declining}
            className="btn btn-secondary flex-1 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {declining ? (
              <div className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin mr-2" />
            ) : null}
            {declining ? 'Declining...' : 'Decline'}
          </button>
          
          <button
            onClick={handleAcceptInvitation}
            disabled={accepting}
            className="btn btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
          >
            {accepting ? (
              <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2" />
            ) : (
              <ArrowRightIcon className="h-4 w-4 mr-2" />
            )}
            {accepting ? 'Joining...' : 'Accept & Join Workspace'}
          </button>
        </div>

        {/* Footer */}
        <div className="mt-8 pt-6 border-t border-default text-center">
          <p className="text-xs text-secondary">
            By accepting this invitation, you agree to the workspace's terms and conditions.
          </p>
        </div>
      </motion.div>
    </div>
  );
};

export default WorkspaceInvitationPage;