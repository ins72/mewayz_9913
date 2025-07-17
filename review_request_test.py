#!/usr/bin/env python3
"""
Review Request Testing for Mewayz Creator Economy Platform
Testing authentication fixes and new admin dashboard functionality
"""

import requests
import json
import sys
import time
from datetime import datetime

class ReviewRequestTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use the existing token from previous tests
        self.auth_token = "1|2mIDz7pbpVkMV6iDfPIZnKdee9T2s9sw6vN4J5Dmedb70bc9"
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

    def test_authentication_fixes(self):
        """Test that the Auth::user() to $request->user() fixes are working properly"""
        print("\n🔐 === Testing Authentication Fixes (Auth::user() to $request->user()) ===")
        
        # Test WorkspaceController
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            self.log_test("WorkspaceController Fix", True, "GET /workspaces working correctly after Auth::user() fix")
        else:
            self.log_test("WorkspaceController Fix", False, f"GET /workspaces failed - Status: {response.status_code if response else 'No response'}")
        
        # Test SocialMediaController
        response = self.make_request('GET', '/social-media/accounts')
        if response and response.status_code == 200:
            self.log_test("SocialMediaController Fix", True, "GET /social-media/accounts working correctly after Auth::user() fix")
        else:
            self.log_test("SocialMediaController Fix", False, f"GET /social-media/accounts failed - Status: {response.status_code if response else 'No response'}")
        
        # Test CrmController
        response = self.make_request('GET', '/crm/contacts')
        if response and response.status_code == 200:
            self.log_test("CrmController Fix", True, "GET /crm/contacts working correctly after Auth::user() fix")
        else:
            self.log_test("CrmController Fix", False, f"GET /crm/contacts failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Analytics Controller
        response = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            self.log_test("AnalyticsController Fix", True, "GET /analytics/overview working correctly after Auth::user() fix")
        else:
            self.log_test("AnalyticsController Fix", False, f"GET /analytics/overview failed - Status: {response.status_code if response else 'No response'}")
        
        # Test Social Media Analytics
        response = self.make_request('GET', '/social-media/analytics')
        if response and response.status_code == 200:
            self.log_test("Social Media Analytics Fix", True, "GET /social-media/analytics working correctly after Auth::user() fix")
        else:
            self.log_test("Social Media Analytics Fix", False, f"GET /social-media/analytics failed - Status: {response.status_code if response else 'No response'}")

    def test_admin_dashboard(self):
        """Test the new admin dashboard endpoints"""
        print("\n🛠️ === Testing Admin Dashboard Functionality ===")
        
        # Test GET /admin/api-keys - Get API keys management
        response = self.make_request('GET', '/admin/api-keys')
        if response and response.status_code == 200:
            self.log_test("Admin API Keys Management", True, "GET /admin/api-keys working correctly")
        else:
            self.log_test("Admin API Keys Management", False, f"GET /admin/api-keys failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /admin/api-keys - Save API key
        api_key_data = {
            "name": "Test API Key",
            "service": "openai",
            "key": "sk-test-key-12345",
            "description": "Test API key for OpenAI service"
        }
        response = self.make_request('POST', '/admin/api-keys', api_key_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Save API Key", True, "POST /admin/api-keys working correctly")
        else:
            self.log_test("Save API Key", False, f"POST /admin/api-keys failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /admin/api-keys/test - Test API key
        test_key_data = {
            "service": "openai",
            "key": "sk-test-key-12345"
        }
        response = self.make_request('POST', '/admin/api-keys/test', test_key_data)
        if response and response.status_code == 200:
            self.log_test("Test API Key", True, "POST /admin/api-keys/test working correctly")
        else:
            self.log_test("Test API Key", False, f"POST /admin/api-keys/test failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /admin/subscription-plans - Get subscription plans
        response = self.make_request('GET', '/admin/subscription-plans')
        if response and response.status_code == 200:
            self.log_test("Admin Subscription Plans", True, "GET /admin/subscription-plans working correctly")
        else:
            self.log_test("Admin Subscription Plans", False, f"GET /admin/subscription-plans failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /admin/subscription-plans - Save subscription plan
        plan_data = {
            "name": "Test Pro Plan",
            "price": 29.99,
            "currency": "USD",
            "features": ["unlimited_bio_sites", "advanced_analytics", "priority_support"],
            "billing_cycle": "monthly"
        }
        response = self.make_request('POST', '/admin/subscription-plans', plan_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Save Subscription Plan", True, "POST /admin/subscription-plans working correctly")
        else:
            self.log_test("Save Subscription Plan", False, f"POST /admin/subscription-plans failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /admin/settings - Get system settings
        response = self.make_request('GET', '/admin/settings')
        if response and response.status_code == 200:
            self.log_test("Admin System Settings", True, "GET /admin/settings working correctly")
        else:
            self.log_test("Admin System Settings", False, f"GET /admin/settings failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /admin/settings - Update system settings
        settings_data = {
            "site_name": "Mewayz Creator Platform",
            "maintenance_mode": False,
            "max_bio_sites_per_user": 10,
            "default_theme": "modern"
        }
        response = self.make_request('POST', '/admin/settings', settings_data)
        if response and response.status_code == 200:
            self.log_test("Update System Settings", True, "POST /admin/settings working correctly")
        else:
            self.log_test("Update System Settings", False, f"POST /admin/settings failed - Status: {response.status_code if response else 'No response'}")

    def test_instagram_database(self):
        """Test the new Instagram Database endpoints"""
        print("\n📸 === Testing Instagram Database Functionality ===")
        
        # Test GET /instagram-database/search - Search profiles with filters
        search_params = {
            "query": "fitness",
            "followers_min": 1000,
            "followers_max": 100000,
            "engagement_rate_min": 2.0
        }
        response = self.make_request('GET', '/instagram-database/search', search_params)
        if response and response.status_code == 200:
            self.log_test("Instagram Database Search", True, "GET /instagram-database/search working correctly")
        else:
            self.log_test("Instagram Database Search", False, f"GET /instagram-database/search failed - Status: {response.status_code if response else 'No response'}")
        
        # Test POST /instagram-database/scrape - Scrape profile data
        scrape_data = {
            "username": "test_fitness_account",
            "include_posts": True,
            "include_stories": False,
            "max_posts": 50
        }
        response = self.make_request('POST', '/instagram-database/scrape', scrape_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Instagram Profile Scraping", True, "POST /instagram-database/scrape working correctly")
        else:
            self.log_test("Instagram Profile Scraping", False, f"POST /instagram-database/scrape failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /instagram-database/analytics - Get analytics
        response = self.make_request('GET', '/instagram-database/analytics')
        if response and response.status_code == 200:
            self.log_test("Instagram Database Analytics", True, "GET /instagram-database/analytics working correctly")
        else:
            self.log_test("Instagram Database Analytics", False, f"GET /instagram-database/analytics failed - Status: {response.status_code if response else 'No response'}")

    def test_critical_endpoints(self):
        """Test the key endpoints that were failing before"""
        print("\n🎯 === Testing Critical Endpoints ===")
        
        # Test GET /workspaces - Workspaces listing
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Workspaces Listing", True, f"GET /workspaces working - Found {len(data.get('data', []))} workspaces")
        else:
            self.log_test("Workspaces Listing", False, f"GET /workspaces failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /crm/contacts - CRM contacts
        response = self.make_request('GET', '/crm/contacts')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("CRM Contacts", True, f"GET /crm/contacts working - Found {len(data.get('data', []))} contacts")
        else:
            self.log_test("CRM Contacts", False, f"GET /crm/contacts failed - Status: {response.status_code if response else 'No response'}")
        
        # Test GET /social-media/accounts - Social media accounts
        response = self.make_request('GET', '/social-media/accounts')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Social Media Accounts", True, f"GET /social-media/accounts working - Found {len(data.get('data', []))} accounts")
        else:
            self.log_test("Social Media Accounts", False, f"GET /social-media/accounts failed - Status: {response.status_code if response else 'No response'}")
        
        # Test authentication endpoint
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            user_name = data.get('user', {}).get('name', 'Unknown')
            self.log_test("Authentication Endpoint", True, f"GET /auth/me working - User: {user_name}")
        else:
            self.log_test("Authentication Endpoint", False, f"GET /auth/me failed - Status: {response.status_code if response else 'No response'}")

    def run_all_tests(self):
        """Run all review request tests"""
        print("🚀 Starting Review Request Testing for Mewayz Creator Economy Platform")
        print("=" * 80)
        
        # Test authentication fixes
        self.test_authentication_fixes()
        
        # Test admin dashboard
        self.test_admin_dashboard()
        
        # Test Instagram database
        self.test_instagram_database()
        
        # Test critical endpoints
        self.test_critical_endpoints()
        
        # Print summary
        self.print_summary()

    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("📊 REVIEW REQUEST TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("\n" + "=" * 80)

if __name__ == "__main__":
    tester = ReviewRequestTester()
    tester.run_all_tests()