#!/usr/bin/env python3
"""
Focused Testing for Enhanced AI Features and Escrow System
Testing timeout issues after EnhancedAIController.php fixes
"""

import requests
import json
import time
from datetime import datetime

class TimeoutTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use the token from test_result.md
        self.auth_token = "3|tBn24bcMfBMYR5OKp7QjsK0RF6fmP57e0h6MWKlpffe81281"
        self.test_results = {}
        self.session = requests.Session()
        
    def log_test(self, test_name, success, message, response_data=None):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status} {test_name}: {message}")
        
        self.test_results[test_name] = {
            'success': success,
            'message': message,
            'response_data': response_data,
            'timestamp': datetime.now().isoformat()
        }
        
    def make_request(self, method, endpoint, data=None, timeout=15):
        """Make HTTP request with timeout handling"""
        url = f"{self.api_url}{endpoint}"
        
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': f'Bearer {self.auth_token}'
        }
        
        try:
            start_time = time.time()
            
            if method.upper() == 'GET':
                response = self.session.get(url, headers=headers, params=data, timeout=timeout)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=headers, json=data, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            elapsed_time = time.time() - start_time
            
            return response, elapsed_time
            
        except requests.exceptions.Timeout:
            elapsed_time = time.time() - start_time
            return None, elapsed_time
        except requests.exceptions.RequestException as e:
            elapsed_time = time.time() - start_time
            print(f"Request failed for {url}: {e}")
            return None, elapsed_time
    
    def test_enhanced_ai_features(self):
        """Test Enhanced AI Features for timeout issues"""
        print("\n🤖 TESTING ENHANCED AI FEATURES")
        print("=" * 50)
        
        # Test 1: Content Generation (working in previous test)
        print("\n1. Testing Content Generation...")
        content_data = {
            "topic": "digital marketing strategies",
            "content_type": "blog_post",
            "target_audience": "small business owners",
            "tone": "professional"
        }
        
        response, elapsed = self.make_request('POST', '/ai/content/generate', content_data)
        if response and response.status_code == 200:
            self.log_test("AI Content Generation", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Content Generation", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Content Generation", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 2: SEO Optimization
        print("\n2. Testing SEO Optimization...")
        seo_data = {
            "content": "This is a sample blog post about digital marketing strategies for small businesses.",
            "target_keywords": ["digital marketing", "small business", "online presence"],
            "meta_description": "Learn effective digital marketing strategies for small businesses"
        }
        
        response, elapsed = self.make_request('POST', '/ai/content/seo-optimize', seo_data)
        if response and response.status_code == 200:
            self.log_test("AI SEO Optimization", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI SEO Optimization", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI SEO Optimization", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 3: Competitor Analysis
        print("\n3. Testing Competitor Analysis...")
        competitor_data = {
            "competitor_urls": ["https://competitor1.com", "https://competitor2.com"],
            "analysis_type": "content_strategy",
            "industry": "digital marketing"
        }
        
        response, elapsed = self.make_request('POST', '/ai/competitors/analyze', competitor_data)
        if response and response.status_code == 200:
            self.log_test("AI Competitor Analysis", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Competitor Analysis", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Competitor Analysis", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 4: Business Insights
        print("\n4. Testing Business Insights...")
        insights_data = {
            "business_type": "e-commerce",
            "metrics": {
                "revenue": 50000,
                "customers": 1200,
                "conversion_rate": 0.03
            },
            "time_period": "last_quarter"
        }
        
        response, elapsed = self.make_request('POST', '/ai/insights/business', insights_data)
        if response and response.status_code == 200:
            self.log_test("AI Business Insights", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Business Insights", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Business Insights", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 5: Sentiment Analysis
        print("\n5. Testing Sentiment Analysis...")
        sentiment_data = {
            "text": "I love this product! It's amazing and works perfectly for my needs.",
            "context": "product_review"
        }
        
        response, elapsed = self.make_request('POST', '/ai/sentiment/analyze', sentiment_data)
        if response and response.status_code == 200:
            self.log_test("AI Sentiment Analysis", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Sentiment Analysis", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Sentiment Analysis", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 6: Price Optimization
        print("\n6. Testing Price Optimization...")
        pricing_data = {
            "product_type": "digital_course",
            "current_price": 99.99,
            "competitor_prices": [89.99, 109.99, 79.99],
            "target_margin": 0.70
        }
        
        response, elapsed = self.make_request('POST', '/ai/pricing/optimize', pricing_data)
        if response and response.status_code == 200:
            self.log_test("AI Price Optimization", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Price Optimization", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Price Optimization", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 7: Lead Scoring
        print("\n7. Testing Lead Scoring...")
        lead_data = {
            "leads": [
                {
                    "email": "potential@customer.com",
                    "company": "Tech Startup",
                    "engagement_score": 85,
                    "source": "website"
                }
            ]
        }
        
        response, elapsed = self.make_request('POST', '/ai/leads/score', lead_data)
        if response and response.status_code == 200:
            self.log_test("AI Lead Scoring", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Lead Scoring", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Lead Scoring", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 8: Chatbot Response
        print("\n8. Testing Chatbot Response...")
        chatbot_data = {
            "message": "How can I improve my website's SEO?",
            "context": "seo_consultation",
            "user_profile": "small_business_owner"
        }
        
        response, elapsed = self.make_request('POST', '/ai/chatbot/respond', chatbot_data)
        if response and response.status_code == 200:
            self.log_test("AI Chatbot Response", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Chatbot Response", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Chatbot Response", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
        
        # Test 9: Trend Prediction
        print("\n9. Testing Trend Prediction...")
        trend_data = {
            "industry": "digital_marketing",
            "data_points": [
                {"date": "2024-01-01", "value": 100},
                {"date": "2024-02-01", "value": 110},
                {"date": "2024-03-01", "value": 125}
            ],
            "prediction_period": "next_quarter"
        }
        
        response, elapsed = self.make_request('POST', '/ai/trends/predict', trend_data)
        if response and response.status_code == 200:
            self.log_test("AI Trend Prediction", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("AI Trend Prediction", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("AI Trend Prediction", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
    
    def test_escrow_system(self):
        """Test Escrow System for timeout issues"""
        print("\n💰 TESTING ESCROW SYSTEM")
        print("=" * 50)
        
        # Test 1: Get Escrow Transactions
        print("\n1. Testing Get Escrow Transactions...")
        response, elapsed = self.make_request('GET', '/escrow/')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Transactions", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("Get Escrow Transactions", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("Get Escrow Transactions", False, f"Failed with status {response.status_code} in {elapsed:.2f}s - {response.text[:100] if response else 'No response'}")
        
        # Test 2: Create Escrow Transaction
        print("\n2. Testing Create Escrow Transaction...")
        escrow_data = {
            "buyer_id": "test-buyer-id",
            "seller_id": "test-seller-id",
            "amount": 100.00,
            "currency": "USD",
            "description": "Test digital product transaction",
            "terms": "Standard escrow terms for digital product delivery"
        }
        
        response, elapsed = self.make_request('POST', '/escrow/', escrow_data)
        escrow_id = None
        if response and response.status_code in [200, 201]:
            self.log_test("Create Escrow Transaction", True, f"Success in {elapsed:.2f}s")
            try:
                data = response.json()
                escrow_id = data.get('data', {}).get('id')
            except:
                pass
        elif response is None:
            self.log_test("Create Escrow Transaction", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("Create Escrow Transaction", False, f"Failed with status {response.status_code} in {elapsed:.2f}s - {response.text[:100] if response else 'No response'}")
        
        # Test 3: Get Escrow Statistics
        print("\n3. Testing Get Escrow Statistics...")
        response, elapsed = self.make_request('GET', '/escrow/statistics/overview')
        if response and response.status_code == 200:
            self.log_test("Get Escrow Statistics", True, f"Success in {elapsed:.2f}s")
        elif response is None:
            self.log_test("Get Escrow Statistics", False, f"TIMEOUT after {elapsed:.2f}s")
        else:
            self.log_test("Get Escrow Statistics", False, f"Failed with status {response.status_code} in {elapsed:.2f}s - {response.text[:100] if response else 'No response'}")
        
        # Test 4: Get Specific Escrow Transaction (if created)
        if escrow_id:
            print(f"\n4. Testing Get Specific Escrow Transaction ({escrow_id})...")
            response, elapsed = self.make_request('GET', f'/escrow/{escrow_id}')
            if response and response.status_code == 200:
                self.log_test("Get Specific Escrow Transaction", True, f"Success in {elapsed:.2f}s")
            elif response is None:
                self.log_test("Get Specific Escrow Transaction", False, f"TIMEOUT after {elapsed:.2f}s")
            else:
                self.log_test("Get Specific Escrow Transaction", False, f"Failed with status {response.status_code} in {elapsed:.2f}s")
    
    def run_focused_tests(self):
        """Run focused tests on Enhanced AI and Escrow systems"""
        print("🎯 FOCUSED TIMEOUT TESTING")
        print("Testing Enhanced AI Features and Escrow System")
        print("Focus: Verify if timeout issues have been resolved")
        print("=" * 60)
        
        # Test Enhanced AI Features
        self.test_enhanced_ai_features()
        
        # Test Escrow System
        self.test_escrow_system()
        
        # Print summary
        print("\n" + "=" * 60)
        print("🎯 FOCUSED TEST SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        timeout_tests = sum(1 for result in self.test_results.values() if 'TIMEOUT' in result['message'])
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Timeouts: {timeout_tests} ⏰")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        # Categorize results
        ai_tests = [name for name in self.test_results.keys() if 'AI' in name]
        escrow_tests = [name for name in self.test_results.keys() if 'Escrow' in name]
        
        ai_passed = sum(1 for name in ai_tests if self.test_results[name]['success'])
        escrow_passed = sum(1 for name in escrow_tests if self.test_results[name]['success'])
        
        print(f"\n📊 SYSTEM BREAKDOWN:")
        print(f"Enhanced AI Features: {ai_passed}/{len(ai_tests)} passed ({(ai_passed/len(ai_tests)*100):.1f}%)")
        print(f"Escrow System: {escrow_passed}/{len(escrow_tests)} passed ({(escrow_passed/len(escrow_tests)*100):.1f}%)")
        
        print(f"\n📋 DETAILED RESULTS:")
        for test_name, result in self.test_results.items():
            status = '✅ PASS' if result['success'] else '❌ FAIL'
            print(f"{status} {test_name}: {result['message']}")
        
        # Timeout analysis
        if timeout_tests > 0:
            print(f"\n⏰ TIMEOUT ANALYSIS:")
            print(f"Found {timeout_tests} timeout issues - these need investigation")
            timeout_names = [name for name, result in self.test_results.items() if 'TIMEOUT' in result['message']]
            for name in timeout_names:
                print(f"  - {name}")
        
        return {
            'total_tests': total_tests,
            'passed_tests': passed_tests,
            'failed_tests': failed_tests,
            'timeout_tests': timeout_tests,
            'success_rate': (passed_tests/total_tests)*100,
            'ai_success_rate': (ai_passed/len(ai_tests)*100) if ai_tests else 0,
            'escrow_success_rate': (escrow_passed/len(escrow_tests)*100) if escrow_tests else 0
        }

if __name__ == "__main__":
    tester = TimeoutTester()
    results = tester.run_focused_tests()