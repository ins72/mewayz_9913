import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import {
  CheckCircleIcon,
  ChevronRightIcon,
  ChevronLeftIcon,
  RocketLaunchIcon,
  UsersIcon,
  CreditCardIcon,
  PhotoIcon,
  GlobeAltIcon,
  EnvelopeIcon,
  ChatBubbleLeftRightIcon,
  ShoppingCartIcon,
  AcademicCapIcon,
  ChartBarIcon,
  SparklesIcon,
  BoltIcon,
  PaintBrushIcon,
  CogIcon,
  CheckIcon,
  XMarkIcon,
  PlusIcon,
  ArrowRightIcon
} from '@heroicons/react/24/outline';
import {
  CheckCircleIcon as CheckCircleIconSolid,
  RocketLaunchIcon as RocketLaunchIconSolid
} from '@heroicons/react/24/solid';

const ProfessionalOnboardingWizard = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(0);
  const [loading, setLoading] = useState(false);
  const [completedSteps, setCompletedSteps] = useState(new Set());

  // Comprehensive onboarding data
  const [onboardingData, setOnboardingData] = useState({
    // Step 1: Welcome & Goals Selection
    selectedGoals: [],
    primaryGoal: null,
    
    // Step 2: Workspace Setup
    workspaceName: '',
    workspaceDescription: '',
    industry: '',
    companySize: '',
    timezone: '',
    
    // Step 3: Team Invitations
    teamMembers: [],
    
    // Step 4: Subscription Selection
    selectedPlan: null,
    billingCycle: 'monthly',
    selectedFeatures: [],
    
    // Step 5: Branding & Customization
    brandName: '',
    brandColors: {
      primary: '#3B82F6',
      secondary: '#10B981',
      accent: '#F59E0B'
    },
    logo: null,
    
    // Step 6: Integration Setup
    integrationsToSetup: [],
    socialAccounts: [],
    
    // Step 7: First Actions
    quickActions: []
  });

  // Available goals with detailed information
  const availableGoals = [
    {
      id: 'instagram',
      name: 'Instagram Management',
      icon: PhotoIcon,
      color: 'from-pink-500 to-purple-600',
      description: 'Manage your Instagram presence, analyze followers, and grow your audience',
      features: ['Follower Analytics', 'Content Scheduling', 'Hashtag Research', 'Lead Generation']
    },
    {
      id: 'link_in_bio',
      name: 'Link in Bio',
      icon: GlobeAltIcon,
      color: 'from-blue-500 to-cyan-600',
      description: 'Create stunning bio link pages that convert visitors into customers',
      features: ['Drag & Drop Builder', 'Custom Domains', 'Analytics', 'E-commerce Integration']
    },
    {
      id: 'courses',
      name: 'Online Courses',
      icon: AcademicCapIcon,
      color: 'from-green-500 to-teal-600',
      description: 'Build and sell online courses with our comprehensive learning platform',
      features: ['Course Builder', 'Video Hosting', 'Student Progress', 'Quizzes & Certificates']
    },
    {
      id: 'ecommerce',
      name: 'E-commerce',
      icon: ShoppingCartIcon,
      color: 'from-orange-500 to-red-600',
      description: 'Launch your online store and start selling products or services',
      features: ['Product Catalog', 'Payment Processing', 'Inventory Management', 'Order Fulfillment']
    },
    {
      id: 'crm',
      name: 'CRM & Email Marketing',
      icon: EnvelopeIcon,
      color: 'from-purple-500 to-pink-600',
      description: 'Manage leads, nurture relationships, and automate email campaigns',
      features: ['Contact Management', 'Email Automation', 'Lead Scoring', 'Pipeline Tracking']
    },
    {
      id: 'ai_content',
      name: 'AI Content Creation',
      icon: SparklesIcon,
      color: 'from-indigo-500 to-purple-600',
      description: 'Leverage AI to create compelling content for all your marketing needs',
      features: ['Content Generation', 'Image Creation', 'SEO Optimization', 'Multi-language Support']
    }
  ];

  const subscriptionPlans = [
    {
      id: 'free',
      name: 'Free Starter',
      price: 0,
      interval: 'month',
      featureLimit: 10,
      description: 'Perfect for trying out the platform',
      features: [
        'Up to 10 features',
        'Basic support',
        '1 workspace',
        'Standard templates',
        '50 AI tokens/month'
      ]
    },
    {
      id: 'pro',
      name: 'Professional',
      price: 1,
      interval: 'month',
      priceYearly: 10,
      featureLimit: null,
      description: 'For serious entrepreneurs and small teams',
      features: [
        '$1 per feature per month',
        'Priority support',
        'Unlimited workspaces',
        'Premium templates',
        '500 AI tokens/month',
        'Custom branding',
        'Advanced analytics'
      ],
      popular: true
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      price: 1.5,
      interval: 'month',
      priceYearly: 15,
      featureLimit: null,
      description: 'For large teams and businesses',
      features: [
        '$1.50 per feature per month',
        'White-label solution',
        'Dedicated support',
        'Custom integrations',
        '2000 AI tokens/month',
        'API access',
        'Priority features'
      ]
    }
  ];

  const steps = [
    {
      id: 'welcome',
      title: 'Welcome to Mewayz',
      subtitle: 'Let\'s set up your business platform',
      component: 'WelcomeStep'
    },
    {
      id: 'goals',
      title: 'Choose Your Goals',
      subtitle: 'What would you like to achieve with Mewayz?',
      component: 'GoalsStep'
    },
    {
      id: 'workspace',
      title: 'Workspace Setup',
      subtitle: 'Tell us about your business',
      component: 'WorkspaceStep'
    },
    {
      id: 'team',
      title: 'Invite Team Members',
      subtitle: 'Collaborate with your team',
      component: 'TeamStep'
    },
    {
      id: 'subscription',
      title: 'Choose Your Plan',
      subtitle: 'Select the perfect plan for your needs',
      component: 'SubscriptionStep'
    },
    {
      id: 'branding',
      title: 'Brand Customization',
      subtitle: 'Make it uniquely yours',
      component: 'BrandingStep'
    },
    {
      id: 'completion',
      title: 'You\'re All Set!',
      subtitle: 'Welcome to your new business platform',
      component: 'CompletionStep'
    }
  ];

  useEffect(() => {
    // Load any existing onboarding progress
    loadOnboardingProgress();
  }, []);

  const loadOnboardingProgress = async () => {
    try {
      const response = await api.get('/onboarding/progress');
      if (response.data.success && response.data.data) {
        setOnboardingData(prev => ({ ...prev, ...response.data.data }));
        setCurrentStep(response.data.data.currentStep || 0);
        setCompletedSteps(new Set(response.data.data.completedSteps || []));
      }
    } catch (err) {
      console.error('Failed to load onboarding progress:', err);
    }
  };

  const saveProgress = async () => {
    try {
      await api.post('/onboarding/progress', {
        currentStep,
        completedSteps: Array.from(completedSteps),
        data: onboardingData
      });
    } catch (err) {
      console.error('Failed to save onboarding progress:', err);
    }
  };

  const nextStep = async () => {
    if (currentStep < steps.length - 1) {
      setCompletedSteps(prev => new Set([...prev, currentStep]));
      setCurrentStep(prev => prev + 1);
      await saveProgress();
    }
  };

  const prevStep = () => {
    if (currentStep > 0) {
      setCurrentStep(prev => prev - 1);
    }
  };

  const completeOnboarding = async () => {
    try {
      setLoading(true);
      
      const response = await api.post('/onboarding/complete', {
        data: onboardingData,
        completedAt: new Date().toISOString()
      });
      
      if (response.data.success) {
        success('Onboarding completed successfully! Welcome to Mewayz!');
        navigate('/dashboard');
      } else {
        error('Failed to complete onboarding. Please try again.');
      }
    } catch (err) {
      console.error('Failed to complete onboarding:', err);
      error('Failed to complete onboarding. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const WelcomeStep = () => (
    <div className="text-center space-y-6">
      <motion.div
        initial={{ scale: 0 }}
        animate={{ scale: 1 }}
        transition={{ duration: 0.5 }}
        className="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto"
      >
        <RocketLaunchIconSolid className="w-12 h-12 text-white" />
      </motion.div>
      
      <div>
        <h2 className="text-3xl font-bold text-foreground mb-4">Welcome to Mewayz!</h2>
        <p className="text-lg text-muted max-w-2xl mx-auto">
          The all-in-one business platform that helps you manage social media, 
          create courses, build websites, and grow your business—all in one place.
        </p>
      </div>

      <div className="bg-card rounded-lg p-6 border border-border max-w-2xl mx-auto">
        <h3 className="text-xl font-semibold text-foreground mb-4">What You'll Get:</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {[
            'Professional business tools',
            'AI-powered content creation',
            'Advanced analytics',
            'Team collaboration',
            'Custom branding',
            'Premium templates'
          ].map((feature, index) => (
            <motion.div
              key={index}
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: index * 0.1 }}
              className="flex items-center space-x-3"
            >
              <CheckCircleIconSolid className="w-5 h-5 text-green-500" />
              <span className="text-foreground">{feature}</span>
            </motion.div>
          ))}
        </div>
      </div>

      <motion.button
        whileHover={{ scale: 1.05 }}
        whileTap={{ scale: 0.95 }}
        onClick={nextStep}
        className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold flex items-center mx-auto"
      >
        Get Started
        <ArrowRightIcon className="w-5 h-5 ml-2" />
      </motion.button>
    </div>
  );

  const GoalsStep = () => (
    <div className="space-y-6">
      <div className="text-center mb-8">
        <h2 className="text-2xl font-bold text-foreground mb-2">What are your main goals?</h2>
        <p className="text-muted">Select all the areas where you'd like Mewayz to help you succeed</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {availableGoals.map((goal) => (
          <motion.div
            key={goal.id}
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            onClick={() => {
              const isSelected = onboardingData.selectedGoals.includes(goal.id);
              setOnboardingData(prev => ({
                ...prev,
                selectedGoals: isSelected
                  ? prev.selectedGoals.filter(g => g !== goal.id)
                  : [...prev.selectedGoals, goal.id],
                primaryGoal: !prev.primaryGoal && !isSelected ? goal.id : prev.primaryGoal
              }));
            }}
            className={`cursor-pointer rounded-xl p-6 border-2 transition-all ${
              onboardingData.selectedGoals.includes(goal.id)
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                : 'border-border bg-card hover:border-blue-300'
            }`}
          >
            <div className="flex items-center justify-between mb-4">
              <div className={`w-12 h-12 rounded-lg bg-gradient-to-r ${goal.color} flex items-center justify-center`}>
                <goal.icon className="w-6 h-6 text-white" />
              </div>
              {onboardingData.selectedGoals.includes(goal.id) && (
                <CheckCircleIconSolid className="w-6 h-6 text-blue-500" />
              )}
            </div>
            
            <h3 className="text-lg font-semibold text-foreground mb-2">{goal.name}</h3>
            <p className="text-muted text-sm mb-4">{goal.description}</p>
            
            <div className="space-y-1">
              {goal.features.map((feature, index) => (
                <div key={index} className="flex items-center text-xs text-muted">
                  <CheckIcon className="w-3 h-3 mr-2 text-green-500" />
                  {feature}
                </div>
              ))}
            </div>
          </motion.div>
        ))}
      </div>

      {onboardingData.selectedGoals.length > 0 && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800"
        >
          <p className="text-blue-800 dark:text-blue-200 text-sm">
            Great! You've selected {onboardingData.selectedGoals.length} goal{onboardingData.selectedGoals.length > 1 ? 's' : ''}. 
            We'll customize your workspace based on these selections.
          </p>
        </motion.div>
      )}
    </div>
  );

  const WorkspaceStep = () => (
    <div className="space-y-6 max-w-2xl mx-auto">
      <div className="text-center mb-8">
        <h2 className="text-2xl font-bold text-foreground mb-2">Set up your workspace</h2>
        <p className="text-muted">Tell us about your business to personalize your experience</p>
      </div>

      <div className="space-y-6">
        <div>
          <label className="block text-sm font-medium text-foreground mb-2">
            Workspace Name *
          </label>
          <input
            type="text"
            value={onboardingData.workspaceName}
            onChange={(e) => setOnboardingData(prev => ({ ...prev, workspaceName: e.target.value }))}
            placeholder="e.g., My Digital Agency"
            className="w-full px-4 py-3 rounded-lg border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-foreground mb-2">
            Description
          </label>
          <textarea
            value={onboardingData.workspaceDescription}
            onChange={(e) => setOnboardingData(prev => ({ ...prev, workspaceDescription: e.target.value }))}
            placeholder="Brief description of your business..."
            rows={3}
            className="w-full px-4 py-3 rounded-lg border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-foreground mb-2">
              Industry
            </label>
            <select
              value={onboardingData.industry}
              onChange={(e) => setOnboardingData(prev => ({ ...prev, industry: e.target.value }))}
              className="w-full px-4 py-3 rounded-lg border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select industry</option>
              <option value="technology">Technology</option>
              <option value="marketing">Marketing & Advertising</option>
              <option value="ecommerce">E-commerce</option>
              <option value="education">Education</option>
              <option value="healthcare">Healthcare</option>
              <option value="finance">Finance</option>
              <option value="consulting">Consulting</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-foreground mb-2">
              Company Size
            </label>
            <select
              value={onboardingData.companySize}
              onChange={(e) => setOnboardingData(prev => ({ ...prev, companySize: e.target.value }))}
              className="w-full px-4 py-3 rounded-lg border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select size</option>
              <option value="solo">Just me</option>
              <option value="small">2-10 employees</option>
              <option value="medium">11-50 employees</option>
              <option value="large">50+ employees</option>
            </select>
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium text-foreground mb-2">
            Timezone
          </label>
          <select
            value={onboardingData.timezone}
            onChange={(e) => setOnboardingData(prev => ({ ...prev, timezone: e.target.value }))}
            className="w-full px-4 py-3 rounded-lg border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Select timezone</option>
            <option value="America/New_York">Eastern Time (ET)</option>
            <option value="America/Chicago">Central Time (CT)</option>
            <option value="America/Denver">Mountain Time (MT)</option>
            <option value="America/Los_Angeles">Pacific Time (PT)</option>
            <option value="Europe/London">London (GMT)</option>
            <option value="Europe/Paris">Paris (CET)</option>
            <option value="Asia/Tokyo">Tokyo (JST)</option>
            <option value="Australia/Sydney">Sydney (AEDT)</option>
          </select>
        </div>
      </div>
    </div>
  );

  const SubscriptionStep = () => (
    <div className="space-y-6">
      <div className="text-center mb-8">
        <h2 className="text-2xl font-bold text-foreground mb-2">Choose your plan</h2>
        <p className="text-muted">Select the perfect plan for your business needs</p>
      </div>

      <div className="flex justify-center mb-6">
        <div className="bg-card rounded-lg p-1 border border-border">
          <button
            onClick={() => setOnboardingData(prev => ({ ...prev, billingCycle: 'monthly' }))}
            className={`px-4 py-2 rounded-md transition-colors ${
              onboardingData.billingCycle === 'monthly'
                ? 'bg-blue-600 text-white'
                : 'text-muted hover:text-foreground'
            }`}
          >
            Monthly
          </button>
          <button
            onClick={() => setOnboardingData(prev => ({ ...prev, billingCycle: 'yearly' }))}
            className={`px-4 py-2 rounded-md transition-colors ${
              onboardingData.billingCycle === 'yearly'
                ? 'bg-blue-600 text-white'
                : 'text-muted hover:text-foreground'
            }`}
          >
            Yearly <span className="text-green-600 text-sm">(Save 83%)</span>
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {subscriptionPlans.map((plan) => (
          <motion.div
            key={plan.id}
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            onClick={() => setOnboardingData(prev => ({ ...prev, selectedPlan: plan.id }))}
            className={`cursor-pointer rounded-xl p-6 border-2 transition-all ${
              plan.popular ? 'ring-2 ring-blue-500 ring-opacity-50' : ''
            } ${
              onboardingData.selectedPlan === plan.id
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                : 'border-border bg-card hover:border-blue-300'
            }`}
          >
            {plan.popular && (
              <div className="bg-blue-500 text-white text-sm font-medium px-3 py-1 rounded-full inline-block mb-4">
                Most Popular
              </div>
            )}
            
            <h3 className="text-xl font-semibold text-foreground mb-2">{plan.name}</h3>
            <div className="mb-4">
              {plan.price === 0 ? (
                <span className="text-3xl font-bold text-foreground">Free</span>
              ) : (
                <div>
                  <span className="text-3xl font-bold text-foreground">
                    ${onboardingData.billingCycle === 'yearly' && plan.priceYearly ? plan.priceYearly : plan.price}
                  </span>
                  <span className="text-muted ml-2">
                    {plan.price === 0 ? '' : `per feature per ${onboardingData.billingCycle === 'yearly' ? 'year' : 'month'}`}
                  </span>
                </div>
              )}
            </div>
            
            <p className="text-muted text-sm mb-4">{plan.description}</p>
            
            <ul className="space-y-2">
              {plan.features.map((feature, index) => (
                <li key={index} className="flex items-start text-sm text-foreground">
                  <CheckIcon className="w-4 h-4 mr-2 text-green-500 flex-shrink-0 mt-0.5" />
                  {feature}
                </li>
              ))}
            </ul>
          </motion.div>
        ))}
      </div>
    </div>
  );

  const CompletionStep = () => (
    <div className="text-center space-y-6">
      <motion.div
        initial={{ scale: 0 }}
        animate={{ scale: 1 }}
        transition={{ duration: 0.5 }}
        className="w-24 h-24 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto"
      >
        <CheckCircleIconSolid className="w-12 h-12 text-white" />
      </motion.div>

      <div>
        <h2 className="text-3xl font-bold text-foreground mb-4">🎉 Welcome to Mewayz!</h2>
        <p className="text-lg text-muted max-w-2xl mx-auto">
          Your workspace "{onboardingData.workspaceName}" has been set up successfully. 
          You're ready to start building your business with our powerful tools!
        </p>
      </div>

      <div className="bg-card rounded-lg p-6 border border-border max-w-2xl mx-auto">
        <h3 className="text-xl font-semibold text-foreground mb-4">Here's what's ready for you:</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {onboardingData.selectedGoals.map((goalId) => {
            const goal = availableGoals.find(g => g.id === goalId);
            return goal ? (
              <motion.div
                key={goalId}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="flex items-center space-x-3"
              >
                <div className={`w-8 h-8 rounded-lg bg-gradient-to-r ${goal.color} flex items-center justify-center`}>
                  <goal.icon className="w-4 h-4 text-white" />
                </div>
                <span className="text-foreground font-medium">{goal.name}</span>
              </motion.div>
            ) : null;
          })}
        </div>
      </div>

      <motion.button
        whileHover={{ scale: 1.05 }}
        whileTap={{ scale: 0.95 }}
        onClick={completeOnboarding}
        disabled={loading}
        className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold flex items-center mx-auto disabled:opacity-50"
      >
        {loading ? 'Setting up...' : 'Enter Dashboard'}
        <RocketLaunchIcon className="w-5 h-5 ml-2" />
      </motion.button>
    </div>
  );

  const renderStepContent = () => {
    switch (steps[currentStep].component) {
      case 'WelcomeStep': return <WelcomeStep />;
      case 'GoalsStep': return <GoalsStep />;
      case 'WorkspaceStep': return <WorkspaceStep />;
      case 'SubscriptionStep': return <SubscriptionStep />;
      case 'CompletionStep': return <CompletionStep />;
      default: return <div>Step not found</div>;
    }
  };

  const canProceed = () => {
    switch (currentStep) {
      case 1: return onboardingData.selectedGoals.length > 0;
      case 2: return onboardingData.workspaceName.trim() !== '';
      case 4: return onboardingData.selectedPlan !== null;
      default: return true;
    }
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto px-4 py-8">
        {/* Progress Header */}
        <div className="mb-8">
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center space-x-4">
              <div className="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                <span className="text-white font-bold text-xl">M</span>
              </div>
              <div>
                <h1 className="text-2xl font-bold text-foreground">Mewayz Onboarding</h1>
                <p className="text-muted">Step {currentStep + 1} of {steps.length}</p>
              </div>
            </div>
            
            <div className="text-right">
              <p className="text-sm text-muted">Progress</p>
              <p className="text-lg font-semibold text-foreground">
                {Math.round(((currentStep + 1) / steps.length) * 100)}%
              </p>
            </div>
          </div>

          {/* Progress Bar */}
          <div className="w-full bg-secondary rounded-full h-2">
            <motion.div
              className="bg-blue-600 h-2 rounded-full"
              initial={{ width: 0 }}
              animate={{ width: `${((currentStep + 1) / steps.length) * 100}%` }}
              transition={{ duration: 0.5 }}
            />
          </div>

          {/* Step Indicators */}
          <div className="flex justify-between mt-4">
            {steps.map((step, index) => (
              <div key={step.id} className="flex flex-col items-center">
                <div
                  className={`w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors ${
                    index <= currentStep
                      ? 'bg-blue-600 border-blue-600 text-white'
                      : 'border-border text-muted'
                  }`}
                >
                  {completedSteps.has(index) ? (
                    <CheckIcon className="w-4 h-4" />
                  ) : (
                    <span className="text-xs">{index + 1}</span>
                  )}
                </div>
                <p className="text-xs text-muted mt-1 text-center max-w-16">{step.title.split(' ')[0]}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Step Content */}
        <div className="max-w-6xl mx-auto">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentStep}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              transition={{ duration: 0.3 }}
              className="bg-card rounded-xl shadow-lg p-8 border border-border"
            >
              <div className="text-center mb-8">
                <h2 className="text-3xl font-bold text-foreground mb-2">{steps[currentStep].title}</h2>
                <p className="text-lg text-muted">{steps[currentStep].subtitle}</p>
              </div>

              {renderStepContent()}
            </motion.div>
          </AnimatePresence>
        </div>

        {/* Navigation */}
        {currentStep < steps.length - 1 && (
          <div className="flex justify-between items-center mt-8 max-w-6xl mx-auto">
            <button
              onClick={prevStep}
              disabled={currentStep === 0}
              className="flex items-center px-6 py-3 border border-border rounded-lg text-foreground hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeftIcon className="w-5 h-5 mr-2" />
              Previous
            </button>

            <button
              onClick={nextStep}
              disabled={!canProceed()}
              className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
              <ChevronRightIcon className="w-5 h-5 ml-2" />
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default ProfessionalOnboardingWizard;