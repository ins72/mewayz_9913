#!/usr/bin/env python3
"""
AI Integration Backend Testing for Mewayz Platform
Tests AI endpoints and core API functionality as requested in review request
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class MewayzAIBackendTester:
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
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            
            return response, response_time
            
        except requests.exceptions.Timeout:
            return None, timeout
        except requests.exceptions.RequestException as e:
            print(f"Request error: {e}")
            return None, 0
    
    def test_health_check(self):
        """Test system health endpoint"""
        response, response_time = self.make_request('GET', '/health')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test(
                    "System Health Check", 
                    True, 
                    f"Status: {data.get('status', 'unknown')}, Version: {data.get('version', 'unknown')}", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("System Health Check", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("System Health Check", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_admin_login(self):
        """Test admin authentication"""
        login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time = self.make_request('POST', '/auth/login', login_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and 'access_token' in data:
                    self.auth_token = data['access_token']
                    user_info = data.get('user', {})
                    self.log_test(
                        "Admin Authentication", 
                        True, 
                        f"User: {user_info.get('name', 'Unknown')}, Role: {user_info.get('role', 'Unknown')}", 
                        response_time
                    )
                    return True
                else:
                    self.log_test("Admin Authentication", False, "No access token in response", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_test("Admin Authentication", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Admin Authentication", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_jwt_token_validation(self):
        """Test JWT token validation"""
        if not self.auth_token:
            self.log_test("JWT Token Validation", False, "No auth token available", 0)
            return False
        
        response, response_time = self.make_request('GET', '/auth/me')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                user_info = data.get('user', {})
                self.log_test(
                    "JWT Token Validation", 
                    True, 
                    f"Token valid, User: {user_info.get('name', 'Unknown')}", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("JWT Token Validation", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("JWT Token Validation", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_ai_services(self):
        """Test AI services endpoint"""
        response, response_time = self.make_request('GET', '/ai/services')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                services = data.get('data', {}).get('services', [])
                service_count = len(services)
                service_names = [s.get('name', 'Unknown') for s in services[:3]]
                self.log_test(
                    "AI Services Catalog", 
                    True, 
                    f"Found {service_count} services: {', '.join(service_names)}", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("AI Services Catalog", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("AI Services Catalog", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_ai_conversations(self):
        """Test AI conversations endpoint"""
        if not self.auth_token:
            self.log_test("AI Conversations", False, "No auth token available", 0)
            return False
        
        response, response_time = self.make_request('GET', '/ai/conversations')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                conversations = data.get('data', {}).get('conversations', [])
                conv_count = len(conversations)
                self.log_test(
                    "AI Conversations", 
                    True, 
                    f"Retrieved {conv_count} conversations", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("AI Conversations", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("AI Conversations", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_create_ai_conversation(self):
        """Test creating AI conversation"""
        if not self.auth_token:
            self.log_test("Create AI Conversation", False, "No auth token available", 0)
            return False
        
        conversation_data = {
            "title": "Test AI Conversation - Review Request"
        }
        
        response, response_time = self.make_request('POST', '/ai/conversations', conversation_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    conv_info = data.get('data', {}).get('conversation', {})
                    self.log_test(
                        "Create AI Conversation", 
                        True, 
                        f"Created conversation: {conv_info.get('title', 'Unknown')}", 
                        response_time
                    )
                    return True
                else:
                    self.log_test("Create AI Conversation", False, "Success flag is False", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_test("Create AI Conversation", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Create AI Conversation", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_workspace_management(self):
        """Test workspace management endpoints"""
        if not self.auth_token:
            self.log_test("Workspace Management", False, "No auth token available", 0)
            return False
        
        response, response_time = self.make_request('GET', '/workspaces')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                workspaces = data.get('data', {}).get('workspaces', [])
                workspace_count = len(workspaces)
                self.log_test(
                    "Workspace Management", 
                    True, 
                    f"Retrieved {workspace_count} workspaces", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("Workspace Management", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Workspace Management", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_social_media_integrations(self):
        """Test social media integration endpoints"""
        if not self.auth_token:
            self.log_test("Social Media Integrations", False, "No auth token available", 0)
            return False
        
        # Test social media accounts endpoint
        response, response_time = self.make_request('GET', '/social/accounts')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                self.log_test(
                    "Social Media Integrations", 
                    True, 
                    f"Social media accounts endpoint accessible", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("Social Media Integrations", False, "Invalid JSON response", response_time)
                return False
        elif response and response.status_code == 404:
            # Try alternative endpoint
            response, response_time = self.make_request('GET', '/social-media/accounts')
            if response and response.status_code == 200:
                self.log_test("Social Media Integrations", True, "Alternative endpoint working", response_time)
                return True
            else:
                self.log_test("Social Media Integrations", False, "Social media endpoints not found", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Social Media Integrations", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_subscription_system(self):
        """Test subscription system endpoints"""
        # Test subscription plans (public endpoint)
        response, response_time = self.make_request('GET', '/subscription/plans')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                plans = data.get('data', {}).get('plans', [])
                plan_count = len(plans)
                self.log_test(
                    "Subscription System", 
                    True, 
                    f"Retrieved {plan_count} subscription plans", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("Subscription System", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Subscription System", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_google_oauth_integration(self):
        """Test Google OAuth integration"""
        response, response_time = self.make_request('GET', '/auth/google/login')
        
        if response and (response.status_code == 200 or response.status_code == 302):
            self.log_test(
                "Google OAuth Integration", 
                True, 
                f"OAuth endpoint accessible (Status: {response.status_code})", 
                response_time
            )
            return True
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Google OAuth Integration", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_stripe_payment_integration(self):
        """Test Stripe payment integration"""
        if not self.auth_token:
            self.log_test("Stripe Payment Integration", False, "No auth token available", 0)
            return False
        
        # Test payment intent creation
        payment_data = {
            "amount": 2900,  # $29.00
            "currency": "usd",
            "plan": "pro"
        }
        
        response, response_time = self.make_request('POST', '/subscription/create-payment-intent', payment_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if 'client_secret' in data:
                    self.log_test(
                        "Stripe Payment Integration", 
                        True, 
                        "Payment intent created successfully", 
                        response_time
                    )
                    return True
                else:
                    self.log_test("Stripe Payment Integration", False, "No client_secret in response", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_test("Stripe Payment Integration", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Stripe Payment Integration", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_admin_dashboard(self):
        """Test admin dashboard endpoint"""
        if not self.auth_token:
            self.log_test("Admin Dashboard", False, "No auth token available", 0)
            return False
        
        response, response_time = self.make_request('GET', '/admin/dashboard')
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                dashboard_data = data.get('data', {})
                self.log_test(
                    "Admin Dashboard", 
                    True, 
                    f"Dashboard data retrieved successfully", 
                    response_time
                )
                return True
            except json.JSONDecodeError:
                self.log_test("Admin Dashboard", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "timeout"
            self.log_test("Admin Dashboard", False, f"Status code: {status_code}", response_time)
            return False
    
    def test_database_operations(self):
        """Test database operations through API endpoints"""
        if not self.auth_token:
            self.log_test("Database Operations", False, "No auth token available", 0)
            return False
        
        # Test multiple endpoints that require database operations
        endpoints_to_test = [
            ('/bio-sites', 'Bio Sites'),
            ('/ecommerce/dashboard', 'E-commerce'),
            ('/analytics/overview', 'Analytics'),
            ('/notifications', 'Notifications')
        ]
        
        successful_tests = 0
        total_tests = len(endpoints_to_test)
        
        for endpoint, name in endpoints_to_test:
            response, response_time = self.make_request('GET', endpoint)
            if response and response.status_code == 200:
                successful_tests += 1
        
        success_rate = (successful_tests / total_tests) * 100
        self.log_test(
            "Database Operations", 
            successful_tests > 0, 
            f"{successful_tests}/{total_tests} database endpoints working ({success_rate:.1f}%)", 
            0
        )
        return successful_tests > 0
    
    def run_comprehensive_test(self):
        """Run all tests in sequence"""
        print("🚀 STARTING AI INTEGRATION & CORE API TESTING FOR MEWAYZ PLATFORM")
        print("=" * 70)
        
        # Core system tests
        print("\n📋 CORE SYSTEM TESTS")
        print("-" * 30)
        self.test_health_check()
        self.test_admin_login()
        self.test_jwt_token_validation()
        
        # AI Integration tests
        print("\n🤖 AI INTEGRATION TESTS")
        print("-" * 30)
        self.test_ai_services()
        self.test_ai_conversations()
        self.test_create_ai_conversation()
        
        # Core API tests
        print("\n🔧 CORE API TESTS")
        print("-" * 30)
        self.test_workspace_management()
        self.test_admin_dashboard()
        self.test_database_operations()
        
        # Integration tests
        print("\n🔗 INTEGRATION TESTS")
        print("-" * 30)
        self.test_social_media_integrations()
        self.test_subscription_system()
        self.test_google_oauth_integration()
        self.test_stripe_payment_integration()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 70)
        print("📊 TEST SUMMARY")
        print("=" * 70)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests) * 100 if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS:")
            for result in self.test_results:
                if not result['success']:
                    print(f"  - {result['test']}: {result['details']}")
        
        print(f"\n🎯 CONCLUSION:")
        if success_rate >= 80:
            print("✅ EXCELLENT: Backend system is highly functional and production-ready")
        elif success_rate >= 60:
            print("⚠️ GOOD: Backend system is mostly functional with minor issues")
        elif success_rate >= 40:
            print("⚠️ FAIR: Backend system has significant issues that need attention")
        else:
            print("❌ POOR: Backend system has critical issues requiring immediate attention")
        
        return {
            'total_tests': total_tests,
            'passed_tests': passed_tests,
            'failed_tests': failed_tests,
            'success_rate': success_rate,
            'test_results': self.test_results
        }

def main():
    # Get backend URL from environment or use default
    backend_url = "https://ff62574f-1eb9-4423-9341-e402e15fa0aa.preview.emergentagent.com"
    
    print(f"🔍 Testing Mewayz Backend at: {backend_url}")
    
    tester = MewayzAIBackendTester(backend_url)
    tester.run_comprehensive_test()
    summary = tester.generate_summary()
    
    return summary

if __name__ == "__main__":
    summary = main()
    
    # Exit with appropriate code
    if summary['success_rate'] >= 60:
        sys.exit(0)  # Success
    else:
        sys.exit(1)  # Failure