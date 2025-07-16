#!/usr/bin/env python3
"""
Focused Backend Testing for Review Request Fixes
===============================================

Testing the specific fixes mentioned in the review request:
1. Stripe Payment Routes - Added `/api/stripe/` aliases to match frontend expectations
2. Instagram Management API - Added missing `instagram_account_id` column to `instagram_posts` table
3. JavaScript Assets - Rebuilt frontend assets with npm run build

Author: Testing Agent
Date: January 2025
"""

import requests
import json
import time
from datetime import datetime
from typing import Dict, List, Optional, Any

class FocusedFixesTester:
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
        
    def authenticate(self) -> bool:
        """Authenticate and get access token"""
        try:
            response = self.session.post(
                f"{self.api_url}/auth/login",
                json=self.admin_credentials,
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                self.auth_token = data.get('token')
                if self.auth_token:
                    self.session.headers.update({
                        'Authorization': f'Bearer {self.auth_token}',
                        'Content-Type': 'application/json'
                    })
                    print("✅ Authentication successful")
                    return True
            
            print(f"❌ Authentication failed: {response.status_code}")
            return False
            
        except Exception as e:
            print(f"❌ Authentication error: {str(e)}")
            return False

    def test_stripe_payment_routes(self) -> Dict[str, Any]:
        """Test Stripe Payment Integration - both /api/payments/ and /api/stripe/ prefixes"""
        results = {
            "category": "Stripe Payment Integration",
            "tests": [],
            "summary": {"passed": 0, "failed": 0, "total": 0}
        }
        
        # Test cases for both route prefixes
        test_cases = [
            {
                "name": "Stripe Packages Endpoint (/api/stripe/packages)",
                "method": "GET",
                "url": f"{self.api_url}/stripe/packages",
                "expected_status": 200,
                "expected_data": ["starter", "professional", "enterprise"]
            },
            {
                "name": "Legacy Payments Packages Endpoint (/api/payments/packages)",
                "method": "GET", 
                "url": f"{self.api_url}/payments/packages",
                "expected_status": 200,
                "expected_data": ["starter", "professional", "enterprise"]
            },
            {
                "name": "Stripe Checkout Session (/api/stripe/checkout/session)",
                "method": "POST",
                "url": f"{self.api_url}/stripe/checkout/session",
                "data": {"package": "starter"},
                "expected_status": 200
            },
            {
                "name": "Legacy Payments Checkout Session (/api/payments/checkout/session)",
                "method": "POST",
                "url": f"{self.api_url}/payments/checkout/session", 
                "data": {"package": "starter"},
                "expected_status": 200
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                
                if test_case["method"] == "GET":
                    response = self.session.get(test_case["url"], timeout=10)
                else:
                    response = self.session.post(test_case["url"], json=test_case.get("data", {}), timeout=10)
                
                response_time = (time.time() - start_time) * 1000
                
                test_result = {
                    "name": test_case["name"],
                    "method": test_case["method"],
                    "url": test_case["url"],
                    "status_code": response.status_code,
                    "response_time": f"{response_time:.2f}ms",
                    "passed": response.status_code == test_case["expected_status"]
                }
                
                if response.status_code == 200:
                    try:
                        data = response.json()
                        test_result["response_data"] = data
                        
                        # Validate expected data for packages endpoints
                        if "packages" in test_case["name"] and "expected_data" in test_case:
                            if "packages" in data:
                                packages = data["packages"]
                                has_expected_packages = all(pkg in packages for pkg in test_case["expected_data"])
                                test_result["packages_validation"] = has_expected_packages
                                if not has_expected_packages:
                                    test_result["passed"] = False
                                    test_result["error"] = f"Missing expected packages: {test_case['expected_data']}"
                            else:
                                test_result["passed"] = False
                                test_result["error"] = "Response missing 'packages' field"
                                
                    except json.JSONDecodeError:
                        test_result["error"] = "Invalid JSON response"
                        test_result["passed"] = False
                else:
                    try:
                        error_data = response.json()
                        test_result["error"] = error_data
                    except:
                        test_result["error"] = response.text[:200]
                
                results["tests"].append(test_result)
                results["summary"]["total"] += 1
                
                if test_result["passed"]:
                    results["summary"]["passed"] += 1
                    print(f"✅ {test_case['name']} - {response.status_code} ({response_time:.2f}ms)")
                else:
                    results["summary"]["failed"] += 1
                    print(f"❌ {test_case['name']} - {response.status_code} ({response_time:.2f}ms)")
                    if "error" in test_result:
                        print(f"   Error: {test_result['error']}")
                        
            except Exception as e:
                test_result = {
                    "name": test_case["name"],
                    "method": test_case["method"],
                    "url": test_case["url"],
                    "error": str(e),
                    "passed": False
                }
                results["tests"].append(test_result)
                results["summary"]["total"] += 1
                results["summary"]["failed"] += 1
                print(f"❌ {test_case['name']} - Exception: {str(e)}")
        
        return results

    def test_instagram_management_api(self) -> Dict[str, Any]:
        """Test Instagram Management API - verify instagram_account_id column fix"""
        results = {
            "category": "Instagram Management API",
            "tests": [],
            "summary": {"passed": 0, "failed": 0, "total": 0}
        }
        
        test_cases = [
            {
                "name": "Instagram Accounts Endpoint",
                "method": "GET",
                "url": f"{self.api_url}/instagram-management/accounts",
                "expected_status": 200
            },
            {
                "name": "Instagram Posts Endpoint", 
                "method": "GET",
                "url": f"{self.api_url}/instagram-management/posts",
                "expected_status": 200
            },
            {
                "name": "Create Instagram Post (test instagram_account_id column)",
                "method": "POST",
                "url": f"{self.api_url}/instagram-management/posts",
                "data": {
                    "caption": "Test post to verify instagram_account_id column fix 🚀 #Testing #Mewayz",
                    "media_url": "https://example.com/test-image.jpg",
                    "hashtags": ["#Testing", "#Mewayz", "#SocialMedia"],
                    "instagram_account_id": 1,
                    "scheduled_at": "2025-01-17 10:00:00"
                },
                "expected_status": 200
            },
            {
                "name": "Instagram Analytics",
                "method": "GET", 
                "url": f"{self.api_url}/instagram-management/analytics",
                "expected_status": 200
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                
                if test_case["method"] == "GET":
                    response = self.session.get(test_case["url"], timeout=10)
                else:
                    response = self.session.post(test_case["url"], json=test_case.get("data", {}), timeout=10)
                
                response_time = (time.time() - start_time) * 1000
                
                test_result = {
                    "name": test_case["name"],
                    "method": test_case["method"],
                    "url": test_case["url"],
                    "status_code": response.status_code,
                    "response_time": f"{response_time:.2f}ms",
                    "passed": response.status_code == test_case["expected_status"]
                }
                
                if response.status_code == 200:
                    try:
                        data = response.json()
                        test_result["response_data"] = data
                        
                        # Special validation for accounts endpoint
                        if "accounts" in test_case["name"] and "accounts" in data:
                            accounts = data["accounts"]
                            test_result["accounts_count"] = len(accounts)
                            if accounts:
                                test_result["sample_account"] = accounts[0]
                                
                        # Special validation for posts endpoint  
                        elif "posts" in test_case["name"] and "posts" in data:
                            posts = data["posts"]
                            test_result["posts_count"] = len(posts)
                            if posts:
                                # Check if instagram_account_id column exists in posts
                                sample_post = posts[0]
                                has_account_id = "instagram_account_id" in sample_post
                                test_result["has_instagram_account_id_column"] = has_account_id
                                if has_account_id:
                                    test_result["instagram_account_id_value"] = sample_post["instagram_account_id"]
                                    
                    except json.JSONDecodeError:
                        test_result["error"] = "Invalid JSON response"
                        test_result["passed"] = False
                else:
                    try:
                        error_data = response.json()
                        test_result["error"] = error_data
                    except:
                        test_result["error"] = response.text[:200]
                
                results["tests"].append(test_result)
                results["summary"]["total"] += 1
                
                if test_result["passed"]:
                    results["summary"]["passed"] += 1
                    print(f"✅ {test_case['name']} - {response.status_code} ({response_time:.2f}ms)")
                    
                    # Additional info for successful tests
                    if "accounts_count" in test_result:
                        print(f"   📊 Found {test_result['accounts_count']} Instagram accounts")
                    if "posts_count" in test_result:
                        print(f"   📊 Found {test_result['posts_count']} Instagram posts")
                    if "has_instagram_account_id_column" in test_result:
                        if test_result["has_instagram_account_id_column"]:
                            print(f"   ✅ instagram_account_id column present in posts")
                        else:
                            print(f"   ⚠️ instagram_account_id column missing in posts")
                else:
                    results["summary"]["failed"] += 1
                    print(f"❌ {test_case['name']} - {response.status_code} ({response_time:.2f}ms)")
                    if "error" in test_result:
                        print(f"   Error: {test_result['error']}")
                        
            except Exception as e:
                test_result = {
                    "name": test_case["name"],
                    "method": test_case["method"],
                    "url": test_case["url"],
                    "error": str(e),
                    "passed": False
                }
                results["tests"].append(test_result)
                results["summary"]["total"] += 1
                results["summary"]["failed"] += 1
                print(f"❌ {test_case['name']} - Exception: {str(e)}")
        
        return results

    def test_previously_working_systems(self) -> Dict[str, Any]:
        """Test key systems that should continue working after fixes"""
        results = {
            "category": "Previously Working Systems",
            "tests": [],
            "summary": {"passed": 0, "failed": 0, "total": 0}
        }
        
        # Test key endpoints that should remain functional
        test_cases = [
            {
                "name": "Authentication System",
                "method": "GET",
                "url": f"{self.api_url}/auth/me",
                "expected_status": 200
            },
            {
                "name": "Email Marketing Campaigns",
                "method": "GET",
                "url": f"{self.api_url}/email-marketing/campaigns",
                "expected_status": 200
            },
            {
                "name": "Team Management",
                "method": "GET",
                "url": f"{self.api_url}/team",
                "expected_status": 200
            },
            {
                "name": "CRM Contacts",
                "method": "GET",
                "url": f"{self.api_url}/crm/contacts",
                "expected_status": 200
            },
            {
                "name": "Analytics Dashboard",
                "method": "GET",
                "url": f"{self.api_url}/analytics",
                "expected_status": 200
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = self.session.get(test_case["url"], timeout=10)
                response_time = (time.time() - start_time) * 1000
                
                test_result = {
                    "name": test_case["name"],
                    "method": test_case["method"],
                    "url": test_case["url"],
                    "status_code": response.status_code,
                    "response_time": f"{response_time:.2f}ms",
                    "passed": response.status_code == test_case["expected_status"]
                }
                
                if response.status_code == 200:
                    try:
                        data = response.json()
                        test_result["response_data"] = data
                    except json.JSONDecodeError:
                        test_result["error"] = "Invalid JSON response"
                        test_result["passed"] = False
                else:
                    try:
                        error_data = response.json()
                        test_result["error"] = error_data
                    except:
                        test_result["error"] = response.text[:200]
                
                results["tests"].append(test_result)
                results["summary"]["total"] += 1
                
                if test_result["passed"]:
                    results["summary"]["passed"] += 1
                    print(f"✅ {test_case['name']} - {response.status_code} ({response_time:.2f}ms)")
                else:
                    results["summary"]["failed"] += 1
                    print(f"❌ {test_case['name']} - {response.status_code} ({response_time:.2f}ms)")
                    if "error" in test_result:
                        print(f"   Error: {test_result['error']}")
                        
            except Exception as e:
                test_result = {
                    "name": test_case["name"],
                    "method": test_case["method"],
                    "url": test_case["url"],
                    "error": str(e),
                    "passed": False
                }
                results["tests"].append(test_result)
                results["summary"]["total"] += 1
                results["summary"]["failed"] += 1
                print(f"❌ {test_case['name']} - Exception: {str(e)}")
        
        return results

    def run_focused_tests(self):
        """Run all focused tests for the review request fixes"""
        print("🚀 STARTING FOCUSED BACKEND TESTING FOR REVIEW REQUEST FIXES")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        print()
        
        # Authenticate first
        if not self.authenticate():
            print("❌ Authentication failed - cannot proceed with tests")
            return
        
        print()
        
        # Run focused tests
        all_results = []
        
        print("=" * 60)
        print("🔍 TESTING STRIPE PAYMENT INTEGRATION")
        print("=" * 60)
        stripe_results = self.test_stripe_payment_routes()
        all_results.append(stripe_results)
        print()
        
        print("=" * 60)
        print("📸 TESTING INSTAGRAM MANAGEMENT API")
        print("=" * 60)
        instagram_results = self.test_instagram_management_api()
        all_results.append(instagram_results)
        print()
        
        print("=" * 60)
        print("🔄 TESTING PREVIOUSLY WORKING SYSTEMS")
        print("=" * 60)
        systems_results = self.test_previously_working_systems()
        all_results.append(systems_results)
        print()
        
        # Generate summary report
        self.generate_summary_report(all_results)
        
        # Save detailed results
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        filename = f"/app/focused_fixes_test_results_{timestamp}.json"
        
        with open(filename, 'w') as f:
            json.dump({
                "test_run": {
                    "timestamp": datetime.now().isoformat(),
                    "base_url": self.base_url,
                    "api_url": self.api_url
                },
                "results": all_results
            }, f, indent=2)
        
        print(f"💾 Detailed results saved to: {filename}")

    def generate_summary_report(self, all_results: List[Dict[str, Any]]):
        """Generate a comprehensive summary report"""
        print("=" * 80)
        print("📋 FOCUSED FIXES TESTING REPORT")
        print("=" * 80)
        print()
        
        total_tests = 0
        total_passed = 0
        total_failed = 0
        
        for result in all_results:
            category = result["category"]
            summary = result["summary"]
            
            total_tests += summary["total"]
            total_passed += summary["passed"]
            total_failed += summary["failed"]
            
            success_rate = (summary["passed"] / summary["total"] * 100) if summary["total"] > 0 else 0
            
            if success_rate >= 80:
                status_icon = "✅"
            elif success_rate >= 50:
                status_icon = "⚠️"
            else:
                status_icon = "❌"
                
            print(f"   {status_icon} {category}: {summary['passed']}/{summary['total']} ({success_rate:.1f}%)")
        
        print()
        overall_success_rate = (total_passed / total_tests * 100) if total_tests > 0 else 0
        
        print("📊 OVERALL STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {total_passed} ({total_passed/total_tests*100:.1f}%)")
        print(f"   ❌ Failed: {total_failed} ({total_failed/total_tests*100:.1f}%)")
        print(f"   🎯 Success Rate: {overall_success_rate:.1f}%")
        print()
        
        # Detailed findings
        print("🔍 KEY FINDINGS:")
        
        # Stripe findings
        stripe_result = next((r for r in all_results if r["category"] == "Stripe Payment Integration"), None)
        if stripe_result:
            stripe_passed = stripe_result["summary"]["passed"]
            stripe_total = stripe_result["summary"]["total"]
            if stripe_passed == stripe_total:
                print("   ✅ Stripe Payment Routes: All route aliases working correctly")
            elif stripe_passed > 0:
                print("   ⚠️ Stripe Payment Routes: Partial functionality - some routes working")
            else:
                print("   ❌ Stripe Payment Routes: Critical issues found")
        
        # Instagram findings
        instagram_result = next((r for r in all_results if r["category"] == "Instagram Management API"), None)
        if instagram_result:
            instagram_passed = instagram_result["summary"]["passed"]
            instagram_total = instagram_result["summary"]["total"]
            
            # Check for instagram_account_id column specifically
            posts_test = next((t for t in instagram_result["tests"] if "posts" in t["name"].lower()), None)
            if posts_test and "has_instagram_account_id_column" in posts_test:
                if posts_test["has_instagram_account_id_column"]:
                    print("   ✅ Instagram Management: instagram_account_id column successfully added")
                else:
                    print("   ❌ Instagram Management: instagram_account_id column still missing")
            
            if instagram_passed >= instagram_total * 0.75:
                print("   ✅ Instagram Management: API endpoints functioning well")
            else:
                print("   ⚠️ Instagram Management: Some API endpoints need attention")
        
        # Systems regression check
        systems_result = next((r for r in all_results if r["category"] == "Previously Working Systems"), None)
        if systems_result:
            systems_passed = systems_result["summary"]["passed"]
            systems_total = systems_result["summary"]["total"]
            if systems_passed == systems_total:
                print("   ✅ No Regressions: All previously working systems remain functional")
            else:
                print(f"   ⚠️ Potential Regressions: {systems_total - systems_passed} systems showing issues")
        
        print()
        print("🏁 TESTING COMPLETED")
        print(f"   Overall Success Rate: {overall_success_rate:.1f}%")
        print(f"   Tests Passed: {total_passed}/{total_tests}")

if __name__ == "__main__":
    tester = FocusedFixesTester()
    tester.run_focused_tests()