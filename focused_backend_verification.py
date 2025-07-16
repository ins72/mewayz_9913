#!/usr/bin/env python3
"""
Focused Backend Verification for Mewayz Platform
===============================================

This script performs focused testing of critical backend systems
to verify current working status and identify issues.

Focus Areas:
- Authentication System
- Core API Health
- Instagram Management
- Email Marketing
- Payment Processing (Stripe)
- CRM System
- Workspace Setup
- Database Connectivity

Author: Testing Agent
Date: January 2025
"""

import requests
import json
import time
from datetime import datetime

class FocusedBackendVerifier:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.results = []
        
        # Admin credentials for testing
        self.admin_credentials = {
            "email": "admin@example.com",
            "password": "admin123"
        }

    def log_test(self, system: str, endpoint: str, method: str, status: str, details: str = ""):
        """Log test result"""
        result = {
            "system": system,
            "endpoint": endpoint,
            "method": method,
            "status": status,
            "details": details,
            "timestamp": datetime.now().isoformat()
        }
        self.results.append(result)
        
        # Color coding
        color = "\033[92m" if status == "PASS" else "\033[91m" if status == "FAIL" else "\033[93m"
        reset = "\033[0m"
        
        print(f"{color}[{status}]{reset} {system}: {method} {endpoint} - {details}")

    def make_request(self, method: str, endpoint: str, data: dict = None, auth_required: bool = True):
        """Make HTTP request with proper error handling"""
        url = f"{self.api_url}{endpoint}"
        
        headers = {
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
        
        if auth_required and self.auth_token:
            headers["Authorization"] = f"Bearer {self.auth_token}"
        
        try:
            if method.upper() == "GET":
                response = self.session.get(url, headers=headers, timeout=10)
            elif method.upper() == "POST":
                response = self.session.post(url, json=data, headers=headers, timeout=10)
            else:
                return None
                
            return response
            
        except requests.exceptions.RequestException as e:
            return None

    def test_system_health(self):
        """Test basic system health"""
        print("\n" + "="*60)
        print("🏥 TESTING SYSTEM HEALTH")
        print("="*60)
        
        # Test server connectivity
        try:
            response = requests.get(self.base_url, timeout=5)
            if response.status_code == 200:
                self.log_test("System Health", "/", "GET", "PASS", "Laravel server accessible")
            else:
                self.log_test("System Health", "/", "GET", "FAIL", f"Server returned {response.status_code}")
        except:
            self.log_test("System Health", "/", "GET", "FAIL", "Cannot connect to server")
            return False
        
        # Test health endpoint
        response = self.make_request("GET", "/health", auth_required=False)
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success"):
                    self.log_test("System Health", "/health", "GET", "PASS", "Health check successful")
                else:
                    self.log_test("System Health", "/health", "GET", "FAIL", "Health check failed")
            except:
                self.log_test("System Health", "/health", "GET", "FAIL", "Invalid JSON response")
        else:
            self.log_test("System Health", "/health", "GET", "FAIL", "Health endpoint not accessible")
        
        return True

    def test_authentication(self):
        """Test authentication system"""
        print("\n" + "="*60)
        print("🔐 TESTING AUTHENTICATION SYSTEM")
        print("="*60)
        
        # Test login
        response = self.make_request("POST", "/auth/login", 
                                   data=self.admin_credentials, 
                                   auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if "token" in data or "access_token" in data:
                    self.auth_token = data.get("token") or data.get("access_token")
                    self.log_test("Authentication", "/auth/login", "POST", "PASS", "Login successful, token obtained")
                else:
                    self.log_test("Authentication", "/auth/login", "POST", "FAIL", "Login successful but no token")
            except:
                self.log_test("Authentication", "/auth/login", "POST", "FAIL", "Invalid JSON response")
        else:
            self.log_test("Authentication", "/auth/login", "POST", "FAIL", "Login failed")
        
        # Test authenticated endpoint
        if self.auth_token:
            response = self.make_request("GET", "/auth/me")
            if response and response.status_code == 200:
                self.log_test("Authentication", "/auth/me", "GET", "PASS", "User profile accessible")
            else:
                self.log_test("Authentication", "/auth/me", "GET", "FAIL", "User profile not accessible")

    def test_instagram_management(self):
        """Test Instagram management system"""
        print("\n" + "="*60)
        print("📸 TESTING INSTAGRAM MANAGEMENT")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("Instagram Management", "N/A", "N/A", "SKIP", "No authentication token")
            return
        
        # Test accounts endpoint
        response = self.make_request("GET", "/instagram-management/accounts")
        if response and response.status_code == 200:
            self.log_test("Instagram Management", "/instagram-management/accounts", "GET", "PASS", "Accounts endpoint working")
        else:
            self.log_test("Instagram Management", "/instagram-management/accounts", "GET", "FAIL", "Accounts endpoint failed")
        
        # Test posts endpoint
        response = self.make_request("GET", "/instagram-management/posts")
        if response and response.status_code == 200:
            self.log_test("Instagram Management", "/instagram-management/posts", "GET", "PASS", "Posts endpoint working")
        else:
            self.log_test("Instagram Management", "/instagram-management/posts", "GET", "FAIL", "Posts endpoint failed")
        
        # Test analytics endpoint
        response = self.make_request("GET", "/instagram-management/analytics")
        if response and response.status_code == 200:
            self.log_test("Instagram Management", "/instagram-management/analytics", "GET", "PASS", "Analytics endpoint working")
        else:
            self.log_test("Instagram Management", "/instagram-management/analytics", "GET", "FAIL", "Analytics endpoint failed")

    def test_email_marketing(self):
        """Test email marketing system"""
        print("\n" + "="*60)
        print("📧 TESTING EMAIL MARKETING")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("Email Marketing", "N/A", "N/A", "SKIP", "No authentication token")
            return
        
        # Test campaigns endpoint
        response = self.make_request("GET", "/email-marketing/campaigns")
        if response and response.status_code == 200:
            self.log_test("Email Marketing", "/email-marketing/campaigns", "GET", "PASS", "Campaigns endpoint working")
        else:
            self.log_test("Email Marketing", "/email-marketing/campaigns", "GET", "FAIL", "Campaigns endpoint failed")
        
        # Test templates endpoint
        response = self.make_request("GET", "/email-marketing/templates")
        if response and response.status_code == 200:
            self.log_test("Email Marketing", "/email-marketing/templates", "GET", "PASS", "Templates endpoint working")
        else:
            self.log_test("Email Marketing", "/email-marketing/templates", "GET", "FAIL", "Templates endpoint failed")

    def test_payment_processing(self):
        """Test payment processing"""
        print("\n" + "="*60)
        print("💳 TESTING PAYMENT PROCESSING")
        print("="*60)
        
        # Test packages endpoint (public)
        response = self.make_request("GET", "/payments/packages", auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Payment Processing", "/payments/packages", "GET", "PASS", "Packages endpoint working")
        else:
            # Try alternative route
            response = self.make_request("GET", "/stripe/packages", auth_required=False)
            if response and response.status_code == 200:
                self.log_test("Payment Processing", "/stripe/packages", "GET", "PASS", "Stripe packages endpoint working")
            else:
                self.log_test("Payment Processing", "/payments/packages", "GET", "FAIL", "Packages endpoint failed")

    def test_crm_system(self):
        """Test CRM system"""
        print("\n" + "="*60)
        print("🤝 TESTING CRM SYSTEM")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("CRM System", "N/A", "N/A", "SKIP", "No authentication token")
            return
        
        # Test contacts endpoint
        response = self.make_request("GET", "/crm/contacts")
        if response and response.status_code == 200:
            self.log_test("CRM System", "/crm/contacts", "GET", "PASS", "Contacts endpoint working")
        else:
            self.log_test("CRM System", "/crm/contacts", "GET", "FAIL", "Contacts endpoint failed")
        
        # Test leads endpoint
        response = self.make_request("GET", "/crm/leads")
        if response and response.status_code == 200:
            self.log_test("CRM System", "/crm/leads", "GET", "PASS", "Leads endpoint working")
        else:
            self.log_test("CRM System", "/crm/leads", "GET", "FAIL", "Leads endpoint failed")

    def test_workspace_setup(self):
        """Test workspace setup system"""
        print("\n" + "="*60)
        print("🧙‍♂️ TESTING WORKSPACE SETUP")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("Workspace Setup", "N/A", "N/A", "SKIP", "No authentication token")
            return
        
        # Test initial data endpoint
        response = self.make_request("GET", "/workspace-setup/initial-data")
        if response and response.status_code == 200:
            self.log_test("Workspace Setup", "/workspace-setup/initial-data", "GET", "PASS", "Initial data endpoint working")
        else:
            self.log_test("Workspace Setup", "/workspace-setup/initial-data", "GET", "FAIL", "Initial data endpoint failed")
        
        # Test main goals endpoint
        response = self.make_request("GET", "/workspace-setup/main-goals")
        if response and response.status_code == 200:
            self.log_test("Workspace Setup", "/workspace-setup/main-goals", "GET", "PASS", "Main goals endpoint working")
        else:
            self.log_test("Workspace Setup", "/workspace-setup/main-goals", "GET", "FAIL", "Main goals endpoint failed")

    def test_oauth_integration(self):
        """Test OAuth integration"""
        print("\n" + "="*60)
        print("🔗 TESTING OAUTH INTEGRATION")
        print("="*60)
        
        # Test OAuth providers endpoint
        response = self.make_request("GET", "/auth/oauth/providers", auth_required=False)
        if response and response.status_code == 200:
            self.log_test("OAuth Integration", "/auth/oauth/providers", "GET", "PASS", "OAuth providers endpoint working")
        else:
            self.log_test("OAuth Integration", "/auth/oauth/providers", "GET", "FAIL", "OAuth providers endpoint failed")
        
        # Test OAuth status endpoint
        if self.auth_token:
            response = self.make_request("GET", "/oauth/status")
            if response and response.status_code == 200:
                self.log_test("OAuth Integration", "/oauth/status", "GET", "PASS", "OAuth status endpoint working")
            else:
                self.log_test("OAuth Integration", "/oauth/status", "GET", "FAIL", "OAuth status endpoint failed")

    def test_ai_integration(self):
        """Test AI integration"""
        print("\n" + "="*60)
        print("🤖 TESTING AI INTEGRATION")
        print("="*60)
        
        if not self.auth_token:
            self.log_test("AI Integration", "N/A", "N/A", "SKIP", "No authentication token")
            return
        
        # Test AI services endpoint
        response = self.make_request("GET", "/ai/services")
        if response and response.status_code == 200:
            self.log_test("AI Integration", "/ai/services", "GET", "PASS", "AI services endpoint working")
        else:
            self.log_test("AI Integration", "/ai/services", "GET", "FAIL", "AI services endpoint failed")

    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "="*80)
        print("📋 FOCUSED BACKEND VERIFICATION SUMMARY")
        print("="*80)
        
        # Group results by system
        systems = {}
        for result in self.results:
            system = result["system"]
            if system not in systems:
                systems[system] = {"total": 0, "passed": 0, "failed": 0, "skipped": 0}
            
            systems[system]["total"] += 1
            if result["status"] == "PASS":
                systems[system]["passed"] += 1
            elif result["status"] == "FAIL":
                systems[system]["failed"] += 1
            elif result["status"] == "SKIP":
                systems[system]["skipped"] += 1
        
        # Calculate overall stats
        total_tests = len([r for r in self.results if r["status"] != "SKIP"])
        passed_tests = len([r for r in self.results if r["status"] == "PASS"])
        failed_tests = len([r for r in self.results if r["status"] == "FAIL"])
        
        overall_success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 OVERALL RESULTS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests} ({passed_tests/total_tests*100:.1f}%)")
        print(f"   ❌ Failed: {failed_tests} ({failed_tests/total_tests*100:.1f}%)")
        print(f"   🎯 Success Rate: {overall_success_rate:.1f}%")
        
        print(f"\n📈 RESULTS BY SYSTEM:")
        for system, stats in systems.items():
            if stats["total"] > stats["skipped"]:
                testable = stats["total"] - stats["skipped"]
                rate = (stats["passed"] / testable * 100) if testable > 0 else 0
                status_icon = "✅" if rate >= 80 else "⚠️" if rate >= 50 else "❌"
                print(f"   {status_icon} {system}: {stats['passed']}/{testable} ({rate:.1f}%)")
        
        # Show critical failures
        failures = [r for r in self.results if r["status"] == "FAIL"]
        if failures:
            print(f"\n🚨 CRITICAL FAILURES:")
            for failure in failures:
                print(f"   ❌ {failure['system']}: {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        return {
            "overall_success_rate": overall_success_rate,
            "total_tests": total_tests,
            "passed": passed_tests,
            "failed": failed_tests,
            "systems": systems,
            "failures": failures
        }

    def run_verification(self):
        """Run focused backend verification"""
        print("🔍 STARTING FOCUSED BACKEND VERIFICATION")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Run verification tests
        if not self.test_system_health():
            print("\n❌ CRITICAL: System health check failed")
            return False
        
        self.test_authentication()
        self.test_instagram_management()
        self.test_email_marketing()
        self.test_payment_processing()
        self.test_crm_system()
        self.test_workspace_setup()
        self.test_oauth_integration()
        self.test_ai_integration()
        
        # Generate summary
        results = self.generate_summary()
        
        print(f"\n🏁 VERIFICATION COMPLETED")
        print(f"   Overall Success Rate: {results['overall_success_rate']:.1f}%")
        print(f"   Critical Systems Status: {'✅ OPERATIONAL' if results['overall_success_rate'] >= 70 else '❌ ISSUES DETECTED'}")
        
        return results

def main():
    verifier = FocusedBackendVerifier("http://localhost:8001")
    results = verifier.run_verification()
    return results

if __name__ == "__main__":
    main()