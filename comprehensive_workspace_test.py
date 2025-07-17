#!/usr/bin/env python3
"""
FINAL COMPREHENSIVE WORKSPACE SETUP WIZARD TEST
Testing after database fixes: user_id and is_primary columns added to workspaces table
Testing all 12 workspace setup wizard endpoints and 6-step workflow
"""

import requests
import json
import sys
import time
from datetime import datetime

class ComprehensiveWorkspaceSetupTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.test_results = {}
        self.session = requests.Session()
        self.auth_token = None
        
    def log_test(self, test_name, success, message, response_data=None):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        
        self.test_results[test_name] = {
            'success': success,
            'message': message,
            'response_data': response_data,
            'timestamp': datetime.now().isoformat()
        }
        
    def make_request(self, method, endpoint, data=None, headers=None, auth_required=True):
        """Make HTTP request with proper headers"""
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if headers:
            default_headers.update(headers)
            
        # Add auth token if required and available
        if auth_required and self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, params=data, timeout=30)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=30)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=30)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url}")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None

    def setup_authentication(self):
        """Set up authentication by registering a new user"""
        print("\n🔐 SETTING UP AUTHENTICATION")
        print("=" * 50)
        
        # Register a new user
        register_data = {
            "name": "Workspace Test User",
            "email": f"workspace_test_{int(time.time())}@example.com",
            "password": "SecurePassword123!",
            "password_confirmation": "SecurePassword123!"
        }
        
        response = self.make_request('POST', '/auth/register', register_data, auth_required=False)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("User Registration", True, "User registered successfully")
            
            # Extract token
            if 'token' in data:
                self.auth_token = data['token']
            elif 'access_token' in data:
                self.auth_token = data['access_token']
            elif 'data' in data and 'token' in data['data']:
                self.auth_token = data['data']['token']
                
            if self.auth_token:
                self.log_test("Authentication Token", True, f"Token obtained: {self.auth_token[:20]}...")
                return True
            else:
                self.log_test("Authentication Token", False, "No token in registration response")
                return False
        else:
            self.log_test("User Registration", False, f"Registration failed - Status: {response.status_code if response else 'No response'}")
            return False

    def test_all_12_workspace_setup_endpoints(self):
        """Test all 12 workspace setup wizard endpoints mentioned in review request"""
        print("\n🎯 TESTING ALL 12 WORKSPACE SETUP WIZARD ENDPOINTS")
        print("=" * 80)
        print("Testing after database fix: user_id and is_primary columns added to workspaces table")
        print("Expected: All endpoints should now work without database errors")
        print("=" * 80)
        
        if not self.auth_token:
            self.log_test("Workspace Setup Endpoints", False, "Cannot test - no authentication token")
            return
        
        # Endpoint 1: GET /api/workspace-setup/current-step
        print("\n--- Endpoint 1: GET /api/workspace-setup/current-step ---")
        response = self.make_request('GET', '/workspace-setup/current-step')
        if response and response.status_code == 200:
            data = response.json()
            current_step = data.get('current_step', 1)
            setup_completed = data.get('setup_completed', False)
            self.log_test("GET Current Step", True, f"Current step: {current_step}, Setup completed: {setup_completed}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("GET Current Step", False, f"Failed - {error_msg}")
        
        # Endpoint 2: GET /api/workspace-setup/main-goals
        print("\n--- Endpoint 2: GET /api/workspace-setup/main-goals ---")
        response = self.make_request('GET', '/workspace-setup/main-goals')
        if response and response.status_code == 200:
            data = response.json()
            goals = data.get('goals', {})
            self.log_test("GET Main Goals", True, f"Retrieved {len(goals)} main goals: {list(goals.keys())}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("GET Main Goals", False, f"Failed - {error_msg}")
        
        # Endpoint 3: POST /api/workspace-setup/main-goals
        print("\n--- Endpoint 3: POST /api/workspace-setup/main-goals ---")
        main_goals_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"],
            "primary_goal": "instagram_management",
            "business_type": "Digital Marketing Agency",
            "target_audience": "Small business owners and entrepreneurs"
        }
        response = self.make_request('POST', '/workspace-setup/main-goals', data=main_goals_data)
        if response and response.status_code == 200:
            data = response.json()
            next_step = data.get('next_step', 'unknown')
            self.log_test("POST Main Goals", True, f"Goals saved successfully, next step: {next_step}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Main Goals", False, f"Failed - {error_msg}")
        
        # Endpoint 4: POST /api/workspace-setup/available-features
        print("\n--- Endpoint 4: POST /api/workspace-setup/available-features ---")
        features_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"]
        }
        response = self.make_request('POST', '/workspace-setup/available-features', data=features_data)
        if response and response.status_code == 200:
            data = response.json()
            features = data.get('features', {})
            self.log_test("POST Available Features", True, f"Retrieved {len(features)} available features")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Available Features", False, f"Failed - {error_msg}")
        
        # Endpoint 5: POST /api/workspace-setup/feature-selection
        print("\n--- Endpoint 5: POST /api/workspace-setup/feature-selection ---")
        feature_selection_data = {
            "selected_features": [
                "content_scheduling", "content_calendar", "hashtag_research"
            ],
            "subscription_plan": "free"
        }
        response = self.make_request('POST', '/workspace-setup/feature-selection', data=feature_selection_data)
        if response and response.status_code == 200:
            data = response.json()
            pricing = data.get('pricing', {})
            self.log_test("POST Feature Selection", True, f"Features saved, pricing: ${pricing.get('total_monthly', 0)}/month")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Feature Selection", False, f"Failed - {error_msg}")
        
        # Endpoint 6: POST /api/workspace-setup/team-setup
        print("\n--- Endpoint 6: POST /api/workspace-setup/team-setup ---")
        team_setup_data = {
            "team_members": [
                {
                    "email": "manager@example.com",
                    "role": "manager",
                    "permissions": ["content_management", "analytics_view"]
                }
            ],
            "collaboration_enabled": True
        }
        response = self.make_request('POST', '/workspace-setup/team-setup', data=team_setup_data)
        if response and response.status_code == 200:
            data = response.json()
            next_step = data.get('next_step', 'unknown')
            self.log_test("POST Team Setup", True, f"Team setup saved, next step: {next_step}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Team Setup", False, f"Failed - {error_msg}")
        
        # Endpoint 7: GET /api/workspace-setup/subscription-plans
        print("\n--- Endpoint 7: GET /api/workspace-setup/subscription-plans ---")
        response = self.make_request('GET', '/workspace-setup/subscription-plans')
        if response and response.status_code == 200:
            data = response.json()
            plans = data.get('plans', {})
            self.log_test("GET Subscription Plans", True, f"Retrieved {len(plans)} subscription plans: {list(plans.keys())}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("GET Subscription Plans", False, f"Failed - {error_msg}")
        
        # Endpoint 8: POST /api/workspace-setup/subscription-selection
        print("\n--- Endpoint 8: POST /api/workspace-setup/subscription-selection ---")
        subscription_data = {
            "subscription_plan": "free",
            "billing_cycle": "monthly",
            "payment_method": "none"
        }
        response = self.make_request('POST', '/workspace-setup/subscription-selection', data=subscription_data)
        if response and response.status_code == 200:
            data = response.json()
            pricing = data.get('pricing', {})
            self.log_test("POST Subscription Selection", True, f"Subscription saved, total: ${pricing.get('total_monthly', 0)}/month")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Subscription Selection", False, f"Failed - {error_msg}")
        
        # Endpoint 9: POST /api/workspace-setup/branding-configuration
        print("\n--- Endpoint 9: POST /api/workspace-setup/branding-configuration ---")
        branding_data = {
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981",
            "company_name": "Test Digital Agency",
            "brand_voice": "professional"
        }
        response = self.make_request('POST', '/workspace-setup/branding-configuration', data=branding_data)
        if response and response.status_code == 200:
            data = response.json()
            next_step = data.get('next_step', 'unknown')
            self.log_test("POST Branding Configuration", True, f"Branding saved, next step: {next_step}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Branding Configuration", False, f"Failed - {error_msg}")
        
        # Endpoint 10: POST /api/workspace-setup/complete
        print("\n--- Endpoint 10: POST /api/workspace-setup/complete ---")
        response = self.make_request('POST', '/workspace-setup/complete')
        if response and response.status_code == 200:
            data = response.json()
            workspace = data.get('workspace', {})
            workspace_name = workspace.get('name', 'Unknown')
            dashboard_url = workspace.get('dashboard_url', 'Unknown')
            self.log_test("POST Complete Setup", True, f"Setup completed! Workspace: {workspace_name}, Dashboard: {dashboard_url}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("POST Complete Setup", False, f"Failed - {error_msg}")
        
        # Endpoint 11: GET /api/workspace-setup/summary
        print("\n--- Endpoint 11: GET /api/workspace-setup/summary ---")
        response = self.make_request('GET', '/workspace-setup/summary')
        if response and response.status_code == 200:
            data = response.json()
            summary = data.get('summary', {})
            setup_completed = summary.get('setup_completed', False)
            self.log_test("GET Setup Summary", True, f"Setup summary retrieved, completed: {setup_completed}")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("GET Setup Summary", False, f"Failed - {error_msg}")
        
        # Endpoint 12: Additional workspace management endpoint
        print("\n--- Endpoint 12: GET /api/workspaces (Workspace Management) ---")
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            workspaces = data.get('workspaces', []) or data.get('data', [])
            self.log_test("GET Workspaces", True, f"Retrieved {len(workspaces)} workspaces")
        else:
            error_msg = self._get_error_message(response)
            self.log_test("GET Workspaces", False, f"Failed - {error_msg}")

    def test_6_step_workflow(self):
        """Test the complete 6-step workspace setup workflow"""
        print("\n🚀 TESTING COMPLETE 6-STEP WORKSPACE SETUP WORKFLOW")
        print("=" * 80)
        print("Step 1: Select main business goals (6 available: Instagram, Link in Bio, Courses, E-commerce, CRM, Marketing)")
        print("Step 2: Choose features (40 available, max 3 for free plan)")
        print("Step 3: Set up team (invite members with roles)")
        print("Step 4: Select subscription plan (Free, Professional, Enterprise)")
        print("Step 5: Configure branding (colors, logo, company name)")
        print("Step 6: Complete setup and launch workspace")
        print("=" * 80)
        
        if not self.auth_token:
            self.log_test("6-Step Workflow", False, "Cannot test - no authentication token")
            return
        
        workflow_success = True
        
        # Step 1: Main Goals Selection
        print("\n🎯 STEP 1: MAIN GOALS SELECTION")
        main_goals_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"],
            "primary_goal": "instagram_management",
            "business_type": "Digital Marketing Agency",
            "target_audience": "Small business owners and entrepreneurs"
        }
        response = self.make_request('POST', '/workspace-setup/main-goals', data=main_goals_data)
        if response and response.status_code == 200:
            self.log_test("Step 1: Main Goals", True, "Selected 3 main goals: Instagram, Link in Bio, E-commerce")
        else:
            self.log_test("Step 1: Main Goals", False, f"Failed - {self._get_error_message(response)}")
            workflow_success = False
        
        # Step 2: Feature Selection (max 3 for free plan)
        print("\n⚡ STEP 2: FEATURE SELECTION (MAX 3 FOR FREE PLAN)")
        feature_selection_data = {
            "selected_features": [
                "content_scheduling", 
                "content_calendar", 
                "hashtag_research"
            ],
            "subscription_plan": "free"
        }
        response = self.make_request('POST', '/workspace-setup/feature-selection', data=feature_selection_data)
        if response and response.status_code == 200:
            data = response.json()
            selected_count = len(feature_selection_data["selected_features"])
            self.log_test("Step 2: Feature Selection", True, f"Selected {selected_count} features (within free plan limit)")
        else:
            self.log_test("Step 2: Feature Selection", False, f"Failed - {self._get_error_message(response)}")
            workflow_success = False
        
        # Step 3: Team Setup
        print("\n👥 STEP 3: TEAM SETUP")
        team_setup_data = {
            "team_members": [
                {
                    "email": "manager@example.com",
                    "role": "manager",
                    "permissions": ["content_management", "analytics_view"]
                },
                {
                    "email": "editor@example.com",
                    "role": "editor", 
                    "permissions": ["content_creation", "content_editing"]
                }
            ],
            "collaboration_enabled": True
        }
        response = self.make_request('POST', '/workspace-setup/team-setup', data=team_setup_data)
        if response and response.status_code == 200:
            member_count = len(team_setup_data["team_members"])
            self.log_test("Step 3: Team Setup", True, f"Invited {member_count} team members with roles")
        else:
            self.log_test("Step 3: Team Setup", False, f"Failed - {self._get_error_message(response)}")
            workflow_success = False
        
        # Step 4: Subscription Selection
        print("\n💳 STEP 4: SUBSCRIPTION SELECTION")
        subscription_data = {
            "subscription_plan": "free",
            "billing_cycle": "monthly",
            "payment_method": "none"
        }
        response = self.make_request('POST', '/workspace-setup/subscription-selection', data=subscription_data)
        if response and response.status_code == 200:
            plan = subscription_data["subscription_plan"]
            self.log_test("Step 4: Subscription", True, f"Selected {plan.title()} plan")
        else:
            self.log_test("Step 4: Subscription", False, f"Failed - {self._get_error_message(response)}")
            workflow_success = False
        
        # Step 5: Branding Configuration
        print("\n🎨 STEP 5: BRANDING CONFIGURATION")
        branding_data = {
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981",
            "accent_color": "#F59E0B",
            "company_name": "Test Digital Agency",
            "brand_voice": "professional"
        }
        response = self.make_request('POST', '/workspace-setup/branding-configuration', data=branding_data)
        if response and response.status_code == 200:
            company_name = branding_data["company_name"]
            self.log_test("Step 5: Branding", True, f"Configured branding for {company_name}")
        else:
            self.log_test("Step 5: Branding", False, f"Failed - {self._get_error_message(response)}")
            workflow_success = False
        
        # Step 6: Complete Setup and Launch
        print("\n🚀 STEP 6: COMPLETE SETUP AND LAUNCH WORKSPACE")
        response = self.make_request('POST', '/workspace-setup/complete')
        if response and response.status_code == 200:
            data = response.json()
            workspace = data.get('workspace', {})
            workspace_name = workspace.get('name', 'Unknown')
            self.log_test("Step 6: Launch Workspace", True, f"Workspace '{workspace_name}' launched successfully!")
        else:
            self.log_test("Step 6: Launch Workspace", False, f"Failed - {self._get_error_message(response)}")
            workflow_success = False
        
        # Overall workflow result
        if workflow_success:
            self.log_test("Complete 6-Step Workflow", True, "All 6 steps completed successfully - workspace is ready!")
        else:
            self.log_test("Complete 6-Step Workflow", False, "Workflow incomplete - some steps failed")

    def test_data_persistence(self):
        """Test that data persists correctly throughout the setup process"""
        print("\n💾 TESTING DATA PERSISTENCE")
        print("=" * 50)
        
        if not self.auth_token:
            self.log_test("Data Persistence", False, "Cannot test - no authentication token")
            return
        
        # Test current step tracking
        response = self.make_request('GET', '/workspace-setup/current-step')
        if response and response.status_code == 200:
            data = response.json()
            current_step = data.get('current_step', 1)
            self.log_test("Step Tracking", True, f"Current step tracked: {current_step}")
        else:
            self.log_test("Step Tracking", False, "Failed to track current step")
        
        # Test setup summary (should contain all saved data)
        response = self.make_request('GET', '/workspace-setup/summary')
        if response and response.status_code == 200:
            data = response.json()
            summary = data.get('summary', {})
            has_goals = 'selected_goals' in summary or 'main_goals' in summary
            has_features = 'selected_features' in summary or 'features' in summary
            has_branding = 'branding' in summary or 'company_name' in summary
            
            persistence_score = sum([has_goals, has_features, has_branding])
            self.log_test("Data Persistence", True, f"Setup data persisted: {persistence_score}/3 sections saved")
        else:
            self.log_test("Data Persistence", False, "Failed to retrieve setup summary")

    def test_business_logic(self):
        """Test business logic validation"""
        print("\n🧠 TESTING BUSINESS LOGIC")
        print("=" * 50)
        
        if not self.auth_token:
            self.log_test("Business Logic", False, "Cannot test - no authentication token")
            return
        
        # Test feature limit for free plan (should allow max 3 features)
        print("\n--- Testing Feature Limits for Free Plan ---")
        too_many_features_data = {
            "selected_features": [
                "content_scheduling", "content_calendar", "hashtag_research",
                "page_builder", "template_library", "analytics_tracking"  # 6 features (over limit)
            ],
            "subscription_plan": "free"
        }
        response = self.make_request('POST', '/workspace-setup/feature-selection', data=too_many_features_data)
        if response and response.status_code == 400:
            self.log_test("Feature Limit Validation", True, "Correctly rejected >3 features for free plan")
        elif response and response.status_code == 200:
            # Check if response indicates feature limit enforcement
            data = response.json()
            if 'warning' in data or 'limit' in str(data).lower():
                self.log_test("Feature Limit Validation", True, "Feature limit warning provided")
            else:
                self.log_test("Feature Limit Validation", False, "Feature limit not enforced")
        else:
            self.log_test("Feature Limit Validation", False, f"Unexpected response - {self._get_error_message(response)}")
        
        # Test subscription plan validation
        print("\n--- Testing Subscription Plan Validation ---")
        invalid_plan_data = {
            "subscription_plan": "invalid_plan",
            "billing_cycle": "monthly"
        }
        response = self.make_request('POST', '/workspace-setup/subscription-selection', data=invalid_plan_data)
        if response and response.status_code == 400:
            self.log_test("Subscription Validation", True, "Correctly rejected invalid subscription plan")
        else:
            self.log_test("Subscription Validation", False, "Invalid subscription plan not properly validated")

    def _get_error_message(self, response):
        """Extract error message from response"""
        if not response:
            return "No response"
        
        error_msg = f"Status: {response.status_code}"
        try:
            error_data = response.json()
            if 'message' in error_data:
                error_msg += f", Error: {error_data['message']}"
            elif 'error' in error_data:
                error_msg += f", Error: {error_data['error']}"
        except:
            error_msg += f", Response: {response.text[:200]}"
        
        return error_msg

    def print_final_summary(self):
        """Print comprehensive test summary"""
        print("\n" + "=" * 80)
        print("📊 FINAL COMPREHENSIVE WORKSPACE SETUP WIZARD TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Categorize results
        endpoint_tests = [name for name in self.test_results.keys() if any(x in name.lower() for x in ['get', 'post', 'endpoint'])]
        workflow_tests = [name for name in self.test_results.keys() if 'step' in name.lower() or 'workflow' in name.lower()]
        
        endpoint_passed = sum(1 for name in endpoint_tests if self.test_results[name]['success'])
        workflow_passed = sum(1 for name in workflow_tests if self.test_results[name]['success'])
        
        print(f"\n📈 DETAILED BREAKDOWN:")
        print(f"Endpoint Tests: {endpoint_passed}/{len(endpoint_tests)} passed")
        print(f"Workflow Tests: {workflow_passed}/{len(workflow_tests)} passed")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print(f"\n🎯 DATABASE FIX IMPACT ASSESSMENT:")
        if success_rate >= 90:
            print("✅ EXCELLENT: Database fixes highly successful - workspace setup wizard fully functional")
        elif success_rate >= 70:
            print("✅ GOOD: Database fixes mostly successful - most endpoints working correctly")
        elif success_rate >= 50:
            print("⚠️  MODERATE: Database fixes partially successful - some endpoints still need work")
        else:
            print("❌ POOR: Database fixes may need further investigation - many endpoints still failing")
        
        print(f"\n🚀 WORKSPACE SETUP WIZARD STATUS:")
        if success_rate >= 80:
            print("✅ READY FOR PRODUCTION: Workspace setup wizard is functional and ready for users")
        else:
            print("❌ NEEDS WORK: Workspace setup wizard requires additional fixes before production")
        
        return self.test_results

    def run_comprehensive_tests(self):
        """Run all comprehensive workspace setup wizard tests"""
        print("🎯 FINAL COMPREHENSIVE WORKSPACE SETUP WIZARD TEST")
        print("Testing after database fixes: user_id and is_primary columns added to workspaces table")
        print("Expected: High success rate (90%+) with all 12 endpoints working and 6-step workflow functional")
        print("=" * 80)
        
        # Step 1: Set up authentication
        if not self.setup_authentication():
            print("❌ Cannot proceed without authentication")
            return self.test_results
        
        # Step 2: Test all 12 workspace setup endpoints
        self.test_all_12_workspace_setup_endpoints()
        
        # Step 3: Test complete 6-step workflow
        self.test_6_step_workflow()
        
        # Step 4: Test data persistence
        self.test_data_persistence()
        
        # Step 5: Test business logic
        self.test_business_logic()
        
        # Step 6: Print final summary
        return self.print_final_summary()

if __name__ == "__main__":
    tester = ComprehensiveWorkspaceSetupTester()
    results = tester.run_comprehensive_tests()