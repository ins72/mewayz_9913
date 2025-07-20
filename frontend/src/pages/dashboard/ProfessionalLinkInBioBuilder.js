import React, { useState, useEffect } from 'react';
import { DndContext, closestCenter, KeyboardSensor, PointerSensor, useSensor, useSensors } from '@dnd-kit/core';
import { arrayMove, SortableContext, sortableKeyboardCoordinates, verticalListSortingStrategy } from '@dnd-kit/sortable';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  PlusIcon,
  LinkIcon,
  PhotoIcon,
  VideoCameraIcon,
  DocumentTextIcon,
  QrCodeIcon,
  PaintBrushIcon,
  EyeIcon,
  ChartBarIcon,
  CogIcon,
  GlobeAltIcon,
  ShoppingBagIcon,
  CalendarIcon,
  PhoneIcon,
  MapPinIcon,
  EnvelopeIcon,
  PlayIcon,
  MusicIcon,
  BookmarkIcon,
  CreditCardIcon,
  UserGroupIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  ArrowTopRightOnSquareIcon,
  DuplicateIcon,
  TrashIcon,
  StarIcon,
  FireIcon,
  BoltIcon,
  SparklesIcon,
  PencilIcon,
  SunIcon,
  MoonIcon
} from '@heroicons/react/24/outline';
import {
  LinkIcon as LinkIconSolid,
  StarIcon as StarIconSolid,
  HeartIcon as HeartIconSolid
} from '@heroicons/react/24/solid';

