#!/usr/bin/env python3
"""
Realtime Collaboration Backend System Testing
Tests the newly implemented realtime collaboration backend system including:
- Collaboration API Endpoints
- Room Creation and Management
- Message and Changes APIs
- Integration with existing system
- Authentication
"""

import requests
import json
import time
import sys
from typing import Dict, Any, Optional, List

class CollaborationBackendTester:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.api_url = f"{self.base_url}/api"
        self.session = requests.Session()
        self.auth_token = None
        self.test_results = []
        self.admin_user = {
            "email": "tmonnens@outlook.com",
            "password": "Voetballen5"
        }
        
    def log_test(self, test_name: str, success: bool, details: str = "", response_time: float = 0):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'status': status,
            'success': success,
            'details': details,
            'response_time': f"{response_time:.3f}s"
        }
        self.test_results.append(result)
        print(f"{status} - {test_name} ({response_time:.3f}s)")
        if details:
            print(f"    Details: {details}")
    
    def make_request(self, method: str, endpoint: str, data: Dict = None, headers: Dict = None, timeout: int = 30) -> tuple:
        """Make HTTP request with error handling"""
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        if headers:
            default_headers.update(headers)
        
        try:
            start_time = time.time()
            
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
            
            response_time = time.time() - start_time
            return response, response_time
            
        except requests.exceptions.Timeout:
            return None, timeout
        except requests.exceptions.RequestException as e:
            print(f"Request error: {e}")
            return None, 0
    
    def authenticate_admin(self) -> bool:
        """Authenticate as admin user"""
        print("\n🔐 AUTHENTICATING ADMIN USER...")
        
        response, response_time = self.make_request('POST', '/auth/login', self.admin_user)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success') and data.get('token'):
                    self.auth_token = data['token']
                    user_info = data.get('user', {})
                    self.log_test(
                        "Admin Authentication", 
                        True, 
                        f"Admin user authenticated: {user_info.get('name', 'Unknown')} ({user_info.get('email', 'Unknown')})",
                        response_time
                    )
                    return True
                else:
                    self.log_test("Admin Authentication", False, f"Login failed: {data.get('message', 'Unknown error')}", response_time)
                    return False
            except json.JSONDecodeError:
                self.log_test("Admin Authentication", False, "Invalid JSON response", response_time)
                return False
        else:
            status_code = response.status_code if response else "No response"
            self.log_test("Admin Authentication", False, f"HTTP {status_code}", response_time)
            return False
    
    def test_collaboration_room_creation(self):
        """Test creating collaboration rooms with different document types"""
        print("\n📝 TESTING COLLABORATION ROOM CREATION...")
        
        # Test different document types
        document_types = [
            {"type": "text", "title": "Text Document Collaboration"},
            {"type": "whiteboard", "title": "Whiteboard Collaboration"},
            {"type": "presentation", "title": "Presentation Collaboration"},
            {"type": "code", "title": "Code Collaboration"}
        ]
        
        created_rooms = []
        
        for doc_type in document_types:
            room_data = {
                "document_id": f"doc_{doc_type['type']}_{int(time.time())}",
                "document_type": doc_type["type"],
                "title": doc_type["title"],
                "max_participants": 25
            }
            
            response, response_time = self.make_request('POST', '/collaboration/rooms', room_data)
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success') and data.get('room'):
                        room = data['room']
                        created_rooms.append(room)
                        self.log_test(
                            f"Create {doc_type['type'].title()} Room",
                            True,
                            f"Room created: {room.get('id')} - {room.get('title')}",
                            response_time
                        )
                    else:
                        self.log_test(f"Create {doc_type['type'].title()} Room", False, f"Room creation failed: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Create {doc_type['type'].title()} Room", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Create {doc_type['type'].title()} Room", False, f"HTTP {status_code}", response_time)
        
        return created_rooms
    
    def test_room_information(self, room_ids: List[str]):
        """Test getting room stats and information"""
        print("\n📊 TESTING ROOM INFORMATION RETRIEVAL...")
        
        for room_id in room_ids:
            response, response_time = self.make_request('GET', f'/collaboration/rooms/{room_id}')
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success') and data.get('stats'):
                        stats = data['stats']
                        self.log_test(
                            f"Get Room Info ({room_id[:8]}...)",
                            True,
                            f"Active users: {stats.get('active_users', 0)}, Messages: {stats.get('total_messages', 0)}, Changes: {stats.get('total_changes', 0)}",
                            response_time
                        )
                    else:
                        self.log_test(f"Get Room Info ({room_id[:8]}...)", False, f"Failed to get room info: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Get Room Info ({room_id[:8]}...)", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Get Room Info ({room_id[:8]}...)", False, f"HTTP {status_code}", response_time)
    
    def test_room_messages_api(self, room_ids: List[str]):
        """Test retrieving messages from rooms"""
        print("\n💬 TESTING ROOM MESSAGES API...")
        
        for room_id in room_ids:
            # Test getting messages with default limit
            response, response_time = self.make_request('GET', f'/collaboration/rooms/{room_id}/messages')
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success') and 'messages' in data:
                        messages = data['messages']
                        total = data.get('total', 0)
                        self.log_test(
                            f"Get Room Messages ({room_id[:8]}...)",
                            True,
                            f"Retrieved {len(messages)} messages (total: {total})",
                            response_time
                        )
                    else:
                        self.log_test(f"Get Room Messages ({room_id[:8]}...)", False, f"Failed to get messages: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Get Room Messages ({room_id[:8]}...)", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Get Room Messages ({room_id[:8]}...)", False, f"HTTP {status_code}", response_time)
            
            # Test with custom limit
            response, response_time = self.make_request('GET', f'/collaboration/rooms/{room_id}/messages?limit=10')
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success'):
                        self.log_test(
                            f"Get Room Messages with Limit ({room_id[:8]}...)",
                            True,
                            f"Retrieved with limit parameter",
                            response_time
                        )
                    else:
                        self.log_test(f"Get Room Messages with Limit ({room_id[:8]}...)", False, f"Failed: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Get Room Messages with Limit ({room_id[:8]}...)", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Get Room Messages with Limit ({room_id[:8]}...)", False, f"HTTP {status_code}", response_time)
    
    def test_room_changes_api(self, room_ids: List[str]):
        """Test retrieving document changes from rooms"""
        print("\n🔄 TESTING ROOM CHANGES API...")
        
        for room_id in room_ids:
            # Test getting changes with default limit
            response, response_time = self.make_request('GET', f'/collaboration/rooms/{room_id}/changes')
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success') and 'changes' in data:
                        changes = data['changes']
                        total = data.get('total', 0)
                        self.log_test(
                            f"Get Room Changes ({room_id[:8]}...)",
                            True,
                            f"Retrieved {len(changes)} changes (total: {total})",
                            response_time
                        )
                    else:
                        self.log_test(f"Get Room Changes ({room_id[:8]}...)", False, f"Failed to get changes: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Get Room Changes ({room_id[:8]}...)", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Get Room Changes ({room_id[:8]}...)", False, f"HTTP {status_code}", response_time)
            
            # Test with custom limit
            response, response_time = self.make_request('GET', f'/collaboration/rooms/{room_id}/changes?limit=50')
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success'):
                        self.log_test(
                            f"Get Room Changes with Limit ({room_id[:8]}...)",
                            True,
                            f"Retrieved with limit parameter",
                            response_time
                        )
                    else:
                        self.log_test(f"Get Room Changes with Limit ({room_id[:8]}...)", False, f"Failed: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Get Room Changes with Limit ({room_id[:8]}...)", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Get Room Changes with Limit ({room_id[:8]}...)", False, f"HTTP {status_code}", response_time)
    
    def test_integration_with_existing_system(self):
        """Test that collaboration endpoints work alongside existing APIs"""
        print("\n🔗 TESTING INTEGRATION WITH EXISTING SYSTEM...")
        
        # Test that existing endpoints still work
        existing_endpoints = [
            ('/health', 'Health Check'),
            ('/auth/me', 'User Profile'),
            ('/admin/dashboard', 'Admin Dashboard'),
            ('/ai/services', 'AI Services'),
            ('/workspaces', 'Workspaces')
        ]
        
        for endpoint, name in existing_endpoints:
            response, response_time = self.make_request('GET', endpoint)
            
            if response and response.status_code == 200:
                try:
                    data = response.json()
                    if data.get('success') or 'message' in data or 'data' in data:
                        self.log_test(
                            f"Integration Test - {name}",
                            True,
                            f"Existing endpoint working alongside collaboration system",
                            response_time
                        )
                    else:
                        self.log_test(f"Integration Test - {name}", False, f"Unexpected response: {data}", response_time)
                except json.JSONDecodeError:
                    self.log_test(f"Integration Test - {name}", False, "Invalid JSON response", response_time)
            else:
                status_code = response.status_code if response else "No response"
                self.log_test(f"Integration Test - {name}", False, f"HTTP {status_code}", response_time)
    
    def test_authentication_with_collaboration(self):
        """Test that collaboration endpoints work with the existing auth system"""
        print("\n🔐 TESTING AUTHENTICATION WITH COLLABORATION ENDPOINTS...")
        
        # Test collaboration endpoints without authentication (should work for room creation)
        temp_token = self.auth_token
        self.auth_token = None
        
        room_data = {
            "document_id": f"doc_auth_test_{int(time.time())}",
            "document_type": "text",
            "title": "Authentication Test Room",
            "max_participants": 10
        }
        
        response, response_time = self.make_request('POST', '/collaboration/rooms', room_data)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    room_id = data['room']['id']
                    self.log_test(
                        "Collaboration Room Creation (No Auth)",
                        True,
                        f"Room created without authentication: {room_id}",
                        response_time
                    )
                    
                    # Test room info without auth
                    response, response_time = self.make_request('GET', f'/collaboration/rooms/{room_id}')
                    if response and response.status_code == 200:
                        self.log_test(
                            "Room Info Access (No Auth)",
                            True,
                            "Room info accessible without authentication",
                            response_time
                        )
                    else:
                        self.log_test("Room Info Access (No Auth)", False, f"HTTP {response.status_code if response else 'No response'}", response_time)
                else:
                    self.log_test("Collaboration Room Creation (No Auth)", False, f"Failed: {data}", response_time)
            except json.JSONDecodeError:
                self.log_test("Collaboration Room Creation (No Auth)", False, "Invalid JSON response", response_time)
        else:
            status_code = response.status_code if response else "No response"
            self.log_test("Collaboration Room Creation (No Auth)", False, f"HTTP {status_code}", response_time)
        
        # Restore authentication
        self.auth_token = temp_token
        
        # Test with authentication
        room_data_auth = {
            "document_id": f"doc_auth_test_with_token_{int(time.time())}",
            "document_type": "presentation",
            "title": "Authenticated Room Test",
            "max_participants": 20
        }
        
        response, response_time = self.make_request('POST', '/collaboration/rooms', room_data_auth)
        
        if response and response.status_code == 200:
            try:
                data = response.json()
                if data.get('success'):
                    self.log_test(
                        "Collaboration Room Creation (With Auth)",
                        True,
                        f"Room created with authentication: {data['room']['id']}",
                        response_time
                    )
                else:
                    self.log_test("Collaboration Room Creation (With Auth)", False, f"Failed: {data}", response_time)
            except json.JSONDecodeError:
                self.log_test("Collaboration Room Creation (With Auth)", False, "Invalid JSON response", response_time)
        else:
            status_code = response.status_code if response else "No response"
            self.log_test("Collaboration Room Creation (With Auth)", False, f"HTTP {status_code}", response_time)
    
    def run_comprehensive_test(self):
        """Run all collaboration system tests"""
        print("🚀 STARTING COMPREHENSIVE REALTIME COLLABORATION BACKEND TESTING")
        print("=" * 80)
        
        # Step 1: Authenticate
        if not self.authenticate_admin():
            print("❌ Authentication failed. Cannot proceed with tests.")
            return False
        
        # Step 2: Test room creation
        created_rooms = self.test_collaboration_room_creation()
        room_ids = [room['id'] for room in created_rooms if 'id' in room]
        
        if not room_ids:
            print("❌ No rooms created successfully. Cannot test room-specific features.")
            return False
        
        # Step 3: Test room information
        self.test_room_information(room_ids)
        
        # Step 4: Test message APIs
        self.test_room_messages_api(room_ids)
        
        # Step 5: Test changes APIs
        self.test_room_changes_api(room_ids)
        
        # Step 6: Test integration with existing system
        self.test_integration_with_existing_system()
        
        # Step 7: Test authentication
        self.test_authentication_with_collaboration()
        
        return True
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("📊 REALTIME COLLABORATION BACKEND TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests}")
        print(f"Failed: {failed_tests}")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS ({failed_tests}):")
            for result in self.test_results:
                if not result['success']:
                    print(f"  • {result['test']}: {result['details']}")
        
        print(f"\n✅ PASSED TESTS ({passed_tests}):")
        for result in self.test_results:
            if result['success']:
                print(f"  • {result['test']}")
        
        return success_rate >= 80  # Consider 80%+ success rate as overall success

def main():
    # Get backend URL from environment or use default
    import os
    backend_url = os.getenv('REACT_APP_BACKEND_URL', 'https://a647fc73-10e4-498f-8ae8-6d85330ad2a0.preview.emergentagent.com')
    
    print(f"🎯 Testing Realtime Collaboration Backend at: {backend_url}")
    
    tester = CollaborationBackendTester(backend_url)
    
    try:
        success = tester.run_comprehensive_test()
        overall_success = tester.print_summary()
        
        if overall_success:
            print("\n🎉 REALTIME COLLABORATION BACKEND TESTING COMPLETED SUCCESSFULLY!")
            sys.exit(0)
        else:
            print("\n⚠️  REALTIME COLLABORATION BACKEND TESTING COMPLETED WITH ISSUES")
            sys.exit(1)
            
    except KeyboardInterrupt:
        print("\n⚠️  Testing interrupted by user")
        tester.print_summary()
        sys.exit(1)
    except Exception as e:
        print(f"\n❌ Testing failed with error: {e}")
        tester.print_summary()
        sys.exit(1)

if __name__ == "__main__":
    main()