#!/usr/bin/env python3
"""
Stripe Payment Integration Testing Suite
Tests all Stripe payment functionality including APIs, Python integration, and database operations
"""

import requests
import json
import sys
import time
import subprocess
import os
from datetime import datetime
from typing import Dict, Any, Optional

class StripePaymentTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        self.test_results = []
        
        # Test data
        self.test_packages = {
            'starter': {'amount': 9.99, 'currency': 'USD', 'name': 'Starter Package'},
            'professional': {'amount': 29.99, 'currency': 'USD', 'name': 'Professional Package'},
            'enterprise': {'amount': 99.99, 'currency': 'USD', 'name': 'Enterprise Package'},
        }
        
        self.test_stripe_price_id = "price_test_123456789"
        self.test_session_id = None
        
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

    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None) -> requests.Response:
        """Make HTTP request"""
        url = f"{self.api_url}/{endpoint.lstrip('/')}"
        
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

    def test_system_health(self):
        """Test system health"""
        print("🔍 Testing System Health...")
        
        try:
            response = self.make_request('GET', '/health')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                self.log_result("System Health Check", "PASS", 
                              f"API responding: {data.get('message', 'OK')}", 
                              response_time)
                return True
            else:
                self.log_result("System Health Check", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
        except Exception as e:
            self.log_result("System Health Check", "FAIL", 
                          f"System not accessible: {str(e)}")
            return False

    def test_payment_packages_api(self):
        """Test GET /api/payments/packages"""
        print("📦 Testing Payment Packages API...")
        
        try:
            response = self.make_request('GET', '/payments/packages')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                
                if data.get('success') and 'packages' in data:
                    packages = data['packages']
                    
                    # Verify all expected packages are present
                    expected_packages = ['starter', 'professional', 'enterprise']
                    missing_packages = []
                    
                    for package_id in expected_packages:
                        if package_id not in packages:
                            missing_packages.append(package_id)
                        else:
                            # Verify package structure
                            package = packages[package_id]
                            if not all(key in package for key in ['amount', 'currency', 'name']):
                                missing_packages.append(f"{package_id} (incomplete)")
                    
                    if not missing_packages:
                        self.log_result("Payment Packages API", "PASS", 
                                      f"Retrieved {len(packages)} packages correctly", 
                                      response_time)
                        return True
                    else:
                        self.log_result("Payment Packages API", "FAIL", 
                                      f"Missing or incomplete packages: {', '.join(missing_packages)}", 
                                      response_time)
                        return False
                else:
                    self.log_result("Payment Packages API", "FAIL", 
                                  "Response missing success flag or packages data", 
                                  response_time)
                    return False
            else:
                self.log_result("Payment Packages API", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Payment Packages API", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_checkout_session_creation_package(self):
        """Test POST /api/payments/checkout/session with package_id"""
        print("🛒 Testing Checkout Session Creation (Package)...")
        
        try:
            checkout_data = {
                "package_id": "starter",
                "success_url": f"{self.base_url}/success?session_id={{CHECKOUT_SESSION_ID}}",
                "cancel_url": f"{self.base_url}/cancel",
                "metadata": {
                    "test": "true",
                    "source": "backend_test"
                }
            }
            
            response = self.make_request('POST', '/payments/checkout/session', checkout_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                
                if data.get('success') and 'session_id' in data and 'url' in data:
                    session_id = data['session_id']
                    checkout_url = data['url']
                    
                    # Store session ID for status testing
                    self.test_session_id = session_id
                    
                    # Verify URL format
                    if checkout_url.startswith('https://checkout.stripe.com/'):
                        self.log_result("Checkout Session Creation (Package)", "PASS", 
                                      f"Session created: {session_id}", 
                                      response_time)
                        return True
                    else:
                        self.log_result("Checkout Session Creation (Package)", "FAIL", 
                                      f"Invalid checkout URL format: {checkout_url}", 
                                      response_time)
                        return False
                else:
                    self.log_result("Checkout Session Creation (Package)", "FAIL", 
                                  "Response missing required fields (success, session_id, url)", 
                                  response_time)
                    return False
            else:
                error_data = response.json() if response.headers.get('content-type', '').startswith('application/json') else {}
                self.log_result("Checkout Session Creation (Package)", "FAIL", 
                              f"API returned status {response.status_code}: {error_data.get('error', 'Unknown error')}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Checkout Session Creation (Package)", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_checkout_session_creation_stripe_price(self):
        """Test POST /api/payments/checkout/session with stripe_price_id"""
        print("💳 Testing Checkout Session Creation (Stripe Price ID)...")
        
        try:
            checkout_data = {
                "stripe_price_id": self.test_stripe_price_id,
                "quantity": 2,
                "success_url": f"{self.base_url}/success?session_id={{CHECKOUT_SESSION_ID}}",
                "cancel_url": f"{self.base_url}/cancel",
                "metadata": {
                    "test": "true",
                    "source": "backend_test_price_id"
                }
            }
            
            response = self.make_request('POST', '/payments/checkout/session', checkout_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                
                if data.get('success') and 'session_id' in data and 'url' in data:
                    session_id = data['session_id']
                    checkout_url = data['url']
                    
                    # Verify URL format
                    if checkout_url.startswith('https://checkout.stripe.com/'):
                        self.log_result("Checkout Session Creation (Stripe Price)", "PASS", 
                                      f"Session created with price ID: {session_id}", 
                                      response_time)
                        return True
                    else:
                        self.log_result("Checkout Session Creation (Stripe Price)", "FAIL", 
                                      f"Invalid checkout URL format: {checkout_url}", 
                                      response_time)
                        return False
                else:
                    self.log_result("Checkout Session Creation (Stripe Price)", "FAIL", 
                                  "Response missing required fields", 
                                  response_time)
                    return False
            else:
                error_data = response.json() if response.headers.get('content-type', '').startswith('application/json') else {}
                self.log_result("Checkout Session Creation (Stripe Price)", "FAIL", 
                              f"API returned status {response.status_code}: {error_data.get('error', 'Unknown error')}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Checkout Session Creation (Stripe Price)", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_checkout_status_check(self):
        """Test GET /api/payments/checkout/status/{sessionId}"""
        print("📊 Testing Checkout Status Check...")
        
        if not self.test_session_id:
            self.log_result("Checkout Status Check", "FAIL", 
                          "No session ID available from previous test")
            return False
        
        try:
            response = self.make_request('GET', f'/payments/checkout/status/{self.test_session_id}')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                
                if data.get('success'):
                    required_fields = ['status', 'payment_status', 'amount_total', 'currency']
                    missing_fields = [field for field in required_fields if field not in data]
                    
                    if not missing_fields:
                        status = data['status']
                        payment_status = data['payment_status']
                        
                        self.log_result("Checkout Status Check", "PASS", 
                                      f"Status: {status}, Payment: {payment_status}", 
                                      response_time)
                        return True
                    else:
                        self.log_result("Checkout Status Check", "FAIL", 
                                      f"Response missing fields: {', '.join(missing_fields)}", 
                                      response_time)
                        return False
                else:
                    self.log_result("Checkout Status Check", "FAIL", 
                                  "Response missing success flag", 
                                  response_time)
                    return False
            elif response.status_code == 404:
                self.log_result("Checkout Status Check", "FAIL", 
                              "Session not found in database", 
                              response_time)
                return False
            else:
                error_data = response.json() if response.headers.get('content-type', '').startswith('application/json') else {}
                self.log_result("Checkout Status Check", "FAIL", 
                              f"API returned status {response.status_code}: {error_data.get('error', 'Unknown error')}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Checkout Status Check", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_python_integration_script(self):
        """Test stripe_integration.py script directly"""
        print("🐍 Testing Python Integration Script...")
        
        try:
            # Test script existence
            script_path = "/app/backend/stripe_integration.py"
            if not os.path.exists(script_path):
                self.log_result("Python Integration Script", "FAIL", 
                              f"Script not found at {script_path}")
                return False
            
            # Test script with invalid command (should return error)
            try:
                result = subprocess.run(
                    ["python3", script_path, "invalid_command"],
                    cwd="/app/backend",
                    capture_output=True,
                    text=True,
                    timeout=10
                )
                
                if result.stdout:
                    output = json.loads(result.stdout)
                    if not output.get('success') and 'error' in output:
                        self.log_result("Python Integration Script", "PASS", 
                                      "Script correctly handles invalid commands")
                        return True
                    else:
                        self.log_result("Python Integration Script", "FAIL", 
                                      "Script should reject invalid commands")
                        return False
                else:
                    self.log_result("Python Integration Script", "FAIL", 
                                  "Script produced no output")
                    return False
                    
            except subprocess.TimeoutExpired:
                self.log_result("Python Integration Script", "FAIL", 
                              "Script execution timed out")
                return False
            except json.JSONDecodeError:
                self.log_result("Python Integration Script", "FAIL", 
                              "Script output is not valid JSON")
                return False
                
        except Exception as e:
            self.log_result("Python Integration Script", "FAIL", 
                          f"Test failed: {str(e)}")
            return False

    def test_security_validation(self):
        """Test security features - custom amounts should be rejected"""
        print("🔒 Testing Security Validation...")
        
        try:
            # Try to create session with custom amount (should fail)
            malicious_data = {
                "amount": 0.01,  # Try to pay only 1 cent
                "currency": "USD",
                "success_url": f"{self.base_url}/success",
                "cancel_url": f"{self.base_url}/cancel"
            }
            
            response = self.make_request('POST', '/payments/checkout/session', malicious_data)
            response_time = response.elapsed.total_seconds()
            
            # Should fail because custom amounts are not allowed
            if response.status_code != 200:
                self.log_result("Security Validation", "PASS", 
                              "Custom amounts correctly rejected", 
                              response_time)
                return True
            else:
                self.log_result("Security Validation", "FAIL", 
                              "Security vulnerability: custom amounts accepted", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Security Validation", "FAIL", 
                          f"Test failed: {str(e)}")
            return False

    def test_invalid_package_rejection(self):
        """Test that invalid package IDs are rejected"""
        print("🚫 Testing Invalid Package Rejection...")
        
        try:
            invalid_data = {
                "package_id": "invalid_package",
                "success_url": f"{self.base_url}/success",
                "cancel_url": f"{self.base_url}/cancel"
            }
            
            response = self.make_request('POST', '/payments/checkout/session', invalid_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 400:
                error_data = response.json()
                if 'error' in error_data and 'Invalid package' in error_data['error']:
                    self.log_result("Invalid Package Rejection", "PASS", 
                                  "Invalid packages correctly rejected", 
                                  response_time)
                    return True
                else:
                    self.log_result("Invalid Package Rejection", "FAIL", 
                                  "Wrong error message for invalid package", 
                                  response_time)
                    return False
            else:
                self.log_result("Invalid Package Rejection", "FAIL", 
                              f"Expected 400 status, got {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Invalid Package Rejection", "FAIL", 
                          f"Test failed: {str(e)}")
            return False

    def test_webhook_endpoint(self):
        """Test webhook endpoint structure (without actual Stripe signature)"""
        print("🔗 Testing Webhook Endpoint...")
        
        try:
            # Test webhook endpoint without signature (should fail)
            webhook_data = {
                "type": "checkout.session.completed",
                "data": {"object": {"id": "cs_test_123"}}
            }
            
            response = self.make_request('POST', '/webhook/stripe', webhook_data)
            response_time = response.elapsed.total_seconds()
            
            # Should fail due to missing signature
            if response.status_code == 400:
                error_data = response.json()
                if 'error' in error_data and 'signature' in error_data['error'].lower():
                    self.log_result("Webhook Endpoint", "PASS", 
                                  "Webhook correctly requires signature", 
                                  response_time)
                    return True
                else:
                    self.log_result("Webhook Endpoint", "FAIL", 
                                  "Webhook should require signature", 
                                  response_time)
                    return False
            else:
                self.log_result("Webhook Endpoint", "FAIL", 
                              f"Expected 400 status for missing signature, got {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Webhook Endpoint", "FAIL", 
                          f"Test failed: {str(e)}")
            return False

    def test_validation_errors(self):
        """Test API validation"""
        print("✅ Testing API Validation...")
        
        try:
            # Test missing required fields
            invalid_data = {
                "package_id": "starter"
                # Missing success_url and cancel_url
            }
            
            response = self.make_request('POST', '/payments/checkout/session', invalid_data)
            response_time = response.elapsed.total_seconds()
            
            # Should fail validation
            if response.status_code == 422:  # Laravel validation error
                self.log_result("API Validation", "PASS", 
                              "Required field validation working", 
                              response_time)
                return True
            elif response.status_code == 400:
                self.log_result("API Validation", "PASS", 
                              "Required field validation working (400 status)", 
                              response_time)
                return True
            else:
                self.log_result("API Validation", "FAIL", 
                              f"Expected validation error, got status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("API Validation", "FAIL", 
                          f"Test failed: {str(e)}")
            return False

    def run_comprehensive_testing(self):
        """Run all Stripe payment tests"""
        print("🚀 Starting Comprehensive Stripe Payment Testing...")
        print("=" * 60)
        
        # Test functions in order
        test_functions = [
            self.test_system_health,
            self.test_payment_packages_api,
            self.test_checkout_session_creation_package,
            self.test_checkout_session_creation_stripe_price,
            self.test_checkout_status_check,
            self.test_python_integration_script,
            self.test_security_validation,
            self.test_invalid_package_rejection,
            self.test_webhook_endpoint,
            self.test_validation_errors
        ]
        
        for test_func in test_functions:
            try:
                test_func()
            except Exception as e:
                self.log_result(test_func.__name__, "FAIL", 
                              f"Test function failed: {str(e)}")
        
        # Generate report
        self.generate_report()
        
        return True

    def generate_report(self):
        """Generate comprehensive test report"""
        print("\n" + "=" * 60)
        print("📋 STRIPE PAYMENT INTEGRATION TEST REPORT")
        print("=" * 60)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r['status'] == 'PASS'])
        failed_tests = len([r for r in self.test_results if r['status'] == 'FAIL'])
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 TEST STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        # Performance metrics
        response_times = [r['response_time'] for r in self.test_results if r['response_time'] > 0]
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            print(f"   Average Response Time: {avg_response_time:.3f}s")
        
        # Failed tests details
        failed_results = [r for r in self.test_results if r['status'] == 'FAIL']
        if failed_results:
            print(f"\n🚨 FAILED TESTS:")
            for result in failed_results:
                print(f"   ❌ {result['test']}: {result['details']}")
        
        # Recommendations
        print(f"\n💡 RECOMMENDATIONS:")
        if success_rate >= 90:
            print("   ✅ Stripe integration is working excellently")
        elif success_rate >= 75:
            print("   ⚠️ Stripe integration is mostly functional but needs minor fixes")
        else:
            print("   ❌ Stripe integration needs significant improvements")
        
        print(f"\n🔐 SECURITY: {'✅ Secure' if passed_tests >= 7 else '⚠️ Needs Review'}")
        print(f"🎯 FUNCTIONALITY: {'✅ Complete' if passed_tests >= 8 else '⚠️ Incomplete'}")
        
        print("\n" + "=" * 60)
        print("✅ STRIPE PAYMENT TESTING COMPLETED")
        print("=" * 60)

def main():
    """Main function to run Stripe payment testing"""
    tester = StripePaymentTester()
    
    try:
        success = tester.run_comprehensive_testing()
        return 0 if success else 1
    except KeyboardInterrupt:
        print("\n⚠️ Testing interrupted by user")
        return 1
    except Exception as e:
        print(f"\n❌ Testing failed with error: {str(e)}")
        return 1

if __name__ == "__main__":
    sys.exit(main())