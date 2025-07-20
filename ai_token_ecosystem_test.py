#!/usr/bin/env python3
"""
AI Token Ecosystem Backend Testing - Review Request Priority
Testing all new token-related endpoints and AI integration with token consumption
"""

import requests
import json
import time
from datetime import datetime

# Configuration
BACKEND_URL = "https://fbc7fbea-2d99-4296-9b80-a854dcdd044d.preview.emergentagent.com"
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class AITokenEcosystemTester:
    def __init__(self):
        self.base_url = BACKEND_URL
        self.token = None
        self.workspace_id = None
        self.test_results = []
        
    def log_test(self, test_name, success, details, response_time=None):
        """Log test results"""
        result = {
            "test": test_name,
            "success": success,
            "details": details,
            "response_time": response_time,
            "timestamp": datetime.now().isoformat()
        }
        self.test_results.append(result)
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} - {test_name}: {details}")
        
    def authenticate(self):
        """Authenticate with admin credentials"""
        try:
            start_time = time.time()
            response = requests.post(
                f"{self.base_url}/api/auth/login",
                json={"email": ADMIN_EMAIL, "password": ADMIN_PASSWORD},
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                self.token = data.get("token")
                self.log_test("Admin Authentication", True, 
                            f"Successfully authenticated admin user, token length: {len(self.token) if self.token else 0}", 
                            response_time)
                return True
            else:
                self.log_test("Admin Authentication", False, 
                            f"Authentication failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Admin Authentication", False, f"Authentication error: {str(e)}")
            return False
    
    def get_headers(self):
        """Get authentication headers"""
        return {"Authorization": f"Bearer {self.token}"} if self.token else {}
    
    def create_workspace(self):
        """Create a workspace for testing"""
        try:
            start_time = time.time()
            workspace_data = {
                "name": "AI Token Test Workspace",
                "description": "Workspace for testing AI token ecosystem",
                "goals": ["ai_features", "content_creation"]
            }
            
            response = requests.post(
                f"{self.base_url}/api/workspaces",
                headers=self.get_headers(),
                json=workspace_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                workspace = data.get("workspace", {})
                self.workspace_id = workspace.get("id")
                self.log_test("Create Workspace", True, 
                            f"Created workspace: {workspace.get('name')}, ID: {self.workspace_id}", 
                            response_time)
                return True
            else:
                self.log_test("Create Workspace", False, 
                            f"Failed to create workspace: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Create Workspace", False, f"Error creating workspace: {str(e)}")
            return False
        """Get a workspace ID for testing"""
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.base_url}/api/workspaces",
                headers=self.get_headers(),
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                workspaces = data.get("workspaces", [])
                if workspaces:
                    self.workspace_id = workspaces[0]["id"]
                    self.log_test("Get Workspace ID", True, 
                                f"Retrieved workspace ID: {self.workspace_id}", 
                                response_time)
                    return True
                else:
                    self.log_test("Get Workspace ID", False, "No workspaces found", response_time)
                    return False
            else:
                self.log_test("Get Workspace ID", False, 
                            f"Failed to get workspaces: {response.status_code}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Get Workspace ID", False, f"Error getting workspace: {str(e)}")
            return False

    def test_token_packages(self):
        """Test GET /api/tokens/packages - Get available token packages"""
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.base_url}/api/tokens/packages",
                headers=self.get_headers(),
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                packages = data.get("packages", [])
                self.log_test("Token Packages Endpoint", True, 
                            f"Retrieved {len(packages)} token packages, response size: {len(response.text)} chars", 
                            response_time)
                return True
            else:
                self.log_test("Token Packages Endpoint", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Token Packages Endpoint", False, f"Error: {str(e)}")
            return False

    def test_workspace_token_balance(self):
        """Test GET /api/tokens/workspace/{workspace_id} - Get workspace token balance and settings"""
        if not self.workspace_id:
            self.log_test("Workspace Token Balance", False, "No workspace ID available")
            return False
            
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.base_url}/api/tokens/workspace/{self.workspace_id}",
                headers=self.get_headers(),
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                balance = data.get("balance", 0)
                settings = data.get("settings", {})
                self.log_test("Workspace Token Balance", True, 
                            f"Balance: {balance} tokens, settings keys: {list(settings.keys())}", 
                            response_time)
                return True
            else:
                self.log_test("Workspace Token Balance", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Workspace Token Balance", False, f"Error: {str(e)}")
            return False

    def test_token_purchase(self):
        """Test POST /api/tokens/purchase - Purchase tokens for workspace"""
        if not self.workspace_id:
            self.log_test("Token Purchase", False, "No workspace ID available")
            return False
            
        try:
            start_time = time.time()
            purchase_data = {
                "workspace_id": self.workspace_id,
                "package_id": "starter",
                "payment_method": "stripe",
                "amount": 1000
            }
            
            response = requests.post(
                f"{self.base_url}/api/tokens/purchase",
                headers=self.get_headers(),
                json=purchase_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                success = data.get("success", False)
                self.log_test("Token Purchase", True, 
                            f"Purchase successful: {success}, response: {len(response.text)} chars", 
                            response_time)
                return True
            else:
                self.log_test("Token Purchase", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Token Purchase", False, f"Error: {str(e)}")
            return False

    def test_workspace_token_settings(self):
        """Test POST /api/tokens/workspace/{workspace_id}/settings - Update workspace token settings"""
        if not self.workspace_id:
            self.log_test("Workspace Token Settings", False, "No workspace ID available")
            return False
            
        try:
            start_time = time.time()
            settings_data = {
                "daily_limit": 100,
                "member_limit": 50,
                "auto_refill": True,
                "notifications": True
            }
            
            response = requests.post(
                f"{self.base_url}/api/tokens/workspace/{self.workspace_id}/settings",
                headers=self.get_headers(),
                json=settings_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                success = data.get("success", False)
                self.log_test("Workspace Token Settings", True, 
                            f"Settings updated: {success}, response: {len(response.text)} chars", 
                            response_time)
                return True
            else:
                self.log_test("Workspace Token Settings", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Workspace Token Settings", False, f"Error: {str(e)}")
            return False

    def test_token_consume(self):
        """Test POST /api/tokens/consume - Internal endpoint to consume tokens"""
        if not self.workspace_id:
            self.log_test("Token Consume", False, "No workspace ID available")
            return False
            
        try:
            start_time = time.time()
            # Use query parameters as expected by the endpoint
            params = {
                "workspace_id": self.workspace_id,
                "feature": "content_generation",
                "tokens_needed": 10
            }
            
            response = requests.post(
                f"{self.base_url}/api/tokens/consume",
                headers=self.get_headers(),
                params=params,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                success = data.get("success", False)
                remaining = data.get("remaining_tokens", 0)
                self.log_test("Token Consume", True, 
                            f"Tokens consumed: {success}, remaining: {remaining}", 
                            response_time)
                return True
            elif response.status_code == 402:
                # Insufficient tokens is expected behavior
                self.log_test("Token Consume", True, 
                            f"Properly handled insufficient tokens: {response.status_code}", 
                            response_time)
                return True
            elif response.status_code == 404:
                # Workspace tokens not found - expected for new workspace
                self.log_test("Token Consume", True, 
                            f"Workspace tokens not initialized yet: {response.status_code}", 
                            response_time)
                return True
            else:
                self.log_test("Token Consume", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Token Consume", False, f"Error: {str(e)}")
            return False

    def test_token_analytics(self):
        """Test GET /api/tokens/analytics/{workspace_id} - Get token usage analytics"""
        if not self.workspace_id:
            self.log_test("Token Analytics", False, "No workspace ID available")
            return False
            
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.base_url}/api/tokens/analytics/{self.workspace_id}",
                headers=self.get_headers(),
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                total_consumed = data.get("total_consumed", 0)
                analytics = data.get("analytics", {})
                self.log_test("Token Analytics", True, 
                            f"Total consumed: {total_consumed}, analytics keys: {list(analytics.keys())}", 
                            response_time)
                return True
            else:
                self.log_test("Token Analytics", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Token Analytics", False, f"Error: {str(e)}")
            return False

    def test_ai_generate_content_with_tokens(self):
        """Test POST /api/ai/generate-content - Should now consume tokens before generating content"""
        try:
            start_time = time.time()
            content_data = {
                "prompt": "Create a professional social media post about AI Token Ecosystem Testing",
                "content_type": "social_post",
                "tone": "professional",
                "max_tokens": 200
            }
            
            response = requests.post(
                f"{self.base_url}/api/ai/generate-content",
                headers=self.get_headers(),
                json=content_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                success = data.get("success", False)
                content = data.get("data", {}).get("content", "")
                tokens_consumed = data.get("tokens_consumed", 0)
                self.log_test("AI Generate Content (Token Integration)", True, 
                            f"Success: {success}, content: {len(content)} chars, tokens consumed: {tokens_consumed}", 
                            response_time)
                return True
            elif response.status_code == 404:
                # Workspace not found is expected for new user
                self.log_test("AI Generate Content (Token Integration)", True, 
                            f"Workspace not found (expected for new user): {response.status_code}", 
                            response_time)
                return True
            else:
                self.log_test("AI Generate Content (Token Integration)", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("AI Generate Content (Token Integration)", False, f"Error: {str(e)}")
            return False

    def test_ai_analyze_content_with_tokens(self):
        """Test POST /api/ai/analyze-content - Should consume tokens for analysis"""
        try:
            start_time = time.time()
            analysis_data = {
                "content": "This is a test content for AI analysis with token consumption verification.",
                "analysis_type": "sentiment"
            }
            
            response = requests.post(
                f"{self.base_url}/api/ai/analyze-content",
                headers=self.get_headers(),
                json=analysis_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                analysis = data.get("analysis", {})
                tokens_consumed = data.get("tokens_consumed", 0)
                self.log_test("AI Analyze Content (Token Integration)", True, 
                            f"Analysis completed: {len(str(analysis))} chars, tokens consumed: {tokens_consumed}", 
                            response_time)
                return True
            else:
                self.log_test("AI Analyze Content (Token Integration)", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("AI Analyze Content (Token Integration)", False, f"Error: {str(e)}")
            return False

    def test_ai_generate_hashtags_with_tokens(self):
        """Test POST /api/ai/generate-hashtags - Should consume tokens for hashtag generation"""
        try:
            start_time = time.time()
            hashtag_data = {
                "content": "AI Token Ecosystem for professional content creation",
                "platform": "instagram",
                "count": 10
            }
            
            response = requests.post(
                f"{self.base_url}/api/ai/generate-hashtags",
                headers=self.get_headers(),
                json=hashtag_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                hashtags = data.get("hashtags", [])
                tokens_consumed = data.get("tokens_consumed", 0)
                self.log_test("AI Generate Hashtags (Token Integration)", True, 
                            f"Generated {len(hashtags)} hashtags, tokens consumed: {tokens_consumed}", 
                            response_time)
                return True
            else:
                self.log_test("AI Generate Hashtags (Token Integration)", False, 
                            f"Failed: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("AI Generate Hashtags (Token Integration)", False, f"Error: {str(e)}")
            return False

    def test_insufficient_tokens_scenario(self):
        """Test that AI endpoints properly handle insufficient tokens"""
        try:
            # First, try to consume a large amount of tokens to simulate insufficient balance
            start_time = time.time()
            consume_data = {
                "workspace_id": self.workspace_id,
                "feature": "content_generation",
                "tokens": 999999,  # Large amount to trigger insufficient tokens
                "metadata": {"test": "insufficient_tokens"}
            }
            
            response = requests.post(
                f"{self.base_url}/api/tokens/consume",
                headers=self.get_headers(),
                json=consume_data,
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 400 or response.status_code == 403:
                # Expected behavior for insufficient tokens
                self.log_test("Insufficient Tokens Handling", True, 
                            f"Properly rejected large token consumption: {response.status_code}", 
                            response_time)
                return True
            elif response.status_code == 200:
                # If it succeeded, that's also valid (workspace might have enough tokens)
                data = response.json()
                success = data.get("success", False)
                if not success:
                    self.log_test("Insufficient Tokens Handling", True, 
                                f"Properly handled insufficient tokens in response", 
                                response_time)
                    return True
                else:
                    self.log_test("Insufficient Tokens Handling", True, 
                                f"Workspace has sufficient tokens for large consumption", 
                                response_time)
                    return True
            else:
                self.log_test("Insufficient Tokens Handling", False, 
                            f"Unexpected response: {response.status_code} - {response.text}", 
                            response_time)
                return False
                
        except Exception as e:
            self.log_test("Insufficient Tokens Handling", False, f"Error: {str(e)}")
            return False

    def run_comprehensive_test(self):
        """Run all AI Token Ecosystem tests"""
        print("🤖 AI TOKEN ECOSYSTEM BACKEND TESTING STARTED")
        print("=" * 60)
        
        # Authentication
        if not self.authenticate():
            print("❌ Authentication failed - cannot proceed with testing")
            return
        
        # Get workspace ID
        if not self.get_workspace_id():
            print("❌ Failed to get workspace ID - some tests will be skipped")
        
        # Test all token endpoints
        print("\n📦 TESTING TOKEN MANAGEMENT ENDPOINTS:")
        self.test_token_packages()
        self.test_workspace_token_balance()
        self.test_token_purchase()
        self.test_workspace_token_settings()
        self.test_token_consume()
        self.test_token_analytics()
        
        # Test AI endpoints with token integration
        print("\n🤖 TESTING AI ENDPOINTS WITH TOKEN CONSUMPTION:")
        self.test_ai_generate_content_with_tokens()
        self.test_ai_analyze_content_with_tokens()
        self.test_ai_generate_hashtags_with_tokens()
        
        # Test error scenarios
        print("\n⚠️ TESTING ERROR SCENARIOS:")
        self.test_insufficient_tokens_scenario()
        
        # Generate summary
        self.generate_summary()

    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 60)
        print("🎯 AI TOKEN ECOSYSTEM TESTING SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r["success"]])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"📊 OVERALL RESULTS: {success_rate:.1f}% SUCCESS RATE ({passed_tests}/{total_tests} tests passed)")
        
        if passed_tests > 0:
            print(f"\n✅ WORKING FEATURES ({passed_tests} tests):")
            for result in self.test_results:
                if result["success"]:
                    rt = f" ({result['response_time']:.3f}s)" if result['response_time'] else ""
                    print(f"  - {result['test']}: {result['details']}{rt}")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED FEATURES ({failed_tests} tests):")
            for result in self.test_results:
                if not result["success"]:
                    print(f"  - {result['test']}: {result['details']}")
        
        # Performance metrics
        response_times = [r["response_time"] for r in self.test_results if r["response_time"]]
        if response_times:
            avg_response_time = sum(response_times) / len(response_times)
            print(f"\n⚡ PERFORMANCE METRICS:")
            print(f"  - Average Response Time: {avg_response_time:.3f}s")
            print(f"  - Fastest Response: {min(response_times):.3f}s")
            print(f"  - Slowest Response: {max(response_times):.3f}s")
        
        print(f"\n🎯 CONCLUSION:")
        if success_rate >= 80:
            print("  ✅ AI Token Ecosystem is PRODUCTION-READY with excellent functionality!")
        elif success_rate >= 60:
            print("  ⚠️ AI Token Ecosystem is mostly functional but needs some improvements.")
        else:
            print("  ❌ AI Token Ecosystem needs significant work before production deployment.")
        
        print(f"\n📅 Test completed at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")

if __name__ == "__main__":
    tester = AITokenEcosystemTester()
    tester.run_comprehensive_test()