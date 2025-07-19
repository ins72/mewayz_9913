import React, { useState, useEffect } from 'react';
import axios from 'axios';
import './App.css';

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL || '/api';

function App() {
  const [healthStatus, setHealthStatus] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Determine the correct backend URL based on environment
  const getBackendUrl = () => {
    // If we have a custom backend URL from env, use it
    if (process.env.REACT_APP_BACKEND_URL && process.env.REACT_APP_BACKEND_URL !== '/api') {
      return process.env.REACT_APP_BACKEND_URL;
    }
    
    // For production or emergent preview, use relative URL
    if (window.location.hostname !== 'localhost') {
      return '/api';
    }
    
    // For local development, use proxy
    return '/api';
  };

  const backendUrl = getBackendUrl();

  useEffect(() => {
    checkHealth();
  }, []);

  const checkHealth = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${backendUrl}/health`);
      setHealthStatus(response.data);
      setError(null);
    } catch (err) {
      setError(`Failed to connect to backend: ${err.message}`);
      setHealthStatus(null);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-100">
      <div className="container mx-auto px-4 py-8">
        <div className="max-w-4xl mx-auto">
          {/* Header */}
          <div className="text-center mb-8">
            <h1 className="text-4xl font-bold text-gray-900 mb-4">
              🚀 Mewayz Platform
            </h1>
            <p className="text-xl text-gray-600">
              Complete Creator Economy Platform - Laravel Backend Ready!
            </p>
          </div>

          {/* Status Card */}
          <div className="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 className="text-2xl font-semibold mb-4 flex items-center">
              <span className="mr-2">⚡</span>
              System Status
            </h2>
            
            {loading && (
              <div className="flex items-center text-blue-600">
                <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                Checking system health...
              </div>
            )}

            {error && (
              <div className="bg-red-50 border border-red-200 rounded-md p-4">
                <div className="flex items-center">
                  <span className="text-red-500 mr-2">❌</span>
                  <span className="text-red-700">{error}</span>
                </div>
                <button 
                  onClick={checkHealth}
                  className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                >
                  Retry Connection
                </button>
              </div>
            )}

            {healthStatus && (
              <div className="space-y-4">
                <div className="flex items-center">
                  <span className="text-green-500 mr-2">✅</span>
                  <span className="text-green-700 font-medium">
                    System Status: {healthStatus.data?.status || 'Unknown'}
                  </span>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {healthStatus.data?.services && Object.entries(healthStatus.data.services).map(([service, status]) => (
                    <div key={service} className="bg-gray-50 p-3 rounded-md">
                      <div className="flex items-center justify-between">
                        <span className="font-medium capitalize">{service}</span>
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                          status === 'healthy' 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800'
                        }`}>
                          {status}
                        </span>
                      </div>
                    </div>
                  ))}
                </div>

                <div className="bg-blue-50 p-4 rounded-md">
                  <h3 className="font-semibold mb-2">Platform Information</h3>
                  <div className="text-sm space-y-1">
                    <div><strong>Version:</strong> {healthStatus.data?.version}</div>
                    <div><strong>Environment:</strong> {healthStatus.data?.environment}</div>
                    <div><strong>Backend URL:</strong> {backendUrl}/health</div>
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* Features Overview */}
          <div className="bg-white rounded-lg shadow-lg p-6">
            <h2 className="text-2xl font-semibold mb-4 flex items-center">
              <span className="mr-2">🎯</span>
              Available Features
            </h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {healthStatus?.data?.features && Object.entries(healthStatus.data.features).map(([feature, enabled]) => (
                <div key={feature} className={`p-4 rounded-md border ${
                  enabled 
                    ? 'bg-green-50 border-green-200' 
                    : 'bg-gray-50 border-gray-200'
                }`}>
                  <div className="flex items-center justify-between">
                    <span className="font-medium capitalize">
                      {feature.replace(/_/g, ' ')}
                    </span>
                    <span className={`text-xs px-2 py-1 rounded-full ${
                      enabled 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-gray-100 text-gray-600'
                    }`}>
                      {enabled ? 'Active' : 'Inactive'}
                    </span>
                  </div>
                </div>
              ))}
            </div>

            {!healthStatus && (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {[
                  'Social Media Management',
                  'E-commerce Platform', 
                  'Course Creation',
                  'CRM System',
                  'Analytics & BI',
                  'Payment Processing',
                  'Bio Sites',
                  'Email Marketing',
                  'Advanced Booking'
                ].map((feature) => (
                  <div key={feature} className="p-4 rounded-md border border-gray-200 bg-gray-50">
                    <span className="font-medium">{feature}</span>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Footer */}
          <div className="text-center mt-8 text-gray-500">
            <p>🏆 Your Mewayz platform is now ready for preview and deployment!</p>
            <p className="text-sm mt-2">
              Backend API: <code className="bg-gray-200 px-2 py-1 rounded">{BACKEND_URL}/health</code>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;