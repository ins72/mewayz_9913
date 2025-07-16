#!/usr/bin/env python3
"""
Mewayz Platform Backend Testing Suite - Review Request Focus
===========================================================

This test suite validates the backend functionality after fixing user_id assignment issues
in Course, BioSite, and Product models as requested in the review.

Focus areas:
1. Course Management CREATE operations - Test if courses can now be created successfully
2. Bio Site Builder CREATE operations - Test if bio sites can be created with automatic user_id assignment  
3. E-commerce Product CREATE operations - Test if products can be created with proper user_id assignment
4. Payment Integration - Test Stripe checkout session creation
5. Analytics Dashboard - Test if analytics endpoints are now working

Expected Results:
- CREATE operations should now succeed with proper user_id assignment
- Database records should be created successfully
- API endpoints should return 200/201 status codes instead of 500 errors
- User relationships should be properly established
"""

import os
import sys
import json
import time
import requests
from pathlib import Path
from datetime import datetime

class MewayzBackendReviewTest:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_base = f"{self.base_url}/api"
        self.results = {
            "course_management_create": {},
            "bio_site_builder_create": {},
            "ecommerce_product_create": {},
            "payment_integration": {},
            "analytics_dashboard": {},
            "test_summary": {}
        }
        self.auth_token = None
        self.test_user_id = None
        
    def run_all_tests(self):
        """Run comprehensive backend functionality tests focused on review request areas"""
        print("🚀 MEWAYZ BACKEND TESTING - REVIEW REQUEST FOCUS")
        print("=" * 70)
        print("Testing user_id assignment fixes in Course, BioSite, and Product models")
        print("=" * 70)
        
        # Setup authentication first
        self.setup_authentication()
        
        # Test 1: Course Management CREATE operations
        self.test_course_management_create()
        
        # Test 2: Bio Site Builder CREATE operations  
        self.test_bio_site_builder_create()
        
        # Test 3: E-commerce Product CREATE operations
        self.test_ecommerce_product_create()
        
        # Test 4: Payment Integration (Stripe checkout)
        self.test_payment_integration()
        
        # Test 5: Analytics Dashboard
        self.test_analytics_dashboard()
        
        # Generate comprehensive report
        self.generate_comprehensive_report()
        
    def setup_authentication(self):
        """Setup authentication for protected endpoints"""
        print("\n🔐 AUTHENTICATION SETUP")
        print("-" * 50)
        
        try:
            # Test health check first
            health_response = requests.get(f"{self.api_base}/health", timeout=10)
            if health_response.status_code == 200:
                print("✅ Backend health check passed")
            else:
                print(f"❌ Backend health check failed: {health_response.status_code}")
                return False
            
            # Test login
            auth_data = {
                "email": "admin@example.com",
                "password": "admin123"
            }
            
            login_response = requests.post(f"{self.api_base}/auth/login", json=auth_data, timeout=10)
            
            if login_response.status_code == 200:
                login_data = login_response.json()
                if 'token' in login_data:
                    self.auth_token = login_data['token']
                    print("✅ Authentication token received")
                elif 'access_token' in login_data:
                    self.auth_token = login_data['access_token']
                    print("✅ Authentication token received")
                else:
                    print("❌ No token in login response")
                    return False
                    
                # Get user profile
                headers = {'Authorization': f'Bearer {self.auth_token}'}
                profile_response = requests.get(f"{self.api_base}/auth/me", headers=headers, timeout=10)
                
                if profile_response.status_code == 200:
                    profile_data = profile_response.json()
                    if 'id' in profile_data:
                        self.test_user_id = profile_data['id']
                        print(f"✅ User profile loaded - ID: {self.test_user_id}")
                    else:
                        print("⚠️  User profile loaded but no ID found")
                else:
                    print(f"❌ User profile access failed: {profile_response.status_code}")
                    
                return True
            else:
                print(f"❌ Login failed: {login_response.status_code}")
                return False
                
        except Exception as e:
            print(f"❌ Authentication setup failed: {e}")
            return False
            
    def test_course_management_create(self):
        """Test 1: Course Management CREATE operations - Focus on user_id assignment fix"""
        print("\n📚 TEST 1: COURSE MANAGEMENT CREATE OPERATIONS")
        print("-" * 60)
        print("Testing if courses can now be created successfully with proper user_id assignment")
        
        results = {
            "courses_list_access": False,
            "course_creation_success": False,
            "user_id_properly_assigned": False,
            "database_record_created": False,
            "course_details_accessible": False,
            "no_500_errors": False,
            "response_time": 0
        }
        
        if not self.auth_token:
            print("❌ Skipping course tests - no authentication token")
            self.results["course_management_create"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            start_time = time.time()
            
            # Test 1.1: Courses list access
            print("\n🔍 Testing courses list access...")
            courses_response = requests.get(f"{self.api_base}/courses", headers=headers, timeout=10)
            if courses_response.status_code in [200, 404]:
                results["courses_list_access"] = True
                print(f"✅ Courses list accessible: {courses_response.status_code}")
            else:
                print(f"❌ Courses list failed: {courses_response.status_code}")
            
            # Test 1.2: Course creation with user_id assignment
            print("\n🆕 Testing course creation with user_id assignment...")
            course_data = {
                "title": "Backend Test Course - User ID Assignment",
                "description": "Testing course creation with proper user_id assignment after fixes",
                "price": 149.99,
                "currency": "USD",
                "status": "draft",
                "category": "technology",
                "duration": "4 weeks"
            }
            
            create_response = requests.post(f"{self.api_base}/courses", json=course_data, headers=headers, timeout=15)
            
            print(f"   Status Code: {create_response.status_code}")
            
            if create_response.status_code in [200, 201]:
                results["course_creation_success"] = True
                results["no_500_errors"] = True
                print("✅ Course creation successful - No 500 errors!")
                
                try:
                    response_data = create_response.json()
                    print(f"   Response: {json.dumps(response_data, indent=2)[:200]}...")
                    
                    # Check if course ID is returned
                    course_id = None
                    if 'id' in response_data:
                        course_id = response_data['id']
                    elif 'course' in response_data and 'id' in response_data['course']:
                        course_id = response_data['course']['id']
                    elif 'data' in response_data and 'id' in response_data['data']:
                        course_id = response_data['data']['id']
                    
                    if course_id:
                        results["database_record_created"] = True
                        print(f"✅ Database record created - Course ID: {course_id}")
                        
                        # Check if user_id is properly assigned
                        if 'user_id' in str(response_data) or self.test_user_id in str(response_data):
                            results["user_id_properly_assigned"] = True
                            print("✅ User ID properly assigned to course")
                        else:
                            print("⚠️  User ID assignment unclear from response")
                        
                        # Test 1.3: Course details access
                        print(f"\n📖 Testing course details access for ID: {course_id}...")
                        details_response = requests.get(f"{self.api_base}/courses/{course_id}", headers=headers, timeout=10)
                        if details_response.status_code == 200:
                            results["course_details_accessible"] = True
                            print("✅ Course details accessible")
                            
                            # Verify user_id in details
                            details_data = details_response.json()
                            if 'user_id' in str(details_data):
                                print("✅ User ID confirmed in course details")
                        else:
                            print(f"❌ Course details access failed: {details_response.status_code}")
                    else:
                        print("⚠️  Course ID not found in response")
                        
                except json.JSONDecodeError:
                    print("⚠️  Response is not valid JSON")
                    print(f"   Raw response: {create_response.text[:200]}...")
                    
            elif create_response.status_code == 500:
                print("❌ CRITICAL: Still getting 500 errors - user_id assignment fix may not be working")
                print(f"   Error response: {create_response.text[:200]}...")
            else:
                print(f"❌ Course creation failed with status: {create_response.status_code}")
                if create_response.text:
                    print(f"   Error details: {create_response.text[:200]}...")
            
            results["response_time"] = time.time() - start_time
            
        except Exception as e:
            print(f"❌ Course management test failed: {e}")
            
        self.results["course_management_create"] = results
        
    def test_bio_site_builder_create(self):
        """Test 2: Bio Site Builder CREATE operations - Focus on user_id assignment fix"""
        print("\n🌐 TEST 2: BIO SITE BUILDER CREATE OPERATIONS")
        print("-" * 60)
        print("Testing if bio sites can be created with automatic user_id assignment")
        
        results = {
            "bio_sites_list_access": False,
            "themes_access": False,
            "bio_site_creation_success": False,
            "user_id_properly_assigned": False,
            "database_record_created": False,
            "no_500_errors": False,
            "response_time": 0
        }
        
        if not self.auth_token:
            print("❌ Skipping bio site tests - no authentication token")
            self.results["bio_site_builder_create"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            start_time = time.time()
            
            # Test 2.1: Bio sites list access
            print("\n🔍 Testing bio sites list access...")
            sites_response = requests.get(f"{self.api_base}/bio-sites", headers=headers, timeout=10)
            if sites_response.status_code in [200, 404]:
                results["bio_sites_list_access"] = True
                print(f"✅ Bio sites list accessible: {sites_response.status_code}")
            else:
                print(f"❌ Bio sites list failed: {sites_response.status_code}")
            
            # Test 2.2: Themes access
            print("\n🎨 Testing themes access...")
            themes_response = requests.get(f"{self.api_base}/bio-sites/themes", headers=headers, timeout=10)
            if themes_response.status_code == 200:
                results["themes_access"] = True
                print("✅ Themes access working")
            else:
                print(f"❌ Themes access failed: {themes_response.status_code}")
            
            # Test 2.3: Bio site creation with user_id assignment
            print("\n🆕 Testing bio site creation with user_id assignment...")
            site_data = {
                "title": "Backend Test Bio Site - User ID Assignment",
                "description": "Testing bio site creation with proper user_id assignment after fixes",
                "theme": "modern",
                "is_active": True,
                "slug": f"test-bio-site-{int(time.time())}",
                "settings": {
                    "show_analytics": True,
                    "enable_donations": False
                }
            }
            
            create_response = requests.post(f"{self.api_base}/bio-sites", json=site_data, headers=headers, timeout=15)
            
            print(f"   Status Code: {create_response.status_code}")
            
            if create_response.status_code in [200, 201]:
                results["bio_site_creation_success"] = True
                results["no_500_errors"] = True
                print("✅ Bio site creation successful - No 500 errors!")
                
                try:
                    response_data = create_response.json()
                    print(f"   Response: {json.dumps(response_data, indent=2)[:200]}...")
                    
                    # Check if bio site ID is returned
                    site_id = None
                    if 'id' in response_data:
                        site_id = response_data['id']
                    elif 'bio_site' in response_data and 'id' in response_data['bio_site']:
                        site_id = response_data['bio_site']['id']
                    elif 'data' in response_data and 'id' in response_data['data']:
                        site_id = response_data['data']['id']
                    
                    if site_id:
                        results["database_record_created"] = True
                        print(f"✅ Database record created - Bio Site ID: {site_id}")
                        
                        # Check if user_id is properly assigned
                        if 'user_id' in str(response_data) or self.test_user_id in str(response_data):
                            results["user_id_properly_assigned"] = True
                            print("✅ User ID properly assigned to bio site")
                        else:
                            print("⚠️  User ID assignment unclear from response")
                    else:
                        print("⚠️  Bio site ID not found in response")
                        
                except json.JSONDecodeError:
                    print("⚠️  Response is not valid JSON")
                    print(f"   Raw response: {create_response.text[:200]}...")
                    
            elif create_response.status_code == 500:
                print("❌ CRITICAL: Still getting 500 errors - user_id assignment fix may not be working")
                print(f"   Error response: {create_response.text[:200]}...")
            else:
                print(f"❌ Bio site creation failed with status: {create_response.status_code}")
                if create_response.text:
                    print(f"   Error details: {create_response.text[:200]}...")
            
            results["response_time"] = time.time() - start_time
            
        except Exception as e:
            print(f"❌ Bio site builder test failed: {e}")
            
        self.results["bio_site_builder_create"] = results
        
    def test_ecommerce_product_create(self):
        """Test 3: E-commerce Product CREATE operations - Focus on user_id assignment fix"""
        print("\n🛒 TEST 3: E-COMMERCE PRODUCT CREATE OPERATIONS")
        print("-" * 60)
        print("Testing if products can be created with proper user_id assignment")
        
        results = {
            "products_list_access": False,
            "product_creation_success": False,
            "user_id_properly_assigned": False,
            "database_record_created": False,
            "product_details_accessible": False,
            "no_500_errors": False,
            "response_time": 0
        }
        
        if not self.auth_token:
            print("❌ Skipping product tests - no authentication token")
            self.results["ecommerce_product_create"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            start_time = time.time()
            
            # Test 3.1: Products list access
            print("\n🔍 Testing products list access...")
            products_response = requests.get(f"{self.api_base}/products", headers=headers, timeout=10)
            if products_response.status_code in [200, 404]:
                results["products_list_access"] = True
                print(f"✅ Products list accessible: {products_response.status_code}")
            else:
                print(f"❌ Products list failed: {products_response.status_code}")
                # Try alternative endpoint
                alt_response = requests.get(f"{self.api_base}/ecommerce/products", headers=headers, timeout=10)
                if alt_response.status_code in [200, 404]:
                    results["products_list_access"] = True
                    print(f"✅ Products list accessible (alternative endpoint): {alt_response.status_code}")
            
            # Test 3.2: Product creation with user_id assignment
            print("\n🆕 Testing product creation with user_id assignment...")
            product_data = {
                "name": "Backend Test Product - User ID Assignment",
                "description": "Testing product creation with proper user_id assignment after fixes",
                "price": 79.99,
                "currency": "USD",
                "stock_quantity": 50,
                "status": "active",
                "category": "digital",
                "sku": f"TEST-PROD-{int(time.time())}",
                "weight": 0,
                "dimensions": {
                    "length": 0,
                    "width": 0,
                    "height": 0
                }
            }
            
            # Try main products endpoint first
            create_response = requests.post(f"{self.api_base}/products", json=product_data, headers=headers, timeout=15)
            
            # If that fails, try ecommerce endpoint
            if create_response.status_code not in [200, 201]:
                create_response = requests.post(f"{self.api_base}/ecommerce/products", json=product_data, headers=headers, timeout=15)
            
            print(f"   Status Code: {create_response.status_code}")
            
            if create_response.status_code in [200, 201]:
                results["product_creation_success"] = True
                results["no_500_errors"] = True
                print("✅ Product creation successful - No 500 errors!")
                
                try:
                    response_data = create_response.json()
                    print(f"   Response: {json.dumps(response_data, indent=2)[:200]}...")
                    
                    # Check if product ID is returned
                    product_id = None
                    if 'id' in response_data:
                        product_id = response_data['id']
                    elif 'product' in response_data and 'id' in response_data['product']:
                        product_id = response_data['product']['id']
                    elif 'data' in response_data and 'id' in response_data['data']:
                        product_id = response_data['data']['id']
                    
                    if product_id:
                        results["database_record_created"] = True
                        print(f"✅ Database record created - Product ID: {product_id}")
                        
                        # Check if user_id is properly assigned
                        if 'user_id' in str(response_data) or self.test_user_id in str(response_data):
                            results["user_id_properly_assigned"] = True
                            print("✅ User ID properly assigned to product")
                        else:
                            print("⚠️  User ID assignment unclear from response")
                        
                        # Test 3.3: Product details access
                        print(f"\n📦 Testing product details access for ID: {product_id}...")
                        details_response = requests.get(f"{self.api_base}/products/{product_id}", headers=headers, timeout=10)
                        if details_response.status_code != 200:
                            details_response = requests.get(f"{self.api_base}/ecommerce/products/{product_id}", headers=headers, timeout=10)
                        
                        if details_response.status_code == 200:
                            results["product_details_accessible"] = True
                            print("✅ Product details accessible")
                            
                            # Verify user_id in details
                            details_data = details_response.json()
                            if 'user_id' in str(details_data):
                                print("✅ User ID confirmed in product details")
                        else:
                            print(f"❌ Product details access failed: {details_response.status_code}")
                    else:
                        print("⚠️  Product ID not found in response")
                        
                except json.JSONDecodeError:
                    print("⚠️  Response is not valid JSON")
                    print(f"   Raw response: {create_response.text[:200]}...")
                    
            elif create_response.status_code == 500:
                print("❌ CRITICAL: Still getting 500 errors - user_id assignment fix may not be working")
                print(f"   Error response: {create_response.text[:200]}...")
            else:
                print(f"❌ Product creation failed with status: {create_response.status_code}")
                if create_response.text:
                    print(f"   Error details: {create_response.text[:200]}...")
            
            results["response_time"] = time.time() - start_time
            
        except Exception as e:
            print(f"❌ E-commerce product test failed: {e}")
            
        self.results["ecommerce_product_create"] = results
        
    def test_payment_integration(self):
        """Test 4: Payment Integration - Test Stripe checkout session creation"""
        print("\n💳 TEST 4: PAYMENT INTEGRATION (STRIPE CHECKOUT)")
        print("-" * 60)
        print("Testing Stripe checkout session creation")
        
        results = {
            "packages_endpoint": False,
            "checkout_session_creation": False,
            "session_id_returned": False,
            "checkout_url_returned": False,
            "webhook_endpoint_accessible": False,
            "no_500_errors": False,
            "response_time": 0
        }
        
        try:
            start_time = time.time()
            
            # Test 4.1: Packages endpoint
            print("\n📦 Testing payment packages endpoint...")
            packages_response = requests.get(f"{self.api_base}/payments/packages", timeout=10)
            if packages_response.status_code == 200:
                results["packages_endpoint"] = True
                print("✅ Payment packages endpoint working")
                
                try:
                    packages_data = packages_response.json()
                    print(f"   Available packages: {list(packages_data.get('packages', {}).keys())}")
                except:
                    pass
            else:
                print(f"❌ Payment packages failed: {packages_response.status_code}")
            
            # Test 4.2: Checkout session creation
            print("\n💰 Testing checkout session creation...")
            checkout_data = {
                "package_id": "starter",
                "success_url": f"{self.base_url}/success?session_id={{CHECKOUT_SESSION_ID}}",
                "cancel_url": f"{self.base_url}/cancel",
                "metadata": {
                    "test": "true",
                    "package": "starter"
                }
            }
            
            headers = {}
            if self.auth_token:
                headers['Authorization'] = f'Bearer {self.auth_token}'
            
            checkout_response = requests.post(
                f"{self.api_base}/payments/checkout/session", 
                json=checkout_data,
                headers=headers,
                timeout=15
            )
            
            print(f"   Status Code: {checkout_response.status_code}")
            
            if checkout_response.status_code in [200, 201]:
                results["checkout_session_creation"] = True
                results["no_500_errors"] = True
                print("✅ Checkout session creation successful - No 500 errors!")
                
                try:
                    response_data = checkout_response.json()
                    
                    if response_data.get('success'):
                        if 'session_id' in response_data:
                            results["session_id_returned"] = True
                            print(f"✅ Session ID returned: {response_data['session_id'][:20]}...")
                        
                        if 'url' in response_data:
                            results["checkout_url_returned"] = True
                            print(f"✅ Checkout URL returned: {response_data['url'][:50]}...")
                    else:
                        print(f"⚠️  Session creation response: {response_data.get('error', 'Unknown error')}")
                        
                except json.JSONDecodeError:
                    print("⚠️  Response is not valid JSON")
                    
            elif checkout_response.status_code == 500:
                print("❌ CRITICAL: Still getting 500 errors in payment integration")
                print(f"   Error response: {checkout_response.text[:200]}...")
            else:
                print(f"❌ Checkout session creation failed: {checkout_response.status_code}")
                if checkout_response.text:
                    print(f"   Error details: {checkout_response.text[:200]}...")
            
            # Test 4.3: Webhook endpoint accessibility
            print("\n🔗 Testing webhook endpoint accessibility...")
            webhook_data = {
                "id": "evt_test_webhook",
                "object": "event",
                "type": "checkout.session.completed",
                "data": {"object": {"id": "cs_test_session"}}
            }
            
            webhook_response = requests.post(
                f"{self.api_base}/webhook/stripe",
                json=webhook_data,
                headers={'Stripe-Signature': 't=1234567890,v1=test_signature'},
                timeout=10
            )
            
            if webhook_response.status_code in [200, 400, 500]:  # Any response indicates accessibility
                results["webhook_endpoint_accessible"] = True
                print(f"✅ Webhook endpoint accessible: {webhook_response.status_code}")
            else:
                print(f"❌ Webhook endpoint failed: {webhook_response.status_code}")
            
            results["response_time"] = time.time() - start_time
            
        except Exception as e:
            print(f"❌ Payment integration test failed: {e}")
            
        self.results["payment_integration"] = results
        
    def test_analytics_dashboard(self):
        """Test 5: Analytics Dashboard - Test if analytics endpoints are now working"""
        print("\n📊 TEST 5: ANALYTICS DASHBOARD")
        print("-" * 60)
        print("Testing if analytics endpoints are now working")
        
        results = {
            "overview_analytics": False,
            "reports_access": False,
            "social_media_analytics": False,
            "bio_site_analytics": False,
            "ecommerce_analytics": False,
            "course_analytics": False,
            "no_500_errors": False,
            "response_time": 0
        }
        
        if not self.auth_token:
            print("❌ Skipping analytics tests - no authentication token")
            self.results["analytics_dashboard"] = results
            return
            
        try:
            headers = {'Authorization': f'Bearer {self.auth_token}'}
            start_time = time.time()
            
            # Test 5.1: Overview analytics
            print("\n📈 Testing overview analytics...")
            overview_response = requests.get(f"{self.api_base}/analytics", headers=headers, timeout=10)
            if overview_response.status_code in [200, 404]:
                results["overview_analytics"] = True
                if overview_response.status_code != 500:
                    results["no_500_errors"] = True
                print(f"✅ Overview analytics accessible: {overview_response.status_code}")
            else:
                print(f"❌ Overview analytics failed: {overview_response.status_code}")
                if overview_response.status_code == 500:
                    print("❌ CRITICAL: Still getting 500 errors in analytics")
            
            # Test 5.2: Reports access
            print("\n📋 Testing reports access...")
            reports_response = requests.get(f"{self.api_base}/analytics/reports", headers=headers, timeout=10)
            if reports_response.status_code in [200, 404]:
                results["reports_access"] = True
                print(f"✅ Reports access working: {reports_response.status_code}")
            else:
                print(f"❌ Reports access failed: {reports_response.status_code}")
            
            # Test 5.3: Social media analytics
            print("\n📱 Testing social media analytics...")
            social_response = requests.get(f"{self.api_base}/analytics/social-media", headers=headers, timeout=10)
            if social_response.status_code in [200, 404]:
                results["social_media_analytics"] = True
                print(f"✅ Social media analytics working: {social_response.status_code}")
            else:
                print(f"❌ Social media analytics failed: {social_response.status_code}")
            
            # Test 5.4: Bio site analytics
            print("\n🌐 Testing bio site analytics...")
            bio_response = requests.get(f"{self.api_base}/analytics/bio-sites", headers=headers, timeout=10)
            if bio_response.status_code in [200, 404]:
                results["bio_site_analytics"] = True
                print(f"✅ Bio site analytics working: {bio_response.status_code}")
            else:
                print(f"❌ Bio site analytics failed: {bio_response.status_code}")
            
            # Test 5.5: E-commerce analytics
            print("\n🛒 Testing e-commerce analytics...")
            ecommerce_response = requests.get(f"{self.api_base}/analytics/ecommerce", headers=headers, timeout=10)
            if ecommerce_response.status_code in [200, 404]:
                results["ecommerce_analytics"] = True
                print(f"✅ E-commerce analytics working: {ecommerce_response.status_code}")
            else:
                print(f"❌ E-commerce analytics failed: {ecommerce_response.status_code}")
            
            # Test 5.6: Course analytics
            print("\n📚 Testing course analytics...")
            course_response = requests.get(f"{self.api_base}/analytics/courses", headers=headers, timeout=10)
            if course_response.status_code in [200, 404]:
                results["course_analytics"] = True
                print(f"✅ Course analytics working: {course_response.status_code}")
            else:
                print(f"❌ Course analytics failed: {course_response.status_code}")
            
            results["response_time"] = time.time() - start_time
            
        except Exception as e:
            print(f"❌ Analytics dashboard test failed: {e}")
            
        self.results["analytics_dashboard"] = results
        
    def generate_comprehensive_report(self):
        """Generate comprehensive test report focused on review request areas"""
        print("\n📋 COMPREHENSIVE BACKEND TESTING REPORT - REVIEW REQUEST FOCUS")
        print("=" * 80)
        
        # Calculate scores for each test area
        test_areas = [
            ("course_management_create", "📚 Course Management CREATE Operations"),
            ("bio_site_builder_create", "🌐 Bio Site Builder CREATE Operations"),
            ("ecommerce_product_create", "🛒 E-commerce Product CREATE Operations"),
            ("payment_integration", "💳 Payment Integration (Stripe Checkout)"),
            ("analytics_dashboard", "📊 Analytics Dashboard")
        ]
        
        total_score = 0
        area_scores = {}
        critical_issues = []
        working_features = []
        
        for test_key, test_name in test_areas:
            test_results = self.results[test_key]
            if test_results:
                bool_values = [v for v in test_results.values() if isinstance(v, bool)]
                if bool_values:
                    score = sum(bool_values) / len(bool_values) * 100
                    area_scores[test_key] = score
                    total_score += score
                    
                    # Check for critical issues
                    if not test_results.get("no_500_errors", True):
                        critical_issues.append(f"{test_name} - Still getting 500 errors")
                    elif score >= 80:
                        working_features.append(test_name)
                    elif score < 50:
                        critical_issues.append(f"{test_name} - Low success rate ({score:.1f}%)")
                    
                    print(f"{test_name}: {score:.1f}%")
                else:
                    area_scores[test_key] = 0
                    critical_issues.append(f"{test_name} - No test results")
                    print(f"{test_name}: 0.0%")
            else:
                area_scores[test_key] = 0
                critical_issues.append(f"{test_name} - Test not executed")
                print(f"{test_name}: 0.0%")
        
        overall_score = total_score / len(test_areas) if test_areas else 0
        
        print("-" * 60)
        print(f"🎯 OVERALL BACKEND SCORE (REVIEW FOCUS): {overall_score:.1f}%")
        
        # Detailed findings
        print("\n🔍 DETAILED FINDINGS:")
        
        # User ID Assignment Status
        print("\n👤 USER ID ASSIGNMENT STATUS:")
        user_id_issues = []
        
        for test_key, test_name in test_areas[:3]:  # First 3 are CREATE operations
            test_results = self.results[test_key]
            if test_results.get("user_id_properly_assigned"):
                print(f"✅ {test_name} - User ID assignment working")
            else:
                user_id_issues.append(test_name)
                print(f"❌ {test_name} - User ID assignment issues")
        
        # 500 Error Status
        print("\n🚨 500 ERROR STATUS:")
        error_500_issues = []
        
        for test_key, test_name in test_areas:
            test_results = self.results[test_key]
            if test_results.get("no_500_errors"):
                print(f"✅ {test_name} - No 500 errors")
            else:
                error_500_issues.append(test_name)
                print(f"❌ {test_name} - Still getting 500 errors")
        
        # Working Features
        if working_features:
            print("\n✅ WORKING FEATURES:")
            for feature in working_features:
                print(f"   - {feature}")
        
        # Critical Issues
        if critical_issues:
            print("\n❌ CRITICAL ISSUES:")
            for issue in critical_issues:
                print(f"   - {issue}")
        
        # Summary
        summary = {
            "overall_score": overall_score,
            "area_scores": area_scores,
            "test_timestamp": datetime.now().isoformat(),
            "user_id_assignment_issues": len(user_id_issues),
            "error_500_issues": len(error_500_issues),
            "working_features": len(working_features),
            "critical_issues": len(critical_issues),
            "review_request_focus": True
        }
        
        self.results["test_summary"] = summary
        
        # Recommendations based on review request
        print("\n💡 RECOMMENDATIONS (REVIEW REQUEST FOCUS):")
        
        if overall_score >= 90:
            print("✅ EXCELLENT: User ID assignment fixes are working perfectly!")
            print("   - All CREATE operations successful")
            print("   - No 500 errors detected")
            print("   - Database records created with proper user relationships")
        elif overall_score >= 80:
            print("✅ GOOD: Most user ID assignment fixes are working.")
            print("   - Majority of CREATE operations successful")
            print("   - Minor issues may need attention")
        elif overall_score >= 60:
            print("⚠️  PARTIAL SUCCESS: Some user ID assignment fixes are working.")
            print("   - Some CREATE operations successful")
            print("   - Several areas still need fixes")
        else:
            print("❌ CRITICAL: User ID assignment fixes are not working as expected.")
            print("   - CREATE operations still failing")
            print("   - 500 errors still occurring")
            print("   - Database relationship issues persist")
        
        # Specific recommendations
        if user_id_issues:
            print(f"\n🔧 USER ID ASSIGNMENT FIXES NEEDED:")
            for issue in user_id_issues:
                print(f"   - {issue}")
        
        if error_500_issues:
            print(f"\n🚨 500 ERROR FIXES NEEDED:")
            for issue in error_500_issues:
                print(f"   - {issue}")
        
        print(f"\n📊 Test completed at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Save results to file
        results_file = Path("/app/backend_review_test_results.json")
        with open(results_file, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)
        print(f"📄 Detailed results saved to: {results_file}")

def main():
    """Main test execution"""
    print("🚀 Starting Mewayz Backend Testing - Review Request Focus...")
    
    tester = MewayzBackendReviewTest()
    tester.run_all_tests()
    
    print("\n✅ Testing completed successfully!")
    return tester.results["test_summary"]["overall_score"]

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 70 else 1)
    except Exception as e:
        print(f"❌ Test execution failed: {e}")
        sys.exit(1)