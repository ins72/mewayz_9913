#!/usr/bin/env python3
"""
Focused Integration Testing for Mewayz Platform
Testing the specific integrations mentioned in the review request:
1. Google OAuth Integration - Real Google OAuth with Laravel Socialite
2. ElasticEmail Integration - Email sending service integrated 
3. OpenAI Integration - Real OpenAI API with emergentintegrations library
"""

import requests
import json
import time
from datetime import datetime

class IntegrationTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
        # Login first to get auth token
        self.login()
    
    def login(self):
        """Login to get authentication token"""
        try:
            response = self.session.post(f"{self.api_url}/auth/login", json={
                "email": "admin@example.com",
                "password": "admin123"
            })
            
            if response.status_code == 200:
                data = response.json()
                self.auth_token = data.get('token')
                self.session.headers.update({'Authorization': f'Bearer {self.auth_token}'})
                print("✅ Authentication successful")
                return True
            else:
                print(f"❌ Authentication failed: {response.status_code}")
                return False
        except Exception as e:
            print(f"❌ Authentication error: {str(e)}")
            return False
    
    def test_request(self, method, endpoint, data=None, description=""):
        """Make a test request and record results"""
        url = f"{self.api_url}{endpoint}"
        start_time = time.time()
        
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, params=data)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url)
            else:
                raise ValueError(f"Unsupported method: {method}")
            
            duration = (time.time() - start_time) * 1000
            
            # Determine if test passed
            passed = 200 <= response.status_code < 300
            
            result = {
                'method': method.upper(),
                'endpoint': endpoint,
                'description': description,
                'status_code': response.status_code,
                'duration_ms': round(duration, 2),
                'passed': passed,
                'response_data': None,
                'error': None
            }
            
            try:
                result['response_data'] = response.json()
            except:
                result['response_data'] = response.text[:500] if response.text else None
            
            if not passed:
                result['error'] = result['response_data']
            
            self.test_results.append(result)
            
            # Print result
            status_icon = "✅" if passed else "❌"
            print(f"{status_icon} {method.upper()} {endpoint} ({duration:.1f}ms) - Status: {response.status_code}")
            if not passed and result['response_data']:
                error_text = str(result['response_data'])[:200]
                print(f"   Error: {error_text}")
            
            return response
            
        except Exception as e:
            duration = (time.time() - start_time) * 1000
            result = {
                'method': method.upper(),
                'endpoint': endpoint,
                'description': description,
                'status_code': 0,
                'duration_ms': round(duration, 2),
                'passed': False,
                'response_data': None,
                'error': str(e)
            }
            self.test_results.append(result)
            print(f"❌ {method.upper()} {endpoint} ({duration:.1f}ms) - Exception: {str(e)}")
            return None
    
    def test_google_oauth_integration(self):
        """Test Google OAuth Integration"""
        print("\n" + "="*60)
        print("🔐 TESTING GOOGLE OAUTH INTEGRATION")
        print("="*60)
        
        # Test OAuth providers endpoint
        self.test_request('GET', '/auth/oauth/providers', description="Get OAuth providers")
        
        # Test Google OAuth redirect
        self.test_request('GET', '/auth/oauth/google', description="Google OAuth redirect")
        
        # Test OAuth status
        self.test_request('GET', '/oauth/status', description="Get OAuth status")
        
        # Test OAuth callback (test mode)
        self.test_request('POST', '/auth/oauth/google/test', {
            'test_user': {
                'id': 'google_test_123',
                'email': 'test.oauth@gmail.com',
                'name': 'OAuth Test User'
            }
        }, description="Google OAuth test callback")
    
    def test_elasticemail_integration(self):
        """Test ElasticEmail Integration"""
        print("\n" + "="*60)
        print("📧 TESTING ELASTICEMAIL INTEGRATION")
        print("="*60)
        
        # Test ElasticEmail connection
        self.test_request('GET', '/test-elastic-email', description="Test ElasticEmail connection")
        
        # Test sending email with ElasticEmail (if campaigns exist)
        campaigns_response = self.test_request('GET', '/email-marketing/campaigns', description="Get campaigns for ElasticEmail test")
        
        if campaigns_response and campaigns_response.status_code == 200:
            campaigns = campaigns_response.json()
            if campaigns.get('data') and len(campaigns['data']) > 0:
                campaign_id = campaigns['data'][0]['id']
                self.test_request('POST', f'/campaigns/{campaign_id}/send-elastic-email', description="Send campaign with ElasticEmail")
    
    def test_openai_integration(self):
        """Test OpenAI Integration"""
        print("\n" + "="*60)
        print("🤖 TESTING OPENAI INTEGRATION")
        print("="*60)
        
        # Test AI services endpoint
        self.test_request('GET', '/ai/services', description="Get AI services")
        
        # Test AI chat with OpenAI
        self.test_request('POST', '/ai/chat', {
            'message': 'Hello, can you help me with my business?',
            'service': 'openai',
            'context': 'Business consultation'
        }, description="OpenAI chat test")
        
        # Test content generation with OpenAI
        self.test_request('POST', '/ai/generate-content', {
            'type': 'social_post',
            'service': 'openai',
            'prompt': 'Create a social media post about business growth',
            'tone': 'professional',
            'length': 'medium'
        }, description="OpenAI content generation")
        
        # Test AI recommendations
        self.test_request('POST', '/ai/recommendations', {
            'type': 'hashtags',
            'service': 'openai',
            'data': {'topic': 'business growth'}
        }, description="OpenAI recommendations")
        
        # Test text analysis
        self.test_request('POST', '/ai/analyze-text', {
            'text': 'This is a great platform for business growth and success!',
            'service': 'openai',
            'analysis_type': 'sentiment'
        }, description="OpenAI text analysis")
    
    def generate_report(self):
        """Generate comprehensive test report"""
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['passed'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "="*80)
        print("📋 INTEGRATION TESTING REPORT")
        print("="*80)
        
        print(f"\n📊 OVERALL STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests} ({success_rate:.1f}%)")
        print(f"   ❌ Failed: {failed_tests} ({100-success_rate:.1f}%)")
        
        # Group results by integration
        oauth_tests = [r for r in self.test_results if 'oauth' in r['endpoint'].lower()]
        email_tests = [r for r in self.test_results if 'elastic' in r['endpoint'].lower() or 'email' in r['endpoint'].lower()]
        ai_tests = [r for r in self.test_results if '/ai/' in r['endpoint']]
        
        def calc_success_rate(tests):
            if not tests:
                return 0
            passed = sum(1 for t in tests if t['passed'])
            return (passed / len(tests)) * 100
        
        print(f"\n📈 RESULTS BY INTEGRATION:")
        print(f"   🔐 Google OAuth: {len([t for t in oauth_tests if t['passed']])}/{len(oauth_tests)} ({calc_success_rate(oauth_tests):.1f}%)")
        print(f"   📧 ElasticEmail: {len([t for t in email_tests if t['passed']])}/{len(email_tests)} ({calc_success_rate(email_tests):.1f}%)")
        print(f"   🤖 OpenAI: {len([t for t in ai_tests if t['passed']])}/{len(ai_tests)} ({calc_success_rate(ai_tests):.1f}%)")
        
        # Show failed tests
        failed_tests_list = [r for r in self.test_results if not r['passed']]
        if failed_tests_list:
            print(f"\n🚨 FAILED TESTS:")
            for test in failed_tests_list:
                print(f"   ❌ {test['method']} {test['endpoint']} - Status: {test['status_code']}")
                if test['error']:
                    error_text = str(test['error'])[:100]
                    print(f"      Error: {error_text}")
        
        # Save detailed results
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"/app/integration_test_results_{timestamp}.json"
        
        with open(filename, 'w') as f:
            json.dump({
                'timestamp': datetime.now().isoformat(),
                'summary': {
                    'total_tests': total_tests,
                    'passed_tests': passed_tests,
                    'failed_tests': failed_tests,
                    'success_rate': success_rate
                },
                'results': self.test_results
            }, f, indent=2)
        
        print(f"\n💾 Detailed results saved to: {filename}")
        
        return success_rate >= 70
    
    def run_all_tests(self):
        """Run all integration tests"""
        print("🚀 STARTING INTEGRATION TESTING")
        print("="*80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("="*80)
        
        if not self.auth_token:
            print("❌ Cannot proceed without authentication")
            return False
        
        try:
            self.test_google_oauth_integration()
            self.test_elasticemail_integration()
            self.test_openai_integration()
        except KeyboardInterrupt:
            print("\n⚠️ Testing interrupted by user")
        except Exception as e:
            print(f"\n❌ Unexpected error during testing: {str(e)}")
        
        return self.generate_report()

def main():
    """Main function to run integration tests"""
    tester = IntegrationTester()
    success = tester.run_all_tests()
    
    print(f"\n🏁 INTEGRATION TESTING COMPLETED")
    if success:
        print("✅ Overall testing successful")
    else:
        print("❌ Some tests failed - review results above")

if __name__ == "__main__":
    main()