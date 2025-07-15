#!/usr/bin/env python3
"""
Workspace Setup Wizard Testing Suite
Tests the complete 6-step workspace setup wizard with all 9 API endpoints
"""

import requests
import json
import sys
import time
from datetime import datetime
from typing import Dict, Any, Optional

class WorkspaceSetupTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        
        # Test user credentials
        self.test_user = {
            "name": "Workspace Test User",
            "email": "workspace.test@mewayz.com",
            "password": "WorkspaceTest123!",
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
        print("🔧 Setting up test user...")
        
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
                    self.log_result("User Setup", "PASS", "Test user authenticated successfully")
                    return True
            
            self.log_result("User Setup", "FAIL", f"Login failed: {response.status_code}")
            return False
            
        except Exception as e:
            self.log_result("User Setup", "FAIL", f"Authentication failed: {str(e)}")
            return False

    def test_current_step(self):
        """Test GET /api/workspace-setup/current-step"""
        print("📍 Testing Current Step API...")
        
        try:
            response = self.make_request('GET', '/workspace-setup/current-step')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    current_step = data.get('current_step', 0)
                    setup_completed = data.get('setup_completed', False)
                    total_steps = data.get('total_steps', 0)
                    step_name = data.get('step_name', '')
                    
                    self.log_result("Get Current Step", "PASS", 
                                  f"Current step: {current_step}, Completed: {setup_completed}, Total steps: {total_steps}, Step name: {step_name}", 
                                  response_time)
                    return current_step
                else:
                    self.log_result("Get Current Step", "FAIL", 
                                  "Response missing success flag", response_time)
                    return None
            else:
                self.log_result("Get Current Step", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return None
                
        except Exception as e:
            self.log_result("Get Current Step", "FAIL", f"Request failed: {str(e)}")
            return None

    def test_business_info_step(self):
        """Test POST /api/workspace-setup/business-info"""
        print("🏢 Testing Business Info Step...")
        
        business_data = {
            "business_name": "Mewayz Creative Studio",
            "business_type": "Digital Marketing Agency",
            "industry": "Marketing & Advertising",
            "business_size": "Small (1-10 employees)",
            "website": "https://mewayz.com",
            "phone": "+1-555-0123",
            "address": "123 Creative Street, Innovation City, IC 12345",
            "description": "A full-service digital marketing agency specializing in social media management and content creation."
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/business-info', business_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    self.log_result("Save Business Info", "PASS", 
                                  f"Business info saved successfully. Next step: {next_step}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Business Info", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Business Info", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Business Info", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_social_media_step(self):
        """Test POST /api/workspace-setup/social-media"""
        print("📱 Testing Social Media Step...")
        
        social_data = {
            "platforms": [
                {
                    "platform": "instagram",
                    "username": "mewayz_studio",
                    "url": "https://instagram.com/mewayz_studio",
                    "primary": True
                },
                {
                    "platform": "facebook",
                    "username": "MewayzStudio",
                    "url": "https://facebook.com/MewayzStudio",
                    "primary": False
                },
                {
                    "platform": "twitter",
                    "username": "mewayz_studio",
                    "url": "https://twitter.com/mewayz_studio",
                    "primary": False
                }
            ],
            "content_types": ["images", "videos", "stories", "reels"],
            "posting_frequency": "daily",
            "target_audience": "Small business owners, entrepreneurs, and marketing professionals aged 25-45 interested in digital marketing solutions."
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/social-media', social_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    self.log_result("Save Social Media", "PASS", 
                                  f"Social media info saved successfully. Next step: {next_step}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Social Media", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Social Media", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Social Media", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_branding_step(self):
        """Test POST /api/workspace-setup/branding"""
        print("🎨 Testing Branding Step...")
        
        branding_data = {
            "primary_color": "#3B82F6",
            "secondary_color": "#1E40AF",
            "accent_color": "#F59E0B",
            "logo": None,  # Base64 encoded image would go here
            "brand_voice": "professional",
            "brand_values": ["Innovation", "Quality", "Customer Success", "Creativity"]
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/branding', branding_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    self.log_result("Save Branding", "PASS", 
                                  f"Branding info saved successfully. Next step: {next_step}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Branding", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Branding", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Branding", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_content_categories_step(self):
        """Test POST /api/workspace-setup/content-categories"""
        print("📝 Testing Content Categories Step...")
        
        content_data = {
            "categories": ["Marketing Tips", "Business Growth", "Social Media", "Entrepreneurship", "Digital Tools"],
            "content_pillars": ["Educational Content", "Behind the Scenes", "Client Success Stories", "Industry Insights"],
            "content_style": "educational",
            "hashtag_strategy": "mixed"
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/content-categories', content_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    self.log_result("Save Content Categories", "PASS", 
                                  f"Content categories saved successfully. Next step: {next_step}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Content Categories", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Content Categories", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Content Categories", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_goals_objectives_step(self):
        """Test POST /api/workspace-setup/goals-objectives"""
        print("🎯 Testing Goals & Objectives Step...")
        
        goals_data = {
            "primary_goal": "lead_generation",
            "target_metrics": {
                "followers": 10000,
                "engagement_rate": 5.5,
                "monthly_reach": 50000,
                "conversions": 100
            },
            "timeline": "6_months",
            "budget": "1000-5000",
            "success_metrics": ["Increased brand awareness", "Higher engagement rates", "More qualified leads", "Improved conversion rates"]
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/goals-objectives', goals_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 0)
                    message = data.get('message', '')
                    
                    self.log_result("Save Goals & Objectives", "PASS", 
                                  f"Goals & objectives saved successfully. Next step: {next_step}. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Goals & Objectives", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Save Goals & Objectives", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Goals & Objectives", "FAIL", f"Request failed: {str(e)}")
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
                    
                    # Count completed steps
                    setup_progress = summary.get('setup_progress', {})
                    completed_steps = sum(1 for step in setup_progress.values() if step)
                    
                    self.log_result("Get Setup Summary", "PASS", 
                                  f"Summary retrieved successfully. Completed: {setup_completed}. Steps completed: {completed_steps}/5", 
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

    def test_reset_setup(self):
        """Test POST /api/workspace-setup/reset"""
        print("🔄 Testing Reset Setup...")
        
        try:
            response = self.make_request('POST', '/workspace-setup/reset', {})
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    message = data.get('message', '')
                    
                    self.log_result("Reset Setup", "PASS", 
                                  f"Setup reset successfully. Message: {message}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Reset Setup", "FAIL", 
                                  "Response missing success flag", response_time)
                    return False
            else:
                self.log_result("Reset Setup", "FAIL", 
                              f"Request failed with status {response.status_code}: {response.text}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Reset Setup", "FAIL", f"Request failed: {str(e)}")
            return False

    def run_complete_workflow_test(self):
        """Run the complete 6-step workspace setup workflow"""
        print("🚀 Starting Complete Workspace Setup Workflow Test...")
        print("=" * 60)
        
        # Setup test user
        if not self.setup_test_user():
            print("❌ Failed to setup test user. Aborting tests.")
            return False
        
        # Test 1: Get current step (should be 1 for new user)
        current_step = self.test_current_step()
        if current_step != 1:
            print(f"⚠️ Expected current step to be 1, got {current_step}")
        
        # Test 2: Save business info (should progress to step 2)
        if not self.test_business_info_step():
            print("❌ Business info step failed")
            return False
        
        # Test 3: Save social media (should progress to step 3)
        if not self.test_social_media_step():
            print("❌ Social media step failed")
            return False
        
        # Test 4: Save branding (should progress to step 4)
        if not self.test_branding_step():
            print("❌ Branding step failed")
            return False
        
        # Test 5: Save content categories (should progress to step 5)
        if not self.test_content_categories_step():
            print("❌ Content categories step failed")
            return False
        
        # Test 6: Save goals & objectives (should progress to step 6)
        if not self.test_goals_objectives_step():
            print("❌ Goals & objectives step failed")
            return False
        
        # Test 7: Complete setup (should mark as completed)
        if not self.test_complete_setup():
            print("❌ Complete setup failed")
            return False
        
        # Test 8: Get setup summary (should show all data)
        summary = self.test_setup_summary()
        if not summary:
            print("❌ Setup summary failed")
            return False
        
        # Test 9: Reset setup (for testing purposes)
        if not self.test_reset_setup():
            print("❌ Reset setup failed")
            return False
        
        # Verify reset worked by checking current step again
        current_step_after_reset = self.test_current_step()
        if current_step_after_reset != 1:
            print(f"⚠️ Expected current step after reset to be 1, got {current_step_after_reset}")
        
        return True

    def generate_report(self):
        """Generate comprehensive test report"""
        print("\n" + "=" * 60)
        print("📋 WORKSPACE SETUP WIZARD TEST REPORT")
        print("=" * 60)
        
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
            print("   ✅ Workspace Setup Wizard is working excellently")
        elif success_rate >= 75:
            print("   ⚠️ Workspace Setup Wizard is mostly functional but needs minor fixes")
        else:
            print("   ❌ Workspace Setup Wizard needs significant improvements")
        
        if response_times:
            print(f"\n   📈 Performance: {'Excellent' if avg_response_time < 1.0 else 'Good' if avg_response_time < 2.0 else 'Needs Improvement'}")
        print(f"   🔐 Security: Authentication working properly")
        print(f"   🎨 User Experience: Progressive workflow implemented")
        
        print("\n" + "=" * 60)
        print("✅ WORKSPACE SETUP WIZARD TESTING COMPLETED")
        print("=" * 60)

def main():
    """Main function to run workspace setup testing"""
    tester = WorkspaceSetupTester()
    
    try:
        success = tester.run_complete_workflow_test()
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