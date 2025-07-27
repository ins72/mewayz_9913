#!/usr/bin/env python3
"""
Simple Mewayz Platform Test
Quick verification of platform status and working endpoints
"""

import requests
import json

def test_endpoint(url, method="GET", data=None):
    """Test a single endpoint"""
    try:
        headers = {"Content-Type": "application/json"}
        
        if method == "GET":
            response = requests.get(url, headers=headers, timeout=10)
        elif method == "POST":
            response = requests.post(url, headers=headers, json=data, timeout=10)
        
        return {
            "status_code": response.status_code,
            "success": response.status_code < 400,
            "response": response.text[:200] if response.text else "",
            "response_time": response.elapsed.total_seconds()
        }
    except Exception as e:
        return {
            "status_code": 0,
            "success": False,
            "error": str(e),
            "response_time": 0
        }

def main():
    base_url = "http://localhost:8001"
    
    print("🔍 Simple Mewayz Platform Test")
    print("=" * 50)
    
    # Test system health
    print("\n📊 System Health:")
    health_endpoints = [
        "/health",
        "/",
        "/api/health",
        "/docs"
    ]
    
    for endpoint in health_endpoints:
        result = test_endpoint(f"{base_url}{endpoint}")
        status = "✅" if result["success"] else "❌"
        print(f"  {status} {endpoint}: {result['status_code']}")
    
    # Test some business endpoints
    print("\n🏢 Business Endpoints:")
    business_endpoints = [
        "/api/dashboard/overview",
        "/api/analytics/dashboard",
        "/api/workspaces",
        "/api/users",
        "/api/auth/register",
        "/api/ecommerce/products",
        "/api/content",
        "/api/ai/services"
    ]
    
    for endpoint in business_endpoints:
        result = test_endpoint(f"{base_url}{endpoint}")
        status = "✅" if result["success"] else "❌"
        print(f"  {status} {endpoint}: {result['status_code']}")
        
        # If it's a 401/403, that's expected for protected endpoints
        if result["status_code"] in [401, 403]:
            print(f"    ℹ️  Protected endpoint (authentication required)")
        elif result["status_code"] == 404:
            print(f"    ❌ Endpoint not found")
        elif result["status_code"] >= 500:
            print(f"    ⚠️  Server error")
    
    # Test authentication
    print("\n🔐 Authentication Test:")
    user_data = {
        "email": "test@example.com",
        "password": "TestPassword123!",
        "name": "Test User",
        "terms_accepted": True
    }
    
    register_result = test_endpoint(f"{base_url}/api/auth/register", method="POST", data=user_data)
    status = "✅" if register_result["success"] else "❌"
    print(f"  {status} Registration: {register_result['status_code']}")
    
    if register_result["status_code"] == 500:
        print(f"    ⚠️  Server error - may need database setup")
    elif register_result["status_code"] == 422:
        print(f"    ℹ️  Validation error - endpoint exists but data invalid")
    
    # Summary
    print("\n📋 Summary:")
    print("  - System health endpoints should all return 200")
    print("  - Business endpoints should return 401/403 (protected) or 404 (not found)")
    print("  - Authentication should return 422 (validation) or 500 (server error)")
    print("  - 404 means endpoint doesn't exist")
    print("  - 500 means server error (database/configuration issue)")

if __name__ == "__main__":
    main() 