#!/usr/bin/env python3
"""
COMPREHENSIVE BACKEND TESTING - MEWAYZ PLATFORM V3.0.0
Final verification of ALL 6 PHASES (Phases 4-6) new features
Testing Agent - January 20, 2025
"""

import requests
import json
import time
from datetime import datetime

# Backend URL from frontend .env
BACKEND_URL = "https://c1377653-96a4-4862-8647-9ed933db2920.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"

# Admin credentials from review request
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

class BackendTester:
    def __init__(self):
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.total_tests = 0
        self.passed_tests = 0
        
    def log_test(self, endpoint, method, status, response_time, success, details="", data_size=0):
        """Log test results"""
        self.total_tests += 1
        if success:
            self.passed_tests += 1
            
        result = {
            'endpoint': endpoint,
            'method': method,
            'status': status,
            'response_time': f"{response_time:.3f}s",
            'success': success,
            'details': details,
            'data_size': data_size
        }
        self.test_results.append(result)
        
        status_icon = "✅" if success else "❌"
        print(f"{status_icon} {method} {endpoint} - {status} ({response_time:.3f}s) - {details}")
        
    def authenticate(self):
        """Authenticate with admin credentials"""
        print(f"\n🔐 AUTHENTICATING WITH ADMIN CREDENTIALS...")
        print(f"Email: {ADMIN_EMAIL}")
        
        login_data = {
            "email": ADMIN_EMAIL,
            "password": ADMIN_PASSWORD
        }
        
        try:
            start_time = time.time()
            response = self.session.post(f"{API_BASE}/auth/login", json=login_data, timeout=30)
            response_time = time.time() - start_time
            
            if response.status_code == 200:
                data = response.json()
                self.auth_token = data.get('token')
                if self.auth_token:
                    self.session.headers.update({'Authorization': f'Bearer {self.auth_token}'})
                    self.log_test("/auth/login", "POST", response.status_code, response_time, True, 
                                f"Admin authentication successful, token: {self.auth_token[:20]}...")
                    return True
                else:
                    self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                                "No access token in response")
                    return False
            else:
                self.log_test("/auth/login", "POST", response.status_code, response_time, False, 
                            f"Login failed: {response.text[:100]}")
                return False
                
        except Exception as e:
            self.log_test("/auth/login", "POST", 0, 0, False, f"Authentication error: {str(e)}")
            return False
    
    def test_endpoint(self, endpoint, method="GET", data=None, expected_status=200, description=""):
        """Test a single endpoint"""
        url = f"{API_BASE}{endpoint}"
        
        try:
            start_time = time.time()
            
            if method == "GET":
                response = self.session.get(url, timeout=30)
            elif method == "POST":
                response = self.session.post(url, json=data, timeout=30)
            elif method == "PUT":
                response = self.session.put(url, json=data, timeout=30)
            elif method == "DELETE":
                response = self.session.delete(url, timeout=30)
            else:
                raise ValueError(f"Unsupported method: {method}")
                
            response_time = time.time() - start_time
            
            # Check if response is successful
            success = response.status_code == expected_status
            
            # Get response details
            try:
                response_data = response.json()
                data_size = len(json.dumps(response_data))
                details = f"{description} - Response size: {data_size} chars"
                if not success:
                    details += f" - Error: {response_data.get('detail', 'Unknown error')}"
            except:
                data_size = len(response.text)
                details = f"{description} - Response size: {data_size} chars"
                if not success:
                    details += f" - Error: {response.text[:100]}"
            
            self.log_test(endpoint, method, response.status_code, response_time, success, details, data_size)
            return success, response
            
        except requests.exceptions.Timeout:
            self.log_test(endpoint, method, 0, 30.0, False, f"{description} - Request timeout")
            return False, None
        except Exception as e:
            self.log_test(endpoint, method, 0, 0, False, f"{description} - Error: {str(e)}")
            return False, None

    def test_phase_4_features(self):
        """Test Phase 4 Features - Missing Features Implementation"""
        print(f"\n🚀 TESTING PHASE 4 FEATURES - MISSING FEATURES IMPLEMENTATION")
        
        # Template marketplace and creation
        self.test_endpoint("/templates/marketplace", "GET", description="Advanced template marketplace")
        self.test_endpoint("/templates/create", "POST", 
                         data={"name": "Test Template", "category": "business", "content": "Template content"},
                         description="Template creation")
        
        # Certificate system
        self.test_endpoint("/courses/test-course-id/certificates", "GET", description="Certificate system")
        
        # Vendor dashboard
        self.test_endpoint("/ecommerce/vendors/dashboard", "GET", description="Vendor dashboard")
        
        # Product comparison
        self.test_endpoint("/ecommerce/products/compare", "POST",
                         data={"product_ids": ["prod1", "prod2"]},
                         description="Product comparison")
        
        # Custom report builder
        self.test_endpoint("/analytics/custom-reports", "GET", description="Custom report builder")
        
        # Webhook configurations
        self.test_endpoint("/integrations/webhooks", "GET", description="Webhook configurations")
        
        # White-label customization
        self.test_endpoint("/admin/white-label/settings", "GET", description="White-label customization")
        
        # Multi-currency support
        self.test_endpoint("/financial/multi-currency", "GET", description="Multi-currency support")

    def test_phase_5_features(self):
        """Test Phase 5 Features - Valuable Expansions"""
        print(f"\n🚀 TESTING PHASE 5 FEATURES - VALUABLE EXPANSIONS")
        
        # Automation workflows
        self.test_endpoint("/automation/workflows", "GET", description="Automation workflows")
        self.test_endpoint("/automation/workflows/create", "POST",
                         data={"name": "Test Workflow", "triggers": ["email_signup"], "actions": ["send_welcome"]},
                         description="Workflow creation")
        
        # Advanced social analytics
        self.test_endpoint("/social/analytics/comprehensive", "GET", description="Advanced social analytics")
        
        # Competitor tracking
        self.test_endpoint("/social/competitors/track", "POST",
                         data={"competitor_url": "https://example.com", "platforms": ["instagram", "twitter"]},
                         description="Competitor tracking")
        
        # Smart AI notifications
        self.test_endpoint("/notifications/smart", "GET", description="Smart AI notifications")
        
        # Affiliate program
        self.test_endpoint("/affiliate/program/overview", "GET", description="Affiliate program")
        
        # Global search system
        self.test_endpoint("/search/global", "GET", description="Global search system")
        
        # Bulk operations
        self.test_endpoint("/bulk/contacts/import", "POST",
                         data={"contacts": [{"name": "Test Contact", "email": "test@example.com"}]},
                         description="Bulk operations")

    def test_phase_6_features(self):
        """Test Phase 6 Features - Additional Valuable Features"""
        print(f"\n🚀 TESTING PHASE 6 FEATURES - ADDITIONAL VALUABLE FEATURES")
        
        # AI business intelligence
        self.test_endpoint("/ai/business-insights", "GET", description="AI business intelligence")
        
        # Smart recommendations
        self.test_endpoint("/recommendations/smart", "GET", description="Smart recommendations")
        
        # Performance optimization
        self.test_endpoint("/performance/optimization-center", "GET", description="Performance optimization")
        
        # Market intelligence
        self.test_endpoint("/trends/market-intelligence", "GET", description="Market intelligence")
        
        # Customer journey analytics
        self.test_endpoint("/analytics/customer-journey", "GET", description="Customer journey analytics")
        
        # Predictive analytics
        self.test_endpoint("/analytics/predictive", "GET", description="Predictive analytics")
        
        # Team productivity
        self.test_endpoint("/team/productivity-insights", "GET", description="Team productivity")

    def test_core_system_health(self):
        """Test Core System Health"""
        print(f"\n🏥 TESTING CORE SYSTEM HEALTH")
        
        # System health check
        self.test_endpoint("/health", "GET", description="System health check")
        
        # Multi-workspace system
        self.test_endpoint("/workspaces", "GET", description="Multi-workspace system")

    def run_comprehensive_test(self):
        """Run comprehensive testing of all phases"""
        print(f"🎯 COMPREHENSIVE BACKEND TESTING - MEWAYZ PLATFORM V3.0.0")
        print(f"Testing ALL 6 PHASES (Phases 4-6) New Features")
        print(f"Backend URL: {BACKEND_URL}")
        print(f"Started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Step 1: Authenticate
        if not self.authenticate():
            print("❌ AUTHENTICATION FAILED - Cannot proceed with testing")
            return
        
        # Step 2: Test core system health
        self.test_core_system_health()
        
        # Step 3: Test Phase 4 features
        self.test_phase_4_features()
        
        # Step 4: Test Phase 5 features  
        self.test_phase_5_features()
        
        # Step 5: Test Phase 6 features
        self.test_phase_6_features()
        
        # Generate final report
        self.generate_final_report()

    def generate_final_report(self):
        """Generate comprehensive final report"""
        print(f"\n" + "="*80)
        print(f"📊 FINAL COMPREHENSIVE TESTING REPORT")
        print(f"="*80)
        
        success_rate = (self.passed_tests / self.total_tests * 100) if self.total_tests > 0 else 0
        
        print(f"🎯 OVERALL RESULTS:")
        print(f"   Total Tests: {self.total_tests}")
        print(f"   Passed: {self.passed_tests}")
        print(f"   Failed: {self.total_tests - self.passed_tests}")
        print(f"   Success Rate: {success_rate:.1f}%")
        
        print(f"\n📋 DETAILED TEST RESULTS:")
        
        # Group results by phase
        phase_4_tests = [r for r in self.test_results if any(endpoint in r['endpoint'] for endpoint in 
                        ['/templates/', '/courses/', '/ecommerce/vendors/', '/ecommerce/products/compare', 
                         '/analytics/custom-reports', '/integrations/webhooks', '/admin/white-label/', '/financial/multi-currency'])]
        
        phase_5_tests = [r for r in self.test_results if any(endpoint in r['endpoint'] for endpoint in 
                        ['/automation/', '/social/analytics/comprehensive', '/social/competitors/', '/notifications/smart',
                         '/affiliate/', '/search/global', '/bulk/'])]
        
        phase_6_tests = [r for r in self.test_results if any(endpoint in r['endpoint'] for endpoint in 
                        ['/ai/business-insights', '/recommendations/smart', '/performance/optimization-center',
                         '/trends/market-intelligence', '/analytics/customer-journey', '/analytics/predictive', '/team/productivity-insights'])]
        
        core_tests = [r for r in self.test_results if r['endpoint'] in ['/auth/login', '/health', '/workspaces']]
        
        def print_phase_results(phase_name, tests):
            if tests:
                passed = sum(1 for t in tests if t['success'])
                total = len(tests)
                rate = (passed / total * 100) if total > 0 else 0
                print(f"\n   {phase_name}: {passed}/{total} ({rate:.1f}%)")
                for test in tests:
                    status_icon = "✅" if test['success'] else "❌"
                    print(f"     {status_icon} {test['method']} {test['endpoint']} - {test['status']} ({test['response_time']})")
        
        print_phase_results("🔐 CORE SYSTEM", core_tests)
        print_phase_results("🚀 PHASE 4 FEATURES", phase_4_tests)
        print_phase_results("🚀 PHASE 5 FEATURES", phase_5_tests)
        print_phase_results("🚀 PHASE 6 FEATURES", phase_6_tests)
        
        # Performance metrics
        successful_tests = [r for r in self.test_results if r['success']]
        if successful_tests:
            avg_response_time = sum(float(r['response_time'].replace('s', '')) for r in successful_tests) / len(successful_tests)
            total_data = sum(r['data_size'] for r in successful_tests)
            print(f"\n📈 PERFORMANCE METRICS:")
            print(f"   Average Response Time: {avg_response_time:.3f}s")
            print(f"   Total Data Transferred: {total_data:,} bytes")
        
        # Final assessment
        print(f"\n🎯 FINAL ASSESSMENT:")
        if success_rate >= 90:
            print(f"   ✅ EXCELLENT - All 6 phases are production-ready!")
        elif success_rate >= 75:
            print(f"   ⚠️  GOOD - Most features working, minor issues to address")
        elif success_rate >= 50:
            print(f"   ⚠️  MODERATE - Significant issues need attention")
        else:
            print(f"   ❌ CRITICAL - Major system issues require immediate attention")
        
        print(f"\nCompleted at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"="*80)

if __name__ == "__main__":
    tester = BackendTester()
    tester.run_comprehensive_test()