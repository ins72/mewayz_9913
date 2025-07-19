import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  MagnifyingGlassIcon,
  XMarkIcon,
  ClockIcon,
  BookmarkIcon,
  DocumentTextIcon,
  UserIcon,
  BuildingOfficeIcon,
  ChartBarIcon,
  CommandLineIcon
} from '@heroicons/react/24/outline';

const GlobalSearch = ({ isOpen, onClose }) => {
  const [query, setQuery] = useState('');
  const [activeCategory, setActiveCategory] = useState('all');
  const [recentSearches] = useState([
    'AI Features',
    'User Management', 
    'Analytics Dashboard',
    'Subscription Plans',
    'Integration Setup'
  ]);

  // Mock search results
  const [results] = useState({
    pages: [
      { id: 1, title: 'AI Features', path: '/dashboard/ai-features', description: 'Manage AI-powered features and settings' },
      { id: 2, title: 'User Settings', path: '/dashboard/settings', description: 'Manage your account and preferences' },
      { id: 3, title: 'Analytics', path: '/dashboard/analytics', description: 'View detailed analytics and reports' },
      { id: 4, title: 'Workspaces', path: '/dashboard/workspaces', description: 'Manage and switch between workspaces' }
    ],
    users: [
      { id: 1, name: 'John Doe', email: 'john@example.com', avatar: 'JD' },
      { id: 2, name: 'Jane Smith', email: 'jane@example.com', avatar: 'JS' },
    ],
    documents: [
      { id: 1, title: 'Project Proposal', type: 'PDF', modified: '2 hours ago' },
      { id: 2, title: 'Meeting Notes', type: 'DOC', modified: '1 day ago' },
    ],
    actions: [
      { id: 1, title: 'Create New Workspace', action: 'create_workspace' },
      { id: 2, title: 'Invite Team Member', action: 'invite_user' },
      { id: 3, title: 'Export Analytics', action: 'export_analytics' },
    ]
  });

  const categories = [
    { id: 'all', name: 'All', icon: MagnifyingGlassIcon },
    { id: 'pages', name: 'Pages', icon: DocumentTextIcon },
    { id: 'users', name: 'Users', icon: UserIcon },
    { id: 'documents', name: 'Documents', icon: DocumentTextIcon },
    { id: 'actions', name: 'Actions', icon: CommandLineIcon },
  ];

  const filteredResults = activeCategory === 'all' 
    ? results 
    : { [activeCategory]: results[activeCategory] || [] };

  const handleNavigate = (path) => {
    onClose();
    // Navigate to path
    window.location.href = path;
  };

  const handleAction = (action) => {
    onClose();
    // Trigger action
    console.log('Trigger action:', action);
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto">
      <div className="flex min-h-full items-start justify-center pt-16 px-4">
        {/* Backdrop */}
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 bg-gray-500 bg-opacity-25 dark:bg-gray-900 dark:bg-opacity-50 transition-opacity"
          onClick={onClose}
        />

        {/* Search Modal */}
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0, scale: 0.95 }}
          className="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700"
        >
          {/* Search Header */}
          <div className="flex items-center p-4 border-b border-gray-200 dark:border-gray-700">
            <MagnifyingGlassIcon className="h-5 w-5 text-gray-400 dark:text-gray-500" />
            <input
              type="text"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Search pages, users, documents..."
              className="flex-1 ml-3 bg-transparent border-none focus:outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
              autoFocus
            />
            <button
              onClick={onClose}
              className="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300"
            >
              <XMarkIcon className="h-5 w-5" />
            </button>
          </div>

          {/* Categories */}
          <div className="flex items-center px-4 py-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            {categories.map((category) => (
              <button
                key={category.id}
                onClick={() => setActiveCategory(category.id)}
                className={`flex items-center space-x-2 px-3 py-1 rounded-lg text-sm font-medium whitespace-nowrap mr-2 transition-colors ${
                  activeCategory === category.id
                    ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
                }`}
              >
                <category.icon className="h-4 w-4" />
                <span>{category.name}</span>
              </button>
            ))}
          </div>

          {/* Results */}
          <div className="max-h-96 overflow-y-auto">
            {query === '' ? (
              // Recent searches and suggestions
              <div className="p-4">
                <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                  <ClockIcon className="h-4 w-4 mr-2" />
                  Recent Searches
                </h3>
                <div className="space-y-1">
                  {recentSearches.map((search, index) => (
                    <button
                      key={index}
                      onClick={() => setQuery(search)}
                      className="w-full text-left px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                      {search}
                    </button>
                  ))}
                </div>

                <h3 className="text-sm font-medium text-gray-900 dark:text-white mt-6 mb-3">
                  Quick Actions
                </h3>
                <div className="grid grid-cols-2 gap-2">
                  <button className="p-3 text-left bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <DocumentTextIcon className="h-5 w-5 text-gray-600 dark:text-gray-400 mb-1" />
                    <p className="text-sm font-medium text-gray-900 dark:text-white">New Document</p>
                  </button>
                  <button className="p-3 text-left bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <UserIcon className="h-5 w-5 text-gray-600 dark:text-gray-400 mb-1" />
                    <p className="text-sm font-medium text-gray-900 dark:text-white">Invite User</p>
                  </button>
                </div>
              </div>
            ) : (
              // Search results
              <div className="p-4 space-y-6">
                {/* Pages */}
                {filteredResults.pages && filteredResults.pages.length > 0 && (
                  <div>
                    <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                      <DocumentTextIcon className="h-4 w-4 mr-2" />
                      Pages
                    </h3>
                    <div className="space-y-1">
                      {filteredResults.pages.map((page) => (
                        <button
                          key={page.id}
                          onClick={() => handleNavigate(page.path)}
                          className="w-full text-left p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                          <p className="text-sm font-medium text-gray-900 dark:text-white">
                            {page.title}
                          </p>
                          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {page.description}
                          </p>
                        </button>
                      ))}
                    </div>
                  </div>
                )}

                {/* Users */}
                {filteredResults.users && filteredResults.users.length > 0 && (
                  <div>
                    <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                      <UserIcon className="h-4 w-4 mr-2" />
                      Users
                    </h3>
                    <div className="space-y-1">
                      {filteredResults.users.map((user) => (
                        <button
                          key={user.id}
                          className="w-full text-left p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors flex items-center"
                        >
                          <div className="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                            {user.avatar}
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900 dark:text-white">
                              {user.name}
                            </p>
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                              {user.email}
                            </p>
                          </div>
                        </button>
                      ))}
                    </div>
                  </div>
                )}

                {/* Documents */}
                {filteredResults.documents && filteredResults.documents.length > 0 && (
                  <div>
                    <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                      <DocumentTextIcon className="h-4 w-4 mr-2" />
                      Documents
                    </h3>
                    <div className="space-y-1">
                      {filteredResults.documents.map((doc) => (
                        <button
                          key={doc.id}
                          className="w-full text-left p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                          <p className="text-sm font-medium text-gray-900 dark:text-white">
                            {doc.title}
                          </p>
                          <p className="text-sm text-gray-500 dark:text-gray-400">
                            {doc.type} • Modified {doc.modified}
                          </p>
                        </button>
                      ))}
                    </div>
                  </div>
                )}

                {/* Actions */}
                {filteredResults.actions && filteredResults.actions.length > 0 && (
                  <div>
                    <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                      <CommandLineIcon className="h-4 w-4 mr-2" />
                      Actions
                    </h3>
                    <div className="space-y-1">
                      {filteredResults.actions.map((action) => (
                        <button
                          key={action.id}
                          onClick={() => handleAction(action.action)}
                          className="w-full text-left p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                          <p className="text-sm font-medium text-gray-900 dark:text-white">
                            {action.title}
                          </p>
                        </button>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Footer */}
          <div className="flex items-center justify-between px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div className="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
              <span className="flex items-center">
                <kbd className="px-1 py-0.5 bg-gray-200 dark:bg-gray-600 rounded mr-1">↑</kbd>
                <kbd className="px-1 py-0.5 bg-gray-200 dark:bg-gray-600 rounded mr-2">↓</kbd>
                Navigate
              </span>
              <span className="flex items-center">
                <kbd className="px-1 py-0.5 bg-gray-200 dark:bg-gray-600 rounded mr-2">Enter</kbd>
                Select
              </span>
              <span className="flex items-center">
                <kbd className="px-1 py-0.5 bg-gray-200 dark:bg-gray-600 rounded mr-2">Esc</kbd>
                Close
              </span>
            </div>
          </div>
        </motion.div>
      </div>
    </div>
  );
};

export default GlobalSearch;