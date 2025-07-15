#!/usr/bin/env python3
"""
Mewayz Platform - Workspace Setup Wizard Testing Suite
Tests the 6-step workspace setup wizard functionality
"""

import requests
import json
import sys
import time
from datetime import datetime
from typing import Dict, Any, Optional, List

class WorkspaceSetupTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        
        # Admin user credentials for testing
        self.admin_user = {
            "email": "admin@example.com", 
            "password": "admin123",
            "token": None,
            "role": 1,
            "id": None
        }
        
        self.current_user = None
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
        
        # Add auth token if user is set
        if self.current_user and self.current_user.get('token'):
            headers = headers or {}
            headers['Authorization'] = f'Bearer {self.current_user["token"]}'
        
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

    def login_admin(self):
        """Login admin user and get token"""
        # First try to register the admin user (in case they don't exist)
        register_data = {
            "name": "Admin User",
            "email": self.admin_user["email"],
            "password": self.admin_user["password"],
            "password_confirmation": self.admin_user["password"]
        }
        
        try:
            register_response = self.make_request('POST', '/auth/register', register_data)
            # Registration might fail if user exists, that's okay
        except:
            pass
        
        # Now login to get token
        login_data = {
            "email": self.admin_user["email"],
            "password": self.admin_user["password"]
        }
        
        try:
            response = self.make_request('POST', '/auth/login', login_data)
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.admin_user['token'] = data['token']
                    self.admin_user['id'] = data.get('user', {}).get('id')
                    self.current_user = self.admin_user
                    return True
            return False
        except Exception as e:
            print(f"Login failed: {str(e)}")
            return False

    def test_system_health(self):
        """Test system health and availability"""
        print("🔍 Testing System Health...")
        
        try:
            response = self.make_request('GET', '/health')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                self.log_result("System Health Check", "PASS", 
                              f"API responding correctly: {data.get('message', 'OK')}", 
                              response_time)
                return True
            else:
                self.log_result("System Health Check", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
        except Exception as e:
            self.log_result("System Health Check", "FAIL", 
                          f"System not accessible: {str(e)}")
            return False

    def test_authentication(self):
        """Test authentication system"""
        print("🔐 Testing Authentication...")
        
        if not self.login_admin():
            self.log_result("Admin Login", "FAIL", "Failed to login admin user")
            return False
        
        self.log_result("Admin Login", "PASS", "Successfully logged in admin user")
        
        # Test getting current user info
        try:
            response = self.make_request('GET', '/auth/me')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('user'):
                    user_info = data['user']
                    self.log_result("Get Current User", "PASS", 
                                  f"User authenticated: {user_info.get('email', 'Unknown')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Get Current User", "FAIL", 
                                  "Response missing user data", 
                                  response_time)
                    return False
            else:
                self.log_result("Get Current User", "FAIL", 
                              f"Authentication failed with status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Get Current User", "FAIL", 
                          f"Authentication request failed: {str(e)}")
            return False

    def test_current_step_api(self):
        """Test GET /api/workspace-setup/current-step"""
        print("📋 Testing Current Step API...")
        
        try:
            response = self.make_request('GET', '/workspace-setup/current-step')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    current_step = data.get('current_step', 1)
                    setup_completed = data.get('setup_completed', False)
                    total_steps = data.get('total_steps', 6)
                    
                    self.log_result("Get Current Step", "PASS", 
                                  f"Current step: {current_step}/{total_steps}, Completed: {setup_completed}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Get Current Step", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Get Current Step", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Get Current Step", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_business_info_step(self):
        """Test POST /api/workspace-setup/business-info"""
        print("🏢 Testing Business Info Step...")
        
        business_data = {
            "business_name": "Test Company",
            "business_type": "llc",
            "industry": "technology",
            "business_size": "1-10",
            "website": "https://testcompany.com",
            "phone": "+1-555-123-4567",
            "address": "123 Test Street, Test City, TC 12345",
            "description": "A test company for workspace setup testing"
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/business-info', business_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 2)
                    self.log_result("Save Business Info", "PASS", 
                                  f"Business info saved, next step: {next_step}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Business Info", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Save Business Info", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Business Info", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_social_media_step(self):
        """Test POST /api/workspace-setup/social-media"""
        print("📱 Testing Social Media Step...")
        
        social_data = {
            "platforms": [
                {
                    "platform": "instagram",
                    "username": "testcompany",
                    "url": "https://instagram.com/testcompany",
                    "primary": True
                },
                {
                    "platform": "facebook",
                    "username": "testcompany",
                    "url": "https://facebook.com/testcompany",
                    "primary": False
                }
            ],
            "content_types": ["photos", "videos", "stories"],
            "posting_frequency": "daily",
            "target_audience": "Tech-savvy professionals aged 25-45 interested in business solutions"
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/social-media', social_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 3)
                    self.log_result("Save Social Media", "PASS", 
                                  f"Social media info saved, next step: {next_step}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Social Media", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Save Social Media", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Social Media", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_branding_step(self):
        """Test POST /api/workspace-setup/branding"""
        print("🎨 Testing Branding Step...")
        
        branding_data = {
            "primary_color": "#007cba",
            "secondary_color": "#0096dd",
            "accent_color": "#28a745",
            "brand_voice": "professional",
            "brand_values": ["innovation", "reliability", "customer-focused"]
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/branding', branding_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 4)
                    self.log_result("Save Branding", "PASS", 
                                  f"Branding info saved, next step: {next_step}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Branding", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Save Branding", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Branding", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_content_categories_step(self):
        """Test POST /api/workspace-setup/content-categories"""
        print("📝 Testing Content Categories Step...")
        
        content_data = {
            "categories": ["technology", "business", "tutorials", "news"],
            "content_pillars": ["education", "entertainment", "inspiration", "promotion"],
            "content_style": "educational",
            "hashtag_strategy": "mixed"
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/content-categories', content_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 5)
                    self.log_result("Save Content Categories", "PASS", 
                                  f"Content categories saved, next step: {next_step}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Content Categories", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Save Content Categories", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Content Categories", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_goals_objectives_step(self):
        """Test POST /api/workspace-setup/goals-objectives"""
        print("🎯 Testing Goals & Objectives Step...")
        
        goals_data = {
            "primary_goal": "brand_awareness",
            "target_metrics": {
                "followers": 10000,
                "engagement_rate": 5.5,
                "monthly_reach": 50000,
                "conversions": 100
            },
            "timeline": "3_months",
            "budget": "1000-5000",
            "success_metrics": ["follower growth", "engagement rate", "website traffic"]
        }
        
        try:
            response = self.make_request('POST', '/workspace-setup/goals-objectives', goals_data)
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    next_step = data.get('next_step', 6)
                    self.log_result("Save Goals & Objectives", "PASS", 
                                  f"Goals & objectives saved, next step: {next_step}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Save Goals & Objectives", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Save Goals & Objectives", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Save Goals & Objectives", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_setup_summary(self):
        """Test GET /api/workspace-setup/summary"""
        print("📊 Testing Setup Summary...")
        
        try:
            response = self.make_request('GET', '/workspace-setup/summary')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    summary = data.get('summary', {})
                    setup_completed = summary.get('setup_completed', False)
                    
                    # Check if all sections are present
                    expected_sections = ['business_info', 'social_media', 'branding', 'content_categories', 'goals_objectives']
                    missing_sections = [section for section in expected_sections if not summary.get(section)]
                    
                    if missing_sections:
                        self.log_result("Get Setup Summary", "FAIL", 
                                      f"Missing sections: {', '.join(missing_sections)}", 
                                      response_time)
                        return False
                    else:
                        self.log_result("Get Setup Summary", "PASS", 
                                      f"Summary retrieved with all sections, completed: {setup_completed}", 
                                      response_time)
                        return True
                else:
                    self.log_result("Get Setup Summary", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Get Setup Summary", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Get Setup Summary", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_complete_setup(self):
        """Test POST /api/workspace-setup/complete"""
        print("✅ Testing Complete Setup...")
        
        try:
            response = self.make_request('POST', '/workspace-setup/complete')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    workspace = data.get('workspace', {})
                    self.log_result("Complete Setup", "PASS", 
                                  f"Setup completed successfully for workspace: {workspace.get('name', 'Unknown')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Complete Setup", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Complete Setup", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Complete Setup", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def test_reset_setup(self):
        """Test POST /api/workspace-setup/reset"""
        print("🔄 Testing Reset Setup...")
        
        try:
            response = self.make_request('POST', '/workspace-setup/reset')
            response_time = response.elapsed.total_seconds()
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("Reset Setup", "PASS", 
                                  "Setup reset successfully", 
                                  response_time)
                    return True
                else:
                    self.log_result("Reset Setup", "FAIL", 
                                  f"API returned error: {data.get('error', 'Unknown error')}", 
                                  response_time)
                    return False
            else:
                self.log_result("Reset Setup", "FAIL", 
                              f"API returned status {response.status_code}", 
                              response_time)
                return False
                
        except Exception as e:
            self.log_result("Reset Setup", "FAIL", 
                          f"Request failed: {str(e)}")
            return False

    def run_workspace_setup_tests(self):
        """Run all workspace setup wizard tests"""
        print("🚀 Starting Workspace Setup Wizard Testing...")
        print("=" * 60)
        
        # Test system health first
        if not self.test_system_health():
            print("❌ System health check failed. Aborting tests.")
            return False
        
        # Test authentication
        if not self.test_authentication():
            print("❌ Authentication failed. Aborting tests.")
            return False
        
        # Test all workspace setup endpoints
        test_functions = [
            self.test_current_step_api,
            self.test_business_info_step,
            self.test_social_media_step,
            self.test_branding_step,
            self.test_content_categories_step,
            self.test_goals_objectives_step,
            self.test_setup_summary,
            self.test_complete_setup,
            self.test_reset_setup
        ]
        
        for test_func in test_functions:
            try:
                test_func()
            except Exception as e:
                self.log_result(test_func.__name__, "FAIL", 
                              f"Test function failed: {str(e)}")
        
        # Generate report
        self.generate_report()
        
        return True

    def generate_report(self):
        """Generate testing report"""
        print("\n" + "=" * 60)
        print("📋 WORKSPACE SETUP WIZARD TEST REPORT")
        print("=" * 60)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r['status'] == 'PASS'])
        failed_tests = len([r for r in self.test_results if r['status'] == 'FAIL'])
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 TESTING STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        # Performance metrics
        response_times = [r['response_time'] for r in self.test_results if r['response_time'] > 0]
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            print(f"   Average Response Time: {avg_response_time:.3f}s")
        
        # Failed tests details
        failed_results = [r for r in self.test_results if r['status'] == 'FAIL']
        if failed_results:
            print(f"\n🚨 FAILED TESTS:")
            for result in failed_results:
                print(f"   ❌ {result['test']}: {result['details']}")
        
        # Recommendations
        print(f"\n💡 RECOMMENDATIONS:")
        if success_rate >= 90:
            print("   ✅ Workspace Setup Wizard is working correctly")
        elif success_rate >= 75:
            print("   ⚠️ Workspace Setup Wizard mostly functional but needs minor fixes")
        else:
            print("   ❌ Workspace Setup Wizard needs significant fixes before production")
        
        print("\n" + "=" * 60)
        print("✅ WORKSPACE SETUP WIZARD TESTING COMPLETED")
        print("=" * 60)

def main():
    """Main function to run workspace setup testing"""
    tester = WorkspaceSetupTester()
    
    try:
        success = tester.run_workspace_setup_tests()
        return 0 if success else 1
    except KeyboardInterrupt:
        print("\n⚠️ Testing interrupted by user")
        return 1
    except Exception as e:
        print(f"\n❌ Testing failed with error: {str(e)}")
        return 1

if __name__ == "__main__":
    sys.exit(main())