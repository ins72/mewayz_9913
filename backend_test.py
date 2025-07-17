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
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token
        self.auth_token = "3|d9wgyYICsOXueMFOUSQcIcsJkNIiyTp8L9Jd2ugL33bfaf85"
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
                response = self.session.get(url, headers=default_headers, params=data, timeout=30)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=30)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=30)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url}")
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
    
    def run_all_tests(self):
        """Run all backend tests"""
        print("🚀 Starting Comprehensive Backend Testing for Mewayz Creator Economy Platform")
        print("=" * 80)
        
        # Run tests in order of priority
        self.test_health_check()
        self.test_database_connectivity()
        self.test_authentication_system()
        self.test_bio_sites()
        self.test_social_media_management()
        self.test_instagram_integration()
        self.test_ecommerce_system()
        self.test_course_creation()
        self.test_email_marketing()
        self.test_analytics_reporting()
        self.test_payment_processing()
        self.test_workspace_management()
        self.test_oauth_integration()
        self.test_two_factor_auth()
        self.test_crm_system()
        self.test_team_management()
        self.test_ai_integration()
        self.test_website_builder()
        
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
    # Initialize tester
    tester = MewayzAPITester()
    
    # Run all tests
    tester.run_all_tests()
    
    # Exit with appropriate code
    failed_count = sum(1 for result in tester.test_results.values() if not result['success'])
    sys.exit(1 if failed_count > 0 else 0)