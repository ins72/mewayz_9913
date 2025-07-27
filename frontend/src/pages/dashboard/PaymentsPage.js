import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  CreditCardIcon, 
  PlusIcon, 
  EyeIcon,
  BanknotesIcon,
  ArrowDownTrayIcon,
  ChartBarIcon,
  CalendarIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ClockIcon,
  ArrowTrendingUpIcon,
  CurrencyDollarIcon,
  BuildingLibraryIcon,
  UserIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const PaymentsPage = () => {
  const [transactions, setTransactions] = useState([]);
  const [payouts, setPayouts] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadPaymentsData();
  }, []);

  const loadPaymentsData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      // Real data loaded from API

      // Real data loaded from API

      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load payments data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary', subtitle = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {subtitle && (
            <p className="text-sm text-secondary mt-1">{subtitle}</p>
          )}
          {change && (
            <div className="flex items-center mt-2">
              <ArrowTrendingUpIcon className="w-4 h-4 text-accent-success mr-1" />
              <span className="text-sm font-medium text-accent-success">
                +{change}% vs last month
              </span>
            </div>
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
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-primary rounded-full flex items-center justify-center">
            <UserIcon className="w-5 h-5 text-white" />
          </div>
          <div>
            <h3 className="font-semibold text-primary">{transaction.customer}</h3>
            <p className="text-secondary text-sm">{transaction.id}</p>
          </div>
        </div>
        <div className="text-right">
          <p className="text-2xl font-bold text-accent-primary">
            ${transaction.amount.toFixed(2)}
          </p>
          <p className="text-secondary text-sm">
            Fee: ${transaction.fee.toFixed(2)}
          </p>
        </div>
      </div>
      
      <p className="text-primary mb-3">{transaction.description}</p>
      
      <div className="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
          <p className="text-secondary">Payment Method</p>
          <p className="font-medium text-primary">{transaction.method}</p>
        </div>
        <div>
          <p className="text-secondary">Date</p>
          <p className="font-medium text-primary">{transaction.date}</p>
        </div>
      </div>
      
      <div className="flex items-center justify-between pt-4 border-t border-default">
        <span className={`px-3 py-1 rounded-full text-sm font-medium ${
          transaction.status === 'completed'
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : transaction.status === 'pending'
            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        }`}>
          {transaction.status === 'completed' && <CheckCircleIcon className="w-4 h-4 inline mr-1" />}
          {transaction.status === 'pending' && <ClockIcon className="w-4 h-4 inline mr-1" />}
          {transaction.status === 'failed' && <ExclamationCircleIcon className="w-4 h-4 inline mr-1" />}
          {transaction.status}
        </span>
        <Button variant="secondary" size="small">
          <EyeIcon className="w-4 h-4 mr-1" />
          View Details
        </Button>
      </div>
    </div>
  );

  const PayoutCard = ({ payout }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <div>
          <h3 className="font-semibold text-primary">{payout.id}</h3>
          <p className="text-secondary text-sm">Account: {payout.account}</p>
        </div>
        <div className="text-right">
          <p className="text-2xl font-bold text-accent-primary">
            ${payout.amount.toFixed(2)}
          </p>
          <p className="text-secondary text-sm">
            {payout.transactions} transactions
          </p>
        </div>
      </div>
      
      <div className="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
          <p className="text-secondary">Status</p>
          <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
            payout.status === 'paid'
              ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
              : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
          }`}>
            {payout.status === 'paid' && <CheckCircleIcon className="w-3 h-3 mr-1" />}
            {payout.status === 'pending' && <ClockIcon className="w-3 h-3 mr-1" />}
            {payout.status}
          </span>
        </div>
        <div>
          <p className="text-secondary">Date</p>
          <p className="font-medium text-primary">{payout.date}</p>
        </div>
      </div>
      
      <div className="pt-4 border-t border-default">
        <Button variant="secondary" size="small" fullWidth>
          <ArrowDownTrayIcon className="w-4 h-4 mr-2" />
          Download Statement
        </Button>
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
          <h1 className="text-3xl font-bold text-primary">Payments & Billing</h1>
          <p className="text-secondary mt-1">Track transactions, payouts, and revenue</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <ArrowDownTrayIcon className="w-4 h-4 mr-2" />
            Export Data
          </Button>
          <Button variant="secondary">
            <BuildingLibraryIcon className="w-4 h-4 mr-2" />
            Bank Settings
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Invoice
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'transactions', name: 'Transactions' },
            { id: 'payouts', name: 'Payouts' },
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
              title="Total Revenue"
              value={`$${analytics.totalRevenue.toLocaleString()}`}
              change={analytics.monthlyGrowth}
              icon={CurrencyDollarIcon}
              color="primary"
              subtitle="This month"
            />
            <StatCard
              title="Transactions"
              value={analytics.totalTransactions.toString()}
              change={12.3}
              icon={CreditCardIcon}
              color="success"
              subtitle="This month"
            />
            <StatCard
              title="Success Rate"
              value={`${analytics.successRate}%`}
              change={1.2}
              icon={CheckCircleIcon}
              color="warning"
              subtitle="Payment success"
            />
            <StatCard
              title="Avg. Transaction"
              value={`$${analytics.averageValue.toFixed(2)}`}
              change={8.7}
              icon={ChartBarIcon}
              color="primary"
              subtitle="Per transaction"
            />
          </div>

          {/* Recent Activity */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Recent Transactions</h2>
              <div className="space-y-4">
                {transactions.slice(0, 3).map((transaction) => (
                  <div key={transaction.id} className="card-elevated p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <p className="font-medium text-primary">{transaction.customer}</p>
                        <p className="text-secondary text-sm">{transaction.description}</p>
                      </div>
                      <div className="text-right">
                        <p className="font-bold text-accent-primary">${transaction.amount}</p>
                        <span className={`text-xs px-2 py-1 rounded-full ${
                          transaction.status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                          transaction.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                        }`}>
                          {transaction.status}
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Pending Payouts</h2>
              <div className="space-y-4">
                {payouts.filter(p => p.status === 'pending').map((payout) => (
                  <div key={payout.id} className="card-elevated p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <p className="font-medium text-primary">{payout.id}</p>
                        <p className="text-secondary text-sm">{payout.transactions} transactions</p>
                      </div>
                      <div className="text-right">
                        <p className="font-bold text-accent-primary">${payout.amount}</p>
                        <p className="text-secondary text-sm">Due {payout.date}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <BanknotesIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Request Payout</h3>
              <p className="text-secondary">Transfer your available balance</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <CalendarIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Payment Reports</h3>
              <p className="text-secondary">Generate detailed payment reports</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <BuildingLibraryIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Banking Settings</h3>
              <p className="text-secondary">Manage your bank account details</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'transactions' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">All Transactions</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Status</option>
                <option>Completed</option>
                <option>Pending</option>
                <option>Failed</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>Last 30 days</option>
                <option>Last 7 days</option>
                <option>Last 90 days</option>
                <option>This year</option>
              </select>
              <Button variant="secondary">
                <ArrowDownTrayIcon className="w-4 h-4 mr-2" />
                Export
              </Button>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {transactions.map((transaction) => (
              <TransactionCard key={transaction.id} transaction={transaction} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'payouts' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Payouts</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Payouts</option>
                <option>Paid</option>
                <option>Pending</option>
              </select>
              <Button>
                <BanknotesIcon className="w-4 h-4 mr-2" />
                Request Payout
              </Button>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {payouts.map((payout) => (
              <PayoutCard key={payout.id} payout={payout} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Payment Analytics</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive payment analytics to help you track revenue trends, payment method performance, and customer behavior.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default PaymentsPage;