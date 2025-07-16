#!/usr/bin/env python3
"""
Review Request Testing Suite - Course Management & Stripe Payment Integration
============================================================================

This test suite focuses on testing the specific fixes mentioned in the review request:

1. Course Management CREATE operations - Test if status field integer fix (status=0 for draft) resolved the course creation issue
2. Stripe Payment Integration - Test if corrected environment variable name (STRIPE_SECRET) resolved checkout session creation

Key API endpoints to test:
- POST /api/courses - Course creation with status=0 (integer)
- POST /api/payments/checkout/session - Stripe checkout with corrected API key reference

Expected Results:
- Course creation should now succeed with status=0 (draft)
- Stripe checkout sessions should be created successfully with the corrected API key
- All operations should return proper JSON responses
- No database type mismatch errors
- No "Invalid API Key" errors
"""

import os
import sys
import json
import time
import requests
from pathlib import Path

class ReviewRequestTester:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_base = f"{self.base_url}/api"
        self.auth_token = None
        self.results = {
            "course_management_test": {},
            "stripe_payment_test": {},
            "test_summary": {}
        }
        
    def run_all_tests(self):
        """Run focused tests for review request areas"""
        print("🎯 REVIEW REQUEST TESTING SUITE - COURSE MANAGEMENT & STRIPE PAYMENT")
        print("=" * 80)
        
        # Step 1: Get authentication token
        self.authenticate()
        
        # Step 2: Test Course Management CREATE operations
        self.test_course_management_create()
        
        # Step 3: Test Stripe Payment Integration
        self.test_stripe_payment_integration()
        
        # Step 4: Generate comprehensive report
        self.generate_test_report()
        
    def authenticate(self):
        """Get authentication token for API requests"""
        print("\n🔐 AUTHENTICATION SETUP")
        print("-" * 50)
        
        try:
            auth_data = {
                "email": "admin@example.com",
                "password": "admin123"
            }
            
            response = requests.post(f"{self.api_base}/auth/login", json=auth_data, timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if 'token' in data:
                    self.auth_token = data['token']
                    print("✅ Authentication successful")
                elif 'access_token' in data:
                    self.auth_token = data['access_token']
                    print("✅ Authentication successful")
                else:
                    print("❌ No token in response")
                    print(f"   Response: {data}")
            else:
                print(f"❌ Authentication failed: {response.status_code}")
                print(f"   Response: {response.text[:200]}")
                
        except Exception as e:
            print(f"❌ Authentication error: {e}")
            
    def test_course_management_create(self):
        """Test Course Management CREATE operations with status=0 (integer) fix"""
        print("\n📚 TEST 1: COURSE MANAGEMENT CREATE OPERATIONS")
        print("-" * 60)
        
        results = {
            "endpoint_accessible": False,
            "authentication_working": False,
            "course_creation_successful": False,
            "status_field_integer_fix": False,
            "proper_json_response": False,
            "no_database_errors": False,
            "user_id_assignment": False,
            "response_status": 0,
            "response_time": 0
        }
        
        if not self.auth_token:
            print("❌ Cannot test course creation - authentication failed")
            self.results["course_management_test"] = results
            return
            
        try:
            # Test data with proper integer status value
            course_data = {
                "name": "Advanced Web Development Course",
                "description": "A comprehensive course covering modern web development techniques including React, Node.js, and database integration.",
                "price": 199.99,
                "category": "Web Development",
                "thumbnail": "https://example.com/course-thumbnail.jpg",
                "level": "advanced"
                # Note: status=0 should be set automatically by the controller as integer
            }
            
            headers = {
                'Authorization': f'Bearer {self.auth_token}',
                'Content-Type': 'application/json'
            }
            
            print(f"🧪 Testing course creation with data:")
            print(f"   Name: {course_data['name']}")
            print(f"   Price: ${course_data['price']}")
            print(f"   Level: {course_data['level']}")
            print(f"   Expected status: 0 (integer, draft)")
            
            start_time = time.time()
            response = requests.post(
                f"{self.api_base}/courses",
                json=course_data,
                headers=headers,
                timeout=15
            )
            response_time = time.time() - start_time
            
            results["endpoint_accessible"] = True
            results["authentication_working"] = True
            results["response_status"] = response.status_code
            results["response_time"] = response_time
            
            print(f"   Status Code: {response.status_code}")
            print(f"   Response Time: {response_time:.3f}s")
            
            # Check if response is JSON
            try:
                data = response.json()
                results["proper_json_response"] = True
                print("   ✅ Response is valid JSON")
            except:
                print("   ❌ Response is not valid JSON")
                print(f"   Raw response: {response.text[:200]}")
                data = None
            
            if response.status_code == 201:
                results["course_creation_successful"] = True
                print("   ✅ Course creation successful (201 Created)")
                
                if data and data.get('success'):
                    course_info = data.get('data', {})
                    
                    # Check if status field is integer 0 (draft)
                    if 'status' in course_info:
                        status_value = course_info['status']
                        if status_value == 0 and isinstance(status_value, int):
                            results["status_field_integer_fix"] = True
                            print(f"   ✅ Status field integer fix working: status={status_value} (integer)")
                        else:
                            print(f"   ❌ Status field issue: status={status_value} (type: {type(status_value)})")
                    
                    # Check user_id assignment
                    if 'user_id' in course_info and course_info['user_id']:
                        results["user_id_assignment"] = True
                        print(f"   ✅ User ID assignment working: user_id={course_info['user_id']}")
                    else:
                        print("   ❌ User ID assignment issue")
                    
                    # Check for database errors in response
                    if 'SQLSTATE' not in str(data) and 'Incorrect integer value' not in str(data):
                        results["no_database_errors"] = True
                        print("   ✅ No database type mismatch errors")
                    else:
                        print("   ❌ Database errors detected in response")
                        
                    print(f"   📋 Course created with ID: {course_info.get('id', 'N/A')}")
                    
            elif response.status_code == 200:
                print("   ⚠️  Course creation returned 200 instead of 201")
                results["course_creation_successful"] = True
                
            else:
                print(f"   ❌ Course creation failed with status {response.status_code}")
                if data:
                    error_msg = data.get('message', data.get('error', 'Unknown error'))
                    print(f"   Error: {error_msg}")
                    
                    # Check for specific database errors
                    if 'SQLSTATE[22007]' in str(data) or 'Incorrect integer value' in str(data):
                        print("   ❌ CRITICAL: Database type mismatch error detected")
                        print("   This indicates the status field integer fix is NOT working")
                    else:
                        results["no_database_errors"] = True
                        
        except requests.exceptions.RequestException as e:
            print(f"   ❌ Request failed: {e}")
        except Exception as e:
            print(f"   ❌ Test failed: {e}")
            
        self.results["course_management_test"] = results
        
    def test_stripe_payment_integration(self):
        """Test Stripe Payment Integration with corrected STRIPE_SECRET environment variable"""
        print("\n💳 TEST 2: STRIPE PAYMENT INTEGRATION")
        print("-" * 60)
        
        results = {
            "packages_endpoint_working": False,
            "checkout_session_creation": False,
            "corrected_api_key_working": False,
            "no_invalid_api_key_errors": False,
            "proper_json_response": False,
            "session_id_returned": False,
            "checkout_url_returned": False,
            "response_status": 0,
            "response_time": 0
        }
        
        # Test 1: Get packages endpoint
        print("\n💰 Testing Stripe packages endpoint...")
        try:
            start_time = time.time()
            response = requests.get(f"{self.api_base}/payments/packages", timeout=10)
            response_time = time.time() - start_time
            
            print(f"   Status Code: {response.status_code}")
            print(f"   Response Time: {response_time:.3f}s")
            
            if response.status_code == 200:
                results["packages_endpoint_working"] = True
                data = response.json()
                results["proper_json_response"] = True
                
                packages = data.get('packages', {})
                print("   ✅ Packages endpoint working")
                print(f"   📦 Available packages: {list(packages.keys())}")
                
                # Verify expected packages and pricing
                expected_packages = {
                    'starter': 9.99,
                    'professional': 29.99,
                    'enterprise': 99.99
                }
                
                for package_id, expected_price in expected_packages.items():
                    if package_id in packages:
                        actual_price = packages[package_id].get('amount')
                        print(f"   ✅ {package_id}: ${actual_price} (expected: ${expected_price})")
                    else:
                        print(f"   ❌ Missing package: {package_id}")
            else:
                print(f"   ❌ Packages endpoint failed: {response.status_code}")
                
        except Exception as e:
            print(f"   ❌ Packages test failed: {e}")
        
        # Test 2: Create checkout session
        print("\n🛒 Testing Stripe checkout session creation...")
        try:
            checkout_data = {
                "package_id": "starter",
                "success_url": f"{self.base_url}/success?session_id={{CHECKOUT_SESSION_ID}}",
                "cancel_url": f"{self.base_url}/cancel",
                "metadata": {
                    "test": "true",
                    "package": "starter",
                    "review_request_test": "true"
                }
            }
            
            headers = {}
            if self.auth_token:
                headers['Authorization'] = f'Bearer {self.auth_token}'
            
            print(f"   Testing with package: {checkout_data['package_id']}")
            
            start_time = time.time()
            response = requests.post(
                f"{self.api_base}/payments/checkout/session",
                json=checkout_data,
                headers=headers,
                timeout=15
            )
            response_time = time.time() - start_time
            
            results["response_status"] = response.status_code
            results["response_time"] = response_time
            
            print(f"   Status Code: {response.status_code}")
            print(f"   Response Time: {response_time:.3f}s")
            
            # Check if response is JSON
            try:
                data = response.json()
                results["proper_json_response"] = True
                print("   ✅ Response is valid JSON")
            except:
                print("   ❌ Response is not valid JSON")
                print(f"   Raw response: {response.text[:200]}")
                data = None
            
            if response.status_code == 200:
                results["checkout_session_creation"] = True
                print("   ✅ Checkout session creation successful")
                
                if data and data.get('success'):
                    # Check for session ID
                    if 'session_id' in data:
                        results["session_id_returned"] = True
                        session_id = data['session_id']
                        print(f"   ✅ Session ID returned: {session_id[:20]}...")
                    
                    # Check for checkout URL
                    if 'url' in data:
                        results["checkout_url_returned"] = True
                        checkout_url = data['url']
                        print(f"   ✅ Checkout URL returned: {checkout_url[:50]}...")
                        
                        # Verify it's a Stripe checkout URL
                        if 'checkout.stripe.com' in checkout_url:
                            print("   ✅ Valid Stripe checkout URL format")
                        else:
                            print("   ⚠️  Unexpected checkout URL format")
                    
                    # Check for API key errors
                    error_msg = str(data).lower()
                    if 'invalid api key' not in error_msg and 'api key' not in error_msg:
                        results["no_invalid_api_key_errors"] = True
                        results["corrected_api_key_working"] = True
                        print("   ✅ No 'Invalid API Key' errors - STRIPE_SECRET fix working")
                    else:
                        print("   ❌ API key errors detected - STRIPE_SECRET fix not working")
                        
            else:
                print(f"   ❌ Checkout session creation failed: {response.status_code}")
                
                if data:
                    error_msg = data.get('error', data.get('message', 'Unknown error'))
                    print(f"   Error: {error_msg}")
                    
                    # Check specifically for API key errors
                    if 'Invalid API Key' in str(data) or 'api key' in str(data).lower():
                        print("   ❌ CRITICAL: Invalid API Key error detected")
                        print("   This indicates the STRIPE_SECRET environment variable fix is NOT working")
                    else:
                        results["no_invalid_api_key_errors"] = True
                        print("   ✅ No API key errors detected")
                        
        except requests.exceptions.RequestException as e:
            print(f"   ❌ Request failed: {e}")
        except Exception as e:
            print(f"   ❌ Test failed: {e}")
            
        self.results["stripe_payment_test"] = results
        
    def generate_test_report(self):
        """Generate comprehensive test report for review request"""
        print("\n📋 REVIEW REQUEST TEST REPORT")
        print("=" * 60)
        
        # Calculate scores
        course_test = self.results["course_management_test"]
        stripe_test = self.results["stripe_payment_test"]
        
        # Course Management Score
        course_score = 0
        course_total = 0
        for key, value in course_test.items():
            if isinstance(value, bool):
                course_total += 1
                if value:
                    course_score += 1
        
        course_percentage = (course_score / course_total * 100) if course_total > 0 else 0
        
        # Stripe Payment Score
        stripe_score = 0
        stripe_total = 0
        for key, value in stripe_test.items():
            if isinstance(value, bool):
                stripe_total += 1
                if value:
                    stripe_score += 1
        
        stripe_percentage = (stripe_score / stripe_total * 100) if stripe_total > 0 else 0
        
        # Overall Score
        overall_percentage = (course_percentage + stripe_percentage) / 2
        
        print(f"📚 Course Management CREATE Operations: {course_percentage:.1f}% ({course_score}/{course_total})")
        print(f"💳 Stripe Payment Integration: {stripe_percentage:.1f}% ({stripe_score}/{stripe_total})")
        print("-" * 40)
        print(f"🎯 OVERALL REVIEW REQUEST SCORE: {overall_percentage:.1f}%")
        
        # Detailed findings
        print("\n🔍 DETAILED FINDINGS:")
        
        # Course Management Analysis
        print("\n📚 COURSE MANAGEMENT CREATE OPERATIONS:")
        if course_test.get("course_creation_successful"):
            print("✅ Course creation endpoint working")
        else:
            print("❌ Course creation endpoint has issues")
            
        if course_test.get("status_field_integer_fix"):
            print("✅ Status field integer fix (status=0) WORKING")
        else:
            print("❌ Status field integer fix (status=0) NOT WORKING")
            
        if course_test.get("no_database_errors"):
            print("✅ No database type mismatch errors")
        else:
            print("❌ Database type mismatch errors detected")
            
        if course_test.get("user_id_assignment"):
            print("✅ User ID assignment working correctly")
        else:
            print("❌ User ID assignment issues")
        
        # Stripe Payment Analysis
        print("\n💳 STRIPE PAYMENT INTEGRATION:")
        if stripe_test.get("packages_endpoint_working"):
            print("✅ Stripe packages endpoint working")
        else:
            print("❌ Stripe packages endpoint has issues")
            
        if stripe_test.get("checkout_session_creation"):
            print("✅ Stripe checkout session creation working")
        else:
            print("❌ Stripe checkout session creation failed")
            
        if stripe_test.get("corrected_api_key_working"):
            print("✅ STRIPE_SECRET environment variable fix WORKING")
        else:
            print("❌ STRIPE_SECRET environment variable fix NOT WORKING")
            
        if stripe_test.get("no_invalid_api_key_errors"):
            print("✅ No 'Invalid API Key' errors")
        else:
            print("❌ 'Invalid API Key' errors detected")
        
        # Summary and recommendations
        print("\n💡 REVIEW REQUEST SUMMARY:")
        
        # Course Management Summary
        if course_test.get("status_field_integer_fix") and course_test.get("no_database_errors"):
            print("✅ COURSE MANAGEMENT: Status field integer fix (status=0) is working correctly")
        else:
            print("❌ COURSE MANAGEMENT: Status field integer fix (status=0) needs attention")
            
        # Stripe Payment Summary
        if stripe_test.get("corrected_api_key_working") and stripe_test.get("checkout_session_creation"):
            print("✅ STRIPE PAYMENT: STRIPE_SECRET environment variable fix is working correctly")
        else:
            print("❌ STRIPE PAYMENT: STRIPE_SECRET environment variable fix needs attention")
        
        # Overall recommendation
        if overall_percentage >= 90:
            print("\n🎉 EXCELLENT: Both critical fixes are working perfectly!")
        elif overall_percentage >= 80:
            print("\n✅ GOOD: Most fixes are working with minor issues.")
        elif overall_percentage >= 70:
            print("\n⚠️  FAIR: Some critical components need attention.")
        else:
            print("\n❌ NEEDS WORK: Significant issues found that require immediate attention.")
        
        # Save results
        summary = {
            "overall_score": overall_percentage,
            "course_management_score": course_percentage,
            "stripe_payment_score": stripe_percentage,
            "test_timestamp": time.strftime('%Y-%m-%d %H:%M:%S'),
            "course_status_fix_working": course_test.get("status_field_integer_fix", False),
            "stripe_secret_fix_working": stripe_test.get("corrected_api_key_working", False),
            "total_response_time": course_test.get("response_time", 0) + stripe_test.get("response_time", 0)
        }
        
        self.results["test_summary"] = summary
        
        print(f"\n📊 Test completed at: {time.strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"⚡ Total response time: {summary['total_response_time']:.3f}s")
        
        # Save results to file
        results_file = Path("/app/review_request_test_results.json")
        with open(results_file, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)
        print(f"📄 Detailed results saved to: {results_file}")

def main():
    """Main test execution"""
    print("🎯 Starting Review Request Testing - Course Management & Stripe Payment...")
    
    tester = ReviewRequestTester()
    tester.run_all_tests()
    
    print("\n✅ Review request testing completed!")
    return tester.results["test_summary"]["overall_score"]

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 80 else 1)
    except Exception as e:
        print(f"❌ Test execution failed: {e}")
        sys.exit(1)