#!/usr/bin/env python3
"""
Comprehensive Platform Testing - Final Validation for Mewayz Platform v3.0.0
Tests all enhanced features including new Link Shortener, Team Management, 
Form Templates, Discount Codes systems, plus verification of existing features.
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class MewayzV3ComprehensiveTester:
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
            return None, 30.0  # Return timeout duration
        except requests.exceptions.RequestException as e:
            print(f"Request error: {str(e)}")
            return None, 0.0

    def test_authentication(self):
        """Test authentication with provided credentials"""
        print("\n=== Testing Authentication System ===")
        
        # Test admin login with provided credentials
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                self.log_test("Admin Authentication", True, f"Admin user: {data.get('user', {}).get('email')}", response_time)
                return True
            else:
                self.log_test("Admin Authentication", False, f"Login failed: {data.get('message')}", response_time)
                return False
        else:
            self.log_test("Admin Authentication", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
            return False

    def test_health_check_v3(self):
        """Test updated health check for v3.0.0"""
        print("\n=== Testing Updated Health Check (v3.0.0) ===")
        
        response, response_time = self.make_request('GET', '/health')
        if response and response.status_code == 200:
            data = response.json()
            version = data.get('version', 'unknown')
            features_count = len(data.get('features', []))
            
            # Check if it shows v3.0.0 and has expected features
            if version == '3.0.0' and features_count >= 18:
                self.log_test("Health Check v3.0.0", True, f"Version: {version}, Features: {features_count}", response_time)
            else:
                self.log_test("Health Check v3.0.0", False, f"Version: {version}, Features: {features_count} (expected v3.0.0 with 18+ features)", response_time)
        else:
            self.log_test("Health Check v3.0.0", False, f"Status: {response.status_code if response else 'timeout'}", response_time)

    def test_link_shortener_system(self):
        """Test Link Shortener System"""
        print("\n=== Testing Link Shortener System ===")
        
        if not self.auth_token:
            self.log_test("Link Shortener System", False, "No authentication token available")
            return
        
        # Test GET /api/link-shortener/links
        response, response_time = self.make_request('GET', '/link-shortener/links')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Link Shortener - Get Links", True, f"Retrieved {len(data.get('links', []))} links", response_time)
        else:
            self.log_test("Link Shortener - Get Links", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/link-shortener/create
        create_data = {
            "original_url": "https://example.com/test-link",
            "custom_code": f"test{int(time.time())}",
            "title": "Test Link"
        }
        
        response, response_time = self.make_request('POST', '/link-shortener/create', create_data)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Link Shortener - Create Link", True, f"Created link: {data.get('short_url', 'unknown')}", response_time)
        else:
            self.log_test("Link Shortener - Create Link", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test GET /api/link-shortener/stats
        response, response_time = self.make_request('GET', '/link-shortener/stats')
        if response and response.status_code == 200:
            data = response.json()
            total_links = data.get('total_links', 0)
            total_clicks = data.get('total_clicks', 0)
            self.log_test("Link Shortener - Get Stats", True, f"Total links: {total_links}, Total clicks: {total_clicks}", response_time)
        else:
            self.log_test("Link Shortener - Get Stats", False, f"Status: {response.status_code if response else 'timeout'}", response_time)

    def test_team_management_system(self):
        """Test Team Management System"""
        print("\n=== Testing Team Management System ===")
        
        if not self.auth_token:
            self.log_test("Team Management System", False, "No authentication token available")
            return
        
        # Test GET /api/team/members
        response, response_time = self.make_request('GET', '/team/members')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Team Management - Get Members", True, f"Retrieved {len(data.get('members', []))} members", response_time)
        else:
            self.log_test("Team Management - Get Members", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/team/invite
        invite_data = {
            "email": f"testmember{int(time.time())}@example.com",
            "role": "member",
            "name": "Test Team Member"
        }
        
        response, response_time = self.make_request('POST', '/team/invite', invite_data)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Team Management - Send Invite", True, f"Invite sent to: {invite_data['email']}", response_time)
        else:
            self.log_test("Team Management - Send Invite", False, f"Status: {response.status_code if response else 'timeout'}", response_time)

    def test_form_templates_system(self):
        """Test Form Templates System"""
        print("\n=== Testing Form Templates System ===")
        
        if not self.auth_token:
            self.log_test("Form Templates System", False, "No authentication token available")
            return
        
        # Test GET /api/form-templates
        response, response_time = self.make_request('GET', '/form-templates')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Form Templates - Get Templates", True, f"Retrieved {len(data.get('templates', []))} templates", response_time)
        else:
            self.log_test("Form Templates - Get Templates", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/form-templates
        template_data = {
            "name": f"Test Template {int(time.time())}",
            "description": "A test form template",
            "category": "feedback",
            "fields": [
                {
                    "name": "name",
                    "type": "text",
                    "label": "Full Name",
                    "required": True
                },
                {
                    "name": "email",
                    "type": "email",
                    "label": "Email Address",
                    "required": True
                },
                {
                    "name": "message",
                    "type": "textarea",
                    "label": "Message",
                    "required": False
                }
            ]
        }
        
        response, response_time = self.make_request('POST', '/form-templates', template_data)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Form Templates - Create Template", True, f"Created template: {template_data['name']}", response_time)
        else:
            self.log_test("Form Templates - Create Template", False, f"Status: {response.status_code if response else 'timeout'}", response_time)

    def test_discount_codes_system(self):
        """Test Discount Codes System"""
        print("\n=== Testing Discount Codes System ===")
        
        if not self.auth_token:
            self.log_test("Discount Codes System", False, "No authentication token available")
            return
        
        # Test GET /api/discount-codes
        response, response_time = self.make_request('GET', '/discount-codes')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Discount Codes - Get Codes", True, f"Retrieved {len(data.get('codes', []))} codes", response_time)
        else:
            self.log_test("Discount Codes - Get Codes", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test POST /api/discount-codes
        code_data = {
            "code": f"TEST{int(time.time())}",
            "description": "Test discount code",
            "type": "percentage",
            "value": 15.0,
            "usage_limit": 100,
            "expires_at": "2025-12-31T23:59:59Z"
        }
        
        response, response_time = self.make_request('POST', '/discount-codes', code_data)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Discount Codes - Create Code", True, f"Created code: {code_data['code']}", response_time)
        else:
            self.log_test("Discount Codes - Create Code", False, f"Status: {response.status_code if response else 'timeout'}", response_time)

    def test_existing_features_verification(self):
        """Verify existing features are still working"""
        print("\n=== Verifying Existing Features ===")
        
        if not self.auth_token:
            self.log_test("Existing Features Verification", False, "No authentication token available")
            return
        
        # Test Bio Sites functionality
        response, response_time = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            self.log_test("Bio Sites - Themes", True, "Bio sites themes accessible", response_time)
        else:
            self.log_test("Bio Sites - Themes", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test E-commerce system
        response, response_time = self.make_request('GET', '/ecommerce/dashboard')
        if response and response.status_code == 200:
            self.log_test("E-commerce - Dashboard", True, "E-commerce dashboard accessible", response_time)
        else:
            self.log_test("E-commerce - Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test Course management
        response, response_time = self.make_request('GET', '/courses/')
        if response and response.status_code == 200:
            self.log_test("Course Management", True, "Course management accessible", response_time)
        else:
            self.log_test("Course Management", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test Analytics system
        response, response_time = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            self.log_test("Analytics System", True, "Analytics system accessible", response_time)
        else:
            self.log_test("Analytics System", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test Workspace system
        response, response_time = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            self.log_test("Workspace System", True, "Workspace system accessible", response_time)
        else:
            self.log_test("Workspace System", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Test AI Services
        response, response_time = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            self.log_test("AI Services", True, "AI services accessible", response_time)
        else:
            self.log_test("AI Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time)

    def run_comprehensive_v3_tests(self):
        """Run all v3.0.0 comprehensive tests"""
        print("🚀 Starting Comprehensive Platform Testing - Final Validation")
        print("Testing Mewayz Platform v3.0.0 Enhanced Features")
        print(f"Testing backend at: {self.api_url}")
        print("=" * 70)
        
        # Test authentication first
        if not self.test_authentication():
            print("❌ CRITICAL: Authentication failed. Cannot proceed with other tests.")
            return
        
        # Test updated health check
        self.test_health_check_v3()
        
        # Test new features
        self.test_link_shortener_system()
        self.test_team_management_system()
        self.test_form_templates_system()
        self.test_discount_codes_system()
        
        # Verify existing features still work
        self.test_existing_features_verification()
        
        # Generate comprehensive summary
        self.generate_comprehensive_summary()
    
    def generate_comprehensive_summary(self):
        """Generate comprehensive test summary"""
        print("\n" + "=" * 70)
        print("📊 COMPREHENSIVE PLATFORM TESTING SUMMARY - MEWAYZ V3.0.0")
        print("=" * 70)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Categorize results by feature
        new_features = [r for r in self.test_results if any(keyword in r['test'] for keyword in ['Link Shortener', 'Team Management', 'Form Templates', 'Discount Codes'])]
        existing_features = [r for r in self.test_results if any(keyword in r['test'] for keyword in ['Bio Sites', 'E-commerce', 'Course', 'Analytics', 'Workspace', 'AI Services'])]
        system_features = [r for r in self.test_results if any(keyword in r['test'] for keyword in ['Authentication', 'Health Check'])]
        
        print(f"\n🆕 NEW FEATURES TESTING:")
        new_passed = sum(1 for r in new_features if r['success'])
        new_percentage = (new_passed/len(new_features)*100) if len(new_features) > 0 else 0
        print(f"   Passed: {new_passed}/{len(new_features)} ({new_percentage:.1f}%)")
        
        print(f"\n🔄 EXISTING FEATURES VERIFICATION:")
        existing_passed = sum(1 for r in existing_features if r['success'])
        existing_percentage = (existing_passed/len(existing_features)*100) if len(existing_features) > 0 else 0
        print(f"   Passed: {existing_passed}/{len(existing_features)} ({existing_percentage:.1f}%)")
        
        print(f"\n⚙️  SYSTEM FEATURES:")
        system_passed = sum(1 for r in system_features if r['success'])
        system_percentage = (system_passed/len(system_features)*100) if len(system_features) > 0 else 0
        print(f"   Passed: {system_passed}/{len(system_features)} ({system_percentage:.1f}%)")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  • {result['test']}")
        
        print("\n" + "=" * 70)
        
        # Overall assessment
        if success_rate >= 90:
            print("🎉 EXCELLENT: Mewayz Platform v3.0.0 is highly functional and production-ready!")
            print("   All enhanced features are working properly with existing functionality maintained.")
        elif success_rate >= 75:
            print("✅ GOOD: Mewayz Platform v3.0.0 is mostly functional with minor issues.")
            print("   Most enhanced features are working with existing functionality largely maintained.")
        elif success_rate >= 50:
            print("⚠️  MODERATE: Mewayz Platform v3.0.0 has some significant issues.")
            print("   Some enhanced features need attention but core functionality may be working.")
        else:
            print("❌ CRITICAL: Mewayz Platform v3.0.0 has major functionality problems.")
            print("   Enhanced features and/or existing functionality have serious issues.")

def main():
    """Main function to run the comprehensive v3.0.0 tests"""
    # Backend URL from environment
    backend_url = "https://a647fc73-10e4-498f-8ae8-6d85330ad2a0.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    print(f"🔐 Authentication: tmonnens@outlook.com / Voetballen5")
    
    # Initialize tester
    tester = MewayzV3ComprehensiveTester(backend_url)
    
    # Run comprehensive v3.0.0 tests
    tester.run_comprehensive_v3_tests()

if __name__ == "__main__":
    main()