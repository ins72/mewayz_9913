#!/usr/bin/env python3
"""
SIXTH WAVE EMAIL MARKETING & ADVANCED ANALYTICS TEST - MEWAYZ PLATFORM
Testing newly integrated Sixth Wave systems
Testing Agent - December 2024
"""

import requests
import json
import time
from datetime import datetime

# Backend URL from frontend .env
BACKEND_URL = "https://e0710948-4e96-4e5f-9b39-4059da05c0de.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"

# Admin credentials from review request
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class SixthWaveTester:
    def __init__(self):
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.total_tests = 0
        self.passed_tests = 0
        
    def log_test(self, endpoint, method, status, response_time, success, details="", data_size=0):
        """Log test results"""
        self.total_tests += 1
        if success:
            self.passed_tests += 1
            
        result = {
            'endpoint': endpoint,
            'method': method,
            'status': status,
            'response_time': f"{response_time:.3f}s",
            'success': success,
            'details': details,
            'data_size': data_size
        }
        self.test_results.append(result)
        
        status_icon = "✅" if success else "❌"
        print(f"{status_icon} {method} {endpoint} - {status} ({response_time:.3f}s) - {details}")
        
    def authenticate(self):
        """Authenticate with admin credentials"""
        print(f"\n🔐 AUTHENTICATING WITH ADMIN CREDENTIALS...")
        print(f"Email: {ADMIN_EMAIL}")
        
        login_data = {
            "username": ADMIN_EMAIL,  # FastAPI OAuth2PasswordRequestForm uses 'username'
            "password": ADMIN_PASSWORD
        }
        
        try:
            start_time = time.time()
            response = self.session.post(f"{API_BASE}/auth/login", data=login_data, timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                self.auth_token = data.get('access_token')
                if self.auth_token:
                    self.session.headers.update({'Authorization': f'Bearer {self.auth_token}'})
                    self.log_test("/auth/login", "POST", response.status_code, response_time, True, 
                                f"Admin authentication successful, token: {self.auth_token[:20]}...")
                    return True
                else:
                    self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                                "No access token in response")
                    return False
            else:
                self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                            f"Login failed: {response.text[:100]}")
                return False
                
        except Exception as e:
            self.log_test("/auth/login", "POST", 0, 0, False, f"Authentication error: {str(e)}")
            return False
    
    def test_endpoint(self, endpoint, method="GET", data=None, form_data=None, expected_status=200, description=""):
        """Test a single endpoint"""
        url = f"{API_BASE}{endpoint}"
        
        try:
            start_time = time.time()
            
            if method == "GET":
                response = self.session.get(url, timeout=30)
            elif method == "POST":
                if form_data:
                    response = self.session.post(url, data=form_data, timeout=30)
                else:
                    response = self.session.post(url, json=data, timeout=30)
            elif method == "PUT":
                response = self.session.put(url, json=data, timeout=30)
            elif method == "DELETE":
                response = self.session.delete(url, timeout=30)
            else:
                raise ValueError(f"Unsupported method: {method}")
                
            response_time = time.time() - start_time
            
            # Check if response is successful
            success = response.status_code == expected_status
            
            # Get response details
            try:
                response_data = response.json()
                data_size = len(json.dumps(response_data))
                details = f"{description} - Response size: {data_size} chars"
                if not success:
                    details += f" - Error: {response_data.get('detail', 'Unknown error')}"
            except:
                data_size = len(response.text)
                details = f"{description} - Response size: {data_size} chars"
                if not success:
                    details += f" - Error: {response.text[:100]}"
            
            self.log_test(endpoint, method, response.status_code, response_time, success, details, data_size)
            return success, response
            
        except requests.exceptions.Timeout:
            self.log_test(endpoint, method, 0, 30.0, False, f"{description} - Request timeout")
            return False, None
        except Exception as e:
            self.log_test(endpoint, method, 0, 0, False, f"{description} - Error: {str(e)}")
            return False, None

    def test_email_marketing_system(self):
        """Test Email Marketing System endpoints"""
        print(f"\n📧 TESTING EMAIL MARKETING SYSTEM")
        
        # Test GET endpoints
        self.test_endpoint("/email-marketing/dashboard", "GET", description="Email marketing dashboard with comprehensive metrics")
        self.test_endpoint("/email-marketing/campaigns", "GET", description="User's email campaigns")
        self.test_endpoint("/email-marketing/lists", "GET", description="Email lists")
        self.test_endpoint("/email-marketing/contacts", "GET", description="Email contacts")
        self.test_endpoint("/email-marketing/templates", "GET", description="Email templates")
        self.test_endpoint("/email-marketing/analytics", "GET", description="Email marketing analytics")
        
        # Test POST endpoints with realistic data
        print(f"\n📧 Testing Email Marketing POST endpoints...")
        
        # Create email campaign
        campaign_data = {
            "name": "Holiday Newsletter 2024",
            "subject": "🎄 Special Holiday Offers Just for You!",
            "content": "<html><body><h1>Holiday Greetings!</h1><p>Check out our amazing holiday deals...</p></body></html>",
            "recipient_list_id": "test-list-123",
            "campaign_type": "regular"
        }
        self.test_endpoint("/email-marketing/campaigns", "POST", data=campaign_data, description="Create email campaign")
        
        # Create email list
        list_data = {
            "name": "Holiday Subscribers 2024",
            "description": "Subscribers interested in holiday promotions",
            "tags": ["holiday", "newsletter", "promotions"]
        }
        self.test_endpoint("/email-marketing/lists", "POST", data=list_data, description="Create email list")

    def test_advanced_analytics_system(self):
        """Test Advanced Analytics System endpoints"""
        print(f"\n📊 TESTING ADVANCED ANALYTICS SYSTEM")
        
        # Test all GET endpoints
        self.test_endpoint("/advanced-analytics/dashboard", "GET", description="Comprehensive analytics dashboard")
        self.test_endpoint("/advanced-analytics/overview", "GET", description="Analytics overview")
        self.test_endpoint("/advanced-analytics/real-time", "GET", description="Real-time analytics")
        self.test_endpoint("/advanced-analytics/business-intelligence", "GET", description="Business intelligence insights")
        self.test_endpoint("/advanced-analytics/customer-analytics", "GET", description="Customer analytics")
        self.test_endpoint("/advanced-analytics/revenue-analytics", "GET", description="Revenue analytics")
        self.test_endpoint("/advanced-analytics/conversion-funnel", "GET", description="Conversion funnel analysis")
        self.test_endpoint("/advanced-analytics/reports", "GET", description="Available reports")

    def run_comprehensive_test(self):
        """Run comprehensive test of Sixth Wave systems"""
        print("=" * 80)
        print("🌊 SIXTH WAVE EMAIL MARKETING & ADVANCED ANALYTICS TEST")
        print("=" * 80)
        print(f"Backend URL: {BACKEND_URL}")
        print(f"Test started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Authenticate first
        if not self.authenticate():
            print("❌ Authentication failed. Cannot proceed with tests.")
            return
        
        # Test Email Marketing System
        self.test_email_marketing_system()
        
        # Test Advanced Analytics System  
        self.test_advanced_analytics_system()
        
        # Print summary
        self.print_summary()
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("📊 SIXTH WAVE TEST SUMMARY")
        print("=" * 80)
        
        success_rate = (self.passed_tests / self.total_tests * 100) if self.total_tests > 0 else 0
        
        print(f"Total Tests: {self.total_tests}")
        print(f"Passed: {self.passed_tests}")
        print(f"Failed: {self.total_tests - self.passed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Calculate average response time
        total_time = 0
        valid_times = 0
        total_data = 0
        
        for result in self.test_results:
            if result['success']:
                try:
                    time_val = float(result['response_time'].replace('s', ''))
                    total_time += time_val
                    valid_times += 1
                    total_data += result['data_size']
                except:
                    pass
        
        if valid_times > 0:
            avg_time = total_time / valid_times
            print(f"Average Response Time: {avg_time:.3f}s")
            print(f"Total Data Processed: {total_data:,} bytes")
        
        print("\n🌊 SIXTH WAVE SYSTEMS STATUS:")
        
        # Email Marketing System Status
        email_tests = [r for r in self.test_results if '/email-marketing/' in r['endpoint']]
        email_passed = len([r for r in email_tests if r['success']])
        email_total = len(email_tests)
        email_rate = (email_passed / email_total * 100) if email_total > 0 else 0
        
        print(f"📧 Email Marketing System: {email_passed}/{email_total} ({email_rate:.1f}%) {'✅ WORKING' if email_rate >= 80 else '⚠️ PARTIAL' if email_rate >= 50 else '❌ BROKEN'}")
        
        # Advanced Analytics System Status
        analytics_tests = [r for r in self.test_results if '/advanced-analytics/' in r['endpoint']]
        analytics_passed = len([r for r in analytics_tests if r['success']])
        analytics_total = len(analytics_tests)
        analytics_rate = (analytics_passed / analytics_total * 100) if analytics_total > 0 else 0
        
        print(f"📊 Advanced Analytics System: {analytics_passed}/{analytics_total} ({analytics_rate:.1f}%) {'✅ WORKING' if analytics_rate >= 80 else '⚠️ PARTIAL' if analytics_rate >= 50 else '❌ BROKEN'}")
        
        print(f"\n🎯 SIXTH WAVE MIGRATION STATUS:")
        if success_rate >= 90:
            print("✅ EXCELLENT - Sixth Wave migration successful with outstanding performance")
        elif success_rate >= 80:
            print("✅ GOOD - Sixth Wave migration successful with good performance")
        elif success_rate >= 70:
            print("⚠️ ACCEPTABLE - Sixth Wave migration mostly successful with minor issues")
        elif success_rate >= 50:
            print("⚠️ PARTIAL - Sixth Wave migration partially successful, needs attention")
        else:
            print("❌ CRITICAL - Sixth Wave migration has significant issues requiring immediate attention")
        
        print(f"\nTest completed at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)

if __name__ == "__main__":
    tester = SixthWaveTester()
    tester.run_comprehensive_test()