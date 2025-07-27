import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CalendarIcon,
  ClockIcon,
  UserIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  MagnifyingGlassIcon,
  FilterIcon,
  ArrowDownTrayIcon,
  CogIcon,
  BellIcon,
  ChartBarIcon,
  UsersIcon,
  CreditCardIcon,
  DocumentTextIcon,
  ShareIcon,
  LinkIcon,
  QrCodeIcon,
  VideoCameraIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  GlobeAltIcon,
  StarIcon,
  HeartIcon,
  ChatBubbleBottomCenterTextIcon,
  BuildingOfficeIcon,
  HomeIcon,
  WrenchScrewdriverIcon,
  AcademicCapIcon,
  BeakerIcon,
  BoltIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid
} from '@heroicons/react/24/solid';

const BookingSystemPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeTab, setActiveTab] = useState('appointments');
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);
  const [viewMode, setViewMode] = useState('calendar');

  // Services Data
  const [services, setServices] = useState([
    {
      id: 'service1',
      name: 'Business Consultation',
      description: 'Strategic business consultation session to help grow your business',
      category: 'Consulting',
      duration: 60, // minutes
      price: 150,
      currency: 'USD',
      availability: {
        monday: { enabled: true, slots: ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'] },
        tuesday: { enabled: true, slots: ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'] },
        wednesday: { enabled: true, slots: ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'] },
        thursday: { enabled: true, slots: ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'] },
        friday: { enabled: true, slots: ['09:00', '10:00', '11:00', '14:00', '15:00'] },
        saturday: { enabled: false, slots: [] },
        sunday: { enabled: false, slots: [] }
      },
      bookingType: 'online', // online, in-person, both
      meetingLink: 'https://meet.google.com/abc-defg-hij',
      location: 'Virtual Meeting',
      staff: 'John Smith',
      color: '#3B82F6',
      active: true,
      bookingsCount: 45,
      rating: 4.8,
      reviews: 23
    },
    {
      id: 'service2',
      name: 'Web Development Project',
      description: 'Custom website development consultation and project planning',
      category: 'Development',
      duration: 90,
      price: 200,
      currency: 'USD',
      availability: {
        monday: { enabled: true, slots: ['09:00', '11:00', '14:00'] },
        tuesday: { enabled: true, slots: ['09:00', '11:00', '14:00'] },
        wednesday: { enabled: true, slots: ['09:00', '11:00', '14:00'] },
        thursday: { enabled: true, slots: ['09:00', '11:00', '14:00'] },
        friday: { enabled: true, slots: ['09:00', '11:00'] },
        saturday: { enabled: false, slots: [] },
        sunday: { enabled: false, slots: [] }
      },
      bookingType: 'both',
      meetingLink: 'https://zoom.us/j/123456789',
      location: '123 Tech Street, San Francisco, CA',
      staff: 'Jane Doe',
      color: '#10B981',
      active: true,
      bookingsCount: 32,
      rating: 4.9,
      reviews: 18
    },
    {
      id: 'service3',
      name: 'Design Review Session',
      description: 'UI/UX design review and feedback session for your project',
      category: 'Design',
      duration: 45,
      price: 100,
      currency: 'USD',
      availability: {
        monday: { enabled: true, slots: ['10:00', '11:00', '15:00', '16:00'] },
        tuesday: { enabled: true, slots: ['10:00', '11:00', '15:00', '16:00'] },
        wednesday: { enabled: true, slots: ['10:00', '11:00', '15:00', '16:00'] },
        thursday: { enabled: true, slots: ['10:00', '11:00', '15:00', '16:00'] },
        friday: { enabled: true, slots: ['10:00', '11:00', '15:00'] },
        saturday: { enabled: true, slots: ['10:00', '11:00'] },
        sunday: { enabled: false, slots: [] }
      },
      bookingType: 'online',
      meetingLink: 'https://teams.microsoft.com/abc123',
      location: 'Virtual Meeting',
      staff: 'Mike Johnson',
      color: '#8B5CF6',
      active: true,
      bookingsCount: 67,
      rating: 4.7,
      reviews: 34
    }
  ]);

  // Appointments Data
  const [appointments, setAppointments] = useState([
    {
      id: 'apt1',
      serviceId: 'service1',
      serviceName: 'Business Consultation',
      client: {
        name: 'Sarah Wilson',
        email: 'sarah@example.com',
        phone: '+1-555-0123',
        company: 'Tech Startup Inc.',
        notes: 'Looking for advice on scaling the business and team management'
      },
      date: '2025-01-20',
      time: '10:00',
      duration: 60,
      status: 'confirmed',
      type: 'online',
      meetingLink: 'https://meet.google.com/abc-defg-hij',
      amount: 150,
      paid: true,
      paymentMethod: 'Credit Card',
      remindersSent: ['24h', '1h'],
      created: '2025-01-18T14:30:00Z',
      staff: 'John Smith',
      color: '#3B82F6'
    },
    {
      id: 'apt2',
      serviceId: 'service2',
      serviceName: 'Web Development Project',
      client: {
        name: 'Michael Chen',
        email: 'mike@company.com',
        phone: '+1-555-0456',
        company: 'Growth Agency',
        notes: 'Need a new website for agency with portfolio showcase and client portal'
      },
      date: '2025-01-20',
      time: '14:00',
      duration: 90,
      status: 'confirmed',
      type: 'in-person',
      location: '123 Tech Street, San Francisco, CA',
      amount: 200,
      paid: true,
      paymentMethod: 'PayPal',
      remindersSent: ['24h'],
      created: '2025-01-19T09:15:00Z',
      staff: 'Jane Doe',
      color: '#10B981'
    },
    {
      id: 'apt3',
      serviceId: 'service3',
      serviceName: 'Design Review Session',
      client: {
        name: 'Emily Rodriguez',
        email: 'emily@creative.com',
        phone: '+1-555-0789',
        company: 'Creative Studio',
        notes: 'Review mobile app designs and user flow improvements'
      },
      date: '2025-01-21',
      time: '11:00',
      duration: 45,
      status: 'pending',
      type: 'online',
      meetingLink: 'https://teams.microsoft.com/abc123',
      amount: 100,
      paid: false,
      paymentMethod: null,
      remindersSent: [],
      created: '2025-01-19T16:45:00Z',
      staff: 'Mike Johnson',
      color: '#8B5CF6'
    },
    {
      id: 'apt4',
      serviceId: 'service1',
      serviceName: 'Business Consultation',
      client: {
        name: 'David Kim',
        email: 'david@startup.co',
        phone: '+1-555-0321',
        company: 'Startup Co',
        notes: 'First-time founder looking for guidance on product development'
      },
      date: '2025-01-22',
      time: '09:00',
      duration: 60,
      status: 'confirmed',
      type: 'online',
      meetingLink: 'https://meet.google.com/def-ghij-klm',
      amount: 150,
      paid: true,
      paymentMethod: 'Credit Card',
      remindersSent: [],
      created: '2025-01-19T11:20:00Z',
      staff: 'John Smith',
      color: '#3B82F6'
    }
  ]);

  // Analytics Data
  const [analytics, setAnalytics] = useState({
    overview: {
      totalBookings: 144,
      confirmedBookings: 132,
      pendingBookings: 8,
      cancelledBookings: 4,
      totalRevenue: 18650,
      averageBookingValue: 129.51,
      bookingRate: 78.2,
      noShowRate: 3.1
    },
    trends: {
      bookingsGrowth: 15.3,
      revenueGrowth: 23.7,
      newClients: 45,
      returningClients: 32,
      popularService: 'Design Review Session',
      peakHours: ['10:00', '11:00', '14:00', '15:00'],
      peakDays: ['Tuesday', 'Wednesday', 'Thursday']
    }
  });

  // Calendar Helpers
  const generateCalendarDates = (date) => {
    const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    const dates = [];
    
    // Add empty cells for days before month starts
    for (let i = 0; i < startingDayOfWeek; i++) {
      dates.push(null);
    }
    
    // Add days of the month
    for (let day = 1; day <= daysInMonth; day++) {
      dates.push(new Date(date.getFullYear(), date.getMonth(), day));
    }
    
    return dates;
  };

  const getAppointmentsForDate = (date) => {
    const dateStr = date.toISOString().split('T')[0];
    return appointments.filter(apt => apt.date === dateStr);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'confirmed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
      case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
      case 'cancelled': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
      case 'completed': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
  };

  const formatTime = (time) => {
    return new Date(`1970-01-01T${time}:00`).toLocaleTimeString('en-US', {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(price);
  };

  const renderStars = (rating) => {
    return [...Array(5)].map((_, i) => (
      <StarIconSolid
        key={i}
        className={`h-4 w-4 ${i < Math.floor(rating) ? 'text-yellow-500' : 'text-gray-300'}`}
      />
    ));
  };

  const renderAppointmentsTab = () => (
    <div className="space-y-6">
      {/* Appointments Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {[
          { label: 'Total Bookings', value: analytics.overview.totalBookings.toString(), icon: CalendarIcon, color: 'bg-blue-500' },
          { label: 'Confirmed', value: analytics.overview.confirmedBookings.toString(), icon: CheckCircleIcon, color: 'bg-green-500' },
          { label: 'Pending', value: analytics.overview.pendingBookings.toString(), icon: ClockIcon, color: 'bg-yellow-500' },
          { label: 'Revenue', value: formatPrice(analytics.overview.totalRevenue), icon: CurrencyDollarIcon, color: 'bg-purple-500' }
        ].map((stat, index) => (
          <div key={index} className="bg-surface-elevated p-6 rounded-xl shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary mt-1">{stat.value}</p>
              </div>
              <div className={`p-3 rounded-lg ${stat.color}`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* View Controls */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <div className="flex items-center space-x-1 bg-surface-elevated rounded-lg p-1">
            <button
              onClick={() => setViewMode('calendar')}
              className={`p-2 rounded ${viewMode === 'calendar' ? 'bg-blue-500 text-white' : 'text-secondary'}`}
            >
              <CalendarIcon className="h-4 w-4" />
            </button>
            <button
              onClick={() => setViewMode('list')}
              className={`p-2 rounded ${viewMode === 'list' ? 'bg-blue-500 text-white' : 'text-secondary'}`}
            >
              <div className="space-y-1">
                <div className="w-4 h-1 bg-current rounded"></div>
                <div className="w-4 h-1 bg-current rounded"></div>
                <div className="w-4 h-1 bg-current rounded"></div>
              </div>
            </button>
          </div>
          
          <input
            type="date"
            value={selectedDate}
            onChange={(e) => setSelectedDate(e.target.value)}
            className="input"
          />
        </div>
        
        <div className="flex items-center space-x-3">
          <div className="relative">
            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-secondary" />
            <input
              type="text"
              placeholder="Search appointments..."
              className="input pl-9 w-64"
            />
          </div>
          <button className="btn btn-secondary flex items-center space-x-2">
            <FilterIcon className="h-4 w-4" />
            <span>Filter</span>
          </button>
          <button className="btn btn-primary flex items-center space-x-2">
            <PlusIcon className="h-4 w-4" />
            <span>New Appointment</span>
          </button>
        </div>
      </div>

      {/* Calendar or List View */}
      {viewMode === 'calendar' ? (
        <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
          <div className="mb-6">
            <h3 className="text-lg font-semibold text-primary">
              {new Date(selectedDate).toLocaleDateString('en-US', { 
                month: 'long', 
                year: 'numeric' 
              })}
            </h3>
          </div>
          
          <div className="grid grid-cols-7 gap-2 mb-4">
            {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(day => (
              <div key={day} className="text-center font-medium text-secondary py-2">
                {day}
              </div>
            ))}
          </div>
          
          <div className="grid grid-cols-7 gap-2">
            {generateCalendarDates(new Date(selectedDate)).map((date, index) => {
              if (!date) {
                return <div key={index} className="h-24"></div>;
              }
              
              const dayAppointments = getAppointmentsForDate(date);
              const isToday = date.toDateString() === new Date().toDateString();
              
              
  const loadBookingData = async () => {
    try {
      setLoading(true);
      const [servicesResponse, appointmentsResponse, analyticsResponse] = await Promise.all([
        fetch('/api/booking/services', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/booking/appointments', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/booking/analytics', {
          headers: { 'Authorization': `Bearer ${token}` }
        })
      ]);
      
      if (servicesResponse.ok && appointmentsResponse.ok && analyticsResponse.ok) {
        const [services, appointments, analytics] = await Promise.all([
          servicesResponse.json(),
          appointmentsResponse.json(),
          analyticsResponse.json()
        ]);
        
        setServices(services.services || []);
        setAppointments(appointments.appointments || []);
        setAnalytics(analytics);
      }
    } catch (error) {
      console.error('Error loading booking data:', error);
    } finally {
      setLoading(false);
    }
  };


  return (
                <div
                  key={index}
                  className={`h-24 p-2 border rounded-lg cursor-pointer hover:bg-surface-hover transition-colors ${
                    isToday ? 'bg-blue-50 border-blue-300 dark:bg-blue-900/20' : 'border-default'
                  }`}
                >
                  <div className="text-sm font-medium text-primary mb-1">
                    {date.getDate()}
                  </div>
                  <div className="space-y-1">
                    {dayAppointments.slice(0, 2).map(apt => (
                      <div
                        key={apt.id}
                        className="text-xs p-1 rounded truncate"
                        style={{ backgroundColor: apt.color + '20', color: apt.color }}
                      >
                        {formatTime(apt.time)} {apt.client.name}
                      </div>
                    ))}
                    {dayAppointments.length > 2 && (
                      <div className="text-xs text-secondary">
                        +{dayAppointments.length - 2} more
                      </div>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      ) : (
        <div className="space-y-4">
          {appointments.map((appointment) => (
            <div key={appointment.id} className="bg-surface-elevated p-6 rounded-xl shadow-default">
              <div className="flex items-start justify-between mb-4">
                <div className="flex items-start space-x-4">
                  <div 
                    className="w-4 h-16 rounded-full"
                    style={{ backgroundColor: appointment.color }}
                  />
                  
                  <div className="flex-1">
                    <div className="flex items-center space-x-3 mb-2">
                      <h3 className="text-lg font-semibold text-primary">{appointment.serviceName}</h3>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(appointment.status)}`}>
                        {appointment.status}
                      </span>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-secondary mb-3">
                      <div className="space-y-2">
                        <div className="flex items-center space-x-2">
                          <UserIcon className="h-4 w-4" />
                          <span>{appointment.client.name}</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <EnvelopeIcon className="h-4 w-4" />
                          <span>{appointment.client.email}</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <PhoneIcon className="h-4 w-4" />
                          <span>{appointment.client.phone}</span>
                        </div>
                      </div>
                      
                      <div className="space-y-2">
                        <div className="flex items-center space-x-2">
                          <CalendarIcon className="h-4 w-4" />
                          <span>{new Date(appointment.date).toLocaleDateString()}</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <ClockIcon className="h-4 w-4" />
                          <span>{formatTime(appointment.time)} ({appointment.duration} min)</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          {appointment.type === 'online' ? (
                            <>
                              <VideoCameraIcon className="h-4 w-4" />
                              <span>Online Meeting</span>
                            </>
                          ) : (
                            <>
                              <MapPinIcon className="h-4 w-4" />
                              <span>{appointment.location}</span>
                            </>
                          )}
                        </div>
                      </div>
                    </div>
                    
                    {appointment.client.notes && (
                      <div className="bg-surface p-3 rounded-lg mb-3">
                        <p className="text-sm text-secondary">
                          <strong>Notes:</strong> {appointment.client.notes}
                        </p>
                      </div>
                    )}
                  </div>
                </div>
                
                <div className="text-right">
                  <div className="text-xl font-bold text-primary">{formatPrice(appointment.amount)}</div>
                  <div className="text-sm text-secondary">
                    {appointment.paid ? (
                      <span className="text-green-600">Paid via {appointment.paymentMethod}</span>
                    ) : (
                      <span className="text-red-600">Payment Pending</span>
                    )}
                  </div>
                </div>
              </div>
              
              <div className="flex items-center justify-between pt-4 border-t border-default">
                <div className="flex items-center space-x-2">
                  {appointment.remindersSent.length > 0 && (
                    <span className="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded-full text-xs">
                      Reminders: {appointment.remindersSent.join(', ')}
                    </span>
                  )}
                  {appointment.meetingLink && (
                    <a 
                      href={appointment.meetingLink}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-sm text-blue-500 hover:text-blue-700"
                    >
                      Join Meeting
                    </a>
                  )}
                </div>
                
                <div className="flex items-center space-x-2">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <EyeIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <PencilIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <ChatBubbleBottomCenterTextIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <ShareIcon className="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );

  const renderServicesTab = () => (
    <div className="space-y-6">
      {/* Services Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {[
          { label: 'Total Services', value: services.length.toString(), icon: WrenchScrewdriverIcon, color: 'bg-blue-500' },
          { label: 'Active Services', value: services.filter(s => s.active).length.toString(), icon: CheckCircleIcon, color: 'bg-green-500' },
          { label: 'Total Bookings', value: services.reduce((sum, s) => sum + s.bookingsCount, 0).toString(), icon: CalendarIcon, color: 'bg-purple-500' },
          { label: 'Avg Rating', value: (services.reduce((sum, s) => sum + s.rating, 0) / services.length).toFixed(1), icon: StarIcon, color: 'bg-orange-500' }
        ].map((stat, index) => (
          <div key={index} className="bg-surface-elevated p-6 rounded-xl shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary mt-1">{stat.value}</p>
              </div>
              <div className={`p-3 rounded-lg ${stat.color}`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Actions */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <button className="btn btn-primary flex items-center space-x-2">
            <PlusIcon className="h-4 w-4" />
            <span>Add Service</span>
          </button>
          <button className="btn btn-secondary flex items-center space-x-2">
            <QrCodeIcon className="h-4 w-4" />
            <span>Booking QR Codes</span>
          </button>
        </div>
        
        <div className="flex items-center space-x-3">
          <div className="relative">
            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-secondary" />
            <input
              type="text"
              placeholder="Search services..."
              className="input pl-9 w-64"
            />
          </div>
          <select className="input">
            <option>All Categories</option>
            <option>Consulting</option>
            <option>Development</option>
            <option>Design</option>
          </select>
        </div>
      </div>

      {/* Services Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {services.map((service) => (
          <motion.div
            key={service.id}
            whileHover={{ y: -5 }}
            className="bg-surface-elevated rounded-xl shadow-default hover:shadow-lg transition-all duration-200"
          >
            {/* Service Header */}
            <div className="p-6 border-b border-default">
              <div className="flex items-start justify-between mb-3">
                <div className="flex-1">
                  <div className="flex items-center space-x-2 mb-2">
                    <div 
                      className="w-4 h-4 rounded-full"
                      style={{ backgroundColor: service.color }}
                    />
                    <h3 className="font-semibold text-primary">{service.name}</h3>
                  </div>
                  <p className="text-sm text-secondary mb-3">{service.description}</p>
                  <span className="px-2 py-1 bg-surface-hover text-xs text-secondary rounded-full">
                    {service.category}
                  </span>
                </div>
                
                <div className="text-right">
                  <div className="text-xl font-bold text-primary">{formatPrice(service.price)}</div>
                  <div className="text-sm text-secondary">{service.duration} min</div>
                </div>
              </div>
            </div>

            {/* Service Details */}
            <div className="p-6">
              <div className="grid grid-cols-2 gap-4 mb-4">
                <div className="text-center p-3 bg-surface rounded-lg">
                  <p className="text-lg font-bold text-primary">{service.bookingsCount}</p>
                  <p className="text-xs text-secondary">Bookings</p>
                </div>
                <div className="text-center p-3 bg-surface rounded-lg">
                  <div className="flex items-center justify-center space-x-1">
                    {renderStars(service.rating)}
                  </div>
                  <p className="text-xs text-secondary mt-1">{service.reviews} reviews</p>
                </div>
              </div>

              {/* Availability */}
              <div className="mb-4">
                <p className="text-sm font-medium text-secondary mb-2">Availability:</p>
                <div className="flex flex-wrap gap-1">
                  {Object.entries(service.availability).map(([day, config]) => (
                    config.enabled && (
                      <span key={day} className="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded-full text-xs">
                        {day.charAt(0).toUpperCase() + day.slice(1, 3)}
                      </span>
                    )
                  ))}
                </div>
              </div>

              {/* Booking Type */}
              <div className="flex items-center space-x-2 mb-4">
                {service.bookingType === 'online' ? (
                  <>
                    <VideoCameraIcon className="h-4 w-4 text-blue-500" />
                    <span className="text-sm text-secondary">Online Only</span>
                  </>
                ) : service.bookingType === 'in-person' ? (
                  <>
                    <MapPinIcon className="h-4 w-4 text-green-500" />
                    <span className="text-sm text-secondary">In-Person Only</span>
                  </>
                ) : (
                  <>
                    <GlobeAltIcon className="h-4 w-4 text-purple-500" />
                    <span className="text-sm text-secondary">Online & In-Person</span>
                  </>
                )}
              </div>

              {/* Staff */}
              <div className="flex items-center space-x-2 mb-4">
                <UserIcon className="h-4 w-4 text-secondary" />
                <span className="text-sm text-secondary">{service.staff}</span>
              </div>

              {/* Action Buttons */}
              <div className="grid grid-cols-2 gap-2">
                <button className="btn btn-secondary text-sm py-2 flex items-center justify-center">
                  <EyeIcon className="h-4 w-4 mr-1" />
                  View
                </button>
                <button className="btn btn-primary text-sm py-2 flex items-center justify-center">
                  <PencilIcon className="h-4 w-4 mr-1" />
                  Edit
                </button>
              </div>

              {/* Booking Link */}
              <div className="mt-4 pt-4 border-t border-default">
                <div className="flex items-center space-x-2">
                  <LinkIcon className="h-4 w-4 text-secondary" />
                  <span className="text-xs text-secondary flex-1 truncate">
                    mewayz.com/book/{service.id}
                  </span>
                  <button className="text-xs text-blue-500 hover:text-blue-700">Copy</button>
                </div>
              </div>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );

  const renderAnalyticsTab = () => (
    <div className="space-y-6">
      {/* Key Metrics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {[
          { 
            label: 'Booking Rate', 
            value: `${analytics.overview.bookingRate}%`,
            change: '+5.2%',
            icon: ChartBarIcon, 
            color: 'bg-blue-500' 
          },
          { 
            label: 'No-Show Rate', 
            value: `${analytics.overview.noShowRate}%`,
            change: '-1.3%',
            icon: ExclamationTriangleIcon, 
            color: 'bg-red-500' 
          },
          { 
            label: 'Avg Booking Value', 
            value: formatPrice(analytics.overview.averageBookingValue),
            change: '+8.7%',
            icon: CurrencyDollarIcon, 
            color: 'bg-green-500' 
          },
          { 
            label: 'New Clients', 
            value: analytics.trends.newClients.toString(),
            change: '+12.4%',
            icon: UsersIcon, 
            color: 'bg-purple-500' 
          }
        ].map((metric, index) => (
          <div key={index} className="bg-surface-elevated p-6 rounded-xl shadow-default">
            <div className="flex items-center justify-between mb-2">
              <div className={`p-3 rounded-lg ${metric.color}`}>
                <metric.icon className="h-6 w-6 text-white" />
              </div>
              <span className={`text-sm font-medium ${metric.change.startsWith('+') ? 'text-green-600' : 'text-red-600'}`}>
                {metric.change}
              </span>
            </div>
            <div>
              <p className="text-2xl font-bold text-primary">{metric.value}</p>
              <p className="text-sm text-secondary">{metric.label}</p>
            </div>
          </div>
        ))}
      </div>

      {/* Charts and Insights */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
          <h3 className="text-lg font-semibold text-primary mb-4">Booking Trends</h3>
          <div className="h-64 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg flex items-center justify-center">
            <p className="text-secondary">Booking trends chart would be rendered here</p>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
          <h3 className="text-lg font-semibold text-primary mb-4">Popular Time Slots</h3>
          <div className="space-y-4">
            {analytics.trends.peakHours.map((hour, index) => (
              <div key={hour} className="flex items-center justify-between">
                <span className="text-sm text-secondary">{formatTime(hour)}</span>
                <div className="flex items-center space-x-2 flex-1 mx-4">
                  <div className="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                      className="bg-blue-500 h-2 rounded-full transition-all duration-300"
                      style={{ width: `${85 - index * 15}%` }}
                    />
                  </div>
                  <span className="text-sm font-medium text-primary">{85 - index * 15}%</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Additional Insights */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
          <h3 className="text-lg font-semibold text-primary mb-4">Top Performing Services</h3>
          <div className="space-y-3">
            {services.map((service, index) => (
              <div key={service.id} className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <span className="text-2xl font-bold text-secondary">#{index + 1}</span>
                  <div>
                    <p className="font-medium text-primary">{service.name}</p>
                    <p className="text-sm text-secondary">{service.bookingsCount} bookings</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="font-semibold text-primary">{formatPrice(service.price * service.bookingsCount)}</p>
                  <div className="flex items-center space-x-1">
                    {renderStars(service.rating)}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
        
        <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
          <h3 className="text-lg font-semibold text-primary mb-4">Client Activity</h3>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-sm text-secondary">New Clients</span>
              <span className="font-semibold text-primary">{analytics.trends.newClients}</span>
            </div>
            <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div className="bg-green-500 h-2 rounded-full" style={{ width: '65%' }} />
            </div>
            
            <div className="flex items-center justify-between">
              <span className="text-sm text-secondary">Returning Clients</span>
              <span className="font-semibold text-primary">{analytics.trends.returningClients}</span>
            </div>
            <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div className="bg-blue-500 h-2 rounded-full" style={{ width: '45%' }} />
            </div>
            
            <div className="pt-4 border-t border-default">
              <p className="text-sm text-secondary mb-2">Most Popular Service</p>
              <p className="font-semibold text-primary">{analytics.trends.popularService}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  