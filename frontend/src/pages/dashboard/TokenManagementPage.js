import React, { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import api from '../../services/api';
import { 
  CurrencyDollarIcon, 
  BoltIcon, 
  ChartBarIcon, 
  ShoppingCartIcon,
  CogIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon,
  UserGroupIcon
} from '@heroicons/react/24/outline';
import LoadingSpinner from '../../components/LoadingSpinner';

const TokenManagementPage = () => {
  const { user } = useAuth();
  const [loading, setLoading] = useState(true);
  const [tokenData, setTokenData] = useState(null);
  const [packages, setPackages] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [selectedPackage, setSelectedPackage] = useState(null);
  const [showPurchaseModal, setShowPurchaseModal] = useState(false);
  const [showSettingsModal, setShowSettingsModal] = useState(false);
  const [workspaceId, setWorkspaceId] = useState(null);

  useEffect(() => {
    loadTokenData();
    loadPackages();
  }, []);

  const loadTokenData = async () => {
    try {
      setLoading(true);
      
      // First get workspace ID
      const workspacesResponse = await api.get('/workspaces');
      if (workspacesResponse.data.success && workspacesResponse.data.data.workspaces.length > 0) {
        const workspace = workspacesResponse.data.data.workspaces[0];
        setWorkspaceId(workspace.id);
        
        // Get token data
        const tokenResponse = await api.get(`/tokens/workspace/${workspace.id}`);
        if (tokenResponse.data.success) {
          setTokenData(tokenResponse.data.data);
        }
        
        // Get analytics
        try {
          const analyticsResponse = await api.get(`/tokens/analytics/${workspace.id}`);
          if (analyticsResponse.data.success) {
            setAnalytics(analyticsResponse.data.data);
          }
        } catch (error) {
          console.warn('Analytics not available:', error);
        }
      }
    } catch (error) {
      console.error('Error loading token data:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadPackages = async () => {
    try {
      const response = await api.get('/tokens/packages');
      if (response.data.success) {
        setPackages(response.data.data.packages);
      }
    } catch (error) {
      console.error('Error loading packages:', error);
    }
  };

  const handlePurchase = async (packageId) => {
    try {
      const purchaseData = {
        workspace_id: workspaceId,
        package_id: packageId,
        payment_method_id: 'pm_card_visa' // Test payment method
      };
      
      const response = await api.post('/tokens/purchase', purchaseData);
      if (response.data.success) {
        alert('Tokens purchased successfully!');
        loadTokenData(); // Refresh data
        setShowPurchaseModal(false);
      } else {
        alert('Purchase failed: ' + (response.data.error || 'Unknown error'));
      }
    } catch (error) {
      console.error('Purchase error:', error);
      alert('Purchase failed. Please try again.');
    }
  };

  const TokenPackageCard = ({ pkg }) => (
    <div className={`bg-card rounded-lg p-6 border ${pkg.is_popular ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-border'}`}>
      {pkg.is_popular && (
        <div className="bg-blue-500 text-white text-sm font-medium px-3 py-1 rounded-full inline-block mb-4">
          Most Popular
        </div>
      )}
      <div className="text-2xl font-bold text-foreground mb-2">{pkg.name}</div>
      <div className="text-muted mb-4">{pkg.description}</div>
      <div className="flex items-baseline mb-6">
        <span className="text-3xl font-bold text-foreground">${pkg.price}</span>
        <span className="text-muted ml-2">USD</span>
      </div>
      <div className="space-y-3 mb-6">
        <div className="flex items-center text-foreground">
          <BoltIcon className="w-5 h-5 mr-2 text-blue-500" />
          <span>{pkg.tokens.toLocaleString()} tokens</span>
        </div>
        {pkg.bonus_tokens > 0 && (
          <div className="flex items-center text-green-600">
            <CheckCircleIcon className="w-5 h-5 mr-2" />
            <span>+{pkg.bonus_tokens.toLocaleString()} bonus tokens</span>
          </div>
        )}
        <div className="flex items-center text-muted">
          <CurrencyDollarIcon className="w-5 h-5 mr-2" />
          <span>${pkg.per_token_price}/token</span>
        </div>
      </div>
      <button
        onClick={() => {
          setSelectedPackage(pkg);
          setShowPurchaseModal(true);
        }}
        className="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
      >
        Purchase Package
      </button>
    </div>
  );

  const FeatureCostCard = ({ feature, cost }) => (
    <div className="bg-card rounded-lg p-4 border border-border">
      <div className="flex items-center justify-between">
        <div>
          <h4 className="font-medium text-foreground capitalize">
            {feature.replace('_', ' ')}
          </h4>
          <p className="text-sm text-muted mt-1">
            AI-powered {feature.replace('_', ' ')} service
          </p>
        </div>
        <div className="text-right">
          <div className="text-2xl font-bold text-foreground">{cost}</div>
          <div className="text-sm text-muted">tokens</div>
        </div>
      </div>
    </div>
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <LoadingSpinner />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-foreground">Token Management</h1>
          <p className="text-muted mt-1">Manage AI tokens for your workspace</p>
        </div>
        <div className="flex space-x-3">
          <button
            onClick={() => setShowSettingsModal(true)}
            className="bg-secondary text-secondary-foreground px-4 py-2 rounded-lg hover:bg-secondary/80 transition-colors flex items-center"
          >
            <CogIcon className="w-4 h-4 mr-2" />
            Settings
          </button>
        </div>
      </div>

      {tokenData && (
        <>
          {/* Token Balance Overview */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-card rounded-lg p-6 border border-border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-muted text-sm">Current Balance</p>
                  <p className="text-2xl font-bold text-foreground mt-1">
                    {tokenData.balance?.toLocaleString() || '0'}
                  </p>
                </div>
                <BoltIcon className="w-8 h-8 text-blue-500" />
              </div>
              <p className="text-xs text-muted mt-2">Purchased tokens available</p>
            </div>

            <div className="bg-card rounded-lg p-6 border border-border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-muted text-sm">Monthly Allowance</p>
                  <p className="text-2xl font-bold text-foreground mt-1">
                    {tokenData.allowance_remaining?.toLocaleString() || '0'} / {tokenData.monthly_allowance?.toLocaleString() || '0'}
                  </p>
                </div>
                <ClockIcon className="w-8 h-8 text-green-500" />
              </div>
              <p className="text-xs text-muted mt-2">Free monthly tokens remaining</p>
            </div>

            <div className="bg-card rounded-lg p-6 border border-border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-muted text-sm">Total Purchased</p>
                  <p className="text-2xl font-bold text-foreground mt-1">
                    {tokenData.total_purchased?.toLocaleString() || '0'}
                  </p>
                </div>
                <ShoppingCartIcon className="w-8 h-8 text-purple-500" />
              </div>
              <p className="text-xs text-muted mt-2">Lifetime token purchases</p>
            </div>

            <div className="bg-card rounded-lg p-6 border border-border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-muted text-sm">Total Used</p>
                  <p className="text-2xl font-bold text-foreground mt-1">
                    {tokenData.total_used?.toLocaleString() || '0'}
                  </p>
                </div>
                <ChartBarIcon className="w-8 h-8 text-red-500" />
              </div>
              <p className="text-xs text-muted mt-2">Lifetime token consumption</p>
            </div>
          </div>

          {/* Low Balance Warning */}
          {tokenData.balance < 50 && (
            <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
              <div className="flex items-start">
                <ExclamationTriangleIcon className="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5" />
                <div>
                  <h3 className="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                    Low Token Balance
                  </h3>
                  <p className="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    Your token balance is running low. Consider purchasing more tokens to continue using AI features.
                  </p>
                </div>
              </div>
            </div>
          )}

          {/* Feature Costs */}
          <div className="bg-card rounded-lg p-6 border border-border">
            <h2 className="text-lg font-semibold text-foreground mb-4">AI Feature Costs</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {Object.entries(tokenData.feature_costs || {}).map(([feature, cost]) => (
                <FeatureCostCard key={feature} feature={feature} cost={cost} />
              ))}
            </div>
          </div>

          {/* Recent Transactions */}
          {tokenData.recent_transactions && tokenData.recent_transactions.length > 0 && (
            <div className="bg-card rounded-lg p-6 border border-border">
              <h2 className="text-lg font-semibold text-foreground mb-4">Recent Transactions</h2>
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b border-border">
                      <th className="text-left py-2 text-muted">Type</th>
                      <th className="text-left py-2 text-muted">Tokens</th>
                      <th className="text-left py-2 text-muted">Feature</th>
                      <th className="text-left py-2 text-muted">Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    {tokenData.recent_transactions.map((tx) => (
                      <tr key={tx.id} className="border-b border-border">
                        <td className="py-2">
                          <span className={`inline-block px-2 py-1 rounded text-xs ${
                            tx.type === 'purchase' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                            tx.type === 'usage' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' :
                            'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
                          }`}>
                            {tx.type}
                          </span>
                        </td>
                        <td className="py-2 text-foreground">
                          <span className={tx.tokens < 0 ? 'text-red-600' : 'text-green-600'}>
                            {tx.tokens > 0 ? '+' : ''}{tx.tokens}
                          </span>
                        </td>
                        <td className="py-2 text-muted">
                          {tx.feature ? tx.feature.replace('_', ' ') : '-'}
                        </td>
                        <td className="py-2 text-muted">
                          {new Date(tx.created_at).toLocaleDateString()}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </>
      )}

      {/* Token Packages */}
      {packages.length > 0 && (
        <div className="bg-card rounded-lg p-6 border border-border">
          <h2 className="text-lg font-semibold text-foreground mb-6">Purchase Token Packages</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {packages.map((pkg) => (
              <TokenPackageCard key={pkg.id} pkg={pkg} />
            ))}
          </div>
        </div>
      )}

      {/* Analytics */}
      {analytics && (
        <div className="bg-card rounded-lg p-6 border border-border">
          <h2 className="text-lg font-semibold text-foreground mb-4">Usage Analytics</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="text-center">
              <div className="text-2xl font-bold text-foreground">
                {analytics.efficiency_metrics?.avg_tokens_per_use || 0}
              </div>
              <div className="text-sm text-muted">Avg Tokens/Use</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-foreground">
                {analytics.efficiency_metrics?.most_used_feature || 'None'}
              </div>
              <div className="text-sm text-muted">Most Used Feature</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-foreground">
                ${analytics.efficiency_metrics?.cost_per_month || 0}
              </div>
              <div className="text-sm text-muted">Est. Monthly Cost</div>
            </div>
          </div>
        </div>
      )}

      {/* Purchase Modal */}
      {showPurchaseModal && selectedPackage && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-card rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-semibold text-foreground mb-4">
              Purchase {selectedPackage.name}
            </h3>
            <div className="space-y-3 mb-6">
              <div className="flex justify-between">
                <span className="text-muted">Tokens:</span>
                <span className="text-foreground">{selectedPackage.tokens.toLocaleString()}</span>
              </div>
              {selectedPackage.bonus_tokens > 0 && (
                <div className="flex justify-between">
                  <span className="text-muted">Bonus Tokens:</span>
                  <span className="text-green-600">+{selectedPackage.bonus_tokens.toLocaleString()}</span>
                </div>
              )}
              <div className="flex justify-between border-t border-border pt-3">
                <span className="font-medium text-foreground">Total:</span>
                <span className="font-bold text-foreground">${selectedPackage.price}</span>
              </div>
            </div>
            <div className="flex space-x-3">
              <button
                onClick={() => setShowPurchaseModal(false)}
                className="flex-1 bg-secondary text-secondary-foreground py-2 px-4 rounded-lg hover:bg-secondary/80 transition-colors"
              >
                Cancel
              </button>
              <button
                onClick={() => handlePurchase(selectedPackage.id)}
                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors"
              >
                Purchase
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default TokenManagementPage;