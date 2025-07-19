import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  CalendarDaysIcon, 
  PlusIcon, 
  ClockIcon,
  UserIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  XCircleIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  CurrencyDollarIcon,
  PhoneIcon,
  EnvelopeIcon,
  MapPinIcon,
  StarIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';
import CreateAppointmentModal from '../../components/CreateAppointmentModal';
import CreateServiceModal from '../../components/CreateServiceModal';
import { bookingAPI } from '../../services/api';
import toast from 'react-hot-toast';

const AdvancedBookingPage = () => {
  const [services, setServices] = useState([]);
  const [appointments, setAppointments] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');
  const [selectedService, setSelectedService] = useState(null);
  const [showCreateServiceModal, setShowCreateServiceModal] = useState(false);
  const [showCreateAppointmentModal, setShowCreateAppointmentModal] = useState(false);

  useEffect(() => {
    loadBookingData();
  }, []);

  const loadBookingData = async () => {
    try {
      // Try to load from API first, fall back to mock data
      try {
        const servicesResponse = await bookingAPI.getServices();
        const appointmentsResponse = await bookingAPI.getAppointments();
        const analyticsResponse = await bookingAPI.getAnalytics();
        
        setServices(servicesResponse.data.services || []);
        setAppointments(appointmentsResponse.data.appointments || []);
        setAnalytics(analyticsResponse.data);
      } catch (apiError) {
        console.log('API not available, using mock data:', apiError.message);
        // Fall back to mock data
        setServices([
          {
            id: 1,
            name: 'Business Consultation',
            description: 'One-on-one business strategy consultation',
            duration: 60,
            price: 150,
            category: 'Consultation',
            status: 'active',
            bookings: 23,
            availability: 'Available'
          },
          {
            id: 2,
            name: 'Digital Marketing Strategy',
            description: 'Comprehensive digital marketing planning session',
            duration: 90,
            price: 200,
            category: 'Marketing',
            status: 'active',
            bookings: 18,
            availability: 'Available'
          },
          {
            id: 3,
            name: 'Technical Support',
            description: 'Technical assistance and troubleshooting',
            duration: 30,
            price: 75,
            category: 'Support',
            status: 'active',
            bookings: 41,
            availability: 'Busy'
          }
        ]);

        setAppointments([
          {
            id: 1,
            service: 'Business Consultation',
            client: 'John Smith',
            clientEmail: 'john@example.com',
            clientPhone: '+1-555-0123',
            date: '2025-07-20',
            time: '10:00',
            duration: 60,
            status: 'confirmed',
            amount: 150,
            notes: 'First-time consultation for startup planning'
          },
          {
            id: 2,
            service: 'Digital Marketing Strategy',
            client: 'Sarah Johnson',
            clientEmail: 'sarah@company.com',
            clientPhone: '+1-555-0456',
            date: '2025-07-20',
            time: '14:30',
            duration: 90,
            status: 'pending',
            amount: 200,
            notes: 'E-commerce marketing strategy review'
          },
          {
            id: 3,
            service: 'Technical Support',
            client: 'Mike Wilson',
            clientEmail: 'mike@tech.com',
            clientPhone: '+1-555-0789',
            date: '2025-07-19',
            time: '16:00',
            duration: 30,
            status: 'completed',
            amount: 75,
            notes: 'WordPress website issues resolved'
          }
        ]);

        setAnalytics({
          totalBookings: 82,
          totalRevenue: 12340,
          averageRating: 4.8,
          completionRate: 94.5,
          upcomingAppointments: 7,
          monthlyGrowth: 23.5
        });
      }
    } catch (error) {
      console.error('Failed to load booking data:', error);
      toast.error('Failed to load booking data');
    } finally {
      setLoading(false);
    }
  };

  const handleCreateService = (newService) => {
    setServices(prev => [newService, ...prev]);
    toast.success('Service created successfully!');
  };

  const handleCreateAppointment = (newAppointment) => {
    setAppointments(prev => [newAppointment, ...prev]);
    toast.success('Appointment created successfully!');
  };

  const handleServiceAction = (action, service) => {
    switch (action) {
      case 'view':
        setSelectedService(service);
        break;
      case 'edit':
        // Open edit modal
        toast.info('Edit functionality coming soon');
        break;
      case 'delete':
        if (window.confirm('Are you sure you want to delete this service?')) {
          setServices(prev => prev.filter(s => s.id !== service.id));
          toast.success('Service deleted successfully');
        }
        break;
      case 'book':
        setShowCreateAppointmentModal(true);
        break;
      default:
        break;
    }
  };

  const handleAppointmentAction = (action, appointment) => {
    switch (action) {
      case 'view':
        toast.info('View appointment details coming soon');
        break;
      case 'edit':
        toast.info('Edit appointment coming soon');
        break;
      case 'confirm':
        setAppointments(prev => 
          prev.map(apt => 
            apt.id === appointment.id 
              ? { ...apt, status: 'confirmed' }
              : apt
          )
        );
        toast.success('Appointment confirmed');
        break;
      case 'cancel':
        if (window.confirm('Are you sure you want to cancel this appointment?')) {
          setAppointments(prev => 
            prev.map(apt => 
              apt.id === appointment.id 
                ? { ...apt, status: 'cancelled' }
                : apt
            )
          );
          toast.success('Appointment cancelled');
        }
        break;
      default:
        break;
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary', suffix = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}{suffix}</p>
          {change && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% from last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const ServiceCard = ({ service }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-12 h-12 bg-gradient-primary rounded-lg flex items-center justify-center">
            <CalendarDaysIcon className="w-6 h-6 text-white" />
          </div>
          <div>
            <h3 className="font-semibold text-primary">{service.name}</h3>
            <p className="text-sm text-secondary">{service.category}</p>
          </div>
        </div>
        <span className={`px-3 py-1 rounded-full text-xs font-medium ${
          service.availability === 'Available'
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
        }`}>
          {service.availability}
        </span>
      </div>

      <p className="text-secondary text-sm mb-4">{service.description}</p>

      <div className="grid grid-cols-3 gap-4 mb-4 text-sm">
        <div>
          <p className="text-secondary">Duration</p>
          <p className="font-medium text-primary">{service.duration} min</p>
        </div>
        <div>
          <p className="text-secondary">Price</p>
          <p className="font-medium text-primary">${service.price}</p>
        </div>
        <div>
          <p className="text-secondary">Bookings</p>
          <p className="font-medium text-primary">{service.bookings}</p>
        </div>
      </div>

      <div className="flex items-center space-x-2">
        <Button 
          variant="primary" 
          size="small" 
          className="flex-1"
          onClick={() => handleServiceAction('book', service)}
        >
          <CalendarDaysIcon className="w-4 h-4 mr-1" />
          Book Now
        </Button>
        <button 
          className="p-2 text-secondary hover:text-primary"
          onClick={() => handleServiceAction('view', service)}
        >
          <EyeIcon className="w-4 h-4" />
        </button>
        <button 
          className="p-2 text-secondary hover:text-primary"
          onClick={() => handleServiceAction('edit', service)}
        >
          <PencilIcon className="w-4 h-4" />
        </button>
      </div>
    </div>
  );

  const AppointmentCard = ({ appointment }) => (
    <div className="card p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <h3 className="font-semibold text-primary">{appointment.service}</h3>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              appointment.status === 'confirmed'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : appointment.status === 'pending'
                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                : appointment.status === 'completed'
                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            }`}>
              {appointment.status}
            </span>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div className="space-y-2">
              <div className="flex items-center space-x-2 text-secondary">
                <UserIcon className="w-4 h-4" />
                <span>{appointment.client}</span>
              </div>
              <div className="flex items-center space-x-2 text-secondary">
                <EnvelopeIcon className="w-4 h-4" />
                <span>{appointment.clientEmail}</span>
              </div>
              <div className="flex items-center space-x-2 text-secondary">
                <PhoneIcon className="w-4 h-4" />
                <span>{appointment.clientPhone}</span>
              </div>
            </div>
            <div className="space-y-2">
              <div className="flex items-center space-x-2 text-secondary">
                <CalendarDaysIcon className="w-4 h-4" />
                <span>{appointment.date}</span>
              </div>
              <div className="flex items-center space-x-2 text-secondary">
                <ClockIcon className="w-4 h-4" />
                <span>{appointment.time} ({appointment.duration} min)</span>
              </div>
              <div className="flex items-center space-x-2 text-secondary">
                <CurrencyDollarIcon className="w-4 h-4" />
                <span>${appointment.amount}</span>
              </div>
            </div>
          </div>
          
          {appointment.notes && (
            <div className="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
              <p className="text-sm text-secondary">{appointment.notes}</p>
            </div>
          )}
        </div>
        
        <div className="flex items-center space-x-2 ml-4">
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
          {appointment.status === 'pending' && (
            <>
              <button className="p-2 text-accent-success hover:text-green-700">
                <CheckCircleIcon className="w-4 h-4" />
              </button>
              <button className="p-2 text-accent-danger hover:text-red-700">
                <XCircleIcon className="w-4 h-4" />
              </button>
            </>
          )}
        </div>
      </div>
    </div>
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  return (
    <>
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">Advanced Booking System</h1>
          <p className="text-secondary mt-1">Manage your services, appointments, and availability</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary" onClick={() => setShowCreateServiceModal(true)}>
            <PlusIcon className="w-4 h-4 mr-2" />
            New Service
          </Button>
          <Button onClick={() => setShowCreateAppointmentModal(true)}>
            <CalendarDaysIcon className="w-4 h-4 mr-2" />
            New Appointment
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'appointments', name: 'Appointments' },
            { id: 'services', name: 'Services' },
            { id: 'calendar', name: 'Calendar' },
            { id: 'analytics', name: 'Analytics' }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`py-2 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-accent-primary text-accent-primary'
                  : 'border-transparent text-secondary hover:text-primary hover:border-gray-300'
              }`}
            >
              {tab.name}
            </button>
          ))}
        </nav>
      </div>

      {/* Content based on active tab */}
      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* Analytics Stats */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Total Bookings"
              value={analytics.totalBookings.toString()}
              change={analytics.monthlyGrowth}
              icon={CalendarDaysIcon}
              color="primary"
            />
            <StatCard
              title="Total Revenue"
              value={`$${analytics.totalRevenue.toLocaleString()}`}
              change={18.5}
              icon={CurrencyDollarIcon}
              color="success"
            />
            <StatCard
              title="Average Rating"
              value={analytics.averageRating.toString()}
              icon={StarIcon}
              color="warning"
            />
            <StatCard
              title="Completion Rate"
              value={analytics.completionRate.toString()}
              icon={CheckCircleIcon}
              color="primary"
              suffix="%"
            />
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <CalendarDaysIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Schedule Appointment</h3>
              <p className="text-secondary">Book a new appointment for a client</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <DocumentTextIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Manage Services</h3>
              <p className="text-secondary">Configure your available services and pricing</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ClockIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Set Availability</h3>
              <p className="text-secondary">Update your schedule and working hours</p>
            </button>
          </div>

          {/* Recent Activity */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Upcoming Appointments</h2>
              <div className="space-y-4">
                {appointments.filter(apt => apt.status === 'confirmed').slice(0, 3).map((appointment) => (
                  <div key={appointment.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{appointment.service}</h4>
                        <p className="text-sm text-secondary">{appointment.client} • {appointment.date} at {appointment.time}</p>
                      </div>
                      <span className="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-medium">
                        {appointment.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Popular Services</h2>
              <div className="space-y-4">
                {services.sort((a, b) => b.bookings - a.bookings).slice(0, 3).map((service) => (
                  <div key={service.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{service.name}</h4>
                        <p className="text-sm text-secondary">{service.bookings} bookings • ${service.price}</p>
                      </div>
                      <span className="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-xs font-medium">
                        {service.availability}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'appointments' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">All Appointments</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Status</option>
                <option>Confirmed</option>
                <option>Pending</option>
                <option>Completed</option>
                <option>Cancelled</option>
              </select>
              <Button onClick={() => setShowCreateAppointmentModal(true)}>
                <PlusIcon className="w-4 h-4 mr-2" />
                New Appointment
              </Button>
            </div>
          </div>
          
          <div className="space-y-4">
            {appointments.map((appointment) => (
              <AppointmentCard key={appointment.id} appointment={appointment} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'services' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Services</h2>
            <Button onClick={() => setShowCreateServiceModal(true)}>
              <PlusIcon className="w-4 h-4 mr-2" />
              Create Service
            </Button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {services.map((service) => (
              <ServiceCard key={service.id} service={service} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'calendar' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Calendar View</h2>
          <div className="card-elevated p-8 text-center">
            <CalendarDaysIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Calendar Integration</h3>
            <p className="text-secondary mb-4">Interactive calendar view for managing appointments and availability</p>
            <Button>
              View Full Calendar
            </Button>
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Booking Analytics</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Monthly Revenue"
              value={`$${analytics.totalRevenue.toLocaleString()}`}
              change={15.3}
              icon={CurrencyDollarIcon}
              color="success"
            />
            <StatCard
              title="Booking Rate"
              value="73.2"
              change={8.7}
              icon={CalendarDaysIcon}
              color="primary"
              suffix="%"
            />
            <StatCard
              title="Client Retention"
              value="68.5"
              change={12.1}
              icon={UserIcon}
              color="warning"
              suffix="%"
            />
            <StatCard
              title="Average Session"
              value="67"
              icon={ClockIcon}
              color="primary"
              suffix=" min"
            />
          </div>

          <div className="card-elevated p-8 text-center">
            <DocumentTextIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics</h3>
            <p className="text-secondary mb-4">Comprehensive booking analytics and performance metrics</p>
            <Button>
              View Detailed Reports
            </Button>
          </div>
        </div>
      )}
    </div>

    {/* Modals */}
    <CreateServiceModal
      isOpen={showCreateServiceModal}
      onClose={() => setShowCreateServiceModal(false)}
      onSuccess={handleCreateService}
    />
    
    <CreateAppointmentModal
      isOpen={showCreateAppointmentModal}
      onClose={() => setShowCreateAppointmentModal(false)}
      onSuccess={handleCreateAppointment}
      services={services}
    />
  </>
  );
};

export default AdvancedBookingPage;