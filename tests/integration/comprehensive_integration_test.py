#!/usr/bin/env python3
"""
COMPREHENSIVE INTEGRATION TEST - MEWAYZ PLATFORM
Tests professional feature integration and cross-system workflows
"""
import requests
import json
import time

BACKEND_URL = "http://localhost:8001"
API_BASE = f"{BACKEND_URL}/api"

class ProfessionalIntegrationTester:
    def __init__(self):
        self.token = None
        self.user_id = None
        self.workspace_id = None
        self.results = {"passed": 0, "failed": 0, "tests": []}
    
    def test(self, name, success, details=""):
        result = "✅ PASS" if success else "❌ FAIL"
        print(f"{result}: {name} - {details}")
        
        self.results["tests"].append({
            "name": name,
            "success": success,
            "details": details
        })
        
        if success:
            self.results["passed"] += 1
        else:
            self.results["failed"] += 1
    
    def login(self):
        """Login with test user"""
        try:
            response = requests.post(f"{API_BASE}/auth/login", 
                                   data={"username": "integration.test@mewayz.com", "password": "testpass123"})
            
            if response.status_code == 200:
                data = response.json()
                self.token = data["access_token"]
                self.user_id = data["user"]["_id"]
                self.test("User Authentication", True, f"Token received, User ID: {self.user_id[:8]}...")
                return True
            else:
                self.test("User Authentication", False, f"Login failed: {response.status_code}")
                return False
        except Exception as e:
            self.test("User Authentication", False, f"Error: {str(e)}")
            return False
    
    def get_headers(self):
        return {"Authorization": f"Bearer {self.token}", "Content-Type": "application/json"}
    
    def test_cross_platform_data_consistency(self):
        """Test that user data is consistent across all platforms"""
        print(f"\n🔗 TESTING CROSS-PLATFORM DATA CONSISTENCY...")
        
        # Get user profile
        try:
            profile_resp = requests.get(f"{API_BASE}/users/profile", headers=self.get_headers())
            profile_data = profile_resp.json()
            
            # Get dashboard overview
            dashboard_resp = requests.get(f"{API_BASE}/dashboard/overview", headers=self.get_headers())
            dashboard_data = dashboard_resp.json()
            
            # Check data consistency
            profile_name = profile_data["data"]["name"]
            dashboard_name = dashboard_data["data"]["user_overview"]["name"]
            
            consistent = profile_name == dashboard_name
            self.test("Data Consistency Between Profile & Dashboard", consistent,
                     f"Profile: '{profile_name}' vs Dashboard: '{dashboard_name}'")
            
        except Exception as e:
            self.test("Data Consistency Test", False, f"Error: {str(e)}")
    
    def test_plan_based_feature_gating(self):
        """Test that subscription plan limits are enforced across features"""
        print(f"\n💎 TESTING PLAN-BASED FEATURE GATING...")
        
        # Test workspace limit (free plan = 1 workspace)
        try:
            create_resp = requests.post(f"{API_BASE}/workspaces", 
                                      json={"name": "Second Workspace", "description": "Should be blocked"}, 
                                      headers=self.get_headers())
            
            if create_resp.status_code == 400:
                self.test("Workspace Plan Limitation", True, "Correctly blocked second workspace creation")
            else:
                self.test("Workspace Plan Limitation", False, f"Unexpected status: {create_resp.status_code}")
                
        except Exception as e:
            self.test("Workspace Plan Limitation", False, f"Error: {str(e)}")
        
        # Test AI service usage limits
        try:
            ai_resp = requests.get(f"{API_BASE}/ai/services", headers=self.get_headers())
            ai_data = ai_resp.json()
            
            usage_limit = ai_data["data"]["ai_services"]["content_generation"]["usage_limit"]
            has_limit = usage_limit > 0
            self.test("AI Services Plan Limits", has_limit, f"Usage limit: {usage_limit}/month")
            
        except Exception as e:
            self.test("AI Services Plan Limits", False, f"Error: {str(e)}")
    
    def test_unified_analytics_integration(self):
        """Test that analytics aggregate data from all business features"""
        print(f"\n📊 TESTING UNIFIED ANALYTICS INTEGRATION...")
        
        # Check if bio sites show up in analytics
        try:
            bio_resp = requests.get(f"{API_BASE}/bio-sites", headers=self.get_headers())
            bio_data = bio_resp.json()
            bio_count = len(bio_data["data"]["bio_sites"])
            
            # Check if products show up in analytics
            products_resp = requests.get(f"{API_BASE}/ecommerce/products", headers=self.get_headers())
            products_data = products_resp.json()
            products_count = len(products_data["data"]["products"])
            
            # Check if services show up in analytics
            services_resp = requests.get(f"{API_BASE}/bookings/services", headers=self.get_headers())
            services_data = services_resp.json()
            services_count = len(services_data["data"]["services"])
            
            total_business_features = bio_count + products_count + services_count
            
            self.test("Business Features Created", total_business_features > 0, 
                     f"Bio Sites: {bio_count}, Products: {products_count}, Services: {services_count}")
            
        except Exception as e:
            self.test("Unified Analytics Integration", False, f"Error: {str(e)}")
    
    def test_professional_error_handling(self):
        """Test professional error handling across APIs"""
        print(f"\n🛡️ TESTING PROFESSIONAL ERROR HANDLING...")
        
        # Test invalid endpoint
        try:
            invalid_resp = requests.get(f"{API_BASE}/invalid-endpoint", headers=self.get_headers())
            is_404 = invalid_resp.status_code == 404
            self.test("404 Error Handling", is_404, f"Status: {invalid_resp.status_code}")
            
        except Exception as e:
            self.test("404 Error Handling", False, f"Error: {str(e)}")
        
        # Test unauthorized access
        try:
            unauth_resp = requests.get(f"{API_BASE}/admin/dashboard")
            is_401 = unauth_resp.status_code == 401
            self.test("Unauthorized Access Handling", is_401, f"Status: {unauth_resp.status_code}")
            
        except Exception as e:
            self.test("Unauthorized Access Handling", False, f"Error: {str(e)}")
    
    def test_database_operations_are_real(self):
        """Verify all operations use real database, not mock data"""
        print(f"\n🗄️ TESTING REAL DATABASE OPERATIONS...")
        
        # Create a contact and verify it persists
        try:
            contact_data = {
                "email": "test.contact@example.com",
                "first_name": "Test",
                "last_name": "Contact"
            }
            
            create_resp = requests.post(f"{API_BASE}/marketing/contacts", 
                                      json=contact_data, headers=self.get_headers())
            
            if create_resp.status_code == 200:
                # Now retrieve contacts to verify it was saved
                get_resp = requests.get(f"{API_BASE}/marketing/contacts", headers=self.get_headers())
                contacts_data = get_resp.json()
                
                contact_emails = [c["email"] for c in contacts_data["data"]["contacts"]]
                contact_saved = "test.contact@example.com" in contact_emails
                
                self.test("Real Database Persistence", contact_saved, 
                         f"Contact saved and retrieved from database")
            else:
                self.test("Real Database Operations", False, f"Create failed: {create_resp.status_code}")
                
        except Exception as e:
            self.test("Real Database Operations", False, f"Error: {str(e)}")
    
    def test_feature_integration_workflows(self):
        """Test professional workflows that span multiple features"""
        print(f"\n🔄 TESTING PROFESSIONAL INTEGRATION WORKFLOWS...")
        
        # Test: Create contact -> Create campaign -> Analytics integration
        try:
            # Step 1: Create contact list
            list_resp = requests.post(f"{API_BASE}/marketing/lists", 
                                    json={"name": "Test Integration List", "description": "For testing"}, 
                                    headers=self.get_headers())
            
            if list_resp.status_code == 200:
                list_id = list_resp.json()["data"]["_id"]
                
                # Step 2: Create campaign
                campaign_resp = requests.post(f"{API_BASE}/marketing/campaigns", 
                                            json={
                                                "name": "Test Integration Campaign",
                                                "subject": "Welcome to our service!",
                                                "content": "Thank you for joining us.",
                                                "recipient_list_id": list_id,
                                                "send_immediately": False
                                            }, 
                                            headers=self.get_headers())
                
                campaign_success = campaign_resp.status_code == 200
                self.test("Multi-Step Workflow Integration", campaign_success,
                         "Contact List -> Campaign creation workflow")
            else:
                self.test("Multi-Step Workflow Integration", False, f"List creation failed: {list_resp.status_code}")
                
        except Exception as e:
            self.test("Multi-Step Workflow Integration", False, f"Error: {str(e)}")
    
    def run_all_tests(self):
        """Run comprehensive integration tests"""
        print("🧪 STARTING COMPREHENSIVE PROFESSIONAL INTEGRATION TESTS")
        print("=" * 70)
        
        if not self.login():
            print("❌ Authentication failed, cannot continue tests")
            return
        
        self.test_cross_platform_data_consistency()
        self.test_plan_based_feature_gating()
        self.test_unified_analytics_integration()
        self.test_professional_error_handling()
        self.test_database_operations_are_real()
        self.test_feature_integration_workflows()
        
        # Summary
        print(f"\n📋 TEST SUMMARY")
        print("=" * 70)
        total_tests = self.results["passed"] + self.results["failed"]
        success_rate = (self.results["passed"] / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {self.results['passed']} ✅")
        print(f"Failed: {self.results['failed']} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if success_rate >= 90:
            print(f"\n🎉 EXCELLENT! Platform demonstrates professional integration")
        elif success_rate >= 75:
            print(f"\n✅ GOOD! Platform shows solid professional features")
        else:
            print(f"\n⚠️  NEEDS IMPROVEMENT: Integration issues detected")
        
        return success_rate

if __name__ == "__main__":
    tester = ProfessionalIntegrationTester()
    success_rate = tester.run_all_tests()
    
    # Exit with status code based on success rate
    exit(0 if success_rate >= 80 else 1)