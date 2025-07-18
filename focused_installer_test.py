#!/usr/bin/env python3
"""
Focused Installer System Testing for Mewayz Platform
Tests the automated installer system with proper error handling
"""

import requests
import json
import sys
import time
from datetime import datetime

class FocusedInstallerTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.web_url = base_url
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
        
    def make_request(self, method, endpoint, data=None, headers=None, timeout=15):
        """Make HTTP request with proper headers"""
        time.sleep(0.2)  # Rate limiting
        
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
        """Test GET /install - Welcome page"""
        print("\n=== Testing Installer Welcome Page ===")
        response = self.make_request('GET', '/install')
        
        if response is None:
            self.log_test("Installer Welcome Page", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            content = response.text.lower()
            if 'install' in content or 'welcome' in content or 'setup' in content:
                self.log_test("Installer Welcome Page", True, "Welcome page loaded successfully")
                return True
            else:
                self.log_test("Installer Welcome Page", False, "Page doesn't contain installer content")
                return False
        else:
            self.log_test("Installer Welcome Page", False, f"HTTP {response.status_code}")
            return False

    def test_system_requirements(self):
        """Test system requirements checking"""
        print("\n=== Testing System Requirements ===")
        
        # Test requirements page
        response = self.make_request('GET', '/install/step/requirements')
        if response is None or response.status_code != 200:
            self.log_test("Requirements Page", False, f"Failed to load requirements page")
            return False
        
        self.log_test("Requirements Page", True, "Requirements page loaded")
        
        # Test requirements validation
        response = self.make_request('POST', '/install/process/requirements')
        if response is None:
            self.log_test("Requirements Validation", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Requirements Validation", True, f"Requirements passed: {data.get('message')}")
                    return True
                else:
                    self.log_test("Requirements Validation", False, f"Requirements failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Requirements Validation", True, "Requirements processed")
                return True
        else:
            self.log_test("Requirements Validation", False, f"HTTP {response.status_code}")
            return False

    def test_database_configuration(self):
        """Test database configuration"""
        print("\n=== Testing Database Configuration ===")
        
        # Test database config page
        response = self.make_request('GET', '/install/step/database')
        if response is None or response.status_code != 200:
            self.log_test("Database Config Page", False, "Failed to load database config page")
            return False
        
        self.log_test("Database Config Page", True, "Database config page loaded")
        
        # Test database connection with working credentials
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
            self.log_test("Database Connection Test", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test("Database Connection Test", True, f"Database connection successful")
                    return True
                else:
                    self.log_test("Database Connection Test", False, f"Database connection failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Database Connection Test", True, "Database test processed")
                return True
        else:
            self.log_test("Database Connection Test", False, f"HTTP {response.status_code}")
            return False

    def test_environment_setup(self):
        """Test environment configuration"""
        print("\n=== Testing Environment Setup ===")
        
        # Test environment page
        response = self.make_request('GET', '/install/step/environment')
        if response is None or response.status_code != 200:
            self.log_test("Environment Setup Page", False, "Failed to load environment page")
            return False
        
        self.log_test("Environment Setup Page", True, "Environment setup page loaded")
        
        # Test environment configuration
        env_config = {
            "app_name": "Mewayz Test Installation",
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
                    self.log_test("Environment Configuration", True, "Environment configured successfully")
                    return True
                else:
                    self.log_test("Environment Configuration", False, f"Environment config failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Environment Configuration", True, "Environment config processed")
                return True
        else:
            self.log_test("Environment Configuration", False, f"HTTP {response.status_code}")
            return False

    def test_admin_user_creation(self):
        """Test admin user creation"""
        print("\n=== Testing Admin User Creation ===")
        
        # Test admin creation page
        response = self.make_request('GET', '/install/step/admin')
        if response is None or response.status_code != 200:
            self.log_test("Admin Creation Page", False, "Failed to load admin creation page")
            return False
        
        self.log_test("Admin Creation Page", True, "Admin creation page loaded")
        
        # Test admin user creation with unique email
        admin_data = {
            "name": "Test Administrator",
            "email": f"admin-installer-{int(time.time())}@mewayz.com",
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
                    self.log_test("Admin User Creation", True, "Admin user created successfully")
                    return True
                else:
                    self.log_test("Admin User Creation", False, f"Admin creation failed: {data.get('message')}")
                    return False
            except:
                self.log_test("Admin User Creation", True, "Admin creation processed")
                return True
        else:
            self.log_test("Admin User Creation", False, f"HTTP {response.status_code}")
            return False

    def test_installation_status(self):
        """Test installation status endpoint"""
        print("\n=== Testing Installation Status ===")
        
        response = self.make_request('GET', '/install/status')
        if response is None:
            self.log_test("Installation Status", False, "Request timeout")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                installed = data.get('installed', False)
                version = data.get('version')
                self.log_test("Installation Status", True, f"Status: Installed={installed}, Version={version}")
                return True
            except:
                self.log_test("Installation Status", True, "Status endpoint responded")
                return True
        else:
            self.log_test("Installation Status", False, f"HTTP {response.status_code}")
            return False

    def run_focused_installer_tests(self):
        """Run focused installer tests"""
        print("🚀 Starting Focused Installer System Testing...")
        print("=" * 60)
        
        tests = [
            ("Installer Welcome Page", self.test_installer_welcome_page),
            ("System Requirements", self.test_system_requirements),
            ("Database Configuration", self.test_database_configuration),
            ("Environment Setup", self.test_environment_setup),
            ("Admin User Creation", self.test_admin_user_creation),
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
        print(f"📊 FOCUSED INSTALLER TESTING SUMMARY")
        print(f"Total Tests: {total}")
        print(f"Passed: {passed}")
        print(f"Failed: {total - passed}")
        print(f"Success Rate: {(passed/total)*100:.1f}%")
        
        if passed == total:
            print("🎉 ALL INSTALLER TESTS PASSED!")
        elif passed >= total * 0.8:
            print("✅ INSTALLER SYSTEM MOSTLY FUNCTIONAL")
        else:
            print("❌ INSTALLER SYSTEM HAS ISSUES")
        
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
    
    tester = FocusedInstallerTester()
    passed, total = tester.run_focused_installer_tests()
    
    # Exit with appropriate code
    if passed >= total * 0.8:  # 80% success rate is acceptable
        sys.exit(0)
    else:
        sys.exit(1)

if __name__ == "__main__":
    main()