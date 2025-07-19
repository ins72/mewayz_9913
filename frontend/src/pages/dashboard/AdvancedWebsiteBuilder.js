import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  PlusIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  Cog6ToothIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  ShareIcon,
  GlobeAltIcon,
  PhotoIcon,
  CursorArrowRaysIcon,
  Squares2X2Icon,
  RectangleGroupIcon,
  DocumentTextIcon,
  PlayIcon,
  ShoppingCartIcon,
  EnvelopeIcon,
  CalendarIcon,
  MapPinIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/react/24/outline';

const AdvancedWebsiteBuilder = () => {
  const [activeTab, setActiveTab] = useState('design');
  const [previewMode, setPreviewMode] = useState('desktop');
  const [selectedElement, setSelectedElement] = useState(null);
  const [website, setWebsite] = useState({
    id: '1',
    name: 'My Professional Website',
    domain: 'mywebsite.mewayz.com',
    customDomain: '',
    published: false,
    template: 'modern-business',
    theme: {
      primaryColor: '#3b82f6',
      secondaryColor: '#64748b',
      backgroundColor: '#ffffff',
      textColor: '#1f2937',
      fontFamily: 'Inter'
    },
    seo: {
      title: 'My Professional Website',
      description: 'Professional website built with Mewayz',
      keywords: 'business, professional, website',
      favicon: ''
    }
  });

  const [pages, setPages] = useState([
    {
      id: 'home',
      name: 'Home',
      slug: '/',
      isActive: true,
      elements: [
        {
          id: '1',
          type: 'hero',
          content: {
            title: 'Welcome to My Business',
            subtitle: 'Professional services for modern entrepreneurs',
            buttonText: 'Get Started',
            buttonLink: '/contact',
            backgroundImage: 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1920&h=1080&fit=crop',
            overlay: true
          },
          styles: {
            minHeight: '80vh',
            textAlign: 'center',
            padding: '120px 20px'
          }
        },
        {
          id: '2',
          type: 'features',
          content: {
            title: 'Our Services',
            subtitle: 'What we offer to help your business grow',
            features: [
              {
                icon: 'chart',
                title: 'Analytics & Insights',
                description: 'Get detailed insights into your business performance'
              },
              {
                icon: 'shield',
                title: 'Secure & Reliable',
                description: 'Your data is protected with enterprise-grade security'
              },
              {
                icon: 'support',
                title: '24/7 Support',
                description: 'Our team is here to help you succeed'
              }
            ]
          }
        },
        {
          id: '3',
          type: 'contact',
          content: {
            title: 'Get In Touch',
            subtitle: 'Ready to take your business to the next level?',
            email: 'contact@mybusiness.com',
            phone: '+1 (555) 123-4567',
            address: '123 Business Ave, Suite 100\nNew York, NY 10001'
          }
        }
      ]
    },
    {
      id: 'about',
      name: 'About',
      slug: '/about',
      isActive: false,
      elements: []
    },
    {
      id: 'services',
      name: 'Services',
      slug: '/services',
      isActive: false,
      elements: []
    },
    {
      id: 'contact',
      name: 'Contact',
      slug: '/contact',
      isActive: false,
      elements: []
    }
  ]);

  const [availableElements] = useState([
    {
      type: 'hero',
      name: 'Hero Section',
      icon: RectangleGroupIcon,
      description: 'Large banner with title, subtitle, and call-to-action'
    },
    {
      type: 'text',
      name: 'Text Block',
      icon: DocumentTextIcon,
      description: 'Rich text content with formatting options'
    },
    {
      type: 'image',
      name: 'Image',
      icon: PhotoIcon,
      description: 'Single image with caption and styling options'
    },
    {
      type: 'gallery',
      name: 'Image Gallery',
      icon: Squares2X2Icon,
      description: 'Grid of images with lightbox functionality'
    },
    {
      type: 'video',
      name: 'Video',
      icon: PlayIcon,
      description: 'Embedded video player with autoplay options'
    },
    {
      type: 'features',
      name: 'Features Grid',
      icon: RectangleGroupIcon,
      description: 'Grid of features with icons and descriptions'
    },
    {
      type: 'testimonials',
      name: 'Testimonials',
      icon: ChatBubbleLeftRightIcon,
      description: 'Customer reviews and testimonials slider'
    },
    {
      type: 'contact',
      name: 'Contact Form',
      icon: EnvelopeIcon,
      description: 'Contact form with validation and email integration'
    },
    {
      type: 'map',
      name: 'Map',
      icon: MapPinIcon,
      description: 'Interactive map with location markers'
    },
    {
      type: 'pricing',
      name: 'Pricing Table',
      icon: ShoppingCartIcon,
      description: 'Pricing plans with feature comparison'
    },
    {
      type: 'calendar',
      name: 'Booking Calendar',
      icon: CalendarIcon,
      description: 'Appointment booking with calendar integration'
    }
  ]);

  const [templates] = useState([
    {
      id: 'modern-business',
      name: 'Modern Business',
      category: 'Business',
      preview: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=300&fit=crop',
      description: 'Clean and professional business template'
    },
    {
      id: 'creative-portfolio',
      name: 'Creative Portfolio',
      category: 'Portfolio',
      preview: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=300&fit=crop',
      description: 'Showcase your creative work beautifully'
    },
    {
      id: 'restaurant',
      name: 'Restaurant',
      category: 'Food & Dining',
      preview: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=300&fit=crop',
      description: 'Perfect for restaurants and food businesses'
    },
    {
      id: 'fitness',
      name: 'Fitness Studio',
      category: 'Health & Fitness',
      preview: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
      description: 'Dynamic template for fitness professionals'
    },
    {
      id: 'ecommerce',
      name: 'E-commerce Store',
      category: 'E-commerce',
      preview: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=300&fit=crop',
      description: 'Complete online store with shopping cart'
    },
    {
      id: 'photography',
      name: 'Photography',
      category: 'Creative',
      preview: 'https://images.unsplash.com/photo-1452457807411-4979b707c5be?w=400&h=300&fit=crop',
      description: 'Stunning gallery for photographers'
    }
  ]);

  const getActivePage = () => {
    return pages.find(page => page.isActive) || pages[0];
  };

  const addElement = (elementType) => {
    const activePage = getActivePage();
    const newElement = {
      id: Date.now().toString(),
      type: elementType,
      content: getDefaultContent(elementType),
      styles: getDefaultStyles(elementType)
    };
    
    const updatedPages = pages.map(page =>
      page.id === activePage.id
        ? { ...page, elements: [...page.elements, newElement] }
        : page
    );
    
    setPages(updatedPages);
  };

  const getDefaultContent = (type) => {
    const defaults = {
      hero: {
        title: 'Your Hero Title',
        subtitle: 'Your compelling subtitle here',
        buttonText: 'Call to Action',
        buttonLink: '#',
        backgroundImage: '',
        overlay: true
      },
      text: {
        content: '<p>Your text content goes here. You can format it with <strong>bold</strong>, <em>italic</em>, and other formatting options.</p>'
      },
      image: {
        src: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
        alt: 'Your image description',
        caption: 'Image caption'
      },
      features: {
        title: 'Features Title',
        subtitle: 'Features subtitle',
        features: [
          { icon: 'star', title: 'Feature 1', description: 'Feature description' },
          { icon: 'star', title: 'Feature 2', description: 'Feature description' },
          { icon: 'star', title: 'Feature 3', description: 'Feature description' }
        ]
      },
      contact: {
        title: 'Contact Us',
        subtitle: 'Get in touch with us',
        email: 'contact@example.com',
        phone: '+1 (555) 123-4567',
        address: 'Your business address'
      }
    };
    
    return defaults[type] || {};
  };

  const getDefaultStyles = (type) => {
    const defaults = {
      hero: {
        minHeight: '60vh',
        textAlign: 'center',
        padding: '80px 20px'
      },
      text: {
        padding: '40px 20px',
        maxWidth: '800px',
        margin: '0 auto'
      },
      image: {
        padding: '20px',
        textAlign: 'center'
      },
      features: {
        padding: '80px 20px'
      },
      contact: {
        padding: '80px 20px'
      }
    };
    
    return defaults[type] || { padding: '20px' };
  };

  const updateElement = (elementId, updates) => {
    const activePage = getActivePage();
    const updatedPages = pages.map(page =>
      page.id === activePage.id
        ? {
            ...page,
            elements: page.elements.map(element =>
              element.id === elementId ? { ...element, ...updates } : element
            )
          }
        : page
    );
    
    setPages(updatedPages);
  };

  const deleteElement = (elementId) => {
    const activePage = getActivePage();
    const updatedPages = pages.map(page =>
      page.id === activePage.id
        ? {
            ...page,
            elements: page.elements.filter(element => element.id !== elementId)
          }
        : page
    );
    
    setPages(updatedPages);
    setSelectedElement(null);
  };

  const ElementPreview = ({ element }) => {
    const getPreviewContent = () => {
      switch (element.type) {
        case 'hero':
          return (
            <div 
              className="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white flex items-center justify-center"
              style={{ minHeight: '200px' }}
            >
              {element.content.overlay && (
                <div className="absolute inset-0 bg-black/40"></div>
              )}
              <div className="relative text-center z-10">
                <h1 className="text-2xl font-bold mb-2">{element.content.title}</h1>
                <p className="mb-4">{element.content.subtitle}</p>
                <button className="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium">
                  {element.content.buttonText}
                </button>
              </div>
            </div>
          );
        case 'text':
          return (
            <div className="p-6">
              <div dangerouslySetInnerHTML={{ __html: element.content.content }} />
            </div>
          );
        case 'image':
          return (
            <div className="p-6 text-center">
              <img 
                src={element.content.src} 
                alt={element.content.alt}
                className="max-w-full h-32 object-cover rounded-lg mx-auto mb-2"
              />
              {element.content.caption && (
                <p className="text-sm text-secondary">{element.content.caption}</p>
              )}
            </div>
          );
        case 'features':
          return (
            <div className="p-6">
              <h2 className="text-xl font-bold text-center mb-2">{element.content.title}</h2>
              <p className="text-secondary text-center mb-4">{element.content.subtitle}</p>
              <div className="grid grid-cols-3 gap-4">
                {element.content.features?.slice(0, 3).map((feature, index) => (
                  <div key={index} className="text-center">
                    <div className="w-8 h-8 bg-blue-600 rounded-lg mx-auto mb-2"></div>
                    <h3 className="text-sm font-medium">{feature.title}</h3>
                  </div>
                ))}
              </div>
            </div>
          );
        case 'contact':
          return (
            <div className="p-6">
              <h2 className="text-xl font-bold text-center mb-2">{element.content.title}</h2>
              <p className="text-secondary text-center mb-4">{element.content.subtitle}</p>
              <div className="space-y-2 text-sm">
                <div>📧 {element.content.email}</div>
                <div>📞 {element.content.phone}</div>
                <div>📍 {element.content.address}</div>
              </div>
            </div>
          );
        default:
          return (
            <div className="p-6 text-center text-secondary">
              <div className="text-2xl mb-2">📄</div>
              <div className="text-sm">{element.type} element</div>
            </div>
          );
      }
    };

    return (
      <motion.div
        layout
        className={`bg-card border rounded-lg overflow-hidden cursor-pointer transition-all ${
          selectedElement?.id === element.id 
            ? 'border-accent-primary ring-2 ring-accent-primary/20' 
            : 'border-default hover:border-accent-primary/50'
        }`}
        onClick={() => setSelectedElement(element)}
      >
        {getPreviewContent()}
        <div className="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            onClick={(e) => {
              e.stopPropagation();
              setSelectedElement(element);
            }}
            className="p-1 bg-black/50 backdrop-blur-sm rounded text-white hover:bg-black/70"
          >
            <PencilIcon className="w-3 h-3" />
          </button>
          <button
            onClick={(e) => {
              e.stopPropagation();
              deleteElement(element.id);
            }}
            className="p-1 bg-red-500/80 backdrop-blur-sm rounded text-white hover:bg-red-500"
          >
            <TrashIcon className="w-3 h-3" />
          </button>
        </div>
      </motion.div>
    );
  };

  const activePage = getActivePage();

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Website Builder</h1>
            <p className="text-secondary mt-2">Create professional websites with drag & drop</p>
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
            
            <button className="bg-surface border border-default text-primary px-4 py-2 rounded-lg hover:bg-hover transition-colors flex items-center space-x-2">
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
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          {/* Sidebar - Tools & Elements */}
          <div className="space-y-6">
            {/* Tabs */}
            <div className="flex flex-col space-y-1 bg-card border border-default rounded-lg p-1">
              {['design', 'pages', 'settings'].map(tab => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`px-3 py-2 rounded text-sm font-medium transition-colors ${
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
            <div className="bg-card border border-default rounded-xl p-4">
              {activeTab === 'design' && (
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Elements</h3>
                  <div className="space-y-2">
                    {availableElements.map(element => {
                      const IconComponent = element.icon;
                      return (
                        <button
                          key={element.type}
                          onClick={() => addElement(element.type)}
                          className="w-full flex items-center space-x-3 p-3 border border-default rounded-lg hover:border-accent-primary hover:bg-accent-primary/5 transition-all text-left"
                        >
                          <IconComponent className="w-5 h-5 text-accent-primary" />
                          <div className="flex-1 min-w-0">
                            <div className="text-sm font-medium text-primary">{element.name}</div>
                            <div className="text-xs text-secondary truncate">{element.description}</div>
                          </div>
                        </button>
                      );
                    })}
                  </div>
                </div>
              )}

              {activeTab === 'pages' && (
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Pages</h3>
                  <div className="space-y-2">
                    {pages.map(page => (
                      <div
                        key={page.id}
                        className={`p-3 border rounded-lg transition-all ${
                          page.isActive 
                            ? 'border-accent-primary bg-accent-primary/10' 
                            : 'border-default hover:border-accent-primary/50'
                        }`}
                      >
                        <div className="flex items-center justify-between">
                          <div>
                            <div className="text-sm font-medium text-primary">{page.name}</div>
                            <div className="text-xs text-secondary">{page.slug}</div>
                          </div>
                          <button
                            onClick={() => {
                              const updatedPages = pages.map(p => ({
                                ...p,
                                isActive: p.id === page.id
                              }));
                              setPages(updatedPages);
                            }}
                            className={`p-1 rounded ${
                              page.isActive ? 'text-accent-primary' : 'text-secondary hover:text-primary'
                            }`}
                          >
                            <EyeIcon className="w-4 h-4" />
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                  
                  <button className="w-full mt-4 p-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-secondary hover:border-accent-primary hover:text-accent-primary transition-colors flex items-center justify-center space-x-2">
                    <PlusIcon className="w-4 h-4" />
                    <span className="text-sm">Add Page</span>
                  </button>
                </div>
              )}

              {activeTab === 'settings' && (
                <div>
                  <h3 className="text-lg font-semibold text-primary mb-4">Website Settings</h3>
                  <div className="space-y-4">
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Site Name</label>
                      <input
                        type="text"
                        value={website.name}
                        onChange={(e) => setWebsite({ ...website, name: e.target.value })}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Custom Domain</label>
                      <input
                        type="text"
                        value={website.customDomain}
                        onChange={(e) => setWebsite({ ...website, customDomain: e.target.value })}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                        placeholder="www.yourdomain.com"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">SEO Title</label>
                      <input
                        type="text"
                        value={website.seo.title}
                        onChange={(e) => setWebsite({ 
                          ...website, 
                          seo: { ...website.seo, title: e.target.value }
                        })}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Meta Description</label>
                      <textarea
                        value={website.seo.description}
                        onChange={(e) => setWebsite({ 
                          ...website, 
                          seo: { ...website.seo, description: e.target.value }
                        })}
                        rows={3}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none resize-none"
                      />
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Main Canvas */}
          <div className="lg:col-span-3">
            <div className="bg-card border border-default rounded-xl overflow-hidden">
              {/* Canvas Header */}
              <div className="bg-surface border-b border-default p-4 flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <h3 className="font-semibold text-primary">{activePage.name}</h3>
                  <span className="text-sm text-secondary">{activePage.slug}</span>
                </div>
                
                <div className="flex items-center space-x-2 text-sm text-secondary">
                  <span>{activePage.elements.length} elements</span>
                </div>
              </div>
              
              {/* Canvas Content */}
              <div className={`${previewMode === 'mobile' ? 'max-w-sm' : 'w-full'} mx-auto bg-white min-h-96`}>
                {activePage.elements.length === 0 ? (
                  <div className="flex items-center justify-center h-96 text-center">
                    <div>
                      <CursorArrowRaysIcon className="w-16 h-16 mx-auto text-secondary mb-4 opacity-50" />
                      <h3 className="text-lg font-medium text-primary mb-2">Start Building</h3>
                      <p className="text-secondary mb-4">Add elements from the sidebar to get started</p>
                      <button
                        onClick={() => setActiveTab('design')}
                        className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity"
                      >
                        Browse Elements
                      </button>
                    </div>
                  </div>
                ) : (
                  <div className="space-y-0">
                    {activePage.elements.map(element => (
                      <div key={element.id} className="group relative">
                        <ElementPreview element={element} />
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Templates Modal would go here */}
        {/* Element Editor Panel would go here */}
      </div>
    </div>
  );
};

export default AdvancedWebsiteBuilder;