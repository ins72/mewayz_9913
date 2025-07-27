import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  UserGroupIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
  CalendarIcon,
  TagIcon,
  StarIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  ShareIcon,
  DocumentArrowDownIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  ArrowTrendingUpIcon,
  ChartBarIcon,
  CurrencyDollarIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  BoltIcon,
  FireIcon,
  HeartIcon,
  ChatBubbleLeftIcon,
  LinkIcon,
  BuildingOfficeIcon,
  GlobeAltIcon,
  BeakerIcon,
  ShoppingBagIcon,
  MegaphoneIcon
} from '@heroicons/react/24/outline';
import {
  UserGroupIcon as UserGroupIconSolid,
  StarIcon as StarIconSolid,
  HeartIcon as HeartIconSolid,
  FireIcon as FireIconSolid
} from '@heroicons/react/24/solid';

const ComprehensiveCRMSystem = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('contacts');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedContact, setSelectedContact] = useState(null);
  const [showContactModal, setShowContactModal] = useState(false);
  const [showEmailModal, setShowEmailModal] = useState(false);
  const [viewMode, setViewMode] = useState('grid');
  
  const [contacts, setContacts] = useState([]);
  const [deals, setDeals] = useState([]);
  const [activities, setActivities] = useState([]);
  const [emailCampaigns, setEmailCampaigns] = useState([]);
  const [filters, setFilters] = useState({
    status: '',
    source: '',
    tags: [],
    dateRange: '',
    value: ''
  });
  
  // Contact sources
  const contactSources = [
    { id: 'website', name: 'Website', icon: GlobeAltIcon, color: 'blue' },
    { id: 'social', name: 'Social Media', icon: ShareIcon, color: 'pink' },
    { id: 'referral', name: 'Referral', icon: UserGroupIcon, color: 'green' },
    { id: 'email', name: 'Email Campaign', icon: EnvelopeIcon, color: 'purple' },
    { id: 'event', name: 'Event', icon: CalendarIcon, color: 'orange' },
    { id: 'cold_outreach', name: 'Cold Outreach', icon: PhoneIcon, color: 'red' },
    { id: 'partner', name: 'Partner', icon: BuildingOfficeIcon, color: 'indigo' },
    { id: 'advertisement', name: 'Advertisement', icon: MegaphoneIcon, color: 'yellow' }
  ];
  
  // Lead stages
  const leadStages = [
    { id: 'new', name: 'New Lead', color: '#3B82F6', order: 1 },
    { id: 'contacted', name: 'Contacted', color: '#8B5CF6', order: 2 },
    { id: 'qualified', name: 'Qualified', color: '#F59E0B', order: 3 },
    { id: 'proposal', name: 'Proposal Sent', color: '#10B981', order: 4 },
    { id: 'negotiation', name: 'Negotiation', color: '#EF4444', order: 5 },
    { id: 'closed_won', name: 'Closed Won', color: '#059669', order: 6 },
    { id: 'closed_lost', name: 'Closed Lost', color: '#6B7280', order: 7 }
  ];
  
  // Mock contacts data
  const mockContacts = [
    {
      id: '1',
      firstName: 'Sarah',
      lastName: 'Johnson',
      email: 'sarah@techstartup.com',
      phone: '+1 (555) 123-4567',
      company: 'Tech Startup Inc.',
      position: 'CEO',
      avatar: 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=EC4899&color=fff',
      source: 'referral',
      stage: 'qualified',
      value: 15000,
      score: 85,
      tags: ['hot-lead', 'enterprise', 'decision-maker'],
      location: 'San Francisco, CA',
      website: 'https://techstartup.com',
      linkedin: 'https://linkedin.com/in/sarahjohnson',
      notes: 'Interested in our enterprise solution. Has budget approved.',
      lastContact: '2025-01-20',
      createdAt: '2025-01-15',
      activities: 12,
      deals: 1,
      emailOpens: 8,
      emailClicks: 3,
      interests: ['AI', 'automation', 'scaling'],
      timezone: 'PST',
      isQualified: true,
      priority: 'high'
    },
    {
      id: '2',
      firstName: 'Michael',
      lastName: 'Chen',
      email: 'mike@designstudio.co',
      phone: '+1 (555) 987-6543',
      company: 'Design Studio Co.',
      position: 'Creative Director',
      avatar: 'https://ui-avatars.com/api/?name=Michael+Chen&background=3B82F6&color=fff',
      source: 'website',
      stage: 'contacted',
      value: 8500,
      score: 72,
      tags: ['creative-agency', 'small-business'],
      location: 'New York, NY',
      website: 'https://designstudio.co',
      linkedin: 'https://linkedin.com/in/michaelchen',
      notes: 'Looking for creative automation tools. Price sensitive.',
      lastContact: '2025-01-19',
      createdAt: '2025-01-10',
      activities: 8,
      deals: 1,
      emailOpens: 5,
      emailClicks: 2,
      interests: ['design', 'templates', 'branding'],
      timezone: 'EST',
      isQualified: false,
      priority: 'medium'
    },
    {
      id: '3',
      firstName: 'Emily',
      lastName: 'Rodriguez',
      email: 'emily@ecommercebrand.com',
      phone: '+1 (555) 456-7890',
      company: 'E-commerce Brand',
      position: 'Marketing Manager',
      avatar: 'https://ui-avatars.com/api/?name=Emily+Rodriguez&background=10B981&color=fff',
      source: 'social',
      stage: 'new',
      value: 12000,
      score: 68,
      tags: ['e-commerce', 'marketing'],
      location: 'Austin, TX',
      website: 'https://ecommercebrand.com',
      linkedin: 'https://linkedin.com/in/emilyrodriguez',
      notes: 'Runs a growing e-commerce brand. Interested in marketing automation.',
      lastContact: '2025-01-21',
      createdAt: '2025-01-18',
      activities: 4,
      deals: 0,
      emailOpens: 2,
      emailClicks: 1,
      interests: ['e-commerce', 'marketing', 'analytics'],
      timezone: 'CST',
      isQualified: false,
      priority: 'medium'
    },
    {
      id: '4',
      firstName: 'David',
      lastName: 'Park',
      email: 'david@consultingfirm.biz',
      phone: '+1 (555) 321-0987',
      company: 'Consulting Firm LLC',
      position: 'Senior Partner',
      avatar: 'https://ui-avatars.com/api/?name=David+Park&background=8B5CF6&color=fff',
      source: 'event',
      stage: 'proposal',
      value: 25000,
      score: 92,
      tags: ['enterprise', 'consulting', 'high-value'],
      location: 'Chicago, IL',
      website: 'https://consultingfirm.biz',
      linkedin: 'https://linkedin.com/in/davidpark',
      notes: 'Met at industry conference. Very interested in our consulting services.',
      lastContact: '2025-01-18',
      createdAt: '2025-01-05',
      activities: 18,
      deals: 2,
      emailOpens: 12,
      emailClicks: 6,
      interests: ['consulting', 'strategy', 'growth'],
      timezone: 'CST',
      isQualified: true,
      priority: 'high'
    }
  ];
  
  // Mock deals data
  const mockDeals = [
    {
      id: 'deal-1',
      title: 'Tech Startup Enterprise License',
      contactId: '1',
      value: 15000,
      stage: 'negotiation',
      probability: 75,
      expectedCloseDate: '2025-02-15',
      createdAt: '2025-01-15',
      lastActivity: '2025-01-20',
      source: 'referral',
      products: ['Enterprise Plan', 'Professional Services'],
      notes: 'Annual contract with potential for expansion'
    },
    {
      id: 'deal-2',
      title: 'Design Studio Creative Package',
      contactId: '2',
      value: 8500,
      stage: 'proposal',
      probability: 50,
      expectedCloseDate: '2025-02-01',
      createdAt: '2025-01-10',
      lastActivity: '2025-01-19',
      source: 'website',
      products: ['Pro Plan', 'Design Templates'],
      notes: 'Waiting for budget approval'
    },
    {
      id: 'deal-3',
      title: 'E-commerce Marketing Automation',
      contactId: '3',
      value: 12000,
      stage: 'qualified',
      probability: 40,
      expectedCloseDate: '2025-02-28',
      createdAt: '2025-01-18',
      lastActivity: '2025-01-21',
      source: 'social',
      products: ['Marketing Suite', 'Analytics Package'],
      notes: 'Evaluating against competitors'
    },
    {
      id: 'deal-4',
      title: 'Consulting Firm Partnership',
      contactId: '4',
      value: 25000,
      stage: 'proposal',
      probability: 80,
      expectedCloseDate: '2025-01-31',
      createdAt: '2025-01-05',
      lastActivity: '2025-01-18',
      source: 'event',
      products: ['Enterprise Plan', 'White Label', 'Custom Integration'],
      notes: 'Strategic partnership opportunity'
    }
  ];
  
  const [crmStats, setCrmStats] = useState({
    totalContacts: 1247,
    qualifiedLeads: 234,
    activeDeals: 56,
    wonDeals: 123,
    totalValue: 2456789,
    conversionRate: 18.7,
    averageDealSize: 15420,
    salesVelocity: 32.5
  });
  
  useEffect(() => {
    // Real data loaded from API
    // Real data loaded from API
  }, []);
  
  const getScoreColor = (score) => {
    if (score >= 80) return 'text-green-600 bg-green-100 dark:bg-green-900/30';
    if (score >= 60) return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30';
    return 'text-red-600 bg-red-100 dark:bg-red-900/30';
  };
  
  const getPriorityColor = (priority) => {
    switch (priority) {
      case 'high': return 'text-red-600 bg-red-100 dark:bg-red-900/30';
      case 'medium': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30';
      case 'low': return 'text-green-600 bg-green-100 dark:bg-green-900/30';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900/30';
    }
  };
  
  const getStageInfo = (stageId) => {
    return leadStages.find(stage => stage.id === stageId) || leadStages[0];
  };
  
  const filteredContacts = contacts.filter(contact => {
    if (searchQuery && !contact.firstName.toLowerCase().includes(searchQuery.toLowerCase()) &&
        !contact.lastName.toLowerCase().includes(searchQuery.toLowerCase()) &&
        !contact.email.toLowerCase().includes(searchQuery.toLowerCase()) &&
        !contact.company.toLowerCase().includes(searchQuery.toLowerCase())) {
      return false;
    }
    
    if (filters.status && contact.stage !== filters.status) {
      return false;
    }
    
    if (filters.source && contact.source !== filters.source) {
      return false;
    }
    
    return true;
  });
  
  const renderContactCard = (contact) => {
    const stageInfo = getStageInfo(contact.stage);
    
    
  const loadCRMData = async () => {
    try {
      setLoading(true);
      const [contactsResponse, dealsResponse, statsResponse] = await Promise.all([
        fetch('/api/crm-management/contacts', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/crm-management/deals', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/crm-management/stats', {
          headers: { 'Authorization': `Bearer ${token}` }
        })
      ]);
      
      if (contactsResponse.ok && dealsResponse.ok && statsResponse.ok) {
        const [contacts, deals, stats] = await Promise.all([
          contactsResponse.json(),
          dealsResponse.json(),
          statsResponse.json()
        ]);
        
        setContacts(contacts.contacts || []);
        setDeals(deals.deals || []);
        setCrmStats(stats);
      }
    } catch (error) {
      console.error('Error loading CRM data:', error);
    } finally {
      setLoading(false);
    }
  };


  return (
      <motion.div
        key={contact.id}
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="bg-surface border border-default rounded-xl p-6 hover:shadow-lg transition-all cursor-pointer"
        onClick={() => setSelectedContact(contact)}
      >
        <div className="flex items-start justify-between mb-4">
          <div className="flex items-center">
            <img
              src={contact.avatar}
              alt={`${contact.firstName} ${contact.lastName}`}
              className="w-12 h-12 rounded-full mr-4"
            />
            <div>
              <h3 className="font-semibold text-primary">
                {contact.firstName} {contact.lastName}
              </h3>
              <p className="text-secondary text-sm">{contact.position}</p>
              <p className="text-secondary text-sm">{contact.company}</p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getPriorityColor(contact.priority)}`}>
              {contact.priority}
            </span>
            <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getScoreColor(contact.score)}`}>
              {contact.score}
            </span>
          </div>
        </div>
        
        <div className="flex items-center justify-between mb-4">
          <div 
            className="px-3 py-1 rounded-full text-sm font-medium text-white"
            style={{ backgroundColor: stageInfo.color }}
          >
            {stageInfo.name}
          </div>
          <div className="text-right">
            <div className="font-bold text-primary">${contact.value?.toLocaleString()}</div>
            <div className="text-xs text-secondary">Deal Value</div>
          </div>
        </div>
        
        <div className="flex items-center space-x-4 text-sm text-secondary mb-4">
          <div className="flex items-center">
            <EnvelopeIcon className="h-4 w-4 mr-1" />
            {contact.emailOpens} opens
          </div>
          <div className="flex items-center">
            <PhoneIcon className="h-4 w-4 mr-1" />
            {contact.activities} activities
          </div>
          <div className="flex items-center">
            <CalendarIcon className="h-4 w-4 mr-1" />
            {new Date(contact.lastContact).toLocaleDateString()}
          </div>
        </div>
        
        <div className="flex flex-wrap gap-1 mb-4">
          {contact.tags.slice(0, 3).map((tag, index) => (
            <span key={index} className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
              {tag}
            </span>
          ))}
          {contact.tags.length > 3 && (
            <span className="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">
              +{contact.tags.length - 3}
            </span>
          )}
        </div>
        
        <div className="flex items-center justify-between">
          <div className="flex items-center text-sm text-secondary">
            <MapPinIcon className="h-4 w-4 mr-1" />
            {contact.location}
          </div>
          <div className="flex space-x-1">
            <button
              onClick={(e) => {
                e.stopPropagation();
                // Send email
              }}
              className="p-2 hover:bg-surface-hover rounded-lg"
            >
              <EnvelopeIcon className="h-4 w-4 text-blue-600" />
            </button>
            <button
              onClick={(e) => {
                e.stopPropagation();
                // Call phone
              }}
              className="p-2 hover:bg-surface-hover rounded-lg"
            >
              <PhoneIcon className="h-4 w-4 text-green-600" />
            </button>
            <button
              onClick={(e) => {
                e.stopPropagation();
                // Edit contact
              }}
              className="p-2 hover:bg-surface-hover rounded-lg"
            >
              <PencilIcon className="h-4 w-4 text-gray-600" />
            </button>
          </div>
        </div>
      </motion.div>
    );
  };
  
  const renderDealCard = (deal) => {
    const contact = contacts.find(c => c.id === deal.contactId);
    const stageInfo = getStageInfo(deal.stage);
    
    