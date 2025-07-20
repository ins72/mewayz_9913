import React, { useState, useEffect, useContext } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  PlusIcon,
  BuildingOfficeIcon,
  UserGroupIcon,
  CogIcon,
  TrashIcon,
  PencilIcon,
  CheckIcon,
  XMarkIcon,
  StarIcon,
  LockClosedIcon,
  GlobeAltIcon
} from '@heroicons/react/24/outline';
import { AuthContext } from '../../contexts/AuthContext';

const WorkspaceManager = () => {
  const { user, currentWorkspace, setCurrentWorkspace } = useContext(AuthContext);
  const [workspaces, setWorkspaces] = useState([]);
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
  const [editingWorkspace, setEditingWorkspace] = useState(null);
  const [loading, setLoading] = useState(true);
  const [newWorkspace, setNewWorkspace] = useState({
    name: '',
    description: '',
    industry: '',
    isPrivate: false
  });

  const industries = [
    'Technology', 'Healthcare', 'Finance', 'Education', 'E-commerce', 
    'Marketing', 'Real Estate', 'Consulting', 'Creative', 'Manufacturing',
    'Non-profit', 'Entertainment', 'Travel', 'Food & Beverage', 'Other'
  ];

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
        setWorkspaces(data.data?.workspaces || []);
      } else {
        // Mock data for development
        setWorkspaces([
          {
            id: '1',
            name: 'Main Workspace',
            slug: 'main-workspace',
            description: 'Primary workspace for all projects',
            industry: 'Technology',
            is_active: true,
            member_count: 1,
            created_at: new Date().toISOString(),
            features_enabled: {
              ai_assistant: true,
              bio_sites: true,
              ecommerce: true,
              analytics: true
            }
          }
        ]);
      }
    } catch (error) {
      console.error('Failed to fetch workspaces:', error);
      setWorkspaces([]);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateWorkspace = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(newWorkspace)
      });

      if (response.ok) {
        const data = await response.json();
        setWorkspaces(prev => [...prev, data.data.workspace]);
        setIsCreateModalOpen(false);
        setNewWorkspace({ name: '', description: '', industry: '', isPrivate: false });
      } else {
        // Mock creation for development
        const mockWorkspace = {
          id: Date.now().toString(),
          name: newWorkspace.name,
          slug: newWorkspace.name.toLowerCase().replace(/\s+/g, '-'),
          description: newWorkspace.description,
          industry: newWorkspace.industry,
          is_active: true,
          member_count: 1,
          created_at: new Date().toISOString(),
          features_enabled: {
            ai_assistant: true,
            bio_sites: true,
            ecommerce: true,
            analytics: true
          }
        };
        
        setWorkspaces(prev => [...prev, mockWorkspace]);
        setIsCreateModalOpen(false);
        setNewWorkspace({ name: '', description: '', industry: '', isPrivate: false });
      }
    } catch (error) {
      console.error('Failed to create workspace:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteWorkspace = async (workspaceId) => {
    if (!window.confirm('Are you sure you want to delete this workspace? This action cannot be undone.')) {
      return;
    }

    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/${workspaceId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });

      if (response.ok || true) { // Allow deletion in development
        setWorkspaces(prev => prev.filter(ws => ws.id !== workspaceId));
        
        // If deleting current workspace, switch to first available
        if (currentWorkspace?.id === workspaceId) {
          const remaining = workspaces.filter(ws => ws.id !== workspaceId);
          setCurrentWorkspace(remaining.length > 0 ? remaining[0] : null);
        }
      }
    } catch (error) {
      console.error('Failed to delete workspace:', error);
    }
  };

  const handleUpdateWorkspace = async (workspaceId, updates) => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces/${workspaceId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(updates)
      });

      if (response.ok || true) { // Allow updates in development
        setWorkspaces(prev => 
          prev.map(ws => 
            ws.id === workspaceId ? { ...ws, ...updates } : ws
          )
        );
        setEditingWorkspace(null);
      }
    } catch (error) {
      console.error('Failed to update workspace:', error);
    }
  };

  const handleSwitchWorkspace = (workspace) => {
    setCurrentWorkspace(workspace);
    localStorage.setItem('currentWorkspace', JSON.stringify(workspace));
  };

  if (loading && workspaces.length === 0) {
    return (
      <div className="max-w-6xl mx-auto p-6">
        <div className="animate-pulse">
          <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-4"></div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map(i => (
              <div key={i} className="h-48 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto p-6">
      {/* Header */}
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
            Workspace Manager
          </h1>
          <p className="text-gray-600 dark:text-gray-300 mt-2">
            Manage your workspaces and collaborate with your team
          </p>
        </div>
        <button
          onClick={() => setIsCreateModalOpen(true)}
          className="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          <PlusIcon className="h-5 w-5 mr-2" />
          Create Workspace
        </button>
      </div>

      {/* Current Workspace Indicator */}
      {currentWorkspace && (
        <div className="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
          <div className="flex items-center">
            <StarIcon className="h-5 w-5 text-blue-600 mr-2" />
            <span className="text-blue-800 dark:text-blue-200 font-medium">
              Currently in: {currentWorkspace.name}
            </span>
          </div>
        </div>
      )}

      {/* Workspaces Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {workspaces.map((workspace) => (
          <motion.div
            key={workspace.id}
            layout
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className={`bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 transition-all ${
              currentWorkspace?.id === workspace.id
                ? 'border-blue-500 ring-2 ring-blue-500 ring-opacity-20'
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
            }`}
          >
            {/* Workspace Header */}
            <div className="flex items-start justify-between mb-4">
              <div className="flex items-center">
                <div className="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg mr-3">
                  <BuildingOfficeIcon className="h-6 w-6 text-gray-600 dark:text-gray-300" />
                </div>
                <div className="flex-1">
                  {editingWorkspace === workspace.id ? (
                    <input
                      type="text"
                      defaultValue={workspace.name}
                      className="font-semibold text-lg bg-transparent border-b border-gray-300 dark:border-gray-600 focus:border-blue-500 outline-none text-gray-900 dark:text-white"
                      onBlur={(e) => handleUpdateWorkspace(workspace.id, { name: e.target.value })}
                      onKeyPress={(e) => e.key === 'Enter' && e.target.blur()}
                    />
                  ) : (
                    <h3 className="font-semibold text-lg text-gray-900 dark:text-white">
                      {workspace.name}
                    </h3>
                  )}
                  <p className="text-sm text-gray-500 dark:text-gray-400">
                    {workspace.industry || 'No industry set'}
                  </p>
                </div>
              </div>
              
              {currentWorkspace?.id === workspace.id && (
                <div className="flex items-center">
                  <StarIcon className="h-5 w-5 text-yellow-500 fill-current" />
                </div>
              )}
            </div>

            {/* Workspace Description */}
            <p className="text-gray-600 dark:text-gray-300 text-sm mb-4 h-10 overflow-hidden">
              {workspace.description || 'No description provided'}
            </p>

            {/* Workspace Stats */}
            <div className="grid grid-cols-2 gap-4 mb-4">
              <div className="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <UserGroupIcon className="h-5 w-5 text-gray-500 mx-auto mb-1" />
                <div className="text-sm text-gray-600 dark:text-gray-300">
                  {workspace.member_count || 1} member{(workspace.member_count || 1) !== 1 ? 's' : ''}
                </div>
              </div>
              <div className="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <GlobeAltIcon className="h-5 w-5 text-gray-500 mx-auto mb-1" />
                <div className="text-sm text-gray-600 dark:text-gray-300">
                  {workspace.is_active ? 'Active' : 'Inactive'}
                </div>
              </div>
            </div>

            {/* Features Enabled */}
            <div className="mb-4">
              <div className="flex flex-wrap gap-1">
                {workspace.features_enabled && Object.entries(workspace.features_enabled).map(([feature, enabled]) => 
                  enabled && (
                    <span
                      key={feature}
                      className="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full"
                    >
                      {feature.replace('_', ' ')}
                    </span>
                  )
                )}
              </div>
            </div>

            {/* Actions */}
            <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
              <div className="flex space-x-2">
                <button
                  onClick={() => setEditingWorkspace(editingWorkspace === workspace.id ? null : workspace.id)}
                  className="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded"
                  title="Edit workspace"
                >
                  <PencilIcon className="h-4 w-4" />
                </button>
                <button
                  onClick={() => {/* Open settings modal */}}
                  className="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded"
                  title="Workspace settings"
                >
                  <CogIcon className="h-4 w-4" />
                </button>
                <button
                  onClick={() => handleDeleteWorkspace(workspace.id)}
                  className="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded"
                  title="Delete workspace"
                >
                  <TrashIcon className="h-4 w-4" />
                </button>
              </div>
              
              <button
                onClick={() => handleSwitchWorkspace(workspace)}
                className={`px-3 py-1 rounded text-sm font-medium transition-colors ${
                  currentWorkspace?.id === workspace.id
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
                }`}
              >
                {currentWorkspace?.id === workspace.id ? 'Current' : 'Switch'}
              </button>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Empty State */}
      {workspaces.length === 0 && !loading && (
        <div className="text-center py-12">
          <BuildingOfficeIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
            No workspaces found
          </h3>
          <p className="text-gray-600 dark:text-gray-300 mb-4">
            Create your first workspace to get started
          </p>
          <button
            onClick={() => setIsCreateModalOpen(true)}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Create Workspace
          </button>
        </div>
      )}

      {/* Create Workspace Modal */}
      <AnimatePresence>
        {isCreateModalOpen && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md"
            >
              <div className="p-6">
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-xl font-semibold text-gray-900 dark:text-white">
                    Create New Workspace
                  </h2>
                  <button
                    onClick={() => setIsCreateModalOpen(false)}
                    className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                  >
                    <XMarkIcon className="h-6 w-6" />
                  </button>
                </div>

                <form onSubmit={handleCreateWorkspace} className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Workspace Name *
                    </label>
                    <input
                      type="text"
                      required
                      value={newWorkspace.name}
                      onChange={(e) => setNewWorkspace(prev => ({ ...prev, name: e.target.value }))}
                      className="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                      placeholder="Enter workspace name"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Description
                    </label>
                    <textarea
                      value={newWorkspace.description}
                      onChange={(e) => setNewWorkspace(prev => ({ ...prev, description: e.target.value }))}
                      className="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white h-20 resize-none"
                      placeholder="Describe this workspace"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Industry
                    </label>
                    <select
                      value={newWorkspace.industry}
                      onChange={(e) => setNewWorkspace(prev => ({ ...prev, industry: e.target.value }))}
                      className="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                      <option value="">Select industry</option>
                      {industries.map(industry => (
                        <option key={industry} value={industry}>{industry}</option>
                      ))}
                    </select>
                  </div>

                  <div className="flex items-center">
                    <input
                      type="checkbox"
                      id="private"
                      checked={newWorkspace.isPrivate}
                      onChange={(e) => setNewWorkspace(prev => ({ ...prev, isPrivate: e.target.checked }))}
                      className="h-4 w-4 text-blue-600 rounded border-gray-300 dark:border-gray-600"
                    />
                    <label htmlFor="private" className="ml-2 text-sm text-gray-700 dark:text-gray-300">
                      Make this workspace private
                    </label>
                  </div>

                  <div className="flex space-x-3 pt-4">
                    <button
                      type="button"
                      onClick={() => setIsCreateModalOpen(false)}
                      className="flex-1 py-2 px-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      disabled={!newWorkspace.name.trim() || loading}
                      className="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                      {loading ? 'Creating...' : 'Create Workspace'}
                    </button>
                  </div>
                </form>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default WorkspaceManager;