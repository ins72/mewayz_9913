#!/usr/bin/env python3
"""
Mewayz Platform Backend Review Testing Suite - FOCUSED
======================================================

This test suite focuses SPECIFICALLY on the issues mentioned in the review request:

1. **E-commerce Product CREATE operations** - Test if the status field integer fix resolved the product creation issue
2. **Stripe Payment Integration** - Test if the new Stripe test API keys resolve the checkout session creation 
3. **Course Management** - Verify the user_id assignment fix is working properly
4. **Bio Site Builder** - Verify the user_id assignment fix is working properly

Key API endpoints to test:
- POST /api/ecommerce/products - Product creation with status=1 (integer)
- POST /api/payments/checkout/session - Stripe checkout with new test keys
- POST /api/courses - Course creation with user_id assignment
- POST /api/bio-sites - Bio site creation with user_id assignment

Expected Results:
- Product creation should now succeed with status=1
- Stripe checkout sessions should be created successfully
- All CREATE operations should return proper JSON responses
- User_id should be automatically assigned to all created records
- No 500 errors should occur
"""

import os
import sys
import json
import time
import requests
from pathlib import Path
from datetime import datetime

class MewayzFocusedReviewTest:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_base = f"{self.base_url}/api"
        self.results = {
            "ecommerce_status_fix": {},
            "stripe_api_keys_fix": {},
            "course_user_id_fix": {},
            "bio_site_user_id_fix": {},
            "test_summary": {}
        }
        self.auth_token = None
        self.test_user_id = None
        
    def run_all_tests(self):
        """Run focused tests on the specific review request issues"""
        print("🎯 MEWAYZ BACKEND REVIEW - FOCUSED TESTING")
        print("=" * 60)
        print("Testing the 4 specific fixes mentioned in the review request")
        print("-" * 60)
        
        # Setup authentication first
        self.setup_authentication()
        
        # Test 1: E-commerce Product CREATE with status field integer fix
        self.test_ecommerce_status_fix()
        
        # Test 2: Stripe Payment Integration with new API keys
        self.test_stripe_api_keys_fix()
        
        # Test 3: Course Management user_id assignment fix
        self.test_course_user_id_fix()
        
        # Test 4: Bio Site Builder user_id assignment fix
        self.test_bio_site_user_id_fix()
        
        # Generate focused report
        self.generate_focused_report()
        
    def setup_authentication(self):
        """Setup authentication for protected endpoints"""
        print("\n🔐 AUTHENTICATION SETUP")
        print("-" * 40)
        
        try:
            # Health check first
            health_response = requests.get(f"{self.api_base}/health", timeout=10)
            if health_response.status_code == 200:
                print("✅ Backend health check passed")
            else:
                print(f"⚠️  Backend health check: {health_response.status_code}")
            
            # Login
            auth_data = {
                "email": "admin@example.com",
                "password": "admin123"
            }
            
            response = requests.post(f"{self.api_base}/auth/login", json=auth_data, timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if 'token' in data:
                    self.auth_token = data['token']
                    print("✅ Authentication token received")
                elif 'access_token' in data:
                    self.auth_token = data['access_token']
                    print("✅ Authentication token received")
                else:
                    print("❌ No token in response")
                    
                # Get user profile
                if self.auth_token:
                    headers = {'Authorization': f'Bearer {self.auth_token}'}
                    profile_response = requests.get(f"{self.api_base}/auth/me", headers=headers, timeout=10)
                    
                    if profile_response.status_code == 200:
                        profile_data = profile_response.json()
                        if 'id' in profile_data:
                            self.test_user_id = profile_data['id']
                            print(f"✅ User ID obtained: {self.test_user_id}")
                        elif 'user' in profile_data and 'id' in profile_data['user']:
                            self.test_user_id = profile_data['user']['id']
                            print(f"✅ User ID obtained: {self.test_user_id}")
                        else:
                            print("⚠️  User profile loaded but no ID found")
            else:
                print(f"❌ Authentication failed: {response.status_code}")
                
        except Exception as e:
            print(f"❌ Authentication error: {e}")
            
    def test_ecommerce_status_fix(self):
        """Test 1: E-commerce Product CREATE with status field integer fix"""
        print("\n🛒 TEST 1: E-COMMERCE STATUS FIELD INTEGER FIX")
        print("-" * 60)
        print("ISSUE: Product creation failing with status field - testing integer fix")
        
        results = {
            "fix_working": False,
            "status_integer_accepted": False,
            "product_created": False,
            "no_500_errors": False,
            "json_response": False,
            "user_id_assigned": False,
            "response_status": 0,
            "error_details": None
        }
        
        if not self.auth_token:
            print("❌ Skipping - no authentication token")
            self.results["ecommerce_status_fix"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            
            # Test with INTEGER status (the fix)
            product_data = {
                "name": "Status Fix Test Product",
                "description": "Testing status field integer fix",
                "price": 29.99,
                "currency": "USD",
                "stock_quantity": 100,
                "status": 1,  # INTEGER VALUE - THE FIX
                "category": "test"
            }
            
            print(f"🧪 Testing product creation with status={product_data['status']} (integer)")
            
            response = requests.post(
                f"{self.api_base}/ecommerce/products",
                json=product_data,
                headers=headers,
                timeout=15
            )
            
            results["response_status"] = response.status_code
            
            print(f"   Response Status: {response.status_code}")
            
            # Check if 500 error is resolved
            if response.status_code != 500:
                results["no_500_errors"] = True
                print("✅ No 500 errors - status field fix working!")
            else:
                results["no_500_errors"] = False
                print("❌ Still getting 500 errors")
                
            # Check for successful creation
            if response.status_code in [200, 201]:
                results["product_created"] = True
                results["status_integer_accepted"] = True
                results["fix_working"] = True
                print("✅ Product creation successful with integer status")
                
                try:
                    response_data = response.json()
                    results["json_response"] = True
                    
                    # Check user_id assignment
                    product_info = response_data.get('data') or response_data.get('product') or response_data
                    if isinstance(product_info, dict) and 'user_id' in product_info:
                        results["user_id_assigned"] = True
                        print(f"✅ User ID assigned: {product_info['user_id']}")
                    
                    print(f"   Product ID: {product_info.get('id', 'N/A')}")
                    
                except json.JSONDecodeError:
                    print("❌ Invalid JSON response")
                    
            else:
                print(f"❌ Product creation failed: {response.status_code}")
                
            # Store error details
            if response.status_code >= 400:
                try:
                    results["error_details"] = response.json()
                except:
                    results["error_details"] = response.text[:300]
                    
        except Exception as e:
            print(f"❌ Test failed: {e}")
            results["error_details"] = str(e)
            
        self.results["ecommerce_status_fix"] = results
        
    def test_stripe_api_keys_fix(self):
        """Test 2: Stripe Payment Integration with new API keys"""
        print("\n💳 TEST 2: STRIPE NEW API KEYS FIX")
        print("-" * 60)
        print("ISSUE: Stripe checkout session creation failing - testing new API keys")
        
        results = {
            "fix_working": False,
            "new_keys_working": False,
            "checkout_session_created": False,
            "no_500_errors": False,
            "session_id_returned": False,
            "checkout_url_returned": False,
            "response_status": 0,
            "error_details": None
        }
        
        try:
            # Test checkout session creation
            checkout_data = {
                "package_id": "starter",
                "success_url": f"{self.base_url}/success?session_id={{CHECKOUT_SESSION_ID}}",
                "cancel_url": f"{self.base_url}/cancel"
            }
            
            headers = {}
            if self.auth_token:
                headers['Authorization'] = f'Bearer {self.auth_token}'
            
            print("🧪 Testing Stripe checkout session creation with new API keys")
            
            response = requests.post(
                f"{self.api_base}/payments/checkout/session",
                json=checkout_data,
                headers=headers,
                timeout=15
            )
            
            results["response_status"] = response.status_code
            
            print(f"   Response Status: {response.status_code}")
            
            # Check if 500 error is resolved
            if response.status_code != 500:
                results["no_500_errors"] = True
                print("✅ No 500 errors - new API keys working!")
            else:
                results["no_500_errors"] = False
                print("❌ Still getting 500 errors")
                
            # Check for successful session creation
            if response.status_code in [200, 201]:
                results["checkout_session_created"] = True
                results["new_keys_working"] = True
                results["fix_working"] = True
                print("✅ Checkout session creation successful")
                
                try:
                    response_data = response.json()
                    
                    if response_data.get('success'):
                        if 'session_id' in response_data:
                            results["session_id_returned"] = True
                            print(f"✅ Session ID: {response_data['session_id'][:20]}...")
                            
                        if 'url' in response_data:
                            results["checkout_url_returned"] = True
                            print(f"✅ Checkout URL: {response_data['url'][:50]}...")
                            
                except json.JSONDecodeError:
                    print("❌ Invalid JSON response")
                    
            else:
                print(f"❌ Checkout session creation failed: {response.status_code}")
                
            # Store error details
            if response.status_code >= 400:
                try:
                    results["error_details"] = response.json()
                except:
                    results["error_details"] = response.text[:300]
                    
        except Exception as e:
            print(f"❌ Test failed: {e}")
            results["error_details"] = str(e)
            
        self.results["stripe_api_keys_fix"] = results
        
    def test_course_user_id_fix(self):
        """Test 3: Course Management user_id assignment fix"""
        print("\n📚 TEST 3: COURSE USER_ID ASSIGNMENT FIX")
        print("-" * 60)
        print("ISSUE: Course creation user_id assignment - testing fix")
        
        results = {
            "fix_working": False,
            "user_id_assigned": False,
            "course_created": False,
            "no_500_errors": False,
            "json_response": False,
            "correct_user_id": False,
            "response_status": 0,
            "error_details": None
        }
        
        if not self.auth_token:
            print("❌ Skipping - no authentication token")
            self.results["course_user_id_fix"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            
            course_data = {
                "title": "User ID Fix Test Course",
                "description": "Testing user_id assignment fix",
                "price": 99.99,
                "currency": "USD",
                "status": "draft"
            }
            
            print("🧪 Testing course creation with user_id assignment fix")
            
            response = requests.post(
                f"{self.api_base}/courses",
                json=course_data,
                headers=headers,
                timeout=15
            )
            
            results["response_status"] = response.status_code
            
            print(f"   Response Status: {response.status_code}")
            
            # Check if 500 error is resolved
            if response.status_code != 500:
                results["no_500_errors"] = True
                print("✅ No 500 errors")
            else:
                results["no_500_errors"] = False
                print("❌ Still getting 500 errors")
                
            # Check for successful creation
            if response.status_code in [200, 201]:
                results["course_created"] = True
                print("✅ Course creation successful")
                
                try:
                    response_data = response.json()
                    results["json_response"] = True
                    
                    # Check user_id assignment
                    course_info = response_data.get('data') or response_data.get('course') or response_data
                    if isinstance(course_info, dict) and 'user_id' in course_info:
                        results["user_id_assigned"] = True
                        results["fix_working"] = True
                        print(f"✅ User ID assigned: {course_info['user_id']}")
                        
                        # Check if it's the correct user ID
                        if str(course_info['user_id']) == str(self.test_user_id):
                            results["correct_user_id"] = True
                            print("✅ Correct user ID assigned")
                        else:
                            print(f"⚠️  User ID mismatch: expected {self.test_user_id}, got {course_info['user_id']}")
                    else:
                        print("❌ User ID not assigned")
                    
                    print(f"   Course ID: {course_info.get('id', 'N/A')}")
                    
                except json.JSONDecodeError:
                    print("❌ Invalid JSON response")
                    
            else:
                print(f"❌ Course creation failed: {response.status_code}")
                
            # Store error details
            if response.status_code >= 400:
                try:
                    results["error_details"] = response.json()
                except:
                    results["error_details"] = response.text[:300]
                    
        except Exception as e:
            print(f"❌ Test failed: {e}")
            results["error_details"] = str(e)
            
        self.results["course_user_id_fix"] = results
        
    def test_bio_site_user_id_fix(self):
        """Test 4: Bio Site Builder user_id assignment fix"""
        print("\n🌐 TEST 4: BIO SITE USER_ID ASSIGNMENT FIX")
        print("-" * 60)
        print("ISSUE: Bio site creation user_id assignment - testing fix")
        
        results = {
            "fix_working": False,
            "user_id_assigned": False,
            "bio_site_created": False,
            "no_500_errors": False,
            "json_response": False,
            "correct_user_id": False,
            "response_status": 0,
            "error_details": None
        }
        
        if not self.auth_token:
            print("❌ Skipping - no authentication token")
            self.results["bio_site_user_id_fix"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            
            bio_site_data = {
                "title": "User ID Fix Test Bio Site",
                "description": "Testing user_id assignment fix",
                "theme": "default",
                "is_active": True,
                "slug": f"user-id-fix-test-{int(time.time())}"
            }
            
            print("🧪 Testing bio site creation with user_id assignment fix")
            
            response = requests.post(
                f"{self.api_base}/bio-sites",
                json=bio_site_data,
                headers=headers,
                timeout=15
            )
            
            results["response_status"] = response.status_code
            
            print(f"   Response Status: {response.status_code}")
            
            # Check if 500 error is resolved
            if response.status_code != 500:
                results["no_500_errors"] = True
                print("✅ No 500 errors")
            else:
                results["no_500_errors"] = False
                print("❌ Still getting 500 errors")
                
            # Check for successful creation
            if response.status_code in [200, 201]:
                results["bio_site_created"] = True
                print("✅ Bio site creation successful")
                
                try:
                    response_data = response.json()
                    results["json_response"] = True
                    
                    # Check user_id assignment
                    bio_site_info = response_data.get('data') or response_data.get('bio_site') or response_data
                    if isinstance(bio_site_info, dict) and 'user_id' in bio_site_info:
                        results["user_id_assigned"] = True
                        results["fix_working"] = True
                        print(f"✅ User ID assigned: {bio_site_info['user_id']}")
                        
                        # Check if it's the correct user ID
                        if str(bio_site_info['user_id']) == str(self.test_user_id):
                            results["correct_user_id"] = True
                            print("✅ Correct user ID assigned")
                        else:
                            print(f"⚠️  User ID mismatch: expected {self.test_user_id}, got {bio_site_info['user_id']}")
                    else:
                        print("❌ User ID not assigned")
                    
                    print(f"   Bio Site ID: {bio_site_info.get('id', 'N/A')}")
                    
                except json.JSONDecodeError:
                    print("❌ Invalid JSON response")
                    
            else:
                print(f"❌ Bio site creation failed: {response.status_code}")
                
            # Store error details
            if response.status_code >= 400:
                try:
                    results["error_details"] = response.json()
                except:
                    results["error_details"] = response.text[:300]
                    
        except Exception as e:
            print(f"❌ Test failed: {e}")
            results["error_details"] = str(e)
            
        self.results["bio_site_user_id_fix"] = results
        
    def generate_focused_report(self):
        """Generate focused report on the 4 specific fixes"""
        print("\n📋 FOCUSED REVIEW REPORT - 4 SPECIFIC FIXES")
        print("=" * 60)
        
        # Count working fixes
        fixes_working = 0
        total_fixes = 4
        
        # Check each fix
        ecommerce_fix = self.results["ecommerce_status_fix"].get("fix_working", False)
        stripe_fix = self.results["stripe_api_keys_fix"].get("fix_working", False)
        course_fix = self.results["course_user_id_fix"].get("fix_working", False)
        bio_site_fix = self.results["bio_site_user_id_fix"].get("fix_working", False)
        
        if ecommerce_fix:
            fixes_working += 1
        if stripe_fix:
            fixes_working += 1
        if course_fix:
            fixes_working += 1
        if bio_site_fix:
            fixes_working += 1
        
        success_rate = (fixes_working / total_fixes) * 100
        
        print(f"🎯 FIXES WORKING: {fixes_working}/{total_fixes} ({success_rate:.1f}%)")
        print("-" * 40)
        
        # Detailed status for each fix
        print("\n🔍 DETAILED FIX STATUS:")
        
        print(f"\n1. 🛒 E-COMMERCE STATUS FIELD INTEGER FIX:")
        if ecommerce_fix:
            print("   ✅ WORKING - Product creation with status=1 (integer) successful")
        else:
            print("   ❌ FAILING - Status field integer fix not working")
            error = self.results["ecommerce_status_fix"].get("error_details")
            if error:
                print(f"   Error: {str(error)[:100]}...")
        
        print(f"\n2. 💳 STRIPE NEW API KEYS FIX:")
        if stripe_fix:
            print("   ✅ WORKING - Checkout session creation with new API keys successful")
        else:
            print("   ❌ FAILING - New Stripe API keys not working")
            error = self.results["stripe_api_keys_fix"].get("error_details")
            if error:
                print(f"   Error: {str(error)[:100]}...")
        
        print(f"\n3. 📚 COURSE USER_ID ASSIGNMENT FIX:")
        if course_fix:
            print("   ✅ WORKING - Course creation with user_id assignment successful")
        else:
            print("   ❌ FAILING - Course user_id assignment fix not working")
            error = self.results["course_user_id_fix"].get("error_details")
            if error:
                print(f"   Error: {str(error)[:100]}...")
        
        print(f"\n4. 🌐 BIO SITE USER_ID ASSIGNMENT FIX:")
        if bio_site_fix:
            print("   ✅ WORKING - Bio site creation with user_id assignment successful")
        else:
            print("   ❌ FAILING - Bio site user_id assignment fix not working")
            error = self.results["bio_site_user_id_fix"].get("error_details")
            if error:
                print(f"   Error: {str(error)[:100]}...")
        
        # Summary
        summary = {
            "fixes_working": fixes_working,
            "total_fixes": total_fixes,
            "success_rate": success_rate,
            "ecommerce_status_fix": ecommerce_fix,
            "stripe_api_keys_fix": stripe_fix,
            "course_user_id_fix": course_fix,
            "bio_site_user_id_fix": bio_site_fix,
            "test_timestamp": datetime.now().isoformat()
        }
        
        self.results["test_summary"] = summary
        
        # Final verdict
        print(f"\n💡 FINAL VERDICT:")
        if fixes_working == 4:
            print("🎉 EXCELLENT: All 4 critical fixes are working perfectly!")
        elif fixes_working == 3:
            print("✅ GOOD: 3 out of 4 fixes working, 1 issue remains")
        elif fixes_working == 2:
            print("⚠️  PARTIAL: 2 out of 4 fixes working, significant issues remain")
        elif fixes_working == 1:
            print("❌ POOR: Only 1 out of 4 fixes working, major issues persist")
        else:
            print("🚨 CRITICAL: None of the 4 fixes are working, immediate attention required")
        
        print(f"\n📊 Test completed at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Save results
        results_file = Path("/app/focused_review_test_results.json")
        with open(results_file, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)
        print(f"📄 Results saved to: {results_file}")

def main():
    """Main test execution"""
    print("🚀 Starting Focused Review Testing...")
    
    tester = MewayzFocusedReviewTest()
    tester.run_all_tests()
    
    print("\n✅ Focused testing completed!")
    return tester.results["test_summary"]["success_rate"]

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 75 else 1)
    except Exception as e:
        print(f"❌ Test execution failed: {e}")
        sys.exit(1)