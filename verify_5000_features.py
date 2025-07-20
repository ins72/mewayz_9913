#!/usr/bin/env python3
"""
5000+ Features Response Verification Script
Verify the actual content and feature count from the API responses
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

def verify_features_count():
    """Verify the 5000+ features count endpoint"""
    token = authenticate()
    if not token:
        print("❌ Authentication failed")
        return
    
    headers = {'Authorization': f'Bearer {token}'}
    
    print("🔍 VERIFYING 5000+ FEATURES COUNT AND CONTENT")
    print("="*60)
    
    # Test the features count endpoint
    response = requests.get(f"{API_BASE}/features/count/comprehensive-5000", headers=headers)
    
    if response.status_code == 200:
        data = response.json()
        print(f"✅ Features Count Endpoint Response:")
        print(f"   Status: {response.status_code}")
        print(f"   Response Size: {len(json.dumps(data))} characters")
        
        # Look for total features count
        if 'total_features' in data:
            total = data['total_features']
            print(f"   🎯 TOTAL FEATURES: {total}")
            if total >= 5000:
                print(f"   ✅ SUCCESS: Exceeds 5000 features target!")
            else:
                print(f"   ❌ WARNING: Below 5000 features target")
        
        # Show key data structure
        print(f"\n📊 Response Structure:")
        for key, value in data.items():
            if isinstance(value, dict):
                print(f"   {key}: {len(value)} items")
            elif isinstance(value, list):
                print(f"   {key}: {len(value)} items")
            else:
                print(f"   {key}: {value}")
        
        # Show sample content
        print(f"\n📋 Sample Response Content (first 500 chars):")
        print(json.dumps(data, indent=2)[:500] + "...")
        
    else:
        print(f"❌ Failed to get features count: {response.status_code}")
        print(f"   Error: {response.text}")

if __name__ == "__main__":
    verify_features_count()