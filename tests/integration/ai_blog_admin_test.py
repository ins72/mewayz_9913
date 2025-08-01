#!/usr/bin/env python3
"""
AI BLOG ADMIN CONTROL SYSTEM TESTING - MEWAYZ PLATFORM
Comprehensive testing of Phase 2 AI-powered blog system with admin control
Testing Agent - July 20, 2025
"""

import requests
import json
import time
from datetime import datetime

# Backend URL from frontend .env
BACKEND_URL = "https://d33eb8ac-7127-4f8c-84c6-cd6985146bee.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"

# Admin credentials from review request
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class AIBlogAdminTester:
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
        print("\n🔐 AUTHENTICATING ADMIN USER...")
        
        login_data = {
            "email": ADMIN_EMAIL,
            "password": ADMIN_PASSWORD
        }
        
        start_time = time.time()
        try:
            response = self.session.post(f"{API_BASE}/auth/login", json=login_data)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                # The token is returned in 'token' field, not 'access_token'
                self.auth_token = data.get('token') or data.get('access_token')
                self.session.headers.update({'Authorization': f'Bearer {self.auth_token}'})
                
                # Test the token by getting user profile
                profile_response = self.session.get(f"{API_BASE}/auth/me")
                if profile_response.status_code == 200:
                    profile_data = profile_response.json()
                    user_role = profile_data.get('role', 'unknown')
                    self.log_test("/auth/login", "POST", response.status_code, response_time, True, 
                                f"Admin authenticated successfully - Role: {user_role}")
                else:
                    self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                                f"Token validation failed: {profile_response.text}")
                    return False
                return True
            else:
                self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                            f"Authentication failed: {response.text}")
                return False
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("/auth/login", "POST", 0, response_time, False, f"Exception: {str(e)}")
            return False
    
    def test_admin_dashboard(self):
        """Test AI Blog Admin Dashboard"""
        print("\n📊 TESTING AI BLOG ADMIN DASHBOARD...")
        
        endpoint = "/ai-blog/admin/dashboard"
        start_time = time.time()
        
        try:
            response = self.session.get(f"{API_BASE}{endpoint}")
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate dashboard data structure
                required_fields = ['total_posts', 'published_posts', 'draft_posts', 'analytics']
                has_required_fields = all(field in data for field in required_fields)
                
                details = f"Dashboard data retrieved ({data_size} chars)"
                if has_required_fields:
                    details += f" - Posts: {data.get('total_posts', 0)}, Published: {data.get('published_posts', 0)}"
                
                self.log_test(endpoint, "GET", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "GET", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "GET", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_content_analytics(self):
        """Test AI Blog Content Analytics"""
        print("\n📈 TESTING AI BLOG CONTENT ANALYTICS...")
        
        endpoint = "/ai-blog/admin/content/analytics"
        start_time = time.time()
        
        try:
            response = self.session.get(f"{API_BASE}{endpoint}")
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate analytics data structure
                analytics_fields = ['performance_metrics', 'engagement_stats', 'content_insights']
                has_analytics = any(field in data for field in analytics_fields)
                
                details = f"Analytics data retrieved ({data_size} chars)"
                if has_analytics:
                    details += " - Performance metrics available"
                
                self.log_test(endpoint, "GET", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "GET", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "GET", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_bulk_content_generation(self):
        """Test AI Blog Bulk Content Generation"""
        print("\n🤖 TESTING AI BLOG BULK CONTENT GENERATION...")
        
        endpoint = "/ai-blog/admin/generate-bulk"
        start_time = time.time()
        
        bulk_request = {
            "topics": [
                "AI in Business Automation",
                "Future of Digital Marketing",
                "Sustainable Business Practices"
            ],
            "content_type": "blog_post",
            "target_audience": "business_professionals",
            "tone": "professional",
            "word_count": 800
        }
        
        try:
            response = self.session.post(f"{API_BASE}{endpoint}", json=bulk_request)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate bulk generation response
                batch_id = data.get('batch_id')
                progress_url = data.get('progress_url')
                
                details = f"Bulk generation initiated ({data_size} chars)"
                if batch_id:
                    details += f" - Batch ID: {batch_id[:8]}..."
                
                self.log_test(endpoint, "POST", response.status_code, response_time, True, 
                            details, data_size)
                
                # Store batch_id for progress testing
                self.batch_id = batch_id
                
            else:
                self.log_test(endpoint, "POST", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "POST", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_batch_progress(self):
        """Test AI Blog Batch Progress Tracking"""
        print("\n⏳ TESTING AI BLOG BATCH PROGRESS...")
        
        # Use batch_id from bulk generation test or create a test one
        batch_id = getattr(self, 'batch_id', 'test-batch-id-12345')
        endpoint = f"/ai-blog/admin/batch/{batch_id}/progress"
        start_time = time.time()
        
        try:
            response = self.session.get(f"{API_BASE}{endpoint}")
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate progress data
                progress_fields = ['batch_id', 'status', 'progress_percentage']
                has_progress = any(field in data for field in progress_fields)
                
                details = f"Progress data retrieved ({data_size} chars)"
                if has_progress:
                    status = data.get('status', 'unknown')
                    progress = data.get('progress_percentage', 0)
                    details += f" - Status: {status}, Progress: {progress}%"
                
                self.log_test(endpoint, "GET", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "GET", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "GET", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_content_approval(self):
        """Test AI Blog Content Approval"""
        print("\n✅ TESTING AI BLOG CONTENT APPROVAL...")
        
        endpoint = "/ai-blog/admin/content/approve"
        start_time = time.time()
        
        approval_request = {
            "content_id": "test-content-id-12345",
            "approval_notes": "Content meets quality standards and brand guidelines",
            "publish_immediately": True,
            "scheduled_publish_date": None
        }
        
        try:
            response = self.session.post(f"{API_BASE}{endpoint}", json=approval_request)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate approval response
                success = data.get('success', False)
                content_id = data.get('content_id')
                
                details = f"Content approval processed ({data_size} chars)"
                if success:
                    details += f" - Content ID: {content_id}, Status: approved"
                
                self.log_test(endpoint, "POST", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "POST", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "POST", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_content_rejection(self):
        """Test AI Blog Content Rejection"""
        print("\n❌ TESTING AI BLOG CONTENT REJECTION...")
        
        endpoint = "/ai-blog/admin/content/reject"
        start_time = time.time()
        
        rejection_request = {
            "content_id": "test-content-id-67890",
            "rejection_reason": "Content does not align with brand voice and requires revision",
            "feedback": "Please revise to include more specific examples and reduce technical jargon",
            "allow_resubmission": True
        }
        
        try:
            response = self.session.post(f"{API_BASE}{endpoint}", json=rejection_request)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate rejection response
                success = data.get('success', False)
                content_id = data.get('content_id')
                
                details = f"Content rejection processed ({data_size} chars)"
                if success:
                    details += f" - Content ID: {content_id}, Status: rejected"
                
                self.log_test(endpoint, "POST", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "POST", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "POST", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_template_creation(self):
        """Test AI Blog Template Creation"""
        print("\n📝 TESTING AI BLOG TEMPLATE CREATION...")
        
        endpoint = "/ai-blog/admin/templates/create"
        start_time = time.time()
        
        template_request = {
            "name": "Business Insights Template",
            "description": "Template for creating business insight articles",
            "template_structure": {
                "introduction": "Hook the reader with a compelling business question",
                "main_points": ["Key insight 1", "Key insight 2", "Key insight 3"],
                "conclusion": "Actionable takeaways for business leaders"
            },
            "content_type": "business_article",
            "target_audience": "executives",
            "estimated_word_count": 1200
        }
        
        try:
            response = self.session.post(f"{API_BASE}{endpoint}", json=template_request)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate template creation response
                template_id = data.get('template_id')
                success = data.get('success', False)
                
                details = f"Template creation processed ({data_size} chars)"
                if success and template_id:
                    details += f" - Template ID: {template_id[:8]}..."
                
                self.log_test(endpoint, "POST", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "POST", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "POST", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_content_schedule(self):
        """Test AI Blog Content Schedule"""
        print("\n📅 TESTING AI BLOG CONTENT SCHEDULE...")
        
        endpoint = "/ai-blog/admin/schedule"
        start_time = time.time()
        
        try:
            response = self.session.get(f"{API_BASE}{endpoint}")
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate schedule data
                schedule_fields = ['scheduled_posts', 'upcoming_deadlines', 'content_calendar']
                has_schedule = any(field in data for field in schedule_fields)
                
                details = f"Schedule data retrieved ({data_size} chars)"
                if has_schedule:
                    scheduled_count = len(data.get('scheduled_posts', []))
                    details += f" - Scheduled posts: {scheduled_count}"
                
                self.log_test(endpoint, "GET", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "GET", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "GET", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_seo_optimization(self):
        """Test AI Blog SEO Optimization"""
        print("\n🔍 TESTING AI BLOG SEO OPTIMIZATION...")
        
        endpoint = "/ai-blog/admin/optimization/seo"
        start_time = time.time()
        
        seo_request = {
            "content_id": "test-content-seo-12345",
            "target_keywords": ["AI automation", "business efficiency", "digital transformation"],
            "optimization_level": "advanced",
            "include_meta_description": True,
            "include_schema_markup": True
        }
        
        try:
            response = self.session.post(f"{API_BASE}{endpoint}", json=seo_request)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate SEO optimization response
                optimizations = data.get('optimizations', {})
                seo_score = data.get('seo_score')
                
                details = f"SEO optimization processed ({data_size} chars)"
                if seo_score:
                    details += f" - SEO Score: {seo_score}/100"
                
                self.log_test(endpoint, "POST", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "POST", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "POST", 0, response_time, False, f"Exception: {str(e)}")
    
    def test_quality_check(self):
        """Test AI Blog Content Quality Check"""
        print("\n🔍 TESTING AI BLOG CONTENT QUALITY CHECK...")
        
        endpoint = "/ai-blog/admin/content/quality-check"
        start_time = time.time()
        
        try:
            response = self.session.get(f"{API_BASE}{endpoint}")
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                data_size = len(response.text)
                
                # Validate quality check data
                quality_fields = ['quality_metrics', 'content_issues', 'recommendations']
                has_quality_data = any(field in data for field in quality_fields)
                
                details = f"Quality check data retrieved ({data_size} chars)"
                if has_quality_data:
                    issues_count = len(data.get('content_issues', []))
                    details += f" - Issues found: {issues_count}"
                
                self.log_test(endpoint, "GET", response.status_code, response_time, True, 
                            details, data_size)
            else:
                self.log_test(endpoint, "GET", response.status_code, response_time, False, 
                            f"Failed: {response.text}")
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test(endpoint, "GET", 0, response_time, False, f"Exception: {str(e)}")
    
    def run_comprehensive_test(self):
        """Run comprehensive AI Blog Admin Control System test"""
        print("🚀 STARTING COMPREHENSIVE AI BLOG ADMIN CONTROL SYSTEM TESTING")
        print("=" * 80)
        
        # Step 1: Authentication
        if not self.authenticate():
            print("❌ Authentication failed. Cannot proceed with testing.")
            return
        
        # Step 2: Test Admin Dashboard & Analytics
        print("\n📊 TESTING ADMIN DASHBOARD & ANALYTICS")
        print("-" * 50)
        self.test_admin_dashboard()
        self.test_content_analytics()
        
        # Step 3: Test Bulk Content Generation & Management
        print("\n🤖 TESTING BULK CONTENT GENERATION & MANAGEMENT")
        print("-" * 50)
        self.test_bulk_content_generation()
        self.test_batch_progress()
        
        # Step 4: Test Content Approval Workflow
        print("\n✅ TESTING CONTENT APPROVAL WORKFLOW")
        print("-" * 50)
        self.test_content_approval()
        self.test_content_rejection()
        
        # Step 5: Test Advanced Features
        print("\n🔧 TESTING ADVANCED FEATURES")
        print("-" * 50)
        self.test_template_creation()
        self.test_content_schedule()
        self.test_seo_optimization()
        self.test_quality_check()
        
        # Step 6: Generate comprehensive report
        self.generate_report()
    
    def generate_report(self):
        """Generate comprehensive test report"""
        print("\n" + "=" * 80)
        print("🎯 AI BLOG ADMIN CONTROL SYSTEM TESTING REPORT")
        print("=" * 80)
        
        success_rate = (self.passed_tests / self.total_tests * 100) if self.total_tests > 0 else 0
        
        print(f"\n📊 OVERALL RESULTS:")
        print(f"   Total Tests: {self.total_tests}")
        print(f"   Passed: {self.passed_tests}")
        print(f"   Failed: {self.total_tests - self.passed_tests}")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        # Calculate performance metrics
        response_times = []
        total_data_size = 0
        
        for result in self.test_results:
            if result['success']:
                try:
                    response_time = float(result['response_time'].replace('s', ''))
                    response_times.append(response_time)
                    total_data_size += result['data_size']
                except:
                    pass
        
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            min_response_time = min(response_times)
            max_response_time = max(response_times)
            
            print(f"\n⚡ PERFORMANCE METRICS:")
            print(f"   Average Response Time: {avg_response_time:.3f}s")
            print(f"   Fastest Response: {min_response_time:.3f}s")
            print(f"   Slowest Response: {max_response_time:.3f}s")
            print(f"   Total Data Processed: {total_data_size:,} bytes")
        
        # Categorize results
        print(f"\n📋 DETAILED RESULTS BY CATEGORY:")
        
        categories = {
            "Admin Dashboard & Analytics": ["/ai-blog/admin/dashboard", "/ai-blog/admin/content/analytics"],
            "Bulk Content Generation": ["/ai-blog/admin/generate-bulk", "/ai-blog/admin/batch/"],
            "Content Approval Workflow": ["/ai-blog/admin/content/approve", "/ai-blog/admin/content/reject"],
            "Advanced Features": ["/ai-blog/admin/templates/create", "/ai-blog/admin/schedule", 
                                "/ai-blog/admin/optimization/seo", "/ai-blog/admin/content/quality-check"]
        }
        
        for category, endpoints in categories.items():
            category_results = [r for r in self.test_results if any(ep in r['endpoint'] for ep in endpoints)]
            if category_results:
                passed = sum(1 for r in category_results if r['success'])
                total = len(category_results)
                rate = (passed / total * 100) if total > 0 else 0
                
                print(f"\n   {category}: {passed}/{total} ({rate:.1f}%)")
                for result in category_results:
                    status_icon = "✅" if result['success'] else "❌"
                    print(f"     {status_icon} {result['method']} {result['endpoint']} - {result['details']}")
        
        # Final assessment
        print(f"\n🎯 FINAL ASSESSMENT:")
        if success_rate >= 90:
            print("   ✅ EXCELLENT - AI Blog Admin Control System is production-ready")
        elif success_rate >= 75:
            print("   ✅ GOOD - AI Blog Admin Control System is mostly functional")
        elif success_rate >= 50:
            print("   ⚠️  PARTIAL - AI Blog Admin Control System has core functionality")
        else:
            print("   ❌ NEEDS WORK - AI Blog Admin Control System requires significant fixes")
        
        print(f"\n🚀 AI BLOG ADMIN CONTROL SYSTEM TESTING COMPLETED")
        print(f"   Phase 2 AI-powered blog system with comprehensive admin control")
        print(f"   Success Rate: {success_rate:.1f}% ({self.passed_tests}/{self.total_tests} tests passed)")
        print("=" * 80)

if __name__ == "__main__":
    tester = AIBlogAdminTester()
    tester.run_comprehensive_test()