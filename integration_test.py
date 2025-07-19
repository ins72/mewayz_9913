#!/usr/bin/env python3
"""
Integration Testing for X (Twitter), TikTok, and ElasticMail
Tests the new social media and email marketing integrations
"""

import requests
import json
import time
from datetime import datetime
from typing import Dict, Any, List

class IntegrationTester:
    def __init__(self):
        # Get backend URL from frontend .env
        with open('/app/frontend/.env', 'r') as f:
            for line in f:
                if line.startswith('REACT_APP_BACKEND_URL='):
                    self.base_url = line.split('=')[1].strip()
                    break
            else:
                self.base_url = "http://localhost:8001"
        
        self.api_url = f"{self.base_url}/api"
        self.admin_credentials = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        self.jwt_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0):
        """Log test result"""
        status = "✅ PASS" if success else "❌ FAIL"
        self.test_results.append({
            "test": test_name,
            "status": status,
            "success": success,
            "details": details,
            "response_time": f"{response_time:.3f}s"
        })
        print(f"{status} {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    {details}")
    
    def authenticate_admin(self) -> bool:
        """Authenticate as admin user"""
        try:
            start_time = time.time()
            response = requests.post(
                f"{self.api_url}/auth/login",
                json=self.admin_credentials,
                timeout=10
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success") and data.get("token"):
                    self.jwt_token = data["token"]
                    user_info = data.get("user", {})
                    self.log_test(
                        "Admin Authentication",
                        True,
                        f"Authenticated as {user_info.get('name', 'Admin')} ({user_info.get('role', 'unknown')})",
                        response_time
                    )
                    return True
            
            self.log_test(
                "Admin Authentication",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("Admin Authentication", False, f"Exception: {str(e)}")
            return False
    
    def get_headers(self) -> Dict[str, str]:
        """Get headers with JWT token"""
        return {
            "Authorization": f"Bearer {self.jwt_token}",
            "Content-Type": "application/json"
        }
    
    def test_available_integrations(self) -> bool:
        """Test GET /api/integrations/available"""
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.api_url}/integrations/available",
                timeout=10
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    integrations = data.get("integrations", {})
                    details = data.get("details", {})
                    
                    # Check if all expected integrations are present
                    expected = ["x_twitter", "tiktok", "elasticmail"]
                    available_count = sum(1 for key in expected if integrations.get(key))
                    
                    self.log_test(
                        "Available Integrations Endpoint",
                        True,
                        f"Found {available_count}/3 integrations configured: X={integrations.get('x_twitter')}, TikTok={integrations.get('tiktok')}, ElasticMail={integrations.get('elasticmail')}",
                        response_time
                    )
                    return True
            
            self.log_test(
                "Available Integrations Endpoint",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("Available Integrations Endpoint", False, f"Exception: {str(e)}")
            return False
    
    def test_x_twitter_auth(self) -> bool:
        """Test POST /api/integrations/social/auth for X (Twitter)"""
        try:
            start_time = time.time()
            auth_data = {
                "platform": "x",
                "callback_url": f"{self.base_url}/api/integrations/social/callback/x"
            }
            
            response = requests.post(
                f"{self.api_url}/integrations/social/auth",
                json=auth_data,
                headers=self.get_headers(),
                timeout=10
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success") and data.get("auth_url"):
                    self.log_test(
                        "X (Twitter) Authentication Initiation",
                        True,
                        f"Auth URL generated: {data.get('auth_url')[:50]}..., OAuth Token: {data.get('oauth_token', 'N/A')[:20]}...",
                        response_time
                    )
                    return True
            
            self.log_test(
                "X (Twitter) Authentication Initiation",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("X (Twitter) Authentication Initiation", False, f"Exception: {str(e)}")
            return False
    
    def test_tiktok_auth(self) -> bool:
        """Test POST /api/integrations/social/auth for TikTok"""
        try:
            start_time = time.time()
            auth_data = {
                "platform": "tiktok",
                "redirect_uri": f"{self.base_url}/api/integrations/social/callback/tiktok"
            }
            
            response = requests.post(
                f"{self.api_url}/integrations/social/auth",
                json=auth_data,
                headers=self.get_headers(),
                timeout=10
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success") and data.get("auth_url"):
                    self.log_test(
                        "TikTok Authentication Initiation",
                        True,
                        f"Auth URL generated: {data.get('auth_url')[:50]}..., State: {data.get('state', 'N/A')[:20]}...",
                        response_time
                    )
                    return True
            
            self.log_test(
                "TikTok Authentication Initiation",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("TikTok Authentication Initiation", False, f"Exception: {str(e)}")
            return False
    
    def test_email_campaign_send(self) -> bool:
        """Test POST /api/integrations/email/send with ElasticMail"""
        try:
            start_time = time.time()
            email_data = {
                "recipients": ["test@example.com"],
                "subject": f"Test Campaign - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}",
                "body": "<h1>Test Email Campaign</h1><p>This is a test email sent via ElasticMail integration from Mewayz Platform.</p><p>Sent at: " + datetime.now().isoformat() + "</p>",
                "sender_email": "noreply@mewayz.com",
                "sender_name": "Mewayz Platform Test"
            }
            
            response = requests.post(
                f"{self.api_url}/integrations/email/send",
                json=email_data,
                headers=self.get_headers(),
                timeout=15
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    self.log_test(
                        "ElasticMail Email Campaign Send",
                        True,
                        f"Email sent successfully. Message ID: {data.get('message_id', 'N/A')}, Transaction ID: {data.get('transaction_id', 'N/A')}",
                        response_time
                    )
                    return True
            
            self.log_test(
                "ElasticMail Email Campaign Send",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("ElasticMail Email Campaign Send", False, f"Exception: {str(e)}")
            return False
    
    def test_email_contact_management(self) -> bool:
        """Test POST /api/integrations/email/contact"""
        try:
            start_time = time.time()
            contact_data = {
                "email": f"testcontact{int(time.time())}@example.com",
                "first_name": "Test",
                "last_name": "Contact",
                "custom_fields": {
                    "company": "Mewayz Test",
                    "source": "integration_test"
                }
            }
            
            response = requests.post(
                f"{self.api_url}/integrations/email/contact",
                json=contact_data,
                headers=self.get_headers(),
                timeout=15
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    self.log_test(
                        "ElasticMail Contact Management",
                        True,
                        f"Contact created successfully. Contact ID: {data.get('contact_id', 'N/A')}, Email: {contact_data['email']}",
                        response_time
                    )
                    return True
            
            self.log_test(
                "ElasticMail Contact Management",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("ElasticMail Contact Management", False, f"Exception: {str(e)}")
            return False
    
    def test_email_statistics(self) -> bool:
        """Test GET /api/integrations/email/stats"""
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.api_url}/integrations/email/stats",
                headers=self.get_headers(),
                timeout=15
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    user_stats = data.get("user_stats", {})
                    account_stats = data.get("account_stats", {})
                    
                    self.log_test(
                        "ElasticMail Email Statistics",
                        True,
                        f"User Stats - Campaigns: {user_stats.get('total_campaigns', 0)}, Emails Sent: {user_stats.get('total_emails_sent', 0)}, Contacts: {user_stats.get('total_contacts', 0)}. Account Credits: {account_stats.get('credits', 'N/A')}",
                        response_time
                    )
                    return True
            
            self.log_test(
                "ElasticMail Email Statistics",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("ElasticMail Email Statistics", False, f"Exception: {str(e)}")
            return False
    
    def test_social_media_activities(self) -> bool:
        """Test GET /api/integrations/social/activities"""
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.api_url}/integrations/social/activities",
                headers=self.get_headers(),
                timeout=10
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    activities = data.get("activities", [])
                    self.log_test(
                        "Social Media Activities",
                        True,
                        f"Retrieved {len(activities)} social media activities. Recent activity types: {list(set(a.get('activity_type', 'unknown') for a in activities[:5]))}",
                        response_time
                    )
                    return True
            
            self.log_test(
                "Social Media Activities",
                False,
                f"Status: {response.status_code}, Response: {response.text[:200]}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("Social Media Activities", False, f"Exception: {str(e)}")
            return False
    
    def test_invalid_platform_auth(self) -> bool:
        """Test authentication with invalid platform"""
        try:
            start_time = time.time()
            auth_data = {
                "platform": "invalid_platform",
                "callback_url": f"{self.base_url}/api/integrations/social/callback/invalid"
            }
            
            response = requests.post(
                f"{self.api_url}/integrations/social/auth",
                json=auth_data,
                headers=self.get_headers(),
                timeout=10
            )
            response_time = time.time() - start_time
            
            # Should return 400 Bad Request for invalid platform
            if response.status_code == 400:
                self.log_test(
                    "Invalid Platform Authentication (Error Handling)",
                    True,
                    f"Correctly rejected invalid platform with status 400",
                    response_time
                )
                return True
            
            self.log_test(
                "Invalid Platform Authentication (Error Handling)",
                False,
                f"Expected 400 but got {response.status_code}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("Invalid Platform Authentication (Error Handling)", False, f"Exception: {str(e)}")
            return False
    
    def test_email_send_without_recipients(self) -> bool:
        """Test email send with empty recipients list"""
        try:
            start_time = time.time()
            email_data = {
                "recipients": [],
                "subject": "Test Empty Recipients",
                "body": "This should fail",
                "sender_email": "noreply@mewayz.com"
            }
            
            response = requests.post(
                f"{self.api_url}/integrations/email/send",
                json=email_data,
                headers=self.get_headers(),
                timeout=10
            )
            response_time = time.time() - start_time
            
            # Should handle empty recipients gracefully
            if response.status_code in [400, 422]:  # Bad Request or Validation Error
                self.log_test(
                    "Email Send Empty Recipients (Error Handling)",
                    True,
                    f"Correctly handled empty recipients with status {response.status_code}",
                    response_time
                )
                return True
            
            self.log_test(
                "Email Send Empty Recipients (Error Handling)",
                False,
                f"Expected 400/422 but got {response.status_code}",
                response_time
            )
            return False
            
        except Exception as e:
            self.log_test("Email Send Empty Recipients (Error Handling)", False, f"Exception: {str(e)}")
            return False
    
    def run_all_tests(self):
        """Run all integration tests"""
        print("🔐 INTEGRATION TESTING FOR X (TWITTER), TIKTOK, AND ELASTICMAIL")
        print("=" * 80)
        print(f"Backend URL: {self.base_url}")
        print(f"Testing Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate_admin():
            print("❌ Authentication failed. Cannot proceed with integration tests.")
            return
        
        # Core integration tests
        print("\n📋 PRIORITY INTEGRATION TESTS:")
        self.test_available_integrations()
        self.test_x_twitter_auth()
        self.test_tiktok_auth()
        self.test_email_campaign_send()
        self.test_email_contact_management()
        self.test_email_statistics()
        self.test_social_media_activities()
        
        # Error handling tests
        print("\n🛡️ ERROR HANDLING TESTS:")
        self.test_invalid_platform_auth()
        self.test_email_send_without_recipients()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result["success"])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "=" * 80)
        print("📊 INTEGRATION TEST SUMMARY")
        print("=" * 80)
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests}")
        print(f"Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        print()
        
        # Show all test results
        for result in self.test_results:
            print(f"{result['status']} {result['test']} ({result['response_time']})")
            if result['details']:
                print(f"    {result['details']}")
        
        print("\n" + "=" * 80)
        
        # Integration-specific summary
        print("🎯 INTEGRATION STATUS SUMMARY:")
        
        # Check which integrations are working
        available_test = next((r for r in self.test_results if "Available Integrations" in r["test"]), None)
        x_auth_test = next((r for r in self.test_results if "X (Twitter) Authentication" in r["test"]), None)
        tiktok_auth_test = next((r for r in self.test_results if "TikTok Authentication" in r["test"]), None)
        email_send_test = next((r for r in self.test_results if "ElasticMail Email Campaign Send" in r["test"]), None)
        email_contact_test = next((r for r in self.test_results if "ElasticMail Contact Management" in r["test"]), None)
        email_stats_test = next((r for r in self.test_results if "ElasticMail Email Statistics" in r["test"]), None)
        social_activities_test = next((r for r in self.test_results if "Social Media Activities" in r["test"]), None)
        
        print(f"✅ Available Integrations Endpoint: {'WORKING' if available_test and available_test['success'] else 'FAILED'}")
        print(f"✅ X (Twitter) Authentication: {'WORKING' if x_auth_test and x_auth_test['success'] else 'FAILED'}")
        print(f"✅ TikTok Authentication: {'WORKING' if tiktok_auth_test and tiktok_auth_test['success'] else 'FAILED'}")
        print(f"✅ ElasticMail Email Sending: {'WORKING' if email_send_test and email_send_test['success'] else 'FAILED'}")
        print(f"✅ ElasticMail Contact Management: {'WORKING' if email_contact_test and email_contact_test['success'] else 'FAILED'}")
        print(f"✅ ElasticMail Statistics: {'WORKING' if email_stats_test and email_stats_test['success'] else 'FAILED'}")
        print(f"✅ Social Media Activities: {'WORKING' if social_activities_test and social_activities_test['success'] else 'FAILED'}")
        
        print("\n🔍 KEY FINDINGS:")
        if success_rate >= 85:
            print("✅ EXCELLENT: Integration system is highly functional and production-ready")
        elif success_rate >= 70:
            print("⚠️ GOOD: Integration system is mostly functional with minor issues")
        elif success_rate >= 50:
            print("⚠️ MODERATE: Integration system has significant issues that need attention")
        else:
            print("❌ CRITICAL: Integration system has major issues and requires immediate fixes")
        
        # Check for authentication working
        if self.jwt_token:
            print("✅ JWT Authentication: Working perfectly with admin credentials")
        else:
            print("❌ JWT Authentication: Failed - this blocks all integration testing")
        
        print("=" * 80)

if __name__ == "__main__":
    tester = IntegrationTester()
    tester.run_all_tests()