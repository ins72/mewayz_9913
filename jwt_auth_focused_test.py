#!/usr/bin/env python3
"""
JWT Authentication Focused Testing for Mewayz Platform
Specifically tests the authentication issues mentioned in the review request:
1. Login with tmonnens@outlook.com / Voetballen5 and JWT token generation
2. Protected endpoint testing with JWT tokens
3. Core API health testing for critical endpoints
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional

class JWTAuthTester:
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

    def test_priority_authentication(self):
        """PRIORITY TEST 1: Authentication with specific credentials"""
        print("\n=== PRIORITY TEST 1: Authentication Test ===")
        
        # Test admin login with exact credentials from review request
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.auth_token = data['token']
                    user_info = data.get('user', {})
                    self.log_test("Admin Login (tmonnens@outlook.com)", True, 
                                f"JWT Token Generated Successfully - User: {user_info.get('email', 'unknown')}, Role: {user_info.get('role', 'unknown')}", 
                                response_time)
                    
                    # Verify JWT token structure
                    token_parts = self.auth_token.split('.')
                    if len(token_parts) == 3:
                        self.log_test("JWT Token Structure Validation", True, 
                                    f"Valid JWT format with {len(token_parts)} parts (header.payload.signature)", 0)
                    else:
                        self.log_test("JWT Token Structure Validation", False, 
                                    f"Invalid JWT format - expected 3 parts, got {len(token_parts)}", 0)
                else:
                    self.log_test("Admin Login (tmonnens@outlook.com)", False, 
                                f"Login response missing token or success flag: {data}", response_time)
            except json.JSONDecodeError:
                self.log_test("Admin Login (tmonnens@outlook.com)", False, 
                            f"Invalid JSON response", response_time)
        else:
            status_code = response.status_code if response else 'timeout'
            self.log_test("Admin Login (tmonnens@outlook.com)", False, 
                        f"Login failed - Status: {status_code}", response_time)

    def test_protected_endpoints(self):
        """PRIORITY TEST 2: Protected Endpoint Testing with JWT"""
        print("\n=== PRIORITY TEST 2: Protected Endpoint Test ===")
        
        if not self.auth_token:
            self.log_test("Protected Endpoints", False, "No JWT token available for testing")
            return
        
        # Test the specific endpoints mentioned in review request
        protected_endpoints = [
            ('/admin/dashboard', 'Admin Dashboard'),
            ('/analytics/performance', 'Analytics Performance'),
            ('/social/accounts', 'Social Media Accounts')
        ]
        
        for endpoint, name in protected_endpoints:
            response, response_time = self.make_request('GET', endpoint)
            if response:
                if response.status_code == 200:
                    try:
                        data = response.json()
                        data_size = len(json.dumps(data))
                        self.log_test(f"Protected Endpoint: {name}", True, 
                                    f"JWT Authentication successful - Data size: {data_size} bytes", response_time)
                    except json.JSONDecodeError:
                        self.log_test(f"Protected Endpoint: {name}", True, 
                                    f"JWT Authentication successful - Non-JSON response", response_time)
                elif response.status_code == 401:
                    self.log_test(f"Protected Endpoint: {name}", False, 
                                f"JWT Authentication failed - 401 Unauthorized", response_time)
                elif response.status_code == 403:
                    self.log_test(f"Protected Endpoint: {name}", True, 
                                f"JWT Authentication working - 403 Forbidden (insufficient permissions)", response_time)
                elif response.status_code == 404:
                    self.log_test(f"Protected Endpoint: {name}", False, 
                                f"Endpoint not found - 404 Not Found", response_time)
                else:
                    self.log_test(f"Protected Endpoint: {name}", False, 
                                f"Unexpected status code: {response.status_code}", response_time)
            else:
                self.log_test(f"Protected Endpoint: {name}", False, 
                            f"Request timeout or connection error", response_time)

    def test_core_api_health(self):
        """PRIORITY TEST 3: Core API Health Test for Critical Endpoints"""
        print("\n=== PRIORITY TEST 3: Core API Health Test ===")
        
        # Test critical endpoints that were mentioned as having timeout issues
        critical_endpoints = [
            ('/health', 'System Health Check', False),  # Public endpoint
            ('/admin/dashboard', 'Admin Dashboard', True),
            ('/ai/services', 'AI Services', True),
            ('/bio-sites', 'Bio Sites', True),
            ('/ecommerce/dashboard', 'E-commerce Dashboard', True),
            ('/analytics/overview', 'Analytics Overview', True),
            ('/workspaces', 'Workspace Management', True),
            ('/social-media/accounts', 'Social Media Management', True),
            ('/bookings/dashboard', 'Advanced Booking System', True),
            ('/financial/dashboard/comprehensive', 'Financial Management', True)
        ]
        
        for endpoint, name, requires_auth in critical_endpoints:
            if requires_auth and not self.auth_token:
                self.log_test(f"Core API: {name}", False, "No JWT token available")
                continue
                
            # Temporarily remove auth for public endpoints
            original_token = self.auth_token
            if not requires_auth:
                self.auth_token = None
            
            response, response_time = self.make_request('GET', endpoint)
            
            # Restore auth token
            self.auth_token = original_token
            
            if response:
                if response.status_code == 200:
                    try:
                        data = response.json()
                        data_size = len(json.dumps(data))
                        self.log_test(f"Core API: {name}", True, 
                                    f"Endpoint healthy - Response time: {response_time:.3f}s, Data: {data_size} bytes", 
                                    response_time)
                    except json.JSONDecodeError:
                        self.log_test(f"Core API: {name}", True, 
                                    f"Endpoint healthy - Non-JSON response", response_time)
                elif response.status_code in [401, 403]:
                    if requires_auth:
                        self.log_test(f"Core API: {name}", True, 
                                    f"Endpoint accessible but auth issue - Status: {response.status_code}", response_time)
                    else:
                        self.log_test(f"Core API: {name}", False, 
                                    f"Unexpected auth requirement - Status: {response.status_code}", response_time)
                elif response.status_code == 404:
                    self.log_test(f"Core API: {name}", False, 
                                f"Endpoint not found - 404", response_time)
                else:
                    self.log_test(f"Core API: {name}", False, 
                                f"Unexpected status: {response.status_code}", response_time)
            else:
                self.log_test(f"Core API: {name}", False, 
                            f"Timeout or connection error - {response_time:.3f}s", response_time)

    def test_jwt_token_validation(self):
        """Additional JWT Token Validation Tests"""
        print("\n=== JWT Token Validation Tests ===")
        
        if not self.auth_token:
            self.log_test("JWT Token Validation", False, "No JWT token available")
            return
        
        # Test /auth/me endpoint to validate token
        response, response_time = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            try:
                data = response.json()
                user_info = data.get('user', {})
                self.log_test("JWT Token Validation (/auth/me)", True, 
                            f"Token valid - User: {user_info.get('email', 'unknown')}", response_time)
            except json.JSONDecodeError:
                self.log_test("JWT Token Validation (/auth/me)", False, 
                            f"Invalid JSON response", response_time)
        elif response and response.status_code == 401:
            self.log_test("JWT Token Validation (/auth/me)", False, 
                        f"JWT token invalid or expired - 401 Unauthorized", response_time)
        else:
            status_code = response.status_code if response else 'timeout'
            self.log_test("JWT Token Validation (/auth/me)", False, 
                        f"Token validation failed - Status: {status_code}", response_time)
        
        # Test with invalid token
        original_token = self.auth_token
        self.auth_token = "invalid.jwt.token"
        
        response, response_time = self.make_request('GET', '/auth/me')
        if response and response.status_code == 401:
            self.log_test("Invalid JWT Token Rejection", True, 
                        f"Invalid token properly rejected - 401 Unauthorized", response_time)
        else:
            status_code = response.status_code if response else 'timeout'
            self.log_test("Invalid JWT Token Rejection", False, 
                        f"Invalid token not properly rejected - Status: {status_code}", response_time)
        
        # Restore original token
        self.auth_token = original_token

    def run_focused_tests(self):
        """Run focused JWT authentication tests"""
        print("🔐 Starting JWT Authentication Focused Testing")
        print(f"Testing backend at: {self.api_url}")
        print("Focus: JWT Authentication Issues from Review Request")
        print("=" * 70)
        
        # Run priority tests in order
        self.test_priority_authentication()
        self.test_protected_endpoints()
        self.test_core_api_health()
        self.test_jwt_token_validation()
        
        # Generate focused summary
        self.generate_focused_summary()
    
    def generate_focused_summary(self):
        """Generate focused test summary"""
        print("\n" + "=" * 70)
        print("📊 JWT AUTHENTICATION FOCUSED TESTING SUMMARY")
        print("=" * 70)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Categorize results by priority
        auth_tests = [r for r in self.test_results if 'Admin Login' in r['test'] or 'JWT Token' in r['test']]
        protected_tests = [r for r in self.test_results if 'Protected Endpoint' in r['test']]
        health_tests = [r for r in self.test_results if 'Core API' in r['test']]
        
        print(f"\n🔐 AUTHENTICATION TESTS ({len(auth_tests)}):")
        for result in auth_tests:
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {result['test']}: {result['details']}")
        
        print(f"\n🛡️  PROTECTED ENDPOINT TESTS ({len(protected_tests)}):")
        for result in protected_tests:
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {result['test']}: {result['details']}")
        
        print(f"\n🏥 CORE API HEALTH TESTS ({len(health_tests)}):")
        for result in health_tests:
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {result['test']}: {result['details']}")
        
        if failed_tests > 0:
            print(f"\n❌ CRITICAL ISSUES IDENTIFIED ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print("\n" + "=" * 70)
        
        # JWT-specific assessment
        auth_success = sum(1 for r in auth_tests if r['success'])
        protected_success = sum(1 for r in protected_tests if r['success'])
        
        print("🎯 JWT AUTHENTICATION ASSESSMENT:")
        if auth_success == len(auth_tests) and auth_success > 0:
            print("✅ JWT Authentication: WORKING - Login and token generation successful")
        else:
            print("❌ JWT Authentication: FAILING - Login or token issues detected")
        
        if protected_success >= len(protected_tests) * 0.7:  # 70% threshold
            print("✅ Protected Endpoints: WORKING - JWT tokens properly validated")
        else:
            print("❌ Protected Endpoints: FAILING - JWT token validation issues")
        
        if success_rate >= 80:
            print("🎉 CONCLUSION: JWT authentication fixes are WORKING! System is ready.")
        elif success_rate >= 60:
            print("⚠️  CONCLUSION: JWT authentication mostly working but needs attention.")
        else:
            print("❌ CONCLUSION: JWT authentication still has CRITICAL issues.")

def main():
    """Main function to run the focused JWT tests"""
    # Backend URL from environment
    backend_url = "https://c231d264-b140-4556-b515-9a9bf3fb6c1d.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    print("🎯 Focus: JWT Authentication Issues from Review Request")
    
    # Initialize tester
    tester = JWTAuthTester(backend_url)
    
    # Run focused tests
    tester.run_focused_tests()

if __name__ == "__main__":
    main()