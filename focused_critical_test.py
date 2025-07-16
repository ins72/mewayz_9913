#!/usr/bin/env python3
"""
Focused Critical Testing for Mewayz Platform
============================================

This script focuses on testing the specific areas mentioned in the review request:
1. CRM System - Should now have much higher success rate than previous 0-28.6%
2. Instagram Management - Should fix the 500 errors for account creation
3. Workspace Setup Wizard - POST endpoints may still need validation fixes

Author: Testing Agent
Date: January 2025
"""

import requests
import json
import time
import sys
from datetime import datetime
from typing import Dict, List, Optional, Any

class FocusedCriticalTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.admin_credentials = {
            "email": "admin@example.com",
            "password": "admin123"
        }
        
        # Test data for critical areas
        self.test_data = {
            "crm_contact": {
                "name": "Emily Rodriguez",
                "email": "emily.rodriguez@techcorp.com",
                "phone": "+1-555-0199",
                "company": "TechCorp Solutions",
                "position": "Marketing Director",
                "tags": ["high-value", "decision-maker"],
                "notes": "Interested in enterprise package"
            },
            "crm_lead": {
                "name": "David Kim",
                "email": "david.kim@startup.io",
                "phone": "+1-555-0188",
                "company": "Startup.io",
                "position": "CEO",
                "status": "hot",
                "source": "website",
                "notes": "Ready to purchase within 30 days"
            },
            "instagram_account": {
                "username": "mewayz_business",
                "display_name": "Mewayz Business Solutions",
                "profile_picture_url": "https://example.com/profile.jpg",
                "bio": "Your all-in-one business platform for digital success",
                "is_active": True
            },
            "instagram_post": {
                "caption": "Transform your business with Mewayz! 🚀 Our all-in-one platform helps you manage social media, email marketing, CRM, and more. #BusinessGrowth #DigitalMarketing #Productivity",
                "media_urls": ["https://example.com/business-post.jpg"],
                "hashtags": ["#BusinessGrowth", "#DigitalMarketing", "#Productivity", "#SocialMedia"],
                "post_type": "photo",
                "scheduled_at": "2025-01-17 14:00:00"
            },
            "workspace_goals": {
                "goals": [1, 2, 3],
                "primary_goal": 1
            },
            "workspace_features": {
                "features": [1, 2, 3, 4, 5],
                "priority_features": [1, 2]
            },
            "workspace_subscription": {
                "plan_id": 2,
                "billing_cycle": "monthly"
            },
            "workspace_branding": {
                "company_name": "Mewayz Digital Solutions",
                "logo_url": "https://example.com/logo.png",
                "primary_color": "#3B82F6",
                "secondary_color": "#10B981"
            }
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

    def make_request(self, method: str, endpoint: str, data: Dict = None, 
                    headers: Dict = None, auth_required: bool = True) -> requests.Response:
        """Make HTTP request with proper headers and authentication"""
        url = f"{self.api_url}{endpoint}"
        
        # Default headers
        request_headers = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "User-Agent": "Mewayz-Critical-Tester/1.0"
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
                error_detail = f"Status: {response.status_code}"
                try:
                    error_data = response.json()
                    if 'message' in error_data:
                        error_detail += f" - {error_data['message'][:100]}"
                    elif 'error' in error_data:
                        error_detail += f" - {error_data['error'][:100]}"
                except:
                    error_detail += f" - {response.text[:100]}"
                
                self.log_result(endpoint, method.upper(), "FAIL", response_time, 
                              error_detail, response.status_code)
            
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

    def test_crm_system_critical(self):
        """Test CRM system with focus on fixed issues"""
        print("\n" + "="*60)
        print("🤝 TESTING CRM SYSTEM - CRITICAL FIXES")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping CRM tests - No authentication token")
            return
        
        # Test basic contact operations
        print("\n📋 Testing Contact Management:")
        self.make_request("GET", "/crm/contacts")
        contact_response = self.make_request("POST", "/crm/contacts", 
                                           data=self.test_data["crm_contact"])
        
        # Test lead operations
        print("\n🎯 Testing Lead Management:")
        self.make_request("GET", "/crm/leads")
        lead_response = self.make_request("POST", "/crm/leads", 
                                        data=self.test_data["crm_lead"])
        
        # Test advanced CRM features that were previously failing
        print("\n🧠 Testing Advanced CRM Features:")
        
        # Test AI lead scoring with proper parameters
        ai_scoring_data = {
            "scoring_model": "standard",
            "scoring_factors": ["demographic", "behavioral", "engagement"],
            "minimum_score": 50,
            "include_predictions": True,
            "include_recommendations": True
        }
        self.make_request("GET", "/crm/ai-lead-scoring", data=ai_scoring_data)
        
        # Test advanced pipeline management with proper parameters
        pipeline_data = {
            "date_range": "last_30_days",
            "include_forecasting": True,
            "include_bottleneck_analysis": True,
            "include_win_loss_analysis": True,
            "include_ai_insights": True
        }
        self.make_request("GET", "/crm/advanced-pipeline-management", data=pipeline_data)
        
        # Test predictive analytics with proper parameters
        predictive_data = {
            "prediction_type": "comprehensive",
            "time_horizon": "90_days",
            "include_churn_prediction": True,
            "include_conversion_probability": True,
            "include_lifetime_value": True,
            "confidence_threshold": 70
        }
        self.make_request("GET", "/crm/predictive-analytics", data=predictive_data)
        
        # Test contact operations if contact was created
        if contact_response and contact_response.status_code in [200, 201]:
            try:
                contact_data = contact_response.json()
                if "data" in contact_data and "id" in contact_data["data"]:
                    contact_id = contact_data["data"]["id"]
                    print(f"\n🔄 Testing Contact Operations (ID: {contact_id}):")
                    self.make_request("GET", f"/crm/contacts/{contact_id}")
                    
                    # Test update
                    update_data = self.test_data["crm_contact"].copy()
                    update_data["phone"] = "+1-555-9999"
                    update_data["notes"] = "Updated contact information"
                    self.make_request("PUT", f"/crm/contacts/{contact_id}", data=update_data)
                    
                    # Test delete
                    self.make_request("DELETE", f"/crm/contacts/{contact_id}")
            except (json.JSONDecodeError, KeyError):
                print("⚠️ Could not extract contact ID from response")

    def test_instagram_management_critical(self):
        """Test Instagram management with focus on fixed issues"""
        print("\n" + "="*60)
        print("📸 TESTING INSTAGRAM MANAGEMENT - CRITICAL FIXES")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping Instagram tests - No authentication token")
            return
        
        # Test account management (previously failing with 500 errors)
        print("\n👤 Testing Account Management:")
        self.make_request("GET", "/instagram-management/accounts")
        account_response = self.make_request("POST", "/instagram-management/accounts", 
                                           data=self.test_data["instagram_account"])
        
        # Test post management (previously failing with 500 errors)
        print("\n📝 Testing Post Management:")
        self.make_request("GET", "/instagram-management/posts")
        post_response = self.make_request("POST", "/instagram-management/posts", 
                                        data=self.test_data["instagram_post"])
        
        # Test hashtag research (previously failing with 500 errors)
        print("\n🏷️ Testing Hashtag Research:")
        self.make_request("GET", "/instagram-management/hashtag-research?keyword=business&limit=20")
        
        # Test analytics
        print("\n📊 Testing Analytics:")
        self.make_request("GET", "/instagram-management/analytics")
        
        # Test Instagram Intelligence Engine with proper parameters
        print("\n🧠 Testing Instagram Intelligence Engine:")
        
        # Test competitor analysis with parameters
        self.make_request("GET", "/instagram/competitor-analysis?username=competitor_account&analysis_type=engagement")
        
        # Test hashtag analysis with parameters
        self.make_request("GET", "/instagram/hashtag-analysis?hashtag=businessgrowth&analysis_depth=detailed")
        
        # Test audience intelligence with parameters
        self.make_request("GET", "/instagram/audience-intelligence?account_id=test_account_id&analysis_type=demographics")
        
        # Test post operations if post was created
        if post_response and post_response.status_code in [200, 201]:
            try:
                post_data = post_response.json()
                if "post" in post_data and "id" in post_data["post"]:
                    post_id = post_data["post"]["id"]
                    print(f"\n🔄 Testing Post Operations (ID: {post_id}):")
                    
                    # Test update
                    update_data = self.test_data["instagram_post"].copy()
                    update_data["caption"] = "Updated: Transform your business with Mewayz! 🚀✨"
                    self.make_request("PUT", f"/instagram-management/posts/{post_id}", 
                                    data=update_data)
                    
                    # Test delete
                    self.make_request("DELETE", f"/instagram-management/posts/{post_id}")
            except (json.JSONDecodeError, KeyError):
                print("⚠️ Could not extract post ID from response")

    def test_workspace_setup_critical(self):
        """Test Workspace Setup Wizard with focus on POST endpoint fixes"""
        print("\n" + "="*60)
        print("🧙‍♂️ TESTING WORKSPACE SETUP WIZARD - CRITICAL POST FIXES")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping workspace tests - No authentication token")
            return
        
        # Test GET endpoints (these were working)
        print("\n📋 Testing GET Endpoints:")
        self.make_request("GET", "/workspace-setup/initial-data")
        self.make_request("GET", "/workspace-setup/main-goals")
        self.make_request("GET", "/workspace-setup/subscription-plans")
        self.make_request("GET", "/workspace-setup/summary")
        self.make_request("GET", "/workspace-setup/status")
        
        # Test critical POST endpoints (these were failing with 500 errors)
        print("\n🚨 Testing CRITICAL POST Endpoints:")
        
        # Step 2: Main goals (previously failing)
        print("Step 2: Main Goals")
        self.make_request("POST", "/workspace-setup/main-goals", 
                         data=self.test_data["workspace_goals"])
        
        # Step 3: Feature selection (previously failing)
        print("Step 3: Feature Selection")
        self.make_request("POST", "/workspace-setup/feature-selection", 
                         data=self.test_data["workspace_features"])
        
        # Step 4: Team setup (this was working)
        print("Step 4: Team Setup")
        team_data = {
            "team_size": "5-10",
            "invitations": [
                {"email": "team1@mewayz.com", "role": "admin"},
                {"email": "team2@mewayz.com", "role": "member"}
            ]
        }
        self.make_request("POST", "/workspace-setup/team-setup", data=team_data)
        
        # Step 5: Subscription selection (previously failing)
        print("Step 5: Subscription Selection")
        self.make_request("POST", "/workspace-setup/subscription-selection", 
                         data=self.test_data["workspace_subscription"])
        
        # Step 6: Branding configuration (previously failing)
        print("Step 6: Branding Configuration")
        self.make_request("POST", "/workspace-setup/branding-configuration", 
                         data=self.test_data["workspace_branding"])
        
        # Complete setup
        print("Final: Complete Setup")
        self.make_request("POST", "/workspace-setup/complete")

    def generate_focused_report(self):
        """Generate focused report on critical areas"""
        print("\n" + "="*80)
        print("📋 FOCUSED CRITICAL TESTING REPORT")
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
        
        # Group results by critical system
        crm_results = [r for r in self.test_results if "/crm" in r["endpoint"]]
        instagram_results = [r for r in self.test_results if "/instagram" in r["endpoint"]]
        workspace_results = [r for r in self.test_results if "/workspace-setup" in r["endpoint"]]
        
        print(f"\n📈 CRITICAL SYSTEMS RESULTS:")
        
        # CRM System Analysis
        if crm_results:
            crm_passed = len([r for r in crm_results if r["status"] == "PASS"])
            crm_total = len(crm_results)
            crm_rate = (crm_passed / crm_total * 100) if crm_total > 0 else 0
            status_icon = "✅" if crm_rate >= 80 else "⚠️" if crm_rate >= 50 else "❌"
            print(f"   {status_icon} CRM System: {crm_passed}/{crm_total} ({crm_rate:.1f}%) - Previous: 0-28.6%")
        
        # Instagram Management Analysis
        if instagram_results:
            ig_passed = len([r for r in instagram_results if r["status"] == "PASS"])
            ig_total = len(instagram_results)
            ig_rate = (ig_passed / ig_total * 100) if ig_total > 0 else 0
            status_icon = "✅" if ig_rate >= 80 else "⚠️" if ig_rate >= 50 else "❌"
            print(f"   {status_icon} Instagram Management: {ig_passed}/{ig_total} ({ig_rate:.1f}%) - Previous: 18.2%")
        
        # Workspace Setup Analysis
        if workspace_results:
            ws_passed = len([r for r in workspace_results if r["status"] == "PASS"])
            ws_total = len(workspace_results)
            ws_rate = (ws_passed / ws_total * 100) if ws_total > 0 else 0
            status_icon = "✅" if ws_rate >= 80 else "⚠️" if ws_rate >= 50 else "❌"
            print(f"   {status_icon} Workspace Setup: {ws_passed}/{ws_total} ({ws_rate:.1f}%) - Previous: 50.0%")
        
        # Show critical failures
        critical_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] >= 500]
        if critical_failures:
            print(f"\n🚨 CRITICAL FAILURES (5xx errors):")
            for failure in critical_failures[:10]:
                print(f"   ❌ {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        # Show improvement analysis
        print(f"\n📈 IMPROVEMENT ANALYSIS:")
        if crm_results:
            crm_improvement = crm_rate - 28.6  # Previous best was 28.6%
            improvement_icon = "📈" if crm_improvement > 0 else "📉"
            print(f"   {improvement_icon} CRM System: {crm_improvement:+.1f}% improvement")
        
        if instagram_results:
            ig_improvement = ig_rate - 18.2  # Previous was 18.2%
            improvement_icon = "📈" if ig_improvement > 0 else "📉"
            print(f"   {improvement_icon} Instagram Management: {ig_improvement:+.1f}% improvement")
        
        if workspace_results:
            ws_improvement = ws_rate - 50.0  # Previous was 50.0%
            improvement_icon = "📈" if ws_improvement > 0 else "📉"
            print(f"   {improvement_icon} Workspace Setup: {ws_improvement:+.1f}% improvement")
        
        # Save detailed results
        report_file = f"/app/focused_critical_test_results_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        with open(report_file, 'w') as f:
            json.dump({
                "summary": {
                    "total_tests": total_tests,
                    "passed": passed_tests,
                    "failed": failed_tests,
                    "errors": error_tests,
                    "success_rate": success_rate,
                    "test_timestamp": datetime.now().isoformat()
                },
                "critical_systems": {
                    "crm": {"passed": crm_passed if crm_results else 0, "total": crm_total if crm_results else 0, "rate": crm_rate if crm_results else 0},
                    "instagram": {"passed": ig_passed if instagram_results else 0, "total": ig_total if instagram_results else 0, "rate": ig_rate if instagram_results else 0},
                    "workspace": {"passed": ws_passed if workspace_results else 0, "total": ws_total if workspace_results else 0, "rate": ws_rate if workspace_results else 0}
                },
                "results": self.test_results
            }, f, indent=2)
        
        print(f"\n💾 Detailed results saved to: {report_file}")
        
        return {
            "success_rate": success_rate,
            "total_tests": total_tests,
            "passed": passed_tests,
            "failed": failed_tests,
            "crm_rate": crm_rate if crm_results else 0,
            "instagram_rate": ig_rate if instagram_results else 0,
            "workspace_rate": ws_rate if workspace_results else 0,
            "critical_failures": len(critical_failures)
        }

    def run_focused_critical_test(self):
        """Run focused critical testing"""
        print("🎯 STARTING FOCUSED CRITICAL TESTING")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Focus: CRM System, Instagram Management, Workspace Setup")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate():
            print("\n❌ CRITICAL: Authentication failed")
            return False
        
        # Run focused tests
        try:
            self.test_crm_system_critical()
            self.test_instagram_management_critical()
            self.test_workspace_setup_critical()
            
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
        
        # Show improvement summary
        print(f"\n📊 IMPROVEMENT SUMMARY:")
        print(f"   CRM System: {results['crm_rate']:.1f}% (Previous: 0-28.6%)")
        print(f"   Instagram Management: {results['instagram_rate']:.1f}% (Previous: 18.2%)")
        print(f"   Workspace Setup: {results['workspace_rate']:.1f}% (Previous: 50.0%)")
        
        return results['success_rate'] >= 60  # Consider 60%+ as acceptable for critical fixes

def main():
    """Main function to run the focused critical tests"""
    if len(sys.argv) > 1:
        base_url = sys.argv[1]
    else:
        base_url = "http://localhost:8001"
    
    tester = FocusedCriticalTester(base_url)
    success = tester.run_focused_critical_test()
    
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()