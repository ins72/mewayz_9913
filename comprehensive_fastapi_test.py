#!/usr/bin/env python3
"""
Comprehensive FastAPI Backend Testing for Mewayz Professional Platform
Tests all new features mentioned in the review request:
1. Multi-step Onboarding System
2. Advanced Subscription Management  
3. Enhanced AI Features
4. Workspace Management
5. Template Marketplace
6. Advanced Analytics
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class ComprehensiveFastAPITester:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'status': status,
            'success': success,
            'details': details,
            'response_time': f"{response_time:.3f}s"
        }
        self.test_results.append(result)
        print(f"{status} - {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    Details: {details}")
    
    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None, timeout: int = 30) -> tuple:
        """Make HTTP request with error handling"""
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        if headers:
            default_headers.update(headers)
        
        try:
            start_time = time.time()
            
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, timeout=timeout)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            
            return response, response_time
            
        except requests.exceptions.Timeout:
            return None, timeout
        except requests.exceptions.RequestException as e:
            print(f"Request error: {e}")
            return None, 0
    
    def test_health_check(self):
        """Test API health check endpoint"""
        response, response_time = self.make_request('GET', '/health')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') or 'healthy' in str(data).lower():
                    self.log_test("Health Check", True, f"Status: {response.status_code}, Response: {data}", response_time)
                    return True
                else:
                    self.log_test("Health Check", False, f"Unexpected response: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_test("Health Check", False, f"Invalid JSON response: {response.text}", response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_test("Health Check", False, f"Status: {status_code}", response_time)
            return False
    
    def test_admin_authentication(self):
        """Test admin authentication with provided credentials"""
        login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', login_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.auth_token = data['token']
                    user_info = data.get('user', {})
                    self.log_test("Admin Authentication", True, 
                                f"Login successful, Role: {user_info.get('role', 'unknown')}, Token received", response_time)
                    return True
                else:
                    self.log_test("Admin Authentication", False, f"Login failed: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_test("Admin Authentication", False, f"Invalid JSON response: {response.text}", response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            error_msg = response.text if response else "No response"
            self.log_test("Admin Authentication", False, f"Status: {status_code}, Error: {error_msg}", response_time)
            return False
    
    def test_onboarding_system(self):
        """Test Multi-step Onboarding System endpoints"""
        print("\n=== TESTING MULTI-STEP ONBOARDING SYSTEM ===")
        
        # Test onboarding status
        response, response_time = self.make_request('GET', '/onboarding/status')
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Onboarding Status Check", True, f"Status retrieved: {data.get('success', False)}", response_time)
            except:
                self.log_test("Onboarding Status Check", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Onboarding Status Check", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test goal selection
        goals_data = {
            "goals": ["instagram", "link_bio", "courses"]
        }
        response, response_time = self.make_request('POST', '/onboarding/goals', goals_data)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Goal Selection", True, f"Goals saved: {data.get('success', False)}", response_time)
            except:
                self.log_test("Goal Selection", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Goal Selection", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test feature selection
        features_data = {
            "features": ["ai_assistant", "bio_sites", "analytics"]
        }
        response, response_time = self.make_request('POST', '/onboarding/features', features_data)
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Feature Selection", True, f"Features selected: {data.get('success', False)}", response_time)
            except:
                self.log_test("Feature Selection", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Feature Selection", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test onboarding completion
        response, response_time = self.make_request('POST', '/onboarding/complete')
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Onboarding Completion", True, f"Onboarding completed: {data.get('success', False)}", response_time)
            except:
                self.log_test("Onboarding Completion", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Onboarding Completion", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def test_subscription_management(self):
        """Test Advanced Subscription Management endpoints"""
        print("\n=== TESTING ADVANCED SUBSCRIPTION MANAGEMENT ===")
        
        # Test subscription plans
        response, response_time = self.make_request('GET', '/subscriptions/plans')
        if response and response.status_code == 200:
            try:
                data = response.json()
                plans = data.get('data', {}).get('plans', [])
                self.log_test("Subscription Plans", True, f"Retrieved {len(plans)} plans", response_time)
            except:
                self.log_test("Subscription Plans", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Subscription Plans", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test current subscription
        response, response_time = self.make_request('GET', '/subscriptions/current')
        if response and response.status_code == 200:
            try:
                data = response.json()
                current_plan = data.get('data', {}).get('current_plan', {})
                self.log_test("Current Subscription", True, f"Plan: {current_plan.get('name', 'Unknown')}", response_time)
            except:
                self.log_test("Current Subscription", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Current Subscription", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test usage statistics
        response, response_time = self.make_request('GET', '/subscriptions/usage')
        if response and response.status_code == 200:
            try:
                data = response.json()
                usage = data.get('data', {})
                self.log_test("Usage Statistics", True, f"Usage data retrieved: {bool(usage)}", response_time)
            except:
                self.log_test("Usage Statistics", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Usage Statistics", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test trial start
        response, response_time = self.make_request('POST', '/subscriptions/trial/start')
        if response and response.status_code in [200, 400]:  # 400 might be "trial already used"
            try:
                data = response.json()
                self.log_test("Trial Start", True, f"Response: {data.get('message', 'Trial processed')}", response_time)
            except:
                self.log_test("Trial Start", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Trial Start", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def test_enhanced_ai_features(self):
        """Test Enhanced AI Features endpoints"""
        print("\n=== TESTING ENHANCED AI FEATURES ===")
        
        # Test AI services catalog
        response, response_time = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            try:
                data = response.json()
                services = data.get('data', {}).get('services', []) or data.get('data', {}).get('available_services', [])
                self.log_test("AI Services Catalog", True, f"Retrieved {len(services)} AI services", response_time)
            except:
                self.log_test("AI Services Catalog", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("AI Services Catalog", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test AI conversations
        response, response_time = self.make_request('GET', '/ai/conversations')
        if response and response.status_code == 200:
            try:
                data = response.json()
                conversations = data.get('data', {}).get('conversations', [])
                self.log_test("AI Conversations", True, f"Retrieved {len(conversations)} conversations", response_time)
            except:
                self.log_test("AI Conversations", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("AI Conversations", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test AI content generation
        content_data = {
            "content_type": "blog_post",
            "topic": "Digital Marketing Trends 2025",
            "length": "medium",
            "tone": "professional"
        }
        response, response_time = self.make_request('POST', '/ai/content/generate', content_data)
        if response and response.status_code == 200:
            try:
                data = response.json()
                content = data.get('data', {}).get('content', '')
                self.log_test("AI Content Generation", True, f"Generated content: {len(content)} characters", response_time)
            except:
                self.log_test("AI Content Generation", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("AI Content Generation", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test create AI conversation
        conversation_data = {"title": "Test AI Conversation"}
        response, response_time = self.make_request('POST', '/ai/conversations', conversation_data)
        if response and response.status_code == 200:
            try:
                data = response.json()
                conversation = data.get('data', {}).get('conversation', {})
                self.log_test("Create AI Conversation", True, f"Created: {conversation.get('title', 'Unknown')}", response_time)
            except:
                self.log_test("Create AI Conversation", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Create AI Conversation", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def test_workspace_management(self):
        """Test Workspace Management endpoints"""
        print("\n=== TESTING WORKSPACE MANAGEMENT ===")
        
        # Test list workspaces
        response, response_time = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            try:
                data = response.json()
                workspaces = data.get('data', {}).get('workspaces', []) or data.get('data', [])
                self.log_test("List Workspaces", True, f"Retrieved {len(workspaces)} workspaces", response_time)
            except:
                self.log_test("List Workspaces", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("List Workspaces", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test create workspace
        workspace_data = {
            "name": "Test Workspace",
            "description": "Test workspace for API testing",
            "goals": ["instagram", "link_bio"]
        }
        response, response_time = self.make_request('POST', '/workspaces/create', workspace_data)
        if response and response.status_code == 200:
            try:
                data = response.json()
                workspace = data.get('data', {}).get('workspace', {})
                self.log_test("Create Workspace", True, f"Created: {workspace.get('name', 'Unknown')}", response_time)
            except:
                self.log_test("Create Workspace", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Create Workspace", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test advanced team management
        response, response_time = self.make_request('GET', '/workspaces/team/advanced')
        if response and response.status_code == 200:
            try:
                data = response.json()
                team_data = data.get('data', {})
                self.log_test("Advanced Team Management", True, f"Team overview retrieved: {bool(team_data)}", response_time)
            except:
                self.log_test("Advanced Team Management", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Advanced Team Management", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def test_template_marketplace(self):
        """Test Template Marketplace endpoints"""
        print("\n=== TESTING TEMPLATE MARKETPLACE ===")
        
        # Test bio site themes (template marketplace)
        response, response_time = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            try:
                data = response.json()
                themes = data.get('data', {}).get('themes', [])
                self.log_test("Bio Site Themes", True, f"Retrieved {len(themes)} themes", response_time)
            except:
                self.log_test("Bio Site Themes", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Bio Site Themes", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test website builder templates
        response, response_time = self.make_request('GET', '/websites/builder/advanced-features')
        if response and response.status_code == 200:
            try:
                data = response.json()
                templates = data.get('data', {}).get('templates', {})
                total_templates = sum(templates.values()) if isinstance(templates, dict) else 0
                self.log_test("Website Builder Templates", True, f"Retrieved {total_templates} templates", response_time)
            except:
                self.log_test("Website Builder Templates", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Website Builder Templates", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def test_advanced_analytics(self):
        """Test Advanced Analytics endpoints"""
        print("\n=== TESTING ADVANCED ANALYTICS ===")
        
        # Test analytics overview
        response, response_time = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            try:
                data = response.json()
                overview = data.get('data', {}).get('overview', {})
                self.log_test("Analytics Overview", True, f"Overview data retrieved: {bool(overview)}", response_time)
            except:
                self.log_test("Analytics Overview", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Analytics Overview", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test advanced business intelligence
        response, response_time = self.make_request('GET', '/analytics/business-intelligence/advanced')
        if response and response.status_code == 200:
            try:
                data = response.json()
                bi_data = data.get('data', {})
                self.log_test("Advanced Business Intelligence", True, f"BI data retrieved: {bool(bi_data)}", response_time)
            except:
                self.log_test("Advanced Business Intelligence", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Advanced Business Intelligence", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test comprehensive financial dashboard
        response, response_time = self.make_request('GET', '/financial/dashboard/comprehensive')
        if response and response.status_code == 200:
            try:
                data = response.json()
                financial_data = data.get('data', {})
                self.log_test("Financial Dashboard", True, f"Financial data retrieved: {bool(financial_data)}", response_time)
            except:
                self.log_test("Financial Dashboard", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Financial Dashboard", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test course analytics
        response, response_time = self.make_request('GET', '/courses/analytics/detailed')
        if response and response.status_code == 200:
            try:
                data = response.json()
                course_analytics = data.get('data', {})
                self.log_test("Course Analytics", True, f"Course analytics retrieved: {bool(course_analytics)}", response_time)
            except:
                self.log_test("Course Analytics", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Course Analytics", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def test_existing_functionality(self):
        """Test existing functionality to ensure nothing is broken"""
        print("\n=== TESTING EXISTING FUNCTIONALITY ===")
        
        # Test admin dashboard
        response, response_time = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 200:
            try:
                data = response.json()
                dashboard_data = data.get('data', {})
                self.log_test("Admin Dashboard", True, f"Dashboard data retrieved: {bool(dashboard_data)}", response_time)
            except:
                self.log_test("Admin Dashboard", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Admin Dashboard", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test bio sites
        response, response_time = self.make_request('GET', '/bio-sites')
        if response and response.status_code == 200:
            try:
                data = response.json()
                bio_sites = data.get('data', {}).get('bio_sites', [])
                self.log_test("Bio Sites", True, f"Retrieved {len(bio_sites)} bio sites", response_time)
            except:
                self.log_test("Bio Sites", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Bio Sites", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test e-commerce dashboard
        response, response_time = self.make_request('GET', '/ecommerce/dashboard')
        if response and response.status_code == 200:
            try:
                data = response.json()
                ecommerce_data = data.get('data', {})
                self.log_test("E-commerce Dashboard", True, f"E-commerce data retrieved: {bool(ecommerce_data)}", response_time)
            except:
                self.log_test("E-commerce Dashboard", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("E-commerce Dashboard", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test booking dashboard
        response, response_time = self.make_request('GET', '/bookings/dashboard')
        if response and response.status_code == 200:
            try:
                data = response.json()
                booking_data = data.get('data', {})
                self.log_test("Booking Dashboard", True, f"Booking data retrieved: {bool(booking_data)}", response_time)
            except:
                self.log_test("Booking Dashboard", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Booking Dashboard", False, f"Status: {response.status_code if response else 'No response'}", response_time)
        
        # Test notifications
        response, response_time = self.make_request('GET', '/notifications')
        if response and response.status_code == 200:
            try:
                data = response.json()
                notifications = data.get('data', {}).get('notifications', [])
                self.log_test("Notifications", True, f"Retrieved {len(notifications)} notifications", response_time)
            except:
                self.log_test("Notifications", False, f"Invalid response: {response.text}", response_time)
        else:
            self.log_test("Notifications", False, f"Status: {response.status_code if response else 'No response'}", response_time)
    
    def run_comprehensive_tests(self):
        """Run all comprehensive tests"""
        print("🚀 STARTING COMPREHENSIVE FASTAPI BACKEND TESTING")
        print("=" * 60)
        
        # Basic connectivity tests
        if not self.test_health_check():
            print("❌ Health check failed - aborting tests")
            return False
        
        if not self.test_admin_authentication():
            print("❌ Authentication failed - aborting tests")
            return False
        
        # Test all new features
        self.test_onboarding_system()
        self.test_subscription_management()
        self.test_enhanced_ai_features()
        self.test_workspace_management()
        self.test_template_marketplace()
        self.test_advanced_analytics()
        
        # Test existing functionality
        self.test_existing_functionality()
        
        # Generate summary
        self.generate_summary()
        
        return True
    
    def generate_summary(self):
        """Generate comprehensive test summary"""
        print("\n" + "=" * 60)
        print("📊 COMPREHENSIVE TEST RESULTS SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests}")
        print(f"Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  - {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  - {result['test']}")
        
        # Calculate average response time
        response_times = [float(result['response_time'].replace('s', '')) for result in self.test_results if 's' in result['response_time']]
        avg_response_time = sum(response_times) / len(response_times) if response_times else 0
        
        print(f"\n⚡ PERFORMANCE METRICS:")
        print(f"Average Response Time: {avg_response_time:.3f}s")
        print(f"Total Test Duration: {sum(response_times):.3f}s")
        
        print("\n🎯 FEATURE TESTING RESULTS:")
        feature_categories = {
            "Multi-step Onboarding": ["Onboarding Status", "Goal Selection", "Feature Selection", "Onboarding Completion"],
            "Subscription Management": ["Subscription Plans", "Current Subscription", "Usage Statistics", "Trial Start"],
            "Enhanced AI Features": ["AI Services", "AI Conversations", "AI Content Generation", "Create AI Conversation"],
            "Workspace Management": ["List Workspaces", "Create Workspace", "Advanced Team Management"],
            "Template Marketplace": ["Bio Site Themes", "Website Builder Templates"],
            "Advanced Analytics": ["Analytics Overview", "Advanced Business Intelligence", "Financial Dashboard", "Course Analytics"],
            "Existing Functionality": ["Admin Dashboard", "Bio Sites", "E-commerce Dashboard", "Booking Dashboard", "Notifications"]
        }
        
        for category, tests in feature_categories.items():
            category_results = [r for r in self.test_results if any(test in r['test'] for test in tests)]
            category_passed = sum(1 for r in category_results if r['success'])
            category_total = len(category_results)
            category_rate = (category_passed / category_total * 100) if category_total > 0 else 0
            status = "✅" if category_rate >= 75 else "⚠️" if category_rate >= 50 else "❌"
            print(f"{status} {category}: {category_passed}/{category_total} ({category_rate:.1f}%)")

def main():
    # Get backend URL from environment file
    try:
        with open('/app/frontend/.env', 'r') as f:
            for line in f:
                if line.startswith('REACT_APP_BACKEND_URL='):
                    backend_url = line.split('=', 1)[1].strip()
                    break
            else:
                backend_url = "https://c231d264-b140-4556-b515-9a9bf3fb6c1d.preview.emergentagent.com"
    except:
        backend_url = "https://c231d264-b140-4556-b515-9a9bf3fb6c1d.preview.emergentagent.com"
    
    print(f"🔗 Testing FastAPI Backend at: {backend_url}")
    
    tester = ComprehensiveFastAPITester(backend_url)
    success = tester.run_comprehensive_tests()
    
    if success:
        print("\n🎉 COMPREHENSIVE TESTING COMPLETED SUCCESSFULLY!")
    else:
        print("\n💥 COMPREHENSIVE TESTING FAILED!")
        sys.exit(1)

if __name__ == "__main__":
    main()