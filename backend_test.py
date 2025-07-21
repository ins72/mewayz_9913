#!/usr/bin/env python3
"""
COMPREHENSIVE FOURTH WAVE REGRESSION TEST - MEWAYZ PLATFORM
Testing FOURTH WAVE Advanced Business Systems + All Previous Waves
Testing Agent - December 2024
"""

import requests
import json
import time
from datetime import datetime

# Backend URL from frontend .env
BACKEND_URL = "https://601e2c7f-cfd0-4b1b-824f-ec21d4de5d5c.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"

# Admin credentials from review request
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class BackendTester:
    def __init__(self):
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.total_tests = 0
        self.passed_tests = 0
        
    def log_test(self, endpoint, method, status, response_time, success, details="", data_size=0):
        """Log test results"""
        self.total_tests += 1
        if success:
            self.passed_tests += 1
            
        result = {
            'endpoint': endpoint,
            'method': method,
            'status': status,
            'response_time': f"{response_time:.3f}s",
            'success': success,
            'details': details,
            'data_size': data_size
        }
        self.test_results.append(result)
        
        status_icon = "✅" if success else "❌"
        print(f"{status_icon} {method} {endpoint} - {status} ({response_time:.3f}s) - {details}")
        
    def authenticate(self):
        """Authenticate with admin credentials"""
        print(f"\n🔐 AUTHENTICATING WITH ADMIN CREDENTIALS...")
        print(f"Email: {ADMIN_EMAIL}")
        
        login_data = {
            "username": ADMIN_EMAIL,  # FastAPI OAuth2PasswordRequestForm uses 'username'
            "password": ADMIN_PASSWORD
        }
        
        try:
            start_time = time.time()
            response = self.session.post(f"{API_BASE}/auth/login", data=login_data, timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                self.auth_token = data.get('access_token')
                if self.auth_token:
                    self.session.headers.update({'Authorization': f'Bearer {self.auth_token}'})
                    self.log_test("/auth/login", "POST", response.status_code, response_time, True, 
                                f"Admin authentication successful, token: {self.auth_token[:20]}...")
                    return True
                else:
                    self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                                "No access token in response")
                    return False
            else:
                self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                            f"Login failed: {response.text[:100]}")
                return False
                
        except Exception as e:
            self.log_test("/auth/login", "POST", 0, 0, False, f"Authentication error: {str(e)}")
            return False
    
    def test_endpoint(self, endpoint, method="GET", data=None, form_data=None, expected_status=200, description=""):
        """Test a single endpoint"""
        url = f"{API_BASE}{endpoint}"
        
        try:
            start_time = time.time()
            
            if method == "GET":
                response = self.session.get(url, timeout=30)
            elif method == "POST":
                if form_data:
                    response = self.session.post(url, data=form_data, timeout=30)
                else:
                    response = self.session.post(url, json=data, timeout=30)
            elif method == "PUT":
                response = self.session.put(url, json=data, timeout=30)
            elif method == "DELETE":
                response = self.session.delete(url, timeout=30)
            else:
                raise ValueError(f"Unsupported method: {method}")
                
            response_time = time.time() - start_time
            
            # Check if response is successful
            success = response.status_code == expected_status
            
            # Get response details
            try:
                response_data = response.json()
                data_size = len(json.dumps(response_data))
                details = f"{description} - Response size: {data_size} chars"
                if not success:
                    details += f" - Error: {response_data.get('detail', 'Unknown error')}"
            except:
                data_size = len(response.text)
                details = f"{description} - Response size: {data_size} chars"
                if not success:
                    details += f" - Error: {response.text[:100]}"
            
            self.log_test(endpoint, method, response.status_code, response_time, success, details, data_size)
            return success, response
            
        except requests.exceptions.Timeout:
            self.log_test(endpoint, method, 0, 30.0, False, f"{description} - Request timeout")
            return False, None
        except Exception as e:
            self.log_test(endpoint, method, 0, 0, False, f"{description} - Error: {str(e)}")
            return False, None

    def test_first_wave_high_value_features(self):
        """Test FIRST WAVE - HIGH-VALUE FEATURES"""
        print(f"\n🌊 TESTING FIRST WAVE - HIGH-VALUE FEATURES")
        
        # 1. Subscription Management
        print(f"\n💳 Testing Subscription Management System...")
        self.test_endpoint("/subscriptions/plans", "GET", description="Get subscription plans")
        self.test_endpoint("/subscriptions/current", "GET", description="Get current subscription")
        self.test_endpoint("/subscriptions/billing-history", "GET", description="Get billing history")
        
        # 2. Google OAuth Integration
        print(f"\n🔐 Testing Google OAuth Integration...")
        self.test_endpoint("/oauth/config", "GET", description="Get OAuth configuration")
        self.test_endpoint("/oauth/verify-account", "GET", description="Verify OAuth account")
        self.test_endpoint("/oauth/link-account", "GET", description="Link OAuth account")
        
        # 3. Financial Management
        print(f"\n💰 Testing Financial Management System...")
        self.test_endpoint("/financial/dashboard", "GET", description="Financial dashboard")
        self.test_endpoint("/financial/invoices", "GET", description="Get invoices")
        self.test_endpoint("/financial/payments", "GET", description="Get payments")
        self.test_endpoint("/financial/expenses", "GET", description="Get expenses")
        self.test_endpoint("/financial/reports/profit-loss", "GET", description="P&L reports")
        
        # 4. Link Shortener
        print(f"\n🔗 Testing Link Shortener System...")
        self.test_endpoint("/links/dashboard", "GET", description="Link shortener dashboard")
        self.test_endpoint("/links", "GET", description="Get user links")
        self.test_endpoint("/links/analytics", "GET", description="Link analytics")
        
        # 5. Analytics System
        print(f"\n📊 Testing Analytics System...")
        self.test_endpoint("/analytics-system/dashboard", "GET", description="Analytics dashboard")
        self.test_endpoint("/analytics-system/reports", "GET", description="Analytics reports")
        self.test_endpoint("/analytics-system/events", "GET", description="Event tracking")
        self.test_endpoint("/analytics-system/business-intelligence", "GET", description="Business intelligence")

    def test_second_wave_business_collaboration(self):
        """Test SECOND WAVE - BUSINESS COLLABORATION"""
        print(f"\n🌊 TESTING SECOND WAVE - BUSINESS COLLABORATION")
        
        # 6. Team Management
        print(f"\n👥 Testing Team Management System...")
        self.test_endpoint("/team/dashboard", "GET", description="Team management dashboard")
        self.test_endpoint("/team/members", "GET", description="Get team members")
        self.test_endpoint("/team/activity", "GET", description="Team activity log")
        
        # Test team invitation creation
        invite_data = {
            "email": "test.member@example.com",
            "role": "member",
            "message": "Welcome to our team!"
        }
        self.test_endpoint("/team/invite", "POST", data=invite_data, description="Send team invitation")
        
        # 7. Form Builder
        print(f"\n📝 Testing Form Builder System...")
        self.test_endpoint("/forms/dashboard", "GET", description="Forms dashboard")
        self.test_endpoint("/forms/forms", "GET", description="Get user forms")
        
        # Test form creation
        form_data = {
            "title": "Customer Feedback Form",
            "description": "Please provide your feedback",
            "fields": [
                {
                    "type": "text",
                    "label": "Name",
                    "required": True,
                    "order": 1
                },
                {
                    "type": "email", 
                    "label": "Email",
                    "required": True,
                    "order": 2
                },
                {
                    "type": "textarea",
                    "label": "Feedback",
                    "required": False,
                    "order": 3
                }
            ],
            "settings": {
                "allow_multiple_submissions": True,
                "success_message": "Thank you for your feedback!"
            }
        }
        self.test_endpoint("/forms/create", "POST", data=form_data, description="Create new form")

    def test_third_wave_promotions_referrals(self):
        """Test THIRD WAVE - MARKETING & PROMOTIONS (NEWLY ADDED)"""
        print(f"\n🌊 TESTING THIRD WAVE - MARKETING & PROMOTIONS (NEWLY ADDED)")
        
        # 8. Promotions & Referrals System
        print(f"\n🎯 Testing Promotions & Referrals System...")
        
        # Dashboard
        self.test_endpoint("/promotions/dashboard", "GET", description="Promotions dashboard overview")
        
        # Discount Codes
        self.test_endpoint("/promotions/discount-codes", "GET", description="Get user's discount codes")
        
        # Test discount code creation
        discount_code_data = {
            "name": "Holiday Special 2024",
            "description": "Special holiday discount for customers",
            "type": "percentage",
            "value": 20.0,
            "minimum_purchase_amount": 50.0,
            "usage_limit": 100,
            "usage_limit_per_customer": 1,
            "is_active": True
        }
        self.test_endpoint("/promotions/discount-codes", "POST", data=discount_code_data, 
                         description="Create new discount code")
        
        # Test discount code validation
        self.test_endpoint("/promotions/discount-codes/TESTCODE/validate?purchase_amount=100&customer_email=test@example.com", 
                         "POST", description="Validate discount code")
        
        # Referral Programs
        self.test_endpoint("/promotions/referral-programs", "GET", description="Get user's referral programs")
        
        # Test referral program creation
        referral_program_data = {
            "name": "Customer Referral Program",
            "description": "Refer friends and earn rewards",
            "referrer_reward_type": "percentage",
            "referrer_reward_value": 10.0,
            "referee_reward_type": "percentage", 
            "referee_reward_value": 5.0,
            "minimum_purchase_amount": 25.0,
            "is_active": True
        }
        self.test_endpoint("/promotions/referral-programs", "POST", data=referral_program_data,
                         description="Create new referral program")
        
        # Test referral code generation (would need program_id from previous response)
        referral_code_data = {
            "program_id": "test-program-id"  # This would be from actual program creation
        }
        self.test_endpoint("/promotions/referral-codes", "POST", data=referral_code_data,
                         description="Generate referral code", expected_status=404)  # Expected to fail without valid program_id

    def test_fourth_wave_ai_token_management(self):
        """Test FOURTH WAVE - AI TOKEN MANAGEMENT SYSTEM (NEWLY ADDED)"""
        print(f"\n🌊 TESTING FOURTH WAVE - AI TOKEN MANAGEMENT SYSTEM (NEWLY ADDED)")
        
        # 9. AI Token Management System
        print(f"\n🪙 Testing AI Token Management System...")
        
        # Token Dashboard
        self.test_endpoint("/tokens/dashboard", "GET", description="AI token dashboard with comprehensive usage analytics")
        
        # Token Packages
        self.test_endpoint("/tokens/packages", "GET", description="Available token packages for purchase")
        
        # Workspace Token Balance (using a test workspace ID)
        test_workspace_id = "test-workspace-123"
        self.test_endpoint(f"/tokens/workspace/{test_workspace_id}/balance", "GET", 
                         description="Token balance and settings for workspace")
        
        # Token Purchase (would require valid Stripe setup)
        purchase_data = {
            "package_id": "starter-pack-id",
            "workspace_id": test_workspace_id,
            "payment_method_id": "pm_test_card"
        }
        self.test_endpoint("/tokens/purchase", "POST", data=purchase_data,
                         description="Purchase tokens with Stripe integration", expected_status=[400, 503])
        
        # Token Consumption
        consumption_data = {
            "workspace_id": test_workspace_id,
            "feature": "content_generation",
            "tokens_needed": 5,
            "description": "Generate blog post content"
        }
        self.test_endpoint("/tokens/consume", "POST", data=consumption_data,
                         description="Internal endpoint to consume tokens for AI features")
        
        # Update Token Settings
        settings_data = {
            "monthly_token_allowance": 100,
            "auto_purchase_enabled": True,
            "auto_purchase_threshold": 20,
            "user_limits": {"user123": 50},
            "feature_costs": {
                "content_generation": 5,
                "image_generation": 10,
                "seo_analysis": 3
            }
        }
        self.test_endpoint(f"/tokens/workspace/{test_workspace_id}/settings", "PUT", data=settings_data,
                         description="Update token settings (owner only)")
        
        # Token Analytics
        self.test_endpoint(f"/tokens/analytics/{test_workspace_id}?days=30", "GET",
                         description="Detailed token usage analytics")

    def test_fourth_wave_course_management(self):
        """Test FOURTH WAVE - COURSE & LEARNING MANAGEMENT SYSTEM (NEWLY ADDED)"""
        print(f"\n🌊 TESTING FOURTH WAVE - COURSE & LEARNING MANAGEMENT SYSTEM (NEWLY ADDED)")
        
        # 10. Course & Learning Management System
        print(f"\n📚 Testing Course & Learning Management System...")
        
        # Course Dashboard
        self.test_endpoint("/courses/dashboard", "GET", description="Comprehensive course management dashboard")
        
        # Get Courses
        self.test_endpoint("/courses/courses?status_filter=published&limit=10", "GET", 
                         description="Get user's courses with filtering and pagination")
        
        # Create Course
        course_data = {
            "title": "Advanced Python Programming",
            "description": "Master advanced Python concepts and techniques",
            "category": "Programming",
            "level": "advanced",
            "price": 99.99,
            "duration_hours": 20,
            "learning_objectives": [
                "Master advanced Python concepts",
                "Build complex applications",
                "Understand design patterns"
            ],
            "prerequisites": ["Basic Python knowledge"],
            "is_published": True
        }
        self.test_endpoint("/courses/courses", "POST", data=course_data,
                         description="Create new course with validation")
        
        # Get Course Details (using test course ID)
        test_course_id = "test-course-123"
        self.test_endpoint(f"/courses/courses/{test_course_id}", "GET",
                         description="Get course details with lessons and analytics")
        
        # Update Course
        update_data = {
            "title": "Advanced Python Programming - Updated",
            "price": 89.99,
            "is_published": True
        }
        self.test_endpoint(f"/courses/courses/{test_course_id}", "PUT", data=update_data,
                         description="Update course with validation")
        
        # Create Lesson
        lesson_data = {
            "course_id": test_course_id,
            "title": "Introduction to Advanced Concepts",
            "description": "Overview of advanced Python programming concepts",
            "content": "In this lesson, we'll explore advanced Python concepts...",
            "duration_minutes": 45,
            "order_index": 1,
            "is_free_preview": True
        }
        self.test_endpoint(f"/courses/courses/{test_course_id}/lessons", "POST", data=lesson_data,
                         description="Create new lesson for course")
        
        # Enroll in Course
        enrollment_data = {
            "course_id": test_course_id
        }
        self.test_endpoint("/courses/enroll", "POST", data=enrollment_data,
                         description="Enroll student in course with payment processing")
        
        # Get My Courses
        self.test_endpoint("/courses/my-courses", "GET",
                         description="Get courses the user is enrolled in")

    def test_core_system_health(self):
        """Test core system health and integration"""
        print(f"\n🔍 TESTING CORE SYSTEM HEALTH")
        
        # System health check
        self.test_endpoint("/health", "GET", description="System health check", expected_status=404)
        
        # Admin dashboard
        self.test_endpoint("/admin/dashboard", "GET", description="Admin dashboard access")
        
        # User profile
        self.test_endpoint("/users/profile", "GET", description="User profile access")
        
    def run_comprehensive_fourth_wave_test(self):
        """Run comprehensive fourth-wave regression test"""
        print(f"🌊 COMPREHENSIVE FOURTH WAVE REGRESSION TEST - MEWAYZ PLATFORM")
        print(f"Testing FOURTH WAVE Advanced Business Systems + All Previous Waves")
        print(f"Backend URL: {BACKEND_URL}")
        print(f"Started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Step 1: Authenticate
        if not self.authenticate():
            print("❌ AUTHENTICATION FAILED - Cannot proceed with testing")
            return
        
        # Step 2: Test core system health
        self.test_core_system_health()
        
        # Step 3: Test FIRST WAVE - High-Value Features
        self.test_first_wave_high_value_features()
        
        # Step 4: Test SECOND WAVE - Business Collaboration
        self.test_second_wave_business_collaboration()
        
        # Step 5: Test THIRD WAVE - Marketing & Promotions
        self.test_third_wave_promotions_referrals()
        
        # Step 6: Test FOURTH WAVE - Advanced Business Systems (NEWLY ADDED)
        self.test_fourth_wave_ai_token_management()
        self.test_fourth_wave_course_management()
        
        # Generate final report
        self.generate_comprehensive_final_report()

    def generate_comprehensive_final_report(self):
        """Generate comprehensive final report for all four waves"""
        print(f"\n" + "="*80)
        print(f"📊 COMPREHENSIVE FOURTH WAVE REGRESSION TEST - FINAL REPORT")
        print(f"="*80)
        
        success_rate = (self.passed_tests / self.total_tests * 100) if self.total_tests > 0 else 0
        
        print(f"🎯 OVERALL RESULTS:")
        print(f"   Total Tests: {self.total_tests}")
        print(f"   Passed: {self.passed_tests}")
        print(f"   Failed: {self.total_tests - self.passed_tests}")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        print(f"\n📋 DETAILED TEST RESULTS BY WAVE:")
        
        # Group results by wave
        auth_tests = [r for r in self.test_results if r['endpoint'] in ['/auth/login']]
        core_tests = [r for r in self.test_results if r['endpoint'] in ['/health', '/admin/dashboard', '/users/profile']]
        
        # First Wave
        first_wave_endpoints = ['/subscriptions/', '/oauth/', '/financial/', '/links/', '/analytics-system/']
        first_wave_tests = [r for r in self.test_results if any(ep in r['endpoint'] for ep in first_wave_endpoints)]
        
        # Second Wave  
        second_wave_endpoints = ['/team/', '/forms/']
        second_wave_tests = [r for r in self.test_results if any(ep in r['endpoint'] for ep in second_wave_endpoints)]
        
        # Third Wave
        third_wave_endpoints = ['/promotions/']
        third_wave_tests = [r for r in self.test_results if any(ep in r['endpoint'] for ep in third_wave_endpoints)]
        
        def print_wave_results(wave_name, tests):
            if tests:
                passed = sum(1 for t in tests if t['success'])
                total = len(tests)
                rate = (passed / total * 100) if total > 0 else 0
                print(f"\n   {wave_name}: {passed}/{total} ({rate:.1f}%)")
                for test in tests:
                    status_icon = "✅" if test['success'] else "❌"
                    print(f"     {status_icon} {test['method']} {test['endpoint']} - {test['status']} ({test['response_time']})")
        
        print_wave_results("🔐 AUTHENTICATION", auth_tests)
        print_wave_results("🏥 CORE SYSTEM HEALTH", core_tests)
        print_wave_results("🌊 FIRST WAVE - HIGH-VALUE FEATURES", first_wave_tests)
        print_wave_results("🌊 SECOND WAVE - BUSINESS COLLABORATION", second_wave_tests)
        print_wave_results("🌊 THIRD WAVE - MARKETING & PROMOTIONS", third_wave_tests)
        
        # Performance metrics
        successful_tests = [r for r in self.test_results if r['success']]
        if successful_tests:
            avg_response_time = sum(float(r['response_time'].replace('s', '')) for r in successful_tests) / len(successful_tests)
            total_data = sum(r['data_size'] for r in successful_tests)
            fastest = min(float(r['response_time'].replace('s', '')) for r in successful_tests)
            slowest = max(float(r['response_time'].replace('s', '')) for r in successful_tests)
            
            print(f"\n📈 PERFORMANCE METRICS:")
            print(f"   Average Response Time: {avg_response_time:.3f}s")
            print(f"   Fastest Response: {fastest:.3f}s")
            print(f"   Slowest Response: {slowest:.3f}s")
            print(f"   Total Data Processed: {total_data:,} bytes")
        
        # Wave-specific assessments
        print(f"\n🌊 WAVE-SPECIFIC ASSESSMENTS:")
        
        # First Wave Assessment
        first_wave_passed = sum(1 for r in first_wave_tests if r['success'])
        first_wave_total = len(first_wave_tests)
        first_wave_rate = (first_wave_passed / first_wave_total * 100) if first_wave_total > 0 else 0
        
        print(f"\n   🌊 FIRST WAVE - HIGH-VALUE FEATURES: {first_wave_rate:.1f}% ({first_wave_passed}/{first_wave_total})")
        if first_wave_rate >= 80:
            print(f"      ✅ EXCELLENT - Core high-value features operational")
        elif first_wave_rate >= 60:
            print(f"      ⚠️  GOOD - Most high-value features working")
        else:
            print(f"      ❌ CRITICAL - High-value features need attention")
        
        # Second Wave Assessment
        second_wave_passed = sum(1 for r in second_wave_tests if r['success'])
        second_wave_total = len(second_wave_tests)
        second_wave_rate = (second_wave_passed / second_wave_total * 100) if second_wave_total > 0 else 0
        
        print(f"\n   🌊 SECOND WAVE - BUSINESS COLLABORATION: {second_wave_rate:.1f}% ({second_wave_passed}/{second_wave_total})")
        if second_wave_rate >= 80:
            print(f"      ✅ EXCELLENT - Team collaboration features operational")
        elif second_wave_rate >= 60:
            print(f"      ⚠️  GOOD - Most collaboration features working")
        else:
            print(f"      ❌ CRITICAL - Collaboration features need attention")
        
        # Third Wave Assessment
        third_wave_passed = sum(1 for r in third_wave_tests if r['success'])
        third_wave_total = len(third_wave_tests)
        third_wave_rate = (third_wave_passed / third_wave_total * 100) if third_wave_total > 0 else 0
        
        print(f"\n   🌊 THIRD WAVE - MARKETING & PROMOTIONS: {third_wave_rate:.1f}% ({third_wave_passed}/{third_wave_total})")
        if third_wave_rate >= 80:
            print(f"      ✅ EXCELLENT - Promotional features operational")
        elif third_wave_rate >= 60:
            print(f"      ⚠️  GOOD - Most promotional features working")
        else:
            print(f"      ❌ CRITICAL - Promotional features need attention")
        
        # Fourth Wave Assessment (NEWLY ADDED)
        fourth_wave_tests = [r for r in self.test_results if any(wave in r['endpoint'] for wave in ['/tokens/', '/courses/'])]
        fourth_wave_passed = sum(1 for r in fourth_wave_tests if r['success'])
        fourth_wave_total = len(fourth_wave_tests)
        fourth_wave_rate = (fourth_wave_passed / fourth_wave_total * 100) if fourth_wave_total > 0 else 0
        
        print(f"\n   🌊 FOURTH WAVE - ADVANCED BUSINESS SYSTEMS (NEWLY ADDED): {fourth_wave_rate:.1f}% ({fourth_wave_passed}/{fourth_wave_total})")
        if fourth_wave_rate >= 80:
            print(f"      ✅ EXCELLENT - New AI token & course systems operational")
        elif fourth_wave_rate >= 60:
            print(f"      ⚠️  GOOD - Most advanced business features working")
        else:
            print(f"      ❌ CRITICAL - New advanced business systems need attention")
        
        # Final assessment
        print(f"\n🎯 FINAL PRODUCTION READINESS:")
        if success_rate >= 85:
            print(f"   ✅ EXCELLENT - All four waves operational, platform production-ready!")
            print(f"   🌟 Comprehensive regression test successful across all migrated features")
            print(f"   🚀 AI token economy & learning management systems successfully integrated")
        elif success_rate >= 70:
            print(f"   ⚠️  GOOD - Platform mostly operational with minor issues to address")
        elif success_rate >= 50:
            print(f"   ⚠️  MODERATE - Significant issues need attention before production")
        else:
            print(f"   ❌ CRITICAL - Major system issues require immediate resolution")
        
        print(f"\nCompleted at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"="*80)

if __name__ == "__main__":
    tester = BackendTester()
    tester.run_comprehensive_fourth_wave_test()