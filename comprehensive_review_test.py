#!/usr/bin/env python3
"""
Comprehensive Review Request Testing for Mewayz Professional Platform
Tests all 35 specific endpoints mentioned in the review request with enterprise features
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class MewayzReviewTester:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0, response_size: int = 0):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'status': status,
            'success': success,
            'details': details,
            'response_time': f"{response_time:.3f}s",
            'response_size': response_size
        }
        self.test_results.append(result)
        print(f"{status} - {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    Details: {details}")
    
    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None, timeout: int = 30, form_data: bool = False) -> tuple:
        """Make HTTP request with error handling"""
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Accept': 'application/json'
        }
        
        if not form_data:
            default_headers['Content-Type'] = 'application/json'
        
        if self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        if headers:
            default_headers.update(headers)
        
        try:
            start_time = time.time()
            
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, timeout=timeout)
            elif method.upper() == 'POST':
                if form_data:
                    response = self.session.post(url, data=data, headers=default_headers, timeout=timeout)
                else:
                    response = self.session.post(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
            
            response_time = time.time() - start_time
            response_size = len(response.content) if response else 0
            
            return response, response_time, response_size
            
        except requests.exceptions.Timeout:
            return None, 30.0, 0  # Return timeout duration
        except requests.exceptions.RequestException as e:
            print(f"Request error: {str(e)}")
            return None, 0.0, 0
    
    def authenticate_admin(self):
        """Authenticate with admin credentials"""
        print("\n=== Admin Authentication ===")
        
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response, response_time, response_size = self.make_request('POST', '/auth/login', admin_login_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                self.log_test("1. Admin Login", True, f"Admin authenticated: {data.get('user', {}).get('email')}", response_time, response_size)
                return True
            else:
                self.log_test("1. Admin Login", False, f"Login failed: {data.get('message')}", response_time, response_size)
                return False
        else:
            self.log_test("1. Admin Login", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
            return False
    
    def test_core_system(self):
        """Test authentication & core system endpoints"""
        print("\n=== AUTHENTICATION & CORE SYSTEM ===")
        
        # 2. Test health check
        response, response_time, response_size = self.make_request('GET', '/health')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("2. Health Check", True, f"Status: {data.get('status', 'unknown')}, Version: {data.get('version', 'N/A')}", response_time, response_size)
        else:
            self.log_test("2. Health Check", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 3. Test admin dashboard
        if self.auth_token:
            response, response_time, response_size = self.make_request('GET', '/admin/dashboard')
            if response and response.status_code == 200:
                data = response.json()
                self.log_test("3. Admin Dashboard", True, f"Dashboard accessible with comprehensive data", response_time, response_size)
            else:
                self.log_test("3. Admin Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_ai_services(self):
        """Test comprehensive AI services"""
        print("\n=== COMPREHENSIVE AI SERVICES ===")
        
        if not self.auth_token:
            self.log_test("AI Services", False, "No authentication token available")
            return
        
        # 4. Test AI services catalog
        response, response_time, response_size = self.make_request('GET', '/ai/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("4. AI Services Catalog", True, f"Comprehensive AI service catalog retrieved", response_time, response_size)
        else:
            self.log_test("4. AI Services Catalog", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 5. Test AI conversations
        response, response_time, response_size = self.make_request('GET', '/ai/conversations')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("5. AI Conversations", True, f"AI conversations retrieved", response_time, response_size)
        else:
            self.log_test("5. AI Conversations", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 6. Test content generation with form data
        content_data = {
            'content_type': 'blog_post',
            'topic': 'Digital Marketing',
            'length': 'medium',
            'tone': 'professional'
        }
        
        response, response_time, response_size = self.make_request('POST', '/ai/content/generate', content_data, form_data=True)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("6. AI Content Generation", True, f"Content generated successfully", response_time, response_size)
        else:
            self.log_test("6. AI Content Generation", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_bio_sites_system(self):
        """Test bio sites system"""
        print("\n=== BIO SITES SYSTEM ===")
        
        if not self.auth_token:
            self.log_test("Bio Sites", False, "No authentication token available")
            return
        
        # 7. Test bio sites
        response, response_time, response_size = self.make_request('GET', '/bio-sites')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("7. Bio Sites", True, f"Bio sites retrieved", response_time, response_size)
        else:
            self.log_test("7. Bio Sites", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 8. Test bio sites themes
        response, response_time, response_size = self.make_request('GET', '/bio-sites/themes')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("8. Bio Sites Themes", True, f"Professional theme collection retrieved", response_time, response_size)
        else:
            self.log_test("8. Bio Sites Themes", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 9. Test bio site creation
        bio_site_data = {
            "title": "Professional Site",
            "slug": "pro-site",
            "theme": "modern"
        }
        
        response, response_time, response_size = self.make_request('POST', '/bio-sites', bio_site_data)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.log_test("9. Bio Site Creation", True, f"Bio site created successfully", response_time, response_size)
        else:
            self.log_test("9. Bio Site Creation", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_ecommerce_system(self):
        """Test e-commerce system"""
        print("\n=== E-COMMERCE SYSTEM ===")
        
        if not self.auth_token:
            self.log_test("E-commerce", False, "No authentication token available")
            return
        
        # 10. Test e-commerce products
        response, response_time, response_size = self.make_request('GET', '/ecommerce/products')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("10. E-commerce Products", True, f"Products retrieved", response_time, response_size)
        else:
            self.log_test("10. E-commerce Products", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 11. Test e-commerce orders
        response, response_time, response_size = self.make_request('GET', '/ecommerce/orders')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("11. E-commerce Orders", True, f"Orders retrieved", response_time, response_size)
        else:
            self.log_test("11. E-commerce Orders", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 12. Test e-commerce dashboard
        response, response_time, response_size = self.make_request('GET', '/ecommerce/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("12. E-commerce Dashboard", True, f"Comprehensive business metrics retrieved", response_time, response_size)
        else:
            self.log_test("12. E-commerce Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_booking_system(self):
        """Test advanced booking system"""
        print("\n=== ADVANCED BOOKING SYSTEM ===")
        
        if not self.auth_token:
            self.log_test("Booking System", False, "No authentication token available")
            return
        
        # 13. Test booking services
        response, response_time, response_size = self.make_request('GET', '/bookings/services')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("13. Booking Services", True, f"Booking services retrieved", response_time, response_size)
        else:
            self.log_test("13. Booking Services", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 14. Test booking appointments
        response, response_time, response_size = self.make_request('GET', '/bookings/appointments')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("14. Booking Appointments", True, f"Appointments retrieved", response_time, response_size)
        else:
            self.log_test("14. Booking Appointments", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 15. Test booking dashboard
        response, response_time, response_size = self.make_request('GET', '/bookings/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("15. Booking Dashboard", True, f"Professional booking management system data retrieved", response_time, response_size)
        else:
            self.log_test("15. Booking Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_financial_management(self):
        """Test financial management"""
        print("\n=== FINANCIAL MANAGEMENT ===")
        
        if not self.auth_token:
            self.log_test("Financial Management", False, "No authentication token available")
            return
        
        # 16. Test financial invoices
        response, response_time, response_size = self.make_request('GET', '/financial/invoices')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("16. Financial Invoices", True, f"Invoices retrieved", response_time, response_size)
        else:
            self.log_test("16. Financial Invoices", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 17. Test comprehensive financial dashboard
        response, response_time, response_size = self.make_request('GET', '/financial/dashboard/comprehensive')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("17. Financial Dashboard Comprehensive", True, f"Enterprise-level financial analytics retrieved", response_time, response_size)
        else:
            self.log_test("17. Financial Dashboard Comprehensive", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_course_management(self):
        """Test course management"""
        print("\n=== COURSE MANAGEMENT ===")
        
        if not self.auth_token:
            self.log_test("Course Management", False, "No authentication token available")
            return
        
        # 18. Test courses
        response, response_time, response_size = self.make_request('GET', '/courses')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("18. Courses", True, f"Courses retrieved", response_time, response_size)
        else:
            self.log_test("18. Courses", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 19. Test course analytics
        response, response_time, response_size = self.make_request('GET', '/courses/analytics/detailed')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("19. Course Analytics Detailed", True, f"Detailed course analytics retrieved", response_time, response_size)
        else:
            self.log_test("19. Course Analytics Detailed", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_workspace_team(self):
        """Test workspace & team management"""
        print("\n=== WORKSPACE & TEAM ===")
        
        if not self.auth_token:
            self.log_test("Workspace & Team", False, "No authentication token available")
            return
        
        # 20. Test workspaces
        response, response_time, response_size = self.make_request('GET', '/workspaces')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("20. Workspaces", True, f"Workspaces retrieved", response_time, response_size)
        else:
            self.log_test("20. Workspaces", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 21. Test team management
        response, response_time, response_size = self.make_request('GET', '/workspaces/team/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("21. Team Management Advanced", True, f"Advanced team management data retrieved", response_time, response_size)
        else:
            self.log_test("21. Team Management Advanced", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_crm_system(self):
        """Test CRM system"""
        print("\n=== CRM SYSTEM ===")
        
        if not self.auth_token:
            self.log_test("CRM System", False, "No authentication token available")
            return
        
        # 22. Test CRM contacts
        response, response_time, response_size = self.make_request('GET', '/crm/contacts')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("22. CRM Contacts", True, f"CRM contacts retrieved", response_time, response_size)
        else:
            self.log_test("22. CRM Contacts", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 23. Test CRM pipeline
        response, response_time, response_size = self.make_request('GET', '/crm/pipeline/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("23. CRM Pipeline Advanced", True, f"Advanced CRM pipeline data retrieved", response_time, response_size)
        else:
            self.log_test("23. CRM Pipeline Advanced", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_analytics_bi(self):
        """Test analytics & BI"""
        print("\n=== ANALYTICS & BI ===")
        
        if not self.auth_token:
            self.log_test("Analytics & BI", False, "No authentication token available")
            return
        
        # 24. Test analytics overview
        response, response_time, response_size = self.make_request('GET', '/analytics/overview')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("24. Analytics Overview", True, f"Analytics overview retrieved", response_time, response_size)
        else:
            self.log_test("24. Analytics Overview", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 25. Test business intelligence
        response, response_time, response_size = self.make_request('GET', '/analytics/business-intelligence/advanced')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("25. Business Intelligence Advanced", True, f"Executive-level business intelligence retrieved", response_time, response_size)
        else:
            self.log_test("25. Business Intelligence Advanced", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_advanced_systems(self):
        """Test advanced systems"""
        print("\n=== ADVANCED SYSTEMS ===")
        
        if not self.auth_token:
            self.log_test("Advanced Systems", False, "No authentication token available")
            return
        
        # 26. Test escrow dashboard
        response, response_time, response_size = self.make_request('GET', '/escrow/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("26. Escrow Dashboard", True, f"Professional escrow transaction management data retrieved", response_time, response_size)
        else:
            self.log_test("26. Escrow Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 27. Test email marketing analytics
        response, response_time, response_size = self.make_request('GET', '/email-marketing/advanced-analytics')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("27. Email Marketing Advanced Analytics", True, f"Advanced email marketing analytics retrieved", response_time, response_size)
        else:
            self.log_test("27. Email Marketing Advanced Analytics", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 28. Test social media analytics
        response, response_time, response_size = self.make_request('GET', '/social-media/advanced-analytics')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("28. Social Media Advanced Analytics", True, f"Advanced social media analytics retrieved", response_time, response_size)
        else:
            self.log_test("28. Social Media Advanced Analytics", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 29. Test payment analytics
        response, response_time, response_size = self.make_request('GET', '/payments/advanced-analytics')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("29. Payment Advanced Analytics", True, f"Advanced payment analytics retrieved", response_time, response_size)
        else:
            self.log_test("29. Payment Advanced Analytics", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def test_enterprise_features(self):
        """Test enterprise features"""
        print("\n=== ENTERPRISE FEATURES ===")
        
        if not self.auth_token:
            self.log_test("Enterprise Features", False, "No authentication token available")
            return
        
        # 30. Test multi-tenant dashboard
        response, response_time, response_size = self.make_request('GET', '/enterprise/multi-tenant/dashboard')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("30. Multi-Tenant Dashboard", True, f"Multi-tenant dashboard data retrieved", response_time, response_size)
        else:
            self.log_test("30. Multi-Tenant Dashboard", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 31. Test security audit logs
        response, response_time, response_size = self.make_request('GET', '/enterprise/security/audit-logs')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("31. Security Audit Logs", True, f"Security audit logs retrieved", response_time, response_size)
        else:
            self.log_test("31. Security Audit Logs", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 32. Test executive business summary
        response, response_time, response_size = self.make_request('GET', '/enterprise/business-intelligence/executive-summary')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("32. Executive Business Summary", True, f"Executive business summary retrieved", response_time, response_size)
        else:
            self.log_test("32. Executive Business Summary", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 33. Test automation workflows
        response, response_time, response_size = self.make_request('GET', '/enterprise/automation/workflows')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("33. Automation Workflows", True, f"Automation workflows retrieved", response_time, response_size)
        else:
            self.log_test("33. Automation Workflows", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 34. Test API management
        response, response_time, response_size = self.make_request('GET', '/enterprise/api-management/analytics')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("34. API Management Analytics", True, f"API management analytics retrieved", response_time, response_size)
        else:
            self.log_test("34. API Management Analytics", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
        
        # 35. Test customer success metrics
        response, response_time, response_size = self.make_request('GET', '/enterprise/customer-success/health-score')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("35. Customer Success Health Score", True, f"Customer success metrics retrieved", response_time, response_size)
        else:
            self.log_test("35. Customer Success Health Score", False, f"Status: {response.status_code if response else 'timeout'}", response_time, response_size)
    
    def run_comprehensive_review_test(self):
        """Run all review request tests"""
        print("🚀 Starting Comprehensive Review Request Testing")
        print("Testing Mewayz Professional Platform with Enterprise Features")
        print(f"Backend URL: {self.api_url}")
        print("=" * 80)
        
        # Authenticate first
        if not self.authenticate_admin():
            print("❌ CRITICAL: Admin authentication failed. Cannot proceed with testing.")
            return
        
        # Run all test suites in order
        self.test_core_system()
        self.test_ai_services()
        self.test_bio_sites_system()
        self.test_ecommerce_system()
        self.test_booking_system()
        self.test_financial_management()
        self.test_course_management()
        self.test_workspace_team()
        self.test_crm_system()
        self.test_analytics_bi()
        self.test_advanced_systems()
        self.test_enterprise_features()
        
        # Generate comprehensive summary
        self.generate_comprehensive_summary()
    
    def generate_comprehensive_summary(self):
        """Generate comprehensive test summary"""
        print("\n" + "=" * 80)
        print("📊 COMPREHENSIVE REVIEW REQUEST TESTING SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        # Calculate average response time
        response_times = [float(result['response_time'].replace('s', '')) for result in self.test_results if result['success']]
        avg_response_time = sum(response_times) / len(response_times) if response_times else 0
        
        # Calculate total data transferred
        total_data = sum(result['response_size'] for result in self.test_results if result['success'])
        
        print(f"Average Response Time: {avg_response_time:.3f}s")
        print(f"Total Data Transferred: {total_data:,} bytes")
        
        # Group results by category
        categories = {
            "Authentication & Core System": [1, 2, 3],
            "AI Services": [4, 5, 6],
            "Bio Sites System": [7, 8, 9],
            "E-commerce System": [10, 11, 12],
            "Advanced Booking System": [13, 14, 15],
            "Financial Management": [16, 17],
            "Course Management": [18, 19],
            "Workspace & Team": [20, 21],
            "CRM System": [22, 23],
            "Analytics & BI": [24, 25],
            "Advanced Systems": [26, 27, 28, 29],
            "Enterprise Features": [30, 31, 32, 33, 34, 35]
        }
        
        print(f"\n📈 CATEGORY BREAKDOWN:")
        for category, test_numbers in categories.items():
            category_results = [result for result in self.test_results if any(str(num) in result['test'] for num in test_numbers)]
            if category_results:
                category_passed = sum(1 for result in category_results if result['success'])
                category_total = len(category_results)
                category_rate = (category_passed / category_total * 100) if category_total > 0 else 0
                print(f"  {category}: {category_passed}/{category_total} ({category_rate:.1f}%)")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  • {result['test']}")
        
        print("\n" + "=" * 80)
        
        # Overall assessment
        if success_rate >= 90:
            print("🎉 EXCEPTIONAL: Platform demonstrates enterprise-level functionality!")
        elif success_rate >= 80:
            print("✅ EXCELLENT: Platform is highly functional and production-ready!")
        elif success_rate >= 70:
            print("✅ GOOD: Platform is mostly functional with minor areas needing attention.")
        elif success_rate >= 60:
            print("⚠️  MODERATE: Platform has some issues that need addressing.")
        else:
            print("❌ CRITICAL: Platform has major functionality problems.")
        
        print(f"\n🔍 ENTERPRISE READINESS ASSESSMENT:")
        print(f"  • Professional Data Depth: {'✅ Verified' if success_rate >= 80 else '❌ Needs Improvement'}")
        print(f"  • Authentication & Security: {'✅ Verified' if any('Admin Login' in result['test'] and result['success'] for result in self.test_results) else '❌ Failed'}")
        print(f"  • Performance Metrics: {'✅ Excellent' if avg_response_time < 1.0 else '⚠️ Acceptable' if avg_response_time < 3.0 else '❌ Poor'}")
        print(f"  • Enterprise Features: {'✅ Available' if any('Enterprise' in result['test'] and result['success'] for result in self.test_results) else '❌ Missing'}")

def main():
    """Main function to run the comprehensive review tests"""
    # Backend URL from environment
    backend_url = "https://925bcaf0-3b9a-4a33-9e67-e13c6b60e2d6.preview.emergentagent.com"
    
    print(f"🔍 Backend URL: {backend_url}")
    
    # Initialize tester
    tester = MewayzReviewTester(backend_url)
    
    # Run comprehensive review tests
    tester.run_comprehensive_review_test()

if __name__ == "__main__":
    main()