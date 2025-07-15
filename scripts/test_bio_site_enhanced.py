#!/usr/bin/env python3
"""
Enhanced Bio Site Builder Testing Script
Tests all enhanced Bio Site Builder features as requested in the review
"""

import requests
import json
import sys
import time
from datetime import datetime
from typing import Dict, Any, Optional, List

class EnhancedBioSiteTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
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

    def authenticate(self):
        """Authenticate with admin credentials"""
        try:
            response = self.make_request('POST', '/auth/login', self.admin_credentials)
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.auth_token = data['token']
                    self.log_result("Authentication", "PASS", "Successfully authenticated")
                    return True
                else:
                    self.log_result("Authentication", "FAIL", "Login succeeded but missing token", data)
                    return False
            else:
                self.log_result("Authentication", "FAIL", f"Login failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Authentication", "FAIL", f"Authentication failed: {str(e)}")
            return False

    def test_enhanced_bio_site_creation(self):
        """Test POST /api/bio-sites with enhanced features"""
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
                    self.log_result("Enhanced Bio Site Creation", "PASS", "Enhanced bio site created successfully with advanced features")
                    return bio_site_id
                else:
                    self.log_result("Enhanced Bio Site Creation", "FAIL", "Creation succeeded but missing success flag", data)
                    return False
            else:
                self.log_result("Enhanced Bio Site Creation", "FAIL", f"Creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Enhanced Bio Site Creation", "FAIL", f"Creation request failed: {str(e)}")
            return False

    def test_themes_endpoint(self):
        """Test GET /api/bio-sites/themes"""
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
                            self.log_result("Theme Management System", "PASS", f"All {len(available_themes)} themes retrieved with proper structure")
                            return True
                        else:
                            self.log_result("Theme Management System", "FAIL", "Theme structure missing required fields", sample_theme)
                            return False
                    else:
                        self.log_result("Theme Management System", "FAIL", f"Missing themes: {missing_themes}", themes)
                        return False
                else:
                    self.log_result("Theme Management System", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Theme Management System", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Theme Management System", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_enhanced_analytics(self, bio_site_id):
        """Test GET /api/bio-sites/{id}/analytics with date filtering"""
        if not bio_site_id:
            self.log_result("Enhanced Analytics", "SKIP", "No bio site ID available")
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
                            self.log_result("Enhanced Analytics", "PASS", "Enhanced analytics retrieved with all sections and date filtering")
                            return True
                        else:
                            self.log_result("Enhanced Analytics", "FAIL", "Overview section missing required fields", overview)
                            return False
                    else:
                        self.log_result("Enhanced Analytics", "FAIL", f"Missing analytics sections: {missing_sections}", analytics)
                        return False
                else:
                    self.log_result("Enhanced Analytics", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Enhanced Analytics", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Enhanced Analytics", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_site_duplication(self, bio_site_id):
        """Test POST /api/bio-sites/{id}/duplicate"""
        if not bio_site_id:
            self.log_result("Site Duplication", "SKIP", "No bio site ID available")
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
                            self.log_result("Site Duplication", "PASS", "Bio site duplicated successfully with proper defaults")
                            return duplicate_info['id']
                        else:
                            self.log_result("Site Duplication", "FAIL", "Duplicated site should be inactive by default", duplicate_info)
                            return False
                    else:
                        self.log_result("Site Duplication", "FAIL", "Duplicate response missing required fields", duplicate_info)
                        return False
                else:
                    self.log_result("Site Duplication", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Site Duplication", "FAIL", f"Duplication failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Site Duplication", "FAIL", f"Duplication request failed: {str(e)}")
            return False

    def test_data_export(self, bio_site_id):
        """Test GET /api/bio-sites/{id}/export"""
        if not bio_site_id:
            self.log_result("Data Export", "SKIP", "No bio site ID available")
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
                            self.log_result("Data Export", "PASS", "Bio site data exported successfully with complete structure")
                            return True
                        else:
                            self.log_result("Data Export", "FAIL", "Bio site section missing required fields", bio_site)
                            return False
                    else:
                        self.log_result("Data Export", "FAIL", "Export data missing required sections", export_data)
                        return False
                else:
                    self.log_result("Data Export", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Data Export", "FAIL", f"Export failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Data Export", "FAIL", f"Export request failed: {str(e)}")
            return False

    def test_link_management(self, bio_site_id):
        """Test GET /api/bio-sites/{bioSiteId}/links"""
        if not bio_site_id:
            self.log_result("Link Management - Get Links", "SKIP", "No bio site ID available")
            return False
            
        try:
            response = self.make_request('GET', f'/bio-sites/{bio_site_id}/links')
            
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'data' in data:
                    links = data['data']
                    if isinstance(links, list):
                        self.log_result("Link Management - Get Links", "PASS", f"Bio site links retrieved successfully ({len(links)} links)")
                        return True
                    else:
                        self.log_result("Link Management - Get Links", "FAIL", "Links data is not a list", links)
                        return False
                else:
                    self.log_result("Link Management - Get Links", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Link Management - Get Links", "FAIL", f"Request failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Link Management - Get Links", "FAIL", f"Request failed: {str(e)}")
            return False

    def test_link_creation(self, bio_site_id):
        """Test POST /api/bio-sites/{bioSiteId}/links"""
        if not bio_site_id:
            self.log_result("Link Creation", "SKIP", "No bio site ID available")
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
                        self.log_result("Link Creation", "PASS", "Bio site link created successfully")
                        return link_info['id']
                    else:
                        self.log_result("Link Creation", "FAIL", "Link response missing required fields", link_info)
                        return False
                else:
                    self.log_result("Link Creation", "FAIL", "Response missing success flag or data", data)
                    return False
            else:
                self.log_result("Link Creation", "FAIL", f"Link creation failed with status {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Link Creation", "FAIL", f"Link creation request failed: {str(e)}")
            return False

    def test_link_reordering(self, bio_site_id):
        """Test POST /api/bio-sites/{bioSiteId}/links/reorder"""
        if not bio_site_id:
            self.log_result("Link Reordering", "SKIP", "No bio site ID available")
            return False
            
        try:
            # First get existing links to reorder them
            links_response = self.make_request('GET', f'/bio-sites/{bio_site_id}/links')
            
            if links_response.status_code != 200:
                self.log_result("Link Reordering", "SKIP", "Could not retrieve existing links")
                return False
                
            links_data = links_response.json()
            if not links_data.get('success') or not links_data.get('data'):
                self.log_result("Link Reordering", "SKIP", "No links available to reorder")
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
                        self.log_result("Link Reordering", "PASS", "Bio site links reordered successfully")
                        return True
                    else:
                        self.log_result("Link Reordering", "FAIL", "Reorder succeeded but missing success flag", data)
                        return False
                else:
                    self.log_result("Link Reordering", "FAIL", f"Reorder failed with status {response.status_code}", response.json())
                    return False
            else:
                self.log_result("Link Reordering", "SKIP", "Not enough links to test reordering")
                return False
                
        except Exception as e:
            self.log_result("Link Reordering", "FAIL", f"Reorder request failed: {str(e)}")
            return False

    def test_comprehensive_validation(self):
        """Test comprehensive validation for enhanced features"""
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
                        self.log_result("Comprehensive Validation", "PASS", f"Enhanced validation working correctly ({len(found_errors)} errors found)")
                        return True
                    else:
                        self.log_result("Comprehensive Validation", "FAIL", f"Expected more validation errors, got: {list(errors.keys())}", errors)
                        return False
                else:
                    self.log_result("Comprehensive Validation", "FAIL", "422 status but no errors field", data)
                    return False
            else:
                self.log_result("Comprehensive Validation", "FAIL", f"Expected 422 but got {response.status_code}", response.json())
                return False
                
        except Exception as e:
            self.log_result("Comprehensive Validation", "FAIL", f"Validation test failed: {str(e)}")
            return False

    def run_enhanced_tests(self):
        """Run all enhanced Bio Site Builder tests"""
        print("🚀 Starting Enhanced Bio Site Builder Tests")
        print("=" * 60)
        
        # Authenticate first
        if not self.authenticate():
            print("❌ Authentication failed. Cannot proceed with tests.")
            return False
        
        print("\n🔗 Testing Enhanced Bio Site Builder Features...")
        
        # Test 1: Enhanced Bio Site Creation
        bio_site_id = self.test_enhanced_bio_site_creation()
        
        # Test 2: Theme Management System
        self.test_themes_endpoint()
        
        # Test 3: Enhanced Analytics (requires bio site ID)
        if bio_site_id:
            self.test_enhanced_analytics(bio_site_id)
        
        # Test 4: Site Duplication
        if bio_site_id:
            duplicate_id = self.test_site_duplication(bio_site_id)
        
        # Test 5: Data Export
        if bio_site_id:
            self.test_data_export(bio_site_id)
        
        # Test 6: Link Management
        if bio_site_id:
            self.test_link_management(bio_site_id)
        
        # Test 7: Link Creation
        if bio_site_id:
            link_id = self.test_link_creation(bio_site_id)
        
        # Test 8: Link Reordering
        if bio_site_id:
            self.test_link_reordering(bio_site_id)
        
        # Test 9: Comprehensive Validation
        self.test_comprehensive_validation()
        
        # Print summary
        print("\n" + "=" * 60)
        print("📊 ENHANCED BIO SITE BUILDER TEST SUMMARY")
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
        
        success_rate = (passed / total) * 100 if total > 0 else 0
        print(f"\n🎯 Success Rate: {success_rate:.1f}%")
        
        return failed == 0

if __name__ == "__main__":
    tester = EnhancedBioSiteTester()
    success = tester.run_enhanced_tests()
    sys.exit(0 if success else 1)