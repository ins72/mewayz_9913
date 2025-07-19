import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import { GoogleLogin } from '@react-oauth/google';
import { useAuth } from '../../contexts/AuthContext';
import { useTheme } from '../../contexts/ThemeContext';
import Button from '../../components/Button';
import { EyeIcon, EyeSlashIcon, SunIcon, MoonIcon } from '@heroicons/react/24/outline';

const LoginPage = () => {
  const { login, isAuthenticated } = useAuth();
  const { theme, toggleTheme } = useTheme();
  const navigate = useNavigate();
  const location = useLocation();
  
  const [formData, setFormData] = useState({
    email: '',
    password: '',
  });
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  const from = location.state?.from?.pathname || '/dashboard';

  useEffect(() => {
    if (isAuthenticated) {
      navigate(from, { replace: true });
    }
  }, [isAuthenticated, navigate, from]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.email) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Email is invalid';
    }
    
    if (!formData.password) {
      newErrors.password = 'Password is required';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) return;

    setLoading(true);
    try {
      console.log('LoginPage: Starting login for:', formData.email);
      const result = await login(formData);
      console.log('LoginPage: Login result:', result);
      
      if (result.success) {
        console.log('LoginPage: Login successful, redirecting to:', from);
        // Use a small delay to ensure state is updated
        setTimeout(() => {
          navigate(from, { replace: true });
        }, 100);
      } else {
        console.error('LoginPage: Login failed:', result);
        setErrors({ general: result.message || result.error || 'Login failed' });
      }
    } catch (error) {
      console.error('LoginPage: Login error:', error);
      setErrors({ general: 'Login failed. Please try again.' });
    } finally {
      setLoading(false);
    }
  };

  // Google OAuth Success Handler
  const handleGoogleSuccess = async (credentialResponse) => {
    setLoading(true);
    try {
      console.log('Google OAuth response:', credentialResponse);
      
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/auth/google/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          credential: credentialResponse.credential,
        }),
      });

      const result = await response.json();
      console.log('Google OAuth verify result:', result);

      if (result.success && result.token) {
        // Store the token
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        
        // Update auth context (this should trigger a re-render)
        console.log('Google OAuth login successful, navigating to:', from);
        setTimeout(() => {
          navigate(from, { replace: true });
        }, 100);
      } else {
        console.error('Google OAuth failed:', result);
        setErrors({ general: result.message || 'Google login failed' });
      }
    } catch (error) {
      console.error('Google OAuth error:', error);
      setErrors({ general: 'Google login failed. Please try again.' });
    } finally {
      setLoading(false);
    }
  };

  // Google OAuth Error Handler
  const handleGoogleError = (error) => {
    console.error('Google OAuth error:', error);
    setErrors({ general: 'Google login failed. Please try again.' });
  };

  return (
    <div className="min-h-screen bg-gradient-hero flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      {/* Theme toggle */}
      <div className="absolute top-4 right-4">
        <button
          onClick={toggleTheme}
          className="p-2 text-secondary hover:text-primary transition-colors focus-ring rounded-lg"
        >
          {theme === 'dark' ? <SunIcon className="w-5 h-5" /> : <MoonIcon className="w-5 h-5" />}
        </button>
      </div>

      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="sm:mx-auto sm:w-full sm:max-w-md"
      >
        <div className="text-center">
          <Link to="/" className="text-3xl font-bold text-accent-primary text-display">
            Mewayz
          </Link>
          <h2 className="mt-6 text-3xl font-bold text-primary text-heading">
            Welcome back
          </h2>
          <p className="mt-2 text-sm text-secondary text-body">
            Don't have an account?{' '}
            <Link to="/register" className="font-medium text-accent-primary hover:opacity-80 transition-opacity">
              Sign up here
            </Link>
          </p>
        </div>
      </motion.div>

      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="mt-8 sm:mx-auto sm:w-full sm:max-w-md"
      >
        <div className="bg-surface-elevated py-8 px-4 rounded-lg sm:px-10">
          <form className="space-y-6" onSubmit={handleSubmit}>
            {errors.general && (
              <div className="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">
                {errors.general}
              </div>
            )}

            <div>
              <label htmlFor="email" className="block text-sm font-medium text-primary text-body">
                Email address
              </label>
              <div className="mt-1">
                <input
                  id="email"
                  name="email"
                  type="email"
                  autoComplete="email"
                  value={formData.email}
                  onChange={handleChange}
                  className={`appearance-none block w-full px-3 py-2 input rounded-md focus-ring sm:text-sm transition-all ${
                    errors.email 
                      ? 'border-accent-danger' 
                      : ''
                  }`}
                  placeholder="Enter your email"
                />
                {errors.email && (
                  <p className="mt-1 text-sm status-error text-caption">{errors.email}</p>
                )}
              </div>
            </div>

            <div>
              <label htmlFor="password" className="block text-sm font-medium text-primary text-body">
                Password
              </label>
              <div className="mt-1 relative">
                <input
                  id="password"
                  name="password"
                  type={showPassword ? 'text' : 'password'}
                  autoComplete="current-password"
                  value={formData.password}
                  onChange={handleChange}
                  className={`appearance-none block w-full px-3 py-2 input rounded-md focus-ring sm:text-sm transition-all ${
                    errors.password 
                      ? 'border-accent-danger' 
                      : ''
                  }`}
                  placeholder="Enter your password"
                />
                <button
                  type="button"
                  className="absolute inset-y-0 right-0 pr-3 flex items-center"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? (
                    <EyeSlashIcon className="h-5 w-5 text-secondary" />
                  ) : (
                    <EyeIcon className="h-5 w-5 text-secondary" />
                  )}
                </button>
                {errors.password && (
                  <p className="mt-1 text-sm status-error text-caption">{errors.password}</p>
                )}
              </div>
            </div>

            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <input
                  id="remember-me"
                  name="remember-me"
                  type="checkbox"
                  className="h-4 w-4 text-accent-primary focus-ring border-default rounded"
                />
                <label htmlFor="remember-me" className="ml-2 block text-sm text-primary">
                  Remember me
                </label>
              </div>

              <div className="text-sm">
                <Link
                  to="/forgot-password"
                  className="font-medium text-accent-primary hover:opacity-80 transition-opacity"
                >
                  Forgot your password?
                </Link>
              </div>
            </div>

            <div>
              <Button
                type="submit"
                fullWidth
                loading={loading}
                disabled={loading}
              >
                Sign in
              </Button>
            </div>
          </form>

          <div className="mt-6">
            <div className="relative">
              <div className="absolute inset-0 flex items-center">
                <div className="w-full border-t border-default" />
              </div>
              <div className="relative flex justify-center text-sm">
                <span className="px-2 bg-surface text-secondary">
                  Or continue with
                </span>
              </div>
            </div>

            <div className="mt-6 grid grid-cols-2 gap-3">
              <button className="w-full inline-flex justify-center items-center py-2 px-4 border border-default rounded-md shadow-sm bg-surface text-sm font-medium text-secondary hover-surface transition-colors focus-ring">
                <svg className="w-5 h-5 mr-2" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                  <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Google</span>
              </button>
              <button className="w-full inline-flex justify-center items-center py-2 px-4 border border-default rounded-md shadow-sm bg-surface text-sm font-medium text-secondary hover-surface transition-colors focus-ring">
                <svg className="w-5 h-5 mr-2" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                </svg>
                <span>Apple</span>
              </button>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default LoginPage;