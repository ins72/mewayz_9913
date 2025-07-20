import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  BuildingOfficeIcon,
  UserGroupIcon,
  CogIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  UserPlusIcon,
  TrophyIcon,
  ShieldCheckIcon,
  EyeIcon,
  DocumentTextIcon,
  CreditCardIcon,
  ChartBarIcon,
  BellIcon,
  KeyIcon,
  GlobeAltIcon,
  PhotoIcon,
  TagIcon,
  CalendarIcon,
  ClockIcon,
  CheckCircleIcon,
  XMarkIcon,
  ArrowRightIcon,
  StarIcon,
  LightBulbIcon,
  RocketLaunchIcon,
  ShoppingBagIcon
} from '@heroicons/react/24/outline';
import {
  BuildingOfficeIcon as BuildingOfficeIconSolid,
  TrophyIcon as TrophyIconSolid,
  StarIcon as StarIconSolid
} from '@heroicons/react/24/solid';

const UltraAdvancedWorkspaceManagement = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('overview');
  const [workspaces, setWorkspaces] = useState([]);
  const [currentWorkspace, setCurrentWorkspace] = useState(null);
  const [loading, setLoading] = useState(false);
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showInviteModal, setShowInviteModal] = useState(false);
  const [showSettingsModal, setShowSettingsModal] = useState(false);
  
  // Form states
  const [createForm, setCreateForm] = useState({
    name: '',
    description: '',
    goals: [],
    features: [],
    branding: {
      logo: '',
      primary_color: '#3B82F6',
      secondary_color: '#10B981',
      domain: ''
    }
  });
  
  const [inviteForm, setInviteForm] = useState({
    email: '',
    role: 'viewer',
    message: ''
  });
  
  // Available goals and features
  const availableGoals = [
    { id: 'instagram', name: 'Instagram Management', icon: PhotoIcon, color: 'pink', description: 'Manage Instagram accounts and content' },
    { id: 'link_in_bio', name: 'Link in Bio', icon: GlobeAltIcon, color: 'blue', description: 'Create beautiful link in bio pages' },
    { id: 'courses', name: 'Course Creation', icon: LightBulbIcon, color: 'yellow', description: 'Build and sell online courses' },
    { id: 'ecommerce', name: 'E-commerce', icon: ShoppingBagIcon, color: 'green', description: 'Manage online stores and products' },
    { id: 'crm', name: 'CRM & Email Marketing', icon: UserGroupIcon, color: 'purple', description: 'Manage contacts and email campaigns' },
    { id: 'analytics', name: 'Advanced Analytics', icon: ChartBarIcon, color: 'indigo', description: 'Comprehensive business analytics' }
  ];
  
  const availableFeatures = [
    { id: 'social_posting', name: 'Social Media Posting', category: 'Social Media' },
    { id: 'content_calendar', name: 'Content Calendar', category: 'Social Media' },
    { id: 'instagram_database', name: 'Instagram Database', category: 'Social Media' },
    { id: 'hashtag_research', name: 'Hashtag Research', category: 'Social Media' },
    { id: 'drag_drop_builder', name: 'Drag & Drop Builder', category: 'Link in Bio' },
    { id: 'custom_domains', name: 'Custom Domains', category: 'Link in Bio' },
    { id: 'qr_codes', name: 'QR Code Generator', category: 'Link in Bio' },
    { id: 'link_analytics', name: 'Link Analytics', category: 'Link in Bio' },
    { id: 'course_builder', name: 'Course Builder', category: 'Courses' },
    { id: 'video_hosting', name: 'Video Hosting', category: 'Courses' },
    { id: 'student_management', name: 'Student Management', category: 'Courses' },
    { id: 'certificates', name: 'Certificates', category: 'Courses' },
    { id: 'product_management', name: 'Product Management', category: 'E-commerce' },
    { id: 'inventory_tracking', name: 'Inventory Tracking', category: 'E-commerce' },
    { id: 'payment_processing', name: 'Payment Processing', category: 'E-commerce' },
    { id: 'order_management', name: 'Order Management', category: 'E-commerce' },
    { id: 'contact_management', name: 'Contact Management', category: 'CRM' },
    { id: 'email_campaigns', name: 'Email Campaigns', category: 'CRM' },
    { id: 'lead_scoring', name: 'Lead Scoring', category: 'CRM' },
    { id: 'automation_workflows', name: 'Automation Workflows', category: 'CRM' },
    { id: 'advanced_analytics', name: 'Advanced Analytics', category: 'Analytics' },
    { id: 'custom_reports', name: 'Custom Reports', category: 'Analytics' },
    { id: 'data_export', name: 'Data Export', category: 'Analytics' },
    { id: 'real_time_dashboard', name: 'Real-time Dashboard', category: 'Analytics' },
    { id: 'ai_content_generation', name: 'AI Content Generation', category: 'AI Features' },
    { id: 'ai_analytics', name: 'AI Analytics', category: 'AI Features' },
    { id: 'chatbot', name: 'AI Chatbot', category: 'AI Features' },
    { id: 'white_label', name: 'White Label', category: 'Enterprise' },
    { id: 'api_access', name: 'API Access', category: 'Enterprise' },
    { id: 'priority_support', name: 'Priority Support', category: 'Enterprise' },
    { id: 'custom_integrations', name: 'Custom Integrations', category: 'Enterprise' }
  ];
  
  const rolePermissions = {
    owner: { 
      name: 'Owner', 
      icon: TrophyIcon, 
      color: 'yellow',
      description: 'Full access to all features and settings',
      permissions: ['all']
    },
    admin: { 
      name: 'Admin', 
      icon: ShieldCheckIcon, 
      color: 'red',
      description: 'Manage users, content, and most settings',
      permissions: ['manage_users', 'manage_content', 'view_analytics', 'manage_billing']
    },
    editor: { 
      name: 'Editor', 
      icon: PencilIcon, 
      color: 'blue',
      description: 'Create and edit content, limited settings access',
      permissions: ['manage_content', 'view_analytics']
    },
    viewer: { 
      name: 'Viewer', 
      icon: EyeIcon, 
      color: 'gray',
      description: 'View-only access to content and basic analytics',
      permissions: ['view_content', 'view_basic_analytics']
    }
  };
  
  useEffect(() => {
    fetchWorkspaces();
  }, []);
  
  const fetchWorkspaces = async () => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setWorkspaces(data.data.workspaces);
        if (data.data.workspaces.length > 0) {
          setCurrentWorkspace(data.data.workspaces[0]);
        }
      }
    } catch (err) {
      console.error('Failed to fetch workspaces:', err);
    }
  };
  
  const createWorkspace = async () => {
    setLoading(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(createForm)
      });
      
      if (response.ok) {
        const data = await response.json();
        success('Workspace created successfully!');
        setShowCreateModal(false);
        setCreateForm({
          name: '',
          description: '',
          goals: [],
          features: [],
          branding: {
            logo: '',
            primary_color: '#3B82F6',
            secondary_color: '#10B981',
            domain: ''
          }
        });
        fetchWorkspaces();
      } else {
        const errorData = await response.json();
        error(errorData.detail || 'Failed to create workspace');
      }
    } catch (err) {
      error('Failed to create workspace');
    } finally {
      setLoading(false);
    }
  };
  
  const inviteTeamMember = async () => {
    setLoading(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/${currentWorkspace.id}/invite`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(inviteForm)
      });
      
      if (response.ok) {
        success('Team member invited successfully!');
        setShowInviteModal(false);
        setInviteForm({ email: '', role: 'viewer', message: '' });
        fetchWorkspaces();
      } else {
        const errorData = await response.json();
        error(errorData.detail || 'Failed to send invitation');
      }
    } catch (err) {
      error('Failed to send invitation');
    } finally {
      setLoading(false);
    }
  };
  
  const renderWorkspaceCard = (workspace) => (
    <motion.div
      key={workspace.id}
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className={`p-6 rounded-xl border-2 cursor-pointer transition-all ${
        currentWorkspace?.id === workspace.id
          ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
          : 'border-default bg-surface hover:bg-surface-hover'
      }`}
      onClick={() => setCurrentWorkspace(workspace)}
    >
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center">
          {workspace.branding?.logo ? (
            <img src={workspace.branding.logo} alt={workspace.name} className="w-12 h-12 rounded-lg mr-4" />
          ) : (
            <div className="w-12 h-12 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-4">
              <BuildingOfficeIconSolid className="h-6 w-6 text-white" />
            </div>
          )}
          <div>
            <h3 className="font-semibold text-primary">{workspace.name}</h3>
            <p className="text-sm text-secondary">{workspace.description}</p>
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
            workspace.subscription_plan === 'enterprise' ? 'bg-yellow-100 text-yellow-800' :
            workspace.subscription_plan === 'pro' ? 'bg-blue-100 text-blue-800' :
            'bg-gray-100 text-gray-800'
          }`}>
            {workspace.subscription_plan || 'Free'}
          </span>
          {workspace.role === 'owner' && <CrownIconSolid className="h-4 w-4 text-yellow-500" />}
        </div>
      </div>
      
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4 text-sm text-secondary">
          <span className="flex items-center">
            <UserGroupIcon className="h-4 w-4 mr-1" />
            {workspace.team_members || 1} members
          </span>
          <span className="flex items-center">
            <StarIcon className="h-4 w-4 mr-1" />
            {workspace.active_features || 0} features
          </span>
        </div>
        <div className="flex space-x-2">
          {workspace.goals?.slice(0, 3).map((goal, index) => {
            const goalInfo = availableGoals.find(g => g.id === goal);
            return goalInfo ? (
              <div key={index} className={`w-8 h-8 rounded-lg bg-${goalInfo.color}-100 dark:bg-${goalInfo.color}-900/30 flex items-center justify-center`}>
                <goalInfo.icon className={`h-4 w-4 text-${goalInfo.color}-600 dark:text-${goalInfo.color}-400`} />
              </div>
            ) : null;
          })}
          {workspace.goals?.length > 3 && (
            <div className="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
              <span className="text-xs font-medium text-gray-600 dark:text-gray-400">+{workspace.goals.length - 3}</span>
            </div>
          )}
        </div>
      </div>
    </motion.div>
  );
  
  const renderCreateWorkspaceModal = () => (
    <AnimatePresence>
      {showCreateModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            exit={{ opacity: 0, scale: 0.9 }}
            className="bg-surface rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
          >
            <div className="p-6 border-b border-default">
              <div className="flex items-center justify-between">
                <h2 className="text-2xl font-bold text-primary">Create New Workspace</h2>
                <button
                  onClick={() => setShowCreateModal(false)}
                  className="p-2 hover:bg-surface-hover rounded-lg"
                >
                  <XMarkIcon className="h-5 w-5" />
                </button>
              </div>
            </div>
            
            <div className="p-6 space-y-8">
              {/* Basic Info */}
              <div>
                <h3 className="text-lg font-semibold text-primary mb-4">Basic Information</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Workspace Name</label>
                    <input
                      type="text"
                      value={createForm.name}
                      onChange={(e) => setCreateForm({...createForm, name: e.target.value})}
                      placeholder="My Awesome Workspace"
                      className="input"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Description</label>
                    <input
                      type="text"
                      value={createForm.description}
                      onChange={(e) => setCreateForm({...createForm, description: e.target.value})}
                      placeholder="Brief description of your workspace"
                      className="input"
                    />
                  </div>
                </div>
              </div>
              
              {/* Goals Selection */}
              <div>
                <h3 className="text-lg font-semibold text-primary mb-4">Select Your Main Goals</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {availableGoals.map((goal) => (
                    <div
                      key={goal.id}
                      onClick={() => {
                        const isSelected = createForm.goals.includes(goal.id);
                        if (isSelected) {
                          setCreateForm({
                            ...createForm,
                            goals: createForm.goals.filter(g => g !== goal.id)
                          });
                        } else {
                          setCreateForm({
                            ...createForm,
                            goals: [...createForm.goals, goal.id]
                          });
                        }
                      }}
                      className={`p-4 rounded-xl border-2 cursor-pointer transition-all ${
                        createForm.goals.includes(goal.id)
                          ? `border-${goal.color}-500 bg-${goal.color}-50 dark:bg-${goal.color}-900/20`
                          : 'border-default bg-surface hover:bg-surface-hover'
                      }`}
                    >
                      <div className={`inline-flex p-3 rounded-xl mb-3 ${
                        createForm.goals.includes(goal.id)
                          ? `bg-${goal.color}-100 dark:bg-${goal.color}-800/30`
                          : 'bg-surface-elevated'
                      }`}>
                        <goal.icon className={`h-6 w-6 ${
                          createForm.goals.includes(goal.id)
                            ? `text-${goal.color}-600 dark:text-${goal.color}-400`
                            : 'text-secondary'
                        }`} />
                      </div>
                      <h4 className="font-semibold text-primary mb-1">{goal.name}</h4>
                      <p className="text-sm text-secondary">{goal.description}</p>
                    </div>
                  ))}
                </div>
              </div>
              
              {/* Features Selection */}
              <div>
                <h3 className="text-lg font-semibold text-primary mb-4">Choose Features</h3>
                <div className="space-y-4">
                  {Object.entries(
                    availableFeatures.reduce((acc, feature) => {
                      if (!acc[feature.category]) acc[feature.category] = [];
                      acc[feature.category].push(feature);
                      return acc;
                    }, {})
                  ).map(([category, features]) => (
                    <div key={category}>
                      <h4 className="font-medium text-primary mb-2">{category}</h4>
                      <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                        {features.map((feature) => (
                          <label key={feature.id} className="flex items-center p-3 rounded-lg bg-surface-elevated hover:bg-surface-hover cursor-pointer">
                            <input
                              type="checkbox"
                              checked={createForm.features.includes(feature.id)}
                              onChange={(e) => {
                                if (e.target.checked) {
                                  setCreateForm({
                                    ...createForm,
                                    features: [...createForm.features, feature.id]
                                  });
                                } else {
                                  setCreateForm({
                                    ...createForm,
                                    features: createForm.features.filter(f => f !== feature.id)
                                  });
                                }
                              }}
                              className="mr-2"
                            />
                            <span className="text-sm text-primary">{feature.name}</span>
                          </label>
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
              
              {/* Branding */}
              <div>
                <h3 className="text-lg font-semibold text-primary mb-4">Branding</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Primary Color</label>
                    <input
                      type="color"
                      value={createForm.branding.primary_color}
                      onChange={(e) => setCreateForm({
                        ...createForm,
                        branding: {...createForm.branding, primary_color: e.target.value}
                      })}
                      className="w-full h-10 rounded-lg"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Secondary Color</label>
                    <input
                      type="color"
                      value={createForm.branding.secondary_color}
                      onChange={(e) => setCreateForm({
                        ...createForm,
                        branding: {...createForm.branding, secondary_color: e.target.value}
                      })}
                      className="w-full h-10 rounded-lg"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-secondary mb-2">Custom Domain (Optional)</label>
                    <input
                      type="text"
                      value={createForm.branding.domain}
                      onChange={(e) => setCreateForm({
                        ...createForm,
                        branding: {...createForm.branding, domain: e.target.value}
                      })}
                      placeholder="your-workspace.com"
                      className="input"
                    />
                  </div>
                </div>
              </div>
            </div>
            
            <div className="p-6 border-t border-default flex items-center justify-end space-x-3">
              <button
                onClick={() => setShowCreateModal(false)}
                className="btn btn-secondary"
              >
                Cancel
              </button>
              <button
                onClick={createWorkspace}
                disabled={loading || !createForm.name}
                className="btn btn-primary"
              >
                {loading ? 'Creating...' : 'Create Workspace'}
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
  
  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <BuildingOfficeIconSolid className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">Workspace Management</h1>
            </div>
            <p className="text-white/80">Create, manage, and collaborate across multiple workspaces</p>
          </div>
          <button
            onClick={() => setShowCreateModal(true)}
            className="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg font-medium transition-colors flex items-center"
          >
            <PlusIcon className="h-5 w-5 mr-2" />
            New Workspace
          </button>
        </div>
      </div>
      
      {/* Workspaces Grid */}
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <h2 className="text-2xl font-bold text-primary">Your Workspaces</h2>
          <span className="text-secondary">{workspaces.length} workspace{workspaces.length !== 1 ? 's' : ''}</span>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {workspaces.map(renderWorkspaceCard)}
        </div>
      </div>
      
      {/* Current Workspace Details */}
      {currentWorkspace && (
        <div className="bg-surface-elevated rounded-xl shadow-default p-6">
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center">
              <div className="w-16 h-16 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-4">
                <BuildingOfficeIconSolid className="h-8 w-8 text-white" />
              </div>
              <div>
                <h3 className="text-2xl font-bold text-primary">{currentWorkspace.name}</h3>
                <p className="text-secondary">{currentWorkspace.description}</p>
              </div>
            </div>
            <div className="flex space-x-3">
              <button
                onClick={() => setShowInviteModal(true)}
                className="btn btn-secondary"
              >
                <UserPlusIcon className="h-4 w-4 mr-2" />
                Invite Member
              </button>
              <button
                onClick={() => setShowSettingsModal(true)}
                className="btn btn-primary"
              >
                <CogIcon className="h-4 w-4 mr-2" />
                Settings
              </button>
            </div>
          </div>
          
          {/* Workspace Stats */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div className="text-center p-4 rounded-lg bg-surface border border-default">
              <UserGroupIcon className="h-8 w-8 mx-auto mb-2 text-blue-600" />
              <div className="text-2xl font-bold text-primary">{currentWorkspace.team_members || 1}</div>
              <div className="text-sm text-secondary">Team Members</div>
            </div>
            <div className="text-center p-4 rounded-lg bg-surface border border-default">
              <RocketLaunchIcon className="h-8 w-8 mx-auto mb-2 text-green-600" />
              <div className="text-2xl font-bold text-primary">{currentWorkspace.goals?.length || 0}</div>
              <div className="text-sm text-secondary">Active Goals</div>
            </div>
            <div className="text-center p-4 rounded-lg bg-surface border border-default">
              <StarIconSolid className="h-8 w-8 mx-auto mb-2 text-yellow-600" />
              <div className="text-2xl font-bold text-primary">{currentWorkspace.active_features || 0}</div>
              <div className="text-sm text-secondary">Features</div>
            </div>
            <div className="text-center p-4 rounded-lg bg-surface border border-default">
              <CreditCardIcon className="h-8 w-8 mx-auto mb-2 text-purple-600" />
              <div className="text-2xl font-bold text-primary capitalize">{currentWorkspace.subscription_plan || 'Free'}</div>
              <div className="text-sm text-secondary">Plan</div>
            </div>
          </div>
          
          {/* Active Goals */}
          {currentWorkspace.goals && currentWorkspace.goals.length > 0 && (
            <div className="mb-8">
              <h4 className="text-lg font-semibold text-primary mb-4">Active Goals</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {currentWorkspace.goals.map((goalId) => {
                  const goal = availableGoals.find(g => g.id === goalId);
                  if (!goal) return null;
                  
                  return (
                    <div key={goalId} className={`p-4 rounded-lg border border-${goal.color}-200 bg-${goal.color}-50 dark:bg-${goal.color}-900/20`}>
                      <div className="flex items-center">
                        <div className={`p-2 rounded-lg bg-${goal.color}-100 dark:bg-${goal.color}-800/30 mr-3`}>
                          <goal.icon className={`h-5 w-5 text-${goal.color}-600 dark:text-${goal.color}-400`} />
                        </div>
                        <div>
                          <div className="font-semibold text-primary">{goal.name}</div>
                          <div className="text-xs text-secondary">{goal.description}</div>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          )}
        </div>
      )}
      
      {renderCreateWorkspaceModal()}
      
      {/* Invite Modal */}
      <AnimatePresence>
        {showInviteModal && (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              className="bg-surface rounded-xl shadow-2xl max-w-md w-full"
            >
              <div className="p-6 border-b border-default">
                <div className="flex items-center justify-between">
                  <h2 className="text-xl font-bold text-primary">Invite Team Member</h2>
                  <button
                    onClick={() => setShowInviteModal(false)}
                    className="p-2 hover:bg-surface-hover rounded-lg"
                  >
                    <XMarkIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
              
              <div className="p-6 space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Email Address</label>
                  <input
                    type="email"
                    value={inviteForm.email}
                    onChange={(e) => setInviteForm({...inviteForm, email: e.target.value})}
                    placeholder="colleague@example.com"
                    className="input"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Role</label>
                  <select
                    value={inviteForm.role}
                    onChange={(e) => setInviteForm({...inviteForm, role: e.target.value})}
                    className="input"
                  >
                    {Object.entries(rolePermissions).map(([role, info]) => (
                      <option key={role} value={role}>{info.name}</option>
                    ))}
                  </select>
                  <p className="text-xs text-secondary mt-1">
                    {rolePermissions[inviteForm.role]?.description}
                  </p>
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Personal Message (Optional)</label>
                  <textarea
                    value={inviteForm.message}
                    onChange={(e) => setInviteForm({...inviteForm, message: e.target.value})}
                    placeholder="Hey! I'd love to have you join our workspace..."
                    className="input h-20"
                  />
                </div>
              </div>
              
              <div className="p-6 border-t border-default flex items-center justify-end space-x-3">
                <button
                  onClick={() => setShowInviteModal(false)}
                  className="btn btn-secondary"
                >
                  Cancel
                </button>
                <button
                  onClick={inviteTeamMember}
                  disabled={loading || !inviteForm.email}
                  className="btn btn-primary"
                >
                  {loading ? 'Sending...' : 'Send Invitation'}
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default UltraAdvancedWorkspaceManagement;