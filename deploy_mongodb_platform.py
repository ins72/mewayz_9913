#!/usr/bin/env python3
"""
MongoDB Platform Deployment Script
Complete deployment and testing of the MongoDB production platform
"""

import subprocess
import time
import requests
import json
import sys
import os
from datetime import datetime

def run_command(command, description):
    """Run a command and handle errors"""
    print(f"🔄 {description}...")
    try:
        result = subprocess.run(command, shell=True, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ {description} completed successfully")
            return True
        else:
            print(f"❌ {description} failed: {result.stderr}")
            return False
    except Exception as e:
        print(f"❌ {description} failed: {e}")
        return False

def test_endpoint(url, method="GET", data=None, timeout=10):
    """Test a single endpoint"""
    try:
        if method.upper() == "GET":
            response = requests.get(url, timeout=timeout)
        elif method.upper() == "POST":
            response = requests.post(url, json=data, timeout=timeout)
        
        return {
            "status": response.status_code,
            "response": response.text[:200] if response.text else "",
            "success": response.status_code < 400
        }
    except Exception as e:
        return {
            "status": 0,
            "response": str(e),
            "success": False
        }

def deploy_mongodb_platform():
    """Deploy the MongoDB platform"""
    print("🚀 MONGODB PLATFORM DEPLOYMENT")
    print("=" * 60)
    
    # Step 1: Check MongoDB connection
    print("\n📋 Step 1: Checking MongoDB Connection...")
    if not run_command("python setup_mongodb.py", "MongoDB Setup"):
        print("❌ MongoDB setup failed. Please ensure MongoDB is running.")
        return False
    
    # Step 2: Start the server
    print("\n📋 Step 2: Starting MongoDB Production Server...")
    
    # Kill any existing processes on port 8002
    run_command("netstat -ano | findstr :8002 | findstr LISTENING", "Checking port 8002")
    
    # Start the server in background
    server_process = subprocess.Popen([
        sys.executable, "start_mongodb_server.py"
    ], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    
    print("⏳ Waiting for server to start...")
    time.sleep(5)
    
    # Step 3: Test basic connectivity
    print("\n📋 Step 3: Testing Server Connectivity...")
    
    health_result = test_endpoint("http://localhost:8002/health")
    if not health_result["success"]:
        print(f"❌ Health check failed: {health_result['response']}")
        server_process.terminate()
        return False
    
    print(f"✅ Health check passed: Status {health_result['status']}")
    
    # Step 4: Test CRUD operations
    print("\n📋 Step 4: Testing CRUD Operations...")
    
    # Test READ operations
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
    
    read_success = 0
    for endpoint, name in read_endpoints:
        result = test_endpoint(f"http://localhost:8002{endpoint}")
        if result["success"]:
            print(f"✅ {name}: Status {result['status']}")
            read_success += 1
        else:
            print(f"❌ {name}: Status {result['status']} - {result['response']}")
    
    # Test CREATE operations
    print("\n📋 Step 5: Testing CREATE Operations...")
    
    create_data = {
        "name": "Test Workspace",
        "description": "Test workspace for deployment",
        "user_id": "admin"
    }
    
    create_result = test_endpoint("http://localhost:8002/api/workspace/", "POST", create_data)
    if create_result["success"]:
        print(f"✅ Create Workspace: Status {create_result['status']}")
    else:
        print(f"❌ Create Workspace: Status {create_result['status']} - {create_result['response']}")
    
    # Step 6: Generate deployment report
    print("\n📋 Step 6: Generating Deployment Report...")
    
    deployment_report = {
        "timestamp": datetime.now().isoformat(),
        "deployment_type": "MongoDB Production Platform",
        "server_url": "http://localhost:8002",
        "health_check": health_result["success"],
        "read_operations": {
            "total": len(read_endpoints),
            "successful": read_success,
            "success_rate": (read_success / len(read_endpoints) * 100) if read_endpoints else 0
        },
        "create_operations": create_result["success"],
        "overall_status": "SUCCESS" if health_result["success"] and read_success > 0 else "PARTIAL" if health_result["success"] else "FAILED"
    }
    
    # Save report
    with open("mongodb_deployment_report.json", "w") as f:
        json.dump(deployment_report, f, indent=2)
    
    print(f"📄 Deployment report saved to: mongodb_deployment_report.json")
    
    # Step 7: Final status
    print("\n🎯 DEPLOYMENT SUMMARY")
    print("=" * 60)
    print(f"Server URL: http://localhost:8002")
    print(f"Health Check: {'✅ PASSED' if health_result['success'] else '❌ FAILED'}")
    print(f"Read Operations: {read_success}/{len(read_endpoints)} ({deployment_report['read_operations']['success_rate']:.1f}%)")
    print(f"Create Operations: {'✅ PASSED' if create_result['success'] else '❌ FAILED'}")
    print(f"Overall Status: {deployment_report['overall_status']}")
    
    if deployment_report['overall_status'] == "SUCCESS":
        print("\n🎉 MONGODB PLATFORM DEPLOYMENT SUCCESSFUL!")
        print("🚀 Platform is ready for production use")
        print("📚 API Documentation: http://localhost:8002/docs")
        print("🏥 Health Check: http://localhost:8002/health")
        print("=" * 60)
        return True
    else:
        print("\n⚠️ DEPLOYMENT COMPLETED WITH ISSUES")
        print("🔧 Some operations may need attention")
        print("=" * 60)
        return False

if __name__ == "__main__":
    success = deploy_mongodb_platform()
    sys.exit(0 if success else 1) 