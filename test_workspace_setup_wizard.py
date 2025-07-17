#!/usr/bin/env python3
"""
Focused Workspace Setup Wizard Testing
Tests the comprehensive 6-step workspace setup process
"""

import requests
import json
import sys
import time
from datetime import datetime

class WorkspaceSetupWizardTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token from the test_result.md
        self.auth_token = "8|L5pG8yu6ajxJpYrBYl3B86QVr01Od97gtnrNgCp46eafafa8"
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
                response = self.session.get(url, headers=default_headers, timeout=30)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=default_headers, timeout=30)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=30)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=30)
            else:
                return None
                
            return response
            
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
            return None

    def test_workspace_setup_wizard(self):
        """Test comprehensive 6-step Workspace Setup Wizard system"""
        print("🎯 WORKSPACE SETUP WIZARD COMPREHENSIVE TEST")
        print("=" * 60)
        print("Testing the complete 6-step workspace setup process:")
        print("1. Main Goals Selection (Instagram, Link in Bio, Courses, E-commerce, CRM, Marketing)")
        print("2. Feature Selection (up to 3 for free plan)")
        print("3. Team Setup (invite team members with roles)")
        print("4. Subscription Selection (Free, Pro, Enterprise)")
        print("5. Branding Configuration (company name, colors, logo)")
        print("6. Final Review and Launch")
        print("=" * 60)
        
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
            error_msg = f"Failed to get current step - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Get Current Setup Step", False, error_msg)
        
        # Step 2: Test GET main goals
        print("\n--- Step 2: Get Main Goals ---")
        response = self.make_request('GET', '/workspace-setup/main-goals')
        if response and response.status_code == 200:
            data = response.json()
            goals = data.get('goals', {})
            self.log_test("Get Main Goals", True, f"Retrieved {len(goals)} main goals: {list(goals.keys())}")
        else:
            self.log_test("Get Main Goals", False, f"Failed to get main goals - Status: {response.status_code if response else 'No response'}")
            return
        
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
            error_msg = f"Failed to save main goals - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Main Goals", False, error_msg)
        
        # Step 4: Test GET available features
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
            self.log_test("Get Available Features", False, f"Failed to get available features - Status: {response.status_code if response else 'No response'}")
        
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
            pricing = data.get('pricing', {})
            self.log_test("Save Feature Selection", True, f"Features saved successfully, pricing: ${pricing.get('total_monthly', 0)}/month")
        else:
            error_msg = f"Failed to save feature selection - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Feature Selection", False, error_msg)
        
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
            error_msg = f"Failed to save team setup - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Team Setup", False, error_msg)
        
        # Step 7: Test GET subscription plans
        print("\n--- Step 7: Get Subscription Plans ---")
        response = self.make_request('GET', '/workspace-setup/subscription-plans')
        if response and response.status_code == 200:
            data = response.json()
            plans = data.get('plans', {})
            self.log_test("Get Subscription Plans", True, f"Retrieved {len(plans)} subscription plans: {list(plans.keys())}")
        else:
            self.log_test("Get Subscription Plans", False, f"Failed to get subscription plans - Status: {response.status_code if response else 'No response'}")
        
        # Step 8: Test POST subscription selection
        print("\n--- Step 8: Save Subscription Selection ---")
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
            error_msg = f"Failed to save subscription - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Subscription Selection", False, error_msg)
        
        # Step 9: Test POST branding configuration
        print("\n--- Step 9: Save Branding Configuration ---")
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
            error_msg = f"Failed to save branding - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Save Branding Configuration", False, error_msg)
        
        # Step 10: Test POST complete setup
        print("\n--- Step 10: Complete Workspace Setup ---")
        response = self.make_request('POST', '/workspace-setup/complete')
        if response and response.status_code == 200:
            data = response.json()
            workspace = data.get('workspace', {})
            self.log_test("Complete Workspace Setup", True, f"Setup completed! Workspace: {workspace.get('name')}, Dashboard: {workspace.get('dashboard_url')}")
        else:
            error_msg = f"Failed to complete setup - Status: {response.status_code if response else 'No response'}"
            if response:
                try:
                    error_data = response.json()
                    error_msg += f", Error: {error_data.get('error', 'Unknown error')}"
                except:
                    error_msg += f", Response: {response.text[:200]}"
            self.log_test("Complete Workspace Setup", False, error_msg)
        
        # Step 11: Test GET setup summary
        print("\n--- Step 11: Get Setup Summary ---")
        response = self.make_request('GET', '/workspace-setup/summary')
        if response and response.status_code == 200:
            data = response.json()
            summary = data.get('summary', {})
            self.log_test("Get Setup Summary", True, f"Setup summary retrieved, completed: {summary.get('setup_completed', False)}")
        else:
            self.log_test("Get Setup Summary", False, f"Failed to get setup summary - Status: {response.status_code if response else 'No response'}")
        
        # Step 12: Test POST reset setup (for testing purposes)
        print("\n--- Step 12: Reset Setup (Testing) ---")
        response = self.make_request('POST', '/workspace-setup/reset')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Reset Setup", True, "Setup reset successfully for testing")
        else:
            self.log_test("Reset Setup", False, f"Failed to reset setup - Status: {response.status_code if response else 'No response'}")
        
        self.print_summary()

    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 60)
        print("📊 WORKSPACE SETUP WIZARD TEST SUMMARY")
        print("=" * 60)
        
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
        
        print("\n🎯 Workspace Setup Wizard Testing Complete!")
        print("This comprehensive test covers all 6 steps of the workspace setup process:")
        print("1. Main Goals Selection (Instagram, Link in Bio, Courses, E-commerce, CRM, Marketing)")
        print("2. Feature Selection (up to 3 for free plan)")
        print("3. Team Setup (invite team members with roles)")
        print("4. Subscription Selection (Free, Pro, Enterprise)")
        print("5. Branding Configuration (company name, colors, logo)")
        print("6. Final Review and Launch")
        print("=" * 60)

if __name__ == "__main__":
    tester = WorkspaceSetupWizardTester()
    tester.test_workspace_setup_wizard()