import React from 'react';
import { motion } from 'framer-motion';

const ProgressBar = ({ 
  progress = 0, 
  size = 'medium', 
  variant = 'default',
  showPercentage = true,
  animated = true,
  className = ''
}) => {
  const sizeClasses = {
    small: 'h-1',
    medium: 'h-2', 
    large: 'h-4'
  };

  const variantClasses = {
    default: 'bg-blue-600 dark:bg-blue-500',
    success: 'bg-green-600 dark:bg-green-500',
    warning: 'bg-yellow-600 dark:bg-yellow-500',
    error: 'bg-red-600 dark:bg-red-500',
    info: 'bg-indigo-600 dark:bg-indigo-500'
  };

  const normalizedProgress = Math.max(0, Math.min(100, progress));

  return (
    <div className={`w-full ${className}`}>
      <div className={`w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden ${sizeClasses[size]}`}>
        <motion.div
          className={`${sizeClasses[size]} ${variantClasses[variant]} rounded-full transition-all duration-300`}
          initial={animated ? { width: 0 } : false}
          animate={{ width: `${normalizedProgress}%` }}
          transition={{ duration: 1, ease: "easeOut" }}
        />
      </div>
      {showPercentage && (
        <div className="flex justify-between text-xs text-gray-600 dark:text-gray-400 mt-1">
          <span>Progress</span>
          <span>{normalizedProgress.toFixed(0)}%</span>
        </div>
      )}
    </div>
  );
};

const CircularProgress = ({
  progress = 0,
  size = 80,
  strokeWidth = 8,
  variant = 'default',
  showPercentage = true,
  animated = true,
  className = ''
}) => {
  const normalizedProgress = Math.max(0, Math.min(100, progress));
  const radius = (size - strokeWidth) / 2;
  const circumference = radius * 2 * Math.PI;
  const strokeDashoffset = circumference - (normalizedProgress / 100) * circumference;

  const variantColors = {
    default: '#3B82F6',
    success: '#10B981', 
    warning: '#F59E0B',
    error: '#EF4444',
    info: '#6366F1'
  };

  return (
    <div className={`inline-flex items-center justify-center ${className}`}>
      <div className="relative">
        <svg
          width={size}
          height={size}
          className="transform -rotate-90"
        >
          {/* Background circle */}
          <circle
            cx={size / 2}
            cy={size / 2}
            r={radius}
            fill="transparent"
            stroke="currentColor"
            strokeWidth={strokeWidth}
            className="text-gray-200 dark:text-gray-700"
          />
          
          {/* Progress circle */}
          <motion.circle
            cx={size / 2}
            cy={size / 2}
            r={radius}
            fill="transparent"
            stroke={variantColors[variant]}
            strokeWidth={strokeWidth}
            strokeLinecap="round"
            strokeDasharray={circumference}
            strokeDashoffset={animated ? circumference : strokeDashoffset}
            animate={animated ? { strokeDashoffset } : {}}
            transition={{ duration: 1, ease: "easeOut" }}
          />
        </svg>
        
        {showPercentage && (
          <div className="absolute inset-0 flex items-center justify-center">
            <span className="text-sm font-medium text-gray-900 dark:text-white">
              {normalizedProgress.toFixed(0)}%
            </span>
          </div>
        )}
      </div>
    </div>
  );
};

const StepProgress = ({
  steps = [],
  currentStep = 0,
  variant = 'default',
  orientation = 'horizontal',
  className = ''
}) => {
  const variantClasses = {
    default: {
      active: 'bg-blue-600 border-blue-600 text-white',
      completed: 'bg-green-600 border-green-600 text-white',
      inactive: 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'
    },
    minimal: {
      active: 'bg-blue-100 dark:bg-blue-900 border-blue-600 text-blue-600 dark:text-blue-400',
      completed: 'bg-green-100 dark:bg-green-900 border-green-600 text-green-600 dark:text-green-400',
      inactive: 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'
    }
  };

  const getStepClass = (index) => {
    if (index < currentStep) return variantClasses[variant].completed;
    if (index === currentStep) return variantClasses[variant].active;
    return variantClasses[variant].inactive;
  };

  const isHorizontal = orientation === 'horizontal';

  return (
    <div className={`${className}`}>
      <div className={`flex ${isHorizontal ? 'flex-row items-center' : 'flex-col'}`}>
        {steps.map((step, index) => (
          <div key={index} className={`flex ${isHorizontal ? 'items-center' : 'flex-col items-start'}`}>
            {/* Step Circle */}
            <div className="flex items-center">
              <div
                className={`
                  w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-medium
                  ${getStepClass(index)}
                  transition-all duration-200
                `}
              >
                {index < currentStep ? (
                  <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                  </svg>
                ) : (
                  index + 1
                )}
              </div>
              
              {/* Step Label */}
              {step && (
                <div className={`${isHorizontal ? 'ml-3' : 'mt-2'}`}>
                  <div className="text-sm font-medium text-gray-900 dark:text-white">
                    {step.title || step}
                  </div>
                  {step.description && (
                    <div className="text-xs text-gray-500 dark:text-gray-400">
                      {step.description}
                    </div>
                  )}
                </div>
              )}
            </div>
            
            {/* Connector Line */}
            {index < steps.length - 1 && (
              <div
                className={`
                  ${isHorizontal ? 'flex-1 h-0.5 mx-4' : 'w-0.5 h-8 ml-4 mt-2'}
                  ${index < currentStep ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600'}
                  transition-colors duration-200
                `}
              />
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export { ProgressBar, CircularProgress, StepProgress };