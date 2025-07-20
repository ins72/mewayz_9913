#!/usr/bin/env python3
"""
AI Endpoints Testing Script - Mewayz Platform v3.0.0
Testing newly added AI endpoints as requested in review request

Test Coverage:
1. /api/ai/generate-content - POST request with JSON body containing prompt, content_type, tone, max_tokens
2. /api/ai/analyze-content - POST request with content and analysis_type
3. /api/ai/generate-hashtags - POST request with content, platform, count
4. /api/ai/improve-content - POST request with content and improvement_type
5. /api/ai/generate-course-content - POST request with topic, lesson_title, difficulty, duration
6. /api/ai/generate-email-sequence - POST request with purpose, audience, sequence_length
7. /api/ai/get-content-ideas - POST request with industry, content_type, count
8. /api/ai/usage-analytics - GET request to retrieve AI usage analytics

Requirements:
- All endpoints require authentication (JWT token)
- Test with realistic data that would be used in the platform
- Verify OpenAI integration is working (API key is configured)
- Check response format and success status
- Test error handling for invalid requests
- Verify AI usage logging is working properly
"""

import requests
import json
import time
from datetime import datetime
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Configuration
BACKEND_URL = "https://c1377653-96a4-4862-8647-9ed933db2920.preview.emergentagent.com"
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class AIEndpointsTestRunner:
    def __init__(self):
        self.backend_url = BACKEND_URL
        self.token = None
        self.test_results = []
        self.total_tests = 0
        self.passed_tests = 0
        
    def log_test(self, test_name, status, details="", response_time=0):
        """Log test results"""
        self.total_tests += 1
        if status == "PASS":
            self.passed_tests += 1
            
        result = {
            "test": test_name,
            "status": status,
            "details": details,
            "response_time": f"{response_time:.3f}s",
            "timestamp": datetime.now().isoformat()
        }
        self.test_results.append(result)
        
        status_icon = "✅" if status == "PASS" else "❌"
        print(f"{status_icon} {test_name}: {status} ({response_time:.3f}s)")
        if details:
            print(f"   Details: {details}")
    
    def authenticate(self):
        """Authenticate and get JWT token"""
        print("🔐 Authenticating admin user...")
        
        try:
            start_time = time.time()
            response = requests.post(
                f"{self.backend_url}/api/auth/login",
                json={
                    "email": ADMIN_EMAIL,
                    "password": ADMIN_PASSWORD
                },
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success") and data.get("token"):
                    self.token = data["token"]
                    user_info = data.get("user", {})
                    self.log_test(
                        "Admin Authentication", 
                        "PASS", 
                        f"Authenticated as {user_info.get('name', 'Admin')} ({user_info.get('role', 'unknown')})",
                        response_time
                    )
                    return True
                else:
                    self.log_test("Admin Authentication", "FAIL", "No token in response", response_time)
                    return False
            else:
                self.log_test("Admin Authentication", "FAIL", f"HTTP {response.status_code}: {response.text}", response_time)
                return False
                
        except Exception as e:
            self.log_test("Admin Authentication", "FAIL", f"Exception: {str(e)}", 0)
            return False
    
    def get_headers(self):
        """Get headers with authentication"""
        return {
            "Authorization": f"Bearer {self.token}",
            "Content-Type": "application/json"
        }
    
    def test_generate_content(self):
        """Test /api/ai/generate-content endpoint"""
        print("\n📝 Testing AI Content Generation...")
        
        test_cases = [
            {
                "name": "Social Media Post Generation",
                "data": {
                    "prompt": "Create a social media post about productivity tips for entrepreneurs",
                    "content_type": "social_post",
                    "tone": "professional",
                    "max_tokens": 300
                }
            },
            {
                "name": "Blog Article Generation",
                "data": {
                    "prompt": "Write an introduction for a blog post about AI tools in business",
                    "content_type": "blog_post",
                    "tone": "informative",
                    "max_tokens": 500
                }
            },
            {
                "name": "Product Description Generation",
                "data": {
                    "prompt": "Create a compelling product description for a digital marketing course",
                    "content_type": "product_description",
                    "tone": "persuasive",
                    "max_tokens": 250
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/generate-content",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        content_data = data["data"]
                        content_length = len(content_data.get("content", ""))
                        self.log_test(
                            f"Generate Content - {test_case['name']}", 
                            "PASS", 
                            f"Generated {content_length} characters, Type: {content_data.get('type')}, Tone: {content_data.get('tone')}",
                            response_time
                        )
                    else:
                        self.log_test(f"Generate Content - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Generate Content - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Generate Content - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_analyze_content(self):
        """Test /api/ai/analyze-content endpoint"""
        print("\n🔍 Testing AI Content Analysis...")
        
        test_cases = [
            {
                "name": "Sentiment Analysis",
                "data": {
                    "content": "I absolutely love this new AI tool! It's revolutionizing how we work and making everything so much more efficient. Highly recommended!",
                    "analysis_type": "sentiment"
                }
            },
            {
                "name": "SEO Analysis",
                "data": {
                    "content": "Digital marketing strategies for small businesses in 2025. Learn how to grow your online presence with proven techniques.",
                    "analysis_type": "seo"
                }
            },
            {
                "name": "Engagement Analysis",
                "data": {
                    "content": "🚀 Ready to transform your business? Join thousands of entrepreneurs who are already using AI to scale their operations!",
                    "analysis_type": "engagement"
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/analyze-content",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        analysis_data = data["data"]
                        analysis_length = len(analysis_data.get("analysis", ""))
                        self.log_test(
                            f"Analyze Content - {test_case['name']}", 
                            "PASS", 
                            f"Analysis type: {analysis_data.get('analysis_type')}, Analysis length: {analysis_length} chars",
                            response_time
                        )
                    else:
                        self.log_test(f"Analyze Content - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Analyze Content - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Analyze Content - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_generate_hashtags(self):
        """Test /api/ai/generate-hashtags endpoint"""
        print("\n#️⃣ Testing AI Hashtag Generation...")
        
        test_cases = [
            {
                "name": "Instagram Hashtags",
                "data": {
                    "content": "AI tools are revolutionizing business productivity and helping entrepreneurs scale faster than ever before",
                    "platform": "instagram",
                    "count": 15
                }
            },
            {
                "name": "LinkedIn Hashtags",
                "data": {
                    "content": "Professional development and leadership skills for modern business executives",
                    "platform": "linkedin",
                    "count": 10
                }
            },
            {
                "name": "TikTok Hashtags",
                "data": {
                    "content": "Quick productivity tips and life hacks for busy professionals",
                    "platform": "tiktok",
                    "count": 12
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/generate-hashtags",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        hashtag_data = data["data"]
                        hashtags = hashtag_data.get("hashtags", [])
                        self.log_test(
                            f"Generate Hashtags - {test_case['name']}", 
                            "PASS", 
                            f"Generated {len(hashtags)} hashtags for {hashtag_data.get('platform')}",
                            response_time
                        )
                    else:
                        self.log_test(f"Generate Hashtags - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Generate Hashtags - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Generate Hashtags - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_improve_content(self):
        """Test /api/ai/improve-content endpoint"""
        print("\n✨ Testing AI Content Improvement...")
        
        test_cases = [
            {
                "name": "Engagement Improvement",
                "data": {
                    "content": "Our company offers digital marketing services. We help businesses grow online.",
                    "improvement_type": "engagement"
                }
            },
            {
                "name": "Professional Tone Improvement",
                "data": {
                    "content": "Hey guys! Check out this awesome new feature we just launched. It's pretty cool!",
                    "improvement_type": "professional"
                }
            },
            {
                "name": "SEO Improvement",
                "data": {
                    "content": "Learn about marketing. We teach you how to market your business better.",
                    "improvement_type": "seo"
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/improve-content",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        improvement_data = data["data"]
                        improved_length = len(improvement_data.get("improved_content", ""))
                        original_length = len(improvement_data.get("original_content", ""))
                        self.log_test(
                            f"Improve Content - {test_case['name']}", 
                            "PASS", 
                            f"Improved content ({original_length} → {improved_length} chars), Type: {improvement_data.get('improvement_type')}",
                            response_time
                        )
                    else:
                        self.log_test(f"Improve Content - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Improve Content - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Improve Content - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_generate_course_content(self):
        """Test /api/ai/generate-course-content endpoint"""
        print("\n🎓 Testing AI Course Content Generation...")
        
        test_cases = [
            {
                "name": "Digital Marketing Course",
                "data": {
                    "topic": "Digital Marketing Fundamentals",
                    "lesson_title": "Introduction to Social Media Marketing",
                    "difficulty": "beginner",
                    "duration": 20
                }
            },
            {
                "name": "AI Business Course",
                "data": {
                    "topic": "AI Tools for Business",
                    "lesson_title": "Implementing AI in Customer Service",
                    "difficulty": "intermediate",
                    "duration": 30
                }
            },
            {
                "name": "Entrepreneurship Course",
                "data": {
                    "topic": "Startup Fundamentals",
                    "lesson_title": "Building Your MVP",
                    "difficulty": "advanced",
                    "duration": 45
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/generate-course-content",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        course_data = data["data"]
                        content_length = len(course_data.get("content", ""))
                        self.log_test(
                            f"Generate Course Content - {test_case['name']}", 
                            "PASS", 
                            f"Generated {content_length} chars, Topic: {course_data.get('topic')}, Duration: {course_data.get('estimated_duration')}min",
                            response_time
                        )
                    else:
                        self.log_test(f"Generate Course Content - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Generate Course Content - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Generate Course Content - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_generate_email_sequence(self):
        """Test /api/ai/generate-email-sequence endpoint"""
        print("\n📧 Testing AI Email Sequence Generation...")
        
        test_cases = [
            {
                "name": "Welcome Email Sequence",
                "data": {
                    "purpose": "Welcome new subscribers and introduce our AI tools platform",
                    "audience": "Small business owners and entrepreneurs",
                    "sequence_length": 5
                }
            },
            {
                "name": "Product Launch Sequence",
                "data": {
                    "purpose": "Announce and promote our new course on digital marketing",
                    "audience": "Marketing professionals and business owners",
                    "sequence_length": 3
                }
            },
            {
                "name": "Re-engagement Sequence",
                "data": {
                    "purpose": "Re-engage inactive subscribers with valuable content",
                    "audience": "Previous customers who haven't engaged recently",
                    "sequence_length": 4
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/generate-email-sequence",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        email_data = data["data"]
                        emails_length = len(str(email_data.get("emails", "")))
                        self.log_test(
                            f"Generate Email Sequence - {test_case['name']}", 
                            "PASS", 
                            f"Generated {email_data.get('sequence_length')} emails ({emails_length} chars), Audience: {email_data.get('audience')}",
                            response_time
                        )
                    else:
                        self.log_test(f"Generate Email Sequence - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Generate Email Sequence - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Generate Email Sequence - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_get_content_ideas(self):
        """Test /api/ai/get-content-ideas endpoint"""
        print("\n💡 Testing AI Content Ideas Generation...")
        
        test_cases = [
            {
                "name": "Tech Industry Blog Ideas",
                "data": {
                    "industry": "Technology",
                    "content_type": "blog_post",
                    "count": 10
                }
            },
            {
                "name": "Healthcare Social Media Ideas",
                "data": {
                    "industry": "Healthcare",
                    "content_type": "social_media",
                    "count": 8
                }
            },
            {
                "name": "Finance Video Ideas",
                "data": {
                    "industry": "Finance",
                    "content_type": "video",
                    "count": 12
                }
            }
        ]
        
        for test_case in test_cases:
            try:
                start_time = time.time()
                response = requests.post(
                    f"{self.backend_url}/api/ai/get-content-ideas",
                    json=test_case["data"],
                    headers=self.get_headers(),
                    timeout=30
                )
                response_time = time.time() - start_time
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get("success") and data.get("data"):
                        ideas_data = data["data"]
                        ideas_count = len(ideas_data.get("ideas", []))
                        self.log_test(
                            f"Get Content Ideas - {test_case['name']}", 
                            "PASS", 
                            f"Generated {ideas_count} ideas for {ideas_data.get('industry')} {ideas_data.get('content_type')}",
                            response_time
                        )
                    else:
                        self.log_test(f"Get Content Ideas - {test_case['name']}", "FAIL", "Invalid response structure", response_time)
                else:
                    self.log_test(f"Get Content Ideas - {test_case['name']}", "FAIL", f"HTTP {response.status_code}", response_time)
                    
            except Exception as e:
                self.log_test(f"Get Content Ideas - {test_case['name']}", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_usage_analytics(self):
        """Test /api/ai/usage-analytics endpoint"""
        print("\n📊 Testing AI Usage Analytics...")
        
        try:
            start_time = time.time()
            response = requests.get(
                f"{self.backend_url}/api/ai/usage-analytics",
                headers=self.get_headers(),
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                if data.get("success") and data.get("data"):
                    analytics_data = data["data"]
                    total_requests = analytics_data.get("total_requests", 0)
                    success_rate = analytics_data.get("success_rate", 0)
                    feature_usage = analytics_data.get("feature_usage", {})
                    self.log_test(
                        "AI Usage Analytics", 
                        "PASS", 
                        f"Total requests: {total_requests}, Success rate: {success_rate:.1f}%, Features tracked: {len(feature_usage)}",
                        response_time
                    )
                else:
                    self.log_test("AI Usage Analytics", "FAIL", "Invalid response structure", response_time)
            else:
                self.log_test("AI Usage Analytics", "FAIL", f"HTTP {response.status_code}", response_time)
                
        except Exception as e:
            self.log_test("AI Usage Analytics", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_openai_integration(self):
        """Test OpenAI API key configuration"""
        print("\n🤖 Testing OpenAI Integration Configuration...")
        
        try:
            # Check if OpenAI API key is configured in environment
            openai_key = os.getenv("OPENAI_API_KEY")
            if openai_key and openai_key.startswith("sk-"):
                self.log_test(
                    "OpenAI API Key Configuration", 
                    "PASS", 
                    f"API key configured (starts with: {openai_key[:10]}...)",
                    0
                )
            else:
                self.log_test(
                    "OpenAI API Key Configuration", 
                    "FAIL", 
                    "OpenAI API key not found or invalid format",
                    0
                )
        except Exception as e:
            self.log_test("OpenAI API Key Configuration", "FAIL", f"Exception: {str(e)}", 0)
    
    def test_error_handling(self):
        """Test error handling for invalid requests"""
        print("\n🚨 Testing Error Handling...")
        
        # Test with invalid content type
        try:
            start_time = time.time()
            response = requests.post(
                f"{self.backend_url}/api/ai/generate-content",
                json={
                    "prompt": "",  # Empty prompt
                    "content_type": "invalid_type",
                    "tone": "professional",
                    "max_tokens": 500
                },
                headers=self.get_headers(),
                timeout=30
            )
            response_time = time.time() - start_time
            
            # Should handle gracefully (either success with fallback or proper error)
            if response.status_code in [200, 400, 422]:
                self.log_test(
                    "Error Handling - Invalid Content Type", 
                    "PASS", 
                    f"Handled gracefully with HTTP {response.status_code}",
                    response_time
                )
            else:
                self.log_test("Error Handling - Invalid Content Type", "FAIL", f"Unexpected HTTP {response.status_code}", response_time)
                
        except Exception as e:
            self.log_test("Error Handling - Invalid Content Type", "FAIL", f"Exception: {str(e)}", 0)
        
        # Test without authentication
        try:
            start_time = time.time()
            response = requests.post(
                f"{self.backend_url}/api/ai/generate-content",
                json={
                    "prompt": "Test prompt",
                    "content_type": "social_post",
                    "tone": "professional",
                    "max_tokens": 100
                },
                headers={"Content-Type": "application/json"},  # No auth header
                timeout=30
            )
            response_time = time.time() - start_time
            
            if response.status_code == 401:
                self.log_test(
                    "Error Handling - No Authentication", 
                    "PASS", 
                    "Properly rejected with 401 Unauthorized",
                    response_time
                )
            else:
                self.log_test("Error Handling - No Authentication", "FAIL", f"Expected 401, got {response.status_code}", response_time)
                
        except Exception as e:
            self.log_test("Error Handling - No Authentication", "FAIL", f"Exception: {str(e)}", 0)
    
    def run_all_tests(self):
        """Run all AI endpoint tests"""
        print("🚀 Starting AI Endpoints Testing - Mewayz Platform v3.0.0")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate():
            print("❌ Authentication failed. Cannot proceed with tests.")
            return
        
        # Test OpenAI integration configuration
        self.test_openai_integration()
        
        # Test all AI endpoints
        self.test_generate_content()
        self.test_analyze_content()
        self.test_generate_hashtags()
        self.test_improve_content()
        self.test_generate_course_content()
        self.test_generate_email_sequence()
        self.test_get_content_ideas()
        self.test_usage_analytics()
        
        # Test error handling
        self.test_error_handling()
        
        # Print summary
        self.print_summary()
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("🎯 AI ENDPOINTS TESTING SUMMARY")
        print("=" * 80)
        
        success_rate = (self.passed_tests / self.total_tests * 100) if self.total_tests > 0 else 0
        
        print(f"📊 Total Tests: {self.total_tests}")
        print(f"✅ Passed: {self.passed_tests}")
        print(f"❌ Failed: {self.total_tests - self.passed_tests}")
        print(f"📈 Success Rate: {success_rate:.1f}%")
        
        if success_rate >= 90:
            print("🎉 EXCELLENT: AI endpoints are working excellently!")
        elif success_rate >= 75:
            print("✅ GOOD: AI endpoints are working well with minor issues")
        elif success_rate >= 50:
            print("⚠️ MODERATE: AI endpoints have some issues that need attention")
        else:
            print("❌ CRITICAL: AI endpoints have significant issues requiring immediate attention")
        
        print("\n📋 DETAILED RESULTS:")
        for result in self.test_results:
            status_icon = "✅" if result["status"] == "PASS" else "❌"
            print(f"{status_icon} {result['test']}: {result['status']} ({result['response_time']})")
            if result["details"]:
                print(f"   {result['details']}")
        
        print("\n🔍 KEY FINDINGS:")
        
        # Check OpenAI integration
        openai_tests = [r for r in self.test_results if "OpenAI" in r["test"]]
        if openai_tests and openai_tests[0]["status"] == "PASS":
            print("✅ OpenAI API integration is properly configured")
        else:
            print("❌ OpenAI API integration needs attention")
        
        # Check authentication
        auth_tests = [r for r in self.test_results if "Authentication" in r["test"]]
        if auth_tests and auth_tests[0]["status"] == "PASS":
            print("✅ JWT authentication is working correctly")
        else:
            print("❌ JWT authentication has issues")
        
        # Check core AI features
        ai_feature_tests = [r for r in self.test_results if any(feature in r["test"] for feature in ["Generate Content", "Analyze Content", "Generate Hashtags", "Improve Content"])]
        ai_passed = len([r for r in ai_feature_tests if r["status"] == "PASS"])
        ai_total = len(ai_feature_tests)
        
        if ai_total > 0:
            ai_success_rate = (ai_passed / ai_total * 100)
            if ai_success_rate >= 80:
                print(f"✅ Core AI features are highly functional ({ai_success_rate:.1f}% success rate)")
            else:
                print(f"⚠️ Core AI features need attention ({ai_success_rate:.1f}% success rate)")
        
        # Check usage analytics
        analytics_tests = [r for r in self.test_results if "Analytics" in r["test"]]
        if analytics_tests and analytics_tests[0]["status"] == "PASS":
            print("✅ AI usage logging and analytics are working")
        else:
            print("❌ AI usage logging and analytics need attention")
        
        print("\n" + "=" * 80)

if __name__ == "__main__":
    runner = AIEndpointsTestRunner()
    runner.run_all_tests()