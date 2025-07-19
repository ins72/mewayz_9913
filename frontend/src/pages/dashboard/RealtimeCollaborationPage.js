import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  UserGroupIcon,
  VideoCameraIcon,
  ChatBubbleLeftRightIcon,
  DocumentTextIcon,
  PencilSquareIcon,
  ArrowRightIcon,
  PlayIcon,
  PlusIcon,
  ClockIcon
} from '@heroicons/react/24/outline';
import RealtimeCollaboration from '../../components/realtime/RealtimeCollaboration';

const RealtimeCollaborationPage = () => {
  const [activeSession, setActiveSession] = useState(null);
  const [viewMode, setViewMode] = useState('overview'); // overview, active, create

  const recentSessions = [
    {
      id: 'session-1',
      title: 'Website Redesign Project',
      type: 'design',
      participants: 4,
      lastActivity: '2 minutes ago',
      status: 'active',
      documentType: 'whiteboard'
    },
    {
      id: 'session-2', 
      title: 'Marketing Campaign Strategy',
      type: 'document',
      participants: 3,
      lastActivity: '15 minutes ago',
      status: 'active',
      documentType: 'text'
    },
    {
      id: 'session-3',
      title: 'Q4 Planning Meeting',
      type: 'presentation',
      participants: 8,
      lastActivity: '1 hour ago',
      status: 'completed',
      documentType: 'presentation'
    }
  ];

  const stats = [
    {
      name: 'Active Sessions',
      value: '12',
      change: '+2.1%',
      changeType: 'positive'
    },
    {
      name: 'Total Collaborators',
      value: '34',
      change: '+12.5%',
      changeType: 'positive'
    },
    {
      name: 'Documents Shared',
      value: '156',
      change: '+8.3%',
      changeType: 'positive'
    },
    {
      name: 'Hours Collaborated',
      value: '89',
      change: '+15.2%',
      changeType: 'positive'
    }
  ];

  const renderOverview = () => (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <motion.div
            key={stat.name}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                  {stat.name}
                </p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {stat.value}
                </p>
              </div>
              <div className={`text-sm ${
                stat.changeType === 'positive' 
                  ? 'text-green-600' 
                  : 'text-red-600'
              }`}>
                {stat.change}
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Quick Actions */}
      <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Quick Actions
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <button 
            onClick={() => setViewMode('create')}
            className="flex items-center space-x-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
          >
            <PlusIcon className="h-8 w-8 text-blue-600" />
            <div className="text-left">
              <p className="font-medium text-gray-900 dark:text-white">New Session</p>
              <p className="text-sm text-gray-500 dark:text-gray-400">Start collaborating</p>
            </div>
          </button>
          
          <button className="flex items-center space-x-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
            <VideoCameraIcon className="h-8 w-8 text-green-600" />
            <div className="text-left">
              <p className="font-medium text-gray-900 dark:text-white">Video Call</p>
              <p className="text-sm text-gray-500 dark:text-gray-400">Start meeting</p>
            </div>
          </button>
          
          <button className="flex items-center space-x-3 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
            <DocumentTextIcon className="h-8 w-8 text-purple-600" />
            <div className="text-left">
              <p className="font-medium text-gray-900 dark:text-white">Shared Docs</p>
              <p className="text-sm text-gray-500 dark:text-gray-400">Browse documents</p>
            </div>
          </button>
        </div>
      </div>

      {/* Recent Sessions */}
      <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            Recent Collaboration Sessions
          </h3>
        </div>
        <div className="divide-y divide-gray-200 dark:divide-gray-700">
          {recentSessions.map((session) => (
            <motion.div
              key={session.id}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
            >
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <div className={`p-2 rounded-lg ${
                    session.status === 'active' 
                      ? 'bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400'
                      : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
                  }`}>
                    {session.type === 'design' ? <PencilSquareIcon className="h-5 w-5" /> :
                     session.type === 'document' ? <DocumentTextIcon className="h-5 w-5" /> :
                     <PlayIcon className="h-5 w-5" />}
                  </div>
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white">
                      {session.title}
                    </h4>
                    <div className="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                      <span className="flex items-center">
                        <UserGroupIcon className="h-4 w-4 mr-1" />
                        {session.participants} participants
                      </span>
                      <span className="flex items-center">
                        <ClockIcon className="h-4 w-4 mr-1" />
                        {session.lastActivity}
                      </span>
                    </div>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  {session.status === 'active' && (
                    <button
                      onClick={() => {
                        setActiveSession(session);
                        setViewMode('active');
                      }}
                      className="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                      <span>Join</span>
                      <ArrowRightIcon className="h-4 w-4" />
                    </button>
                  )}
                  <button className="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <ChatBubbleLeftRightIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderCreateSession = () => (
    <div className="max-w-2xl mx-auto">
      <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
          Create New Collaboration Session
        </h3>
        
        <form className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Session Title
            </label>
            <input
              type="text"
              placeholder="Enter session title..."
              className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Collaboration Type
            </label>
            <div className="grid grid-cols-3 gap-4">
              {[
                { type: 'text', label: 'Document', icon: DocumentTextIcon },
                { type: 'whiteboard', label: 'Whiteboard', icon: PencilSquareIcon },
                { type: 'presentation', label: 'Presentation', icon: PlayIcon }
              ].map(({ type, label, icon: Icon }) => (
                <label key={type} className="cursor-pointer">
                  <input
                    type="radio"
                    name="documentType"
                    value={type}
                    className="sr-only"
                  />
                  <div className="p-4 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <Icon className="h-8 w-8 text-gray-600 dark:text-gray-400 mb-2 mx-auto" />
                    <p className="text-sm font-medium text-gray-900 dark:text-white text-center">
                      {label}
                    </p>
                  </div>
                </label>
              ))}
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Description (Optional)
            </label>
            <textarea
              rows={3}
              placeholder="Describe what you'll be working on..."
              className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div className="flex items-center justify-between pt-6">
            <button
              type="button"
              onClick={() => setViewMode('overview')}
              className="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Create Session
            </button>
          </div>
        </form>
      </div>
    </div>
  );

  const renderActiveSession = () => (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            {activeSession?.title || 'Active Session'}
          </h3>
          <p className="text-sm text-gray-500 dark:text-gray-400">
            Collaborating in real-time
          </p>
        </div>
        <button
          onClick={() => setViewMode('overview')}
          className="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          Leave Session
        </button>
      </div>
      
      <RealtimeCollaboration 
        documentId={activeSession?.id || 'demo-doc'}
        documentType={activeSession?.documentType || 'general'}
      />
    </div>
  );

  return (
    <div className="p-6">
      <div className="mb-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
              Real-time Collaboration
            </h1>
            <p className="text-gray-600 dark:text-gray-400 mt-1">
              Collaborate with your team in real-time on documents, designs, and projects
            </p>
          </div>
          
          {viewMode === 'overview' && (
            <button
              onClick={() => setViewMode('create')}
              className="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <PlusIcon className="h-5 w-5" />
              <span>New Session</span>
            </button>
          )}
        </div>

        {/* Navigation Tabs */}
        <div className="mt-6 border-b border-gray-200 dark:border-gray-700">
          <nav className="-mb-px flex space-x-8">
            {[
              { key: 'overview', label: 'Overview', icon: UserGroupIcon },
              { key: 'active', label: 'Active Session', icon: VideoCameraIcon },
              { key: 'create', label: 'Create Session', icon: PlusIcon }
            ].map((tab) => (
              <button
                key={tab.key}
                onClick={() => setViewMode(tab.key)}
                className={`flex items-center space-x-2 py-2 px-1 border-b-2 font-medium text-sm ${
                  viewMode === tab.key
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300'
                }`}
              >
                <tab.icon className="h-5 w-5" />
                <span>{tab.label}</span>
              </button>
            ))}
          </nav>
        </div>
      </div>

      {/* Content Area */}
      <motion.div
        key={viewMode}
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.2 }}
      >
        {viewMode === 'overview' && renderOverview()}
        {viewMode === 'create' && renderCreateSession()}
        {viewMode === 'active' && renderActiveSession()}
      </motion.div>
    </div>
  );
};

export default RealtimeCollaborationPage;