import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import toast from 'react-hot-toast';
import {
  BanknotesIcon,
  ChartBarIcon,
  TrendingUpIcon,
  TrendingDownIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  CreditCardIcon,
  DocumentChartBarIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon,
  CalendarIcon
} from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';

const FinancialManagementPage = () => {
  const { user } = useAuth();
  const [financialData, setFinancialData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedPeriod, setSelectedPeriod] = useState('monthly');

  useEffect(() => {
    fetchFinancialData();
  }, []);

  const fetchFinancialData = async () => {
    try {
      setLoading(true);
      // Mock comprehensive financial data
      const mockData = {
        financial_overview: {
          total_revenue: 567890.45,
          total_expenses: 234567.23,
          net_profit: 333323.22,
          profit_margin: 58.7,
          cash_flow: 45670.89,
          burn_rate: 12340.56,
          runway_months: 27
        },
        revenue_streams: [
          { source: "Subscription Revenue", amount: 234567, percentage: 41.3, growth: "+15.6%" },
          { source: "Course Sales", amount: 156789, percentage: 27.6, growth: "+23.4%" },
          { source: "Consulting Services", amount: 123456, percentage: 21.7, growth: "+8.9%" },
          { source: "Affiliate Commissions", amount: 53078, percentage: 9.4, growth: "+34.2%" }
        ],
        expense_breakdown: [
          { category: "Personnel", amount: 89456, percentage: 38.1, budget_variance: "+2.3%" },
          { category: "Technology", amount: 45678, percentage: 19.5, budget_variance: "-5.7%" },
          { category: "Marketing", amount: 34567, percentage: 14.7, budget_variance: "+12.4%" },
          { category: "Operations", amount: 23456, percentage: 10.0, budget_variance: "-1.2%" }
        ],
        financial_ratios: {
          current_ratio: 2.45,
          quick_ratio: 1.89,
          debt_to_equity: 0.34,
          return_on_assets: 12.7,
          return_on_equity: 18.9
        },
        forecasting: {
          next_quarter_revenue: 178450,
          year_end_projection: 689750,
          growth_rate_forecast: 24.7,
          scenario_analysis: {
            optimistic: 756890,
            realistic: 689750,
            pessimistic: 612340
          }
        }
      };
      
      setFinancialData(mockData);
    } catch (error) {
      console.error('Failed to fetch financial data:', error);
      toast.error('Failed to load financial data');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-primary"></div>
      </div>
    );
  }

  const overviewCards = [
    {
      title: 'Total Revenue',
      value: `$${financialData?.financial_overview?.total_revenue?.toLocaleString()}`,
      change: '+15.6%',
      changeType: 'positive',
      icon: BanknotesIcon,
      color: 'bg-green-500'
    },
    {
      title: 'Net Profit',
      value: `$${financialData?.financial_overview?.net_profit?.toLocaleString()}`,
      change: '+23.4%',
      changeType: 'positive',
      icon: TrendingUpIcon,
      color: 'bg-blue-500'
    },
    {
      title: 'Profit Margin',
      value: `${financialData?.financial_overview?.profit_margin}%`,
      change: '+2.8%',
      changeType: 'positive',
      icon: ChartBarIcon,
      color: 'bg-purple-500'
    },
    {
      title: 'Cash Flow',
      value: `$${financialData?.financial_overview?.cash_flow?.toLocaleString()}`,
      change: '+8.9%',
      changeType: 'positive',
      icon: CreditCardIcon,
      color: 'bg-indigo-500'
    }
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="flex items-center justify-between"
      >
        <div>
          <h1 className="text-3xl font-bold text-primary mb-2 flex items-center">
            <BanknotesIcon className="h-8 w-8 text-accent-primary mr-3" />
            Financial Management
          </h1>
          <p className="text-secondary">
            Comprehensive financial analytics and business intelligence.
          </p>
        </div>
        
        <div className="flex items-center space-x-4">
          <select
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value)}
            className="input rounded-lg focus-ring"
          >
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="yearly">Yearly</option>
          </select>
        </div>
      </motion.div>

      {/* Overview Cards */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        {overviewCards.map((card, index) => (
          <div key={card.title} className="bg-surface-elevated p-6 rounded-lg shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary mb-1">{card.title}</p>
                <p className="text-2xl font-bold text-primary">{card.value}</p>
                <div className="flex items-center mt-2">
                  {card.changeType === 'positive' ? (
                    <ArrowUpIcon className="w-4 h-4 text-green-500 mr-1" />
                  ) : (
                    <ArrowDownIcon className="w-4 h-4 text-red-500 mr-1" />
                  )}
                  <span className={`text-sm ${
                    card.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {card.change}
                  </span>
                </div>
              </div>
              <div className={`p-3 rounded-lg ${card.color}`}>
                <card.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Revenue & Expenses Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue Streams */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Revenue Streams</h3>
          <div className="space-y-4">
            {financialData?.revenue_streams?.map((stream, index) => (
              <div key={index} className="flex items-center justify-between p-4 bg-surface rounded-lg">
                <div>
                  <p className="font-medium text-primary">{stream.source}</p>
                  <p className="text-sm text-secondary">{stream.percentage}% of total</p>
                </div>
                <div className="text-right">
                  <p className="font-semibold text-primary">${stream.amount.toLocaleString()}</p>
                  <p className="text-sm text-green-600">{stream.growth}</p>
                </div>
              </div>
            ))}
          </div>
        </motion.div>

        {/* Expense Breakdown */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.3 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Expense Breakdown</h3>
          <div className="space-y-4">
            {financialData?.expense_breakdown?.map((expense, index) => (
              <div key={index} className="flex items-center justify-between p-4 bg-surface rounded-lg">
                <div>
                  <p className="font-medium text-primary">{expense.category}</p>
                  <p className="text-sm text-secondary">{expense.percentage}% of total</p>
                </div>
                <div className="text-right">
                  <p className="font-semibold text-primary">${expense.amount.toLocaleString()}</p>
                  <p className={`text-sm ${
                    expense.budget_variance.startsWith('+') ? 'text-red-600' : 'text-green-600'
                  }`}>
                    {expense.budget_variance} vs budget
                  </p>
                </div>
              </div>
            ))}
          </div>
        </motion.div>
      </div>

      {/* Financial Ratios & Forecasting */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Financial Ratios */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Financial Ratios</h3>
          <div className="grid grid-cols-2 gap-4">
            {Object.entries(financialData?.financial_ratios || {}).map(([key, value]) => (
              <div key={key} className="text-center p-4 bg-surface rounded-lg">
                <p className="text-2xl font-bold text-accent-primary">{value}</p>
                <p className="text-sm text-secondary capitalize">
                  {key.replace(/_/g, ' ')}
                </p>
              </div>
            ))}
          </div>
        </motion.div>

        {/* Forecasting */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.5 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Revenue Forecasting</h3>
          <div className="space-y-4">
            <div className="flex justify-between items-center p-3 bg-surface rounded-lg">
              <span className="text-secondary">Next Quarter</span>
              <span className="font-semibold text-primary">
                ${financialData?.forecasting?.next_quarter_revenue?.toLocaleString()}
              </span>
            </div>
            <div className="flex justify-between items-center p-3 bg-surface rounded-lg">
              <span className="text-secondary">Year-end Projection</span>
              <span className="font-semibold text-primary">
                ${financialData?.forecasting?.year_end_projection?.toLocaleString()}
              </span>
            </div>
            <div className="flex justify-between items-center p-3 bg-surface rounded-lg">
              <span className="text-secondary">Growth Rate Forecast</span>
              <span className="font-semibold text-green-500">
                +{financialData?.forecasting?.growth_rate_forecast}%
              </span>
            </div>
            
            <div className="mt-6">
              <h4 className="font-medium text-primary mb-3">Scenario Analysis</h4>
              <div className="space-y-2">
                {Object.entries(financialData?.forecasting?.scenario_analysis || {}).map(([scenario, value]) => (
                  <div key={scenario} className="flex justify-between items-center">
                    <span className="text-secondary capitalize">{scenario}</span>
                    <span className="font-medium text-primary">${value.toLocaleString()}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </motion.div>
      </div>

      {/* Cash Flow Analysis */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.6 }}
        className="bg-surface-elevated p-6 rounded-lg shadow-default"
      >
        <h3 className="text-lg font-semibold text-primary mb-4">Cash Flow Analysis</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="text-center">
            <div className="text-3xl font-bold text-green-500 mb-2">
              ${financialData?.financial_overview?.cash_flow?.toLocaleString()}
            </div>
            <p className="text-secondary">Current Cash Flow</p>
          </div>
          <div className="text-center">
            <div className="text-3xl font-bold text-orange-500 mb-2">
              ${financialData?.financial_overview?.burn_rate?.toLocaleString()}
            </div>
            <p className="text-secondary">Monthly Burn Rate</p>
          </div>
          <div className="text-center">
            <div className="text-3xl font-bold text-blue-500 mb-2">
              {financialData?.financial_overview?.runway_months} months
            </div>
            <p className="text-secondary">Cash Runway</p>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default FinancialManagementPage;