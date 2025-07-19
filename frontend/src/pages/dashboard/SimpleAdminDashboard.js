import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';

const SimpleAdminDashboard = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('overview');

  if (!user || user.role !== 'admin') {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <div className="text-6xl mb-4">🛡️</div>
          <h3 className="text-lg font-medium text-primary">Access Denied</h3>
          <p className="text-secondary">Admin privileges required to access this page.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Admin Header */}
      <div className="bg-gradient-to-r from-red-600 via-orange-600 to-yellow-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold mb-2">Admin Dashboard</h1>
            <p className="text-white/80">Comprehensive platform management and monitoring</p>
          </div>
          <div className="flex items-center space-x-6">
            <div className="text-center">
              <div className="text-2xl font-bold">15,847</div>
              <div className="text-sm text-white/70">Total Users</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold">$284K</div>
              <div className="text-sm text-white/70">Total Revenue</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold">99.8%</div>
              <div className="text-sm text-white/70">System Health</div>
            </div>
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        {[
          { name: 'Manage Users', icon: '👥', color: 'bg-blue-500' },
          { name: 'Revenue Reports', icon: '💰', color: 'bg-green-500' },
          { name: 'System Status', icon: '🖥️', color: 'bg-purple-500' },
          { name: 'Content Review', icon: '📄', color: 'bg-orange-500' },
          { name: 'Feature Usage', icon: '📊', color: 'bg-pink-500' },
          { name: 'Settings', icon: '⚙️', color: 'bg-gray-500' }
        ].map((action) => (
          <button
            key={action.name}
            onClick={() => setActiveTab(action.name.toLowerCase().replace(' ', '_'))}
            className="p-4 bg-surface-elevated rounded-xl shadow-default hover:shadow-lg transition-all text-left"
          >
            <div className={`w-10 h-10 rounded-lg ${action.color} flex items-center justify-center mb-3 text-xl`}>
              {action.icon}
            </div>
            <p className="text-sm font-medium text-primary">{action.name}</p>
          </button>
        ))}
      </div>

      {/* Main Content Tabs */}
      <div className="bg-surface-elevated rounded-xl shadow-default">
        <div className="border-b border-default">
          <nav className="flex space-x-8 px-6">
            {[
              { id: 'overview', name: 'Platform Overview' },
              { id: 'users', name: 'User Management' },
              { id: 'revenue', name: 'Revenue Analytics' },
              { id: 'system', name: 'System Monitoring' },
              { id: 'content', name: 'Content Management' },
              { id: 'features', name: 'Feature Analytics' }
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`py-4 px-1 border-b-2 font-medium text-sm ${
                  activeTab === tab.id
                    ? 'border-red-500 text-red-600 dark:text-red-400'
                    : 'border-transparent text-secondary hover:text-primary'
                }`}
              >
                {tab.name}
              </button>
            ))}
          </nav>
        </div>

        <div className="p-6">
          {activeTab === 'overview' && (
            <div className="space-y-6">
              {/* Platform Metrics */}
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Total Users</p>
                      <p className="text-2xl font-bold text-primary">15,847</p>
                      <div className="flex items-center mt-1">
                        <span className="text-sm text-green-500">+12.5%</span>
                      </div>
                    </div>
                    <div className="text-3xl">👥</div>
                  </div>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.1 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Monthly Revenue</p>
                      <p className="text-2xl font-bold text-primary">$284,568</p>
                      <div className="flex items-center mt-1">
                        <span className="text-sm text-green-500">+31.2%</span>
                      </div>
                    </div>
                    <div className="text-3xl">💰</div>
                  </div>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.2 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">System Health</p>
                      <p className="text-2xl font-bold text-primary">99.8%</p>
                      <div className="flex items-center mt-1">
                        <span className="text-sm text-green-500">All systems operational</span>
                      </div>
                    </div>
                    <div className="text-3xl">🖥️</div>
                  </div>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.3 }}
                  className="bg-surface border border-default rounded-xl p-6"
                >
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">API Calls</p>
                      <p className="text-2xl font-bold text-primary">2.8M</p>
                      <div className="flex items-center mt-1">
                        <span className="text-sm text-green-500">+15.3%</span>
                      </div>
                    </div>
                    <div className="text-3xl">🌐</div>
                  </div>
                </motion.div>
              </div>

              {/* Platform Activity */}
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Recent Activity</h3>
                  <div className="space-y-4">
                    {[
                      { type: 'user', message: 'New user registration: Sarah Johnson (Pro Plan)', time: '2 minutes ago' },
                      { type: 'revenue', message: 'Payment received: $299.99 Enterprise subscription', time: '5 minutes ago' },
                      { type: 'content', message: 'Template approved: Modern Dashboard UI Kit', time: '12 minutes ago' },
                      { type: 'system', message: 'System backup completed successfully', time: '1 hour ago' }
                    ].map((activity, index) => (
                      <div key={index} className="flex items-start space-x-3">
                        <div className="p-2 bg-surface-elevated rounded-lg">
                          <div className="text-lg">
                            {activity.type === 'user' ? '👤' : 
                             activity.type === 'revenue' ? '💳' :
                             activity.type === 'content' ? '✅' : '☁️'}
                          </div>
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="text-sm text-primary">{activity.message}</p>
                          <p className="text-xs text-secondary mt-1">{activity.time}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">System Resources</h3>
                  <div className="space-y-4">
                    {[
                      { name: 'CPU Usage', value: 45, color: 'blue' },
                      { name: 'Memory Usage', value: 62, color: 'green' },
                      { name: 'Disk Usage', value: 38, color: 'yellow' },
                      { name: 'Network I/O', value: 23, color: 'purple' }
                    ].map((resource) => (
                      <div key={resource.name}>
                        <div className="flex justify-between items-center mb-2">
                          <span className="text-sm font-medium text-secondary">{resource.name}</span>
                          <span className="text-sm text-primary">{resource.value}%</span>
                        </div>
                        <div className="w-full bg-surface-elevated rounded-full h-2">
                          <div
                            className={`bg-${resource.color}-500 h-2 rounded-full transition-all duration-300`}
                            style={{ width: `${resource.value}%` }}
                          ></div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'users' && (
            <div className="space-y-6">
              <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold text-primary">User Management</h2>
                <div className="flex space-x-3">
                  <button className="btn btn-secondary">📊 Export Users</button>
                  <button className="btn btn-primary">➕ Add User</button>
                </div>
              </div>

              {/* User Statistics */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">User Distribution</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-secondary">Free Plan</span>
                      <span className="font-semibold text-primary">12,391</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-secondary">Pro Plan</span>
                      <span className="font-semibold text-primary">2,134</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-secondary">Enterprise</span>
                      <span className="font-semibold text-primary">1,322</span>
                    </div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Top Users by Revenue</h3>
                  <div className="space-y-3">
                    {[
                      { name: 'Digital Agency Pro', revenue: 12450, templates: 67 },
                      { name: 'Creative Studio XYZ', revenue: 9876, templates: 43 },
                      { name: 'Marketing Experts', revenue: 8765, templates: 52 }
                    ].map((user, index) => (
                      <div key={index} className="flex justify-between items-center">
                        <div>
                          <p className="text-sm font-medium text-primary">{user.name}</p>
                          <p className="text-xs text-secondary">{user.templates} templates</p>
                        </div>
                        <div className="text-right">
                          <p className="text-sm font-bold text-green-600">${user.revenue.toLocaleString()}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Recent Registrations</h3>
                  <div className="space-y-3">
                    {[
                      { name: 'Sarah Johnson', email: 'sarah@example.com', plan: 'Pro' },
                      { name: 'Mike Chen', email: 'mike@example.com', plan: 'Enterprise' },
                      { name: 'Emily Rodriguez', email: 'emily@example.com', plan: 'Free' }
                    ].map((user, index) => (
                      <div key={index} className="border-b border-default pb-3 last:border-b-0">
                        <div className="flex justify-between items-start">
                          <div>
                            <p className="text-sm font-medium text-primary">{user.name}</p>
                            <p className="text-xs text-secondary">{user.email}</p>
                          </div>
                          <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{user.plan}</span>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'revenue' && (
            <div className="space-y-6">
              <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold text-primary">Revenue Analytics</h2>
                <button className="btn btn-primary">📊 Export Report</button>
              </div>

              {/* Revenue Overview */}
              <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Total Revenue</p>
                      <p className="text-2xl font-bold text-primary">$284,568</p>
                    </div>
                    <div className="text-3xl">💵</div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Monthly Recurring</p>
                      <p className="text-2xl font-bold text-primary">$156,780</p>
                    </div>
                    <div className="text-3xl">💳</div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Template Sales</p>
                      <p className="text-2xl font-bold text-primary">$38,331</p>
                    </div>
                    <div className="text-3xl">🛍️</div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Avg Order Value</p>
                      <p className="text-2xl font-bold text-primary">$89.45</p>
                    </div>
                    <div className="text-3xl">📊</div>
                  </div>
                </div>
              </div>

              {/* Recent Transactions */}
              <div className="bg-surface border border-default rounded-xl p-6">
                <h3 className="text-lg font-semibold text-primary mb-4">Recent Transactions</h3>
                <div className="space-y-4">
                  {[
                    { user: 'Sarah Johnson', amount: 29.99, type: 'Pro Subscription', status: 'completed' },
                    { user: 'TechStart Inc.', amount: 299.99, type: 'Enterprise Plan', status: 'completed' },
                    { user: 'Creative Studio', amount: 89.99, type: 'Template Purchase', status: 'pending' }
                  ].map((transaction, index) => (
                    <div key={index} className="flex items-center justify-between p-4 bg-surface-elevated rounded-lg">
                      <div>
                        <p className="font-medium text-primary">{transaction.user}</p>
                        <p className="text-sm text-secondary">{transaction.type}</p>
                      </div>
                      <div className="text-right">
                        <p className="font-semibold text-green-600">${transaction.amount}</p>
                        <span className={`text-xs px-2 py-1 rounded-full ${
                          transaction.status === 'completed' ? 'bg-green-100 text-green-800' :
                          'bg-yellow-100 text-yellow-800'
                        }`}>
                          {transaction.status}
                        </span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {activeTab === 'system' && (
            <div className="space-y-6">
              <h2 className="text-xl font-semibold text-primary">System Monitoring</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">Server Health</h3>
                  <div className="space-y-4">
                    {[
                      { name: 'CPU Usage', value: 45, status: 'good' },
                      { name: 'Memory Usage', value: 62, status: 'warning' },
                      { name: 'Disk Usage', value: 38, status: 'good' },
                      { name: 'Network I/O', value: 23, status: 'good' }
                    ].map((metric) => (
                      <div key={metric.name} className="flex items-center justify-between">
                        <span className="text-sm text-secondary">{metric.name}</span>
                        <div className="flex items-center space-x-2">
                          <span className="text-sm font-medium text-primary">{metric.value}%</span>
                          <div className={`w-2 h-2 rounded-full ${
                            metric.status === 'good' ? 'bg-green-500' : 
                            metric.status === 'warning' ? 'bg-yellow-500' : 'bg-red-500'
                          }`}></div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">API Performance</h3>
                  <div className="space-y-4">
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Total API Calls</span>
                      <span className="text-sm font-medium text-primary">2,847,593</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Avg Response Time</span>
                      <span className="text-sm font-medium text-primary">89ms</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Error Rate</span>
                      <span className="text-sm font-medium text-green-600">0.2%</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Uptime</span>
                      <span className="text-sm font-medium text-green-600">99.9%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'content' && (
            <div className="space-y-6">
              <h2 className="text-xl font-semibold text-primary">Content Management</h2>
              <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6 text-center">
                  <div className="text-3xl mb-2">📄</div>
                  <div className="text-2xl font-bold text-primary mb-1">2,847</div>
                  <div className="text-sm text-secondary">Total Templates</div>
                </div>
                <div className="bg-surface border border-default rounded-xl p-6 text-center">
                  <div className="text-3xl mb-2">⬇️</div>
                  <div className="text-2xl font-bold text-primary mb-1">456K</div>
                  <div className="text-sm text-secondary">Total Downloads</div>
                </div>
                <div className="bg-surface border border-default rounded-xl p-6 text-center">
                  <div className="text-3xl mb-2">⏳</div>
                  <div className="text-2xl font-bold text-primary mb-1">23</div>
                  <div className="text-sm text-secondary">Pending Review</div>
                </div>
                <div className="bg-surface border border-default rounded-xl p-6 text-center">
                  <div className="text-3xl mb-2">💾</div>
                  <div className="text-2xl font-bold text-primary mb-1">1.2TB</div>
                  <div className="text-sm text-secondary">Storage Used</div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'features' && (
            <div className="space-y-6">
              <h2 className="text-xl font-semibold text-primary">Feature Analytics</h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">🤖 AI Generation</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Total Requests</span>
                      <span className="text-sm font-medium text-primary">789K</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Success Rate</span>
                      <span className="text-sm font-medium text-green-600">98.7%</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Avg Time</span>
                      <span className="text-sm font-medium text-primary">2.3s</span>
                    </div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">📱 Social Media</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Connected Accounts</span>
                      <span className="text-sm font-medium text-primary">45.8K</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Scheduled Posts</span>
                      <span className="text-sm font-medium text-primary">23.5K</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Published Posts</span>
                      <span className="text-sm font-medium text-primary">189K</span>
                    </div>
                  </div>
                </div>

                <div className="bg-surface border border-default rounded-xl p-6">
                  <h3 className="text-lg font-semibold text-primary mb-4">📊 Analytics</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Active Reports</span>
                      <span className="text-sm font-medium text-primary">15.7K</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Data Processed</span>
                      <span className="text-sm font-medium text-primary">234GB</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-secondary">Insights Generated</span>
                      <span className="text-sm font-medium text-primary">67.9K</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default SimpleAdminDashboard;