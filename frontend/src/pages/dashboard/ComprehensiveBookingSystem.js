import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  CalendarIcon,
  ClockIcon,
  UserIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  CheckCircleIcon,
  XMarkIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
  CreditCardIcon,
  BellIcon,
  Cog6ToothIcon,
  ChartBarIcon,
  UsersIcon,
  CalendarDaysIcon,
  ClockIcon as TimeIcon
} from '@heroicons/react/24/outline';

const ComprehensiveBookingSystem = () => {
  const [activeTab, setActiveTab] = useState('calendar');
  const [selectedDate, setSelectedDate] = useState(new Date());
  const [selectedService, setSelectedService] = useState(null);
  const [appointments, setAppointments] = useState([]);
  const [services, setServices] = useState([]);
  const [clients, setClients] = useState([]);
  const [availability, setAvailability] = useState({});
  const [loading, setLoading] = useState(true);

  // Mock data for demonstration
  const mockServices = [
    {
      id: '1',
      name: 'Consultation Call',
      duration: 60,
      price: 150,
      description: 'One-on-one consultation session',
      color: '#3b82f6',
      requiresDeposit: true,
      depositAmount: 50,
      category: 'Consulting',
      isActive: true
    },
    {
      id: '2',
      name: 'Strategy Session',
      duration: 90,
      price: 250,
      description: 'Deep dive strategy planning session',
      color: '#10b981',
      requiresDeposit: true,
      depositAmount: 75,
      category: 'Strategy',
      isActive: true
    },
    {
      id: '3',
      name: 'Quick Review',
      duration: 30,
      price: 75,
      description: 'Quick review and feedback session',
      color: '#f59e0b',
      requiresDeposit: false,
      depositAmount: 0,
      category: 'Review',
      isActive: true
    },
    {
      id: '4',
      name: 'Workshop Session',
      duration: 120,
      price: 400,
      description: 'Comprehensive workshop training',
      color: '#8b5cf6',
      requiresDeposit: true,
      depositAmount: 150,
      category: 'Training',
      isActive: true
    }
  ];

  const mockAppointments = [
    {
      id: '1',
      serviceId: '1',
      serviceName: 'Consultation Call',
      clientName: 'John Smith',
      clientEmail: 'john@example.com',
      clientPhone: '+1 (555) 123-4567',
      date: '2025-07-20',
      startTime: '10:00',
      endTime: '11:00',
      status: 'confirmed',
      price: 150,
      depositPaid: true,
      notes: 'Initial consultation for digital marketing strategy',
      reminderSent: false,
      meetingLink: 'https://meet.google.com/abc-defg-hij',
      createdAt: '2025-07-18T10:00:00Z'
    },
    {
      id: '2',
      serviceId: '2',
      serviceName: 'Strategy Session',
      clientName: 'Sarah Johnson',
      clientEmail: 'sarah@example.com',
      clientPhone: '+1 (555) 234-5678',
      date: '2025-07-20',
      startTime: '14:00',
      endTime: '15:30',
      status: 'pending',
      price: 250,
      depositPaid: false,
      notes: 'Follow-up strategy session',
      reminderSent: false,
      meetingLink: null,
      createdAt: '2025-07-19T09:00:00Z'
    },
    {
      id: '3',
      serviceId: '3',
      serviceName: 'Quick Review',
      clientName: 'Mike Brown',
      clientEmail: 'mike@example.com',
      clientPhone: '+1 (555) 345-6789',
      date: '2025-07-21',
      startTime: '11:00',
      endTime: '11:30',
      status: 'confirmed',
      price: 75,
      depositPaid: false,
      notes: 'Website review session',
      reminderSent: true,
      meetingLink: 'https://meet.google.com/xyz-uvw-rst',
      createdAt: '2025-07-19T14:00:00Z'
    }
  ];

  const mockClients = [
    {
      id: '1',
      name: 'John Smith',
      email: 'john@example.com',
      phone: '+1 (555) 123-4567',
      totalAppointments: 5,
      totalSpent: 750,
      lastAppointment: '2025-07-15',
      notes: 'Preferred time: mornings',
      timezone: 'EST'
    },
    {
      id: '2',
      name: 'Sarah Johnson',
      email: 'sarah@example.com',
      phone: '+1 (555) 234-5678',
      totalAppointments: 3,
      totalSpent: 500,
      lastAppointment: '2025-07-10',
      notes: 'Flexible scheduling',
      timezone: 'PST'
    },
    {
      id: '3',
      name: 'Mike Brown',
      email: 'mike@example.com',
      phone: '+1 (555) 345-6789',
      totalAppointments: 8,
      totalSpent: 1200,
      lastAppointment: '2025-07-18',
      notes: 'Prefers afternoon slots',
      timezone: 'CST'
    }
  ];

  useEffect(() => {
    setServices(mockServices);
    setAppointments(mockAppointments);
    setClients(mockClients);
    setLoading(false);
  }, []);

  const getAppointmentsForDate = (date) => {
    const dateString = date.toISOString().split('T')[0];
    return appointments.filter(apt => apt.date === dateString);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'confirmed': return 'text-green-600 bg-green-100 dark:bg-green-900/20';
      case 'pending': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/20';
      case 'cancelled': return 'text-red-600 bg-red-100 dark:bg-red-900/20';
      case 'completed': return 'text-blue-600 bg-blue-100 dark:bg-blue-900/20';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900/20';
    }
  };

  const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const hour12 = hours % 12 || 12;
    const ampm = hours < 12 ? 'AM' : 'PM';
    return `${hour12}:${minutes} ${ampm}`;
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(price);
  };

  const AppointmentCard = ({ appointment }) => (
    <motion.div
      layout
      className="bg-card border border-default rounded-xl p-4 hover:shadow-md transition-all duration-300"
    >
      <div className="flex items-start justify-between mb-3">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-1">
            <h3 className="font-semibold text-primary">{appointment.serviceName}</h3>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(appointment.status)}`}>
              {appointment.status}
            </span>
          </div>
          <p className="text-sm text-secondary mb-2">{appointment.clientName}</p>
          <div className="flex items-center space-x-4 text-xs text-secondary">
            <div className="flex items-center space-x-1">
              <ClockIcon className="w-3 h-3" />
              <span>{formatTime(appointment.startTime)} - {formatTime(appointment.endTime)}</span>
            </div>
            <div className="flex items-center space-x-1">
              <CreditCardIcon className="w-3 h-3" />
              <span>{formatPrice(appointment.price)}</span>
            </div>
          </div>
        </div>
        
        <div className="flex items-center space-x-2">
          <button className="p-1 text-blue-600 hover:text-blue-800 rounded">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-1 text-green-600 hover:text-green-800 rounded">
            <PencilIcon className="w-4 h-4" />
          </button>
          <button className="p-1 text-red-600 hover:text-red-800 rounded">
            <XMarkIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
      
      {appointment.notes && (
        <p className="text-xs text-secondary mb-2 p-2 bg-surface rounded border border-default">
          📝 {appointment.notes}
        </p>
      )}
      
      <div className="flex items-center justify-between pt-2 border-t border-default">
        <div className="flex items-center space-x-2">
          {appointment.depositPaid && (
            <div className="flex items-center space-x-1 text-green-600 text-xs">
              <CheckCircleIcon className="w-3 h-3" />
              <span>Deposit Paid</span>
            </div>
          )}
          {appointment.reminderSent && (
            <div className="flex items-center space-x-1 text-blue-600 text-xs">
              <BellIcon className="w-3 h-3" />
              <span>Reminder Sent</span>
            </div>
          )}
        </div>
        
        <div className="flex items-center space-x-2">
          <button className="text-xs text-accent-primary hover:opacity-80 font-medium">
            Contact Client
          </button>
        </div>
      </div>
    </motion.div>
  );

  const ServiceCard = ({ service }) => (
    <motion.div
      layout
      className="bg-card border border-default rounded-xl p-6"
    >
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <div 
              className="w-4 h-4 rounded-full"
              style={{ backgroundColor: service.color }}
            ></div>
            <h3 className="text-lg font-semibold text-primary">{service.name}</h3>
          </div>
          <p className="text-sm text-secondary mb-3">{service.description}</p>
          
          <div className="space-y-2">
            <div className="flex items-center justify-between text-sm">
              <span className="text-secondary">Duration:</span>
              <span className="text-primary font-medium">{service.duration} minutes</span>
            </div>
            <div className="flex items-center justify-between text-sm">
              <span className="text-secondary">Price:</span>
              <span className="text-primary font-medium">{formatPrice(service.price)}</span>
            </div>
            {service.requiresDeposit && (
              <div className="flex items-center justify-between text-sm">
                <span className="text-secondary">Deposit:</span>
                <span className="text-accent-primary font-medium">{formatPrice(service.depositAmount)}</span>
              </div>
            )}
          </div>
        </div>
        
        <div className="flex items-center space-x-2">
          <button className="p-2 text-blue-600 hover:text-blue-800 rounded-lg transition-colors">
            <PencilIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-red-600 hover:text-red-800 rounded-lg transition-colors">
            <TrashIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
      
      <div className="flex items-center justify-between pt-4 border-t border-default">
        <div className="flex items-center space-x-2">
          <span className={`px-2 py-1 rounded-full text-xs font-medium ${
            service.isActive ? 'text-green-600 bg-green-100 dark:bg-green-900/20' : 'text-gray-600 bg-gray-100 dark:bg-gray-900/20'
          }`}>
            {service.isActive ? 'Active' : 'Inactive'}
          </span>
          <span className="px-2 py-1 rounded-full text-xs font-medium text-purple-600 bg-purple-100 dark:bg-purple-900/20">
            {service.category}
          </span>
        </div>
        
        <button className="text-sm text-accent-primary hover:opacity-80 font-medium">
          Book Appointment
        </button>
      </div>
    </motion.div>
  );

  const ClientCard = ({ client }) => (
    <motion.div
      layout
      className="bg-card border border-default rounded-xl p-6"
    >
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-12 h-12 bg-accent-primary/20 rounded-full flex items-center justify-center">
            <UserIcon className="w-6 h-6 text-accent-primary" />
          </div>
          <div>
            <h3 className="text-lg font-semibold text-primary">{client.name}</h3>
            <p className="text-sm text-secondary">{client.email}</p>
            <p className="text-sm text-secondary">{client.phone}</p>
          </div>
        </div>
        
        <div className="flex items-center space-x-2">
          <button className="p-2 text-blue-600 hover:text-blue-800 rounded-lg transition-colors">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-green-600 hover:text-green-800 rounded-lg transition-colors">
            <EnvelopeIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
      
      <div className="grid grid-cols-3 gap-4 mb-4">
        <div className="text-center">
          <div className="text-xl font-bold text-primary">{client.totalAppointments}</div>
          <div className="text-xs text-secondary">Appointments</div>
        </div>
        <div className="text-center">
          <div className="text-xl font-bold text-green-600">{formatPrice(client.totalSpent)}</div>
          <div className="text-xs text-secondary">Total Spent</div>
        </div>
        <div className="text-center">
          <div className="text-sm font-bold text-purple-600">{client.timezone}</div>
          <div className="text-xs text-secondary">Timezone</div>
        </div>
      </div>
      
      <div className="space-y-2">
        <div className="text-sm">
          <span className="text-secondary">Last Appointment:</span>
          <span className="text-primary ml-2">{new Date(client.lastAppointment).toLocaleDateString()}</span>
        </div>
        {client.notes && (
          <div className="text-sm">
            <span className="text-secondary">Notes:</span>
            <p className="text-primary mt-1 text-xs p-2 bg-surface rounded border border-default">
              {client.notes}
            </p>
          </div>
        )}
      </div>
      
      <div className="flex space-x-2 mt-4">
        <button className="flex-1 bg-accent-primary text-white px-3 py-2 rounded-lg hover:opacity-90 transition-opacity text-sm font-medium">
          Book Appointment
        </button>
        <button className="px-3 py-2 border border-default rounded-lg hover:bg-hover transition-colors text-sm">
          Contact
        </button>
      </div>
    </motion.div>
  );

  if (loading) {
    return (
      <div className="min-h-screen bg-app flex items-center justify-center">
        <div className="animate-pulse text-primary">Loading booking system...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Booking System</h1>
            <p className="text-secondary mt-2">Manage appointments, services, and clients</p>
          </div>
          
          <div className="flex items-center space-x-4">
            <button className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2">
              <PlusIcon className="w-4 h-4" />
              <span>New Appointment</span>
            </button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div className="bg-card border border-default rounded-xl p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">Today's Appointments</p>
                <p className="text-2xl font-bold text-primary mt-2">
                  {getAppointmentsForDate(new Date()).length}
                </p>
              </div>
              <div className="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                <CalendarIcon className="w-6 h-6 text-blue-600" />
              </div>
            </div>
          </div>
          
          <div className="bg-card border border-default rounded-xl p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">Total Services</p>
                <p className="text-2xl font-bold text-primary mt-2">{services.filter(s => s.isActive).length}</p>
              </div>
              <div className="p-3 rounded-lg bg-green-100 dark:bg-green-900/20">
                <Cog6ToothIcon className="w-6 h-6 text-green-600" />
              </div>
            </div>
          </div>
          
          <div className="bg-card border border-default rounded-xl p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">Total Clients</p>
                <p className="text-2xl font-bold text-primary mt-2">{clients.length}</p>
              </div>
              <div className="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                <UsersIcon className="w-6 h-6 text-purple-600" />
              </div>
            </div>
          </div>
          
          <div className="bg-card border border-default rounded-xl p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">Month Revenue</p>
                <p className="text-2xl font-bold text-primary mt-2">
                  {formatPrice(appointments.reduce((sum, apt) => sum + apt.price, 0))}
                </p>
              </div>
              <div className="p-3 rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                <ChartBarIcon className="w-6 h-6 text-emerald-600" />
              </div>
            </div>
          </div>
        </div>

        {/* Tabs */}
        <div className="flex space-x-1 bg-card border border-default rounded-lg p-1 mb-8">
          {['calendar', 'appointments', 'services', 'clients', 'analytics'].map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`flex-1 px-4 py-2 rounded text-sm font-medium transition-colors ${
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
        {activeTab === 'calendar' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Calendar */}
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="text-xl font-semibold text-primary mb-4">Calendar</h3>
              <div className="text-center text-secondary">
                <CalendarDaysIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
                <p>Calendar component would go here</p>
                <p className="text-sm mt-2">Click on dates to view appointments</p>
              </div>
            </div>

            {/* Today's Appointments */}
            <div className="lg:col-span-2">
              <div className="bg-card border border-default rounded-xl p-6">
                <h3 className="text-xl font-semibold text-primary mb-4">
                  Today's Appointments ({getAppointmentsForDate(new Date()).length})
                </h3>
                
                {getAppointmentsForDate(new Date()).length === 0 ? (
                  <div className="text-center py-8 text-secondary">
                    <TimeIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
                    <p>No appointments scheduled for today</p>
                  </div>
                ) : (
                  <div className="space-y-4">
                    {getAppointmentsForDate(new Date()).map(appointment => (
                      <AppointmentCard key={appointment.id} appointment={appointment} />
                    ))}
                  </div>
                )}
              </div>
            </div>
          </div>
        )}

        {activeTab === 'appointments' && (
          <div className="bg-card border border-default rounded-xl p-6">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-xl font-semibold text-primary">All Appointments</h3>
              <div className="flex items-center space-x-2">
                <select className="px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary outline-none">
                  <option value="all">All Status</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="pending">Pending</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="completed">Completed</option>
                </select>
                <button className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                  Filter
                </button>
              </div>
            </div>
            
            <div className="space-y-4">
              {appointments.map(appointment => (
                <AppointmentCard key={appointment.id} appointment={appointment} />
              ))}
            </div>
          </div>
        )}

        {activeTab === 'services' && (
          <div>
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-xl font-semibold text-primary">Services</h3>
              <button className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2">
                <PlusIcon className="w-4 h-4" />
                <span>Add Service</span>
              </button>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {services.map(service => (
                <ServiceCard key={service.id} service={service} />
              ))}
            </div>
          </div>
        )}

        {activeTab === 'clients' && (
          <div>
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-xl font-semibold text-primary">Clients</h3>
              <button className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2">
                <PlusIcon className="w-4 h-4" />
                <span>Add Client</span>
              </button>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {clients.map(client => (
                <ClientCard key={client.id} client={client} />
              ))}
            </div>
          </div>
        )}

        {activeTab === 'analytics' && (
          <div className="bg-card border border-default rounded-xl p-6">
            <h3 className="text-xl font-semibold text-primary mb-6">Booking Analytics</h3>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <div className="bg-surface border border-default rounded-lg p-4 text-center">
                <div className="text-2xl font-bold text-primary">89%</div>
                <div className="text-sm text-secondary">Booking Rate</div>
              </div>
              <div className="bg-surface border border-default rounded-lg p-4 text-center">
                <div className="text-2xl font-bold text-primary">4.2</div>
                <div className="text-sm text-secondary">Avg Rating</div>
              </div>
              <div className="bg-surface border border-default rounded-lg p-4 text-center">
                <div className="text-2xl font-bold text-primary">73</div>
                <div className="text-sm text-secondary">Minutes Avg</div>
              </div>
              <div className="bg-surface border border-default rounded-lg p-4 text-center">
                <div className="text-2xl font-bold text-primary">15%</div>
                <div className="text-sm text-secondary">No-show Rate</div>
              </div>
            </div>
            
            <div className="text-center text-secondary">
              <ChartBarIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
              <p>Advanced analytics charts would display here</p>
              <p className="text-sm mt-2">Revenue trends, booking patterns, and performance metrics</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default ComprehensiveBookingSystem;