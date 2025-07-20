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
  TrendingUpIcon,
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
  ShoppingBagIcon
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
    setContacts(mockContacts);
    setDeals(mockDeals);
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
    
    return (
      <motion.div
        key={deal.id}
        initial={{ opacity: 0, x: -20 }}
        animate={{ opacity: 1, x: 0 }}
        className="bg-surface border border-default rounded-xl p-4 mb-3 hover:shadow-md transition-all"
      >
        <div className="flex items-start justify-between mb-3">
          <div>
            <h4 className="font-medium text-primary">{deal.title}</h4>
            <p className="text-sm text-secondary">
              {contact?.firstName} {contact?.lastName} • {contact?.company}
            </p>
          </div>
          <div className="text-right">
            <div className="font-bold text-primary">${deal.value.toLocaleString()}</div>
            <div className="text-xs text-secondary">{deal.probability}% probability</div>
          </div>
        </div>
        
        <div className="flex items-center justify-between mb-3">
          <div 
            className="px-2 py-1 rounded-full text-xs font-medium text-white"
            style={{ backgroundColor: stageInfo.color }}
          >
            {stageInfo.name}
          </div>
          <div className="text-sm text-secondary">
            Close: {new Date(deal.expectedCloseDate).toLocaleDateString()}
          </div>
        </div>
        
        <div className="w-full bg-gray-200 rounded-full h-2 mb-3">
          <div 
            className="h-2 rounded-full"
            style={{ 
              width: `${deal.probability}%`,
              backgroundColor: stageInfo.color 
            }}
          ></div>
        </div>
        
        <div className="flex items-center justify-between text-sm">
          <div className="text-secondary">
            Last activity: {new Date(deal.lastActivity).toLocaleDateString()}
          </div>
          <div className="flex space-x-1">
            <button className="p-1 hover:bg-surface-hover rounded">
              <EyeIcon className="h-4 w-4 text-blue-600" />
            </button>
            <button className="p-1 hover:bg-surface-hover rounded">
              <PencilIcon className="h-4 w-4 text-green-600" />
            </button>
          </div>
        </div>
      </motion.div>
    );
  };
  
  const renderContactModal = () => {
    if (!selectedContact) return null;
    
    return (
      <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-surface rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        >
          <div className="p-6 border-b border-default">
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <img
                  src={selectedContact.avatar}
                  alt={`${selectedContact.firstName} ${selectedContact.lastName}`}
                  className="w-16 h-16 rounded-full mr-4"
                />
                <div>
                  <h2 className="text-2xl font-bold text-primary">
                    {selectedContact.firstName} {selectedContact.lastName}
                  </h2>
                  <p className="text-secondary">{selectedContact.position} at {selectedContact.company}</p>
                  <div className="flex items-center space-x-3 mt-2">
                    <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getScoreColor(selectedContact.score)}`}>
                      Score: {selectedContact.score}
                    </span>
                    <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getPriorityColor(selectedContact.priority)}`}>
                      {selectedContact.priority} priority
                    </span>
                  </div>
                </div>
              </div>
              <button
                onClick={() => setSelectedContact(null)}
                className="p-2 hover:bg-surface-hover rounded-lg"
              >
                <XCircleIcon className="h-6 w-6" />
              </button>
            </div>
          </div>
          
          <div className="p-6">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <div className="lg:col-span-2 space-y-6">
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Contact Information</h3>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex items-center">
                      <EnvelopeIcon className="h-5 w-5 text-secondary mr-3" />
                      <div>
                        <div className="text-sm text-secondary">Email</div>
                        <div className="font-medium text-primary">{selectedContact.email}</div>
                      </div>
                    </div>
                    <div className="flex items-center">
                      <PhoneIcon className="h-5 w-5 text-secondary mr-3" />
                      <div>
                        <div className="text-sm text-secondary">Phone</div>
                        <div className="font-medium text-primary">{selectedContact.phone}</div>
                      </div>
                    </div>
                    <div className="flex items-center">
                      <MapPinIcon className="h-5 w-5 text-secondary mr-3" />
                      <div>
                        <div className="text-sm text-secondary">Location</div>
                        <div className="font-medium text-primary">{selectedContact.location}</div>
                      </div>
                    </div>
                    <div className="flex items-center">
                      <GlobeAltIcon className="h-5 w-5 text-secondary mr-3" />
                      <div>
                        <div className="text-sm text-secondary">Website</div>
                        <a href={selectedContact.website} className="font-medium text-blue-600 hover:underline">
                          {selectedContact.website}
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Notes</h3>
                  <div className="bg-surface-elevated rounded-lg p-4">
                    <p className="text-secondary">{selectedContact.notes}</p>
                  </div>
                </div>
                
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Tags & Interests</h3>
                  <div className="flex flex-wrap gap-2 mb-4">
                    {selectedContact.tags.map((tag, index) => (
                      <span key={index} className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                        {tag}
                      </span>
                    ))}
                  </div>
                  <div className="flex flex-wrap gap-2">
                    {selectedContact.interests.map((interest, index) => (
                      <span key={index} className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        {interest}
                      </span>
                    ))}
                  </div>
                </div>
              </div>
              
              <div className="space-y-6">
                <div className="bg-surface-elevated rounded-xl p-4">
                  <h4 className="font-semibold text-primary mb-4">Lead Score</h4>
                  <div className="text-center">
                    <div className={`text-3xl font-bold ${getScoreColor(selectedContact.score).split(' ')[0]}`}>
                      {selectedContact.score}
                    </div>
                    <div className="text-sm text-secondary">out of 100</div>
                  </div>
                </div>
                
                <div className="bg-surface-elevated rounded-xl p-4">
                  <h4 className="font-semibold text-primary mb-4">Engagement</h4>
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="text-secondary">Email Opens</span>
                      <span className="font-medium text-primary">{selectedContact.emailOpens}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-secondary">Email Clicks</span>
                      <span className="font-medium text-primary">{selectedContact.emailClicks}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-secondary">Activities</span>
                      <span className="font-medium text-primary">{selectedContact.activities}</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-secondary">Deals</span>
                      <span className="font-medium text-primary">{selectedContact.deals}</span>
                    </div>
                  </div>
                </div>
                
                <div className="space-y-2">
                  <button className="btn btn-primary w-full">
                    <EnvelopeIcon className="h-4 w-4 mr-2" />
                    Send Email
                  </button>
                  <button className="btn btn-secondary w-full">
                    <PhoneIcon className="h-4 w-4 mr-2" />
                    Schedule Call
                  </button>
                  <button className="btn btn-secondary w-full">
                    <CalendarIcon className="h-4 w-4 mr-2" />
                    Add Task
                  </button>
                  <button className="btn btn-secondary w-full">
                    <PencilIcon className="h-4 w-4 mr-2" />
                    Edit Contact
                  </button>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    );
  };
  
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <UserGroupIconSolid className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">CRM & Lead Management</h1>
            </div>
            <p className="text-white/80">Manage contacts, deals, and customer relationships</p>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{crmStats.totalContacts.toLocaleString()}</div>
              <div className="text-sm text-white/70">Total Contacts</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{crmStats.qualifiedLeads}</div>
              <div className="text-sm text-white/70">Qualified Leads</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">${crmStats.averageDealSize.toLocaleString()}</div>
              <div className="text-sm text-white/70">Avg Deal Size</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{crmStats.conversionRate}%</div>
              <div className="text-sm text-white/70">Conversion Rate</div>
            </div>
          </div>
        </div>
      </div>
      
      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'contacts', name: 'Contacts', icon: UserGroupIcon },
            { id: 'pipeline', name: 'Sales Pipeline', icon: TrendingUpIcon },
            { id: 'deals', name: 'Deals', icon: CurrencyDollarIcon },
            { id: 'activities', name: 'Activities', icon: CalendarIcon },
            { id: 'email', name: 'Email Campaigns', icon: EnvelopeIcon },
            { id: 'analytics', name: 'Analytics', icon: ChartBarIcon }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                  : 'border-transparent text-secondary hover:text-primary'
              }`}
            >
              <tab.icon className="h-4 w-4 mr-2" />
              {tab.name}
            </button>
          ))}
        </nav>
      </div>
      
      {/* Content */}
      {activeTab === 'contacts' && (
        <div className="space-y-6">
          {/* Search and Filters */}
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center space-x-4 flex-1">
                <div className="relative flex-1 max-w-lg">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Search contacts..."
                    className="pl-10 input"
                  />
                </div>
                <select
                  value={filters.status}
                  onChange={(e) => setFilters({ ...filters, status: e.target.value })}
                  className="input w-48"
                >
                  <option value="">All Stages</option>
                  {leadStages.map((stage) => (
                    <option key={stage.id} value={stage.id}>{stage.name}</option>
                  ))}
                </select>
                <select
                  value={filters.source}
                  onChange={(e) => setFilters({ ...filters, source: e.target.value })}
                  className="input w-48"
                >
                  <option value="">All Sources</option>
                  {contactSources.map((source) => (
                    <option key={source.id} value={source.id}>{source.name}</option>
                  ))}
                </select>
              </div>
              <div className="flex items-center space-x-3">
                <button
                  onClick={() => setViewMode(viewMode === 'grid' ? 'list' : 'grid')}
                  className="btn btn-secondary"
                >
                  {viewMode === 'grid' ? 'List View' : 'Grid View'}
                </button>
                <button
                  onClick={() => setShowContactModal(true)}
                  className="btn btn-primary"
                >
                  <PlusIcon className="h-4 w-4 mr-2" />
                  Add Contact
                </button>
              </div>
            </div>
            
            {/* Quick Stats */}
            <div className="grid grid-cols-5 gap-4">
              <div className="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div className="text-xl font-bold text-blue-600">{filteredContacts.length}</div>
                <div className="text-xs text-secondary">Total</div>
              </div>
              <div className="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div className="text-xl font-bold text-green-600">
                  {filteredContacts.filter(c => c.isQualified).length}
                </div>
                <div className="text-xs text-secondary">Qualified</div>
              </div>
              <div className="text-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div className="text-xl font-bold text-red-600">
                  {filteredContacts.filter(c => c.priority === 'high').length}
                </div>
                <div className="text-xs text-secondary">High Priority</div>
              </div>
              <div className="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div className="text-xl font-bold text-yellow-600">
                  {filteredContacts.filter(c => c.stage === 'new').length}
                </div>
                <div className="text-xs text-secondary">New Leads</div>
              </div>
              <div className="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <div className="text-xl font-bold text-purple-600">
                  ${filteredContacts.reduce((sum, c) => sum + (c.value || 0), 0).toLocaleString()}
                </div>
                <div className="text-xs text-secondary">Total Value</div>
              </div>
            </div>
          </div>
          
          {/* Contacts Grid */}
          <div className={viewMode === 'grid' 
            ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' 
            : 'space-y-3'
          }>
            {filteredContacts.map(renderContactCard)}
          </div>
          
          {filteredContacts.length === 0 && (
            <div className="text-center py-12">
              <UserGroupIcon className="h-12 w-12 mx-auto mb-4 text-gray-400" />
              <h3 className="text-lg font-medium text-primary">No contacts found</h3>
              <p className="text-secondary">Try adjusting your search criteria</p>
            </div>
          )}
        </div>
      )}
      
      {activeTab === 'deals' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-2xl font-bold text-primary">Active Deals</h2>
              <p className="text-secondary">Track and manage your sales opportunities</p>
            </div>
            <button className="btn btn-primary">
              <PlusIcon className="h-4 w-4 mr-2" />
              New Deal
            </button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {deals.map(renderDealCard)}
          </div>
        </div>
      )}
      
      {renderContactModal()}
    </div>
  );
};

export default ComprehensiveCRMSystem;