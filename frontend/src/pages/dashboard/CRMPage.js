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
      // Mock data for now - replace with actual API calls
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

      setDeals([
        {
          id: 1,
          title: 'Enterprise Software License',
          contact: 'John Smith',
          value: 15000,
          stage: 'proposal',
          probability: 75,
          closeDate: '2025-08-15',
          activities: 8
        },
        {
          id: 2,
          title: 'Consulting Services Package',
          contact: 'Sarah Johnson',
          value: 8000,
          stage: 'negotiation',
          probability: 90,
          closeDate: '2025-07-30',
          activities: 12
        },
        {
          id: 3,
          title: 'Monthly Subscription',
          contact: 'Mike Chen',
          value: 2400,
          stage: 'closed_won',
          probability: 100,
          closeDate: '2025-07-15',
          activities: 6
        }
      ]);

      setAnalytics({
        totalContacts: 847,
        totalDeals: 156,
        pipelineValue: 125000,
        conversionRate: 18.5,
        averageDealSize: 8950,
        topLeadSource: 'Website'
      });
    } catch (error) {
      console.error('Failed to load CRM data:', error);
    } finally {
      setLoading(false);
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
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">CRM System</h1>
          <p className="text-secondary mt-1">Manage your customers and sales pipeline</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <FunnelIcon className="w-4 h-4 mr-2" />
            Manage Pipeline
          </Button>
          <Button>
            <UserPlusIcon className="w-4 h-4 mr-2" />
            Add Contact
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'contacts', name: 'Contacts' },
            { id: 'deals', name: 'Deals' },
            { id: 'analytics', name: 'Analytics' }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`py-2 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-accent-primary text-accent-primary'
                  : 'border-transparent text-secondary hover:text-primary hover:border-gray-300'
              }`}
            >
              {tab.name}
            </button>
          ))}
        </nav>
      </div>

      {/* Content based on active tab */}
      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* Analytics Stats */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Total Contacts"
              value={analytics.totalContacts.toLocaleString()}
              change={8.2}
              icon={UsersIcon}
              color="primary"
            />
            <StatCard
              title="Active Deals"
              value={analytics.totalDeals.toLocaleString()}
              change={15.1}
              icon={CurrencyDollarIcon}
              color="success"
            />
            <StatCard
              title="Pipeline Value"
              value={`$${analytics.pipelineValue.toLocaleString()}`}
              change={22.5}
              icon={FunnelIcon}
              color="warning"
            />
            <StatCard
              title="Conversion Rate"
              value={`${analytics.conversionRate}%`}
              change={3.7}
              icon={ChartBarIcon}
              color="primary"
            />
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <UserPlusIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Add New Contact</h3>
              <p className="text-secondary">Import or manually add new leads and customers</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <CalendarIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Schedule Follow-up</h3>
              <p className="text-secondary">Set reminders for important customer interactions</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ChartBarIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">View Reports</h3>
              <p className="text-secondary">Analyze sales performance and trends</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'contacts' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Contacts</h2>
            <div className="flex items-center space-x-3">
              <input 
                type="text" 
                placeholder="Search contacts..."
                className="input px-3 py-2 rounded-md"
              />
              <select className="input px-3 py-2 rounded-md">
                <option>All Contacts</option>
                <option>Leads</option>
                <option>Prospects</option>
                <option>Customers</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>All Sources</option>
                <option>Website</option>
                <option>Referral</option>
                <option>Social Media</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {contacts.map((contact) => (
              <ContactCard key={contact.id} contact={contact} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'deals' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Sales Pipeline</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Stages</option>
                <option>Prospect</option>
                <option>Proposal</option>
                <option>Negotiation</option>
                <option>Closed Won</option>
                <option>Closed Lost</option>
              </select>
              <Button>
                <PlusIcon className="w-4 h-4 mr-2" />
                New Deal
              </Button>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {deals.map((deal) => (
              <DealCard key={deal.id} deal={deal} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">CRM Analytics</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive CRM analytics to help you track sales performance, customer behavior, and pipeline health.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default CRMPage;