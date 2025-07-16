#!/usr/bin/env python3
"""
Focused Backend Testing for Critical Issues
===========================================

Testing the critical issues identified in test_result.md:
1. CRM System (0% success rate with PHP redeclaration errors)
2. Instagram Management (18.2% success rate with 500 errors)
3. Workspace Setup Wizard (50% success rate with POST endpoint failures)
"""

import requests
import json
import time
from datetime import datetime

class FocusedBackendTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
        # Admin credentials
        self.admin_credentials = {
            "email": "admin@example.com",
            "password": "admin123"
        }

    def log_result(self, endpoint: str, method: str, status: str, 
                   response_time: float, details: str = "", 
                   status_code: int = 0):
        """Log test result"""
        result = {
            "timestamp": datetime.now().isoformat(),
            "endpoint": endpoint,
            "method": method,
            "status": status,
            "response_time_ms": round(response_time * 1000, 2),
            "status_code": status_code,
            "details": details
        }
        self.test_results.append(result)
        
        # Color coding for console output
        color = "\033[92m" if status == "PASS" else "\033[91m" if status == "FAIL" else "\033[93m"
        reset = "\033[0m"
        
        print(f"{color}[{status}]{reset} {method} {endpoint} ({result['response_time_ms']}ms) - {details}")

    def make_request(self, method: str, endpoint: str, data: dict = None, 
                    headers: dict = None, auth_required: bool = True) -> requests.Response:
        """Make HTTP request with proper headers and authentication"""
        url = f"{self.api_url}{endpoint}"
        
        # Default headers
        request_headers = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "User-Agent": "Mewayz-Focused-Tester/1.0"
        }
        
        # Add authentication if required and available
        if auth_required and self.auth_token:
            request_headers["Authorization"] = f"Bearer {self.auth_token}"
        
        # Merge with custom headers
        if headers:
            request_headers.update(headers)
        
        # Make request
        start_time = time.time()
        try:
            if method.upper() == "GET":
                response = self.session.get(url, headers=request_headers, timeout=30)
            elif method.upper() == "POST":
                response = self.session.post(url, json=data, headers=request_headers, timeout=30)
            elif method.upper() == "PUT":
                response = self.session.put(url, json=data, headers=request_headers, timeout=30)
            elif method.upper() == "DELETE":
                response = self.session.delete(url, headers=request_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            response_time = time.time() - start_time
            
            # Log the result
            if response.status_code < 400:
                self.log_result(endpoint, method.upper(), "PASS", response_time, 
                              f"Status: {response.status_code}", response.status_code)
            else:
                error_details = f"Status: {response.status_code}"
                try:
                    error_json = response.json()
                    if "message" in error_json:
                        error_details += f" - {error_json['message'][:100]}"
                except:
                    error_details += f" - {response.text[:100]}"
                
                self.log_result(endpoint, method.upper(), "FAIL", response_time, 
                              error_details, response.status_code)
            
            return response
            
        except requests.exceptions.RequestException as e:
            response_time = time.time() - start_time
            self.log_result(endpoint, method.upper(), "ERROR", response_time, 
                          f"Request failed: {str(e)}")
            return None

    def authenticate(self):
        """Authenticate and get token"""
        print("\n" + "="*60)
        print("🔐 AUTHENTICATING")
        print("="*60)
        
        response = self.make_request("POST", "/auth/login", 
                                   data=self.admin_credentials, 
                                   auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if "token" in data or "access_token" in data:
                    self.auth_token = data.get("token") or data.get("access_token")
                    print(f"✅ Authentication successful - Token obtained")
                    return True
                else:
                    print(f"⚠️ Login successful but no token in response")
                    return False
            except json.JSONDecodeError:
                print(f"⚠️ Login response is not valid JSON")
                return False
        else:
            print(f"❌ Authentication failed")
            return False

    def test_crm_system(self):
        """Test CRM system - Focus on PHP redeclaration errors"""
        print("\n" + "="*60)
        print("🤝 TESTING CRM SYSTEM (CRITICAL ISSUE)")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping CRM tests - No authentication token")
            return
        
        # Test all CRM endpoints to identify redeclaration issues
        crm_endpoints = [
            ("/crm/contacts", "GET"),
            ("/crm/contacts", "POST", {
                "name": "Michael Chen",
                "email": "michael.chen@techstartup.com",
                "phone": "+1-555-0123",
                "company": "Tech Startup Inc",
                "position": "CEO"
            }),
            ("/crm/leads", "GET"),
            ("/crm/leads", "POST", {
                "name": "Sarah Wilson",
                "email": "sarah.wilson@startup.com",
                "phone": "+1-555-0456",
                "company": "Startup Co",
                "status": "new",
                "source": "website"
            }),
            ("/crm/ai-lead-scoring", "GET"),
            ("/crm/advanced-pipeline-management", "GET"),
            ("/crm/predictive-analytics", "GET")
        ]
        
        for endpoint_data in crm_endpoints:
            if len(endpoint_data) == 2:
                endpoint, method = endpoint_data
                self.make_request(method, endpoint)
            else:
                endpoint, method, data = endpoint_data
                self.make_request(method, endpoint, data=data)

    def test_instagram_management(self):
        """Test Instagram management - Focus on 500 errors"""
        print("\n" + "="*60)
        print("📸 TESTING INSTAGRAM MANAGEMENT (CRITICAL ISSUE)")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping Instagram tests - No authentication token")
            return
        
        # Test Instagram endpoints that were failing
        instagram_endpoints = [
            ("/instagram-management/accounts", "GET"),
            ("/instagram-management/accounts", "POST", {
                "username": "mewayz_official",
                "account_type": "business",
                "followers_count": 15000
            }),
            ("/instagram-management/posts", "GET"),
            ("/instagram-management/posts", "POST", {
                "caption": "Exciting new features coming to Mewayz! 🚀 #DigitalMarketing #SocialMedia #BusinessGrowth",
                "media_url": "https://example.com/image.jpg",
                "hashtags": ["#DigitalMarketing", "#SocialMedia", "#BusinessGrowth"],
                "scheduled_at": "2025-01-15 10:00:00"
            }),
            ("/instagram-management/hashtag-research", "GET"),
            ("/instagram-management/analytics", "GET"),
            ("/instagram/competitor-analysis", "GET"),
            ("/instagram/hashtag-analysis", "GET"),
            ("/instagram/analytics", "GET"),
            ("/instagram/audience-intelligence", "GET"),
            ("/instagram/content-suggestions", "GET")
        ]
        
        for endpoint_data in instagram_endpoints:
            if len(endpoint_data) == 2:
                endpoint, method = endpoint_data
                self.make_request(method, endpoint)
            else:
                endpoint, method, data = endpoint_data
                self.make_request(method, endpoint, data=data)

    def test_workspace_setup_wizard(self):
        """Test Workspace Setup Wizard - Focus on POST endpoint failures"""
        print("\n" + "="*60)
        print("🧙‍♂️ TESTING WORKSPACE SETUP WIZARD (CRITICAL ISSUE)")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping workspace tests - No authentication token")
            return
        
        # Test all 6 steps of the workspace setup wizard
        # Step 1: Get initial data
        self.make_request("GET", "/workspace-setup/initial-data")
        
        # Step 2: Main goals (GET and POST)
        self.make_request("GET", "/workspace-setup/main-goals")
        self.make_request("POST", "/workspace-setup/main-goals", data={
            "goals": [1, 2, 3],
            "primary_goal": 1
        })
        
        # Step 3: Feature selection (GET and POST)
        self.make_request("GET", "/workspace-setup/available-features")
        self.make_request("POST", "/workspace-setup/feature-selection", data={
            "features": [1, 2, 3, 4, 5],
            "priority_features": [1, 2]
        })
        
        # Step 4: Team setup (POST)
        self.make_request("POST", "/workspace-setup/team-setup", data={
            "team_size": "5-10",
            "invitations": [
                {"email": "team1@mewayz.com", "role": "admin"},
                {"email": "team2@mewayz.com", "role": "member"}
            ]
        })
        
        # Step 5: Subscription selection (GET and POST)
        self.make_request("GET", "/workspace-setup/subscription-plans")
        self.make_request("POST", "/workspace-setup/subscription-selection", data={
            "plan_id": 2,
            "billing_cycle": "monthly"
        })
        
        # Step 6: Branding configuration (POST)
        self.make_request("POST", "/workspace-setup/branding-configuration", data={
            "company_name": "Mewayz Digital Agency",
            "logo_url": "https://example.com/logo.png",
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981"
        })
        
        # Complete setup
        self.make_request("POST", "/workspace-setup/complete")
        
        # Get setup summary and status
        self.make_request("GET", "/workspace-setup/summary")
        self.make_request("GET", "/workspace-setup/status")

    def generate_focused_report(self):
        """Generate focused test report"""
        print("\n" + "="*80)
        print("📋 FOCUSED BACKEND TESTING REPORT")
        print("="*80)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r["status"] == "PASS"])
        failed_tests = len([r for r in self.test_results if r["status"] == "FAIL"])
        error_tests = len([r for r in self.test_results if r["status"] == "ERROR"])
        
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 OVERALL STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests} ({passed_tests/total_tests*100:.1f}%)")
        print(f"   ❌ Failed: {failed_tests} ({failed_tests/total_tests*100:.1f}%)")
        print(f"   ⚠️  Errors: {error_tests} ({error_tests/total_tests*100:.1f}%)")
        print(f"   🎯 Success Rate: {success_rate:.1f}%")
        
        # Group results by system
        systems = {
            "CRM System": [r for r in self.test_results if "/crm" in r["endpoint"]],
            "Instagram Management": [r for r in self.test_results if "/instagram" in r["endpoint"]],
            "Workspace Setup": [r for r in self.test_results if "/workspace-setup" in r["endpoint"]],
            "Authentication": [r for r in self.test_results if "/auth" in r["endpoint"]]
        }
        
        print(f"\n📈 RESULTS BY CRITICAL SYSTEM:")
        for system, results in systems.items():
            if results:
                passed = len([r for r in results if r["status"] == "PASS"])
                total = len(results)
                rate = (passed / total * 100) if total > 0 else 0
                status_icon = "✅" if rate >= 80 else "⚠️" if rate >= 50 else "❌"
                print(f"   {status_icon} {system}: {passed}/{total} ({rate:.1f}%)")
        
        # Show critical failures (5xx errors)
        critical_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] >= 500]
        if critical_failures:
            print(f"\n🚨 CRITICAL FAILURES (5xx errors):")
            for failure in critical_failures:
                print(f"   ❌ {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        # Show authentication issues
        auth_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] in [401, 403]]
        if auth_failures:
            print(f"\n🔐 AUTHENTICATION ISSUES:")
            for failure in auth_failures:
                print(f"   🔒 {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        return {
            "success_rate": success_rate,
            "total_tests": total_tests,
            "passed": passed_tests,
            "failed": failed_tests,
            "critical_failures": len(critical_failures),
            "systems": {system: {"passed": len([r for r in results if r["status"] == "PASS"]), 
                               "total": len(results)} for system, results in systems.items() if results}
        }

    def run_focused_test(self):
        """Run focused tests on critical issues"""
        print("🎯 STARTING FOCUSED BACKEND TESTING FOR CRITICAL ISSUES")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate():
            print("\n❌ CRITICAL: Authentication failed - Cannot test authenticated endpoints")
            return False
        
        # Run focused tests on critical systems
        try:
            self.test_crm_system()
            self.test_instagram_management()
            self.test_workspace_setup_wizard()
            
        except KeyboardInterrupt:
            print("\n⚠️ Testing interrupted by user")
        except Exception as e:
            print(f"\n❌ Unexpected error during testing: {str(e)}")
        
        # Generate focused report
        results = self.generate_focused_report()
        
        print(f"\n🏁 FOCUSED TESTING COMPLETED")
        print(f"   Overall Success Rate: {results['success_rate']:.1f}%")
        print(f"   Tests Passed: {results['passed']}/{results['total_tests']}")
        
        if results['critical_failures'] > 0:
            print(f"   🚨 Critical Failures: {results['critical_failures']}")
        
        return results

def main():
    """Main function to run focused backend tests"""
    tester = FocusedBackendTester()
    results = tester.run_focused_test()
    
    # Save results
    with open('/app/focused_test_results.json', 'w') as f:
        json.dump(results, f, indent=2)
    
    return results

if __name__ == "__main__":
    main()