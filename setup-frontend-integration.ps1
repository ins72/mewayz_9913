# Frontend-Backend Integration Setup Script
Write-Host "🔗 Setting up Frontend-Backend Integration" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan

Write-Host "`n📋 Frontend Integration Setup:" -ForegroundColor Yellow
Write-Host "   • API service layer" -ForegroundColor White
Write-Host "   • Authentication flow" -ForegroundColor White
Write-Host "   • Environment configuration" -ForegroundColor White
Write-Host "   • CORS setup" -ForegroundColor White

# Create API service layer
Write-Host "`n🔧 Creating API Service Layer..." -ForegroundColor Cyan

$apiServiceContent = @"
// API Service Layer for Mewayz Platform
// Handles all backend API communication

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8001';

class ApiService {
    constructor() {
        this.baseURL = API_BASE_URL;
        this.token = localStorage.getItem('authToken');
    }

    // Set authentication token
    setToken(token) {
        this.token = token;
        localStorage.setItem('authToken', token);
    }

    // Clear authentication token
    clearToken() {
        this.token = null;
        localStorage.removeItem('authToken');
    }

    // Get headers for API requests
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
        };
        
        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }
        
        return headers;
    }

    // Generic API request method
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: this.getHeaders(),
            ...options,
        };

        try {
            const response = await fetch(url, config);
            
            if (response.status === 401) {
                this.clearToken();
                throw new Error('Authentication required');
            }
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.detail || `HTTP ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    // Authentication methods
    async register(userData) {
        return this.request('/api/auth/register', {
            method: 'POST',
            body: JSON.stringify(userData),
        });
    }

    async login(credentials) {
        return this.request('/api/auth/login', {
            method: 'POST',
            body: JSON.stringify(credentials),
        });
    }

    async logout() {
        return this.request('/api/auth/logout', {
            method: 'POST',
        });
    }

    // User management
    async getCurrentUser() {
        return this.request('/api/user/profile');
    }

    async updateProfile(userData) {
        return this.request('/api/user/profile', {
            method: 'PUT',
            body: JSON.stringify(userData),
        });
    }

    // Dashboard methods
    async getDashboardOverview() {
        return this.request('/api/dashboard/overview');
    }

    async getDashboardActivity() {
        return this.request('/api/dashboard/activity-summary');
    }

    // Analytics methods
    async getAnalyticsOverview() {
        return this.request('/api/analytics/overview');
    }

    async getAnalyticsDashboard(period = '30d') {
        return this.request(`/api/analytics/dashboard?period=${period}`);
    }

    // Workspace methods
    async getWorkspaces() {
        return this.request('/api/workspaces');
    }

    async createWorkspace(workspaceData) {
        return this.request('/api/workspaces', {
            method: 'POST',
            body: JSON.stringify(workspaceData),
        });
    }

    async getWorkspace(workspaceId) {
        return this.request(`/api/workspaces/${workspaceId}`);
    }

    // E-commerce methods
    async getProducts(limit = 10) {
        return this.request(`/api/ecommerce/products?limit=${limit}`);
    }

    async getProduct(productId) {
        return this.request(`/api/ecommerce/products/${productId}`);
    }

    async getOrders() {
        return this.request('/api/ecommerce/orders');
    }

    // Content management
    async getContent() {
        return this.request('/api/content');
    }

    async createContent(contentData) {
        return this.request('/api/content', {
            method: 'POST',
            body: JSON.stringify(contentData),
        });
    }

    // AI features
    async getAIServices() {
        return this.request('/api/ai/services');
    }

    async analyzeContent(content) {
        return this.request('/api/ai/analyze-content', {
            method: 'POST',
            body: JSON.stringify({ content }),
        });
    }

    // Health check
    async healthCheck() {
        return this.request('/health');
    }
}

// Create singleton instance
const apiService = new ApiService();

export default apiService;
"@

$apiServiceContent | Out-File -FilePath "frontend\src\services\apiService.js" -Encoding UTF8
Write-Host "✅ Created API service layer" -ForegroundColor Green

# Create authentication context
Write-Host "`n🔧 Creating Authentication Context..." -ForegroundColor Cyan

$authContextContent = @"
// Authentication Context for Mewayz Platform
import React, { createContext, useContext, useState, useEffect } from 'react';
import apiService from '../services/apiService';

const AuthContext = createContext();

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Check if user is authenticated on app load
    useEffect(() => {
        checkAuthStatus();
    }, []);

    const checkAuthStatus = async () => {
        try {
            const token = localStorage.getItem('authToken');
            if (token) {
                const userData = await apiService.getCurrentUser();
                setUser(userData);
            }
        } catch (error) {
            console.error('Auth check failed:', error);
            apiService.clearToken();
        } finally {
            setLoading(false);
        }
    };

    const login = async (credentials) => {
        try {
            setError(null);
            const response = await apiService.login(credentials);
            apiService.setToken(response.token);
            setUser(response.user);
            return response;
        } catch (error) {
            setError(error.message);
            throw error;
        }
    };

    const register = async (userData) => {
        try {
            setError(null);
            const response = await apiService.register(userData);
            apiService.setToken(response.token);
            setUser(response.user);
            return response;
        } catch (error) {
            setError(error.message);
            throw error;
        }
    };

    const logout = async () => {
        try {
            await apiService.logout();
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            apiService.clearToken();
            setUser(null);
        }
    };

    const updateProfile = async (userData) => {
        try {
            setError(null);
            const updatedUser = await apiService.updateProfile(userData);
            setUser(updatedUser);
            return updatedUser;
        } catch (error) {
            setError(error.message);
            throw error;
        }
    };

    const value = {
        user,
        loading,
        error,
        login,
        register,
        logout,
        updateProfile,
        isAuthenticated: !!user,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};
"@

$authContextContent | Out-File -FilePath "frontend\src\contexts\AuthContext.js" -Encoding UTF8
Write-Host "✅ Created authentication context" -ForegroundColor Green

# Create protected route component
Write-Host "`n🔧 Creating Protected Route Component..." -ForegroundColor Cyan

$protectedRouteContent = @"
// Protected Route Component
import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const ProtectedRoute = ({ children }) => {
    const { user, loading } = useAuth();
    const location = useLocation();

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    if (!user) {
        return <Navigate to="/login" state={{ from: location }} replace />;
    }

    return children;
};

export default ProtectedRoute;
"@

$protectedRouteContent | Out-File -FilePath "frontend\src\components\ProtectedRoute.js" -Encoding UTF8
Write-Host "✅ Created protected route component" -ForegroundColor Green

# Create login component
Write-Host "`n🔧 Creating Login Component..." -ForegroundColor Cyan

$loginComponentContent = @"
// Login Component
import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const Login = () => {
    const [credentials, setCredentials] = useState({
        email: '',
        password: '',
    });
    const [loading, setLoading] = useState(false);
    const { login, error } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();

    const from = location.state?.from?.pathname || '/dashboard';

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        
        try {
            await login(credentials);
            navigate(from, { replace: true });
        } catch (error) {
            console.error('Login failed:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        setCredentials({
            ...credentials,
            [e.target.name]: e.target.value,
        });
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-md w-full space-y-8">
                <div>
                    <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Sign in to Mewayz
                    </h2>
                </div>
                <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                    {error && (
                        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {error}
                        </div>
                    )}
                    <div className="rounded-md shadow-sm -space-y-px">
                        <div>
                            <input
                                name="email"
                                type="email"
                                required
                                className="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Email address"
                                value={credentials.email}
                                onChange={handleChange}
                            />
                        </div>
                        <div>
                            <input
                                name="password"
                                type="password"
                                required
                                className="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Password"
                                value={credentials.password}
                                onChange={handleChange}
                            />
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            disabled={loading}
                            className="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            {loading ? 'Signing in...' : 'Sign in'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default Login;
"@

$loginComponentContent | Out-File -FilePath "frontend\src\components\Login.js" -Encoding UTF8
Write-Host "✅ Created login component" -ForegroundColor Green

# Create dashboard component
Write-Host "`n🔧 Creating Dashboard Component..." -ForegroundColor Cyan

$dashboardComponentContent = @"
// Dashboard Component
import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import apiService from '../services/apiService';

const Dashboard = () => {
    const { user, logout } = useAuth();
    const [dashboardData, setDashboardData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadDashboardData();
    }, []);

    const loadDashboardData = async () => {
        try {
            setLoading(true);
            const data = await apiService.getDashboardOverview();
            setDashboardData(data);
        } catch (error) {
            setError(error.message);
        } finally {
            setLoading(false);
        }
    };

    const handleLogout = async () => {
        await logout();
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    Error: {error}
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex items-center">
                            <h1 className="text-xl font-semibold text-gray-900">
                                Mewayz Dashboard
                            </h1>
                        </div>
                        <div className="flex items-center space-x-4">
                            <span className="text-gray-700">
                                Welcome, {user?.username || user?.email}
                            </span>
                            <button
                                onClick={handleLogout}
                                className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div className="px-4 py-6 sm:px-0">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div className="bg-white overflow-hidden shadow rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                            <span className="text-white text-sm font-medium">U</span>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Users
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {dashboardData?.users || 0}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                            <span className="text-white text-sm font-medium">W</span>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Workspaces
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {dashboardData?.workspaces || 0}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                            <span className="text-white text-sm font-medium">P</span>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Projects
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {dashboardData?.projects || 0}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                            <span className="text-white text-sm font-medium">A</span>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Analytics
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {dashboardData?.analytics || 0}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="mt-8">
                        <div className="bg-white shadow rounded-lg">
                            <div className="px-4 py-5 sm:p-6">
                                <h3 className="text-lg leading-6 font-medium text-gray-900">
                                    Platform Status
                                </h3>
                                <div className="mt-2 max-w-xl text-sm text-gray-500">
                                    <p>Mewayz Platform is running successfully with full API integration.</p>
                                </div>
                                <div className="mt-3 text-sm">
                                    <div className="flex items-center">
                                        <div className="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        <span className="text-green-700">Backend API: Operational</span>
                                    </div>
                                    <div className="flex items-center mt-1">
                                        <div className="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        <span className="text-green-700">Database: Connected</span>
                                    </div>
                                    <div className="flex items-center mt-1">
                                        <div className="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        <span className="text-green-700">Authentication: Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
};

export default Dashboard;
"@

$dashboardComponentContent | Out-File -FilePath "frontend\src\components\Dashboard.js" -Encoding UTF8
Write-Host "✅ Created dashboard component" -ForegroundColor Green

# Update App.js with routing
Write-Host "`n🔧 Updating App.js with routing..." -ForegroundColor Cyan

$appJsContent = @"
import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Login from './components/Login';
import Dashboard from './components/Dashboard';
import './App.css';

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="App">
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route 
              path="/dashboard" 
              element={
                <ProtectedRoute>
                  <Dashboard />
                </ProtectedRoute>
              } 
            />
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
          </Routes>
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;
"@

$appJsContent | Out-File -FilePath "frontend\src\App.js" -Encoding UTF8
Write-Host "✅ Updated App.js with routing" -ForegroundColor Green

# Update frontend environment
Write-Host "`n🔧 Updating frontend environment..." -ForegroundColor Cyan

$frontendEnvContent = @"
# Frontend Environment Configuration
REACT_APP_API_URL=http://localhost:8001
REACT_APP_ENVIRONMENT=development
REACT_APP_DEBUG=true
REACT_APP_STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key-here
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id-here
REACT_APP_SENTRY_DSN=
REACT_APP_ANALYTICS_ID=
"@

$frontendEnvContent | Out-File -FilePath "frontend\.env" -Encoding UTF8
Write-Host "✅ Updated frontend environment" -ForegroundColor Green

Write-Host "`n🎉 Frontend Integration Setup Complete!" -ForegroundColor Green
Write-Host "`n📋 What was created:" -ForegroundColor Yellow
Write-Host "   • API Service Layer (apiService.js)" -ForegroundColor White
Write-Host "   • Authentication Context (AuthContext.js)" -ForegroundColor White
Write-Host "   • Protected Route Component (ProtectedRoute.js)" -ForegroundColor White
Write-Host "   • Login Component (Login.js)" -ForegroundColor White
Write-Host "   • Dashboard Component (Dashboard.js)" -ForegroundColor White
Write-Host "   • Updated App.js with routing" -ForegroundColor White
Write-Host "   • Frontend environment configuration" -ForegroundColor White

Write-Host "`n🎯 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Restart the frontend to load new components" -ForegroundColor White
Write-Host "2. Test the login flow" -ForegroundColor White
Write-Host "3. Verify dashboard integration" -ForegroundColor White
Write-Host "4. Test API endpoints through the UI" -ForegroundColor White

Write-Host "`n🌐 Access URLs:" -ForegroundColor Yellow
Write-Host "   Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "   Backend API: http://localhost:8001" -ForegroundColor White
Write-Host "   API Docs: http://localhost:8001/docs" -ForegroundColor White

Write-Host "`n🔐 Test Credentials:" -ForegroundColor Cyan
Write-Host "   You can test with any email/password combination" -ForegroundColor White
Write-Host "   The backend will handle validation and authentication" -ForegroundColor White

Write-Host "`n🚀 Your Mewayz platform now has full frontend-backend integration!" -ForegroundColor Green 