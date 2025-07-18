#!/usr/bin/env python3
"""
Comprehensive Backend Testing for Mewayz Platform - Post Syntax Fix
Testing all backend systems with correct endpoint configurations
"""

import requests
import json
import sys
import time
from datetime import datetime

class MewayzComprehensiveTester:
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

    def test_core_infrastructure(self):
        """Test core infrastructure components"""
        print("\n=== Testing Core Infrastructure ===")
        
        # Health check
        response = self.make_request('GET', '/health', auth_required=False, use_api=True)
        if response and response.status_code == 200:
            self.log_test("API Health Check", True, "Health check working")
        else:
            self.log_test("API Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test endpoint
        response = self.make_request('GET', '/test', auth_required=False, use_api=True)
        if response and response.status_code == 200:
            self.log_test("API Test Endpoint", True, "Test endpoint working")
        else:
            self.log_test("API Test Endpoint", False, f"Test endpoint failed - Status: {response.status_code if response else 'No response'}")

    def test_authentication_system(self):
        """Test authentication system"""
        print("\n=== Testing Authentication System ===")
        
        # Test custom auth middleware
        response = self.make_request('GET', '/test-custom-auth', use_api=True)
        if response and response.status_code == 200:
            self.log_test("Custom Auth Middleware", True, "Custom auth working")
        else:
            self.log_test("Custom Auth Middleware", False, f"Custom auth failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user profile
        response = self.make_request('GET', '/auth/me', use_api=True)
        if response and response.status_code == 200:
            self.log_test("User Profile", True, "Profile retrieval working")
        else:
            self.log_test("User Profile", False, f"Profile retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_legal_pages_system(self):
        """Test Legal Pages System - CORRECTED APPROACH"""
        print("\n=== Testing Legal Pages System ===")
        
        legal_pages = [
            'terms-of-service',
            'privacy-policy', 
            'cookie-policy',
            'refund-policy',
            'accessibility'
        ]
        
        working_pages = 0
        for page in legal_pages:
            # Legal pages are web routes, not API routes
            response = self.make_request('GET', f'/{page}', auth_required=False, use_api=False)
            
            if response and response.status_code == 200:
                self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", True, f"{page} page accessible")
                working_pages += 1
            else:
                self.log_test(f"Legal Page - {page.replace('-', ' ').title()}", False, f"{page} page failed - Status: {response.status_code if response else 'No response'}")
        
        print(f"Legal Pages Success Rate: {(working_pages/len(legal_pages)*100):.1f}% ({working_pages}/{len(legal_pages)})")

    def test_database_tables(self):
        """Test database table accessibility"""
        print("\n=== Testing Database Tables ===")
        
        # Test bio_sites table
        response = self.make_request('GET', '/bio-sites/', use_api=True)
        if response and response.status_code == 200:
            self.log_test("Bio Sites Table", True, "bio_sites table accessible")
        else:
            self.log_test("Bio Sites Table", False, f"bio_sites table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test escrow_transactions table
        response = self.make_request('GET', '/escrow/', use_api=True)
        if response and response.status_code == 200:
            self.log_test("Escrow Transactions Table", True, "escrow_transactions table accessible")
        else:
            self.log_test("Escrow Transactions Table", False, f"escrow_transactions table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test workspaces table
        response = self.make_request('GET', '/workspaces', use_api=True)
        if response and response.status_code == 200:
            self.log_test("Workspaces Table", True, "workspaces table accessible")
        else:
            self.log_test("Workspaces Table", False, f"workspaces table failed - Status: {response.status_code if response else 'No response'}")

    def test_admin_dashboard(self):
        """Test admin dashboard system"""
        print("\n=== Testing Admin Dashboard System ===")
        
        # Test admin dashboard access (should return 403 for non-admin users)
        response = self.make_request('GET', '/admin/dashboard', use_api=True)
        if response and response.status_code == 403:
            self.log_test("Admin Dashboard Security", True, "Admin dashboard properly secured - returns 403 for non-admin users")
        elif response and response.status_code == 200:
            self.log_test("Admin Dashboard Access", True, "Admin dashboard accessible (user has admin privileges)")
        else:
            self.log_test("Admin Dashboard", False, f"Admin dashboard failed - Status: {response.status_code if response else 'No response'}")

    def test_payment_processing(self):
        """Test payment processing"""
        print("\n=== Testing Payment Processing ===")
        
        # Test payment packages (public endpoint)
        response = self.make_request('GET', '/payments/packages', auth_required=False, use_api=True)
        if response and response.status_code == 200:
            self.log_test("Payment Packages", True, "Payment packages accessible")
        else:
            self.log_test("Payment Packages", False, f"Payment packages failed - Status: {response.status_code if response else 'No response'}")

    def test_installer_system(self):
        """Test installer system"""
        print("\n=== Testing Installer System ===")
        
        # Test installation status
        response = self.make_request('GET', '/install/status', auth_required=False, use_api=True)
        if response and response.status_code == 200:
            self.log_test("Installer Status", True, "Installer status accessible")
        else:
            self.log_test("Installer Status", False, f"Installer status failed - Status: {response.status_code if response else 'No response'}")

    def run_comprehensive_test(self):
        """Run comprehensive backend test"""
        print("🚀 COMPREHENSIVE BACKEND TESTING - POST SYNTAX FIX")
        print("=" * 70)
        print(f"Testing against: {self.base_url}")
        print(f"API Endpoint: {self.api_url}")
        print("=" * 70)
        
        # Core tests
        self.test_core_infrastructure()
        self.test_authentication_system()
        self.test_legal_pages_system()
        self.test_database_tables()
        self.test_admin_dashboard()
        self.test_payment_processing()
        self.test_installer_system()
        
        # Generate report
        self.generate_comprehensive_report()

    def generate_comprehensive_report(self):
        """Generate comprehensive report"""
        print("\n" + "=" * 70)
        print("🎯 COMPREHENSIVE BACKEND TESTING REPORT")
        print("=" * 70)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"📊 OVERALL STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print(f"   Success Rate: {success_rate:.1f}%")
        print()
        
        # Show passed tests
        passed_test_names = [name for name, result in self.test_results.items() if result['success']]
        if passed_test_names:
            print("✅ WORKING SYSTEMS:")
            for test_name in passed_test_names:
                print(f"   • {test_name}")
            print()
        
        # Show failed tests
        failed_test_names = [name for name, result in self.test_results.items() if not result['success']]
        if failed_test_names:
            print("❌ FAILING SYSTEMS:")
            for test_name in failed_test_names:
                print(f"   • {test_name}")
            print()
        
        # Assessment
        print("🏆 BACKEND SYSTEM ASSESSMENT:")
        if success_rate >= 80:
            print("   Status: ✅ EXCELLENT - Backend systems working well")
        elif success_rate >= 60:
            print("   Status: ✅ GOOD - Most backend systems functional")
        elif success_rate >= 40:
            print("   Status: ⚠️  MODERATE - Some backend systems working")
        else:
            print("   Status: ❌ NEEDS WORK - Many backend systems failing")
        
        print("\n" + "=" * 70)
        print("🎯 COMPREHENSIVE BACKEND TESTING COMPLETE")
        print("=" * 70)

if __name__ == "__main__":
    tester = MewayzComprehensiveTester()
    tester.run_comprehensive_test()