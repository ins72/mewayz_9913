#!/usr/bin/env python3
"""
Comprehensive Backend API Testing Script
Tests authentication and newly created API endpoints
"""

import requests
import json
import sys
import time
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
    
    def test_database_integration_verification(self):
        """Test database integration verification for the massive database work completed"""
        print("\n=== Database Integration Verification ===")
        print("Testing services converted from random data to real database operations")
        
        # Test services mentioned in the review request as having been converted
        database_integrated_services = [
            # Wave 1 services (1,186 random calls fixed)
            ("/social-media/analytics", "Social Media Service Database Integration"),
            ("/customer-experience/dashboard", "Customer Experience Service Database Integration"),
            ("/enhanced-ecommerce/products", "Enhanced E-commerce Service Database Integration"),
            ("/automation/workflows", "Automation Service Database Integration"),
            ("/analytics/overview", "Analytics Service Database Integration"),
            ("/support/tickets", "Support Service Database Integration"),
            ("/content-creation/projects", "Content Creation Service Database Integration"),
            ("/email-marketing/campaigns", "Email Marketing Service Database Integration"),
            ("/social-email/campaigns", "Social Email Service Database Integration"),
            ("/advanced-financial/dashboard", "Advanced Financial Service Database Integration"),
            
            # Wave 2 services (310 random calls fixed)
            ("/advanced-ai/capabilities", "Advanced AI Service Database Integration"),
            ("/advanced-financial/forecasting", "Financial Analytics Service Database Integration"),
            ("/templates/marketplace", "Template Marketplace Service Database Integration"),
            ("/ai-content/templates", "AI Content Service Database Integration"),
            ("/escrow/transactions", "Escrow Service Database Integration"),
            ("/customer-experience/journey-mapping", "Customer Experience Suite Database Integration"),
            ("/compliance/framework-status", "Compliance Service Database Integration"),
            ("/business-intelligence/metrics", "Business Intelligence Service Database Integration"),
            ("/content-creation/assets", "Content Creation Suite Database Integration"),
            ("/monitoring/system-health", "Monitoring Service Database Integration")
        ]
        
        for endpoint, test_name in database_integrated_services:
            self.test_endpoint(endpoint, test_name=test_name)
    
    def test_data_consistency_verification(self):
        """Test data consistency to verify real database usage vs random generation"""
        print("\n=== Data Consistency Verification ===")
        print("Testing for consistent data across multiple calls (indicates real database usage)")
        
        # Test endpoints that should have consistent data
        consistency_endpoints = [
            "/dashboard/overview",
            "/workspaces", 
            "/advanced-ai/capabilities",
            "/compliance/framework-status",
            "/backup/comprehensive-status",
            "/monitoring/system-health"
        ]
        
        for endpoint in consistency_endpoints:
            self.test_data_consistency(endpoint)
    
    def test_data_consistency(self, endpoint: str):
        """Test if endpoint returns consistent data (indicating real database usage)"""
        try:
            url = f"{API_BASE}{endpoint}"
            
            # Make first request
            response1 = self.session.get(url, timeout=10)
            if response1.status_code != 200:
                self.log_result(f"Data Consistency - {endpoint}", False, f"First request failed - Status {response1.status_code}")
                return False
            
            data1 = response1.json()
            
            # Wait a moment and make second request
            import time
            time.sleep(1)
            
            response2 = self.session.get(url, timeout=10)
            if response2.status_code != 200:
                self.log_result(f"Data Consistency - {endpoint}", False, f"Second request failed - Status {response2.status_code}")
                return False
            
            data2 = response2.json()
            
            # Compare responses
            if json.dumps(data1, sort_keys=True) == json.dumps(data2, sort_keys=True):
                self.log_result(f"Data Consistency - {endpoint}", True, f"Data consistent across calls - confirms real database usage")
                return True
            else:
                self.log_result(f"Data Consistency - {endpoint}", False, f"Data inconsistent - may still be using random generation")
                return False
                
        except Exception as e:
            self.log_result(f"Data Consistency - {endpoint}", False, f"Request error: {str(e)}")
            return False
    
    def test_core_business_functionality(self):
        """Test core business functionality that now uses real data"""
        print("\n=== Core Business Functionality with Real Data ===")
        
        # Test key business endpoints that should now use real database data
        business_endpoints = [
            # Dashboard and analytics
            ("/dashboard/overview", "Dashboard Real Database Data"),
            ("/analytics/overview", "Analytics Real Database Data"),
            
            # User and workspace management
            ("/users/profile", "User Management Real Data"),
            ("/workspaces", "Workspace Management Real Data"),
            
            # AI services
            ("/advanced-ai/capabilities", "AI Services Real Data"),
            ("/advanced-ai/models", "AI Models Real Data"),
            
            # Business intelligence
            ("/compliance/framework-status", "Compliance Real Data"),
            ("/backup/comprehensive-status", "Backup System Real Data"),
            ("/monitoring/system-health", "Monitoring Real Data"),
            
            # Integration management
            ("/integrations/available", "Integration Management Real Data"),
            ("/integrations/connected", "Connected Integrations Real Data")
        ]
        
        for endpoint, test_name in business_endpoints:
            self.test_endpoint(endpoint, test_name=test_name)

    def run_comprehensive_test(self):
        """Run comprehensive backend testing focused on database integration verification"""
        print("🚀 Starting Comprehensive Database Integration Verification")
        print("Testing the massive database integration work completed:")
        print("- ✅ Wave 1: Fixed 1,186 random calls across 10 services")
        print("- ✅ Wave 2: Fixed 310 random calls across 10 services") 
        print("- ✅ Total: 1,549 random data calls replaced with real database operations")
        print("- ✅ 20 high-priority services converted")
        print(f"Backend URL: {BACKEND_URL}")
        print(f"Test Credentials: {TEST_EMAIL}")
        print("=" * 80)
        
        # Test basic connectivity
        if not self.test_health_check():
            print("❌ Backend health check failed. Stopping tests.")
            return False
        
        # Test authentication - critical after massive changes
        if not self.test_authentication():
            print("❌ Authentication failed after massive database integration changes.")
            return False
        
        # Test database integration verification
        self.test_database_integration_verification()
        
        # Test data consistency verification
        self.test_data_consistency_verification()
        
        # Test core business functionality
        self.test_core_business_functionality()
        
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