import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import {
  DocumentTextIcon,
  PlusIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  DuplicateIcon,
  FolderIcon,
  MagnifyingGlassIcon
} from '@heroicons/react/24/outline';

const FormTemplatesPage = () => {
  // const { user } = useAuth(); // Temporarily disable auth to prevent errors
  const [templates, setTemplates] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [loading, setLoading] = useState(false);

  // Mock templates data
  const mockTemplates = [
    {
      id: '1',
      name: 'Contact Form',
      description: 'Basic contact form with name, email, and message fields',
      category: 'contact',
      fields: ['name', 'email', 'message'],
      submissions: 142,
      lastModified: '2 days ago',
      isPublished: true,
      createdBy: 'You'
    },
    {
      id: '2',
      name: 'Newsletter Signup',
      description: 'Simple newsletter subscription form',
      category: 'marketing',
      fields: ['email', 'firstName'],
      submissions: 89,
      lastModified: '1 week ago',
      isPublished: true,
      createdBy: user?.name || 'You'
    },
    {
      id: '3',
      name: 'Course Registration',
      description: 'Registration form for online courses',
      category: 'education',
      fields: ['name', 'email', 'phone', 'course_preference'],
      submissions: 34,
      lastModified: '3 days ago',
      isPublished: false,
      createdBy: user?.name || 'You'
    },
    {
      id: '4',
      name: 'Event RSVP',
      description: 'RSVP form for events and webinars',
      category: 'events',
      fields: ['name', 'email', 'attendance_type', 'dietary_requirements'],
      submissions: 67,
      lastModified: '5 days ago',
      isPublished: true,
      createdBy: user?.name || 'You'
    }
  ];

  const categories = [
    { value: 'all', label: 'All Categories' },
    { value: 'contact', label: 'Contact Forms' },
    { value: 'marketing', label: 'Marketing' },
    { value: 'education', label: 'Education' },
    { value: 'events', label: 'Events' },
    { value: 'surveys', label: 'Surveys' }
  ];

  useEffect(() => {
    setTemplates(mockTemplates);
  }, []);

  const filteredTemplates = templates.filter(template => {
    const matchesSearch = template.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         template.description.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || template.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  const getCategoryColor = (category) => {
    const colors = {
      contact: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
      marketing: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
      education: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
      events: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
      surveys: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300'
    };
    return colors[category] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="mb-8"
      >
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold text-primary mb-2">Form Templates</h1>
            <p className="text-secondary">Create and manage reusable form templates</p>
          </div>
          <button className="btn btn-primary flex items-center space-x-2">
            <PlusIcon className="h-5 w-5" />
            <span>Create Template</span>
          </button>
        </div>
      </motion.div>

      {/* Stats Cards */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8"
      >
        {[
          { label: 'Total Templates', value: templates.length.toString(), icon: DocumentTextIcon, color: 'bg-blue-500' },
          { label: 'Published', value: templates.filter(t => t.isPublished).length.toString(), icon: EyeIcon, color: 'bg-green-500' },
          { label: 'Total Submissions', value: templates.reduce((sum, t) => sum + t.submissions, 0).toString(), icon: FolderIcon, color: 'bg-purple-500' },
          { label: 'Categories', value: new Set(templates.map(t => t.category)).size.toString(), icon: FolderIcon, color: 'bg-orange-500' }
        ].map((stat, index) => (
          <div key={index} className="bg-surface p-6 rounded-lg shadow-default">
            <div className="flex items-center">
              <div className={`p-3 rounded-lg ${stat.color} mr-4`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary">{stat.value}</p>
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Filters and Search */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="bg-surface p-6 rounded-lg shadow-default"
      >
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="relative">
            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Search templates..."
              className="input w-full pl-10"
            />
          </div>
          <select
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
            className="input w-full"
          >
            {categories.map(category => (
              <option key={category.value} value={category.value}>
                {category.label}
              </option>
            ))}
          </select>
        </div>
      </motion.div>

      {/* Templates Grid */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.3 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        {filteredTemplates.map((template) => (
          <div key={template.id} className="bg-surface rounded-lg shadow-default hover:shadow-lg transition-all duration-200">
            <div className="p-6">
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center space-x-3">
                  <div className="p-2 bg-accent-primary/10 rounded-lg">
                    <DocumentTextIcon className="h-6 w-6 text-accent-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-primary">{template.name}</h3>
                    <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${getCategoryColor(template.category)}`}>
                      {template.category}
                    </span>
                  </div>
                </div>
                <div className={`w-3 h-3 rounded-full ${template.isPublished ? 'bg-green-500' : 'bg-gray-400'}`}></div>
              </div>
              
              <p className="text-secondary text-sm mb-4 line-clamp-2">
                {template.description}
              </p>
              
              <div className="space-y-2 mb-4">
                <div className="flex items-center justify-between text-sm text-secondary">
                  <span>Fields: {template.fields.length}</span>
                  <span>Submissions: {template.submissions}</span>
                </div>
                <div className="text-xs text-secondary">
                  Modified {template.lastModified} • by {template.createdBy}
                </div>
              </div>
              
              <div className="flex items-center justify-between pt-4 border-t border-default">
                <div className="flex items-center space-x-2">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors" title="View">
                    <EyeIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors" title="Edit">
                    <PencilIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors" title="Duplicate">
                    <DuplicateIcon className="h-4 w-4" />
                  </button>
                </div>
                <button className="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-colors" title="Delete">
                  <TrashIcon className="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {filteredTemplates.length === 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="text-center py-12"
        >
          <DocumentTextIcon className="mx-auto h-12 w-12 text-secondary mb-4" />
          <h3 className="text-lg font-medium text-primary mb-2">No templates found</h3>
          <p className="text-secondary">Try adjusting your search or create a new template.</p>
        </motion.div>
      )}
    </div>
  );
};

export default FormTemplatesPage;