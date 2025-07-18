#!/usr/bin/env python3
"""
Focused Backend Testing for Current Focus Tasks
Testing only the tasks in current_focus from test_result.md
"""

import requests
import json
import sys
import time
from datetime import datetime

class FocusedMewayzAPITester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token from the test_result.md
        self.auth_token = "3|yHHRGVcNjzxdu8szdT1LRua2Dy2GPnff0iQyCSm7cf941e64"
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
        """Make HTTP request with proper headers and longer delays"""
        # Add longer delay to avoid rate limiting
        time.sleep(2)
        
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
    
    def test_enhanced_controllers(self):
        """Test Enhanced Controllers Testing - current_focus task"""
        print("\n=== Testing Enhanced Controllers (Current Focus Task) ===")
        
        if not self.auth_token:
            self.log_test("Enhanced Controllers", False, "Cannot test - no authentication token")
            return
        
        # Test BioSiteController
        print("Testing BioSiteController...")
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            self.log_test("BioSiteController - GET", True, "Bio sites retrieval successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("BioSiteController - GET", False, f"Bio sites retrieval failed - Status: {status_code}, Response: {response_text}")
        
        # Test BioSiteController POST
        bio_site_data = {
            "name": "Test Enhanced Bio",
            "title": "Test Enhanced Bio",
            "slug": f"test-enhanced-{int(time.time())}",
            "description": "Testing enhanced controllers",
            "theme": "modern"
        }
        
        response = self.make_request('POST', '/bio-sites/', bio_site_data)
        if response and response.status_code in [200, 201]:
            self.log_test("BioSiteController - POST", True, "Bio site creation successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("BioSiteController - POST", False, f"Bio site creation failed - Status: {status_code}, Response: {response_text}")
        
        # Test EmailMarketingController
        print("Testing EmailMarketingController...")
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            self.log_test("EmailMarketingController - GET", True, "Email campaigns retrieval successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("EmailMarketingController - GET", False, f"Email campaigns retrieval failed - Status: {status_code}, Response: {response_text}")
    
    def test_admin_dashboard(self):
        """Test Ultra-Comprehensive Admin Dashboard System - current_focus task"""
        print("\n=== Testing Admin Dashboard System (Current Focus Task) ===")
        
        if not self.auth_token:
            self.log_test("Admin Dashboard", False, "Cannot test - no authentication token")
            return
        
        # Test admin dashboard
        response = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 200:
            self.log_test("Admin Dashboard - GET", True, "Admin dashboard retrieval successful")
        elif response and response.status_code == 403:
            self.log_test("Admin Dashboard - GET", True, "Admin dashboard properly secured (403 Forbidden - expected for non-admin users)")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Admin Dashboard - GET", False, f"Admin dashboard failed - Status: {status_code}, Response: {response_text}")
    
    def test_escrow_system(self):
        """Test Escrow & Transaction Security - current_focus task"""
        print("\n=== Testing Escrow System (Current Focus Task) ===")
        
        if not self.auth_token:
            self.log_test("Escrow System", False, "Cannot test - no authentication token")
            return
        
        # Test get escrow transactions
        response = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("Escrow - GET Transactions", True, "Escrow transactions retrieval successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Escrow - GET Transactions", False, f"Escrow transactions retrieval failed - Status: {status_code}, Response: {response_text}")
        
        # Test escrow statistics
        response = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            self.log_test("Escrow - GET Statistics", True, "Escrow statistics retrieval successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Escrow - GET Statistics", False, f"Escrow statistics retrieval failed - Status: {status_code}, Response: {response_text}")
        
        # Test create escrow transaction
        escrow_data = {
            "buyer_id": "test-buyer-id",
            "seller_id": "test-seller-id",
            "amount": 100.00,
            "currency": "USD",
            "description": "Test enhanced escrow transaction",
            "terms": "Standard escrow terms for testing"
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Escrow - POST Transaction", True, "Escrow transaction creation successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Escrow - POST Transaction", False, f"Escrow transaction creation failed - Status: {status_code}, Response: {response_text}")
    
    def test_advanced_booking(self):
        """Test Advanced Booking System - current_focus task"""
        print("\n=== Testing Advanced Booking System (Current Focus Task) ===")
        
        if not self.auth_token:
            self.log_test("Advanced Booking", False, "Cannot test - no authentication token")
            return
        
        # Test get booking services
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("Booking - GET Services", True, "Booking services retrieval successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Booking - GET Services", False, f"Booking services retrieval failed - Status: {status_code}, Response: {response_text}")
        
        # Test create booking service
        service_data = {
            "name": "Enhanced Business Consultation",
            "description": "Advanced business strategy consultation",
            "duration": 90,
            "price": 200.00,
            "currency": "USD",
            "category": "consultation"
        }
        
        response = self.make_request('POST', '/booking/services', service_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Booking - POST Service", True, "Booking service creation successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Booking - POST Service", False, f"Booking service creation failed - Status: {status_code}, Response: {response_text}")
        
        # Test get appointments
        response = self.make_request('GET', '/booking/appointments')
        if response and response.status_code == 200:
            self.log_test("Booking - GET Appointments", True, "Appointments retrieval successful")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("Booking - GET Appointments", False, f"Appointments retrieval failed - Status: {status_code}, Response: {response_text}")
    
    def test_basic_health_check(self):
        """Test basic API health to ensure server is responding"""
        print("\n=== Testing Basic API Health ===")
        
        # Test basic health endpoint
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Health Check", True, f"API is healthy - Status: {data.get('status', 'unknown')}")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("API Health Check", False, f"Health check failed - Status: {status_code}, Response: {response_text}")
        
        # Test basic test endpoint
        response = self.make_request('GET', '/test', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Test Endpoint", True, f"Test endpoint working - Message: {data.get('message', 'No message')}")
        else:
            status_code = response.status_code if response else "No response"
            response_text = response.text[:200] if response else "No response"
            self.log_test("API Test Endpoint", False, f"Test endpoint failed - Status: {status_code}, Response: {response_text}")
    
    def run_focused_tests(self):
        """Run focused tests for current_focus tasks only"""
        print("🎯 FOCUSED BACKEND TESTING - CURRENT FOCUS TASKS ONLY")
        print("=" * 80)
        print("Testing only the tasks marked in current_focus:")
        print("- Enhanced Controllers Testing")
        print("- Ultra-Comprehensive Admin Dashboard System")
        print("- Escrow & Transaction Security")
        print("- Advanced Booking System")
        print("=" * 80)
        
        # Test basic health first
        self.test_basic_health_check()
        
        # Test current_focus tasks
        self.test_enhanced_controllers()
        self.test_admin_dashboard()
        self.test_escrow_system()
        self.test_advanced_booking()
        
        # Print summary
        print("\n" + "=" * 80)
        print("📊 FOCUSED TEST SUMMARY")
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
            print("\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("=" * 80)
        
        return self.test_results

if __name__ == "__main__":
    tester = FocusedMewayzAPITester()
    results = tester.run_focused_tests()