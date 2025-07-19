#!/usr/bin/env python3
"""
Focused test for the specific endpoints mentioned in the review request
"""

import requests
import json
import time

class ReviewRequestTester:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0, response_data: str = ""):
        """Log test results with response data"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'status': status,
            'success': success,
            'details': details,
            'response_time': f"{response_time:.3f}s",
            'response_data': response_data[:500] if response_data else ""  # Limit response data
        }
        self.test_results.append(result)
        print(f"{status} - {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    Details: {details}")
        if response_data and len(response_data) > 0:
            print(f"    Response: {response_data[:200]}...")
    
    def make_request(self, method: str, endpoint: str, data: dict = None, timeout: int = 10) -> tuple:
        """Make HTTP request with error handling"""
        url = f"{self.api_url}{endpoint}"
        
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if self.auth_token:
            headers['Authorization'] = f'Bearer {self.auth_token}'
        
        try:
            start_time = time.time()
            
            if method.upper() == 'GET':
                response = self.session.get(url, headers=headers, timeout=timeout)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            
            return response, response_time
            
        except requests.exceptions.Timeout:
            return None, timeout
        except requests.exceptions.RequestException as e:
            print(f"Request error: {str(e)}")
            return None, 0.0
    
    def test_authentication_flow(self):
        """Test authentication flow: POST /api/auth/login"""
        print("\n=== 1. Testing Authentication Flow ===")
        
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                self.log_test("POST /api/auth/login", True, 
                            f"Admin authenticated: {data.get('user', {}).get('email')}", 
                            response_time, json.dumps(data, indent=2))
            else:
                self.log_test("POST /api/auth/login", False, 
                            f"Login failed: {data.get('message')}", 
                            response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("POST /api/auth/login", False, error_msg, response_time, response_data)
    
    def test_admin_dashboard(self):
        """Test comprehensive admin dashboard: GET /api/admin/dashboard"""
        print("\n=== 2. Testing Admin Dashboard ===")
        
        if not self.auth_token:
            self.log_test("GET /api/admin/dashboard", False, "No authentication token available")
            return
        
        response, response_time = self.make_request('GET', '/admin/dashboard')
        if response:
            if response.status_code == 200:
                data = response.json()
                self.log_test("GET /api/admin/dashboard", True, 
                            "Admin dashboard data retrieved", 
                            response_time, json.dumps(data, indent=2))
            else:
                error_msg = f"Status: {response.status_code}"
                response_data = response.text if response else ""
                self.log_test("GET /api/admin/dashboard", False, error_msg, response_time, response_data)
        else:
            self.log_test("GET /api/admin/dashboard", False, "Request timeout", response_time)
    
    def test_ai_features(self):
        """Test AI features"""
        print("\n=== 3. Testing AI Features ===")
        
        if not self.auth_token:
            self.log_test("AI Features", False, "No authentication token available")
            return
        
        # Test GET /api/ai/services
        response, response_time = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/ai/services", True, 
                        "AI services retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/ai/services", False, error_msg, response_time, response_data)
        
        # Test GET /api/ai/conversations
        response, response_time = self.make_request('GET', '/ai/conversations')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/ai/conversations", True, 
                        "AI conversations retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/ai/conversations", False, error_msg, response_time, response_data)
        
        # Test POST /api/ai/conversations
        conversation_data = {"title": "Test Conversation"}
        response, response_time = self.make_request('POST', '/ai/conversations', conversation_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                self.log_test("POST /api/ai/conversations", True, 
                            "AI conversation created", 
                            response_time, json.dumps(data, indent=2))
            else:
                error_msg = f"Status: {response.status_code}"
                response_data = response.text if response else ""
                self.log_test("POST /api/ai/conversations", False, error_msg, response_time, response_data)
        else:
            self.log_test("POST /api/ai/conversations", False, "Request timeout", response_time)
    
    def test_bio_sites_system(self):
        """Test enhanced bio sites system"""
        print("\n=== 4. Testing Bio Sites System ===")
        
        if not self.auth_token:
            self.log_test("Bio Sites System", False, "No authentication token available")
            return
        
        # Test GET /api/bio-sites
        response, response_time = self.make_request('GET', '/bio-sites')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/bio-sites", True, 
                        "Bio sites retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/bio-sites", False, error_msg, response_time, response_data)
        
        # Test GET /api/bio-sites/themes
        response, response_time = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/bio-sites/themes", True, 
                        "Bio sites themes retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/bio-sites/themes", False, error_msg, response_time, response_data)
        
        # Test POST /api/bio-sites
        bio_site_data = {
            "title": "Test Site",
            "slug": "test-site",
            "theme": "modern"
        }
        response, response_time = self.make_request('POST', '/bio-sites', bio_site_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                self.log_test("POST /api/bio-sites", True, 
                            "Bio site created", 
                            response_time, json.dumps(data, indent=2))
            else:
                error_msg = f"Status: {response.status_code}"
                response_data = response.text if response else ""
                self.log_test("POST /api/bio-sites", False, error_msg, response_time, response_data)
        else:
            self.log_test("POST /api/bio-sites", False, "Request timeout", response_time)
    
    def test_ecommerce(self):
        """Test comprehensive e-commerce"""
        print("\n=== 5. Testing E-commerce ===")
        
        if not self.auth_token:
            self.log_test("E-commerce", False, "No authentication token available")
            return
        
        # Test GET /api/ecommerce/products
        response, response_time = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/ecommerce/products", True, 
                        "E-commerce products retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/ecommerce/products", False, error_msg, response_time, response_data)
        
        # Test GET /api/ecommerce/orders
        response, response_time = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/ecommerce/orders", True, 
                        "E-commerce orders retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/ecommerce/orders", False, error_msg, response_time, response_data)
        
        # Test GET /api/ecommerce/dashboard (if available)
        response, response_time = self.make_request('GET', '/ecommerce/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/ecommerce/dashboard", True, 
                        "E-commerce dashboard retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/ecommerce/dashboard", False, error_msg, response_time, response_data)
    
    def test_booking_system(self):
        """Test advanced booking system"""
        print("\n=== 6. Testing Booking System ===")
        
        if not self.auth_token:
            self.log_test("Booking System", False, "No authentication token available")
            return
        
        # Test GET /api/bookings/services
        response, response_time = self.make_request('GET', '/bookings/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/bookings/services", True, 
                        "Booking services retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/bookings/services", False, error_msg, response_time, response_data)
        
        # Test GET /api/bookings/appointments
        response, response_time = self.make_request('GET', '/bookings/appointments')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/bookings/appointments", True, 
                        "Booking appointments retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/bookings/appointments", False, error_msg, response_time, response_data)
        
        # Test GET /api/bookings/dashboard (if available)
        response, response_time = self.make_request('GET', '/bookings/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/bookings/dashboard", True, 
                        "Booking dashboard retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/bookings/dashboard", False, error_msg, response_time, response_data)
    
    def test_financial_system(self):
        """Test comprehensive financial system"""
        print("\n=== 7. Testing Financial System ===")
        
        if not self.auth_token:
            self.log_test("Financial System", False, "No authentication token available")
            return
        
        # Test GET /api/financial/invoices
        response, response_time = self.make_request('GET', '/financial/invoices')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/financial/invoices", True, 
                        "Financial invoices retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/financial/invoices", False, error_msg, response_time, response_data)
        
        # Test GET /api/financial/dashboard/comprehensive (if available)
        response, response_time = self.make_request('GET', '/financial/dashboard/comprehensive')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/financial/dashboard/comprehensive", True, 
                        "Financial comprehensive dashboard retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/financial/dashboard/comprehensive", False, error_msg, response_time, response_data)
    
    def test_analytics(self):
        """Test advanced analytics"""
        print("\n=== 8. Testing Analytics ===")
        
        if not self.auth_token:
            self.log_test("Analytics", False, "No authentication token available")
            return
        
        # Test GET /api/analytics/overview
        response, response_time = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/analytics/overview", True, 
                        "Analytics overview retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/analytics/overview", False, error_msg, response_time, response_data)
        
        # Test GET /api/analytics/business-intelligence/advanced (if available)
        response, response_time = self.make_request('GET', '/analytics/business-intelligence/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/analytics/business-intelligence/advanced", True, 
                        "Advanced business intelligence retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/analytics/business-intelligence/advanced", False, error_msg, response_time, response_data)
    
    def test_notifications(self):
        """Test notification system"""
        print("\n=== 9. Testing Notifications ===")
        
        if not self.auth_token:
            self.log_test("Notifications", False, "No authentication token available")
            return
        
        # Test GET /api/notifications
        response, response_time = self.make_request('GET', '/notifications')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/notifications", True, 
                        "Notifications retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/notifications", False, error_msg, response_time, response_data)
        
        # Test GET /api/notifications/advanced (if available)
        response, response_time = self.make_request('GET', '/notifications/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/notifications/advanced", True, 
                        "Advanced notifications retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/notifications/advanced", False, error_msg, response_time, response_data)
    
    def test_workspace_management(self):
        """Test workspace management"""
        print("\n=== 10. Testing Workspace Management ===")
        
        if not self.auth_token:
            self.log_test("Workspace Management", False, "No authentication token available")
            return
        
        # Test GET /api/workspaces
        response, response_time = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("GET /api/workspaces", True, 
                        "Workspaces retrieved", 
                        response_time, json.dumps(data, indent=2))
        else:
            error_msg = f"Status: {response.status_code}" if response else "timeout"
            response_data = response.text if response else ""
            self.log_test("GET /api/workspaces", False, error_msg, response_time, response_data)
        
        # Test POST /api/workspaces
        workspace_data = {
            "name": "Test Workspace",
            "description": "Test workspace"
        }
        response, response_time = self.make_request('POST', '/workspaces', workspace_data)
        if response:
            if response.status_code in [200, 201]:
                data = response.json()
                self.log_test("POST /api/workspaces", True, 
                            "Workspace created", 
                            response_time, json.dumps(data, indent=2))
            else:
                error_msg = f"Status: {response.status_code}"
                response_data = response.text if response else ""
                self.log_test("POST /api/workspaces", False, error_msg, response_time, response_data)
        else:
            self.log_test("POST /api/workspaces", False, "Request timeout", response_time)
    
    def run_review_request_tests(self):
        """Run all review request tests"""
        print("🚀 Starting Review Request Backend Testing")
        print(f"Testing backend at: {self.api_url}")
        print("=" * 80)
        
        # Run all test suites in order
        self.test_authentication_flow()
        self.test_admin_dashboard()
        self.test_ai_features()
        self.test_bio_sites_system()
        self.test_ecommerce()
        self.test_booking_system()
        self.test_financial_system()
        self.test_analytics()
        self.test_notifications()
        self.test_workspace_management()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 80)
        print("📊 REVIEW REQUEST TESTING SUMMARY")
        print("=" * 80)
        
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
                print(f"  • {result['test']}")
        
        print("\n" + "=" * 80)
        
        # Overall assessment
        if success_rate >= 80:
            print("🎉 EXCELLENT: All review request endpoints are highly functional!")
        elif success_rate >= 60:
            print("✅ GOOD: Most review request endpoints are functional.")
        elif success_rate >= 40:
            print("⚠️  MODERATE: Some review request endpoints need attention.")
        else:
            print("❌ CRITICAL: Major issues with review request endpoints.")

def main():
    """Main function to run the review request tests"""
    backend_url = "https://1fa91da9-a61e-4244-86cb-361982f500be.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    
    # Initialize tester
    tester = ReviewRequestTester(backend_url)
    
    # Run review request tests
    tester.run_review_request_tests()

if __name__ == "__main__":
    main()