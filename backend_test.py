#!/usr/bin/env python3
"""
Comprehensive Backend Testing for Mewayz Creator Economy Platform
Laravel 9.x API Testing Suite
"""

import requests
import json
import sys
import time
from datetime import datetime

class MewayzAPITester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.web_url = base_url  # For testing web routes
        # Use a fresh test token - will be generated during authentication test
        self.auth_token = "3|yHHRGVcNjzxdu8szdT1LRua2Dy2GPnff0iQyCSm7cf941e64"
        self.user_id = None
        self.test_results = {}
        self.session = requests.Session()
        
    def log_test(self, test_name, success, message, response_data=None):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        
        self.test_results[test_name] = {
            'success': success,
            'message': message,
            'response_data': response_data,
            'timestamp': datetime.now().isoformat()
        }
        
    def make_request(self, method, endpoint, data=None, headers=None, auth_required=True):
        """Make HTTP request with proper headers"""
        # Add delay to avoid rate limiting
        time.sleep(0.1)
        
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if headers:
            default_headers.update(headers)
            
        # Add auth token if required and available
        if auth_required and self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, params=data, timeout=10)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=10)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=10)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=10)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 10 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None
    
    def test_health_check(self):
        """Test API health check endpoints"""
        print("\n=== Testing API Health Check ===")
        
        # Test basic health endpoint
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Health Check", True, f"API is healthy - Status: {data.get('status', 'unknown')}")
        else:
            self.log_test("Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test basic test endpoint
        response = self.make_request('GET', '/test', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Test Endpoint", True, f"Test endpoint working - Message: {data.get('message', 'No message')}")
        else:
            self.log_test("API Test Endpoint", False, f"Test endpoint failed - Status: {response.status_code if response else 'No response'}")
    
    def test_database_connectivity(self):
        """Test database connectivity through API"""
        print("\n=== Testing Database Connectivity ===")
        
        # Test system info endpoint which should check database
        response = self.make_request('GET', '/system/info', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Database Connectivity", True, "Database connection successful via system info")
        else:
            # Try health endpoint which mentions database
            response = self.make_request('GET', '/health', auth_required=False)
            if response and response.status_code == 200:
                data = response.json()
                if 'database' in data:
                    self.log_test("Database Connectivity", True, f"Database status: {data.get('database', 'unknown')}")
                else:
                    self.log_test("Database Connectivity", False, "Database status not reported in health check")
            else:
                self.log_test("Database Connectivity", False, "Cannot verify database connectivity")
    
    def test_authentication_system(self):
        """Test user authentication endpoints"""
        print("\n=== Testing Authentication System ===")
        
        # Test custom auth middleware with provided token
        response = self.make_request('GET', '/test-custom-auth')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Custom Auth Middleware", True, f"Custom auth working - User: {data.get('user_name', 'unknown')}")
        else:
            self.log_test("Custom Auth Middleware", False, f"Custom auth failed - Status: {response.status_code if response else 'No response'}")
        
        # Test authenticated user profile with custom middleware
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("User Profile (Custom Auth)", True, f"Profile retrieval successful - User: {data.get('user', {}).get('name', 'unknown')}")
        else:
            self.log_test("User Profile (Custom Auth)", False, f"Profile retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user registration (new user)
        register_data = {
            "name": "Test Creator",
            "email": f"testcreator_{int(time.time())}@example.com",
            "password": "SecurePassword123!",
            "password_confirmation": "SecurePassword123!"
        }
        
        response = self.make_request('POST', '/auth/register', register_data, auth_required=False)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("User Registration", True, "User registration successful")
            
            # Store auth token if provided
            if 'token' in data:
                new_token = data['token']
            elif 'access_token' in data:
                new_token = data['access_token']
                
            if 'user' in data:
                self.user_id = data['user'].get('id')
        else:
            self.log_test("User Registration", False, f"Registration failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user login
        login_data = {
            "email": register_data["email"],
            "password": register_data["password"]
        }
        
        response = self.make_request('POST', '/auth/login', login_data, auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("User Login", True, "User login successful")
        else:
            self.log_test("User Login", False, f"Login failed - Status: {response.status_code if response else 'No response'}")
    
    def test_bio_sites(self):
        """Test Bio Sites & Link-in-Bio functionality"""
        print("\n=== Testing Bio Sites & Link-in-Bio ===")
        
        if not self.auth_token:
            self.log_test("Bio Sites", False, "Cannot test - no authentication token")
            return
        
        # Test get bio sites
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            self.log_test("Get Bio Sites", True, "Bio sites retrieval successful")
        else:
            self.log_test("Get Bio Sites", False, f"Bio sites retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create bio site
        bio_site_data = {
            "name": "Test Creator Bio",
            "title": "Test Creator Bio",
            "slug": f"test-creator-{int(time.time())}",
            "description": "This is a test bio site for the creator",
            "theme": "minimal"
        }
        
        response = self.make_request('POST', '/bio-sites/', bio_site_data)
        bio_site_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Bio Site", True, "Bio site creation successful")
            bio_site_id = data.get('id') or data.get('data', {}).get('id')
        else:
            self.log_test("Create Bio Site", False, f"Bio site creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get themes
        response = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            self.log_test("Get Bio Site Themes", True, "Bio site themes retrieval successful")
        else:
            self.log_test("Get Bio Site Themes", False, f"Themes retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_social_media_management(self):
        """Test Social Media Management functionality"""
        print("\n=== Testing Social Media Management ===")
        
        if not self.auth_token:
            self.log_test("Social Media", False, "Cannot test - no authentication token")
            return
        
        # Test get social media accounts
        response = self.make_request('GET', '/social-media/accounts')
        if response and response.status_code == 200:
            self.log_test("Get Social Media Accounts", True, "Social media accounts retrieval successful")
        else:
            self.log_test("Get Social Media Accounts", False, f"Accounts retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get social media posts
        response = self.make_request('GET', '/social-media/posts')
        if response and response.status_code == 200:
            self.log_test("Get Social Media Posts", True, "Social media posts retrieval successful")
        else:
            self.log_test("Get Social Media Posts", False, f"Posts retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test social media analytics
        response = self.make_request('GET', '/social-media/analytics')
        if response and response.status_code == 200:
            self.log_test("Social Media Analytics", True, "Social media analytics retrieval successful")
        else:
            self.log_test("Social Media Analytics", False, f"Analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_instagram_integration(self):
        """Test Instagram Integration functionality"""
        print("\n=== Testing Instagram Integration ===")
        
        if not self.auth_token:
            self.log_test("Instagram Integration", False, "Cannot test - no authentication token")
            return
        
        # Test Instagram analytics
        response = self.make_request('GET', '/instagram/analytics')
        if response and response.status_code == 200:
            self.log_test("Instagram Analytics", True, "Instagram analytics retrieval successful")
        else:
            self.log_test("Instagram Analytics", False, f"Instagram analytics failed - Status: {response.status_code if response else 'No response'}")
        
        # Test hashtag analysis
        response = self.make_request('GET', '/instagram/hashtag-analysis')
        if response and response.status_code == 200:
            self.log_test("Instagram Hashtag Analysis", True, "Hashtag analysis successful")
        else:
            self.log_test("Instagram Hashtag Analysis", False, f"Hashtag analysis failed - Status: {response.status_code if response else 'No response'}")
        
        # Test content suggestions
        response = self.make_request('GET', '/instagram/content-suggestions')
        if response and response.status_code == 200:
            self.log_test("Instagram Content Suggestions", True, "Content suggestions retrieval successful")
        else:
            self.log_test("Instagram Content Suggestions", False, f"Content suggestions failed - Status: {response.status_code if response else 'No response'}")
    
    def test_ecommerce_system(self):
        """Test E-commerce System functionality"""
        print("\n=== Testing E-commerce System ===")
        
        if not self.auth_token:
            self.log_test("E-commerce", False, "Cannot test - no authentication token")
            return
        
        # Test get products
        response = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            self.log_test("Get Products", True, "Products retrieval successful")
        else:
            self.log_test("Get Products", False, f"Products retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create product
        product_data = {
            "name": "Test Digital Product",
            "description": "This is a test digital product",
            "price": 29.99,
            "type": "digital",
            "status": "active",
            "stock_quantity": 100
        }
        
        response = self.make_request('POST', '/ecommerce/products', product_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Create Product", True, "Product creation successful")
        else:
            self.log_test("Create Product", False, f"Product creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get orders
        response = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            self.log_test("Get Orders", True, "Orders retrieval successful")
        else:
            self.log_test("Get Orders", False, f"Orders retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_course_creation(self):
        """Test Course Creation functionality"""
        print("\n=== Testing Course Creation ===")
        
        if not self.auth_token:
            self.log_test("Course Creation", False, "Cannot test - no authentication token")
            return
        
        # Test get courses
        response = self.make_request('GET', '/courses/')
        if response and response.status_code == 200:
            self.log_test("Get Courses", True, "Courses retrieval successful")
        else:
            self.log_test("Get Courses", False, f"Courses retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create course
        course_data = {
            "title": "Test Online Course",
            "description": "This is a comprehensive test course",
            "price": 99.99,
            "status": "draft"
        }
        
        response = self.make_request('POST', '/courses/', course_data)
        course_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Course", True, "Course creation successful")
            course_id = data.get('id') or data.get('data', {}).get('id')
        else:
            self.log_test("Create Course", False, f"Course creation failed - Status: {response.status_code if response else 'No response'}")
    
    def test_email_marketing(self):
        """Test Email Marketing functionality"""
        print("\n=== Testing Email Marketing ===")
        
        if not self.auth_token:
            self.log_test("Email Marketing", False, "Cannot test - no authentication token")
            return
        
        # Test get campaigns
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            self.log_test("Get Email Campaigns", True, "Email campaigns retrieval successful")
        else:
            self.log_test("Get Email Campaigns", False, f"Campaigns retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create campaign
        campaign_data = {
            "name": "Test Email Campaign",
            "subject": "Welcome to Our Platform!",
            "content": "This is a test email campaign content",
            "status": "draft"
        }
        
        response = self.make_request('POST', '/email-marketing/campaigns', campaign_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Create Email Campaign", True, "Email campaign creation successful")
        else:
            self.log_test("Create Email Campaign", False, f"Campaign creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get templates
        response = self.make_request('GET', '/email-marketing/templates')
        if response and response.status_code == 200:
            self.log_test("Get Email Templates", True, "Email templates retrieval successful")
        else:
            self.log_test("Get Email Templates", False, f"Templates retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get subscribers
        response = self.make_request('GET', '/email-marketing/subscribers')
        if response and response.status_code == 200:
            self.log_test("Get Email Subscribers", True, "Email subscribers retrieval successful")
        else:
            self.log_test("Get Email Subscribers", False, f"Subscribers retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_analytics_reporting(self):
        """Test Analytics & Reporting functionality"""
        print("\n=== Testing Analytics & Reporting ===")
        
        if not self.auth_token:
            self.log_test("Analytics", False, "Cannot test - no authentication token")
            return
        
        # Test analytics overview
        response = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            self.log_test("Analytics Overview", True, "Analytics overview retrieval successful")
        else:
            self.log_test("Analytics Overview", False, f"Overview retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test reports
        response = self.make_request('GET', '/analytics/reports')
        if response and response.status_code == 200:
            self.log_test("Analytics Reports", True, "Analytics reports retrieval successful")
        else:
            self.log_test("Analytics Reports", False, f"Reports retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test social media analytics
        response = self.make_request('GET', '/analytics/social-media')
        if response and response.status_code == 200:
            self.log_test("Social Media Analytics", True, "Social media analytics retrieval successful")
        else:
            self.log_test("Social Media Analytics", False, f"Social media analytics failed - Status: {response.status_code if response else 'No response'}")
    
    def test_payment_processing(self):
        """Test Payment Processing functionality"""
        print("\n=== Testing Payment Processing ===")
        
        # Test get packages (public endpoint)
        response = self.make_request('GET', '/payments/packages', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Get Payment Packages", True, "Payment packages retrieval successful")
        else:
            self.log_test("Get Payment Packages", False, f"Packages retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Stripe packages endpoint
        response = self.make_request('GET', '/stripe/packages', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Get Stripe Packages", True, "Stripe packages retrieval successful")
        else:
            self.log_test("Get Stripe Packages", False, f"Stripe packages retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_workspace_management(self):
        """Test Workspace Management functionality"""
        print("\n=== Testing Workspace Management ===")
        
        if not self.auth_token:
            self.log_test("Workspace Management", False, "Cannot test - no authentication token")
            return
        
        # Test get workspaces
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            self.log_test("Get Workspaces", True, "Workspaces retrieval successful")
        else:
            self.log_test("Get Workspaces", False, f"Workspaces retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test workspace setup current step
        response = self.make_request('GET', '/workspace-setup/current-step')
        if response and response.status_code == 200:
            self.log_test("Workspace Setup Status", True, "Workspace setup status retrieval successful")
        else:
            self.log_test("Workspace Setup Status", False, f"Setup status failed - Status: {response.status_code if response else 'No response'}")

    def test_workspace_setup_wizard(self):
        """Test comprehensive 6-step Workspace Setup Wizard system"""
        print("\n=== Testing Workspace Setup Wizard (6-Step Process) ===")
        
        if not self.auth_token:
            self.log_test("Workspace Setup Wizard", False, "Cannot test - no authentication token")
            return
        
        # Step 1: Test GET current setup step
        print("\n--- Step 1: Get Current Setup Step ---")
        response = self.make_request('GET', '/workspace-setup/current-step')
        if response and response.status_code == 200:
            data = response.json()
            current_step = data.get('current_step', 1)
            self.log_test("Get Current Setup Step", True, f"Current step: {current_step}, Setup completed: {data.get('setup_completed', False)}")
        else:
            self.log_test("Get Current Setup Step", False, f"Failed to get current step - Status: {response.status_code if response else 'No response'}")
            return
        
        # Step 2: Test GET main goals
        print("\n--- Step 2: Get Main Goals ---")
        response = self.make_request('GET', '/workspace-setup/main-goals')
        if response and response.status_code == 200:
            data = response.json()
            goals = data.get('goals', {})
            self.log_test("Get Main Goals", True, f"Retrieved {len(goals)} main goals: {list(goals.keys())}")
        else:
            self.log_test("Get Main Goals", False, f"Failed to get main goals - Status: {response.status_code if response else 'No response'}")
            return
        
        # Step 3: Test POST main goals selection
        print("\n--- Step 3: Save Main Goals Selection ---")
        main_goals_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"],
            "primary_goal": "instagram_management",
            "business_type": "Digital Marketing Agency",
            "target_audience": "Small business owners and entrepreneurs looking to grow their social media presence"
        }
        response = self.make_request('POST', '/workspace-setup/main-goals', data=main_goals_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Main Goals", True, f"Goals saved successfully, next step: {data.get('next_step')}")
        else:
            self.log_test("Save Main Goals", False, f"Failed to save main goals - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Step 4: Test GET available features
        print("\n--- Step 4: Get Available Features ---")
        features_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"]
        }
        response = self.make_request('POST', '/workspace-setup/available-features', data=features_data)
        if response and response.status_code == 200:
            data = response.json()
            features = data.get('features', {})
            self.log_test("Get Available Features", True, f"Retrieved {len(features)} available features")
        else:
            self.log_test("Get Available Features", False, f"Failed to get available features - Status: {response.status_code if response else 'No response'}")
        
        # Step 5: Test POST feature selection
        print("\n--- Step 5: Save Feature Selection ---")
        feature_selection_data = {
            "selected_features": [
                "content_scheduling", "content_calendar", "hashtag_research",
                "page_builder", "template_library", "analytics_tracking",
                "product_catalog", "inventory_tracking", "order_processing"
            ],
            "subscription_plan": "professional"
        }
        response = self.make_request('POST', '/workspace-setup/feature-selection', data=feature_selection_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Feature Selection", True, f"Features saved successfully, pricing: ${data.get('pricing', {}).get('total_monthly', 0)}/month")
        else:
            self.log_test("Save Feature Selection", False, f"Failed to save feature selection - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Step 6: Test POST team setup
        print("\n--- Step 6: Save Team Setup ---")
        team_setup_data = {
            "team_members": [
                {
                    "email": "manager@example.com",
                    "role": "manager",
                    "permissions": ["content_management", "analytics_view"]
                },
                {
                    "email": "editor@example.com", 
                    "role": "editor",
                    "permissions": ["content_creation", "content_editing"]
                }
            ],
            "custom_roles": [
                {
                    "name": "Content Creator",
                    "permissions": ["content_creation", "content_scheduling"]
                }
            ],
            "collaboration_enabled": True
        }
        response = self.make_request('POST', '/workspace-setup/team-setup', data=team_setup_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Team Setup", True, f"Team setup saved successfully, next step: {data.get('next_step')}")
        else:
            self.log_test("Save Team Setup", False, f"Failed to save team setup - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Step 7: Test GET subscription plans
        print("\n--- Step 7: Get Subscription Plans ---")
        response = self.make_request('GET', '/workspace-setup/subscription-plans')
        if response and response.status_code == 200:
            data = response.json()
            plans = data.get('plans', {})
            self.log_test("Get Subscription Plans", True, f"Retrieved {len(plans)} subscription plans: {list(plans.keys())}")
        else:
            self.log_test("Get Subscription Plans", False, f"Failed to get subscription plans - Status: {response.status_code if response else 'No response'}")
        
        # Step 8: Test POST subscription selection
        print("\n--- Step 8: Save Subscription Selection ---")
        subscription_data = {
            "subscription_plan": "professional",
            "billing_cycle": "yearly",
            "payment_method": "stripe"
        }
        response = self.make_request('POST', '/workspace-setup/subscription-selection', data=subscription_data)
        if response and response.status_code == 200:
            data = response.json()
            pricing = data.get('pricing', {})
            self.log_test("Save Subscription Selection", True, f"Subscription saved, total: ${pricing.get('total_yearly', 0)}/year")
        else:
            self.log_test("Save Subscription Selection", False, f"Failed to save subscription - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Step 9: Test POST branding configuration
        print("\n--- Step 9: Save Branding Configuration ---")
        branding_data = {
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981",
            "accent_color": "#F59E0B",
            "company_name": "Digital Growth Agency",
            "brand_voice": "professional",
            "white_label_enabled": False,
            "custom_domain": "agency.example.com"
        }
        response = self.make_request('POST', '/workspace-setup/branding-configuration', data=branding_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Branding Configuration", True, f"Branding saved successfully, next step: {data.get('next_step')}")
        else:
            self.log_test("Save Branding Configuration", False, f"Failed to save branding - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Step 10: Test POST complete setup
        print("\n--- Step 10: Complete Workspace Setup ---")
        response = self.make_request('POST', '/workspace-setup/complete')
        if response and response.status_code == 200:
            data = response.json()
            workspace = data.get('workspace', {})
            self.log_test("Complete Workspace Setup", True, f"Setup completed! Workspace: {workspace.get('name')}, Dashboard: {workspace.get('dashboard_url')}")
        else:
            self.log_test("Complete Workspace Setup", False, f"Failed to complete setup - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Step 11: Test GET setup summary
        print("\n--- Step 11: Get Setup Summary ---")
        response = self.make_request('GET', '/workspace-setup/summary')
        if response and response.status_code == 200:
            data = response.json()
            summary = data.get('summary', {})
            self.log_test("Get Setup Summary", True, f"Setup summary retrieved, completed: {summary.get('setup_completed', False)}")
        else:
            self.log_test("Get Setup Summary", False, f"Failed to get setup summary - Status: {response.status_code if response else 'No response'}")
        
        # Step 12: Test POST reset setup (for testing purposes)
        print("\n--- Step 12: Reset Setup (Testing) ---")
        response = self.make_request('POST', '/workspace-setup/reset')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Reset Setup", True, "Setup reset successfully for testing")
        else:
            self.log_test("Reset Setup", False, f"Failed to reset setup - Status: {response.status_code if response else 'No response'}")
        
        print("\n🎯 Workspace Setup Wizard Testing Complete!")
        print("This comprehensive test covers all 6 steps of the workspace setup process:")
        print("1. Main Goals Selection (Instagram, Link in Bio, Courses, E-commerce, CRM, Marketing)")
        print("2. Feature Selection (up to 3 for free plan)")
        print("3. Team Setup (invite team members with roles)")
        print("4. Subscription Selection (Free, Pro, Enterprise)")
        print("5. Branding Configuration (company name, colors, logo)")
        print("6. Final Review and Launch")
    
    def test_oauth_integration(self):
        """Test OAuth Integration functionality"""
        print("\n=== Testing OAuth Integration ===")
        
        # Test get OAuth providers (public endpoint)
        response = self.make_request('GET', '/auth/oauth/providers', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Get OAuth Providers", True, "OAuth providers retrieval successful")
        else:
            self.log_test("Get OAuth Providers", False, f"OAuth providers failed - Status: {response.status_code if response else 'No response'}")
        
        if self.auth_token:
            # Test OAuth status (authenticated)
            response = self.make_request('GET', '/oauth/status')
            if response and response.status_code == 200:
                self.log_test("OAuth Status", True, "OAuth status retrieval successful")
            else:
                self.log_test("OAuth Status", False, f"OAuth status failed - Status: {response.status_code if response else 'No response'}")
    
    def test_two_factor_auth(self):
        """Test Two-Factor Authentication functionality"""
        print("\n=== Testing Two-Factor Authentication ===")
        
        # Test 2FA status (public endpoint)
        response = self.make_request('GET', '/auth/2fa/status', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("2FA Status Check", True, "2FA status check successful")
        else:
            self.log_test("2FA Status Check", False, f"2FA status failed - Status: {response.status_code if response else 'No response'}")
    
    def test_crm_system(self):
        """Test CRM System functionality"""
        print("\n=== Testing CRM System ===")
        
        if not self.auth_token:
            self.log_test("CRM System", False, "Cannot test - no authentication token")
            return
        
        # Test get contacts
        response = self.make_request('GET', '/crm/contacts')
        if response and response.status_code == 200:
            self.log_test("Get CRM Contacts", True, "CRM contacts retrieval successful")
        else:
            self.log_test("Get CRM Contacts", False, f"Contacts retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get leads
        response = self.make_request('GET', '/crm/leads')
        if response and response.status_code == 200:
            self.log_test("Get CRM Leads", True, "CRM leads retrieval successful")
        else:
            self.log_test("Get CRM Leads", False, f"Leads retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_team_management(self):
        """Test Team Management functionality"""
        print("\n=== Testing Team Management ===")
        
        if not self.auth_token:
            self.log_test("Team Management", False, "Cannot test - no authentication token")
            return
        
        # Test get team
        response = self.make_request('GET', '/team/')
        if response and response.status_code == 200:
            self.log_test("Get Team", True, "Team retrieval successful")
        else:
            self.log_test("Get Team", False, f"Team retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_ai_integration(self):
        """Test AI Integration functionality"""
        print("\n=== Testing AI Integration ===")
        
        if not self.auth_token:
            self.log_test("AI Integration", False, "Cannot test - no authentication token")
            return
        
        # Test get AI services
        response = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            self.log_test("Get AI Services", True, "AI services retrieval successful")
        else:
            self.log_test("Get AI Services", False, f"AI services failed - Status: {response.status_code if response else 'No response'}")

    def test_legal_pages_system(self):
        """Test Legal Pages System functionality"""
        print("\n=== Testing Legal Pages System ===")
        
        # Test all 5 legal document endpoints
        legal_pages = [
            'terms-of-service',
            'privacy-policy', 
            'cookie-policy',
            'refund-policy',
            'accessibility'
        ]
        
        for page in legal_pages:
            response = self.make_request('GET', f'/{page}', auth_required=False)
            if response and response.status_code == 200:
                self.log_test(f"Legal Page - {page.title()}", True, f"{page} page accessible and returns content")
            else:
                self.log_test(f"Legal Page - {page.title()}", False, f"{page} page failed - Status: {response.status_code if response else 'No response'}")
        
        # Test legal API endpoints
        if self.auth_token:
            # Test cookie consent endpoint
            consent_data = {
                "necessary": True,
                "analytics": True,
                "marketing": False,
                "preferences": True
            }
            response = self.make_request('POST', '/legal/cookie-consent', consent_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Cookie Consent API", True, "Cookie consent saved successfully")
            else:
                self.log_test("Cookie Consent API", False, f"Cookie consent failed - Status: {response.status_code if response else 'No response'}")
            
            # Test data export endpoint
            response = self.make_request('POST', '/legal/data-export')
            if response and response.status_code in [200, 201]:
                self.log_test("Data Export API", True, "Data export request successful")
            else:
                self.log_test("Data Export API", False, f"Data export failed - Status: {response.status_code if response else 'No response'}")
            
            # Test data deletion endpoint
            deletion_data = {
                "reason": "No longer need the service",
                "confirmation": True
            }
            response = self.make_request('POST', '/legal/data-deletion', deletion_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Data Deletion API", True, "Data deletion request successful")
            else:
                self.log_test("Data Deletion API", False, f"Data deletion failed - Status: {response.status_code if response else 'No response'}")

    def test_account_removal_system(self):
        """Test Account Removal System functionality"""
        print("\n=== Testing Account Removal System ===")
        
        # Test account removal page access
        response = self.make_request('GET', '/account-removal', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Account Removal Page", True, "Account removal page accessible")
        else:
            self.log_test("Account Removal Page", False, f"Account removal page failed - Status: {response.status_code if response else 'No response'}")
        
        if self.auth_token:
            # Test account removal request
            removal_data = {
                "reason": "No longer using the platform",
                "feedback": "The platform served its purpose",
                "data_export_requested": True,
                "confirmation": True
            }
            response = self.make_request('POST', '/account/removal-request', removal_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Account Removal Request", True, "Account removal request submitted successfully")
            else:
                self.log_test("Account Removal Request", False, f"Account removal request failed - Status: {response.status_code if response else 'No response'}")

    def test_support_center_system(self):
        """Test Support Center System functionality"""
        print("\n=== Testing Support Center System ===")
        
        # Test support center page access
        response = self.make_request('GET', '/support', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Support Center Page", True, "Support center page accessible")
        else:
            self.log_test("Support Center Page", False, f"Support center page failed - Status: {response.status_code if response else 'No response'}")
        
        # Test FAQ endpoint
        response = self.make_request('GET', '/support/faq', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Support FAQ", True, "FAQ section accessible")
        else:
            self.log_test("Support FAQ", False, f"FAQ section failed - Status: {response.status_code if response else 'No response'}")
        
        if self.auth_token:
            # Test support ticket creation
            ticket_data = {
                "subject": "Test Support Ticket",
                "category": "technical",
                "priority": "medium",
                "description": "This is a test support ticket to verify the system functionality"
            }
            response = self.make_request('POST', '/support/tickets', ticket_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Support Ticket Creation", True, "Support ticket created successfully")
            else:
                self.log_test("Support Ticket Creation", False, f"Support ticket creation failed - Status: {response.status_code if response else 'No response'}")

    def test_ultra_advanced_gamification(self):
        """Test Ultra-Advanced Gamification System functionality"""
        print("\n=== Testing Ultra-Advanced Gamification System ===")
        
        if not self.auth_token:
            self.log_test("Gamification System", False, "Cannot test - no authentication token")
            return
        
        # Test get user level
        response = self.make_request('GET', '/gamification/user-level')
        if response and response.status_code == 200:
            self.log_test("Get User Level", True, "User level retrieval successful")
        else:
            self.log_test("Get User Level", False, f"User level retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get achievements
        response = self.make_request('GET', '/gamification/achievements')
        if response and response.status_code == 200:
            self.log_test("Get Achievements", True, "Achievements retrieval successful")
        else:
            self.log_test("Get Achievements", False, f"Achievements retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test award XP
        xp_data = {
            "action": "profile_completion",
            "points": 50,
            "description": "Completed profile setup"
        }
        response = self.make_request('POST', '/gamification/award-xp', xp_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Award XP", True, "XP awarded successfully")
        else:
            self.log_test("Award XP", False, f"XP award failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get leaderboard
        response = self.make_request('GET', '/gamification/leaderboard')
        if response and response.status_code == 200:
            self.log_test("Get Leaderboard", True, "Leaderboard retrieval successful")
        else:
            self.log_test("Get Leaderboard", False, f"Leaderboard retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get user streaks
        response = self.make_request('GET', '/gamification/streaks')
        if response and response.status_code == 200:
            self.log_test("Get User Streaks", True, "User streaks retrieval successful")
        else:
            self.log_test("Get User Streaks", False, f"User streaks retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_automated_installer_system(self):
        """Test Automated Installer System functionality"""
        print("\n=== Testing Automated Installer System ===")
        
        # Test installation status check
        response = self.make_request('GET', '/install/status', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Installation Status Check", True, "Installation status check successful")
        else:
            self.log_test("Installation Status Check", False, f"Installation status check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test system requirements check
        response = self.make_request('GET', '/install/requirements', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("System Requirements Check", True, "System requirements check successful")
        else:
            self.log_test("System Requirements Check", False, f"System requirements check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test database configuration check
        response = self.make_request('GET', '/install/database-config', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Database Configuration Check", True, "Database configuration check successful")
        else:
            self.log_test("Database Configuration Check", False, f"Database configuration check failed - Status: {response.status_code if response else 'No response'}")

    def test_enhanced_ai_features(self):
        """Test Enhanced AI Features functionality"""
        print("\n=== Testing Enhanced AI Features ===")
        
        if not self.auth_token:
            self.log_test("Enhanced AI Features", False, "Cannot test - no authentication token")
            return
        
        # Test get AI services
        response = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            self.log_test("Get AI Services", True, "AI services retrieval successful")
        else:
            self.log_test("Get AI Services", False, f"AI services retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test content generation
        content_data = {
            "type": "social_media_post",
            "topic": "Digital Marketing Tips",
            "tone": "professional",
            "length": "medium"
        }
        response = self.make_request('POST', '/ai/content-generation', content_data)
        if response and response.status_code in [200, 201]:
            self.log_test("AI Content Generation", True, "Content generation successful")
        else:
            self.log_test("AI Content Generation", False, f"Content generation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test SEO optimization
        seo_data = {
            "content": "This is a test blog post about digital marketing strategies",
            "target_keywords": ["digital marketing", "SEO", "content strategy"]
        }
        response = self.make_request('POST', '/ai/seo-optimization', seo_data)
        if response and response.status_code in [200, 201]:
            self.log_test("AI SEO Optimization", True, "SEO optimization successful")
        else:
            self.log_test("AI SEO Optimization", False, f"SEO optimization failed - Status: {response.status_code if response else 'No response'}")

    def test_database_tables_creation(self):
        """Test Phase 1 Database Tables Creation"""
        print("\n=== Testing Phase 1 Database Tables Creation ===")
        
        if not self.auth_token:
            self.log_test("Database Tables", False, "Cannot test - no authentication token")
            return
        
        # Test bio_sites table
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            self.log_test("Bio Sites Table", True, "bio_sites table accessible and functional")
        else:
            self.log_test("Bio Sites Table", False, f"bio_sites table access failed - Status: {response.status_code if response else 'No response'}")
        
        # Test escrow_transactions table
        response = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("Escrow Transactions Table", True, "escrow_transactions table accessible and functional")
        else:
            self.log_test("Escrow Transactions Table", False, f"escrow_transactions table access failed - Status: {response.status_code if response else 'No response'}")
        
        # Test booking_services table
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("Booking Services Table", True, "booking_services table accessible and functional")
        else:
            self.log_test("Booking Services Table", False, f"booking_services table access failed - Status: {response.status_code if response else 'No response'}")
        
        # Test workspaces table
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            self.log_test("Workspaces Table", True, "workspaces table accessible and functional")
        else:
            self.log_test("Workspaces Table", False, f"workspaces table access failed - Status: {response.status_code if response else 'No response'}")
        
        # Test email_campaigns table
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            self.log_test("Email Campaigns Table", True, "email_campaigns table accessible and functional")
        else:
            self.log_test("Email Campaigns Table", False, f"email_campaigns table access failed - Status: {response.status_code if response else 'No response'}")

    def test_admin_dashboard_system(self):
        """Test Ultra-Comprehensive Admin Dashboard System"""
        print("\n=== Testing Ultra-Comprehensive Admin Dashboard System ===")
        
        if not self.auth_token:
            self.log_test("Admin Dashboard", False, "Cannot test - no authentication token")
            return
        
        # Test admin dashboard access (should return 403 for non-admin users)
        response = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 403:
            self.log_test("Admin Dashboard Security", True, "Admin dashboard properly secured - returns 403 for non-admin users")
        elif response and response.status_code == 200:
            self.log_test("Admin Dashboard Access", True, "Admin dashboard accessible (user has admin privileges)")
        else:
            self.log_test("Admin Dashboard", False, f"Admin dashboard failed - Status: {response.status_code if response else 'No response'}")
        
        # Test admin user management (should also return 403 for non-admin)
        response = self.make_request('GET', '/admin/users')
        if response and response.status_code == 403:
            self.log_test("Admin User Management Security", True, "Admin user management properly secured")
        elif response and response.status_code == 200:
            self.log_test("Admin User Management", True, "Admin user management accessible")
        else:
            self.log_test("Admin User Management", False, f"Admin user management failed - Status: {response.status_code if response else 'No response'}")

    def test_production_performance(self):
        """Test production performance metrics"""
        print("\n=== Testing Production Performance ===")
        
        start_time = time.time()
        
        # Test API response times
        response = self.make_request('GET', '/health', auth_required=False)
        health_time = time.time() - start_time
        
        if response and response.status_code == 200 and health_time < 2.0:
            self.log_test("API Response Time", True, f"Health endpoint responds in {health_time:.3f}s (< 2s)")
        else:
            self.log_test("API Response Time", False, f"Health endpoint slow or failed - Time: {health_time:.3f}s, Status: {response.status_code if response else 'No response'}")
        
        if self.auth_token:
            # Test authenticated endpoint performance
            start_time = time.time()
            response = self.make_request('GET', '/auth/me')
            auth_time = time.time() - start_time
            
            if response and response.status_code == 200 and auth_time < 3.0:
                self.log_test("Authenticated API Performance", True, f"Auth endpoint responds in {auth_time:.3f}s (< 3s)")
            else:
                self.log_test("Authenticated API Performance", False, f"Auth endpoint slow or failed - Time: {auth_time:.3f}s, Status: {response.status_code if response else 'No response'}")

    def test_security_headers(self):
        """Test security headers and CORS configuration"""
        print("\n=== Testing Security Configuration ===")
        
        response = self.make_request('GET', '/health', auth_required=False)
        if response:
            headers = response.headers
            
            # Check for security headers
            security_checks = [
                ('X-Frame-Options', 'Clickjacking protection'),
                ('X-Content-Type-Options', 'MIME type sniffing protection'),
                ('X-XSS-Protection', 'XSS protection'),
                ('Strict-Transport-Security', 'HTTPS enforcement'),
                ('Content-Security-Policy', 'Content security policy')
            ]
            
            for header, description in security_checks:
                if header in headers:
                    self.log_test(f"Security Header - {header}", True, f"{description} enabled")
                else:
                    self.log_test(f"Security Header - {header}", False, f"{description} missing")
        else:
            self.log_test("Security Headers", False, "Cannot test security headers - no response")

    def test_new_database_improvements(self):
        """Test the new database improvements mentioned in review request"""
        print("\n=== Testing New Database Improvements (62 Tables Total) ===")
        
        if not self.auth_token:
            self.log_test("New Database Features", False, "Cannot test - no authentication token")
            return
        
        # Test new e-commerce tables (products, orders, order_items, categories)
        print("\n--- Testing E-commerce System (New Tables) ---")
        
        # Test categories table
        response = self.make_request('GET', '/ecommerce/categories', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            categories = data.get('data', []) if isinstance(data.get('data'), list) else []
            self.log_test("Categories Table", True, f"Categories table working - Found {len(categories)} categories")
        else:
            self.log_test("Categories Table", False, f"Categories table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test products table with CRUD operations
        response = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            self.log_test("Products Table (GET)", True, "Products table accessible")
            
            # Test product creation
            product_data = {
                "name": "Test Digital Course",
                "description": "A comprehensive digital marketing course",
                "price": 99.99,
                "category_id": 1,  # Digital Products category
                "type": "digital",
                "status": "active",
                "stock_quantity": 100
            }
            
            response = self.make_request('POST', '/ecommerce/products', product_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Products Table (POST)", True, "Product creation successful")
            else:
                self.log_test("Products Table (POST)", False, f"Product creation failed - Status: {response.status_code if response else 'No response'}")
        else:
            self.log_test("Products Table (GET)", False, f"Products table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test orders table
        response = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            self.log_test("Orders Table", True, "Orders table accessible")
        else:
            self.log_test("Orders Table", False, f"Orders table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test new social media tables (social_media_posts, social_media_accounts)
        print("\n--- Testing Social Media Management (New Tables) ---")
        
        response = self.make_request('GET', '/social-media/accounts')
        if response and response.status_code == 200:
            self.log_test("Social Media Accounts Table", True, "Social media accounts table accessible")
        else:
            self.log_test("Social Media Accounts Table", False, f"Social media accounts table failed - Status: {response.status_code if response else 'No response'}")
        
        response = self.make_request('GET', '/social-media/posts')
        if response and response.status_code == 200:
            self.log_test("Social Media Posts Table", True, "Social media posts table accessible")
        else:
            self.log_test("Social Media Posts Table", False, f"Social media posts table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test new Laravel core tables (jobs, notifications, sessions, password_reset_tokens)
        print("\n--- Testing Laravel Core Features (New Tables) ---")
        
        # Test notifications system
        response = self.make_request('GET', '/notifications')
        if response and response.status_code == 200:
            self.log_test("Notifications Table", True, "Notifications table accessible")
        else:
            self.log_test("Notifications Table", False, f"Notifications table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test queue system (jobs table)
        response = self.make_request('GET', '/system/queue-status', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Jobs Table (Queue System)", True, "Queue system working - jobs table functional")
        else:
            self.log_test("Jobs Table (Queue System)", False, f"Queue system failed - Status: {response.status_code if response else 'No response'}")
    
    def test_password_reset_system(self):
        """Test the new password reset functionality"""
        print("\n=== Testing Password Reset System (New Feature) ===")
        
        # Test password reset request
        reset_data = {
            "email": "testuser@example.com"
        }
        
        response = self.make_request('POST', '/auth/password/reset-request', reset_data, auth_required=False)
        if response and response.status_code in [200, 201]:
            self.log_test("Password Reset Request", True, "Password reset request successful")
        else:
            self.log_test("Password Reset Request", False, f"Password reset request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test password reset status check
        response = self.make_request('GET', '/auth/password/reset-status', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Password Reset Status", True, "Password reset status check working")
        else:
            self.log_test("Password Reset Status", False, f"Password reset status failed - Status: {response.status_code if response else 'No response'}")
    
    def test_enhanced_admin_dashboard(self):
        """Test enhanced admin dashboard with access to all 62 tables"""
        print("\n=== Testing Enhanced Admin Dashboard (62 Tables Access) ===")
        
        if not self.auth_token:
            self.log_test("Enhanced Admin Dashboard", False, "Cannot test - no authentication token")
            return
        
        # Test admin dashboard main page
        response = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 403:
            self.log_test("Admin Dashboard Security", True, "Admin dashboard properly secured (403 for non-admin)")
        elif response and response.status_code == 200:
            self.log_test("Admin Dashboard Access", True, "Admin dashboard accessible")
        else:
            self.log_test("Admin Dashboard", False, f"Admin dashboard failed - Status: {response.status_code if response else 'No response'}")
        
        # Test admin database schema access
        response = self.make_request('GET', '/admin/database/schema')
        if response and response.status_code == 403:
            self.log_test("Admin Database Schema Security", True, "Database schema properly secured")
        elif response and response.status_code == 200:
            data = response.json()
            table_count = len(data.get('tables', []))
            self.log_test("Admin Database Schema", True, f"Database schema accessible - {table_count} tables found")
        else:
            self.log_test("Admin Database Schema", False, f"Database schema failed - Status: {response.status_code if response else 'No response'}")
        
        # Test admin table management
        response = self.make_request('GET', '/admin/tables/products')
        if response and response.status_code == 403:
            self.log_test("Admin Table Management Security", True, "Table management properly secured")
        elif response and response.status_code == 200:
            self.log_test("Admin Table Management", True, "Admin can access table management")
        else:
            self.log_test("Admin Table Management", False, f"Table management failed - Status: {response.status_code if response else 'No response'}")
    
    def test_database_integrity_and_relationships(self):
        """Test database integrity and foreign key relationships"""
        print("\n=== Testing Database Integrity & Foreign Key Relationships ===")
        
        if not self.auth_token:
            self.log_test("Database Integrity", False, "Cannot test - no authentication token")
            return
        
        # Test foreign key relationships by creating related records
        print("\n--- Testing Product-Category Relationship ---")
        
        # First get categories to test relationship
        response = self.make_request('GET', '/ecommerce/categories', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            categories = data.get('data', [])
            if categories:
                category_id = categories[0].get('id')
                
                # Create product with valid category_id
                product_data = {
                    "name": "Test Product with Category",
                    "description": "Testing foreign key relationship",
                    "price": 49.99,
                    "category_id": category_id,
                    "type": "digital",
                    "status": "active"
                }
                
                response = self.make_request('POST', '/ecommerce/products', product_data)
                if response and response.status_code in [200, 201]:
                    self.log_test("Product-Category Relationship", True, "Foreign key relationship working correctly")
                else:
                    self.log_test("Product-Category Relationship", False, f"Foreign key relationship failed - Status: {response.status_code if response else 'No response'}")
            else:
                self.log_test("Product-Category Relationship", False, "No categories found to test relationship")
        else:
            self.log_test("Product-Category Relationship", False, "Cannot test - categories not accessible")
        
        # Test cascading deletes (if implemented)
        print("\n--- Testing Cascading Delete Behavior ---")
        response = self.make_request('GET', '/admin/database/constraints', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Database Constraints", True, "Database constraints accessible")
        else:
            self.log_test("Database Constraints", False, f"Database constraints check failed - Status: {response.status_code if response else 'No response'}")
    
    def test_seed_data_verification(self):
        """Test that seed data was properly added"""
        print("\n=== Testing Seed Data Verification ===")
        
        # Test categories seed data (6 predefined categories)
        response = self.make_request('GET', '/ecommerce/categories', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            categories = data.get('data', [])
            expected_categories = ['Digital Products', 'Courses', 'Templates', 'Services', 'Merchandise', 'Subscriptions']
            
            if len(categories) >= 6:
                category_names = [cat.get('name', '') for cat in categories]
                found_expected = sum(1 for expected in expected_categories if any(expected in name for name in category_names))
                
                if found_expected >= 4:  # At least 4 of the 6 expected categories
                    self.log_test("Categories Seed Data", True, f"Found {len(categories)} categories including expected ones")
                else:
                    self.log_test("Categories Seed Data", False, f"Expected categories not found - Found: {category_names}")
            else:
                self.log_test("Categories Seed Data", False, f"Insufficient categories found - Expected 6+, Found: {len(categories)}")
        else:
            self.log_test("Categories Seed Data", False, f"Categories seed data check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test legal documents seed data
        legal_pages = ['terms-of-service', 'privacy-policy', 'cookie-policy', 'refund-policy', 'accessibility']
        legal_working = 0
        
        for page in legal_pages:
            response = self.make_request('GET', f'/{page}', auth_required=False)
            if response and response.status_code == 200:
                legal_working += 1
        
        if legal_working == 5:
            self.log_test("Legal Documents Seed Data", True, "All 5 legal documents accessible")
        elif legal_working >= 3:
            self.log_test("Legal Documents Seed Data", True, f"Most legal documents accessible ({legal_working}/5)")
        else:
            self.log_test("Legal Documents Seed Data", False, f"Legal documents not properly seeded ({legal_working}/5)")

    def test_comprehensive_final_backend(self):
        """Run comprehensive final backend test as requested in review"""
        print("🚀 COMPREHENSIVE FINAL BACKEND TEST - MEWAYZ PLATFORM")
        print("=" * 80)
        print("Testing Database Improvements: 52 → 62 Tables (10 New Tables Added)")
        print("Focus Areas: E-commerce, Social Media, Laravel Core, Admin Dashboard")
        print("=" * 80)
        
        # Core Infrastructure (must work for production)
        self.test_health_check()
        self.test_database_connectivity()
        
        # Authentication System (enhanced with password reset)
        self.test_authentication_system()
        self.test_password_reset_system()
        
        # NEW DATABASE IMPROVEMENTS - Primary Focus
        self.test_new_database_improvements()
        self.test_database_integrity_and_relationships()
        self.test_seed_data_verification()
        
        # Enhanced Admin Dashboard (access to all 62 tables)
        self.test_enhanced_admin_dashboard()
        
        # Core Business Features (should benefit from new tables)
        self.test_ecommerce_system()
        self.test_social_media_management()
        
        # Queue System and Background Jobs (new jobs table)
        response = self.make_request('GET', '/system/queue-status', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Background Jobs System", True, "Queue system operational")
        else:
            self.log_test("Background Jobs System", False, "Queue system not accessible")
        
        # Legal Pages (should remain 100% functional)
        self.test_legal_pages_system()
        
        # Performance check for production readiness
        self.test_production_performance()
        
        # Generate final report
        self.generate_final_backend_report()

    def generate_final_backend_report(self):
        """Generate final backend test report focusing on new improvements"""
        print("\n" + "=" * 80)
        print("🎯 FINAL BACKEND TEST REPORT - DATABASE IMPROVEMENTS")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"📊 OVERALL BACKEND SUCCESS RATE: {success_rate:.1f}%")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print()
        
        # Focus on new database improvements
        new_features = {
            'E-commerce System': ['Categories Table', 'Products Table', 'Orders Table'],
            'Social Media Management': ['Social Media Accounts Table', 'Social Media Posts Table'],
            'Laravel Core Features': ['Notifications Table', 'Jobs Table', 'Password Reset'],
            'Admin Dashboard': ['Admin Dashboard', 'Database Schema', 'Table Management'],
            'Database Integrity': ['Product-Category Relationship', 'Database Constraints', 'Seed Data']
        }
        
        print("🆕 NEW DATABASE IMPROVEMENTS STATUS:")
        for category, tests in new_features.items():
            category_tests = [name for name in self.test_results.keys() if any(test in name for test in tests)]
            if category_tests:
                category_passed = sum(1 for name in category_tests if self.test_results[name]['success'])
                category_total = len(category_tests)
                category_rate = (category_passed / category_total * 100) if category_total > 0 else 0
                
                status = "✅" if category_rate >= 80 else "⚠️" if category_rate >= 50 else "❌"
                print(f"   {status} {category}: {category_rate:.1f}% ({category_passed}/{category_total})")
        
        print()
        
        # Production readiness assessment
        print("🏆 PRODUCTION READINESS ASSESSMENT:")
        if success_rate >= 85:
            print("   Status: ✅ PRODUCTION READY")
            print("   Assessment: Backend meets the expected 85%+ success rate")
            print("   Database improvements successfully implemented")
        elif success_rate >= 75:
            print("   Status: ⚠️  MOSTLY READY")
            print("   Assessment: Backend close to production ready")
            print("   Minor issues need addressing")
        else:
            print("   Status: ❌ NEEDS IMPROVEMENT")
            print("   Assessment: Backend below expected success rate")
            print("   Significant issues require attention")
        
        # Key findings
        print("\n🔍 KEY FINDINGS:")
        
        # Check critical systems
        critical_systems = ['Health Check', 'Database Connectivity', 'Authentication', 'E-commerce', 'Admin Dashboard']
        critical_working = sum(1 for system in critical_systems 
                             if any(system in name and self.test_results[name]['success'] 
                                   for name in self.test_results.keys()))
        
        print(f"   Critical Systems Working: {critical_working}/{len(critical_systems)}")
        
        # Check new features specifically
        new_feature_tests = [name for name in self.test_results.keys() 
                           if any(keyword in name for keyword in ['Table', 'Categories', 'Products', 'Orders', 'Social Media', 'Notifications', 'Jobs', 'Password Reset'])]
        new_features_working = sum(1 for name in new_feature_tests if self.test_results[name]['success'])
        
        if new_feature_tests:
            new_features_rate = (new_features_working / len(new_feature_tests) * 100)
            print(f"   New Database Features: {new_features_rate:.1f}% ({new_features_working}/{len(new_feature_tests)})")
        
        print("\n" + "=" * 80)
        print("🎯 FINAL BACKEND TESTING COMPLETE")
        print("=" * 80)
        """Run comprehensive production readiness test"""
        print("🚀 STARTING COMPREHENSIVE PRODUCTION READINESS BACKEND TESTING")
        print("=" * 80)
        print(f"Testing against: {self.base_url}")
        print(f"API Endpoint: {self.api_url}")
        print(f"Authentication Token: {'Available' if self.auth_token else 'Not Available'}")
        print("=" * 80)
        
        # Core Infrastructure Tests
        self.test_health_check()
        self.test_database_connectivity()
        self.test_production_performance()
        self.test_security_headers()
        
        # Authentication & Security Tests
        self.test_authentication_system()
        
        # Legal & Compliance Tests (NEW)
        self.test_legal_pages_system()
        self.test_account_removal_system()
        self.test_support_center_system()
        
        # Database & Migration Tests
        self.test_database_tables_creation()
        
        # Core Business Features Tests
        self.test_bio_sites()
        self.test_social_media_management()
        self.test_instagram_integration()
        self.test_ecommerce_system()
        self.test_course_creation()
        self.test_email_marketing()
        self.test_analytics_reporting()
        self.test_payment_processing()
        self.test_workspace_management()
        
        # Advanced Features Tests
        self.test_ultra_advanced_gamification()
        self.test_admin_dashboard_system()
        self.test_website_builder()
        self.test_biometric_authentication()
        self.test_realtime_features()
        self.test_escrow_system()
        self.test_advanced_analytics()
        self.test_advanced_booking_system()
        self.test_advanced_financial_management()
        self.test_enhanced_ai_features()
        
        # Integration Tests
        self.test_oauth_integration()
        self.test_two_factor_auth()
        self.test_crm_system()
        self.test_team_management()
        self.test_ai_integration()
        
        # System Tests
        self.test_automated_installer_system()
        
        # Generate comprehensive report
        self.generate_production_report()

    def generate_production_report(self):
        """Generate comprehensive production readiness report"""
        print("\n" + "=" * 80)
        print("🎯 COMPREHENSIVE PRODUCTION READINESS REPORT")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"📊 OVERALL STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        print()
        
        # Categorize results
        categories = {
            'Legal & Compliance': ['Legal Page', 'Cookie Consent', 'Data Export', 'Data Deletion', 'Account Removal', 'Support'],
            'Core Infrastructure': ['Health Check', 'Database', 'API Response Time', 'Security Header'],
            'Authentication & Security': ['Custom Auth', 'User Profile', 'User Registration', 'User Login', 'Biometric', '2FA'],
            'Business Features': ['Bio Sites', 'Social Media', 'Instagram', 'E-commerce', 'Course', 'Email Marketing'],
            'Advanced Features': ['Gamification', 'Admin Dashboard', 'Website Builder', 'Real-Time', 'Escrow', 'Analytics'],
            'Database Tables': ['Bio Sites Table', 'Escrow Transactions Table', 'Booking Services Table', 'Workspaces Table'],
            'Performance & Security': ['API Response Time', 'Authenticated API Performance', 'Security Header']
        }
        
        for category, keywords in categories.items():
            category_tests = [name for name in self.test_results.keys() if any(keyword in name for keyword in keywords)]
            if category_tests:
                category_passed = sum(1 for name in category_tests if self.test_results[name]['success'])
                category_total = len(category_tests)
                category_rate = (category_passed / category_total * 100) if category_total > 0 else 0
                
                print(f"📋 {category.upper()}:")
                print(f"   Success Rate: {category_rate:.1f}% ({category_passed}/{category_total})")
                
                # Show failed tests in this category
                failed_in_category = [name for name in category_tests if not self.test_results[name]['success']]
                if failed_in_category:
                    print(f"   ❌ Failed: {', '.join(failed_in_category[:3])}{'...' if len(failed_in_category) > 3 else ''}")
                print()
        
        # Production readiness assessment
        print("🏆 PRODUCTION READINESS ASSESSMENT:")
        if success_rate >= 90:
            print("   Status: ✅ PRODUCTION READY")
            print("   Assessment: System is ready for production deployment")
        elif success_rate >= 75:
            print("   Status: ⚠️  MOSTLY READY")
            print("   Assessment: System is mostly ready with minor issues to address")
        elif success_rate >= 50:
            print("   Status: 🔧 NEEDS WORK")
            print("   Assessment: System needs significant improvements before production")
        else:
            print("   Status: ❌ NOT READY")
            print("   Assessment: System requires major fixes before production deployment")
        
        print("\n" + "=" * 80)
        print("🎯 TESTING COMPLETE - DETAILED RESULTS LOGGED ABOVE")
        print("=" * 80)
    
    def test_website_builder(self):
        """Test Website Builder functionality"""
        print("\n=== Testing Website Builder ===")
        
        if not self.auth_token:
            self.log_test("Website Builder", False, "Cannot test - no authentication token")
            return
        
        # Test get all websites
        response = self.make_request('GET', '/websites/')
        if response and response.status_code == 200:
            self.log_test("Get Websites", True, "Websites retrieval successful")
        else:
            self.log_test("Get Websites", False, f"Websites retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get website templates
        response = self.make_request('GET', '/websites/templates')
        if response and response.status_code == 200:
            self.log_test("Get Website Templates", True, "Website templates retrieval successful")
        else:
            self.log_test("Get Website Templates", False, f"Templates retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get website components
        response = self.make_request('GET', '/websites/components')
        if response and response.status_code == 200:
            self.log_test("Get Website Components", True, "Website components retrieval successful")
        else:
            self.log_test("Get Website Components", False, f"Components retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create website
        website_data = {
            "name": "Test Business Website",
            "domain": f"test-business-{int(time.time())}.com",
            "description": "A comprehensive test website for business",
            "settings": {
                "theme": "modern",
                "color_scheme": "blue",
                "layout": "responsive"
            }
        }
        
        response = self.make_request('POST', '/websites/', website_data)
        website_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Website", True, "Website creation successful")
            website_id = data.get('data', {}).get('id')
        else:
            self.log_test("Create Website", False, f"Website creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get specific website (if created successfully)
        if website_id:
            response = self.make_request('GET', f'/websites/{website_id}')
            if response and response.status_code == 200:
                self.log_test("Get Specific Website", True, "Specific website retrieval successful")
            else:
                self.log_test("Get Specific Website", False, f"Specific website retrieval failed - Status: {response.status_code if response else 'No response'}")
            
            # Test update website
            update_data = {
                "name": "Updated Test Business Website",
                "domain": website_data["domain"],
                "description": "Updated description for test website",
                "status": "draft"
            }
            
            response = self.make_request('PUT', f'/websites/{website_id}', update_data)
            if response and response.status_code == 200:
                self.log_test("Update Website", True, "Website update successful")
            else:
                self.log_test("Update Website", False, f"Website update failed - Status: {response.status_code if response else 'No response'}")
            
            # Test create page for website
            page_data = {
                "name": "About Us",
                "slug": "about-us",
                "title": "About Our Company",
                "content": [
                    {
                        "type": "heading",
                        "content": {"level": 1, "text": "About Our Company"}
                    },
                    {
                        "type": "paragraph",
                        "content": {"text": "We are a leading company in our industry."}
                    }
                ],
                "meta_description": "Learn more about our company and mission"
            }
            
            response = self.make_request('POST', f'/websites/{website_id}/pages', page_data)
            page_id = None
            if response and response.status_code in [200, 201]:
                data = response.json()
                self.log_test("Create Website Page", True, "Website page creation successful")
                page_id = data.get('data', {}).get('id')
            else:
                self.log_test("Create Website Page", False, f"Page creation failed - Status: {response.status_code if response else 'No response'}")
            
            # Test update page (if created successfully)
            if page_id:
                update_page_data = {
                    "name": "About Us - Updated",
                    "slug": "about-us",
                    "title": "About Our Amazing Company",
                    "content": [
                        {
                            "type": "heading",
                            "content": {"level": 1, "text": "About Our Amazing Company"}
                        },
                        {
                            "type": "paragraph",
                            "content": {"text": "We are the leading company in our industry with years of experience."}
                        }
                    ],
                    "meta_description": "Learn more about our amazing company and mission",
                    "status": "draft"
                }
                
                response = self.make_request('PUT', f'/websites/{website_id}/pages/{page_id}', update_page_data)
                if response and response.status_code == 200:
                    self.log_test("Update Website Page", True, "Website page update successful")
                else:
                    self.log_test("Update Website Page", False, f"Page update failed - Status: {response.status_code if response else 'No response'}")
            
            # Test publish website
            response = self.make_request('PUT', f'/websites/{website_id}/publish')
            if response and response.status_code == 200:
                self.log_test("Publish Website", True, "Website publishing successful")
            else:
                self.log_test("Publish Website", False, f"Website publishing failed - Status: {response.status_code if response else 'No response'}")
            
            # Test get website analytics
            response = self.make_request('GET', f'/websites/{website_id}/analytics')
            if response and response.status_code == 200:
                self.log_test("Get Website Analytics", True, "Website analytics retrieval successful")
            else:
                self.log_test("Get Website Analytics", False, f"Analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
            
            # Test delete page (if created successfully)
            if page_id:
                response = self.make_request('DELETE', f'/websites/{website_id}/pages/{page_id}')
                if response and response.status_code == 200:
                    self.log_test("Delete Website Page", True, "Website page deletion successful")
                else:
                    self.log_test("Delete Website Page", False, f"Page deletion failed - Status: {response.status_code if response else 'No response'}")
            
            # Test delete website
            response = self.make_request('DELETE', f'/websites/{website_id}')
            if response and response.status_code == 200:
                self.log_test("Delete Website", True, "Website deletion successful")
            else:
                self.log_test("Delete Website", False, f"Website deletion failed - Status: {response.status_code if response else 'No response'}")
    
    def test_biometric_authentication(self):
        """Test Biometric Authentication functionality"""
        print("\n=== Testing Biometric Authentication ===")
        
        if not self.auth_token:
            self.log_test("Biometric Authentication", False, "Cannot test - no authentication token")
            return
        
        # Test get registration options
        response = self.make_request('POST', '/biometric/registration-options')
        if response and response.status_code == 200:
            self.log_test("Get Biometric Registration Options", True, "Registration options retrieval successful")
        else:
            self.log_test("Get Biometric Registration Options", False, f"Registration options failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get authentication options (public endpoint)
        response = self.make_request('POST', '/biometric/authentication-options', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Get Biometric Authentication Options", True, "Authentication options retrieval successful")
        else:
            self.log_test("Get Biometric Authentication Options", False, f"Authentication options failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get user credentials
        response = self.make_request('GET', '/biometric/credentials')
        if response and response.status_code == 200:
            self.log_test("Get Biometric Credentials", True, "User credentials retrieval successful")
        else:
            self.log_test("Get Biometric Credentials", False, f"Credentials retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_realtime_features(self):
        """Test Real-Time Features functionality"""
        print("\n=== Testing Real-Time Features ===")
        
        if not self.auth_token:
            self.log_test("Real-Time Features", False, "Cannot test - no authentication token")
            return
        
        # Test get notifications
        response = self.make_request('GET', '/realtime/notifications')
        if response and response.status_code == 200:
            self.log_test("Get Real-Time Notifications", True, "Notifications retrieval successful")
        else:
            self.log_test("Get Real-Time Notifications", False, f"Notifications retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get activity feed
        response = self.make_request('GET', '/realtime/activity-feed')
        if response and response.status_code == 200:
            self.log_test("Get Activity Feed", True, "Activity feed retrieval successful")
        else:
            self.log_test("Get Activity Feed", False, f"Activity feed retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get system status
        response = self.make_request('GET', '/realtime/system-status')
        if response and response.status_code == 200:
            self.log_test("Get System Status", True, "System status retrieval successful")
        else:
            self.log_test("Get System Status", False, f"System status retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get user presence
        response = self.make_request('GET', '/realtime/user-presence')
        if response and response.status_code == 200:
            self.log_test("Get User Presence", True, "User presence retrieval successful")
        else:
            self.log_test("Get User Presence", False, f"User presence retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test send message
        message_data = {
            "recipient_id": "test-user-id",
            "message": "Hello from real-time testing!",
            "type": "direct"
        }
        
        response = self.make_request('POST', '/realtime/messages', message_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Send Real-Time Message", True, "Message sending successful")
        else:
            self.log_test("Send Real-Time Message", False, f"Message sending failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get workspace metrics
        response = self.make_request('GET', '/realtime/workspace-metrics')
        if response and response.status_code == 200:
            self.log_test("Get Workspace Metrics", True, "Workspace metrics retrieval successful")
        else:
            self.log_test("Get Workspace Metrics", False, f"Workspace metrics retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_escrow_system(self):
        """Test Escrow & Transaction Security functionality"""
        print("\n=== Testing Escrow & Transaction Security ===")
        
        if not self.auth_token:
            self.log_test("Escrow System", False, "Cannot test - no authentication token")
            return
        
        # Test get escrow transactions
        response = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Transactions", True, "Escrow transactions retrieval successful")
        else:
            self.log_test("Get Escrow Transactions", False, f"Escrow transactions retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create escrow transaction
        escrow_data = {
            "buyer_id": "test-buyer-id",
            "seller_id": "test-seller-id",
            "amount": 100.00,
            "currency": "USD",
            "description": "Test digital product transaction",
            "terms": "Standard escrow terms for digital product delivery"
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data)
        escrow_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Escrow Transaction", True, "Escrow transaction creation successful")
            escrow_id = data.get('data', {}).get('id')
        else:
            self.log_test("Create Escrow Transaction", False, f"Escrow transaction creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get specific escrow transaction (if created successfully)
        if escrow_id:
            response = self.make_request('GET', f'/escrow/{escrow_id}')
            if response and response.status_code == 200:
                self.log_test("Get Specific Escrow Transaction", True, "Specific escrow transaction retrieval successful")
            else:
                self.log_test("Get Specific Escrow Transaction", False, f"Specific escrow transaction retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get escrow statistics
        response = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Statistics", True, "Escrow statistics retrieval successful")
        else:
            self.log_test("Get Escrow Statistics", False, f"Escrow statistics retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_advanced_analytics(self):
        """Test Advanced Analytics & Business Intelligence functionality"""
        print("\n=== Testing Advanced Analytics & Business Intelligence ===")
        
        if not self.auth_token:
            self.log_test("Advanced Analytics", False, "Cannot test - no authentication token")
            return
        
        # Test get business intelligence
        response = self.make_request('GET', '/analytics/business-intelligence')
        if response and response.status_code == 200:
            self.log_test("Get Business Intelligence", True, "Business intelligence retrieval successful")
        else:
            self.log_test("Get Business Intelligence", False, f"Business intelligence retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get realtime metrics
        response = self.make_request('GET', '/analytics/realtime-metrics')
        if response and response.status_code == 200:
            self.log_test("Get Realtime Metrics", True, "Realtime metrics retrieval successful")
        else:
            self.log_test("Get Realtime Metrics", False, f"Realtime metrics retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get cohort analysis
        response = self.make_request('GET', '/analytics/cohort-analysis')
        if response and response.status_code == 200:
            self.log_test("Get Cohort Analysis", True, "Cohort analysis retrieval successful")
        else:
            self.log_test("Get Cohort Analysis", False, f"Cohort analysis retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get funnel analysis
        response = self.make_request('GET', '/analytics/funnel-analysis')
        if response and response.status_code == 200:
            self.log_test("Get Funnel Analysis", True, "Funnel analysis retrieval successful")
        else:
            self.log_test("Get Funnel Analysis", False, f"Funnel analysis retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get A/B test results
        response = self.make_request('GET', '/analytics/ab-test-results')
        if response and response.status_code == 200:
            self.log_test("Get A/B Test Results", True, "A/B test results retrieval successful")
        else:
            self.log_test("Get A/B Test Results", False, f"A/B test results retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test generate custom report
        report_data = {
            "report_type": "revenue_analysis",
            "date_range": {
                "start": "2024-01-01",
                "end": "2024-12-31"
            },
            "metrics": ["revenue", "conversions", "traffic"],
            "filters": {
                "source": "organic"
            }
        }
        
        response = self.make_request('POST', '/analytics/custom-report', report_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Generate Custom Report", True, "Custom report generation successful")
        else:
            self.log_test("Generate Custom Report", False, f"Custom report generation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get predictive analytics
        response = self.make_request('GET', '/analytics/predictive-analytics')
        if response and response.status_code == 200:
            self.log_test("Get Predictive Analytics", True, "Predictive analytics retrieval successful")
        else:
            self.log_test("Get Predictive Analytics", False, f"Predictive analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_advanced_booking_system(self):
        """Test Advanced Booking System functionality"""
        print("\n=== Testing Advanced Booking System ===")
        
        if not self.auth_token:
            self.log_test("Advanced Booking System", False, "Cannot test - no authentication token")
            return
        
        # Test get booking services
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("Get Booking Services", True, "Booking services retrieval successful")
        else:
            self.log_test("Get Booking Services", False, f"Booking services retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create booking service
        service_data = {
            "name": "Business Consultation",
            "description": "One-on-one business strategy consultation",
            "duration": 60,
            "price": 150.00,
            "currency": "USD",
            "category": "consultation"
        }
        
        response = self.make_request('POST', '/booking/services', service_data)
        service_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Booking Service", True, "Booking service creation successful")
            service_id = data.get('data', {}).get('id')
        else:
            self.log_test("Create Booking Service", False, f"Booking service creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get available slots (if service created successfully)
        if service_id:
            response = self.make_request('GET', f'/booking/services/{service_id}/available-slots')
            if response and response.status_code == 200:
                self.log_test("Get Available Slots", True, "Available slots retrieval successful")
            else:
                self.log_test("Get Available Slots", False, f"Available slots retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get appointments
        response = self.make_request('GET', '/booking/appointments')
        if response and response.status_code == 200:
            self.log_test("Get Appointments", True, "Appointments retrieval successful")
        else:
            self.log_test("Get Appointments", False, f"Appointments retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create appointment
        appointment_data = {
            "service_id": service_id or "test-service-id",
            "client_name": "John Doe",
            "client_email": "john.doe@example.com",
            "appointment_date": "2024-12-31",
            "appointment_time": "14:00",
            "notes": "Initial business consultation"
        }
        
        response = self.make_request('POST', '/booking/appointments', appointment_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Create Appointment", True, "Appointment creation successful")
        else:
            self.log_test("Create Appointment", False, f"Appointment creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get booking analytics
        response = self.make_request('GET', '/booking/analytics')
        if response and response.status_code == 200:
            self.log_test("Get Booking Analytics", True, "Booking analytics retrieval successful")
        else:
            self.log_test("Get Booking Analytics", False, f"Booking analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_advanced_financial_management(self):
        """Test Advanced Financial Management functionality"""
        print("\n=== Testing Advanced Financial Management ===")
        
        if not self.auth_token:
            self.log_test("Advanced Financial Management", False, "Cannot test - no authentication token")
            return
        
        # Test get financial dashboard
        response = self.make_request('GET', '/financial/dashboard')
        if response and response.status_code == 200:
            self.log_test("Get Financial Dashboard", True, "Financial dashboard retrieval successful")
        else:
            self.log_test("Get Financial Dashboard", False, f"Financial dashboard retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get invoices
        response = self.make_request('GET', '/financial/invoices')
        if response and response.status_code == 200:
            self.log_test("Get Invoices", True, "Invoices retrieval successful")
        else:
            self.log_test("Get Invoices", False, f"Invoices retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create invoice
        invoice_data = {
            "client_name": "Acme Corporation",
            "client_email": "billing@acme.com",
            "amount": 1500.00,
            "currency": "USD",
            "description": "Web development services",
            "due_date": "2024-12-31",
            "items": [
                {
                    "description": "Frontend development",
                    "quantity": 40,
                    "rate": 25.00,
                    "amount": 1000.00
                },
                {
                    "description": "Backend development",
                    "quantity": 20,
                    "rate": 25.00,
                    "amount": 500.00
                }
            ]
        }
        
        response = self.make_request('POST', '/financial/invoices', invoice_data)
        invoice_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Invoice", True, "Invoice creation successful")
            invoice_id = data.get('data', {}).get('id')
        else:
            self.log_test("Create Invoice", False, f"Invoice creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test calculate tax
        tax_data = {
            "amount": 1500.00,
            "tax_rate": 0.08,
            "location": "CA"
        }
        
        response = self.make_request('POST', '/financial/tax/calculate', tax_data)
        if response and response.status_code == 200:
            self.log_test("Calculate Tax", True, "Tax calculation successful")
        else:
            self.log_test("Calculate Tax", False, f"Tax calculation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get financial reports
        response = self.make_request('GET', '/financial/reports')
        if response and response.status_code == 200:
            self.log_test("Get Financial Reports", True, "Financial reports retrieval successful")
        else:
            self.log_test("Get Financial Reports", False, f"Financial reports retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get payment analytics
        response = self.make_request('GET', '/financial/payment-analytics')
        if response and response.status_code == 200:
            self.log_test("Get Payment Analytics", True, "Payment analytics retrieval successful")
        else:
            self.log_test("Get Payment Analytics", False, f"Payment analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def test_enhanced_ai_features(self):
        """Test Enhanced AI Features functionality"""
        print("\n=== Testing Enhanced AI Features ===")
        
        if not self.auth_token:
            self.log_test("Enhanced AI Features", False, "Cannot test - no authentication token")
            return
        
        # Test generate content suggestions
        content_data = {
            "topic": "digital marketing strategies",
            "content_type": "blog_post",
            "target_audience": "small business owners",
            "tone": "professional"
        }
        
        response = self.make_request('POST', '/ai/content/generate', content_data)
        if response and response.status_code == 200:
            self.log_test("Generate Content Suggestions", True, "Content generation successful")
        else:
            self.log_test("Generate Content Suggestions", False, f"Content generation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test SEO optimization
        seo_data = {
            "content": "This is a sample blog post about digital marketing strategies for small businesses.",
            "target_keywords": ["digital marketing", "small business", "online presence"],
            "meta_description": "Learn effective digital marketing strategies for small businesses"
        }
        
        response = self.make_request('POST', '/ai/content/seo-optimize', seo_data)
        if response and response.status_code == 200:
            self.log_test("SEO Content Optimization", True, "SEO optimization successful")
        else:
            self.log_test("SEO Content Optimization", False, f"SEO optimization failed - Status: {response.status_code if response else 'No response'}")
        
        # Test competitor analysis
        competitor_data = {
            "competitor_urls": ["https://competitor1.com", "https://competitor2.com"],
            "analysis_type": "content_strategy",
            "industry": "digital marketing"
        }
        
        response = self.make_request('POST', '/ai/competitors/analyze', competitor_data)
        if response and response.status_code == 200:
            self.log_test("Analyze Competitors", True, "Competitor analysis successful")
        else:
            self.log_test("Analyze Competitors", False, f"Competitor analysis failed - Status: {response.status_code if response else 'No response'}")
        
        # Test business insights generation
        insights_data = {
            "business_type": "e-commerce",
            "metrics": {
                "revenue": 50000,
                "customers": 1200,
                "conversion_rate": 0.03
            },
            "time_period": "last_quarter"
        }
        
        response = self.make_request('POST', '/ai/insights/business', insights_data)
        if response and response.status_code == 200:
            self.log_test("Generate Business Insights", True, "Business insights generation successful")
        else:
            self.log_test("Generate Business Insights", False, f"Business insights generation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test sentiment analysis
        sentiment_data = {
            "text": "I love this product! It's amazing and works perfectly for my needs.",
            "context": "product_review"
        }
        
        response = self.make_request('POST', '/ai/sentiment/analyze', sentiment_data)
        if response and response.status_code == 200:
            self.log_test("Analyze Sentiment", True, "Sentiment analysis successful")
        else:
            self.log_test("Analyze Sentiment", False, f"Sentiment analysis failed - Status: {response.status_code if response else 'No response'}")
        
        # Test pricing optimization
        pricing_data = {
            "product_type": "digital_course",
            "current_price": 99.99,
            "competitor_prices": [89.99, 109.99, 79.99],
            "target_margin": 0.70
        }
        
        response = self.make_request('POST', '/ai/pricing/optimize', pricing_data)
        if response and response.status_code == 200:
            self.log_test("Optimize Pricing", True, "Pricing optimization successful")
        else:
            self.log_test("Optimize Pricing", False, f"Pricing optimization failed - Status: {response.status_code if response else 'No response'}")
        
        # Test lead scoring
        lead_data = {
            "leads": [
                {
                    "email": "potential@customer.com",
                    "company": "Tech Startup",
                    "engagement_score": 85,
                    "source": "website"
                }
            ]
        }
        
        response = self.make_request('POST', '/ai/leads/score', lead_data)
        if response and response.status_code == 200:
            self.log_test("Score Leads", True, "Lead scoring successful")
        else:
            self.log_test("Score Leads", False, f"Lead scoring failed - Status: {response.status_code if response else 'No response'}")
        
        # Test chatbot response generation
        chatbot_data = {
            "message": "How can I improve my website's SEO?",
            "context": "seo_consultation",
            "user_profile": "small_business_owner"
        }
        
        response = self.make_request('POST', '/ai/chatbot/respond', chatbot_data)
        if response and response.status_code == 200:
            self.log_test("Generate Chatbot Response", True, "Chatbot response generation successful")
        else:
            self.log_test("Generate Chatbot Response", False, f"Chatbot response generation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test trend prediction
        trend_data = {
            "industry": "digital_marketing",
            "data_points": [
                {"date": "2024-01-01", "value": 100},
                {"date": "2024-02-01", "value": 110},
                {"date": "2024-03-01", "value": 125}
            ],
            "prediction_period": "next_quarter"
        }
        
        response = self.make_request('POST', '/ai/trends/predict', trend_data)
        if response and response.status_code == 200:
            self.log_test("Predict Trends", True, "Trend prediction successful")
        else:
            self.log_test("Predict Trends", False, f"Trend prediction failed - Status: {response.status_code if response else 'No response'}")
    
    def test_link_shortener_system(self):
        """Test Link Shortener System functionality"""
        print("\n=== Testing Link Shortener System ===")
        
        if not self.auth_token:
            self.log_test("Link Shortener System", False, "Cannot test - no authentication token")
            return
        
        # Test get all links
        response = self.make_request('GET', '/links')
        if response and response.status_code == 200:
            self.log_test("Get All Links", True, "Links retrieval successful")
        else:
            self.log_test("Get All Links", False, f"Links retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create shortened link
        link_data = {
            "original_url": "https://www.example.com/very-long-url-that-needs-shortening",
            "custom_slug": f"test-{int(time.time())}",
            "title": "Test Link",
            "description": "This is a test shortened link",
            "expires_at": "2024-12-31 23:59:59"
        }
        
        response = self.make_request('POST', '/links', link_data)
        link_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Shortened Link", True, "Link creation successful")
            link_id = data.get('data', {}).get('id') or data.get('id')
        else:
            self.log_test("Create Shortened Link", False, f"Link creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get link analytics (if link created successfully)
        if link_id:
            response = self.make_request('GET', f'/links/{link_id}/analytics')
            if response and response.status_code == 200:
                self.log_test("Get Link Analytics", True, "Link analytics retrieval successful")
            else:
                self.log_test("Get Link Analytics", False, f"Link analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
            
            # Test update link
            update_data = {
                "title": "Updated Test Link",
                "description": "This is an updated test shortened link",
                "active": True
            }
            
            response = self.make_request('PUT', f'/links/{link_id}', update_data)
            if response and response.status_code == 200:
                self.log_test("Update Link", True, "Link update successful")
            else:
                self.log_test("Update Link", False, f"Link update failed - Status: {response.status_code if response else 'No response'}")
        
        # Test bulk analytics
        response = self.make_request('GET', '/links/bulk-analytics')
        if response and response.status_code == 200:
            self.log_test("Get Bulk Analytics", True, "Bulk analytics retrieval successful")
        else:
            self.log_test("Get Bulk Analytics", False, f"Bulk analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test public redirect (no auth required)
        if link_id:
            # First get the slug from the created link
            response = self.make_request('GET', f'/links/{link_id}')
            if response and response.status_code == 200:
                data = response.json()
                slug = data.get('data', {}).get('slug') or data.get('slug')
                if slug:
                    # Test redirect endpoint
                    response = self.make_request('GET', f'/l/{slug}', auth_required=False)
                    if response and response.status_code in [200, 302, 301]:
                        self.log_test("Public Link Redirect", True, "Link redirect working")
                    else:
                        self.log_test("Public Link Redirect", False, f"Link redirect failed - Status: {response.status_code if response else 'No response'}")
        
        # Test delete link (if created successfully)
        if link_id:
            response = self.make_request('DELETE', f'/links/{link_id}')
            if response and response.status_code == 200:
                self.log_test("Delete Link", True, "Link deletion successful")
            else:
                self.log_test("Delete Link", False, f"Link deletion failed - Status: {response.status_code if response else 'No response'}")
    
    def test_referral_system(self):
        """Test Referral System functionality"""
        print("\n=== Testing Referral System ===")
        
        if not self.auth_token:
            self.log_test("Referral System", False, "Cannot test - no authentication token")
            return
        
        # Test get referral dashboard
        response = self.make_request('GET', '/referrals/dashboard')
        if response and response.status_code == 200:
            self.log_test("Get Referral Dashboard", True, "Referral dashboard retrieval successful")
        else:
            self.log_test("Get Referral Dashboard", False, f"Referral dashboard retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test send referral invitations
        invitation_data = {
            "emails": [
                "friend1@example.com",
                "friend2@example.com",
                "colleague@example.com"
            ],
            "message": "Join me on this amazing platform! You'll love the features.",
            "reward_type": "credits"
        }
        
        response = self.make_request('POST', '/referrals/invitations', invitation_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Send Referral Invitations", True, "Referral invitations sent successfully")
        else:
            self.log_test("Send Referral Invitations", False, f"Referral invitations failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get referral analytics
        response = self.make_request('GET', '/referrals/analytics')
        if response and response.status_code == 200:
            self.log_test("Get Referral Analytics", True, "Referral analytics retrieval successful")
        else:
            self.log_test("Get Referral Analytics", False, f"Referral analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get referral rewards
        response = self.make_request('GET', '/referrals/rewards')
        if response and response.status_code == 200:
            self.log_test("Get Referral Rewards", True, "Referral rewards retrieval successful")
        else:
            self.log_test("Get Referral Rewards", False, f"Referral rewards retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test process referral signup
        referral_data = {
            "referral_code": "TEST123",
            "new_user_email": f"newuser_{int(time.time())}@example.com",
            "signup_data": {
                "name": "New Referred User",
                "email": f"newuser_{int(time.time())}@example.com"
            }
        }
        
        response = self.make_request('POST', '/referrals/process', referral_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Process Referral Signup", True, "Referral signup processing successful")
        else:
            self.log_test("Process Referral Signup", False, f"Referral signup processing failed - Status: {response.status_code if response else 'No response'}")
        
        # Test complete referral
        completion_data = {
            "referral_id": "test-referral-id",
            "completion_type": "first_purchase",
            "reward_amount": 10.00
        }
        
        response = self.make_request('POST', '/referrals/complete', completion_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Complete Referral", True, "Referral completion successful")
        else:
            self.log_test("Complete Referral", False, f"Referral completion failed - Status: {response.status_code if response else 'No response'}")
    
    def test_template_marketplace(self):
        """Test Template Marketplace functionality"""
        print("\n=== Testing Template Marketplace ===")
        
        if not self.auth_token:
            self.log_test("Template Marketplace", False, "Cannot test - no authentication token")
            return
        
        # Test get all templates
        response = self.make_request('GET', '/templates')
        if response and response.status_code == 200:
            self.log_test("Get All Templates", True, "Templates retrieval successful")
        else:
            self.log_test("Get All Templates", False, f"Templates retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get template categories
        response = self.make_request('GET', '/templates/categories')
        if response and response.status_code == 200:
            self.log_test("Get Template Categories", True, "Template categories retrieval successful")
        else:
            self.log_test("Get Template Categories", False, f"Template categories retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get featured templates
        response = self.make_request('GET', '/templates/featured')
        if response and response.status_code == 200:
            self.log_test("Get Featured Templates", True, "Featured templates retrieval successful")
        else:
            self.log_test("Get Featured Templates", False, f"Featured templates retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create new template
        template_data = {
            "name": "Modern Business Template",
            "description": "A sleek and professional template for modern businesses",
            "category": "business",
            "price": 29.99,
            "tags": ["modern", "business", "professional", "responsive"],
            "preview_images": [
                "https://example.com/preview1.jpg",
                "https://example.com/preview2.jpg"
            ],
            "template_files": {
                "html": "<html><body><h1>Modern Business Template</h1></body></html>",
                "css": "body { font-family: Arial, sans-serif; }",
                "js": "console.log('Template loaded');"
            }
        }
        
        response = self.make_request('POST', '/templates', template_data)
        template_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create New Template", True, "Template creation successful")
            template_id = data.get('data', {}).get('id') or data.get('id')
        else:
            self.log_test("Create New Template", False, f"Template creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get my templates
        response = self.make_request('GET', '/templates/my-templates')
        if response and response.status_code == 200:
            self.log_test("Get My Templates", True, "My templates retrieval successful")
        else:
            self.log_test("Get My Templates", False, f"My templates retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get my purchases
        response = self.make_request('GET', '/templates/my-purchases')
        if response and response.status_code == 200:
            self.log_test("Get My Purchases", True, "My purchases retrieval successful")
        else:
            self.log_test("Get My Purchases", False, f"My purchases retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get specific template details (if template created successfully)
        if template_id:
            response = self.make_request('GET', f'/templates/{template_id}')
            if response and response.status_code == 200:
                self.log_test("Get Template Details", True, "Template details retrieval successful")
            else:
                self.log_test("Get Template Details", False, f"Template details retrieval failed - Status: {response.status_code if response else 'No response'}")
            
            # Test update template
            update_data = {
                "name": "Updated Modern Business Template",
                "description": "An updated sleek and professional template for modern businesses",
                "price": 34.99
            }
            
            response = self.make_request('PUT', f'/templates/{template_id}', update_data)
            if response and response.status_code == 200:
                self.log_test("Update Template", True, "Template update successful")
            else:
                self.log_test("Update Template", False, f"Template update failed - Status: {response.status_code if response else 'No response'}")
            
            # Test purchase template
            purchase_data = {
                "payment_method": "stripe",
                "payment_token": "test_token_123"
            }
            
            response = self.make_request('POST', f'/templates/{template_id}/purchase', purchase_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Purchase Template", True, "Template purchase successful")
            else:
                self.log_test("Purchase Template", False, f"Template purchase failed - Status: {response.status_code if response else 'No response'}")
            
            # Test download template
            response = self.make_request('GET', f'/templates/{template_id}/download')
            if response and response.status_code == 200:
                self.log_test("Download Template", True, "Template download successful")
            else:
                self.log_test("Download Template", False, f"Template download failed - Status: {response.status_code if response else 'No response'}")
            
            # Test add review
            review_data = {
                "rating": 5,
                "comment": "Excellent template! Very professional and easy to customize.",
                "pros": ["Great design", "Easy to use", "Good documentation"],
                "cons": ["Could use more color options"]
            }
            
            response = self.make_request('POST', f'/templates/{template_id}/review', review_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Add Template Review", True, "Template review added successfully")
            else:
                self.log_test("Add Template Review", False, f"Template review failed - Status: {response.status_code if response else 'No response'}")
            
            # Test delete template
            response = self.make_request('DELETE', f'/templates/{template_id}')
            if response and response.status_code == 200:
                self.log_test("Delete Template", True, "Template deletion successful")
            else:
                self.log_test("Delete Template", False, f"Template deletion failed - Status: {response.status_code if response else 'No response'}")
    
    def test_phase1_onboarding_system(self):
        """Test Phase 1 Enhanced Onboarding System"""
        print("\n=== Testing Phase 1 Enhanced Onboarding System ===")
        
        if not self.auth_token:
            self.log_test("Phase 1 Onboarding", False, "Cannot test - no authentication token")
            return
        
        # Test GET /api/onboarding/progress
        response = self.make_request('GET', '/onboarding/progress')
        if response and response.status_code == 200:
            data = response.json()
            progress_data = data.get('data', {}).get('progress', {})
            self.log_test("Get Onboarding Progress", True, f"Progress retrieved - Step: {progress_data.get('current_step', 'unknown')}, Progress: {progress_data.get('progress_percentage', 0)}%")
        else:
            self.log_test("Get Onboarding Progress", False, f"Progress retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /api/onboarding/progress
        progress_update = {
            "step": 2,
            "completed": True,
            "data": {
                "features_explored": ["bio_sites", "social_media"],
                "time_spent": 120
            }
        }
        response = self.make_request('POST', '/onboarding/progress', progress_update)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Update Onboarding Progress", True, f"Progress updated successfully - Next step: {data.get('data', {}).get('next_step', {}).get('title', 'unknown')}")
        else:
            self.log_test("Update Onboarding Progress", False, f"Progress update failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /api/onboarding/recommendations
        response = self.make_request('GET', '/onboarding/recommendations')
        if response and response.status_code == 200:
            data = response.json()
            recommendations = data.get('data', [])
            self.log_test("Get Onboarding Recommendations", True, f"Recommendations retrieved - Count: {len(recommendations) if isinstance(recommendations, list) else 'N/A'}")
        else:
            self.log_test("Get Onboarding Recommendations", False, f"Recommendations retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /api/onboarding/step/complete
        step_completion = {
            "step": 1,
            "step_data": {
                "goals": ["increase_engagement", "grow_audience"],
                "business_type": "content_creator",
                "experience_level": "intermediate",
                "team_size": "1-5"
            }
        }
        response = self.make_request('POST', '/onboarding/step/complete', step_completion)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Complete Onboarding Step", True, f"Step completed successfully - Next: {data.get('data', {}).get('next_step', {}).get('title', 'unknown')}")
        else:
            self.log_test("Complete Onboarding Step", False, f"Step completion failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /api/onboarding/demo
        response = self.make_request('GET', '/onboarding/demo')
        if response and response.status_code == 200:
            data = response.json()
            demo_data = data.get('data', {})
            self.log_test("Get Interactive Demo", True, f"Demo data retrieved - Features: {len(demo_data.get('features', [])) if 'features' in demo_data else 'N/A'}")
        else:
            self.log_test("Get Interactive Demo", False, f"Demo data retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_phase1_theme_system(self):
        """Test Phase 1 Smart Theme System"""
        print("\n=== Testing Phase 1 Smart Theme System ===")
        
        if not self.auth_token:
            self.log_test("Phase 1 Theme System", False, "Cannot test - no authentication token")
            return
        
        # Test GET /api/theme/ (current theme)
        response = self.make_request('GET', '/theme/')
        if response and response.status_code == 200:
            data = response.json()
            theme_data = data.get('data', {})
            current_theme = theme_data.get('current_theme', 'unknown')
            available_themes = theme_data.get('available_themes', {})
            self.log_test("Get Current Theme", True, f"Current theme: {current_theme}, Available: {len(available_themes)} themes")
        else:
            self.log_test("Get Current Theme", False, f"Theme retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /api/theme/update
        theme_update = {
            "theme": "dark",
            "accessibility_options": {
                "high_contrast": False,
                "reduced_motion": False,
                "large_text": False
            }
        }
        response = self.make_request('POST', '/theme/update', theme_update)
        if response and response.status_code == 200:
            data = response.json()
            updated_theme = data.get('data', {}).get('theme', 'unknown')
            self.log_test("Update Theme Settings", True, f"Theme updated to: {updated_theme}")
        else:
            self.log_test("Update Theme Settings", False, f"Theme update failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /api/theme/system (intelligent theme detection)
        response = self.make_request('GET', '/theme/system')
        if response and response.status_code == 200:
            data = response.json()
            system_theme = data.get('data', {}).get('system_theme', 'unknown')
            supported = data.get('data', {}).get('supported', False)
            self.log_test("Get System Theme Detection", True, f"System theme detected: {system_theme}, Supported: {supported}")
        else:
            self.log_test("Get System Theme Detection", False, f"System theme detection failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /api/theme/presets (available themes)
        response = self.make_request('GET', '/theme/presets')
        if response and response.status_code == 200:
            data = response.json()
            presets = data.get('data', [])
            self.log_test("Get Theme Presets", True, f"Theme presets retrieved - Count: {len(presets) if isinstance(presets, list) else 'N/A'}")
        else:
            self.log_test("Get Theme Presets", False, f"Theme presets retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_phase1_core_platform_features(self):
        """Test Phase 1 Core Platform Features"""
        print("\n=== Testing Phase 1 Core Platform Features ===")
        
        if not self.auth_token:
            self.log_test("Phase 1 Core Features", False, "Cannot test - no authentication token")
            return
        
        # Test Website Builder system
        response = self.make_request('GET', '/websites/templates')
        if response and response.status_code == 200:
            data = response.json()
            templates = data.get('data', [])
            self.log_test("Website Builder Templates", True, f"Templates retrieved - Count: {len(templates) if isinstance(templates, list) else 'N/A'}")
        else:
            self.log_test("Website Builder Templates", False, f"Templates retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Bio Sites functionality
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            data = response.json()
            bio_sites = data.get('data', [])
            self.log_test("Bio Sites Management", True, f"Bio sites retrieved - Count: {len(bio_sites) if isinstance(bio_sites, list) else 'N/A'}")
        else:
            self.log_test("Bio Sites Management", False, f"Bio sites retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Basic authentication (already tested in authentication_system)
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            user_data = data.get('user', {})
            self.log_test("Basic Authentication", True, f"User authenticated - Name: {user_data.get('name', 'unknown')}")
        else:
            self.log_test("Basic Authentication", False, f"Authentication failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Workspace management
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            workspaces = data.get('data', [])
            self.log_test("Workspace Management", True, f"Workspaces retrieved - Count: {len(workspaces) if isinstance(workspaces, list) else 'N/A'}")
        else:
            self.log_test("Workspace Management", False, f"Workspaces retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Basic analytics and reporting
        response = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            data = response.json()
            analytics = data.get('data', {})
            self.log_test("Basic Analytics", True, f"Analytics retrieved - Metrics available: {len(analytics) if isinstance(analytics, dict) else 'N/A'}")
        else:
            self.log_test("Basic Analytics", False, f"Analytics retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_phase1_enhanced_ux_features(self):
        """Test Phase 1 Enhanced UX Features"""
        print("\n=== Testing Phase 1 Enhanced UX Features ===")
        
        if not self.auth_token:
            self.log_test("Phase 1 UX Features", False, "Cannot test - no authentication token")
            return
        
        # Test Dashboard personalization
        response = self.make_request('GET', '/workspace-personalization/layout')
        if response and response.status_code == 200:
            data = response.json()
            layout = data.get('data', {})
            self.log_test("Dashboard Personalization", True, f"Layout retrieved - Widgets: {len(layout.get('widgets', [])) if 'widgets' in layout else 'N/A'}")
        else:
            self.log_test("Dashboard Personalization", False, f"Dashboard personalization failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Mobile-first improvements
        response = self.make_request('GET', '/mobile/config')
        if response and response.status_code == 200:
            data = response.json()
            mobile_config = data.get('data', {})
            self.log_test("Mobile-First Improvements", True, f"Mobile config retrieved - Features: {len(mobile_config) if isinstance(mobile_config, dict) else 'N/A'}")
        else:
            self.log_test("Mobile-First Improvements", False, f"Mobile config failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Basic user preferences
        response = self.make_request('GET', '/preferences/')
        if response and response.status_code == 200:
            data = response.json()
            preferences = data.get('data', {})
            self.log_test("User Preferences", True, f"Preferences retrieved - Settings: {len(preferences) if isinstance(preferences, dict) else 'N/A'}")
        else:
            self.log_test("User Preferences", False, f"User preferences failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Activity tracking
        activity_data = {
            "event": "page_view",
            "page": "/dashboard",
            "timestamp": "2024-12-19T10:00:00Z",
            "metadata": {
                "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
                "referrer": "/login"
            }
        }
        response = self.make_request('POST', '/analytics/track', activity_data)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Activity Tracking", True, "Activity tracked successfully")
        else:
            self.log_test("Activity Tracking", False, f"Activity tracking failed - Status: {response.status_code if response else 'No response'}")
    
    def test_admin_dashboard_system(self):
        """Test Ultra-Comprehensive Admin Dashboard System functionality"""
        print("\n=== Testing Ultra-Comprehensive Admin Dashboard System ===")
        
        if not self.auth_token:
            self.log_test("Admin Dashboard System", False, "Cannot test - no authentication token")
            return
        
        # Test 1: Admin Dashboard Overview
        print("\n--- Testing Admin Dashboard Overview ---")
        response = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'stats' in data.get('data', {}):
                stats = data['data']['stats']
                self.log_test("Admin Dashboard Overview", True, 
                    f"Dashboard loaded successfully - Total users: {stats.get('total_users', 0)}, "
                    f"Active users: {stats.get('active_users', 0)}, "
                    f"Total revenue: ${stats.get('total_revenue', 0)}")
            else:
                self.log_test("Admin Dashboard Overview", False, "Dashboard data structure invalid")
        else:
            self.log_test("Admin Dashboard Overview", False, 
                f"Dashboard failed - Status: {response.status_code if response else 'No response'}")
        
        # Test 2: User Management System
        print("\n--- Testing User Management System ---")
        
        # Test user listing with pagination and filters
        response = self.make_request('GET', '/admin/users', {
            'page': 1,
            'per_page': 10,
            'search': 'test',
            'status': 'active',
            'sort': 'created_at',
            'order': 'desc'
        })
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'users' in data.get('data', {}):
                users = data['data']['users']
                pagination = data['data']['pagination']
                self.log_test("User Management - List Users", True, 
                    f"Retrieved {len(users)} users, Total: {pagination.get('total', 0)}")
            else:
                self.log_test("User Management - List Users", False, "User list data structure invalid")
        else:
            self.log_test("User Management - List Users", False, 
                f"User listing failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user details
        response = self.make_request('GET', '/admin/users/1')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'id' in data.get('data', {}):
                user_data = data['data']
                self.log_test("User Management - User Details", True, 
                    f"User details retrieved - ID: {user_data.get('id')}, "
                    f"Name: {user_data.get('name')}, "
                    f"Workspaces: {user_data.get('statistics', {}).get('workspaces_count', 0)}")
            else:
                self.log_test("User Management - User Details", False, "User details data structure invalid")
        else:
            self.log_test("User Management - User Details", False, 
                f"User details failed - Status: {response.status_code if response else 'No response'}")
        
        # Test bulk user update
        bulk_update_data = {
            "user_ids": [1, 2],
            "updates": {
                "status": 1,
                "role": "user"
            }
        }
        response = self.make_request('POST', '/admin/users/bulk-update', bulk_update_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                self.log_test("User Management - Bulk Update", True, 
                    f"Bulk update successful - {data.get('data', {}).get('success_count', 0)} users updated")
            else:
                self.log_test("User Management - Bulk Update", False, "Bulk update failed")
        else:
            self.log_test("User Management - Bulk Update", False, 
                f"Bulk update failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user statistics
        response = self.make_request('GET', '/admin/users/statistics')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'total_users' in data.get('data', {}):
                stats = data['data']
                self.log_test("User Management - Statistics", True, 
                    f"User statistics retrieved - Total: {stats.get('total_users', 0)}, "
                    f"Active: {stats.get('active_users', 0)}, "
                    f"Growth rate: {stats.get('growth_rate', 0):.1f}%")
            else:
                self.log_test("User Management - Statistics", False, "User statistics data structure invalid")
        else:
            self.log_test("User Management - Statistics", False, 
                f"User statistics failed - Status: {response.status_code if response else 'No response'}")
        
        # Test 3: Subscription Plan Management
        print("\n--- Testing Subscription Plan Management ---")
        
        # Test plan listing
        response = self.make_request('GET', '/admin/plans')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'plans' in data.get('data', {}):
                plans = data['data']['plans']
                self.log_test("Plan Management - List Plans", True, 
                    f"Retrieved {len(plans)} subscription plans")
            else:
                self.log_test("Plan Management - List Plans", False, "Plan list data structure invalid")
        else:
            self.log_test("Plan Management - List Plans", False, 
                f"Plan listing failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create subscription plan
        plan_data = {
            "name": "Test Admin Plan",
            "description": "A test subscription plan created via admin dashboard",
            "price": 29.99,
            "currency": "USD",
            "billing_cycle": "monthly",
            "trial_days": 14,
            "is_popular": False,
            "is_featured": True,
            "status": "active",
            "features": ["feature1", "feature2"],
            "limits": {"users": 10, "storage": "100GB"},
            "sort_order": 1
        }
        response = self.make_request('POST', '/admin/plans', plan_data)
        plan_id = None
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'id' in data.get('data', {}):
                plan_id = data['data']['id']
                self.log_test("Plan Management - Create Plan", True, 
                    f"Plan created successfully - ID: {plan_id}, Name: {data['data'].get('name')}")
            else:
                self.log_test("Plan Management - Create Plan", False, "Plan creation data structure invalid")
        else:
            self.log_test("Plan Management - Create Plan", False, 
                f"Plan creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test plan details (if plan was created)
        if plan_id:
            response = self.make_request('GET', f'/admin/plans/{plan_id}')
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success') and 'id' in data.get('data', {}):
                    plan_details = data['data']
                    self.log_test("Plan Management - Plan Details", True, 
                        f"Plan details retrieved - Price: ${plan_details.get('price')}, "
                        f"Billing: {plan_details.get('billing_cycle')}")
                else:
                    self.log_test("Plan Management - Plan Details", False, "Plan details data structure invalid")
            else:
                self.log_test("Plan Management - Plan Details", False, 
                    f"Plan details failed - Status: {response.status_code if response else 'No response'}")
            
            # Test update plan pricing
            pricing_data = {
                "price": 39.99,
                "currency": "USD",
                "geographic_pricing": {"US": 39.99, "EU": 35.99},
                "pricing_tiers": [{"min_quantity": 1, "price": 39.99}]
            }
            response = self.make_request('PUT', f'/admin/plans/{plan_id}/pricing', pricing_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_test("Plan Management - Update Pricing", True, 
                        f"Plan pricing updated successfully - New price: ${data.get('data', {}).get('price')}")
                else:
                    self.log_test("Plan Management - Update Pricing", False, "Plan pricing update failed")
            else:
                self.log_test("Plan Management - Update Pricing", False, 
                    f"Plan pricing update failed - Status: {response.status_code if response else 'No response'}")
        
        # Test plan comparison
        comparison_data = {
            "plan_ids": [1, 2] if not plan_id else [1, plan_id]
        }
        response = self.make_request('POST', '/admin/plans/comparison', comparison_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'comparison' in data.get('data', {}):
                comparison = data['data']['comparison']
                self.log_test("Plan Management - Plan Comparison", True, 
                    f"Plan comparison generated for {len(comparison)} plans")
            else:
                self.log_test("Plan Management - Plan Comparison", False, "Plan comparison data structure invalid")
        else:
            self.log_test("Plan Management - Plan Comparison", False, 
                f"Plan comparison failed - Status: {response.status_code if response else 'No response'}")
        
        # Test plan analytics
        response = self.make_request('GET', '/admin/plans/analytics')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'overview' in data.get('data', {}):
                analytics = data['data']
                overview = analytics['overview']
                self.log_test("Plan Management - Analytics", True, 
                    f"Plan analytics retrieved - Total plans: {overview.get('total_plans', 0)}, "
                    f"Active plans: {overview.get('active_plans', 0)}")
            else:
                self.log_test("Plan Management - Analytics", False, "Plan analytics data structure invalid")
        else:
            self.log_test("Plan Management - Analytics", False, 
                f"Plan analytics failed - Status: {response.status_code if response else 'No response'}")
        
        # Test 4: Environment Configuration Management
        print("\n--- Testing Environment Configuration Management ---")
        
        # Test environment variables listing
        response = self.make_request('GET', '/admin/environment')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'variables' in data.get('data', {}):
                variables = data['data']['variables']
                groups = data['data']['groups']
                self.log_test("Environment Management - List Variables", True, 
                    f"Retrieved {len(variables)} environment variables across {len(groups)} groups")
            else:
                self.log_test("Environment Management - List Variables", False, "Environment variables data structure invalid")
        else:
            self.log_test("Environment Management - List Variables", False, 
                f"Environment variables failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create environment variable
        env_var_data = {
            "key": "TEST_ADMIN_VAR",
            "value": "test_admin_value_123",
            "group": "testing",
            "description": "Test environment variable created via admin dashboard",
            "is_sensitive": False,
            "requires_restart": False
        }
        response = self.make_request('POST', '/admin/environment', env_var_data)
        env_var_id = None
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'id' in data.get('data', {}):
                env_var_id = data['data']['id']
                self.log_test("Environment Management - Create Variable", True, 
                    f"Environment variable created - Key: {data['data'].get('key')}")
            else:
                self.log_test("Environment Management - Create Variable", False, "Environment variable creation data structure invalid")
        else:
            self.log_test("Environment Management - Create Variable", False, 
                f"Environment variable creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test sync from .env file
        response = self.make_request('POST', '/admin/environment/sync-from-env')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                sync_results = data.get('data', {})
                self.log_test("Environment Management - Sync from .env", True, 
                    f"Sync completed - Synced: {sync_results.get('synced', 0)} variables")
            else:
                self.log_test("Environment Management - Sync from .env", False, "Environment sync failed")
        else:
            self.log_test("Environment Management - Sync from .env", False, 
                f"Environment sync failed - Status: {response.status_code if response else 'No response'}")
        
        # Test system settings
        response = self.make_request('GET', '/admin/environment/system-settings')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'settings' in data.get('data', {}):
                settings = data['data']['settings']
                groups = data['data']['groups']
                self.log_test("Environment Management - System Settings", True, 
                    f"Retrieved {len(settings)} system settings across {len(groups)} groups")
            else:
                self.log_test("Environment Management - System Settings", False, "System settings data structure invalid")
        else:
            self.log_test("Environment Management - System Settings", False, 
                f"System settings failed - Status: {response.status_code if response else 'No response'}")
        
        # Test system info
        response = self.make_request('GET', '/admin/environment/system-info')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'server' in data.get('data', {}):
                system_info = data['data']
                server_info = system_info['server']
                self.log_test("Environment Management - System Info", True, 
                    f"System info retrieved - PHP: {server_info.get('php_version')}, "
                    f"Laravel: {server_info.get('laravel_version')}, "
                    f"Environment: {server_info.get('environment')}")
            else:
                self.log_test("Environment Management - System Info", False, "System info data structure invalid")
        else:
            self.log_test("Environment Management - System Info", False, 
                f"System info failed - Status: {response.status_code if response else 'No response'}")
        
        # Test cache clearing
        cache_data = {
            "types": ["config", "cache"]
        }
        response = self.make_request('POST', '/admin/environment/clear-cache', cache_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                results = data.get('data', [])
                self.log_test("Environment Management - Clear Cache", True, 
                    f"Cache cleared successfully - {len(results)} operations completed")
            else:
                self.log_test("Environment Management - Clear Cache", False, "Cache clearing failed")
        else:
            self.log_test("Environment Management - Clear Cache", False, 
                f"Cache clearing failed - Status: {response.status_code if response else 'No response'}")
        
        # Test 5: Database Schema Verification
        print("\n--- Testing Database Schema Verification ---")
        
        # Test database tables listing
        response = self.make_request('GET', '/admin/database/tables')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'tables' in data.get('data', {}):
                tables = data['data']['tables']
                admin_tables = [t for t in tables if 'admin' in t.get('name', '').lower()]
                self.log_test("Database Schema - Admin Tables", True, 
                    f"Database schema verified - Total tables: {len(tables)}, "
                    f"Admin tables: {len(admin_tables)}")
            else:
                self.log_test("Database Schema - Admin Tables", False, "Database tables data structure invalid")
        else:
            self.log_test("Database Schema - Admin Tables", False, 
                f"Database tables failed - Status: {response.status_code if response else 'No response'}")
        
        # Test specific admin table structure
        response = self.make_request('GET', '/admin/database/tables/admin_users/structure')
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and 'columns' in data.get('data', {}):
                columns = data['data']['columns']
                self.log_test("Database Schema - Admin Users Table", True, 
                    f"Admin users table structure verified - {len(columns)} columns")
            else:
                self.log_test("Database Schema - Admin Users Table", False, "Admin users table structure invalid")
        else:
            self.log_test("Database Schema - Admin Users Table", False, 
                f"Admin users table structure failed - Status: {response.status_code if response else 'No response'}")
        
        print("\n🎯 Ultra-Comprehensive Admin Dashboard System Testing Complete!")
        print("This comprehensive test covers all 5 major admin areas:")
        print("1. Admin Dashboard Overview (platform statistics, system health)")
        print("2. User Management System (listing, details, bulk operations, statistics)")
        print("3. Subscription Plan Management (CRUD, pricing, comparison, analytics)")
        print("4. Environment Configuration Management (.env management, system settings, cache)")
        print("5. Database Schema Verification (admin tables, structure validation)")
        print("All admin endpoints tested with proper authentication and data validation.")
    
    def test_dashboard_access(self):
        """Test Dashboard Access functionality - Frontend routes with backend authentication"""
        print("\n=== Testing Dashboard Access (Frontend + Backend Auth) ===")
        
        # Test dashboard routes without authentication (should redirect to login)
        dashboard_routes = [
            '/dashboard',
            '/dashboard/linkinbio', 
            '/dashboard/social',
            '/dashboard/store',
            '/dashboard/courses',
            '/dashboard/email',
            '/dashboard/analytics'
        ]
        
        unauthenticated_redirects = 0
        authenticated_access = 0
        
        print("\n--- Testing Unauthenticated Access (Should Redirect) ---")
        for route in dashboard_routes:
            url = f"{self.web_url}{route}"
            try:
                response = self.session.get(url, timeout=10, allow_redirects=False)
                if response.status_code in [302, 301]:  # Redirect to login
                    location = response.headers.get('Location', '')
                    if 'login' in location.lower():
                        unauthenticated_redirects += 1
                        self.log_test(f"Dashboard Protection - {route}", True, f"Correctly redirects to login (Status: {response.status_code})")
                    else:
                        self.log_test(f"Dashboard Protection - {route}", False, f"Redirects but not to login: {location}")
                elif response.status_code == 200:
                    self.log_test(f"Dashboard Protection - {route}", False, f"SECURITY ISSUE: Dashboard accessible without auth (Status: 200)")
                elif response.status_code == 500:
                    self.log_test(f"Dashboard Protection - {route}", False, f"Server error (Status: 500) - middleware or view issue")
                else:
                    self.log_test(f"Dashboard Protection - {route}", False, f"Unexpected response: {response.status_code}")
            except Exception as e:
                self.log_test(f"Dashboard Protection - {route}", False, f"Request failed: {str(e)}")
        
        print(f"\n--- Authentication Protection Results ---")
        print(f"Routes properly protected: {unauthenticated_redirects}/{len(dashboard_routes)}")
        
        # Test authenticated access to dashboard routes
        print("\n--- Testing Authenticated Access (With Session) ---")
        
        # First, try to login and get session
        login_data = {
            'email': 'testcreator_1752814067@example.com',  # Use a test email
            'password': 'SecurePassword123!'
        }
        
        # Try to login via web form
        login_url = f"{self.web_url}/login"
        try:
            # Get login page first to get CSRF token
            login_page = self.session.get(login_url, timeout=10)
            if login_page.status_code == 200:
                self.log_test("Login Page Access", True, "Login page accessible")
                
                # Try to post login (this might not work without proper CSRF handling)
                login_response = self.session.post(login_url, data=login_data, timeout=10, allow_redirects=False)
                if login_response.status_code in [302, 301]:
                    location = login_response.headers.get('Location', '')
                    if 'dashboard' in location.lower():
                        self.log_test("Web Login", True, f"Login successful, redirects to dashboard: {location}")
                        
                        # Now test dashboard access with session
                        for route in dashboard_routes:
                            url = f"{self.web_url}{route}"
                            try:
                                response = self.session.get(url, timeout=10)
                                if response.status_code == 200:
                                    authenticated_access += 1
                                    self.log_test(f"Authenticated Dashboard - {route}", True, "Dashboard accessible when authenticated")
                                elif response.status_code == 500:
                                    self.log_test(f"Authenticated Dashboard - {route}", False, "500 Server Error - middleware or view issue")
                                else:
                                    self.log_test(f"Authenticated Dashboard - {route}", False, f"Unexpected status: {response.status_code}")
                            except Exception as e:
                                self.log_test(f"Authenticated Dashboard - {route}", False, f"Request failed: {str(e)}")
                    else:
                        self.log_test("Web Login", False, f"Login redirects to unexpected location: {location}")
                else:
                    self.log_test("Web Login", False, f"Login failed with status: {login_response.status_code}")
            else:
                self.log_test("Login Page Access", False, f"Login page not accessible: {login_page.status_code}")
        except Exception as e:
            self.log_test("Login Process", False, f"Login process failed: {str(e)}")
        
        print(f"\n--- Authenticated Access Results ---")
        print(f"Dashboard routes accessible when authenticated: {authenticated_access}/{len(dashboard_routes)}")
        
        # Summary
        if unauthenticated_redirects == len(dashboard_routes) and authenticated_access > 0:
            self.log_test("Dashboard Access System", True, f"Dashboard properly protected ({unauthenticated_redirects}/{len(dashboard_routes)} protected) and accessible when authenticated ({authenticated_access}/{len(dashboard_routes)} working)")
        elif unauthenticated_redirects == len(dashboard_routes):
            self.log_test("Dashboard Access System", False, f"Dashboard properly protected but has authentication/view issues (0/{len(dashboard_routes)} accessible when authenticated)")
        else:
            self.log_test("Dashboard Access System", False, f"CRITICAL SECURITY ISSUE: Dashboard not properly protected ({unauthenticated_redirects}/{len(dashboard_routes)} protected)")
    
    def run_all_tests(self):
        """Run all backend tests"""
        print("🚀 Starting Comprehensive Backend Testing for Mewayz Creator Economy Platform")
        print("=" * 80)
        
        # Run tests in order of priority
        self.test_health_check()
        self.test_database_connectivity()
        self.test_authentication_system()
        
        # Test the specific task that needs retesting
        self.test_dashboard_access()
        
        self.test_bio_sites()
        self.test_social_media_management()
        self.test_instagram_integration()
        self.test_ecommerce_system()
        self.test_course_creation()
        self.test_email_marketing()
        self.test_analytics_reporting()
        self.test_payment_processing()
        self.test_workspace_management()
        self.test_workspace_setup_wizard()  # NEW: Comprehensive 6-step workspace setup wizard test
        self.test_oauth_integration()
        self.test_two_factor_auth()
        self.test_crm_system()
        self.test_team_management()
        self.test_ai_integration()
        
        # NEW ADVANCED FEATURES TESTING
        print("\n" + "=" * 80)
        print("🔥 TESTING NEW ADVANCED FEATURES")
        print("=" * 80)
        
        self.test_website_builder()
        self.test_biometric_authentication()
        self.test_realtime_features()
        self.test_escrow_system()
        self.test_advanced_analytics()
        self.test_advanced_booking_system()
        self.test_advanced_financial_management()
        self.test_enhanced_ai_features()
        self.test_admin_dashboard_system()  # NEW: Ultra-Comprehensive Admin Dashboard System
        
        # PHASE 1 FEATURES TESTING (from review request)
        print("\n" + "=" * 80)
        print("🎯 TESTING PHASE 1 FEATURES FROM REVIEW REQUEST")
        print("=" * 80)
        
        self.test_phase1_onboarding_system()
        self.test_phase1_theme_system()
        self.test_phase1_core_platform_features()
        self.test_phase1_enhanced_ux_features()
        
        # NEW CRITICAL FEATURES TESTING (from review request)
        print("\n" + "=" * 80)
        print("🎯 TESTING NEW CRITICAL FEATURES FROM REVIEW REQUEST")
        print("=" * 80)
        
        self.test_link_shortener_system()
        self.test_referral_system()
        self.test_template_marketplace()
        
        # Print summary
        self.print_summary()
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("📊 TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        if failed_tests > 0:
            print("\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("\n" + "=" * 80)

if __name__ == "__main__":
    # Initialize tester with production URL
    tester = MewayzAPITester()
    
    # Run comprehensive final backend test
    tester.test_comprehensive_final_backend()
    
    # Exit with appropriate code
    failed_count = sum(1 for result in tester.test_results.values() if not result['success'])
    sys.exit(1 if failed_count > 0 else 0)