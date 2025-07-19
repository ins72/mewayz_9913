import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  CurrencyDollarIcon, 
  PlusIcon, 
  DocumentTextIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  CalendarDaysIcon,
  CreditCardIcon,
  BanknotesIcon,
  ReceiptPercentIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  ChartBarIcon,
  ClockIcon,
  ExclamationTriangleIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const FinancialManagementPage = () => {
  const [financialData, setFinancialData] = useState(null);
  const [transactions, setTransactions] = useState([]);
  const [invoices, setInvoices] = useState([]);
  const [expenses, setExpenses] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadFinancialData();
  }, []);

  const loadFinancialData = async () => {
    try {
      // Mock data for now - will be replaced with actual API calls
      setFinancialData({
        totalRevenue: 45230,
        totalExpenses: 18750,
        netProfit: 26480,
        profitMargin: 58.5,
        monthlyGrowth: 12.3,
        pendingInvoices: 8,
        overdueInvoices: 2,
        cashFlow: 26480
      });

      setTransactions([
        {
          id: 1,
          type: 'income',
          description: 'Payment from Client ABC',
          amount: 2500,
          date: '2025-07-19',
          category: 'Service Payment',
          status: 'completed',
          reference: 'INV-2025-001'
        },
        {
          id: 2,
          type: 'expense',
          description: 'Office Rent',
          amount: 1200,
          date: '2025-07-18',
          category: 'Office Expenses',
          status: 'completed',
          reference: 'EXP-2025-045'
        },
        {
          id: 3,
          type: 'income',
          description: 'Subscription Revenue',
          amount: 890,
          date: '2025-07-17',
          category: 'Recurring Revenue',
          status: 'completed',
          reference: 'SUB-2025-128'
        },
        {
          id: 4,
          type: 'expense',
          description: 'Software Licenses',
          amount: 450,
          date: '2025-07-16',
          category: 'Software',
          status: 'pending',
          reference: 'EXP-2025-046'
        }
      ]);

      setInvoices([
        {
          id: 1,
          invoiceNumber: 'INV-2025-001',
          client: 'Acme Corporation',
          amount: 2500,
          status: 'paid',
          dueDate: '2025-07-15',
          issuedDate: '2025-06-15',
          services: 'Digital Marketing Strategy'
        },
        {
          id: 2,
          invoiceNumber: 'INV-2025-002',
          client: 'Tech Startup Inc',
          amount: 1800,
          status: 'pending',
          dueDate: '2025-07-25',
          issuedDate: '2025-07-10',
          services: 'Website Development'
        },
        {
          id: 3,
          invoiceNumber: 'INV-2025-003',
          client: 'Global Solutions Ltd',
          amount: 3200,
          status: 'overdue',
          dueDate: '2025-07-10',
          issuedDate: '2025-06-25',
          services: 'Brand Identity Package'
        }
      ]);

      setExpenses([
        {
          id: 1,
          description: 'Office Rent',
          amount: 1200,
          category: 'Office',
          date: '2025-07-18',
          recurring: true,
          status: 'paid'
        },
        {
          id: 2,
          description: 'Software Subscriptions',
          amount: 450,
          category: 'Software',
          date: '2025-07-16',
          recurring: true,
          status: 'pending'
        },
        {
          id: 3,
          description: 'Marketing Campaign',
          amount: 800,
          category: 'Marketing',
          date: '2025-07-15',
          recurring: false,
          status: 'paid'
        }
      ]);

      setAnalytics({
        monthlyRevenue: [32000, 38000, 35000, 42000, 45230],
        monthlyExpenses: [15000, 17500, 16800, 19200, 18750],
        profitTrend: [17000, 20500, 18200, 22800, 26480],
        categories: {
          'Service Payment': 65,
          'Recurring Revenue': 25,
          'Other Income': 10
        }
      });

    } catch (error) {
      console.error('Failed to load financial data:', error);
    } finally {
      setLoading(false);
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
            <p className={`text-sm mt-2 flex items-center ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? <ArrowUpIcon className="w-4 h-4 mr-1" /> : <ArrowDownIcon className="w-4 h-4 mr-1" />}
              {Math.abs(change)}% from last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const TransactionRow = ({ transaction }) => (
    <div className="card p-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${
            transaction.type === 'income' 
              ? 'bg-green-100 dark:bg-green-900' 
              : 'bg-red-100 dark:bg-red-900'
          }`}>
            {transaction.type === 'income' ? (
              <ArrowTrendingUpIcon className={`w-5 h-5 ${
                transaction.type === 'income' 
                  ? 'text-green-600 dark:text-green-400' 
                  : 'text-red-600 dark:text-red-400'
              }`} />
            ) : (
              <ArrowTrendingDownIcon className="w-5 h-5 text-red-600 dark:text-red-400" />
            )}
          </div>
          <div>
            <h4 className="font-medium text-primary">{transaction.description}</h4>
            <p className="text-sm text-secondary">{transaction.category} • {transaction.date}</p>
          </div>
        </div>
        <div className="flex items-center space-x-4">
          <div className="text-right">
            <p className={`font-semibold ${
              transaction.type === 'income' 
                ? 'text-accent-success' 
                : 'text-accent-danger'
            }`}>
              {transaction.type === 'income' ? '+' : '-'}${transaction.amount.toLocaleString()}
            </p>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              transaction.status === 'completed'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            }`}>
              {transaction.status}
            </span>
          </div>
          <div className="flex items-center space-x-2">
            <button className="p-2 text-secondary hover:text-primary">
              <EyeIcon className="w-4 h-4" />
            </button>
            <button className="p-2 text-secondary hover:text-primary">
              <PencilIcon className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  );

  const InvoiceRow = ({ invoice }) => (
    <div className="card p-4">
      <div className="flex items-center justify-between">
        <div className="flex-1">
          <div className="flex items-center space-x-3 mb-2">
            <h4 className="font-medium text-primary">{invoice.invoiceNumber}</h4>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              invoice.status === 'paid'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : invoice.status === 'pending'
                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            }`}>
              {invoice.status}
            </span>
          </div>
          <p className="text-sm text-secondary mb-1">{invoice.client} • {invoice.services}</p>
          <p className="text-xs text-secondary">Issued: {invoice.issuedDate} • Due: {invoice.dueDate}</p>
        </div>
        <div className="flex items-center space-x-4">
          <div className="text-right">
            <p className="font-semibold text-primary">${invoice.amount.toLocaleString()}</p>
            {invoice.status === 'overdue' && (
              <p className="text-xs text-accent-danger flex items-center">
                <ExclamationTriangleIcon className="w-3 h-3 mr-1" />
                Overdue
              </p>
            )}
          </div>
          <div className="flex items-center space-x-2">
            <button className="p-2 text-secondary hover:text-primary">
              <EyeIcon className="w-4 h-4" />
            </button>
            <button className="p-2 text-secondary hover:text-primary">
              <PencilIcon className="w-4 h-4" />
            </button>
          </div>
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
          <h1 className="text-3xl font-bold text-primary">Financial Management</h1>
          <p className="text-secondary mt-1">Monitor revenue, expenses, and financial performance</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <DocumentTextIcon className="w-4 h-4 mr-2" />
            Generate Report
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Add Transaction
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'transactions', name: 'Transactions' },
            { id: 'invoices', name: 'Invoices' },
            { id: 'expenses', name: 'Expenses' },
            { id: 'reports', name: 'Reports' }
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
          {/* Financial Stats */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Total Revenue"
              value={financialData.totalRevenue.toLocaleString()}
              change={financialData.monthlyGrowth}
              icon={CurrencyDollarIcon}
              color="success"
              prefix="$"
            />
            <StatCard
              title="Total Expenses"
              value={financialData.totalExpenses.toLocaleString()}
              change={-8.2}
              icon={BanknotesIcon}
              color="warning"
              prefix="$"
            />
            <StatCard
              title="Net Profit"
              value={financialData.netProfit.toLocaleString()}
              change={financialData.monthlyGrowth}
              icon={ArrowTrendingUpIcon}
              color="primary"
              prefix="$"
            />
            <StatCard
              title="Profit Margin"
              value={financialData.profitMargin.toString()}
              change={3.7}
              icon={ReceiptPercentIcon}
              color="primary"
              suffix="%"
            />
          </div>

          {/* Quick Insights */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="card-elevated p-6">
              <div className="flex items-center space-x-3 mb-4">
                <div className="w-10 h-10 bg-gradient-primary rounded-lg flex items-center justify-center">
                  <DocumentTextIcon className="w-5 h-5 text-white" />
                </div>
                <div>
                  <h3 className="font-semibold text-primary">Pending Invoices</h3>
                  <p className="text-2xl font-bold text-accent-warning">{financialData.pendingInvoices}</p>
                </div>
              </div>
              <p className="text-secondary text-sm">Invoices waiting for payment</p>
            </div>

            <div className="card-elevated p-6">
              <div className="flex items-center space-x-3 mb-4">
                <div className="w-10 h-10 bg-gradient-danger rounded-lg flex items-center justify-center">
                  <ExclamationTriangleIcon className="w-5 h-5 text-white" />
                </div>
                <div>
                  <h3 className="font-semibold text-primary">Overdue Invoices</h3>
                  <p className="text-2xl font-bold text-accent-danger">{financialData.overdueInvoices}</p>
                </div>
              </div>
              <p className="text-secondary text-sm">Invoices past due date</p>
            </div>

            <div className="card-elevated p-6">
              <div className="flex items-center space-x-3 mb-4">
                <div className="w-10 h-10 bg-gradient-success rounded-lg flex items-center justify-center">
                  <TrendingUpIcon className="w-5 h-5 text-white" />
                </div>
                <div>
                  <h3 className="font-semibold text-primary">Cash Flow</h3>
                  <p className="text-2xl font-bold text-accent-success">${financialData.cashFlow.toLocaleString()}</p>
                </div>
              </div>
              <p className="text-secondary text-sm">Current month cash flow</p>
            </div>
          </div>

          {/* Recent Activity */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Recent Transactions</h2>
              <div className="space-y-3">
                {transactions.slice(0, 4).map((transaction) => (
                  <div key={transaction.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{transaction.description}</h4>
                        <p className="text-sm text-secondary">{transaction.category}</p>
                      </div>
                      <p className={`font-semibold ${
                        transaction.type === 'income' ? 'text-accent-success' : 'text-accent-danger'
                      }`}>
                        {transaction.type === 'income' ? '+' : '-'}${transaction.amount.toLocaleString()}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Invoice Status</h2>
              <div className="space-y-3">
                {invoices.slice(0, 4).map((invoice) => (
                  <div key={invoice.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{invoice.invoiceNumber}</h4>
                        <p className="text-sm text-secondary">{invoice.client}</p>
                      </div>
                      <div className="text-right">
                        <p className="font-semibold text-primary">${invoice.amount.toLocaleString()}</p>
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                          invoice.status === 'paid'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            : invoice.status === 'pending'
                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                        }`}>
                          {invoice.status}
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'transactions' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">All Transactions</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Types</option>
                <option>Income</option>
                <option>Expense</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>All Categories</option>
                <option>Service Payment</option>
                <option>Office Expenses</option>
                <option>Software</option>
              </select>
              <Button>
                <PlusIcon className="w-4 h-4 mr-2" />
                Add Transaction
              </Button>
            </div>
          </div>
          
          <div className="space-y-4">
            {transactions.map((transaction) => (
              <TransactionRow key={transaction.id} transaction={transaction} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'invoices' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Invoices</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Status</option>
                <option>Paid</option>
                <option>Pending</option>
                <option>Overdue</option>
              </select>
              <Button>
                <PlusIcon className="w-4 h-4 mr-2" />
                Create Invoice
              </Button>
            </div>
          </div>
          
          <div className="space-y-4">
            {invoices.map((invoice) => (
              <InvoiceRow key={invoice.id} invoice={invoice} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'expenses' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Expenses</h2>
            <Button>
              <PlusIcon className="w-4 h-4 mr-2" />
              Add Expense
            </Button>
          </div>
          
          <div className="space-y-4">
            {expenses.map((expense) => (
              <div key={expense.id} className="card p-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-4">
                    <div className="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                      <BanknotesIcon className="w-5 h-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                      <h4 className="font-medium text-primary">{expense.description}</h4>
                      <p className="text-sm text-secondary">{expense.category} • {expense.date}</p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-4">
                    <div className="text-right">
                      <p className="font-semibold text-accent-danger">-${expense.amount.toLocaleString()}</p>
                      <div className="flex items-center space-x-2">
                        {expense.recurring && (
                          <span className="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-xs font-medium">
                            Recurring
                          </span>
                        )}
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                          expense.status === 'paid'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                        }`}>
                          {expense.status}
                        </span>
                      </div>
                    </div>
                    <div className="flex items-center space-x-2">
                      <button className="p-2 text-secondary hover:text-primary">
                        <EyeIcon className="w-4 h-4" />
                      </button>
                      <button className="p-2 text-secondary hover:text-primary">
                        <PencilIcon className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {activeTab === 'reports' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Financial Reports</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ChartBarIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Profit & Loss</h3>
              <p className="text-secondary">Comprehensive P&L statements and trends</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ArrowTrendingUpIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Cash Flow</h3>
              <p className="text-secondary">Track money in and out of your business</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ReceiptPercentIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Tax Reports</h3>
              <p className="text-secondary">Generate tax-ready financial reports</p>
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default FinancialManagementPage;