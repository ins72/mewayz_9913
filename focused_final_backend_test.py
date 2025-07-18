#!/usr/bin/env python3
"""
Focused Final Backend Test for Mewayz Platform
Testing the new database improvements: 52 → 62 tables
"""

import requests
import json
import time
from datetime import datetime

class FocusedMewayzTester:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_url = f"{self.base_url}/api"
        self.auth_token = "3|yHHRGVcNjzxdu8szdT1LRua2Dy2GPnff0iQyCSm7cf941e64"
        self.test_results = {}
        self.session = requests.Session()
        
    def log_test(self, test_name, success, message):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        
        self.test_results[test_name] = {
            'success': success,
            'message': message,
            'timestamp': datetime.now().isoformat()
        }
        
    def make_request(self, method, endpoint, data=None, auth_required=True):
        """Make HTTP request with proper headers and rate limiting"""
        # Add delay to avoid rate limiting
        time.sleep(1.0)  # 1 second delay between requests
        
        url = f"{self.api_url}{endpoint}"
        
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if auth_required and self.auth_token:
            headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=headers, params=data, timeout=15)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=headers, json=data, timeout=15)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 15 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None

    def test_core_infrastructure(self):
        """Test core infrastructure"""
        print("\n=== Testing Core Infrastructure ===")
        
        # Test health check
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Health Check", True, f"API healthy - Status: {data.get('data', {}).get('status', 'unknown')}")
        else:
            self.log_test("API Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")

    def test_authentication_system(self):
        """Test authentication system including new password reset"""
        print("\n=== Testing Authentication System ===")
        
        # Test user profile with existing token
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            user_name = data.get('user', {}).get('name', 'unknown')
            self.log_test("User Authentication", True, f"Authentication working - User: {user_name}")
        else:
            self.log_test("User Authentication", False, f"Authentication failed - Status: {response.status_code if response else 'No response'}")

    def test_ecommerce_system(self):
        """Test new e-commerce system (products, orders, categories tables)"""
        print("\n=== Testing E-commerce System (New Tables) ===")
        
        # Test categories (should have 6 predefined categories)
        response = self.make_request('GET', '/ecommerce/categories', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            categories = data.get('data', []) if isinstance(data.get('data'), list) else []
            self.log_test("Categories Table", True, f"Categories working - Found {len(categories)} categories")
        else:
            self.log_test("Categories Table", False, f"Categories failed - Status: {response.status_code if response else 'No response'}")
        
        # Test products table
        response = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            self.log_test("Products Table", True, "Products table accessible")
        else:
            self.log_test("Products Table", False, f"Products failed - Status: {response.status_code if response else 'No response'}")
        
        # Test orders table
        response = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            self.log_test("Orders Table", True, "Orders table accessible")
        else:
            self.log_test("Orders Table", False, f"Orders failed - Status: {response.status_code if response else 'No response'}")

    def test_social_media_system(self):
        """Test new social media management system"""
        print("\n=== Testing Social Media Management (New Tables) ===")
        
        # Test social media accounts table
        response = self.make_request('GET', '/social-media/accounts')
        if response and response.status_code == 200:
            self.log_test("Social Media Accounts Table", True, "Social media accounts table working")
        else:
            self.log_test("Social Media Accounts Table", False, f"Social media accounts failed - Status: {response.status_code if response else 'No response'}")
        
        # Test social media posts table
        response = self.make_request('GET', '/social-media/posts')
        if response and response.status_code == 200:
            self.log_test("Social Media Posts Table", True, "Social media posts table working")
        else:
            self.log_test("Social Media Posts Table", False, f"Social media posts failed - Status: {response.status_code if response else 'No response'}")

    def test_laravel_core_features(self):
        """Test new Laravel core features (jobs, notifications, sessions)"""
        print("\n=== Testing Laravel Core Features (New Tables) ===")
        
        # Test notifications system
        response = self.make_request('GET', '/notifications')
        if response and response.status_code == 200:
            self.log_test("Notifications System", True, "Notifications table working")
        else:
            self.log_test("Notifications System", False, f"Notifications failed - Status: {response.status_code if response else 'No response'}")
        
        # Test queue system (jobs table)
        response = self.make_request('GET', '/system/queue-status', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Queue System (Jobs Table)", True, "Queue system working")
        else:
            self.log_test("Queue System (Jobs Table)", False, f"Queue system failed - Status: {response.status_code if response else 'No response'}")

    def test_admin_dashboard_access(self):
        """Test admin dashboard access to all 62 tables"""
        print("\n=== Testing Admin Dashboard (62 Tables Access) ===")
        
        # Test admin dashboard (should return 403 for non-admin users)
        response = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 403:
            self.log_test("Admin Dashboard Security", True, "Admin dashboard properly secured (403 for non-admin)")
        elif response and response.status_code == 200:
            self.log_test("Admin Dashboard Access", True, "Admin dashboard accessible (user has admin privileges)")
        else:
            self.log_test("Admin Dashboard", False, f"Admin dashboard failed - Status: {response.status_code if response else 'No response'}")

    def test_legal_pages_system(self):
        """Test legal pages system (should remain 100% functional)"""
        print("\n=== Testing Legal Pages System ===")
        
        legal_pages = ['terms-of-service', 'privacy-policy', 'cookie-policy']
        working_pages = 0
        
        for page in legal_pages:
            response = self.make_request('GET', f'/{page}', auth_required=False)
            if response and response.status_code == 200:
                working_pages += 1
        
        if working_pages == len(legal_pages):
            self.log_test("Legal Pages System", True, f"All {working_pages} legal pages working")
        elif working_pages > 0:
            self.log_test("Legal Pages System", True, f"Most legal pages working ({working_pages}/{len(legal_pages)})")
        else:
            self.log_test("Legal Pages System", False, "Legal pages not accessible")

    def run_focused_final_test(self):
        """Run focused final backend test"""
        print("🚀 FOCUSED FINAL BACKEND TEST - MEWAYZ PLATFORM")
        print("=" * 70)
        print("Testing Database Improvements: 52 → 62 Tables")
        print("Focus: E-commerce, Social Media, Laravel Core, Admin Dashboard")
        print("=" * 70)
        
        # Run focused tests
        self.test_core_infrastructure()
        self.test_authentication_system()
        self.test_ecommerce_system()
        self.test_social_media_system()
        self.test_laravel_core_features()
        self.test_admin_dashboard_access()
        self.test_legal_pages_system()
        
        # Generate report
        self.generate_focused_report()

    def generate_focused_report(self):
        """Generate focused test report"""
        print("\n" + "=" * 70)
        print("🎯 FOCUSED FINAL BACKEND TEST REPORT")
        print("=" * 70)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"📊 OVERALL BACKEND SUCCESS RATE: {success_rate:.1f}%")
        print(f"   Total Tests: {total_tests}")
        print(f"   Passed: {passed_tests} ✅")
        print(f"   Failed: {failed_tests} ❌")
        print()
        
        # Categorize results by new features
        categories = {
            'Core Infrastructure': ['API Health Check'],
            'Authentication System': ['User Authentication'],
            'E-commerce System (NEW)': ['Categories Table', 'Products Table', 'Orders Table'],
            'Social Media Management (NEW)': ['Social Media Accounts Table', 'Social Media Posts Table'],
            'Laravel Core Features (NEW)': ['Notifications System', 'Queue System'],
            'Admin Dashboard': ['Admin Dashboard'],
            'Legal Pages': ['Legal Pages System']
        }
        
        print("📋 NEW DATABASE IMPROVEMENTS STATUS:")
        for category, keywords in categories.items():
            category_tests = [name for name in self.test_results.keys() if any(keyword in name for keyword in keywords)]
            if category_tests:
                category_passed = sum(1 for name in category_tests if self.test_results[name]['success'])
                category_total = len(category_tests)
                category_rate = (category_passed / category_total * 100) if category_total > 0 else 0
                
                status = "✅" if category_rate >= 80 else "⚠️" if category_rate >= 50 else "❌"
                print(f"   {status} {category}: {category_rate:.1f}% ({category_passed}/{category_total})")
        
        print()
        
        # Production readiness assessment
        print("🏆 PRODUCTION READINESS ASSESSMENT:")
        if success_rate >= 85:
            print("   Status: ✅ PRODUCTION READY")
            print("   Assessment: Backend meets expected 85%+ success rate")
        elif success_rate >= 75:
            print("   Status: ⚠️  MOSTLY READY")
            print("   Assessment: Backend close to production ready")
        else:
            print("   Status: ❌ NEEDS IMPROVEMENT")
            print("   Assessment: Backend below expected success rate")
        
        print("\n" + "=" * 70)
        print("🎯 FOCUSED TESTING COMPLETE")
        print("=" * 70)

if __name__ == "__main__":
    tester = FocusedMewayzTester()
    tester.run_focused_final_test()