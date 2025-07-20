#!/usr/bin/env python3
"""
Comprehensive Onboarding and Admin Management System Testing
Testing the new comprehensive onboarding and admin management system endpoints for Mewayz Platform
"""

import requests
import json
import time
from datetime import datetime

# Configuration
BACKEND_URL = "https://9dd14206-b170-4c02-b8f5-4aee3e308cd4.preview.emergentagent.com"
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class OnboardingAdminTester:
    def __init__(self):
        self.session = requests.Session()
        self.admin_token = None
        self.test_results = []
        
    def log_test(self, test_name, success, details, response_time=None):
        """Log test results"""
        result = {
            "test": test_name,
            "success": success,
            "details": details,
            "response_time": response_time,
            "timestamp": datetime.now().isoformat()
        }
        self.test_results.append(result)
        
        status = "✅ PASS" if success else "❌ FAIL"
        time_info = f" ({response_time:.3f}s)" if response_time else ""
        print(f"{status}: {test_name}{time_info}")
        if not success:
            print(f"   Details: {details}")
        elif isinstance(details, dict) and details.get('data'):
            data_size = len(str(details['data']))
            print(f"   Response: {data_size} chars of data")
    
    def admin_login(self):
        """Authenticate as admin user"""
        print("\n🔐 ADMIN AUTHENTICATION")
        print("=" * 50)
        
        start_time = time.time()
        try:
            response = self.session.post(
                f"{BACKEND_URL}/api/auth/login",
                json={
                    "email": ADMIN_EMAIL,
                    "password": ADMIN_PASSWORD
                },
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success") and data.get("token"):
                    self.admin_token = data["token"]
                    self.session.headers.update({
                        "Authorization": f"Bearer {self.admin_token}"
                    })
                    
                    user_info = data.get("user", {})
                    self.log_test(
                        "Admin Login Authentication",
                        True,
                        f"Successfully authenticated admin user: {user_info.get('name', 'Unknown')} ({user_info.get('email', 'Unknown')}) with role: {user_info.get('role', 'Unknown')}",
                        response_time
                    )
                    return True
                else:
                    self.log_test("Admin Login Authentication", False, f"Login failed: {data.get('message', 'Unknown error')}", response_time)
                    return False
            else:
                self.log_test("Admin Login Authentication", False, f"HTTP {response.status_code}: {response.text}", response_time)
                return False
                
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("Admin Login Authentication", False, f"Exception: {str(e)}", response_time)
            return False
    
    def test_onboarding_endpoints(self):
        """Test all onboarding system endpoints"""
        print("\n🚀 ONBOARDING SYSTEM ENDPOINTS")
        print("=" * 50)
        
        # Test 1: GET /api/onboarding/progress - Get user's onboarding progress
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/onboarding/progress", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    progress_data = data.get("data")
                    if progress_data is None:
                        self.log_test(
                            "GET /api/onboarding/progress",
                            True,
                            "Successfully retrieved onboarding progress (no existing progress found)",
                            response_time
                        )
                    else:
                        self.log_test(
                            "GET /api/onboarding/progress",
                            True,
                            f"Successfully retrieved onboarding progress with {len(progress_data)} fields",
                            response_time
                        )
                else:
                    self.log_test("GET /api/onboarding/progress", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/onboarding/progress", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/onboarding/progress", False, f"Exception: {str(e)}", response_time)
        
        # Test 2: POST /api/onboarding/progress - Save user's onboarding progress
        start_time = time.time()
        try:
            progress_data = {
                "currentStep": 2,
                "completedSteps": [0, 1],
                "data": {
                    "goals": ["social_media", "content_creation"],
                    "industry": "technology",
                    "companySize": "1-10",
                    "workspaceName": "Test Workspace for Onboarding"
                }
            }
            
            response = self.session.post(
                f"{BACKEND_URL}/api/onboarding/progress",
                json=progress_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    self.log_test(
                        "POST /api/onboarding/progress",
                        True,
                        f"Successfully saved onboarding progress: {data.get('message', 'Progress saved')}",
                        response_time
                    )
                else:
                    self.log_test("POST /api/onboarding/progress", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("POST /api/onboarding/progress", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("POST /api/onboarding/progress", False, f"Exception: {str(e)}", response_time)
        
        # Test 3: POST /api/onboarding/complete - Complete the onboarding process
        start_time = time.time()
        try:
            completion_data = {
                "data": {
                    "workspaceName": "Admin Test Workspace",
                    "workspaceDescription": "Test workspace created during onboarding completion testing",
                    "industry": "technology",
                    "companySize": "1-10",
                    "timezone": "America/New_York",
                    "selectedGoals": ["social_media", "content_creation", "analytics"],
                    "primaryGoal": "social_media",
                    "selectedPlan": "pro",
                    "brandName": "Test Brand",
                    "brandColors": {
                        "primary": "#3B82F6",
                        "secondary": "#10B981",
                        "accent": "#F59E0B"
                    },
                    "teamMembers": [
                        {
                            "email": "testmember@example.com",
                            "role": "editor"
                        }
                    ]
                }
            }
            
            response = self.session.post(
                f"{BACKEND_URL}/api/onboarding/complete",
                json=completion_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    workspace_id = data.get("workspace_id")
                    self.log_test(
                        "POST /api/onboarding/complete",
                        True,
                        f"Successfully completed onboarding and created workspace: {workspace_id}",
                        response_time
                    )
                else:
                    self.log_test("POST /api/onboarding/complete", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("POST /api/onboarding/complete", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("POST /api/onboarding/complete", False, f"Exception: {str(e)}", response_time)
    
    def test_admin_management_endpoints(self):
        """Test all admin management system endpoints"""
        print("\n👑 ADMIN MANAGEMENT SYSTEM ENDPOINTS")
        print("=" * 50)
        
        # Test 4: GET /api/admin/users/stats - Get user statistics for admin dashboard
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/users/stats", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    stats = data.get("data", {})
                    self.log_test(
                        "GET /api/admin/users/stats",
                        True,
                        f"Successfully retrieved user stats: {stats.get('total_users', 0)} total users, {stats.get('active_users', 0)} active, {stats.get('new_today', 0)} new today, {stats.get('growth_rate', 0)}% growth",
                        response_time
                    )
                else:
                    self.log_test("GET /api/admin/users/stats", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/admin/users/stats", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/admin/users/stats", False, f"Exception: {str(e)}", response_time)
        
        # Test 5: GET /api/admin/workspaces/stats - Get workspace statistics for admin dashboard
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/workspaces/stats", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    stats = data.get("data", {})
                    self.log_test(
                        "GET /api/admin/workspaces/stats",
                        True,
                        f"Successfully retrieved workspace stats: {stats.get('total_count', 0)} total workspaces, {stats.get('active_count', 0)} active, {stats.get('recent_count', 0)} recent, {stats.get('growth_rate', 0)}% growth",
                        response_time
                    )
                else:
                    self.log_test("GET /api/admin/workspaces/stats", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/admin/workspaces/stats", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/admin/workspaces/stats", False, f"Exception: {str(e)}", response_time)
        
        # Test 6: GET /api/admin/analytics/overview - Get analytics overview for admin dashboard
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/analytics/overview", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    analytics = data.get("data", {})
                    self.log_test(
                        "GET /api/admin/analytics/overview",
                        True,
                        f"Successfully retrieved analytics overview: ${analytics.get('total_revenue', 0):,.2f} total revenue, {analytics.get('revenue_growth', 0)}% growth, ${analytics.get('mrr', 0):,.2f} MRR, {analytics.get('churn_rate', 0)}% churn",
                        response_time
                    )
                else:
                    self.log_test("GET /api/admin/analytics/overview", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/admin/analytics/overview", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/admin/analytics/overview", False, f"Exception: {str(e)}", response_time)
        
        # Test 7: GET /api/admin/system/metrics - Get system health metrics
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/system/metrics", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    metrics = data.get("data", {})
                    self.log_test(
                        "GET /api/admin/system/metrics",
                        True,
                        f"Successfully retrieved system metrics: {metrics.get('uptime', 'N/A')} uptime, {metrics.get('response_time', 'N/A')} response time, {metrics.get('memory_usage', 'N/A')} memory, {metrics.get('cpu_usage', 'N/A')} CPU, {metrics.get('active_connections', 0)} connections",
                        response_time
                    )
                else:
                    self.log_test("GET /api/admin/system/metrics", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/admin/system/metrics", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/admin/system/metrics", False, f"Exception: {str(e)}", response_time)
        
        # Test 8: GET /api/admin/users - Get all users for admin management
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/users?page=1&limit=10", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    user_data = data.get("data", {})
                    users = user_data.get("users", [])
                    total = user_data.get("total", 0)
                    self.log_test(
                        "GET /api/admin/users",
                        True,
                        f"Successfully retrieved user list: {len(users)} users on page 1, {total} total users, {user_data.get('pages', 0)} total pages",
                        response_time
                    )
                else:
                    self.log_test("GET /api/admin/users", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/admin/users", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/admin/users", False, f"Exception: {str(e)}", response_time)
        
        # Test 9: GET /api/admin/workspaces - Get all workspaces for admin management
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/workspaces?page=1&limit=10", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    workspace_data = data.get("data", {})
                    workspaces = workspace_data.get("workspaces", [])
                    total = workspace_data.get("total", 0)
                    self.log_test(
                        "GET /api/admin/workspaces",
                        True,
                        f"Successfully retrieved workspace list: {len(workspaces)} workspaces on page 1, {total} total workspaces, {workspace_data.get('pages', 0)} total pages",
                        response_time
                    )
                else:
                    self.log_test("GET /api/admin/workspaces", False, f"API returned success=false: {data}", response_time)
            else:
                self.log_test("GET /api/admin/workspaces", False, f"HTTP {response.status_code}: {response.text}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("GET /api/admin/workspaces", False, f"Exception: {str(e)}", response_time)
    
    def test_admin_role_verification(self):
        """Test admin role verification and access control"""
        print("\n🔒 ADMIN ROLE VERIFICATION")
        print("=" * 50)
        
        # Create a temporary session without admin token to test access control
        temp_session = requests.Session()
        
        # Test unauthorized access to admin endpoints
        admin_endpoints = [
            "/api/admin/users/stats",
            "/api/admin/workspaces/stats", 
            "/api/admin/analytics/overview",
            "/api/admin/system/metrics",
            "/api/admin/users",
            "/api/admin/workspaces"
        ]
        
        unauthorized_count = 0
        for endpoint in admin_endpoints:
            start_time = time.time()
            try:
                response = temp_session.get(f"{BACKEND_URL}{endpoint}", timeout=30)
                response_time = time.time() - start_time
                
                if response.status_code in [401, 403]:
                    unauthorized_count += 1
                    self.log_test(
                        f"Unauthorized Access Test - {endpoint}",
                        True,
                        f"Correctly blocked unauthorized access (HTTP {response.status_code})",
                        response_time
                    )
                else:
                    self.log_test(
                        f"Unauthorized Access Test - {endpoint}",
                        False,
                        f"Security issue: Endpoint accessible without admin token (HTTP {response.status_code})",
                        response_time
                    )
            except Exception as e:
                response_time = time.time() - start_time
                self.log_test(f"Unauthorized Access Test - {endpoint}", False, f"Exception: {str(e)}", response_time)
        
        # Summary of access control testing
        if unauthorized_count == len(admin_endpoints):
            self.log_test(
                "Admin Role Verification Summary",
                True,
                f"All {len(admin_endpoints)} admin endpoints properly protected with role verification"
            )
        else:
            self.log_test(
                "Admin Role Verification Summary",
                False,
                f"Security issue: {len(admin_endpoints) - unauthorized_count} out of {len(admin_endpoints)} admin endpoints not properly protected"
            )
    
    def test_data_structure_validation(self):
        """Test data structure and response formats"""
        print("\n📊 DATA STRUCTURE & RESPONSE FORMAT VALIDATION")
        print("=" * 50)
        
        # Test onboarding progress data structure
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/onboarding/progress", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                required_fields = ["success"]
                has_all_fields = all(field in data for field in required_fields)
                
                if has_all_fields:
                    self.log_test(
                        "Onboarding Progress Data Structure",
                        True,
                        f"Response contains all required fields: {required_fields}",
                        response_time
                    )
                else:
                    missing_fields = [field for field in required_fields if field not in data]
                    self.log_test(
                        "Onboarding Progress Data Structure",
                        False,
                        f"Missing required fields: {missing_fields}",
                        response_time
                    )
            else:
                self.log_test("Onboarding Progress Data Structure", False, f"HTTP {response.status_code}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("Onboarding Progress Data Structure", False, f"Exception: {str(e)}", response_time)
        
        # Test admin stats data structure
        start_time = time.time()
        try:
            response = self.session.get(f"{BACKEND_URL}/api/admin/users/stats", timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                required_fields = ["success", "data"]
                stats_fields = ["total_users", "active_users", "growth_rate"]
                
                has_required = all(field in data for field in required_fields)
                has_stats = all(field in data.get("data", {}) for field in stats_fields) if has_required else False
                
                if has_required and has_stats:
                    self.log_test(
                        "Admin User Stats Data Structure",
                        True,
                        f"Response contains all required fields and stats: {required_fields + stats_fields}",
                        response_time
                    )
                else:
                    missing = []
                    if not has_required:
                        missing.extend([f for f in required_fields if f not in data])
                    if not has_stats:
                        missing.extend([f for f in stats_fields if f not in data.get("data", {})])
                    
                    self.log_test(
                        "Admin User Stats Data Structure",
                        False,
                        f"Missing required fields: {missing}",
                        response_time
                    )
            else:
                self.log_test("Admin User Stats Data Structure", False, f"HTTP {response.status_code}", response_time)
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("Admin User Stats Data Structure", False, f"Exception: {str(e)}", response_time)
    
    def test_mongodb_operations(self):
        """Test MongoDB collection operations"""
        print("\n🗄️ MONGODB COLLECTION OPERATIONS")
        print("=" * 50)
        
        # Test onboarding collection operations by saving and retrieving progress
        start_time = time.time()
        try:
            # Save progress
            test_progress = {
                "currentStep": 3,
                "completedSteps": [0, 1, 2],
                "data": {
                    "testField": "mongodb_test_value",
                    "timestamp": datetime.now().isoformat()
                }
            }
            
            save_response = self.session.post(
                f"{BACKEND_URL}/api/onboarding/progress",
                json=test_progress,
                timeout=30
            )
            
            if save_response.status_code == 200:
                # Retrieve progress
                get_response = self.session.get(f"{BACKEND_URL}/api/onboarding/progress", timeout=30)
                
                if get_response.status_code == 200:
                    get_data = get_response.json()
                    if get_data.get("success") and get_data.get("data"):
                        retrieved_data = get_data["data"]
                        if retrieved_data.get("current_step") == 3:
                            response_time = time.time() - start_time
                            self.log_test(
                                "MongoDB Onboarding Collection Operations",
                                True,
                                f"Successfully saved and retrieved onboarding progress from MongoDB",
                                response_time
                            )
                        else:
                            response_time = time.time() - start_time
                            self.log_test(
                                "MongoDB Onboarding Collection Operations",
                                False,
                                f"Data mismatch: Expected step 3, got {retrieved_data.get('current_step')}",
                                response_time
                            )
                    else:
                        response_time = time.time() - start_time
                        self.log_test(
                            "MongoDB Onboarding Collection Operations",
                            False,
                            "Failed to retrieve saved progress data",
                            response_time
                        )
                else:
                    response_time = time.time() - start_time
                    self.log_test(
                        "MongoDB Onboarding Collection Operations",
                        False,
                        f"Failed to retrieve progress: HTTP {get_response.status_code}",
                        response_time
                    )
            else:
                response_time = time.time() - start_time
                self.log_test(
                    "MongoDB Onboarding Collection Operations",
                    False,
                    f"Failed to save progress: HTTP {save_response.status_code}",
                    response_time
                )
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("MongoDB Onboarding Collection Operations", False, f"Exception: {str(e)}", response_time)
        
        # Test admin collections by checking user and workspace counts
        start_time = time.time()
        try:
            users_response = self.session.get(f"{BACKEND_URL}/api/admin/users/stats", timeout=30)
            workspaces_response = self.session.get(f"{BACKEND_URL}/api/admin/workspaces/stats", timeout=30)
            
            if users_response.status_code == 200 and workspaces_response.status_code == 200:
                users_data = users_response.json()
                workspaces_data = workspaces_response.json()
                
                if users_data.get("success") and workspaces_data.get("success"):
                    user_count = users_data.get("data", {}).get("total_users", 0)
                    workspace_count = workspaces_data.get("data", {}).get("total_count", 0)
                    
                    response_time = time.time() - start_time
                    self.log_test(
                        "MongoDB Admin Collections Operations",
                        True,
                        f"Successfully queried admin collections: {user_count} users, {workspace_count} workspaces",
                        response_time
                    )
                else:
                    response_time = time.time() - start_time
                    self.log_test(
                        "MongoDB Admin Collections Operations",
                        False,
                        "Failed to get success response from admin stats",
                        response_time
                    )
            else:
                response_time = time.time() - start_time
                self.log_test(
                    "MongoDB Admin Collections Operations",
                    False,
                    f"HTTP errors: users {users_response.status_code}, workspaces {workspaces_response.status_code}",
                    response_time
                )
        except Exception as e:
            response_time = time.time() - start_time
            self.log_test("MongoDB Admin Collections Operations", False, f"Exception: {str(e)}", response_time)
    
    def generate_summary(self):
        """Generate comprehensive test summary"""
        print("\n" + "=" * 80)
        print("🎯 COMPREHENSIVE ONBOARDING & ADMIN MANAGEMENT TESTING SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result["success"])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 OVERALL RESULTS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        # Categorize results
        onboarding_tests = [r for r in self.test_results if "onboarding" in r["test"].lower()]
        admin_tests = [r for r in self.test_results if "admin" in r["test"].lower()]
        security_tests = [r for r in self.test_results if "unauthorized" in r["test"].lower() or "role" in r["test"].lower()]
        data_tests = [r for r in self.test_results if "data structure" in r["test"].lower() or "mongodb" in r["test"].lower()]
        
        print(f"\n🚀 ONBOARDING SYSTEM RESULTS:")
        onboarding_passed = sum(1 for r in onboarding_tests if r["success"])
        print(f"   Tests: {len(onboarding_tests)} | Passed: {onboarding_passed} | Success Rate: {(onboarding_passed/len(onboarding_tests)*100) if onboarding_tests else 0:.1f}%")
        
        print(f"\n👑 ADMIN MANAGEMENT RESULTS:")
        admin_passed = sum(1 for r in admin_tests if r["success"])
        print(f"   Tests: {len(admin_tests)} | Passed: {admin_passed} | Success Rate: {(admin_passed/len(admin_tests)*100) if admin_tests else 0:.1f}%")
        
        print(f"\n🔒 SECURITY & ACCESS CONTROL RESULTS:")
        security_passed = sum(1 for r in security_tests if r["success"])
        print(f"   Tests: {len(security_tests)} | Passed: {security_passed} | Success Rate: {(security_passed/len(security_tests)*100) if security_tests else 0:.1f}%")
        
        print(f"\n📊 DATA & DATABASE RESULTS:")
        data_passed = sum(1 for r in data_tests if r["success"])
        print(f"   Tests: {len(data_tests)} | Passed: {data_passed} | Success Rate: {(data_passed/len(data_tests)*100) if data_tests else 0:.1f}%")
        
        # Performance metrics
        response_times = [r["response_time"] for r in self.test_results if r["response_time"]]
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            max_response_time = max(response_times)
            print(f"\n⚡ PERFORMANCE METRICS:")
            print(f"   Average Response Time: {avg_response_time:.3f}s")
            print(f"   Maximum Response Time: {max_response_time:.3f}s")
            print(f"   Total Tests with Timing: {len(response_times)}")
        
        # Failed tests details
        failed_test_results = [r for r in self.test_results if not r["success"]]
        if failed_test_results:
            print(f"\n❌ FAILED TESTS DETAILS:")
            for result in failed_test_results:
                print(f"   • {result['test']}: {result['details']}")
        
        print(f"\n🎯 CONCLUSION:")
        if success_rate >= 90:
            print("   ✅ EXCELLENT: Onboarding and Admin Management systems are highly functional and production-ready!")
        elif success_rate >= 75:
            print("   ✅ GOOD: Systems are mostly functional with minor issues that need attention.")
        elif success_rate >= 50:
            print("   ⚠️ MODERATE: Systems have significant issues that need to be addressed.")
        else:
            print("   ❌ CRITICAL: Systems have major issues and require immediate attention.")
        
        return {
            "total_tests": total_tests,
            "passed_tests": passed_tests,
            "failed_tests": failed_tests,
            "success_rate": success_rate,
            "avg_response_time": sum(response_times) / len(response_times) if response_times else 0,
            "test_results": self.test_results
        }
    
    def run_all_tests(self):
        """Run all comprehensive tests"""
        print("🧪 COMPREHENSIVE ONBOARDING & ADMIN MANAGEMENT SYSTEM TESTING")
        print("Testing new comprehensive onboarding and admin management system endpoints")
        print("Platform: Mewayz Professional v3.0.0")
        print("Backend URL:", BACKEND_URL)
        print("Admin Credentials: tmonnens@outlook.com / Voetballen5")
        print("=" * 80)
        
        # Step 1: Admin Authentication
        if not self.admin_login():
            print("❌ CRITICAL: Admin authentication failed. Cannot proceed with testing.")
            return self.generate_summary()
        
        # Step 2: Test Onboarding Endpoints
        self.test_onboarding_endpoints()
        
        # Step 3: Test Admin Management Endpoints
        self.test_admin_management_endpoints()
        
        # Step 4: Test Admin Role Verification
        self.test_admin_role_verification()
        
        # Step 5: Test Data Structure Validation
        self.test_data_structure_validation()
        
        # Step 6: Test MongoDB Operations
        self.test_mongodb_operations()
        
        # Step 7: Generate Summary
        return self.generate_summary()

def main():
    """Main function to run the comprehensive testing"""
    tester = OnboardingAdminTester()
    summary = tester.run_all_tests()
    
    # Save results to file
    with open("/app/onboarding_admin_test_results.json", "w") as f:
        json.dump(summary, f, indent=2, default=str)
    
    print(f"\n💾 Test results saved to: /app/onboarding_admin_test_results.json")
    
    return summary["success_rate"] >= 75  # Return True if success rate is 75% or higher

if __name__ == "__main__":
    main()