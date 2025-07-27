import React from 'react';
import { motion } from 'framer-motion';
import {
  DocumentTextIcon,
  FolderIcon,
  InboxIcon,
  UserGroupIcon,
  ChartBarIcon,
  ExclamationTriangleIcon
} from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


const EmptyState = ({ 
  icon: Icon = InboxIcon,
  title,
  description,
  action,
  size = 'medium',
  variant = 'default'
}) => {
  const sizeClasses = {
    small: {
      container: 'py-8',
      icon: 'h-12 w-12',
      title: 'text-base',
      description: 'text-sm'
    },
    medium: {
      container: 'py-12',
      icon: 'h-16 w-16',
      title: 'text-lg',
      description: 'text-base'
    },
    large: {
      container: 'py-20',
      icon: 'h-24 w-24',
      title: 'text-xl',
      description: 'text-lg'
    }
  };

  const variantClasses = {
    default: {
      icon: 'text-gray-400 dark:text-gray-500',
      title: 'text-gray-900 dark:text-white',
      description: 'text-gray-600 dark:text-gray-400'
    },
    error: {
      icon: 'text-red-400 dark:text-red-500',
      title: 'text-red-900 dark:text-red-100',
      description: 'text-red-600 dark:text-red-400'
    },
    success: {
      icon: 'text-green-400 dark:text-green-500',
      title: 'text-green-900 dark:text-green-100',
      description: 'text-green-600 dark:text-green-400'
    }
  };

  const currentSize = sizeClasses[size];
  const currentVariant = variantClasses[variant];

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className={`text-center ${currentSize.container}`}
    >
      <div className={`mx-auto ${currentSize.icon} ${currentVariant.icon} mb-6`}>
        <Icon className="w-full h-full" />
      </div>
      
      <h3 className={`font-semibold ${currentSize.title} ${currentVariant.title} mb-2`}>
        {title}
      </h3>
      
      {description && (
        <p className={`max-w-sm mx-auto ${currentSize.description} ${currentVariant.description} mb-6`}>
          {description}
        </p>
      )}
      
      {action && (
        <div>
          {typeof action === 'string' ? (
            <button className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
              {action}
            </button>
          ) : (
            action
          )}
        </div>
      )}
    </motion.div>
  );
};

// Pre-configured empty states for common scenarios
export const NoData = ({ title = "No data available", description, action }) => (
  <EmptyState
    icon={ChartBarIcon}
    title={title}
    description={description || "There's no data to display at the moment."}
    action={action}
  />
);

export const NoDocuments = ({ title = "No documents", description, action }) => (
  <EmptyState
    icon={DocumentTextIcon}
    title={title}
    description={description || "You haven't created any documents yet."}
    action={action || "Create your first document"}
  />
);

export const NoFolders = ({ title = "No folders", description, action }) => (
  <EmptyState
    icon={FolderIcon}
    title={title}
    description={description || "Create folders to organize your content."}
    action={action || "Create folder"}
  />
);

export const NoUsers = ({ title = "No users", description, action }) => (
  <EmptyState
    icon={UserGroupIcon}
    title={title}
    description={description || "No users have been added to this workspace yet."}
    action={action || "Invite users"}
  />
);

export const SearchEmpty = ({ query, description, action }) => (
  <EmptyState
    icon={InboxIcon}
    title={`No results for "${query}"`}
    description={description || "Try adjusting your search terms or filters."}
    action={action}
  />
);

export const ErrorState = ({ title = "Something went wrong", description, action }) => (
  <EmptyState
    icon={ExclamationTriangleIcon}
    title={title}
    description={description || "We encountered an error while loading this content."}
    action={action || "Try again"}
    variant="error"
  />
);

export const LoadingEmpty = ({ title = "Loading...", description }) => (
  <EmptyState
    icon={InboxIcon}
    title={title}
    description={description || "Please wait while we load your content."}
  />
);

export default EmptyState;