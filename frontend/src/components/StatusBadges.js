import React from 'react';

const StatusBadge = ({ 
  status, 
  variant = 'default',
  size = 'medium',
  className = ''
}) => {
  const variantClasses = {
    default: {
      active: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
      inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
      pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
      error: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
      success: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
      warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
      info: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
    },
    outline: {
      active: 'border-green-200 text-green-700 dark:border-green-700 dark:text-green-400',
      inactive: 'border-gray-200 text-gray-700 dark:border-gray-600 dark:text-gray-300',
      pending: 'border-yellow-200 text-yellow-700 dark:border-yellow-700 dark:text-yellow-400',
      error: 'border-red-200 text-red-700 dark:border-red-700 dark:text-red-400',
      success: 'border-green-200 text-green-700 dark:border-green-700 dark:text-green-400',
      warning: 'border-yellow-200 text-yellow-700 dark:border-yellow-700 dark:text-yellow-400',
      info: 'border-blue-200 text-blue-700 dark:border-blue-700 dark:text-blue-400'
    },
    solid: {
      active: 'bg-green-600 text-white',
      inactive: 'bg-gray-600 text-white',
      pending: 'bg-yellow-600 text-white',
      error: 'bg-red-600 text-white',
      success: 'bg-green-600 text-white',
      warning: 'bg-yellow-600 text-white',
      info: 'bg-blue-600 text-white'
    }
  };

  const sizeClasses = {
    small: 'px-2 py-0.5 text-xs',
    medium: 'px-2.5 py-0.5 text-sm',
    large: 'px-3 py-1 text-base'
  };

  const statusKey = status?.toLowerCase() || 'inactive';
  const baseClasses = 'inline-flex items-center font-medium rounded-full';
  const variantClass = variantClasses[variant]?.[statusKey] || variantClasses[variant].inactive;
  const sizeClass = sizeClasses[size];
  const borderClass = variant === 'outline' ? 'border' : '';

  return (
    <span className={`${baseClasses} ${variantClass} ${sizeClass} ${borderClass} ${className}`}>
      {status}
    </span>
  );
};

const PriorityBadge = ({ priority = 'medium', className = '' }) => {
  const priorityConfig = {
    low: {
      color: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
      label: 'Low'
    },
    medium: {
      color: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
      label: 'Medium'
    },
    high: {
      color: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
      label: 'High'
    },
    urgent: {
      color: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
      label: 'Urgent'
    }
  };

  const config = priorityConfig[priority.toLowerCase()] || priorityConfig.medium;

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color} ${className}`}>
      <span className="w-1.5 h-1.5 mr-1.5 rounded-full bg-current"></span>
      {config.label}
    </span>
  );
};

const FeatureBadge = ({ 
  feature, 
  available = true, 
  isPro = false, 
  isNew = false,
  className = '' 
}) => {
  if (isNew) {
    return (
      <span className={`inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 dark:from-purple-900/20 dark:to-pink-900/20 dark:text-purple-400 ${className}`}>
        <span className="w-1.5 h-1.5 mr-1.5 rounded-full bg-purple-600 animate-pulse"></span>
        NEW
      </span>
    );
  }

  if (isPro) {
    return (
      <span className={`inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 dark:from-yellow-900/20 dark:to-orange-900/20 dark:text-yellow-400 ${className}`}>
        <svg className="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
        PRO
      </span>
    );
  }

  if (!available) {
    return (
      <span className={`inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 ${className}`}>
        <svg className="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path fillRule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clipRule="evenodd" />
        </svg>
        UNAVAILABLE
      </span>
    );
  }

  return null;
};

const ConnectedBadge = ({ 
  isConnected = false, 
  service = '',
  showDot = true,
  className = '' 
}) => {
  if (isConnected) {
    return (
      <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 ${className}`}>
        {showDot && <span className="w-1.5 h-1.5 mr-1.5 bg-green-400 rounded-full"></span>}
        {service ? `${service} Connected` : 'Connected'}
      </span>
    );
  }

  return (
    <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 ${className}`}>
      {showDot && <span className="w-1.5 h-1.5 mr-1.5 bg-gray-400 rounded-full"></span>}
      {service ? `${service} Disconnected` : 'Disconnected'}
    </span>
  );
};

const CountBadge = ({ 
  count = 0, 
  variant = 'default',
  max = 99,
  showZero = false,
  className = ''
}) => {
  if (count === 0 && !showZero) return null;

  const displayCount = count > max ? `${max}+` : count.toString();

  const variantClasses = {
    default: 'bg-red-500 text-white',
    primary: 'bg-blue-500 text-white',
    success: 'bg-green-500 text-white',
    warning: 'bg-yellow-500 text-white',
    gray: 'bg-gray-500 text-white'
  };

  return (
    <span className={`
      inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1 
      text-xs font-bold rounded-full 
      ${variantClasses[variant]}
      ${className}
    `}>
      {displayCount}
    </span>
  );
};

export {
  StatusBadge,
  PriorityBadge, 
  FeatureBadge,
  ConnectedBadge,
  CountBadge
};