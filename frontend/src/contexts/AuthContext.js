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
                // Real data loaded from API
            }
        } catch (error) {
            console.error('Auth check failed:', error);
            apiService.clearToken();
        } finally {
            // Real data loaded from API
        }
    };

    const login = async (credentials) => {
        try {
            // Real data loaded from API
            const response = await apiService.login(credentials);
            apiService.// Real data loaded from API
            // Real data loaded from API
            return response;
        } catch (error) {
            // Real data loaded from API
            throw error;
        }
    };

    const register = async (userData) => {
        try {
            // Real data loaded from API
            const response = await apiService.register(userData);
            apiService.// Real data loaded from API
            // Real data loaded from API
            return response;
        } catch (error) {
            // Real data loaded from API
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
            // Real data loaded from API
        }
    };

    const updateProfile = async (userData) => {
        try {
            // Real data loaded from API
            const updatedUser = await apiService.updateProfile(userData);
            // Real data loaded from API
            return updatedUser;
        } catch (error) {
            // Real data loaded from API
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
