import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
// import { useAuth } from '../../contexts/AuthContext';
import {
  ChartBarIcon,
  GlobeAltIcon,
  AcademicCapIcon,
  ShoppingBagIcon,
  UsersIcon,
  BoltIcon,
  CheckIcon,
  ArrowRightIcon,
  ArrowLeftIcon,
  SparklesIcon
} from '@heroicons/react/24/outline';

const OnboardingWizard = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(0);
  const [selectedGoals, setSelectedGoals] = useState([]);
  const [workspaceData, setWorkspaceData] = useState({
    name: '',
    description: '',
    industry: '',
    team_size: '',
    goals: [],
    features: []
  });
  const [teamInvites, setTeamInvites] = useState([{ email: '', role: 'editor' }]);
  const [loading, setLoading] = useState(false);

  const steps = [
    { 
      title: 'Welcome to Mewayz', 
      subtitle: 'Let\'s set up your workspace for success',
      component: 'welcome'
    },
    { 
      title: 'Choose Your Main Goals', 
      subtitle: 'Select up to 3 primary business objectives',
      component: 'goals'
    },
    { 
      title: 'Workspace Details', 
      subtitle: 'Tell us about your business',
      component: 'workspace'
    },
    { 
      title: 'Invite Your Team', 
      subtitle: 'Collaborate with team members (optional)',
      component: 'team'
    },
    { 
      title: 'Choose Your Plan', 
      subtitle: 'Select features that match your needs',
      component: 'subscription'
    },
    { 
      title: 'All Set!', 
      subtitle: 'Your workspace is ready to go',
      component: 'completion'
    }
  ];

  const businessGoals = [
    {
      id: 'instagram',
      name: 'Instagram Growth',
      description: 'Build your Instagram presence with database access and analytics',
      icon: ChartBarIcon,
      color: 'from-pink-500 to-rose-600',
      features: ['Instagram Database', 'Lead Generation', 'Analytics', 'Content Planning']
    },
    {
      id: 'link_in_bio',
      name: 'Link in Bio',
      description: 'Create professional bio pages with drag-and-drop builder',
      icon: GlobeAltIcon,
      color: 'from-blue-500 to-cyan-600',
      features: ['Drag & Drop Builder', 'Custom Domains', 'Analytics', 'QR Codes']
    },
    {
      id: 'courses',
      name: 'Courses & Education',
      description: 'Build and sell online courses with community features',
      icon: AcademicCapIcon,
      color: 'from-green-500 to-emerald-600',
      features: ['Video Hosting', 'Course Builder', 'Student Management', 'Certificates']
    },
    {
      id: 'ecommerce',
      name: 'E-commerce Store',
      description: 'Create your online store and marketplace presence',
      icon: ShoppingBagIcon,
      color: 'from-purple-500 to-violet-600',
      features: ['Product Management', 'Order Processing', 'Payment Gateway', 'Inventory']
    },
    {
      id: 'crm',
      name: 'CRM & Marketing',
      description: 'Manage leads and automate your marketing campaigns',
      icon: UsersIcon,
      color: 'from-orange-500 to-red-600',
      features: ['Contact Management', 'Email Marketing', 'Lead Scoring', 'Automation']
    },
    {
      id: 'content_creation',
      name: 'Content Creation',
      description: 'AI-powered content generation and social media management',
      icon: SparklesIcon,
      color: 'from-indigo-500 to-purple-600',
      features: ['AI Content', 'Social Scheduling', 'Template Library', 'Brand Kit']
    }
  ];

  const subscriptionPlans = [
    {
      id: 'free',
      name: 'Free Plan',
      price: '$0',
      period: 'forever',
      description: 'Perfect for getting started',
      features: ['Up to 10 platform features', 'Basic workspace', 'Community support'],
      maxFeatures: 10,
      color: 'border-gray-300',
      popular: false
    },
    {
      id: 'pro',
      name: 'Pro Plan',
      price: '$1/feature',
      period: 'per month',
      yearlyPrice: '$10/feature/year',
      description: 'Best for growing businesses',
      features: ['Unlimited selected features', 'Priority support', 'Advanced analytics', 'Team collaboration'],
      maxFeatures: -1,
      color: 'border-blue-500',
      popular: true
    },
    {
      id: 'enterprise',
      name: 'Enterprise Plan',
      price: '$1.5/feature',
      period: 'per month',
      yearlyPrice: '$15/feature/year',
      description: 'For large organizations',
      features: ['All platform features', 'White-label options', 'Custom branding', 'Dedicated support'],
      maxFeatures: -1,
      color: 'border-purple-500',
      popular: false
    }
  ];

  const handleGoalSelect = (goalId) => {
    setSelectedGoals(prev => {
      if (prev.includes(goalId)) {
        return prev.filter(id => id !== goalId);
      } else if (prev.length < 3) {
        return [...prev, goalId];
      }
      return prev;
    });
  };

  const handleNext = () => {
    if (currentStep < steps.length - 1) {
      setCurrentStep(currentStep + 1);
    }
  };

  const handleBack = () => {
    if (currentStep > 0) {
      setCurrentStep(currentStep - 1);
    }
  };

  const handleTeamInviteChange = (index, field, value) => {
    setTeamInvites(prev => prev.map((invite, i) => 
      i === index ? { ...invite, [field]: value } : invite
    ));
  };

  const addTeamInvite = () => {
    setTeamInvites(prev => [...prev, { email: '', role: 'editor' }]);
  };

  const removeTeamInvite = (index) => {
    setTeamInvites(prev => prev.filter((_, i) => i !== index));
  };

  const handleComplete = async () => {
    setLoading(true);
    
    try {
      // Create workspace with selected goals and features
      const workspacePayload = {
        ...workspaceData,
        goals: selectedGoals,
        team_invites: teamInvites.filter(invite => invite.email)
      };

      // API call would go here
      console.log('Creating workspace:', workspacePayload);
      
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      // Navigate to dashboard
      navigate('/dashboard');
    } catch (error) {
      console.error('Error creating workspace:', error);
    } finally {
      setLoading(false);
    }
  };

  const renderStepContent = () => {
    const step = steps[currentStep];

    switch (step.component) {
      case 'welcome':
        return (
          <div className="text-center py-12">
            <motion.div
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              transition={{ duration: 0.5 }}
              className="mb-8"
            >
              <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <BoltIcon className="h-12 w-12 text-white" />
              </div>
              <h1 className="text-4xl font-bold text-primary mb-4">Welcome to Mewayz!</h1>
              <p className="text-lg text-secondary max-w-md mx-auto">
                The all-in-one platform to grow your business, manage your audience, and scale your success.
              </p>
            </motion.div>
            
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
              {['Social Media', 'E-commerce', 'Courses', 'CRM', 'Analytics', 'Templates'].map((feature, index) => (
                <motion.div
                  key={feature}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: index * 0.1 + 0.3 }}
                  className="bg-surface p-4 rounded-lg text-center"
                >
                  <p className="text-sm font-medium text-primary">{feature}</p>
                </motion.div>
              ))}
            </div>
          </div>
        );

      case 'goals':
        return (
          <div className="py-8">
            <div className="text-center mb-8">
              <p className="text-secondary mb-4">Select up to 3 main business objectives</p>
              <p className="text-sm text-secondary">
                Selected: {selectedGoals.length}/3
              </p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
              {businessGoals.map((goal) => {
                const isSelected = selectedGoals.includes(goal.id);
                const canSelect = selectedGoals.length < 3 || isSelected;
                
                return (
                  <motion.div
                    key={goal.id}
                    whileHover={canSelect ? { scale: 1.02 } : {}}
                    whileTap={canSelect ? { scale: 0.98 } : {}}
                    className={`relative p-6 rounded-xl border-2 cursor-pointer transition-all ${
                      isSelected 
                        ? 'border-blue-500 bg-blue-500/10' 
                        : canSelect 
                        ? 'border-default hover:border-blue-300' 
                        : 'border-default opacity-50 cursor-not-allowed'
                    }`}
                    onClick={() => canSelect && handleGoalSelect(goal.id)}
                  >
                    {isSelected && (
                      <div className="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                        <CheckIcon className="h-4 w-4 text-white" />
                      </div>
                    )}
                    
                    <div className={`w-12 h-12 bg-gradient-to-br ${goal.color} rounded-lg flex items-center justify-center mb-4`}>
                      <goal.icon className="h-6 w-6 text-white" />
                    </div>
                    
                    <h3 className="text-lg font-semibold text-primary mb-2">{goal.name}</h3>
                    <p className="text-sm text-secondary mb-4">{goal.description}</p>
                    
                    <div className="space-y-1">
                      {goal.features.slice(0, 3).map((feature) => (
                        <div key={feature} className="flex items-center text-xs text-secondary">
                          <CheckIcon className="h-3 w-3 text-green-500 mr-2" />
                          {feature}
                        </div>
                      ))}
                    </div>
                  </motion.div>
                );
              })}
            </div>
          </div>
        );

      case 'workspace':
        return (
          <div className="max-w-2xl mx-auto py-8">
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-primary mb-2">
                  Workspace Name *
                </label>
                <input
                  type="text"
                  value={workspaceData.name}
                  onChange={(e) => setWorkspaceData(prev => ({ ...prev, name: e.target.value }))}
                  placeholder="My Awesome Business"
                  className="input w-full"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-primary mb-2">
                  Description
                </label>
                <textarea
                  value={workspaceData.description}
                  onChange={(e) => setWorkspaceData(prev => ({ ...prev, description: e.target.value }))}
                  placeholder="Tell us about your business..."
                  rows={3}
                  className="input w-full resize-none"
                />
              </div>
              
              <div className="grid grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-primary mb-2">
                    Industry
                  </label>
                  <select
                    value={workspaceData.industry}
                    onChange={(e) => setWorkspaceData(prev => ({ ...prev, industry: e.target.value }))}
                    className="input w-full"
                  >
                    <option value="">Select Industry</option>
                    <option value="technology">Technology</option>
                    <option value="retail">Retail & E-commerce</option>
                    <option value="education">Education</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="finance">Finance</option>
                    <option value="marketing">Marketing & Advertising</option>
                    <option value="consulting">Consulting</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-primary mb-2">
                    Team Size
                  </label>
                  <select
                    value={workspaceData.team_size}
                    onChange={(e) => setWorkspaceData(prev => ({ ...prev, team_size: e.target.value }))}
                    className="input w-full"
                  >
                    <option value="">Select Size</option>
                    <option value="1">Just me</option>
                    <option value="2-5">2-5 people</option>
                    <option value="6-10">6-10 people</option>
                    <option value="11-50">11-50 people</option>
                    <option value="50+">50+ people</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        );

      case 'team':
        return (
          <div className="max-w-2xl mx-auto py-8">
            <div className="text-center mb-8">
              <p className="text-secondary">Invite team members to collaborate on your workspace</p>
              <p className="text-sm text-secondary mt-2">You can skip this step and invite members later</p>
            </div>
            
            <div className="space-y-4">
              {teamInvites.map((invite, index) => (
                <div key={index} className="flex gap-4 items-end">
                  <div className="flex-1">
                    <label className="block text-sm font-medium text-primary mb-2">
                      Email Address
                    </label>
                    <input
                      type="email"
                      value={invite.email}
                      onChange={(e) => handleTeamInviteChange(index, 'email', e.target.value)}
                      placeholder="teammate@example.com"
                      className="input w-full"
                    />
                  </div>
                  
                  <div className="w-32">
                    <label className="block text-sm font-medium text-primary mb-2">
                      Role
                    </label>
                    <select
                      value={invite.role}
                      onChange={(e) => handleTeamInviteChange(index, 'role', e.target.value)}
                      className="input w-full"
                    >
                      <option value="viewer">Viewer</option>
                      <option value="editor">Editor</option>
                      <option value="admin">Admin</option>
                    </select>
                  </div>
                  
                  {teamInvites.length > 1 && (
                    <button
                      onClick={() => removeTeamInvite(index)}
                      className="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg"
                    >
                      Remove
                    </button>
                  )}
                </div>
              ))}
            </div>
            
            <button
              onClick={addTeamInvite}
              className="mt-4 text-sm text-blue-500 hover:text-blue-700 font-medium"
            >
              + Add Another Team Member
            </button>
          </div>
        );

      case 'subscription':
        return (
          <div className="py-8">
            <div className="text-center mb-8">
              <p className="text-secondary mb-2">Choose a plan that fits your needs</p>
              <p className="text-sm text-secondary">You can change your plan anytime</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
              {subscriptionPlans.map((plan) => (
                <div
                  key={plan.id}
                  className={`relative p-6 rounded-xl border-2 ${plan.color} ${
                    plan.popular ? 'ring-2 ring-blue-500 ring-opacity-50' : ''
                  }`}
                >
                  {plan.popular && (
                    <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                      <span className="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        Most Popular
                      </span>
                    </div>
                  )}
                  
                  <div className="text-center mb-6">
                    <h3 className="text-xl font-bold text-primary mb-2">{plan.name}</h3>
                    <div className="mb-2">
                      <span className="text-3xl font-bold text-primary">{plan.price}</span>
                      <span className="text-secondary ml-1">/{plan.period}</span>
                    </div>
                    {plan.yearlyPrice && (
                      <p className="text-sm text-secondary">or {plan.yearlyPrice}</p>
                    )}
                    <p className="text-sm text-secondary mt-2">{plan.description}</p>
                  </div>
                  
                  <ul className="space-y-3 mb-6">
                    {plan.features.map((feature) => (
                      <li key={feature} className="flex items-center text-sm">
                        <CheckIcon className="h-4 w-4 text-green-500 mr-3 flex-shrink-0" />
                        <span className="text-secondary">{feature}</span>
                      </li>
                    ))}
                  </ul>
                  
                  <button
                    className={`w-full py-2 px-4 rounded-lg font-medium transition-colors ${
                      plan.popular
                        ? 'bg-blue-500 text-white hover:bg-blue-600'
                        : 'bg-surface-hover text-primary hover:bg-surface-elevated'
                    }`}
                  >
                    {plan.id === 'free' ? 'Start Free' : 'Choose Plan'}
                  </button>
                </div>
              ))}
            </div>
          </div>
        );

      case 'completion':
        return (
          <div className="text-center py-12">
            <motion.div
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              transition={{ duration: 0.5 }}
              className="mb-8"
            >
              <div className="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <CheckIcon className="h-12 w-12 text-white" />
              </div>
              <h1 className="text-4xl font-bold text-primary mb-4">You're All Set!</h1>
              <p className="text-lg text-secondary max-w-md mx-auto mb-8">
                Your workspace is ready. Let's start building your success story with Mewayz.
              </p>
            </motion.div>
            
            <div className="bg-surface p-6 rounded-lg max-w-md mx-auto mb-8">
              <h3 className="font-semibold text-primary mb-4">Your Selected Goals:</h3>
              <div className="space-y-2">
                {selectedGoals.map((goalId) => {
                  const goal = businessGoals.find(g => g.id === goalId);
                  return (
                    <div key={goalId} className="flex items-center">
                      <goal.icon className="h-5 w-5 text-blue-500 mr-3" />
                      <span className="text-sm text-secondary">{goal.name}</span>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-app py-8 px-4">
      <div className="max-w-6xl mx-auto">
        {/* Progress Bar */}
        <div className="mb-12">
          <div className="flex items-center justify-between mb-4">
            {steps.map((step, index) => (
              <div key={index} className="flex items-center">
                <div
                  className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold ${
                    index <= currentStep
                      ? 'bg-blue-500 text-white'
                      : 'bg-surface-elevated text-secondary'
                  }`}
                >
                  {index < currentStep ? <CheckIcon className="h-4 w-4" /> : index + 1}
                </div>
                {index < steps.length - 1 && (
                  <div
                    className={`h-1 w-16 mx-2 ${
                      index < currentStep ? 'bg-blue-500' : 'bg-surface-elevated'
                    }`}
                  />
                )}
              </div>
            ))}
          </div>
          <div className="text-center">
            <h2 className="text-2xl font-bold text-primary">{steps[currentStep].title}</h2>
            <p className="text-secondary mt-2">{steps[currentStep].subtitle}</p>
          </div>
        </div>

        {/* Step Content */}
        <AnimatePresence mode="wait">
          <motion.div
            key={currentStep}
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -20 }}
            transition={{ duration: 0.3 }}
            className="mb-12"
          >
            {renderStepContent()}
          </motion.div>
        </AnimatePresence>

        {/* Navigation */}
        <div className="flex justify-between items-center max-w-2xl mx-auto">
          <button
            onClick={handleBack}
            disabled={currentStep === 0}
            className={`flex items-center px-4 py-2 rounded-lg font-medium transition-colors ${
              currentStep === 0
                ? 'text-secondary cursor-not-allowed'
                : 'text-primary hover:bg-surface-hover'
            }`}
          >
            <ArrowLeftIcon className="h-4 w-4 mr-2" />
            Back
          </button>
          
          {currentStep === steps.length - 1 ? (
            <button
              onClick={handleComplete}
              disabled={loading}
              className="flex items-center px-6 py-3 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {loading ? 'Setting up...' : 'Go to Dashboard'}
              <ArrowRightIcon className="h-4 w-4 ml-2" />
            </button>
          ) : (
            <button
              onClick={handleNext}
              disabled={currentStep === 1 && selectedGoals.length === 0}
              className={`flex items-center px-6 py-3 rounded-lg font-medium transition-colors ${
                (currentStep === 1 && selectedGoals.length === 0)
                  ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  : 'bg-blue-500 text-white hover:bg-blue-600'
              }`}
            >
              Next
              <ArrowRightIcon className="h-4 w-4 ml-2" />
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

export default OnboardingWizard;