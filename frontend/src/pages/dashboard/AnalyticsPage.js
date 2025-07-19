import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  ChartBarIcon, 
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  EyeIcon,
  UserGroupIcon,
  CurrencyDollarIcon,
  ClockIcon,
  CalendarIcon,
  FunnelIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  GlobeAltIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const AnalyticsPage = () => {
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');
  const [dateRange, setDateRange] = useState('7d');

  useEffect(() => {
    loadAnalyticsData();
  }, [dateRange]);

  const loadAnalyticsData = async () => {
    try {
      // Mock data for now - replace with actual API calls
      setAnalytics({
        overview: {
          totalVisitors: 15420,
          visitorChange: 12.5,
          pageViews: 45680,
          pageViewChange: 8.3,
          bounceRate: 2.4,
          bounceRateChange: -0.8,
          avgSessionDuration: '4:32',
          sessionChange: 15.2
        },
        traffic: {
          organic: 6200,
          direct: 3800,
          referral: 2100,
          social: 1900,
          email: 1420
        },
        topPages: [
          { page: '/', views: 8920, bounce: '24%' },
          { page: '/about', views: 4560, bounce: '31%' },
          { page: '/pricing', views: 3240, bounce: '18%' },
          { page: '/login', views: 2890, bounce: '45%' },
          { page: '/register', views: 2340, bounce: '52%' }
        ],
        devices: {
          desktop: 8900,
          mobile: 5200,
          tablet: 1320
        },
        locations: [
          { country: 'United States', visitors: 6200, percentage: 40.2 },
          { country: 'United Kingdom', visitors: 2340, percentage: 15.2 },
          { country: 'Canada', visitors: 1890, percentage: 12.3 },
          { country: 'Australia', visitors: 1450, percentage: 9.4 },
          { country: 'Germany', visitors: 1120, percentage: 7.3 }
        ],
        conversions: {
          signUps: 234,
          purchases: 89,
          subscriptions: 156,
          contactForms: 78
        }
      });
    } catch (error) {
      console.error('Failed to load analytics data:', error);
    } finally {
      setLoading(false);
    }
  };

  const MetricCard = ({ title, value, change, icon: Icon, color = 'primary', suffix = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}{suffix}</p>
          <div className="flex items-center mt-2">
            {change > 0 ? (
              <ArrowTrendingUpIcon className="w-4 h-4 text-accent-success mr-1" />
            ) : (
              <ArrowTrendingDownIcon className="w-4 h-4 text-accent-danger mr-1" />
            )}
            <span className={`text-sm font-medium ${
              change > 0 ? 'text-accent-success' : 'text-accent-danger'
            }`}>
              {Math.abs(change)}%
            </span>
            <span className="text-secondary text-sm ml-1">vs last period</span>
          </div>
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const TrafficSourceCard = ({ source, value, total }) => {
    const percentage = ((value / total) * 100).toFixed(1);
    return (
      <div className="flex items-center justify-between py-3">
        <div className="flex items-center space-x-3">
          <div className="w-3 h-3 bg-accent-primary rounded-full"></div>
          <span className="text-primary font-medium capitalize">{source}</span>
        </div>
        <div className="text-right">
          <p className="text-primary font-bold">{value.toLocaleString()}</p>
          <p className="text-secondary text-sm">{percentage}%</p>
        </div>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  const totalTraffic = Object.values(analytics.traffic).reduce((a, b) => a + b, 0);

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">Analytics Dashboard</h1>
          <p className="text-secondary mt-1">Track your website and business performance</p>
        </div>
        <div className="flex items-center space-x-3">
          <select 
            className="input px-3 py-2 rounded-md"
            value={dateRange}
            onChange={(e) => setDateRange(e.target.value)}
          >
            <option value="7d">Last 7 days</option>
            <option value="30d">Last 30 days</option>
            <option value="90d">Last 90 days</option>
            <option value="1y">Last year</option>
          </select>
          <Button variant="secondary">
            <CalendarIcon className="w-4 h-4 mr-2" />
            Custom Range
          </Button>
          <Button>
            Export Report
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'traffic', name: 'Traffic Sources' },
            { id: 'pages', name: 'Top Pages' },
            { id: 'audience', name: 'Audience' },
            { id: 'conversions', name: 'Conversions' }
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
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <MetricCard
              title="Total Visitors"
              value={analytics.overview.totalVisitors.toLocaleString()}
              change={analytics.overview.visitorChange}
              icon={UserGroupIcon}
              color="primary"
            />
            <MetricCard
              title="Page Views"
              value={analytics.overview.pageViews.toLocaleString()}
              change={analytics.overview.pageViewChange}
              icon={EyeIcon}
              color="success"
            />
            <MetricCard
              title="Bounce Rate"
              value={analytics.overview.bounceRate}
              change={analytics.overview.bounceRateChange}
              icon={ArrowTrendingDownIcon}
              color="warning"
              suffix="%"
            />
            <MetricCard
              title="Avg. Session Duration"
              value={analytics.overview.avgSessionDuration}
              change={analytics.overview.sessionChange}
              icon={ClockIcon}
              color="primary"
            />
          </div>

          {/* Charts Placeholder */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="card-elevated p-6">
              <h3 className="text-lg font-semibold text-primary mb-4">Visitor Trends</h3>
              <div className="h-64 bg-gradient-surface rounded-lg flex items-center justify-center">
                <div className="text-center">
                  <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
                  <p className="text-secondary">Interactive charts coming soon</p>
                </div>
              </div>
            </div>
            
            <div className="card-elevated p-6">
              <h3 className="text-lg font-semibold text-primary mb-4">Top Traffic Sources</h3>
              <div className="space-y-1">
                {Object.entries(analytics.traffic).map(([source, value]) => (
                  <TrafficSourceCard 
                    key={source} 
                    source={source} 
                    value={value} 
                    total={totalTraffic} 
                  />
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'traffic' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Traffic Sources</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {Object.entries(analytics.traffic).map(([source, value]) => {
              const percentage = ((value / totalTraffic) * 100).toFixed(1);
              return (
                <div key={source} className="card-elevated p-6">
                  <div className="flex items-center justify-between mb-4">
                    <h3 className="font-semibold text-primary capitalize">{source}</h3>
                    <div className="text-right">
                      <p className="text-2xl font-bold text-accent-primary">{value.toLocaleString()}</p>
                      <p className="text-secondary text-sm">{percentage}%</p>
                    </div>
                  </div>
                  <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                      className="bg-accent-primary h-2 rounded-full transition-all duration-300"
                      style={{ width: `${percentage}%` }}
                    ></div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {activeTab === 'pages' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Top Pages</h2>
          
          <div className="card-elevated overflow-hidden">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead className="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Page
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Views
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Bounce Rate
                    </th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                  {analytics.topPages.map((page, index) => (
                    <tr key={index} className="hover-surface">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className="text-primary font-medium">{page.page}</span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className="text-primary">{page.views.toLocaleString()}</span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className="text-primary">{page.bounce}</span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'audience' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Audience Insights</h2>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Device Types */}
            <div className="card-elevated p-6">
              <h3 className="text-lg font-semibold text-primary mb-4">Device Types</h3>
              <div className="space-y-4">
                {Object.entries(analytics.devices).map(([device, value]) => {
                  const total = Object.values(analytics.devices).reduce((a, b) => a + b, 0);
                  const percentage = ((value / total) * 100).toFixed(1);
                  const Icon = device === 'desktop' ? ComputerDesktopIcon : 
                              device === 'mobile' ? DevicePhoneMobileIcon : GlobeAltIcon;
                  
                  return (
                    <div key={device} className="flex items-center justify-between">
                      <div className="flex items-center space-x-3">
                        <Icon className="w-5 h-5 text-accent-primary" />
                        <span className="text-primary font-medium capitalize">{device}</span>
                      </div>
                      <div className="text-right">
                        <p className="text-primary font-bold">{value.toLocaleString()}</p>
                        <p className="text-secondary text-sm">{percentage}%</p>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
            
            {/* Top Locations */}
            <div className="card-elevated p-6">
              <h3 className="text-lg font-semibold text-primary mb-4">Top Locations</h3>
              <div className="space-y-3">
                {analytics.locations.map((location, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <span className="text-primary font-medium">{location.country}</span>
                    <div className="text-right">
                      <p className="text-primary font-bold">{location.visitors.toLocaleString()}</p>
                      <p className="text-secondary text-sm">{location.percentage}%</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'conversions' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Conversion Tracking</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {Object.entries(analytics.conversions).map(([type, value]) => (
              <div key={type} className="card-elevated p-6 text-center">
                <FunnelIcon className="w-12 h-12 text-accent-primary mx-auto mb-4" />
                <h3 className="font-semibold text-primary mb-2 capitalize">{type.replace(/([A-Z])/g, ' $1')}</h3>
                <p className="text-3xl font-bold text-accent-primary">{value}</p>
                <p className="text-secondary text-sm mt-1">This period</p>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default AnalyticsPage;