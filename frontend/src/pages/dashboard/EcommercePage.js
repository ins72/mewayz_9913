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
      // Mock data for now - replace with actual API calls
      setProducts([
        {
          id: 1,
          name: 'Premium Course Bundle',
          price: 299.99,
          stock: 100,
          sales: 45,
          status: 'active',
          image: '/api/placeholder/150/150'
        },
        {
          id: 2,
          name: 'Consultation Service',
          price: 199.99,
          stock: 0,
          sales: 23,
          status: 'active',
          image: '/api/placeholder/150/150'
        },
        {
          id: 3,
          name: 'Digital Templates Pack',
          price: 49.99,
          stock: 500,
          sales: 156,
          status: 'active',
          image: '/api/placeholder/150/150'
        }
      ]);

      setOrders([
        {
          id: 'ORD-001',
          customer: 'John Doe',
          total: 299.99,
          status: 'completed',
          date: '2025-07-19',
          items: 1
        },
        {
          id: 'ORD-002',
          customer: 'Jane Smith',
          total: 49.99,
          status: 'processing',
          date: '2025-07-19',
          items: 1
        },
        {
          id: 'ORD-003',
          customer: 'Mike Johnson',
          total: 199.99,
          status: 'shipped',
          date: '2025-07-18',
          items: 1
        }
      ]);

      setAnalytics({
        totalRevenue: 15420,
        totalOrders: 234,
        totalProducts: 12,
        conversionRate: 3.2,
        averageOrderValue: 65.90,
        topSellingProduct: 'Digital Templates Pack'
      });
    } catch (error) {
      console.error('Failed to load e-commerce data:', error);
    } finally {
      setLoading(false);
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
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">E-commerce Store</h1>
          <p className="text-secondary mt-1">Manage your online store and products</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <ArchiveBoxIcon className="w-4 h-4 mr-2" />
            Manage Inventory
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Add Product
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'products', name: 'Products' },
            { id: 'orders', name: 'Orders' },
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
              title="Total Revenue"
              value={`$${analytics.totalRevenue.toLocaleString()}`}
              change={12.5}
              icon={CurrencyDollarIcon}
              color="primary"
            />
            <StatCard
              title="Total Orders"
              value={analytics.totalOrders.toLocaleString()}
              change={8.2}
              icon={ShoppingBagIcon}
              color="success"
            />
            <StatCard
              title="Total Products"
              value={analytics.totalProducts.toString()}
              change={5.1}
              icon={ArchiveBoxIcon}
              color="warning"
            />
            <StatCard
              title="Conversion Rate"
              value={`${analytics.conversionRate}%`}
              change={2.1}
              icon={ChartBarIcon}
              color="primary"
            />
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <TagIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Create Product</h3>
              <p className="text-secondary">Add new products to your store</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <TruckIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Process Orders</h3>
              <p className="text-secondary">Fulfill and ship customer orders</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <UserGroupIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Customer Support</h3>
              <p className="text-secondary">Help customers with their purchases</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'products' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Products</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Categories</option>
                <option>Digital Products</option>
                <option>Services</option>
                <option>Physical Goods</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>All Status</option>
                <option>Active</option>
                <option>Draft</option>
                <option>Out of Stock</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'orders' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Recent Orders</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Orders</option>
                <option>Pending</option>
                <option>Processing</option>
                <option>Shipped</option>
                <option>Completed</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            {orders.map((order) => (
              <OrderCard key={order.id} order={order} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Store Analytics</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive e-commerce analytics to help you track sales, customer behavior, and store performance.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default EcommercePage;