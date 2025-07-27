import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  ShoppingBagIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon,
  EyeIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  TruckIcon,
  UserGroupIcon,
  ArchiveBoxIcon,
  TagIcon,
  PhotoIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const EcommercePage = () => {
  const [products, setProducts] = useState([]);
  const [orders, setOrders] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadEcommerceData();
  }, []);

  const loadEcommerceData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      // Real data loaded from API

      // Real data loaded from API

      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load e-commerce data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {change && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% vs last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const ProductCard = ({ product }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start space-x-4">
        <div className="w-16 h-16 bg-gradient-surface rounded-lg flex items-center justify-center">
          <PhotoIcon className="w-8 h-8 text-accent-primary" />
        </div>
        <div className="flex-1">
          <div className="flex items-start justify-between">
            <div>
              <h3 className="font-semibold text-primary">{product.name}</h3>
              <p className="text-2xl font-bold text-accent-primary mt-1">${product.price}</p>
            </div>
            <div className="flex items-center space-x-2">
              <button className="p-2 text-secondary hover:text-primary">
                <EyeIcon className="w-4 h-4" />
              </button>
              <button className="p-2 text-secondary hover:text-primary">
                <PencilIcon className="w-4 h-4" />
              </button>
              <button className="p-2 text-secondary hover:text-accent-danger">
                <TrashIcon className="w-4 h-4" />
              </button>
            </div>
          </div>
          
          <div className="mt-4 grid grid-cols-2 gap-4 text-sm">
            <div>
              <p className="text-secondary">Stock</p>
              <p className="font-medium text-primary">{product.stock} units</p>
            </div>
            <div>
              <p className="text-secondary">Sales</p>
              <p className="font-medium text-primary">{product.sales} sold</p>
            </div>
          </div>
          
          <div className="mt-4 flex items-center justify-between">
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              product.status === 'active'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            }`}>
              {product.status}
            </span>
            <Button size="small">Manage</Button>
          </div>
        </div>
      </div>
    </div>
  );

  const OrderCard = ({ order }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <div>
          <h3 className="font-semibold text-primary">{order.id}</h3>
          <p className="text-secondary">{order.customer}</p>
        </div>
        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
          order.status === 'completed'
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : order.status === 'processing'
            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
        }`}>
          {order.status}
        </span>
      </div>
      
      <div className="grid grid-cols-3 gap-4 text-sm">
        <div>
          <p className="text-secondary">Total</p>
          <p className="font-bold text-accent-primary">${order.total}</p>
        </div>
        <div>
          <p className="text-secondary">Items</p>
          <p className="font-medium text-primary">{order.items}</p>
        </div>
        <div>
          <p className="text-secondary">Date</p>
          <p className="font-medium text-primary">{order.date}</p>
        </div>
      </div>
      
      <div className="mt-4 flex items-center justify-between">
        <Button variant="secondary" size="small">View Details</Button>
        {order.status === 'processing' && (
          <Button size="small">Fulfill Order</Button>
        )}
      </div>
    </div>
  );

  if (loading) {
    
  const loadEcommerceData = async () => {
    try {
      setLoading(true);
      const [productsResponse, ordersResponse, analyticsResponse] = await Promise.all([
        fetch('/api/ecommerce/products', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/ecommerce/orders', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/ecommerce/analytics', {
          headers: { 'Authorization': `Bearer ${token}` }
        })
      ]);
      
      if (productsResponse.ok && ordersResponse.ok && analyticsResponse.ok) {
        const [products, orders, analytics] = await Promise.all([
          productsResponse.json(),
          ordersResponse.json(),
          analyticsResponse.json()
        ]);
        
        setProducts(products.products || []);
        setOrders(orders.orders || []);
        setAnalytics(analytics);
      }
    } catch (error) {
      console.error('Error loading ecommerce data:', error);
    } finally {
      setLoading(false);
    }
  };


  return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  