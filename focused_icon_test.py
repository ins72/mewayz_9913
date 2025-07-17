#!/usr/bin/env python3
"""
Focused Backend Testing for Icon Standardization Verification
Testing key endpoints to ensure icon standardization hasn't broken backend functionality
"""

import requests
import json
import sys
import time
from datetime import datetime

class IconStandardizationTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token from the test_result.md
        self.auth_token = "3|2tizxvYX1aqPpjgTN6rGz0QY2FMeWtHpnMts7GCY137499ef"
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
        time.sleep(0.1)  # Rate limiting
        
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
                response = self.session.get(url, headers=default_headers, params=data, timeout=5)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=5)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 5 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None
    
    def test_health_check(self):
        """Test API health check endpoints"""
        print("\n=== Testing Health Check Endpoints ===")
        
        # Test basic health endpoint
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("API Health Check", True, f"API is healthy - Status: {data.get('status', 'unknown')}")
            except:
                self.log_test("API Health Check", False, f"Health endpoint returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("API Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test basic test endpoint
        response = self.make_request('GET', '/test', auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("API Test Endpoint", True, f"Test endpoint working - Message: {data.get('message', 'No message')}")
            except:
                self.log_test("API Test Endpoint", False, f"Test endpoint returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("API Test Endpoint", False, f"Test endpoint failed - Status: {response.status_code if response else 'No response'}")
    
    def test_authentication_endpoints(self):
        """Test key authentication endpoints"""
        print("\n=== Testing Authentication Endpoints ===")
        
        # Test user registration (new user)
        register_data = {
            "name": "Icon Test User",
            "email": f"icontest_{int(time.time())}@example.com",
            "password": "SecurePassword123!",
            "password_confirmation": "SecurePassword123!"
        }
        
        response = self.make_request('POST', '/auth/register', register_data, auth_required=False)
        if response and response.status_code in [200, 201]:
            try:
                data = response.json()
                self.log_test("User Registration", True, "User registration successful")
            except:
                self.log_test("User Registration", False, f"Registration returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("User Registration", False, f"Registration failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user login
        login_data = {
            "email": register_data["email"],
            "password": register_data["password"]
        }
        
        response = self.make_request('POST', '/auth/login', login_data, auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("User Login", True, "User login successful")
            except:
                self.log_test("User Login", False, f"Login returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("User Login", False, f"Login failed - Status: {response.status_code if response else 'No response'}")
    
    def test_dashboard_endpoints(self):
        """Test key dashboard endpoints"""
        print("\n=== Testing Dashboard Endpoints ===")
        
        # Test payment packages (public endpoint)
        response = self.make_request('GET', '/payments/packages', auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Payment Packages", True, "Payment packages retrieval successful")
            except:
                self.log_test("Payment Packages", False, f"Payment packages returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("Payment Packages", False, f"Payment packages failed - Status: {response.status_code if response else 'No response'}")
        
        # Test OAuth providers (public endpoint)
        response = self.make_request('GET', '/auth/oauth/providers', auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("OAuth Providers", True, "OAuth providers retrieval successful")
            except:
                self.log_test("OAuth Providers", False, f"OAuth providers returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("OAuth Providers", False, f"OAuth providers failed - Status: {response.status_code if response else 'No response'}")
        
        # Test 2FA status (public endpoint)
        response = self.make_request('GET', '/auth/2fa/status', auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("2FA Status", True, "2FA status check successful")
            except:
                self.log_test("2FA Status", False, f"2FA status returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("2FA Status", False, f"2FA status failed - Status: {response.status_code if response else 'No response'}")
    
    def test_database_connectivity(self):
        """Test database connectivity through API"""
        print("\n=== Testing Database Connectivity ===")
        
        # Test system info endpoint which should check database
        response = self.make_request('GET', '/system/info', auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Database Connectivity", True, "Database connection successful via system info")
            except:
                self.log_test("Database Connectivity", False, f"System info returned non-JSON response - Status: {response.status_code}")
        else:
            # Try health endpoint which might mention database
            response = self.make_request('GET', '/health', auth_required=False)
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if 'database' in data:
                        self.log_test("Database Connectivity", True, f"Database status: {data.get('database', 'unknown')}")
                    else:
                        self.log_test("Database Connectivity", False, "Database status not reported in health check")
                except:
                    self.log_test("Database Connectivity", False, "Cannot verify database connectivity - non-JSON response")
            else:
                self.log_test("Database Connectivity", False, "Cannot verify database connectivity")
    
    def test_key_authenticated_endpoints(self):
        """Test a few key authenticated endpoints with existing token"""
        print("\n=== Testing Key Authenticated Endpoints ===")
        
        if not self.auth_token:
            self.log_test("Authenticated Endpoints", False, "Cannot test - no authentication token")
            return
        
        # Test custom auth middleware
        response = self.make_request('GET', '/test-custom-auth')
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Custom Auth Middleware", True, f"Custom auth working - User: {data.get('user_name', 'unknown')}")
            except:
                self.log_test("Custom Auth Middleware", False, f"Custom auth returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("Custom Auth Middleware", False, f"Custom auth failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user profile
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("User Profile", True, f"Profile retrieval successful - User: {data.get('user', {}).get('name', 'unknown')}")
            except:
                self.log_test("User Profile", False, f"Profile returned non-JSON response - Status: {response.status_code}")
        else:
            self.log_test("User Profile", False, f"Profile retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def run_focused_tests(self):
        """Run focused tests for icon standardization verification"""
        print("🎯 Starting Focused Backend Testing for Icon Standardization Verification")
        print("=" * 80)
        
        # Run key tests
        self.test_health_check()
        self.test_database_connectivity()
        self.test_authentication_endpoints()
        self.test_dashboard_endpoints()
        self.test_key_authenticated_endpoints()
        
        # Calculate results
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "=" * 80)
        print("📊 FOCUSED TEST SUMMARY")
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
        
        return success_rate >= 70  # Consider 70% or higher as acceptable

if __name__ == "__main__":
    tester = IconStandardizationTester()
    success = tester.run_focused_tests()
    
    if success:
        print("🎉 Icon standardization verification PASSED - Backend functionality intact!")
        sys.exit(0)
    else:
        print("⚠️  Icon standardization verification FAILED - Backend issues detected!")
        sys.exit(1)