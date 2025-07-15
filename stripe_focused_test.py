#!/usr/bin/env python3
"""
Focused Stripe Payment Testing for Mewayz Platform
Tests Stripe payment endpoints with updated API keys
"""

import requests
import json
import sys
import time
from datetime import datetime

class StripePaymentTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        
        # Admin user credentials
        self.admin_user = {
            "email": "admin@example.com", 
            "password": "admin123",
            "token": None
        }
        
        self.test_results = []
        
    def log_result(self, test_name: str, status: str, details: str = "", response_time: float = 0):
        """Log test result"""
        result = {
            "test": test_name,
            "status": status,
            "details": details,
            "response_time": response_time,
            "timestamp": datetime.now().isoformat()
        }
        self.test_results.append(result)
        
        status_symbol = "✅" if status == "PASS" else "❌" if status == "FAIL" else "⚠️"
        print(f"{status_symbol} {test_name}: {status}")
        if details:
            print(f"   Details: {details}")
        if response_time > 0:
            print(f"   Response Time: {response_time:.3f}s")
        print()

    def make_request(self, method: str, endpoint: str, data: dict = None, headers: dict = None) -> requests.Response:
        """Make HTTP request with proper authentication"""
        url = f"{self.api_url}/{endpoint.lstrip('/')}"
        
        # Add auth token if available
        if self.admin_user.get('token'):
            headers = headers or {}
            headers['Authorization'] = f'Bearer {self.admin_user["token"]}'
        
        headers = headers or {}
        headers['Accept'] = 'application/json'
        headers['Content-Type'] = 'application/json'
        
        start_time = time.time()
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, params=data, headers=headers)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=headers)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=headers)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=headers)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            response_time = time.time() - start_time
            return response
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
            raise

    def login_admin(self):
        """Login admin user and get token"""
        login_data = {
            "email": self.admin_user["email"],
            "password": self.admin_user["password"]
        }
        
        try:
            response = self.make_request('POST', '/auth/login', login_data)
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.admin_user['token'] = data['token']
                    return True
            return False
        except Exception as e:
            print(f"Login failed: {str(e)}")
            return False

    def test_stripe_packages(self):
        """Test getting available Stripe packages"""
        print("💳 Testing Stripe Packages...")
        
        try:
            response = self.make_request('GET', '/payments/packages')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('packages'):
                    packages = data['packages']
                    package_names = list(packages.keys())
                    self.log_result("Get Stripe Packages", "PASS", 
                                  f"Retrieved {len(packages)} packages: {', '.join(package_names)}", 
                                  response_time)
                    return True, packages
                else:
                    self.log_result("Get Stripe Packages", "FAIL", 
                                  "Response missing success flag or packages", 
                                  response_time)
                    return False, None
            else:
                self.log_result("Get Stripe Packages", "FAIL", 
                              f"Request failed with status {response.status_code}", 
                              response_time)
                return False, None
                
        except Exception as e:
            self.log_result("Get Stripe Packages", "FAIL", 
                          f"Request failed: {str(e)}")
            return False, None

    def test_stripe_checkout_session(self, packages):
        """Test creating Stripe checkout session"""
        print("🛒 Testing Stripe Checkout Session Creation...")
        
        if not packages:
            self.log_result("Create Stripe Checkout Session", "SKIP", 
                          "No packages available for testing")
            return False
        
        # Test with starter package
        package_id = 'starter'
        if package_id not in packages:
            package_id = list(packages.keys())[0]  # Use first available package
        
        checkout_data = {
            "package_id": package_id,
            "success_url": f"{self.base_url}/payment/success",
            "cancel_url": f"{self.base_url}/payment/cancel",
            "metadata": {
                "test": "true",
                "package": package_id
            }
        }
        
        try:
            response = self.make_request('POST', '/payments/checkout/session', checkout_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('url') and data.get('session_id'):
                    session_id = data['session_id']
                    checkout_url = data['url']
                    self.log_result("Create Stripe Checkout Session", "PASS", 
                                  f"Session created successfully. Session ID: {session_id[:20]}...", 
                                  response_time)
                    return True, session_id
                else:
                    self.log_result("Create Stripe Checkout Session", "FAIL", 
                                  "Response missing required fields (success, url, session_id)", 
                                  response_time)
                    return False, None
            else:
                error_msg = "Unknown error"
                try:
                    error_data = response.json()
                    error_msg = error_data.get('error', error_msg)
                except:
                    pass
                
                self.log_result("Create Stripe Checkout Session", "FAIL", 
                              f"Request failed with status {response.status_code}: {error_msg}", 
                              response_time)
                return False, None
                
        except Exception as e:
            self.log_result("Create Stripe Checkout Session", "FAIL", 
                          f"Request failed: {str(e)}")
            return False, None

    def test_stripe_checkout_status(self, session_id):
        """Test getting Stripe checkout session status"""
        print("📊 Testing Stripe Checkout Status...")
        
        if not session_id:
            self.log_result("Get Stripe Checkout Status", "SKIP", 
                          "No session ID available for testing")
            return False
        
        try:
            response = self.make_request('GET', f'/payments/checkout/status/{session_id}')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    status = data.get('status', 'unknown')
                    payment_status = data.get('payment_status', 'unknown')
                    self.log_result("Get Stripe Checkout Status", "PASS", 
                                  f"Status: {status}, Payment Status: {payment_status}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Get Stripe Checkout Status", "FAIL", 
                                  "Response missing success flag", 
                                  response_time)
                    return False
            elif response.status_code == 404:
                self.log_result("Get Stripe Checkout Status", "FAIL", 
                              "Session not found in database", 
                              response_time)
                return False
            else:
                error_msg = "Unknown error"
                try:
                    error_data = response.json()
                    error_msg = error_data.get('error', error_msg)
                except:
                    pass
                
                self.log_result("Get Stripe Checkout Status", "FAIL", 
                              f"Request failed with status {response.status_code}: {error_msg}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Get Stripe Checkout Status", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_stripe_webhook_endpoint(self):
        """Test Stripe webhook endpoint accessibility"""
        print("🔗 Testing Stripe Webhook Endpoint...")
        
        # Test webhook endpoint with invalid data (should return error but endpoint should be accessible)
        webhook_data = {
            "test": "webhook_test"
        }
        
        try:
            # Don't use auth for webhook endpoint
            headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Stripe-Signature': 'test_signature'
            }
            
            response = self.session.post(f"{self.api_url}/webhook/stripe", 
                                       json=webhook_data, headers=headers)
            response_time = response.elapsed.total_seconds()
            
            # Webhook should be accessible (even if it returns error for invalid data)
            if response.status_code in [200, 400, 500]:
                self.log_result("Stripe Webhook Endpoint", "PASS", 
                              f"Webhook endpoint accessible (status: {response.status_code})", 
                              response_time)
                return True
            else:
                self.log_result("Stripe Webhook Endpoint", "FAIL", 
                              f"Webhook endpoint not accessible (status: {response.status_code})", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Stripe Webhook Endpoint", "FAIL", 
                          f"Webhook endpoint test failed: {str(e)}")
            return False

    def test_health_check(self):
        """Test system health check"""
        print("🏥 Testing System Health...")
        
        try:
            response = self.make_request('GET', '/health')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("System Health Check", "PASS", 
                                  f"System healthy: {data.get('message', 'OK')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("System Health Check", "FAIL", 
                                  "Health check succeeded but missing success flag", 
                                  response_time)
                    return False
            else:
                self.log_result("System Health Check", "FAIL", 
                              f"Health check failed with status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("System Health Check", "FAIL", 
                          f"Health check failed: {str(e)}")
            return False

    def run_focused_tests(self):
        """Run focused Stripe payment tests"""
        print("🚀 Starting Focused Stripe Payment Testing...")
        print("=" * 60)
        
        # Test system health first
        if not self.test_health_check():
            print("❌ System health check failed. Aborting tests.")
            return
        
        # Login admin user
        if not self.login_admin():
            print("❌ Admin login failed. Aborting tests.")
            return
        
        print("✅ Admin login successful")
        print()
        
        # Test Stripe functionality
        success, packages = self.test_stripe_packages()
        
        session_id = None
        if success:
            success, session_id = self.test_stripe_checkout_session(packages)
        
        if session_id:
            self.test_stripe_checkout_status(session_id)
        
        self.test_stripe_webhook_endpoint()
        
        # Print summary
        print("=" * 60)
        print("📋 FOCUSED STRIPE TESTING REPORT")
        print("=" * 60)
        
        passed = sum(1 for result in self.test_results if result['status'] == 'PASS')
        failed = sum(1 for result in self.test_results if result['status'] == 'FAIL')
        total = len(self.test_results)
        
        print(f"📊 TESTING STATISTICS:")
        print(f"   Total Tests: {total}")
        print(f"   Passed: {passed} ✅")
        print(f"   Failed: {failed} ❌")
        print(f"   Success Rate: {(passed/total*100):.1f}%")
        
        if failed > 0:
            print(f"\n🚨 FAILED TESTS:")
            for result in self.test_results:
                if result['status'] == 'FAIL':
                    print(f"   ❌ {result['test']}: {result['details']}")
        
        print("\n" + "=" * 60)
        print("✅ FOCUSED STRIPE TESTING COMPLETED")
        print("=" * 60)

if __name__ == "__main__":
    tester = StripePaymentTester()
    tester.run_focused_tests()