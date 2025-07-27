import React, { useState } from 'react';
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { useNotification } from '../contexts/NotificationContext';
import WorkspaceSelector from '../components/WorkspaceSelector';
import GlobalSearch from '../components/GlobalSearch';
import HelpSupportCenter from '../components/HelpSupportCenter';
import Breadcrumb from '../components/Breadcrumb';
import {
  HomeIcon,
  SparklesIcon,
  BuildingOffice2Icon,
  BuildingOfficeIcon,
  CreditCardIcon,
  DocumentTextIcon,
  ChartBarIcon,
  GlobeAltIcon,
  ShoppingBagIcon,
  AcademicCapIcon,
  UserIcon,
  UsersIcon,
  EnvelopeIcon,
  WrenchScrewdriverIcon,
  CalendarIcon,
  BanknotesIcon,
  ShieldCheckIcon,
  CogIcon,
  LockClosedIcon,
  ChatBubbleLeftRightIcon,
  PuzzlePieceIcon,
  UserPlusIcon,
  Bars3Icon,
  XMarkIcon,
  SunIcon,
  MoonIcon,
  MagnifyingGlassIcon,
  QuestionMarkCircleIcon,
  BellIcon,
  UserCircleIcon,
  LinkIcon,
  TicketIcon,
  BoltIcon
} from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


const DashboardLayout = ({ isAdmin = false }) => {
  const { user, logout } = useAuth();
  const { theme, toggleTheme } = useTheme();
  const { success } = useNotification();
  const location = useLocation();
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [helpOpen, setHelpOpen] = useState(false);

  const navigation = [
    // Core Navigation
    { name: 'Console', href: '/dashboard', icon: HomeIcon, description: 'Dashboard Overview' },
    { name: 'Socials', href: '/dashboard/social-media', icon: ChartBarIcon, description: 'Social Media Management' },
    { name: 'Instagram Leads', href: '/dashboard/instagram-leads', icon: UserIcon, description: 'Instagram Lead Generation' },
    { name: 'Link in Bio', href: '/dashboard/bio-sites', icon: GlobeAltIcon, description: 'Bio Link Pages' },
    { name: 'Leads', href: '/dashboard/crm', icon: EnvelopeIcon, description: 'CRM & Email Marketing' },
    { name: 'Link Shortener', href: '/dashboard/link-shortener', icon: LinkIcon, description: 'URL Shortening Service' },
    { name: 'Referral System', href: '/dashboard/referrals', icon: UserPlusIcon, description: 'Referral Program' },
    
    // Business Tools
    { name: 'Website Builder', href: '/dashboard/website-builder', icon: WrenchScrewdriverIcon, description: 'Build Websites' },
    { name: 'Users', href: '/dashboard/team-management', icon: UsersIcon, description: 'Team Management' },
    { name: 'Form Templates', href: '/dashboard/form-templates', icon: DocumentTextIcon, description: 'Form Builder' },
    { name: 'Discount Codes', href: '/dashboard/discount-codes', icon: TicketIcon, description: 'Promotional Codes' },
    { name: 'Finance', href: '/dashboard/financial-management', icon: BanknotesIcon, description: 'Payments & Invoicing' },
    
    // Content & Education
    { name: 'Courses & Community', href: '/dashboard/courses', icon: AcademicCapIcon, description: 'Education Platform' },
    { name: 'Marketplace & Stores', href: '/dashboard/ecommerce', icon: ShoppingBagIcon, description: 'E-commerce Platform' },
    { name: 'Template Library', href: '/dashboard/template-marketplace', icon: DocumentTextIcon, description: 'Template Marketplace' },
    { name: 'Escrow System', href: '/dashboard/escrow-system', icon: ShieldCheckIcon, description: 'Secure Transactions' },
    { name: 'Analytics & Reporting', href: '/dashboard/gamified-analytics', icon: ChartBarIcon, description: 'Business Intelligence' },
    
    // Additional Features
    { name: 'AI Features', href: '/dashboard/ai-features', icon: SparklesIcon, description: 'AI Tools' },
    { name: 'Token Management', href: '/dashboard/token-management', icon: BoltIcon, description: 'AI Token System' },
    { name: 'Email Marketing', href: '/dashboard/email-marketing', icon: EnvelopeIcon, description: 'Email Campaigns' },
    { name: 'Advanced Booking', href: '/dashboard/advanced-booking', icon: CalendarIcon, description: 'Appointment Scheduling' },
    { name: 'Realtime Collaboration', href: '/dashboard/realtime-collaboration', icon: ChatBubbleLeftRightIcon, description: 'Team Collaboration' },
    { name: 'Integrations', href: '/dashboard/integrations', icon: PuzzlePieceIcon, description: 'Third-party Integrations' },
    { name: 'Workspaces', href: '/dashboard/workspaces', icon: BuildingOffice2Icon, description: 'Workspace Management' },
    { name: 'Workspace Settings', href: '/dashboard/workspace-settings', icon: CogIcon, description: 'Team & Configuration' },
    { name: 'Subscription', href: '/dashboard/subscription', icon: CreditCardIcon, description: 'Billing & Plans' },
    { name: 'Settings', href: '/dashboard/settings', icon: UserCircleIcon, description: 'Account Settings' },
  ];

  // Admin-only navigation items
  const adminNavigation = user?.role === 'admin' ? [
    { name: 'Admin Dashboard', href: '/dashboard/admin', icon: ShieldCheckIcon, isAdmin: true },
    { name: 'User Management', href: '/dashboard/admin/users', icon: UsersIcon, isAdmin: true },
    { name: 'System Settings', href: '/dashboard/admin/system', icon: CogIcon, isAdmin: true },
    { name: 'Security Center', href: '/dashboard/admin/security', icon: LockClosedIcon, isAdmin: true },
  ] : [];

  // Support navigation items
  const supportNavigation = [
    { name: 'Contact Us', href: '/contact', icon: EnvelopeIcon, external: true },
  ];

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  
  const loadDashboardData = async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/dashboard/overview', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setMetrics(data.metrics || {});
        setRecentActivity(data.recent_activity || []);
        setSystemHealth(data.system_health || {});
      } else {
        console.error('Failed to load dashboard data');
      }
    } catch (error) {
      console.error('Error loading dashboard data:', error);
    } finally {
      setLoading(false);
    }
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
                className="p-2 text-secondary hover:text-primary rounded-lg hover:bg-surface-hover"
              >
                <XMarkIcon className="w-5 h-5" />
              </button>
            </div>
            <nav className="mt-4">
              {navigation.map((item) => {
                const isActive = location.pathname === item.href;
                