import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  ShieldCheckIcon, 
  PlusIcon, 
  LockClosedIcon,
  CurrencyDollarIcon,
  DocumentTextIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  HandRaisedIcon,
  UserIcon,
  EyeIcon,
  PencilIcon,
  ArrowPathIcon,
  BanknotesIcon,
  TruckIcon,
  FlagIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const EscrowSystemPage = () => {
  const [escrowTransactions, setEscrowTransactions] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');
  const [selectedTransaction, setSelectedTransaction] = useState(null);

  useEffect(() => {
    loadEscrowData();
  }, []);

  const loadEscrowData = async () => {
    try {
      // Mock data for now - will be replaced with actual API calls
      setEscrowTransactions([
        {
          id: 1,
          transactionId: 'ESC-2025-001',
          buyer: 'John Smith',
          seller: 'Tech Solutions Inc',
          amount: 2500,
          description: 'Custom Website Development',
          status: 'funded',
          createdDate: '2025-07-15',
          fundedDate: '2025-07-16',
          milestones: [
            { id: 1, description: 'Initial Design Mockups', amount: 750, status: 'completed', completedDate: '2025-07-17' },
            { id: 2, description: 'Frontend Development', amount: 1000, status: 'in_progress' },
            { id: 3, description: 'Backend Integration', amount: 500, status: 'pending' },
            { id: 4, description: 'Testing & Deployment', amount: 250, status: 'pending' }
          ],
          dispute: null
        },
        {
          id: 2,
          transactionId: 'ESC-2025-002',
          buyer: 'Digital Marketing Co',
          seller: 'Creative Agency Ltd',
          amount: 1800,
          description: 'Brand Identity Package',
          status: 'in_delivery',
          createdDate: '2025-07-10',
          fundedDate: '2025-07-11',
          deliveryDate: '2025-07-18',
          milestones: [
            { id: 1, description: 'Logo Design', amount: 800, status: 'completed', completedDate: '2025-07-14' },
            { id: 2, description: 'Brand Guidelines', amount: 600, status: 'completed', completedDate: '2025-07-17' },
            { id: 3, description: 'Marketing Materials', amount: 400, status: 'delivered', deliveredDate: '2025-07-18' }
          ],
          dispute: null
        },
        {
          id: 3,
          transactionId: 'ESC-2025-003',
          buyer: 'StartupXYZ',
          seller: 'Mobile Dev Studio',
          amount: 5000,
          description: 'Mobile App Development',
          status: 'disputed',
          createdDate: '2025-06-25',
          fundedDate: '2025-06-26',
          milestones: [
            { id: 1, description: 'App Wireframes', amount: 1000, status: 'completed', completedDate: '2025-07-01' },
            { id: 2, description: 'iOS Development', amount: 2000, status: 'disputed' },
            { id: 3, description: 'Android Development', amount: 2000, status: 'pending' }
          ],
          dispute: {
            id: 1,
            reason: 'Delivered work does not match specifications',
            filedBy: 'buyer',
            filedDate: '2025-07-15',
            status: 'under_review',
            mediator: 'Escrow Mediation Team'
          }
        },
        {
          id: 4,
          transactionId: 'ESC-2025-004',
          buyer: 'E-commerce Plus',
          seller: 'Web Design Pro',
          amount: 3200,
          description: 'E-commerce Website Build',
          status: 'completed',
          createdDate: '2025-06-01',
          fundedDate: '2025-06-02',
          completedDate: '2025-07-10',
          milestones: [
            { id: 1, description: 'Design & Layout', amount: 800, status: 'completed', completedDate: '2025-06-15' },
            { id: 2, description: 'Product Catalog Setup', amount: 1200, status: 'completed', completedDate: '2025-06-28' },
            { id: 3, description: 'Payment Integration', amount: 800, status: 'completed', completedDate: '2025-07-05' },
            { id: 4, description: 'Testing & Launch', amount: 400, status: 'completed', completedDate: '2025-07-10' }
          ],
          dispute: null,
          rating: 5
        }
      ]);

      setAnalytics({
        totalTransactions: 47,
        totalVolume: 156780,
        activeTransactions: 12,
        completedTransactions: 31,
        disputeRate: 4.2,
        averageCompletionTime: 18,
        successRate: 95.8,
        securedFunds: 48500
      });

    } catch (error) {
      console.error('Failed to load escrow data:', error);
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'created': return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
      case 'funded': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
      case 'in_progress': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
      case 'in_delivery': return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
      case 'disputed': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
      case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
  };

  const getStatusIcon = (status) => {
    switch (status) {
      case 'funded': return <LockClosedIcon className="w-4 h-4" />;
      case 'in_progress': return <ArrowPathIcon className="w-4 h-4" />;
      case 'in_delivery': return <TruckIcon className="w-4 h-4" />;
      case 'disputed': return <ExclamationTriangleIcon className="w-4 h-4" />;
      case 'completed': return <CheckCircleIcon className="w-4 h-4" />;
      default: return <ClockIcon className="w-4 h-4" />;
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary', suffix = '', prefix = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{prefix}{value}{suffix}</p>
          {change !== undefined && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% from last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const TransactionCard = ({ transaction }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <h3 className="font-semibold text-primary">{transaction.transactionId}</h3>
            <span className={`px-2 py-1 rounded-full text-xs font-medium flex items-center space-x-1 ${getStatusColor(transaction.status)}`}>
              {getStatusIcon(transaction.status)}
              <span>{transaction.status.replace('_', ' ')}</span>
            </span>
          </div>
          <p className="text-secondary text-sm mb-3">{transaction.description}</p>
          
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p className="text-secondary">Buyer</p>
              <p className="font-medium text-primary">{transaction.buyer}</p>
            </div>
            <div>
              <p className="text-secondary">Seller</p>
              <p className="font-medium text-primary">{transaction.seller}</p>
            </div>
            <div>
              <p className="text-secondary">Amount</p>
              <p className="font-medium text-primary">${transaction.amount.toLocaleString()}</p>
            </div>
            <div>
              <p className="text-secondary">Created</p>
              <p className="font-medium text-primary">{transaction.createdDate}</p>
            </div>
          </div>

          {/* Milestones Progress */}
          <div className="mt-4">
            <div className="flex items-center justify-between mb-2">
              <p className="text-sm font-medium text-secondary">Progress</p>
              <p className="text-xs text-secondary">
                {transaction.milestones.filter(m => m.status === 'completed').length} / {transaction.milestones.length} milestones
              </p>
            </div>
            <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div 
                className="bg-accent-primary h-2 rounded-full transition-all duration-300"
                style={{ 
                  width: `${(transaction.milestones.filter(m => m.status === 'completed').length / transaction.milestones.length) * 100}%` 
                }}
              ></div>
            </div>
          </div>

          {/* Dispute Information */}
          {transaction.dispute && (
            <div className="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
              <div className="flex items-center space-x-2 mb-2">
                <FlagIcon className="w-4 h-4 text-accent-danger" />
                <span className="text-sm font-medium text-accent-danger">Dispute Active</span>
              </div>
              <p className="text-xs text-secondary">{transaction.dispute.reason}</p>
              <p className="text-xs text-secondary mt-1">
                Filed by {transaction.dispute.filedBy} on {transaction.dispute.filedDate}
              </p>
            </div>
          )}

        </div>
        
        <div className="flex items-center space-x-2 ml-4">
          <button 
            className="p-2 text-secondary hover:text-primary"
            onClick={() => setSelectedTransaction(transaction)}
          >
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
        </div>
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
          <h1 className="text-3xl font-bold text-primary">Escrow System</h1>
          <p className="text-secondary mt-1">Secure payment protection for all transactions</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <DocumentTextIcon className="w-4 h-4 mr-2" />
            Guidelines
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Escrow
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'active', name: 'Active Transactions' },
            { id: 'completed', name: 'Completed' },
            { id: 'disputed', name: 'Disputes' },
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
              title="Total Volume"
              value={analytics.totalVolume.toLocaleString()}
              change={24.7}
              icon={CurrencyDollarIcon}
              color="success"
              prefix="$"
            />
            <StatCard
              title="Active Transactions"
              value={analytics.activeTransactions.toString()}
              icon={ArrowPathIcon}
              color="primary"
            />
            <StatCard
              title="Success Rate"
              value={analytics.successRate.toString()}
              change={2.1}
              icon={CheckCircleIcon}
              color="success"
              suffix="%"
            />
            <StatCard
              title="Secured Funds"
              value={analytics.securedFunds.toLocaleString()}
              icon={ShieldCheckIcon}
              color="primary"
              prefix="$"
            />
          </div>

          {/* How It Works */}
          <div className="card-elevated p-6">
            <h2 className="text-xl font-semibold text-primary mb-6">How Escrow Protection Works</h2>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div className="text-center">
                <div className="w-12 h-12 bg-gradient-primary rounded-lg flex items-center justify-center mx-auto mb-3">
                  <PlusIcon className="w-6 h-6 text-white" />
                </div>
                <h3 className="font-semibold text-primary mb-2">1. Create</h3>
                <p className="text-sm text-secondary">Create an escrow transaction with agreed terms and milestones</p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-gradient-primary rounded-lg flex items-center justify-center mx-auto mb-3">
                  <LockClosedIcon className="w-6 h-6 text-white" />
                </div>
                <h3 className="font-semibold text-primary mb-2">2. Fund</h3>
                <p className="text-sm text-secondary">Buyer securely funds the escrow account</p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-gradient-primary rounded-lg flex items-center justify-center mx-auto mb-3">
                  <TruckIcon className="w-6 h-6 text-white" />
                </div>
                <h3 className="font-semibold text-primary mb-2">3. Deliver</h3>
                <p className="text-sm text-secondary">Seller delivers work according to milestones</p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-gradient-success rounded-lg flex items-center justify-center mx-auto mb-3">
                  <CheckCircleIcon className="w-6 h-6 text-white" />
                </div>
                <h3 className="font-semibold text-primary mb-2">4. Release</h3>
                <p className="text-sm text-secondary">Funds are released upon buyer approval</p>
              </div>
            </div>
          </div>

          {/* Recent Activity */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Recent Transactions</h2>
              <div className="space-y-4">
                {escrowTransactions.slice(0, 3).map((transaction) => (
                  <div key={transaction.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{transaction.transactionId}</h4>
                        <p className="text-sm text-secondary">{transaction.description}</p>
                      </div>
                      <div className="text-right">
                        <p className="font-semibold text-primary">${transaction.amount.toLocaleString()}</p>
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(transaction.status)}`}>
                          {transaction.status.replace('_', ' ')}
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Key Metrics</h2>
              <div className="space-y-4">
                <div className="card p-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <ClockIcon className="w-4 h-4 text-blue-600 dark:text-blue-400" />
                      </div>
                      <div>
                        <p className="font-medium text-primary">Average Completion</p>
                        <p className="text-sm text-secondary">Time to complete transactions</p>
                      </div>
                    </div>
                    <p className="font-semibold text-primary">{analytics.averageCompletionTime} days</p>
                  </div>
                </div>
                
                <div className="card p-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <ExclamationTriangleIcon className="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                      </div>
                      <div>
                        <p className="font-medium text-primary">Dispute Rate</p>
                        <p className="text-sm text-secondary">Percentage of disputed transactions</p>
                      </div>
                    </div>
                    <p className="font-semibold text-primary">{analytics.disputeRate}%</p>
                  </div>
                </div>
                
                <div className="card p-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <CheckCircleIcon className="w-4 h-4 text-green-600 dark:text-green-400" />
                      </div>
                      <div>
                        <p className="font-medium text-primary">Completed</p>
                        <p className="text-sm text-secondary">Successfully completed transactions</p>
                      </div>
                    </div>
                    <p className="font-semibold text-primary">{analytics.completedTransactions}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'active' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Active Transactions</h2>
            <Button>
              <PlusIcon className="w-4 h-4 mr-2" />
              Create New Escrow
            </Button>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {escrowTransactions.filter(t => ['funded', 'in_progress', 'in_delivery'].includes(t.status)).map((transaction) => (
              <TransactionCard key={transaction.id} transaction={transaction} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'completed' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Completed Transactions</h2>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {escrowTransactions.filter(t => t.status === 'completed').map((transaction) => (
              <TransactionCard key={transaction.id} transaction={transaction} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'disputed' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Disputed Transactions</h2>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {escrowTransactions.filter(t => t.status === 'disputed').map((transaction) => (
              <TransactionCard key={transaction.id} transaction={transaction} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Escrow Analytics</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Total Transactions"
              value={analytics.totalTransactions.toString()}
              change={18.5}
              icon={DocumentTextIcon}
              color="primary"
            />
            <StatCard
              title="Monthly Volume"
              value={analytics.totalVolume.toLocaleString()}
              change={32.1}
              icon={BanknotesIcon}
              color="success"
              prefix="$"
            />
            <StatCard
              title="Success Rate"
              value={analytics.successRate.toString()}
              change={1.5}
              icon={CheckCircleIcon}
              color="success"
              suffix="%"
            />
            <StatCard
              title="Dispute Resolution"
              value="96.8"
              icon={HandRaisedIcon}
              color="warning"
              suffix="%"
            />
          </div>

          <div className="card-elevated p-8 text-center">
            <DocumentTextIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics</h3>
            <p className="text-secondary mb-4">Comprehensive escrow transaction analytics and performance reports</p>
            <Button>
              Generate Full Report
            </Button>
          </div>
        </div>
      )}
    </div>
  );
};

export default EscrowSystemPage;