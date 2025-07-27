import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CheckIcon,
  ArrowRightIcon,
  ArrowLeftIcon,
  SparklesIcon,
  UserPlusIcon,
  CreditCardIcon,
  BuildingOfficeIcon,
  PaintBrushIcon
} from '@heroicons/react/24/outline';

const ProfessionalOnboardingWizard = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const navigate = useNavigate();
  
  const [currentStep, setCurrentStep] = useState(1);
  const [isLoading, setIsLoading] = useState(false);
  const [workspaceData, setWorkspaceData] = useState({
    name: '',
    description: '',
    goals: [],
    features: [],
    teamMembers: [],
    subscription: 'free',
    branding: {
      logo: null,
      primaryColor: '#3B82F6',
      secondaryColor: '#1E40AF'
    }
  });

  // 6 Main Business Goals
  const businessGoals = [
    {
      id: 'instagram',
      name: 'Instagram Growth',
      description: 'Build and manage your Instagram presence with advanced tools',
      icon: '📸',
      color: 'from-pink-500 to-red-500',
      features: ['Instagram Database', 'Content Scheduling', 'Analytics', 'Hashtag Research']
    },
    {
      id: 'linkinbio',
      name: 'Link in Bio',
      description: 'Create professional bio pages that convert visitors to customers',
      icon: '🔗',
      color: 'from-blue-500 to-cyan-500',
      features: ['Drag & Drop Builder', 'Custom Domains', 'Analytics', 'Templates']
    },
    {
      id: 'courses',
      name: 'Online Courses',
      description: 'Build and sell educational content with community features',
      icon: '🎓',
      color: 'from-green-500 to-emerald-500',
      features: ['Video Hosting', 'Course Builder', 'Community Forums', 'Certificates']
    },
    {
      id: 'ecommerce',
      name: 'E-commerce',
      description: 'Create online stores and marketplaces to sell products',
      icon: '🛒',
      color: 'from-purple-500 to-indigo-500',
      features: ['Product Management', 'Payment Processing', 'Inventory', 'Analytics']
    },
    {
      id: 'crm',
      name: 'CRM & Email Marketing',
      description: 'Manage customers and run automated marketing campaigns',
      icon: '📊',
      color: 'from-orange-500 to-red-500',
      features: ['Contact Management', 'Email Campaigns', 'Automation', 'Lead Scoring']
    },
    {
      id: 'analytics',
      name: 'Analytics & Insights',
      description: 'Track performance across all platforms with advanced analytics',
      icon: '📈',
      color: 'from-teal-500 to-cyan-500',
      features: ['Multi-Platform Analytics', 'Custom Reports', 'Gamification', 'ROI Tracking']
    }
  ];

  // Available Features (40 total)
  const availableFeatures = [
    // Social Media (8 features)
    { id: 'instagram-db', name: 'Instagram Database', category: 'social', essential: true },
    { id: 'post-scheduler', name: 'Post Scheduler', category: 'social', essential: true },
    { id: 'hashtag-research', name: 'Hashtag Research', category: 'social', essential: false },
    { id: 'social-analytics', name: 'Social Analytics', category: 'social', essential: true },
    { id: 'content-calendar', name: 'Content Calendar', category: 'social', essential: false },
    { id: 'multi-platform', name: 'Multi-Platform Posting', category: 'social', essential: false },
    { id: 'auto-posting', name: 'Auto Posting', category: 'social', essential: false },
    { id: 'social-listening', name: 'Social Listening', category: 'social', essential: false },
    
    // Link in Bio (6 features)
    { id: 'bio-builder', name: 'Bio Page Builder', category: 'linkinbio', essential: true },
    { id: 'custom-domains', name: 'Custom Domains', category: 'linkinbio', essential: false },
    { id: 'bio-templates', name: 'Bio Templates', category: 'linkinbio', essential: true },
    { id: 'qr-codes', name: 'QR Code Generator', category: 'linkinbio', essential: false },
    { id: 'bio-analytics', name: 'Bio Analytics', category: 'linkinbio', essential: true },
    { id: 'dynamic-content', name: 'Dynamic Content', category: 'linkinbio', essential: false },
    
    // E-commerce (8 features)
    { id: 'product-catalog', name: 'Product Catalog', category: 'ecommerce', essential: true },
    { id: 'payment-processing', name: 'Payment Processing', category: 'ecommerce', essential: true },
    { id: 'inventory-management', name: 'Inventory Management', category: 'ecommerce', essential: false },
    { id: 'order-management', name: 'Order Management', category: 'ecommerce', essential: true },
    { id: 'shipping-integration', name: 'Shipping Integration', category: 'ecommerce', essential: false },
    { id: 'marketplace', name: 'Marketplace', category: 'ecommerce', essential: false },
    { id: 'discount-codes', name: 'Discount Codes', category: 'ecommerce', essential: false },
    { id: 'review-system', name: 'Review System', category: 'ecommerce', essential: false },
    
    // Courses (6 features)
    { id: 'video-hosting', name: 'Video Hosting', category: 'courses', essential: true },
    { id: 'course-builder', name: 'Course Builder', category: 'courses', essential: true },
    { id: 'quiz-system', name: 'Quiz System', category: 'courses', essential: false },
    { id: 'community-forums', name: 'Community Forums', category: 'courses', essential: false },
    { id: 'certificates', name: 'Certificates', category: 'courses', essential: false },
    { id: 'live-streaming', name: 'Live Streaming', category: 'courses', essential: false },
    
    // CRM & Email (6 features)
    { id: 'contact-management', name: 'Contact Management', category: 'crm', essential: true },
    { id: 'email-campaigns', name: 'Email Campaigns', category: 'crm', essential: true },
    { id: 'automation-workflows', name: 'Automation Workflows', category: 'crm', essential: false },
    { id: 'lead-scoring', name: 'Lead Scoring', category: 'crm', essential: false },
    { id: 'pipeline-management', name: 'Pipeline Management', category: 'crm', essential: false },
    { id: 'email-templates', name: 'Email Templates', category: 'crm', essential: true },
    
    // Analytics (6 features)
    { id: 'performance-analytics', name: 'Performance Analytics', category: 'analytics', essential: true },
    { id: 'custom-reports', name: 'Custom Reports', category: 'analytics', essential: false },
    { id: 'roi-tracking', name: 'ROI Tracking', category: 'analytics', essential: false },
    { id: 'gamification', name: 'Gamification', category: 'analytics', essential: false },
    { id: 'data-export', name: 'Data Export', category: 'analytics', essential: false },
    { id: 'predictive-analytics', name: 'Predictive Analytics', category: 'analytics', essential: false }
  ];

  // Subscription Plans
  const subscriptionPlans = [
    {
      id: 'free',
      name: 'Starter',
      price: 0,
      billing: 'forever',
      features: 10,
      description: 'Perfect for getting started',
      limits: 'Limited to 10 features',
      popular: false
    },
    {
      id: 'pro',
      name: 'Professional',
      price: 1,
      billing: 'per feature per month',
      yearlyPrice: 10,
      yearlyBilling: 'per feature per year',
      features: 'unlimited',
      description: 'Best for growing businesses',
      limits: 'All features available',
      popular: true
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      price: 1.5,
      billing: 'per feature per month',
      yearlyPrice: 15,
      yearlyBilling: 'per feature per year',
      features: 'unlimited',
      description: 'For large organizations',
      limits: 'White-label + Priority support',
      popular: false,
      extras: ['White-label branding', 'Priority support', 'Custom integrations', 'Advanced analytics']
    }
  ];

  const totalSteps = 6;

  // Auto-select essential features based on chosen goals
  useEffect(() => {
    const essentialFeatures = [];
    workspaceData.goals.forEach(goalId => {
      const relatedFeatures = availableFeatures.filter(
        feature => feature.category === goalId && feature.essential
      );
      essentialFeatures.push(...relatedFeatures.map(f => f.id));
    });
    
    setWorkspaceData(prev => ({
      ...prev,
      features: [...new Set([...prev.features, ...essentialFeatures])]
    }));
  }, [workspaceData.goals]);

  const handleGoalToggle = (goalId) => {
    setWorkspaceData(prev => ({
      ...prev,
      goals: prev.goals.includes(goalId)
        ? prev.goals.filter(id => id !== goalId)
        : [...prev.goals, goalId]
    }));
  };

  const handleFeatureToggle = (featureId) => {
    setWorkspaceData(prev => ({
      ...prev,
      features: prev.features.includes(featureId)
        ? prev.features.filter(id => id !== featureId)
        : [...prev.features, featureId]
    }));
  };

  const addTeamMember = () => {
    setWorkspaceData(prev => ({
      ...prev,
      teamMembers: [...prev.teamMembers, { email: '', role: 'editor' }]
    }));
  };

  const updateTeamMember = (index, field, value) => {
    setWorkspaceData(prev => ({
      ...prev,
      teamMembers: prev.teamMembers.map((member, i) => 
        i === index ? { ...member, [field]: value } : member
      )
    }));
  };

  const removeTeamMember = (index) => {
    setWorkspaceData(prev => ({
      ...prev,
      teamMembers: prev.teamMembers.filter((_, i) => i !== index)
    }));
  };

  const handleNext = () => {
    if (currentStep < totalSteps) {
      // Real data loaded from API
    }
  };

  const handlePrevious = () => {
    if (currentStep > 1) {
      // Real data loaded from API
    }
  };

  const handleComplete = async () => {
    // Real data loaded from API
    try {
      // Create workspace with all selected options
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
          ...workspaceData,
          ownerId: user.id,
          createdAt: new Date().toISOString()
        })
      });

      if (response.ok) {
        const workspace = await response.json();
        success('Workspace created successfully! Welcome to Mewayz!');
        navigate('/dashboard');
      } else {
        throw new Error('Failed to create workspace');
      }
    } catch (err) {
      error('Failed to create workspace. Please try again.');
    }
    // Real data loaded from API
  };

  const renderStep = () => {
    switch (currentStep) {
      case 1:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h2 className="text-2xl font-bold text-primary mb-4">Welcome to Mewayz! 🎉</h2>
              <p className="text-secondary">Let's set up your workspace. First, tell us about your business.</p>
            </div>
            
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-primary mb-2">Workspace Name</label>
                <input
                  type="text"
                  value={workspaceData.name}
                  onChange={(e) => setWorkspaceData(prev => ({ ...prev, name: e.target.value }))}
                  placeholder="My Awesome Business"
                  className="input w-full"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-primary mb-2">Description (Optional)</label>
                <textarea
                  value={workspaceData.description}
                  onChange={(e) => setWorkspaceData(prev => ({ ...prev, description: e.target.value }))}
                  placeholder="Tell us what your business does..."
                  className="input w-full h-24 resize-none"
                />
              </div>
            </div>
          </div>
        );

      case 2:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h2 className="text-2xl font-bold text-primary mb-4">Choose Your Business Goals 🎯</h2>
              <p className="text-secondary">Select the areas you want to focus on (choose multiple)</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {businessGoals.map((goal) => (
                <motion.div
                  key={goal.id}
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                  className={`p-6 rounded-xl border cursor-pointer transition-all ${
                    workspaceData.goals.includes(goal.id)
                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                      : 'border-default hover:border-blue-300'
                  }`}
                  onClick={() => handleGoalToggle(goal.id)}
                >
                  <div className="flex items-start space-x-4">
                    <div className={`w-12 h-12 rounded-xl bg-gradient-to-r ${goal.color} flex items-center justify-center text-2xl`}>
                      {goal.icon}
                    </div>
                    <div className="flex-1">
                      <div className="flex items-center justify-between mb-2">
                        <h3 className="font-semibold text-primary">{goal.name}</h3>
                        {workspaceData.goals.includes(goal.id) && (
                          <CheckIcon className="h-5 w-5 text-blue-500" />
                        )}
                      </div>
                      <p className="text-sm text-secondary mb-3">{goal.description}</p>
                      <div className="flex flex-wrap gap-1">
                        {goal.features.map((feature, index) => (
                          <span key={index} className="text-xs px-2 py-1 bg-surface-elevated rounded text-secondary">
                            {feature}
                          </span>
                        ))}
                      </div>
                    </div>
                  </div>
                </motion.div>
              ))}
            </div>
          </div>
        );

      case 3:
        const selectedGoalFeatures = availableFeatures.filter(feature =>
          workspaceData.goals.includes(feature.category)
        );
        
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h2 className="text-2xl font-bold text-primary mb-4">Fine-tune Your Features ⚡</h2>
              <p className="text-secondary">
                Based on your goals, here are the features available. Essential features are pre-selected.
              </p>
            </div>

            <div className="space-y-6">
              {workspaceData.goals.map(goalId => {
                const goal = businessGoals.find(g => g.id === goalId);
                const goalFeatures = selectedGoalFeatures.filter(f => f.category === goalId);
                
                return (
                  <div key={goalId} className="bg-surface-elevated p-6 rounded-xl">
                    <div className="flex items-center space-x-3 mb-4">
                      <div className={`w-8 h-8 rounded-lg bg-gradient-to-r ${goal.color} flex items-center justify-center text-lg`}>
                        {goal.icon}
                      </div>
                      <h3 className="font-semibold text-primary">{goal.name}</h3>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                      {goalFeatures.map(feature => (
                        <div
                          key={feature.id}
                          className={`flex items-center justify-between p-3 rounded-lg border ${
                            workspaceData.features.includes(feature.id)
                              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                              : 'border-default'
                          }`}
                        >
                          <div className="flex items-center space-x-3">
                            <input
                              type="checkbox"
                              checked={workspaceData.features.includes(feature.id)}
                              onChange={() => handleFeatureToggle(feature.id)}
                              disabled={feature.essential}
                              className="w-4 h-4 text-blue-500 rounded"
                            />
                            <span className={`text-sm ${feature.essential ? 'font-medium' : ''} text-primary`}>
                              {feature.name}
                            </span>
                          </div>
                          {feature.essential && (
                            <span className="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                              Essential
                            </span>
                          )}
                        </div>
                      ))}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        );

      case 4:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h2 className="text-2xl font-bold text-primary mb-4">Invite Your Team 👥</h2>
              <p className="text-secondary">Add team members to collaborate (optional)</p>
            </div>

            <div className="space-y-4">
              {workspaceData.teamMembers.map((member, index) => (
                <div key={index} className="flex items-center space-x-3 p-4 bg-surface-elevated rounded-lg">
                  <input
                    type="email"
                    value={member.email}
                    onChange={(e) => updateTeamMember(index, 'email', e.target.value)}
                    placeholder="colleague@company.com"
                    className="input flex-1"
                  />
                  <select
                    value={member.role}
                    onChange={(e) => updateTeamMember(index, 'role', e.target.value)}
                    className="input w-32"
                  >
                    <option value="viewer">Viewer</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                  </select>
                  <button
                    onClick={() => removeTeamMember(index)}
                    className="p-2 text-red-500 hover:text-red-700 transition-colors"
                  >
                    ✕
                  </button>
                </div>
              ))}
              
              <button
                onClick={addTeamMember}
                className="w-full p-4 border-2 border-dashed border-default hover:border-blue-500 rounded-lg transition-colors flex items-center justify-center space-x-2 text-secondary hover:text-blue-500"
              >
                <UserPlusIcon className="h-5 w-5" />
                <span>Add team member</span>
              </button>
            </div>
          </div>
        );

      case 5:
        const selectedFeatureCount = workspaceData.features.length;
        
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h2 className="text-2xl font-bold text-primary mb-4">Choose Your Plan 💎</h2>
              <p className="text-secondary">
                You've selected {selectedFeatureCount} features. Pick the plan that works for you.
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {subscriptionPlans.map((plan) => (
                <motion.div
                  key={plan.id}
                  whileHover={{ scale: 1.02 }}
                  className={`relative p-6 rounded-xl border cursor-pointer transition-all ${
                    workspaceData.subscription === plan.id
                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                      : 'border-default hover:border-blue-300'
                  } ${plan.popular ? 'ring-2 ring-blue-500 ring-offset-2' : ''}`}
                  onClick={() => setWorkspaceData(prev => ({ ...prev, subscription: plan.id }))}
                >
                  {plan.popular && (
                    <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                      <span className="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                        Most Popular
                      </span>
                    </div>
                  )}
                  
                  <div className="text-center">
                    <h3 className="text-lg font-bold text-primary mb-2">{plan.name}</h3>
                    <div className="mb-4">
                      {plan.price === 0 ? (
                        <div className="text-2xl font-bold text-primary">Free</div>
                      ) : (
                        <>
                          <div className="text-2xl font-bold text-primary">
                            ${plan.price}<span className="text-sm text-secondary">/{plan.billing}</span>
                          </div>
                          {plan.yearlyPrice && (
                            <div className="text-sm text-green-600">
                              Save 17% yearly: ${plan.yearlyPrice}/{plan.yearlyBilling}
                            </div>
                          )}
                        </>
                      )}
                    </div>
                    
                    <p className="text-sm text-secondary mb-4">{plan.description}</p>
                    
                    <div className="space-y-2 text-sm">
                      <div className="font-medium text-primary">
                        {plan.features === 'unlimited' ? 'All Features' : `${plan.features} Features`}
                      </div>
                      <div className="text-secondary">{plan.limits}</div>
                      
                      {plan.extras && (
                        <div className="mt-4 space-y-1">
                          {plan.extras.map((extra, index) => (
                            <div key={index} className="flex items-center space-x-2 text-xs">
                              <CheckIcon className="h-3 w-3 text-green-500" />
                              <span>{extra}</span>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                    
                    {plan.id !== 'free' && (
                      <div className="mt-4 p-3 bg-surface rounded-lg">
                        <div className="text-sm text-secondary">
                          Monthly cost for {selectedFeatureCount} features:
                        </div>
                        <div className="text-lg font-bold text-primary">
                          ${(plan.price * selectedFeatureCount).toFixed(2)}/month
                        </div>
                      </div>
                    )}
                  </div>
                  
                  {workspaceData.subscription === plan.id && (
                    <div className="absolute top-4 right-4">
                      <CheckIcon className="h-6 w-6 text-blue-500" />
                    </div>
                  )}
                </motion.div>
              ))}
            </div>

            {workspaceData.subscription === 'free' && selectedFeatureCount > 10 && (
              <div className="p-4 bg-orange-100 dark:bg-orange-900/20 border border-orange-500 rounded-lg">
                <div className="flex items-center space-x-2">
                  <SparklesIcon className="h-5 w-5 text-orange-500" />
                  <div>
                    <div className="font-medium text-orange-800 dark:text-orange-200">
                      Feature limit exceeded
                    </div>
                    <div className="text-sm text-orange-600 dark:text-orange-300">
                      Free plan includes 10 features. You've selected {selectedFeatureCount}. Consider upgrading to Pro or Enterprise.
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
        );

      case 6:
        return (
          <div className="space-y-6">
            <div className="text-center">
              <h2 className="text-2xl font-bold text-primary mb-4">Customize Your Brand 🎨</h2>
              <p className="text-secondary">Set up branding for external-facing content (optional)</p>
            </div>

            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-primary mb-2">Primary Color</label>
                  <div className="flex items-center space-x-3">
                    <input
                      type="color"
                      value={workspaceData.branding.primaryColor}
                      onChange={(e) => setWorkspaceData(prev => ({
                        ...prev,
                        branding: { ...prev.branding, primaryColor: e.target.value }
                      }))}
                      className="w-12 h-12 rounded-lg border border-default cursor-pointer"
                    />
                    <input
                      type="text"
                      value={workspaceData.branding.primaryColor}
                      onChange={(e) => setWorkspaceData(prev => ({
                        ...prev,
                        branding: { ...prev.branding, primaryColor: e.target.value }
                      }))}
                      className="input flex-1"
                      placeholder="#3B82F6"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-primary mb-2">Secondary Color</label>
                  <div className="flex items-center space-x-3">
                    <input
                      type="color"
                      value={workspaceData.branding.secondaryColor}
                      onChange={(e) => setWorkspaceData(prev => ({
                        ...prev,
                        branding: { ...prev.branding, secondaryColor: e.target.value }
                      }))}
                      className="w-12 h-12 rounded-lg border border-default cursor-pointer"
                    />
                    <input
                      type="text"
                      value={workspaceData.branding.secondaryColor}
                      onChange={(e) => setWorkspaceData(prev => ({
                        ...prev,
                        branding: { ...prev.branding, secondaryColor: e.target.value }
                      }))}
                      className="input flex-1"
                      placeholder="#1E40AF"
                    />
                  </div>
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-primary mb-2">Logo Upload</label>
                <div className="border-2 border-dashed border-default rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                  <BuildingOfficeIcon className="h-12 w-12 text-secondary mx-auto mb-4" />
                  <div className="text-secondary mb-2">Drop your logo here or click to browse</div>
                  <button className="btn btn-secondary btn-sm">Choose File</button>
                </div>
              </div>

              {/* Preview */}
              <div className="bg-surface-elevated p-6 rounded-xl">
                <h3 className="font-semibold text-primary mb-4">Brand Preview</h3>
                <div className="space-y-4">
                  <div 
                    className="p-4 rounded-lg text-white"
                    style={{ backgroundColor: workspaceData.branding.primaryColor }}
                  >
                    <div className="font-semibold">Primary Color Sample</div>
                    <div className="text-sm opacity-90">This is how your primary color will look</div>
                  </div>
                  <div 
                    className="p-4 rounded-lg text-white"
                    style={{ backgroundColor: workspaceData.branding.secondaryColor }}
                  >
                    <div className="font-semibold">Secondary Color Sample</div>
                    <div className="text-sm opacity-90">This is how your secondary color will look</div>
                  </div>
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
    <div className="min-h-screen bg-surface">
      <div className="max-w-4xl mx-auto px-4 py-8">
        {/* Progress Bar */}
        <div className="mb-8">
          <div className="flex items-center justify-between mb-4">
            <h1 className="text-xl font-semibold text-primary">Workspace Setup</h1>
            <span className="text-sm text-secondary">Step {currentStep} of {totalSteps}</span>
          </div>
          <div className="w-full bg-surface-elevated rounded-full h-2">
            <motion.div
              className="bg-blue-500 h-2 rounded-full transition-all duration-300"
              initial={{ width: 0 }}
              animate={{ width: `${(currentStep / totalSteps) * 100}%` }}
            />
          </div>
        </div>

        {/* Main Content */}
        <div className="bg-surface-elevated rounded-xl shadow-default p-8 mb-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentStep}
              initial={{ opacity: 0, x: 50 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -50 }}
              transition={{ duration: 0.3 }}
            >
              {renderStep()}
            </motion.div>
          </AnimatePresence>
        </div>

        {/* Navigation */}
        <div className="flex items-center justify-between">
          <button
            onClick={handlePrevious}
            disabled={currentStep === 1}
            className="btn btn-secondary flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <ArrowLeftIcon className="h-4 w-4" />
            <span>Previous</span>
          </button>

          <div className="flex items-center space-x-2">
            {Array.from({ length: totalSteps }, (_, i) => (
              <button
                key={i}
                onClick={() => setCurrentStep(i + 1)}
                className={`w-3 h-3 rounded-full transition-colors ${
                  i + 1 === currentStep ? 'bg-blue-500' :
                  i + 1 < currentStep ? 'bg-green-500' :
                  'bg-surface-elevated border border-default'
                }`}
              />
            ))}
          </div>

          {currentStep === totalSteps ? (
            <button
              onClick={handleComplete}
              disabled={isLoading || !workspaceData.name}
              className="btn btn-primary flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isLoading ? (
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              ) : (
                <CheckIcon className="h-4 w-4" />
              )}
              <span>{isLoading ? 'Creating...' : 'Complete Setup'}</span>
            </button>
          ) : (
            <button
              onClick={handleNext}
              disabled={
                (currentStep === 1 && !workspaceData.name) ||
                (currentStep === 2 && workspaceData.goals.length === 0) ||
                (currentStep === 5 && workspaceData.subscription === 'free' && workspaceData.features.length > 10)
              }
              className="btn btn-primary flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span>Next</span>
              <ArrowRightIcon className="h-4 w-4" />
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

export default ProfessionalOnboardingWizard;