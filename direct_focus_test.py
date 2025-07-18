#!/usr/bin/env python3
"""
Simple Direct Testing for Current Focus Tasks
Using direct curl-like approach that works
"""

import subprocess
import json
import time

class DirectTester:
    def __init__(self):
        self.base_url = "http://localhost:8001/api"
        self.auth_token = "3|yHHRGVcNjzxdu8szdT1LRua2Dy2GPnff0iQyCSm7cf941e64"
        self.results = {}
        
    def make_curl_request(self, endpoint, method="GET", data=None):
        """Make request using curl subprocess"""
        url = f"{self.base_url}{endpoint}"
        
        cmd = [
            "curl", "-s", 
            "-H", f"Authorization: Bearer {self.auth_token}",
            "-H", "Accept: application/json",
            "--max-time", "10"
        ]
        
        if method == "POST" and data:
            cmd.extend(["-H", "Content-Type: application/json"])
            cmd.extend(["-d", json.dumps(data)])
            cmd.extend(["-X", "POST"])
        
        cmd.append(url)
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=12)
            if result.returncode == 0:
                try:
                    return json.loads(result.stdout)
                except json.JSONDecodeError:
                    return {"raw_response": result.stdout}
            else:
                return {"error": f"Curl failed: {result.stderr}"}
        except subprocess.TimeoutExpired:
            return {"error": "Request timeout"}
        except Exception as e:
            return {"error": f"Request failed: {str(e)}"}
    
    def log_result(self, test_name, success, message):
        """Log test result"""
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        self.results[test_name] = {"success": success, "message": message}
    
    def test_current_focus_tasks(self):
        """Test the 4 current focus tasks"""
        print("🎯 DIRECT TESTING FOR CURRENT_FOCUS TASKS")
        print("=" * 60)
        
        # Test 1: Enhanced Controllers - BioSiteController
        print("\n--- Testing BioSiteController ---")
        response = self.make_curl_request("/bio-sites/")
        if "error" in response:
            self.log_result("BioSiteController", False, f"Request error: {response['error']}")
        elif "success" in response and response["success"] is False:
            self.log_result("BioSiteController", False, f"Controller error: {response.get('message', 'Unknown error')}")
        elif "success" in response and response["success"] is True:
            self.log_result("BioSiteController", True, "Bio sites retrieved successfully")
        else:
            self.log_result("BioSiteController", False, f"Unexpected response: {response}")
        
        time.sleep(1)
        
        # Test 2: Enhanced Controllers - EmailMarketingController
        print("\n--- Testing EmailMarketingController ---")
        response = self.make_curl_request("/email-marketing/campaigns")
        if "error" in response:
            if response["error"] == "Request timeout":
                self.log_result("EmailMarketingController", False, "Request timeout - controller implementation issue")
            else:
                self.log_result("EmailMarketingController", False, f"Request error: {response['error']}")
        elif "error" in response and "Failed to fetch campaigns" in str(response):
            self.log_result("EmailMarketingController", False, "Controller error: Failed to fetch campaigns")
        elif "success" in response and response["success"] is True:
            self.log_result("EmailMarketingController", True, "Email campaigns retrieved successfully")
        else:
            # Check if response contains error message
            response_str = str(response)
            if "Failed to fetch campaigns" in response_str:
                self.log_result("EmailMarketingController", False, "Controller error: Failed to fetch campaigns")
            else:
                self.log_result("EmailMarketingController", False, f"Unexpected response: {response}")
        
        time.sleep(1)
        
        # Test 3: Admin Dashboard System
        print("\n--- Testing Admin Dashboard System ---")
        response = self.make_curl_request("/admin/dashboard")
        if "error" in response:
            if "Admin access required" in str(response):
                self.log_result("Admin Dashboard", False, "Expected behavior: Admin access required (non-admin user)")
            elif response["error"] == "Request timeout":
                self.log_result("Admin Dashboard", False, "Request timeout - controller implementation issue")
            else:
                self.log_result("Admin Dashboard", False, f"Request error: {response['error']}")
        elif "error" in response and "Admin access required" in str(response):
            self.log_result("Admin Dashboard", False, "Expected behavior: Admin access required (non-admin user)")
        elif "success" in response and response["success"] is True:
            self.log_result("Admin Dashboard", True, "Admin dashboard accessed successfully")
        else:
            # Check if response contains admin access error
            response_str = str(response)
            if "Admin access required" in response_str:
                self.log_result("Admin Dashboard", False, "Expected behavior: Admin access required (non-admin user)")
            else:
                self.log_result("Admin Dashboard", False, f"Unexpected response: {response}")
        
        time.sleep(1)
        
        # Test 4: Escrow & Transaction Security
        print("\n--- Testing Escrow System ---")
        response = self.make_curl_request("/escrow/")
        if "error" in response:
            if response["error"] == "Request timeout":
                self.log_result("Escrow System", False, "Request timeout - controller implementation issue")
            else:
                self.log_result("Escrow System", False, f"Request error: {response['error']}")
        elif "success" in response and response["success"] is False:
            self.log_result("Escrow System", False, f"Controller error: {response.get('message', 'Unknown error')}")
        elif "success" in response and response["success"] is True:
            self.log_result("Escrow System", True, "Escrow transactions retrieved successfully")
        else:
            self.log_result("Escrow System", False, f"Unexpected response: {response}")
        
        time.sleep(1)
        
        # Test 5: Advanced Booking System
        print("\n--- Testing Advanced Booking System ---")
        response = self.make_curl_request("/booking/services")
        if "error" in response:
            if response["error"] == "Request timeout":
                self.log_result("Advanced Booking System", False, "Request timeout - controller implementation issue")
            else:
                self.log_result("Advanced Booking System", False, f"Request error: {response['error']}")
        elif "success" in response and response["success"] is False:
            self.log_result("Advanced Booking System", False, f"Controller error: {response.get('message', 'Unknown error')}")
        elif "success" in response and response["success"] is True:
            self.log_result("Advanced Booking System", True, "Booking services retrieved successfully")
        else:
            self.log_result("Advanced Booking System", False, f"Unexpected response: {response}")
        
        # Print summary
        print("\n" + "=" * 60)
        print("📊 DIRECT TEST SUMMARY")
        print("=" * 60)
        
        passed = sum(1 for result in self.results.values() if result['success'])
        total = len(self.results)
        success_rate = (passed / total * 100) if total > 0 else 0
        
        print(f"Total Tests: {total}")
        print(f"✅ Passed: {passed}")
        print(f"❌ Failed: {total - passed}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        print("\n🔍 DETAILED RESULTS:")
        for test_name, result in self.results.items():
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {test_name}: {result['message']}")
        
        return self.results

if __name__ == "__main__":
    tester = DirectTester()
    results = tester.test_current_focus_tasks()