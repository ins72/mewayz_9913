#!/usr/bin/env python3
"""
Focused Bio Sites Testing for Mewayz Creator Economy Platform
Testing Bio Sites & Link-in-Bio functionality specifically
"""

import requests
import json
import sys
import time
from datetime import datetime

class BioSitesAPITester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.auth_token = None
        self.user_id = None
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
        # Add delay to avoid rate limiting
        time.sleep(0.2)
        
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
                response = self.session.get(url, headers=default_headers, params=data, timeout=15)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=15)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=15)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=15)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 15 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None
    
    def test_authentication_setup(self):
        """Set up authentication for testing"""
        print("\n=== Setting Up Authentication ===")
        
        # Test user registration (new user)
        register_data = {
            "name": "Bio Sites Tester",
            "email": f"biosites_tester_{int(time.time())}@example.com",
            "password": "SecurePassword123!",
            "password_confirmation": "SecurePassword123!"
        }
        
        response = self.make_request('POST', '/auth/register', register_data, auth_required=False)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("User Registration", True, "User registration successful")
            
            # Store auth token if provided
            if 'token' in data:
                self.auth_token = data['token']
            elif 'access_token' in data:
                self.auth_token = data['access_token']
                
            if 'user' in data:
                self.user_id = data['user'].get('id')
                
            print(f"Auth token: {self.auth_token}")
            return True
        else:
            self.log_test("User Registration", False, f"Registration failed - Status: {response.status_code if response else 'No response'}")
            return False
    
    def test_bio_sites_comprehensive(self):
        """Test Bio Sites & Link-in-Bio functionality comprehensively"""
        print("\n=== Testing Bio Sites & Link-in-Bio (Comprehensive) ===")
        
        if not self.auth_token:
            self.log_test("Bio Sites", False, "Cannot test - no authentication token")
            return
        
        # Test 1: Get bio sites (should work)
        print("\n--- Test 1: Get Bio Sites ---")
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Get Bio Sites", True, f"Bio sites retrieval successful - Found {len(data.get('data', []))} sites")
        else:
            error_msg = "Unknown error"
            if response:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', f'HTTP {response.status_code}')
                except:
                    error_msg = f'HTTP {response.status_code}'
            self.log_test("Get Bio Sites", False, f"Bio sites retrieval failed - {error_msg}")
        
        # Test 2: Get bio site themes (should work)
        print("\n--- Test 2: Get Bio Site Themes ---")
        response = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            data = response.json()
            themes = data.get('data', [])
            self.log_test("Get Bio Site Themes", True, f"Themes retrieval successful - Found {len(themes)} themes")
        else:
            error_msg = "Unknown error"
            if response:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', f'HTTP {response.status_code}')
                except:
                    error_msg = f'HTTP {response.status_code}'
            self.log_test("Get Bio Site Themes", False, f"Themes retrieval failed - {error_msg}")
        
        # Test 3: Create bio site (comprehensive data)
        print("\n--- Test 3: Create Bio Site ---")
        bio_site_data = {
            "name": "Creative Portfolio Bio",
            "title": "Creative Portfolio Bio",
            "slug": f"creative-portfolio-{int(time.time())}",
            "description": "A comprehensive bio site for creative professionals showcasing their work and services",
            "theme": "minimal",
            "theme_config": {
                "primary_color": "#3B82F6",
                "secondary_color": "#10B981",
                "font_family": "Inter",
                "layout": "centered"
            },
            "is_active": True,
            "meta_title": "Creative Portfolio - Professional Bio Site",
            "meta_description": "Discover my creative work and professional services through this comprehensive bio site"
        }
        
        response = self.make_request('POST', '/bio-sites/', bio_site_data)
        bio_site_id = None
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("Create Bio Site", True, "Bio site creation successful")
            bio_site_id = data.get('id') or data.get('data', {}).get('id')
            print(f"Created bio site ID: {bio_site_id}")
        else:
            error_msg = "Unknown error"
            if response:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    if 'errors' in error_data:
                        error_msg += f" - Validation errors: {error_data['errors']}"
                except:
                    error_msg = f'HTTP {response.status_code}'
            self.log_test("Create Bio Site", False, f"Bio site creation failed - {error_msg}")
        
        # Test 4: Get specific bio site (if created successfully)
        if bio_site_id:
            print(f"\n--- Test 4: Get Specific Bio Site (ID: {bio_site_id}) ---")
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}')
            if response and response.status_code == 200:
                data = response.json()
                self.log_test("Get Specific Bio Site", True, f"Specific bio site retrieval successful - Name: {data.get('data', {}).get('name', 'Unknown')}")
            else:
                error_msg = "Unknown error"
                if response:
                    try:
                        error_data = response.json()
                        error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    except:
                        error_msg = f'HTTP {response.status_code}'
                self.log_test("Get Specific Bio Site", False, f"Specific bio site retrieval failed - {error_msg}")
        
        # Test 5: Update bio site (if created successfully)
        if bio_site_id:
            print(f"\n--- Test 5: Update Bio Site (ID: {bio_site_id}) ---")
            update_data = {
                "title": "Updated Creative Portfolio Bio",
                "description": "Updated description for the creative portfolio bio site",
                "theme_config": {
                    "primary_color": "#EF4444",
                    "secondary_color": "#F59E0B",
                    "font_family": "Poppins",
                    "layout": "sidebar"
                }
            }
            
            response = self.make_request('PUT', f'/bio-sites/{bio_site_id}', update_data)
            if response and response.status_code == 200:
                self.log_test("Update Bio Site", True, "Bio site update successful")
            else:
                error_msg = "Unknown error"
                if response:
                    try:
                        error_data = response.json()
                        error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    except:
                        error_msg = f'HTTP {response.status_code}'
                self.log_test("Update Bio Site", False, f"Bio site update failed - {error_msg}")
        
        # Test 6: Get bio site analytics (if created successfully)
        if bio_site_id:
            print(f"\n--- Test 6: Get Bio Site Analytics (ID: {bio_site_id}) ---")
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/analytics')
            if response and response.status_code == 200:
                data = response.json()
                self.log_test("Get Bio Site Analytics", True, "Bio site analytics retrieval successful")
            else:
                error_msg = "Unknown error"
                if response:
                    try:
                        error_data = response.json()
                        error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    except:
                        error_msg = f'HTTP {response.status_code}'
                self.log_test("Get Bio Site Analytics", False, f"Analytics retrieval failed - {error_msg}")
        
        # Test 7: Create bio site links (if bio site created successfully)
        if bio_site_id:
            print(f"\n--- Test 7: Create Bio Site Links (ID: {bio_site_id}) ---")
            link_data = {
                "title": "Portfolio Website",
                "url": "https://myportfolio.example.com",
                "description": "Check out my complete portfolio",
                "icon": "globe",
                "is_active": True,
                "order": 1
            }
            
            response = self.make_request('POST', f'/bio-sites/{bio_site_id}/links', link_data)
            link_id = None
            if response and response.status_code in [200, 201]:
                data = response.json()
                self.log_test("Create Bio Site Link", True, "Bio site link creation successful")
                link_id = data.get('id') or data.get('data', {}).get('id')
            else:
                error_msg = "Unknown error"
                if response:
                    try:
                        error_data = response.json()
                        error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    except:
                        error_msg = f'HTTP {response.status_code}'
                self.log_test("Create Bio Site Link", False, f"Link creation failed - {error_msg}")
        
        # Test 8: Get bio site links (if bio site created successfully)
        if bio_site_id:
            print(f"\n--- Test 8: Get Bio Site Links (ID: {bio_site_id}) ---")
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/links')
            if response and response.status_code == 200:
                data = response.json()
                links = data.get('data', [])
                self.log_test("Get Bio Site Links", True, f"Bio site links retrieval successful - Found {len(links)} links")
            else:
                error_msg = "Unknown error"
                if response:
                    try:
                        error_data = response.json()
                        error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    except:
                        error_msg = f'HTTP {response.status_code}'
                self.log_test("Get Bio Site Links", False, f"Links retrieval failed - {error_msg}")
        
        # Test 9: Delete bio site (cleanup - if created successfully)
        if bio_site_id:
            print(f"\n--- Test 9: Delete Bio Site (ID: {bio_site_id}) ---")
            response = self.make_request('DELETE', f'/bio-sites/{bio_site_id}')
            if response and response.status_code == 200:
                self.log_test("Delete Bio Site", True, "Bio site deletion successful")
            else:
                error_msg = "Unknown error"
                if response:
                    try:
                        error_data = response.json()
                        error_msg = error_data.get('message', f'HTTP {response.status_code}')
                    except:
                        error_msg = f'HTTP {response.status_code}'
                self.log_test("Delete Bio Site", False, f"Bio site deletion failed - {error_msg}")
    
    def run_bio_sites_tests(self):
        """Run comprehensive bio sites tests"""
        print("🚀 Starting Bio Sites Testing for Mewayz Creator Economy Platform")
        print("Testing Bio Sites & Link-in-Bio functionality")
        print("=" * 80)
        
        # Set up authentication first
        if not self.test_authentication_setup():
            print("❌ Authentication setup failed. Cannot proceed with bio sites testing.")
            return
        
        # Test bio sites functionality
        self.test_bio_sites_comprehensive()
        
        # Print summary
        print("\n" + "=" * 80)
        print("📊 BIO SITES TEST SUMMARY")
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
        
        # Determine overall status
        critical_tests = ["Get Bio Sites", "Get Bio Site Themes", "Create Bio Site"]
        critical_failures = [test for test in critical_tests if test in self.test_results and not self.test_results[test]['success']]
        
        if len(critical_failures) == 0:
            print(f"\n🎉 BIO SITES SYSTEM STATUS: ✅ WORKING")
            print("All critical bio sites functionality is operational!")
        elif len(critical_failures) <= 1:
            print(f"\n⚠️ BIO SITES SYSTEM STATUS: 🟡 PARTIALLY WORKING")
            print(f"Minor issues found in: {', '.join(critical_failures)}")
        else:
            print(f"\n❌ BIO SITES SYSTEM STATUS: ❌ NOT WORKING")
            print(f"Critical issues found in: {', '.join(critical_failures)}")
        
        print("=" * 80)
        
        return success_rate >= 70  # Consider working if 70% or more tests pass

if __name__ == "__main__":
    tester = BioSitesAPITester()
    success = tester.run_bio_sites_tests()
    sys.exit(0 if success else 1)