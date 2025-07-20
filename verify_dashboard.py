#!/usr/bin/env python3
"""
User-Friendly Dashboard Content Verification
"""

import requests
import json

# Backend URL and credentials
BACKEND_URL = "https://5bde9595-e877-4e2b-9a14-404677567ffb.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

def authenticate():
    """Get authentication token"""
    login_data = {"email": ADMIN_EMAIL, "password": ADMIN_PASSWORD}
    response = requests.post(f"{API_BASE}/auth/login", json=login_data)
    if response.status_code == 200:
        return response.json().get('token')
    return None

def verify_user_friendly_dashboard():
    """Verify the user-friendly dashboard content"""
    token = authenticate()
    if not token:
        print("❌ Authentication failed")
        return
    
    headers = {'Authorization': f'Bearer {token}'}
    
    print("🔍 VERIFYING USER-FRIENDLY DASHBOARD CONTENT")
    print("="*50)
    
    # Test the user-friendly dashboard endpoint
    response = requests.get(f"{API_BASE}/features/user-friendly/dashboard", headers=headers)
    
    if response.status_code == 200:
        data = response.json()
        print(f"✅ User-Friendly Dashboard Response:")
        print(f"   Status: {response.status_code}")
        print(f"   Response Size: {len(json.dumps(data))} characters")
        
        # Show key features for user-friendliness
        print(f"\n📊 User-Friendly Features Verification:")
        
        if 'data' in data and 'user_friendly_features' in data['data']:
            features = data['data']['user_friendly_features']
            print(f"   🎯 One-Click Solutions: {features.get('one_click_solutions', 'N/A')}")
            print(f"   🤖 Smart Automation: {features.get('smart_automation', 'N/A')}")
            print(f"   📱 Intuitive Interface: {features.get('intuitive_interface', 'N/A')}")
            print(f"   🎓 Guided Tutorials: {features.get('guided_tutorials', 'N/A')}")
        
        # Show sample content
        print(f"\n📋 Sample Dashboard Content (first 800 chars):")
        print(json.dumps(data, indent=2)[:800] + "...")
        
    else:
        print(f"❌ Failed to get dashboard: {response.status_code}")
        print(f"   Error: {response.text}")

if __name__ == "__main__":
    verify_user_friendly_dashboard()