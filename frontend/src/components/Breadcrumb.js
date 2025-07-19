import React from 'react';
import { ChevronRightIcon, HomeIcon } from '@heroicons/react/24/outline';
import { Link, useLocation } from 'react-router-dom';

const Breadcrumb = ({ items = [], separator = ChevronRightIcon }) => {
  const location = useLocation();
  
  // Auto-generate breadcrumbs from URL if no items provided
  const generateBreadcrumbs = () => {
    const pathSegments = location.pathname.split('/').filter(segment => segment);
    const breadcrumbs = [{ name: 'Home', href: '/dashboard', current: false }];
    
    let currentPath = '';
    pathSegments.forEach((segment, index) => {
      currentPath += `/${segment}`;
      const isLast = index === pathSegments.length - 1;
      
      // Format segment name (capitalize and replace hyphens with spaces)
      const name = segment
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
      
      breadcrumbs.push({
        name,
        href: currentPath,
        current: isLast
      });
    });
    
    return breadcrumbs;
  };

  const breadcrumbItems = items.length > 0 ? items : generateBreadcrumbs();

  if (breadcrumbItems.length <= 1) {
    return null;
  }

  return (
    <nav className="flex" aria-label="Breadcrumb">
      <ol role="list" className="flex items-center space-x-2">
        {breadcrumbItems.map((item, index) => (
          <li key={item.name} className="flex items-center">
            {index === 0 && (
              <HomeIcon className="h-4 w-4 text-gray-400 dark:text-gray-500 mr-2" />
            )}
            
            <div className="flex items-center">
              {item.current ? (
                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">
                  {item.name}
                </span>
              ) : (
                <Link
                  to={item.href}
                  className="text-sm font-medium text-gray-900 dark:text-white hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                >
                  {item.name}
                </Link>
              )}
            </div>
            
            {index < breadcrumbItems.length - 1 && (
              <ChevronRightIcon className="h-4 w-4 text-gray-400 dark:text-gray-500 ml-2" />
            )}
          </li>
        ))}
      </ol>
    </nav>
  );
};

export default Breadcrumb;