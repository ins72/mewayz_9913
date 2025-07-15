#!/usr/bin/env python3
"""
Backend Testing Script for Mewayz Laravel Backend
Testing the updated Stripe integration after removing Python dependencies
"""

import requests
import json
import time
import sys
from typing import Dict, Any, List

class BackendTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, message: str, details: Dict = None):
        """Log test results"""
        result = {
            'test': test_name,
            'success': success,
            'message': message,
            'details': details or {},
            'timestamp': time.time()
        }
        self.test_results.append(result)
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status}: {test_name} - {message}")
        if details:
            print(f"   Details: {json.dumps(details, indent=2)}")
    
    def test_health_endpoint(self):
        """Test GET /api/health endpoint"""
        try:
            response = self.session.get(f"{self.base_url}/api/health", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                self.log_test(
                    "Health Check Endpoint",
                    True,
                    "Health endpoint accessible and returning 200",
                    {"status_code": response.status_code, "response": data}
                )
                return True
            else:
                self.log_test(
                    "Health Check Endpoint",
                    False,
                    f"Health endpoint returned status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except requests.exceptions.ConnectionError:
            self.log_test(
                "Health Check Endpoint",
                False,
                "Backend server not accessible - connection refused",
                {"base_url": self.base_url}
            )
            return False
        except Exception as e:
            self.log_test(
                "Health Check Endpoint",
                False,
                f"Health endpoint test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_authentication_login(self):
        """Test POST /api/auth/login endpoint"""
        try:
            login_data = {
                "email": "admin@example.com",
                "password": "admin123"
            }
            
            response = self.session.post(
                f"{self.base_url}/api/auth/login",
                json=login_data,
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                if 'token' in data or 'access_token' in data:
                    # Store token for future requests
                    self.auth_token = data.get('token') or data.get('access_token')
                    self.session.headers.update({
                        'Authorization': f'Bearer {self.auth_token}'
                    })
                    
                    self.log_test(
                        "Authentication Login",
                        True,
                        "Login successful with admin credentials",
                        {"status_code": response.status_code, "has_token": bool(self.auth_token)}
                    )
                    return True
                else:
                    self.log_test(
                        "Authentication Login",
                        False,
                        "Login response missing token",
                        {"status_code": response.status_code, "response": data}
                    )
                    return False
            else:
                self.log_test(
                    "Authentication Login",
                    False,
                    f"Login failed with status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except Exception as e:
            self.log_test(
                "Authentication Login",
                False,
                f"Login test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_authentication_me(self):
        """Test GET /api/auth/me endpoint"""
        if not self.auth_token:
            self.log_test(
                "Authentication Me Endpoint",
                False,
                "No auth token available - login first",
                {}
            )
            return False
            
        try:
            response = self.session.get(f"{self.base_url}/api/auth/me", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                self.log_test(
                    "Authentication Me Endpoint",
                    True,
                    "User profile retrieved successfully",
                    {"status_code": response.status_code, "user_email": data.get('email')}
                )
                return True
            else:
                self.log_test(
                    "Authentication Me Endpoint",
                    False,
                    f"Me endpoint failed with status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except Exception as e:
            self.log_test(
                "Authentication Me Endpoint",
                False,
                f"Me endpoint test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_stripe_packages(self):
        """Test GET /api/payments/packages endpoint"""
        try:
            response = self.session.get(f"{self.base_url}/api/payments/packages", timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if 'packages' in data and isinstance(data['packages'], dict):
                    packages = data['packages']
                    expected_packages = ['starter', 'professional', 'enterprise']
                    
                    all_packages_present = all(pkg in packages for pkg in expected_packages)
                    
                    self.log_test(
                        "Stripe Packages Endpoint",
                        all_packages_present,
                        f"Packages endpoint returned {len(packages)} packages",
                        {
                            "status_code": response.status_code,
                            "packages": list(packages.keys()),
                            "all_expected_present": all_packages_present
                        }
                    )
                    return all_packages_present
                else:
                    self.log_test(
                        "Stripe Packages Endpoint",
                        False,
                        "Packages response missing packages data",
                        {"status_code": response.status_code, "response": data}
                    )
                    return False
            else:
                self.log_test(
                    "Stripe Packages Endpoint",
                    False,
                    f"Packages endpoint failed with status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except Exception as e:
            self.log_test(
                "Stripe Packages Endpoint",
                False,
                f"Packages endpoint test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_stripe_checkout_session(self):
        """Test POST /api/payments/checkout/session endpoint"""
        try:
            checkout_data = {
                "package_id": "starter",
                "success_url": "http://localhost:8001/success",
                "cancel_url": "http://localhost:8001/cancel",
                "metadata": {
                    "test": "true"
                }
            }
            
            response = self.session.post(
                f"{self.base_url}/api/payments/checkout/session",
                json=checkout_data,
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                if 'url' in data and 'session_id' in data:
                    self.log_test(
                        "Stripe Checkout Session Creation",
                        True,
                        "Checkout session created successfully",
                        {
                            "status_code": response.status_code,
                            "has_url": bool(data.get('url')),
                            "has_session_id": bool(data.get('session_id')),
                            "url_domain": data.get('url', '').split('/')[2] if data.get('url') else None
                        }
                    )
                    return data.get('session_id')
                else:
                    self.log_test(
                        "Stripe Checkout Session Creation",
                        False,
                        "Checkout response missing required fields",
                        {"status_code": response.status_code, "response": data}
                    )
                    return False
            else:
                self.log_test(
                    "Stripe Checkout Session Creation",
                    False,
                    f"Checkout session creation failed with status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except Exception as e:
            self.log_test(
                "Stripe Checkout Session Creation",
                False,
                f"Checkout session test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_stripe_checkout_status(self, session_id: str):
        """Test GET /api/payments/checkout/status/{session_id} endpoint"""
        if not session_id:
            self.log_test(
                "Stripe Checkout Status",
                False,
                "No session ID available for status check",
                {}
            )
            return False
            
        try:
            response = self.session.get(
                f"{self.base_url}/api/payments/checkout/status/{session_id}",
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                self.log_test(
                    "Stripe Checkout Status",
                    True,
                    "Checkout status retrieved successfully",
                    {
                        "status_code": response.status_code,
                        "payment_status": data.get('payment_status'),
                        "status": data.get('status')
                    }
                )
                return True
            else:
                self.log_test(
                    "Stripe Checkout Status",
                    False,
                    f"Checkout status failed with status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except Exception as e:
            self.log_test(
                "Stripe Checkout Status",
                False,
                f"Checkout status test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_stripe_webhook(self):
        """Test POST /api/webhook/stripe endpoint"""
        try:
            # Create a mock webhook payload
            webhook_data = {
                "id": "evt_test_webhook",
                "object": "event",
                "type": "checkout.session.completed",
                "data": {
                    "object": {
                        "id": "cs_test_session",
                        "payment_status": "paid",
                        "metadata": {}
                    }
                }
            }
            
            # Note: Real webhook would need proper Stripe signature
            response = self.session.post(
                f"{self.base_url}/api/webhook/stripe",
                json=webhook_data,
                headers={"Stripe-Signature": "test_signature"},
                timeout=10
            )
            
            # Webhook might return 400 due to invalid signature, but endpoint should be accessible
            if response.status_code in [200, 400]:
                self.log_test(
                    "Stripe Webhook Endpoint",
                    True,
                    f"Webhook endpoint accessible (status: {response.status_code})",
                    {"status_code": response.status_code}
                )
                return True
            else:
                self.log_test(
                    "Stripe Webhook Endpoint",
                    False,
                    f"Webhook endpoint failed with status {response.status_code}",
                    {"status_code": response.status_code, "response": response.text}
                )
                return False
                
        except Exception as e:
            self.log_test(
                "Stripe Webhook Endpoint",
                False,
                f"Webhook endpoint test failed: {str(e)}",
                {"error": str(e)}
            )
            return False
    
    def test_other_api_endpoints(self):
        """Test other critical API endpoints to ensure they still work"""
        endpoints_to_test = [
            ("/api/system/info", "System Info"),
            ("/api/platform/overview", "Platform Overview"),
            ("/api/branding/info", "Branding Info"),
        ]
        
        results = []
        for endpoint, name in endpoints_to_test:
            try:
                response = self.session.get(f"{self.base_url}{endpoint}", timeout=10)
                success = response.status_code == 200
                
                self.log_test(
                    f"API Endpoint: {name}",
                    success,
                    f"Endpoint returned status {response.status_code}",
                    {"endpoint": endpoint, "status_code": response.status_code}
                )
                results.append(success)
                
            except Exception as e:
                self.log_test(
                    f"API Endpoint: {name}",
                    False,
                    f"Endpoint test failed: {str(e)}",
                    {"endpoint": endpoint, "error": str(e)}
                )
                results.append(False)
        
        return results
    
    def run_all_tests(self):
        """Run all backend tests"""
        print("🚀 Starting Backend Testing for Mewayz Laravel Backend")
        print("=" * 60)
        
        # Test 1: Health Check
        health_ok = self.test_health_endpoint()
        
        if not health_ok:
            print("\n❌ CRITICAL: Backend server not accessible. Cannot proceed with API testing.")
            print("   The Laravel backend needs to be running on port 8001 for testing.")
            return self.generate_report()
        
        # Test 2: Authentication
        login_ok = self.test_authentication_login()
        if login_ok:
            self.test_authentication_me()
        
        # Test 3: Stripe Integration (main focus of review)
        print("\n🎯 Testing Stripe Integration (Main Focus)")
        print("-" * 40)
        
        packages_ok = self.test_stripe_packages()
        session_id = self.test_stripe_checkout_session()
        
        if session_id:
            self.test_stripe_checkout_status(session_id)
        
        self.test_stripe_webhook()
        
        # Test 4: Other API endpoints
        print("\n🔍 Testing Other API Endpoints")
        print("-" * 30)
        self.test_other_api_endpoints()
        
        return self.generate_report()
    
    def generate_report(self):
        """Generate test report"""
        print("\n" + "=" * 60)
        print("📊 TEST RESULTS SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {(passed_tests/total_tests*100):.1f}%" if total_tests > 0 else "0%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS:")
            for result in self.test_results:
                if not result['success']:
                    print(f"   • {result['test']}: {result['message']}")
        
        # Focus on Stripe integration results
        stripe_tests = [r for r in self.test_results if 'stripe' in r['test'].lower()]
        if stripe_tests:
            stripe_passed = sum(1 for r in stripe_tests if r['success'])
            print(f"\n🎯 STRIPE INTEGRATION RESULTS:")
            print(f"   Stripe Tests: {len(stripe_tests)}")
            print(f"   Stripe Passed: {stripe_passed}/{len(stripe_tests)}")
            print(f"   Stripe Success Rate: {(stripe_passed/len(stripe_tests)*100):.1f}%")
        
        return {
            'total_tests': total_tests,
            'passed_tests': passed_tests,
            'failed_tests': failed_tests,
            'success_rate': (passed_tests/total_tests*100) if total_tests > 0 else 0,
            'results': self.test_results
        }

def main():
    """Main testing function"""
    print("Backend Testing Script for Mewayz Laravel Backend")
    print("Testing updated Stripe integration after removing Python dependencies")
    print()
    
    # Initialize tester
    tester = BackendTester()
    
    # Run all tests
    report = tester.run_all_tests()
    
    # Return appropriate exit code
    if report['failed_tests'] == 0:
        print("\n🎉 ALL TESTS PASSED!")
        sys.exit(0)
    else:
        print(f"\n⚠️  {report['failed_tests']} TESTS FAILED")
        sys.exit(1)

if __name__ == "__main__":
    main()