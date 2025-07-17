#!/usr/bin/env python3
"""
Focused Testing for New Advanced Features in Mewayz Platform
"""

import requests
import json
import sys
import time
from datetime import datetime

class AdvancedFeaturesTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Fresh token from registration
        self.auth_token = "3|a8NQOqIT8AWMNS2TqOYGvtWf2Z06s8MgsuOqjNvNe9951d3a"
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

    def test_website_builder(self):
        """Test Website Builder functionality"""
        print("\n=== Testing Website Builder System ===")
        
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
        
        return website_id

    def test_biometric_authentication(self):
        """Test Biometric Authentication functionality"""
        print("\n=== Testing Biometric Authentication ===")
        
        # Test get registration options
        response = self.make_request('POST', '/biometric/registration-options')
        if response and response.status_code == 200:
            self.log_test("Get Biometric Registration Options", True, "Registration options retrieval successful")
        else:
            self.log_test("Get Biometric Registration Options", False, f"Registration options failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get authentication options (public endpoint)
        auth_data = {"email": "test@example.com"}
        response = self.make_request('POST', '/biometric/authentication-options', auth_data, auth_required=False)
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

    def test_escrow_system(self):
        """Test Escrow & Transaction Security functionality"""
        print("\n=== Testing Escrow & Transaction Security ===")
        
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
        if response and response.status_code in [200, 201]:
            self.log_test("Create Escrow Transaction", True, "Escrow transaction creation successful")
        else:
            self.log_test("Create Escrow Transaction", False, f"Escrow transaction creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get escrow statistics
        response = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Statistics", True, "Escrow statistics retrieval successful")
        else:
            self.log_test("Get Escrow Statistics", False, f"Escrow statistics retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_advanced_analytics(self):
        """Test Advanced Analytics & Business Intelligence functionality"""
        print("\n=== Testing Advanced Analytics & Business Intelligence ===")
        
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

    def run_focused_tests(self):
        """Run focused tests on new advanced features"""
        print("🔥 Testing NEW ADVANCED FEATURES in Mewayz Platform")
        print("=" * 60)
        
        self.test_website_builder()
        self.test_biometric_authentication()
        self.test_realtime_features()
        self.test_escrow_system()
        self.test_advanced_analytics()
        
        # Print summary
        self.print_summary()
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 60)
        print("📊 ADVANCED FEATURES TEST SUMMARY")
        print("=" * 60)
        
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
        
        print("\n" + "=" * 60)

if __name__ == "__main__":
    # Initialize tester
    tester = AdvancedFeaturesTester()
    
    # Run focused tests
    tester.run_focused_tests()
    
    # Exit with appropriate code
    failed_count = sum(1 for result in tester.test_results.values() if not result['success'])
    sys.exit(1 if failed_count > 0 else 0)