import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  QuestionMarkCircleIcon,
  XMarkIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon
} from '@heroicons/react/24/outline';

const Tooltip = ({ 
  children, 
  content, 
  position = 'top',
  variant = 'default',
  delay = 300,
  disabled = false 
}) => {
  const [isVisible, setIsVisible] = useState(false);
  const [timeoutId, setTimeoutId] = useState(null);

  const showTooltip = () => {
    if (disabled) return;
    
    const id = setTimeout(() => {
      setIsVisible(true);
    }, delay);
    setTimeoutId(id);
  };

  const hideTooltip = () => {
    if (timeoutId) {
      clearTimeout(timeoutId);
      setTimeoutId(null);
    }
    setIsVisible(false);
  };

  const getPositionClasses = () => {
    const positions = {
      top: 'bottom-full left-1/2 transform -translate-x-1/2 mb-2',
      bottom: 'top-full left-1/2 transform -translate-x-1/2 mt-2',
      left: 'right-full top-1/2 transform -translate-y-1/2 mr-2',
      right: 'left-full top-1/2 transform -translate-y-1/2 ml-2'
    };
    return positions[position] || positions.top;
  };

  const getArrowClasses = () => {
    const arrows = {
      top: 'top-full left-1/2 transform -translate-x-1/2 border-t-gray-900 dark:border-t-gray-100 border-t-8 border-x-8 border-x-transparent border-b-0',
      bottom: 'bottom-full left-1/2 transform -translate-x-1/2 border-b-gray-900 dark:border-b-gray-100 border-b-8 border-x-8 border-x-transparent border-t-0',
      left: 'left-full top-1/2 transform -translate-y-1/2 border-l-gray-900 dark:border-l-gray-100 border-l-8 border-y-8 border-y-transparent border-r-0',
      right: 'right-full top-1/2 transform -translate-y-1/2 border-r-gray-900 dark:border-r-gray-100 border-r-8 border-y-8 border-y-transparent border-l-0'
    };
    return arrows[position] || arrows.top;
  };

  const getVariantClasses = () => {
    const variants = {
      default: 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900',
      info: 'bg-blue-600 dark:bg-blue-500 text-white',
      warning: 'bg-yellow-600 dark:bg-yellow-500 text-white',
      error: 'bg-red-600 dark:bg-red-500 text-white',
      success: 'bg-green-600 dark:bg-green-500 text-white'
    };
    return variants[variant] || variants.default;
  };

  if (disabled || !content) {
    return children;
  }

  return (
    <div
      className="relative inline-block"
      onMouseEnter={showTooltip}
      onMouseLeave={hideTooltip}
      onFocus={showTooltip}
      onBlur={hideTooltip}
    >
      {children}
      
      <AnimatePresence>
        {isVisible && (
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            exit={{ opacity: 0, scale: 0.95 }}
            transition={{ duration: 0.1 }}
            className={`
              absolute z-50 px-3 py-2 text-sm font-medium rounded-lg shadow-lg
              ${getPositionClasses()}
              ${getVariantClasses()}
              max-w-xs whitespace-pre-wrap
            `}
          >
            {content}
            
            {/* Arrow */}
            <div className={`absolute w-0 h-0 ${getArrowClasses()}`} />
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

// Helper component for info tooltips with question mark icon
export const InfoTooltip = ({ content, className = '' }) => (
  <Tooltip content={content} variant="info">
    <QuestionMarkCircleIcon className={`h-4 w-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 cursor-help ${className}`} />
  </Tooltip>
);

// Helper component for help text with different variants
export const HelpTooltip = ({ content, variant = 'info', className = '' }) => {
  const icons = {
    info: InformationCircleIcon,
    warning: ExclamationTriangleIcon,
    error: XMarkIcon,
    success: CheckCircleIcon
  };
  
  const Icon = icons[variant] || icons.info;
  
  return (
    <Tooltip content={content} variant={variant}>
      <Icon className={`h-4 w-4 cursor-help ${className}`} />
    </Tooltip>
  );
};

export default Tooltip;