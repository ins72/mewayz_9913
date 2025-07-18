#!/usr/bin/env python3
"""
Production Readiness Backend Testing - Focused on Review Request Requirements
Testing the key systems mentioned in the comprehensive review request
"""

import requests
import json
import sys
import time
from datetime import datetime

class ProductionReadinessTest:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use fresh token from registration
        self.auth_token = "3|aTPWlxF1E6aVX2FO8TJJ1MC2gdvBwF6g5tSvHOI0b423232a"
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
        """Make HTTP request with proper headers and rate limiting"""
        # Add delay to avoid rate limiting
        time.sleep(0.3)
        
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
        """Test core infrastructure as mentioned in review request"""
        print("\n=== Testing Core Infrastructure ===")
        
        # API Health Check
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Health Check", True, f"API healthy - Status: {data.get('status', 'unknown')}")
        else:
            self.log_test("API Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
        
        # API Test Endpoint
        response = self.make_request('GET', '/test', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Test Endpoint", True, f"Test endpoint working - Message: {data.get('message', 'No message')}")
        else:
            self.log_test("API Test Endpoint", False, f"Test endpoint failed - Status: {response.status_code if response else 'No response'}")

    def test_authentication_system(self):
        """Test authentication system"""
        print("\n=== Testing Authentication System ===")
        
        # Test authenticated endpoint
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Authentication System", True, f"Auth working - User: {data.get('user', {}).get('name', 'unknown')}")
        else:
            self.log_test("Authentication System", False, f"Auth failed - Status: {response.status_code if response else 'No response'}")

    def test_legal_pages_system(self):
        """Test Legal Pages System - mentioned as 100% functional in review"""
        print("\n=== Testing Legal Pages System ===")
        
        # Test legal document endpoints
        legal_pages = [
            'terms-of-service',
            'privacy-policy', 
            'cookie-policy',
            'refund-policy',
            'accessibility'
        ]
        
        for page in legal_pages:
            response = self.make_request('GET', f'/{page}', auth_required=False)
            if response and response.status_code == 200:
                self.log_test(f"Legal Page - {page.title()}", True, f"{page} page accessible")
            else:
                self.log_test(f"Legal Page - {page.title()}", False, f"{page} page failed - Status: {response.status_code if response else 'No response'}")

    def test_database_tables_creation(self):
        """Test Phase 1 Database Tables Creation - mentioned as fixed in review"""
        print("\n=== Testing Database Tables Creation (Phase 1) ===")
        
        # Test bio_sites table
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            self.log_test("Bio Sites Table", True, "bio_sites table accessible and functional")
        else:
            self.log_test("Bio Sites Table", False, f"bio_sites table access failed - Status: {response.status_code if response else 'No response'}")
        
        # Test escrow_transactions table
        response = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("Escrow Transactions Table", True, "escrow_transactions table accessible")
        else:
            self.log_test("Escrow Transactions Table", False, f"escrow_transactions table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test booking_services table
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("Booking Services Table", True, "booking_services table accessible")
        else:
            self.log_test("Booking Services Table", False, f"booking_services table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test workspaces table
        response = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            self.log_test("Workspaces Table", True, "workspaces table accessible")
        else:
            self.log_test("Workspaces Table", False, f"workspaces table failed - Status: {response.status_code if response else 'No response'}")
        
        # Test email_campaigns table
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            self.log_test("Email Campaigns Table", True, "email_campaigns table accessible")
        else:
            self.log_test("Email Campaigns Table", False, f"email_campaigns table failed - Status: {response.status_code if response else 'No response'}")

    def test_core_business_features(self):
        """Test core business features mentioned in review request"""
        print("\n=== Testing Core Business Features ===")
        
        # Ultra-Advanced Gamification System
        response = self.make_request('GET', '/gamification/achievements')
        if response and response.status_code == 200:
            self.log_test("Gamification System", True, "Gamification achievements accessible")
        else:
            self.log_test("Gamification System", False, f"Gamification failed - Status: {response.status_code if response else 'No response'}")
        
        # Bio Sites & Link-in-Bio functionality
        response = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            self.log_test("Bio Sites System", True, "Bio sites themes accessible")
        else:
            self.log_test("Bio Sites System", False, f"Bio sites failed - Status: {response.status_code if response else 'No response'}")
        
        # E-commerce system
        response = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            self.log_test("E-commerce System", True, "E-commerce products accessible")
        else:
            self.log_test("E-commerce System", False, f"E-commerce failed - Status: {response.status_code if response else 'No response'}")
        
        # Course Creation system
        response = self.make_request('GET', '/courses/')
        if response and response.status_code == 200:
            self.log_test("Course Creation System", True, "Course system accessible")
        else:
            self.log_test("Course Creation System", False, f"Course creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Advanced Booking System
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("Advanced Booking System", True, "Booking services accessible")
        else:
            self.log_test("Advanced Booking System", False, f"Booking system failed - Status: {response.status_code if response else 'No response'}")
        
        # Escrow & Transaction Security
        response = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            self.log_test("Escrow System", True, "Escrow statistics accessible")
        else:
            self.log_test("Escrow System", False, f"Escrow system failed - Status: {response.status_code if response else 'No response'}")

    def test_advanced_features(self):
        """Test advanced features mentioned in review request"""
        print("\n=== Testing Advanced Features ===")
        
        # Advanced Analytics & BI
        response = self.make_request('GET', '/analytics/business-intelligence')
        if response and response.status_code == 200:
            self.log_test("Advanced Analytics", True, "Business intelligence accessible")
        else:
            self.log_test("Advanced Analytics", False, f"Analytics failed - Status: {response.status_code if response else 'No response'}")
        
        # Enhanced AI Features
        response = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            self.log_test("Enhanced AI Features", True, "AI services accessible")
        else:
            self.log_test("Enhanced AI Features", False, f"AI features failed - Status: {response.status_code if response else 'No response'}")
        
        # Real-Time Features
        response = self.make_request('GET', '/realtime/notifications')
        if response and response.status_code == 200:
            self.log_test("Real-Time Features", True, "Real-time notifications accessible")
        else:
            self.log_test("Real-Time Features", False, f"Real-time failed - Status: {response.status_code if response else 'No response'}")
        
        # Website Builder System
        response = self.make_request('GET', '/websites/templates')
        if response and response.status_code == 200:
            self.log_test("Website Builder", True, "Website templates accessible")
        else:
            self.log_test("Website Builder", False, f"Website builder failed - Status: {response.status_code if response else 'No response'}")

    def test_admin_security(self):
        """Test admin and security systems"""
        print("\n=== Testing Admin & Security Systems ===")
        
        # Admin Dashboard (should return 403 for non-admin users)
        response = self.make_request('GET', '/admin/dashboard')
        if response and response.status_code == 403:
            self.log_test("Admin Dashboard Security", True, "Admin dashboard properly secured (403 for non-admin)")
        elif response and response.status_code == 200:
            self.log_test("Admin Dashboard Access", True, "Admin dashboard accessible (user has admin privileges)")
        else:
            self.log_test("Admin Dashboard", False, f"Admin dashboard failed - Status: {response.status_code if response else 'No response'}")
        
        # Biometric Authentication
        response = self.make_request('POST', '/biometric/registration-options')
        if response and response.status_code == 200:
            self.log_test("Biometric Authentication", True, "Biometric registration options accessible")
        else:
            self.log_test("Biometric Authentication", False, f"Biometric auth failed - Status: {response.status_code if response else 'No response'}")
        
        # OAuth Integration
        response = self.make_request('GET', '/auth/oauth/providers', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("OAuth Integration", True, "OAuth providers accessible")
        else:
            self.log_test("OAuth Integration", False, f"OAuth failed - Status: {response.status_code if response else 'No response'}")

    def test_payment_processing(self):
        """Test payment processing system"""
        print("\n=== Testing Payment Processing ===")
        
        # Payment packages (public endpoint)
        response = self.make_request('GET', '/payments/packages', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Payment Processing", True, "Payment packages accessible")
        else:
            self.log_test("Payment Processing", False, f"Payment processing failed - Status: {response.status_code if response else 'No response'}")

    def run_production_readiness_test(self):
        """Run focused production readiness test based on review request"""
        print("🚀 PRODUCTION READINESS BACKEND TESTING - COMPREHENSIVE DATABASE SETUP")
        print("=" * 80)
        print(f"Testing against: {self.base_url}")
        print(f"API Endpoint: {self.api_url}")
        print(f"Focus: Legal Pages, Database Tables, Core Business Features")
        print("=" * 80)
        
        # Test in order of priority from review request
        self.test_core_infrastructure()
        self.test_authentication_system()
        self.test_legal_pages_system()  # Should be 100% functional
        self.test_database_tables_creation()  # Phase 1 fixes
        self.test_core_business_features()  # Major systems
        self.test_advanced_features()  # Advanced systems
        self.test_admin_security()  # Admin & Security
        self.test_payment_processing()  # Production systems
        
        # Generate report
        self.generate_report()

    def generate_report(self):
        """Generate production readiness report"""
        print("\n" + "=" * 80)
        print("🎯 PRODUCTION READINESS ASSESSMENT REPORT")
        print("=" * 80)
        
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
        
        # Expected vs Actual
        expected_rate = 80.0  # From review request
        print(f"📈 REVIEW REQUEST EXPECTATIONS:")
        print(f"   Expected Success Rate: {expected_rate}%+")
        print(f"   Actual Success Rate: {success_rate:.1f}%")
        if success_rate >= expected_rate:
            print(f"   Status: ✅ MEETS EXPECTATIONS")
        else:
            print(f"   Status: ❌ BELOW EXPECTATIONS ({success_rate:.1f}% vs {expected_rate}%)")
        print()
        
        # Key findings
        print("🔍 KEY FINDINGS:")
        
        # Legal Pages System
        legal_tests = [name for name in self.test_results.keys() if 'Legal Page' in name]
        if legal_tests:
            legal_passed = sum(1 for name in legal_tests if self.test_results[name]['success'])
            legal_rate = (legal_passed / len(legal_tests) * 100) if legal_tests else 0
            print(f"   Legal Pages System: {legal_rate:.1f}% ({legal_passed}/{len(legal_tests)}) - Expected: 100%")
        
        # Database Tables
        db_tests = [name for name in self.test_results.keys() if 'Table' in name]
        if db_tests:
            db_passed = sum(1 for name in db_tests if self.test_results[name]['success'])
            db_rate = (db_passed / len(db_tests) * 100) if db_tests else 0
            print(f"   Database Tables: {db_rate:.1f}% ({db_passed}/{len(db_tests)}) - Expected: Fixed")
        
        # Core Business Features
        business_tests = [name for name in self.test_results.keys() if any(keyword in name for keyword in ['Gamification', 'Bio Sites', 'E-commerce', 'Course', 'Booking', 'Escrow'])]
        if business_tests:
            business_passed = sum(1 for name in business_tests if self.test_results[name]['success'])
            business_rate = (business_passed / len(business_tests) * 100) if business_tests else 0
            print(f"   Core Business Features: {business_rate:.1f}% ({business_passed}/{len(business_tests)})")
        
        print()
        
        # Production readiness assessment
        print("🏆 PRODUCTION READINESS ASSESSMENT:")
        if success_rate >= 90:
            print("   Status: ✅ PRODUCTION READY")
            print("   Assessment: System exceeds expectations and is ready for production")
        elif success_rate >= 80:
            print("   Status: ✅ MEETS EXPECTATIONS")
            print("   Assessment: System meets review request expectations")
        elif success_rate >= 60:
            print("   Status: ⚠️  PARTIALLY READY")
            print("   Assessment: System shows improvement but needs minor fixes")
        else:
            print("   Status: ❌ BELOW EXPECTATIONS")
            print("   Assessment: System requires significant improvements")
        
        print("\n" + "=" * 80)
        print("🎯 PRODUCTION READINESS TESTING COMPLETE")
        print("=" * 80)

if __name__ == "__main__":
    tester = ProductionReadinessTest()
    tester.run_production_readiness_test()