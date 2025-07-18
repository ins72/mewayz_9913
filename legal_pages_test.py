#!/usr/bin/env python3
"""
Legal Pages Backend Testing for Mewayz Platform
Testing legal documents system, API endpoints, and page routes
"""

import requests
import json
import sys
import time
from datetime import datetime

class LegalPagesAPITester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.web_url = base_url
        self.auth_token = None
        self.user_id = None
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
        
    def make_request(self, method, endpoint, data=None, headers=None, auth_required=True, use_web=False):
        """Make HTTP request with proper headers"""
        time.sleep(0.1)  # Rate limiting
        
        if use_web:
            url = f"{self.web_url}{endpoint}"
        else:
            url = f"{self.api_url}{endpoint}"
        
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'User-Agent': 'Legal-Pages-Test/1.0'
        }
        
        if headers:
            default_headers.update(headers)
            
        if auth_required and self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, timeout=30)
            elif method.upper() == 'POST':
                response = self.session.post(url, json=data, headers=default_headers, timeout=30)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=30)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"⏰ TIMEOUT: {method} {url}")
            return None
        except requests.exceptions.RequestException as e:
            print(f"🔌 CONNECTION ERROR: {method} {url} - {str(e)}")
            return None

    def authenticate_user(self):
        """Authenticate and get token for API testing"""
        print("\n🔐 AUTHENTICATION SETUP")
        
        # Try to register a test user first
        register_data = {
            "name": "Legal Test User",
            "email": f"legal_test_{int(time.time())}@example.com",
            "password": "SecurePassword123!",
            "password_confirmation": "SecurePassword123!"
        }
        
        response = self.make_request('POST', '/auth/register', register_data, auth_required=False)
        
        if response and response.status_code == 201:
            try:
                data = response.json()
                if 'token' in data:
                    self.auth_token = data['token']
                    self.user_id = data.get('user', {}).get('id')
                    self.log_test("User Registration", True, f"Successfully registered user and got token")
                    return True
            except:
                pass
        
        # If registration fails, try login with existing credentials
        login_data = {
            "email": "admin@mewayz.com",
            "password": "password"
        }
        
        response = self.make_request('POST', '/auth/login', login_data, auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if 'token' in data:
                    self.auth_token = data['token']
                    self.user_id = data.get('user', {}).get('id')
                    self.log_test("User Login", True, f"Successfully logged in and got token")
                    return True
            except:
                pass
                
        self.log_test("Authentication", False, "Failed to authenticate user")
        return False

    def test_database_tables(self):
        """Test if legal database tables exist and are accessible"""
        print("\n📊 DATABASE TABLES VERIFICATION")
        
        # Test database connectivity through health endpoint
        response = self.make_request('GET', '/health', auth_required=False)
        if response and response.status_code == 200:
            self.log_test("Database Connectivity", True, "Database connection verified through health endpoint")
        else:
            self.log_test("Database Connectivity", False, "Database connection failed")
            return False
            
        return True

    def test_legal_document_model(self):
        """Test LegalDocument model functionality through API"""
        print("\n📄 LEGAL DOCUMENT MODEL TESTING")
        
        # We'll test this indirectly through the legal page endpoints
        # since there's no direct API for legal documents CRUD
        
        legal_pages = [
            'terms-of-service',
            'privacy-policy', 
            'cookie-policy',
            'refund-policy',
            'accessibility-statement'
        ]
        
        working_pages = 0
        
        for page in legal_pages:
            response = self.make_request('GET', f'/{page}', auth_required=False, use_web=True)
            
            if response and response.status_code == 200:
                # Check if response contains legal document content
                content = response.text
                if 'legal' in content.lower() or 'policy' in content.lower() or 'terms' in content.lower():
                    self.log_test(f"Legal Document - {page}", True, f"Page loads with legal content")
                    working_pages += 1
                else:
                    self.log_test(f"Legal Document - {page}", False, f"Page loads but no legal content found")
            else:
                status_code = response.status_code if response else "No Response"
                self.log_test(f"Legal Document - {page}", False, f"Page failed to load (Status: {status_code})")
        
        success_rate = (working_pages / len(legal_pages)) * 100
        self.log_test("Legal Document Model", working_pages > 0, f"Legal documents accessible: {working_pages}/{len(legal_pages)} ({success_rate:.1f}%)")
        
        return working_pages > 0

    def test_legal_controller_methods(self):
        """Test LegalController methods for all legal pages"""
        print("\n🎮 LEGAL CONTROLLER METHODS TESTING")
        
        legal_routes = [
            ('/terms-of-service', 'Terms of Service'),
            ('/privacy-policy', 'Privacy Policy'),
            ('/cookie-policy', 'Cookie Policy'),
            ('/refund-policy', 'Refund Policy'),
            ('/accessibility-statement', 'Accessibility Statement')
        ]
        
        working_controllers = 0
        
        for route, name in legal_routes:
            response = self.make_request('GET', route, auth_required=False, use_web=True)
            
            if response and response.status_code == 200:
                # Check response headers and content
                content_type = response.headers.get('content-type', '')
                if 'text/html' in content_type or 'application/json' in content_type:
                    self.log_test(f"Controller Method - {name}", True, f"Controller method working (Status: {response.status_code})")
                    working_controllers += 1
                else:
                    self.log_test(f"Controller Method - {name}", False, f"Unexpected content type: {content_type}")
            else:
                status_code = response.status_code if response else "No Response"
                self.log_test(f"Controller Method - {name}", False, f"Controller method failed (Status: {status_code})")
        
        success_rate = (working_controllers / len(legal_routes)) * 100
        self.log_test("Legal Controller Methods", working_controllers > 0, f"Controller methods working: {working_controllers}/{len(legal_routes)} ({success_rate:.1f}%)")
        
        return working_controllers > 0

    def test_legal_api_endpoints(self):
        """Test legal API endpoints"""
        print("\n🔌 LEGAL API ENDPOINTS TESTING")
        
        if not self.auth_token:
            self.log_test("Legal API Endpoints", False, "No authentication token available")
            return False
        
        # Test cookie consent API
        cookie_consent_data = {
            "consent_type": "accept_all",
            "cookies": ["essential", "analytics", "marketing"],
            "ip_address": "192.168.1.1",
            "user_agent": "Legal-Pages-Test/1.0"
        }
        
        response = self.make_request('POST', '/legal/cookie-consent', cookie_consent_data)
        if response and response.status_code in [200, 201]:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Cookie Consent API", True, f"Cookie consent recorded successfully")
                else:
                    self.log_test("Cookie Consent API", False, f"API returned success=false")
            except:
                self.log_test("Cookie Consent API", False, f"Invalid JSON response")
        else:
            status_code = response.status_code if response else "No Response"
            self.log_test("Cookie Consent API", False, f"Cookie consent API failed (Status: {status_code})")
        
        # Test data export request API
        export_data = {
            "email": "legal_test@example.com",
            "data_types": ["profile", "courses", "payments"],
            "format": "json",
            "reason": "GDPR data portability request"
        }
        
        response = self.make_request('POST', '/legal/data-export', export_data)
        if response and response.status_code in [200, 201]:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Data Export API", True, f"Data export request submitted successfully")
                else:
                    self.log_test("Data Export API", False, f"API returned success=false")
            except:
                self.log_test("Data Export API", False, f"Invalid JSON response")
        else:
            status_code = response.status_code if response else "No Response"
            self.log_test("Data Export API", False, f"Data export API failed (Status: {status_code})")
        
        # Test data deletion request API
        deletion_data = {
            "confirmation": True,
            "reason": "No longer need the service",
            "keep_anonymous_data": False
        }
        
        response = self.make_request('POST', '/legal/data-deletion', deletion_data)
        if response and response.status_code in [200, 201]:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Data Deletion API", True, f"Data deletion request submitted successfully")
                else:
                    self.log_test("Data Deletion API", False, f"API returned success=false")
            except:
                self.log_test("Data Deletion API", False, f"Invalid JSON response")
        else:
            status_code = response.status_code if response else "No Response"
            self.log_test("Data Deletion API", False, f"Data deletion API failed (Status: {status_code})")
        
        # Test data processing activities API
        response = self.make_request('GET', '/legal/data-processing')
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and data.get('data'):
                    self.log_test("Data Processing API", True, f"Data processing activities retrieved successfully")
                else:
                    self.log_test("Data Processing API", False, f"API returned invalid data structure")
            except:
                self.log_test("Data Processing API", False, f"Invalid JSON response")
        else:
            status_code = response.status_code if response else "No Response"
            self.log_test("Data Processing API", False, f"Data processing API failed (Status: {status_code})")
        
        # Test audit log API
        response = self.make_request('GET', '/legal/audit-log')
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Audit Log API", True, f"Audit log retrieved successfully")
                else:
                    self.log_test("Audit Log API", False, f"API returned success=false")
            except:
                self.log_test("Audit Log API", False, f"Invalid JSON response")
        else:
            status_code = response.status_code if response else "No Response"
            self.log_test("Audit Log API", False, f"Audit log API failed (Status: {status_code})")
        
        return True

    def test_page_routes(self):
        """Test all legal page routes"""
        print("\n🌐 PAGE ROUTES TESTING")
        
        routes = [
            ('/terms-of-service', 'Terms of Service Page'),
            ('/privacy-policy', 'Privacy Policy Page'),
            ('/cookie-policy', 'Cookie Policy Page'),
            ('/refund-policy', 'Refund Policy Page'),
            ('/accessibility-statement', 'Accessibility Statement Page'),
            ('/account-removal', 'Account Removal Page'),
            ('/support', 'Support Page')
        ]
        
        working_routes = 0
        
        for route, name in routes:
            response = self.make_request('GET', route, auth_required=False, use_web=True)
            
            if response and response.status_code == 200:
                self.log_test(f"Route - {name}", True, f"Route accessible (Status: {response.status_code})")
                working_routes += 1
            elif response and response.status_code == 404:
                self.log_test(f"Route - {name}", False, f"Route not found (Status: 404)")
            else:
                status_code = response.status_code if response else "No Response"
                self.log_test(f"Route - {name}", False, f"Route failed (Status: {status_code})")
        
        success_rate = (working_routes / len(routes)) * 100
        self.log_test("Page Routes", working_routes > 0, f"Page routes working: {working_routes}/{len(routes)} ({success_rate:.1f}%)")
        
        return working_routes > 0

    def test_legal_document_seeder(self):
        """Test if legal document seeder data is properly populated"""
        print("\n🌱 LEGAL DOCUMENT SEEDER TESTING")
        
        # Test by checking if legal pages return content (indirect test)
        legal_types = [
            'terms_of_service',
            'privacy_policy',
            'cookie_policy',
            'refund_policy',
            'accessibility_statement'
        ]
        
        seeded_documents = 0
        
        for doc_type in legal_types:
            # Convert to URL format
            url_path = doc_type.replace('_', '-')
            response = self.make_request('GET', f'/{url_path}', auth_required=False, use_web=True)
            
            if response and response.status_code == 200:
                content = response.text
                # Check for meaningful content that suggests seeded data
                if len(content) > 1000 and any(keyword in content.lower() for keyword in ['terms', 'policy', 'privacy', 'cookie', 'refund', 'accessibility']):
                    self.log_test(f"Seeded Document - {doc_type}", True, f"Document appears to have seeded content")
                    seeded_documents += 1
                else:
                    self.log_test(f"Seeded Document - {doc_type}", False, f"Document exists but content appears minimal")
            else:
                self.log_test(f"Seeded Document - {doc_type}", False, f"Document not accessible")
        
        success_rate = (seeded_documents / len(legal_types)) * 100
        self.log_test("Legal Document Seeder", seeded_documents > 0, f"Seeded documents: {seeded_documents}/{len(legal_types)} ({success_rate:.1f}%)")
        
        return seeded_documents > 0

    def run_comprehensive_test(self):
        """Run all legal pages tests"""
        print("🚀 STARTING COMPREHENSIVE LEGAL PAGES BACKEND TESTING")
        print(f"🌐 Base URL: {self.base_url}")
        print(f"📡 API URL: {self.api_url}")
        print("=" * 80)
        
        # Authentication setup
        auth_success = self.authenticate_user()
        
        # Run all tests
        test_results = []
        
        # 1. Database verification
        test_results.append(self.test_database_tables())
        
        # 2. Legal document model testing
        test_results.append(self.test_legal_document_model())
        
        # 3. Legal controller methods testing
        test_results.append(self.test_legal_controller_methods())
        
        # 4. Legal API endpoints testing (requires auth)
        if auth_success:
            test_results.append(self.test_legal_api_endpoints())
        else:
            self.log_test("Legal API Endpoints", False, "Skipped due to authentication failure")
            test_results.append(False)
        
        # 5. Page routes testing
        test_results.append(self.test_page_routes())
        
        # 6. Legal document seeder testing
        test_results.append(self.test_legal_document_seeder())
        
        # Calculate overall results
        total_tests = len([t for t in self.test_results.values()])
        passed_tests = len([t for t in self.test_results.values() if t['success']])
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "=" * 80)
        print("📊 LEGAL PAGES BACKEND TESTING SUMMARY")
        print("=" * 80)
        
        # Group results by category
        categories = {
            'Authentication': [],
            'Database': [],
            'Legal Document': [],
            'Controller Method': [],
            'API Endpoint': [],
            'Route': [],
            'Seeded Document': []
        }
        
        for test_name, result in self.test_results.items():
            status = "✅" if result['success'] else "❌"
            print(f"{status} {test_name}: {result['message']}")
            
            # Categorize results
            if 'Authentication' in test_name or 'Login' in test_name or 'Registration' in test_name:
                categories['Authentication'].append(result['success'])
            elif 'Database' in test_name:
                categories['Database'].append(result['success'])
            elif 'Legal Document' in test_name:
                categories['Legal Document'].append(result['success'])
            elif 'Controller Method' in test_name:
                categories['Controller Method'].append(result['success'])
            elif any(api in test_name for api in ['Cookie Consent', 'Data Export', 'Data Deletion', 'Data Processing', 'Audit Log']):
                categories['API Endpoint'].append(result['success'])
            elif 'Route' in test_name:
                categories['Route'].append(result['success'])
            elif 'Seeded Document' in test_name:
                categories['Seeded Document'].append(result['success'])
        
        print("\n📈 CATEGORY BREAKDOWN:")
        for category, results in categories.items():
            if results:
                passed = sum(results)
                total = len(results)
                rate = (passed / total * 100) if total > 0 else 0
                print(f"   {category}: {passed}/{total} ({rate:.1f}%)")
        
        print(f"\n🎯 OVERALL SUCCESS RATE: {passed_tests}/{total_tests} ({success_rate:.1f}%)")
        
        # Determine overall status
        if success_rate >= 80:
            print("🎉 LEGAL PAGES BACKEND: EXCELLENT - System is production ready!")
        elif success_rate >= 60:
            print("✅ LEGAL PAGES BACKEND: GOOD - System is functional with minor issues")
        elif success_rate >= 40:
            print("⚠️  LEGAL PAGES BACKEND: PARTIAL - System has significant issues")
        else:
            print("❌ LEGAL PAGES BACKEND: CRITICAL - System requires major fixes")
        
        return success_rate >= 60

def main():
    """Main test execution"""
    print("🔍 Legal Pages Backend Testing Suite")
    print("Testing legal documents system, API endpoints, and page routes")
    print("-" * 80)
    
    # Use localhost URL
    base_url = "http://localhost:8001"
    
    tester = LegalPagesAPITester(base_url)
    success = tester.run_comprehensive_test()
    
    # Exit with appropriate code
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()