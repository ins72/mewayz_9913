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
        # Use the provided test token from the review request
        self.auth_token = "4|6AHqx0qtn59SBkCoejV1tsh7M9RDpyQRWMaBxR3R352c7ba3"
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
                response = self.session.get(url, headers=default_headers, params=data)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
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
        
        # Test user registration
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
                self.auth_token = data['token']
            elif 'access_token' in data:
                self.auth_token = data['access_token']
                
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
            
            # Update auth token
            if 'token' in data:
                self.auth_token = data['token']
            elif 'access_token' in data:
                self.auth_token = data['access_token']
        else:
            self.log_test("User Login", False, f"Login failed - Status: {response.status_code if response else 'No response'}")
        
        # Test authenticated user profile
        if self.auth_token:
            response = self.make_request('GET', '/auth/me')
            if response and response.status_code == 200:
                self.log_test("User Profile", True, "User profile retrieval successful")
            else:
                self.log_test("User Profile", False, f"Profile retrieval failed - Status: {response.status_code if response else 'No response'}")
    
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
            "title": "Test Creator Bio",
            "slug": f"test-creator-{int(time.time())}",
            "description": "This is a test bio site for the creator",
            "theme": "default"
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
            "status": "active"
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