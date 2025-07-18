#!/usr/bin/env python3
"""
Focused Database Table Testing for Mewayz Creator Economy Platform
Testing specific endpoints that should now work after database table creation
"""

import requests
import json
import sys
import time
from datetime import datetime

class FocusedDatabaseTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use the latest valid test token from test_result.md
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
    
    def test_bio_sites_controller(self):
        """Test BioSiteController - should now work with bio_sites table"""
        print("\n=== Testing BioSiteController (Priority Focus) ===")
        
        # Test GET /api/bio-sites/ - should now return data instead of "Failed to retrieve bio sites"
        response = self.make_request('GET', '/bio-sites/')
        if response:
            if response.status_code == 200:
                try:
                    data = response.json()
                    if isinstance(data, dict) and 'message' in data and 'Failed to retrieve' in data['message']:
                        self.log_test("BioSiteController GET", False, f"Still returns 'Failed to retrieve' error: {data['message']}")
                    else:
                        self.log_test("BioSiteController GET", True, f"Bio sites retrieval successful - Status: {response.status_code}")
                except:
                    self.log_test("BioSiteController GET", True, f"Bio sites endpoint working - Status: {response.status_code}")
            else:
                self.log_test("BioSiteController GET", False, f"Bio sites failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("BioSiteController GET", False, "Bio sites endpoint timeout/no response")
        
        # Test POST /api/bio-sites/ - create a bio site
        bio_site_data = {
            "name": "Test Creator Bio",
            "title": "Test Creator Bio",
            "slug": f"test-creator-{int(time.time())}",
            "description": "This is a test bio site for the creator",
            "theme": "minimal"
        }
        
        response = self.make_request('POST', '/bio-sites/', bio_site_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("BioSiteController POST", True, f"Bio site creation successful - Status: {response.status_code}")
            else:
                self.log_test("BioSiteController POST", False, f"Bio site creation failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("BioSiteController POST", False, "Bio site creation timeout/no response")
    
    def test_email_marketing_controller(self):
        """Test EmailMarketingController - should now work with email_campaigns table"""
        print("\n=== Testing EmailMarketingController (Priority Focus) ===")
        
        # Test GET /api/email-marketing/campaigns - should now return data instead of "Failed to fetch campaigns"
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response:
            if response.status_code == 200:
                try:
                    data = response.json()
                    if isinstance(data, dict) and 'message' in data and 'Failed to fetch' in data['message']:
                        self.log_test("EmailMarketingController GET", False, f"Still returns 'Failed to fetch' error: {data['message']}")
                    else:
                        self.log_test("EmailMarketingController GET", True, f"Email campaigns retrieval successful - Status: {response.status_code}")
                except:
                    self.log_test("EmailMarketingController GET", True, f"Email campaigns endpoint working - Status: {response.status_code}")
            else:
                self.log_test("EmailMarketingController GET", False, f"Email campaigns failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("EmailMarketingController GET", False, "Email campaigns endpoint timeout/no response")
        
        # Test POST /api/email-marketing/campaigns - create a campaign
        campaign_data = {
            "name": "Test Email Campaign",
            "subject": "Welcome to Our Platform!",
            "content": "This is a test email campaign content",
            "status": "draft"
        }
        
        response = self.make_request('POST', '/email-marketing/campaigns', campaign_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("EmailMarketingController POST", True, f"Email campaign creation successful - Status: {response.status_code}")
            else:
                self.log_test("EmailMarketingController POST", False, f"Email campaign creation failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("EmailMarketingController POST", False, "Email campaign creation timeout/no response")
    
    def test_escrow_controller(self):
        """Test EscrowController - should now work with escrow_transactions table"""
        print("\n=== Testing EscrowController (Priority Focus) ===")
        
        # Test GET /api/escrow/ - should now return data instead of "Failed to retrieve escrow transactions"
        response = self.make_request('GET', '/escrow/')
        if response:
            if response.status_code == 200:
                try:
                    data = response.json()
                    if isinstance(data, dict) and 'message' in data and 'Failed to retrieve' in data['message']:
                        self.log_test("EscrowController GET", False, f"Still returns 'Failed to retrieve' error: {data['message']}")
                    else:
                        self.log_test("EscrowController GET", True, f"Escrow transactions retrieval successful - Status: {response.status_code}")
                except:
                    self.log_test("EscrowController GET", True, f"Escrow transactions endpoint working - Status: {response.status_code}")
            else:
                self.log_test("EscrowController GET", False, f"Escrow transactions failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("EscrowController GET", False, "Escrow transactions endpoint timeout/no response")
        
        # Test POST /api/escrow/ - create an escrow transaction
        escrow_data = {
            "buyer_id": "test-buyer-id",
            "seller_id": "test-seller-id",
            "amount": 100.00,
            "currency": "USD",
            "description": "Test digital product transaction",
            "terms": "Standard escrow terms for digital product delivery"
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("EscrowController POST", True, f"Escrow transaction creation successful - Status: {response.status_code}")
            else:
                self.log_test("EscrowController POST", False, f"Escrow transaction creation failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("EscrowController POST", False, "Escrow transaction creation timeout/no response")
        
        # Test GET /api/escrow/statistics/overview - escrow statistics
        response = self.make_request('GET', '/escrow/statistics/overview')
        if response:
            if response.status_code == 200:
                try:
                    data = response.json()
                    if isinstance(data, dict) and 'message' in data and 'Failed to retrieve' in data['message']:
                        self.log_test("EscrowController Statistics", False, f"Still returns 'Failed to retrieve' error: {data['message']}")
                    else:
                        self.log_test("EscrowController Statistics", True, f"Escrow statistics retrieval successful - Status: {response.status_code}")
                except:
                    self.log_test("EscrowController Statistics", True, f"Escrow statistics endpoint working - Status: {response.status_code}")
            else:
                self.log_test("EscrowController Statistics", False, f"Escrow statistics failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("EscrowController Statistics", False, "Escrow statistics endpoint timeout/no response")
    
    def test_advanced_booking_controller(self):
        """Test AdvancedBookingController - should now work with booking_services table"""
        print("\n=== Testing AdvancedBookingController (Priority Focus) ===")
        
        # Test GET /api/booking/services - should now return data instead of "Failed to retrieve booking services"
        response = self.make_request('GET', '/booking/services')
        if response:
            if response.status_code == 200:
                try:
                    data = response.json()
                    if isinstance(data, dict) and 'message' in data and 'Failed to retrieve' in data['message']:
                        self.log_test("AdvancedBookingController GET", False, f"Still returns 'Failed to retrieve' error: {data['message']}")
                    else:
                        self.log_test("AdvancedBookingController GET", True, f"Booking services retrieval successful - Status: {response.status_code}")
                except:
                    self.log_test("AdvancedBookingController GET", True, f"Booking services endpoint working - Status: {response.status_code}")
            else:
                self.log_test("AdvancedBookingController GET", False, f"Booking services failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("AdvancedBookingController GET", False, "Booking services endpoint timeout/no response")
        
        # Test POST /api/booking/services - create a booking service
        service_data = {
            "name": "Business Consultation",
            "description": "One-on-one business strategy consultation",
            "duration": 60,
            "price": 150.00,
            "currency": "USD",
            "category": "consultation"
        }
        
        response = self.make_request('POST', '/booking/services', service_data)
        if response:
            if response.status_code in [200, 201]:
                self.log_test("AdvancedBookingController POST", True, f"Booking service creation successful - Status: {response.status_code}")
            else:
                self.log_test("AdvancedBookingController POST", False, f"Booking service creation failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("AdvancedBookingController POST", False, "Booking service creation timeout/no response")
        
        # Test GET /api/booking/appointments - get appointments
        response = self.make_request('GET', '/booking/appointments')
        if response:
            if response.status_code == 200:
                try:
                    data = response.json()
                    if isinstance(data, dict) and 'message' in data and 'Failed to retrieve' in data['message']:
                        self.log_test("AdvancedBookingController Appointments", False, f"Still returns 'Failed to retrieve' error: {data['message']}")
                    else:
                        self.log_test("AdvancedBookingController Appointments", True, f"Appointments retrieval successful - Status: {response.status_code}")
                except:
                    self.log_test("AdvancedBookingController Appointments", True, f"Appointments endpoint working - Status: {response.status_code}")
            else:
                self.log_test("AdvancedBookingController Appointments", False, f"Appointments failed - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("AdvancedBookingController Appointments", False, "Appointments endpoint timeout/no response")
    
    def test_admin_dashboard_system(self):
        """Test Ultra-Comprehensive Admin Dashboard System - expected to still require admin access"""
        print("\n=== Testing Admin Dashboard System (Expected Admin Access Required) ===")
        
        # Test GET /api/admin/dashboard - should return 403 Forbidden (admin access required)
        response = self.make_request('GET', '/admin/dashboard')
        if response:
            if response.status_code == 403:
                self.log_test("Admin Dashboard Access Control", True, f"Correctly returns 403 Forbidden - admin access required (expected behavior)")
            elif response.status_code == 200:
                self.log_test("Admin Dashboard Access Control", False, f"Unexpectedly allows access - Status: {response.status_code} (security concern)")
            else:
                self.log_test("Admin Dashboard Access Control", False, f"Unexpected response - Status: {response.status_code}, Response: {response.text[:200]}")
        else:
            self.log_test("Admin Dashboard Access Control", False, "Admin dashboard endpoint timeout/no response")
    
    def test_authentication_system(self):
        """Test authentication system to verify token is working"""
        print("\n=== Testing Authentication System (Token Verification) ===")
        
        # Test custom auth middleware with provided token
        response = self.make_request('GET', '/test-custom-auth')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Custom Auth Middleware", True, f"Custom auth working - User: {data.get('user_name', 'unknown')}")
        else:
            self.log_test("Custom Auth Middleware", False, f"Custom auth failed - Status: {response.status_code if response else 'No response'}")
        
        # Test authenticated user profile
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("User Profile Authentication", True, f"Profile retrieval successful - User: {data.get('user', {}).get('name', 'unknown')}")
        else:
            self.log_test("User Profile Authentication", False, f"Profile retrieval failed - Status: {response.status_code if response else 'No response'}")
    
    def run_focused_tests(self):
        """Run all focused tests for database table verification"""
        print("🎯 FOCUSED DATABASE TABLE TESTING")
        print("=" * 60)
        print("Testing specific endpoints that should now work after database table creation:")
        print("- BioSiteController (bio_sites table)")
        print("- EmailMarketingController (email_campaigns table)")
        print("- EscrowController (escrow_transactions table)")
        print("- AdvancedBookingController (booking_services table)")
        print("- Admin Dashboard (expected to still require admin access)")
        print("=" * 60)
        
        # First verify authentication is working
        self.test_authentication_system()
        
        # Test the 4 priority focus areas
        self.test_bio_sites_controller()
        self.test_email_marketing_controller()
        self.test_escrow_controller()
        self.test_advanced_booking_controller()
        self.test_admin_dashboard_system()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 60)
        print("🎯 FOCUSED DATABASE TABLE TESTING SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests}")
        print(f"Failed: {failed_tests}")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        print("\n📊 DETAILED RESULTS:")
        for test_name, result in self.test_results.items():
            status = "✅ PASS" if result['success'] else "❌ FAIL"
            print(f"{status} {test_name}: {result['message']}")
        
        print("\n🎯 KEY FINDINGS:")
        
        # Check if the main issues are resolved
        bio_sites_fixed = any("BioSiteController GET" in test and result['success'] for test, result in self.test_results.items())
        email_marketing_fixed = any("EmailMarketingController GET" in test and result['success'] for test, result in self.test_results.items())
        escrow_fixed = any("EscrowController GET" in test and result['success'] for test, result in self.test_results.items())
        booking_fixed = any("AdvancedBookingController GET" in test and result['success'] for test, result in self.test_results.items())
        
        if bio_sites_fixed:
            print("✅ BioSiteController: Database table creation SUCCESSFUL - no longer returns 'Failed to retrieve bio sites'")
        else:
            print("❌ BioSiteController: Still has issues - may still return 'Failed to retrieve bio sites'")
            
        if email_marketing_fixed:
            print("✅ EmailMarketingController: Database table creation SUCCESSFUL - no longer returns 'Failed to fetch campaigns'")
        else:
            print("❌ EmailMarketingController: Still has issues - may still return 'Failed to fetch campaigns'")
            
        if escrow_fixed:
            print("✅ EscrowController: Database table creation SUCCESSFUL - no longer returns 'Failed to retrieve escrow transactions'")
        else:
            print("❌ EscrowController: Still has issues - may still return 'Failed to retrieve escrow transactions'")
            
        if booking_fixed:
            print("✅ AdvancedBookingController: Database table creation SUCCESSFUL - no longer returns 'Failed to retrieve booking services'")
        else:
            print("❌ AdvancedBookingController: Still has issues - may still return 'Failed to retrieve booking services'")
        
        # Check admin dashboard
        admin_working = any("Admin Dashboard Access Control" in test and result['success'] for test, result in self.test_results.items())
        if admin_working:
            print("✅ Admin Dashboard: Correctly secured - returns 403 Forbidden as expected")
        else:
            print("❌ Admin Dashboard: Unexpected behavior - not returning expected 403 Forbidden")
        
        print("\n" + "=" * 60)

if __name__ == "__main__":
    tester = FocusedDatabaseTester()
    tester.run_focused_tests()