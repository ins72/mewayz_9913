#!/usr/bin/env python3
"""
Focused Backend Testing for Review Request
Tests the specific endpoints mentioned in the review request
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional

class ReviewRequestTester:
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
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            
            return response, response_time
            
        except requests.exceptions.Timeout:
            return None, 30.0  # Return timeout duration
        except requests.exceptions.RequestException as e:
            print(f"Request error: {str(e)}")
            return None, 0.0
    
    def test_review_endpoints(self):
        """Test all endpoints mentioned in the review request"""
        print("🚀 Starting Review Request Backend Testing")
        print(f"Testing backend at: {self.api_url}")
        print("=" * 60)
        
        # 1. Test health check
        print("\n=== 1. Testing Health Check ===")
        response, response_time = self.make_request('GET', '/health')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Health Check", True, f"Status: {data.get('status', 'healthy')}", response_time)
        else:
            self.log_test("Health Check", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 2. Test admin login
        print("\n=== 2. Testing Admin Login ===")
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                self.log_test("Admin Login", True, f"Admin user: {data.get('user', {}).get('email')}", response_time)
            else:
                self.log_test("Admin Login", False, f"Login failed: {data.get('message')}", response_time)
        else:
            self.log_test("Admin Login", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        if not self.auth_token:
            print("❌ Cannot continue testing - no authentication token")
            return
        
        # 3. Test comprehensive AI services
        print("\n=== 3. Testing AI Services ===")
        response, response_time = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("AI Services", True, f"Services available: {len(data.get('services', []))}", response_time)
        else:
            self.log_test("AI Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 4. Test bio sites themes
        print("\n=== 4. Testing Bio Sites Themes ===")
        response, response_time = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Bio Sites Themes", True, f"Themes available: {len(data.get('themes', []))}", response_time)
        else:
            self.log_test("Bio Sites Themes", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 5. Test e-commerce dashboard
        print("\n=== 5. Testing E-commerce Dashboard ===")
        response, response_time = self.make_request('GET', '/ecommerce/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("E-commerce Dashboard", True, f"Dashboard data retrieved", response_time)
        else:
            self.log_test("E-commerce Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 6. Test advanced booking dashboard
        print("\n=== 6. Testing Advanced Booking Dashboard ===")
        response, response_time = self.make_request('GET', '/bookings/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Advanced Booking Dashboard", True, f"Dashboard data retrieved", response_time)
        else:
            self.log_test("Advanced Booking Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 7. Test comprehensive financial dashboard
        print("\n=== 7. Testing Financial Dashboard ===")
        response, response_time = self.make_request('GET', '/financial/dashboard/comprehensive')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Financial Dashboard Comprehensive", True, f"Dashboard data retrieved", response_time)
        else:
            self.log_test("Financial Dashboard Comprehensive", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 8. Test advanced business intelligence
        print("\n=== 8. Testing Advanced Business Intelligence ===")
        response, response_time = self.make_request('GET', '/analytics/business-intelligence/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Advanced Business Intelligence", True, f"BI data retrieved", response_time)
        else:
            self.log_test("Advanced Business Intelligence", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 9. Test escrow dashboard
        print("\n=== 9. Testing Escrow Dashboard ===")
        response, response_time = self.make_request('GET', '/escrow/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Escrow Dashboard", True, f"Dashboard data retrieved", response_time)
        else:
            self.log_test("Escrow Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # 10. Test advanced notifications
        print("\n=== 10. Testing Advanced Notifications ===")
        response, response_time = self.make_request('GET', '/notifications/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Advanced Notifications", True, f"Notifications retrieved", response_time)
        else:
            self.log_test("Advanced Notifications", False, f"Status: {response.status_code if response else 'timeout'}", response_time)
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 60)
        print("📊 REVIEW REQUEST TESTING SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  • {result['test']}: {result['details']}")
        
        print("\n" + "=" * 60)
        
        # Overall assessment
        if success_rate >= 80:
            print("🎉 EXCELLENT: Review request endpoints are highly functional!")
        elif success_rate >= 60:
            print("✅ GOOD: Most review request endpoints are functional.")
        elif success_rate >= 40:
            print("⚠️  MODERATE: Some review request endpoints need attention.")
        else:
            print("❌ CRITICAL: Review request endpoints have major issues.")

def main():
    """Main function to run the tests"""
    backend_url = "https://c1377653-96a4-4862-8647-9ed933db2920.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    
    # Initialize tester
    tester = ReviewRequestTester(backend_url)
    
    # Run review request tests
    tester.test_review_endpoints()

if __name__ == "__main__":
    main()