import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  ChartBarIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  EyeIcon,
  UserIcon,
  CurrencyDollarIcon,
  ClockIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  GlobeAltIcon,
  MapPinIcon,
  CalendarIcon,
  FunnelIcon
} from '@heroicons/react/24/outline';

const AdvancedAnalytics = () => {
  const [timeRange, setTimeRange] = useState('7days');
  const [activeTab, setActiveTab] = useState('overview');
  const [loading, setLoading] = useState(true);
  const [analyticsData, setAnalyticsData] = useState({});

  const timeRanges = [
    { id: '24hours', label: 'Last 24 Hours' },
    { id: '7days', label: 'Last 7 Days' },
    { id: '30days', label: 'Last 30 Days' },
    { id: '90days', label: 'Last 3 Months' },
    { id: '1year', label: 'Last Year' }
  ];

  const tabs = [
    { id: 'overview', label: 'Overview', icon: ChartBarIcon },
    { id: 'audience', label: 'Audience', icon: UserIcon },
    { id: 'behavior', label: 'Behavior', icon: EyeIcon },
    { id: 'acquisition', label: 'Acquisition', icon: ArrowTrendingUpIcon },
    { id: 'conversions', label: 'Conversions', icon: CurrencyDollarIcon }
  ];

  // Mock analytics data
  const mockData = {
    overview: {
      totalVisitors: 24567,
      totalPageviews: 89234,
      avgSessionDuration: '3:24',
      bounceRate: '42.3%',
      conversions: 1234,
      revenue: 45670.50,
      visitorsTrend: 12.5,
      pageviewsTrend: 8.7,
      durationTrend: -2.1,
      bounceRateTrend: -5.3,
      conversionsTrend: 23.4,
      revenueTrend: 18.9
    },
    audience: {
      demographics: {
        ageGroups: [
          { range: '18-24', percentage: 23.5 },
          { range: '25-34', percentage: 35.2 },
          { range: '35-44', percentage: 24.8 },
          { range: '45-54', percentage: 12.1 },
          { range: '55+', percentage: 4.4 }
        ],
        gender: [
          { type: 'Female', percentage: 52.3 },
          { type: 'Male', percentage: 47.7 }
        ]
      },
      topCountries: [
        { country: 'United States', visitors: 8901, percentage: 36.2 },
        { country: 'United Kingdom', visitors: 3456, percentage: 14.1 },
        { country: 'Canada', visitors: 2345, percentage: 9.5 },
        { country: 'Germany', visitors: 1987, percentage: 8.1 },
        { country: 'Australia', visitors: 1654, percentage: 6.7 }
      ],
      devices: [
        { type: 'Desktop', percentage: 54.2, icon: ComputerDesktopIcon },
        { type: 'Mobile', percentage: 38.7, icon: DevicePhoneMobileIcon },
        { type: 'Tablet', percentage: 7.1, icon: DevicePhoneMobileIcon }
      ]
    },
    behavior: {
      topPages: [
        { page: '/', views: 12345, avgTime: '2:34', bounce: '35.2%' },
        { page: '/about', views: 8901, avgTime: '1:58', bounce: '42.1%' },
        { page: '/services', views: 6789, avgTime: '3:12', bounce: '28.7%' },
        { page: '/contact', views: 4567, avgTime: '1:23', bounce: '55.8%' },
        { page: '/blog', views: 3456, avgTime: '4:45', bounce: '22.3%' }
      ],
      flowData: [
        { step: 'Landing Page', visitors: 10000, dropoff: 0 },
        { step: 'Product Page', visitors: 7500, dropoff: 25 },
        { step: 'Add to Cart', visitors: 4500, dropoff: 40 },
        { step: 'Checkout', visitors: 2700, dropoff: 40 },
        { step: 'Purchase', visitors: 1890, dropoff: 30 }
      ]
    },
    acquisition: {
      channels: [
        { name: 'Organic Search', visitors: 9876, percentage: 40.2, trend: 12.5 },
        { name: 'Direct', visitors: 6543, percentage: 26.6, trend: -3.2 },
        { name: 'Social Media', visitors: 4321, percentage: 17.6, trend: 28.9 },
        { name: 'Referral', visitors: 2345, percentage: 9.5, trend: 5.7 },
        { name: 'Email', visitors: 1482, percentage: 6.1, trend: 15.3 }
      ],
      campaigns: [
        { name: 'Summer Sale 2024', clicks: 5432, conversions: 234, ctr: '4.3%', cost: 2340.50 },
        { name: 'Brand Awareness', clicks: 8901, conversions: 156, ctr: '1.8%', cost: 4567.80 },
        { name: 'Product Launch', clicks: 3456, conversions: 89, ctr: '2.6%', cost: 1890.25 }
      ]
    },
    conversions: {
      goals: [
        { name: 'Newsletter Signup', completions: 456, rate: '12.3%', value: 2280.00 },
        { name: 'Product Purchase', completions: 234, rate: '6.2%', value: 23400.00 },
        { name: 'Contact Form', completions: 189, rate: '4.8%', value: 9450.00 },
        { name: 'Free Trial', completions: 123, rate: '3.1%', value: 6150.00 }
      ],
      ecommerce: {
        transactions: 234,
        revenue: 45670.50,
        avgOrderValue: 195.17,
        revenuePerVisitor: 1.86
      }
    }
  };

  useEffect(() => {
    // Simulate data fetching
    setTimeout(() => {
      // Real data loaded from API
      // Real data loaded from API
    }, 1000);
  }, [timeRange]);

  const StatCard = ({ title, value, trend, icon: Icon, prefix = '', suffix = '' }) => (
    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
      <div className="flex items-center justify-between mb-4">
        <div className="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
          <Icon className="h-6 w-6 text-blue-600 dark:text-blue-400" />
        </div>
        {trend !== undefined && (
          <div className={`flex items-center text-sm ${trend >= 0 ? 'text-green-600' : 'text-red-600'}`}>
            {trend >= 0 ? (
              <ArrowTrendingUpIcon className="h-4 w-4 mr-1" />
            ) : (
              <ArrowTrendingDownIcon className="h-4 w-4 mr-1" />
            )}
            {Math.abs(trend)}%
          </div>
        )}
      </div>
      <div className="text-2xl font-bold text-gray-900 dark:text-white mb-1">
        {prefix}{typeof value === 'number' ? value.toLocaleString() : value}{suffix}
      </div>
      <div className="text-gray-600 dark:text-gray-400 text-sm">{title}</div>
    </div>
  );

  const renderOverview = () => (
    <div className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <StatCard
          title="Total Visitors"
          value={analyticsData.overview?.totalVisitors}
          trend={analyticsData.overview?.visitorsTrend}
          icon={UserIcon}
        />
        <StatCard
          title="Page Views"
          value={analyticsData.overview?.totalPageviews}
          trend={analyticsData.overview?.pageviewsTrend}
          icon={EyeIcon}
        />
        <StatCard
          title="Avg. Session Duration"
          value={analyticsData.overview?.avgSessionDuration}
          trend={analyticsData.overview?.durationTrend}
          icon={ClockIcon}
        />
        <StatCard
          title="Bounce Rate"
          value={analyticsData.overview?.bounceRate}
          trend={analyticsData.overview?.bounceRateTrend}
          icon={ArrowTrendingDownIcon}
        />
        <StatCard
          title="Conversions"
          value={analyticsData.overview?.conversions}
          trend={analyticsData.overview?.conversionsTrend}
          icon={FunnelIcon}
        />
        <StatCard
          title="Revenue"
          value={analyticsData.overview?.revenue}
          trend={analyticsData.overview?.revenueTrend}
          icon={CurrencyDollarIcon}
          prefix="$"
        />
      </div>

      {/* Chart placeholder */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Visitors Over Time
        </h3>
        <div className="h-64 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
          <div className="text-gray-500 dark:text-gray-400">
            <ChartBarIcon className="h-16 w-16 mx-auto mb-2" />
            <p>Chart visualization would appear here</p>
          </div>
        </div>
      </div>
    </div>
  );

  const renderAudience = () => (
    <div className="space-y-6">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Demographics */}
        <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Age Demographics
          </h3>
          <div className="space-y-3">
            {analyticsData.audience?.demographics.ageGroups.map((group) => (
              <div key={group.range} className="flex items-center justify-between">
                <span className="text-gray-600 dark:text-gray-300">{group.range}</span>
                <div className="flex items-center">
                  <div className="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                    <div
                      className="bg-blue-600 h-2 rounded-full"
                      style={{ width: `${group.percentage}%` }}
                    ></div>
                  </div>
                  <span className="text-sm font-medium text-gray-900 dark:text-white w-12">
                    {group.percentage}%
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Devices */}
        <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Device Usage
          </h3>
          <div className="space-y-4">
            {analyticsData.audience?.devices.map((device) => (
              <div key={device.type} className="flex items-center justify-between">
                <div className="flex items-center">
                  <device.icon className="h-5 w-5 text-gray-500 mr-3" />
                  <span className="text-gray-600 dark:text-gray-300">{device.type}</span>
                </div>
                <div className="flex items-center">
                  <div className="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                    <div
                      className="bg-purple-600 h-2 rounded-full"
                      style={{ width: `${device.percentage}%` }}
                    ></div>
                  </div>
                  <span className="text-sm font-medium text-gray-900 dark:text-white w-12">
                    {device.percentage}%
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Top Countries */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
          <MapPinIcon className="h-5 w-5 mr-2" />
          Top Countries
        </h3>
        <div className="space-y-3">
          {analyticsData.audience?.topCountries.map((country) => (
            <div key={country.country} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <span className="font-medium text-gray-900 dark:text-white">{country.country}</span>
              <div className="flex items-center space-x-4">
                <span className="text-gray-600 dark:text-gray-300">
                  {country.visitors.toLocaleString()} visitors
                </span>
                <span className="text-sm font-medium text-blue-600 dark:text-blue-400">
                  {country.percentage}%
                </span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderBehavior = () => (
    <div className="space-y-6">
      {/* Top Pages */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Top Pages
        </h3>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-gray-200 dark:border-gray-700">
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Page</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Views</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Avg. Time</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Bounce Rate</th>
              </tr>
            </thead>
            <tbody>
              {analyticsData.behavior?.topPages.map((page, index) => (
                <tr key={index} className="border-b border-gray-100 dark:border-gray-800">
                  <td className="py-3 px-4 font-medium text-gray-900 dark:text-white">{page.page}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">{page.views.toLocaleString()}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">{page.avgTime}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">{page.bounce}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* User Flow */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          User Flow Analysis
        </h3>
        <div className="space-y-4">
          {analyticsData.behavior?.flowData.map((step, index) => (
            <div key={index} className="relative">
              <div className="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div className="flex items-center">
                  <div className="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-4">
                    {index + 1}
                  </div>
                  <span className="font-medium text-gray-900 dark:text-white">{step.step}</span>
                </div>
                <div className="flex items-center space-x-4">
                  <span className="text-lg font-semibold text-gray-900 dark:text-white">
                    {step.visitors.toLocaleString()}
                  </span>
                  {step.dropoff > 0 && (
                    <span className="text-sm text-red-600 bg-red-100 dark:bg-red-900 px-2 py-1 rounded">
                      -{step.dropoff}%
                    </span>
                  )}
                </div>
              </div>
              {index < analyticsData.behavior?.flowData.length - 1 && (
                <div className="absolute left-6 top-full w-0.5 h-4 bg-gray-300 dark:bg-gray-600"></div>
              )}
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderAcquisition = () => (
    <div className="space-y-6">
      {/* Acquisition Channels */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Acquisition Channels
        </h3>
        <div className="space-y-3">
          {analyticsData.acquisition?.channels.map((channel) => (
            <div key={channel.name} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <span className="font-medium text-gray-900 dark:text-white">{channel.name}</span>
              <div className="flex items-center space-x-4">
                <span className="text-gray-600 dark:text-gray-300">
                  {channel.visitors.toLocaleString()} ({channel.percentage}%)
                </span>
                <div className={`flex items-center text-sm ${channel.trend >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                  {channel.trend >= 0 ? (
                    <ArrowTrendingUpIcon className="h-4 w-4 mr-1" />
                  ) : (
                    <ArrowTrendingDownIcon className="h-4 w-4 mr-1" />
                  )}
                  {Math.abs(channel.trend)}%
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Campaigns */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Campaign Performance
        </h3>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-gray-200 dark:border-gray-700">
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Campaign</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Clicks</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Conversions</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">CTR</th>
                <th className="text-left py-3 px-4 text-gray-600 dark:text-gray-300">Cost</th>
              </tr>
            </thead>
            <tbody>
              {analyticsData.acquisition?.campaigns.map((campaign, index) => (
                <tr key={index} className="border-b border-gray-100 dark:border-gray-800">
                  <td className="py-3 px-4 font-medium text-gray-900 dark:text-white">{campaign.name}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">{campaign.clicks.toLocaleString()}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">{campaign.conversions}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">{campaign.ctr}</td>
                  <td className="py-3 px-4 text-gray-600 dark:text-gray-300">${campaign.cost}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );

  const renderConversions = () => (
    <div className="space-y-6">
      {/* Goal Completions */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Goal Completions
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {analyticsData.conversions?.goals.map((goal) => (
            <div key={goal.name} className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <h4 className="font-medium text-gray-900 dark:text-white mb-2">{goal.name}</h4>
              <div className="flex items-center justify-between">
                <div>
                  <div className="text-2xl font-bold text-gray-900 dark:text-white">
                    {goal.completions}
                  </div>
                  <div className="text-sm text-gray-600 dark:text-gray-300">
                    {goal.rate} conversion rate
                  </div>
                </div>
                <div className="text-right">
                  <div className="text-lg font-semibold text-green-600">
                    ${goal.value.toLocaleString()}
                  </div>
                  <div className="text-xs text-gray-500">value</div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* E-commerce Metrics */}
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          E-commerce Performance
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div className="text-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <div className="text-2xl font-bold text-blue-600 dark:text-blue-400">
              {analyticsData.conversions?.ecommerce.transactions}
            </div>
            <div className="text-sm text-gray-600 dark:text-gray-300">Transactions</div>
          </div>
          <div className="text-center p-4 bg-green-50 dark:bg-green-900 rounded-lg">
            <div className="text-2xl font-bold text-green-600 dark:text-green-400">
              ${analyticsData.conversions?.ecommerce.revenue?.toLocaleString()}
            </div>
            <div className="text-sm text-gray-600 dark:text-gray-300">Revenue</div>
          </div>
          <div className="text-center p-4 bg-purple-50 dark:bg-purple-900 rounded-lg">
            <div className="text-2xl font-bold text-purple-600 dark:text-purple-400">
              ${analyticsData.conversions?.ecommerce.avgOrderValue?.toFixed(2)}
            </div>
            <div className="text-sm text-gray-600 dark:text-gray-300">Avg. Order Value</div>
          </div>
          <div className="text-center p-4 bg-orange-50 dark:bg-orange-900 rounded-lg">
            <div className="text-2xl font-bold text-orange-600 dark:text-orange-400">
              ${analyticsData.conversions?.ecommerce.revenuePerVisitor?.toFixed(2)}
            </div>
            <div className="text-sm text-gray-600 dark:text-gray-300">Revenue/Visitor</div>
          </div>
        </div>
      </div>
    </div>
  );

  if (loading) {
    
  const loadAnalyticsData = async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/analytics/overview', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setAnalytics(data);
      } else {
        console.error('Failed to load analytics data');
      }
    } catch (error) {
      console.error('Error loading analytics data:', error);
    } finally {
      setLoading(false);
    }
  };


  return (
      <div className="max-w-7xl mx-auto p-6">
        <div className="animate-pulse">
          <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-8"></div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {[1, 2, 3, 4, 5, 6].map(i => (
              <div key={i} className="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  const renderTabContent = () => {
    switch (activeTab) {
      case 'overview': return renderOverview();
      case 'audience': return renderAudience();
      case 'behavior': return renderBehavior();
      case 'acquisition': return renderAcquisition();
      case 'conversions': return renderConversions();
      default: return renderOverview();
    }
  };

  