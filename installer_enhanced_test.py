#!/usr/bin/env python3
"""
Enhanced Automated Installer System Testing for Mewayz
Testing production-ready installer features with comprehensive validation
"""

import requests
import json
import sys
import time
from datetime import datetime

class EnhancedInstallerTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.install_url = f"{base_url}/install"
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
        
        url = f"{self.install_url}{endpoint}"
        
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
                response = self.session.post(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=default_headers, timeout=timeout)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=timeout)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"⚠️  Request timeout for {method} {url}")
            return None
        except requests.exceptions.RequestException as e:
            print(f"⚠️  Request failed for {method} {url}: {str(e)}")
            return None

    def test_installation_status(self):
        """Test GET /install/status - Installation status check"""
        print("\n🔍 Testing Installation Status...")
        
        response = self.make_request('GET', '/status')
        
        if response is None:
            self.log_test("Installation Status", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                self.log_test("Installation Status", True, 
                    f"Status check successful - Installed: {data.get('installed', 'Unknown')}")
                return True
            except json.JSONDecodeError:
                self.log_test("Installation Status", False, "Invalid JSON response")
                return False
        else:
            self.log_test("Installation Status", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_system_requirements_check(self):
        """Test GET /install/step/requirements - Enhanced system requirements"""
        print("\n🔍 Testing Enhanced System Requirements Check...")
        
        response = self.make_request('GET', '/step/requirements')
        
        if response is None:
            self.log_test("System Requirements Check", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            # For web routes, we might get HTML response
            content_type = response.headers.get('content-type', '')
            if 'application/json' in content_type:
                try:
                    data = response.json()
                    requirements = data.get('requirements', {})
                    permissions = data.get('permissions', {})
                    services = data.get('services', {})
                    php_config = data.get('phpConfig', {})
                    
                    self.log_test("System Requirements Check", True, 
                        f"Requirements check successful - PHP Extensions: {len(requirements)}, "
                        f"Permissions: {len(permissions)}, Services: {len(services)}, "
                        f"PHP Config: {len(php_config)}")
                    return True
                except json.JSONDecodeError:
                    pass
            
            # If HTML response, check if it contains requirements content
            if 'requirements' in response.text.lower() or 'system' in response.text.lower():
                self.log_test("System Requirements Check", True, 
                    "Requirements page loaded successfully (HTML response)")
                return True
            else:
                self.log_test("System Requirements Check", False, 
                    "Requirements page doesn't contain expected content")
                return False
        else:
            self.log_test("System Requirements Check", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_requirements_validation(self):
        """Test POST /install/process/requirements - Validate comprehensive requirements"""
        print("\n🔍 Testing Requirements Validation...")
        
        response = self.make_request('POST', '/process/requirements')
        
        if response is None:
            self.log_test("Requirements Validation", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                success = data.get('success', False)
                message = data.get('message', '')
                next_step = data.get('nextStep', '')
                
                self.log_test("Requirements Validation", success, 
                    f"Validation result: {message}, Next step: {next_step}")
                return success
            except json.JSONDecodeError:
                self.log_test("Requirements Validation", False, "Invalid JSON response")
                return False
        elif response.status_code == 400:
            try:
                data = response.json()
                message = data.get('message', 'Requirements not met')
                self.log_test("Requirements Validation", True, 
                    f"Expected validation failure: {message}")
                return True  # This is expected if requirements aren't met
            except json.JSONDecodeError:
                self.log_test("Requirements Validation", False, "Invalid error response")
                return False
        else:
            self.log_test("Requirements Validation", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_database_configuration(self):
        """Test POST /install/process/database - Production database setup"""
        print("\n🔍 Testing Production Database Configuration...")
        
        # Production-ready database configuration
        db_config = {
            "host": "127.0.0.1",
            "port": 3306,
            "database": "mewayz",
            "username": "root",
            "password": "",
            "connection": "mysql"
        }
        
        response = self.make_request('POST', '/process/database', db_config)
        
        if response is None:
            self.log_test("Database Configuration", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                success = data.get('success', False)
                message = data.get('message', '')
                details = data.get('details', {})
                next_step = data.get('nextStep', '')
                
                version = details.get('version', 'Unknown')
                charset = details.get('charset', 'Unknown')
                timezone = details.get('timezone', 'Unknown')
                
                self.log_test("Database Configuration", success, 
                    f"Database setup: {message}, Version: {version}, "
                    f"Charset: {charset}, Timezone: {timezone}, Next: {next_step}")
                return success
            except json.JSONDecodeError:
                self.log_test("Database Configuration", False, "Invalid JSON response")
                return False
        elif response.status_code == 400:
            try:
                data = response.json()
                message = data.get('message', 'Database configuration failed')
                self.log_test("Database Configuration", False, 
                    f"Database error: {message}")
                return False
            except json.JSONDecodeError:
                self.log_test("Database Configuration", False, "Invalid error response")
                return False
        else:
            self.log_test("Database Configuration", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_environment_setup(self):
        """Test POST /install/process/environment - Production environment config"""
        print("\n🔍 Testing Production Environment Setup...")
        
        # Production-ready environment configuration
        env_config = {
            "app_name": "Mewayz",
            "app_url": "https://mewayz.com",
            "app_env": "production",
            "app_debug": False,
            "mail_driver": "smtp",
            "mail_host": "smtp.mailgun.org",
            "mail_port": 587,
            "mail_username": "noreply@mewayz.com",
            "mail_password": "secure_password",
            "mail_encryption": "tls",
            "mail_from_address": "noreply@mewayz.com",
            "mail_from_name": "Mewayz",
            "broadcast_driver": "redis",
            "cache_driver": "redis",
            "queue_connection": "redis",
            "session_driver": "redis",
            "redis_host": "127.0.0.1",
            "redis_port": 6379,
            "redis_password": ""
        }
        
        response = self.make_request('POST', '/process/environment', env_config)
        
        if response is None:
            self.log_test("Environment Setup", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                success = data.get('success', False)
                message = data.get('message', '')
                next_step = data.get('nextStep', '')
                
                self.log_test("Environment Setup", success, 
                    f"Environment config: {message}, Next step: {next_step}")
                return success
            except json.JSONDecodeError:
                self.log_test("Environment Setup", False, "Invalid JSON response")
                return False
        elif response.status_code == 400:
            try:
                data = response.json()
                message = data.get('message', 'Environment configuration failed')
                errors = data.get('errors', {})
                self.log_test("Environment Setup", False, 
                    f"Environment error: {message}, Errors: {len(errors)}")
                return False
            except json.JSONDecodeError:
                self.log_test("Environment Setup", False, "Invalid error response")
                return False
        else:
            self.log_test("Environment Setup", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_admin_user_creation(self):
        """Test POST /install/process/admin - Admin user creation"""
        print("\n🔍 Testing Admin User Creation...")
        
        # Admin user configuration
        admin_config = {
            "name": "Administrator",
            "email": "admin@mewayz.com",
            "password": "SecureAdmin123!",
            "password_confirmation": "SecureAdmin123!"
        }
        
        response = self.make_request('POST', '/process/admin', admin_config)
        
        if response is None:
            self.log_test("Admin User Creation", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                success = data.get('success', False)
                message = data.get('message', '')
                next_step = data.get('nextStep', '')
                
                self.log_test("Admin User Creation", success, 
                    f"Admin creation: {message}, Next step: {next_step}")
                return success
            except json.JSONDecodeError:
                self.log_test("Admin User Creation", False, "Invalid JSON response")
                return False
        elif response.status_code == 400:
            try:
                data = response.json()
                message = data.get('message', 'Admin user creation failed')
                errors = data.get('errors', {})
                self.log_test("Admin User Creation", False, 
                    f"Admin creation error: {message}, Errors: {len(errors)}")
                return False
            except json.JSONDecodeError:
                self.log_test("Admin User Creation", False, "Invalid error response")
                return False
        else:
            self.log_test("Admin User Creation", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_installation_finalization(self):
        """Test POST /install/process/finalize - Complete production setup"""
        print("\n🔍 Testing Installation Finalization...")
        
        # Finalization configuration
        finalize_config = {
            "create_backup": True,
            "run_migrations": True,
            "run_seeders": True,
            "optimize_application": True,
            "setup_security": True,
            "configure_monitoring": True
        }
        
        response = self.make_request('POST', '/process/finalize', finalize_config, timeout=60)
        
        if response is None:
            self.log_test("Installation Finalization", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                success = data.get('success', False)
                message = data.get('message', '')
                results = data.get('results', {})
                installation_info = data.get('installation_info', {})
                next_step = data.get('nextStep', '')
                
                # Count successful operations
                successful_ops = sum(1 for result in results.values() 
                                   if isinstance(result, dict) and result.get('success', False))
                total_ops = len(results)
                
                version = installation_info.get('version', 'Unknown')
                features = installation_info.get('features', {})
                enabled_features = sum(1 for enabled in features.values() if enabled)
                
                self.log_test("Installation Finalization", success, 
                    f"Finalization: {message}, Operations: {successful_ops}/{total_ops}, "
                    f"Version: {version}, Features: {enabled_features}, Next: {next_step}")
                return success
            except json.JSONDecodeError:
                self.log_test("Installation Finalization", False, "Invalid JSON response")
                return False
        elif response.status_code == 500:
            try:
                data = response.json()
                message = data.get('message', 'Installation failed')
                error = data.get('error', '')
                self.log_test("Installation Finalization", False, 
                    f"Installation error: {message}")
                return False
            except json.JSONDecodeError:
                self.log_test("Installation Finalization", False, "Installation failed with server error")
                return False
        else:
            self.log_test("Installation Finalization", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def test_installation_complete_status(self):
        """Test GET /install/status after installation - Verify completion"""
        print("\n🔍 Testing Installation Complete Status...")
        
        response = self.make_request('GET', '/status')
        
        if response is None:
            self.log_test("Installation Complete Status", False, "Request timeout or connection error")
            return False
            
        if response.status_code == 200:
            try:
                data = response.json()
                installed = data.get('installed', False)
                version_info = data.get('version', {})
                
                if installed and version_info:
                    version = version_info.get('version', 'Unknown')
                    installed_at = version_info.get('installed_at', 'Unknown')
                    features = version_info.get('features', {})
                    enabled_features = sum(1 for enabled in features.values() if enabled)
                    
                    self.log_test("Installation Complete Status", True, 
                        f"Installation verified - Version: {version}, "
                        f"Installed: {installed_at}, Features: {enabled_features}")
                    return True
                else:
                    self.log_test("Installation Complete Status", False, 
                        f"Installation not complete - Installed: {installed}")
                    return False
            except json.JSONDecodeError:
                self.log_test("Installation Complete Status", False, "Invalid JSON response")
                return False
        else:
            self.log_test("Installation Complete Status", False, 
                f"Status {response.status_code}: {response.text[:200]}")
            return False

    def run_comprehensive_installer_tests(self):
        """Run all enhanced installer tests"""
        print("🚀 Starting Enhanced Automated Installer System Tests...")
        print(f"🔗 Base URL: {self.base_url}")
        print(f"🔗 Install URL: {self.install_url}")
        
        tests = [
            ("Installation Status Check", self.test_installation_status),
            ("System Requirements Check", self.test_system_requirements_check),
            ("Requirements Validation", self.test_requirements_validation),
            ("Database Configuration", self.test_database_configuration),
            ("Environment Setup", self.test_environment_setup),
            ("Admin User Creation", self.test_admin_user_creation),
            ("Installation Finalization", self.test_installation_finalization),
            ("Installation Complete Status", self.test_installation_complete_status),
        ]
        
        passed = 0
        total = len(tests)
        
        for test_name, test_func in tests:
            try:
                if test_func():
                    passed += 1
            except Exception as e:
                self.log_test(test_name, False, f"Test exception: {str(e)}")
        
        # Print summary
        print(f"\n📊 Enhanced Installer Test Summary:")
        print(f"✅ Passed: {passed}/{total} ({(passed/total)*100:.1f}%)")
        print(f"❌ Failed: {total-passed}/{total}")
        
        if passed == total:
            print("🎉 All enhanced installer tests passed!")
        elif passed >= total * 0.8:
            print("⚠️  Most installer tests passed - minor issues detected")
        else:
            print("🚨 Significant installer issues detected")
        
        return passed, total

def main():
    """Main test execution"""
    if len(sys.argv) > 1:
        base_url = sys.argv[1]
    else:
        base_url = "http://localhost:8001"
    
    print("🔧 Enhanced Automated Installer System Testing")
    print("=" * 60)
    
    tester = EnhancedInstallerTester(base_url)
    passed, total = tester.run_comprehensive_installer_tests()
    
    # Exit with appropriate code
    if passed == total:
        sys.exit(0)  # All tests passed
    elif passed >= total * 0.8:
        sys.exit(1)  # Minor issues
    else:
        sys.exit(2)  # Major issues

if __name__ == "__main__":
    main()