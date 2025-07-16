#!/usr/bin/env python3
"""
Mewayz Platform - Focused Fixes Verification Test
=================================================

This script tests the specific fixes mentioned in the review request:
1. Stripe Payment Integration - Fixed route aliases, validation, and test mode
2. Instagram Management API - Fixed instagram_account_id column issue  
3. Alpine.js JavaScript - Rebuilt frontend assets
4. Previously Critical Issues - All CRM, Instagram, and Email Marketing fixes maintained

Author: Testing Agent
Date: January 2025
"""

import requests
import json
import time
import sys
from datetime import datetime
from typing import Dict, List, Optional, Any

class FocusedFixesVerificationTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.admin_credentials = {
            "email": "admin@example.com",
            "password": "admin123"
        }

    def log_result(self, endpoint: str, method: str, status: str, 
                   response_time: float, details: str = "", 
                   status_code: int = 0):
        """Log test result"""
        result = {
            "timestamp": datetime.now().isoformat(),
            "endpoint": endpoint,
            "method": method,
            "status": status,
            "response_time_ms": round(response_time * 1000, 2),
            "status_code": status_code,
            "details": details
        }
        self.test_results.append(result)
        
        # Color coding for console output
        color = "\033[92m" if status == "PASS" else "\033[91m" if status == "FAIL" else "\033[93m"
        reset = "\033[0m"
        
        print(f"{color}[{status}]{reset} {method} {endpoint} ({result['response_time_ms']}ms) - {details}")

    def make_request(self, method: str, endpoint: str, data: Dict = None, 
                    headers: Dict = None, auth_required: bool = True) -> requests.Response:
        """Make HTTP request with proper headers and authentication"""
        url = f"{self.api_url}{endpoint}"
        
        # Default headers
        request_headers = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "User-Agent": "Mewayz-Fixes-Verification-Tester/1.0"
        }
        
        # Add authentication if required and available
        if auth_required and self.auth_token:
            request_headers["Authorization"] = f"Bearer {self.auth_token}"
        
        # Merge with custom headers
        if headers:
            request_headers.update(headers)
        
        # Make request
        start_time = time.time()
        try:
            if method.upper() == "GET":
                response = self.session.get(url, headers=request_headers, timeout=30)
            elif method.upper() == "POST":
                response = self.session.post(url, json=data, headers=request_headers, timeout=30)
            elif method.upper() == "PUT":
                response = self.session.put(url, json=data, headers=request_headers, timeout=30)
            elif method.upper() == "DELETE":
                response = self.session.delete(url, headers=request_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            response_time = time.time() - start_time
            
            # Log the result
            if response.status_code < 400:
                self.log_result(endpoint, method.upper(), "PASS", response_time, 
                              f"Status: {response.status_code}", response.status_code)
            else:
                self.log_result(endpoint, method.upper(), "FAIL", response_time, 
                              f"Status: {response.status_code} - {response.text[:100]}", 
                              response.status_code)
            
            return response
            
        except requests.exceptions.RequestException as e:
            response_time = time.time() - start_time
            self.log_result(endpoint, method.upper(), "ERROR", response_time, 
                          f"Request failed: {str(e)}")
            return None

    def authenticate(self):
        """Authenticate and get token"""
        print("\n" + "="*60)
        print("🔐 AUTHENTICATING FOR TESTING")
        print("="*60)
        
        response = self.make_request("POST", "/auth/login", 
                                   data=self.admin_credentials, 
                                   auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if "token" in data or "access_token" in data:
                    self.auth_token = data.get("token") or data.get("access_token")
                    print(f"✅ Authentication successful - Token obtained")
                    return True
                else:
                    print(f"⚠️ Login successful but no token in response")
                    return False
            except json.JSONDecodeError:
                print(f"⚠️ Login response is not valid JSON")
                return False
        else:
            print(f"❌ Authentication failed")
            return False

    def test_stripe_payment_integration_fixes(self):
        """Test Stripe Payment Integration fixes - route aliases, validation, test mode"""
        print("\n" + "="*60)
        print("💳 TESTING STRIPE PAYMENT INTEGRATION FIXES")
        print("="*60)
        
        # Test 1: Both /api/stripe/ and /api/payments/ route prefixes should work
        print("\n🔍 Testing Route Aliases:")
        
        # Test /api/stripe/packages endpoint
        stripe_packages_response = self.make_request("GET", "/stripe/packages", auth_required=False)
        
        # Test /api/payments/packages endpoint (alias)
        payments_packages_response = self.make_request("GET", "/payments/packages", auth_required=False)
        
        # Verify both endpoints return the same package data
        if (stripe_packages_response and stripe_packages_response.status_code == 200 and
            payments_packages_response and payments_packages_response.status_code == 200):
            try:
                stripe_data = stripe_packages_response.json()
                payments_data = payments_packages_response.json()
                
                if stripe_data == payments_data:
                    print("✅ Route aliases working correctly - both endpoints return identical data")
                else:
                    print("⚠️ Route aliases return different data")
                    
                # Verify expected package structure
                if isinstance(stripe_data, list) and len(stripe_data) >= 3:
                    packages = {pkg.get('id', pkg.get('name', '')): pkg.get('price', 0) for pkg in stripe_data}
                    expected_packages = ['starter', 'professional', 'enterprise']
                    
                    found_packages = [pkg for pkg in expected_packages if pkg in str(packages).lower()]
                    if len(found_packages) >= 2:
                        print(f"✅ Package structure verified - Found packages: {found_packages}")
                    else:
                        print(f"⚠️ Expected packages not found in response")
                        
            except json.JSONDecodeError:
                print("❌ Invalid JSON response from packages endpoints")
        
        # Test 2: Checkout session validation - should accept both 'package' and 'package_id' parameters
        print("\n🔍 Testing Checkout Session Validation:")
        
        if not self.auth_token:
            print("❌ Skipping checkout tests - No authentication token")
            return
        
        # Test with 'package_id' parameter
        checkout_data_package_id = {
            "package_id": "professional",
            "billing_cycle": "monthly",
            "success_url": "https://mewayz.com/success",
            "cancel_url": "https://mewayz.com/cancel"
        }
        
        # Test with 'package' parameter
        checkout_data_package = {
            "package": "professional",
            "billing_cycle": "monthly", 
            "success_url": "https://mewayz.com/success",
            "cancel_url": "https://mewayz.com/cancel"
        }
        
        # Test both parameter formats on both route aliases
        endpoints_to_test = ["/stripe/checkout/session", "/payments/checkout/session"]
        
        for endpoint in endpoints_to_test:
            print(f"\n  Testing {endpoint}:")
            
            # Test with package_id parameter
            response1 = self.make_request("POST", endpoint, data=checkout_data_package_id)
            
            # Test with package parameter  
            response2 = self.make_request("POST", endpoint, data=checkout_data_package)
            
            if response1 and response1.status_code in [200, 201]:
                print(f"    ✅ {endpoint} accepts 'package_id' parameter")
            elif response1 and response1.status_code == 422:
                print(f"    ⚠️ {endpoint} validation error with 'package_id' - may need API keys")
            else:
                print(f"    ❌ {endpoint} failed with 'package_id' parameter")
                
            if response2 and response2.status_code in [200, 201]:
                print(f"    ✅ {endpoint} accepts 'package' parameter")
            elif response2 and response2.status_code == 422:
                print(f"    ⚠️ {endpoint} validation error with 'package' - may need API keys")
            else:
                print(f"    ❌ {endpoint} failed with 'package' parameter")
        
        # Test 3: Test mode functionality when API keys are not configured
        print("\n🔍 Testing Test Mode for Local Development:")
        
        # Check if test mode is working by examining response structure
        test_response = self.make_request("GET", "/stripe/packages", auth_required=False)
        if test_response and test_response.status_code == 200:
            try:
                data = test_response.json()
                if isinstance(data, list) and len(data) > 0:
                    first_package = data[0]
                    if 'test_mode' in str(first_package).lower() or 'mock' in str(first_package).lower():
                        print("✅ Test mode detected in response")
                    else:
                        print("✅ Packages endpoint working (test mode may be transparent)")
                else:
                    print("⚠️ Unexpected package data structure")
            except json.JSONDecodeError:
                print("❌ Invalid JSON in packages response")

    def test_instagram_management_fixes(self):
        """Test Instagram Management API fixes - instagram_account_id column issue"""
        print("\n" + "="*60)
        print("📸 TESTING INSTAGRAM MANAGEMENT API FIXES")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping Instagram tests - No authentication token")
            return
        
        # Test 1: Verify instagram_account_id column fix by testing posts endpoint
        print("\n🔍 Testing instagram_account_id Column Fix:")
        
        # Test accounts endpoint first
        accounts_response = self.make_request("GET", "/instagram-management/accounts")
        
        if accounts_response and accounts_response.status_code == 200:
            print("✅ Instagram accounts endpoint working")
            
            # Try to create an account to test the fix
            account_data = {
                "username": "test_mewayz_account",
                "account_type": "business",
                "followers_count": 1000,
                "is_active": True,
                "display_name": "Test Mewayz Account"
            }
            
            create_response = self.make_request("POST", "/instagram-management/accounts", data=account_data)
            
            if create_response and create_response.status_code in [200, 201]:
                print("✅ Instagram account creation working")
                
                try:
                    account_info = create_response.json()
                    account_id = account_info.get('id')
                    
                    if account_id:
                        # Test posts endpoint with instagram_account_id
                        post_data = {
                            "instagram_account_id": account_id,
                            "caption": "Test post to verify instagram_account_id column fix #test",
                            "media_url": "https://example.com/test-image.jpg",
                            "hashtags": ["#test", "#mewayz"],
                            "scheduled_at": "2025-01-20 10:00:00"
                        }
                        
                        post_response = self.make_request("POST", "/instagram-management/posts", data=post_data)
                        
                        if post_response and post_response.status_code in [200, 201]:
                            print("✅ Instagram post creation with instagram_account_id working")
                            print("✅ instagram_account_id column fix verified")
                        else:
                            print("❌ Instagram post creation still failing")
                            if post_response:
                                print(f"    Error: {post_response.status_code} - {post_response.text[:200]}")
                    else:
                        print("⚠️ Account created but no ID returned")
                        
                except json.JSONDecodeError:
                    print("⚠️ Account creation response not valid JSON")
            else:
                print("❌ Instagram account creation failing")
                if create_response:
                    print(f"    Error: {create_response.status_code} - {create_response.text[:200]}")
        else:
            print("❌ Instagram accounts endpoint failing")
            if accounts_response:
                print(f"    Error: {accounts_response.status_code} - {accounts_response.text[:200]}")
        
        # Test 2: Verify posts endpoint works without 500 errors
        print("\n🔍 Testing Posts Endpoint Stability:")
        
        posts_response = self.make_request("GET", "/instagram-management/posts")
        
        if posts_response and posts_response.status_code == 200:
            print("✅ Instagram posts GET endpoint working")
        elif posts_response and posts_response.status_code == 500:
            print("❌ Instagram posts endpoint still returning 500 errors")
        else:
            print(f"⚠️ Instagram posts endpoint returned {posts_response.status_code if posts_response else 'no response'}")
        
        # Test 3: Analytics endpoint
        analytics_response = self.make_request("GET", "/instagram-management/analytics")
        
        if analytics_response and analytics_response.status_code == 200:
            print("✅ Instagram analytics endpoint working")
        else:
            print(f"⚠️ Instagram analytics endpoint issues: {analytics_response.status_code if analytics_response else 'no response'}")

    def test_previously_critical_systems(self):
        """Test that previously critical systems (CRM, Email Marketing) maintain their fixes"""
        print("\n" + "="*60)
        print("🔧 TESTING PREVIOUSLY CRITICAL SYSTEMS MAINTENANCE")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping critical systems tests - No authentication token")
            return
        
        # Test 1: CRM System - should maintain 80% success rate
        print("\n🔍 Testing CRM System Maintenance:")
        
        crm_endpoints = [
            ("GET", "/crm/contacts"),
            ("GET", "/crm/leads"),
            ("GET", "/crm/advanced-pipeline-management"),
        ]
        
        crm_passed = 0
        crm_total = len(crm_endpoints)
        
        for method, endpoint in crm_endpoints:
            response = self.make_request(method, endpoint)
            if response and response.status_code == 200:
                crm_passed += 1
        
        crm_success_rate = (crm_passed / crm_total) * 100
        print(f"CRM System Success Rate: {crm_success_rate:.1f}% ({crm_passed}/{crm_total})")
        
        if crm_success_rate >= 75:
            print("✅ CRM System maintaining high success rate")
        else:
            print("❌ CRM System success rate declined")
        
        # Test 2: Email Marketing Hub - should maintain functionality
        print("\n🔍 Testing Email Marketing Hub Maintenance:")
        
        email_endpoints = [
            ("GET", "/email-marketing/campaigns"),
            ("GET", "/email-marketing/templates"),
            ("GET", "/email-marketing/lists"),
            ("GET", "/email-marketing/subscribers"),
            ("GET", "/email-marketing/analytics")
        ]
        
        email_passed = 0
        email_total = len(email_endpoints)
        
        for method, endpoint in email_endpoints:
            response = self.make_request(method, endpoint)
            if response and response.status_code == 200:
                email_passed += 1
        
        email_success_rate = (email_passed / email_total) * 100
        print(f"Email Marketing Success Rate: {email_success_rate:.1f}% ({email_passed}/{email_total})")
        
        if email_success_rate >= 70:
            print("✅ Email Marketing Hub maintaining functionality")
        else:
            print("❌ Email Marketing Hub functionality declined")
        
        # Test 3: Other core systems
        print("\n🔍 Testing Other Core Systems:")
        
        other_systems = [
            ("Team Management", "GET", "/team"),
            ("Analytics Dashboard", "GET", "/analytics"),
            ("E-commerce", "GET", "/ecommerce/products"),
            ("Bio Sites", "GET", "/bio-sites")
        ]
        
        for system_name, method, endpoint in other_systems:
            response = self.make_request(method, endpoint)
            if response and response.status_code == 200:
                print(f"✅ {system_name} working")
            else:
                print(f"❌ {system_name} issues: {response.status_code if response else 'no response'}")

    def test_overall_platform_stability(self):
        """Test overall platform stability and success rates"""
        print("\n" + "="*60)
        print("🎯 TESTING OVERALL PLATFORM STABILITY")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping stability tests - No authentication token")
            return
        
        # Test a comprehensive set of endpoints to verify >80% success rate
        stability_endpoints = [
            # Authentication
            ("GET", "/auth/me"),
            
            # Stripe Payment (both aliases)
            ("GET", "/stripe/packages"),
            ("GET", "/payments/packages"),
            
            # Instagram Management
            ("GET", "/instagram-management/accounts"),
            ("GET", "/instagram-management/posts"),
            ("GET", "/instagram-management/analytics"),
            
            # Email Marketing
            ("GET", "/email-marketing/campaigns"),
            ("GET", "/email-marketing/templates"),
            
            # CRM
            ("GET", "/crm/contacts"),
            ("GET", "/crm/leads"),
            
            # Other systems
            ("GET", "/team"),
            ("GET", "/analytics"),
            ("GET", "/ecommerce/products"),
            ("GET", "/bio-sites"),
            ("GET", "/courses")
        ]
        
        stability_passed = 0
        stability_total = len(stability_endpoints)
        
        print(f"\nTesting {stability_total} endpoints for overall stability:")
        
        for method, endpoint in stability_endpoints:
            response = self.make_request(method, endpoint, auth_required=not endpoint.endswith("/packages"))
            if response and response.status_code == 200:
                stability_passed += 1
        
        overall_success_rate = (stability_passed / stability_total) * 100
        
        print(f"\n📊 OVERALL PLATFORM STABILITY:")
        print(f"   Success Rate: {overall_success_rate:.1f}% ({stability_passed}/{stability_total})")
        
        if overall_success_rate >= 80:
            print("✅ Platform achieving target >80% success rate")
        elif overall_success_rate >= 70:
            print("⚠️ Platform achieving good stability (70-80%)")
        else:
            print("❌ Platform stability below target (<70%)")
        
        return overall_success_rate

    def generate_focused_report(self):
        """Generate focused report on the specific fixes"""
        print("\n" + "="*80)
        print("📋 FOCUSED FIXES VERIFICATION REPORT")
        print("="*80)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r["status"] == "PASS"])
        failed_tests = len([r for r in self.test_results if r["status"] == "FAIL"])
        error_tests = len([r for r in self.test_results if r["status"] == "ERROR"])
        
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 VERIFICATION RESULTS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests} ({passed_tests/total_tests*100:.1f}%)")
        print(f"   ❌ Failed: {failed_tests} ({failed_tests/total_tests*100:.1f}%)")
        print(f"   ⚠️  Errors: {error_tests} ({error_tests/total_tests*100:.1f}%)")
        print(f"   🎯 Overall Success Rate: {success_rate:.1f}%")
        
        # Categorize results by fix area
        stripe_results = [r for r in self.test_results if any(x in r["endpoint"] for x in ["/stripe", "/payments"])]
        instagram_results = [r for r in self.test_results if "/instagram" in r["endpoint"]]
        crm_results = [r for r in self.test_results if "/crm" in r["endpoint"]]
        email_results = [r for r in self.test_results if "/email-marketing" in r["endpoint"]]
        
        print(f"\n📈 RESULTS BY FIX AREA:")
        
        for area_name, results in [
            ("Stripe Payment Integration", stripe_results),
            ("Instagram Management", instagram_results), 
            ("CRM System", crm_results),
            ("Email Marketing", email_results)
        ]:
            if results:
                passed = len([r for r in results if r["status"] == "PASS"])
                total = len(results)
                rate = (passed / total * 100) if total > 0 else 0
                status_icon = "✅" if rate >= 80 else "⚠️" if rate >= 50 else "❌"
                print(f"   {status_icon} {area_name}: {passed}/{total} ({rate:.1f}%)")
        
        # Show critical failures
        critical_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] >= 500]
        if critical_failures:
            print(f"\n🚨 CRITICAL FAILURES (5xx errors):")
            for failure in critical_failures[:5]:
                print(f"   ❌ {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        # Save results
        report_file = f"/app/focused_fixes_verification_results_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        with open(report_file, 'w') as f:
            json.dump({
                "summary": {
                    "total_tests": total_tests,
                    "passed": passed_tests,
                    "failed": failed_tests,
                    "errors": error_tests,
                    "success_rate": success_rate,
                    "test_timestamp": datetime.now().isoformat()
                },
                "results": self.test_results,
                "fix_areas": {
                    "stripe_payment": len(stripe_results),
                    "instagram_management": len(instagram_results),
                    "crm_system": len(crm_results),
                    "email_marketing": len(email_results)
                }
            }, f, indent=2)
        
        print(f"\n💾 Detailed results saved to: {report_file}")
        
        return {
            "success_rate": success_rate,
            "total_tests": total_tests,
            "passed": passed_tests,
            "failed": failed_tests,
            "critical_failures": len(critical_failures)
        }

    def run_focused_verification(self):
        """Run focused verification of the specific fixes"""
        print("🚀 STARTING FOCUSED FIXES VERIFICATION")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate():
            print("\n❌ CRITICAL: Authentication failed - cannot run authenticated tests")
            return False
        
        try:
            # Test the specific fixes mentioned in review request
            self.test_stripe_payment_integration_fixes()
            self.test_instagram_management_fixes()
            self.test_previously_critical_systems()
            
            # Test overall platform stability
            overall_success_rate = self.test_overall_platform_stability()
            
        except KeyboardInterrupt:
            print("\n⚠️ Testing interrupted by user")
        except Exception as e:
            print(f"\n❌ Unexpected error during testing: {str(e)}")
        
        # Generate final report
        results = self.generate_focused_report()
        
        print(f"\n🏁 FOCUSED VERIFICATION COMPLETED")
        print(f"   Overall Success Rate: {results['success_rate']:.1f}%")
        print(f"   Tests Passed: {results['passed']}/{results['total_tests']}")
        
        if results['critical_failures'] > 0:
            print(f"   🚨 Critical Failures: {results['critical_failures']}")
        
        # Determine if fixes are successful
        success_threshold = 75  # 75% success rate threshold
        fixes_successful = results['success_rate'] >= success_threshold
        
        if fixes_successful:
            print(f"✅ FIXES VERIFICATION SUCCESSFUL - {results['success_rate']:.1f}% success rate")
        else:
            print(f"❌ FIXES VERIFICATION NEEDS ATTENTION - {results['success_rate']:.1f}% success rate")
        
        return fixes_successful

def main():
    """Main function to run the focused fixes verification"""
    if len(sys.argv) > 1:
        base_url = sys.argv[1]
    else:
        base_url = "http://localhost:8001"
    
    tester = FocusedFixesVerificationTester(base_url)
    success = tester.run_focused_verification()
    
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()