#!/usr/bin/env python3
"""
Instagram Management System - Comprehensive Backend Testing Suite
Tests all 8 Instagram Management API endpoints for Phase 2 implementation
"""

import requests
import json
import sys
import time
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, List

class InstagramManagementTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        
        # Test user credentials
        self.test_user = {
            "email": "testuser@example.com",
            "password": "password123",
            "token": None,
            "id": None
        }
        
        self.test_results = []
        self.issues_found = []
        
    def log_result(self, test_name: str, status: str, details: str = "", response_time: float = 0):
        """Log test result"""
        result = {
            "test": test_name,
            "status": status,
            "details": details,
            "response_time": response_time,
            "timestamp": datetime.now().isoformat()
        }
        self.test_results.append(result)
        
        status_icon = "✅" if status == "PASS" else "❌" if status == "FAIL" else "⚠️"
        print(f"{status_icon} {test_name}: {status}")
        if details:
            print(f"   Details: {details}")
        if response_time > 0:
            print(f"   Response time: {response_time:.3f}s")
        print()

    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None) -> tuple:
        """Make HTTP request and return response and timing"""
        url = f"{self.api_url}{endpoint}"
        
        # Add auth header if token exists
        if self.test_user["token"]:
            if not headers:
                headers = {}
            headers["Authorization"] = f"Bearer {self.test_user['token']}"
            headers["Accept"] = "application/json"
            headers["Content-Type"] = "application/json"
        
        start_time = time.time()
        try:
            if method.upper() == "GET":
                response = self.session.get(url, headers=headers, params=data)
            elif method.upper() == "POST":
                response = self.session.post(url, headers=headers, json=data)
            elif method.upper() == "PUT":
                response = self.session.put(url, headers=headers, json=data)
            elif method.upper() == "DELETE":
                response = self.session.delete(url, headers=headers)
            else:
                raise ValueError(f"Unsupported method: {method}")
                
            response_time = time.time() - start_time
            return response, response_time
        except Exception as e:
            response_time = time.time() - start_time
            return None, response_time

    def authenticate_user(self) -> bool:
        """Authenticate test user and get token"""
        print("🔐 Authenticating test user...")
        
        # First try to register user
        register_data = {
            "name": "Test User",
            "email": self.test_user["email"],
            "password": self.test_user["password"],
            "password_confirmation": self.test_user["password"]
        }
        
        response, response_time = self.make_request("POST", "/auth/register", register_data)
        
        # Then login (whether register succeeded or failed)
        login_data = {
            "email": self.test_user["email"],
            "password": self.test_user["password"]
        }
        
        response, response_time = self.make_request("POST", "/auth/login", login_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if "access_token" in data:
                    self.test_user["token"] = data["access_token"]
                    self.test_user["id"] = data.get("user", {}).get("id")
                    self.log_result("User Authentication", "PASS", 
                                  f"Successfully authenticated user: {self.test_user['email']}", response_time)
                    return True
            except json.JSONDecodeError:
                pass
        
        self.log_result("User Authentication", "FAIL", 
                       f"Failed to authenticate user. Status: {response.status_code if response else 'No response'}", response_time)
        return False

    def test_instagram_accounts_get(self) -> bool:
        """Test GET /api/instagram/accounts - Get workspace accounts"""
        print("📱 Testing Instagram Accounts - GET...")
        
        response, response_time = self.make_request("GET", "/instagram/accounts")
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "accounts" in data:
                    accounts = data["accounts"]
                    self.log_result("GET Instagram Accounts", "PASS", 
                                  f"Retrieved {len(accounts)} Instagram accounts", response_time)
                    return True
                else:
                    self.log_result("GET Instagram Accounts", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("GET Instagram Accounts", "FAIL", 
                              "Invalid JSON response", response_time)
                return False
        else:
            self.log_result("GET Instagram Accounts", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return False

    def test_instagram_accounts_post(self) -> Optional[str]:
        """Test POST /api/instagram/accounts - Add new Instagram account"""
        print("📱 Testing Instagram Accounts - POST...")
        
        account_data = {
            "username": "testaccount",
            "bio": "Test Instagram account for Mewayz platform",
            "profile_picture_url": "https://example.com/profile.jpg",
            "is_primary": True
        }
        
        response, response_time = self.make_request("POST", "/instagram/accounts", account_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "account" in data:
                    account = data["account"]
                    account_id = account.get("id")
                    self.log_result("POST Instagram Account", "PASS", 
                                  f"Created Instagram account: {account.get('username')} (ID: {account_id})", response_time)
                    return account_id
                else:
                    self.log_result("POST Instagram Account", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return None
            except json.JSONDecodeError:
                self.log_result("POST Instagram Account", "FAIL", 
                              "Invalid JSON response", response_time)
                return None
        else:
            self.log_result("POST Instagram Account", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return None

    def test_instagram_posts_get(self) -> bool:
        """Test GET /api/instagram/posts - Get posts with pagination and filters"""
        print("📝 Testing Instagram Posts - GET...")
        
        # Test basic get
        response, response_time = self.make_request("GET", "/instagram/posts")
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "posts" in data and "pagination" in data:
                    posts = data["posts"]
                    pagination = data["pagination"]
                    self.log_result("GET Instagram Posts", "PASS", 
                                  f"Retrieved {len(posts)} posts with pagination (total: {pagination.get('total', 0)})", response_time)
                    
                    # Test with filters
                    filter_params = {
                        "status": "draft",
                        "post_type": "feed"
                    }
                    response2, response_time2 = self.make_request("GET", "/instagram/posts", filter_params)
                    
                    if response2 and response2.status_code == 200:
                        data2 = response2.json()
                        if data2.get("success"):
                            self.log_result("GET Instagram Posts (Filtered)", "PASS", 
                                          f"Filtered posts working correctly", response_time2)
                            return True
                    
                    return True
                else:
                    self.log_result("GET Instagram Posts", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("GET Instagram Posts", "FAIL", 
                              "Invalid JSON response", response_time)
                return False
        else:
            self.log_result("GET Instagram Posts", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return False

    def test_instagram_posts_post(self) -> Optional[str]:
        """Test POST /api/instagram/posts - Create new post with scheduling"""
        print("📝 Testing Instagram Posts - POST...")
        
        # Create a scheduled post
        future_date = (datetime.now() + timedelta(hours=2)).isoformat()
        
        post_data = {
            "title": "Test Instagram Post",
            "caption": "This is a test Instagram post created by Mewayz platform! #test #instagram #mewayz",
            "media_urls": [
                "https://example.com/image1.jpg",
                "https://example.com/image2.jpg"
            ],
            "hashtags": ["test", "instagram", "mewayz", "socialmedia"],
            "post_type": "feed",
            "scheduled_at": future_date
        }
        
        response, response_time = self.make_request("POST", "/instagram/posts", post_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "post" in data:
                    post = data["post"]
                    post_id = post.get("id")
                    self.log_result("POST Instagram Post", "PASS", 
                                  f"Created scheduled post: {post.get('title')} (ID: {post_id}, Status: {post.get('status')})", response_time)
                    return post_id
                else:
                    self.log_result("POST Instagram Post", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return None
            except json.JSONDecodeError:
                self.log_result("POST Instagram Post", "FAIL", 
                              "Invalid JSON response", response_time)
                return None
        else:
            self.log_result("POST Instagram Post", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return None

    def test_instagram_posts_put(self, post_id: str) -> bool:
        """Test PUT /api/instagram/posts/{id} - Update existing post"""
        print("📝 Testing Instagram Posts - PUT...")
        
        if not post_id:
            self.log_result("PUT Instagram Post", "SKIP", "No post ID available for update test")
            return False
        
        update_data = {
            "title": "Updated Test Instagram Post",
            "caption": "This post has been updated! #updated #test #instagram",
            "hashtags": ["updated", "test", "instagram", "modified"]
        }
        
        response, response_time = self.make_request("PUT", f"/instagram/posts/{post_id}", update_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "post" in data:
                    post = data["post"]
                    self.log_result("PUT Instagram Post", "PASS", 
                                  f"Updated post: {post.get('title')} (ID: {post_id})", response_time)
                    return True
                else:
                    self.log_result("PUT Instagram Post", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("PUT Instagram Post", "FAIL", 
                              "Invalid JSON response", response_time)
                return False
        else:
            self.log_result("PUT Instagram Post", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return False

    def test_instagram_posts_delete(self, post_id: str) -> bool:
        """Test DELETE /api/instagram/posts/{id} - Delete post"""
        print("📝 Testing Instagram Posts - DELETE...")
        
        if not post_id:
            self.log_result("DELETE Instagram Post", "SKIP", "No post ID available for delete test")
            return False
        
        response, response_time = self.make_request("DELETE", f"/instagram/posts/{post_id}")
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success"):
                    self.log_result("DELETE Instagram Post", "PASS", 
                                  f"Successfully deleted post (ID: {post_id})", response_time)
                    return True
                else:
                    self.log_result("DELETE Instagram Post", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("DELETE Instagram Post", "FAIL", 
                              "Invalid JSON response", response_time)
                return False
        else:
            self.log_result("DELETE Instagram Post", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return False

    def test_hashtag_research(self) -> bool:
        """Test GET /api/instagram/hashtag-research - Search hashtags by keyword"""
        print("🏷️ Testing Hashtag Research...")
        
        research_params = {
            "keyword": "marketing",
            "limit": 10
        }
        
        response, response_time = self.make_request("GET", "/instagram/hashtag-research", research_params)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "hashtags" in data:
                    hashtags = data["hashtags"]
                    if len(hashtags) > 0:
                        sample_hashtag = hashtags[0]
                        required_fields = ["hashtag", "post_count", "engagement_rate", "difficulty", "difficulty_color"]
                        
                        if all(field in sample_hashtag for field in required_fields):
                            self.log_result("Hashtag Research", "PASS", 
                                          f"Retrieved {len(hashtags)} hashtags with complete data structure", response_time)
                            return True
                        else:
                            missing_fields = [field for field in required_fields if field not in sample_hashtag]
                            self.log_result("Hashtag Research", "FAIL", 
                                          f"Missing required fields: {missing_fields}", response_time)
                            return False
                    else:
                        self.log_result("Hashtag Research", "PASS", 
                                      "No hashtags found but API working correctly", response_time)
                        return True
                else:
                    self.log_result("Hashtag Research", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("Hashtag Research", "FAIL", 
                              "Invalid JSON response", response_time)
                return False
        else:
            self.log_result("Hashtag Research", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return False

    def test_instagram_analytics(self) -> bool:
        """Test GET /api/instagram/analytics - Get Instagram analytics and metrics"""
        print("📊 Testing Instagram Analytics...")
        
        analytics_params = {
            "date_range": "30"
        }
        
        response, response_time = self.make_request("GET", "/instagram/analytics", analytics_params)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get("success") and "analytics" in data:
                    analytics = data["analytics"]
                    
                    # Check required analytics structure
                    required_sections = ["overview", "top_posts", "top_hashtags", "date_range", "period"]
                    if all(section in analytics for section in required_sections):
                        overview = analytics["overview"]
                        required_metrics = ["total_posts", "total_followers", "total_engagement", "engagement_rate"]
                        
                        if all(metric in overview for metric in required_metrics):
                            self.log_result("Instagram Analytics", "PASS", 
                                          f"Complete analytics data: {overview['total_posts']} posts, {overview['total_followers']} followers, {overview['engagement_rate']}% engagement", response_time)
                            return True
                        else:
                            missing_metrics = [metric for metric in required_metrics if metric not in overview]
                            self.log_result("Instagram Analytics", "FAIL", 
                                          f"Missing overview metrics: {missing_metrics}", response_time)
                            return False
                    else:
                        missing_sections = [section for section in required_sections if section not in analytics]
                        self.log_result("Instagram Analytics", "FAIL", 
                                      f"Missing analytics sections: {missing_sections}", response_time)
                        return False
                else:
                    self.log_result("Instagram Analytics", "FAIL", 
                                  f"Invalid response structure: {data}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_result("Instagram Analytics", "FAIL", 
                              "Invalid JSON response", response_time)
                return False
        else:
            self.log_result("Instagram Analytics", "FAIL", 
                          f"HTTP {response.status_code if response else 'No response'}: {response.text if response else 'Connection failed'}", response_time)
            return False

    def run_comprehensive_test(self):
        """Run all Instagram Management tests"""
        print("🚀 Starting Instagram Management System - Comprehensive Backend Testing")
        print("=" * 80)
        print()
        
        # Step 1: Authentication
        if not self.authenticate_user():
            print("❌ Authentication failed. Cannot proceed with Instagram Management tests.")
            return False
        
        print("📱 INSTAGRAM MANAGEMENT SYSTEM TESTING")
        print("-" * 50)
        
        # Step 2: Test Instagram Accounts Management
        print("1️⃣ INSTAGRAM ACCOUNTS MANAGEMENT")
        accounts_get_success = self.test_instagram_accounts_get()
        account_id = self.test_instagram_accounts_post()
        
        # Step 3: Test Instagram Posts Management
        print("2️⃣ INSTAGRAM POSTS MANAGEMENT")
        posts_get_success = self.test_instagram_posts_get()
        post_id = self.test_instagram_posts_post()
        posts_put_success = self.test_instagram_posts_put(post_id) if post_id else False
        posts_delete_success = self.test_instagram_posts_delete(post_id) if post_id else False
        
        # Step 4: Test Hashtag Research
        print("3️⃣ HASHTAG RESEARCH")
        hashtag_research_success = self.test_hashtag_research()
        
        # Step 5: Test Analytics Dashboard
        print("4️⃣ ANALYTICS DASHBOARD")
        analytics_success = self.test_instagram_analytics()
        
        # Generate comprehensive report
        self.generate_test_report()
        
        return True

    def generate_test_report(self):
        """Generate comprehensive test report"""
        print("\n" + "=" * 80)
        print("📊 INSTAGRAM MANAGEMENT SYSTEM - TEST RESULTS SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r["status"] == "PASS"])
        failed_tests = len([r for r in self.test_results if r["status"] == "FAIL"])
        skipped_tests = len([r for r in self.test_results if r["status"] == "SKIP"])
        
        print(f"📈 OVERALL RESULTS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests}")
        print(f"   ❌ Failed: {failed_tests}")
        print(f"   ⚠️ Skipped: {skipped_tests}")
        print(f"   Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        print()
        
        # Performance metrics
        response_times = [r["response_time"] for r in self.test_results if r["response_time"] > 0]
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            print(f"⚡ PERFORMANCE METRICS:")
            print(f"   Average Response Time: {avg_response_time:.3f}s")
            print(f"   Fastest Response: {min(response_times):.3f}s")
            print(f"   Slowest Response: {max(response_times):.3f}s")
            print()
        
        # Feature status
        print("🎯 FEATURE STATUS:")
        feature_groups = {
            "Instagram Accounts": ["GET Instagram Accounts", "POST Instagram Account"],
            "Instagram Posts": ["GET Instagram Posts", "POST Instagram Post", "PUT Instagram Post", "DELETE Instagram Post"],
            "Hashtag Research": ["Hashtag Research"],
            "Analytics Dashboard": ["Instagram Analytics"]
        }
        
        for feature, tests in feature_groups.items():
            feature_results = [r for r in self.test_results if r["test"] in tests]
            feature_passed = len([r for r in feature_results if r["status"] == "PASS"])
            feature_total = len(feature_results)
            
            if feature_total > 0:
                status = "✅ WORKING" if feature_passed == feature_total else "❌ ISSUES" if feature_passed == 0 else "⚠️ PARTIAL"
                print(f"   {feature}: {status} ({feature_passed}/{feature_total})")
        
        print()
        
        # Failed tests details
        failed_results = [r for r in self.test_results if r["status"] == "FAIL"]
        if failed_results:
            print("❌ FAILED TESTS DETAILS:")
            for result in failed_results:
                print(f"   • {result['test']}: {result['details']}")
            print()
        
        # Critical issues
        critical_issues = []
        if failed_tests > 0:
            critical_issues.append(f"{failed_tests} API endpoints are not working correctly")
        
        if critical_issues:
            print("🚨 CRITICAL ISSUES REQUIRING IMMEDIATE ATTENTION:")
            for issue in critical_issues:
                print(f"   • {issue}")
            print()
        
        print("📋 DETAILED TEST LOG:")
        for result in self.test_results:
            status_icon = "✅" if result["status"] == "PASS" else "❌" if result["status"] == "FAIL" else "⚠️"
            print(f"   {status_icon} {result['test']}: {result['status']}")
            if result["details"]:
                print(f"      └─ {result['details']}")
        
        print("\n" + "=" * 80)
        print("🎉 Instagram Management System Testing Complete!")
        print("=" * 80)

def main():
    """Main function to run Instagram Management tests"""
    if len(sys.argv) > 1:
        base_url = sys.argv[1]
    else:
        base_url = "http://localhost:8001"
    
    tester = InstagramManagementTester(base_url)
    tester.run_comprehensive_test()

if __name__ == "__main__":
    main()