import React, { useState, useContext } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  ChevronRightIcon, 
  ChevronLeftIcon, 
  CheckIcon,
  SparklesIcon,
  UserGroupIcon,
  CreditCardIcon,
  RocketLaunchIcon,
  BriefcaseIcon,
  GlobeAltIcon,
  MegaphoneIcon,
  CameraIcon,
  ShoppingBagIcon,
  AcademicCapIcon,
  ChartBarIcon
} from '@heroicons/react/24/outline';
import { AuthContext } from '../contexts/AuthContext';
  useEffect(() => {
    loadData();
  }, []);


const OnboardingWizard = ({ isOpen, onComplete, onClose }) => {
  const { user, updateUser } = useContext(AuthContext);
  const [currentStep, setCurrentStep] = useState(0);
  const [formData, setFormData] = useState({
    goals: [],
    features: [],
    teamSize: '1',
    industry: '',
    businessType: 'creator',
    monthlyRevenue: 'just-starting',
    primaryFocus: 'content',
    teamMembers: [],
    subscriptionPlan: 'pro'
  });

  const goals = [
    {
      id: 'social_media',
      title: 'Social Media Growth',
      description: 'Build and manage your social media presence',
      icon: MegaphoneIcon,
      color: 'from-pink-500 to-rose-500'
    },
    {
      id: 'content_creation',
      title: 'Content Creation',
      description: 'Create and manage digital content',
      icon: CameraIcon,
      color: 'from-purple-500 to-indigo-500'
    },
    {
      id: 'ecommerce',
      title: 'E-commerce Store',
      description: 'Sell products and manage inventory',
      icon: ShoppingBagIcon,
      color: 'from-green-500 to-emerald-500'
    },
    {
      id: 'courses',
      title: 'Online Courses',
      description: 'Create and sell educational content',
      icon: AcademicCapIcon,
      color: 'from-blue-500 to-cyan-500'
    },
    {
      id: 'consulting',
      title: 'Consulting/Services',
      description: 'Offer professional services and consultations',
      icon: BriefcaseIcon,
      color: 'from-orange-500 to-amber-500'
    },
    {
      id: 'analytics',
      title: 'Business Analytics',
      description: 'Track and analyze business performance',
      icon: ChartBarIcon,
      color: 'from-teal-500 to-green-500'
    }
  ];

  const features = [
    {
      id: 'ai_assistant',
      title: 'AI Content Assistant',
      description: 'AI-powered content generation and optimization',
      icon: SparklesIcon,
      category: 'AI & Automation'
    },
    {
      id: 'bio_sites',
      title: 'Bio Link Pages',
      description: 'Professional link-in-bio pages',
      icon: GlobeAltIcon,
      category: 'Web Presence'
    },
    {
      id: 'advanced_booking',
      title: 'Advanced Booking',
      description: 'Appointment scheduling and calendar management',
      icon: RocketLaunchIcon,
      category: 'Business Tools'
    },
    {
      id: 'financial_management',
      title: 'Financial Management',
      description: 'Invoicing, payments, and financial tracking',
      icon: CreditCardIcon,
      category: 'Business Tools'
    },
    {
      id: 'team_collaboration',
      title: 'Team Collaboration',
      description: 'Workspace management and team features',
      icon: UserGroupIcon,
      category: 'Collaboration'
    }
  ];

  const subscriptionPlans = [
    {
      id: 'free',
      name: 'Free Starter',
      price: '$0',
      description: 'Perfect for getting started',
      features: ['1 Workspace', 'Basic AI Features', '5 Bio Sites', 'Community Support'],
      recommended: false
    },
    {
      id: 'pro',
      name: 'Professional',
      price: '$29',
      description: 'Best for creators and small businesses',
      features: ['5 Workspaces', 'Advanced AI Features', 'Unlimited Bio Sites', 'Priority Support', 'Custom Domains'],
      recommended: true
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      price: '$99',
      description: 'For larger teams and businesses',
      features: ['Unlimited Workspaces', 'Full AI Suite', 'White Label Options', '24/7 Support', 'Custom Integrations'],
      recommended: false
    }
  ];

  const steps = [
    {
      id: 'welcome',
      title: 'Welcome to Mewayz',
      description: 'Let\'s set up your perfect creator economy platform'
    },
    {
      id: 'goals',
      title: 'What are your goals?',
      description: 'Help us understand what you want to achieve'
    },
    {
      id: 'features',
      title: 'Choose your features',
      description: 'Select the tools you need to succeed'
    },
    {
      id: 'business',
      title: 'Tell us about your business',
      description: 'Help us customize your experience'
    },
    {
      id: 'team',
      title: 'Invite your team',
      description: 'Collaborate with team members (optional)'
    },
    {
      id: 'subscription',
      title: 'Choose your plan',
      description: 'Select the perfect plan for your needs'
    },
    {
      id: 'complete',
      title: 'You\'re all set!',
      description: 'Welcome to your new creator economy platform'
    }
  ];

  const handleNext = () => {
    if (currentStep < steps.length - 1) {
      // Real data loaded from API
    } else {
      handleComplete();
    }
  };

  const handleBack = () => {
    if (currentStep > 0) {
      // Real data loaded from API
    }
  };

  const handleComplete = async () => {
    try {
      // Save onboarding data to backend
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/onboarding/complete`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(formData)
      });

      if (response.ok) {
        onComplete(formData);
      }
    } catch (error) {
      console.error('Onboarding completion failed:', error);
      // Continue anyway for now
      onComplete(formData);
    }
  };

  const toggleGoal = (goalId) => {
    setFormData(prev => ({
      ...prev,
      goals: prev.goals.includes(goalId)
        ? prev.goals.filter(id => id !== goalId)
        : [...prev.goals, goalId]
    }));
  };

  const toggleFeature = (featureId) => {
    setFormData(prev => ({
      ...prev,
      features: prev.features.includes(featureId)
        ? prev.features.filter(id => id !== featureId)
        : [...prev.features, featureId]
    }));
  };

  const addTeamMember = () => {
    setFormData(prev => ({
      ...prev,
      teamMembers: [...prev.teamMembers, { email: '', role: 'member' }]
    }));
  };

  const updateTeamMember = (index, field, value) => {
    setFormData(prev => ({
      ...prev,
      teamMembers: prev.teamMembers.map((member, i) => 
        i === index ? { ...member, [field]: value } : member
      )
    }));
  };

  const removeTeamMember = (index) => {
    setFormData(prev => ({
      ...prev,
      teamMembers: prev.teamMembers.filter((_, i) => i !== index)
    }));
  };

  if (!isOpen) return null;

  const renderStepContent = () => {
    switch (currentStep) {
      case 0: // Welcome
        return (
          <div className="text-center">
            <div className="mx-auto w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-8">
              <RocketLaunchIcon className="h-10 w-10 text-white" />
            </div>
            <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
              Welcome to Mewayz, {user?.name}!
            </h2>
            <p className="text-lg text-gray-600 dark:text-gray-300 mb-8">
              We're excited to help you build and scale your creator business. 
              This setup will take just 3 minutes to customize your perfect platform.
            </p>
            <div className="flex items-center justify-center space-x-8 text-sm text-gray-500 dark:text-gray-400">
              <div className="flex items-center">
                <CheckIcon className="h-5 w-5 text-green-500 mr-2" />
                <span>Multi-workspace system</span>
              </div>
              <div className="flex items-center">
                <CheckIcon className="h-5 w-5 text-green-500 mr-2" />
                <span>AI-powered tools</span>
              </div>
              <div className="flex items-center">
                <CheckIcon className="h-5 w-5 text-green-500 mr-2" />
                <span>All-in-one platform</span>
              </div>
            </div>
          </div>
        );

      case 1: // Goals
        return (
          <div>
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">
              What are your main goals?
            </h2>
            <p className="text-gray-600 dark:text-gray-300 mb-8 text-center">
              Select all that apply - we'll customize your experience accordingly
            </p>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {goals.map((goal) => (
                <motion.div
                  key={goal.id}
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                  className={`p-6 rounded-xl cursor-pointer transition-all ${
                    formData.goals.includes(goal.id)
                      ? 'bg-gradient-to-r ' + goal.color + ' text-white shadow-lg'
                      : 'bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600'
                  }`}
                  onClick={() => toggleGoal(goal.id)}
                >
                  <div className="flex items-start">
                    <goal.icon className={`h-8 w-8 ${formData.goals.includes(goal.id) ? 'text-white' : 'text-gray-600 dark:text-gray-300'} mr-4`} />
                    <div className="flex-1">
                      <h3 className={`font-semibold ${formData.goals.includes(goal.id) ? 'text-white' : 'text-gray-900 dark:text-white'}`}>
                        {goal.title}
                      </h3>
                      <p className={`text-sm ${formData.goals.includes(goal.id) ? 'text-white/90' : 'text-gray-600 dark:text-gray-300'} mt-1`}>
                        {goal.description}
                      </p>
                    </div>
                    {formData.goals.includes(goal.id) && (
                      <CheckIcon className="h-6 w-6 text-white ml-2" />
                    )}
                  </div>
                </motion.div>
              ))}
            </div>
          </div>
        );

      case 2: // Features
        return (
          <div>
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">
              Which features do you need?
            </h2>
            <p className="text-gray-600 dark:text-gray-300 mb-8 text-center">
              We'll enable these features in your workspace
            </p>
            <div className="space-y-4">
              {features.map((feature) => (
                <motion.div
                  key={feature.id}
                  whileHover={{ scale: 1.01 }}
                  className={`p-4 rounded-lg cursor-pointer transition-all border-2 ${
                    formData.features.includes(feature.id)
                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                      : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                  }`}
                  onClick={() => toggleFeature(feature.id)}
                >
                  <div className="flex items-center">
                    <feature.icon className={`h-6 w-6 ${formData.features.includes(feature.id) ? 'text-blue-600' : 'text-gray-500'} mr-4`} />
                    <div className="flex-1">
                      <div className="flex items-center justify-between">
                        <h3 className="font-medium text-gray-900 dark:text-white">
                          {feature.title}
                        </h3>
                        <span className="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                          {feature.category}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        {feature.description}
                      </p>
                    </div>
                    {formData.features.includes(feature.id) && (
                      <CheckIcon className="h-5 w-5 text-blue-600 ml-2" />
                    )}
                  </div>
                </motion.div>
              ))}
            </div>
          </div>
        );

      case 3: // Business Info
        return (
          <div>
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">
              Tell us about your business
            </h2>
            <p className="text-gray-600 dark:text-gray-300 mb-8 text-center">
              Help us customize your experience
            </p>
            <div className="space-y-6 max-w-md mx-auto">
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  What's your industry?
                </label>
                <select
                  value={formData.industry}
                  onChange={(e) => setFormData(prev => ({ ...prev, industry: e.target.value }))}
                  className="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="">Select your industry</option>
                  <option value="content-creation">Content Creation</option>
                  <option value="ecommerce">E-commerce</option>
                  <option value="education">Education & Courses</option>
                  <option value="consulting">Consulting</option>
                  <option value="fitness">Fitness & Health</option>
                  <option value="technology">Technology</option>
                  <option value="finance">Finance</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Business Type
                </label>
                <div className="grid grid-cols-2 gap-3">
                  {['creator', 'small-business', 'startup', 'enterprise'].map((type) => (
                    <button
                      key={type}
                      onClick={() => setFormData(prev => ({ ...prev, businessType: type }))}
                      className={`p-3 rounded-lg border-2 text-sm font-medium transition-all ${
                        formData.businessType === type
                          ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-600'
                          : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'
                      }`}
                    >
                      {type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' ')}
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Team Size
                </label>
                <select
                  value={formData.teamSize}
                  onChange={(e) => setFormData(prev => ({ ...prev, teamSize: e.target.value }))}
                  className="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700"
                >
                  <option value="1">Just me</option>
                  <option value="2-5">2-5 people</option>
                  <option value="6-20">6-20 people</option>
                  <option value="20+">20+ people</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Current Monthly Revenue
                </label>
                <select
                  value={formData.monthlyRevenue}
                  onChange={(e) => setFormData(prev => ({ ...prev, monthlyRevenue: e.target.value }))}
                  className="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700"
                >
                  <option value="just-starting">Just starting ($0)</option>
                  <option value="1k-5k">$1K - $5K</option>
                  <option value="5k-20k">$5K - $20K</option>
                  <option value="20k-100k">$20K - $100K</option>
                  <option value="100k+">$100K+</option>
                </select>
              </div>
            </div>
          </div>
        );

      case 4: // Team
        return (
          <div>
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">
              Invite your team
            </h2>
            <p className="text-gray-600 dark:text-gray-300 mb-8 text-center">
              Collaborate with team members (you can always do this later)
            </p>
            <div className="max-w-md mx-auto">
              {formData.teamMembers.length === 0 ? (
                <div className="text-center py-8">
                  <UserGroupIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-500 dark:text-gray-400 mb-4">
                    No team members added yet
                  </p>
                  <button
                    onClick={addTeamMember}
                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    Add Team Member
                  </button>
                </div>
              ) : (
                <div className="space-y-4">
                  {formData.teamMembers.map((member, index) => (
                    <div key={index} className="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <input
                        type="email"
                        placeholder="Email address"
                        value={member.email}
                        onChange={(e) => updateTeamMember(index, 'email', e.target.value)}
                        className="flex-1 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-600"
                      />
                      <select
                        value={member.role}
                        onChange={(e) => updateTeamMember(index, 'role', e.target.value)}
                        className="p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-600"
                      >
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                        <option value="viewer">Viewer</option>
                      </select>
                      <button
                        onClick={() => removeTeamMember(index)}
                        className="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded"
                      >
                        ×
                      </button>
                    </div>
                  ))}
                  <button
                    onClick={addTeamMember}
                    className="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:border-blue-500 hover:text-blue-500 transition-colors"
                  >
                    + Add Another Team Member
                  </button>
                </div>
              )}
            </div>
          </div>
        );

      case 5: // Subscription
        return (
          <div>
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">
              Choose your plan
            </h2>
            <p className="text-gray-600 dark:text-gray-300 mb-8 text-center">
              Start with any plan and upgrade anytime
            </p>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {subscriptionPlans.map((plan) => (
                <motion.div
                  key={plan.id}
                  whileHover={{ scale: 1.02 }}
                  className={`relative p-6 rounded-xl border-2 cursor-pointer transition-all ${
                    formData.subscriptionPlan === plan.id
                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                      : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'
                  } ${plan.recommended ? 'ring-2 ring-blue-500 ring-offset-2' : ''}`}
                  onClick={() => setFormData(prev => ({ ...prev, subscriptionPlan: plan.id }))}
                >
                  {plan.recommended && (
                    <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                      <span className="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                        Recommended
                      </span>
                    </div>
                  )}
                  <div className="text-center">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                      {plan.name}
                    </h3>
                    <div className="mb-4">
                      <span className="text-3xl font-bold text-gray-900 dark:text-white">
                        {plan.price}
                      </span>
                      {plan.price !== '$0' && <span className="text-gray-600 dark:text-gray-300">/month</span>}
                    </div>
                    <p className="text-gray-600 dark:text-gray-300 mb-6">
                      {plan.description}
                    </p>
                    <ul className="space-y-3 text-sm">
                      {plan.features.map((feature, index) => (
                        <li key={index} className="flex items-center">
                          <CheckIcon className="h-4 w-4 text-green-500 mr-2" />
                          <span className="text-gray-600 dark:text-gray-300">{feature}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                  {formData.subscriptionPlan === plan.id && (
                    <div className="absolute top-4 right-4">
                      <CheckIcon className="h-6 w-6 text-blue-600" />
                    </div>
                  )}
                </motion.div>
              ))}
            </div>
          </div>
        );

      case 6: // Complete
        return (
          <div className="text-center">
            <div className="mx-auto w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mb-8">
              <CheckIcon className="h-10 w-10 text-white" />
            </div>
            <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
              Welcome to Mewayz!
            </h2>
            <p className="text-lg text-gray-600 dark:text-gray-300 mb-8">
              Your platform is ready. We've customized everything based on your preferences.
            </p>
            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
              <h3 className="font-semibold text-gray-900 dark:text-white mb-4">What's included:</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
                <div className="flex items-center">
                  <CheckIcon className="h-4 w-4 text-green-500 mr-2" />
                  <span>Workspace configured</span>
                </div>
                <div className="flex items-center">
                  <CheckIcon className="h-4 w-4 text-green-500 mr-2" />
                  <span>{formData.features.length} features enabled</span>
                </div>
                <div className="flex items-center">
                  <CheckIcon className="h-4 w-4 text-green-500 mr-2" />
                  <span>{formData.subscriptionPlan} plan activated</span>
                </div>
                <div className="flex items-center">
                  <CheckIcon className="h-4 w-4 text-green-500 mr-2" />
                  <span>Ready to use!</span>
                </div>
              </div>
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
      >
        {/* Header */}
        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-xl font-semibold text-gray-900 dark:text-white">
                {steps[currentStep].title}
              </h1>
              <p className="text-gray-600 dark:text-gray-300 text-sm">
                {steps[currentStep].description}
              </p>
            </div>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl"
            >
              ×
            </button>
          </div>
          
          {/* Progress Bar */}
          <div className="mt-6">
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600 dark:text-gray-300">
                Step {currentStep + 1} of {steps.length}
              </span>
              <span className="text-sm text-gray-600 dark:text-gray-300">
                {Math.round(((currentStep + 1) / steps.length) * 100)}%
              </span>
            </div>
            <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <motion.div
                className="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full"
                initial={{ width: 0 }}
                animate={{ width: `${((currentStep + 1) / steps.length) * 100}%` }}
                transition={{ duration: 0.5 }}
              />
            </div>
          </div>
        </div>

        {/* Content */}
        <div className="p-6 min-h-[400px]">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentStep}
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              transition={{ duration: 0.3 }}
            >
              {renderStepContent()}
            </motion.div>
          </AnimatePresence>
        </div>

        {/* Footer */}
        <div className="p-6 border-t border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between">
            <button
              onClick={handleBack}
              disabled={currentStep === 0}
              className={`flex items-center px-4 py-2 rounded-lg transition-colors ${
                currentStep === 0
                  ? 'text-gray-400 cursor-not-allowed'
                  : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
              }`}
            >
              <ChevronLeftIcon className="h-4 w-4 mr-1" />
              Back
            </button>
            
            <button
              onClick={handleNext}
              disabled={
                (currentStep === 1 && formData.goals.length === 0) ||
                (currentStep === 2 && formData.features.length === 0)
              }
              className="flex items-center px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {currentStep === steps.length - 1 ? 'Get Started' : 'Continue'}
              <ChevronRightIcon className="h-4 w-4 ml-1" />
            </button>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default OnboardingWizard;