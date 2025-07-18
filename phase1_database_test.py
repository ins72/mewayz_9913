#!/usr/bin/env python3
"""
Focused Testing for Phase 1 Database Tables Creation
Testing specific tables: bio_sites, escrow_transactions, booking_services, workspaces, email_campaigns
"""

import requests
import json
import sys
import time
from datetime import datetime

class Phase1DatabaseTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use the token from test_result.md
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
        """Make HTTP request with proper headers"""
        time.sleep(0.1)  # Rate limiting
        
        url = f"{self.api_url}{endpoint}"
        
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
    
    def test_bio_sites_table(self):
        """Test bio_sites table functionality"""
        print("\n=== Testing bio_sites Table ===")
        
        # Test GET /bio-sites/ (should work if table exists)
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            self.log_test("bio_sites Table - GET", True, "Bio sites table accessible and working")
            
            # Test POST /bio-sites/ (create new bio site)
            bio_site_data = {
                "name": "Test Bio Site",
                "title": "My Test Bio Site",
                "description": "Testing bio site creation",
                "theme": "default"
            }
            
            response = self.make_request('POST', '/bio-sites/', bio_site_data)
            if response and response.status_code in [200, 201]:
                self.log_test("bio_sites Table - POST", True, "Bio site creation successful")
            else:
                self.log_test("bio_sites Table - POST", False, f"Bio site creation failed - Status: {response.status_code if response else 'No response'}")
        else:
            self.log_test("bio_sites Table - GET", False, f"Bio sites table access failed - Status: {response.status_code if response else 'No response'}")
    
    def test_escrow_transactions_table(self):
        """Test escrow_transactions table functionality"""
        print("\n=== Testing escrow_transactions Table ===")
        
        # Test GET /escrow/ (should work if table exists)
        response = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("escrow_transactions Table - GET", True, "Escrow transactions table accessible and working")
            
            # Test GET /escrow/statistics/overview
            response = self.make_request('GET', '/escrow/statistics/overview')
            if response and response.status_code == 200:
                self.log_test("escrow_transactions Table - Statistics", True, "Escrow statistics working")
            else:
                self.log_test("escrow_transactions Table - Statistics", False, f"Escrow statistics failed - Status: {response.status_code if response else 'No response'}")
        else:
            self.log_test("escrow_transactions Table - GET", False, f"Escrow transactions table access failed - Status: {response.status_code if response else 'No response'}")
    
    def test_booking_services_table(self):
        """Test booking_services table functionality"""
        print("\n=== Testing booking_services Table ===")
        
        # Test GET /booking/services (should work if table exists)
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("booking_services Table - GET", True, "Booking services table accessible and working")
            
            # Test POST /booking/services (create new booking service)
            booking_service_data = {
                "name": "Test Consultation",
                "description": "Testing booking service creation",
                "price": 100.00,
                "duration": 60
            }
            
            response = self.make_request('POST', '/booking/services', booking_service_data)
            if response and response.status_code in [200, 201]:
                self.log_test("booking_services Table - POST", True, "Booking service creation successful")
            else:
                self.log_test("booking_services Table - POST", False, f"Booking service creation failed - Status: {response.status_code if response else 'No response'}")
        else:
            self.log_test("booking_services Table - GET", False, f"Booking services table access failed - Status: {response.status_code if response else 'No response'}")
    
    def test_workspaces_table(self):
        """Test workspaces table functionality (UUID)"""
        print("\n=== Testing workspaces Table (UUID) ===")
        
        # Test GET /workspaces (should work if table exists)
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            self.log_test("workspaces Table - GET", True, "Workspaces table accessible and working")
        else:
            self.log_test("workspaces Table - GET", False, f"Workspaces table access failed - Status: {response.status_code if response else 'No response'}")
    
    def test_email_campaigns_table(self):
        """Test email_campaigns table functionality"""
        print("\n=== Testing email_campaigns Table ===")
        
        # Test GET /email-marketing/campaigns (should work if table exists)
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            self.log_test("email_campaigns Table - GET", True, "Email campaigns table accessible and working")
        else:
            self.log_test("email_campaigns Table - GET", False, f"Email campaigns table access failed - Status: {response.status_code if response else 'No response'}")
    
    def run_all_tests(self):
        """Run all Phase 1 database table tests"""
        print("🚀 Starting Phase 1 Database Tables Creation Testing")
        print("=" * 60)
        print("Testing specific tables: bio_sites, escrow_transactions, booking_services, workspaces, email_campaigns")
        print("=" * 60)
        
        # Test each table mentioned in the Phase 1 Database Tables Creation task
        self.test_bio_sites_table()
        self.test_escrow_transactions_table()
        self.test_booking_services_table()
        self.test_workspaces_table()
        self.test_email_campaigns_table()
        
        # Summary
        print("\n" + "=" * 60)
        print("📊 PHASE 1 DATABASE TABLES TEST SUMMARY")
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
            print("\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("\n" + "=" * 60)
        
        # Determine overall status
        if success_rate >= 80:
            print("🎉 PHASE 1 DATABASE TABLES CREATION: WORKING")
            return True
        elif success_rate >= 50:
            print("⚠️ PHASE 1 DATABASE TABLES CREATION: PARTIALLY WORKING")
            return False
        else:
            print("❌ PHASE 1 DATABASE TABLES CREATION: NOT WORKING")
            return False

if __name__ == "__main__":
    tester = Phase1DatabaseTester()
    success = tester.run_all_tests()
    sys.exit(0 if success else 1)