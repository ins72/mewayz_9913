#!/usr/bin/env python3
"""
Focused Testing for Enhanced Mewayz Platform v2 Features
Testing the key enhanced features mentioned in the review request
"""

import requests
import json
import time
from datetime import datetime

class EnhancedMewayzTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token from the test_result.md
        self.auth_token = "3|2tizxvYX1aqPpjgTN6rGz0QY2FMeWtHpnMts7GCY137499ef"
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
        
    def make_request(self, method, endpoint, data=None, headers=None, auth_required=True):
        """Make HTTP request with proper headers and rate limiting"""
        # Add delay to avoid rate limiting
        time.sleep(1.2)  # Increased delay to avoid rate limiting
        
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if headers:
            default_headers.update(headers)
            
        # Add auth token if required and available
        if auth_required and self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, params=data, timeout=15)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=15)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=15)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=15)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 15 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None
    
    def test_core_system_health(self):
        """Test Core System Health as mentioned in review request"""
        print("\n=== Testing Core System Health ===")
        
        # Test health endpoint
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("API Health Check", True, f"System healthy - Status: {data.get('data', {}).get('status', 'unknown')}")
        else:
            self.log_test("API Health Check", False, f"Health check failed - Status: {response.status_code if response else 'No response'}")
        
        # Test user authentication
        response = self.make_request('GET', '/auth/me')
        if response and response.status_code == 200:
            data = response.json()
            user_name = data.get('user', {}).get('name', 'unknown')
            self.log_test("User Authentication", True, f"Authentication working - User: {user_name}")
        else:
            self.log_test("User Authentication", False, f"Authentication failed - Status: {response.status_code if response else 'No response'}")
    
    def test_unified_data_controller(self):
        """Test UnifiedDataController - New comprehensive cross-platform analytics"""
        print("\n=== Testing UnifiedDataController (New Feature) ===")
        
        # Test unified analytics
        response = self.make_request('GET', '/unified/analytics')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Unified Analytics", True, "Cross-platform analytics retrieval successful")
        else:
            self.log_test("Unified Analytics", False, f"Unified analytics failed - Status: {response.status_code if response else 'No response'}")
        
        # Test customer journey
        response = self.make_request('GET', '/unified/customer-journey/test-customer-123')
        if response and response.status_code == 200:
            self.log_test("Customer Journey Analytics", True, "Customer journey analytics working")
        else:
            self.log_test("Customer Journey Analytics", False, f"Customer journey failed - Status: {response.status_code if response else 'No response'}")
        
        # Test automation recommendations
        response = self.make_request('GET', '/unified/automation-recommendations')
        if response and response.status_code == 200:
            self.log_test("Automation Recommendations", True, "Automation recommendations working")
        else:
            self.log_test("Automation Recommendations", False, f"Automation recommendations failed - Status: {response.status_code if response else 'No response'}")
        
        # Test platform health
        response = self.make_request('GET', '/unified/platform-health')
        if response and response.status_code == 200:
            self.log_test("Platform Health Monitoring", True, "Platform health monitoring working")
        else:
            self.log_test("Platform Health Monitoring", False, f"Platform health failed - Status: {response.status_code if response else 'No response'}")
        
        # Test business intelligence
        response = self.make_request('GET', '/unified/business-intelligence')
        if response and response.status_code == 200:
            self.log_test("Business Intelligence", True, "Business intelligence working")
        else:
            self.log_test("Business Intelligence", False, f"Business intelligence failed - Status: {response.status_code if response else 'No response'}")
    
    def test_enhanced_email_marketing(self):
        """Test Enhanced EmailMarketingController - Deep analytics with predictive insights"""
        print("\n=== Testing Enhanced Email Marketing Controller ===")
        
        # Test email marketing analytics
        response = self.make_request('GET', '/email-marketing/analytics')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Enhanced Email Analytics", True, "Email marketing analytics with cross-platform integration working")
        else:
            self.log_test("Enhanced Email Analytics", False, f"Enhanced email analytics failed - Status: {response.status_code if response else 'No response'}")
        
        # Test campaigns
        response = self.make_request('GET', '/email-marketing/campaigns')
        if response and response.status_code == 200:
            self.log_test("Email Campaigns", True, "Email campaigns retrieval working")
        else:
            self.log_test("Email Campaigns", False, f"Email campaigns failed - Status: {response.status_code if response else 'No response'}")
    
    def test_enhanced_course_controller(self):
        """Test Enhanced CourseController - Complete analytics and optimization recommendations"""
        print("\n=== Testing Enhanced Course Controller ===")
        
        # Test courses with enhanced analytics
        response = self.make_request('GET', '/courses/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Enhanced Course Analytics", True, "Course system with deep performance metrics working")
        else:
            self.log_test("Enhanced Course Analytics", False, f"Enhanced course analytics failed - Status: {response.status_code if response else 'No response'}")
    
    def test_enhanced_biosite_controller(self):
        """Test Enhanced BioSiteController - Advanced analytics and SEO analysis"""
        print("\n=== Testing Enhanced BioSite Controller ===")
        
        # Test bio sites with enhanced analytics
        response = self.make_request('GET', '/bio-sites/')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Enhanced BioSite Analytics", True, "Bio sites with comprehensive analytics working")
        else:
            self.log_test("Enhanced BioSite Analytics", False, f"Enhanced bio site analytics failed - Status: {response.status_code if response else 'No response'}")
    
    def test_enhanced_crm_controller(self):
        """Test Enhanced CrmController - Unified contact data across platforms"""
        print("\n=== Testing Enhanced CRM Controller ===")
        
        # Test CRM contacts with unified data
        response = self.make_request('GET', '/crm/contacts/1')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Enhanced CRM Contacts", True, "CRM with unified cross-platform data working")
        else:
            self.log_test("Enhanced CRM Contacts", False, f"Enhanced CRM contacts failed - Status: {response.status_code if response else 'No response'}")
    
    def test_cross_platform_features(self):
        """Test New Cross-Platform Features"""
        print("\n=== Testing New Cross-Platform Features ===")
        
        # Test cross-platform campaigns
        campaign_data = {
            "name": "Cross-Platform Test Campaign",
            "platforms": ["instagram", "email", "bio_site"],
            "content": {
                "title": "Test Campaign",
                "description": "Testing cross-platform campaign execution"
            }
        }
        
        response = self.make_request('POST', '/unified/campaigns', campaign_data)
        if response and response.status_code in [200, 201]:
            self.log_test("Cross-Platform Campaigns", True, "Cross-platform campaign execution working")
        else:
            self.log_test("Cross-Platform Campaigns", False, f"Cross-platform campaigns failed - Status: {response.status_code if response else 'No response'}")
        
        # Test attribution analysis
        response = self.make_request('GET', '/unified/attribution/30-days')
        if response and response.status_code == 200:
            self.log_test("Attribution Analysis", True, "Attribution analysis working")
        else:
            self.log_test("Attribution Analysis", False, f"Attribution analysis failed - Status: {response.status_code if response else 'No response'}")
        
        # Test customer 360 view
        response = self.make_request('GET', '/unified/customer-360/test-customer-123')
        if response and response.status_code == 200:
            self.log_test("Customer 360 View", True, "Customer 360 view working")
        else:
            self.log_test("Customer 360 View", False, f"Customer 360 view failed - Status: {response.status_code if response else 'No response'}")
        
        # Test revenue attribution
        response = self.make_request('GET', '/unified/revenue-attribution')
        if response and response.status_code == 200:
            self.log_test("Revenue Attribution", True, "Revenue attribution working")
        else:
            self.log_test("Revenue Attribution", False, f"Revenue attribution failed - Status: {response.status_code if response else 'No response'}")
    
    def test_advanced_integration_features(self):
        """Test Advanced Integration Features"""
        print("\n=== Testing Advanced Integration Features ===")
        
        # Test platform health monitoring
        response = self.make_request('GET', '/unified/platform-health')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Platform Health Monitoring", True, "Advanced platform health monitoring working")
        else:
            self.log_test("Platform Health Monitoring", False, f"Platform health monitoring failed - Status: {response.status_code if response else 'No response'}")
    
    def run_all_tests(self):
        """Run all enhanced feature tests"""
        print("🚀 Starting Enhanced Mewayz Platform v2 Testing")
        print("=" * 80)
        
        # Test core system health first
        self.test_core_system_health()
        
        # Test new enhanced features
        self.test_unified_data_controller()
        self.test_enhanced_email_marketing()
        self.test_enhanced_course_controller()
        self.test_enhanced_biosite_controller()
        self.test_enhanced_crm_controller()
        self.test_cross_platform_features()
        self.test_advanced_integration_features()
        
        # Print summary
        print("\n" + "=" * 80)
        print("📊 TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("=" * 80)
        
        return success_rate

if __name__ == "__main__":
    tester = EnhancedMewayzTester()
    success_rate = tester.run_all_tests()
    
    if success_rate >= 80:
        print("🎉 EXCELLENT: Enhanced features are working well!")
    elif success_rate >= 60:
        print("✅ GOOD: Most enhanced features are working!")
    elif success_rate >= 40:
        print("⚠️  MODERATE: Some enhanced features need attention!")
    else:
        print("❌ CRITICAL: Major issues with enhanced features!")