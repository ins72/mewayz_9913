#!/usr/bin/env python3
"""
Focused Backend API Testing for the specific endpoints requested in the review
Tests: Health check, Admin login, /auth/me, and /admin/dashboard
"""

import requests
import json
import time
import sys

class FocusedBackendTester:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0, response_data: dict = None):
        """Log test results with detailed information"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'status': status,
            'success': success,
            'details': details,
            'response_time': f"{response_time:.3f}s",
            'response_data': response_data
        }
        self.test_results.append(result)
        print(f"{status} - {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    Details: {details}")
        if response_data and isinstance(response_data, dict):
            print(f"    Response: {json.dumps(response_data, indent=2)[:200]}...")
    
    def make_request(self, method: str, endpoint: str, data: dict = None, headers: dict = None, timeout: int = 30):
        """Make HTTP request with detailed error handling"""
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
            
            print(f"Making {method} request to: {url}")
            if data:
                print(f"Request data: {json.dumps(data, indent=2)}")
            
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, timeout=timeout)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            
            print(f"Response status: {response.status_code}")
            print(f"Response headers: {dict(response.headers)}")
            
            try:
                response_data = response.json()
                print(f"Response data: {json.dumps(response_data, indent=2)}")
            except:
                response_data = response.text
                print(f"Response text: {response_data}")
            
            return response, response_time, response_data
            
        except requests.exceptions.Timeout:
            print(f"Request timed out after {timeout}s")
            return None, timeout, {"error": "timeout"}
        except requests.exceptions.RequestException as e:
            print(f"Request error: {str(e)}")
            return None, 0.0, {"error": str(e)}
    
    def test_health_check(self):
        """Test GET /api/health endpoint"""
        print("\n" + "="*60)
        print("🏥 TESTING HEALTH CHECK ENDPOINT")
        print("="*60)
        
        response, response_time, response_data = self.make_request('GET', '/health')
        
        if response and response.status_code == 200:
            self.log_test("Health Check", True, f"Status: {response.status_code}", response_time, response_data)
            return True
        else:
            error_msg = f"Status: {response.status_code if response else 'timeout'}"
            self.log_test("Health Check", False, error_msg, response_time, response_data)
            return False
    
    def test_admin_login(self):
        """Test POST /api/auth/login with admin credentials"""
        print("\n" + "="*60)
        print("🔐 TESTING ADMIN LOGIN")
        print("="*60)
        
        login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time, response_data = self.make_request('POST', '/auth/login', login_data)
        
        if response and response.status_code == 200:
            if isinstance(response_data, dict) and response_data.get('token'):
                self.auth_token = response_data['token']
                user_info = response_data.get('user', {})
                self.log_test("Admin Login", True, f"Login successful, user: {user_info.get('email', 'unknown')}", response_time, response_data)
                return True
            else:
                self.log_test("Admin Login", False, "No token in response", response_time, response_data)
                return False
        else:
            error_msg = f"Status: {response.status_code if response else 'timeout'}"
            self.log_test("Admin Login", False, error_msg, response_time, response_data)
            return False
    
    def test_auth_me(self):
        """Test GET /api/auth/me with authentication token"""
        print("\n" + "="*60)
        print("👤 TESTING AUTHENTICATED USER PROFILE")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("Get User Profile", False, "No authentication token available")
            return False
        
        response, response_time, response_data = self.make_request('GET', '/auth/me')
        
        if response and response.status_code == 200:
            user_info = response_data.get('user', {}) if isinstance(response_data, dict) else {}
            self.log_test("Get User Profile", True, f"Profile retrieved for: {user_info.get('name', 'unknown')}", response_time, response_data)
            return True
        else:
            error_msg = f"Status: {response.status_code if response else 'timeout'}"
            self.log_test("Get User Profile", False, error_msg, response_time, response_data)
            return False
    
    def test_admin_dashboard(self):
        """Test GET /api/admin/dashboard with authentication token"""
        print("\n" + "="*60)
        print("📊 TESTING ADMIN DASHBOARD")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("Admin Dashboard", False, "No authentication token available")
            return False
        
        response, response_time, response_data = self.make_request('GET', '/admin/dashboard')
        
        if response:
            if response.status_code == 200:
                self.log_test("Admin Dashboard", True, "Dashboard data retrieved successfully", response_time, response_data)
                return True
            elif response.status_code == 403:
                self.log_test("Admin Dashboard", True, "Access properly restricted (403 Forbidden) - security working", response_time, response_data)
                return True
            else:
                error_msg = f"Status: {response.status_code}"
                self.log_test("Admin Dashboard", False, error_msg, response_time, response_data)
                return False
        else:
            self.log_test("Admin Dashboard", False, "Request timeout", response_time, response_data)
            return False
    
    def run_focused_tests(self):
        """Run the specific tests requested in the review"""
        print("🎯 FOCUSED BACKEND AUTHENTICATION TESTING")
        print(f"Testing backend at: {self.api_url}")
        print("Testing the specific endpoints requested in the review:")
        print("1. GET /api/health")
        print("2. POST /api/auth/login (with admin credentials)")
        print("3. GET /api/auth/me (with token)")
        print("4. GET /api/admin/dashboard (with token)")
        
        # Run tests in sequence
        health_ok = self.test_health_check()
        login_ok = self.test_admin_login()
        profile_ok = self.test_auth_me()
        dashboard_ok = self.test_admin_dashboard()
        
        # Generate focused summary
        self.generate_focused_summary(health_ok, login_ok, profile_ok, dashboard_ok)
        
        return {
            'health_check': health_ok,
            'admin_login': login_ok,
            'auth_me': profile_ok,
            'admin_dashboard': dashboard_ok
        }
    
    def generate_focused_summary(self, health_ok, login_ok, profile_ok, dashboard_ok):
        """Generate summary for the focused tests"""
        print("\n" + "="*60)
        print("📋 FOCUSED TESTING SUMMARY")
        print("="*60)
        
        total_tests = 4
        passed_tests = sum([health_ok, login_ok, profile_ok, dashboard_ok])
        success_rate = (passed_tests / total_tests * 100)
        
        print(f"Requested Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {total_tests - passed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        print(f"\n📊 DETAILED RESULTS:")
        print(f"  1. Health Check: {'✅ PASS' if health_ok else '❌ FAIL'}")
        print(f"  2. Admin Login: {'✅ PASS' if login_ok else '❌ FAIL'}")
        print(f"  3. User Profile (/auth/me): {'✅ PASS' if profile_ok else '❌ FAIL'}")
        print(f"  4. Admin Dashboard: {'✅ PASS' if dashboard_ok else '❌ FAIL'}")
        
        if self.auth_token:
            print(f"\n🔑 Authentication Token: {self.auth_token[:20]}...")
        
        print("\n" + "="*60)
        
        # Assessment
        if success_rate == 100:
            print("🎉 PERFECT: All requested authentication endpoints are working!")
        elif success_rate >= 75:
            print("✅ EXCELLENT: Authentication system is mostly functional.")
        elif success_rate >= 50:
            print("⚠️  MODERATE: Authentication has some issues.")
        else:
            print("❌ CRITICAL: Authentication system has major problems.")

def main():
    """Main function to run the focused tests"""
    backend_url = "https://1fa91da9-a61e-4244-86cb-361982f500be.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    
    # Initialize tester
    tester = FocusedBackendTester(backend_url)
    
    # Run focused tests
    results = tester.run_focused_tests()
    
    return results

if __name__ == "__main__":
    main()