#!/usr/bin/env python3
"""
Comprehensive Backend API Testing for Mewayz Laravel Platform
=============================================================

This script tests all backend API endpoints as requested in the review.
Tests include:
1. Infrastructure & Health checks
2. Authentication system
3. Core features (CRM, Instagram, E-commerce, etc.)
4. Database operations
5. Security features
6. Performance testing
7. Error handling
"""

import requests
import json
import time
import sys
from typing import Dict, List, Any

class MewayzBackendTester:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = {
            "infrastructure": {},
            "authentication": {},
            "core_features": {},
            "database_operations": {},
            "security": {},
            "performance": {},
            "error_handling": {},
            "summary": {}
        }
        
    def run_comprehensive_tests(self):
        """Run all comprehensive backend tests"""
        print("🚀 COMPREHENSIVE BACKEND API TESTING")
        print("=" * 60)
        
        # Test 1: Infrastructure & Health
        self.test_infrastructure()
        
        # Test 2: Authentication System
        self.test_authentication()
        
        # Test 3: Core Features
        self.test_core_features()
        
        # Test 4: Database Operations
        self.test_database_operations()
        
        # Test 5: Security Features
        self.test_security()
        
        # Test 6: Performance Testing
        self.test_performance()
        
        # Test 7: Error Handling
        self.test_error_handling()
        
        # Generate Final Report
        self.generate_final_report()
        
    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None) -> Dict:
        """Make HTTP request with error handling"""
        url = f"{self.api_url}{endpoint}"
        
        # Add auth token if available
        if self.auth_token and headers is None:
            headers = {"Authorization": f"Bearer {self.auth_token}"}
        elif self.auth_token and headers:
            headers["Authorization"] = f"Bearer {self.auth_token}"
            
        try:
            start_time = time.time()
            
            if method.upper() == "GET":
                response = self.session.get(url, headers=headers)
            elif method.upper() == "POST":
                response = self.session.post(url, json=data, headers=headers)
            elif method.upper() == "PUT":
                response = self.session.put(url, json=data, headers=headers)
            elif method.upper() == "DELETE":
                response = self.session.delete(url, headers=headers)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            response_time = time.time() - start_time
            
            return {
                "status_code": response.status_code,
                "response_time": response_time,
                "success": response.status_code < 400,
                "data": response.json() if response.content else {},
                "headers": dict(response.headers)
            }
        except Exception as e:
            return {
                "status_code": 0,
                "response_time": 0,
                "success": False,
                "error": str(e),
                "data": {}
            }
    
    def test_infrastructure(self):
        """Test 1: Infrastructure and Health checks"""
        print("\n🏗️ TEST 1: INFRASTRUCTURE & HEALTH CHECKS")
        print("-" * 50)
        
        tests = [
            ("Health Check", "/health"),
            ("System Info", "/system/info"),
            ("System Maintenance", "/system/maintenance"),
            ("Platform Overview", "/platform/overview"),
            ("Platform Features", "/platform/features"),
            ("Branding Info", "/branding/info"),
            ("Performance Metrics", "/optimization/performance")
        ]
        
        results = {}
        for test_name, endpoint in tests:
            result = self.make_request("GET", endpoint)
            results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        self.test_results["infrastructure"] = results
        
    def test_authentication(self):
        """Test 2: Authentication System"""
        print("\n🔐 TEST 2: AUTHENTICATION SYSTEM")
        print("-" * 50)
        
        # Test login
        login_data = {
            "email": "admin@example.com",
            "password": "admin123"
        }
        
        login_result = self.make_request("POST", "/auth/login", login_data)
        
        if login_result["success"] and "token" in login_result["data"]:
            self.auth_token = login_result["data"]["token"]
            print(f"   ✅ Login: {login_result['status_code']} (Token obtained)")
        else:
            print(f"   ❌ Login: {login_result['status_code']} (Failed)")
            
        # Test authenticated endpoints
        auth_tests = [
            ("Get User Profile", "/auth/me"),
            ("OAuth Status", "/auth/oauth-status"),
            ("2FA Status", "/auth/2fa/status")
        ]
        
        auth_results = {"login": login_result}
        
        for test_name, endpoint in auth_tests:
            result = self.make_request("GET", endpoint)
            auth_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        self.test_results["authentication"] = auth_results
        
    def test_core_features(self):
        """Test 3: Core Features"""
        print("\n⚙️ TEST 3: CORE FEATURES")
        print("-" * 50)
        
        # Workspace Setup Wizard
        print("\n📋 Workspace Setup Wizard:")
        workspace_tests = [
            ("Current Step", "/workspace-setup/current-step"),
            ("Main Goals", "/workspace-setup/main-goals"),
            ("Available Features", "/workspace-setup/available-features"),
            ("Subscription Plans", "/workspace-setup/subscription-plans"),
            ("Setup Summary", "/workspace-setup/summary")
        ]
        
        workspace_results = {}
        for test_name, endpoint in workspace_tests:
            result = self.make_request("GET", endpoint)
            workspace_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # Instagram Management
        print("\n📸 Instagram Management:")
        instagram_tests = [
            ("Get Accounts", "/instagram-management/accounts"),
            ("Get Posts", "/instagram-management/posts"),
            ("Hashtag Research", "/instagram-management/hashtag-research"),
            ("Analytics", "/instagram-management/analytics")
        ]
        
        instagram_results = {}
        for test_name, endpoint in instagram_tests:
            result = self.make_request("GET", endpoint)
            instagram_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # CRM System
        print("\n👥 CRM System:")
        crm_tests = [
            ("Get Contacts", "/crm/contacts"),
            ("Get Leads", "/crm/leads"),
            ("AI Lead Scoring", "/crm/ai-lead-scoring"),
            ("Pipeline Management", "/crm/advanced-pipeline-management")
        ]
        
        crm_results = {}
        for test_name, endpoint in crm_tests:
            result = self.make_request("GET", endpoint)
            crm_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # E-commerce Platform
        print("\n🛒 E-commerce Platform:")
        ecommerce_tests = [
            ("Get Products", "/ecommerce/products"),
            ("Get Orders", "/ecommerce/orders")
        ]
        
        ecommerce_results = {}
        for test_name, endpoint in ecommerce_tests:
            result = self.make_request("GET", endpoint)
            ecommerce_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # Email Marketing
        print("\n📧 Email Marketing:")
        email_tests = [
            ("Get Campaigns", "/email-marketing/campaigns"),
            ("Get Templates", "/email-marketing/templates")
        ]
        
        email_results = {}
        for test_name, endpoint in email_tests:
            result = self.make_request("GET", endpoint)
            email_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # Course Management
        print("\n🎓 Course Management:")
        course_tests = [
            ("Get Courses", "/courses"),
        ]
        
        course_results = {}
        for test_name, endpoint in course_tests:
            result = self.make_request("GET", endpoint)
            course_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # Bio Sites Management
        print("\n🔗 Bio Sites Management:")
        bio_tests = [
            ("Get Bio Sites", "/bio-sites/"),
            ("Get Themes", "/bio-sites/themes")
        ]
        
        bio_results = {}
        for test_name, endpoint in bio_tests:
            result = self.make_request("GET", endpoint)
            bio_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # Analytics Dashboard
        print("\n📊 Analytics Dashboard:")
        analytics_tests = [
            ("Overview Analytics", "/analytics/"),
            ("Reports", "/analytics/reports"),
            ("Social Media Analytics", "/analytics/social-media"),
            ("Bio Site Analytics", "/analytics/bio-sites")
        ]
        
        analytics_results = {}
        for test_name, endpoint in analytics_tests:
            result = self.make_request("GET", endpoint)
            analytics_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        # Payment Processing
        print("\n💳 Payment Processing:")
        payment_tests = [
            ("Get Packages", "/payments/packages"),
        ]
        
        payment_results = {}
        for test_name, endpoint in payment_tests:
            result = self.make_request("GET", endpoint)
            payment_results[test_name] = result
            
            status = "✅" if result["success"] else "❌"
            response_time = f"{result['response_time']*1000:.1f}ms"
            print(f"   {status} {test_name}: {result['status_code']} ({response_time})")
            
        self.test_results["core_features"] = {
            "workspace_setup": workspace_results,
            "instagram_management": instagram_results,
            "crm_system": crm_results,
            "ecommerce": ecommerce_results,
            "email_marketing": email_results,
            "course_management": course_results,
            "bio_sites": bio_results,
            "analytics": analytics_results,
            "payment_processing": payment_results
        }
        
    def test_database_operations(self):
        """Test 4: Database Operations (CRUD)"""
        print("\n🗄️ TEST 4: DATABASE OPERATIONS")
        print("-" * 50)
        
        # Test creating a contact (CREATE)
        contact_data = {
            "name": "Test Contact",
            "email": "test@example.com",
            "phone": "+1234567890",
            "company": "Test Company"
        }
        
        create_result = self.make_request("POST", "/crm/contacts", contact_data)
        print(f"   {'✅' if create_result['success'] else '❌'} Create Contact: {create_result['status_code']}")
        
        # Test reading contacts (READ)
        read_result = self.make_request("GET", "/crm/contacts")
        print(f"   {'✅' if read_result['success'] else '❌'} Read Contacts: {read_result['status_code']}")
        
        # Test creating a bio site
        bio_data = {
            "title": "Test Bio Site",
            "description": "Test Description",
            "theme": "default"
        }
        
        bio_create_result = self.make_request("POST", "/bio-sites/", bio_data)
        print(f"   {'✅' if bio_create_result['success'] else '❌'} Create Bio Site: {bio_create_result['status_code']}")
        
        # Test creating a course
        course_data = {
            "title": "Test Course",
            "description": "Test Course Description",
            "price": 99.99
        }
        
        course_create_result = self.make_request("POST", "/courses", course_data)
        print(f"   {'✅' if course_create_result['success'] else '❌'} Create Course: {course_create_result['status_code']}")
        
        self.test_results["database_operations"] = {
            "create_contact": create_result,
            "read_contacts": read_result,
            "create_bio_site": bio_create_result,
            "create_course": course_create_result
        }
        
    def test_security(self):
        """Test 5: Security Features"""
        print("\n🔒 TEST 5: SECURITY FEATURES")
        print("-" * 50)
        
        # Test unauthenticated access to protected endpoints
        temp_token = self.auth_token
        self.auth_token = None
        
        protected_endpoints = [
            "/auth/me",
            "/crm/contacts",
            "/bio-sites/",
            "/courses"
        ]
        
        security_results = {}
        
        for endpoint in protected_endpoints:
            result = self.make_request("GET", endpoint)
            is_protected = result["status_code"] == 401
            security_results[f"Protection_{endpoint}"] = result
            
            status = "✅" if is_protected else "❌"
            print(f"   {status} Protected {endpoint}: {result['status_code']} ({'Protected' if is_protected else 'Unprotected'})")
            
        # Restore auth token
        self.auth_token = temp_token
        
        # Test invalid token
        invalid_headers = {"Authorization": "Bearer invalid_token_here"}
        invalid_result = self.make_request("GET", "/auth/me", headers=invalid_headers)
        is_invalid_rejected = invalid_result["status_code"] == 401
        
        status = "✅" if is_invalid_rejected else "❌"
        print(f"   {status} Invalid Token Rejection: {invalid_result['status_code']} ({'Rejected' if is_invalid_rejected else 'Accepted'})")
        
        security_results["invalid_token"] = invalid_result
        
        self.test_results["security"] = security_results
        
    def test_performance(self):
        """Test 6: Performance Testing"""
        print("\n⚡ TEST 6: PERFORMANCE TESTING")
        print("-" * 50)
        
        # Test response times for key endpoints
        performance_endpoints = [
            "/health",
            "/auth/me",
            "/crm/contacts",
            "/analytics/",
            "/payments/packages"
        ]
        
        performance_results = {}
        total_time = 0
        successful_requests = 0
        
        for endpoint in performance_endpoints:
            # Make 3 requests to get average response time
            times = []
            for _ in range(3):
                result = self.make_request("GET", endpoint)
                if result["success"]:
                    times.append(result["response_time"])
                    
            if times:
                avg_time = sum(times) / len(times)
                total_time += avg_time
                successful_requests += 1
                
                performance_results[endpoint] = {
                    "average_response_time": avg_time,
                    "times": times
                }
                
                status = "✅" if avg_time < 0.5 else "⚠️" if avg_time < 1.0 else "❌"
                print(f"   {status} {endpoint}: {avg_time*1000:.1f}ms avg")
            else:
                print(f"   ❌ {endpoint}: Failed to get response times")
                
        if successful_requests > 0:
            overall_avg = total_time / successful_requests
            print(f"\n   🎯 Overall Average Response Time: {overall_avg*1000:.1f}ms")
            performance_results["overall_average"] = overall_avg
            
        self.test_results["performance"] = performance_results
        
    def test_error_handling(self):
        """Test 7: Error Handling"""
        print("\n🚨 TEST 7: ERROR HANDLING")
        print("-" * 50)
        
        # Test 404 for non-existent endpoints
        not_found_result = self.make_request("GET", "/non-existent-endpoint")
        handles_404 = not_found_result["status_code"] == 404
        
        status = "✅" if handles_404 else "❌"
        print(f"   {status} 404 Handling: {not_found_result['status_code']} ({'Proper' if handles_404 else 'Improper'})")
        
        # Test invalid data submission
        invalid_data = {"invalid": "data"}
        invalid_data_result = self.make_request("POST", "/crm/contacts", invalid_data)
        handles_validation = invalid_data_result["status_code"] in [400, 422]
        
        status = "✅" if handles_validation else "❌"
        print(f"   {status} Validation Errors: {invalid_data_result['status_code']} ({'Handled' if handles_validation else 'Not Handled'})")
        
        # Test method not allowed
        method_result = self.make_request("DELETE", "/health")
        handles_method = method_result["status_code"] == 405
        
        status = "✅" if handles_method else "❌"
        print(f"   {status} Method Not Allowed: {method_result['status_code']} ({'Handled' if handles_method else 'Not Handled'})")
        
        self.test_results["error_handling"] = {
            "404_handling": not_found_result,
            "validation_errors": invalid_data_result,
            "method_not_allowed": method_result
        }
        
    def generate_final_report(self):
        """Generate comprehensive final report"""
        print("\n📋 COMPREHENSIVE BACKEND TEST REPORT")
        print("=" * 60)
        
        # Calculate success rates
        def calculate_success_rate(results):
            if isinstance(results, dict):
                total = 0
                successful = 0
                for key, value in results.items():
                    if isinstance(value, dict):
                        if "success" in value:
                            total += 1
                            if value["success"]:
                                successful += 1
                        else:
                            # Nested results
                            nested_total, nested_successful = calculate_success_rate(value)
                            total += nested_total
                            successful += nested_successful
                return total, successful
            return 0, 0
        
        # Infrastructure
        infra_total, infra_success = calculate_success_rate(self.test_results["infrastructure"])
        infra_rate = (infra_success / infra_total * 100) if infra_total > 0 else 0
        
        # Authentication
        auth_total, auth_success = calculate_success_rate(self.test_results["authentication"])
        auth_rate = (auth_success / auth_total * 100) if auth_total > 0 else 0
        
        # Core Features
        core_total, core_success = calculate_success_rate(self.test_results["core_features"])
        core_rate = (core_success / core_total * 100) if core_total > 0 else 0
        
        # Database Operations
        db_total, db_success = calculate_success_rate(self.test_results["database_operations"])
        db_rate = (db_success / db_total * 100) if db_total > 0 else 0
        
        # Security
        security_total, security_success = calculate_success_rate(self.test_results["security"])
        security_rate = (security_success / security_total * 100) if security_total > 0 else 0
        
        # Performance (based on response times < 500ms)
        perf_results = self.test_results["performance"]
        perf_good = sum(1 for k, v in perf_results.items() 
                       if isinstance(v, dict) and "average_response_time" in v 
                       and v["average_response_time"] < 0.5)
        perf_total = sum(1 for k, v in perf_results.items() 
                        if isinstance(v, dict) and "average_response_time" in v)
        perf_rate = (perf_good / perf_total * 100) if perf_total > 0 else 0
        
        # Error Handling
        error_total, error_success = calculate_success_rate(self.test_results["error_handling"])
        error_rate = (error_success / error_total * 100) if error_total > 0 else 0
        
        # Overall Score
        overall_score = (infra_rate + auth_rate + core_rate + db_rate + security_rate + perf_rate + error_rate) / 7
        
        print(f"🏗️ Infrastructure & Health: {infra_rate:.1f}% ({infra_success}/{infra_total})")
        print(f"🔐 Authentication System: {auth_rate:.1f}% ({auth_success}/{auth_total})")
        print(f"⚙️ Core Features: {core_rate:.1f}% ({core_success}/{core_total})")
        print(f"🗄️ Database Operations: {db_rate:.1f}% ({db_success}/{db_total})")
        print(f"🔒 Security Features: {security_rate:.1f}% ({security_success}/{security_total})")
        print(f"⚡ Performance: {perf_rate:.1f}% ({perf_good}/{perf_total} under 500ms)")
        print(f"🚨 Error Handling: {error_rate:.1f}% ({error_success}/{error_total})")
        print("-" * 40)
        print(f"🎯 OVERALL BACKEND SCORE: {overall_score:.1f}%")
        
        # Performance metrics
        if "overall_average" in perf_results:
            avg_response = perf_results["overall_average"] * 1000
            print(f"📊 Average Response Time: {avg_response:.1f}ms")
            
        # Summary
        summary = {
            "overall_score": overall_score,
            "infrastructure_rate": infra_rate,
            "authentication_rate": auth_rate,
            "core_features_rate": core_rate,
            "database_rate": db_rate,
            "security_rate": security_rate,
            "performance_rate": perf_rate,
            "error_handling_rate": error_rate,
            "total_tests": infra_total + auth_total + core_total + db_total + security_total + perf_total + error_total,
            "successful_tests": infra_success + auth_success + core_success + db_success + security_success + perf_good + error_success
        }
        
        self.test_results["summary"] = summary
        
        # Recommendations
        print(f"\n💡 RECOMMENDATIONS:")
        if overall_score >= 90:
            print("✅ EXCELLENT: Backend is production-ready with comprehensive functionality!")
        elif overall_score >= 80:
            print("✅ GOOD: Backend is solid with minor improvements needed.")
        elif overall_score >= 70:
            print("⚠️ FAIR: Some components need attention before production.")
        else:
            print("❌ NEEDS WORK: Significant issues require immediate attention.")
            
        # Specific recommendations
        if infra_rate < 90:
            print("   - Review infrastructure and health check implementations")
        if auth_rate < 90:
            print("   - Strengthen authentication system")
        if core_rate < 80:
            print("   - Fix core feature implementations")
        if security_rate < 90:
            print("   - Enhance security measures")
        if perf_rate < 80:
            print("   - Optimize API response times")
            
        # Save results
        with open("/app/comprehensive_backend_api_test_results.json", "w") as f:
            json.dump(self.test_results, f, indent=2, default=str)
            
        print(f"\n📄 Detailed results saved to: /app/comprehensive_backend_api_test_results.json")
        print(f"📊 Test completed at: {time.strftime('%Y-%m-%d %H:%M:%S')}")
        
        return overall_score

def main():
    """Main execution"""
    print("🚀 Starting Comprehensive Backend API Testing...")
    
    tester = MewayzBackendTester()
    score = tester.run_comprehensive_tests()
    
    print(f"\n✅ Testing completed with overall score: {score:.1f}%")
    return score

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 70 else 1)
    except Exception as e:
        print(f"❌ Testing failed: {e}")
        sys.exit(1)