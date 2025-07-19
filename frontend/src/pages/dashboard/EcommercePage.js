import React from 'react';

const EcommercePage = () => {
  return (
    <div className="space-y-6">
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">E-commerce</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">
          Manage your online store, products, orders, and inventory.
        </p>
        <div className="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
          <h3 className="text-lg font-medium text-indigo-900 dark:text-indigo-300 mb-2">
            Coming Soon
          </h3>
          <p className="text-indigo-700 dark:text-indigo-400">
            E-commerce platform is being actively developed.
          </p>
        </div>
      </div>
    </div>
  );
};

export default EcommercePage;