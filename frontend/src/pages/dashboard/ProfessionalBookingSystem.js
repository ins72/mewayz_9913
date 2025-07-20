import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CalendarDaysIcon,
  ClockIcon,
  UserIcon,
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  CreditCardIcon,
  CheckCircleIcon,
  XMarkIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  QrCodeIcon,
  ShareIcon,
  BellIcon,
  CogIcon,
  ChartBarIcon,
  CalendarIcon,
  VideoCameraIcon,
  BuildingOfficeIcon,
  GlobeAltIcon,
  DocumentTextIcon,
  UserGroupIcon,
  StarIcon,
  HeartIcon,
  EyeIcon,
  AcademicCapIcon
} from '@heroicons/react/24/outline';
import {
  CalendarDaysIcon as CalendarDaysIconSolid,
  CheckCircleIcon as CheckCircleIconSolid,
  ClockIcon as ClockIconSolid
} from '@heroicons/react/24/solid';

const ProfessionalBookingSystem = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('calendar');
  const [selectedDate, setSelectedDate] = useState(new Date());
  const [selectedService, setSelectedService] = useState(null);
  const [showServiceModal, setShowServiceModal] = useState(false);
  const [showBookingModal, setShowBookingModal] = useState(false);
  const [viewMode, setViewMode] = useState('month');
  
  const [services, setServices] = useState([]);
  const [bookings, setBookings] = useState([]);
  const [availability, setAvailability] = useState({});
  const [customers, setCustomers] = useState([]);
  const [staff, setStaff] = useState([]);
  
  // Service categories
  const serviceCategories = [
    { id: 'consultation', name: 'Consultation', icon: UserIcon, color: 'blue' },
    { id: 'coaching', name: 'Coaching', icon: UserGroupIcon, color: 'green' },
    { id: 'workshop', name: 'Workshop', icon: AcademicCapIcon, color: 'purple' },
    { id: 'meeting', name: 'Meeting', icon: VideoCameraIcon, color: 'orange' },
    { id: 'therapy', name: 'Therapy', icon: HeartIcon, color: 'pink' },
    { id: 'training', name: 'Training', icon: DocumentTextIcon, color: 'indigo' }
  ];
  
  // Mock services
  const mockServices = [
    {
      id: '1',
      name: 'Business Strategy Consultation',
      description: 'One-on-one consultation to discuss your business strategy and growth opportunities.',
      duration: 60,
      price: 150.00,
      category: 'consultation',
      location: 'Online via Zoom',
      isActive: true,
      bookingBuffer: 15,
      maxAdvanceBooking: 30,
      cancellationPolicy: '24 hours notice required',
      staff: ['staff-1'],
      color: '#3B82F6',
      image: 'https://ui-avatars.com/api/?name=Business+Strategy&background=3B82F6&color=fff',
      features: [
        'Detailed business analysis',
        'Growth strategy recommendations',
        'Action plan with milestones',
        'Follow-up email summary'
      ]
    },
    {
      id: '2',
      name: 'Digital Marketing Workshop',
      description: 'Comprehensive workshop covering social media, SEO, and content marketing strategies.',
      duration: 120,
      price: 89.00,
      category: 'workshop',
      location: 'Conference Room A',
      isActive: true,
      bookingBuffer: 30,
      maxAdvanceBooking: 60,
      cancellationPolicy: '48 hours notice required',
      staff: ['staff-1', 'staff-2'],
      color: '#10B981',
      image: 'https://ui-avatars.com/api/?name=Digital+Marketing&background=10B981&color=fff',
      features: [
        'Hands-on practical exercises',
        'Marketing toolkit included',
        'Group collaboration',
        'Certificate of completion'
      ]
    },
    {
      id: '3',
      name: 'Personal Coaching Session',
      description: 'Personalized coaching session to help you achieve your personal and professional goals.',
      duration: 45,
      price: 120.00,
      category: 'coaching',
      location: 'Online via Google Meet',
      isActive: true,
      bookingBuffer: 15,
      maxAdvanceBooking: 45,
      cancellationPolicy: '24 hours notice required',
      staff: ['staff-2'],
      color: '#8B5CF6',
      image: 'https://ui-avatars.com/api/?name=Personal+Coaching&background=8B5CF6&color=fff',
      features: [
        'Goal setting and planning',
        'Accountability framework',
        'Progress tracking tools',
        'Weekly check-in emails'
      ]
    },
    {
      id: '4',
      name: 'Team Building Workshop',
      description: 'Interactive workshop designed to improve team collaboration and communication.',
      duration: 180,
      price: 299.00,
      category: 'workshop',
      location: 'Main Conference Hall',
      isActive: true,
      bookingBuffer: 60,
      maxAdvanceBooking: 90,
      cancellationPolicy: '72 hours notice required',
      staff: ['staff-1', 'staff-3'],
      color: '#F59E0B',
      image: 'https://ui-avatars.com/api/?name=Team+Building&background=F59E0B&color=fff',
      features: [
        'Interactive team activities',
        'Communication exercises',
        'Team assessment report',
        'Follow-up action plan'
      ]
    }
  ];
  
  // Mock staff
  const mockStaff = [
    {
      id: 'staff-1',
      name: 'Sarah Johnson',
      title: 'Senior Business Consultant',
      avatar: 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=EC4899&color=fff',
      email: 'sarah@example.com',
      phone: '+1 (555) 123-4567',
      bio: 'Over 10 years of experience helping businesses scale and optimize their operations.',
      specialties: ['Business Strategy', 'Operations', 'Leadership'],
      rating: 4.9,
      totalBookings: 156,
      isAvailable: true
    },
    {
      id: 'staff-2',
      name: 'Mike Chen',
      title: 'Digital Marketing Expert',
      avatar: 'https://ui-avatars.com/api/?name=Mike+Chen&background=3B82F6&color=fff',
      email: 'mike@example.com',
      phone: '+1 (555) 987-6543',
      bio: 'Digital marketing specialist with expertise in social media, SEO, and content marketing.',
      specialties: ['Digital Marketing', 'SEO', 'Social Media'],
      rating: 4.8,
      totalBookings: 203,
      isAvailable: true
    },
    {
      id: 'staff-3',
      name: 'Emma Davis',
      title: 'Team Development Coach',
      avatar: 'https://ui-avatars.com/api/?name=Emma+Davis&background=10B981&color=fff',
      email: 'emma@example.com',
      phone: '+1 (555) 456-7890',
      bio: 'Certified coach specializing in team dynamics and organizational development.',
      specialties: ['Team Building', 'Leadership', 'Communication'],
      rating: 4.9,
      totalBookings: 89,
      isAvailable: false
    }
  ];
  
  // Mock bookings
  const mockBookings = [
    {
      id: 'booking-1',
      serviceId: '1',
      customerName: 'John Smith',
      customerEmail: 'john@example.com',
      customerPhone: '+1 (555) 111-2222',
      date: '2025-01-22',
      startTime: '10:00',
      endTime: '11:00',
      status: 'confirmed',
      staffId: 'staff-1',
      notes: 'Interested in expanding to new markets',
      totalAmount: 150.00,
      paymentStatus: 'paid',
      createdAt: '2025-01-19'
    },
    {
      id: 'booking-2',
      serviceId: '2',
      customerName: 'Alice Johnson',
      customerEmail: 'alice@example.com',
      customerPhone: '+1 (555) 333-4444',
      date: '2025-01-23',
      startTime: '14:00',
      endTime: '16:00',
      status: 'pending',
      staffId: 'staff-1',
      notes: 'Small business owner looking to improve online presence',
      totalAmount: 89.00,
      paymentStatus: 'pending',
      createdAt: '2025-01-20'
    },
    {
      id: 'booking-3',
      serviceId: '3',
      customerName: 'Robert Wilson',
      customerEmail: 'robert@example.com',
      customerPhone: '+1 (555) 555-6666',
      date: '2025-01-24',
      startTime: '09:30',
      endTime: '10:15',
      status: 'confirmed',
      staffId: 'staff-2',
      notes: 'Career transition coaching',
      totalAmount: 120.00,
      paymentStatus: 'paid',
      createdAt: '2025-01-18'
    }
  ];
  
  const [bookingStats, setBookingStats] = useState({
    totalBookings: 156,
    confirmedBookings: 134,
    pendingBookings: 12,
    cancelledBookings: 10,
    totalRevenue: 18450.00,
    averageBookingValue: 118.27,
    bookingGrowth: 23.5,
    customerSatisfaction: 4.8
  });
  
  useEffect(() => {
    setServices(mockServices);
    setStaff(mockStaff);
    setBookings(mockBookings);
  }, []);
  
  const getBookingsForDate = (date) => {
    const dateString = date.toISOString().split('T')[0];
    return bookings.filter(booking => booking.date === dateString);
  };
  
  const getStatusColor = (status) => {
    switch (status) {
      case 'confirmed': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
      case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
      case 'cancelled': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
      case 'completed': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
    }
  };
  
  const renderServiceCard = (service) => (
    <motion.div
      key={service.id}
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="bg-surface border border-default rounded-xl p-6 hover:shadow-lg transition-all"
    >
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center">
          <img
            src={service.image}
            alt={service.name}
            className="w-16 h-16 rounded-xl mr-4"
          />
          <div>
            <h3 className="font-semibold text-primary mb-1">{service.name}</h3>
            <p className="text-secondary text-sm">{serviceCategories.find(c => c.id === service.category)?.name}</p>
            <div className="flex items-center mt-1">
              <ClockIcon className="h-4 w-4 text-secondary mr-1" />
              <span className="text-secondary text-sm">{service.duration} min</span>
              <span className="mx-2 text-secondary">•</span>
              <span className="text-primary font-semibold">${service.price}</span>
            </div>
          </div>
        </div>
        <div className={`w-3 h-3 rounded-full ${service.isActive ? 'bg-green-500' : 'bg-red-500'}`}></div>
      </div>
      
      <p className="text-secondary text-sm mb-4 line-clamp-2">{service.description}</p>
      
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center text-sm text-secondary">
          <MapPinIcon className="h-4 w-4 mr-1" />
          {service.location}
        </div>
        <div className="flex -space-x-2">
          {service.staff.map((staffId) => {
            const staffMember = mockStaff.find(s => s.id === staffId);
            return staffMember ? (
              <img
                key={staffId}
                src={staffMember.avatar}
                alt={staffMember.name}
                className="w-8 h-8 rounded-full border-2 border-surface"
                title={staffMember.name}
              />
            ) : null;
          })}
        </div>
      </div>
      
      <div className="flex space-x-2">
        <button
          onClick={() => setSelectedService(service)}
          className="btn btn-primary flex-1"
        >
          <CalendarIcon className="h-4 w-4 mr-2" />
          Book Now
        </button>
        <button
          onClick={() => {/* Edit service */}}
          className="btn btn-secondary px-3"
        >
          <PencilIcon className="h-4 w-4" />
        </button>
        <button
          onClick={() => {/* Share service */}}
          className="btn btn-secondary px-3"
        >
          <ShareIcon className="h-4 w-4" />
        </button>
      </div>
    </motion.div>
  );
  
  const renderBookingCard = (booking) => {
    const service = services.find(s => s.id === booking.serviceId);
    const staffMember = mockStaff.find(s => s.id === booking.staffId);
    
    return (
      <motion.div
        key={booking.id}
        initial={{ opacity: 0, x: -20 }}
        animate={{ opacity: 1, x: 0 }}
        className="bg-surface border border-default rounded-xl p-4 mb-3 hover:shadow-md transition-all"
      >
        <div className="flex items-start justify-between">
          <div className="flex items-start space-x-3">
            <div 
              className="w-3 h-12 rounded-full" 
              style={{ backgroundColor: service?.color || '#3B82F6' }}
            ></div>
            <div>
              <h4 className="font-medium text-primary">{service?.name}</h4>
              <div className="flex items-center text-sm text-secondary mt-1">
                <UserIcon className="h-4 w-4 mr-1" />
                {booking.customerName}
                <span className="mx-2">•</span>
                <ClockIcon className="h-4 w-4 mr-1" />
                {booking.startTime} - {booking.endTime}
              </div>
              {staffMember && (
                <div className="flex items-center text-sm text-secondary mt-1">
                  <img 
                    src={staffMember.avatar} 
                    alt={staffMember.name}
                    className="w-4 h-4 rounded-full mr-1"
                  />
                  {staffMember.name}
                </div>
              )}
              {booking.notes && (
                <p className="text-xs text-secondary mt-2 line-clamp-1">{booking.notes}</p>
              )}
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(booking.status)}`}>
              {booking.status}
            </span>
            <span className="text-sm font-medium text-primary">${booking.totalAmount}</span>
          </div>
        </div>
      </motion.div>
    );
  };
  
  const renderCalendarDay = (date) => {
    const dayBookings = getBookingsForDate(date);
    const isToday = date.toDateString() === new Date().toDateString();
    const isSelected = date.toDateString() === selectedDate.toDateString();
    
    return (
      <div
        key={date.toISOString()}
        onClick={() => setSelectedDate(date)}
        className={`p-2 border border-default rounded-lg cursor-pointer transition-all ${
          isSelected 
            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' 
            : isToday 
            ? 'border-blue-300 bg-blue-25 dark:bg-blue-900/10'
            : 'hover:bg-surface-hover'
        }`}
      >
        <div className="text-center mb-1">
          <div className={`text-sm font-medium ${isToday ? 'text-blue-600' : 'text-primary'}`}>
            {date.getDate()}
          </div>
        </div>
        {dayBookings.length > 0 && (
          <div className="space-y-1">
            {dayBookings.slice(0, 2).map((booking) => {
              const service = services.find(s => s.id === booking.serviceId);
              return (
                <div 
                  key={booking.id}
                  className="text-xs p-1 rounded truncate"
                  style={{ 
                    backgroundColor: service?.color + '20',
                    color: service?.color || '#3B82F6'
                  }}
                >
                  {booking.startTime} {service?.name.substring(0, 15)}...
                </div>
              );
            })}
            {dayBookings.length > 2 && (
              <div className="text-xs text-secondary text-center">
                +{dayBookings.length - 2} more
              </div>
            )}
          </div>
        )}
      </div>
    );
  };
  
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <CalendarDaysIconSolid className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">Booking System</h1>
            </div>
            <p className="text-white/80">Manage appointments, services, and customer bookings</p>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{bookingStats.totalBookings}</div>
              <div className="text-sm text-white/70">Total Bookings</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">${bookingStats.totalRevenue.toLocaleString()}</div>
              <div className="text-sm text-white/70">Revenue</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{bookingStats.pendingBookings}</div>
              <div className="text-sm text-white/70">Pending</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{bookingStats.customerSatisfaction}</div>
              <div className="text-sm text-white/70">Satisfaction</div>
            </div>
          </div>
        </div>
      </div>
      
      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'calendar', name: 'Calendar', icon: CalendarDaysIcon },
            { id: 'services', name: 'Services', icon: CogIcon },
            { id: 'bookings', name: 'All Bookings', icon: DocumentTextIcon },
            { id: 'customers', name: 'Customers', icon: UserGroupIcon },
            { id: 'staff', name: 'Staff', icon: UserIcon },
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
      
      {/* Content */}
      {activeTab === 'calendar' && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Calendar */}
          <div className="lg:col-span-2">
            <div className="bg-surface-elevated rounded-xl shadow-default p-6">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-xl font-semibold text-primary">
                  {selectedDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
                </h2>
                <div className="flex space-x-2">
                  <button
                    onClick={() => setViewMode('month')}
                    className={`btn btn-sm ${viewMode === 'month' ? 'btn-primary' : 'btn-secondary'}`}
                  >
                    Month
                  </button>
                  <button
                    onClick={() => setViewMode('week')}
                    className={`btn btn-sm ${viewMode === 'week' ? 'btn-primary' : 'btn-secondary'}`}
                  >
                    Week
                  </button>
                  <button
                    onClick={() => setViewMode('day')}
                    className={`btn btn-sm ${viewMode === 'day' ? 'btn-primary' : 'btn-secondary'}`}
                  >
                    Day
                  </button>
                </div>
              </div>
              
              {/* Calendar Grid */}
              <div className="grid grid-cols-7 gap-2">
                {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((day) => (
                  <div key={day} className="text-center text-sm font-medium text-secondary py-2">
                    {day}
                  </div>
                ))}
                {/* Calendar days would be generated here */}
                {Array.from({ length: 35 }, (_, i) => {
                  const date = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), i - 6);
                  return renderCalendarDay(date);
                })}
              </div>
            </div>
          </div>
          
          {/* Daily Schedule */}
          <div>
            <div className="bg-surface-elevated rounded-xl shadow-default p-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-semibold text-primary">
                  {selectedDate.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    month: 'short', 
                    day: 'numeric' 
                  })}
                </h3>
                <button className="btn btn-primary btn-sm">
                  <PlusIcon className="h-4 w-4 mr-2" />
                  New Booking
                </button>
              </div>
              
              <div className="space-y-3">
                {getBookingsForDate(selectedDate).map(renderBookingCard)}
                
                {getBookingsForDate(selectedDate).length === 0 && (
                  <div className="text-center py-8">
                    <CalendarIcon className="h-12 w-12 mx-auto mb-4 text-gray-400" />
                    <p className="text-secondary">No bookings for this day</p>
                    <button className="btn btn-primary mt-4">
                      <PlusIcon className="h-4 w-4 mr-2" />
                      Add Booking
                    </button>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
      
      {activeTab === 'services' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-2xl font-bold text-primary">Services</h2>
              <p className="text-secondary">Manage your bookable services and pricing</p>
            </div>
            <button
              onClick={() => setShowServiceModal(true)}
              className="btn btn-primary"
            >
              <PlusIcon className="h-4 w-4 mr-2" />
              Add Service
            </button>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            {services.map(renderServiceCard)}
          </div>
        </div>
      )}
      
      {activeTab === 'bookings' && (
        <div className="space-y-6">
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-xl font-semibold text-primary">All Bookings</h2>
              <div className="flex space-x-3">
                <select className="input w-48">
                  <option value="">All Statuses</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="pending">Pending</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="completed">Completed</option>
                </select>
                <button className="btn btn-secondary">
                  Export
                </button>
              </div>
            </div>
            
            <div className="space-y-3">
              {bookings.map(renderBookingCard)}
            </div>
          </div>
        </div>
      )}
      
      {activeTab === 'staff' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-2xl font-bold text-primary">Staff Management</h2>
              <p className="text-secondary">Manage your team members and their availability</p>
            </div>
            <button className="btn btn-primary">
              <PlusIcon className="h-4 w-4 mr-2" />
              Add Staff Member
            </button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {mockStaff.map((staffMember) => (
              <motion.div
                key={staffMember.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="bg-surface border border-default rounded-xl p-6"
              >
                <div className="flex items-start justify-between mb-4">
                  <div className="flex items-center">
                    <img
                      src={staffMember.avatar}
                      alt={staffMember.name}
                      className="w-16 h-16 rounded-full mr-4"
                    />
                    <div>
                      <h3 className="font-semibold text-primary">{staffMember.name}</h3>
                      <p className="text-secondary text-sm">{staffMember.title}</p>
                      <div className="flex items-center mt-1">
                        <StarIcon className="h-4 w-4 text-yellow-400 fill-current mr-1" />
                        <span className="text-sm text-secondary">
                          {staffMember.rating} • {staffMember.totalBookings} bookings
                        </span>
                      </div>
                    </div>
                  </div>
                  <div className={`w-3 h-3 rounded-full ${
                    staffMember.isAvailable ? 'bg-green-500' : 'bg-red-500'
                  }`}></div>
                </div>
                
                <p className="text-secondary text-sm mb-4">{staffMember.bio}</p>
                
                <div className="flex flex-wrap gap-1 mb-4">
                  {staffMember.specialties.map((specialty, index) => (
                    <span key={index} className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                      {specialty}
                    </span>
                  ))}
                </div>
                
                <div className="flex space-x-2">
                  <button className="btn btn-secondary flex-1">
                    <PencilIcon className="h-4 w-4 mr-2" />
                    Edit
                  </button>
                  <button className="btn btn-secondary">
                    <CalendarIcon className="h-4 w-4" />
                  </button>
                  <button className="btn btn-secondary">
                    <CogIcon className="h-4 w-4" />
                  </button>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      )}
      
      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-surface-elevated rounded-xl p-6 text-center">
              <CheckCircleIconSolid className="h-8 w-8 mx-auto mb-3 text-green-600" />
              <div className="text-2xl font-bold text-primary">{bookingStats.confirmedBookings}</div>
              <div className="text-sm text-secondary">Confirmed Bookings</div>
            </div>
            <div className="bg-surface-elevated rounded-xl p-6 text-center">
              <ClockIconSolid className="h-8 w-8 mx-auto mb-3 text-yellow-600" />
              <div className="text-2xl font-bold text-primary">{bookingStats.pendingBookings}</div>
              <div className="text-sm text-secondary">Pending Bookings</div>
            </div>
            <div className="bg-surface-elevated rounded-xl p-6 text-center">
              <CreditCardIcon className="h-8 w-8 mx-auto mb-3 text-blue-600" />
              <div className="text-2xl font-bold text-primary">${bookingStats.averageBookingValue}</div>
              <div className="text-sm text-secondary">Avg Booking Value</div>
            </div>
            <div className="bg-surface-elevated rounded-xl p-6 text-center">
              <StarIcon className="h-8 w-8 mx-auto mb-3 text-purple-600" />
              <div className="text-2xl font-bold text-primary">{bookingStats.customerSatisfaction}</div>
              <div className="text-sm text-secondary">Customer Rating</div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ProfessionalBookingSystem;