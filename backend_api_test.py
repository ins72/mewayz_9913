#!/usr/bin/env python3
"""
Mewayz Laravel Backend API Testing Suite
Tests all API endpoints with proper authentication
"""

import requests
import json
import sys
import time
from datetime import datetime

class MewayzAPITester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        self.test_results = []
        self.auth_token = None
        
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

    def make_request(self, method: str, endpoint: str, data: dict = None, auth_required: bool = True):
        """Make HTTP request with proper headers"""
        url = f"{self.api_url}/{endpoint.lstrip('/')}"
        
        headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
        
        if auth_required and self.auth_token:
            headers['Authorization'] = f'Bearer {self.auth_token}'
        
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
            return response, response_time
        except requests.exceptions.RequestException as e:
            response_time = time.time() - start_time
            print(f"Request failed: {e}")
            return None, response_time

    def test_health_check(self):
        """Test API health endpoint"""
        print("🔍 Testing API Health Check...")
        
        response, response_time = self.make_request('GET', '/health', auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('status') == 'ok':
                    self.log_result("API Health Check", "PASS", 
                                  f"API is healthy: {data.get('message', 'OK')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("API Health Check", "FAIL", 
                                  "Health check returned unexpected status", 
                                  response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("API Health Check", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("API Health Check", "FAIL", 
                          f"Health check failed with status {status_code}", 
                          response_time)
            return False

    def test_user_registration(self):
        """Test user registration"""
        print("📝 Testing User Registration...")
        
        # Create unique email for testing
        timestamp = int(time.time())
        register_data = {
            "name": "Test User",
            "email": f"testuser{timestamp}@example.com",
            "password": "password123",
            "password_confirmation": "password123"
        }
        
        response, response_time = self.make_request('POST', '/auth/register', register_data, auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.auth_token = data['token']  # Store token for subsequent tests
                    self.log_result("User Registration", "PASS", 
                                  f"User registered successfully: {data.get('user', {}).get('email')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("User Registration", "FAIL", 
                                  "Registration response missing success or token", 
                                  response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("User Registration", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("User Registration", "FAIL", 
                          f"Registration failed with status {status_code}", 
                          response_time)
            return False

    def test_user_authentication(self):
        """Test user authentication endpoints"""
        print("🔐 Testing User Authentication...")
        
        # Test /auth/me endpoint
        response, response_time = self.make_request('GET', '/auth/me')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and data.get('user'):
                    user_info = data['user']
                    self.log_result("Get Current User", "PASS", 
                                  f"User authenticated: {user_info.get('email')}", 
                                  response_time)
                    return True
                else:
                    self.log_result("Get Current User", "FAIL", 
                                  "Authentication response missing user data", 
                                  response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("Get Current User", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("Get Current User", "FAIL", 
                          f"Authentication failed with status {status_code}", 
                          response_time)
            return False

    def test_profile_update(self):
        """Test profile update"""
        print("👤 Testing Profile Update...")
        
        update_data = {
            "name": "Updated Test User"
        }
        
        response, response_time = self.make_request('PUT', '/auth/profile', update_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_result("Profile Update", "PASS", 
                                  "Profile updated successfully", 
                                  response_time)
                    return True
                else:
                    self.log_result("Profile Update", "FAIL", 
                                  "Profile update response missing success flag", 
                                  response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("Profile Update", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("Profile Update", "FAIL", 
                          f"Profile update failed with status {status_code}", 
                          response_time)
            return False

    def test_workspace_endpoints(self):
        """Test workspace management endpoints"""
        print("🏢 Testing Workspace Endpoints...")
        
        # Test list workspaces
        response, response_time = self.make_request('GET', '/workspaces')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                workspaces = data.get('data', []) if isinstance(data.get('data'), list) else []
                self.log_result("List Workspaces", "PASS", 
                              f"Retrieved {len(workspaces)} workspaces", 
                              response_time)
                
                # Test create workspace
                workspace_data = {
                    "name": f"Test Workspace {int(time.time())}",
                    "description": "Test workspace for API testing"
                }
                
                create_response, create_time = self.make_request('POST', '/workspaces', workspace_data)
                
                if create_response and create_response.status_code in [200, 201]:
                    create_data = create_response.json()
                    self.log_result("Create Workspace", "PASS", 
                                  "Workspace created successfully", 
                                  create_time)
                    return True
                else:
                    status_code = create_response.status_code if create_response else "No response"
                    self.log_result("Create Workspace", "FAIL", 
                                  f"Workspace creation failed with status {status_code}", 
                                  create_time)
                    return False
                    
            except json.JSONDecodeError:
                self.log_result("List Workspaces", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("List Workspaces", "FAIL", 
                          f"List workspaces failed with status {status_code}", 
                          response_time)
            return False

    def test_social_media_endpoints(self):
        """Test social media management endpoints"""
        print("📱 Testing Social Media Endpoints...")
        
        # Test get social media accounts
        response, response_time = self.make_request('GET', '/social-media/accounts')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    accounts = data.get('data', [])
                    self.log_result("Get Social Media Accounts", "PASS", 
                                  f"Retrieved {len(accounts)} social media accounts", 
                                  response_time)
                    
                    # Test social media analytics
                    analytics_response, analytics_time = self.make_request('GET', '/social-media/analytics')
                    
                    if analytics_response and analytics_response.status_code == 200:
                        analytics_data = analytics_response.json()
                        if analytics_data.get('success'):
                            self.log_result("Social Media Analytics", "PASS", 
                                          "Social media analytics retrieved successfully", 
                                          analytics_time)
                            return True
                        else:
                            self.log_result("Social Media Analytics", "FAIL", 
                                          "Analytics response missing success flag", 
                                          analytics_time)
                            return False
                    else:
                        status_code = analytics_response.status_code if analytics_response else "No response"
                        self.log_result("Social Media Analytics", "FAIL", 
                                      f"Analytics failed with status {status_code}", 
                                      analytics_time)
                        return False
                else:
                    self.log_result("Get Social Media Accounts", "FAIL", 
                                  "Response missing success flag", 
                                  response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("Get Social Media Accounts", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("Get Social Media Accounts", "FAIL", 
                          f"Failed with status {status_code}", 
                          response_time)
            return False

    def test_bio_site_endpoints(self):
        """Test bio site management endpoints"""
        print("🔗 Testing Bio Site Endpoints...")
        
        # Test list bio sites
        response, response_time = self.make_request('GET', '/bio-sites')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    bio_sites = data.get('data', [])
                    self.log_result("List Bio Sites", "PASS", 
                                  f"Retrieved {len(bio_sites)} bio sites", 
                                  response_time)
                    
                    # Test create bio site
                    bio_site_data = {
                        "name": f"Test Bio Site {int(time.time())}",
                        "slug": f"test-bio-{int(time.time())}",
                        "description": "Test bio site for API testing",
                        "theme": "modern",
                        "is_active": True
                    }
                    
                    create_response, create_time = self.make_request('POST', '/bio-sites', bio_site_data)
                    
                    if create_response and create_response.status_code in [200, 201]:
                        create_data = create_response.json()
                        if create_data.get('success'):
                            self.log_result("Create Bio Site", "PASS", 
                                          "Bio site created successfully", 
                                          create_time)
                            return True
                        else:
                            self.log_result("Create Bio Site", "FAIL", 
                                          "Creation succeeded but missing success flag", 
                                          create_time)
                            return False
                    else:
                        status_code = create_response.status_code if create_response else "No response"
                        self.log_result("Create Bio Site", "FAIL", 
                                      f"Bio site creation failed with status {status_code}", 
                                      create_time)
                        return False
                else:
                    self.log_result("List Bio Sites", "FAIL", 
                                  "Response missing success flag", 
                                  response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("List Bio Sites", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("List Bio Sites", "FAIL", 
                          f"Failed with status {status_code}", 
                          response_time)
            return False

    def test_analytics_endpoints(self):
        """Test analytics endpoints"""
        print("📊 Testing Analytics Endpoints...")
        
        # Test analytics overview
        response, response_time = self.make_request('GET', '/analytics')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_result("Analytics Overview", "PASS", 
                              "Analytics overview retrieved successfully", 
                              response_time)
                
                # Test analytics reports
                reports_response, reports_time = self.make_request('GET', '/analytics/reports')
                
                if reports_response and reports_response.status_code == 200:
                    reports_data = reports_response.json()
                    self.log_result("Analytics Reports", "PASS", 
                                  "Analytics reports retrieved successfully", 
                                  reports_time)
                    return True
                else:
                    status_code = reports_response.status_code if reports_response else "No response"
                    self.log_result("Analytics Reports", "FAIL", 
                                  f"Reports request failed with status {status_code}", 
                                  reports_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("Analytics Overview", "FAIL", 
                              "Invalid JSON response", 
                              response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_result("Analytics Overview", "FAIL", 
                          f"Overview request failed with status {status_code}", 
                          response_time)
            return False

    def run_comprehensive_test(self):
        """Run comprehensive API testing"""
        print("🚀 Starting Comprehensive Mewayz Backend API Testing...")
        print("=" * 60)
        
        # Test sequence
        test_functions = [
            self.test_health_check,
            self.test_user_registration,
            self.test_user_authentication,
            self.test_profile_update,
            self.test_workspace_endpoints,
            self.test_social_media_endpoints,
            self.test_bio_site_endpoints,
            self.test_analytics_endpoints
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
        print("📋 BACKEND API TESTING REPORT")
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
        
        # List passed tests
        passed_results = [r for r in self.test_results if r['status'] == 'PASS']
        if passed_results:
            print(f"\n✅ PASSED TESTS:")
            for result in passed_results:
                print(f"   ✅ {result['test']}")
        
        # List failed tests
        failed_results = [r for r in self.test_results if r['status'] == 'FAIL']
        if failed_results:
            print(f"\n❌ FAILED TESTS:")
            for result in failed_results:
                print(f"   ❌ {result['test']}: {result['details']}")
        
        # Overall assessment
        print(f"\n🎯 OVERALL ASSESSMENT:")
        if success_rate >= 90:
            print("   ✅ Backend API is production-ready with excellent functionality")
        elif success_rate >= 75:
            print("   ⚠️ Backend API is mostly functional but needs minor fixes")
        else:
            print("   ❌ Backend API needs significant improvements before production")
        
        print("\n" + "=" * 60)
        print("✅ BACKEND API TESTING COMPLETED")
        print("=" * 60)

def main():
    """Main function to run API testing"""
    tester = MewayzAPITester()
    
    try:
        success = tester.run_comprehensive_test()
        return 0 if success else 1
    except KeyboardInterrupt:
        print("\n⚠️ Testing interrupted by user")
        return 1
    except Exception as e:
        print(f"\n❌ Testing failed with error: {str(e)}")
        return 1

if __name__ == "__main__":
    sys.exit(main())