#!/usr/bin/env python3
"""
Focused Production Readiness Test for Mewayz Backend
Tests key systems with proper rate limiting handling
"""

import requests
import json
import time
from datetime import datetime

class FocusedProductionTester:
    def __init__(self):
        self.base_url = "http://localhost:8001"
        self.api_url = f"{self.base_url}/api"
        self.auth_token = "3|SavoOPZ385eWP1JSP3Ms8AiKnV4nWY6ifHDQNaNica2277c4"
        self.results = {}
        
    def make_request(self, method, endpoint, data=None, auth_required=True):
        """Make HTTP request with rate limiting protection"""
        time.sleep(2)  # 2 second delay between requests
        
        url = f"{self.api_url}{endpoint}"
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if auth_required and self.auth_token:
            headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = requests.get(url, headers=headers, params=data, timeout=15)
            elif method.upper() == 'POST':
                response = requests.post(url, headers=headers, json=data, timeout=15)
            else:
                raise ValueError(f"Unsupported method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request error: {e}")
            return None
    
    def test_system(self, name, endpoint, method='GET', data=None, auth_required=True):
        """Test a single system endpoint"""
        print(f"Testing {name}...")
        response = self.make_request(method, endpoint, data, auth_required)
        
        if response is None:
            self.results[name] = {'status': 'TIMEOUT', 'working': False}
            print(f"  ❌ TIMEOUT: {name}")
            return False
            
        if response.status_code == 429:
            self.results[name] = {'status': 'RATE_LIMITED', 'working': False}
            print(f"  ⚠️ RATE LIMITED: {name}")
            return False
            
        if response.status_code in [200, 201]:
            self.results[name] = {'status': 'SUCCESS', 'working': True, 'data': response.json()}
            print(f"  ✅ SUCCESS: {name}")
            return True
        else:
            self.results[name] = {'status': f'HTTP_{response.status_code}', 'working': False}
            print(f"  ❌ FAILED: {name} (Status: {response.status_code})")
            return False
    
    def run_comprehensive_test(self):
        """Run comprehensive production readiness test"""
        print("🚀 FOCUSED PRODUCTION READINESS TEST")
        print("=" * 60)
        
        # Core API Tests
        print("\n📡 CORE API SYSTEMS")
        print("-" * 30)
        self.test_system("Health Check", "/health", auth_required=False)
        self.test_system("API Test", "/test", auth_required=False)
        
        # Authentication Tests
        print("\n🔐 AUTHENTICATION SYSTEMS")
        print("-" * 30)
        self.test_system("Custom Auth Test", "/test-custom-auth")
        self.test_system("User Profile", "/auth/me")
        
        # Core Business Systems
        print("\n💼 CORE BUSINESS SYSTEMS")
        print("-" * 30)
        self.test_system("Bio Sites", "/bio-sites/")
        self.test_system("Bio Site Themes", "/bio-sites/themes")
        self.test_system("Payment Packages", "/payments/packages", auth_required=False)
        self.test_system("Stripe Packages", "/stripe/packages", auth_required=False)
        
        # Advanced Systems
        print("\n🚀 ADVANCED SYSTEMS")
        print("-" * 30)
        self.test_system("Business Intelligence", "/analytics/business-intelligence")
        self.test_system("Real-time Metrics", "/analytics/realtime-metrics")
        self.test_system("Financial Dashboard", "/financial/dashboard")
        self.test_system("AI Services", "/ai/services")
        self.test_system("OAuth Providers", "/auth/oauth/providers", auth_required=False)
        
        # Real-time Features
        print("\n⚡ REAL-TIME FEATURES")
        print("-" * 30)
        self.test_system("Notifications", "/realtime/notifications")
        self.test_system("Activity Feed", "/realtime/activity-feed")
        self.test_system("System Status", "/realtime/system-status")
        
        # Website Builder
        print("\n🌐 WEBSITE BUILDER")
        print("-" * 30)
        self.test_system("Website Components", "/websites/components")
        self.test_system("Website Templates", "/websites/templates")
        
        # Biometric Auth
        print("\n🔒 BIOMETRIC AUTHENTICATION")
        print("-" * 30)
        self.test_system("Biometric Registration", "/biometric/registration-options", method='POST')
        
        # Generate Summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate comprehensive test summary"""
        print("\n" + "=" * 60)
        print("📊 PRODUCTION READINESS SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.results)
        working_tests = sum(1 for r in self.results.values() if r['working'])
        success_rate = (working_tests / total_tests) * 100 if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Working: {working_tests}")
        print(f"❌ Failing: {total_tests - working_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Categorize results
        working_systems = []
        failing_systems = []
        rate_limited = []
        timeouts = []
        
        for name, result in self.results.items():
            if result['working']:
                working_systems.append(name)
            elif result['status'] == 'RATE_LIMITED':
                rate_limited.append(name)
            elif result['status'] == 'TIMEOUT':
                timeouts.append(name)
            else:
                failing_systems.append(name)
        
        print(f"\n✅ WORKING SYSTEMS ({len(working_systems)}):")
        for system in working_systems:
            print(f"  - {system}")
        
        if rate_limited:
            print(f"\n⚠️ RATE LIMITED ({len(rate_limited)}):")
            for system in rate_limited:
                print(f"  - {system}")
        
        if timeouts:
            print(f"\n⏱️ TIMEOUTS ({len(timeouts)}):")
            for system in timeouts:
                print(f"  - {system}")
        
        if failing_systems:
            print(f"\n❌ FAILING SYSTEMS ({len(failing_systems)}):")
            for system in failing_systems:
                print(f"  - {system}")
        
        # Production readiness assessment
        if success_rate >= 90:
            readiness = "PRODUCTION READY ✅"
        elif success_rate >= 75:
            readiness = "MOSTLY READY ⚠️"
        elif success_rate >= 50:
            readiness = "PARTIALLY READY ❌"
        else:
            readiness = "NOT PRODUCTION READY ❌"
        
        print(f"\n🎯 ASSESSMENT: {readiness}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        return {
            'total_tests': total_tests,
            'working_tests': working_tests,
            'success_rate': success_rate,
            'working_systems': working_systems,
            'failing_systems': failing_systems,
            'rate_limited': rate_limited,
            'timeouts': timeouts,
            'readiness': readiness
        }

if __name__ == "__main__":
    tester = FocusedProductionTester()
    tester.run_comprehensive_test()