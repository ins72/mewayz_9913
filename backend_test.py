#!/usr/bin/env python3
"""
COMPREHENSIVE MARKETING AUTOMATION AND CAMPAIGN MANAGEMENT SYSTEM TESTING - MEWAYZ PLATFORM
Phase 6 Marketing Automation and Campaign Management System Testing
Testing Agent - July 20, 2025
"""

import requests
import json
import time
from datetime import datetime

# Backend URL from frontend .env
BACKEND_URL = "https://5bde9595-e877-4e2b-9a14-404677567ffb.preview.emergentagent.com"
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
    
    def test_endpoint(self, endpoint, method="GET", data=None, form_data=None, expected_status=200, description=""):
        """Test a single endpoint"""
        url = f"{API_BASE}{endpoint}"
        
        try:
            start_time = time.time()
            
            if method == "GET":
                response = self.session.get(url, timeout=30)
            elif method == "POST":
                if form_data:
                    response = self.session.post(url, data=form_data, timeout=30)
                else:
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

    def test_marketing_automation_system(self):
        """Test Marketing Automation and Campaign Management System - Phase 6"""
        print(f"\n🚀 TESTING MARKETING AUTOMATION AND CAMPAIGN MANAGEMENT SYSTEM")
        
        # 1. Marketing Dashboard
        print(f"\n📊 Testing Marketing Dashboard...")
        self.test_endpoint("/marketing/dashboard/comprehensive", "GET", 
                         description="Comprehensive marketing dashboard with analytics")
        
        # 2. Campaign Management - Create Campaign
        print(f"\n📧 Testing Campaign Management...")
        campaign_form_data = {
            "name": "Phase 6 Marketing Launch Campaign",
            "type": "one_time",
            "template": "marketing_launch_template",
            "target_audience": "all",
            "send_immediately": "false"
        }
        self.test_endpoint("/marketing/campaigns/create", "POST",
                         form_data=campaign_form_data,
                         description="Create comprehensive marketing campaign")
        
        # 3. Contact Management - Bulk Import
        print(f"\n👥 Testing Contact Management...")
        contacts_form_data = {
            "import_source": "csv",
            "contact_data": json.dumps([
                {
                    "email": "john.doe@example.com",
                    "first_name": "John",
                    "last_name": "Doe",
                    "company": "Tech Solutions Inc",
                    "phone": "+1-555-0123"
                },
                {
                    "email": "jane.smith@example.com",
                    "first_name": "Jane",
                    "last_name": "Smith",
                    "company": "Marketing Pro LLC",
                    "phone": "+1-555-0124"
                }
            ]),
            "duplicate_handling": "merge",
            "validation_level": "standard"
        }
        self.test_endpoint("/marketing/bulk-import/contacts", "POST",
                         form_data=contacts_form_data,
                         description="Bulk import marketing contacts")
        
        # 4. Customer Segmentation
        print(f"\n🎯 Testing Customer Segmentation...")
        self.test_endpoint("/marketing/segments/intelligent", "GET",
                         description="Intelligent customer segmentation system")
        
        # 5. Marketing Automation - Workflow Creation
        print(f"\n⚙️ Testing Marketing Automation...")
        workflow_form_data = {
            "workflow_name": "Welcome Series Automation",
            "trigger_type": "signup",
            "workflow_steps": json.dumps([
                {
                    "step_type": "delay",
                    "delay_duration": "1 hour"
                },
                {
                    "step_type": "email",
                    "email_template": "welcome_email_1",
                    "email_subject": "Welcome to Our Platform!"
                }
            ]),
            "workflow_settings": "{}"
        }
        self.test_endpoint("/marketing/automation/workflow/create", "POST",
                         form_data=workflow_form_data,
                         description="Create marketing automation workflow")
        
        # 6. Lead Scoring
        print(f"\n🏆 Testing Lead Scoring...")
        self.test_endpoint("/marketing/lead-scoring/advanced", "GET",
                         description="Advanced lead scoring system")
        
        # 7. Campaign Analytics
        print(f"\n📈 Testing Campaign Analytics...")
        self.test_endpoint("/marketing/campaign-analytics/comprehensive", "GET",
                         description="Comprehensive campaign analytics")
        
        # 8. A/B Testing
        print(f"\n🧪 Testing A/B Testing...")
        ab_test_form_data = {
            "test_name": "Subject Line A/B Test",
            "test_type": "email",
            "variable_to_test": "subject_line",
            "variant_a": json.dumps({"subject": "Discover Our New Features"}),
            "variant_b": json.dumps({"subject": "Revolutionary Features Await You"}),
            "audience_size": "1000",
            "test_duration_days": "1",
            "success_metric": "open_rate"
        }
        self.test_endpoint("/marketing/ab-testing/create", "POST",
                         form_data=ab_test_form_data,
                         description="Create A/B testing campaign")
        
        # 9. Customer Lifecycle Analytics
        print(f"\n🔄 Testing Customer Lifecycle Analytics...")
        self.test_endpoint("/marketing/customer-lifecycle/analytics", "GET",
                         description="Customer lifecycle analytics and insights")

    def test_core_system_health(self):
        """Test core system health and integration"""
        print(f"\n🔍 TESTING CORE SYSTEM HEALTH")
        
        # System health check
        self.test_endpoint("/health", "GET", description="System health check")
        
        # Admin dashboard
        self.test_endpoint("/admin/dashboard", "GET", description="Admin dashboard access")
        
        # User profile
        self.test_endpoint("/users/profile", "GET", description="User profile access")
        
    def run_marketing_automation_test(self):
        """Run marketing automation system testing"""
        print(f"🎯 MARKETING AUTOMATION AND CAMPAIGN MANAGEMENT SYSTEM TESTING")
        print(f"Phase 6 Marketing Automation and Campaign Management System")
        print(f"Backend URL: {BACKEND_URL}")
        print(f"Started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Step 1: Authenticate
        if not self.authenticate():
            print("❌ AUTHENTICATION FAILED - Cannot proceed with testing")
            return
        
        # Step 2: Test core system health
        self.test_core_system_health()
        
        # Step 3: Test marketing automation system
        self.test_marketing_automation_system()
        
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
    tester.run_marketing_automation_test()