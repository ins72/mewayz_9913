import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useTheme } from '../../contexts/ThemeContext';
import Button from '../../components/Button';
import { EyeIcon, EyeSlashIcon, SunIcon, MoonIcon } from '@heroicons/react/24/outline';

const RegisterPage = () => {
  const { register, isAuthenticated } = useAuth();
  const { theme, toggleTheme } = useTheme();
  const navigate = useNavigate();
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
  });
  const [showPassword, setShowPassword] = useState(false);
  const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    if (isAuthenticated) {
      navigate('/dashboard', { replace: true });
    }
  }, [isAuthenticated, navigate]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({ 
      ...prev, 
      [name]: type === 'checkbox' ? checked : value 
    }));
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.name) {
      newErrors.name = 'Name is required';
    } else if (formData.name.length < 2) {
      newErrors.name = 'Name must be at least 2 characters';
    }
    
    if (!formData.email) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Email is invalid';
    }
    
    if (!formData.password) {
      newErrors.password = 'Password is required';
    } else if (formData.password.length < 8) {
      newErrors.password = 'Password must be at least 8 characters';
    }
    
    if (!formData.password_confirmation) {
      newErrors.password_confirmation = 'Please confirm your password';
    } else if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = 'Passwords do not match';
    }
    
    if (!formData.terms) {
      newErrors.terms = 'You must accept the terms and conditions';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) return;

    setLoading(true);
    const result = await register(formData);
    setLoading(false);

    if (result.success) {
      navigate('/dashboard', { replace: true });
    } else {
      setErrors({ general: result.error || 'Registration failed' });
    }
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
            Create your account
          </h2>
          <p className="mt-2 text-sm text-secondary text-body">
            Already have an account?{' '}
            <Link to="/login" className="font-medium text-accent-primary hover:opacity-80 transition-opacity">
              Sign in here
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
              <label htmlFor="name" className="block text-sm font-medium text-primary text-body">
                Full Name
              </label>
              <div className="mt-1">
                <input
                  id="name"
                  name="name"
                  type="text"
                  autoComplete="name"
                  value={formData.name}
                  onChange={handleChange}
                  className={`appearance-none block w-full px-3 py-2 input rounded-md focus-ring sm:text-sm transition-all ${
                    errors.name ? 'border-accent-danger' : ''
                  }`}
                  placeholder="Enter your full name"
                />
                {errors.name && (
                  <p className="mt-1 text-sm status-error text-caption">{errors.name}</p>
                )}
              </div>
            </div>

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
                    errors.email ? 'border-accent-danger' : ''
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
                  autoComplete="new-password"
                  value={formData.password}
                  onChange={handleChange}
                  className={`appearance-none block w-full px-3 py-2 input rounded-md focus-ring sm:text-sm transition-all ${
                    errors.password ? 'border-accent-danger' : ''
                  }`}
                  placeholder="Create a strong password"
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

            <div>
              <label htmlFor="password_confirmation" className="block text-sm font-medium text-primary text-body">
                Confirm Password
              </label>
              <div className="mt-1 relative">
                <input
                  id="password_confirmation"
                  name="password_confirmation"
                  type={showPasswordConfirmation ? 'text' : 'password'}
                  autoComplete="new-password"
                  value={formData.password_confirmation}
                  onChange={handleChange}
                  className={`appearance-none block w-full px-3 py-2 input rounded-md focus-ring sm:text-sm transition-all ${
                    errors.password_confirmation ? 'border-accent-danger' : ''
                  }`}
                  placeholder="Confirm your password"
                />
                <button
                  type="button"
                  className="absolute inset-y-0 right-0 pr-3 flex items-center"
                  onClick={() => setShowPasswordConfirmation(!showPasswordConfirmation)}
                >
                  {showPasswordConfirmation ? (
                    <EyeSlashIcon className="h-5 w-5 text-secondary" />
                  ) : (
                    <EyeIcon className="h-5 w-5 text-secondary" />
                  )}
                </button>
                {errors.password_confirmation && (
                  <p className="mt-1 text-sm status-error text-caption">{errors.password_confirmation}</p>
                )}
              </div>
            </div>

            <div>
              <div className="flex items-start">
                <div className="flex items-center h-5">
                  <input
                    id="terms"
                    name="terms"
                    type="checkbox"
                    checked={formData.terms}
                    onChange={handleChange}
                    className="h-4 w-4 text-accent-primary focus-ring border-default rounded"
                  />
                </div>
                <div className="ml-3 text-sm">
                  <label htmlFor="terms" className="text-primary">
                    I agree to the{' '}
                    <Link to="/terms-of-service" className="text-accent-primary hover:opacity-80">
                      Terms of Service
                    </Link>{' '}
                    and{' '}
                    <Link to="/privacy-policy" className="text-accent-primary hover:opacity-80">
                      Privacy Policy
                    </Link>
                  </label>
                  {errors.terms && (
                    <p className="mt-1 text-sm status-error text-caption">{errors.terms}</p>
                  )}
                </div>
              </div>
            </div>

            <div>
              <Button
                type="submit"
                fullWidth
                loading={loading}
                disabled={loading}
              >
                Create Account
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
              <button className="w-full inline-flex justify-center py-2 px-4 border border-default rounded-md shadow-sm bg-surface text-sm font-medium text-secondary hover-surface transition-colors focus-ring">
                <span>Google</span>
              </button>
              <button className="w-full inline-flex justify-center py-2 px-4 border border-default rounded-md shadow-sm bg-surface text-sm font-medium text-secondary hover-surface transition-colors focus-ring">
                <span>GitHub</span>
              </button>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default RegisterPage;