const ProfessionalLinkInBioBuilder = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('design');
  const [bioSite, setBioSite] = useState({
    id: '',
    title: 'My Link in Bio',
    description: 'Discover all my links in one place',
    avatar: '',
    backgroundType: 'gradient',
    backgroundValue: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    theme: 'modern',
    customDomain: '',
    seoTitle: '',
    seoDescription: '',
    analyticsCode: '',
    isPublished: false
  });
  
  const [links, setLinks] = useState([]);
  const [showLinkModal, setShowLinkModal] = useState(false);
  const [editingLink, setEditingLink] = useState(null);
  const [previewMode, setPreviewMode] = useState('mobile');
  const [analytics, setAnalytics] = useState({
    totalViews: 0,
    totalClicks: 0,
    conversionRate: 0,
    topLinks: []
  });
  
  // Drag and drop sensors
  const sensors = useSensors(
    useSensor(PointerSensor),
    useSensor(KeyboardSensor, {
      coordinateGetter: sortableKeyboardCoordinates,
    })
  );
  
  // Link types
  const linkTypes = [
    { id: 'url', name: 'Website Link', icon: LinkIcon, description: 'Add any external link' },
    { id: 'social', name: 'Social Media', icon: UserGroupIcon, description: 'Link to social profiles' },
    { id: 'video', name: 'Video', icon: VideoCameraIcon, description: 'YouTube, TikTok, or other videos' },
    { id: 'music', name: 'Music', icon: MusicIcon, description: 'Spotify, Apple Music, etc.' },
    { id: 'shop', name: 'Shop', icon: ShoppingBagIcon, description: 'E-commerce product or store' },
    { id: 'booking', name: 'Booking', icon: CalendarIcon, description: 'Appointment scheduling' },
    { id: 'contact', name: 'Contact', icon: PhoneIcon, description: 'Contact information' },
    { id: 'email', name: 'Email Signup', icon: EnvelopeIcon, description: 'Email collection form' },
    { id: 'payment', name: 'Payment', icon: CreditCardIcon, description: 'Payment or donation link' }
  ];
  
  // Themes
  const themes = [
    { id: 'modern', name: 'Modern', preview: 'bg-gradient-to-br from-purple-500 to-pink-500' },
    { id: 'minimal', name: 'Minimal', preview: 'bg-white border' },
    { id: 'dark', name: 'Dark', preview: 'bg-gray-900' },
    { id: 'neon', name: 'Neon', preview: 'bg-black border border-green-500' },
    { id: 'nature', name: 'Nature', preview: 'bg-gradient-to-br from-green-400 to-blue-500' },
    { id: 'sunset', name: 'Sunset', preview: 'bg-gradient-to-br from-orange-400 to-pink-600' }
  ];
  
  // Background options
  const backgrounds = [
    { type: 'gradient', name: 'Purple Gradient', value: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
    { type: 'gradient', name: 'Ocean Gradient', value: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)' },
    { type: 'gradient', name: 'Sunset Gradient', value: 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)' },
    { type: 'gradient', name: 'Forest Gradient', value: 'linear-gradient(135deg, #c3cfe2 0%, #c3cfe2 100%)' },
    { type: 'solid', name: 'White', value: '#ffffff' },
    { type: 'solid', name: 'Black', value: '#000000' },
    { type: 'solid', name: 'Navy', value: '#1a365d' }
  ];
  
  useEffect(() => {
    loadBioSite();
    loadAnalytics();
  }, []);
  
  const loadBioSite = async () => {
    // Mock data - in real implementation, this would fetch from API
    setBioSite({
      id: '1',
      title: 'John Creator',
      description: '🚀 Digital Marketing Expert | 📱 Content Creator | 🌟 Helping businesses grow online',
      avatar: 'https://ui-avatars.com/api/?name=John+Creator&background=3b82f6&color=fff',
      backgroundType: 'gradient',
      backgroundValue: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      theme: 'modern',
      customDomain: '',
      seoTitle: 'John Creator - Digital Marketing Expert',
      seoDescription: 'Discover all my links, courses, and content in one place',
      analyticsCode: '',
      isPublished: true
    });
    
    setLinks([
      {
        id: '1',
        type: 'url',
        title: 'My Latest Course: Digital Marketing Mastery',
        subtitle: 'Learn proven strategies to grow your business online',
        url: 'https://example.com/course',
        thumbnail: 'https://ui-avatars.com/api/?name=Course&background=10b981&color=fff',
        isActive: true,
        clicks: 1247,
        priority: 'high'
      },
      {
        id: '2',
        type: 'social',
        title: 'Follow me on Instagram',
        subtitle: '@johncreator',
        url: 'https://instagram.com/johncreator',
        thumbnail: 'https://ui-avatars.com/api/?name=IG&background=E4405F&color=fff',
        isActive: true,
        clicks: 892,
        priority: 'medium'
      },
      {
        id: '3',
        type: 'video',
        title: 'Watch: 5 Marketing Tips for 2025',
        subtitle: 'YouTube • 2.1M views',
        url: 'https://youtube.com/watch?v=example',
        thumbnail: 'https://ui-avatars.com/api/?name=YT&background=FF0000&color=fff',
        isActive: true,
        clicks: 654,
        priority: 'medium'
      },
      {
        id: '4',
        type: 'booking',
        title: 'Book a 1-on-1 Consultation',
        subtitle: '30 min • $99',
        url: 'https://calendly.com/johncreator',
        thumbnail: 'https://ui-avatars.com/api/?name=Book&background=f59e0b&color=fff',
        isActive: true,
        clicks: 234,
        priority: 'high'
      }
    ]);
  };
  
  const loadAnalytics = () => {
    setAnalytics({
      totalViews: 12547,
      totalClicks: 3027,
      conversionRate: 24.1,
      topLinks: [
        { title: 'Digital Marketing Course', clicks: 1247, percentage: 41.2 },
        { title: 'Instagram Profile', clicks: 892, percentage: 29.5 },
        { title: 'YouTube Video', clicks: 654, percentage: 21.6 },
        { title: 'Consultation Booking', clicks: 234, percentage: 7.7 }
      ]
    });
  };
  
  const handleDragEnd = (event) => {
    const { active, over } = event;
    
    if (active.id !== over.id) {
      setLinks((items) => {
        const oldIndex = items.findIndex(item => item.id === active.id);
        const newIndex = items.findIndex(item => item.id === over.id);
        return arrayMove(items, oldIndex, newIndex);
      });
    }
  };
  
  const addLink = (linkData) => {
    const newLink = {
      id: Date.now().toString(),
      ...linkData,
      isActive: true,
      clicks: 0,
      priority: 'medium'
    };
    setLinks([...links, newLink]);
    setShowLinkModal(false);
    success('Link added successfully!');
  };
  
  const updateLink = (linkData) => {
    setLinks(links.map(link => 
      link.id === editingLink.id ? { ...editingLink, ...linkData } : link
    ));
    setEditingLink(null);
    success('Link updated successfully!');
  };
  
  const deleteLink = (linkId) => {
    setLinks(links.filter(link => link.id !== linkId));
    success('Link deleted successfully!');
  };
  
  const toggleLinkStatus = (linkId) => {
    setLinks(links.map(link => 
      link.id === linkId ? { ...link, isActive: !link.isActive } : link
    ));
  };
  
  const duplicateLink = (link) => {
    const duplicatedLink = {
      ...link,
      id: Date.now().toString(),
      title: `${link.title} (Copy)`,
      clicks: 0
    };
    setLinks([...links, duplicatedLink]);
    success('Link duplicated successfully!');
  };
  
  const saveBioSite = async () => {
    try {
      // Mock API call
      console.log('Saving bio site:', bioSite);
      success('Bio site saved successfully!');
    } catch (err) {
      error('Failed to save bio site');
    }
  };
  
  const publishBioSite = async () => {
    try {
      setBioSite({ ...bioSite, isPublished: !bioSite.isPublished });
      success(bioSite.isPublished ? 'Bio site unpublished' : 'Bio site published successfully!');
    } catch (err) {
      error('Failed to publish bio site');
    }
  };
  
  const generateQRCode = () => {
    // Mock QR code generation
    success('QR code generated! Check your downloads.');
  };
  
  const SortableLink = ({ link }) => {
    const {
      attributes,
      listeners,
      setNodeRef,
      transform,
      transition,
    } = useSortable({ id: link.id });

    const style = {
      transform: CSS.Transform.toString(transform),
      transition,
    };

    const getLinkIcon = (type) => {
      const iconMap = {
        url: LinkIcon,
        social: UserGroupIcon,
        video: VideoCameraIcon,
        music: MusicIcon,
        shop: ShoppingBagIcon,
        booking: CalendarIcon,
        contact: PhoneIcon,
        email: EnvelopeIcon,
        payment: CreditCardIcon
      };
      return iconMap[type] || LinkIcon;
    };

    const Icon = getLinkIcon(link.type);

    return (
      <div
        ref={setNodeRef}
        style={style}
        {...attributes}
        {...listeners}
        className={`p-4 rounded-xl border-2 cursor-move transition-all ${
          link.isActive 
            ? 'border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800' 
            : 'border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 opacity-60'
        }`}
      >
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="p-2 rounded-lg bg-surface border border-default">
              <Icon className="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <h4 className="font-medium text-primary">{link.title}</h4>
              <p className="text-sm text-secondary">{link.subtitle}</p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className="text-sm text-secondary">{link.clicks} clicks</span>
            <div className="flex space-x-1">
              <button
                onClick={() => toggleLinkStatus(link.id)}
                className={`p-1 rounded ${link.isActive ? 'text-green-600' : 'text-gray-400'}`}
              >
                <EyeIcon className="h-4 w-4" />
              </button>
              <button
                onClick={() => setEditingLink(link)}
                className="p-1 rounded text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900"
              >
                <PencilIcon className="h-4 w-4" />
              </button>
              <button
                onClick={() => duplicateLink(link)}
                className="p-1 rounded text-green-600 hover:bg-green-100 dark:hover:bg-green-900"
              >
                <DuplicateIcon className="h-4 w-4" />
              </button>
              <button
                onClick={() => deleteLink(link.id)}
                className="p-1 rounded text-red-600 hover:bg-red-100 dark:hover:bg-red-900"
              >
                <TrashIcon className="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  };
  
  const LinkModal = ({ isOpen, onClose, onSubmit, editData = null }) => {
    const [formData, setFormData] = useState({
      type: 'url',
      title: '',
      subtitle: '',
      url: '',
      thumbnail: ''
    });
    
    useEffect(() => {
      if (editData) {
        setFormData(editData);
      }
    }, [editData]);
    
    const handleSubmit = (e) => {
      e.preventDefault();
      onSubmit(formData);
      setFormData({ type: 'url', title: '', subtitle: '', url: '', thumbnail: '' });
      onClose();
    };
    
    if (!isOpen) return null;
    
    return (
      <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-surface rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto"
        >
          <div className="p-6 border-b border-default">
            <h2 className="text-xl font-bold text-primary">
              {editData ? 'Edit Link' : 'Add New Link'}
            </h2>
          </div>
          
          <form onSubmit={handleSubmit} className="p-6 space-y-6">
            {/* Link Type Selection */}
            <div>
              <label className="block text-sm font-medium text-secondary mb-3">Link Type</label>
              <div className="grid grid-cols-3 gap-3">
                {linkTypes.map((type) => (
                  <button
                    key={type.id}
                    type="button"
                    onClick={() => setFormData({ ...formData, type: type.id })}
                    className={`p-3 rounded-lg border text-center transition-all ${
                      formData.type === type.id
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                        : 'border-default hover:bg-surface-hover'
                    }`}
                  >
                    <type.icon className="h-6 w-6 mx-auto mb-2 text-blue-600" />
                    <div className="text-sm font-medium text-primary">{type.name}</div>
                  </button>
                ))}
              </div>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Title</label>
                <input
                  type="text"
                  value={formData.title}
                  onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                  placeholder="Enter link title"
                  className="input"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Subtitle (optional)</label>
                <input
                  type="text"
                  value={formData.subtitle}
                  onChange={(e) => setFormData({ ...formData, subtitle: e.target.value })}
                  placeholder="Enter subtitle"
                  className="input"
                />
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">URL</label>
              <input
                type="url"
                value={formData.url}
                onChange={(e) => setFormData({ ...formData, url: e.target.value })}
                placeholder="https://example.com"
                className="input"
                required
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Thumbnail URL (optional)</label>
              <input
                type="url"
                value={formData.thumbnail}
                onChange={(e) => setFormData({ ...formData, thumbnail: e.target.value })}
                placeholder="https://example.com/image.jpg"
                className="input"
              />
            </div>
            
            <div className="flex items-center justify-end space-x-3 pt-4">
              <button
                type="button"
                onClick={onClose}
                className="btn btn-secondary"
              >
                Cancel
              </button>
              <button
                type="submit"
                className="btn btn-primary"
              >
                {editData ? 'Update Link' : 'Add Link'}
              </button>
            </div>
          </form>
        </motion.div>
      </div>
    );
  };
  
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <LinkIconSolid className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">Link in Bio Builder</h1>
            </div>
            <p className="text-white/80">Create beautiful, customizable link pages with drag & drop</p>
          </div>
          <div className="flex space-x-3">
            <button
              onClick={generateQRCode}
              className="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg font-medium"
            >
              <QrCodeIcon className="h-5 w-5 mr-2 inline" />
              QR Code
            </button>
            <button
              onClick={publishBioSite}
              className={`px-4 py-2 rounded-lg font-medium ${
                bioSite.isPublished
                  ? 'bg-green-500 hover:bg-green-600'
                  : 'bg-white/20 hover:bg-white/30'
              }`}
            >
              {bioSite.isPublished ? 'Published' : 'Publish'}
            </button>
          </div>
        </div>
      </div>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Builder Panel */}
        <div className="lg:col-span-2 space-y-6">
          {/* Tabs */}
          <div className="border-b border-default">
            <nav className="flex space-x-8">
              {[
                { id: 'design', name: 'Design', icon: PaintBrushIcon },
                { id: 'links', name: 'Links', icon: LinkIcon },
                { id: 'settings', name: 'Settings', icon: CogIcon },
                { id: 'analytics', name: 'Analytics', icon: ChartBarIcon }
              ].map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                    activeTab === tab.id
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                      : 'border-transparent text-secondary hover:text-primary'
                  }`}
                >
                  <tab.icon className="h-4 w-4 mr-2" />
                  {tab.name}
                </button>
              ))}
            </nav>
          </div>
          
          {/* Tab Content */}
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            {activeTab === 'design' && (
              <div className="space-y-6">
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Profile Information</h3>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Title</label>
                      <input
                        type="text"
                        value={bioSite.title}
                        onChange={(e) => setBioSite({ ...bioSite, title: e.target.value })}
                        className="input"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Avatar URL</label>
                      <input
                        type="url"
                        value={bioSite.avatar}
                        onChange={(e) => setBioSite({ ...bioSite, avatar: e.target.value })}
                        className="input"
                      />
                    </div>
                    <div className="md:col-span-2">
                      <label className="block text-sm font-medium text-secondary mb-2">Description</label>
                      <textarea
                        value={bioSite.description}
                        onChange={(e) => setBioSite({ ...bioSite, description: e.target.value })}
                        rows={3}
                        className="input"
                      />
                    </div>
                  </div>
                </div>
                
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Theme</h3>
                  <div className="grid grid-cols-3 gap-3">
                    {themes.map((theme) => (
                      <button
                        key={theme.id}
                        onClick={() => setBioSite({ ...bioSite, theme: theme.id })}
                        className={`p-3 rounded-lg border transition-all ${
                          bioSite.theme === theme.id
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                            : 'border-default hover:bg-surface-hover'
                        }`}
                      >
                        <div className={`w-full h-12 rounded mb-2 ${theme.preview}`}></div>
                        <div className="text-sm font-medium text-primary">{theme.name}</div>
                      </button>
                    ))}
                  </div>
                </div>
                
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Background</h3>
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                    {backgrounds.map((bg, index) => (
                      <button
                        key={index}
                        onClick={() => setBioSite({ 
                          ...bioSite, 
                          backgroundType: bg.type, 
                          backgroundValue: bg.value 
                        })}
                        className={`p-3 rounded-lg border transition-all ${
                          bioSite.backgroundValue === bg.value
                            ? 'border-blue-500'
                            : 'border-default hover:bg-surface-hover'
                        }`}
                      >
                        <div 
                          className="w-full h-12 rounded mb-2"
                          style={{ 
                            background: bg.type === 'gradient' ? bg.value : bg.value 
                          }}
                        ></div>
                        <div className="text-xs font-medium text-primary">{bg.name}</div>
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            )}
            
            {activeTab === 'links' && (
              <div className="space-y-6">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-primary">Manage Links</h3>
                  <button
                    onClick={() => setShowLinkModal(true)}
                    className="btn btn-primary"
                  >
                    <PlusIcon className="h-4 w-4 mr-2" />
                    Add Link
                  </button>
                </div>
                
                <DndContext 
                  sensors={sensors}
                  collisionDetection={closestCenter}
                  onDragEnd={handleDragEnd}
                >
                  <SortableContext items={links.map(link => link.id)} strategy={verticalListSortingStrategy}>
                    <div className="space-y-3">
                      {links.map((link) => (
                        <SortableLink key={link.id} link={link} />
                      ))}
                    </div>
                  </SortableContext>
                </DndContext>
                
                {links.length === 0 && (
                  <div className="text-center py-12 border border-dashed border-default rounded-lg">
                    <LinkIcon className="h-12 w-12 mx-auto mb-4 text-gray-400" />
                    <h3 className="text-lg font-medium text-primary">No links yet</h3>
                    <p className="text-secondary mb-4">Add your first link to get started</p>
                    <button
                      onClick={() => setShowLinkModal(true)}
                      className="btn btn-primary"
                    >
                      <PlusIcon className="h-4 w-4 mr-2" />
                      Add Link
                    </button>
                  </div>
                )}
              </div>
            )}
            
            {activeTab === 'analytics' && (
              <div className="space-y-6">
                <h3 className="text-lg font-semibold text-primary">Analytics Overview</h3>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div className="text-center p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <EyeIcon className="h-8 w-8 mx-auto mb-2 text-blue-600" />
                    <div className="text-2xl font-bold text-primary">{analytics.totalViews.toLocaleString()}</div>
                    <div className="text-sm text-secondary">Total Views</div>
                  </div>
                  <div className="text-center p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
                    <ArrowTopRightOnSquareIcon className="h-8 w-8 mx-auto mb-2 text-green-600" />
                    <div className="text-2xl font-bold text-primary">{analytics.totalClicks.toLocaleString()}</div>
                    <div className="text-sm text-secondary">Total Clicks</div>
                  </div>
                  <div className="text-center p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20">
                    <ChartBarIcon className="h-8 w-8 mx-auto mb-2 text-purple-600" />
                    <div className="text-2xl font-bold text-primary">{analytics.conversionRate}%</div>
                    <div className="text-sm text-secondary">Conversion Rate</div>
                  </div>
                </div>
                
                <div>
                  <h4 className="font-medium text-primary mb-4">Top Performing Links</h4>
                  <div className="space-y-3">
                    {analytics.topLinks.map((link, index) => (
                      <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-surface border border-default">
                        <div>
                          <div className="font-medium text-primary">{link.title}</div>
                          <div className="text-sm text-secondary">{link.clicks} clicks</div>
                        </div>
                        <div className="text-right">
                          <div className="font-bold text-primary">{link.percentage}%</div>
                          <div className="text-sm text-secondary">of total</div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
        
        {/* Live Preview */}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-primary">Live Preview</h3>
            <div className="flex space-x-2">
              <button
                onClick={() => setPreviewMode('mobile')}
                className={`p-2 rounded ${previewMode === 'mobile' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900' : 'text-secondary'}`}
              >
                <DevicePhoneMobileIcon className="h-5 w-5" />
              </button>
              <button
                onClick={() => setPreviewMode('desktop')}
                className={`p-2 rounded ${previewMode === 'desktop' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900' : 'text-secondary'}`}
              >
                <ComputerDesktopIcon className="h-5 w-5" />
              </button>
            </div>
          </div>
          
          <div className={`mx-auto bg-gray-100 dark:bg-gray-800 rounded-xl p-4 ${
            previewMode === 'mobile' ? 'max-w-sm' : 'max-w-full'
          }`}>
            <div 
              className="rounded-lg p-6 min-h-[600px] text-white relative overflow-hidden"
              style={{ 
                background: bioSite.backgroundType === 'gradient' 
                  ? bioSite.backgroundValue 
                  : bioSite.backgroundValue 
              }}
            >
              {/* Profile Section */}
              <div className="text-center mb-8">
                {bioSite.avatar && (
                  <img 
                    src={bioSite.avatar} 
                    alt={bioSite.title}
                    className="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-white/20"
                  />
                )}
                <h2 className="text-2xl font-bold mb-2">{bioSite.title}</h2>
                <p className="text-white/80 mb-6">{bioSite.description}</p>
              </div>
              
              {/* Links Section */}
              <div className="space-y-3">
                {links.filter(link => link.isActive).map((link) => (
                  <div 
                    key={link.id}
                    className="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-4 hover:bg-white/20 transition-all cursor-pointer"
                  >
                    <div className="flex items-center">
                      {link.thumbnail && (
                        <img 
                          src={link.thumbnail} 
                          alt=""
                          className="w-12 h-12 rounded-lg mr-4"
                        />
                      )}
                      <div className="flex-1">
                        <div className="font-medium">{link.title}</div>
                        {link.subtitle && (
                          <div className="text-sm text-white/60">{link.subtitle}</div>
                        )}
                      </div>
                      <ArrowTopRightOnSquareIcon className="h-5 w-5 text-white/60" />
                    </div>
                  </div>
                ))}
              </div>
              
              {links.filter(link => link.isActive).length === 0 && (
                <div className="text-center text-white/60 py-12">
                  <LinkIcon className="h-12 w-12 mx-auto mb-4" />
                  <p>No active links to display</p>
                </div>
              )}
            </div>
          </div>
          
          <div className="flex space-x-2">
            <button
              onClick={saveBioSite}
              className="btn btn-secondary flex-1"
            >
              Save Changes
            </button>
            <button className="btn btn-primary">
              <EyeIcon className="h-4 w-4 mr-2" />
              View Live
            </button>
          </div>
        </div>
      </div>
      
      {/* Link Modal */}
      <LinkModal
        isOpen={showLinkModal || editingLink !== null}
        onClose={() => {
          setShowLinkModal(false);
          setEditingLink(null);
        }}
        onSubmit={editingLink ? updateLink : addLink}
        editData={editingLink}
      />
    </div>
  );
};

export default ProfessionalLinkInBioBuilder;