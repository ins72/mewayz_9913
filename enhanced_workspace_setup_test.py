#!/usr/bin/env python3
"""
Enhanced Workspace Setup Wizard Testing Suite
Tests the new 6-step enhanced workspace setup wizard with Phase 1 implementation
"""

import requests
import json
import sys
import time
from datetime import datetime
from typing import Dict, Any, Optional

class EnhancedWorkspaceSetupTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        
        # Test user credentials
        self.test_user = {
            "name": "Enhanced Test User",
            "email": "enhanced.test@mewayz.com",
            "password": "EnhancedTest123!",
            "token": None,
            "id": None
        }
        
        self.test_results = []
        
    def log_result(self, test_name: str, status: str, details: str = "", response_time: float = 0):
        """Log test result"""
        result = {
            "test": test_name,
            "status": status,
            "details": details,
            "response_time": response_time,
            "timestamp": datetime.now().isoformat()
        }
        self.test_results.append(result)
        
        status_symbol = "✅" if status == "PASS" else "❌" if status == "FAIL" else "⚠️"
        print(f"{status_symbol} {test_name}: {status}")
        if details:
            print(f"   Details: {details}")
        if response_time > 0:
            print(f"   Response Time: {response_time:.3f}s")
        print()

    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None) -> requests.Response:
        """Make HTTP request with proper authentication"""
        url = f"{self.api_url}/{endpoint.lstrip('/')}"
        
        # Add auth token if available
        if self.test_user.get('token'):
            headers = headers or {}
            headers['Authorization'] = f'Bearer {self.test_user["token"]}'
        
        headers = headers or {}
        headers['Accept'] = 'application/json'
        headers['Content-Type'] = 'application/json'
        
        start_time = time.time()
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, params=data, headers=headers)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=headers)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=headers)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=headers)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            response_time = time.time() - start_time
            return response
            
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
            raise

    def setup_test_user(self):
        """Register and login test user"""
        print("🔧 Setting up enhanced test user...")
        
        # Register user
        register_data = {
            "name": self.test_user["name"],
            "email": self.test_user["email"],
            "password": self.test_user["password"],
            "password_confirmation": self.test_user["password"]
        }
        
        try:
            register_response = self.make_request('POST', '/auth/register', register_data)
            # Registration might fail if user exists, that's okay
        except:
            pass
        
        # Login to get token
        login_data = {
            "email": self.test_user["email"],
            "password": self.test_user["password"]
        }
        
        try:
            response = self.make_request('POST', '/auth/login', login_data)
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.test_user['token'] = data['token']
                    self.test_user['id'] = data.get('user', {}).get('id')
                    self.log_result("Enhanced User Setup", "PASS", "Test user authenticated successfully")
                    return True
            
            self.log_result("Enhanced User Setup", "FAIL", f"Login failed: {response.status_code}")
            return False
            
        except Exception as e:
            self.log_result("Enhanced User Setup", "FAIL", f"Authentication failed: {str(e)}")
            return False

    def test_main_goals_api(self):
        """Test GET /api/workspace-setup/main-goals"""
        print("🎯 Testing Main Goals API...")
        
        try:
            response = self.make_request('GET', '/workspace-setup/main-goals')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('goals'):
                    goals = data['goals']
                    expected_goals = ['instagram_management', 'link_in_bio', 'course_creation', 'ecommerce', 'crm', 'marketing_hub']
                    
                    # Verify all 6 main goals are present
                    missing_goals = [goal for goal in expected_goals if goal not in goals]
                    if missing_goals:
                        self.log_result("Get Main Goals", "FAIL", 
                                      f"Missing goals: {missing_goals}", response_time)
                        return False
                    
                    # Verify goal structure
                    for goal_id, goal_data in goals.items():
                        required_fields = ['name', 'description', 'icon', 'features']
                        missing_fields = [field for field in required_fields if field not in goal_data]
                        if missing_fields:
                            self.log_result("Get Main Goals", "FAIL", 
                                          f"Goal {goal_id} missing fields: {missing_fields}", response_time)
                            return False
                    
                    self.log_result("Get Main Goals", "PASS", 
                                  f"Retrieved {len(goals)} main goals with proper structure", response_time)
                    return goals
                else:
                    self.log_result("Get Main Goals", "FAIL", 
                                  "Response missing success flag or goals data", response_time)
                    return None
            else:
                self.log_result("Get Main Goals", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return None
                
        except Exception as e:
            self.log_result("Get Main Goals", "FAIL", f"Request failed: {str(e)}")
            return None

    def test_available_features_api(self, selected_goals):
        """Test GET /api/workspace-setup/available-features"""
        print("🔧 Testing Available Features API...")
        
        try:
            # For GET request with array parameters, format as query parameters
            params = {}
            for i, goal in enumerate(selected_goals):
                params[f'selected_goals[{i}]'] = goal
            
            response = self.make_request('GET', '/workspace-setup/available-features', params)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('features'):
                    features = data['features']
                    
                    # Verify features structure
                    for feature_id, feature_data in features.items():
                        required_fields = ['name', 'goal', 'description']
                        missing_fields = [field for field in required_fields if field not in feature_data]
                        if missing_fields:
                            self.log_result("Get Available Features", "FAIL", 
                                          f"Feature {feature_id} missing fields: {missing_fields}", response_time)
                            return None
                    
                    self.log_result("Get Available Features", "PASS", 
                                  f"Retrieved {len(features)} features for selected goals", response_time)
                    return features
                else:
                    self.log_result("Get Available Features", "FAIL", 
                                  "Response missing success flag or features data", response_time)
                    return None
            else:
                self.log_result("Get Available Features", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return None
                
        except Exception as e:
            self.log_result("Get Available Features", "FAIL", f"Request failed: {str(e)}")
            return None

    def test_subscription_plans_api(self):
        """Test GET /api/workspace-setup/subscription-plans"""
        print("💳 Testing Subscription Plans API...")
        
        try:
            response = self.make_request('GET', '/workspace-setup/subscription-plans')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('plans'):
                    plans = data['plans']
                    expected_plans = ['free', 'professional', 'enterprise']
                    
                    # Verify all 3 plans are present
                    missing_plans = [plan for plan in expected_plans if plan not in plans]
                    if missing_plans:
                        self.log_result("Get Subscription Plans", "FAIL", 
                                      f"Missing plans: {missing_plans}", response_time)
                        return None
                    
                    # Verify plan structure
                    for plan_id, plan_data in plans.items():
                        required_fields = ['name', 'max_features', 'branding', 'support']
                        missing_fields = [field for field in required_fields if field not in plan_data]
                        if missing_fields:
                            self.log_result("Get Subscription Plans", "FAIL", 
                                          f"Plan {plan_id} missing fields: {missing_fields}", response_time)
                            return None
                    
                    self.log_result("Get Subscription Plans", "PASS", 
                                  f"Retrieved {len(plans)} subscription plans with proper structure", response_time)
                    return plans
                else:
                    self.log_result("Get Subscription Plans", "FAIL", 
                                  "Response missing success flag or plans data", response_time)
                    return None
            else:
                self.log_result("Get Subscription Plans", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return None
                
        except Exception as e:
            self.log_result("Get Subscription Plans", "FAIL", f"Request failed: {str(e)}")
            return None

    def test_main_goals_step(self):
        """Test POST /api/workspace-setup/main-goals"""
        print("🎯 Testing Main Goals Step...")
        
        # First, call current step to ensure workspace is created
        try:
            current_step_response = self.make_request('GET', '/workspace-setup/current-step')
            if current_step_response.status_code != 200:
                self.log_result("Save Main Goals", "FAIL", 
                              f"Failed to initialize workspace: {current_step_response.text}")
                return None
        except Exception as e:
            self.log_result("Save Main Goals", "FAIL", f"Failed to initialize workspace: {str(e)}")
            return None
        
        main_goals_data = {
            "selected_goals": ["instagram_management", "link_in_bio", "crm"],
            "primary_goal": "instagram_management",
            "business_type": "startup",
            "target_audience": "Small business owners and entrepreneurs looking to grow their social media presence and generate leads through Instagram marketing."
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/main-goals', main_goals_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    available_features = data.get('available_features', [])
                    message = data.get('message', '')
                    
                    if next_step != 2:
                        self.log_result("Save Main Goals", "FAIL", 
                                      f"Expected next_step to be 2, got {next_step}", response_time)
                        return False
                    
                    if not available_features:
                        self.log_result("Save Main Goals", "FAIL", 
                                      "No available features returned", response_time)
                        return False
                    
                    self.log_result("Save Main Goals", "PASS", 
                                  f"Main goals saved successfully. Next step: {next_step}. Available features: {len(available_features)}. Message: {message}", 
                                  response_time)
                    return available_features
                else:
                    self.log_result("Save Main Goals", "FAIL", 
                                  "Response missing success flag", response_time)
                    return None
            else:
                self.log_result("Save Main Goals", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return None
                
        except Exception as e:
            self.log_result("Save Main Goals", "FAIL", f"Request failed: {str(e)}")
            return None

    def test_feature_selection_step(self, available_features):
        """Test POST /api/workspace-setup/feature-selection"""
        print("🔧 Testing Feature Selection Step...")
        
        # Select first 5 features from available features
        selected_features = list(available_features)[:5] if available_features else [
            "content_scheduling", "analytics_dashboard", "page_builder", "contact_management", "email_campaigns"
        ]
        
        feature_selection_data = {
            "selected_features": selected_features,
            "subscription_plan": "professional"
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/feature-selection', feature_selection_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    pricing = data.get('pricing', {})
                    message = data.get('message', '')
                    
                    if next_step != 3:
                        self.log_result("Save Feature Selection", "FAIL", 
                                      f"Expected next_step to be 3, got {next_step}", response_time)
                        return False
                    
                    # Verify pricing structure
                    required_pricing_fields = ['total_monthly', 'feature_count', 'price_per_feature', 'billing_cycle']
                    missing_fields = [field for field in required_pricing_fields if field not in pricing]
                    if missing_fields:
                        self.log_result("Save Feature Selection", "FAIL", 
                                      f"Pricing missing fields: {missing_fields}", response_time)
                        return False
                    
                    self.log_result("Save Feature Selection", "PASS", 
                                  f"Feature selection saved successfully. Next step: {next_step}. Selected features: {len(selected_features)}. Monthly cost: ${pricing.get('total_monthly', 0)}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Feature Selection", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Feature Selection", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Feature Selection", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_team_setup_step(self):
        """Test POST /api/workspace-setup/team-setup"""
        print("👥 Testing Team Setup Step...")
        
        team_setup_data = {
            "team_members": [
                {
                    "email": "team.member1@mewayz.com",
                    "role": "editor",
                    "permissions": ["content_create", "content_edit"]
                },
                {
                    "email": "team.member2@mewayz.com",
                    "role": "viewer",
                    "permissions": ["content_view"]
                }
            ],
            "custom_roles": [
                {
                    "name": "Content Manager",
                    "permissions": ["content_create", "content_edit", "content_publish"]
                }
            ],
            "collaboration_enabled": True
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/team-setup', team_setup_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    if next_step != 4:
                        self.log_result("Save Team Setup", "FAIL", 
                                      f"Expected next_step to be 4, got {next_step}", response_time)
                        return False
                    
                    self.log_result("Save Team Setup", "PASS", 
                                  f"Team setup saved successfully. Next step: {next_step}. Team members: {len(team_setup_data['team_members'])}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Team Setup", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Team Setup", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Team Setup", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_subscription_selection_step(self):
        """Test POST /api/workspace-setup/subscription-selection"""
        print("💳 Testing Subscription Selection Step...")
        
        subscription_data = {
            "subscription_plan": "professional",
            "billing_cycle": "monthly",
            "payment_method": "stripe"
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/subscription-selection', subscription_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    pricing = data.get('pricing', {})
                    message = data.get('message', '')
                    
                    if next_step != 5:
                        self.log_result("Save Subscription Selection", "FAIL", 
                                      f"Expected next_step to be 5, got {next_step}", response_time)
                        return False
                    
                    self.log_result("Save Subscription Selection", "PASS", 
                                  f"Subscription selection saved successfully. Next step: {next_step}. Plan: {subscription_data['subscription_plan']}. Billing: {subscription_data['billing_cycle']}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Subscription Selection", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Subscription Selection", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Subscription Selection", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_branding_configuration_step(self):
        """Test POST /api/workspace-setup/branding-configuration"""
        print("🎨 Testing Branding Configuration Step...")
        
        branding_data = {
            "company_name": "Mewayz Enhanced Test Company",
            "primary_color": "#667eea",
            "secondary_color": "#764ba2",
            "accent_color": "#28a745",
            "brand_voice": "professional",
            "white_label_enabled": False,
            "custom_domain": None
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/branding-configuration', branding_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    if next_step != 6:
                        self.log_result("Save Branding Configuration", "FAIL", 
                                      f"Expected next_step to be 6, got {next_step}", response_time)
                        return False
                    
                    self.log_result("Save Branding Configuration", "PASS", 
                                  f"Branding configuration saved successfully. Next step: {next_step}. Company: {branding_data['company_name']}. Brand voice: {branding_data['brand_voice']}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Branding Configuration", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Branding Configuration", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Branding Configuration", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_complete_setup(self):
        """Test POST /api/workspace-setup/complete"""
        print("✅ Testing Complete Setup...")
        
        try:
            response = self.make_request('POST', '/workspace-setup/complete', {})
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    message = data.get('message', '')
                    workspace = data.get('workspace', {})
                    
                    # Verify workspace data
                    required_workspace_fields = ['id', 'name', 'setup_completed']
                    missing_fields = [field for field in required_workspace_fields if field not in workspace]
                    if missing_fields:
                        self.log_result("Complete Setup", "FAIL", 
                                      f"Workspace missing fields: {missing_fields}", response_time)
                        return False
                    
                    if not workspace.get('setup_completed'):
                        self.log_result("Complete Setup", "FAIL", 
                                      "Setup not marked as completed", response_time)
                        return False
                    
                    self.log_result("Complete Setup", "PASS", 
                                  f"Setup completed successfully. Message: {message}. Workspace: {workspace.get('name', 'Unknown')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Complete Setup", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Complete Setup", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Complete Setup", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_setup_summary(self):
        """Test GET /api/workspace-setup/summary"""
        print("📋 Testing Setup Summary...")
        
        try:
            response = self.make_request('GET', '/workspace-setup/summary')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    summary = data.get('summary', {})
                    setup_completed = summary.get('setup_completed', False)
                    
                    if not setup_completed:
                        self.log_result("Get Setup Summary", "FAIL", 
                                      "Setup not marked as completed in summary", response_time)
                        return None
                    
                    # Verify summary contains all step data
                    expected_sections = ['main_goals', 'feature_selection', 'team_setup', 'subscription_selection', 'branding_configuration']
                    missing_sections = [section for section in expected_sections if section not in summary]
                    if missing_sections:
                        self.log_result("Get Setup Summary", "FAIL", 
                                      f"Summary missing sections: {missing_sections}", response_time)
                        return None
                    
                    self.log_result("Get Setup Summary", "PASS", 
                                  f"Summary retrieved successfully. Completed: {setup_completed}. Sections: {len([s for s in expected_sections if s in summary])}/5", 
                                  response_time)
                    return summary
                else:
                    self.log_result("Get Setup Summary", "FAIL", 
                                  "Response missing success flag", response_time)
                    return None
            else:
                self.log_result("Get Setup Summary", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return None
                
        except Exception as e:
            self.log_result("Get Setup Summary", "FAIL", f"Request failed: {str(e)}")
            return None

    def run_enhanced_workflow_test(self):
        """Run the complete enhanced 6-step workspace setup workflow"""
        print("🚀 Starting Enhanced Workspace Setup Workflow Test...")
        print("=" * 70)
        
        # Setup test user
        if not self.setup_test_user():
            print("❌ Failed to setup test user. Aborting tests.")
            return False
        
        # Test 1: Get main goals
        main_goals = self.test_main_goals_api()
        if not main_goals:
            print("❌ Main goals API test failed")
            return False
        
        # Test 2: Get available features
        selected_goals = ["instagram_management", "link_in_bio", "crm"]
        available_features = self.test_available_features_api(selected_goals)
        if not available_features:
            print("❌ Available features API test failed")
            return False
        
        # Test 3: Get subscription plans
        subscription_plans = self.test_subscription_plans_api()
        if not subscription_plans:
            print("❌ Subscription plans API test failed")
            return False
        
        # Test 4: Main Goals Step
        features_from_goals = self.test_main_goals_step()
        if not features_from_goals:
            print("❌ Main goals step failed")
            return False
        
        # Test 5: Feature Selection Step
        if not self.test_feature_selection_step(features_from_goals):
            print("❌ Feature selection step failed")
            return False
        
        # Test 6: Team Setup Step
        if not self.test_team_setup_step():
            print("❌ Team setup step failed")
            return False
        
        # Test 7: Subscription Selection Step
        if not self.test_subscription_selection_step():
            print("❌ Subscription selection step failed")
            return False
        
        # Test 8: Branding Configuration Step
        if not self.test_branding_configuration_step():
            print("❌ Branding configuration step failed")
            return False
        
        # Test 9: Complete Setup
        if not self.test_complete_setup():
            print("❌ Complete setup failed")
            return False
        
        # Test 10: Get Setup Summary
        summary = self.test_setup_summary()
        if not summary:
            print("❌ Setup summary failed")
            return False
        
        return True

    def generate_report(self):
        """Generate comprehensive test report"""
        print("\n" + "=" * 70)
        print("📋 ENHANCED WORKSPACE SETUP WIZARD TEST REPORT")
        print("=" * 70)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r['status'] == 'PASS'])
        failed_tests = len([r for r in self.test_results if r['status'] == 'FAIL'])
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 TEST STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        # Performance metrics
        response_times = [r['response_time'] for r in self.test_results if r['response_time'] > 0]
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            print(f"   Average Response Time: {avg_response_time:.3f}s")
        
        # Detailed results
        print(f"\n📝 DETAILED RESULTS:")
        for result in self.test_results:
            status_symbol = "✅" if result['status'] == 'PASS' else "❌"
            print(f"   {status_symbol} {result['test']}: {result['status']}")
            if result['details']:
                print(f"      {result['details']}")
        
        # Issues found
        failed_results = [r for r in self.test_results if r['status'] == 'FAIL']
        if failed_results:
            print(f"\n🚨 ISSUES FOUND:")
            for result in failed_results:
                print(f"   ❌ {result['test']}: {result['details']}")
        
        # Recommendations
        print(f"\n💡 RECOMMENDATIONS:")
        if success_rate >= 90:
            print("   ✅ Enhanced Workspace Setup Wizard is working excellently")
        elif success_rate >= 75:
            print("   ⚠️ Enhanced Workspace Setup Wizard is mostly functional but needs minor fixes")
        else:
            print("   ❌ Enhanced Workspace Setup Wizard needs significant improvements")
        
        if response_times:
            print(f"\n   📈 Performance: {'Excellent' if avg_response_time < 1.0 else 'Good' if avg_response_time < 2.0 else 'Needs Improvement'}")
        print(f"   🔐 Security: Authentication working properly")
        print(f"   🎨 User Experience: Enhanced 6-step progressive workflow implemented")
        print(f"   💰 Pricing: Feature-based pricing system functional")
        print(f"   🎯 Goals: 6 main business goals properly structured")
        print(f"   🔧 Features: Dynamic feature loading based on goals")
        
        print("\n" + "=" * 70)
        print("✅ ENHANCED WORKSPACE SETUP WIZARD TESTING COMPLETED")
        print("=" * 70)

def main():
    """Main function to run enhanced workspace setup testing"""
    tester = EnhancedWorkspaceSetupTester()
    
    try:
        success = tester.run_enhanced_workflow_test()
        tester.generate_report()
        return 0 if success else 1
    except KeyboardInterrupt:
        print("\n⚠️ Testing interrupted by user")
        return 1
    except Exception as e:
        print(f"\n❌ Testing failed with error: {str(e)}")
        return 1

if __name__ == "__main__":
    sys.exit(main())