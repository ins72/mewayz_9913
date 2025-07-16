#!/usr/bin/env python3
"""
Mewayz Platform Focused Backend Testing Suite
==============================================

This test suite focuses on the specific backend tasks that need testing:
1. Analytics Dashboard - working: false, needs_retesting: true, priority: high
2. Bio Sites Management - working: false, needs_retesting: false, priority: high

The Laravel application should be running on port 8001 with comprehensive API endpoints.
"""

import os
import sys
import json
import time
import requests
from pathlib import Path

class MewayzFocusedBackendTest:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_base = f"{self.base_url}/api"
        self.results = {
            "server_connectivity": {},
            "analytics_dashboard_test": {},
            "bio_sites_management_test": {},
            "test_summary": {}
        }
        self.auth_token = None
        
    def run_all_tests(self):
        """Run focused backend tests for priority tasks"""
        print("🚀 MEWAYZ FOCUSED BACKEND TESTING SUITE")
        print("=" * 60)
        print("Focus: Analytics Dashboard & Bio Sites Management")
        print("-" * 60)
        
        # Test 1: Server Connectivity
        if not self.test_server_connectivity():
            print("❌ Server not accessible - cannot proceed with API tests")
            self.generate_test_report()
            return
        
        # Test 2: Authentication (needed for protected endpoints)
        self.test_authentication()
        
        # Test 3: Analytics Dashboard Testing
        self.test_analytics_dashboard()
        
        # Test 4: Bio Sites Management Testing
        self.test_bio_sites_management()
        
        # Generate comprehensive report
        self.generate_test_report()
        
    def test_server_connectivity(self):
        """Test 1: Server connectivity and health check"""
        print("\n🔗 TEST 1: SERVER CONNECTIVITY")
        print("-" * 50)
        
        results = {
            "server_accessible": False,
            "health_endpoint": False,
            "api_base_accessible": False,
            "response_time": 0
        }
        
        try:
            # Test basic server connectivity
            start_time = time.time()
            response = requests.get(self.base_url, timeout=5)
            response_time = time.time() - start_time
            
            results["server_accessible"] = True
            results["response_time"] = response_time
            
            print(f"✅ Server accessible: {response.status_code}")
            print(f"✅ Response time: {response_time:.3f}s")
            
            # Test health endpoint
            try:
                health_response = requests.get(f"{self.api_base}/health", timeout=5)
                if health_response.status_code == 200:
                    results["health_endpoint"] = True
                    print("✅ Health endpoint working")
                else:
                    print(f"⚠️  Health endpoint returned: {health_response.status_code}")
            except:
                print("⚠️  Health endpoint not accessible")
            
            # Test API base
            try:
                api_response = requests.get(f"{self.api_base}", timeout=5)
                results["api_base_accessible"] = True
                print(f"✅ API base accessible: {api_response.status_code}")
            except:
                print("⚠️  API base not accessible")
                
        except requests.exceptions.ConnectionError:
            print("❌ Server connection refused - server not running")
        except requests.exceptions.Timeout:
            print("❌ Server connection timeout")
        except Exception as e:
            print(f"❌ Server connectivity test failed: {e}")
            
        self.results["server_connectivity"] = results
        return results["server_accessible"]
        
    def test_authentication(self):
        """Get authentication token for protected endpoints"""
        print("\n🔐 AUTHENTICATION SETUP")
        print("-" * 50)
        
        try:
            auth_data = {
                "email": "admin@example.com",
                "password": "admin123"
            }
            
            response = requests.post(f"{self.api_base}/auth/login", json=auth_data, timeout=10)
            
            if response.status_code == 200:
                data = response.json()
                if 'token' in data:
                    self.auth_token = data['token']
                    print("✅ Authentication successful")
                elif 'access_token' in data:
                    self.auth_token = data['access_token']
                    print("✅ Authentication successful")
                else:
                    print("❌ No token in response")
            else:
                print(f"❌ Authentication failed: {response.status_code}")
                
        except Exception as e:
            print(f"❌ Authentication error: {e}")
            
    def test_analytics_dashboard(self):
        """Test 2: Analytics Dashboard - Priority High, needs_retesting: true"""
        print("\n📊 TEST 2: ANALYTICS DASHBOARD")
        print("-" * 50)
        print("Task: Analytics Dashboard")
        print("Status: working: false, needs_retesting: true, priority: high")
        print("File: /app/backend/app/Http/Controllers/Api/AnalyticsController.php")
        
        results = {
            "overview_analytics": False,
            "reports_access": False,
            "social_media_analytics": False,
            "bio_site_analytics": False,
            "ecommerce_analytics": False,
            "course_analytics": False,
            "endpoints_tested": 0,
            "endpoints_working": 0,
            "response_times": [],
            "errors_found": []
        }
        
        # Test endpoints that were previously failing with 500 errors
        analytics_endpoints = [
            ("/api/analytics", "Overview Analytics"),
            ("/api/analytics/reports", "Reports Access"),
            ("/api/analytics/social-media", "Social Media Analytics"),
            ("/api/analytics/bio-sites", "Bio Site Analytics"),
            ("/api/analytics/ecommerce", "E-commerce Analytics"),
            ("/api/analytics/courses", "Course Analytics")
        ]
        
        headers = {}
        if self.auth_token:
            headers['Authorization'] = f'Bearer {self.auth_token}'
        
        for endpoint, description in analytics_endpoints:
            try:
                print(f"\n🧪 Testing {description}...")
                results["endpoints_tested"] += 1
                
                start_time = time.time()
                response = requests.get(f"{self.base_url}{endpoint}", headers=headers, timeout=10)
                response_time = time.time() - start_time
                results["response_times"].append(response_time)
                
                print(f"   Status: {response.status_code}")
                print(f"   Response time: {response_time:.3f}s")
                
                if response.status_code == 200:
                    results["endpoints_working"] += 1
                    endpoint_key = description.lower().replace(" ", "_").replace("-", "_")
                    results[endpoint_key] = True
                    print(f"   ✅ {description} working")
                    
                    # Try to parse response
                    try:
                        data = response.json()
                        if isinstance(data, dict) and data:
                            print(f"   ✅ Valid JSON response with {len(data)} fields")
                        elif isinstance(data, list):
                            print(f"   ✅ Valid JSON array with {len(data)} items")
                    except:
                        print("   ⚠️  Response not JSON format")
                        
                elif response.status_code == 404:
                    print(f"   ⚠️  {description} endpoint not found (404) - acceptable for some endpoints")
                elif response.status_code == 500:
                    print(f"   ❌ {description} server error (500) - CRITICAL ISSUE")
                    results["errors_found"].append(f"{description}: 500 Server Error")
                else:
                    print(f"   ❌ {description} failed with status {response.status_code}")
                    results["errors_found"].append(f"{description}: HTTP {response.status_code}")
                    
            except requests.exceptions.RequestException as e:
                print(f"   ❌ Request failed: {e}")
                results["errors_found"].append(f"{description}: Request failed - {e}")
            except Exception as e:
                print(f"   ❌ Test failed: {e}")
                results["errors_found"].append(f"{description}: Test failed - {e}")
        
        # Calculate success rate
        success_rate = (results["endpoints_working"] / results["endpoints_tested"]) * 100 if results["endpoints_tested"] > 0 else 0
        results["success_rate"] = success_rate
        results["average_response_time"] = sum(results["response_times"]) / len(results["response_times"]) if results["response_times"] else 0
        
        print(f"\n📈 ANALYTICS DASHBOARD RESULTS:")
        print(f"   Endpoints tested: {results['endpoints_tested']}")
        print(f"   Endpoints working: {results['endpoints_working']}")
        print(f"   Success rate: {success_rate:.1f}%")
        print(f"   Average response time: {results['average_response_time']:.3f}s")
        
        if results["errors_found"]:
            print(f"   ❌ Errors found: {len(results['errors_found'])}")
            for error in results["errors_found"]:
                print(f"      - {error}")
        
        self.results["analytics_dashboard_test"] = results
        
    def test_bio_sites_management(self):
        """Test 3: Bio Sites Management - Priority High, working: false"""
        print("\n🌐 TEST 3: BIO SITES MANAGEMENT")
        print("-" * 50)
        print("Task: Bio Sites Management")
        print("Status: working: false, needs_retesting: false, priority: high")
        print("File: /app/backend/app/Http/Controllers/Api/BioSiteController.php")
        print("Issue: API routing problems - returns HTML instead of JSON")
        
        results = {
            "bio_sites_list": False,
            "bio_site_creation": False,
            "themes_access": False,
            "analytics_access": False,
            "user_id_assignment": False,
            "json_response_format": False,
            "endpoints_tested": 0,
            "endpoints_working": 0,
            "response_times": [],
            "errors_found": []
        }
        
        # Test Bio Sites endpoints
        bio_sites_endpoints = [
            ("GET", "/api/bio-sites", "Bio Sites List"),
            ("GET", "/api/bio-sites/themes", "Themes Access"),
            ("GET", "/api/bio-sites/analytics", "Analytics Access")
        ]
        
        headers = {}
        if self.auth_token:
            headers['Authorization'] = f'Bearer {self.auth_token}'
        
        for method, endpoint, description in bio_sites_endpoints:
            try:
                print(f"\n🧪 Testing {description}...")
                results["endpoints_tested"] += 1
                
                start_time = time.time()
                if method == "GET":
                    response = requests.get(f"{self.base_url}{endpoint}", headers=headers, timeout=10)
                response_time = time.time() - start_time
                results["response_times"].append(response_time)
                
                print(f"   Status: {response.status_code}")
                print(f"   Response time: {response_time:.3f}s")
                print(f"   Content-Type: {response.headers.get('Content-Type', 'Not specified')}")
                
                if response.status_code == 200:
                    results["endpoints_working"] += 1
                    endpoint_key = description.lower().replace(" ", "_").replace("-", "_")
                    results[endpoint_key] = True
                    print(f"   ✅ {description} accessible")
                    
                    # Check if response is JSON (critical issue was HTML responses)
                    content_type = response.headers.get('Content-Type', '')
                    if 'application/json' in content_type:
                        results["json_response_format"] = True
                        print("   ✅ Response is JSON format")
                        
                        try:
                            data = response.json()
                            if isinstance(data, dict) and data:
                                print(f"   ✅ Valid JSON response with {len(data)} fields")
                            elif isinstance(data, list):
                                print(f"   ✅ Valid JSON array with {len(data)} items")
                        except:
                            print("   ⚠️  Response not valid JSON")
                    elif 'text/html' in content_type:
                        print("   ❌ Response is HTML format - ROUTING ISSUE CONFIRMED")
                        results["errors_found"].append(f"{description}: Returns HTML instead of JSON")
                    else:
                        print(f"   ⚠️  Unexpected content type: {content_type}")
                        
                else:
                    print(f"   ❌ {description} failed with status {response.status_code}")
                    results["errors_found"].append(f"{description}: HTTP {response.status_code}")
                    
            except requests.exceptions.RequestException as e:
                print(f"   ❌ Request failed: {e}")
                results["errors_found"].append(f"{description}: Request failed - {e}")
            except Exception as e:
                print(f"   ❌ Test failed: {e}")
                results["errors_found"].append(f"{description}: Test failed - {e}")
        
        # Test Bio Site Creation (POST) - the critical issue
        try:
            print(f"\n🧪 Testing Bio Site Creation (POST)...")
            results["endpoints_tested"] += 1
            
            bio_site_data = {
                "title": "Test Bio Site",
                "description": "Test bio site for API testing",
                "theme": "default",
                "is_active": True
            }
            
            start_time = time.time()
            response = requests.post(f"{self.base_url}/api/bio-sites", json=bio_site_data, headers=headers, timeout=10)
            response_time = time.time() - start_time
            results["response_times"].append(response_time)
            
            print(f"   Status: {response.status_code}")
            print(f"   Response time: {response_time:.3f}s")
            print(f"   Content-Type: {response.headers.get('Content-Type', 'Not specified')}")
            
            if response.status_code in [200, 201]:
                results["endpoints_working"] += 1
                results["bio_site_creation"] = True
                print("   ✅ Bio Site Creation working")
                
                # Check response format and user_id assignment
                content_type = response.headers.get('Content-Type', '')
                if 'application/json' in content_type:
                    results["json_response_format"] = True
                    print("   ✅ Response is JSON format")
                    
                    try:
                        data = response.json()
                        if 'user_id' in data:
                            results["user_id_assignment"] = True
                            print(f"   ✅ User ID assigned: {data['user_id']}")
                        else:
                            print("   ⚠️  User ID not found in response")
                            results["errors_found"].append("Bio Site Creation: user_id not assigned")
                    except:
                        print("   ⚠️  Response not valid JSON")
                elif 'text/html' in content_type:
                    print("   ❌ Response is HTML format - ROUTING ISSUE CONFIRMED")
                    results["errors_found"].append("Bio Site Creation: Returns HTML instead of JSON - routing problem")
                    
            else:
                print(f"   ❌ Bio Site Creation failed with status {response.status_code}")
                results["errors_found"].append(f"Bio Site Creation: HTTP {response.status_code}")
                
        except Exception as e:
            print(f"   ❌ Bio Site Creation test failed: {e}")
            results["errors_found"].append(f"Bio Site Creation: Test failed - {e}")
        
        # Calculate success rate
        success_rate = (results["endpoints_working"] / results["endpoints_tested"]) * 100 if results["endpoints_tested"] > 0 else 0
        results["success_rate"] = success_rate
        results["average_response_time"] = sum(results["response_times"]) / len(results["response_times"]) if results["response_times"] else 0
        
        print(f"\n🌐 BIO SITES MANAGEMENT RESULTS:")
        print(f"   Endpoints tested: {results['endpoints_tested']}")
        print(f"   Endpoints working: {results['endpoints_working']}")
        print(f"   Success rate: {success_rate:.1f}%")
        print(f"   Average response time: {results['average_response_time']:.3f}s")
        print(f"   JSON Response Format: {'✅' if results['json_response_format'] else '❌'}")
        print(f"   User ID Assignment: {'✅' if results['user_id_assignment'] else '❌'}")
        
        if results["errors_found"]:
            print(f"   ❌ Errors found: {len(results['errors_found'])}")
            for error in results["errors_found"]:
                print(f"      - {error}")
        
        self.results["bio_sites_management_test"] = results
        
    def generate_test_report(self):
        """Generate comprehensive test report"""
        print("\n📋 FOCUSED BACKEND TEST REPORT")
        print("=" * 60)
        
        # Server connectivity
        server_test = self.results["server_connectivity"]
        server_score = (sum(server_test.values()) / len(server_test)) * 100 if server_test else 0
        
        # Analytics dashboard
        analytics_test = self.results.get("analytics_dashboard_test", {})
        analytics_score = analytics_test.get("success_rate", 0)
        
        # Bio sites management
        bio_sites_test = self.results.get("bio_sites_management_test", {})
        bio_sites_score = bio_sites_test.get("success_rate", 0)
        
        # Overall score
        scores = [score for score in [server_score, analytics_score, bio_sites_score] if score > 0]
        overall_score = sum(scores) / len(scores) if scores else 0
        
        print(f"🔗 Server Connectivity: {server_score:.1f}%")
        print(f"📊 Analytics Dashboard: {analytics_score:.1f}%")
        print(f"🌐 Bio Sites Management: {bio_sites_score:.1f}%")
        print("-" * 40)
        print(f"🎯 OVERALL FOCUSED TEST SCORE: {overall_score:.1f}%")
        
        # Detailed findings
        print("\n🔍 DETAILED FINDINGS:")
        
        # Server connectivity
        if server_test.get("server_accessible"):
            print("✅ Server connectivity working")
        else:
            print("❌ Server connectivity issues - cannot access Laravel application")
            
        # Analytics dashboard
        if analytics_test.get("endpoints_working", 0) > 0:
            working = analytics_test.get("endpoints_working", 0)
            total = analytics_test.get("endpoints_tested", 0)
            print(f"📊 Analytics Dashboard: {working}/{total} endpoints working")
            
            if analytics_test.get("errors_found"):
                print("   Critical issues found:")
                for error in analytics_test["errors_found"][:3]:  # Show first 3 errors
                    print(f"   - {error}")
        else:
            print("❌ Analytics Dashboard: No endpoints accessible")
            
        # Bio sites management
        if bio_sites_test.get("endpoints_working", 0) > 0:
            working = bio_sites_test.get("endpoints_working", 0)
            total = bio_sites_test.get("endpoints_tested", 0)
            print(f"🌐 Bio Sites Management: {working}/{total} endpoints working")
            
            if not bio_sites_test.get("json_response_format"):
                print("   ❌ CRITICAL: API returns HTML instead of JSON (routing issue)")
            if not bio_sites_test.get("user_id_assignment"):
                print("   ❌ CRITICAL: User ID not assigned to created bio sites")
                
            if bio_sites_test.get("errors_found"):
                print("   Issues found:")
                for error in bio_sites_test["errors_found"][:3]:  # Show first 3 errors
                    print(f"   - {error}")
        else:
            print("❌ Bio Sites Management: No endpoints accessible")
        
        # Summary
        summary = {
            "overall_score": overall_score,
            "server_score": server_score,
            "analytics_score": analytics_score,
            "bio_sites_score": bio_sites_score,
            "test_timestamp": time.strftime('%Y-%m-%d %H:%M:%S'),
            "server_accessible": server_test.get("server_accessible", False),
            "analytics_endpoints_working": analytics_test.get("endpoints_working", 0),
            "bio_sites_endpoints_working": bio_sites_test.get("endpoints_working", 0),
            "critical_issues": []
        }
        
        # Identify critical issues
        if not server_test.get("server_accessible"):
            summary["critical_issues"].append("Server not accessible - Laravel application not running")
            
        if analytics_test.get("errors_found"):
            for error in analytics_test["errors_found"]:
                if "500" in error:
                    summary["critical_issues"].append(f"Analytics: {error}")
                    
        if bio_sites_test.get("errors_found"):
            for error in bio_sites_test["errors_found"]:
                if "HTML instead of JSON" in error:
                    summary["critical_issues"].append(f"Bio Sites: {error}")
        
        self.results["test_summary"] = summary
        
        # Recommendations
        print("\n💡 RECOMMENDATIONS:")
        if overall_score >= 90:
            print("✅ EXCELLENT: Both priority tasks are working well!")
        elif overall_score >= 70:
            print("✅ GOOD: Most functionality working with minor issues.")
        elif overall_score >= 50:
            print("⚠️  FAIR: Some critical components need attention.")
        else:
            print("❌ NEEDS WORK: Significant issues found that require immediate attention.")
        
        # Specific recommendations
        if not server_test.get("server_accessible"):
            print("   🚨 URGENT: Start Laravel server - application not accessible")
        if analytics_score < 70:
            print("   📊 Fix Analytics Dashboard 500 errors - investigate controller issues")
        if bio_sites_score < 70:
            print("   🌐 Fix Bio Sites API routing - ensure JSON responses instead of HTML")
        if bio_sites_test.get("user_id_assignment") == False:
            print("   👤 Fix Bio Sites user_id assignment in creation process")
        
        print(f"\n📊 Test completed at: {time.strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Save results to file
        results_file = Path("/app/focused_backend_test_results.json")
        with open(results_file, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)
        print(f"📄 Detailed results saved to: {results_file}")

def main():
    """Main test execution"""
    print("🚀 Starting Mewayz Focused Backend Testing...")
    print("Focus: Analytics Dashboard & Bio Sites Management")
    
    tester = MewayzFocusedBackendTest()
    tester.run_all_tests()
    
    print("\n✅ Focused testing completed!")
    return tester.results["test_summary"]["overall_score"]

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 50 else 1)
    except Exception as e:
        print(f"❌ Test execution failed: {e}")
        sys.exit(1)