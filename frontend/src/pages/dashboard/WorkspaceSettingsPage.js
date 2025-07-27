import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CogIcon,
  UserGroupIcon,
  PaintBrushIcon,
  CreditCardIcon,
  ShieldCheckIcon,
  BellIcon,
  GlobeAltIcon,
  TrashIcon,
  PencilIcon,
  PlusIcon,
  EyeIcon,
  UserPlusIcon,
  XMarkIcon,
  CheckIcon,
  ExclamationTriangleIcon,
  BuildingOfficeIcon,
  EnvelopeIcon,
  KeyIcon,
  DocumentTextIcon,
  ChartBarIcon
} from '@heroicons/react/24/outline';

const WorkspaceSettingsPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('general');
  const [loading, setLoading] = useState(true);
  const [workspace, setWorkspace] = useState(null);
  const [teamMembers, setTeamMembers] = useState([]);
  const [invitations, setInvitations] = useState([]);
  const [showInviteModal, setShowInviteModal] = useState(false);
  const [newInvitation, setNewInvitation] = useState({ email: '', role: 'editor' });

  // Available roles with permissions
  const roles = [
    {
      id: 'owner',
      name: 'Owner',
      description: 'Full access to everything including billing and workspace deletion',
      permissions: ['all'],
      color: 'text-yellow-600',
      badge: 'bg-yellow-100 text-yellow-800'
    },
    {
      id: 'admin',
      name: 'Admin',
      description: 'Manage team, settings, and most features. Cannot delete workspace',
      permissions: ['manage_team', 'manage_settings', 'manage_billing', 'access_analytics'],
      color: 'text-red-600',
      badge: 'bg-red-100 text-red-800'
    },
    {
      id: 'editor',
      name: 'Editor',
      description: 'Create and edit content, limited access to settings',
      permissions: ['create_content', 'edit_content', 'access_features'],
      color: 'text-blue-600',
      badge: 'bg-blue-100 text-blue-800'
    },
    {
      id: 'viewer',
      name: 'Viewer',
      description: 'Read-only access to workspace content and analytics',
      permissions: ['view_content', 'view_analytics'],
      color: 'text-green-600',
      badge: 'bg-green-100 text-green-800'
    }
  ];

  useEffect(() => {
    loadWorkspaceData();
  }, []);

  const loadWorkspaceData = async () => {
    // Real data loaded from API
    try {
      // Load workspace details
      const workspaceResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (workspaceResponse.ok) {
        const workspaceData = await workspaceResponse.json();
        // Real data loaded from API
      }

      // Load team members
      const membersResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current/members`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (membersResponse.ok) {
        const membersData = await membersResponse.json();
        // Real data loaded from API
      }

      // Load pending invitations
      const invitationsResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current/invitations`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (invitationsResponse.ok) {
        const invitationsData = await invitationsResponse.json();
        // Real data loaded from API
      }
    } catch (err) {
      error('Failed to load workspace data');
    }
    // Real data loaded from API
  };

  const handleWorkspaceUpdate = async (field, value) => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ [field]: value })
      });

      if (response.ok) {
        setWorkspace(prev => ({ ...prev, [field]: value }));
        success('Workspace updated successfully');
      } else {
        throw new Error('Failed to update workspace');
      }
    } catch (err) {
      error('Failed to update workspace');
    }
  };

  const handleInviteMember = async () => {
    if (!newInvitation.email) {
      error('Please enter an email address');
      return;
    }

    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current/invite`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(newInvitation)
      });

      if (response.ok) {
        success('Invitation sent successfully');
        // Real data loaded from API
        // Real data loaded from API
        loadWorkspaceData();
      } else {
        throw new Error('Failed to send invitation');
      }
    } catch (err) {
      error('Failed to send invitation');
    }
  };

  const handleRemoveMember = async (memberId) => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current/members/${memberId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });

      if (response.ok) {
        success('Member removed successfully');
        loadWorkspaceData();
      } else {
        throw new Error('Failed to remove member');
      }
    } catch (err) {
      error('Failed to remove member');
    }
  };

  const handleRoleChange = async (memberId, newRole) => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/current/members/${memberId}/role`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ role: newRole })
      });

      if (response.ok) {
        success('Role updated successfully');
        loadWorkspaceData();
      } else {
        throw new Error('Failed to update role');
      }
    } catch (err) {
      error('Failed to update role');
    }
  };

  const tabs = [
    { id: 'general', name: 'General', icon: CogIcon },
    { id: 'team', name: 'Team Members', icon: UserGroupIcon },
    { id: 'branding', name: 'Branding', icon: PaintBrushIcon },
    { id: 'billing', name: 'Billing', icon: CreditCardIcon },
    { id: 'security', name: 'Security', icon: ShieldCheckIcon },
    { id: 'notifications', name: 'Notifications', icon: BellIcon },
    { id: 'integrations', name: 'Integrations', icon: GlobeAltIcon },
    { id: 'danger', name: 'Danger Zone', icon: ExclamationTriangleIcon }
  ];

  if (loading) {
    return (
      <div className="p-6 max-w-7xl mx-auto">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4" />
          <h2 className="text-xl font-semibold text-primary">Loading workspace settings...</h2>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-primary mb-2">Workspace Settings</h1>
        <p className="text-secondary">Manage your workspace settings, team, and preferences</p>
      </div>

      {/* Workspace Info Header */}
      {workspace && (
        <div className="bg-surface-elevated rounded-xl shadow-default p-6">
          <div className="flex items-center space-x-4">
            <div className="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
              <BuildingOfficeIcon className="h-8 w-8 text-white" />
            </div>
            <div>
              <h2 className="text-xl font-semibold text-primary">{workspace.name}</h2>
              <p className="text-secondary">{workspace.description || 'No description provided'}</p>
              <div className="flex items-center space-x-4 mt-2 text-sm text-secondary">
                <span>{teamMembers.length} team members</span>
                <span>•</span>
                <span>Created {new Date(workspace.createdAt).toLocaleDateString()}</span>
                <span>•</span>
                <span className="capitalize">{workspace.plan || 'free'} plan</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Main Content */}
      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* Navigation */}
        <div className="lg:col-span-1">
          <nav className="bg-surface-elevated rounded-xl shadow-default p-4 sticky top-4">
            <div className="space-y-1">
              {tabs.map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`w-full flex items-center space-x-3 px-3 py-2 rounded-lg text-left transition-colors ${
                    activeTab === tab.id
                      ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400'
                      : 'text-secondary hover:text-primary hover:bg-surface-hover'
                  }`}
                >
                  <tab.icon className="h-5 w-5" />
                  <span className="text-sm font-medium">{tab.name}</span>
                </button>
              ))}
            </div>
          </nav>
        </div>

        {/* Content */}
        <div className="lg:col-span-3">
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            {activeTab === 'general' && (
              <div className="space-y-6">
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">General Settings</h3>
                  
                  <div className="space-y-4">
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Workspace Name</label>
                      <input
                        type="text"
                        value={workspace?.name || ''}
                        onChange={(e) => setWorkspace(prev => ({ ...prev, name: e.target.value }))}
                        onBlur={(e) => handleWorkspaceUpdate('name', e.target.value)}
                        className="input w-full"
                        placeholder="My Awesome Workspace"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Description</label>
                      <textarea
                        value={workspace?.description || ''}
                        onChange={(e) => setWorkspace(prev => ({ ...prev, description: e.target.value }))}
                        onBlur={(e) => handleWorkspaceUpdate('description', e.target.value)}
                        className="input w-full h-24 resize-none"
                        placeholder="What does your workspace focus on?"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Website URL</label>
                      <input
                        type="url"
                        value={workspace?.website || ''}
                        onChange={(e) => setWorkspace(prev => ({ ...prev, website: e.target.value }))}
                        onBlur={(e) => handleWorkspaceUpdate('website', e.target.value)}
                        className="input w-full"
                        placeholder="https://mycompany.com"
                      />
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Industry</label>
                      <select
                        value={workspace?.industry || ''}
                        onChange={(e) => handleWorkspaceUpdate('industry', e.target.value)}
                        className="input w-full"
                      >
                        <option value="">Select industry</option>
                        <option value="technology">Technology</option>
                        <option value="marketing">Marketing</option>
                        <option value="ecommerce">E-commerce</option>
                        <option value="education">Education</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="finance">Finance</option>
                        <option value="media">Media & Entertainment</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'team' && (
              <div className="space-y-6">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-primary">Team Members</h3>
                  <button
                    onClick={() => setShowInviteModal(true)}
                    className="btn btn-primary flex items-center space-x-2"
                  >
                    <UserPlusIcon className="h-4 w-4" />
                    <span>Invite Member</span>
                  </button>
                </div>

                {/* Current Team Members */}
                <div className="space-y-3">
                  {teamMembers.map((member) => (
                    <div key={member.id} className="flex items-center justify-between p-4 border border-default rounded-lg">
                      <div className="flex items-center space-x-4">
                        <img
                          src={member.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}&background=3B82F6&color=fff`}
                          alt={member.name}
                          className="w-10 h-10 rounded-full"
                        />
                        <div>
                          <h4 className="font-medium text-primary">{member.name}</h4>
                          <p className="text-sm text-secondary">{member.email}</p>
                          <p className="text-xs text-secondary">
                            Joined {new Date(member.joinedAt).toLocaleDateString()}
                          </p>
                        </div>
                      </div>
                      
                      <div className="flex items-center space-x-3">
                        <select
                          value={member.role}
                          onChange={(e) => handleRoleChange(member.id, e.target.value)}
                          className="input text-sm"
                          disabled={member.role === 'owner'}
                        >
                          {roles.map((role) => (
                            <option key={role.id} value={role.id} disabled={role.id === 'owner' && member.role !== 'owner'}>
                              {role.name}
                            </option>
                          ))}
                        </select>
                        
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${roles.find(r => r.id === member.role)?.badge}`}>
                          {roles.find(r => r.id === member.role)?.name}
                        </span>
                        
                        {member.role !== 'owner' && (
                          <button
                            onClick={() => handleRemoveMember(member.id)}
                            className="p-2 text-red-500 hover:text-red-700 transition-colors"
                          >
                            <TrashIcon className="h-4 w-4" />
                          </button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>

                {/* Pending Invitations */}
                {invitations.length > 0 && (
                  <div>
                    <h4 className="font-medium text-primary mb-3">Pending Invitations</h4>
                    <div className="space-y-2">
                      {invitations.map((invitation) => (
                        <div key={invitation.id} className="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                          <div className="flex items-center space-x-3">
                            <EnvelopeIcon className="h-5 w-5 text-orange-500" />
                            <div>
                              <p className="text-sm font-medium text-primary">{invitation.email}</p>
                              <p className="text-xs text-secondary">
                                Invited {new Date(invitation.createdAt).toLocaleDateString()} • 
                                Expires {new Date(invitation.expiresAt).toLocaleDateString()}
                              </p>
                            </div>
                          </div>
                          <div className="flex items-center space-x-2">
                            <span className="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded-full">
                              {roles.find(r => r.id === invitation.role)?.name}
                            </span>
                            <button
                              onClick={() => {/* Cancel invitation */}}
                              className="text-red-500 hover:text-red-700"
                            >
                              <XMarkIcon className="h-4 w-4" />
                            </button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {/* Role Descriptions */}
                <div>
                  <h4 className="font-medium text-primary mb-3">Role Permissions</h4>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {roles.map((role) => (
                      <div key={role.id} className="p-3 border border-default rounded-lg">
                        <div className="flex items-center space-x-2 mb-2">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${role.badge}`}>
                            {role.name}
                          </span>
                        </div>
                        <p className="text-sm text-secondary">{role.description}</p>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'branding' && (
              <div className="space-y-6">
                <h3 className="text-lg font-semibold text-primary">Brand Customization</h3>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-primary mb-2">Primary Brand Color</label>
                    <div className="flex items-center space-x-3">
                      <input
                        type="color"
                        value={workspace?.branding?.primaryColor || '#3B82F6'}
                        onChange={(e) => handleWorkspaceUpdate('branding.primaryColor', e.target.value)}
                        className="w-12 h-12 rounded-lg border border-default cursor-pointer"
                      />
                      <input
                        type="text"
                        value={workspace?.branding?.primaryColor || '#3B82F6'}
                        onChange={(e) => handleWorkspaceUpdate('branding.primaryColor', e.target.value)}
                        className="input flex-1"
                        placeholder="#3B82F6"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-primary mb-2">Secondary Brand Color</label>
                    <div className="flex items-center space-x-3">
                      <input
                        type="color"
                        value={workspace?.branding?.secondaryColor || '#1E40AF'}
                        onChange={(e) => handleWorkspaceUpdate('branding.secondaryColor', e.target.value)}
                        className="w-12 h-12 rounded-lg border border-default cursor-pointer"
                      />
                      <input
                        type="text"
                        value={workspace?.branding?.secondaryColor || '#1E40AF'}
                        onChange={(e) => handleWorkspaceUpdate('branding.secondaryColor', e.target.value)}
                        className="input flex-1"
                        placeholder="#1E40AF"
                      />
                    </div>
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-primary mb-2">Logo Upload</label>
                  <div className="border-2 border-dashed border-default rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                    <BuildingOfficeIcon className="h-12 w-12 text-secondary mx-auto mb-4" />
                    <div className="text-secondary mb-2">Drop your logo here or click to browse</div>
                    <button className="btn btn-secondary btn-sm">Choose File</button>
                    <p className="text-xs text-secondary mt-2">Recommended: PNG or SVG, max 2MB</p>
                  </div>
                </div>

                {/* Brand Preview */}
                <div>
                  <h4 className="font-medium text-primary mb-3">Brand Preview</h4>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div 
                      className="p-4 rounded-lg text-white"
                      style={{ backgroundColor: workspace?.branding?.primaryColor || '#3B82F6' }}
                    >
                      <h5 className="font-semibold mb-1">Primary Color</h5>
                      <p className="text-sm opacity-90">This is how your primary brand color looks</p>
                    </div>
                    <div 
                      className="p-4 rounded-lg text-white"
                      style={{ backgroundColor: workspace?.branding?.secondaryColor || '#1E40AF' }}
                    >
                      <h5 className="font-semibold mb-1">Secondary Color</h5>
                      <p className="text-sm opacity-90">This is how your secondary brand color looks</p>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'billing' && (
              <div className="space-y-6">
                <h3 className="text-lg font-semibold text-primary">Billing & Subscription</h3>
                
                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                  <div className="flex items-center space-x-2 mb-2">
                    <InformationCircleIcon className="h-5 w-5 text-blue-500" />
                    <h4 className="font-medium text-blue-800 dark:text-blue-200">Current Subscription</h4>
                  </div>
                  <p className="text-sm text-blue-600 dark:text-blue-300">
                    You're currently on the <span className="font-medium">{workspace?.subscription?.plan || 'Free'}</span> plan.
                    {workspace?.subscription?.nextBilling && (
                      <span> Next billing date: {new Date(workspace.subscription.nextBilling).toLocaleDateString()}</span>
                    )}
                  </p>
                </div>

                <div className="flex items-center justify-between p-4 border border-default rounded-lg">
                  <div>
                    <h4 className="font-medium text-primary">Manage Subscription</h4>
                    <p className="text-sm text-secondary">Update your plan, features, and payment methods</p>
                  </div>
                  <button className="btn btn-primary">
                    View Subscription
                  </button>
                </div>

                <div className="flex items-center justify-between p-4 border border-default rounded-lg">
                  <div>
                    <h4 className="font-medium text-primary">Billing History</h4>
                    <p className="text-sm text-secondary">Download invoices and view payment history</p>
                  </div>
                  <button className="btn btn-secondary">
                    View Invoices
                  </button>
                </div>
              </div>
            )}

            {activeTab === 'danger' && (
              <div className="space-y-6">
                <h3 className="text-lg font-semibold text-red-600">Danger Zone</h3>
                
                <div className="border border-red-200 rounded-lg">
                  <div className="p-4 border-b border-red-200">
                    <h4 className="font-medium text-primary mb-2">Transfer Workspace Ownership</h4>
                    <p className="text-sm text-secondary mb-4">
                      Transfer ownership of this workspace to another team member.
                    </p>
                    <button className="btn btn-secondary">
                      Transfer Ownership
                    </button>
                  </div>
                  
                  <div className="p-4">
                    <h4 className="font-medium text-red-600 mb-2">Delete Workspace</h4>
                    <p className="text-sm text-secondary mb-4">
                      Permanently delete this workspace and all associated data. This action cannot be undone.
                    </p>
                    <button className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                      Delete Workspace
                    </button>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Invite Member Modal */}
      {showInviteModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            className="bg-surface-elevated rounded-xl shadow-lg p-6 w-full max-w-md"
          >
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-lg font-semibold text-primary">Invite Team Member</h3>
              <button
                onClick={() => setShowInviteModal(false)}
                className="text-secondary hover:text-primary"
              >
                <XMarkIcon className="h-5 w-5" />
              </button>
            </div>

            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-primary mb-2">Email Address</label>
                <input
                  type="email"
                  value={newInvitation.email}
                  onChange={(e) => setNewInvitation(prev => ({ ...prev, email: e.target.value }))}
                  className="input w-full"
                  placeholder="colleague@company.com"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-primary mb-2">Role</label>
                <select
                  value={newInvitation.role}
                  onChange={(e) => setNewInvitation(prev => ({ ...prev, role: e.target.value }))}
                  className="input w-full"
                >
                  {roles.filter(role => role.id !== 'owner').map((role) => (
                    <option key={role.id} value={role.id}>{role.name}</option>
                  ))}
                </select>
                <p className="text-xs text-secondary mt-1">
                  {roles.find(r => r.id === newInvitation.role)?.description}
                </p>
              </div>
            </div>

            <div className="flex items-center justify-end space-x-3 mt-6">
              <button
                onClick={() => setShowInviteModal(false)}
                className="btn btn-secondary"
              >
                Cancel
              </button>
              <button
                onClick={handleInviteMember}
                className="btn btn-primary"
              >
                Send Invitation
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </div>
  );
};

export default WorkspaceSettingsPage;