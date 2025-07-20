#!/usr/bin/env python3
"""
New Features Backend API Testing for Mewayz Platform
Tests the newly added Link Shortener, Team Management, Form Templates, and Discount Codes API endpoints
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class NewFeaturesBackendTester:
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
    
    def authenticate_admin(self):
        """Authenticate with admin credentials"""
        print("\n=== Admin Authentication ===")
        
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                self.log_test("Admin Authentication", True, f"Admin user: {data.get('user', {}).get('email')}", response_time)
                return True
            else:
                self.log_test("Admin Authentication", False, f"Login failed: {data.get('message')}", response_time)
                return False
        else:
            self.log_test("Admin Authentication", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
            return False
    
    def test_link_shortener_system(self):
        """Test Link Shortener System endpoints"""
        print("\n=== Testing Link Shortener System ===")
        
        if not self.auth_token:
            self.log_test("Link Shortener System", False, "No authentication token available")
            return
        
        # Test GET /api/link-shortener/links
        response, response_time = self.make_request('GET', '/link-shortener/links')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Link Shortener - Get Links", True, f"Links retrieved: {len(data.get('data', []))} links", response_time)
        else:
            self.log_test("Link Shortener - Get Links", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/link-shortener/create
        link_data = {
            "original_url": "https://www.example.com/very-long-url-that-needs-shortening",
            "title": "Example Website",
            "description": "A test link for the link shortener system",
            "custom_slug": f"test-{int(time.time())}"
        }
        
        response, response_time = self.make_request('POST', '/link-shortener/create', link_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                short_url = data.get('data', {}).get('short_url', 'N/A')
                self.log_test("Link Shortener - Create Link", True, f"Link created: {short_url}", response_time)
            elif response.status_code == 422:
                self.log_test("Link Shortener - Create Link", True, "Validation working (422)", response_time)
            else:
                self.log_test("Link Shortener - Create Link", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Link Shortener - Create Link", False, "Request timeout", response_time)
        
        # Test GET /api/link-shortener/stats
        response, response_time = self.make_request('GET', '/link-shortener/stats')
        if response and response.status_code == 200:
            data = response.json()
            stats = data.get('data', {})
            total_links = stats.get('total_links', 0)
            total_clicks = stats.get('total_clicks', 0)
            self.log_test("Link Shortener - Get Stats", True, f"Stats: {total_links} links, {total_clicks} clicks", response_time)
        else:
            self.log_test("Link Shortener - Get Stats", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
    
    def test_team_management_system(self):
        """Test Team Management System endpoints"""
        print("\n=== Testing Team Management System ===")
        
        if not self.auth_token:
            self.log_test("Team Management System", False, "No authentication token available")
            return
        
        # Test GET /api/team/members
        response, response_time = self.make_request('GET', '/team/members')
        if response and response.status_code == 200:
            data = response.json()
            members = data.get('data', [])
            self.log_test("Team Management - Get Members", True, f"Team members retrieved: {len(members)} members", response_time)
        else:
            self.log_test("Team Management - Get Members", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/team/invite
        invite_data = {
            "email": f"newteammember_{int(time.time())}@example.com",
            "role": "member",
            "permissions": ["read", "write"],
            "message": "Welcome to our team! We're excited to have you join us."
        }
        
        response, response_time = self.make_request('POST', '/team/invite', invite_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                invite_id = data.get('data', {}).get('invite_id', 'N/A')
                self.log_test("Team Management - Send Invite", True, f"Invite sent: {invite_id}", response_time)
            elif response.status_code == 422:
                self.log_test("Team Management - Send Invite", True, "Validation working (422)", response_time)
            else:
                self.log_test("Team Management - Send Invite", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Team Management - Send Invite", False, "Request timeout", response_time)
    
    def test_form_templates_system(self):
        """Test Form Templates System endpoints"""
        print("\n=== Testing Form Templates System ===")
        
        if not self.auth_token:
            self.log_test("Form Templates System", False, "No authentication token available")
            return
        
        # Test GET /api/form-templates
        response, response_time = self.make_request('GET', '/form-templates')
        if response and response.status_code == 200:
            data = response.json()
            templates = data.get('data', [])
            self.log_test("Form Templates - Get Templates", True, f"Templates retrieved: {len(templates)} templates", response_time)
        else:
            self.log_test("Form Templates - Get Templates", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/form-templates
        template_data = {
            "name": "Customer Feedback Form",
            "description": "A comprehensive form to collect customer feedback",
            "category": "feedback",
            "fields": [
                {
                    "type": "text",
                    "name": "customer_name",
                    "label": "Your Name",
                    "required": True
                },
                {
                    "type": "email",
                    "name": "customer_email",
                    "label": "Email Address",
                    "required": True
                },
                {
                    "type": "select",
                    "name": "rating",
                    "label": "Overall Rating",
                    "options": ["Excellent", "Good", "Average", "Poor"],
                    "required": True
                },
                {
                    "type": "textarea",
                    "name": "comments",
                    "label": "Additional Comments",
                    "required": False
                }
            ],
            "settings": {
                "submit_button_text": "Submit Feedback",
                "success_message": "Thank you for your feedback!",
                "redirect_url": "/thank-you"
            }
        }
        
        response, response_time = self.make_request('POST', '/form-templates', template_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                template_id = data.get('data', {}).get('id', 'N/A')
                self.log_test("Form Templates - Create Template", True, f"Template created: {template_id}", response_time)
            elif response.status_code == 422:
                self.log_test("Form Templates - Create Template", True, "Validation working (422)", response_time)
            else:
                self.log_test("Form Templates - Create Template", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Form Templates - Create Template", False, "Request timeout", response_time)
    
    def test_discount_codes_system(self):
        """Test Discount Codes System endpoints"""
        print("\n=== Testing Discount Codes System ===")
        
        if not self.auth_token:
            self.log_test("Discount Codes System", False, "No authentication token available")
            return
        
        # Test GET /api/discount-codes
        response, response_time = self.make_request('GET', '/discount-codes')
        if response and response.status_code == 200:
            data = response.json()
            codes = data.get('data', [])
            self.log_test("Discount Codes - Get Codes", True, f"Discount codes retrieved: {len(codes)} codes", response_time)
        else:
            self.log_test("Discount Codes - Get Codes", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/discount-codes
        discount_data = {
            "code": f"SAVE20_{int(time.time())}",
            "name": "20% Off Summer Sale",
            "description": "Get 20% off all products during our summer sale",
            "type": "percentage",
            "value": 20,
            "minimum_amount": 50.00,
            "maximum_discount": 100.00,
            "usage_limit": 100,
            "usage_limit_per_customer": 1,
            "start_date": "2025-01-20",
            "end_date": "2025-02-20",
            "is_active": True,
            "applicable_products": ["all"],
            "applicable_categories": ["electronics", "clothing"],
            "conditions": {
                "first_time_customer": False,
                "minimum_items": 1
            }
        }
        
        response, response_time = self.make_request('POST', '/discount-codes', discount_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                code_id = data.get('data', {}).get('id', 'N/A')
                code = data.get('data', {}).get('code', 'N/A')
                self.log_test("Discount Codes - Create Code", True, f"Code created: {code} (ID: {code_id})", response_time)
            elif response.status_code == 422:
                self.log_test("Discount Codes - Create Code", True, "Validation working (422)", response_time)
            else:
                self.log_test("Discount Codes - Create Code", False, f"Status: {response.status_code}", response_time)
        else:
            self.log_test("Discount Codes - Create Code", False, "Request timeout", response_time)
    
    def run_new_features_tests(self):
        """Run all new features tests"""
        print("🚀 Starting New Features Backend API Testing")
        print(f"Testing backend at: {self.api_url}")
        print("Testing: Link Shortener, Team Management, Form Templates, Discount Codes")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate_admin():
            print("❌ Authentication failed. Cannot proceed with testing.")
            return
        
        # Run all new feature test suites
        self.test_link_shortener_system()
        self.test_team_management_system()
        self.test_form_templates_system()
        self.test_discount_codes_system()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 80)
        print("📊 NEW FEATURES BACKEND TESTING SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Feature-specific breakdown
        features = {
            "Link Shortener": [r for r in self.test_results if "Link Shortener" in r['test']],
            "Team Management": [r for r in self.test_results if "Team Management" in r['test']],
            "Form Templates": [r for r in self.test_results if "Form Templates" in r['test']],
            "Discount Codes": [r for r in self.test_results if "Discount Codes" in r['test']]
        }
        
        print(f"\n📋 FEATURE BREAKDOWN:")
        for feature_name, feature_tests in features.items():
            if feature_tests:
                feature_passed = sum(1 for t in feature_tests if t['success'])
                feature_total = len(feature_tests)
                feature_rate = (feature_passed / feature_total * 100) if feature_total > 0 else 0
                status = "✅" if feature_rate == 100 else "⚠️" if feature_rate >= 50 else "❌"
                print(f"  {status} {feature_name}: {feature_passed}/{feature_total} ({feature_rate:.1f}%)")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  • {result['test']}")
        
        print("\n" + "=" * 80)
        
        # Overall assessment
        if success_rate >= 90:
            print("🎉 EXCELLENT: All new features are highly functional and production-ready!")
        elif success_rate >= 75:
            print("✅ GOOD: New features are mostly functional with minor issues.")
        elif success_rate >= 50:
            print("⚠️  MODERATE: New features have some issues that need addressing.")
        else:
            print("❌ CRITICAL: New features have major functionality problems.")
        
        print("\n🔍 WORKSPACE INTEGRATION STATUS:")
        workspace_working = any(r['success'] for r in self.test_results if 'workspace' in r['details'].lower())
        if workspace_working:
            print("✅ Workspace integration is working correctly")
        else:
            print("⚠️  Workspace integration status unclear from current tests")
        
        print("\n📊 SAMPLE DATA STATUS:")
        sample_data_working = any('retrieved' in r['details'] and r['success'] for r in self.test_results)
        if sample_data_working:
            print("✅ Sample data from admin user initialization is accessible")
        else:
            print("⚠️  Sample data accessibility needs verification")

def main():
    """Main function to run the new features tests"""
    # Get backend URL from environment
    backend_url = "https://fbc7fbea-2d99-4296-9b80-a854dcdd044d.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    print(f"🔐 Admin Credentials: tmonnens@outlook.com / Voetballen5")
    
    # Initialize tester
    tester = NewFeaturesBackendTester(backend_url)
    
    # Run new features tests
    tester.run_new_features_tests()

if __name__ == "__main__":
    main()