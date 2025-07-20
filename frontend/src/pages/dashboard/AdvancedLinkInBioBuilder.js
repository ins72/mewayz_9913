import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  PlusIcon, 
  TrashIcon, 
  PencilIcon,
  EyeIcon,
  LinkIcon,
  PhotoIcon,
  VideoCameraIcon,
  MusicalNoteIcon,
  ShoppingBagIcon,
  CalendarIcon,
  PhoneIcon,
  EnvelopeIcon,
  MapPinIcon,
  GlobeAltIcon,
  QrCodeIcon,
  ShareIcon,
  Cog6ToothIcon,
  SwatchIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  DocumentDuplicateIcon
} from '@heroicons/react/24/outline';
import { DndContext, closestCenter, KeyboardSensor, PointerSensor, useSensor, useSensors } from '@dnd-kit/core';
import { arrayMove, SortableContext, sortableKeyboardCoordinates, verticalListSortingStrategy } from '@dnd-kit/sortable';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

const AdvancedLinkInBioBuilder = () => {
  const [activeTab, setActiveTab] = useState('builder');
  const [previewMode, setPreviewMode] = useState('mobile');
  const [bioSite, setBioSite] = useState({
    id: '1',
    title: 'My Link in Bio',
    description: 'Welcome to my digital space',
    profileImage: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=face',
    backgroundColor: '#ffffff',
    backgroundImage: null,
    textColor: '#000000',
    accentColor: '#3b82f6',
    fontFamily: 'inter',
    customCSS: '',
    seo: {
      title: 'My Link in Bio',
      description: 'Welcome to my digital space',
      keywords: 'creator, links, social'
    },
    analytics: {
      enabled: true,
      trackClicks: true,
      trackViews: true
    }
  });
  const [links, setLinks] = useState([
    { id: '1', type: 'link', title: 'My Website', url: 'https://example.com', icon: 'globe', color: '#3b82f6', enabled: true },
    { id: '2', type: 'social', title: 'Instagram', url: 'https://instagram.com/user', icon: 'instagram', color: '#e1306c', enabled: true },
    { id: '3', type: 'link', title: 'My YouTube Channel', url: 'https://youtube.com/channel', icon: 'video', color: '#ff0000', enabled: true },
    { id: '4', type: 'contact', title: 'Email Me', url: 'mailto:hello@example.com', icon: 'email', color: '#10b981', enabled: true }
  ]);
  const [selectedLink, setSelectedLink] = useState(null);
  const [showLinkModal, setShowLinkModal] = useState(false);

  const sensors = useSensors(
    useSensor(PointerSensor),
    useSensor(KeyboardSensor, {
      coordinateGetter: sortableKeyboardCoordinates,
    })
  );

  const linkTypes = [
    { type: 'link', name: 'Website Link', icon: LinkIcon, color: '#3b82f6' },
    { type: 'social', name: 'Social Media', icon: ShareIcon, color: '#10b981' },
    { type: 'video', name: 'Video Content', icon: VideoCameraIcon, color: '#f59e0b' },
    { type: 'music', name: 'Music/Audio', icon: MusicalNoteIcon, color: '#8b5cf6' },
    { type: 'shop', name: 'Shop/Product', icon: ShoppingBagIcon, color: '#ef4444' },
    { type: 'event', name: 'Event/Calendar', icon: CalendarIcon, color: '#f97316' },
    { type: 'contact', name: 'Contact Info', icon: PhoneIcon, color: '#06b6d4' },
    { type: 'location', name: 'Location', icon: MapPinIcon, color: '#84cc16' }
  ];

  const themes = [
    { id: 'default', name: 'Clean White', bg: '#ffffff', text: '#000000', accent: '#3b82f6' },
    { id: 'dark', name: 'Dark Mode', bg: '#1f2937', text: '#ffffff', accent: '#60a5fa' },
    { id: 'gradient1', name: 'Ocean Gradient', bg: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', text: '#ffffff', accent: '#ffffff' },
    { id: 'gradient2', name: 'Sunset Gradient', bg: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', text: '#ffffff', accent: '#ffffff' },
    { id: 'minimal', name: 'Minimal Gray', bg: '#f9fafb', text: '#374151', accent: '#111827' },
    { id: 'neon', name: 'Neon Vibes', bg: '#000000', text: '#00ff00', accent: '#ff00ff' }
  ];

  const fonts = [
    { id: 'inter', name: 'Inter', family: 'Inter, sans-serif' },
    { id: 'poppins', name: 'Poppins', family: 'Poppins, sans-serif' },
    { id: 'playfair', name: 'Playfair Display', family: 'Playfair Display, serif' },
    { id: 'roboto', name: 'Roboto', family: 'Roboto, sans-serif' },
    { id: 'montserrat', name: 'Montserrat', family: 'Montserrat, sans-serif' }
  ];

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

  const addNewLink = (type) => {
    const newLink = {
      id: Date.now().toString(),
      type: type,
      title: `New ${type.charAt(0).toUpperCase() + type.slice(1)}`,
      url: '',
      icon: type,
      color: linkTypes.find(t => t.type === type)?.color || '#3b82f6',
      enabled: true
    };
    setLinks([...links, newLink]);
    setSelectedLink(newLink);
    setShowLinkModal(true);
  };

  const updateLink = (linkId, updates) => {
    setLinks(links.map(link => 
      link.id === linkId ? { ...link, ...updates } : link
    ));
  };

  const deleteLink = (linkId) => {
    setLinks(links.filter(link => link.id !== linkId));
  };

  const duplicateLink = (linkId) => {
    const linkToDuplicate = links.find(link => link.id === linkId);
    if (linkToDuplicate) {
      const duplicatedLink = {
        ...linkToDuplicate,
        id: Date.now().toString(),
        title: `${linkToDuplicate.title} (Copy)`
      };
      setLinks([...links, duplicatedLink]);
    }
  };

  const applyTheme = (theme) => {
    setBioSite({
      ...bioSite,
      backgroundColor: theme.bg,
      textColor: theme.text,
      accentColor: theme.accent
    });
  };

  // Sortable Link Component
  const SortableLink = ({ link }) => {
    const {
      attributes,
      listeners,
      setNodeRef,
      transform,
      transition,
      isDragging
    } = useSortable({ id: link.id });

    const style = {
      transform: CSS.Transform.toString(transform),
      transition,
      opacity: isDragging ? 0.5 : 1
    };

    const getIcon = (iconName) => {
      const icons = {
        globe: GlobeAltIcon,
        instagram: PhotoIcon,
        video: VideoCameraIcon,
        email: EnvelopeIcon,
        phone: PhoneIcon,
        location: MapPinIcon,
        music: MusicalNoteIcon,
        shop: ShoppingBagIcon,
        event: CalendarIcon
      };
      const IconComponent = icons[iconName] || LinkIcon;
      return <IconComponent className="w-4 h-4" />;
    };

    return (
      <div
        ref={setNodeRef}
        style={style}
        className="bg-card border border-default rounded-lg p-4 mb-3 hover:shadow-md transition-all"
      >
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3 flex-1">
            <div
              {...attributes}
              {...listeners}
              className="cursor-grab hover:cursor-grabbing"
            >
              <div className="w-2 h-8 bg-gray-300 dark:bg-gray-600 rounded flex flex-col justify-center">
                <div className="w-0.5 h-2 bg-gray-500 dark:bg-gray-400 rounded mx-auto mb-0.5"></div>
                <div className="w-0.5 h-2 bg-gray-500 dark:bg-gray-400 rounded mx-auto"></div>
              </div>
            </div>
            
            <div 
              className="p-2 rounded-lg"
              style={{ backgroundColor: link.color + '20', color: link.color }}
            >
              {getIcon(link.icon)}
            </div>
            
            <div className="flex-1 min-w-0">
              <h4 className="text-sm font-medium text-primary truncate">{link.title}</h4>
              <p className="text-xs text-secondary truncate">{link.url}</p>
            </div>
          </div>

          <div className="flex items-center space-x-2">
            <button
              onClick={() => updateLink(link.id, { enabled: !link.enabled })}
              className={`p-1 rounded ${link.enabled ? 'text-green-600' : 'text-gray-400'}`}
            >
              <EyeIcon className="w-4 h-4" />
            </button>
            <button
              onClick={() => {
                setSelectedLink(link);
                setShowLinkModal(true);
              }}
              className="p-1 text-blue-600 hover:text-blue-800 rounded"
            >
              <PencilIcon className="w-4 h-4" />
            </button>
            <button
              onClick={() => duplicateLink(link.id)}
              className="p-1 text-gray-600 hover:text-gray-800 rounded"
            >
                <DocumentDuplicateIcon className="w-4 h-4" />
            </button>
            <button
              onClick={() => deleteLink(link.id)}
              className="p-1 text-red-600 hover:text-red-800 rounded"
            >
              <TrashIcon className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    );
  };

  // Link Modal Component
  const LinkModal = () => {
    if (!selectedLink) return null;

    return (
      <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-card border border-default rounded-xl p-6 w-full max-w-md mx-4"
        >
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold text-primary">Edit Link</h3>
            <button
              onClick={() => setShowLinkModal(false)}
              className="text-secondary hover:text-primary"
            >
              ✕
            </button>
          </div>

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Title</label>
              <input
                type="text"
                value={selectedLink.title}
                onChange={(e) => setSelectedLink({ ...selectedLink, title: e.target.value })}
                className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-2">URL</label>
              <input
                type="url"
                value={selectedLink.url}
                onChange={(e) => setSelectedLink({ ...selectedLink, url: e.target.value })}
                className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                placeholder="https://example.com"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Color</label>
              <div className="flex items-center space-x-2">
                <input
                  type="color"
                  value={selectedLink.color}
                  onChange={(e) => setSelectedLink({ ...selectedLink, color: e.target.value })}
                  className="w-12 h-10 border border-default rounded-lg"
                />
                <input
                  type="text"
                  value={selectedLink.color}
                  onChange={(e) => setSelectedLink({ ...selectedLink, color: e.target.value })}
                  className="flex-1 px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                />
              </div>
            </div>
          </div>

          <div className="flex space-x-3 mt-6">
            <button
              onClick={() => {
                updateLink(selectedLink.id, selectedLink);
                setShowLinkModal(false);
                setSelectedLink(null);
              }}
              className="flex-1 bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity"
            >
              Save Changes
            </button>
            <button
              onClick={() => setShowLinkModal(false)}
              className="px-4 py-2 border border-default rounded-lg hover:bg-hover transition-colors text-secondary"
            >
              Cancel
            </button>
          </div>
        </motion.div>
      </div>
    );
  };

  // Mobile Preview Component
  const MobilePreview = () => (
    <div className="w-full max-w-sm mx-auto">
      <div className="relative">
        {/* Phone Frame */}
        <div className="bg-gray-900 rounded-3xl p-2">
          <div className="bg-black rounded-2xl overflow-hidden">
            {/* Status Bar */}
            <div className="h-6 bg-black flex items-center justify-between px-4 text-white text-xs">
              <span>9:41</span>
              <span>●●●●●</span>
            </div>
            
            {/* Content Area */}
            <div 
              className="min-h-[600px] overflow-y-auto"
              style={{ 
                background: bioSite.backgroundColor,
                color: bioSite.textColor,
                fontFamily: fonts.find(f => f.id === bioSite.fontFamily)?.family || 'Inter, sans-serif'
              }}
            >
              <div className="p-6 text-center">
                {/* Profile Section */}
                <div className="mb-6">
                  <img
                    src={bioSite.profileImage}
                    alt="Profile"
                    className="w-24 h-24 rounded-full mx-auto mb-4 object-cover"
                  />
                  <h1 className="text-xl font-bold mb-2">{bioSite.title}</h1>
                  <p className="text-sm opacity-80">{bioSite.description}</p>
                </div>

                {/* Links */}
                <div className="space-y-3">
                  {links.filter(link => link.enabled).map(link => (
                    <div
                      key={link.id}
                      className="w-full p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:opacity-80 transition-opacity cursor-pointer"
                      style={{ 
                        backgroundColor: link.color + '10',
                        borderColor: link.color + '30'
                      }}
                    >
                      <div className="flex items-center justify-center space-x-2">
                        <div style={{ color: link.color }}>
                          {React.createElement(LinkIcon, { className: "w-5 h-5" })}
                        </div>
                        <span className="font-medium">{link.title}</span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Link in Bio Builder</h1>
            <p className="text-secondary mt-2">Create your personalized link in bio page with drag & drop</p>
          </div>
          
          <div className="flex items-center space-x-4">
            <div className="flex items-center space-x-2 bg-card border border-default rounded-lg p-1">
              <button
                onClick={() => setPreviewMode('mobile')}
                className={`p-2 rounded ${previewMode === 'mobile' ? 'bg-accent-primary text-white' : 'text-secondary hover:text-primary'}`}
              >
                <DevicePhoneMobileIcon className="w-4 h-4" />
              </button>
              <button
                onClick={() => setPreviewMode('desktop')}
                className={`p-2 rounded ${previewMode === 'desktop' ? 'bg-accent-primary text-white' : 'text-secondary hover:text-primary'}`}
              >
                <ComputerDesktopIcon className="w-4 h-4" />
              </button>
            </div>
            
            <button className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2">
              <EyeIcon className="w-4 h-4" />
              <span>Preview</span>
            </button>
            
            <button className="bg-green-600 text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2">
              <ShareIcon className="w-4 h-4" />
              <span>Publish</span>
            </button>
          </div>
        </div>

        {/* Main Content */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Builder Panel */}
          <div className="space-y-6">
            {/* Tabs */}
            <div className="flex space-x-1 bg-card border border-default rounded-lg p-1">
              {['builder', 'design', 'settings', 'analytics'].map(tab => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`flex-1 px-3 py-2 rounded text-sm font-medium transition-colors ${
                    activeTab === tab 
                      ? 'bg-accent-primary text-white' 
                      : 'text-secondary hover:text-primary'
                  }`}
                >
                  {tab.charAt(0).toUpperCase() + tab.slice(1)}
                </button>
              ))}
            </div>

            {/* Tab Content */}
            <div className="bg-card border border-default rounded-xl p-6">
              {activeTab === 'builder' && (
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <h3 className="text-lg font-semibold text-primary">Your Links</h3>
                    <div className="relative">
                      <select
                        onChange={(e) => addNewLink(e.target.value)}
                        value=""
                        className="appearance-none bg-accent-primary text-white px-4 py-2 rounded-lg pr-8 text-sm font-medium hover:opacity-90 transition-opacity"
                      >
                        <option value="" disabled>+ Add Link</option>
                        {linkTypes.map(type => (
                          <option key={type.type} value={type.type}>
                            {type.name}
                          </option>
                        ))}
                      </select>
                      <PlusIcon className="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white pointer-events-none" />
                    </div>
                  </div>

                  <DndContext
                    sensors={sensors}
                    collisionDetection={closestCenter}
                    onDragEnd={handleDragEnd}
                  >
                    <SortableContext items={links} strategy={verticalListSortingStrategy}>
                      {links.map(link => (
                        <SortableLink key={link.id} link={link} />
                      ))}
                    </SortableContext>
                  </DndContext>

                  {links.length === 0 && (
                    <div className="text-center py-8 text-secondary">
                      <LinkIcon className="w-12 h-12 mx-auto mb-4 opacity-50" />
                      <p>No links added yet. Click "Add Link" to get started!</p>
                    </div>
                  )}
                </div>
              )}

              {activeTab === 'design' && (
                <div className="space-y-6">
                  <div>
                    <h3 className="text-lg font-semibold text-primary mb-4">Profile Settings</h3>
                    <div className="space-y-4">
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Title</label>
                        <input
                          type="text"
                          value={bioSite.title}
                          onChange={(e) => setBioSite({ ...bioSite, title: e.target.value })}
                          className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Description</label>
                        <textarea
                          value={bioSite.description}
                          onChange={(e) => setBioSite({ ...bioSite, description: e.target.value })}
                          rows={3}
                          className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                        />
                      </div>
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-semibold text-primary mb-4">Theme</h3>
                    <div className="grid grid-cols-2 gap-3">
                      {themes.map(theme => (
                        <button
                          key={theme.id}
                          onClick={() => applyTheme(theme)}
                          className="p-3 border border-default rounded-lg text-left hover:border-accent-primary transition-colors"
                        >
                          <div
                            className="w-full h-8 rounded mb-2"
                            style={{ background: theme.bg }}
                          ></div>
                          <p className="text-sm font-medium text-primary">{theme.name}</p>
                        </button>
                      ))}
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-semibold text-primary mb-4">Font</h3>
                    <select
                      value={bioSite.fontFamily}
                      onChange={(e) => setBioSite({ ...bioSite, fontFamily: e.target.value })}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                    >
                      {fonts.map(font => (
                        <option key={font.id} value={font.id}>
                          {font.name}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>
              )}

              {activeTab === 'settings' && (
                <div className="space-y-6">
                  <div>
                    <h3 className="text-lg font-semibold text-primary mb-4">SEO Settings</h3>
                    <div className="space-y-4">
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Meta Title</label>
                        <input
                          type="text"
                          value={bioSite.seo.title}
                          onChange={(e) => setBioSite({ 
                            ...bioSite, 
                            seo: { ...bioSite.seo, title: e.target.value }
                          })}
                          className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Meta Description</label>
                        <textarea
                          value={bioSite.seo.description}
                          onChange={(e) => setBioSite({ 
                            ...bioSite, 
                            seo: { ...bioSite.seo, description: e.target.value }
                          })}
                          rows={3}
                          className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                        />
                      </div>
                    </div>
                  </div>

                  <div>
                    <h3 className="text-lg font-semibold text-primary mb-4">Analytics</h3>
                    <div className="space-y-3">
                      <label className="flex items-center space-x-2">
                        <input
                          type="checkbox"
                          checked={bioSite.analytics.enabled}
                          onChange={(e) => setBioSite({
                            ...bioSite,
                            analytics: { ...bioSite.analytics, enabled: e.target.checked }
                          })}
                          className="rounded border-default text-accent-primary focus:ring-2 focus:ring-accent-primary/20"
                        />
                        <span className="text-sm text-primary">Enable analytics tracking</span>
                      </label>
                      <label className="flex items-center space-x-2">
                        <input
                          type="checkbox"
                          checked={bioSite.analytics.trackClicks}
                          onChange={(e) => setBioSite({
                            ...bioSite,
                            analytics: { ...bioSite.analytics, trackClicks: e.target.checked }
                          })}
                          className="rounded border-default text-accent-primary focus:ring-2 focus:ring-accent-primary/20"
                        />
                        <span className="text-sm text-primary">Track link clicks</span>
                      </label>
                    </div>
                  </div>
                </div>
              )}

              {activeTab === 'analytics' && (
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Analytics Overview</h3>
                  <div className="grid grid-cols-2 gap-4 mb-6">
                    <div className="bg-surface border border-default rounded-lg p-4 text-center">
                      <div className="text-2xl font-bold text-primary">1,247</div>
                      <div className="text-sm text-secondary">Total Views</div>
                    </div>
                    <div className="bg-surface border border-default rounded-lg p-4 text-center">
                      <div className="text-2xl font-bold text-primary">892</div>
                      <div className="text-sm text-secondary">Total Clicks</div>
                    </div>
                  </div>
                  <div className="space-y-3">
                    <h4 className="font-medium text-primary">Top Performing Links</h4>
                    {links.slice(0, 3).map((link, index) => (
                      <div key={link.id} className="flex items-center justify-between p-3 bg-surface border border-default rounded-lg">
                        <span className="text-sm text-primary">{link.title}</span>
                        <span className="text-sm font-medium text-secondary">{Math.floor(Math.random() * 100) + 50} clicks</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Preview Panel */}
          <div className="bg-card border border-default rounded-xl p-6">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-lg font-semibold text-primary">Live Preview</h3>
              <div className="flex items-center space-x-2">
                <button className="p-2 text-secondary hover:text-primary rounded-lg transition-colors">
                  <QrCodeIcon className="w-5 h-5" />
                </button>
                <button className="p-2 text-secondary hover:text-primary rounded-lg transition-colors">
                  <ShareIcon className="w-5 h-5" />
                </button>
              </div>
            </div>
            
            <div className="flex justify-center">
              <MobilePreview />
            </div>
          </div>
        </div>
      </div>

      {/* Link Modal */}
      <AnimatePresence>
        {showLinkModal && <LinkModal />}
      </AnimatePresence>
    </div>
  );
};

export default AdvancedLinkInBioBuilder;