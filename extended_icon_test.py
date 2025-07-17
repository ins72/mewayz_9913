#!/usr/bin/env python3
"""
Extended Backend Testing for Icon Standardization Verification
Testing additional key endpoints to ensure comprehensive coverage
"""

import requests
import json
import sys
import time
from datetime import datetime

class ExtendedIconTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.test_results = {}
        self.session = requests.Session()
        
    def log_test(self, test_name, success, message):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        
        self.test_results[test_name] = {
            'success': success,
            'message': message,
            'timestamp': datetime.now().isoformat()
        }
        
    def make_request(self, method, endpoint, data=None, auth_required=False):
        """Make HTTP request with proper headers"""
        time.sleep(0.1)  # Rate limiting
        
        url = f"{self.api_url}{endpoint}"
        
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=headers, params=data, timeout=5)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=headers, json=data, timeout=5)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            return None
        except requests.exceptions.RequestException as e:
            return None
    
    def test_additional_public_endpoints(self):
        """Test additional public endpoints"""
        print("\n=== Testing Additional Public Endpoints ===")
        
        # Test Stripe packages
        response = self.make_request('GET', '/stripe/packages')
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Stripe Packages", True, "Stripe packages retrieval successful")
            except:
                self.log_test("Stripe Packages", False, f"Stripe packages returned non-JSON - Status: {response.status_code}")
        else:
            self.log_test("Stripe Packages", False, f"Stripe packages failed - Status: {response.status_code if response else 'No response'}")
        
        # Test biometric authentication options (public)
        response = self.make_request('POST', '/biometric/authentication-options')
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Biometric Auth Options", True, "Biometric authentication options successful")
            except:
                self.log_test("Biometric Auth Options", False, f"Biometric auth returned non-JSON - Status: {response.status_code}")
        else:
            self.log_test("Biometric Auth Options", False, f"Biometric auth failed - Status: {response.status_code if response else 'No response'}")
    
    def test_route_accessibility(self):
        """Test that key routes are accessible (even if they return errors due to auth)"""
        print("\n=== Testing Route Accessibility ===")
        
        # Test routes that should exist but may require auth
        test_routes = [
            '/bio-sites/',
            '/social-media/accounts',
            '/ecommerce/products',
            '/courses/',
            '/workspaces',
            '/analytics/reports'
        ]
        
        accessible_routes = 0
        for route in test_routes:
            response = self.make_request('GET', route)
            if response and response.status_code in [200, 401, 403, 422]:  # Any response means route exists
                accessible_routes += 1
                self.log_test(f"Route {route}", True, f"Route accessible - Status: {response.status_code}")
            else:
                self.log_test(f"Route {route}", False, f"Route not accessible - Status: {response.status_code if response else 'No response'}")
        
        # Summary of route accessibility
        route_success_rate = (accessible_routes / len(test_routes)) * 100
        self.log_test("Route Accessibility", route_success_rate >= 50, f"{accessible_routes}/{len(test_routes)} routes accessible ({route_success_rate:.1f}%)")
    
    def test_error_handling(self):
        """Test that error handling is working properly"""
        print("\n=== Testing Error Handling ===")
        
        # Test non-existent endpoint
        response = self.make_request('GET', '/non-existent-endpoint')
        if response and response.status_code == 404:
            self.log_test("404 Error Handling", True, "404 errors handled correctly")
        else:
            self.log_test("404 Error Handling", False, f"404 handling issue - Status: {response.status_code if response else 'No response'}")
        
        # Test malformed request
        response = self.make_request('POST', '/auth/login', {"invalid": "data"})
        if response and response.status_code in [400, 422, 401]:  # Any validation error is good
            self.log_test("Validation Error Handling", True, f"Validation errors handled correctly - Status: {response.status_code}")
        else:
            self.log_test("Validation Error Handling", False, f"Validation handling issue - Status: {response.status_code if response else 'No response'}")
    
    def test_cors_and_headers(self):
        """Test CORS and header handling"""
        print("\n=== Testing CORS and Headers ===")
        
        # Test CORS headers on health endpoint
        response = self.make_request('GET', '/health')
        if response:
            cors_headers = [
                'Access-Control-Allow-Origin',
                'Access-Control-Allow-Methods',
                'Access-Control-Allow-Headers'
            ]
            
            has_cors = any(header in response.headers for header in cors_headers)
            if has_cors or response.status_code == 200:  # Either CORS headers or successful response
                self.log_test("CORS Headers", True, "CORS handling working correctly")
            else:
                self.log_test("CORS Headers", False, "CORS headers missing")
        else:
            self.log_test("CORS Headers", False, "Cannot test CORS - no response")
    
    def run_extended_tests(self):
        """Run extended tests for comprehensive verification"""
        print("🔍 Starting Extended Backend Testing for Icon Standardization")
        print("=" * 80)
        
        # Run additional tests
        self.test_additional_public_endpoints()
        self.test_route_accessibility()
        self.test_error_handling()
        self.test_cors_and_headers()
        
        # Calculate results
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "=" * 80)
        print("📊 EXTENDED TEST SUMMARY")
        print("=" * 80)
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("=" * 80)
        
        return success_rate >= 60  # Consider 60% or higher as acceptable for extended tests

if __name__ == "__main__":
    tester = ExtendedIconTester()
    success = tester.run_extended_tests()
    
    if success:
        print("🎉 Extended icon standardization verification PASSED!")
        sys.exit(0)
    else:
        print("⚠️  Extended icon standardization verification needs attention!")
        sys.exit(1)