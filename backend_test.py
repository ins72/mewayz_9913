#!/usr/bin/env python3
"""
Mewayz Laravel Backend API Testing Suite
Tests all API endpoints for functionality, validation, and error handling
"""

import requests
import json
import sys
import time
from datetime import datetime
from typing import Dict, Any, Optional, List

class MewayzAPITester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_user_data = {
            "name": "John Doe",
            "email": "john.doe@example.com",
            "password": "password123",
            "password_confirmation": "password123"
        }
        self.admin_credentials = {
            "email": "admin@mewayz.com",
            "password": "password"
        }
        self.results = []
        
    def log_result(self, test_name: str, status: str, details: str = "", response_data: Any = None):
        """Log test result"""
        result = {
            "test": test_name,
            "status": status,
            "details": details,
            "timestamp": datetime.now().isoformat(),
            "response_data": response_data
        }
        self.results.append(result)
        
        status_symbol = "✅" if status == "PASS" else "❌" if status == "FAIL" else "⚠️"
        print(f"{status_symbol} {test_name}: {status}")
        if details:
            print(f"   Details: {details}")
        if status == "FAIL" and response_data:
            print(f"   Response: {response_data}")
        print()

    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None, files: Dict = None) -> requests.Response:
        """Make HTTP request with proper error handling"""
        url = f"{self.api_url}/{endpoint.lstrip('/')}"
        
        # Add auth token if available
        if self.auth_token and headers is None:
            headers = {}
        if self.auth_token:
            headers = headers or {}
            headers['Authorization'] = f'Bearer {self.auth_token}'
        
        # Always request JSON responses
        headers = headers or {}
        headers['Accept'] = 'application/json'
        headers['Content-Type'] = 'application/json'
        
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, params=data, headers=headers)
            elif method.upper() == 'POST':
                if files:
                    response = self.session.post(url, data=data, files=files, headers=headers)
                else:
                    response = self.session.post(url, json=data, headers=headers)
            elif method.upper() == 'PUT':
                response = self.session.put(url, json=data, headers=headers)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=headers)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
            raise

    def test_server_health(self):
        """Test if the Laravel server is responding"""
        try:
            response = requests.get(self.base_url, timeout=10)
            if response.status_code == 200:
                self.log_result("Server Health Check", "PASS", "Laravel server is responding")
                return True
            else:
                self.log_result("Server Health Check", "FAIL", f"Server returned status {response.status_code}")
                return False
        except Exception as e:
            self.log_result("Server Health Check", "FAIL", f"Server not accessible: {str(e)}")
            return False

    def test_auth_register(self):
        """Test user registration"""
        try:
            # Use unique email to avoid conflicts
            unique_email = f"test_{int(time.time())}@example.com"
            test_data = {
                "name": "Test User",
                "email": unique_email,
                "password": "password123",
                "password_confirmation": "password123"
            }
            
            response = self.make_request('POST', '/auth/register', test_data)
            
            if response.status_code == 201:
                try:
                    data = response.json()
                    if data.get('success') and data.get('token'):
                        self.log_result("User Registration", "PASS", "User registered successfully with token")
                        return True
                    else:
                        self.log_result("User Registration", "FAIL", "Registration succeeded but missing token or success flag", data)
                        return False
                except ValueError as e:
                    self.log_result("User Registration", "FAIL", f"Invalid JSON response: {str(e)}", response.text[:200])
                    return False
            elif response.status_code == 422:
                try:
                    data = response.json()
                    self.log_result("User Registration", "FAIL", f"Validation errors: {data.get('errors', 'Unknown validation error')}", data)
                    return False
                except ValueError:
                    self.log_result("User Registration", "FAIL", f"422 status with invalid JSON response", response.text[:200])
                    return False
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("User Registration", "FAIL", f"Registration failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("User Registration", "FAIL", f"Registration request failed: {str(e)}")
            return False

    def test_auth_login(self):
        """Test user login with admin credentials"""
        try:
            response = self.make_request('POST', '/auth/login', self.admin_credentials)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.auth_token = data['token']
                    self.log_result("User Login", "PASS", "Login successful with token received")
                    return True
                else:
                    self.log_result("User Login", "FAIL", "Login succeeded but missing token or success flag", data)
                    return False
            else:
                self.log_result("User Login", "FAIL", f"Login failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("User Login", "FAIL", f"Login request failed: {str(e)}")
            return False

    def test_auth_me(self):
        """Test getting current user info"""
        if not self.auth_token:
            self.log_result("Get Current User", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/auth/me')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('user'):
                    self.log_result("Get Current User", "PASS", "User info retrieved successfully")
                    return True
                else:
                    self.log_result("Get Current User", "FAIL", "Response missing user data", data)
                    return False
            else:
                self.log_result("Get Current User", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Current User", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_auth_profile_update(self):
        """Test profile update"""
        if not self.auth_token:
            self.log_result("Profile Update", "SKIP", "No auth token available")
            return False
            
        try:
            update_data = {
                "name": "Updated Admin User",
                "email": "admin@mewayz.com"  # Keep same email to avoid unique constraint
            }
            
            response = self.make_request('PUT', '/auth/profile', update_data)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("Profile Update", "PASS", "Profile updated successfully")
                    return True
                else:
                    self.log_result("Profile Update", "FAIL", "Update succeeded but missing success flag", data)
                    return False
            else:
                self.log_result("Profile Update", "FAIL", f"Update failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Profile Update", "FAIL", f"Update request failed: {str(e)}")
            return False

    def test_workspaces_list(self):
        """Test listing workspaces"""
        if not self.auth_token:
            self.log_result("List Workspaces", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/workspaces')
            
            if response.status_code == 200:
                data = response.json()
                self.log_result("List Workspaces", "PASS", f"Workspaces retrieved successfully")
                return True
            else:
                self.log_result("List Workspaces", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("List Workspaces", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_workspaces_create(self):
        """Test creating a workspace"""
        if not self.auth_token:
            self.log_result("Create Workspace", "SKIP", "No auth token available")
            return False
            
        try:
            workspace_data = {
                "name": f"Test Workspace {int(time.time())}",
                "description": "Test workspace for API testing"
            }
            
            response = self.make_request('POST', '/workspaces', workspace_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                self.log_result("Create Workspace", "PASS", "Workspace created successfully")
                return data.get('data', {}).get('id')  # Return workspace ID for further tests
            else:
                self.log_result("Create Workspace", "FAIL", f"Creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Create Workspace", "FAIL", f"Creation request failed: {str(e)}")
            return False

    def test_social_media_accounts(self):
        """Test getting social media accounts"""
        if not self.auth_token:
            self.log_result("Get Social Media Accounts", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/social-media/accounts')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("Get Social Media Accounts", "PASS", "Social media accounts retrieved successfully")
                    return True
                else:
                    self.log_result("Get Social Media Accounts", "FAIL", "Response missing success flag", data)
                    return False
            else:
                self.log_result("Get Social Media Accounts", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Social Media Accounts", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_social_media_connect(self):
        """Test connecting a social media account"""
        if not self.auth_token:
            self.log_result("Connect Social Media Account", "SKIP", "No auth token available")
            return False
            
        try:
            connect_data = {
                "platform": "instagram",
                "username": "test_instagram_user",
                "display_name": "Test Instagram User",
                "access_token": "fake_access_token_for_testing"
            }
            
            response = self.make_request('POST', '/social-media/accounts/connect', connect_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success'):
                    self.log_result("Connect Social Media Account", "PASS", "Social media account connected successfully")
                    return data.get('data', {}).get('id')  # Return account ID
                else:
                    self.log_result("Connect Social Media Account", "FAIL", "Connection succeeded but missing success flag", data)
                    return False
            else:
                self.log_result("Connect Social Media Account", "FAIL", f"Connection failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Connect Social Media Account", "FAIL", f"Connection request failed: {str(e)}")
            return False

    def test_social_media_analytics(self):
        """Test getting social media analytics"""
        if not self.auth_token:
            self.log_result("Get Social Media Analytics", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/social-media/analytics')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("Get Social Media Analytics", "PASS", "Social media analytics retrieved successfully")
                    return True
                else:
                    self.log_result("Get Social Media Analytics", "FAIL", "Response missing success flag", data)
                    return False
            else:
                self.log_result("Get Social Media Analytics", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Social Media Analytics", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_list(self):
        """Test listing bio sites"""
        if not self.auth_token:
            self.log_result("List Bio Sites", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/bio-sites')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("List Bio Sites", "PASS", "Bio sites retrieved successfully")
                    return True
                else:
                    self.log_result("List Bio Sites", "FAIL", "Response missing success flag", data)
                    return False
            else:
                self.log_result("List Bio Sites", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("List Bio Sites", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_create(self):
        """Test creating a bio site"""
        if not self.auth_token:
            self.log_result("Create Bio Site", "SKIP", "No auth token available")
            return False
            
        try:
            bio_site_data = {
                "title": f"Test Bio Site {int(time.time())}",
                "slug": f"test-bio-{int(time.time())}",
                "description": "Test bio site for API testing",
                "theme_config": {
                    "primary_color": "#FDFDFD",
                    "background_color": "#101010",
                    "text_color": "#F1F1F1"
                }
            }
            
            response = self.make_request('POST', '/bio-sites', bio_site_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success'):
                    self.log_result("Create Bio Site", "PASS", "Bio site created successfully")
                    return data.get('data', {}).get('id')  # Return bio site ID
                else:
                    self.log_result("Create Bio Site", "FAIL", "Creation succeeded but missing success flag", data)
                    return False
            else:
                self.log_result("Create Bio Site", "FAIL", f"Creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Create Bio Site", "FAIL", f"Creation request failed: {str(e)}")
            return False

    def test_bio_sites_show(self, bio_site_id):
        """Test getting a specific bio site"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Get Bio Site Details", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_result("Get Bio Site Details", "PASS", "Bio site details retrieved successfully")
                    return True
                else:
                    self.log_result("Get Bio Site Details", "FAIL", "Response missing success flag", data)
                    return False
            else:
                self.log_result("Get Bio Site Details", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Bio Site Details", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_validation_errors(self):
        """Test API validation error handling"""
        if not self.auth_token:
            self.log_result("Validation Error Handling", "SKIP", "No auth token available")
            return False
            
        try:
            # Test bio site creation with invalid data
            invalid_data = {
                "title": "",  # Required field empty
                "slug": "invalid slug with spaces",  # Invalid slug format
                "theme_config": {
                    "primary_color": "invalid_color"  # Invalid color format
                }
            }
            
            response = self.make_request('POST', '/bio-sites', invalid_data)
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("Validation Error Handling", "PASS", "Validation errors returned correctly")
                    return True
                else:
                    self.log_result("Validation Error Handling", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Validation Error Handling", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Validation Error Handling", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_crm_contacts(self):
        """Test CRM contact management"""
        if not self.auth_token:
            self.log_result("CRM Contacts", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting contacts
            response = self.make_request('GET', '/crm/contacts')
            
            if response.status_code == 200:
                self.log_result("CRM Contacts", "PASS", "CRM contacts retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("CRM Contacts", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("CRM Contacts", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_crm_leads(self):
        """Test CRM lead management"""
        if not self.auth_token:
            self.log_result("CRM Leads", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting leads
            response = self.make_request('GET', '/crm/leads')
            
            if response.status_code == 200:
                self.log_result("CRM Leads", "PASS", "CRM leads retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("CRM Leads", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("CRM Leads", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_email_campaigns(self):
        """Test email marketing campaigns"""
        if not self.auth_token:
            self.log_result("Email Campaigns", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting campaigns
            response = self.make_request('GET', '/email-marketing/campaigns')
            
            if response.status_code == 200:
                self.log_result("Email Campaigns", "PASS", "Email campaigns retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("Email Campaigns", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("Email Campaigns", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_email_templates(self):
        """Test email marketing templates"""
        if not self.auth_token:
            self.log_result("Email Templates", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting templates
            response = self.make_request('GET', '/email-marketing/templates')
            
            if response.status_code == 200:
                self.log_result("Email Templates", "PASS", "Email templates retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("Email Templates", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("Email Templates", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_ecommerce_products(self):
        """Test e-commerce product management"""
        if not self.auth_token:
            self.log_result("E-commerce Products", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting products
            response = self.make_request('GET', '/ecommerce/products')
            
            if response.status_code == 200:
                self.log_result("E-commerce Products", "PASS", "E-commerce products retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("E-commerce Products", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("E-commerce Products", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_ecommerce_orders(self):
        """Test e-commerce order management"""
        if not self.auth_token:
            self.log_result("E-commerce Orders", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting orders
            response = self.make_request('GET', '/ecommerce/orders')
            
            if response.status_code == 200:
                self.log_result("E-commerce Orders", "PASS", "E-commerce orders retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("E-commerce Orders", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("E-commerce Orders", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_courses_management(self):
        """Test course management"""
        if not self.auth_token:
            self.log_result("Course Management", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting courses
            response = self.make_request('GET', '/courses')
            
            if response.status_code == 200:
                self.log_result("Course Management", "PASS", "Courses retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("Course Management", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("Course Management", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_analytics_overview(self):
        """Test analytics overview"""
        if not self.auth_token:
            self.log_result("Analytics Overview", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting analytics overview
            response = self.make_request('GET', '/analytics')
            
            if response.status_code == 200:
                self.log_result("Analytics Overview", "PASS", "Analytics overview retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("Analytics Overview", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("Analytics Overview", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_analytics_reports(self):
        """Test analytics reports"""
        if not self.auth_token:
            self.log_result("Analytics Reports", "SKIP", "No auth token available")
            return False
            
        try:
            # Test getting analytics reports
            response = self.make_request('GET', '/analytics/reports')
            
            if response.status_code == 200:
                self.log_result("Analytics Reports", "PASS", "Analytics reports retrieved successfully")
                return True
            else:
                try:
                    data = response.json()
                except ValueError:
                    data = response.text[:200]
                self.log_result("Analytics Reports", "FAIL", f"Request failed with status {response.status_code}", data)
                return False
                
        except Exception as e:
            self.log_result("Analytics Reports", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_unauthorized_access(self):
        """Test unauthorized access handling"""
        try:
            # Temporarily remove auth token
            original_token = self.auth_token
            self.auth_token = None
            
            response = self.make_request('GET', '/auth/me')
            
            # Restore auth token
            self.auth_token = original_token
            
            if response.status_code == 401:
                self.log_result("Unauthorized Access Handling", "PASS", "401 status returned for unauthorized request")
                return True
            elif response.status_code == 302:
                # Laravel might redirect to login page for unauthenticated requests
                self.log_result("Unauthorized Access Handling", "PASS", "302 redirect returned for unauthorized request (acceptable)")
                return True
            else:
                try:
                    response_data = response.json()
                except:
                    response_data = response.text[:200]  # First 200 chars if not JSON
                self.log_result("Unauthorized Access Handling", "FAIL", f"Expected 401/302 but got {response.status_code}", response_data)
                return False
                
        except Exception as e:
            self.log_result("Unauthorized Access Handling", "FAIL", f"Request failed: {str(e)}")
            return False

    # OAuth Testing Methods
    def test_oauth_google_redirect(self):
        """Test Google OAuth redirect"""
        try:
            response = self.make_request('GET', '/auth/oauth/google')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('redirect_url'):
                    self.log_result("OAuth Google Redirect", "PASS", "Google OAuth redirect URL generated successfully")
                    return True
                else:
                    self.log_result("OAuth Google Redirect", "FAIL", "Response missing redirect_url", data)
                    return False
            else:
                self.log_result("OAuth Google Redirect", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("OAuth Google Redirect", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_oauth_facebook_redirect(self):
        """Test Facebook OAuth redirect"""
        try:
            response = self.make_request('GET', '/auth/oauth/facebook')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('redirect_url'):
                    self.log_result("OAuth Facebook Redirect", "PASS", "Facebook OAuth redirect URL generated successfully")
                    return True
                else:
                    self.log_result("OAuth Facebook Redirect", "FAIL", "Response missing redirect_url", data)
                    return False
            else:
                self.log_result("OAuth Facebook Redirect", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("OAuth Facebook Redirect", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_oauth_apple_redirect(self):
        """Test Apple OAuth redirect"""
        try:
            response = self.make_request('GET', '/auth/oauth/apple')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('redirect_url'):
                    self.log_result("OAuth Apple Redirect", "PASS", "Apple OAuth redirect URL generated successfully")
                    return True
                else:
                    self.log_result("OAuth Apple Redirect", "FAIL", "Response missing redirect_url", data)
                    return False
            else:
                self.log_result("OAuth Apple Redirect", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("OAuth Apple Redirect", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_oauth_twitter_redirect(self):
        """Test Twitter OAuth redirect"""
        try:
            response = self.make_request('GET', '/auth/oauth/twitter')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('redirect_url'):
                    self.log_result("OAuth Twitter Redirect", "PASS", "Twitter OAuth redirect URL generated successfully")
                    return True
                else:
                    self.log_result("OAuth Twitter Redirect", "FAIL", "Response missing redirect_url", data)
                    return False
            else:
                self.log_result("OAuth Twitter Redirect", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("OAuth Twitter Redirect", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_oauth_invalid_provider(self):
        """Test OAuth with invalid provider"""
        try:
            response = self.make_request('GET', '/auth/oauth/invalid_provider')
            
            if response.status_code == 400:
                data = response.json()
                if not data.get('success') and 'Invalid OAuth provider' in data.get('message', ''):
                    self.log_result("OAuth Invalid Provider", "PASS", "Invalid provider properly rejected")
                    return True
                else:
                    self.log_result("OAuth Invalid Provider", "FAIL", "Expected invalid provider error", data)
                    return False
            else:
                self.log_result("OAuth Invalid Provider", "FAIL", f"Expected 400 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("OAuth Invalid Provider", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_oauth_status(self):
        """Test OAuth status endpoint"""
        if not self.auth_token:
            self.log_result("OAuth Status", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/auth/oauth-status')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'oauth_status' in data:
                    oauth_status = data['oauth_status']
                    required_fields = ['linked', 'provider', 'has_password', 'can_unlink']
                    if all(field in oauth_status for field in required_fields):
                        self.log_result("OAuth Status", "PASS", "OAuth status retrieved successfully")
                        return True
                    else:
                        self.log_result("OAuth Status", "FAIL", "Missing required OAuth status fields", data)
                        return False
                else:
                    self.log_result("OAuth Status", "FAIL", "Response missing oauth_status", data)
                    return False
            else:
                self.log_result("OAuth Status", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("OAuth Status", "FAIL", f"Request failed: {str(e)}")
            return False

    # 2FA Testing Methods
    def test_2fa_generate_secret(self):
        """Test 2FA secret generation"""
        if not self.auth_token:
            self.log_result("2FA Generate Secret", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('POST', '/auth/2fa/generate')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('secret') and data.get('qr_code_url'):
                    self.log_result("2FA Generate Secret", "PASS", "2FA secret and QR code generated successfully")
                    return data.get('secret')  # Return secret for further testing
                else:
                    self.log_result("2FA Generate Secret", "FAIL", "Response missing secret or QR code", data)
                    return False
            elif response.status_code == 400:
                data = response.json()
                if '2FA is already enabled' in data.get('message', ''):
                    self.log_result("2FA Generate Secret", "PASS", "2FA already enabled (expected behavior)")
                    return True
                else:
                    self.log_result("2FA Generate Secret", "FAIL", "Unexpected 400 error", data)
                    return False
            else:
                self.log_result("2FA Generate Secret", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("2FA Generate Secret", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_2fa_enable_invalid_code(self):
        """Test 2FA enable with invalid code"""
        if not self.auth_token:
            self.log_result("2FA Enable Invalid Code", "SKIP", "No auth token available")
            return False
            
        try:
            # Try to enable 2FA with invalid code
            invalid_data = {
                "code": "000000"  # Invalid code
            }
            
            response = self.make_request('POST', '/auth/2fa/enable', invalid_data)
            
            if response.status_code == 400:
                data = response.json()
                if not data.get('success') and 'Invalid verification code' in data.get('message', ''):
                    self.log_result("2FA Enable Invalid Code", "PASS", "Invalid 2FA code properly rejected")
                    return True
                else:
                    self.log_result("2FA Enable Invalid Code", "FAIL", "Expected invalid code error", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("2FA Enable Invalid Code", "PASS", "Validation errors returned correctly")
                    return True
                else:
                    self.log_result("2FA Enable Invalid Code", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("2FA Enable Invalid Code", "FAIL", f"Expected 400/422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("2FA Enable Invalid Code", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_2fa_status(self):
        """Test 2FA status endpoint"""
        if not self.auth_token:
            self.log_result("2FA Status", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/auth/2fa/status')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'two_factor_enabled' in data:
                    required_fields = ['two_factor_enabled', 'has_recovery_codes', 'recovery_codes_count']
                    if all(field in data for field in required_fields):
                        self.log_result("2FA Status", "PASS", "2FA status retrieved successfully")
                        return True
                    else:
                        self.log_result("2FA Status", "FAIL", "Missing required 2FA status fields", data)
                        return False
                else:
                    self.log_result("2FA Status", "FAIL", "Response missing 2FA status fields", data)
                    return False
            else:
                self.log_result("2FA Status", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("2FA Status", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_2fa_disable_not_enabled(self):
        """Test 2FA disable when not enabled"""
        if not self.auth_token:
            self.log_result("2FA Disable Not Enabled", "SKIP", "No auth token available")
            return False
            
        try:
            disable_data = {
                "code": "123456"  # Any code
            }
            
            response = self.make_request('POST', '/auth/2fa/disable', disable_data)
            
            if response.status_code == 400:
                data = response.json()
                if not data.get('success') and 'not enabled' in data.get('message', ''):
                    self.log_result("2FA Disable Not Enabled", "PASS", "2FA disable properly rejected when not enabled")
                    return True
                else:
                    self.log_result("2FA Disable Not Enabled", "FAIL", "Expected 'not enabled' error", data)
                    return False
            else:
                self.log_result("2FA Disable Not Enabled", "FAIL", f"Expected 400 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("2FA Disable Not Enabled", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_2fa_recovery_codes_not_enabled(self):
        """Test 2FA recovery codes when not enabled"""
        if not self.auth_token:
            self.log_result("2FA Recovery Codes Not Enabled", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('POST', '/auth/2fa/recovery-codes')
            
            if response.status_code == 400:
                data = response.json()
                if not data.get('success') and 'not enabled' in data.get('message', ''):
                    self.log_result("2FA Recovery Codes Not Enabled", "PASS", "Recovery codes properly rejected when 2FA not enabled")
                    return True
                else:
                    self.log_result("2FA Recovery Codes Not Enabled", "FAIL", "Expected 'not enabled' error", data)
                    return False
            else:
                self.log_result("2FA Recovery Codes Not Enabled", "FAIL", f"Expected 400 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("2FA Recovery Codes Not Enabled", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_enhanced_login_tracking(self):
        """Test enhanced login with tracking"""
        try:
            # Test login to verify tracking fields are updated
            response = self.make_request('POST', '/auth/login', self.admin_credentials)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('token'):
                    # Now get user info to check if tracking fields are present
                    temp_token = data['token']
                    original_token = self.auth_token
                    self.auth_token = temp_token
                    
                    user_response = self.make_request('GET', '/auth/me')
                    self.auth_token = original_token
                    
                    if user_response.status_code == 200:
                        user_data = user_response.json()
                        user = user_data.get('user', {})
                        
                        # Check if user has 2FA fields
                        if 'two_factor_enabled' in user:
                            self.log_result("Enhanced Login Tracking", "PASS", "Login tracking and 2FA fields present")
                            return True
                        else:
                            self.log_result("Enhanced Login Tracking", "FAIL", "Missing 2FA tracking fields", user)
                            return False
                    else:
                        self.log_result("Enhanced Login Tracking", "FAIL", "Could not retrieve user info after login")
                        return False
                else:
                    self.log_result("Enhanced Login Tracking", "FAIL", "Login succeeded but missing token", data)
                    return False
            else:
                self.log_result("Enhanced Login Tracking", "FAIL", f"Login failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Enhanced Login Tracking", "FAIL", f"Request failed: {str(e)}")
            return False

    def run_all_tests(self):
        """Run all API tests"""
        print("🚀 Starting Mewayz Laravel Backend API Tests")
        print("=" * 60)
        
        # Basic connectivity
        if not self.test_server_health():
            print("❌ Server health check failed. Stopping tests.")
            return False
        
        # Authentication tests
        print("\n📝 Testing Authentication System...")
        self.test_auth_register()
        if not self.test_auth_login():
            print("❌ Login failed. Cannot proceed with authenticated tests.")
            return False
            
        self.test_auth_me()
        self.test_auth_profile_update()
        self.test_enhanced_login_tracking()
        
        # OAuth tests
        print("\n🔐 Testing OAuth Authentication...")
        self.test_oauth_google_redirect()
        self.test_oauth_facebook_redirect()
        self.test_oauth_apple_redirect()
        self.test_oauth_twitter_redirect()
        self.test_oauth_invalid_provider()
        self.test_oauth_status()
        
        # 2FA tests
        print("\n🔒 Testing Two-Factor Authentication...")
        self.test_2fa_generate_secret()
        self.test_2fa_enable_invalid_code()
        self.test_2fa_status()
        self.test_2fa_disable_not_enabled()
        self.test_2fa_recovery_codes_not_enabled()
        
        # Workspace tests
        print("\n🏢 Testing Workspace Management...")
        self.test_workspaces_list()
        workspace_id = self.test_workspaces_create()
        
        # Social Media tests
        print("\n📱 Testing Social Media Management...")
        self.test_social_media_accounts()
        social_account_id = self.test_social_media_connect()
        self.test_social_media_analytics()
        
        # Bio Sites tests
        print("\n🔗 Testing Bio Site Management...")
        self.test_bio_sites_list()
        bio_site_id = self.test_bio_sites_create()
        if bio_site_id:
            self.test_bio_sites_show(bio_site_id)
        
        # CRM tests
        print("\n👥 Testing CRM Management...")
        self.test_crm_contacts()
        self.test_crm_leads()
        
        # Email Marketing tests
        print("\n📧 Testing Email Marketing...")
        self.test_email_campaigns()
        self.test_email_templates()
        
        # E-commerce tests
        print("\n🛒 Testing E-commerce Management...")
        self.test_ecommerce_products()
        self.test_ecommerce_orders()
        
        # Course Management tests
        print("\n🎓 Testing Course Management...")
        self.test_courses_management()
        
        # Analytics tests
        print("\n📊 Testing Analytics...")
        self.test_analytics_overview()
        self.test_analytics_reports()
        
        # Error handling tests
        print("\n⚠️ Testing Error Handling...")
        self.test_validation_errors()
        self.test_unauthorized_access()
        
        # Summary
        print("\n" + "=" * 60)
        print("📊 TEST SUMMARY")
        print("=" * 60)
        
        passed = len([r for r in self.results if r['status'] == 'PASS'])
        failed = len([r for r in self.results if r['status'] == 'FAIL'])
        skipped = len([r for r in self.results if r['status'] == 'SKIP'])
        total = len(self.results)
        
        print(f"✅ Passed: {passed}")
        print(f"❌ Failed: {failed}")
        print(f"⚠️ Skipped: {skipped}")
        print(f"📊 Total: {total}")
        
        if failed > 0:
            print(f"\n❌ FAILED TESTS:")
            for result in self.results:
                if result['status'] == 'FAIL':
                    print(f"   • {result['test']}: {result['details']}")
        
        success_rate = (passed / (total - skipped)) * 100 if (total - skipped) > 0 else 0
        print(f"\n🎯 Success Rate: {success_rate:.1f}%")
        
        return failed == 0

if __name__ == "__main__":
    tester = MewayzAPITester()
    success = tester.run_all_tests()
    sys.exit(0 if success else 1)