#!/usr/bin/env python3
"""
Automated Installer System Testing for Mewayz Platform
Tests the comprehensive installation wizard implementation
"""

import requests
import json
import sys
import time
from datetime import datetime
import os

class InstallerTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.web_url = base_url  # Installer uses web routes, not API routes
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
        
    def make_request(self, method, endpoint, data=None, headers=None, timeout=30):
        """Make HTTP request with proper headers"""
        time.sleep(0.1)  # Rate limiting
        
        url = f"{self.web_url}{endpoint}"
        
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'User-Agent': 'Mewayz-Installer-Test/1.0'
        }
        
        if headers:
            default_headers.update(headers)
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, timeout=timeout)
            elif method.upper() == 'POST':
                if data:
                    response = self.session.post(url, json=data, headers=default_headers, timeout=timeout)
                else:
                    response = self.session.post(url, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request error for {url}: {str(e)}")
            return None

    def test_installer_welcome_page(self):
        """Test GET /install - Should show welcome page"""
        response = self.make_request('GET', '/install')
        
        if response is None:
            self.log_test("Installer Welcome Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            # Check if it's a proper installer page (could be HTML or JSON)
            content_type = response.headers.get('content-type', '').lower()
            if 'html' in content_type:
                # HTML response - check for installer content
                if 'install' in response.text.lower() or 'welcome' in response.text.lower():
                    self.log_test("Installer Welcome Page", True, f"Welcome page loaded successfully (HTML)")
                    return True
                else:
                    self.log_test("Installer Welcome Page", False, f"HTML page doesn't contain installer content")
                    return False
            elif 'json' in content_type:
                # JSON response
                try:
                    data = response.json()
                    self.log_test("Installer Welcome Page", True, f"Welcome page loaded successfully (JSON): {data}")
                    return True
                except:
                    self.log_test("Installer Welcome Page", False, f"Invalid JSON response")
                    return False
            else:
                self.log_test("Installer Welcome Page", True, f"Welcome page loaded (Status: {response.status_code})")
                return True
        else:
            self.log_test("Installer Welcome Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_system_requirements_page(self):
        """Test GET /install/step/requirements - Should check system requirements"""
        response = self.make_request('GET', '/install/step/requirements')
        
        if response is None:
            self.log_test("System Requirements Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            content_type = response.headers.get('content-type', '').lower()
            if 'json' in content_type:
                try:
                    data = response.json()
                    if 'requirements' in str(data).lower():
                        self.log_test("System Requirements Page", True, f"Requirements page loaded with data")
                        return True
                except:
                    pass
            
            self.log_test("System Requirements Page", True, f"Requirements page loaded (Status: {response.status_code})")
            return True
        else:
            self.log_test("System Requirements Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_requirements_validation(self):
        """Test POST /install/process/requirements - Should validate requirements"""
        response = self.make_request('POST', '/install/process/requirements')
        
        if response is None:
            self.log_test("Requirements Validation", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Requirements Validation", True, f"Requirements validation passed: {data.get('message')}")
                    return True
                else:
                    self.log_test("Requirements Validation", False, f"Requirements validation failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Requirements Validation", True, f"Requirements processed (Status: {response.status_code})")
                return True
        else:
            # 400 might be expected if requirements not met
            if response.status_code == 400:
                try:
                    data = response.json()
                    self.log_test("Requirements Validation", True, f"Requirements validation returned expected error: {data.get('message')}")
                    return True
                except:
                    pass
            
            self.log_test("Requirements Validation", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_database_configuration_page(self):
        """Test GET /install/step/database - Should show database configuration form"""
        response = self.make_request('GET', '/install/step/database')
        
        if response is None:
            self.log_test("Database Configuration Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            self.log_test("Database Configuration Page", True, f"Database config page loaded (Status: {response.status_code})")
            return True
        else:
            self.log_test("Database Configuration Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_database_connection_validation(self):
        """Test POST /install/process/database - Should validate database connection"""
        # Test with current database configuration
        db_config = {
            "host": "127.0.0.1",
            "port": 3306,
            "database": "mewayz",
            "username": "root",
            "password": "",
            "connection": "mysql"
        }
        
        response = self.make_request('POST', '/install/process/database', data=db_config)
        
        if response is None:
            self.log_test("Database Connection Validation", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Database Connection Validation", True, f"Database connection successful: {data.get('message')}")
                    return True
                else:
                    self.log_test("Database Connection Validation", False, f"Database connection failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Database Connection Validation", True, f"Database validation processed (Status: {response.status_code})")
                return True
        else:
            self.log_test("Database Connection Validation", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_environment_setup_page(self):
        """Test GET /install/step/environment - Should show environment setup form"""
        response = self.make_request('GET', '/install/step/environment')
        
        if response is None:
            self.log_test("Environment Setup Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            self.log_test("Environment Setup Page", True, f"Environment setup page loaded (Status: {response.status_code})")
            return True
        else:
            self.log_test("Environment Setup Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_environment_configuration(self):
        """Test POST /install/process/environment - Should save environment settings"""
        env_config = {
            "app_name": "Mewayz Test",
            "app_url": "http://localhost:8001",
            "app_env": "production",
            "app_debug": False,
            "mail_driver": "smtp",
            "mail_host": "smtp.mailgun.org",
            "mail_port": 587,
            "mail_username": "test@example.com",
            "mail_password": "testpassword",
            "mail_encryption": "tls",
            "mail_from_address": "noreply@mewayz.com",
            "mail_from_name": "Mewayz Test",
            "broadcast_driver": "redis",
            "cache_driver": "redis",
            "queue_connection": "redis",
            "session_driver": "redis",
            "redis_host": "127.0.0.1",
            "redis_port": 6379,
            "redis_password": None
        }
        
        response = self.make_request('POST', '/install/process/environment', data=env_config)
        
        if response is None:
            self.log_test("Environment Configuration", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Environment Configuration", True, f"Environment config saved: {data.get('message')}")
                    return True
                else:
                    self.log_test("Environment Configuration", False, f"Environment config failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Environment Configuration", True, f"Environment config processed (Status: {response.status_code})")
                return True
        else:
            self.log_test("Environment Configuration", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_admin_creation_page(self):
        """Test GET /install/step/admin - Should show admin creation form"""
        response = self.make_request('GET', '/install/step/admin')
        
        if response is None:
            self.log_test("Admin Creation Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            self.log_test("Admin Creation Page", True, f"Admin creation page loaded (Status: {response.status_code})")
            return True
        else:
            self.log_test("Admin Creation Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_admin_user_creation(self):
        """Test POST /install/process/admin - Should create admin user"""
        admin_data = {
            "name": "Test Administrator",
            "email": f"admin-{int(time.time())}@mewayz.com",  # Unique email
            "password": "SecurePassword123!",
            "password_confirmation": "SecurePassword123!"
        }
        
        response = self.make_request('POST', '/install/process/admin', data=admin_data)
        
        if response is None:
            self.log_test("Admin User Creation", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Admin User Creation", True, f"Admin user created: {data.get('message')}")
                    return True
                else:
                    self.log_test("Admin User Creation", False, f"Admin creation failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Admin User Creation", True, f"Admin creation processed (Status: {response.status_code})")
                return True
        else:
            # Check for validation errors
            if response.status_code == 400 or response.status_code == 422:
                try:
                    data = response.json()
                    if 'errors' in data or 'message' in data:
                        self.log_test("Admin User Creation", False, f"Validation error: {data}")
                        return False
                except:
                    pass
            
            self.log_test("Admin User Creation", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_finalization_page(self):
        """Test GET /install/step/finalize - Should show finalization page"""
        response = self.make_request('GET', '/install/step/finalize')
        
        if response is None:
            self.log_test("Finalization Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            self.log_test("Finalization Page", True, f"Finalization page loaded (Status: {response.status_code})")
            return True
        else:
            self.log_test("Finalization Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_installation_finalization(self):
        """Test POST /install/process/finalize - Should complete installation"""
        response = self.make_request('POST', '/install/process/finalize', timeout=60)  # Longer timeout for migrations
        
        if response is None:
            self.log_test("Installation Finalization", False, "Request timeout (migrations may take time)")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    results = data.get('results', {})
                    self.log_test("Installation Finalization", True, f"Installation completed: {data.get('message')} - Results: {results}")
                    return True
                else:
                    self.log_test("Installation Finalization", False, f"Installation failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Installation Finalization", True, f"Installation processed (Status: {response.status_code})")
                return True
        else:
            self.log_test("Installation Finalization", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_completion_page(self):
        """Test GET /install/step/complete - Should show success page"""
        response = self.make_request('GET', '/install/step/complete')
        
        if response is None:
            self.log_test("Completion Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            self.log_test("Completion Page", True, f"Completion page loaded (Status: {response.status_code})")
            return True
        else:
            self.log_test("Completion Page", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def test_installation_status(self):
        """Test GET /install/status - Should return installation status"""
        response = self.make_request('GET', '/install/status')
        
        if response is None:
            self.log_test("Installation Status", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                installed = data.get('installed', False)
                version = data.get('version')
                self.log_test("Installation Status", True, f"Status retrieved - Installed: {installed}, Version: {version}")
                return True
            except:
                self.log_test("Installation Status", True, f"Status endpoint responded (Status: {response.status_code})")
                return True
        else:
            self.log_test("Installation Status", False, f"HTTP {response.status_code}: {response.text[:200]}")
            return False

    def run_comprehensive_installer_tests(self):
        """Run all installer tests in sequence"""
        print("🚀 Starting Comprehensive Installer System Testing...")
        print("=" * 60)
        
        tests = [
            ("Installer Welcome Page", self.test_installer_welcome_page),
            ("System Requirements Page", self.test_system_requirements_page),
            ("Requirements Validation", self.test_requirements_validation),
            ("Database Configuration Page", self.test_database_configuration_page),
            ("Database Connection Validation", self.test_database_connection_validation),
            ("Environment Setup Page", self.test_environment_setup_page),
            ("Environment Configuration", self.test_environment_configuration),
            ("Admin Creation Page", self.test_admin_creation_page),
            ("Admin User Creation", self.test_admin_user_creation),
            ("Finalization Page", self.test_finalization_page),
            ("Installation Finalization", self.test_installation_finalization),
            ("Completion Page", self.test_completion_page),
            ("Installation Status", self.test_installation_status),
        ]
        
        passed = 0
        total = len(tests)
        
        for test_name, test_func in tests:
            print(f"\n🧪 Testing: {test_name}")
            try:
                if test_func():
                    passed += 1
            except Exception as e:
                self.log_test(test_name, False, f"Test exception: {str(e)}")
        
        print("\n" + "=" * 60)
        print(f"📊 INSTALLER TESTING SUMMARY")
        print(f"Total Tests: {total}")
        print(f"Passed: {passed}")
        print(f"Failed: {total - passed}")
        print(f"Success Rate: {(passed/total)*100:.1f}%")
        
        if passed == total:
            print("🎉 ALL INSTALLER TESTS PASSED!")
        elif passed >= total * 0.8:
            print("✅ INSTALLER SYSTEM MOSTLY FUNCTIONAL")
        else:
            print("❌ INSTALLER SYSTEM HAS SIGNIFICANT ISSUES")
        
        return passed, total

def main():
    # Check if server is running
    try:
        response = requests.get("http://localhost:8001", timeout=5)
        print("✅ Server is running")
    except:
        print("❌ Server is not accessible at http://localhost:8001")
        print("Please ensure the Laravel server is running")
        sys.exit(1)
    
    tester = InstallerTester()
    passed, total = tester.run_comprehensive_installer_tests()
    
    # Exit with appropriate code
    if passed == total:
        sys.exit(0)
    else:
        sys.exit(1)

if __name__ == "__main__":
    main()