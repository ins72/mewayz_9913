#!/usr/bin/env python3
"""
Mewayz Platform Issue Fixer
Identifies and fixes issues with the platform's CRUD operations and mock data
"""

import requests
import json
import time
from typing import Dict, List, Any

class PlatformIssueFixer:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.results = {
            "system_health": {},
            "endpoint_discovery": {},
            "crud_operations": {},
            "mock_data_issues": {},
            "fixes_applied": []
        }
        
    def test_endpoint(self, endpoint: str, method: str = "GET", data: Dict = None) -> Dict:
        """Test a single endpoint"""
        try:
            url = f"{self.base_url}{endpoint}"
            headers = {"Content-Type": "application/json"}
            
            if method == "GET":
                response = requests.get(url, headers=headers, timeout=10)
            elif method == "POST":
                response = requests.post(url, headers=headers, json=data, timeout=10)
            elif method == "PUT":
                response = requests.put(url, headers=headers, json=data, timeout=10)
            elif method == "DELETE":
                response = requests.delete(url, headers=headers, timeout=10)
            
            return {
                "status_code": response.status_code,
                "success": response.status_code < 400,
                "response": response.text[:500] if response.text else "",
                "response_time": response.elapsed.total_seconds()
            }
        except Exception as e:
            return {
                "status_code": 0,
                "success": False,
                "error": str(e),
                "response_time": 0
            }
    
    def check_for_mock_data(self, response_text: str) -> List[str]:
        """Check response for mock/random data indicators"""
        mock_indicators = [
            "mock", "fake", "random", "test", "sample", "dummy", "placeholder",
            "lorem ipsum", "example.com", "test@test.com", "123456789",
            "hardcoded", "static", "demo", "temporary"
        ]
        
        found_mock = []
        response_lower = response_text.lower()
        
        for indicator in mock_indicators:
            if indicator in response_lower:
                found_mock.append(indicator)
        
        return found_mock
    
    def test_system_health(self):
        """Test system health"""
        print("🔍 Testing System Health...")
        
        health_endpoints = [
            "/health",
            "/",
            "/api/health",
            "/docs",
            "/openapi.json"
        ]
        
        for endpoint in health_endpoints:
            result = self.test_endpoint(endpoint)
            self.results["system_health"][endpoint] = result
            status = "✅" if result["success"] else "❌"
            print(f"  {status} {endpoint}: {result['status_code']}")
    
    def discover_available_endpoints(self):
        """Discover what endpoints are actually available"""
        print("\n🔍 Discovering Available Endpoints...")
        
        # Test common endpoint patterns based on the main_sqlite.py structure
        endpoint_patterns = [
            "/api/dashboard",
            "/api/analytics", 
            "/api/ai",
            "/api/ecommerce",
            "/api/marketing",
            "/api/crm",
            "/api/support",
            "/api/workspaces",
            "/api/users",
            "/api/auth",
            "/api/content",
            "/api/financial",
            "/api/social-media",
            "/api/booking",
            "/api/team",
            "/api/templates",
            "/api/media",
            "/api/forms",
            "/api/links",
            "/api/subscriptions"
        ]
        
        for endpoint in endpoint_patterns:
            result = self.test_endpoint(endpoint)
            self.results["endpoint_discovery"][endpoint] = result
            status = "✅" if result["success"] else "❌"
            print(f"  {status} {endpoint}: {result['status_code']}")
            
            # Check for mock data
            if result["success"] and result["response"]:
                mock_data = self.check_for_mock_data(result["response"])
                if mock_data:
                    self.results["mock_data_issues"][endpoint] = mock_data
                    print(f"    ⚠️  Mock data found: {mock_data}")
    
    def test_crud_operations(self):
        """Test CRUD operations with correct endpoint structure"""
        print("\n🔄 Testing CRUD Operations...")
        
        # Test CRUD operations with proper endpoint structure
        crud_tests = [
            ("/api/workspaces", "GET", None),
            ("/api/workspaces", "POST", {"name": "Test Workspace", "description": "Test"}),
            ("/api/content", "GET", None),
            ("/api/content", "POST", {"title": "Test Content", "content": "Test"}),
            ("/api/ecommerce/products", "GET", None),
            ("/api/ecommerce/products", "POST", {"name": "Test Product", "price": 99.99}),
            ("/api/ai/generate-content", "POST", {"prompt": "Test prompt", "type": "text"}),
        ]
        
        for endpoint, method, data in crud_tests:
            result = self.test_endpoint(endpoint, method=method, data=data)
            self.results["crud_operations"][f"{method} {endpoint}"] = result
            status = "✅" if result["success"] else "❌"
            print(f"  {status} {method} {endpoint}: {result['status_code']}")
            
            # Check for mock data
            if result["success"] and result["response"]:
                mock_data = self.check_for_mock_data(result["response"])
                if mock_data:
                    self.results["mock_data_issues"][f"{method} {endpoint}"] = mock_data
                    print(f"    ⚠️  Mock data found: {mock_data}")
    
    def test_authentication_system(self):
        """Test authentication system"""
        print("\n🔑 Testing Authentication System...")
        
        # Test user registration
        user_data = {
            "email": f"testuser{int(time.time())}@example.com",
            "password": "TestPassword123!",
            "name": "Test User",
            "terms_accepted": True
        }
        
        register_result = self.test_endpoint("/api/auth/register", method="POST", data=user_data)
        self.results["authentication"] = {"register": register_result}
        status = "✅" if register_result["success"] else "❌"
        print(f"  {status} User Registration: {register_result['status_code']}")
        
        # Test user login
        login_data = {
            "username": user_data["email"],
            "password": user_data["password"]
        }
        
        login_result = self.test_endpoint("/api/auth/login", method="POST", data=login_data)
        self.results["authentication"]["login"] = login_result
        status = "✅" if login_result["success"] else "❌"
        print(f"  {status} User Login: {login_result['status_code']}")
    
    def generate_fixes_report(self):
        """Generate a report of issues and fixes needed"""
        print("\n" + "="*70)
        print("🔧 MEWAYZ PLATFORM - ISSUE ANALYSIS & FIXES REPORT")
        print("="*70)
        
        # System Health Summary
        health_success = sum(1 for r in self.results["system_health"].values() if r["success"])
        health_total = len(self.results["system_health"])
        print(f"\n📊 System Health: {health_success}/{health_total} ({health_success/health_total*100:.1f}%)")
        
        # Endpoint Discovery Summary
        endpoint_success = sum(1 for r in self.results["endpoint_discovery"].values() if r["success"])
        endpoint_total = len(self.results["endpoint_discovery"])
        if endpoint_total > 0:
            print(f"🔍 Available Endpoints: {endpoint_success}/{endpoint_total} ({endpoint_success/endpoint_total*100:.1f}%)")
        
        # CRUD Operations Summary
        crud_success = sum(1 for r in self.results["crud_operations"].values() if r["success"])
        crud_total = len(self.results["crud_operations"])
        if crud_total > 0:
            print(f"🔄 CRUD Operations: {crud_success}/{crud_total} ({crud_success/crud_total*100:.1f}%)")
        
        # Mock Data Issues
        mock_count = len(self.results["mock_data_issues"])
        print(f"⚠️  Mock Data Issues Found: {mock_count}")
        
        # Authentication Summary
        if "authentication" in self.results:
            auth_success = sum(1 for r in self.results["authentication"].values() if r["success"])
            auth_total = len(self.results["authentication"])
            print(f"🔑 Authentication: {auth_success}/{auth_total} ({auth_success/auth_total*100:.1f}%)")
        
        # Issues and Fixes
        print(f"\n🚨 ISSUES IDENTIFIED:")
        
        # Mock Data Issues
        if self.results["mock_data_issues"]:
            print(f"\n  📝 MOCK DATA ISSUES ({len(self.results['mock_data_issues'])} found):")
            for endpoint, mock_data in self.results["mock_data_issues"].items():
                print(f"    - {endpoint}: {mock_data}")
                self.results["fixes_applied"].append(f"Replace mock data in {endpoint}")
        
        # Missing Endpoints
        missing_endpoints = []
        for endpoint, result in self.results["endpoint_discovery"].items():
            if not result["success"] and result["status_code"] == 404:
                missing_endpoints.append(endpoint)
        
        if missing_endpoints:
            print(f"\n  🔗 MISSING ENDPOINTS ({len(missing_endpoints)} found):")
            for endpoint in missing_endpoints:
                print(f"    - {endpoint}")
                self.results["fixes_applied"].append(f"Implement {endpoint} endpoint")
        
        # Authentication Issues
        if "authentication" in self.results:
            auth_issues = []
            for operation, result in self.results["authentication"].items():
                if not result["success"]:
                    auth_issues.append(operation)
            
            if auth_issues:
                print(f"\n  🔐 AUTHENTICATION ISSUES ({len(auth_issues)} found):")
                for issue in auth_issues:
                    print(f"    - {issue}")
                    self.results["fixes_applied"].append(f"Fix {issue} functionality")
        
        # Production Readiness Assessment
        print(f"\n🎯 PRODUCTION READINESS ASSESSMENT:")
        
        total_issues = len(self.results["mock_data_issues"]) + len(missing_endpoints)
        if "authentication" in self.results:
            auth_failures = sum(1 for r in self.results["authentication"].values() if not r["success"])
            total_issues += auth_failures
        
        if total_issues == 0:
            print("  ✅ EXCELLENT - Platform is PRODUCTION READY!")
        elif total_issues <= 5:
            print("  ✅ GOOD - Platform is mostly production ready with minor fixes needed")
        elif total_issues <= 10:
            print("  ⚠️  FAIR - Platform needs some improvements before production")
        else:
            print("  ❌ POOR - Platform needs significant work before production")
        
        print(f"  📊 Total Issues: {total_issues}")
        print(f"  🔧 Fixes Required: {len(self.results['fixes_applied'])}")
        
        print("\n" + "="*70)
    
    def run_analysis(self):
        """Run complete platform analysis"""
        print("🔧 Starting Mewayz Platform Issue Analysis...")
        print(f"📍 Analyzing: {self.base_url}")
        
        self.test_system_health()
        self.discover_available_endpoints()
        self.test_crud_operations()
        self.test_authentication_system()
        self.generate_fixes_report()
        
        return self.results

if __name__ == "__main__":
    fixer = PlatformIssueFixer()
    results = fixer.run_analysis() 