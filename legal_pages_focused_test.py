#!/usr/bin/env python3
"""
Focused Legal Pages System Testing for Mewayz Platform
Testing the critical Legal Pages System that needs retesting
"""

import requests
import json
import sys
import time
from datetime import datetime

class LegalPagesAPITester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.auth_token = "5|rpvwxCPPru4PP6xZLhyN7cIkpxfNfVoKubcxQILka00cb0e4"
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
        
    def make_request(self, method, endpoint, data=None, headers=None, auth_required=True, use_api=True):
        """Make HTTP request with proper headers"""
        time.sleep(0.1)  # Rate limiting
        
        if use_api:
            url = f"{self.api_url}{endpoint}"
        else:
            url = f"{self.base_url}{endpoint}"
        
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if headers:
            default_headers.update(headers)
            
        if auth_required and self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, params=data, timeout=10)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=10)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 10 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None

    def test_legal_pages_system(self):
        """Test Legal Pages System functionality - CRITICAL FOCUS"""
        print("\n=== FOCUSED TESTING: Legal Pages System ===")
        print("Testing all 5 legal document endpoints that were previously failing...")
        
        # Test all 5 legal document endpoints that were failing
        legal_pages = [
            'terms-of-service',
            'privacy-policy', 
            'cookie-policy',
            'refund-policy',
            'accessibility'
        ]
        
        working_pages = 0
        total_pages = len(legal_pages)
        
        for page in legal_pages:
            print(f"\n--- Testing {page} endpoint ---")
            # Legal pages are web routes, not API routes
            response = self.make_request('GET', f'/{page}', auth_required=False, use_api=False)
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", True, 
                                f"{page} page accessible and returns valid JSON content")
                    working_pages += 1
                    print(f"✅ SUCCESS: {page} returns {response.status_code} with content")
                except json.JSONDecodeError:
                    # Check if it returns HTML content instead
                    if 'text/html' in response.headers.get('content-type', ''):
                        self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", True, 
                                    f"{page} page accessible and returns HTML content")
                        working_pages += 1
                        print(f"✅ SUCCESS: {page} returns {response.status_code} with HTML content")
                    else:
                        self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", False, 
                                    f"{page} page returns invalid content format")
                        print(f"❌ FAIL: {page} returns invalid content format")
            elif response and response.status_code == 404:
                self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", False, 
                            f"{page} page returns 404 'API endpoint not found' - route missing")
                print(f"❌ FAIL: {page} returns 404 - API endpoint not found")
            else:
                self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", False, 
                            f"{page} page failed - Status: {response.status_code if response else 'No response'}")
                print(f"❌ FAIL: {page} - Status: {response.status_code if response else 'No response'}")
        
        # Calculate success rate
        success_rate = (working_pages / total_pages) * 100
        print(f"\n📊 LEGAL PAGES SUCCESS RATE: {success_rate:.1f}% ({working_pages}/{total_pages})")
        
        # Test legal API endpoints if we have authentication
        if self.auth_token:
            print("\n--- Testing Legal API Endpoints ---")
            
            # Test cookie consent endpoint
            consent_data = {
                "necessary": True,
                "analytics": True,
                "marketing": False,
                "preferences": True
            }
            response = self.make_request('POST', '/legal/cookie-consent', consent_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Cookie Consent API", True, "Cookie consent saved successfully")
                print("✅ Cookie Consent API working")
            else:
                self.log_test("Cookie Consent API", False, f"Cookie consent failed - Status: {response.status_code if response else 'No response'}")
                print(f"❌ Cookie Consent API failed - Status: {response.status_code if response else 'No response'}")
            
            # Test data export endpoint
            response = self.make_request('POST', '/legal/data-export')
            if response and response.status_code in [200, 201]:
                self.log_test("Data Export API", True, "Data export request successful")
                print("✅ Data Export API working")
            else:
                self.log_test("Data Export API", False, f"Data export failed - Status: {response.status_code if response else 'No response'}")
                print(f"❌ Data Export API failed - Status: {response.status_code if response else 'No response'}")
            
            # Test data deletion endpoint
            deletion_data = {
                "reason": "No longer need the service",
                "confirmation": True
            }
            response = self.make_request('POST', '/legal/data-deletion', deletion_data)
            if response and response.status_code in [200, 201]:
                self.log_test("Data Deletion API", True, "Data deletion request successful")
                print("✅ Data Deletion API working")
            else:
                self.log_test("Data Deletion API", False, f"Data deletion failed - Status: {response.status_code if response else 'No response'}")
                print(f"❌ Data Deletion API failed - Status: {response.status_code if response else 'No response'}")

    def test_health_check_verification(self):
        """Verify the health check is working as mentioned in review request"""
        print("\n=== VERIFICATION: Health Check Status ===")
        
        response = self.make_request('GET', '/health', auth_required=False, use_api=True)
        if response and response.status_code == 200:
            try:
                data = response.json()
                status = data.get('status', 'unknown')
                self.log_test("Health Check Verification", True, f"API health check working - Status: {status}")
                print(f"✅ VERIFIED: Health check returns '{status}' status")
                return True
            except json.JSONDecodeError:
                self.log_test("Health Check Verification", False, "Health check returns invalid JSON")
                print("❌ Health check returns invalid JSON")
                return False
        else:
            self.log_test("Health Check Verification", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
            print(f"❌ Health check failed - Status: {response.status_code if response else 'No response'}")
            return False

    def test_api_syntax_fix_verification(self):
        """Verify the API syntax fix mentioned in review request"""
        print("\n=== VERIFICATION: API Syntax Fix ===")
        
        # Test a basic API endpoint to verify syntax is fixed
        response = self.make_request('GET', '/test', auth_required=False, use_api=True)
        if response and response.status_code == 200:
            try:
                data = response.json()
                message = data.get('message', 'No message')
                self.log_test("API Syntax Fix Verification", True, f"API syntax fix successful - Test endpoint working")
                print(f"✅ VERIFIED: API syntax fix successful - Test endpoint returns: {message}")
                return True
            except json.JSONDecodeError:
                self.log_test("API Syntax Fix Verification", False, "API test endpoint returns invalid JSON")
                print("❌ API test endpoint returns invalid JSON")
                return False
        else:
            self.log_test("API Syntax Fix Verification", False, f"API test endpoint failed - Status: {response.status_code if response else 'No response'}")
            print(f"❌ API test endpoint failed - Status: {response.status_code if response else 'No response'}")
            return False

    def run_focused_legal_test(self):
        """Run focused test on Legal Pages System"""
        print("🎯 FOCUSED LEGAL PAGES SYSTEM TESTING")
        print("=" * 60)
        print(f"Testing against: {self.base_url}")
        print(f"API Endpoint: {self.api_url}")
        print(f"Focus: Legal Pages System (needs_retesting: true)")
        print("=" * 60)
        
        # Verify the fixes mentioned in review request
        health_ok = self.test_health_check_verification()
        syntax_ok = self.test_api_syntax_fix_verification()
        
        if not health_ok or not syntax_ok:
            print("\n⚠️  WARNING: Basic API functionality issues detected")
            print("Legal pages testing may be affected by underlying API problems")
        
        # Main focus: Test Legal Pages System
        self.test_legal_pages_system()
        
        # Generate focused report
        self.generate_focused_report()

    def generate_focused_report(self):
        """Generate focused report on Legal Pages System"""
        print("\n" + "=" * 60)
        print("🎯 FOCUSED LEGAL PAGES SYSTEM REPORT")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"📊 LEGAL PAGES TESTING RESULTS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        print()
        
        # Categorize legal pages results
        legal_page_tests = [name for name in self.test_results.keys() if 'Legal Page' in name]
        legal_api_tests = [name for name in self.test_results.keys() if any(api in name for api in ['Cookie Consent', 'Data Export', 'Data Deletion'])]
        
        if legal_page_tests:
            legal_passed = sum(1 for name in legal_page_tests if self.test_results[name]['success'])
            legal_total = len(legal_page_tests)
            legal_rate = (legal_passed / legal_total * 100) if legal_total > 0 else 0
            
            print(f"📋 LEGAL DOCUMENT PAGES:")
            print(f"   Success Rate: {legal_rate:.1f}% ({legal_passed}/{legal_total})")
            
            failed_pages = [name for name in legal_page_tests if not self.test_results[name]['success']]
            if failed_pages:
                print(f"   ❌ Failed Pages: {', '.join([name.replace('Legal Page - ', '') for name in failed_pages])}")
            else:
                print(f"   ✅ All legal document pages working!")
            print()
        
        if legal_api_tests:
            api_passed = sum(1 for name in legal_api_tests if self.test_results[name]['success'])
            api_total = len(legal_api_tests)
            api_rate = (api_passed / api_total * 100) if api_total > 0 else 0
            
            print(f"📋 LEGAL API ENDPOINTS:")
            print(f"   Success Rate: {api_rate:.1f}% ({api_passed}/{api_total})")
            
            failed_apis = [name for name in legal_api_tests if not self.test_results[name]['success']]
            if failed_apis:
                print(f"   ❌ Failed APIs: {', '.join(failed_apis)}")
            else:
                print(f"   ✅ All legal API endpoints working!")
            print()
        
        # Assessment specific to Legal Pages System
        print("🏆 LEGAL PAGES SYSTEM ASSESSMENT:")
        if success_rate >= 90:
            print("   Status: ✅ LEGAL SYSTEM WORKING")
            print("   Assessment: Legal pages system is fully functional and production-ready")
        elif success_rate >= 70:
            print("   Status: ⚠️  MOSTLY WORKING")
            print("   Assessment: Legal pages system mostly functional with minor issues")
        elif success_rate >= 50:
            print("   Status: 🔧 PARTIAL FUNCTIONALITY")
            print("   Assessment: Legal pages system has significant issues requiring fixes")
        else:
            print("   Status: ❌ SYSTEM FAILING")
            print("   Assessment: Legal pages system is not working and requires immediate attention")
        
        print("\n" + "=" * 60)
        print("🎯 FOCUSED LEGAL PAGES TESTING COMPLETE")
        print("=" * 60)

if __name__ == "__main__":
    tester = LegalPagesAPITester()
    tester.run_focused_legal_test()