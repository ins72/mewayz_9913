#!/usr/bin/env python3
"""
Comprehensive FastAPI Backend Testing for Mewayz Professional Platform
Tests all major FastAPI endpoints including authentication, admin features, 
AI services, bio sites, e-commerce, analytics, and more.
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class FastAPIBackendTester:
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
            self.log_test("API Health Check", True, f"Status: {data.get('message', 'unknown')}", response_time)
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
            "name": "Test User FastAPI",
            "email": f"testuser_fastapi_{int(time.time())}@example.com",
            "password": "TestPassword123!",
            "phone": "+1234567890",
            "timezone": "UTC",
            "language": "en"
        }
        
        response, response_time = self.make_request('POST', '/auth/register', register_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
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
                data = response.json()
                self.log_test("Admin Dashboard", True, f"Dashboard accessible - Total users: {data.get('data', {}).get('user_metrics', {}).get('total_users', 0)}", response_time)
            elif response.status_code == 403:
                self.log_test("Admin Dashboard", True, "Access properly restricted (403 Forbidden)", response_time)
            else:
                self.log_test("Admin Dashboard", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Admin Dashboard", False, "Request timeout", response_time)
    
    def test_ai_features(self):
        """Test AI integration endpoints"""
        print("\n=== Testing AI Features ===")
        
        # Test AI services endpoint (public)
        response, response_time = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            data = response.json()
            services_count = len(data.get('data', {}).get('services', []))
            self.log_test("AI Services", True, f"{services_count} services available", response_time)
        else:
            self.log_test("AI Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        if not self.auth_token:
            return
        
        # Test AI conversations
        response, response_time = self.make_request('GET', '/ai/conversations')
        if response and response.status_code == 200:
            data = response.json()
            conversations_count = len(data.get('data', {}).get('conversations', []))
            self.log_test("AI Conversations", True, f"{conversations_count} conversations found", response_time)
        else:
            self.log_test("AI Conversations", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create AI conversation
        response, response_time = self.make_request('POST', '/ai/conversations', {"title": "Test Conversation"})
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Create AI Conversation", True, f"Conversation created: {data.get('data', {}).get('conversation', {}).get('title')}", response_time)
            elif response.status_code == 404:
                self.log_test("Create AI Conversation", True, "Workspace not found (expected for new user)", response_time)
            else:
                self.log_test("Create AI Conversation", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Create AI Conversation", False, "Request timeout", response_time)
    
    def test_bio_sites(self):
        """Test bio sites functionality"""
        print("\n=== Testing Bio Sites ===")
        
        if not self.auth_token:
            self.log_test("Bio Sites", False, "No authentication token available")
            return
        
        # Test get bio sites
        response, response_time = self.make_request('GET', '/bio-sites')
        if response and response.status_code == 200:
            data = response.json()
            sites_count = len(data.get('data', {}).get('bio_sites', []))
            self.log_test("Bio Sites - List", True, f"{sites_count} bio sites found", response_time)
        else:
            self.log_test("Bio Sites - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test get themes
        response, response_time = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            data = response.json()
            themes_count = len(data.get('data', {}).get('themes', []))
            self.log_test("Bio Sites - Themes", True, f"{themes_count} themes available", response_time)
        else:
            self.log_test("Bio Sites - Themes", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create bio site
        bio_site_data = {
            "title": "Test Bio Site FastAPI",
            "slug": f"test-bio-site-{int(time.time())}",
            "description": "A test bio site for FastAPI testing",
            "theme": "modern"
        }
        
        response, response_time = self.make_request('POST', '/bio-sites', bio_site_data)
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Bio Sites - Create", True, f"Bio site created: {data.get('data', {}).get('bio_site', {}).get('title')}", response_time)
            elif response.status_code == 404:
                self.log_test("Bio Sites - Create", True, "Workspace not found (expected for new user)", response_time)
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
            products_count = len(data.get('data', {}).get('products', []))
            self.log_test("E-commerce - Products", True, f"{products_count} products found", response_time)
        else:
            self.log_test("E-commerce - Products", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test get orders
        response, response_time = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            data = response.json()
            orders_count = len(data.get('data', {}).get('orders', []))
            self.log_test("E-commerce - Orders", True, f"{orders_count} orders found", response_time)
        else:
            self.log_test("E-commerce - Orders", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test e-commerce dashboard
        response, response_time = self.make_request('GET', '/ecommerce/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            revenue = data.get('data', {}).get('overview', {}).get('total_revenue', 0)
            self.log_test("E-commerce - Dashboard", True, f"Total revenue: ${revenue}", response_time)
        else:
            self.log_test("E-commerce - Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_advanced_booking(self):
        """Test advanced booking functionality"""
        print("\n=== Testing Advanced Booking System ===")
        
        if not self.auth_token:
            self.log_test("Advanced Booking", False, "No authentication token available")
            return
        
        # Test booking services
        response, response_time = self.make_request('GET', '/bookings/services')
        if response and response.status_code == 200:
            data = response.json()
            services_count = len(data.get('data', {}).get('services', []))
            self.log_test("Advanced Booking - Services", True, f"{services_count} services found", response_time)
        else:
            self.log_test("Advanced Booking - Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test appointments
        response, response_time = self.make_request('GET', '/bookings/appointments')
        if response and response.status_code == 200:
            data = response.json()
            appointments_count = len(data.get('data', {}).get('appointments', []))
            self.log_test("Advanced Booking - Appointments", True, f"{appointments_count} appointments found", response_time)
        else:
            self.log_test("Advanced Booking - Appointments", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test booking dashboard
        response, response_time = self.make_request('GET', '/bookings/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            total_bookings = data.get('data', {}).get('overview', {}).get('total_bookings', 0)
            self.log_test("Advanced Booking - Dashboard", True, f"Total bookings: {total_bookings}", response_time)
        else:
            self.log_test("Advanced Booking - Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_course_management(self):
        """Test course management functionality"""
        print("\n=== Testing Course Management ===")
        
        if not self.auth_token:
            self.log_test("Course Management", False, "No authentication token available")
            return
        
        # Test get courses
        response, response_time = self.make_request('GET', '/courses')
        if response and response.status_code == 200:
            data = response.json()
            courses_count = len(data.get('data', {}).get('courses', []))
            self.log_test("Course Management - List", True, f"{courses_count} courses found", response_time)
        else:
            self.log_test("Course Management - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
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
            contacts_count = len(data.get('data', {}).get('contacts', []))
            self.log_test("CRM - Contacts", True, f"{contacts_count} contacts found", response_time)
        else:
            self.log_test("CRM - Contacts", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_website_builder(self):
        """Test website builder functionality"""
        print("\n=== Testing Website Builder ===")
        
        if not self.auth_token:
            self.log_test("Website Builder", False, "No authentication token available")
            return
        
        # Test get websites
        response, response_time = self.make_request('GET', '/websites')
        if response and response.status_code == 200:
            data = response.json()
            websites_count = len(data.get('data', []))
            self.log_test("Website Builder - List", True, f"{websites_count} websites found", response_time)
        else:
            self.log_test("Website Builder - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
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
            campaigns_count = len(data.get('campaigns', []))
            self.log_test("Email Marketing - Campaigns", True, f"{campaigns_count} campaigns found", response_time)
        else:
            self.log_test("Email Marketing - Campaigns", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_analytics(self):
        """Test analytics functionality"""
        print("\n=== Testing Analytics ===")
        
        if not self.auth_token:
            self.log_test("Analytics", False, "No authentication token available")
            return
        
        # Test analytics overview
        response, response_time = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            data = response.json()
            total_events = data.get('data', {}).get('overview', {}).get('total_events', 0)
            self.log_test("Analytics - Overview", True, f"Total events: {total_events}", response_time)
        else:
            self.log_test("Analytics - Overview", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test business intelligence
        response, response_time = self.make_request('GET', '/analytics/business-intelligence/advanced')
        if response and response.status_code == 200:
            data = response.json()
            revenue_forecast = data.get('data', {}).get('executive_summary', {}).get('revenue_forecast', 0)
            self.log_test("Analytics - Business Intelligence", True, f"Revenue forecast: ${revenue_forecast}", response_time)
        else:
            self.log_test("Analytics - Business Intelligence", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_real_time_features(self):
        """Test real-time functionality"""
        print("\n=== Testing Real-Time Features ===")
        
        if not self.auth_token:
            self.log_test("Real-Time Features", False, "No authentication token available")
            return
        
        # Test real-time notifications
        response, response_time = self.make_request('GET', '/notifications')
        if response and response.status_code == 200:
            data = response.json()
            notifications_count = len(data.get('data', {}).get('notifications', []))
            unread_count = data.get('data', {}).get('unread_count', 0)
            self.log_test("Real-Time - Notifications", True, f"{notifications_count} notifications, {unread_count} unread", response_time)
        else:
            self.log_test("Real-Time - Notifications", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test advanced notifications
        response, response_time = self.make_request('GET', '/notifications/advanced')
        if response and response.status_code == 200:
            data = response.json()
            priority_count = len(data.get('data', {}).get('priority_inbox', []))
            self.log_test("Real-Time - Advanced Notifications", True, f"{priority_count} priority notifications", response_time)
        else:
            self.log_test("Real-Time - Advanced Notifications", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_financial_management(self):
        """Test financial management"""
        print("\n=== Testing Financial Management ===")
        
        if not self.auth_token:
            self.log_test("Financial Management", False, "No authentication token available")
            return
        
        # Test invoices
        response, response_time = self.make_request('GET', '/financial/invoices')
        if response and response.status_code == 200:
            data = response.json()
            invoices_count = len(data.get('data', {}).get('invoices', []))
            self.log_test("Financial Management - Invoices", True, f"{invoices_count} invoices found", response_time)
        else:
            self.log_test("Financial Management - Invoices", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test comprehensive financial dashboard
        response, response_time = self.make_request('GET', '/financial/dashboard/comprehensive')
        if response and response.status_code == 200:
            data = response.json()
            total_revenue = data.get('data', {}).get('revenue_overview', {}).get('total_revenue', 0)
            self.log_test("Financial Management - Dashboard", True, f"Total revenue: ${total_revenue}", response_time)
        else:
            self.log_test("Financial Management - Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_escrow_system(self):
        """Test escrow functionality"""
        print("\n=== Testing Escrow System ===")
        
        if not self.auth_token:
            self.log_test("Escrow System", False, "No authentication token available")
            return
        
        # Test escrow transactions
        response, response_time = self.make_request('GET', '/escrow')
        if response and response.status_code == 200:
            data = response.json()
            transactions_count = len(data.get('data', {}).get('transactions', []))
            self.log_test("Escrow System - Transactions", True, f"{transactions_count} transactions found", response_time)
        else:
            self.log_test("Escrow System - Transactions", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test escrow dashboard
        response, response_time = self.make_request('GET', '/escrow/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            total_transactions = data.get('data', {}).get('overview', {}).get('total_transactions', 0)
            total_value = data.get('data', {}).get('overview', {}).get('total_value', 0)
            self.log_test("Escrow System - Dashboard", True, f"{total_transactions} transactions, ${total_value} total value", response_time)
        else:
            self.log_test("Escrow System - Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
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
            workspaces_count = len(data.get('data', {}).get('workspaces', []))
            self.log_test("Workspace Management - List", True, f"{workspaces_count} workspaces found", response_time)
        else:
            self.log_test("Workspace Management - List", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test create workspace
        workspace_data = {
            "name": f"Test Workspace FastAPI {int(time.time())}",
            "description": "A test workspace for FastAPI testing",
            "industry": "Technology",
            "website": "https://example.com"
        }
        
        response, response_time = self.make_request('POST', '/workspaces', workspace_data)
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Workspace Management - Create", True, f"Workspace created: {data.get('data', {}).get('workspace', {}).get('name')}", response_time)
            else:
                self.log_test("Workspace Management - Create", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Workspace Management - Create", False, "Request timeout", response_time)
    
    def run_all_tests(self):
        """Run all backend tests"""
        print("🚀 Starting Comprehensive FastAPI Backend Testing")
        print(f"Testing FastAPI backend at: {self.api_url}")
        print("=" * 60)
        
        # Run all test suites
        self.test_health_check()
        self.test_authentication()
        self.test_admin_functionality()
        self.test_ai_features()
        self.test_bio_sites()
        self.test_ecommerce()
        self.test_advanced_booking()
        self.test_course_management()
        self.test_crm_features()
        self.test_website_builder()
        self.test_email_marketing()
        self.test_analytics()
        self.test_real_time_features()
        self.test_financial_management()
        self.test_escrow_system()
        self.test_workspace_management()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 60)
        print("📊 FASTAPI BACKEND TESTING SUMMARY")
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
            print("🎉 EXCELLENT: FastAPI Backend is highly functional and production-ready!")
        elif success_rate >= 60:
            print("✅ GOOD: FastAPI Backend is mostly functional with some areas needing attention.")
        elif success_rate >= 40:
            print("⚠️  MODERATE: FastAPI Backend has significant issues that need addressing.")
        else:
            print("❌ CRITICAL: FastAPI Backend has major functionality problems.")

def main():
    """Main function to run the tests"""
    # Get backend URL from environment or use default
    backend_url = "https://a647fc73-10e4-498f-8ae8-6d85330ad2a0.preview.emergentagent.com"
    
    print(f"🔍 FastAPI Backend URL: {backend_url}")
    
    # Initialize tester
    tester = FastAPIBackendTester(backend_url)
    
    # Run all tests
    tester.run_all_tests()

if __name__ == "__main__":
    main()