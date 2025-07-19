import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { bookingAPI } from '../../services/api';
import CreateAppointmentModal from '../../components/CreateAppointmentModal';
import CreateServiceModal from '../../components/CreateServiceModal';
import ViewDetailsModal from '../../components/modals/ViewDetailsModal';
import {
  CalendarIcon,
  ClockIcon,
  UserGroupIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  PlusIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  PhoneIcon,
  VideoCallIcon,
  MapPinIcon
} from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';
import { format, startOfWeek, addDays, isSameDay, parseISO } from 'date-fns';

const AdvancedBookingPage = () => {
  const { user } = useAuth();
  const [bookingData, setBookingData] = useState(null);
  const [services, setServices] = useState([]);
  const [appointments, setAppointments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedDate, setSelectedDate] = useState(new Date());
  const [viewMode, setViewMode] = useState('dashboard'); // dashboard, calendar, services, appointments
  const [showCreateBookingModal, setShowCreateBookingModal] = useState(false);
  const [showCreateServiceModal, setShowCreateServiceModal] = useState(false);
  const [showViewModal, setShowViewModal] = useState(false);
  const [selectedItem, setSelectedItem] = useState(null);

  useEffect(() => {
    fetchBookingData();
  }, []);

  const fetchBookingData = async () => {
    try {
      setLoading(true);
      const [dashboardResponse, servicesResponse, appointmentsResponse] = await Promise.all([
        fetch('/api/bookings/dashboard').then(res => res.json()).catch(() => ({ data: mockDashboardData })),
        bookingAPI.getServices(),
        bookingAPI.getAppointments()
      ]);
      
      setBookingData(dashboardResponse.data || mockDashboardData);
      setServices(servicesResponse.data.data.services || []);
      setAppointments(appointmentsResponse.data.data.appointments || []);
    } catch (error) {
      console.error('Failed to fetch booking data:', error);
      // Set mock data on error
      setBookingData(mockDashboardData);
      toast.error('Failed to load booking data');
    } finally {
      setLoading(false);
    }
  };

  // Mock data for demonstration
  const mockDashboardData = {
    booking_metrics: {
      total_bookings: 847,
      upcoming_bookings: 23,
      confirmed_bookings: 89,
      completed_bookings: 720,
      cancelled_bookings: 15,
      no_show_rate: 3.2,
      revenue_generated: 45670.25,
      avg_booking_value: 53.87
    },
    calendar_overview: {
      today_appointments: 5,
      this_week: 28,
      next_week: 31,
      utilization_rate: 78.3,
      peak_hours: ["10:00-11:00", "14:00-15:00", "16:00-17:00"],
      available_slots: 156
    },
    service_performance: [
      { name: "Business Consultation", bookings: 245, revenue: 18375, avg_duration: 60 },
      { name: "Strategy Session", bookings: 189, revenue: 14175, avg_duration: 90 },
      { name: "Quick Review", bookings: 156, revenue: 4680, avg_duration: 30 }
    ]
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-primary"></div>
      </div>
    );
  }

  const stats = [
    {
      name: 'Total Bookings',
      value: bookingData?.booking_metrics?.total_bookings || 0,
      change: '+12%',
      changeType: 'increase',
      icon: CalendarIcon,
      color: 'bg-blue-500'
    },
    {
      name: 'Revenue Generated',
      value: `$${(bookingData?.booking_metrics?.revenue_generated || 0).toLocaleString()}`,
      change: '+18%',
      changeType: 'increase',
      icon: CurrencyDollarIcon,
      color: 'bg-green-500'
    },
    {
      name: 'Upcoming Bookings',
      value: bookingData?.booking_metrics?.upcoming_bookings || 0,
      change: '+5%',
      changeType: 'increase',
      icon: ClockIcon,
      color: 'bg-purple-500'
    },
    {
      name: 'Utilization Rate',
      value: `${bookingData?.calendar_overview?.utilization_rate || 0}%`,
      change: '+3%',
      changeType: 'increase',
      icon: ChartBarIcon,
      color: 'bg-indigo-500'
    }
  ];

  const renderDashboard = () => (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat, index) => (
          <motion.div
            key={stat.name}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: index * 0.1 }}
            className="bg-surface-elevated p-6 rounded-lg shadow-default"
          >
            <div className="flex items-center">
              <div className={`flex-shrink-0 p-3 rounded-lg ${stat.color}`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-secondary">{stat.name}</p>
                <p className="text-2xl font-semibold text-primary">{stat.value}</p>
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Calendar Overview & Service Performance */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Today's Schedule */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Today's Schedule</h3>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 bg-surface rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="w-3 h-3 bg-blue-500 rounded-full"></div>
                <div>
                  <p className="font-medium text-primary">Strategy Session</p>
                  <p className="text-sm text-secondary">with Sarah Johnson</p>
                </div>
              </div>
              <div className="text-right">
                <p className="text-sm font-medium text-primary">10:00 AM</p>
                <p className="text-xs text-secondary">90 min</p>
              </div>
            </div>
            
            <div className="flex items-center justify-between p-4 bg-surface rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                <div>
                  <p className="font-medium text-primary">Business Consultation</p>
                  <p className="text-sm text-secondary">with Mike Chen</p>
                </div>
              </div>
              <div className="text-right">
                <p className="text-sm font-medium text-primary">2:00 PM</p>
                <p className="text-xs text-secondary">60 min</p>
              </div>
            </div>

            <div className="flex items-center justify-between p-4 bg-surface rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="w-3 h-3 bg-purple-500 rounded-full"></div>
                <div>
                  <p className="font-medium text-primary">Quick Review</p>
                  <p className="text-sm text-secondary">with Lisa Rodriguez</p>
                </div>
              </div>
              <div className="text-right">
                <p className="text-sm font-medium text-primary">4:30 PM</p>
                <p className="text-xs text-secondary">30 min</p>
              </div>
            </div>
          </div>
        </motion.div>

        {/* Service Performance */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.3 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Service Performance</h3>
          <div className="space-y-4">
            {bookingData?.service_performance?.map((service, index) => (
              <div key={index} className="flex items-center justify-between">
                <div>
                  <p className="font-medium text-primary">{service.name}</p>
                  <p className="text-sm text-secondary">{service.bookings} bookings</p>
                </div>
                <div className="text-right">
                  <p className="font-medium text-green-500">${service.revenue.toLocaleString()}</p>
                  <p className="text-sm text-secondary">{service.avg_duration} min avg</p>
                </div>
              </div>
            ))}
          </div>
        </motion.div>
      </div>

      {/* Peak Hours & Available Slots */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Peak Booking Hours</h3>
          <div className="space-y-3">
            {bookingData?.calendar_overview?.peak_hours?.map((hour, index) => (
              <div key={index} className="flex items-center justify-between p-3 bg-surface rounded-lg">
                <span className="text-primary font-medium">{hour}</span>
                <div className="w-20 bg-surface-hover rounded-full h-2">
                  <div 
                    className="h-2 bg-accent-primary rounded-full"
                    style={{ width: `${80 - (index * 15)}%` }}
                  />
                </div>
              </div>
            ))}
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.5 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Quick Actions</h3>
          <div className="space-y-3">
            <button 
              className="w-full flex items-center justify-center p-3 bg-accent-primary hover:bg-accent-primary/90 text-white rounded-lg transition-colors"
              onClick={() => setShowCreateBookingModal(true)}
            >
              <PlusIcon className="h-5 w-5 mr-2" />
              New Booking
            </button>
            <button 
              className="w-full flex items-center justify-center p-3 bg-surface hover:bg-surface-hover text-primary rounded-lg transition-colors"
              onClick={() => setViewMode('calendar')}
            >
              <CalendarIcon className="h-5 w-5 mr-2" />
              View Calendar
            </button>
            <button 
              className="w-full flex items-center justify-center p-3 bg-surface hover:bg-surface-hover text-primary rounded-lg transition-colors"
              onClick={() => setViewMode('appointments')}
            >
              <ChartBarIcon className="h-5 w-5 mr-2" />
              Analytics Report
            </button>
          </div>
        </motion.div>
      </div>
    </div>
  );

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="flex items-center justify-between"
      >
        <div>
          <h1 className="text-3xl font-bold text-primary mb-2 flex items-center">
            <CalendarIcon className="h-8 w-8 text-accent-primary mr-3" />
            Advanced Booking System
          </h1>
          <p className="text-secondary">
            Manage your appointments, services, and booking analytics.
          </p>
        </div>
        
        <div className="flex items-center space-x-4">
          <div className="flex bg-surface rounded-lg p-1">
            {['dashboard', 'calendar', 'services', 'appointments'].map((mode) => (
              <button
                key={mode}
                onClick={() => setViewMode(mode)}
                className={`px-4 py-2 text-sm font-medium capitalize rounded-md transition-colors ${
                  viewMode === mode
                    ? 'bg-accent-primary text-white'
                    : 'text-secondary hover:text-primary'
                }`}
              >
                {mode}
              </button>
            ))}
          </div>
        </div>
      </motion.div>

      {/* Content based on view mode */}
      {viewMode === 'dashboard' && renderDashboard()}
      
      {viewMode === 'calendar' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="bg-surface-elevated p-6 rounded-lg shadow-default"
        >
          <h3 className="text-lg font-semibold text-primary mb-4">Calendar View</h3>
          <div className="text-center text-secondary py-12">
            <CalendarIcon className="h-16 w-16 mx-auto mb-4 opacity-50" />
            <p>Calendar integration coming soon...</p>
          </div>
        </motion.div>
      )}

      {viewMode === 'services' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="space-y-6"
        >
          <div className="flex justify-between items-center">
            <h3 className="text-lg font-semibold text-primary">Services</h3>
            <button className="btn-primary flex items-center">
              <PlusIcon className="h-5 w-5 mr-2" />
              Add Service
            </button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {services.length > 0 ? services.map((service, index) => (
              <div key={service.id} className="bg-surface-elevated p-6 rounded-lg shadow-default">
                <h4 className="font-semibold text-primary mb-2">{service.name}</h4>
                <p className="text-sm text-secondary mb-4">{service.description}</p>
                <div className="flex items-center justify-between">
                  <span className="text-lg font-bold text-accent-primary">${service.price}</span>
                  <span className="text-sm text-secondary">{service.duration} min</span>
                </div>
              </div>
            )) : (
              <div className="col-span-full text-center py-12">
                <UserGroupIcon className="h-16 w-16 mx-auto mb-4 opacity-50 text-secondary" />
                <p className="text-secondary">No services found. Create your first service!</p>
              </div>
            )}
          </div>
        </motion.div>
      )}

      {viewMode === 'appointments' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="space-y-6"
        >
          <div className="flex justify-between items-center">
            <h3 className="text-lg font-semibold text-primary">Appointments</h3>
            <button className="btn-primary flex items-center">
              <PlusIcon className="h-5 w-5 mr-2" />
              New Appointment
            </button>
          </div>
          
          <div className="bg-surface-elevated rounded-lg shadow-default overflow-hidden">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-default">
                <thead className="bg-surface">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Client
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Service
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Date & Time
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Status
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-surface-elevated divide-y divide-default">
                  {appointments.length > 0 ? appointments.map((appointment, index) => (
                    <tr key={appointment.id}>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div>
                          <div className="text-sm font-medium text-primary">{appointment.client_name}</div>
                          <div className="text-sm text-secondary">{appointment.client_email}</div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-primary">{appointment.service_name}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-primary">
                          {format(parseISO(appointment.scheduled_at), 'MMM dd, yyyy')}
                        </div>
                        <div className="text-sm text-secondary">
                          {format(parseISO(appointment.scheduled_at), 'h:mm a')}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                          appointment.status === 'confirmed' ? 'bg-green-100 text-green-800' :
                          appointment.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                          'bg-red-100 text-red-800'
                        }`}>
                          {appointment.status}
                        </span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div className="flex items-center space-x-2">
                          <button className="text-blue-600 hover:text-blue-900">
                            <EyeIcon className="h-4 w-4" />
                          </button>
                          <button className="text-green-600 hover:text-green-900">
                            <PencilIcon className="h-4 w-4" />
                          </button>
                          <button className="text-red-600 hover:text-red-900">
                            <TrashIcon className="h-4 w-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  )) : (
                    <tr>
                      <td colSpan="5" className="px-6 py-12 text-center">
                        <ClockIcon className="h-16 w-16 mx-auto mb-4 opacity-50 text-secondary" />
                        <p className="text-secondary">No appointments found.</p>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </motion.div>
      )}
    </div>
  );
};

export default AdvancedBookingPage;