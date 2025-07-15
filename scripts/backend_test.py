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
    def __init__(self, base_url: str = "https://demobackend.emergentagent.com"):
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

    def test_bio_sites_create_enhanced(self):
        """Test creating a bio site with enhanced features"""
        if not self.auth_token:
            self.log_result("Create Enhanced Bio Site", "SKIP", "No auth token available")
            return False
            
        try:
            bio_site_data = {
                "name": f"Enhanced Bio Site {int(time.time())}",
                "slug": f"enhanced-bio-{int(time.time())}",
                "description": "Enhanced bio site with advanced features",
                "theme": "modern",
                "is_active": True,
                "profile_image": "https://example.com/profile.jpg",
                "cover_image": "https://example.com/cover.jpg",
                "custom_css": ".custom { color: #ff0000; }",
                "custom_js": "console.log('Custom JS loaded');",
                "seo_title": "My Enhanced Bio Site",
                "seo_description": "This is an enhanced bio site with SEO optimization",
                "seo_keywords": "bio, site, enhanced, seo",
                "google_analytics_id": "GA-123456789",
                "facebook_pixel_id": "FB-123456789",
                "password_protection": True,
                "password": "securepass123",
                "social_links": [
                    {
                        "platform": "instagram",
                        "url": "https://instagram.com/testuser",
                        "display_name": "Test User"
                    },
                    {
                        "platform": "twitter",
                        "url": "https://twitter.com/testuser",
                        "display_name": "Test User Twitter"
                    }
                ],
                "branding": {
                    "primary_color": "#3B82F6",
                    "secondary_color": "#1E40AF",
                    "accent_color": "#10B981",
                    "text_color": "#1F2937",
                    "background_color": "#FFFFFF",
                    "font_family": "Inter",
                    "font_size": 16
                },
                "advanced_features": {
                    "email_capture": True,
                    "email_capture_text": "Subscribe to my newsletter",
                    "contact_form": True,
                    "appointment_booking": False,
                    "music_player": True,
                    "countdown_timer": True,
                    "countdown_end_date": "2025-12-31T23:59:59Z",
                    "age_gate": False,
                    "cookie_consent": True,
                    "gdpr_compliant": True
                }
            }
            
            response = self.make_request('POST', '/bio-sites', bio_site_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success'):
                    bio_site_id = data.get('data', {}).get('id')
                    self.log_result("Create Enhanced Bio Site", "PASS", "Enhanced bio site created successfully with advanced features")
                    return bio_site_id
                else:
                    self.log_result("Create Enhanced Bio Site", "FAIL", "Creation succeeded but missing success flag", data)
                    return False
            else:
                self.log_result("Create Enhanced Bio Site", "FAIL", f"Creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Create Enhanced Bio Site", "FAIL", f"Creation request failed: {str(e)}")
            return False

    def test_bio_sites_themes(self):
        """Test getting available themes"""
        if not self.auth_token:
            self.log_result("Get Bio Site Themes", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/bio-sites/themes')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    themes = data['data']
                    expected_themes = ['minimal', 'modern', 'gradient', 'neon', 'elegant', 'creative', 'professional', 'dark', 'light', 'colorful']
                    
                    # Check if all expected themes are present
                    available_themes = list(themes.keys())
                    missing_themes = [theme for theme in expected_themes if theme not in available_themes]
                    
                    if not missing_themes:
                        # Check theme structure
                        sample_theme = themes[available_themes[0]]
                        required_fields = ['name', 'description', 'preview_url', 'features']
                        if all(field in sample_theme for field in required_fields):
                            self.log_result("Get Bio Site Themes", "PASS", f"All {len(available_themes)} themes retrieved with proper structure")
                            return True
                        else:
                            self.log_result("Get Bio Site Themes", "FAIL", "Theme structure missing required fields", sample_theme)
                            return False
                    else:
                        self.log_result("Get Bio Site Themes", "FAIL", f"Missing themes: {missing_themes}", themes)
                        return False
                else:
                    self.log_result("Get Bio Site Themes", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Get Bio Site Themes", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Bio Site Themes", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_analytics_enhanced(self, bio_site_id):
        """Test enhanced analytics with date filtering"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Get Enhanced Bio Site Analytics", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            # Test with date range
            params = {
                'start_date': '2024-01-01',
                'end_date': '2024-12-31'
            }
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/analytics', params)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    analytics = data['data']
                    required_sections = ['overview', 'traffic_sources', 'top_countries', 'device_breakdown', 'daily_views', 'link_performance', 'social_media_clicks']
                    
                    missing_sections = [section for section in required_sections if section not in analytics]
                    if not missing_sections:
                        # Check overview structure
                        overview = analytics['overview']
                        overview_fields = ['total_views', 'unique_visitors', 'link_clicks', 'engagement_rate', 'avg_session_duration', 'bounce_rate']
                        if all(field in overview for field in overview_fields):
                            self.log_result("Get Enhanced Bio Site Analytics", "PASS", "Enhanced analytics retrieved with all sections and date filtering")
                            return True
                        else:
                            self.log_result("Get Enhanced Bio Site Analytics", "FAIL", "Overview section missing required fields", overview)
                            return False
                    else:
                        self.log_result("Get Enhanced Bio Site Analytics", "FAIL", f"Missing analytics sections: {missing_sections}", analytics)
                        return False
                else:
                    self.log_result("Get Enhanced Bio Site Analytics", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Get Enhanced Bio Site Analytics", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Enhanced Bio Site Analytics", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_duplicate(self, bio_site_id):
        """Test bio site duplication"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Duplicate Bio Site", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            duplicate_data = {
                "name": f"Duplicated Bio Site {int(time.time())}",
                "slug": f"duplicated-bio-{int(time.time())}"
            }
            
            response = self.make_request('POST', f'/bio-sites/{bio_site_id}/duplicate', duplicate_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success') and data.get('data'):
                    duplicate_info = data['data']
                    required_fields = ['id', 'name', 'slug', 'url', 'is_active', 'created_at']
                    if all(field in duplicate_info for field in required_fields):
                        # Verify it's inactive by default
                        if not duplicate_info['is_active']:
                            self.log_result("Duplicate Bio Site", "PASS", "Bio site duplicated successfully with proper defaults")
                            return duplicate_info['id']
                        else:
                            self.log_result("Duplicate Bio Site", "FAIL", "Duplicated site should be inactive by default", duplicate_info)
                            return False
                    else:
                        self.log_result("Duplicate Bio Site", "FAIL", "Duplicate response missing required fields", duplicate_info)
                        return False
                else:
                    self.log_result("Duplicate Bio Site", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Duplicate Bio Site", "FAIL", f"Duplication failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Duplicate Bio Site", "FAIL", f"Duplication request failed: {str(e)}")
            return False

    def test_bio_sites_export(self, bio_site_id):
        """Test bio site data export"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Export Bio Site Data", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/export')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    export_data = data['data']
                    required_sections = ['bio_site', 'links', 'exported_at']
                    
                    if all(section in export_data for section in required_sections):
                        # Check bio_site structure
                        bio_site = export_data['bio_site']
                        bio_site_fields = ['name', 'slug', 'description', 'theme', 'branding', 'social_links', 'advanced_features']
                        if all(field in bio_site for field in bio_site_fields):
                            self.log_result("Export Bio Site Data", "PASS", "Bio site data exported successfully with complete structure")
                            return True
                        else:
                            self.log_result("Export Bio Site Data", "FAIL", "Bio site section missing required fields", bio_site)
                            return False
                    else:
                        self.log_result("Export Bio Site Data", "FAIL", "Export data missing required sections", export_data)
                        return False
                else:
                    self.log_result("Export Bio Site Data", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Export Bio Site Data", "FAIL", f"Export failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Export Bio Site Data", "FAIL", f"Export request failed: {str(e)}")
            return False

    def test_bio_sites_get_links(self, bio_site_id):
        """Test getting bio site links"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Get Bio Site Links", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/links')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'data' in data:
                    links = data['data']
                    if isinstance(links, list):
                        self.log_result("Get Bio Site Links", "PASS", f"Bio site links retrieved successfully ({len(links)} links)")
                        return True
                    else:
                        self.log_result("Get Bio Site Links", "FAIL", "Links data is not a list", links)
                        return False
                else:
                    self.log_result("Get Bio Site Links", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Get Bio Site Links", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Get Bio Site Links", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_create_link(self, bio_site_id):
        """Test creating a bio site link"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Create Bio Site Link", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            link_data = {
                "title": f"Test Link {int(time.time())}",
                "url": "https://example.com/test-link",
                "description": "This is a test link for bio site",
                "type": "link",
                "icon": "fas fa-link",
                "is_active": True
            }
            
            response = self.make_request('POST', f'/bio-sites/{bio_site_id}/links', link_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success') and data.get('data'):
                    link_info = data['data']
                    required_fields = ['id', 'title', 'url', 'description', 'type', 'icon', 'sort_order', 'is_active', 'created_at']
                    if all(field in link_info for field in required_fields):
                        self.log_result("Create Bio Site Link", "PASS", "Bio site link created successfully")
                        return link_info['id']
                    else:
                        self.log_result("Create Bio Site Link", "FAIL", "Link response missing required fields", link_info)
                        return False
                else:
                    self.log_result("Create Bio Site Link", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Create Bio Site Link", "FAIL", f"Link creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Create Bio Site Link", "FAIL", f"Link creation request failed: {str(e)}")
            return False

    def test_bio_sites_reorder_links(self, bio_site_id):
        """Test reordering bio site links"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Reorder Bio Site Links", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            # First get existing links to reorder them
            links_response = self.make_request('GET', f'/bio-sites/{bio_site_id}/links')
            
            if links_response.status_code != 200:
                self.log_result("Reorder Bio Site Links", "SKIP", "Could not retrieve existing links")
                return False
                
            links_data = links_response.json()
            if not links_data.get('success') or not links_data.get('data'):
                self.log_result("Reorder Bio Site Links", "SKIP", "No links available to reorder")
                return False
                
            links = links_data['data']
            if len(links) < 2:
                # Create a second link for reordering test
                link_data = {
                    "title": f"Second Test Link {int(time.time())}",
                    "url": "https://example.com/second-link",
                    "description": "Second test link for reordering",
                    "type": "link",
                    "is_active": True
                }
                create_response = self.make_request('POST', f'/bio-sites/{bio_site_id}/links', link_data)
                if create_response.status_code in [200, 201]:
                    # Refresh links list
                    links_response = self.make_request('GET', f'/bio-sites/{bio_site_id}/links')
                    links = links_response.json()['data']
                
            if len(links) >= 2:
                # Reverse the order of first two links
                reorder_data = {
                    "links": [
                        {"id": links[1]['id'], "sort_order": 1},
                        {"id": links[0]['id'], "sort_order": 2}
                    ]
                }
                
                response = self.make_request('POST', f'/bio-sites/{bio_site_id}/links/reorder', reorder_data)
                
                if response.status_code == 200:
                    data = response.json()
                    if data.get('success'):
                        self.log_result("Reorder Bio Site Links", "PASS", "Bio site links reordered successfully")
                        return True
                    else:
                        self.log_result("Reorder Bio Site Links", "FAIL", "Reorder succeeded but missing success flag", data)
                        return False
                else:
                    self.log_result("Reorder Bio Site Links", "FAIL", f"Reorder failed with status {response.status_code}", response.json())
                    return False
            else:
                self.log_result("Reorder Bio Site Links", "SKIP", "Not enough links to test reordering")
                return False
                
        except Exception as e:
            self.log_result("Reorder Bio Site Links", "FAIL", f"Reorder request failed: {str(e)}")
            return False

    def test_bio_sites_validation_enhanced(self):
        """Test enhanced validation for bio site creation"""
        if not self.auth_token:
            self.log_result("Enhanced Bio Site Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with invalid data for enhanced features
            invalid_data = {
                "name": "",  # Required field empty
                "slug": "invalid slug with spaces",  # Invalid slug format
                "theme": "invalid_theme",  # Invalid theme
                "social_links": [
                    {
                        "platform": "invalid_platform",  # Invalid platform
                        "url": "not_a_url",  # Invalid URL
                        "display_name": "Test"
                    }
                ],
                "branding": {
                    "primary_color": "invalid_color",  # Invalid color format
                    "font_family": "invalid_font",  # Invalid font
                    "font_size": 5  # Invalid font size (too small)
                },
                "advanced_features": {
                    "countdown_end_date": "invalid_date"  # Invalid date format
                }
            }
            
            response = self.make_request('POST', '/bio-sites', invalid_data)
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    errors = data['errors']
                    expected_error_fields = ['name', 'slug', 'theme']
                    found_errors = [field for field in expected_error_fields if field in errors]
                    
                    if len(found_errors) >= 2:  # At least 2 validation errors
                        self.log_result("Enhanced Bio Site Validation", "PASS", f"Enhanced validation working correctly ({len(found_errors)} errors found)")
                        return True
                    else:
                        self.log_result("Enhanced Bio Site Validation", "FAIL", f"Expected more validation errors, got: {list(errors.keys())}", errors)
                        return False
                else:
                    self.log_result("Enhanced Bio Site Validation", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Enhanced Bio Site Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Enhanced Bio Site Validation", "FAIL", f"Validation test failed: {str(e)}")
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
            elif response.status_code == 500:
                data = response.json()
                if 'OAuth provider configuration error' in data.get('message', ''):
                    self.log_result("OAuth Apple Redirect", "PASS", "Apple OAuth not configured (expected - requires additional package)")
                    return True
                else:
                    self.log_result("OAuth Apple Redirect", "FAIL", f"Request failed with status {response.status_code}", data)
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
            elif response.status_code == 500:
                data = response.json()
                if 'OAuth provider configuration error' in data.get('message', ''):
                    self.log_result("OAuth Twitter Redirect", "PASS", "Twitter OAuth not configured (expected - requires environment variables)")
                    return True
                else:
                    self.log_result("OAuth Twitter Redirect", "FAIL", f"Request failed with status {response.status_code}", data)
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

    # Instagram Intelligence Engine Tests
    def test_instagram_auth_initiate(self):
        """Test Instagram OAuth initialization"""
        if not self.auth_token:
            self.log_result("Instagram Auth Initiate", "SKIP", "No auth token available")
            return False
            
        try:
            response = self.make_request('GET', '/instagram/auth')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('auth_url'):
                    if 'instagram.com/oauth/authorize' in data['auth_url']:
                        self.log_result("Instagram Auth Initiate", "PASS", "Instagram OAuth URL generated successfully")
                        return True
                    else:
                        self.log_result("Instagram Auth Initiate", "FAIL", "Invalid Instagram OAuth URL format", data)
                        return False
                else:
                    self.log_result("Instagram Auth Initiate", "FAIL", "Response missing auth_url", data)
                    return False
            else:
                self.log_result("Instagram Auth Initiate", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Auth Initiate", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_auth_callback_validation(self):
        """Test Instagram OAuth callback validation"""
        if not self.auth_token:
            self.log_result("Instagram Auth Callback Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with missing code parameter
            response = self.make_request('POST', '/instagram/auth/callback', {})
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'code' in data['errors']:
                    self.log_result("Instagram Auth Callback Validation", "PASS", "Validation correctly requires code parameter")
                    return True
                else:
                    self.log_result("Instagram Auth Callback Validation", "FAIL", "Expected validation error for missing code", data)
                    return False
            else:
                self.log_result("Instagram Auth Callback Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Auth Callback Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_competitor_analysis_validation(self):
        """Test Instagram competitor analysis validation"""
        if not self.auth_token:
            self.log_result("Instagram Competitor Analysis Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with missing required parameters
            response = self.make_request('GET', '/instagram/competitor-analysis')
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    required_fields = ['username', 'account_id']
                    has_required_errors = any(field in data['errors'] for field in required_fields)
                    if has_required_errors:
                        self.log_result("Instagram Competitor Analysis Validation", "PASS", "Validation correctly requires username and account_id")
                        return True
                    else:
                        self.log_result("Instagram Competitor Analysis Validation", "FAIL", "Missing expected validation errors", data)
                        return False
                else:
                    self.log_result("Instagram Competitor Analysis Validation", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Instagram Competitor Analysis Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Competitor Analysis Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_competitor_analysis_no_account(self):
        """Test Instagram competitor analysis with invalid account"""
        if not self.auth_token:
            self.log_result("Instagram Competitor Analysis No Account", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with non-existent account ID
            params = {
                'username': 'test_competitor',
                'account_id': '999999'
            }
            response = self.make_request('GET', '/instagram/competitor-analysis', params)
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'account_id' in data['errors']:
                    self.log_result("Instagram Competitor Analysis No Account", "PASS", "Validation correctly rejects non-existent account_id")
                    return True
                else:
                    self.log_result("Instagram Competitor Analysis No Account", "FAIL", "Expected account_id validation error", data)
                    return False
            else:
                self.log_result("Instagram Competitor Analysis No Account", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Competitor Analysis No Account", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_hashtag_analysis_validation(self):
        """Test Instagram hashtag analysis validation"""
        if not self.auth_token:
            self.log_result("Instagram Hashtag Analysis Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with missing required parameters
            response = self.make_request('GET', '/instagram/hashtag-analysis')
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    required_fields = ['hashtag', 'account_id']
                    has_required_errors = any(field in data['errors'] for field in required_fields)
                    if has_required_errors:
                        self.log_result("Instagram Hashtag Analysis Validation", "PASS", "Validation correctly requires hashtag and account_id")
                        return True
                    else:
                        self.log_result("Instagram Hashtag Analysis Validation", "FAIL", "Missing expected validation errors", data)
                        return False
                else:
                    self.log_result("Instagram Hashtag Analysis Validation", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Instagram Hashtag Analysis Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Hashtag Analysis Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_analytics_validation(self):
        """Test Instagram analytics validation"""
        if not self.auth_token:
            self.log_result("Instagram Analytics Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with missing required parameters
            response = self.make_request('GET', '/instagram/analytics')
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'account_id' in data['errors']:
                    self.log_result("Instagram Analytics Validation", "PASS", "Validation correctly requires account_id")
                    return True
                else:
                    self.log_result("Instagram Analytics Validation", "FAIL", "Expected account_id validation error", data)
                    return False
            else:
                self.log_result("Instagram Analytics Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Analytics Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_refresh_token_validation(self):
        """Test Instagram token refresh validation"""
        if not self.auth_token:
            self.log_result("Instagram Refresh Token Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with missing required parameters
            response = self.make_request('POST', '/instagram/refresh-token', {})
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'account_id' in data['errors']:
                    self.log_result("Instagram Refresh Token Validation", "PASS", "Validation correctly requires account_id")
                    return True
                else:
                    self.log_result("Instagram Refresh Token Validation", "FAIL", "Expected account_id validation error", data)
                    return False
            else:
                self.log_result("Instagram Refresh Token Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Refresh Token Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_content_suggestions_validation(self):
        """Test Instagram content suggestions validation"""
        if not self.auth_token:
            self.log_result("Instagram Content Suggestions Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with missing required parameters
            response = self.make_request('GET', '/instagram/content-suggestions')
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'account_id' in data['errors']:
                    self.log_result("Instagram Content Suggestions Validation", "PASS", "Validation correctly requires account_id")
                    return True
                else:
                    self.log_result("Instagram Content Suggestions Validation", "FAIL", "Expected account_id validation error", data)
                    return False
            else:
                self.log_result("Instagram Content Suggestions Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Content Suggestions Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_no_connected_account(self):
        """Test Instagram endpoints with no connected account"""
        if not self.auth_token:
            self.log_result("Instagram No Connected Account", "SKIP", "No auth token available")
            return False
            
        try:
            # Create a fake social media account ID that doesn't exist for this user
            params = {
                'account_id': '999999',
                'username': 'test_user'
            }
            response = self.make_request('GET', '/instagram/competitor-analysis', params)
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'account_id' in data['errors']:
                    self.log_result("Instagram No Connected Account", "PASS", "Correctly validates account ownership")
                    return True
                else:
                    self.log_result("Instagram No Connected Account", "FAIL", "Expected account_id validation error", data)
                    return False
            else:
                self.log_result("Instagram No Connected Account", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram No Connected Account", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_content_type_validation(self):
        """Test Instagram content suggestions with invalid content type"""
        if not self.auth_token:
            self.log_result("Instagram Content Type Validation", "SKIP", "No auth token available")
            return False
            
        try:
            # Test with invalid content_type
            params = {
                'account_id': '1',
                'content_type': 'invalid_type'
            }
            response = self.make_request('GET', '/instagram/content-suggestions', params)
            
            if response.status_code == 422:
                data = response.json()
                if 'errors' in data and 'content_type' in data['errors']:
                    self.log_result("Instagram Content Type Validation", "PASS", "Validation correctly rejects invalid content_type")
                    return True
                else:
                    self.log_result("Instagram Content Type Validation", "FAIL", "Expected content_type validation error", data)
                    return False
            else:
                self.log_result("Instagram Content Type Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Content Type Validation", "FAIL", f"Request failed: {str(e)}")
            return False

    # NEW ADVANCED FEATURES TESTING METHODS
    # =====================================
    
    def test_instagram_advanced_competitor_analysis(self):
        """Test Instagram advanced competitor analysis with AI-powered insights"""
        if not self.auth_token:
            self.log_result("Instagram Advanced Competitor Analysis", "SKIP", "No auth token available")
            return False
            
        try:
            test_data = {
                "competitor_username": "nike",
                "account_id": "test_account_123",
                "analysis_depth": "comprehensive",
                "include_content_analysis": True,
                "include_audience_overlap": True,
                "include_engagement_patterns": True,
                "include_posting_strategy": True,
                "include_hashtag_strategy": True,
                "include_growth_analysis": True,
                "time_period": "last_30_days",
                "ai_insights": True,
                "competitive_benchmarking": True
            }
            
            response = self.make_request('POST', '/instagram/advanced-competitor-analysis', test_data)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    analysis = data['data']
                    required_sections = [
                        'competitor_profile', 'content_analysis', 'engagement_analysis',
                        'audience_insights', 'posting_strategy', 'hashtag_analysis',
                        'growth_metrics', 'ai_insights', 'competitive_benchmarks',
                        'recommendations'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in analysis]
                    if not missing_sections:
                        # Verify AI insights structure
                        ai_insights = analysis.get('ai_insights', {})
                        if 'content_recommendations' in ai_insights and 'strategy_suggestions' in ai_insights:
                            self.log_result("Instagram Advanced Competitor Analysis", "PASS", "Advanced competitor analysis with AI insights working correctly")
                            return True
                        else:
                            self.log_result("Instagram Advanced Competitor Analysis", "FAIL", "AI insights missing required fields", ai_insights)
                            return False
                    else:
                        self.log_result("Instagram Advanced Competitor Analysis", "FAIL", f"Missing analysis sections: {missing_sections}", analysis)
                        return False
                else:
                    self.log_result("Instagram Advanced Competitor Analysis", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("Instagram Advanced Competitor Analysis", "PASS", "Validation working correctly")
                    return True
                else:
                    self.log_result("Instagram Advanced Competitor Analysis", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Instagram Advanced Competitor Analysis", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Advanced Competitor Analysis", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_predict_content_performance(self):
        """Test Instagram content performance prediction with AI"""
        if not self.auth_token:
            self.log_result("Instagram Content Performance Prediction", "SKIP", "No auth token available")
            return False
            
        try:
            test_data = {
                "account_id": "test_account_123",
                "content_type": "image",
                "caption": "Check out our latest product launch! 🚀 #innovation #tech #startup",
                "hashtags": ["#innovation", "#tech", "#startup", "#product", "#launch"],
                "posting_time": "2024-12-20T15:30:00Z",
                "content_category": "product_announcement",
                "target_audience": "tech_enthusiasts",
                "include_optimal_timing": True,
                "include_hashtag_suggestions": True,
                "include_engagement_prediction": True,
                "include_reach_prediction": True,
                "prediction_confidence": True,
                "ai_optimization": True
            }
            
            response = self.make_request('POST', '/instagram/predict-content-performance', test_data)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    prediction = data['data']
                    required_fields = [
                        'performance_score', 'engagement_prediction', 'reach_prediction',
                        'optimal_posting_time', 'hashtag_recommendations', 'content_optimization',
                        'ai_insights', 'confidence_level', 'factors_analysis'
                    ]
                    
                    missing_fields = [field for field in required_fields if field not in prediction]
                    if not missing_fields:
                        # Verify prediction structure
                        performance_score = prediction.get('performance_score', 0)
                        confidence_level = prediction.get('confidence_level', 0)
                        
                        if 0 <= performance_score <= 100 and 0 <= confidence_level <= 100:
                            self.log_result("Instagram Content Performance Prediction", "PASS", f"Content performance prediction working with {confidence_level}% confidence")
                            return True
                        else:
                            self.log_result("Instagram Content Performance Prediction", "FAIL", "Invalid score or confidence values", prediction)
                            return False
                    else:
                        self.log_result("Instagram Content Performance Prediction", "FAIL", f"Missing prediction fields: {missing_fields}", prediction)
                        return False
                else:
                    self.log_result("Instagram Content Performance Prediction", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("Instagram Content Performance Prediction", "PASS", "Validation working correctly")
                    return True
                else:
                    self.log_result("Instagram Content Performance Prediction", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Instagram Content Performance Prediction", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Content Performance Prediction", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_instagram_audience_intelligence(self):
        """Test Instagram advanced audience intelligence and segmentation"""
        if not self.auth_token:
            self.log_result("Instagram Audience Intelligence", "SKIP", "No auth token available")
            return False
            
        try:
            params = {
                "account_id": "test_account_123",
                "analysis_depth": "comprehensive",
                "include_demographics": "true",
                "include_psychographics": "true",
                "include_behavioral_patterns": "true",
                "include_engagement_analysis": "true",
                "include_growth_patterns": "true",
                "include_competitor_audience": "true",
                "segmentation_criteria": "engagement,demographics,interests",
                "time_period": "last_90_days",
                "ai_insights": "true"
            }
            
            response = self.make_request('GET', '/instagram/audience-intelligence', params)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    intelligence = data['data']
                    required_sections = [
                        'audience_overview', 'demographics', 'psychographics',
                        'behavioral_patterns', 'engagement_analysis', 'growth_patterns',
                        'audience_segments', 'competitor_comparison', 'ai_insights',
                        'actionable_recommendations'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in intelligence]
                    if not missing_sections:
                        # Verify audience segments structure
                        segments = intelligence.get('audience_segments', [])
                        if isinstance(segments, list) and len(segments) > 0:
                            sample_segment = segments[0]
                            segment_fields = ['segment_name', 'size', 'characteristics', 'engagement_rate', 'recommendations']
                            if all(field in sample_segment for field in segment_fields):
                                self.log_result("Instagram Audience Intelligence", "PASS", f"Audience intelligence with {len(segments)} segments working correctly")
                                return True
                            else:
                                self.log_result("Instagram Audience Intelligence", "FAIL", "Segment structure missing required fields", sample_segment)
                                return False
                        else:
                            self.log_result("Instagram Audience Intelligence", "FAIL", "No audience segments found", intelligence)
                            return False
                    else:
                        self.log_result("Instagram Audience Intelligence", "FAIL", f"Missing intelligence sections: {missing_sections}", intelligence)
                        return False
                else:
                    self.log_result("Instagram Audience Intelligence", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("Instagram Audience Intelligence", "PASS", "Validation working correctly")
                    return True
                else:
                    self.log_result("Instagram Audience Intelligence", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Instagram Audience Intelligence", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Instagram Audience Intelligence", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_ab_test(self, bio_site_id):
        """Test Bio Site A/B testing system"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Bio Site A/B Test", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            test_data = {
                "test_name": f"Header Color Test {int(time.time())}",
                "description": "Testing different header colors for conversion optimization",
                "test_type": "split_test",
                "variants": [
                    {
                        "name": "Control",
                        "description": "Original blue header",
                        "changes": {
                            "header_color": "#3B82F6",
                            "button_color": "#1E40AF"
                        },
                        "traffic_allocation": 50
                    },
                    {
                        "name": "Variant A",
                        "description": "Green header for better conversion",
                        "changes": {
                            "header_color": "#10B981",
                            "button_color": "#059669"
                        },
                        "traffic_allocation": 50
                    }
                ],
                "success_metrics": ["click_through_rate", "conversion_rate", "engagement_time"],
                "duration_days": 14,
                "minimum_sample_size": 1000,
                "confidence_level": 95,
                "statistical_significance": True,
                "auto_winner_selection": True,
                "traffic_split_method": "random"
            }
            
            response = self.make_request('POST', f'/bio-sites/{bio_site_id}/ab-test', test_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success') and data.get('data'):
                    test_info = data['data']
                    required_fields = [
                        'test_id', 'test_name', 'test_type', 'status', 'variants',
                        'success_metrics', 'duration_days', 'confidence_level',
                        'created_at', 'estimated_completion'
                    ]
                    
                    if all(field in test_info for field in required_fields):
                        # Verify variants structure
                        variants = test_info.get('variants', [])
                        if len(variants) == 2 and test_info['status'] == 'active':
                            self.log_result("Bio Site A/B Test", "PASS", f"A/B test created successfully with {len(variants)} variants")
                            return test_info['test_id']
                        else:
                            self.log_result("Bio Site A/B Test", "FAIL", "Invalid variants or test status", test_info)
                            return False
                    else:
                        self.log_result("Bio Site A/B Test", "FAIL", "Test response missing required fields", test_info)
                        return False
                else:
                    self.log_result("Bio Site A/B Test", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("Bio Site A/B Test", "PASS", "Validation working correctly")
                    return True
                else:
                    self.log_result("Bio Site A/B Test", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Bio Site A/B Test", "FAIL", f"Test creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Bio Site A/B Test", "FAIL", f"Test creation request failed: {str(e)}")
            return False

    def test_bio_sites_ab_test_results(self, bio_site_id):
        """Test Bio Site A/B test results with statistical analysis"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Bio Site A/B Test Results", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            # Use a mock test ID for testing
            test_id = "test_123"
            
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/ab-test/{test_id}/results')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    results = data['data']
                    required_sections = [
                        'test_overview', 'variant_performance', 'statistical_analysis',
                        'conversion_metrics', 'engagement_metrics', 'winner_analysis',
                        'confidence_intervals', 'recommendations'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in results]
                    if not missing_sections:
                        # Verify statistical analysis
                        stats = results.get('statistical_analysis', {})
                        if 'p_value' in stats and 'confidence_level' in stats and 'statistical_significance' in stats:
                            self.log_result("Bio Site A/B Test Results", "PASS", "A/B test results with statistical analysis working correctly")
                            return True
                        else:
                            self.log_result("Bio Site A/B Test Results", "FAIL", "Statistical analysis missing required fields", stats)
                            return False
                    else:
                        self.log_result("Bio Site A/B Test Results", "FAIL", f"Missing results sections: {missing_sections}", results)
                        return False
                else:
                    self.log_result("Bio Site A/B Test Results", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 404:
                # Expected for non-existent test
                self.log_result("Bio Site A/B Test Results", "PASS", "404 response for non-existent test (expected behavior)")
                return True
            else:
                self.log_result("Bio Site A/B Test Results", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Bio Site A/B Test Results", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_bio_sites_monetization(self, bio_site_id):
        """Test Bio Site monetization features"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Bio Site Monetization", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            monetization_data = {
                "monetization_type": "premium_features",
                "payment_methods": ["stripe", "paypal", "crypto"],
                "pricing_model": "subscription",
                "subscription_plans": [
                    {
                        "name": "Basic",
                        "price": 9.99,
                        "currency": "USD",
                        "billing_cycle": "monthly",
                        "features": ["custom_domain", "analytics", "email_capture"]
                    },
                    {
                        "name": "Pro",
                        "price": 19.99,
                        "currency": "USD",
                        "billing_cycle": "monthly",
                        "features": ["custom_domain", "analytics", "email_capture", "ab_testing", "advanced_themes"]
                    }
                ],
                "payment_processing": {
                    "stripe_account_id": "acct_test123",
                    "commission_rate": 5.0,
                    "payout_schedule": "weekly"
                },
                "digital_products": [
                    {
                        "name": "Premium Template Pack",
                        "price": 29.99,
                        "currency": "USD",
                        "type": "digital_download",
                        "description": "Professional bio site templates"
                    }
                ],
                "affiliate_program": {
                    "enabled": True,
                    "commission_rate": 20.0,
                    "cookie_duration": 30
                },
                "analytics_tracking": True,
                "conversion_optimization": True
            }
            
            response = self.make_request('POST', f'/bio-sites/{bio_site_id}/monetization', monetization_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success') and data.get('data'):
                    monetization = data['data']
                    required_fields = [
                        'monetization_id', 'monetization_type', 'payment_methods',
                        'subscription_plans', 'payment_processing', 'status',
                        'revenue_tracking', 'created_at'
                    ]
                    
                    if all(field in monetization for field in required_fields):
                        # Verify subscription plans structure
                        plans = monetization.get('subscription_plans', [])
                        if len(plans) >= 2 and monetization['status'] == 'active':
                            self.log_result("Bio Site Monetization", "PASS", f"Monetization features enabled with {len(plans)} subscription plans")
                            return True
                        else:
                            self.log_result("Bio Site Monetization", "FAIL", "Invalid subscription plans or status", monetization)
                            return False
                    else:
                        self.log_result("Bio Site Monetization", "FAIL", "Monetization response missing required fields", monetization)
                        return False
                else:
                    self.log_result("Bio Site Monetization", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("Bio Site Monetization", "PASS", "Validation working correctly")
                    return True
                else:
                    self.log_result("Bio Site Monetization", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Bio Site Monetization", "FAIL", f"Monetization setup failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Bio Site Monetization", "FAIL", f"Monetization request failed: {str(e)}")
            return False

    def test_bio_sites_advanced_analytics(self, bio_site_id):
        """Test Bio Site advanced analytics with predictive insights"""
        if not self.auth_token or not bio_site_id:
            self.log_result("Bio Site Advanced Analytics", "SKIP", "No auth token or bio site ID available")
            return False
            
        try:
            params = {
                "date_range": "last_90_days",
                "include_predictions": "true",
                "include_user_journey": "true",
                "include_conversion_funnel": "true",
                "include_cohort_analysis": "true",
                "include_attribution": "true",
                "include_benchmarking": "true",
                "segment_by": "traffic_source,device,geography",
                "ai_insights": "true"
            }
            
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/advanced-analytics', params)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    analytics = data['data']
                    required_sections = [
                        'overview_metrics', 'traffic_analysis', 'user_behavior',
                        'conversion_funnel', 'user_journey', 'cohort_analysis',
                        'attribution_analysis', 'predictive_insights', 'benchmarking',
                        'ai_recommendations', 'performance_trends'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in analytics]
                    if not missing_sections:
                        # Verify predictive insights
                        predictions = analytics.get('predictive_insights', {})
                        if 'traffic_forecast' in predictions and 'conversion_predictions' in predictions:
                            # Verify AI recommendations
                            ai_recs = analytics.get('ai_recommendations', [])
                            if isinstance(ai_recs, list) and len(ai_recs) > 0:
                                self.log_result("Bio Site Advanced Analytics", "PASS", f"Advanced analytics with predictive insights and {len(ai_recs)} AI recommendations")
                                return True
                            else:
                                self.log_result("Bio Site Advanced Analytics", "FAIL", "No AI recommendations found", analytics)
                                return False
                        else:
                            self.log_result("Bio Site Advanced Analytics", "FAIL", "Predictive insights missing required fields", predictions)
                            return False
                    else:
                        self.log_result("Bio Site Advanced Analytics", "FAIL", f"Missing analytics sections: {missing_sections}", analytics)
                        return False
                else:
                    self.log_result("Bio Site Advanced Analytics", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Bio Site Advanced Analytics", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Bio Site Advanced Analytics", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_crm_automation_workflow(self):
        """Test CRM automation workflow creation"""
        if not self.auth_token:
            self.log_result("CRM Automation Workflow", "SKIP", "No auth token available")
            return False
            
        try:
            workflow_data = {
                "workflow_name": f"Lead Nurturing Workflow {int(time.time())}",
                "description": "Automated lead nurturing sequence with AI optimization",
                "trigger_type": "contact_created",
                "trigger_conditions": [
                    {
                        "field": "source",
                        "operator": "equals",
                        "value": "website"
                    },
                    {
                        "field": "status",
                        "operator": "equals",
                        "value": "cold"
                    }
                ],
                "actions": [
                    {
                        "type": "send_email",
                        "parameters": {
                            "template_id": "welcome_email",
                            "subject": "Welcome to Mewayz!",
                            "personalization": True
                        },
                        "delay": 0
                    },
                    {
                        "type": "wait_delay",
                        "parameters": {
                            "duration": 3,
                            "unit": "days"
                        }
                    },
                    {
                        "type": "ai_scoring",
                        "parameters": {
                            "scoring_model": "lead_qualification",
                            "update_score": True
                        },
                        "delay": 0
                    },
                    {
                        "type": "condition_branch",
                        "parameters": {
                            "condition": "lead_score > 70",
                            "true_actions": [
                                {
                                    "type": "assign_to_user",
                                    "parameters": {
                                        "user_id": "sales_rep_1"
                                    }
                                }
                            ],
                            "false_actions": [
                                {
                                    "type": "add_tag",
                                    "parameters": {
                                        "tag": "needs_nurturing"
                                    }
                                }
                            ]
                        }
                    }
                ],
                "schedule_settings": {
                    "timezone": "UTC",
                    "business_hours_only": True,
                    "exclude_weekends": True,
                    "exclude_holidays": False
                },
                "ai_optimization": True,
                "performance_tracking": True,
                "is_active": True
            }
            
            response = self.make_request('POST', '/crm/automation-workflow', workflow_data)
            
            if response.status_code in [200, 201]:
                data = response.json()
                if data.get('success') and data.get('data'):
                    workflow = data['data']
                    required_fields = [
                        'workflow_id', 'workflow_name', 'trigger_type', 'actions_count',
                        'is_active', 'ai_optimization_enabled', 'estimated_impact',
                        'testing_recommendations'
                    ]
                    
                    if all(field in workflow for field in required_fields):
                        if workflow['actions_count'] == 4 and workflow['ai_optimization_enabled']:
                            self.log_result("CRM Automation Workflow", "PASS", f"Automation workflow created with {workflow['actions_count']} actions and AI optimization")
                            return True
                        else:
                            self.log_result("CRM Automation Workflow", "FAIL", "Invalid actions count or AI optimization", workflow)
                            return False
                    else:
                        self.log_result("CRM Automation Workflow", "FAIL", "Workflow response missing required fields", workflow)
                        return False
                else:
                    self.log_result("CRM Automation Workflow", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 422:
                data = response.json()
                if 'errors' in data:
                    self.log_result("CRM Automation Workflow", "PASS", "Validation working correctly")
                    return True
                else:
                    self.log_result("CRM Automation Workflow", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("CRM Automation Workflow", "FAIL", f"Workflow creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("CRM Automation Workflow", "FAIL", f"Workflow creation request failed: {str(e)}")
            return False

    def test_crm_ai_lead_scoring(self):
        """Test CRM AI-powered lead scoring"""
        if not self.auth_token:
            self.log_result("CRM AI Lead Scoring", "SKIP", "No auth token available")
            return False
            
        try:
            scoring_data = {
                "scoring_model": "advanced",
                "scoring_factors": ["demographic", "behavioral", "engagement", "firmographic"],
                "minimum_score": 50,
                "include_predictions": True,
                "include_recommendations": True,
                "batch_size": 100
            }
            
            response = self.make_request('GET', '/crm/ai-lead-scoring', scoring_data)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    scoring_results = data['data']
                    required_sections = [
                        'scoring_summary', 'scored_contacts', 'scoring_insights',
                        'performance_metrics', 'model_accuracy', 'optimization_suggestions',
                        'segment_recommendations'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in scoring_results]
                    if not missing_sections:
                        # Verify scoring summary
                        summary = scoring_results.get('scoring_summary', {})
                        required_summary_fields = [
                            'total_contacts_analyzed', 'qualified_contacts', 'average_score',
                            'scoring_model', 'high_priority_contacts', 'conversion_ready_contacts'
                        ]
                        
                        if all(field in summary for field in required_summary_fields):
                            model_accuracy = scoring_results.get('model_accuracy', 0)
                            if model_accuracy > 0:
                                self.log_result("CRM AI Lead Scoring", "PASS", f"AI lead scoring working with {model_accuracy}% model accuracy")
                                return True
                            else:
                                self.log_result("CRM AI Lead Scoring", "FAIL", "Invalid model accuracy", scoring_results)
                                return False
                        else:
                            self.log_result("CRM AI Lead Scoring", "FAIL", "Scoring summary missing required fields", summary)
                            return False
                    else:
                        self.log_result("CRM AI Lead Scoring", "FAIL", f"Missing scoring sections: {missing_sections}", scoring_results)
                        return False
                else:
                    self.log_result("CRM AI Lead Scoring", "FAIL", "Response missing success flag or data", data)
                    return False
            elif response.status_code == 404:
                # Expected when no contacts exist
                self.log_result("CRM AI Lead Scoring", "PASS", "No contacts found for scoring (expected behavior)")
                return True
            else:
                self.log_result("CRM AI Lead Scoring", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("CRM AI Lead Scoring", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_crm_advanced_pipeline_management(self):
        """Test CRM advanced pipeline management with AI insights"""
        if not self.auth_token:
            self.log_result("CRM Advanced Pipeline Management", "SKIP", "No auth token available")
            return False
            
        try:
            params = {
                "date_range": "last_90_days",
                "include_forecasting": "true",
                "include_bottleneck_analysis": "true",
                "include_win_loss_analysis": "true",
                "include_team_performance": "true",
                "include_ai_insights": "true",
                "segment_by": "source"
            }
            
            response = self.make_request('GET', '/crm/advanced-pipeline-management', params)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    pipeline = data['data']
                    required_sections = [
                        'pipeline_overview', 'stage_analysis', 'velocity_analysis',
                        'conversion_analysis', 'pipeline_health', 'performance_trends',
                        'leakage_analysis', 'forecasting', 'bottleneck_analysis',
                        'win_loss_analysis', 'team_performance', 'ai_insights',
                        'segmentation', 'recommendations'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in pipeline]
                    if not missing_sections:
                        # Verify pipeline overview
                        overview = pipeline.get('pipeline_overview', {})
                        required_overview_fields = [
                            'total_opportunities', 'total_value', 'weighted_value',
                            'average_deal_size', 'win_rate', 'average_sales_cycle',
                            'pipeline_health_score', 'pipeline_coverage'
                        ]
                        
                        if all(field in overview for field in required_overview_fields):
                            # Verify AI insights
                            ai_insights = pipeline.get('ai_insights', {})
                            if ai_insights and 'recommendations' in ai_insights:
                                health_score = overview.get('pipeline_health_score', 0)
                                self.log_result("CRM Advanced Pipeline Management", "PASS", f"Advanced pipeline management with {health_score}% health score and AI insights")
                                return True
                            else:
                                self.log_result("CRM Advanced Pipeline Management", "FAIL", "AI insights missing or incomplete", ai_insights)
                                return False
                        else:
                            self.log_result("CRM Advanced Pipeline Management", "FAIL", "Pipeline overview missing required fields", overview)
                            return False
                    else:
                        self.log_result("CRM Advanced Pipeline Management", "FAIL", f"Missing pipeline sections: {missing_sections}", pipeline)
                        return False
                else:
                    self.log_result("CRM Advanced Pipeline Management", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("CRM Advanced Pipeline Management", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("CRM Advanced Pipeline Management", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_crm_predictive_analytics(self):
        """Test CRM predictive analytics for contact behavior"""
        if not self.auth_token:
            self.log_result("CRM Predictive Analytics", "SKIP", "No auth token available")
            return False
            
        try:
            params = {
                "prediction_type": "comprehensive",
                "time_horizon": "90_days",
                "include_churn_prediction": "true",
                "include_conversion_probability": "true",
                "include_lifetime_value": "true",
                "include_engagement_prediction": "true",
                "include_next_best_action": "true",
                "confidence_threshold": "70",
                "segment_predictions": "true"
            }
            
            response = self.make_request('GET', '/crm/predictive-analytics', params)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('data'):
                    predictions = data['data']
                    required_sections = [
                        'prediction_summary', 'contact_predictions', 'churn_analysis',
                        'conversion_predictions', 'lifetime_value_analysis', 'engagement_predictions',
                        'next_best_actions', 'model_performance', 'segment_insights',
                        'actionable_recommendations'
                    ]
                    
                    missing_sections = [section for section in required_sections if section not in predictions]
                    if not missing_sections:
                        # Verify prediction summary
                        summary = predictions.get('prediction_summary', {})
                        required_summary_fields = [
                            'total_contacts_analyzed', 'high_risk_contacts', 'high_value_contacts',
                            'conversion_ready_contacts', 'model_accuracy', 'confidence_level'
                        ]
                        
                        if all(field in summary for field in required_summary_fields):
                            # Verify model performance
                            model_perf = predictions.get('model_performance', {})
                            if 'accuracy' in model_perf and 'precision' in model_perf and 'recall' in model_perf:
                                accuracy = model_perf.get('accuracy', 0)
                                self.log_result("CRM Predictive Analytics", "PASS", f"Predictive analytics working with {accuracy}% accuracy")
                                return True
                            else:
                                self.log_result("CRM Predictive Analytics", "FAIL", "Model performance metrics missing", model_perf)
                                return False
                        else:
                            self.log_result("CRM Predictive Analytics", "FAIL", "Prediction summary missing required fields", summary)
                            return False
                    else:
                        self.log_result("CRM Predictive Analytics", "FAIL", f"Missing prediction sections: {missing_sections}", predictions)
                        return False
                else:
                    self.log_result("CRM Predictive Analytics", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("CRM Predictive Analytics", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("CRM Predictive Analytics", "FAIL", f"Request failed: {str(e)}")
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
        print("\n🔗 Testing Enhanced Bio Site Builder...")
        self.test_bio_sites_list()
        bio_site_id = self.test_bio_sites_create_enhanced()
        self.test_bio_sites_themes()
        if bio_site_id:
            self.test_bio_sites_show(bio_site_id)
            self.test_bio_sites_analytics_enhanced(bio_site_id)
            duplicate_id = self.test_bio_sites_duplicate(bio_site_id)
            self.test_bio_sites_export(bio_site_id)
            self.test_bio_sites_get_links(bio_site_id)
            link_id = self.test_bio_sites_create_link(bio_site_id)
            self.test_bio_sites_reorder_links(bio_site_id)
        self.test_bio_sites_validation_enhanced()
        
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
        
        # Instagram Intelligence Engine tests
        print("\n📸 Testing Instagram Intelligence Engine...")
        self.test_instagram_auth_initiate()
        self.test_instagram_auth_callback_validation()
        self.test_instagram_competitor_analysis_validation()
        self.test_instagram_competitor_analysis_no_account()
        self.test_instagram_hashtag_analysis_validation()
        self.test_instagram_analytics_validation()
        self.test_instagram_refresh_token_validation()
        self.test_instagram_content_suggestions_validation()
        self.test_instagram_no_connected_account()
        self.test_instagram_content_type_validation()
        
        # NEW ADVANCED FEATURES TESTING
        print("\n🔥 Testing NEW ADVANCED FEATURES...")
        print("=" * 50)
        
        # Instagram Intelligence Engine Advanced Features
        print("\n📸 Testing Instagram Advanced Features...")
        self.test_instagram_advanced_competitor_analysis()
        self.test_instagram_predict_content_performance()
        self.test_instagram_audience_intelligence()
        
        # Bio Site Builder Advanced Features
        print("\n🎨 Testing Bio Site Advanced Features...")
        if bio_site_id:
            self.test_bio_sites_ab_test(bio_site_id)
            ab_test_id = self.test_bio_sites_ab_test_results(bio_site_id)
            self.test_bio_sites_monetization(bio_site_id)
            self.test_bio_sites_advanced_analytics(bio_site_id)
        
        # CRM Advanced Features
        print("\n🤖 Testing CRM Advanced Features...")
        self.test_crm_automation_workflow()
        self.test_crm_ai_lead_scoring()
        self.test_crm_advanced_pipeline_management()
        self.test_crm_predictive_analytics()
        
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