import React, { useState } from 'react';
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import WorkspaceSelector from '../components/WorkspaceSelector';
import {
  HomeIcon,
  UserIcon,
  CogIcon,
  ChartBarIcon,
  ShoppingBagIcon,
  AcademicCapIcon,
  UsersIcon,
  EnvelopeIcon,
  CalendarIcon,
  CreditCardIcon,
  GlobeAltIcon,
  SparklesIcon,
  UserGroupIcon,
  BuildingOfficeIcon,
  DocumentTextIcon,
  BuildingOffice2Icon,
  Bars3Icon,
  XMarkIcon,
  SunIcon,
  MoonIcon,
  ArrowRightOnRectangleIcon,
  BellIcon,
  MagnifyingGlassIcon,
  ShieldCheckIcon,
  BanknotesIcon,
  WrenchScrewdriverIcon,
  LockClosedIcon,
} from '@heroicons/react/24/outline';

const DashboardLayout = ({ isAdmin = false }) => {
  const { user, logout } = useAuth();
  const { theme, toggleTheme } = useTheme();
  const location = useLocation();
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
    { name: 'AI Features', href: '/dashboard/ai-features', icon: SparklesIcon },
    { name: 'Workspaces', href: '/dashboard/workspaces', icon: BuildingOffice2Icon },
    { name: 'Subscription', href: '/dashboard/subscription', icon: CreditCardIcon },
    { name: 'Templates', href: '/dashboard/templates', icon: DocumentTextIcon },
    { name: 'Advanced Analytics', href: '/dashboard/advanced-analytics', icon: ChartBarIcon },
    { name: 'Workspace', href: '/dashboard/workspace', icon: BuildingOfficeIcon },
    { name: 'Website Builder', href: '/dashboard/website-builder', icon: WrenchScrewdriverIcon },
    { name: 'Social Media', href: '/dashboard/social-media', icon: ChartBarIcon },
    { name: 'Bio Sites', href: '/dashboard/bio-sites', icon: GlobeAltIcon },
    { name: 'E-commerce', href: '/dashboard/ecommerce', icon: ShoppingBagIcon },
    { name: 'Courses', href: '/dashboard/courses', icon: AcademicCapIcon },
    { name: 'CRM', href: '/dashboard/crm', icon: UsersIcon },
    { name: 'Email Marketing', href: '/dashboard/email-marketing', icon: EnvelopeIcon },
    { name: 'Analytics', href: '/dashboard/analytics', icon: ChartBarIcon },
    { name: 'Advanced Booking', href: '/dashboard/advanced-booking', icon: CalendarIcon },
    { name: 'Financial Management', href: '/dashboard/financial-management', icon: BanknotesIcon },
    { name: 'Escrow System', href: '/dashboard/escrow-system', icon: ShieldCheckIcon },
    { name: 'Payments', href: '/dashboard/payments', icon: CreditCardIcon },
  ];

  // Admin-only navigation items
  const adminNavigation = user?.role === 'admin' ? [
    { name: 'Admin Dashboard', href: '/dashboard/admin', icon: ShieldCheckIcon, isAdmin: true },
    { name: 'User Management', href: '/dashboard/admin/users', icon: UsersIcon, isAdmin: true },
    { name: 'System Settings', href: '/dashboard/admin/system', icon: CogIcon, isAdmin: true },
    { name: 'Security Center', href: '/dashboard/admin/security', icon: LockClosedIcon, isAdmin: true },
  ] : [];

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <div className="min-h-screen bg-app">
      {/* Mobile sidebar backdrop */}
      <AnimatePresence>
        {sidebarOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            onClick={() => setSidebarOpen(false)}
          />
        )}
      </AnimatePresence>

      {/* Mobile sidebar */}
      <AnimatePresence>
        {sidebarOpen && (
          <motion.div
            initial={{ x: '-100%' }}
            animate={{ x: 0 }}
            exit={{ x: '-100%' }}
            transition={{ type: 'tween', duration: 0.3 }}
            className="fixed inset-y-0 left-0 z-50 w-64 bg-surface shadow-xl lg:hidden"
          >
            <div className="flex items-center h-16 px-4 border-b border-default">
              <h1 className="text-xl font-bold text-primary">
                {isAdmin ? 'Admin' : 'Mewayz'}
              </h1>
              <button
                onClick={() => setSidebarOpen(false)}
                className="p-2 text-secondary hover:text-primary"
              >
                <XMarkIcon className="w-5 h-5" />
              </button>
            </div>
            <nav className="mt-4">
              {navigation.map((item) => {
                const isActive = location.pathname === item.href;
                return (
                  <Link
                    key={item.name}
                    to={item.href}
                    onClick={() => setSidebarOpen(false)}
                    className={`flex items-center px-4 py-3 text-sm font-medium transition-colors ${
                      isActive
                        ? 'nav-active'
                        : 'text-secondary hover-surface hover:text-primary'
                    }`}
                  >
                    <item.icon className="w-5 h-5 mr-3" />
                    {item.name}
                  </Link>
                );
              })}
            </nav>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Desktop sidebar */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div className="flex flex-col flex-grow bg-surface shadow-default border-default">
          <div className="flex items-center flex-shrink-0 px-4 mb-6">
            <WorkspaceSelector />
          </div>
          <nav className="flex-1 mt-4">
            {/* Regular Navigation */}
            {navigation.map((item) => {
              const isActive = location.pathname === item.href;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center px-4 py-3 text-sm font-medium transition-colors ${
                    isActive
                      ? 'nav-active'
                      : 'text-secondary hover-surface hover:text-primary'
                  }`}
                >
                  <item.icon className="w-5 h-5 mr-3" />
                  {item.name}
                </Link>
              );
            })}
            
            {/* Admin Section */}
            {user?.role === 'admin' && adminNavigation.length > 0 && (
              <>
                <div className="pt-6 pb-2">
                  <h3 className="px-3 text-xs font-semibold text-secondary uppercase tracking-wide">
                    Admin Panel
                  </h3>
                </div>
                {adminNavigation.map((item) => {
                  const isActive = location.pathname === item.href;
                  return (
                    <Link
                      key={item.name}
                      to={item.href}
                      className={`flex items-center px-4 py-3 text-sm font-medium transition-colors ${
                        isActive
                          ? 'bg-red-500/10 text-red-400 border-l-4 border-red-400'
                          : 'text-secondary hover:bg-red-500/5 hover:text-red-300'
                      }`}
                    >
                      <item.icon className="w-5 h-5 mr-3" />
                      {item.name}
                    </Link>
                  );
                })}
              </>
            )}
          </nav>
        </div>
      </div>

      {/* Main content */}
      <div className="lg:pl-64">
        {/* Top header */}
        <header className="nav-bg shadow-sm">
          <div className="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <div className="flex items-center">
              <button
                onClick={() => setSidebarOpen(true)}
                className="p-2 text-secondary hover:text-primary lg:hidden"
              >
                <Bars3Icon className="w-5 h-5" />
              </button>
              
              <div className="flex-1 max-w-md ml-4">
                <div className="relative">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-secondary" />
                  <input
                    type="text"
                    placeholder="Search..."
                    className="w-full pl-10 pr-4 py-2 text-sm input rounded-lg focus-ring"
                  />
                </div>
              </div>
            </div>

            <div className="flex items-center space-x-4">
              <button
                onClick={toggleTheme}
                className="p-2 text-secondary hover:text-primary transition-colors"
              >
                {theme === 'dark' ? (
                  <SunIcon className="w-5 h-5" />
                ) : (
                  <MoonIcon className="w-5 h-5" />
                )}
              </button>

              <button className="p-2 text-secondary hover:text-primary transition-colors relative">
                <BellIcon className="w-5 h-5" />
                <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              </button>

              <div className="flex items-center space-x-3">
                <div className="hidden sm:block text-right">
                  <p className="text-sm font-medium text-primary">
                    {user?.name || 'User'}
                  </p>
                  <p className="text-xs text-secondary">
                    {user?.email}
                  </p>
                </div>

                <Link
                  to="/dashboard/profile"
                  className="flex-shrink-0 w-8 h-8 bg-gradient-primary rounded-full flex items-center justify-center shadow-default"
                >
                  <UserIcon className="w-4 h-4 text-white" />
                </Link>

                <button
                  onClick={handleLogout}
                  className="p-2 text-secondary hover:text-primary transition-colors"
                  title="Logout"
                >
                  <ArrowRightOnRectangleIcon className="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </header>

        {/* Page content */}
        <main className="flex-1">
          <div className="py-6">
            <div className="px-4 sm:px-6 lg:px-8">
              <Outlet />
            </div>
          </div>
        </main>
      </div>
    </div>
  );
};

export default DashboardLayout;