#!/usr/bin/env python3
"""
Focused Backend Testing for Mewayz Creator Economy Platform
Testing current_focus tasks from test_result.md
"""

import requests
import json
import sys
import time
from datetime import datetime

class FocusedMewayzTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.auth_token = "4|izvKQFwwuOh8g84sheh2esWZUsA9fg36n9zWq1CH3554df4b"
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
        time.sleep(0.2)  # Increased delay
        
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
            print(f"Request timeout for {url}")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None

    def test_biometric_authentication(self):
        """Test Biometric Authentication functionality"""
        print("\n=== Testing Biometric Authentication ===")
        
        # Test get registration options
        response = self.make_request('POST', '/biometric/registration-options')
        if response and response.status_code == 200:
            self.log_test("Biometric Registration Options", True, "Registration options retrieval successful")
        else:
            self.log_test("Biometric Registration Options", False, f"Registration options failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get authentication options (public endpoint)
        response = self.make_request('POST', '/biometric/authentication-options', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Biometric Authentication Options", True, "Authentication options retrieval successful")
        else:
            self.log_test("Biometric Authentication Options", False, f"Authentication options failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get user credentials
        response = self.make_request('GET', '/biometric/credentials')
        if response and response.status_code == 200:
            self.log_test("Biometric Credentials", True, "User credentials retrieval successful")
        else:
            self.log_test("Biometric Credentials", False, f"Credentials retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_realtime_features(self):
        """Test Real-Time Features functionality"""
        print("\n=== Testing Real-Time Features ===")
        
        # Test get notifications
        response = self.make_request('GET', '/realtime/notifications')
        if response and response.status_code == 200:
            self.log_test("Real-Time Notifications", True, "Notifications retrieval successful")
        else:
            self.log_test("Real-Time Notifications", False, f"Notifications retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get activity feed
        response = self.make_request('GET', '/realtime/activity-feed')
        if response and response.status_code == 200:
            self.log_test("Activity Feed", True, "Activity feed retrieval successful")
        else:
            self.log_test("Activity Feed", False, f"Activity feed retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get system status
        response = self.make_request('GET', '/realtime/system-status')
        if response and response.status_code == 200:
            self.log_test("System Status", True, "System status retrieval successful")
        else:
            self.log_test("System Status", False, f"System status retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get user presence
        response = self.make_request('GET', '/realtime/user-presence')
        if response and response.status_code == 200:
            self.log_test("User Presence", True, "User presence retrieval successful")
        else:
            self.log_test("User Presence", False, f"User presence retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_escrow_system(self):
        """Test Escrow & Transaction Security functionality"""
        print("\n=== Testing Escrow & Transaction Security ===")
        
        # Test get escrow transactions
        response = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Transactions", True, "Escrow transactions retrieval successful")
        else:
            self.log_test("Get Escrow Transactions", False, f"Escrow transactions retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create escrow transaction
        escrow_data = {
            "seller_id": "1",
            "item_type": "service",
            "item_title": "Test Item",
            "item_description": "Test Description",
            "total_amount": 100,
            "currency": "USD"
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Create Escrow Transaction", True, "Escrow transaction creation successful")
        else:
            self.log_test("Create Escrow Transaction", False, f"Escrow transaction creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get escrow statistics
        response = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Statistics", True, "Escrow statistics retrieval successful")
        else:
            self.log_test("Get Escrow Statistics", False, f"Escrow statistics retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_advanced_analytics(self):
        """Test Advanced Analytics & Business Intelligence functionality"""
        print("\n=== Testing Advanced Analytics & Business Intelligence ===")
        
        # Test get business intelligence
        response = self.make_request('GET', '/analytics/business-intelligence')
        if response and response.status_code == 200:
            self.log_test("Business Intelligence", True, "Business intelligence retrieval successful")
        else:
            self.log_test("Business Intelligence", False, f"Business intelligence retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get realtime metrics
        response = self.make_request('GET', '/analytics/realtime-metrics')
        if response and response.status_code == 200:
            self.log_test("Realtime Metrics", True, "Realtime metrics retrieval successful")
        else:
            self.log_test("Realtime Metrics", False, f"Realtime metrics retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_advanced_booking_system(self):
        """Test Advanced Booking System functionality"""
        print("\n=== Testing Advanced Booking System ===")
        
        # Test get booking services
        response = self.make_request('GET', '/booking/services')
        if response and response.status_code == 200:
            self.log_test("Get Booking Services", True, "Booking services retrieval successful")
        else:
            self.log_test("Get Booking Services", False, f"Booking services retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test create booking service
        service_data = {
            "name": "Business Consultation",
            "description": "One-on-one business strategy consultation",
            "duration": 60,
            "price": 150.00,
            "currency": "USD",
            "category": "consultation"
        }
        
        response = self.make_request('POST', '/booking/services', service_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Create Booking Service", True, "Booking service creation successful")
        else:
            self.log_test("Create Booking Service", False, f"Booking service creation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get appointments
        response = self.make_request('GET', '/booking/appointments')
        if response and response.status_code == 200:
            self.log_test("Get Appointments", True, "Appointments retrieval successful")
        else:
            self.log_test("Get Appointments", False, f"Appointments retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_advanced_financial_management(self):
        """Test Advanced Financial Management functionality"""
        print("\n=== Testing Advanced Financial Management ===")
        
        # Test get financial dashboard
        response = self.make_request('GET', '/financial/dashboard')
        if response and response.status_code == 200:
            self.log_test("Financial Dashboard", True, "Financial dashboard retrieval successful")
        else:
            self.log_test("Financial Dashboard", False, f"Financial dashboard retrieval failed - Status: {response.status_code if response else 'No response'}")
        
        # Test get invoices
        response = self.make_request('GET', '/financial/invoices')
        if response and response.status_code == 200:
            self.log_test("Get Invoices", True, "Invoices retrieval successful")
        else:
            self.log_test("Get Invoices", False, f"Invoices retrieval failed - Status: {response.status_code if response else 'No response'}")

    def test_enhanced_ai_features(self):
        """Test Enhanced AI Features functionality"""
        print("\n=== Testing Enhanced AI Features ===")
        
        # Test generate content suggestions
        content_data = {
            "content_type": "social_media_post",
            "topic": "digital marketing",
            "tone": "professional",
            "length": "medium"
        }
        
        response = self.make_request('POST', '/ai/content/generate', content_data)
        if response and response.status_code == 200:
            self.log_test("Generate Content Suggestions", True, "Content generation successful")
        else:
            self.log_test("Generate Content Suggestions", False, f"Content generation failed - Status: {response.status_code if response else 'No response'}")
        
        # Test lead scoring
        lead_data = {
            "lead_id": "test-lead-123",
            "email": "test@example.com",
            "company": "Test Company",
            "industry": "Technology"
        }
        
        response = self.make_request('POST', '/ai/leads/score', lead_data)
        if response and response.status_code == 200:
            self.log_test("Score Leads", True, "Lead scoring successful")
        else:
            self.log_test("Score Leads", False, f"Lead scoring failed - Status: {response.status_code if response else 'No response'}")

    def run_focused_tests(self):
        """Run all focused tests"""
        print("🎯 Starting Focused Backend Testing for Current Focus Tasks")
        print("=" * 80)
        
        self.test_biometric_authentication()
        self.test_realtime_features()
        self.test_escrow_system()
        self.test_advanced_analytics()
        self.test_advanced_booking_system()
        self.test_advanced_financial_management()
        self.test_enhanced_ai_features()
        
        # Summary
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests) * 100 if total_tests > 0 else 0
        
        print("\n" + "=" * 80)
        print("📊 FOCUSED TEST SUMMARY")
        print("=" * 80)
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")

if __name__ == "__main__":
    tester = FocusedMewayzTester()
    tester.run_focused_tests()