#!/usr/bin/env python3
"""
Focused Testing for New Critical Features from Review Request
- Link Shortener System
- Referral System  
- Template Marketplace
"""

import requests
import json
import sys
import time
from datetime import datetime

class NewSystemsTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Fresh token from registration
        self.auth_token = "19|wHpIy5SmB7nvZAeA8gkNsLvWNn0Ew8QP4qufeJ0w5ba2f222"
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

    def test_link_shortener_system(self):
        """Test Link Shortener System functionality"""
        print("\n=== Testing Link Shortener System ===")
        
        # Test get all links
        response = self.make_request('GET', '/links')
        if response and response.status_code == 200:
            self.log_test("Get All Links", True, "Links retrieval successful")
        else:
            self.log_test("Get All Links", False, f"Links retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test create shortened link (need workspace_id)
        # First, let's try to get workspaces to find a valid workspace_id
        workspace_response = self.make_request('GET', '/workspaces')
        workspace_id = None
        if workspace_response and workspace_response.status_code == 200:
            workspaces_data = workspace_response.json()
            if workspaces_data.get('data') and len(workspaces_data['data']) > 0:
                workspace_id = workspaces_data['data'][0]['id']
        
        if not workspace_id:
            # Create a simple link without workspace_id first
            link_data = {
                "original_url": "https://www.example.com/very-long-url-that-needs-shortening",
                "title": "Test Link",
                "description": "This is a test shortened link"
            }
        else:
            link_data = {
                "original_url": "https://www.example.com/very-long-url-that-needs-shortening",
                "workspace_id": workspace_id,
                "title": "Test Link",
                "description": "This is a test shortened link"
            }
        
        response = self.make_request('POST', '/links', link_data)
        link_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Shortened Link", True, "Link creation successful")
            link_id = data.get('data', {}).get('id') or data.get('id')
        else:
            self.log_test("Create Shortened Link", False, f"Link creation failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get link analytics (if link created successfully)
        if link_id:
            response = self.make_request('GET', f'/links/{link_id}/analytics')
            if response and response.status_code == 200:
                self.log_test("Get Link Analytics", True, "Link analytics retrieval successful")
            else:
                self.log_test("Get Link Analytics", False, f"Link analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test bulk analytics
        response = self.make_request('GET', '/links/bulk-analytics')
        if response and response.status_code == 200:
            self.log_test("Get Bulk Analytics", True, "Bulk analytics retrieval successful")
        else:
            self.log_test("Get Bulk Analytics", False, f"Bulk analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")

    def test_referral_system(self):
        """Test Referral System functionality"""
        print("\n=== Testing Referral System ===")
        
        # Test get referral dashboard
        response = self.make_request('GET', '/referrals/dashboard')
        if response and response.status_code == 200:
            self.log_test("Get Referral Dashboard", True, "Referral dashboard retrieval successful")
        else:
            self.log_test("Get Referral Dashboard", False, f"Referral dashboard retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get referral analytics
        response = self.make_request('GET', '/referrals/analytics')
        if response and response.status_code == 200:
            self.log_test("Get Referral Analytics", True, "Referral analytics retrieval successful")
        else:
            self.log_test("Get Referral Analytics", False, f"Referral analytics retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get referral rewards
        response = self.make_request('GET', '/referrals/rewards')
        if response and response.status_code == 200:
            self.log_test("Get Referral Rewards", True, "Referral rewards retrieval successful")
        else:
            self.log_test("Get Referral Rewards", False, f"Referral rewards retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")

    def test_template_marketplace(self):
        """Test Template Marketplace functionality"""
        print("\n=== Testing Template Marketplace ===")
        
        # Test get all templates
        response = self.make_request('GET', '/templates')
        if response and response.status_code == 200:
            self.log_test("Get All Templates", True, "Templates retrieval successful")
        else:
            self.log_test("Get All Templates", False, f"Templates retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get template categories
        response = self.make_request('GET', '/templates/categories')
        if response and response.status_code == 200:
            self.log_test("Get Template Categories", True, "Template categories retrieval successful")
        else:
            self.log_test("Get Template Categories", False, f"Template categories retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get featured templates
        response = self.make_request('GET', '/templates/featured')
        if response and response.status_code == 200:
            self.log_test("Get Featured Templates", True, "Featured templates retrieval successful")
        else:
            self.log_test("Get Featured Templates", False, f"Featured templates retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get my templates
        response = self.make_request('GET', '/templates/my-templates')
        if response and response.status_code == 200:
            self.log_test("Get My Templates", True, "My templates retrieval successful")
        else:
            self.log_test("Get My Templates", False, f"My templates retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")
        
        # Test get my purchases
        response = self.make_request('GET', '/templates/my-purchases')
        if response and response.status_code == 200:
            self.log_test("Get My Purchases", True, "My purchases retrieval successful")
        else:
            self.log_test("Get My Purchases", False, f"My purchases retrieval failed - Status: {response.status_code if response else 'No response'}")
            if response:
                print(f"Response: {response.text}")

    def run_tests(self):
        """Run all tests for the three new systems"""
        print("🎯 Testing New Critical Features from Review Request")
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
    # Initialize tester
    tester = NewSystemsTester()
    
    # Run all tests
    tester.run_tests()
    
    # Exit with appropriate code
    failed_count = sum(1 for result in tester.test_results.values() if not result['success'])
    sys.exit(1 if failed_count > 0 else 0)