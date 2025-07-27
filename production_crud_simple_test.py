#!/usr/bin/env python3
"""
Production CRUD Test for Mewayz Platform
Verifies all CRUD operations and checks for mock data
"""

import requests
import json
import time
from typing import Dict, List, Any

class MewayzCRUDTester:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.results = {
            "system_health": {},
            "crud_operations": {},
            "mock_data_check": {},
            "overall_score": 0
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
        """Test system health endpoints"""
        print("🔍 Testing System Health...")
        
        health_endpoints = [
            "/health",
            "/",
            "/docs",
            "/openapi.json"
        ]
        
        for endpoint in health_endpoints:
            result = self.test_endpoint(endpoint)
            self.results["system_health"][endpoint] = result
            status = "✅" if result["success"] else "❌"
            print(f"  {status} {endpoint}: {result['status_code']}")
    
    def test_crud_operations(self):
        """Test CRUD operations"""
        print("\n🔧 Testing CRUD Operations...")
        
        # Test public endpoints that should work
        public_endpoints = [
            "/api/analytics/dashboard",
            "/api/dashboard/metrics",
            "/api/advanced-ai/overview",
            "/api/advanced-ai-analytics/overview",
            "/api/financial-management/overview",
            "/api/advanced-financial-analytics/overview",
            "/api/customer-experience/overview",
            "/api/workflow-automation/overview"
        ]
        
        for endpoint in public_endpoints:
            result = self.test_endpoint(endpoint)
            self.results["crud_operations"][endpoint] = result
            status = "✅" if result["success"] else "❌"
            print(f"  {status} {endpoint}: {result['status_code']}")
            
            # Check for mock data
            if result["success"] and result["response"]:
                mock_data = self.check_for_mock_data(result["response"])
                if mock_data:
                    self.results["mock_data_check"][endpoint] = mock_data
                    print(f"    ⚠️  Mock data found: {mock_data}")
    
    def test_database_operations(self):
        """Test database-dependent operations"""
        print("\n💾 Testing Database Operations...")
        
        # Test endpoints that should use real database
        db_endpoints = [
            "/api/dashboard/overview",
            "/api/analytics/overview", 
            "/api/ai/services",
            "/api/ecommerce/products",
            "/api/ecommerce/dashboard",
            "/api/marketing/analytics",
            "/api/crm-management/contacts",
            "/api/support-system/tickets"
        ]
        
        for endpoint in db_endpoints:
            result = self.test_endpoint(endpoint)
            self.results["crud_operations"][endpoint] = result
            status = "✅" if result["success"] else "❌"
            print(f"  {status} {endpoint}: {result['status_code']}")
            
            # Check for mock data
            if result["success"] and result["response"]:
                mock_data = self.check_for_mock_data(result["response"])
                if mock_data:
                    self.results["mock_data_check"][endpoint] = mock_data
                    print(f"    ⚠️  Mock data found: {mock_data}")
    
    def calculate_score(self):
        """Calculate overall success score"""
        total_tests = 0
        passed_tests = 0
        
        # Count system health tests
        for result in self.results["system_health"].values():
            total_tests += 1
            if result["success"]:
                passed_tests += 1
        
        # Count CRUD tests
        for result in self.results["crud_operations"].values():
            total_tests += 1
            if result["success"]:
                passed_tests += 1
        
        if total_tests > 0:
            self.results["overall_score"] = (passed_tests / total_tests) * 100
        
        # Deduct points for mock data
        mock_data_count = len(self.results["mock_data_check"])
        if mock_data_count > 0:
            self.results["overall_score"] = max(0, self.results["overall_score"] - (mock_data_count * 5))
    
    def generate_report(self):
        """Generate comprehensive report"""
        print("\n" + "="*60)
        print("🎯 MEWAYZ PLATFORM - PRODUCTION CRUD TEST REPORT")
        print("="*60)
        
        # System Health Summary
        health_success = sum(1 for r in self.results["system_health"].values() if r["success"])
        health_total = len(self.results["system_health"])
        print(f"\n📊 System Health: {health_success}/{health_total} ({health_success/health_total*100:.1f}%)")
        
        # CRUD Operations Summary
        crud_success = sum(1 for r in self.results["crud_operations"].values() if r["success"])
        crud_total = len(self.results["crud_operations"])
        print(f"🔧 CRUD Operations: {crud_success}/{crud_total} ({crud_success/crud_total*100:.1f}%)")
        
        # Mock Data Summary
        mock_count = len(self.results["mock_data_check"])
        print(f"⚠️  Mock Data Found: {mock_count} endpoints")
        
        # Overall Score
        print(f"\n🎯 Overall Score: {self.results['overall_score']:.1f}%")
        
        if self.results["overall_score"] >= 90:
            print("✅ PRODUCTION READY - Excellent performance!")
        elif self.results["overall_score"] >= 80:
            print("✅ PRODUCTION READY - Good performance with minor issues")
        elif self.results["overall_score"] >= 70:
            print("⚠️  NEARLY READY - Some issues need attention")
        else:
            print("❌ NOT READY - Significant issues need fixing")
        
        # Mock Data Details
        if self.results["mock_data_check"]:
            print(f"\n🚨 MOCK DATA ISSUES FOUND:")
            for endpoint, mock_data in self.results["mock_data_check"].items():
                print(f"  {endpoint}: {mock_data}")
        
        print("\n" + "="*60)
    
    def run_all_tests(self):
        """Run all tests"""
        print("🚀 Starting Mewayz Platform Production CRUD Test...")
        print(f"📍 Testing against: {self.base_url}")
        
        self.test_system_health()
        self.test_crud_operations()
        self.test_database_operations()
        self.calculate_score()
        self.generate_report()
        
        return self.results

if __name__ == "__main__":
    tester = MewayzCRUDTester()
    results = tester.run_all_tests() 