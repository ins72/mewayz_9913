import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import {
  ChartBarIcon,
  UserGroupIcon,
  CalendarIcon,
  MagnifyingGlassIcon,
  PlusIcon,
  FilterIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  HeartIcon,
  ChatBubbleOvalLeftIcon,
  ShareIcon,
  AdjustmentsHorizontalIcon,
  CheckIcon,
  ClockIcon
} from '@heroicons/react/24/outline';

const SocialMediaPage = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('database');
  const [searchQuery, setSearchQuery] = useState('');
  const [filters, setFilters] = useState({
    followerRange: 'all',
    engagementRate: 'all',
    accountType: 'all',
    location: '',
    hashtags: ''
  });

  // Mock Instagram database data
  const [instagramAccounts, setInstagramAccounts] = useState([
    {
      id: '1',
      username: 'fitness_guru_miami',
      displayName: 'Miami Fitness Guru',
      followers: 45000,
      following: 2300,
      posts: 892,
      engagementRate: 4.2,
      accountType: 'business',
      verified: false,
      profilePicture: null,
      bio: '💪 Fitness coach in Miami | Personal training | Nutrition tips | DM for coaching',
      location: 'Miami, FL',
      email: 'contact@fitnessguru.com',
      website: 'www.fitnessguru.com',
      lastActive: '2 hours ago'
    },
    {
      id: '2',
      username: 'tech_startup_nyc',
      displayName: 'TechStartup NYC',
      followers: 15000,
      following: 1200,
      posts: 456,
      engagementRate: 6.8,
      accountType: 'business',
      verified: true,
      profilePicture: null,
      bio: '🚀 Building the future of tech | NYC-based startup | Join our journey',
      location: 'New York, NY',
      email: 'hello@techstartup.com',
      website: 'www.techstartup.com',
      lastActive: '1 day ago'
    },
    {
      id: '3',
      username: 'food_blogger_la',
      displayName: 'LA Food Explorer',
      followers: 85000,
      following: 3400,
      posts: 1240,
      engagementRate: 3.9,
      accountType: 'creator',
      verified: true,
      profilePicture: null,
      bio: '🍕 Exploring LA\'s best eats | Food reviews | Restaurant recommendations',
      location: 'Los Angeles, CA',
      email: 'collab@lafoodie.com',
      website: 'www.lafoodexplorer.com',
      lastActive: '30 minutes ago'
    }
  ]);

  const [scheduledPosts, setScheduledPosts] = useState([
    {
      id: '1',
      caption: 'New product launch coming soon! 🚀 Stay tuned for something amazing...',
      platforms: ['instagram', 'facebook', 'twitter'],
      media: ['image1.jpg'],
      scheduledDate: '2025-07-21T14:00:00Z',
      status: 'scheduled',
      hashtags: ['#launch', '#product', '#amazing']
    },
    {
      id: '2',
      caption: 'Behind the scenes of our latest project. The team is working hard! 💪',
      platforms: ['instagram', 'linkedin'],
      media: ['video1.mp4'],
      scheduledDate: '2025-07-22T10:30:00Z',
      status: 'scheduled',
      hashtags: ['#behindthescenes', '#team', '#work']
    }
  ]);

  const formatNumber = (num) => {
    if (num >= 1000000) 
  const loadSocialMediaData = async () => {
    try {
      setLoading(true);
      const [accountsResponse, postsResponse, analyticsResponse] = await Promise.all([
        fetch('/api/social-media/accounts', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/social-media/posts', {
          headers: { 'Authorization': `Bearer ${token}` }
        }),
        fetch('/api/social-media/analytics', {
          headers: { 'Authorization': `Bearer ${token}` }
        })
      ]);
      
      if (accountsResponse.ok && postsResponse.ok && analyticsResponse.ok) {
        const [accounts, posts, analytics] = await Promise.all([
          accountsResponse.json(),
          postsResponse.json(),
          analyticsResponse.json()
        ]);
        
        setAccounts(accounts.accounts || []);
        setPosts(posts.posts || []);
        setAnalytics(analytics);
      }
    } catch (error) {
      console.error('Error loading social media data:', error);
    } finally {
      setLoading(false);
    }
  };


  return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) 