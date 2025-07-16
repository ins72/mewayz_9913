#!/usr/bin/env python3
"""
FINAL COMPREHENSIVE VERIFICATION TEST
=====================================

Testing the specific critical fixes mentioned in the review request:

1. Workspace Setup Wizard - ALL STEPS WORKING
2. Instagram Management - ALL ENDPOINTS FIXED  
3. Payment Processing - STRIPE INTEGRATION
4. All other systems verification

Expected Results:
- Overall Success Rate: >95% (up from 57.3%)
- Critical Failures: 0 (down from 12)
- Workspace Setup Wizard: 100% success rate (up from 50%)
- Instagram Management: 100% success rate (up from 27.3%)
- Payment Processing: 100% success rate (up from 66.7%)
"""

import requests
import json
import time
from datetime import datetime

class FinalVerificationTester:
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
            "User-Agent": "Mewayz-Final-Verification/1.0"
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
                    elif "error" in error_json:
                        error_details += f" - {error_json['error'][:100]}"
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
        print("🔐 AUTHENTICATING FOR FINAL VERIFICATION")
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

    def test_workspace_setup_wizard_critical_fixes(self):
        """Test Workspace Setup Wizard - Verify ALL STEPS WORKING as claimed"""
        print("\n" + "="*80)
        print("🧙‍♂️ TESTING WORKSPACE SETUP WIZARD - CRITICAL FIXES VERIFICATION")
        print("Expected: 100% success rate (up from 50%)")
        print("="*80)
        
        if not self.auth_token:
            print("❌ Skipping workspace tests - No authentication token")
            return
        
        # Step 1: Main Goals Selection
        print("\n📋 Step 1: Main Goals Selection")
        self.make_request("GET", "/workspace-setup/main-goals")
        goals_data = {
            "selected_goals": [1, 2, 3],
            "primary_goal": 1,
            "business_type": "digital_marketing_agency",
            "target_audience": "small_businesses"
        }
        self.make_request("POST", "/workspace-setup/main-goals", data=goals_data)
        
        # Step 2: Feature Selection  
        print("\n🎯 Step 2: Feature Selection")
        self.make_request("GET", "/workspace-setup/available-features")
        features_data = {
            "selected_features": [1, 2, 3, 4, 5],
            "subscription_plan": "professional",
            "feature_preferences": ["instagram_management", "email_marketing"]
        }
        self.make_request("POST", "/workspace-setup/feature-selection", data=features_data)
        
        # Step 3: Team Setup (existing functionality)
        print("\n👥 Step 3: Team Setup")
        team_data = {
            "team_size": "5-10",
            "invitations": [
                {"email": "team1@mewayz.com", "role": "admin"},
                {"email": "team2@mewayz.com", "role": "member"}
            ]
        }
        self.make_request("POST", "/workspace-setup/team-setup", data=team_data)
        
        # Step 4: Subscription Selection
        print("\n💳 Step 4: Subscription Selection")
        self.make_request("GET", "/workspace-setup/subscription-plans")
        subscription_data = {
            "subscription_plan": "professional",
            "billing_cycle": "monthly",
            "payment_method": "stripe",
            "auto_renewal": True
        }
        self.make_request("POST", "/workspace-setup/subscription-selection", data=subscription_data)
        
        # Step 5: Branding Configuration
        print("\n🎨 Step 5: Branding Configuration")
        branding_data = {
            "logo": "https://example.com/logo.png",
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981",
            "accent_color": "#F59E0B",
            "company_name": "Mewayz Digital Agency",
            "brand_voice": "professional",
            "white_label_enabled": True,
            "custom_domain": "agency.mewayz.com"
        }
        self.make_request("POST", "/workspace-setup/branding-configuration", data=branding_data)
        
        # Step 6: Final Review (existing functionality)
        print("\n✅ Step 6: Final Review")
        self.make_request("POST", "/workspace-setup/complete")
        self.make_request("GET", "/workspace-setup/summary")
        self.make_request("GET", "/workspace-setup/status")

    def test_instagram_management_critical_fixes(self):
        """Test Instagram Management - Verify ALL ENDPOINTS FIXED as claimed"""
        print("\n" + "="*80)
        print("📸 TESTING INSTAGRAM MANAGEMENT - CRITICAL FIXES VERIFICATION")
        print("Expected: 100% success rate (up from 27.3%)")
        print("="*80)
        
        if not self.auth_token:
            print("❌ Skipping Instagram tests - No authentication token")
            return
        
        # Account creation working
        print("\n👤 Account Management")
        self.make_request("GET", "/instagram-management/accounts")
        account_data = {
            "username": "mewayz_official",
            "account_type": "business",
            "display_name": "Mewayz Official",
            "bio": "All-in-one business platform for digital success",
            "followers_count": 15000,
            "following_count": 500,
            "posts_count": 250,
            "is_connected": True,
            "access_token": "test_access_token_123"
        }
        self.make_request("POST", "/instagram-management/accounts", data=account_data)
        
        # Post creation working
        print("\n📝 Post Management")
        self.make_request("GET", "/instagram-management/posts")
        post_data = {
            "title": "New Feature Launch",
            "caption": "Exciting new features coming to Mewayz! 🚀 #DigitalMarketing #SocialMedia #BusinessGrowth",
            "media_urls": ["https://example.com/image1.jpg", "https://example.com/image2.jpg"],
            "hashtags": ["#DigitalMarketing", "#SocialMedia", "#BusinessGrowth"],
            "post_type": "feed",  # feed/story/reel
            "scheduled_at": "2025-01-15 10:00:00"
        }
        self.make_request("POST", "/instagram-management/posts", data=post_data)
        
        # Analytics endpoints working
        print("\n📊 Analytics")
        self.make_request("GET", "/instagram-management/analytics")
        self.make_request("GET", "/instagram-management/hashtag-research")

    def test_payment_processing_stripe_integration(self):
        """Test Payment Processing - Verify STRIPE INTEGRATION working"""
        print("\n" + "="*80)
        print("💳 TESTING PAYMENT PROCESSING - STRIPE INTEGRATION VERIFICATION")
        print("Expected: 100% success rate (up from 66.7%)")
        print("="*80)
        
        # Packages endpoint working (public)
        print("\n📦 Payment Packages")
        self.make_request("GET", "/payments/packages", auth_required=False)
        
        if not self.auth_token:
            print("❌ Skipping authenticated payment tests - No authentication token")
            return
        
        # Checkout session creation working (test mode)
        print("\n🛒 Checkout Session Creation")
        checkout_data = {
            "package": "professional",  # or package_id
            "billing_cycle": "monthly",
            "success_url": "https://mewayz.com/success",
            "cancel_url": "https://mewayz.com/cancel"
        }
        session_response = self.make_request("POST", "/payments/checkout/session", data=checkout_data)
        
        # Payment status verification
        print("\n✅ Payment Status Verification")
        if session_response and session_response.status_code in [200, 201]:
            try:
                session_data = session_response.json()
                if "session_id" in session_data:
                    session_id = session_data["session_id"]
                    self.make_request("GET", f"/payments/checkout/status/{session_id}")
            except json.JSONDecodeError:
                pass

    def test_all_other_systems_verification(self):
        """Test all other systems to ensure they're still working"""
        print("\n" + "="*80)
        print("🔧 TESTING ALL OTHER SYSTEMS - VERIFICATION")
        print("Expected: All systems operational")
        print("="*80)
        
        if not self.auth_token:
            print("❌ Skipping system tests - No authentication token")
            return
        
        # Authentication system working
        print("\n🔐 Authentication System")
        self.make_request("GET", "/auth/me")
        
        # Email Marketing Hub working
        print("\n📧 Email Marketing Hub")
        self.make_request("GET", "/email-marketing/campaigns")
        self.make_request("GET", "/email-marketing/templates")
        
        # CRM System working
        print("\n🤝 CRM System")
        self.make_request("GET", "/crm/contacts")
        
        # Team Management working
        print("\n👥 Team Management")
        self.make_request("GET", "/team")
        
        # Analytics Dashboard working
        print("\n📊 Analytics Dashboard")
        self.make_request("GET", "/analytics")
        
        # AI Integration working
        print("\n🤖 AI Integration")
        # Note: These endpoints might need specific parameters
        # self.make_request("GET", "/ai/services")
        
        # OAuth Integration working
        print("\n🔗 OAuth Integration")
        # Note: OAuth endpoints might need specific handling
        # self.make_request("GET", "/auth/oauth/providers", auth_required=False)

    def generate_final_verification_report(self):
        """Generate final verification report"""
        print("\n" + "="*80)
        print("📋 FINAL COMPREHENSIVE VERIFICATION REPORT")
        print("="*80)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r["status"] == "PASS"])
        failed_tests = len([r for r in self.test_results if r["status"] == "FAIL"])
        error_tests = len([r for r in self.test_results if r["status"] == "ERROR"])
        
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"\n📊 FINAL VERIFICATION STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests} ({passed_tests/total_tests*100:.1f}%)")
        print(f"   ❌ Failed: {failed_tests} ({failed_tests/total_tests*100:.1f}%)")
        print(f"   ⚠️  Errors: {error_tests} ({error_tests/total_tests*100:.1f}%)")
        print(f"   🎯 Overall Success Rate: {success_rate:.1f}%")
        
        # Group results by critical system
        systems = {
            "Workspace Setup Wizard": [r for r in self.test_results if "/workspace-setup" in r["endpoint"]],
            "Instagram Management": [r for r in self.test_results if "/instagram" in r["endpoint"]],
            "Payment Processing": [r for r in self.test_results if "/payments" in r["endpoint"]],
            "Authentication": [r for r in self.test_results if "/auth" in r["endpoint"]],
            "Email Marketing": [r for r in self.test_results if "/email-marketing" in r["endpoint"]],
            "CRM System": [r for r in self.test_results if "/crm" in r["endpoint"]],
            "Team Management": [r for r in self.test_results if "/team" in r["endpoint"]],
            "Analytics": [r for r in self.test_results if "/analytics" in r["endpoint"]]
        }
        
        print(f"\n📈 RESULTS BY CRITICAL SYSTEM:")
        for system, results in systems.items():
            if results:
                passed = len([r for r in results if r["status"] == "PASS"])
                total = len(results)
                rate = (passed / total * 100) if total > 0 else 0
                status_icon = "✅" if rate >= 95 else "⚠️" if rate >= 80 else "❌"
                print(f"   {status_icon} {system}: {passed}/{total} ({rate:.1f}%)")
        
        # Show critical failures (5xx errors)
        critical_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] >= 500]
        critical_count = len(critical_failures)
        
        print(f"\n🚨 CRITICAL FAILURES COUNT: {critical_count}")
        if critical_failures:
            print("   Critical 5xx errors found:")
            for failure in critical_failures[:10]:  # Show first 10
                print(f"   ❌ {failure['method']} {failure['endpoint']} - {failure['details']}")
        else:
            print("   ✅ No critical 5xx errors found!")
        
        # Expected vs Actual Results
        print(f"\n🎯 EXPECTED VS ACTUAL RESULTS:")
        print(f"   Expected Overall Success Rate: >95%")
        print(f"   Actual Overall Success Rate: {success_rate:.1f}%")
        print(f"   Expected Critical Failures: 0")
        print(f"   Actual Critical Failures: {critical_count}")
        
        # Workspace Setup specific
        workspace_results = systems.get("Workspace Setup Wizard", [])
        if workspace_results:
            workspace_passed = len([r for r in workspace_results if r["status"] == "PASS"])
            workspace_total = len(workspace_results)
            workspace_rate = (workspace_passed / workspace_total * 100) if workspace_total > 0 else 0
            print(f"   Expected Workspace Setup Success: 100%")
            print(f"   Actual Workspace Setup Success: {workspace_rate:.1f}%")
        
        # Instagram Management specific
        instagram_results = systems.get("Instagram Management", [])
        if instagram_results:
            instagram_passed = len([r for r in instagram_results if r["status"] == "PASS"])
            instagram_total = len(instagram_results)
            instagram_rate = (instagram_passed / instagram_total * 100) if instagram_total > 0 else 0
            print(f"   Expected Instagram Management Success: 100%")
            print(f"   Actual Instagram Management Success: {instagram_rate:.1f}%")
        
        # Payment Processing specific
        payment_results = systems.get("Payment Processing", [])
        if payment_results:
            payment_passed = len([r for r in payment_results if r["status"] == "PASS"])
            payment_total = len(payment_results)
            payment_rate = (payment_passed / payment_total * 100) if payment_total > 0 else 0
            print(f"   Expected Payment Processing Success: 100%")
            print(f"   Actual Payment Processing Success: {payment_rate:.1f}%")
        
        # Final assessment
        print(f"\n🏆 FINAL ASSESSMENT:")
        if success_rate >= 95 and critical_count == 0:
            print("   ✅ PLATFORM IS PRODUCTION READY - All critical fixes verified!")
            print("   ✅ Platform meets >95% success rate requirement")
            print("   ✅ Zero critical failures achieved")
        elif success_rate >= 80:
            print("   ⚠️ PLATFORM NEEDS MINOR FIXES - Most systems operational")
            print(f"   ⚠️ Success rate {success_rate:.1f}% is below target >95%")
            if critical_count > 0:
                print(f"   ⚠️ {critical_count} critical failures need attention")
        else:
            print("   ❌ PLATFORM NOT PRODUCTION READY - Major issues remain")
            print(f"   ❌ Success rate {success_rate:.1f}% is significantly below target")
            print(f"   ❌ {critical_count} critical failures require immediate attention")
        
        return {
            "success_rate": success_rate,
            "total_tests": total_tests,
            "passed": passed_tests,
            "failed": failed_tests,
            "critical_failures": critical_count,
            "systems": {system: {"passed": len([r for r in results if r["status"] == "PASS"]), 
                               "total": len(results)} for system, results in systems.items() if results},
            "production_ready": success_rate >= 95 and critical_count == 0
        }

    def run_final_verification(self):
        """Run final comprehensive verification"""
        print("🎯 STARTING FINAL COMPREHENSIVE VERIFICATION")
        print("=" * 80)
        print("Testing all critical fixes mentioned in review request:")
        print("1. ✅ WORKSPACE SETUP WIZARD - ALL STEPS WORKING")
        print("2. ✅ INSTAGRAM MANAGEMENT - ALL ENDPOINTS FIXED")
        print("3. ✅ PAYMENT PROCESSING - STRIPE INTEGRATION")
        print("4. ✅ ALL OTHER SYSTEMS")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate():
            print("\n❌ CRITICAL: Authentication failed - Cannot test authenticated endpoints")
            return False
        
        # Run verification tests
        try:
            self.test_workspace_setup_wizard_critical_fixes()
            time.sleep(2)  # Brief pause between test suites
            self.test_instagram_management_critical_fixes()
            time.sleep(2)
            self.test_payment_processing_stripe_integration()
            time.sleep(2)
            self.test_all_other_systems_verification()
            
        except KeyboardInterrupt:
            print("\n⚠️ Testing interrupted by user")
        except Exception as e:
            print(f"\n❌ Unexpected error during testing: {str(e)}")
        
        # Generate final verification report
        results = self.generate_final_verification_report()
        
        print(f"\n🏁 FINAL COMPREHENSIVE VERIFICATION COMPLETED")
        print(f"   Overall Success Rate: {results['success_rate']:.1f}%")
        print(f"   Tests Passed: {results['passed']}/{results['total_tests']}")
        print(f"   Critical Failures: {results['critical_failures']}")
        print(f"   Production Ready: {'✅ YES' if results['production_ready'] else '❌ NO'}")
        
        return results

def main():
    """Main function to run final verification"""
    tester = FinalVerificationTester()
    results = tester.run_final_verification()
    
    # Save results
    with open('/app/final_verification_results.json', 'w') as f:
        json.dump(results, f, indent=2)
    
    return results

if __name__ == "__main__":
    main()