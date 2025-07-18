#!/usr/bin/env python3
"""
Focused Testing for Current Focus Tasks from test_result.md
Testing only the 4 tasks mentioned in current_focus
"""

import requests
import json
import time
from datetime import datetime

class FocusedTester:
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
        """Make HTTP request with proper headers"""
        # Add delay to avoid rate limiting
        time.sleep(2)  # Increased delay to avoid rate limiting
        
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
        """Test Enhanced Controllers (BioSiteController, EmailMarketingController)"""
        print("\n=== Testing Enhanced Controllers ===")
        
        # Test BioSiteController
        print("\n--- Testing BioSiteController ---")
        response = self.make_request('GET', '/bio-sites/')
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("BioSiteController - Get Bio Sites", True, f"Bio sites retrieved successfully. Count: {len(data.get('data', []))}")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("BioSiteController - Get Bio Sites", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("BioSiteController - Get Bio Sites", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("BioSiteController - Get Bio Sites", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("BioSiteController - Get Bio Sites", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("BioSiteController - Get Bio Sites", False, "No response received")
        
        # Test EmailMarketingController
        print("\n--- Testing EmailMarketingController ---")
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("EmailMarketingController - Get Campaigns", True, f"Email campaigns retrieved successfully. Count: {len(data.get('data', []))}")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("EmailMarketingController - Get Campaigns", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("EmailMarketingController - Get Campaigns", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("EmailMarketingController - Get Campaigns", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("EmailMarketingController - Get Campaigns", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("EmailMarketingController - Get Campaigns", False, "No response received")

    def test_admin_dashboard(self):
        """Test Ultra-Comprehensive Admin Dashboard System"""
        print("\n=== Testing Ultra-Comprehensive Admin Dashboard System ===")
        
        response = self.make_request('GET', '/admin/dashboard')
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Admin Dashboard", True, "Admin dashboard accessed successfully")
            elif response.status_code == 403:
                self.log_test("Admin Dashboard", False, "Access denied (403) - admin privileges required")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("Admin Dashboard", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("Admin Dashboard", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("Admin Dashboard", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Admin Dashboard", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Admin Dashboard", False, "No response received")

    def test_escrow_system(self):
        """Test Escrow & Transaction Security"""
        print("\n=== Testing Escrow & Transaction Security ===")
        
        # Test get escrow transactions
        response = self.make_request('GET', '/escrow/')
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Escrow System - Get Transactions", True, f"Escrow transactions retrieved successfully. Count: {len(data.get('data', []))}")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("Escrow System - Get Transactions", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("Escrow System - Get Transactions", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("Escrow System - Get Transactions", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Escrow System - Get Transactions", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Escrow System - Get Transactions", False, "No response received")
        
        # Test create escrow transaction
        escrow_data = {
            "buyer_id": "test-buyer-123",
            "seller_id": "test-seller-456", 
            "amount": 250.00,
            "currency": "USD",
            "description": "Digital marketing consultation service",
            "terms": "Payment held in escrow until service delivery confirmed"
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                self.log_test("Escrow System - Create Transaction", True, "Escrow transaction created successfully")
            elif response.status_code == 422:
                try:
                    error_data = response.json()
                    errors = error_data.get('errors', {})
                    self.log_test("Escrow System - Create Transaction", False, f"Validation error (422): {errors}")
                except:
                    self.log_test("Escrow System - Create Transaction", False, f"Validation error (422): {response.text[:200]}")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("Escrow System - Create Transaction", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("Escrow System - Create Transaction", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("Escrow System - Create Transaction", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Escrow System - Create Transaction", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Escrow System - Create Transaction", False, "No response received")

    def test_advanced_booking_system(self):
        """Test Advanced Booking System"""
        print("\n=== Testing Advanced Booking System ===")
        
        # Test get booking services
        response = self.make_request('GET', '/booking/services')
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Advanced Booking - Get Services", True, f"Booking services retrieved successfully. Count: {len(data.get('data', []))}")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("Advanced Booking - Get Services", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("Advanced Booking - Get Services", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("Advanced Booking - Get Services", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Advanced Booking - Get Services", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Advanced Booking - Get Services", False, "No response received")
        
        # Test create booking service
        service_data = {
            "name": "Strategic Business Consultation",
            "description": "Comprehensive business strategy and growth planning session",
            "duration": 90,
            "price": 200.00,
            "currency": "USD",
            "category": "business_consultation"
        }
        
        response = self.make_request('POST', '/booking/services', service_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                self.log_test("Advanced Booking - Create Service", True, "Booking service created successfully")
            elif response.status_code == 422:
                try:
                    error_data = response.json()
                    errors = error_data.get('errors', {})
                    self.log_test("Advanced Booking - Create Service", False, f"Validation error (422): {errors}")
                except:
                    self.log_test("Advanced Booking - Create Service", False, f"Validation error (422): {response.text[:200]}")
            elif response.status_code == 500:
                try:
                    error_data = response.json()
                    error_msg = error_data.get('message', 'Unknown server error')
                    self.log_test("Advanced Booking - Create Service", False, f"Server error (500): {error_msg}")
                except:
                    self.log_test("Advanced Booking - Create Service", False, f"Server error (500): {response.text[:200]}")
            elif response.status_code == 429:
                self.log_test("Advanced Booking - Create Service", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Advanced Booking - Create Service", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Advanced Booking - Create Service", False, "No response received")

    def test_core_api_functionality(self):
        """Test core API functionality like health checks, authentication"""
        print("\n=== Testing Core API Functionality ===")
        
        # Test health check (public endpoint)
        response = self.make_request('GET', '/health', auth_required=False)
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("Core API - Health Check", True, f"API health check successful. Status: {data.get('status', 'unknown')}")
            elif response.status_code == 429:
                self.log_test("Core API - Health Check", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Core API - Health Check", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Core API - Health Check", False, "No response received")
        
        # Test authentication with custom middleware
        response = self.make_request('GET', '/auth/me')
        if response:
            if response.status_code == 200:
                data = response.json()
                user_name = data.get('user', {}).get('name', 'Unknown')
                self.log_test("Core API - Authentication", True, f"Authentication successful. User: {user_name}")
            elif response.status_code == 401:
                self.log_test("Core API - Authentication", False, "Authentication failed (401) - invalid token")
            elif response.status_code == 429:
                self.log_test("Core API - Authentication", False, "Rate limited (429) - too many requests")
            else:
                self.log_test("Core API - Authentication", False, f"HTTP {response.status_code}: {response.text[:200]}")
        else:
            self.log_test("Core API - Authentication", False, "No response received")

    def run_focused_tests(self):
        """Run all focused tests for current_focus tasks"""
        print("🎯 FOCUSED TESTING FOR CURRENT_FOCUS TASKS")
        print("=" * 60)
        print("Testing the 4 tasks mentioned in current_focus:")
        print("1. Enhanced Controllers Testing")
        print("2. Ultra-Comprehensive Admin Dashboard System") 
        print("3. Escrow & Transaction Security")
        print("4. Advanced Booking System")
        print("=" * 60)
        
        # Test core functionality first
        self.test_core_api_functionality()
        
        # Test the 4 current_focus tasks
        self.test_enhanced_controllers()
        self.test_admin_dashboard()
        self.test_escrow_system()
        self.test_advanced_booking_system()
        
        # Print summary
        print("\n" + "=" * 60)
        print("📊 FOCUSED TEST SUMMARY")
        print("=" * 60)
        
        passed = sum(1 for result in self.test_results.values() if result['success'])
        total = len(self.test_results)
        success_rate = (passed / total * 100) if total > 0 else 0
        
        print(f"Total Tests: {total}")
        print(f"✅ Passed: {passed}")
        print(f"❌ Failed: {total - passed}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        print("\n🔍 DETAILED RESULTS:")
        for test_name, result in self.test_results.items():
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {test_name}: {result['message']}")
        
        return self.test_results

if __name__ == "__main__":
    tester = FocusedTester()
    results = tester.run_focused_tests()