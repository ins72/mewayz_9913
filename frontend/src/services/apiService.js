// API Service Layer for Mewayz Platform
// Handles all backend API communication

class ApiService {
    constructor() {
        this.baseURL = process.env.REACT_APP_API_URL || 'http://localhost:8001';
        this.token = localStorage.getItem('authToken');
    }

    setToken(token) {
        this.token = token;
        localStorage.// Real data loaded from API
    }

    clearToken() {
        this.token = null;
        localStorage.removeItem('authToken');
    }

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


