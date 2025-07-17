#!/usr/bin/env python3
"""
Focused Workspace Setup Wizard Testing for Mewayz Creator Economy Platform
Testing after database fix - user_id column added to workspaces table
"""

import requests
import json
import sys
import time
from datetime import datetime

class WorkspaceSetupTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token from the test_result.md
        self.auth_token = "13|fLLuf9oSzYffXGv7MbxBULCWymNrQ4j2kzCO48Ae5579b65d"
        self.test_results = {}
        self.session = requests.Session()
        
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
        """Make HTTP request with proper headers and rate limiting"""
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
            # Add delay to avoid rate limiting
            time.sleep(1)
            
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

    def test_workspace_setup_wizard(self):
        """Test comprehensive 6-step Workspace Setup Wizard system"""
        print("\n🎯 WORKSPACE SETUP WIZARD RETEST AFTER DATABASE FIX")
        print("=" * 80)
        print("Testing after user_id column was added to workspaces table")
        print("Expected: Previously failing endpoints should now work")
        print("=" * 80)
        
        if not self.auth_token:
            self.log_test("Workspace Setup Wizard", False, "Cannot test - no authentication token")
            return
        
        # Step 1: Test GET current setup step
        print("\n--- Step 1: Get Current Setup Step ---")
        response = self.make_request('GET', '/workspace-setup/current-step')
        if response and response.status_code == 200:
            data = response.json()
            current_step = data.get('current_step', 1)
            self.log_test("Get Current Setup Step", True, f"Current step: {current_step}, Setup completed: {data.get('setup_completed', False)}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Get Current Setup Step", False, f"Failed to get current step - {error_msg}")
        
        # Step 2: Test GET main goals
        print("\n--- Step 2: Get Main Goals ---")
        response = self.make_request('GET', '/workspace-setup/main-goals')
        if response and response.status_code == 200:
            data = response.json()
            goals = data.get('goals', {})
            self.log_test("Get Main Goals", True, f"Retrieved {len(goals)} main goals: {list(goals.keys())}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Get Main Goals", False, f"Failed to get main goals - {error_msg}")
        
        # Step 3: Test POST main goals selection
        print("\n--- Step 3: Save Main Goals Selection ---")
        main_goals_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"],
            "primary_goal": "instagram_management",
            "business_type": "Digital Marketing Agency",
            "target_audience": "Small business owners and entrepreneurs looking to grow their social media presence"
        }
        response = self.make_request('POST', '/workspace-setup/main-goals', data=main_goals_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Main Goals", True, f"Goals saved successfully, next step: {data.get('next_step')}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Main Goals", False, f"Failed to save main goals - {error_msg}")
        
        # Step 4: Test POST available features
        print("\n--- Step 4: Get Available Features ---")
        features_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "ecommerce"]
        }
        response = self.make_request('POST', '/workspace-setup/available-features', data=features_data)
        if response and response.status_code == 200:
            data = response.json()
            features = data.get('features', {})
            self.log_test("Get Available Features", True, f"Retrieved {len(features)} available features")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Get Available Features", False, f"Failed to get available features - {error_msg}")
        
        # Step 5: Test POST feature selection
        print("\n--- Step 5: Save Feature Selection ---")
        feature_selection_data = {
            "selected_features": [
                "content_scheduling", "content_calendar", "hashtag_research",
                "page_builder", "template_library", "analytics_tracking",
                "product_catalog", "inventory_tracking", "order_processing"
            ],
            "subscription_plan": "professional"
        }
        response = self.make_request('POST', '/workspace-setup/feature-selection', data=feature_selection_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Feature Selection", True, f"Features saved successfully, pricing: ${data.get('pricing', {}).get('total_monthly', 0)}/month")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Feature Selection", False, f"Failed to save feature selection - {error_msg}")
        
        # Step 6: Test POST team setup
        print("\n--- Step 6: Save Team Setup ---")
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
            "custom_roles": [
                {
                    "name": "Content Creator",
                    "permissions": ["content_creation", "content_scheduling"]
                }
            ],
            "collaboration_enabled": True
        }
        response = self.make_request('POST', '/workspace-setup/team-setup', data=team_setup_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Team Setup", True, f"Team setup saved successfully, next step: {data.get('next_step')}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Team Setup", False, f"Failed to save team setup - {error_msg}")
        
        # Step 7: Test POST subscription selection
        print("\n--- Step 7: Save Subscription Selection ---")
        subscription_data = {
            "subscription_plan": "professional",
            "billing_cycle": "yearly",
            "payment_method": "stripe"
        }
        response = self.make_request('POST', '/workspace-setup/subscription-selection', data=subscription_data)
        if response and response.status_code == 200:
            data = response.json()
            pricing = data.get('pricing', {})
            self.log_test("Save Subscription Selection", True, f"Subscription saved, total: ${pricing.get('total_yearly', 0)}/year")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Subscription Selection", False, f"Failed to save subscription - {error_msg}")
        
        # Step 8: Test POST branding configuration
        print("\n--- Step 8: Save Branding Configuration ---")
        branding_data = {
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981",
            "accent_color": "#F59E0B",
            "company_name": "Digital Growth Agency",
            "brand_voice": "professional",
            "white_label_enabled": False,
            "custom_domain": "agency.example.com"
        }
        response = self.make_request('POST', '/workspace-setup/branding-configuration', data=branding_data)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Save Branding Configuration", True, f"Branding saved successfully, next step: {data.get('next_step')}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Branding Configuration", False, f"Failed to save branding - {error_msg}")
        
        # Step 9: Test POST complete setup
        print("\n--- Step 9: Complete Workspace Setup ---")
        response = self.make_request('POST', '/workspace-setup/complete')
        if response and response.status_code == 200:
            data = response.json()
            workspace = data.get('workspace', {})
            self.log_test("Complete Workspace Setup", True, f"Setup completed! Workspace: {workspace.get('name')}, Dashboard: {workspace.get('dashboard_url')}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Complete Workspace Setup", False, f"Failed to complete setup - {error_msg}")
        
        # Additional tests for workspace management
        print("\n--- Additional Workspace Tests ---")
        
        # Test get workspaces
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            workspaces = data.get('workspaces', [])
            self.log_test("Get Workspaces", True, f"Retrieved {len(workspaces)} workspaces")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Get Workspaces", False, f"Failed to get workspaces - {error_msg}")
        
        # Test workspace setup summary
        response = self.make_request('GET', '/workspace-setup/summary')
        if response and response.status_code == 200:
            data = response.json()
            summary = data.get('summary', {})
            self.log_test("Get Setup Summary", True, f"Setup summary retrieved, completed: {summary.get('setup_completed', False)}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Get Setup Summary", False, f"Failed to get setup summary - {error_msg}")

    def test_basic_auth_and_health(self):
        """Test basic authentication and health endpoints first"""
        print("\n🔍 BASIC CONNECTIVITY TESTS")
        print("=" * 50)
        
        # Test health endpoint (no auth required)
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Health Check", True, f"API is healthy - Status: {data.get('status', 'unknown')}")
        else:
            self.log_test("API Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test custom auth middleware
        response = self.make_request('GET', '/test-custom-auth')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Custom Auth Middleware", True, f"Custom auth working - User: {data.get('user_name', 'unknown')}")
        else:
            error_msg = f"Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('message', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Custom Auth Middleware", False, f"Custom auth failed - {error_msg}")

    def run_tests(self):
        """Run all workspace setup wizard tests"""
        print("🚀 WORKSPACE SETUP WIZARD RETEST")
        print("Testing after database fix: user_id column added to workspaces table")
        print("Expected improvement: Previously failing endpoints should now work")
        print("=" * 80)
        
        # First test basic connectivity
        self.test_basic_auth_and_health()
        
        # Then test the workspace setup wizard
        self.test_workspace_setup_wizard()
        
        # Print summary
        print("\n" + "=" * 80)
        print("📊 WORKSPACE SETUP WIZARD TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("\n🎯 DATABASE FIX IMPACT:")
        if success_rate > 50:
            print("✅ Database fix appears to be successful - most endpoints working")
        else:
            print("❌ Database fix may need further investigation - many endpoints still failing")
        
        return self.test_results

if __name__ == "__main__":
    tester = WorkspaceSetupTester()
    results = tester.run_tests()