import React from 'react';

const WorkspacePage = () => {
  return (
    <div className="space-y-6">
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">Workspace</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">
          This is the Workspace page. Full functionality will be implemented here.
        </p>
        <div className="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
          <h3 className="text-lg font-medium text-indigo-900 dark:text-indigo-300 mb-2">
            Coming Soon
          </h3>
          <p className="text-indigo-700 dark:text-indigo-400">
            This feature is being actively developed and will be available soon.
          </p>
        </div>
      </div>
    </div>
  );
};

export default WorkspacePage;
