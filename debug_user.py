#!/usr/bin/env python3
"""
Debug user authentication and service calls
"""

import requests
import json

# Backend URL
BACKEND_URL = "https://7cfbae80-c985-4454-b805-9babb474ff5c.preview.emergentagent.com"
API_BASE = f"{BACKEND_URL}/api"

# Admin credentials
ADMIN_EMAIL = "tmonnens@outlook.com"
ADMIN_PASSWORD = "Voetballen5"

def test_user_profile():
    """Test user profile endpoint to see user object structure"""
    
    # Authenticate
    auth_data = {"username": ADMIN_EMAIL, "password": ADMIN_PASSWORD}
    response = requests.post(f"{API_BASE}/auth/login", data=auth_data)
    
    if response.status_code != 200:
        print(f"❌ Authentication failed: {response.status_code}")
        return
    
    token = response.json()["access_token"]
    headers = {"Authorization": f"Bearer {token}"}
    
    # Test user profile
    response = requests.get(f"{API_BASE}/users/profile", headers=headers)
    print(f"User Profile Status: {response.status_code}")
    if response.status_code == 200:
        user_data = response.json()
        print(f"User Data Structure: {json.dumps(user_data, indent=2)}")
    else:
        print(f"User Profile Error: {response.text}")

if __name__ == "__main__":
    test_user_profile()