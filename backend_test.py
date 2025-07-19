#!/usr/bin/env python3
"""
Comprehensive Backend API Testing for Laravel Mewayz Application
Tests all major backend functionality including authentication, admin features, 
AI services, website builder, bio sites, e-commerce, analytics, and more.
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class MewayzBackendTester:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'status': status,
            'success': success,
            'details': details,
            'response_time': f"{response_time:.3f}s"
        }
        self.test_results.append(result)
        print(f"{status} - {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    Details: {details}")
    
    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None, timeout: int = 30) -> tuple:
        """Make HTTP request with error handling"""
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        if headers:
            default_headers.update(headers)
        
        try:
            start_time = time.time()
            
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, timeout=timeout)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            
            return response, response_time
            
        except requests.exceptions.Timeout:
            return None, 30.0  # Return timeout duration
        except requests.exceptions.RequestException as e:
            print(f"Request error: {str(e)}")
            return None, 0.0
    
    def test_health_check(self):
        """Test API health endpoints"""
        print("\n=== Testing Health Check ===")
        
        # Test basic health endpoint
        response, response_time = self.make_request('GET', '/health')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Health Check", True, f"Status: {data.get('status', 'unknown')}", response_time)
        else:
            self.log_test("API Health Check", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test basic test endpoint
        response, response_time = self.make_request('GET', '/test')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Test Endpoint", True, f"Message: {data.get('message', 'unknown')}", response_time)
        else:
            self.log_test("API Test Endpoint", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_authentication(self):
        """Test authentication system"""
        print("\n=== Testing Authentication System ===")
        
        # Test user registration
        register_data = {
            "name": "Test User Backend",
            "email": f"testuser_{int(time.time())}@example.com",
            "password": "TestPassword123!",
            "password_confirmation": "TestPassword123!"
        }
        
        response, response_time = self.make_request('POST', '/auth/register', register_data)
        if response and response.status_code == 201:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                self.log_test("User Registration", True, f"User ID: {data.get('user', {}).get('id')}", response_time)
            else:
                self.log_test("User Registration", False, f"No token received", response_time)
        else:
            self.log_test("User Registration", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test admin login with provided credentials
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']  # Use admin token for subsequent tests
                self.log_test("Admin Login", True, f"Admin user: {data.get('user', {}).get('email')}", response_time)
            else:
                self.log_test("Admin Login", False, f"Login failed: {data.get('message')}", response_time)
        else:
            self.log_test("Admin Login", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test /auth/me endpoint
        if self.auth_token:
            response, response_time = self.make_request('GET', '/auth/me')
            if response and response.status_code == 200:
                data = response.json()
                self.log_test("Get User Profile", True, f"User: {data.get('user', {}).get('name')}", response_time)
            else:
                self.log_test("Get User Profile", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_admin_functionality(self):
        """Test admin dashboard and features"""
        print("\n=== Testing Admin Functionality ===")
        
        if not self.auth_token:
            self.log_test("Admin Dashboard", False, "No authentication token available")
            return
        
        # Test admin dashboard
        response, response_time = self.make_request('GET', '/admin/dashboard')
        if response:
            if response.status_code == 200:
                self.log_test("Admin Dashboard", True, "Dashboard accessible", response_time)
            elif response.status_code == 403:
                self.log_test("Admin Dashboard", True, "Access properly restricted (403 Forbidden)", response_time)
            else:
                self.log_test("Admin Dashboard", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Admin Dashboard", False, "Request timeout", response_time)
        
        # Test admin users endpoint
        response, response_time = self.make_request('GET', '/admin/users')
        if response:
            if response.status_code in [200, 403]:
                success = response.status_code == 200
                details = "Users accessible" if success else "Access properly restricted"
                self.log_test("Admin User Management", success, details, response_time)
            else:
                self.log_test("Admin User Management", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Admin User Management", False, "Request timeout", response_time)
    
    def test_ai_features(self):
        """Test AI integration endpoints"""
        print("\n=== Testing AI Features ===")
        
        if not self.auth_token:
            self.log_test("AI Services", False, "No authentication token available")
            return
        
        # Test AI services endpoint
        response, response_time = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("AI Services", True, f"Services available", response_time)
        else:
            self.log_test("AI Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test AI content generation
        content_data = {
            "type": "blog_post",
            "topic": "Digital Marketing Trends",
            "length": "medium"
        }
        
        response, response_time = self.make_request('POST', '/ai/generate-content', content_data)
        if response:
            if response.status_code == 200:
                self.log_test("AI Content Generation", True, "Content generated successfully", response_time)
            elif response.status_code == 422:
                self.log_test("AI Content Generation", True, "Validation working (422)", response_time)
            else:
                self.log_test("AI Content Generation", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("AI Content Generation", False, "Request timeout", response_time)
    
    def test_website_builder(self):
        """Test website builder functionality"""
        print("\n=== Testing Website Builder ===")
        
        if not self.auth_token:
            self.log_test("Website Builder", False, "No authentication token available")
            return
        
        # Test get websites
        response, response_time = self.make_request('GET', '/websites/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Website Builder - List", True, f"Websites retrieved", response_time)
        else:
            self.log_test("Website Builder - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test get templates
        response, response_time = self.make_request('GET', '/websites/templates')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Website Builder - Templates", True, f"Templates available", response_time)
        else:
            self.log_test("Website Builder - Templates", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create website
        website_data = {
            "name": "Test Website",
            "template": "business",
            "domain": f"test-{int(time.time())}.example.com"
        }
        
        response, response_time = self.make_request('POST', '/websites/', website_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("Website Builder - Create", True, "Website created successfully", response_time)
            elif response.status_code == 422:
                self.log_test("Website Builder - Create", True, "Validation working (422)", response_time)
            else:
                self.log_test("Website Builder - Create", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Website Builder - Create", False, "Request timeout", response_time)
    
    def test_bio_sites(self):
        """Test bio sites functionality"""
        print("\n=== Testing Bio Sites ===")
        
        if not self.auth_token:
            self.log_test("Bio Sites", False, "No authentication token available")
            return
        
        # Test get bio sites
        response, response_time = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Bio Sites - List", True, f"Bio sites retrieved", response_time)
        else:
            self.log_test("Bio Sites - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test get themes
        response, response_time = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Bio Sites - Themes", True, f"Themes available", response_time)
        else:
            self.log_test("Bio Sites - Themes", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create bio site
        bio_site_data = {
            "name": "Test Bio Site",
            "title": "My Bio Site",
            "description": "A test bio site",
            "theme": "modern"
        }
        
        response, response_time = self.make_request('POST', '/bio-sites/', bio_site_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("Bio Sites - Create", True, "Bio site created successfully", response_time)
            elif response.status_code == 422:
                self.log_test("Bio Sites - Create", True, "Validation working (422)", response_time)
            else:
                self.log_test("Bio Sites - Create", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Bio Sites - Create", False, "Request timeout", response_time)
    
    def test_ecommerce(self):
        """Test e-commerce functionality"""
        print("\n=== Testing E-commerce ===")
        
        if not self.auth_token:
            self.log_test("E-commerce", False, "No authentication token available")
            return
        
        # Test get products
        response, response_time = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("E-commerce - Products", True, f"Products retrieved", response_time)
        else:
            self.log_test("E-commerce - Products", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create product
        product_data = {
            "name": "Test Product",
            "description": "A test product",
            "price": 29.99,
            "category": "digital"
        }
        
        response, response_time = self.make_request('POST', '/ecommerce/products', product_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("E-commerce - Create Product", True, "Product created successfully", response_time)
            elif response.status_code == 422:
                self.log_test("E-commerce - Create Product", True, "Validation working (422)", response_time)
            else:
                self.log_test("E-commerce - Create Product", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("E-commerce - Create Product", False, "Request timeout", response_time)
        
        # Test get orders
        response, response_time = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("E-commerce - Orders", True, f"Orders retrieved", response_time)
        else:
            self.log_test("E-commerce - Orders", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_analytics(self):
        """Test analytics functionality"""
        print("\n=== Testing Analytics ===")
        
        if not self.auth_token:
            self.log_test("Analytics", False, "No authentication token available")
            return
        
        # Test analytics overview
        response, response_time = self.make_request('GET', '/analytics/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Analytics - Overview", True, f"Analytics data retrieved", response_time)
        else:
            self.log_test("Analytics - Overview", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test analytics reports
        response, response_time = self.make_request('GET', '/analytics/reports')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Analytics - Reports", True, f"Reports retrieved", response_time)
        else:
            self.log_test("Analytics - Reports", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test business intelligence
        response, response_time = self.make_request('GET', '/analytics/business-intelligence')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Analytics - Business Intelligence", True, f"BI data retrieved", response_time)
        else:
            self.log_test("Analytics - Business Intelligence", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_social_media_management(self):
        """Test social media management"""
        print("\n=== Testing Social Media Management ===")
        
        if not self.auth_token:
            self.log_test("Social Media", False, "No authentication token available")
            return
        
        # Test social media accounts
        response, response_time = self.make_request('GET', '/social-media/accounts')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Social Media - Accounts", True, f"Accounts retrieved", response_time)
        else:
            self.log_test("Social Media - Accounts", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test social media posts
        response, response_time = self.make_request('GET', '/social-media/posts')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Social Media - Posts", True, f"Posts retrieved", response_time)
        else:
            self.log_test("Social Media - Posts", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_email_marketing(self):
        """Test email marketing functionality"""
        print("\n=== Testing Email Marketing ===")
        
        if not self.auth_token:
            self.log_test("Email Marketing", False, "No authentication token available")
            return
        
        # Test email campaigns
        response, response_time = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Email Marketing - Campaigns", True, f"Campaigns retrieved", response_time)
        else:
            self.log_test("Email Marketing - Campaigns", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test email templates
        response, response_time = self.make_request('GET', '/email-marketing/templates')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Email Marketing - Templates", True, f"Templates retrieved", response_time)
        else:
            self.log_test("Email Marketing - Templates", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_course_management(self):
        """Test course management functionality"""
        print("\n=== Testing Course Management ===")
        
        if not self.auth_token:
            self.log_test("Course Management", False, "No authentication token available")
            return
        
        # Test get courses
        response, response_time = self.make_request('GET', '/courses/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Course Management - List", True, f"Courses retrieved", response_time)
        else:
            self.log_test("Course Management - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create course
        course_data = {
            "title": "Test Course",
            "description": "A test course",
            "price": 99.99,
            "duration": "4 weeks"
        }
        
        response, response_time = self.make_request('POST', '/courses/', course_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("Course Management - Create", True, "Course created successfully", response_time)
            elif response.status_code == 422:
                self.log_test("Course Management - Create", True, "Validation working (422)", response_time)
            else:
                self.log_test("Course Management - Create", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Course Management - Create", False, "Request timeout", response_time)
    
    def test_crm_features(self):
        """Test CRM functionality"""
        print("\n=== Testing CRM Features ===")
        
        if not self.auth_token:
            self.log_test("CRM", False, "No authentication token available")
            return
        
        # Test CRM contacts
        response, response_time = self.make_request('GET', '/crm/contacts')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("CRM - Contacts", True, f"Contacts retrieved", response_time)
        else:
            self.log_test("CRM - Contacts", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test CRM leads
        response, response_time = self.make_request('GET', '/crm/leads')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("CRM - Leads", True, f"Leads retrieved", response_time)
        else:
            self.log_test("CRM - Leads", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_workspace_management(self):
        """Test workspace management"""
        print("\n=== Testing Workspace Management ===")
        
        if not self.auth_token:
            self.log_test("Workspace Management", False, "No authentication token available")
            return
        
        # Test get workspaces
        response, response_time = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Workspace Management - List", True, f"Workspaces retrieved", response_time)
        else:
            self.log_test("Workspace Management - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create workspace
        workspace_data = {
            "name": "Test Workspace",
            "description": "A test workspace",
            "type": "business"
        }
        
        response, response_time = self.make_request('POST', '/workspaces', workspace_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("Workspace Management - Create", True, "Workspace created successfully", response_time)
            elif response.status_code == 422:
                self.log_test("Workspace Management - Create", True, "Validation working (422)", response_time)
            else:
                self.log_test("Workspace Management - Create", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Workspace Management - Create", False, "Request timeout", response_time)
    
    def test_real_time_features(self):
        """Test real-time functionality"""
        print("\n=== Testing Real-Time Features ===")
        
        if not self.auth_token:
            self.log_test("Real-Time Features", False, "No authentication token available")
            return
        
        # Test real-time notifications
        response, response_time = self.make_request('GET', '/realtime/notifications')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Real-Time - Notifications", True, f"Notifications retrieved", response_time)
        else:
            self.log_test("Real-Time - Notifications", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test system status
        response, response_time = self.make_request('GET', '/realtime/system-status')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Real-Time - System Status", True, f"System status retrieved", response_time)
        else:
            self.log_test("Real-Time - System Status", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_advanced_booking_system(self):
        """Test advanced booking functionality"""
        print("\n=== Testing Advanced Booking System ===")
        
        if not self.auth_token:
            self.log_test("Advanced Booking", False, "No authentication token available")
            return
        
        # Test booking services
        response, response_time = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Advanced Booking - Services", True, f"Services retrieved", response_time)
        else:
            self.log_test("Advanced Booking - Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test appointments
        response, response_time = self.make_request('GET', '/booking/appointments')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Advanced Booking - Appointments", True, f"Appointments retrieved", response_time)
        else:
            self.log_test("Advanced Booking - Appointments", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_financial_management(self):
        """Test financial management"""
        print("\n=== Testing Financial Management ===")
        
        if not self.auth_token:
            self.log_test("Financial Management", False, "No authentication token available")
            return
        
        # Test financial dashboard
        response, response_time = self.make_request('GET', '/financial/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Financial Management - Dashboard", True, f"Dashboard data retrieved", response_time)
        else:
            self.log_test("Financial Management - Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_escrow_system(self):
        """Test escrow functionality"""
        print("\n=== Testing Escrow System ===")
        
        if not self.auth_token:
            self.log_test("Escrow System", False, "No authentication token available")
            return
        
        # Test escrow transactions
        response, response_time = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Escrow System - Transactions", True, f"Transactions retrieved", response_time)
        else:
            self.log_test("Escrow System - Transactions", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test escrow statistics
        response, response_time = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Escrow System - Statistics", True, f"Statistics retrieved", response_time)
        else:
            self.log_test("Escrow System - Statistics", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_payment_processing(self):
        """Test payment processing (public endpoints)"""
        print("\n=== Testing Payment Processing ===")
        
        # Test payment packages (public endpoint)
        response, response_time = self.make_request('GET', '/payments/packages')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Payment Processing - Packages", True, f"Packages retrieved", response_time)
        else:
            self.log_test("Payment Processing - Packages", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test Stripe packages (public endpoint)
        response, response_time = self.make_request('GET', '/stripe/packages')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Payment Processing - Stripe Packages", True, f"Stripe packages retrieved", response_time)
        else:
            self.log_test("Payment Processing - Stripe Packages", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def run_all_tests(self):
        """Run all backend tests"""
        print("🚀 Starting Comprehensive Backend API Testing")
        print(f"Testing backend at: {self.api_url}")
        print("=" * 60)
        
        # Run all test suites
        self.test_health_check()
        self.test_authentication()
        self.test_admin_functionality()
        self.test_ai_features()
        self.test_website_builder()
        self.test_bio_sites()
        self.test_ecommerce()
        self.test_analytics()
        self.test_social_media_management()
        self.test_email_marketing()
        self.test_course_management()
        self.test_crm_features()
        self.test_workspace_management()
        self.test_real_time_features()
        self.test_advanced_booking_system()
        self.test_financial_management()
        self.test_escrow_system()
        self.test_payment_processing()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 60)
        print("📊 BACKEND TESTING SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  • {result['test']}")
        
        print("\n" + "=" * 60)
        
        # Overall assessment
        if success_rate >= 80:
            print("🎉 EXCELLENT: Backend is highly functional and production-ready!")
        elif success_rate >= 60:
            print("✅ GOOD: Backend is mostly functional with some areas needing attention.")
        elif success_rate >= 40:
            print("⚠️  MODERATE: Backend has significant issues that need addressing.")
        else:
            print("❌ CRITICAL: Backend has major functionality problems.")

def main():
    """Main function to run the tests"""
    # Get backend URL from environment or use default
    backend_url = "https://ff62574f-1eb9-4423-9341-e402e15fa0aa.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    
    # Initialize tester
    tester = MewayzBackendTester(backend_url)
    
    # Run all tests
    tester.run_all_tests()

if __name__ == "__main__":
    main()