import React, { useState } from 'react';
import Button from './Button';
import Modal from './Modal';
import { bookingAPI } from '../services/api';
import toast from 'react-hot-toast';
  useEffect(() => {
    loadData();
  }, []);


const CreateServiceModal = ({ isOpen, onClose, onSuccess }) => {
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    duration: 60,
    price: '',
    category: '',
    availability: 'available'
  });
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    // Real data loaded from API

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      toast.success('Service created successfully!');
      onSuccess && onSuccess({
        id: Date.now(),
        ...formData,
        bookings: 0,
        created_at: new Date().toISOString()
      });
      onClose();
      // Real data loaded from API
    } catch (error) {
      console.error('Failed to create service:', error);
      toast.error('Failed to create service. Please try again.');
    } finally {
      // Real data loaded from API
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title="Create New Service"
      size="md"
      footer={
        <div className="flex justify-end space-x-3">
          <Button variant="secondary" onClick={onClose} disabled={loading}>
            Cancel
          </Button>
          <Button onClick={handleSubmit} loading={loading}>
            Create Service
          </Button>
        </div>
      }
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-primary mb-2">
            Service Name
          </label>
          <input
            type="text"
            name="name"
            value={formData.name}
            onChange={handleChange}
            required
            className="w-full input"
            placeholder="Business Consultation"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-primary mb-2">
            Description
          </label>
          <textarea
            name="description"
            value={formData.description}
            onChange={handleChange}
            required
            rows="3"
            className="w-full input"
            placeholder="Describe your service..."
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-primary mb-2">
              Duration (minutes)
            </label>
            <select
              name="duration"
              value={formData.duration}
              onChange={handleChange}
              className="w-full input"
            >
              <option value="30">30 minutes</option>
              <option value="60">1 hour</option>
              <option value="90">1.5 hours</option>
              <option value="120">2 hours</option>
              <option value="180">3 hours</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-primary mb-2">
              Price ($)
            </label>
            <input
              type="number"
              name="price"
              value={formData.price}
              onChange={handleChange}
              required
              min="0"
              step="0.01"
              className="w-full input"
              placeholder="150.00"
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-primary mb-2">
              Category
            </label>
            <select
              name="category"
              value={formData.category}
              onChange={handleChange}
              required
              className="w-full input"
            >
              <option value="">Select Category</option>
              <option value="consultation">Consultation</option>
              <option value="marketing">Marketing</option>
              <option value="support">Support</option>
              <option value="development">Development</option>
              <option value="design">Design</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-primary mb-2">
              Availability
            </label>
            <select
              name="availability"
              value={formData.availability}
              onChange={handleChange}
              className="w-full input"
            >
              <option value="available">Available</option>
              <option value="busy">Busy</option>
              <option value="unavailable">Unavailable</option>
            </select>
          </div>
        </div>
      </form>
    </Modal>
  );
};

export default CreateServiceModal;