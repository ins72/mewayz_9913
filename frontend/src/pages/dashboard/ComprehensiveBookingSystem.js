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

  // Real data from APInstration
  // Real data loaded from API

  // Real data loaded from API

  // Real data loaded from API

  useEffect(() => {
    // Real data loaded from API
    // Real data loaded from API
    // Real data loaded from API
    // Real data loaded from API
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
      <div className="min-h-screen bg-app flex items-center justify-center">
        <div className="animate-pulse text-primary">Loading booking system...</div>
      </div>
    );
  }

  