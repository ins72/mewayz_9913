#!/usr/bin/env python3
"""
Mewayz Platform - Comprehensive Backend API Testing Suite
=========================================================

This script tests all backend API endpoints for the Mewayz Laravel application.
Based on the comprehensive audit reports and API routes analysis.

Test Coverage:
- Health & System endpoints
- Authentication system
- Workspace setup wizard (6 steps)
- Instagram management
- Email marketing hub
- Payment processing (Stripe)
- Team management
- CRM system
- E-commerce management
- Course management
- Analytics dashboard
- Bio site management
- Social media management

Author: Testing Agent
Date: January 2025
"""

import requests
import json
import time
import sys
from datetime import datetime
from typing import Dict, List, Optional, Any

class MewayzBackendTester:
    def __init__(self, base_url: str = "http://localhost:8001"):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.admin_credentials = {
            "email": "admin@example.com",
            "password": "admin123"
        }
        
        # Test data for various endpoints
        self.test_data = {
            "user": {
                "name": "Sarah Johnson",
                "email": "sarah.johnson@mewayz.com",
                "password": "SecurePass123!",
                "password_confirmation": "SecurePass123!"
            },
            "workspace": {
                "name": "Digital Marketing Agency",
                "description": "Full-service digital marketing solutions"
            },
            "instagram_account": {
                "username": "mewayz_official",
                "account_type": "business",
                "followers_count": 15000
            },
            "instagram_post": {
                "caption": "Exciting new features coming to Mewayz! 🚀 #DigitalMarketing #SocialMedia #BusinessGrowth",
                "media_url": "https://example.com/image.jpg",
                "hashtags": ["#DigitalMarketing", "#SocialMedia", "#BusinessGrowth"],
                "scheduled_at": "2025-01-15 10:00:00"
            },
            "email_campaign": {
                "name": "Welcome Series - January 2025",
                "subject": "Welcome to Mewayz - Your Digital Success Starts Here!",
                "content": "Welcome to the Mewayz platform! We're excited to help you grow your business.",
                "template_id": 1,
                "list_id": 1
            },
            "crm_contact": {
                "name": "Michael Chen",
                "email": "michael.chen@techstartup.com",
                "phone": "+1-555-0123",
                "company": "Tech Startup Inc",
                "position": "CEO"
            },
            "product": {
                "name": "Digital Marketing Course",
                "description": "Complete guide to digital marketing success",
                "price": 299.99,
                "category": "Education",
                "stock": 100
            },
            "course": {
                "title": "Advanced Instagram Marketing",
                "description": "Master Instagram marketing strategies for business growth",
                "price": 199.99,
                "duration": "6 weeks"
            },
            "bio_site": {
                "title": "Sarah's Digital Hub",
                "description": "Digital marketing expert and business consultant",
                "theme": "modern",
                "custom_domain": "sarah.mewayz.com"
            }
        }

    def log_result(self, endpoint: str, method: str, status: str, 
                   response_time: float, details: str = "", 
                   status_code: int = 0):
        """Log test result"""
        result = {
            "timestamp": datetime.now().isoformat(),
            "endpoint": endpoint,
            "method": method,
            "status": status,
            "response_time_ms": round(response_time * 1000, 2),
            "status_code": status_code,
            "details": details
        }
        self.test_results.append(result)
        
        # Color coding for console output
        color = "\033[92m" if status == "PASS" else "\033[91m" if status == "FAIL" else "\033[93m"
        reset = "\033[0m"
        
        print(f"{color}[{status}]{reset} {method} {endpoint} ({result['response_time_ms']}ms) - {details}")

    def make_request(self, method: str, endpoint: str, data: Dict = None, 
                    headers: Dict = None, auth_required: bool = True) -> requests.Response:
        """Make HTTP request with proper headers and authentication"""
        url = f"{self.api_url}{endpoint}"
        
        # Default headers
        request_headers = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "User-Agent": "Mewayz-Backend-Tester/1.0"
        }
        
        # Add authentication if required and available
        if auth_required and self.auth_token:
            request_headers["Authorization"] = f"Bearer {self.auth_token}"
        
        # Merge with custom headers
        if headers:
            request_headers.update(headers)
        
        # Make request
        start_time = time.time()
        try:
            if method.upper() == "GET":
                response = self.session.get(url, headers=request_headers, timeout=30)
            elif method.upper() == "POST":
                response = self.session.post(url, json=data, headers=request_headers, timeout=30)
            elif method.upper() == "PUT":
                response = self.session.put(url, json=data, headers=request_headers, timeout=30)
            elif method.upper() == "DELETE":
                response = self.session.delete(url, headers=request_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            response_time = time.time() - start_time
            
            # Log the result
            if response.status_code < 400:
                self.log_result(endpoint, method.upper(), "PASS", response_time, 
                              f"Status: {response.status_code}", response.status_code)
            else:
                self.log_result(endpoint, method.upper(), "FAIL", response_time, 
                              f"Status: {response.status_code} - {response.text[:100]}", 
                              response.status_code)
            
            return response
            
        except requests.exceptions.RequestException as e:
            response_time = time.time() - start_time
            self.log_result(endpoint, method.upper(), "ERROR", response_time, 
                          f"Request failed: {str(e)}")
            return None

    def test_server_connectivity(self):
        """Test if the Laravel server is running and accessible"""
        print("\n" + "="*60)
        print("🔍 TESTING SERVER CONNECTIVITY")
        print("="*60)
        
        try:
            response = requests.get(f"{self.base_url}", timeout=10)
            if response.status_code == 200:
                self.log_result("/", "GET", "PASS", 0.1, "Laravel server is running")
                return True
            else:
                self.log_result("/", "GET", "FAIL", 0.1, f"Server returned {response.status_code}")
                return False
        except requests.exceptions.RequestException as e:
            self.log_result("/", "GET", "ERROR", 0.1, f"Cannot connect to server: {str(e)}")
            return False

    def test_health_endpoints(self):
        """Test health and system endpoints"""
        print("\n" + "="*60)
        print("🏥 TESTING HEALTH & SYSTEM ENDPOINTS")
        print("="*60)
        
        endpoints = [
            ("/health", "GET"),
            ("/system/info", "GET"),
            ("/system/maintenance", "GET"),
            ("/platform/overview", "GET"),
            ("/platform/statistics", "GET"),
            ("/platform/features", "GET"),
            ("/branding/info", "GET"),
            ("/optimization/performance", "GET")
        ]
        
        for endpoint, method in endpoints:
            self.make_request(method, endpoint, auth_required=False)

    def test_authentication_system(self):
        """Test authentication endpoints and flows"""
        print("\n" + "="*60)
        print("🔐 TESTING AUTHENTICATION SYSTEM")
        print("="*60)
        
        # Test user registration
        response = self.make_request("POST", "/auth/register", 
                                   data=self.test_data["user"], 
                                   auth_required=False)
        
        # Test admin login
        response = self.make_request("POST", "/auth/login", 
                                   data=self.admin_credentials, 
                                   auth_required=False)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if "token" in data or "access_token" in data:
                    self.auth_token = data.get("token") or data.get("access_token")
                    print(f"✅ Authentication successful - Token obtained")
                else:
                    print(f"⚠️ Login successful but no token in response")
            except json.JSONDecodeError:
                print(f"⚠️ Login response is not valid JSON")
        
        # Test authenticated endpoints
        if self.auth_token:
            self.make_request("GET", "/auth/me")
            self.make_request("PUT", "/auth/profile", 
                            data={"name": "Updated Admin User"})
        
        # Test OAuth status
        self.make_request("GET", "/auth/oauth-status", auth_required=False)
        
        # Test 2FA endpoints
        self.make_request("GET", "/auth/2fa/status", auth_required=False)

    def test_workspace_setup_wizard(self):
        """Test the 6-step workspace setup wizard"""
        print("\n" + "="*60)
        print("🧙‍♂️ TESTING WORKSPACE SETUP WIZARD (6 STEPS)")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping workspace tests - No authentication token")
            return
        
        # Step 1: Get initial data
        self.make_request("GET", "/workspace-setup/initial-data")
        
        # Step 2: Get and save main goals
        self.make_request("GET", "/workspace-setup/main-goals")
        goals_data = {
            "goals": [1, 2, 3],  # Assuming goal IDs
            "primary_goal": 1
        }
        self.make_request("POST", "/workspace-setup/main-goals", data=goals_data)
        
        # Step 3: Get and save features
        self.make_request("GET", "/workspace-setup/available-features")
        features_data = {
            "features": [1, 2, 3, 4, 5],  # Assuming feature IDs
            "priority_features": [1, 2]
        }
        self.make_request("POST", "/workspace-setup/feature-selection", data=features_data)
        
        # Step 4: Team setup
        team_data = {
            "team_size": "5-10",
            "invitations": [
                {"email": "team1@mewayz.com", "role": "admin"},
                {"email": "team2@mewayz.com", "role": "member"}
            ]
        }
        self.make_request("POST", "/workspace-setup/team-setup", data=team_data)
        
        # Step 5: Subscription plans and selection
        self.make_request("GET", "/workspace-setup/subscription-plans")
        subscription_data = {
            "plan_id": 2,  # Professional plan
            "billing_cycle": "monthly"
        }
        self.make_request("POST", "/workspace-setup/subscription-selection", data=subscription_data)
        
        # Step 6: Branding configuration
        branding_data = {
            "company_name": "Mewayz Digital Agency",
            "logo_url": "https://example.com/logo.png",
            "primary_color": "#3B82F6",
            "secondary_color": "#10B981"
        }
        self.make_request("POST", "/workspace-setup/branding-configuration", data=branding_data)
        
        # Complete setup
        self.make_request("POST", "/workspace-setup/complete")
        
        # Get setup summary and status
        self.make_request("GET", "/workspace-setup/summary")
        self.make_request("GET", "/workspace-setup/status")

    def test_instagram_management(self):
        """Test Instagram management system"""
        print("\n" + "="*60)
        print("📸 TESTING INSTAGRAM MANAGEMENT SYSTEM")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping Instagram tests - No authentication token")
            return
        
        # Test account management
        self.make_request("GET", "/instagram-management/accounts")
        response = self.make_request("POST", "/instagram-management/accounts", 
                                   data=self.test_data["instagram_account"])
        
        # Test post management
        self.make_request("GET", "/instagram-management/posts")
        post_response = self.make_request("POST", "/instagram-management/posts", 
                                        data=self.test_data["instagram_post"])
        
        # If post was created, test update and delete
        if post_response and post_response.status_code in [200, 201]:
            try:
                post_data = post_response.json()
                if "id" in post_data:
                    post_id = post_data["id"]
                    # Test update
                    update_data = self.test_data["instagram_post"].copy()
                    update_data["caption"] = "Updated caption with new hashtags! 🎉"
                    self.make_request("PUT", f"/instagram-management/posts/{post_id}", 
                                    data=update_data)
                    # Test delete
                    self.make_request("DELETE", f"/instagram-management/posts/{post_id}")
            except json.JSONDecodeError:
                pass
        
        # Test hashtag research and analytics
        self.make_request("GET", "/instagram-management/hashtag-research")
        self.make_request("GET", "/instagram-management/analytics")
        
        # Test Instagram Intelligence Engine
        self.make_request("GET", "/instagram/competitor-analysis")
        self.make_request("GET", "/instagram/hashtag-analysis")
        self.make_request("GET", "/instagram/analytics")
        self.make_request("GET", "/instagram/audience-intelligence")
        self.make_request("GET", "/instagram/content-suggestions")

    def test_email_marketing_hub(self):
        """Test email marketing system"""
        print("\n" + "="*60)
        print("📧 TESTING EMAIL MARKETING HUB")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping email marketing tests - No authentication token")
            return
        
        # Test campaign management
        self.make_request("GET", "/email-marketing/campaigns")
        campaign_response = self.make_request("POST", "/email-marketing/campaigns", 
                                            data=self.test_data["email_campaign"])
        
        # Test templates and lists
        self.make_request("GET", "/email-marketing/templates")
        self.make_request("GET", "/email-marketing/lists")
        self.make_request("GET", "/email-marketing/subscribers")
        self.make_request("GET", "/email-marketing/analytics")
        
        # If campaign was created, test individual campaign operations
        if campaign_response and campaign_response.status_code in [200, 201]:
            try:
                campaign_data = campaign_response.json()
                if "id" in campaign_data:
                    campaign_id = campaign_data["id"]
                    self.make_request("GET", f"/email-marketing/campaigns/{campaign_id}")
                    
                    # Test campaign update
                    update_data = self.test_data["email_campaign"].copy()
                    update_data["subject"] = "Updated: Welcome to Mewayz Platform!"
                    self.make_request("PUT", f"/email-marketing/campaigns/{campaign_id}", 
                                    data=update_data)
                    
                    # Test send campaign
                    self.make_request("POST", f"/email-marketing/campaigns/{campaign_id}/send")
                    
                    # Test delete campaign
                    self.make_request("DELETE", f"/email-marketing/campaigns/{campaign_id}")
            except json.JSONDecodeError:
                pass

    def test_payment_processing(self):
        """Test Stripe payment integration"""
        print("\n" + "="*60)
        print("💳 TESTING PAYMENT PROCESSING (STRIPE)")
        print("="*60)
        
        # Test payment packages (public endpoint)
        self.make_request("GET", "/payments/packages", auth_required=False)
        
        if not self.auth_token:
            print("❌ Skipping authenticated payment tests - No authentication token")
            return
        
        # Test checkout session creation
        checkout_data = {
            "package_id": "professional",
            "billing_cycle": "monthly",
            "success_url": "https://mewayz.com/success",
            "cancel_url": "https://mewayz.com/cancel"
        }
        session_response = self.make_request("POST", "/payments/checkout/session", 
                                           data=checkout_data)
        
        # If session was created, test status check
        if session_response and session_response.status_code in [200, 201]:
            try:
                session_data = session_response.json()
                if "session_id" in session_data:
                    session_id = session_data["session_id"]
                    self.make_request("GET", f"/payments/checkout/status/{session_id}")
            except json.JSONDecodeError:
                pass

    def test_team_management(self):
        """Test team management system"""
        print("\n" + "="*60)
        print("👥 TESTING TEAM MANAGEMENT SYSTEM")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping team management tests - No authentication token")
            return
        
        # Test team operations
        self.make_request("GET", "/team")
        
        # Test team invitation
        invitation_data = {
            "email": "newteam@mewayz.com",
            "role": "member",
            "message": "Welcome to our Mewayz team!"
        }
        invite_response = self.make_request("POST", "/team/invite", data=invitation_data)
        
        # Note: Testing invitation acceptance/rejection would require actual email workflow
        # which is not feasible in automated testing

    def test_crm_system(self):
        """Test CRM system"""
        print("\n" + "="*60)
        print("🤝 TESTING CRM SYSTEM")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping CRM tests - No authentication token")
            return
        
        # Test contact management
        self.make_request("GET", "/crm/contacts")
        contact_response = self.make_request("POST", "/crm/contacts", 
                                           data=self.test_data["crm_contact"])
        
        # Test lead management
        self.make_request("GET", "/crm/leads")
        lead_data = self.test_data["crm_contact"].copy()
        lead_data["status"] = "new"
        lead_data["source"] = "website"
        lead_response = self.make_request("POST", "/crm/leads", data=lead_data)
        
        # Test advanced CRM features
        self.make_request("GET", "/crm/ai-lead-scoring")
        self.make_request("GET", "/crm/advanced-pipeline-management")
        self.make_request("GET", "/crm/predictive-analytics")
        
        # Test contact operations if contact was created
        if contact_response and contact_response.status_code in [200, 201]:
            try:
                contact_data = contact_response.json()
                if "id" in contact_data:
                    contact_id = contact_data["id"]
                    self.make_request("GET", f"/crm/contacts/{contact_id}")
                    
                    # Test update
                    update_data = self.test_data["crm_contact"].copy()
                    update_data["phone"] = "+1-555-9999"
                    self.make_request("PUT", f"/crm/contacts/{contact_id}", data=update_data)
                    
                    # Test delete
                    self.make_request("DELETE", f"/crm/contacts/{contact_id}")
            except json.JSONDecodeError:
                pass

    def test_ecommerce_management(self):
        """Test e-commerce system"""
        print("\n" + "="*60)
        print("🛒 TESTING E-COMMERCE MANAGEMENT")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping e-commerce tests - No authentication token")
            return
        
        # Test product management
        self.make_request("GET", "/ecommerce/products")
        product_response = self.make_request("POST", "/ecommerce/products", 
                                           data=self.test_data["product"])
        
        # Test order management
        self.make_request("GET", "/ecommerce/orders")
        
        # Test product operations if product was created
        if product_response and product_response.status_code in [200, 201]:
            try:
                product_data = product_response.json()
                if "id" in product_data:
                    product_id = product_data["id"]
                    self.make_request("GET", f"/ecommerce/products/{product_id}")
                    
                    # Test update
                    update_data = self.test_data["product"].copy()
                    update_data["price"] = 399.99
                    self.make_request("PUT", f"/ecommerce/products/{product_id}", data=update_data)
                    
                    # Test delete
                    self.make_request("DELETE", f"/ecommerce/products/{product_id}")
            except json.JSONDecodeError:
                pass

    def test_course_management(self):
        """Test course management system"""
        print("\n" + "="*60)
        print("🎓 TESTING COURSE MANAGEMENT SYSTEM")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping course management tests - No authentication token")
            return
        
        # Test course operations
        self.make_request("GET", "/courses")
        course_response = self.make_request("POST", "/courses", 
                                          data=self.test_data["course"])
        
        # Test course operations if course was created
        if course_response and course_response.status_code in [200, 201]:
            try:
                course_data = course_response.json()
                if "id" in course_data:
                    course_id = course_data["id"]
                    self.make_request("GET", f"/courses/{course_id}")
                    self.make_request("GET", f"/courses/{course_id}/lessons")
                    self.make_request("GET", f"/courses/{course_id}/students")
                    
                    # Test lesson creation
                    lesson_data = {
                        "title": "Introduction to Instagram Marketing",
                        "content": "Learn the basics of Instagram marketing",
                        "duration": 30,
                        "order": 1
                    }
                    self.make_request("POST", f"/courses/{course_id}/lessons", data=lesson_data)
                    
                    # Test student enrollment
                    enrollment_data = {"student_email": "student@example.com"}
                    self.make_request("POST", f"/courses/{course_id}/enroll", data=enrollment_data)
                    
                    # Test update and delete
                    update_data = self.test_data["course"].copy()
                    update_data["price"] = 249.99
                    self.make_request("PUT", f"/courses/{course_id}", data=update_data)
                    self.make_request("DELETE", f"/courses/{course_id}")
            except json.JSONDecodeError:
                pass

    def test_analytics_dashboard(self):
        """Test analytics dashboard"""
        print("\n" + "="*60)
        print("📊 TESTING ANALYTICS DASHBOARD")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping analytics tests - No authentication token")
            return
        
        # Test analytics endpoints
        self.make_request("GET", "/analytics")
        self.make_request("GET", "/analytics/reports")
        self.make_request("GET", "/analytics/social-media")
        self.make_request("GET", "/analytics/bio-sites")
        self.make_request("GET", "/analytics/ecommerce")
        self.make_request("GET", "/analytics/email-marketing")

    def test_bio_site_management(self):
        """Test bio site management"""
        print("\n" + "="*60)
        print("🔗 TESTING BIO SITE MANAGEMENT")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping bio site tests - No authentication token")
            return
        
        # Test bio site operations
        self.make_request("GET", "/bio-sites")
        self.make_request("GET", "/bio-sites/themes")
        
        bio_response = self.make_request("POST", "/bio-sites", 
                                       data=self.test_data["bio_site"])
        
        # Test bio site operations if created
        if bio_response and bio_response.status_code in [200, 201]:
            try:
                bio_data = bio_response.json()
                if "id" in bio_data:
                    bio_id = bio_data["id"]
                    self.make_request("GET", f"/bio-sites/{bio_id}")
                    self.make_request("GET", f"/bio-sites/{bio_id}/analytics")
                    self.make_request("GET", f"/bio-sites/{bio_id}/links")
                    
                    # Test link creation
                    link_data = {
                        "title": "My Portfolio",
                        "url": "https://portfolio.mewayz.com",
                        "description": "Check out my latest work",
                        "order": 1
                    }
                    self.make_request("POST", f"/bio-sites/{bio_id}/links", data=link_data)
                    
                    # Test update and delete
                    update_data = self.test_data["bio_site"].copy()
                    update_data["title"] = "Updated Digital Hub"
                    self.make_request("PUT", f"/bio-sites/{bio_id}", data=update_data)
                    self.make_request("DELETE", f"/bio-sites/{bio_id}")
            except json.JSONDecodeError:
                pass

    def test_social_media_management(self):
        """Test social media management"""
        print("\n" + "="*60)
        print("📱 TESTING SOCIAL MEDIA MANAGEMENT")
        print("="*60)
        
        if not self.auth_token:
            print("❌ Skipping social media tests - No authentication token")
            return
        
        # Test social media endpoints
        self.make_request("GET", "/social-media/accounts")
        self.make_request("GET", "/social-media/analytics")
        self.make_request("GET", "/social-media/posts")
        
        # Test post creation
        social_post_data = {
            "content": "Exciting updates coming to Mewayz! 🚀",
            "platforms": ["instagram", "facebook", "twitter"],
            "scheduled_at": "2025-01-15 14:00:00"
        }
        post_response = self.make_request("POST", "/social-media/posts", data=social_post_data)
        
        # Test account connection
        connect_data = {
            "platform": "instagram",
            "account_id": "mewayz_official"
        }
        self.make_request("POST", "/social-media/accounts/connect", data=connect_data)

    def generate_report(self):
        """Generate comprehensive test report"""
        print("\n" + "="*80)
        print("📋 COMPREHENSIVE BACKEND TESTING REPORT")
        print("="*80)
        
        # Calculate statistics
        total_tests = len(self.test_results)
        passed_tests = len([r for r in self.test_results if r["status"] == "PASS"])
        failed_tests = len([r for r in self.test_results if r["status"] == "FAIL"])
        error_tests = len([r for r in self.test_results if r["status"] == "ERROR"])
        
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        avg_response_time = sum(r["response_time_ms"] for r in self.test_results) / total_tests if total_tests > 0 else 0
        
        print(f"\n📊 OVERALL STATISTICS:")
        print(f"   Total Tests: {total_tests}")
        print(f"   ✅ Passed: {passed_tests} ({passed_tests/total_tests*100:.1f}%)")
        print(f"   ❌ Failed: {failed_tests} ({failed_tests/total_tests*100:.1f}%)")
        print(f"   ⚠️  Errors: {error_tests} ({error_tests/total_tests*100:.1f}%)")
        print(f"   🎯 Success Rate: {success_rate:.1f}%")
        print(f"   ⚡ Average Response Time: {avg_response_time:.1f}ms")
        
        # Group results by endpoint category
        categories = {
            "Health & System": [r for r in self.test_results if any(x in r["endpoint"] for x in ["/health", "/system", "/platform", "/branding", "/optimization"])],
            "Authentication": [r for r in self.test_results if "/auth" in r["endpoint"]],
            "Workspace Setup": [r for r in self.test_results if "/workspace-setup" in r["endpoint"]],
            "Instagram Management": [r for r in self.test_results if "/instagram" in r["endpoint"]],
            "Email Marketing": [r for r in self.test_results if "/email-marketing" in r["endpoint"]],
            "Payment Processing": [r for r in self.test_results if "/payments" in r["endpoint"]],
            "Team Management": [r for r in self.test_results if "/team" in r["endpoint"]],
            "CRM System": [r for r in self.test_results if "/crm" in r["endpoint"]],
            "E-commerce": [r for r in self.test_results if "/ecommerce" in r["endpoint"]],
            "Course Management": [r for r in self.test_results if "/courses" in r["endpoint"]],
            "Analytics": [r for r in self.test_results if "/analytics" in r["endpoint"]],
            "Bio Sites": [r for r in self.test_results if "/bio-sites" in r["endpoint"]],
            "Social Media": [r for r in self.test_results if "/social-media" in r["endpoint"]]
        }
        
        print(f"\n📈 RESULTS BY CATEGORY:")
        for category, results in categories.items():
            if results:
                passed = len([r for r in results if r["status"] == "PASS"])
                total = len(results)
                rate = (passed / total * 100) if total > 0 else 0
                status_icon = "✅" if rate >= 80 else "⚠️" if rate >= 50 else "❌"
                print(f"   {status_icon} {category}: {passed}/{total} ({rate:.1f}%)")
        
        # Show critical failures
        critical_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] >= 500]
        if critical_failures:
            print(f"\n🚨 CRITICAL FAILURES (5xx errors):")
            for failure in critical_failures[:10]:  # Show first 10
                print(f"   ❌ {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        # Show authentication issues
        auth_failures = [r for r in self.test_results if r["status"] in ["FAIL", "ERROR"] and r["status_code"] in [401, 403]]
        if auth_failures:
            print(f"\n🔐 AUTHENTICATION ISSUES:")
            for failure in auth_failures[:5]:  # Show first 5
                print(f"   🔒 {failure['method']} {failure['endpoint']} - {failure['details']}")
        
        # Performance analysis
        slow_endpoints = [r for r in self.test_results if r["response_time_ms"] > 1000]
        if slow_endpoints:
            print(f"\n⏱️  SLOW ENDPOINTS (>1000ms):")
            for endpoint in sorted(slow_endpoints, key=lambda x: x["response_time_ms"], reverse=True)[:5]:
                print(f"   🐌 {endpoint['method']} {endpoint['endpoint']} - {endpoint['response_time_ms']}ms")
        
        # Save detailed results to file
        report_file = f"/app/backend_test_results_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        with open(report_file, 'w') as f:
            json.dump({
                "summary": {
                    "total_tests": total_tests,
                    "passed": passed_tests,
                    "failed": failed_tests,
                    "errors": error_tests,
                    "success_rate": success_rate,
                    "avg_response_time_ms": avg_response_time,
                    "test_timestamp": datetime.now().isoformat()
                },
                "results": self.test_results,
                "categories": {cat: len(results) for cat, results in categories.items() if results}
            }, f, indent=2)
        
        print(f"\n💾 Detailed results saved to: {report_file}")
        
        return {
            "success_rate": success_rate,
            "total_tests": total_tests,
            "passed": passed_tests,
            "failed": failed_tests,
            "critical_failures": len(critical_failures),
            "auth_issues": len(auth_failures)
        }

    def run_comprehensive_test(self):
        """Run all backend tests"""
        print("🚀 STARTING COMPREHENSIVE MEWAYZ BACKEND TESTING")
        print("=" * 80)
        print(f"Target URL: {self.base_url}")
        print(f"API URL: {self.api_url}")
        print(f"Test Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 80)
        
        # Check server connectivity first
        if not self.test_server_connectivity():
            print("\n❌ CRITICAL: Cannot connect to Laravel server")
            print("   Please ensure the server is running on the expected port")
            return False
        
        # Run all test suites
        try:
            self.test_health_endpoints()
            self.test_authentication_system()
            self.test_workspace_setup_wizard()
            self.test_instagram_management()
            self.test_email_marketing_hub()
            self.test_payment_processing()
            self.test_team_management()
            self.test_crm_system()
            self.test_ecommerce_management()
            self.test_course_management()
            self.test_analytics_dashboard()
            self.test_bio_site_management()
            self.test_social_media_management()
            
        except KeyboardInterrupt:
            print("\n⚠️ Testing interrupted by user")
        except Exception as e:
            print(f"\n❌ Unexpected error during testing: {str(e)}")
        
        # Generate final report
        results = self.generate_report()
        
        print(f"\n🏁 TESTING COMPLETED")
        print(f"   Overall Success Rate: {results['success_rate']:.1f}%")
        print(f"   Tests Passed: {results['passed']}/{results['total_tests']}")
        
        if results['critical_failures'] > 0:
            print(f"   🚨 Critical Failures: {results['critical_failures']}")
        
        if results['auth_issues'] > 0:
            print(f"   🔐 Authentication Issues: {results['auth_issues']}")
        
        return results['success_rate'] >= 70  # Consider 70%+ as acceptable

def main():
    """Main function to run the backend tests"""
    if len(sys.argv) > 1:
        base_url = sys.argv[1]
    else:
        base_url = "http://localhost:8001"
    
    tester = MewayzBackendTester(base_url)
    success = tester.run_comprehensive_test()
    
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()