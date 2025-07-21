#!/usr/bin/env python3
"""
Comprehensive Backend API Testing Script
Tests authentication and newly created API endpoints
"""

import requests
import json
import sys
from typing import Dict, Any, Optional

# Backend URL from environment
BACKEND_URL = "https://24cf731f-7b16-4968-bceb-592500093c66.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"

# Test credentials
TEST_EMAIL = "tmonnens@outlook.com"
TEST_PASSWORD = "Voetballen5"

class BackendTester:
    def __init__(self):
        self.session = requests.Session()
        self.access_token = None
        self.test_results = []
        
    def log_result(self, test_name: str, success: bool, message: str, response_data: Any = None):
        """Log test result"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            "test": test_name,
            "status": status,
            "success": success,
            "message": message,
            "response_size": len(str(response_data)) if response_data else 0
        }
        self.test_results.append(result)
        print(f"{status}: {test_name} - {message}")
        if response_data and len(str(response_data)) > 0:
            print(f"   Response size: {len(str(response_data))} chars")
    
    def test_health_check(self):
        """Test basic health check endpoint"""
        try:
            response = self.session.get(f"{BACKEND_URL}/health", timeout=10)
            if response.status_code == 200:
                data = response.json()
                self.log_result("Health Check", True, f"Backend is healthy - {data.get('app_name', 'Unknown')}", data)
                return True
            else:
                self.log_result("Health Check", False, f"Health check failed with status {response.status_code}")
                return False
        except Exception as e:
            self.log_result("Health Check", False, f"Health check error: {str(e)}")
            return False
    
    def test_authentication(self):
        """Test authentication with provided credentials"""
        try:
            # Test login
            login_data = {
                "username": TEST_EMAIL,
                "password": TEST_PASSWORD
            }
            
            response = self.session.post(
                f"{API_BASE}/auth/login",
                data=login_data,  # OAuth2PasswordRequestForm expects form data
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                self.access_token = data.get("access_token")
                if self.access_token:
                    # Set authorization header for future requests
                    self.session.headers.update({"Authorization": f"Bearer {self.access_token}"})
                    self.log_result("Authentication", True, f"Login successful - Token received", data)
                    return True
                else:
                    self.log_result("Authentication", False, "Login response missing access_token")
                    return False
            else:
                self.log_result("Authentication", False, f"Login failed with status {response.status_code}: {response.text}")
                return False
                
        except Exception as e:
            self.log_result("Authentication", False, f"Authentication error: {str(e)}")
            return False
    
    def test_endpoint(self, endpoint: str, method: str = "GET", data: Dict = None, test_name: str = None):
        """Test a specific API endpoint"""
        if not test_name:
            test_name = f"{method} {endpoint}"
            
        try:
            url = f"{API_BASE}{endpoint}"
            
            if method.upper() == "GET":
                response = self.session.get(url, timeout=10)
            elif method.upper() == "POST":
                response = self.session.post(url, json=data, timeout=10)
            elif method.upper() == "PUT":
                response = self.session.put(url, json=data, timeout=10)
            elif method.upper() == "DELETE":
                response = self.session.delete(url, timeout=10)
            else:
                self.log_result(test_name, False, f"Unsupported method: {method}")
                return False
            
            if response.status_code in [200, 201]:
                try:
                    data = response.json()
                    self.log_result(test_name, True, f"Endpoint accessible - Status {response.status_code}", data)
                    return True
                except:
                    self.log_result(test_name, True, f"Endpoint accessible - Status {response.status_code} (non-JSON response)")
                    return True
            elif response.status_code == 404:
                self.log_result(test_name, False, f"Endpoint not found (404) - May not be implemented or imported")
                return False
            elif response.status_code == 401:
                self.log_result(test_name, False, f"Authentication required (401)")
                return False
            elif response.status_code == 403:
                self.log_result(test_name, False, f"Access forbidden (403)")
                return False
            else:
                self.log_result(test_name, False, f"Endpoint error - Status {response.status_code}: {response.text}")
                return False
                
        except Exception as e:
            self.log_result(test_name, False, f"Request error: {str(e)}")
            return False
    
    def test_newly_created_apis(self):
        """Test the newly created API modules mentioned in the request"""
        print("\n=== Testing Newly Created API Modules ===")
        
        # Test Advanced AI API
        self.test_endpoint("/advanced-ai/capabilities", test_name="Advanced AI - Capabilities")
        self.test_endpoint("/advanced-ai/models", test_name="Advanced AI - Models")
        self.test_endpoint("/advanced-ai/insights", test_name="Advanced AI - Insights")
        
        # Test Advanced Financial API
        self.test_endpoint("/advanced-financial/dashboard", test_name="Advanced Financial - Dashboard")
        self.test_endpoint("/advanced-financial/forecasting", test_name="Advanced Financial - Forecasting")
        self.test_endpoint("/advanced-financial/risk-analysis", test_name="Advanced Financial - Risk Analysis")
        
        # Test AI Content API
        self.test_endpoint("/ai-content/templates", test_name="AI Content - Templates")
        self.test_endpoint("/ai-content/suggestions", test_name="AI Content - Suggestions")
        
        # Test Booking API
        self.test_endpoint("/booking/services", test_name="Booking - Services")
        self.test_endpoint("/booking/availability", test_name="Booking - Availability")
        self.test_endpoint("/booking/appointments", test_name="Booking - Appointments")
        
        # Test Content Creation API
        self.test_endpoint("/content-creation/projects", test_name="Content Creation - Projects")
        self.test_endpoint("/content-creation/templates", test_name="Content Creation - Templates")
        self.test_endpoint("/content-creation/assets", test_name="Content Creation - Assets")
        
        # Test Content API
        self.test_endpoint("/content/", test_name="Content - List")
        self.test_endpoint("/content/categories/list", test_name="Content - Categories")
        
        # Test Customer Experience API
        self.test_endpoint("/customer-experience/dashboard", test_name="Customer Experience - Dashboard")
        self.test_endpoint("/customer-experience/journey-mapping", test_name="Customer Experience - Journey Mapping")
        self.test_endpoint("/customer-experience/feedback", test_name="Customer Experience - Feedback")
        
        # Test Integration API
        self.test_endpoint("/integration/available", test_name="Integration - Available")
        self.test_endpoint("/integration/connected", test_name="Integration - Connected")
        
        # Test Media API
        self.test_endpoint("/media/files", test_name="Media - Files")
        self.test_endpoint("/media/folders/list", test_name="Media - Folders")
        
        # Test Social Email API
        self.test_endpoint("/social-email/campaigns", test_name="Social Email - Campaigns")
        self.test_endpoint("/social-email/templates", test_name="Social Email - Templates")
        
        # Test User API
        self.test_endpoint("/user/profile", test_name="User - Profile")
        self.test_endpoint("/user/preferences", test_name="User - Preferences")
        self.test_endpoint("/user/activity", test_name="User - Activity")
        
        # Test Workspace API
        self.test_endpoint("/workspace/list", test_name="Workspace - List")
    
    def test_existing_priority_apis(self):
        """Test existing priority API endpoints"""
        print("\n=== Testing Existing Priority APIs ===")
        
        # Test User Management (existing users API)
        self.test_endpoint("/users/profile", test_name="Users - Profile")
        self.test_endpoint("/users/preferences", test_name="Users - Preferences")
        
        # Test Workspace Management (existing workspaces API)
        self.test_endpoint("/workspaces", test_name="Workspaces - List")
        
        # Test Integration endpoints (existing integrations API)
        self.test_endpoint("/integrations/available", test_name="Integrations - Available")
        self.test_endpoint("/integrations/connected", test_name="Integrations - Connected")
        
        # Test some working endpoints from the test_result.md
        self.test_endpoint("/compliance/framework-status", test_name="Compliance - Framework Status")
        self.test_endpoint("/backup/comprehensive-status", test_name="Backup - Status")
        self.test_endpoint("/monitoring/system-health", test_name="Monitoring - System Health")
    
    def run_comprehensive_test(self):
        """Run comprehensive backend testing"""
        print("🚀 Starting Comprehensive Backend API Testing")
        print(f"Backend URL: {BACKEND_URL}")
        print(f"Test Credentials: {TEST_EMAIL}")
        print("=" * 60)
        
        # Test basic connectivity
        if not self.test_health_check():
            print("❌ Backend health check failed. Stopping tests.")
            return False
        
        # Test authentication
        if not self.test_authentication():
            print("❌ Authentication failed. Some tests may not work.")
        
        # Test newly created API modules
        self.test_newly_created_apis()
        
        # Test existing priority APIs
        self.test_existing_priority_apis()
        
        # Print summary
        self.print_summary()
        
        return True
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 60)
        print("📊 TEST SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result["success"])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result["success"]:
                    print(f"  - {result['test']}: {result['message']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result["success"]:
                print(f"  - {result['test']}: {result['message']}")

if __name__ == "__main__":
    tester = BackendTester()
    success = tester.run_comprehensive_test()
    sys.exit(0 if success else 1)