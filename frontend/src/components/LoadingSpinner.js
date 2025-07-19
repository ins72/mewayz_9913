import React from 'react';

const LoadingSpinner = ({ size = 'large', className = '' }) => {
  const sizeClasses = {
    small: 'w-4 h-4',
    medium: 'w-6 h-6',
    large: 'w-8 h-8',
    xl: 'w-12 h-12'
  };

  return (
    <div className={`flex items-center justify-center ${className}`}>
      <div 
        className={`${sizeClasses[size]} border-2 border-gray-300 dark:border-gray-600 border-t-current rounded-full animate-spin text-primary`}
      />
    </div>
  );
};

export default LoadingSpinner;