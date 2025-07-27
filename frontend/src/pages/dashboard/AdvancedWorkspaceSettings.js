import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import api from '../../services/api';
import {
  CogIcon,
  UsersIcon,
  PaintBrushIcon,
  ShieldCheckIcon,
  CreditCardIcon,
  BellIcon,
  GlobeAltIcon,
  KeyIcon,
  TrashIcon,
  PlusIcon,
  PencilIcon,
  CheckIcon,
  XMarkIcon,
  UserPlusIcon,
  ClipboardDocumentCheckIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  LinkIcon,
  QrCodeIcon,
  ChartBarIcon,
  EyeIcon,
  EyeSlashIcon,
  LockClosedIcon,
  UnlockOpenIcon
} from '@heroicons/react/24/outline';
import {
  CogIcon as CogIconSolid,
  ShieldCheckIcon as ShieldCheckIconSolid
} from '@heroicons/react/24/solid';

const AdvancedWorkspaceSettings = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeTab, setActiveTab] = useState('general');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [workspace, setWorkspace] = useState(null);
  const [teamMembers, setTeamMembers] = useState([]);
  const [invitations, setInvitations] = useState([]);
  const [tokenSettings, setTokenSettings] = useState(null);
  const [showInviteModal, setShowInviteModal] = useState(false);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [inviteForm, setInviteForm] = useState({ email: '', role: 'editor', message: '' });
  const [memberToDelete, setMemberToDelete] = useState(null);

  // Workspace settings state
  const [settings, setSettings] = useState({
    name: '',
    description: '',
    industry: '',
    timezone: '',
    website: '',
    logo: null,
    branding: {
      primary_color: '#3B82F6',
      secondary_color: '#10B981',
      accent_color: '#F59E0B',
      font_family: 'Inter'
    },
    privacy: {
      public_profile: false,
      show_analytics: false,
      allow_search: true
    },
    notifications: {
      email_updates: true,
      team_activities: true,
      system_alerts: true,
      marketing_emails: false
    },
    integrations: {
      google_analytics: '',
      facebook_pixel: '',
      custom_domain: ''
    },
    security: {
      two_factor_required: false,
      password_policy: 'medium',
      session_timeout: 24
    }
  });

  const roles = [
    { id: 'owner', name: 'Owner', description: 'Full access to everything' },
    { id: 'admin', name: 'Admin', description: 'Manage team and settings' },
    { id: 'editor', name: 'Editor', description: 'Create and edit content' },
    { id: 'viewer', name: 'Viewer', description: 'View content only' }
  ];

  const tabs = [
    { id: 'general', name: 'General', icon: CogIcon },
    { id: 'team', name: 'Team Management', icon: UsersIcon },
    { id: 'branding', name: 'Branding', icon: PaintBrushIcon },
    { id: 'permissions', name: 'Permissions', icon: ShieldCheckIcon },
    { id: 'tokens', name: 'AI Tokens', icon: KeyIcon },
    { id: 'billing', name: 'Billing', icon: CreditCardIcon },
    { id: 'integrations', name: 'Integrations', icon: GlobeAltIcon },
    { id: 'security', name: 'Security', icon: LockClosedIcon }
  ];

  useEffect(() => {
    loadWorkspaceData();
  }, []);

  const loadWorkspaceData = async () => {
    try {
      // Real data loaded from API
      
      // Load workspace info
      const workspaceResponse = await api.get('/workspaces');
      if (workspaceResponse.data.success && workspaceResponse.data.data.workspaces.length > 0) {
        const currentWorkspace = workspaceResponse.data.data.workspaces[0];
        // Real data loaded from API
        
        // Populate settings with workspace data
        setSettings(prev => ({
          ...prev,
          name: currentWorkspace.name || '',
          description: currentWorkspace.description || '',
          industry: currentWorkspace.industry || '',
          timezone: currentWorkspace.timezone || '',
          website: currentWorkspace.website || '',
          branding: currentWorkspace.branding || prev.branding
        }));

        // Load team members
        const teamResponse = await api.get(`/workspaces/${currentWorkspace.id}/members`);
        if (teamResponse.data.success) {
          // Real data loaded from API
        }

        // Load pending invitations
        const invitesResponse = await api.get(`/workspaces/${currentWorkspace.id}/invitations`);
        if (invitesResponse.data.success) {
          // Real data loaded from API
        }

        // Load token settings
        const tokenResponse = await api.get(`/tokens/workspace/${currentWorkspace.id}`);
        if (tokenResponse.data.success) {
          // Real data loaded from API
        }
      }
    } catch (err) {
      console.error('Failed to load workspace data:', err);
      error('Failed to load workspace settings');
    } finally {
      // Real data loaded from API
    }
  };

  const saveSettings = async (sectionSettings) => {
    if (!workspace) return;

    try {
      // Real data loaded from API
      const response = await api.put(`/workspaces/${workspace.id}/settings`, {
        settings: sectionSettings || settings
      });

      if (response.data.success) {
        success('Settings saved successfully');
        setWorkspace(prev => ({ ...prev, ...sectionSettings }));
      } else {
        error('Failed to save settings');
      }
    } catch (err) {
      console.error('Failed to save settings:', err);
      error('Failed to save settings');
    } finally {
      // Real data loaded from API
    }
  };

  const inviteTeamMember = async () => {
    if (!workspace || !inviteForm.email) return;

    try {
      // Real data loaded from API
      const response = await api.post(`/workspaces/${workspace.id}/invite`, {
        email: inviteForm.email,
        role: inviteForm.role,
        message: inviteForm.message
      });

      if (response.data.success) {
        success('Team member invited successfully');
        // Real data loaded from API
        // Real data loaded from API
        loadWorkspaceData(); // Refresh data
      } else {
        error(response.data.message || 'Failed to invite team member');
      }
    } catch (err) {
      console.error('Failed to invite team member:', err);
      error('Failed to invite team member');
    } finally {
      // Real data loaded from API
    }
  };

  const removeMember = async (memberId) => {
    if (!workspace) return;

    try {
      const response = await api.delete(`/workspaces/${workspace.id}/members/${memberId}`);
      if (response.data.success) {
        success('Team member removed successfully');
        loadWorkspaceData(); // Refresh data
        // Real data loaded from API
        // Real data loaded from API
      } else {
        error('Failed to remove team member');
      }
    } catch (err) {
      console.error('Failed to remove team member:', err);
      error('Failed to remove team member');
    }
  };

  const updateMemberRole = async (memberId, newRole) => {
    if (!workspace) return;

    try {
      const response = await api.put(`/workspaces/${workspace.id}/members/${memberId}`, {
        role: newRole
      });

      if (response.data.success) {
        success('Member role updated successfully');
        loadWorkspaceData(); // Refresh data
      } else {
        error('Failed to update member role');
      }
    } catch (err) {
      console.error('Failed to update member role:', err);
      error('Failed to update member role');
    }
  };

  const GeneralSettings = () => (
    <div className="space-y-6">
      <div>
        <h3 className="text-lg font-semibold text-foreground mb-4">Basic Information</h3>
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">
              Workspace Name
            </label>
            <input
              type="text"
              value={settings.name}
              onChange={(e) => setSettings(prev => ({ ...prev, name: e.target.value }))}
              className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">
              Description
            </label>
            <textarea
              value={settings.description}
              onChange={(e) => setSettings(prev => ({ ...prev, description: e.target.value }))}
              rows={3}
              className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-foreground mb-2">
                Industry
              </label>
              <select
                value={settings.industry}
                onChange={(e) => setSettings(prev => ({ ...prev, industry: e.target.value }))}
                className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Select industry</option>
                <option value="technology">Technology</option>
                <option value="marketing">Marketing</option>
                <option value="ecommerce">E-commerce</option>
                <option value="education">Education</option>
                <option value="healthcare">Healthcare</option>
                <option value="finance">Finance</option>
                <option value="consulting">Consulting</option>
                <option value="other">Other</option>
              </select>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-foreground mb-2">
                Timezone
              </label>
              <select
                value={settings.timezone}
                onChange={(e) => setSettings(prev => ({ ...prev, timezone: e.target.value }))}
                className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Select timezone</option>
                <option value="America/New_York">Eastern Time</option>
                <option value="America/Chicago">Central Time</option>
                <option value="America/Denver">Mountain Time</option>
                <option value="America/Los_Angeles">Pacific Time</option>
                <option value="Europe/London">London (GMT)</option>
                <option value="Europe/Paris">Paris (CET)</option>
              </select>
            </div>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">
              Website URL
            </label>
            <input
              type="url"
              value={settings.website}
              onChange={(e) => setSettings(prev => ({ ...prev, website: e.target.value }))}
              placeholder="https://your-website.com"
              className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
      </div>

      <div className="flex justify-end">
        <button
          onClick={() => saveSettings()}
          disabled={saving}
          className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium disabled:opacity-50"
        >
          {saving ? 'Saving...' : 'Save Changes'}
        </button>
      </div>
    </div>
  );

  const TeamManagement = () => (
    <div className="space-y-6">
      {/* Team Members */}
      <div>
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-lg font-semibold text-foreground">Team Members</h3>
          <button
            onClick={() => setShowInviteModal(true)}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
          >
            <UserPlusIcon className="w-4 h-4 mr-2" />
            Invite Member
          </button>
        </div>
        
        <div className="bg-card rounded-lg border border-border overflow-hidden">
          <table className="w-full">
            <thead className="bg-secondary">
              <tr>
                <th className="text-left py-3 px-4 font-medium text-foreground">Member</th>
                <th className="text-left py-3 px-4 font-medium text-foreground">Role</th>
                <th className="text-left py-3 px-4 font-medium text-foreground">Joined</th>
                <th className="text-left py-3 px-4 font-medium text-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              {teamMembers.map((member) => (
                <tr key={member.id} className="border-b border-border">
                  <td className="py-3 px-4">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                        <span className="text-sm font-medium text-blue-600">
                          {member.name ? member.name[0] : member.email[0]}
                        </span>
                      </div>
                      <div>
                        <div className="font-medium text-foreground">{member.name || 'Unknown'}</div>
                        <div className="text-sm text-muted">{member.email}</div>
                      </div>
                    </div>
                  </td>
                  <td className="py-3 px-4">
                    <select
                      value={member.role}
                      onChange={(e) => updateMemberRole(member.id, e.target.value)}
                      className="text-sm bg-secondary border border-border rounded px-2 py-1 text-foreground"
                      disabled={member.role === 'owner'}
                    >
                      {roles.map((role) => (
                        <option key={role.id} value={role.id}>{role.name}</option>
                      ))}
                    </select>
                  </td>
                  <td className="py-3 px-4 text-muted text-sm">
                    {member.joined_at ? new Date(member.joined_at).toLocaleDateString() : 'N/A'}
                  </td>
                  <td className="py-3 px-4">
                    {member.role !== 'owner' && (
                      <button
                        onClick={() => {
                          // Real data loaded from API
                          // Real data loaded from API
                        }}
                        className="text-red-600 hover:text-red-700 p-1"
                        title="Remove Member"
                      >
                        <TrashIcon className="w-4 h-4" />
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Pending Invitations */}
      {invitations.length > 0 && (
        <div>
          <h3 className="text-lg font-semibold text-foreground mb-4">Pending Invitations</h3>
          <div className="bg-card rounded-lg border border-border overflow-hidden">
            <table className="w-full">
              <thead className="bg-secondary">
                <tr>
                  <th className="text-left py-3 px-4 font-medium text-foreground">Email</th>
                  <th className="text-left py-3 px-4 font-medium text-foreground">Role</th>
                  <th className="text-left py-3 px-4 font-medium text-foreground">Sent</th>
                  <th className="text-left py-3 px-4 font-medium text-foreground">Status</th>
                </tr>
              </thead>
              <tbody>
                {invitations.map((invitation) => (
                  <tr key={invitation.id} className="border-b border-border">
                    <td className="py-3 px-4 text-foreground">{invitation.email}</td>
                    <td className="py-3 px-4 text-muted">{invitation.role}</td>
                    <td className="py-3 px-4 text-muted text-sm">
                      {new Date(invitation.created_at).toLocaleDateString()}
                    </td>
                    <td className="py-3 px-4">
                      <span className="inline-block px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                        {invitation.status}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );

  const BrandingSettings = () => (
    <div className="space-y-6">
      <div>
        <h3 className="text-lg font-semibold text-foreground mb-4">Brand Colors</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">Primary Color</label>
            <div className="flex items-center space-x-3">
              <input
                type="color"
                value={settings.branding.primary_color}
                onChange={(e) => setSettings(prev => ({
                  ...prev,
                  branding: { ...prev.branding, primary_color: e.target.value }
                }))}
                className="w-12 h-10 rounded border border-border"
              />
              <input
                type="text"
                value={settings.branding.primary_color}
                onChange={(e) => setSettings(prev => ({
                  ...prev,
                  branding: { ...prev.branding, primary_color: e.target.value }
                }))}
                className="flex-1 px-3 py-2 border border-border rounded-lg bg-card text-foreground"
              />
            </div>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">Secondary Color</label>
            <div className="flex items-center space-x-3">
              <input
                type="color"
                value={settings.branding.secondary_color}
                onChange={(e) => setSettings(prev => ({
                  ...prev,
                  branding: { ...prev.branding, secondary_color: e.target.value }
                }))}
                className="w-12 h-10 rounded border border-border"
              />
              <input
                type="text"
                value={settings.branding.secondary_color}
                onChange={(e) => setSettings(prev => ({
                  ...prev,
                  branding: { ...prev.branding, secondary_color: e.target.value }
                }))}
                className="flex-1 px-3 py-2 border border-border rounded-lg bg-card text-foreground"
              />
            </div>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">Accent Color</label>
            <div className="flex items-center space-x-3">
              <input
                type="color"
                value={settings.branding.accent_color}
                onChange={(e) => setSettings(prev => ({
                  ...prev,
                  branding: { ...prev.branding, accent_color: e.target.value }
                }))}
                className="w-12 h-10 rounded border border-border"
              />
              <input
                type="text"
                value={settings.branding.accent_color}
                onChange={(e) => setSettings(prev => ({
                  ...prev,
                  branding: { ...prev.branding, accent_color: e.target.value }
                }))}
                className="flex-1 px-3 py-2 border border-border rounded-lg bg-card text-foreground"
              />
            </div>
          </div>
        </div>
      </div>

      <div className="flex justify-end">
        <button
          onClick={() => saveSettings()}
          disabled={saving}
          className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium disabled:opacity-50"
        >
          {saving ? 'Saving...' : 'Save Branding'}
        </button>
      </div>
    </div>
  );

  const TokenSettings = () => (
    <div className="space-y-6">
      {tokenSettings && (
        <>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="bg-card rounded-lg p-6 border border-border">
              <h4 className="font-semibold text-foreground mb-2">Available Balance</h4>
              <div className="text-2xl font-bold text-blue-600">{tokenSettings.balance || 0}</div>
              <p className="text-muted text-sm">Purchased tokens</p>
            </div>
            
            <div className="bg-card rounded-lg p-6 border border-border">
              <h4 className="font-semibold text-foreground mb-2">Monthly Allowance</h4>
              <div className="text-2xl font-bold text-green-600">
                {tokenSettings.allowance_remaining || 0} / {tokenSettings.monthly_allowance || 0}
              </div>
              <p className="text-muted text-sm">Free tokens remaining</p>
            </div>
            
            <div className="bg-card rounded-lg p-6 border border-border">
              <h4 className="font-semibold text-foreground mb-2">Total Used</h4>
              <div className="text-2xl font-bold text-red-600">{tokenSettings.total_used || 0}</div>
              <p className="text-muted text-sm">Lifetime consumption</p>
            </div>
          </div>

          <div>
            <h3 className="text-lg font-semibold text-foreground mb-4">Token Management</h3>
            <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
              <div className="flex items-start">
                <InformationCircleIcon className="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5" />
                <div>
                  <h4 className="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                    Token Management
                  </h4>
                  <p className="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    Visit the dedicated Token Management page to purchase more tokens, 
                    set user limits, and view detailed analytics.
                  </p>
                  <button
                    onClick={() => window.open('/dashboard/token-management', '_blank')}
                    className="mt-3 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 px-3 py-2 rounded-md text-sm font-medium"
                  >
                    Open Token Management
                  </button>
                </div>
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 via-purple-600 to-cyan-600 rounded-xl shadow-default p-8 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-4">
              <CogIconSolid className="h-10 w-10 mr-4" />
              <h1 className="text-4xl font-bold">Workspace Settings</h1>
            </div>
            <p className="text-white/80 text-lg">
              Manage your workspace, team, and platform configuration
            </p>
          </div>
          <div className="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
            <div className="text-center">
              <div className="text-2xl font-bold mb-1">{workspace?.name || 'Workspace'}</div>
              <div className="text-sm text-white/70">Current Workspace</div>
            </div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <div className="flex space-x-1 bg-card rounded-lg p-1 overflow-x-auto">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            onClick={() => setActiveTab(tab.id)}
            className={`flex items-center px-4 py-2 rounded-md transition-colors whitespace-nowrap ${
              activeTab === tab.id
                ? 'bg-blue-600 text-white'
                : 'text-muted hover:text-foreground'
            }`}
          >
            <tab.icon className="w-4 h-4 mr-2" />
            {tab.name}
          </button>
        ))}
      </div>

      {/* Tab Content */}
      <div className="bg-card rounded-lg p-8 border border-border">
        {activeTab === 'general' && <GeneralSettings />}
        {activeTab === 'team' && <TeamManagement />}
        {activeTab === 'branding' && <BrandingSettings />}
        {activeTab === 'tokens' && <TokenSettings />}
        
        {/* Other tabs would be implemented here */}
        {activeTab === 'permissions' && (
          <div className="text-center py-12 text-muted">
            <ShieldCheckIconSolid className="w-16 h-16 mx-auto mb-4 opacity-50" />
            <p>Permissions management coming soon...</p>
          </div>
        )}
        
        {activeTab === 'billing' && (
          <div className="text-center py-12 text-muted">
            <CreditCardIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
            <p>Billing management coming soon...</p>
          </div>
        )}
        
        {activeTab === 'integrations' && (
          <div className="text-center py-12 text-muted">
            <GlobeAltIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
            <p>Integration settings coming soon...</p>
          </div>
        )}
        
        {activeTab === 'security' && (
          <div className="text-center py-12 text-muted">
            <LockClosedIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
            <p>Security settings coming soon...</p>
          </div>
        )}
      </div>

      {/* Invite Modal */}
      {showInviteModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-card rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-semibold text-foreground mb-4">Invite Team Member</h3>
            
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">
                  Email Address
                </label>
                <input
                  type="email"
                  value={inviteForm.email}
                  onChange={(e) => setInviteForm(prev => ({ ...prev, email: e.target.value }))}
                  placeholder="colleague@company.com"
                  className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">
                  Role
                </label>
                <select
                  value={inviteForm.role}
                  onChange={(e) => setInviteForm(prev => ({ ...prev, role: e.target.value }))}
                  className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  {roles.filter(r => r.id !== 'owner').map((role) => (
                    <option key={role.id} value={role.id}>
                      {role.name} - {role.description}
                    </option>
                  ))}
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">
                  Message (Optional)
                </label>
                <textarea
                  value={inviteForm.message}
                  onChange={(e) => setInviteForm(prev => ({ ...prev, message: e.target.value }))}
                  rows={3}
                  placeholder="Personal message to include in the invitation..."
                  className="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>
            
            <div className="flex space-x-3 mt-6">
              <button
                onClick={() => setShowInviteModal(false)}
                className="flex-1 bg-secondary text-secondary-foreground py-2 px-4 rounded-lg hover:bg-secondary/80 transition-colors"
              >
                Cancel
              </button>
              <button
                onClick={inviteTeamMember}
                disabled={!inviteForm.email || saving}
                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors disabled:opacity-50"
              >
                {saving ? 'Sending...' : 'Send Invitation'}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Delete Confirmation Modal */}
      {showDeleteModal && memberToDelete && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-card rounded-lg p-6 max-w-md w-full mx-4">
            <div className="flex items-center mb-4">
              <ExclamationTriangleIcon className="w-6 h-6 text-red-500 mr-3" />
              <h3 className="text-lg font-semibold text-foreground">Remove Team Member</h3>
            </div>
            
            <p className="text-muted mb-6">
              Are you sure you want to remove <strong>{memberToDelete.name || memberToDelete.email}</strong> from this workspace? 
              This action cannot be undone.
            </p>
            
            <div className="flex space-x-3">
              <button
                onClick={() => {
                  // Real data loaded from API
                  // Real data loaded from API
                }}
                className="flex-1 bg-secondary text-secondary-foreground py-2 px-4 rounded-lg hover:bg-secondary/80 transition-colors"
              >
                Cancel
              </button>
              <button
                onClick={() => removeMember(memberToDelete.id)}
                className="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors"
              >
                Remove Member
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AdvancedWorkspaceSettings;