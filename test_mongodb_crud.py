#!/usr/bin/env python3
"""
MongoDB CRUD Test
Comprehensive test for MongoDB CRUD operations
"""

import requests
import json
import time
from datetime import datetime

def test_endpoint(url, method="GET", data=None, headers=None):
    """Test a single endpoint"""
    try:
        if method.upper() == "GET":
            response = requests.get(url, timeout=10, headers=headers)
        elif method.upper() == "POST":
            response = requests.post(url, json=data, timeout=10, headers=headers)
        elif method.upper() == "PUT":
            response = requests.put(url, json=data, timeout=10, headers=headers)
        elif method.upper() == "DELETE":
            response = requests.delete(url, timeout=10, headers=headers)
        
        return {
            "status": response.status_code,
            "response": response.text[:500] if response.text else "",
            "headers": dict(response.headers),
            "success": response.status_code < 400
        }
    except Exception as e:
        return {
            "status": 0,
            "response": str(e),
            "headers": {},
            "success": False
        }

def test_mongodb_crud():
    """Test MongoDB CRUD operations"""
    print("🔍 Testing MongoDB CRUD Operations...")
    
    base_url = "http://localhost:8002"
    results = []
    
    # Test system health
    print("\n🏥 Testing System Health...")
    health_result = test_endpoint(f"{base_url}/health")
    print(f"  Health: Status {health_result['status']}")
    results.append({
        "test": "System Health",
        "status": health_result['status'],
        "success": health_result['success']
    })
    
    # Test authentication
    print("\n🔐 Testing Authentication...")
    
    # Test user registration
    register_data = {
        "email": "testuser@mewayz.com",
        "username": "testuser",
        "password": "TestPassword123!",
        "full_name": "Test User"
    }
    
    register_result = test_endpoint(f"{base_url}/api/auth/register", "POST", register_data)
    print(f"  Registration: Status {register_result['status']}")
    results.append({
        "test": "User Registration",
        "status": register_result['status'],
        "success": register_result['success']
    })
    
    # Test user login
    login_data = {
        "username": "testuser",
        "password": "TestPassword123!"
    }
    
    login_result = test_endpoint(f"{base_url}/api/auth/login", "POST", login_data)
    print(f"  Login: Status {login_result['status']}")
    results.append({
        "test": "User Login",
        "status": login_result['status'],
        "success": login_result['success']
    })
    
    # Test READ operations
    print("\n📖 Testing READ Operations...")
    
    read_endpoints = [
        ("/api/analytics/overview", "Analytics Overview"),
        ("/api/ecommerce/products", "E-commerce Products"),
        ("/api/crm-management/contacts", "CRM Contacts"),
        ("/api/support-system/tickets", "Support Tickets"),
        ("/api/workspace/", "Workspaces"),
        ("/api/ai/services", "AI Services"),
        ("/api/dashboard/overview", "Dashboard Overview"),
        ("/api/marketing/analytics", "Marketing Analytics")
    ]
    
    for endpoint, name in read_endpoints:
        result = test_endpoint(f"{base_url}{endpoint}")
        print(f"  {name}: Status {result['status']}")
        results.append({
            "test": f"READ {name}",
            "status": result['status'],
            "success": result['success']
        })
    
    # Test CREATE operations
    print("\n➕ Testing CREATE Operations...")
    
    # Create workspace
    workspace_data = {
        "name": "Test Workspace",
        "description": "Test workspace for CRUD testing",
        "user_id": "testuser"
    }
    
    create_workspace_result = test_endpoint(f"{base_url}/api/workspace/", "POST", workspace_data)
    print(f"  Create Workspace: Status {create_workspace_result['status']}")
    results.append({
        "test": "CREATE Workspace",
        "status": create_workspace_result['status'],
        "success": create_workspace_result['success']
    })
    
    # Create product
    product_data = {
        "name": "Test Product",
        "description": "Test product for CRUD testing",
        "price": 99.99,
        "category": "test",
        "workspace_id": "testuser"
    }
    
    create_product_result = test_endpoint(f"{base_url}/api/ecommerce/products", "POST", product_data)
    print(f"  Create Product: Status {create_product_result['status']}")
    results.append({
        "test": "CREATE Product",
        "status": create_product_result['status'],
        "success": create_product_result['success']
    })
    
    # Create contact
    contact_data = {
        "name": "Test Contact",
        "email": "test.contact@example.com",
        "phone": "+1-555-0123",
        "company": "Test Company",
        "position": "Test Position",
        "workspace_id": "testuser"
    }
    
    create_contact_result = test_endpoint(f"{base_url}/api/crm-management/contacts", "POST", contact_data)
    print(f"  Create Contact: Status {create_contact_result['status']}")
    results.append({
        "test": "CREATE Contact",
        "status": create_contact_result['status'],
        "success": create_contact_result['success']
    })
    
    # Test UPDATE operations (if we have IDs from create operations)
    print("\n✏️ Testing UPDATE Operations...")
    
    # Try to update workspace (we'll need to get an ID first)
    try:
        workspaces_response = requests.get(f"{base_url}/api/workspace/")
        if workspaces_response.status_code == 200:
            workspaces_data = workspaces_response.json()
            if workspaces_data.get("workspaces") and len(workspaces_data["workspaces"]) > 0:
                workspace_id = workspaces_data["workspaces"][0]["_id"]
                
                update_workspace_data = {
                    "name": "Updated Test Workspace",
                    "description": "Updated test workspace"
                }
                
                update_workspace_result = test_endpoint(f"{base_url}/api/workspace/{workspace_id}", "PUT", update_workspace_data)
                print(f"  Update Workspace: Status {update_workspace_result['status']}")
                results.append({
                    "test": "UPDATE Workspace",
                    "status": update_workspace_result['status'],
                    "success": update_workspace_result['success']
                })
    except Exception as e:
        print(f"  Update Workspace: Error {e}")
        results.append({
            "test": "UPDATE Workspace",
            "status": 0,
            "success": False
        })
    
    # Test DELETE operations
    print("\n🗑️ Testing DELETE Operations...")
    
    # Try to delete workspace
    try:
        workspaces_response = requests.get(f"{base_url}/api/workspace/")
        if workspaces_response.status_code == 200:
            workspaces_data = workspaces_response.json()
            if workspaces_data.get("workspaces") and len(workspaces_data["workspaces"]) > 0:
                workspace_id = workspaces_data["workspaces"][0]["_id"]
                
                delete_workspace_result = test_endpoint(f"{base_url}/api/workspace/{workspace_id}", "DELETE")
                print(f"  Delete Workspace: Status {delete_workspace_result['status']}")
                results.append({
                    "test": "DELETE Workspace",
                    "status": delete_workspace_result['status'],
                    "success": delete_workspace_result['success']
                })
    except Exception as e:
        print(f"  Delete Workspace: Error {e}")
        results.append({
            "test": "DELETE Workspace",
            "status": 0,
            "success": False
        })
    
    # Test public endpoints
    print("\n🌐 Testing Public Endpoints...")
    
    public_endpoints = [
        ("/api/website-builder/health", "Website Builder Health"),
        ("/api/template-marketplace/health", "Template Marketplace Health"),
        ("/api/team-management/health", "Team Management Health"),
        ("/api/webhook/health", "Webhook Health"),
        ("/api/workflow-automation/health", "Workflow Automation Health")
    ]
    
    for endpoint, name in public_endpoints:
        result = test_endpoint(f"{base_url}{endpoint}")
        print(f"  {name}: Status {result['status']}")
        results.append({
            "test": f"Public {name}",
            "status": result['status'],
            "success": result['success']
        })
    
    # Analyze results
    total_tests = len(results)
    successful_tests = sum(1 for r in results if r['success'])
    
    print(f"\n📊 MONGODB CRUD TEST RESULTS")
    print("=" * 60)
    print(f"Total Tests: {total_tests}")
    print(f"Successful Tests: {successful_tests}")
    print(f"Failed Tests: {total_tests - successful_tests}")
    print(f"Success Rate: {(successful_tests/total_tests*100):.1f}%")
    
    # Show failed tests
    failed_tests = [r for r in results if not r['success']]
    if failed_tests:
        print(f"\n❌ FAILED TESTS:")
        for test in failed_tests:
            print(f"  - {test['test']}: Status {test['status']}")
    
    # Final assessment
    print(f"\n🎯 FINAL ASSESSMENT")
    print("=" * 60)
    
    if successful_tests == total_tests:
        print("✅ EXCELLENT - All MongoDB CRUD operations working perfectly")
        print("🚀 Platform is ready for production deployment")
    elif successful_tests >= total_tests * 0.8:
        print("🟡 GOOD - Most MongoDB CRUD operations working")
        print("🔧 Minor issues need to be addressed")
    else:
        print("❌ POOR - Many MongoDB CRUD operations failing")
        print("🔧 Significant issues need to be fixed")
    
    # Save results
    test_results = {
        "timestamp": datetime.now().isoformat(),
        "test_type": "MongoDB CRUD Operations",
        "base_url": base_url,
        "results": results,
        "summary": {
            "total_tests": total_tests,
            "successful_tests": successful_tests,
            "failed_tests": total_tests - successful_tests,
            "success_rate": (successful_tests/total_tests*100) if total_tests > 0 else 0
        }
    }
    
    with open("mongodb_crud_test_results.json", "w") as f:
        json.dump(test_results, f, indent=2)
    
    print(f"\n📄 Results saved to: mongodb_crud_test_results.json")
    print("=" * 60)

if __name__ == "__main__":
    test_mongodb_crud() 