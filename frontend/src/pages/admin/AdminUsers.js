import React from 'react';

const AdminUsers = () => {
  return (
    <div className="space-y-6">
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">AdminUsers</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">
          This is the AdminUsers page. Admin functionality will be implemented here.
        </p>
        <div className="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
          <h3 className="text-lg font-medium text-red-900 dark:text-red-300 mb-2">
            Admin Feature
          </h3>
          <p className="text-red-700 dark:text-red-400">
            This is an admin-only feature currently under development.
          </p>
        </div>
      </div>
    </div>
  );
};

export default AdminUsers;
