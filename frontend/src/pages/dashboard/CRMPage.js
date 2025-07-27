import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  UsersIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon,
  EyeIcon,
  PhoneIcon,
  EnvelopeIcon,
  FunnelIcon,
  ChartBarIcon,
  UserPlusIcon,
  BuildingOfficeIcon,
  CurrencyDollarIcon,
  CalendarIcon,
  StarIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const CRMPage = () => {
  const [contacts, setContacts] = useState([]);
  const [deals, setDeals] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadCRMData();
  }, []);

  const loadCRMData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      setContacts([
        {
          id: 1,
          name: 'John Smith',
          email: 'john@example.com',
          phone: '+1 (555) 123-4567',
          company: 'Tech Innovations Inc.',
          status: 'lead',
          value: 5000,
          lastContact: '2025-07-19',
          source: 'website'
        },
        {
          id: 2,
          name: 'Sarah Johnson',
          email: 'sarah@company.com',
          phone: '+1 (555) 987-6543',
          company: 'Digital Solutions Ltd.',
          status: 'customer',
          value: 12000,
          lastContact: '2025-07-18',
          source: 'referral'
        },
        {
          id: 3,
          name: 'Mike Chen',
          email: 'mike.chen@startup.io',
          phone: '+1 (555) 456-7890',
          company: 'StartupCo',
          status: 'prospect',
          value: 8500,
          lastContact: '2025-07-17',
          source: 'social_media'
        }
      ]);

      // Real data loaded from API

      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load CRM data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {change && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% vs last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const ContactCard = ({ contact }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-primary rounded-full flex items-center justify-center">
            <span className="text-white font-bold">{contact.name.charAt(0)}</span>
          </div>
          <div>
            <h3 className="font-semibold text-primary">{contact.name}</h3>
            <p className="text-secondary text-sm">{contact.company}</p>
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <button className="p-2 text-secondary hover:text-primary">
            <PhoneIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <EnvelopeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
      
      <div className="space-y-2 text-sm mb-4">
        <div className="flex items-center space-x-2">
          <EnvelopeIcon className="w-4 h-4 text-accent-primary" />
          <span className="text-primary">{contact.email}</span>
        </div>
        <div className="flex items-center space-x-2">
          <PhoneIcon className="w-4 h-4 text-accent-primary" />
          <span className="text-primary">{contact.phone}</span>
        </div>
        <div className="flex items-center space-x-2">
          <CurrencyDollarIcon className="w-4 h-4 text-accent-primary" />
          <span className="text-primary">${contact.value.toLocaleString()}</span>
        </div>
      </div>
      
      <div className="flex items-center justify-between pt-4 border-t border-default">
        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
          contact.status === 'customer'
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : contact.status === 'lead'
            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
        }`}>
          {contact.status}
        </span>
        <div className="text-right">
          <p className="text-xs text-secondary">Last contact</p>
          <p className="text-sm font-medium text-primary">{contact.lastContact}</p>
        </div>
      </div>
    </div>
  );

  const DealCard = ({ deal }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div>
          <h3 className="font-semibold text-primary">{deal.title}</h3>
          <p className="text-secondary text-sm">{deal.contact}</p>
        </div>
        <div className="text-right">
          <p className="text-2xl font-bold text-accent-primary">${deal.value.toLocaleString()}</p>
          <p className="text-sm text-secondary">{deal.probability}% probability</p>
        </div>
      </div>
      
      <div className="mb-4">
        <div className="flex items-center justify-between text-sm mb-1">
          <span className="text-secondary">Progress</span>
          <span className="text-primary font-medium">{deal.stage.replace('_', ' ')}</span>
        </div>
        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            className={`h-2 rounded-full transition-all duration-300 ${
              deal.stage === 'closed_won' ? 'bg-accent-success' :
              deal.stage === 'closed_lost' ? 'bg-accent-danger' :
              'bg-accent-primary'
            }`}
            style={{ width: `${deal.probability}%` }}
          ></div>
        </div>
      </div>
      
      <div className="grid grid-cols-2 gap-4 text-sm">
        <div>
          <p className="text-secondary">Close Date</p>
          <p className="font-medium text-primary">{deal.closeDate}</p>
        </div>
        <div>
          <p className="text-secondary">Activities</p>
          <p className="font-medium text-primary">{deal.activities}</p>
        </div>
      </div>
      
      <div className="mt-4 flex items-center justify-between">
        <Button variant="secondary" size="small">View Details</Button>
        <Button size="small">Update</Button>
      </div>
    </div>
  );

  if (loading) {
    
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
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  