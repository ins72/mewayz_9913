#!/usr/bin/env python3
"""
Detailed Response Analysis for Review Request Endpoints
Analyzes the actual response data to verify professional depth
"""

import requests
import json
import time

class DetailedResponseAnalyzer:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        
    def authenticate(self):
        """Authenticate with admin credentials"""
        admin_login_data = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
        response = self.session.post(
            f"{self.api_url}/auth/login",
            json=admin_login_data,
            headers={'Content-Type': 'application/json', 'Accept': 'application/json'},
            timeout=30
        )
        
        if response.status_code == 200:
            data = response.json()
            if data.get('success') and data.get('token'):
                self.auth_token = data['token']
                print("✅ Authentication successful")
                return True
        
        print("❌ Authentication failed")
        return False
    
    def analyze_endpoint(self, endpoint: str, name: str):
        """Analyze a specific endpoint response"""
        print(f"\n=== Analyzing {name} ===")
        
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': f'Bearer {self.auth_token}'
        }
        
        try:
            response = self.session.get(f"{self.api_url}{endpoint}", headers=headers, timeout=30)
            
            if response.status_code == 200:
                data = response.json()
                print(f"✅ Status: 200 OK")
                print(f"📊 Response size: {len(json.dumps(data))} characters")
                
                # Analyze response structure
                if isinstance(data, dict):
                    print(f"🔑 Top-level keys: {list(data.keys())}")
                    
                    # Look for data depth indicators
                    for key, value in data.items():
                        if isinstance(value, list):
                            print(f"   • {key}: Array with {len(value)} items")
                        elif isinstance(value, dict):
                            print(f"   • {key}: Object with {len(value)} properties")
                        else:
                            print(f"   • {key}: {type(value).__name__}")
                
                # Pretty print a sample of the data
                print(f"📄 Sample response data:")
                print(json.dumps(data, indent=2)[:1000] + ("..." if len(json.dumps(data)) > 1000 else ""))
                
            else:
                print(f"❌ Status: {response.status_code}")
                print(f"Response: {response.text[:500]}")
                
        except Exception as e:
            print(f"❌ Error: {str(e)}")
    
    def run_analysis(self):
        """Run detailed analysis on all review request endpoints"""
        print("🔍 Starting Detailed Response Analysis")
        print("=" * 60)
        
        if not self.authenticate():
            return
        
        # Analyze each endpoint from the review request
        endpoints = [
            ("/health", "Health Check"),
            ("/ai/services", "AI Services"),
            ("/bio-sites/themes", "Bio Sites Themes"),
            ("/ecommerce/dashboard", "E-commerce Dashboard"),
            ("/bookings/dashboard", "Advanced Booking Dashboard"),
            ("/financial/dashboard/comprehensive", "Financial Dashboard Comprehensive"),
            ("/analytics/business-intelligence/advanced", "Advanced Business Intelligence"),
            ("/escrow/dashboard", "Escrow Dashboard"),
            ("/notifications/advanced", "Advanced Notifications")
        ]
        
        for endpoint, name in endpoints:
            self.analyze_endpoint(endpoint, name)
        
        print("\n" + "=" * 60)
        print("✅ Analysis Complete")

def main():
    backend_url = "https://8499964d-e0a0-442e-a40c-54d88efd4128.preview.emergentagent.com"
    analyzer = DetailedResponseAnalyzer(backend_url)
    analyzer.run_analysis()

if __name__ == "__main__":
    main()