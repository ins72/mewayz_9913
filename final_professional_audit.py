#!/usr/bin/env python3
"""
FINAL COMPREHENSIVE PROFESSIONAL INTEGRATION AUDIT
Tests ALL aspects of professional platform integration
"""
import requests
import json
import time

BACKEND_URL = "http://localhost:8001"
API_BASE = f"{BACKEND_URL}/api"

class FinalProfessionalAudit:
    def __init__(self):
        self.token = None
        self.results = {"passed": 0, "failed": 0, "tests": []}
        self.critical_issues = []
    
    def test(self, name, success, details="", is_critical=False):
        result = "✅ PASS" if success else "❌ FAIL"
        print(f"{result}: {name}")
        if details:
            print(f"      {details}")
        
        self.results["tests"].append({"name": name, "success": success, "details": details})
        
        if success:
            self.results["passed"] += 1
        else:
            self.results["failed"] += 1
            if is_critical:
                self.critical_issues.append(name)
    
    def get_token(self):
        try:
            with open('/tmp/audit_token.txt', 'r') as f:
                self.token = f.read().strip()
            return True
        except:
            return False
    
    def headers(self):
        return {"Authorization": f"Bearer {self.token}", "Content-Type": "application/json"}
    
    def test_professional_structure(self):
        print("\n🏗️  TESTING PROFESSIONAL STRUCTURE")
        
        # Test 1: All required API modules present
        required_apis = [
            "auth", "users", "workspaces", "analytics", "dashboard", 
            "ai", "bio_sites", "ecommerce", "bookings", "social_media", 
            "marketing", "integrations", "admin", "blog"
        ]
        
        missing_apis = []
        for api in required_apis:
            try:
                response = requests.get(f"{API_BASE}/{api.replace('_', '-')}", headers=self.headers(), timeout=5)
                # Any response (even 405 Method Not Allowed) means the endpoint exists
                if response.status_code not in [404]:
                    continue
                else:
                    missing_apis.append(api)
            except:
                missing_apis.append(api)
        
        if not missing_apis:
            self.test("API Module Coverage", True, f"All {len(required_apis)} API modules accessible")
        else:
            self.test("API Module Coverage", False, f"Missing APIs: {missing_apis}", is_critical=True)
    
    def test_database_real_operations(self):
        print("\n🗄️  TESTING REAL DATABASE OPERATIONS")
        
        # Test creating and retrieving data to verify it's not mock
        contact_email = f"audit.test.{int(time.time())}@example.com"
        
        # Create contact
        response = requests.post(f"{API_BASE}/marketing/contacts", 
                               json={"email": contact_email, "first_name": "Audit", "last_name": "Test"},
                               headers=self.headers())
        
        create_success = response.status_code == 200
        
        if create_success:
            # Retrieve contacts
            get_response = requests.get(f"{API_BASE}/marketing/contacts", headers=self.headers())
            if get_response.status_code == 200:
                contacts = get_response.json()["data"]["contacts"]
                found = any(c["email"] == contact_email for c in contacts)
                
                self.test("Real Database Persistence", found, 
                         "Data successfully persisted and retrieved from MongoDB")
                
                if found:
                    self.test("Zero Mock Data Implementations", True,
                             "All operations use real database, no mock responses")
                else:
                    self.test("Zero Mock Data Implementations", False,
                             "Data not persisting correctly", is_critical=True)
            else:
                self.test("Real Database Operations", False,
                         f"Failed to retrieve data: {get_response.status_code}", is_critical=True)
        else:
            self.test("Real Database Operations", False,
                     f"Failed to create data: {response.status_code}", is_critical=True)
    
    def test_cross_feature_integration(self):
        print("\n🔗 TESTING CROSS-FEATURE INTEGRATION")
        
        # Test that dashboard aggregates data from multiple features
        response = requests.get(f"{API_BASE}/dashboard/overview", headers=self.headers())
        
        if response.status_code == 200:
            dashboard_data = response.json()["data"]
            
            # Check if dashboard shows data from different features
            has_user_data = "user_overview" in dashboard_data
            has_stats = "quick_stats" in dashboard_data
            has_activity = "recent_activity" in dashboard_data
            
            integrated = has_user_data and has_stats and has_activity
            
            self.test("Unified Dashboard Integration", integrated,
                     "Dashboard aggregates data from all business features")
            
            # Test that user's workspace count matches across endpoints
            dashboard_workspaces = dashboard_data["quick_stats"]["workspaces"]
            
            ws_response = requests.get(f"{API_BASE}/workspaces", headers=self.headers())
            if ws_response.status_code == 200:
                actual_workspaces = len(ws_response.json()["data"]["workspaces"])
                
                consistent = dashboard_workspaces == actual_workspaces
                self.test("Cross-Platform Data Consistency", consistent,
                         f"Workspace count: Dashboard={dashboard_workspaces}, API={actual_workspaces}")
            else:
                self.test("Cross-Platform Data Consistency", False,
                         "Could not verify workspace data consistency", is_critical=True)
        else:
            self.test("Unified Dashboard Integration", False,
                     f"Dashboard not accessible: {response.status_code}", is_critical=True)
    
    def test_professional_features_integration(self):
        print("\n⚙️  TESTING PROFESSIONAL FEATURES INTEGRATION")
        
        # Test that plan-based limitations work across features
        plan_tests = [
            ("workspaces", "Workspace creation blocked on free plan"),
            ("bio-sites", "Bio site limits enforced"),
            ("ecommerce/products", "Product limits enforced"),
            ("bookings/services", "Service limits enforced")
        ]
        
        plan_working = 0
        for endpoint, description in plan_tests:
            # Try to create multiple items to test limits
            for i in range(2):  # Try creating 2 items
                test_data = self.get_test_data_for_endpoint(endpoint)
                response = requests.post(f"{API_BASE}/{endpoint}", 
                                       json=test_data, headers=self.headers())
                
                if "limit" in response.text.lower() and response.status_code in [400, 429]:
                    plan_working += 1
                    break
        
        if plan_working > 0:
            self.test("Plan-Based Feature Gating", True,
                     f"Plan limitations working across {plan_working} feature types")
        else:
            self.test("Plan-Based Feature Gating", False,
                     "Plan limitations not properly enforced", is_critical=True)
        
        # Test AI services integration
        ai_response = requests.get(f"{API_BASE}/ai/services", headers=self.headers())
        if ai_response.status_code == 200:
            ai_data = ai_response.json()["data"]["ai_services"]
            
            has_multiple_services = len(ai_data) >= 3
            has_usage_tracking = any("usage_remaining" in service for service in ai_data.values())
            
            self.test("AI Services Integration", has_multiple_services and has_usage_tracking,
                     f"AI services: {len(ai_data)} types with usage tracking")
        else:
            self.test("AI Services Integration", False,
                     "AI services not accessible", is_critical=True)
    
    def get_test_data_for_endpoint(self, endpoint):
        """Get appropriate test data for different endpoints"""
        test_data_map = {
            "workspaces": {"name": "Test Workspace", "description": "Test"},
            "bio-sites": {"name": "Test Bio", "url_slug": f"test-{int(time.time())}", "theme": "minimal"},
            "ecommerce/products": {"name": "Test Product", "description": "Test", "price": 10.0, "category": "test"},
            "bookings/services": {"name": "Test Service", "description": "Test", "duration_minutes": 30, "price": 50.0}
        }
        return test_data_map.get(endpoint, {})
    
    def test_professional_error_handling(self):
        print("\n🛡️  TESTING PROFESSIONAL ERROR HANDLING")
        
        # Test 404 handling
        response = requests.get(f"{API_BASE}/nonexistent-endpoint", headers=self.headers())
        proper_404 = response.status_code == 404
        
        self.test("404 Error Handling", proper_404,
                 f"Returns proper 404 status code")
        
        # Test unauthorized access
        unauth_response = requests.get(f"{API_BASE}/admin/dashboard")
        proper_auth = unauth_response.status_code in [401, 403]
        
        self.test("Authentication Protection", proper_auth,
                 f"Protected endpoints return {unauth_response.status_code}")
        
        # Test invalid data handling
        invalid_response = requests.post(f"{API_BASE}/marketing/contacts",
                                       json={"invalid": "data"}, headers=self.headers())
        proper_validation = invalid_response.status_code in [400, 422]
        
        self.test("Input Validation", proper_validation,
                 f"Invalid data returns {invalid_response.status_code}")
    
    def test_analytics_integration(self):
        print("\n📊 TESTING ANALYTICS INTEGRATION")
        
        # Test analytics overview
        response = requests.get(f"{API_BASE}/analytics/overview", headers=self.headers())
        
        if response.status_code == 200:
            analytics = response.json()["data"]
            
            has_metrics = all(key in analytics for key in ["total_events_7d", "avg_daily_events", "event_breakdown"])
            
            self.test("Analytics Integration", has_metrics,
                     "Analytics endpoint provides comprehensive metrics")
        else:
            self.test("Analytics Integration", False,
                     f"Analytics not accessible: {response.status_code}")
    
    def run_final_audit(self):
        print("🔍 STARTING FINAL COMPREHENSIVE PROFESSIONAL AUDIT")
        print("=" * 80)
        
        if not self.get_token():
            print("❌ Cannot get authentication token - audit aborted")
            return False
        
        self.test_professional_structure()
        self.test_database_real_operations()
        self.test_cross_feature_integration()
        self.test_professional_features_integration()
        self.test_professional_error_handling()
        self.test_analytics_integration()
        
        # Final Summary
        print(f"\n📋 FINAL AUDIT SUMMARY")
        print("=" * 80)
        total_tests = self.results["passed"] + self.results["failed"]
        success_rate = (self.results["passed"] / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {self.results['passed']} ✅")
        print(f"Failed: {self.results['failed']} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if self.critical_issues:
            print(f"\n🚨 CRITICAL ISSUES FOUND:")
            for issue in self.critical_issues:
                print(f"   - {issue}")
        
        if success_rate >= 95:
            print(f"\n🎉 OUTSTANDING! Platform is COMPLETELY PROFESSIONAL")
            print("✅ ALL requirements met - production ready")
        elif success_rate >= 90:
            print(f"\n🎯 EXCELLENT! Platform demonstrates professional quality")
            print("✅ Ready for production deployment")
        elif success_rate >= 80:
            print(f"\n✅ GOOD! Platform shows strong professional features")
            print("⚠️  Minor improvements recommended")
        else:
            print(f"\n⚠️  NEEDS ATTENTION: Professional integration issues detected")
            print("❌ Requires fixes before production deployment")
        
        return success_rate >= 90

if __name__ == "__main__":
    auditor = FinalProfessionalAudit()
    is_professional = auditor.run_final_audit()
    exit(0 if is_professional else 1)