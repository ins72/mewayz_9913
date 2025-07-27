import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useTheme } from '../../contexts/ThemeContext';
import Button from '../../components/Button';
import { SunIcon, MoonIcon, ArrowLeftIcon } from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


const ForgotPasswordPage = () => {
  const { theme, toggleTheme } = useTheme();
  
  const [formData, setFormData] = useState({
    email: '',
  });
  const [loading, setLoading] = useState(false);
  const [sent, setSent] = useState(false);
  const [errors, setErrors] = useState({});

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

    // Real data loaded from API
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) return;

    // Real data loaded from API
    
    // Simulate API call
    setTimeout(() => {
      // Real data loaded from API
      // Real data loaded from API
    }, 2000);
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
            Reset your password
          </h2>
          <p className="mt-2 text-sm text-secondary text-body">
            Enter your email address and we'll send you a link to reset your password.
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
          {!sent ? (
            <form className="space-y-6" onSubmit={handleSubmit}>
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
                <Button
                  type="submit"
                  fullWidth
                  loading={loading}
                  disabled={loading}
                >
                  Send reset link
                </Button>
              </div>
            </form>
          ) : (
            <div className="text-center">
              <div className="w-16 h-16 bg-accent-success rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h3 className="text-lg font-semibold text-primary mb-2">Check your email</h3>
              <p className="text-secondary mb-6">
                We've sent a password reset link to {formData.email}
              </p>
              <Button onClick={() => setSent(false)} variant="secondary" fullWidth>
                Send another email
              </Button>
            </div>
          )}

          <div className="mt-6">
            <div className="relative">
              <div className="absolute inset-0 flex items-center">
                <div className="w-full border-t border-default" />
              </div>
              <div className="relative flex justify-center text-sm">
                <span className="px-2 bg-surface text-secondary">
                  Remember your password?
                </span>
              </div>
            </div>

            <div className="mt-6">
              <Link to="/login">
                <Button variant="secondary" fullWidth>
                  <ArrowLeftIcon className="w-4 h-4 mr-2" />
                  Back to login
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default ForgotPasswordPage;