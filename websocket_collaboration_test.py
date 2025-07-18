#!/usr/bin/env python3
"""
WebSocket Collaboration System Testing for Mewayz Platform
Comprehensive testing of WebSocket endpoints and real-time features
"""

import requests
import json
import sys
import time
from datetime import datetime

class WebSocketCollaborationTester:
    def __init__(self, base_url="http://localhost:8001"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Use a valid test token from the test_result.md
        self.auth_token = "3|2tizxvYX1aqPpjgTN6rGz0QY2FMeWtHpnMts7GCY137499ef"
        self.test_results = {}
        self.session = requests.Session()
        self.workspace_id = "test-workspace-123"
        self.document_id = "test-document-456"
        self.session_id = None
        
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
        
    def make_request(self, method, endpoint, data=None, headers=None, auth_required=True):
        """Make HTTP request with proper headers"""
        time.sleep(0.1)  # Rate limiting
        
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if headers:
            default_headers.update(headers)
            
        # Add auth token if required and available
        if auth_required and self.auth_token:
            default_headers['Authorization'] = f'Bearer {self.auth_token}'
            
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, params=data, timeout=10)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=10)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=10)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=10)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url} after 10 seconds")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None
    
    def test_workspace_management(self):
        """Test workspace join/leave functionality"""
        print("\n=== Testing Workspace Management ===")
        
        # Test joining workspace
        join_data = {
            "workspace_id": self.workspace_id
        }
        
        response = self.make_request('POST', '/websocket/join-workspace', join_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                self.log_test("Join Workspace", True, f"Successfully joined workspace {self.workspace_id}")
                print(f"   Current users: {len(data.get('data', {}).get('current_users', []))}")
            else:
                self.log_test("Join Workspace", False, f"Join failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Join Workspace", False, f"Join request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test getting workspace users
        response = self.make_request('GET', f'/websocket/workspace-users/{self.workspace_id}')
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Get Workspace Users", True, f"Retrieved workspace users successfully")
        else:
            self.log_test("Get Workspace Users", False, f"Failed to get workspace users - Status: {response.status_code if response else 'No response'}")
        
        # Test leaving workspace
        leave_data = {
            "workspace_id": self.workspace_id
        }
        
        response = self.make_request('POST', '/websocket/leave-workspace', leave_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                self.log_test("Leave Workspace", True, f"Successfully left workspace {self.workspace_id}")
            else:
                self.log_test("Leave Workspace", False, f"Leave failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Leave Workspace", False, f"Leave request failed - Status: {response.status_code if response else 'No response'}")
    
    def test_cursor_tracking(self):
        """Test real-time cursor tracking"""
        print("\n=== Testing Cursor Tracking ===")
        
        # First join workspace for cursor tracking
        join_data = {"workspace_id": self.workspace_id}
        self.make_request('POST', '/websocket/join-workspace', join_data)
        
        # Test cursor position update
        cursor_data = {
            "workspace_id": self.workspace_id,
            "cursor_position": {
                "x": 150,
                "y": 300,
                "element_id": "bio-site-editor",
                "page_section": "header"
            },
            "page_url": "/dashboard/linkinbio/edit"
        }
        
        response = self.make_request('POST', '/websocket/update-cursor', cursor_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                self.log_test("Update Cursor Position", True, "Cursor position updated successfully")
            else:
                self.log_test("Update Cursor Position", False, f"Cursor update failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Update Cursor Position", False, f"Cursor update request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test cursor tracking with different positions
        cursor_positions = [
            {"x": 200, "y": 400, "element_id": "social-media-post", "page_section": "content"},
            {"x": 350, "y": 150, "element_id": "analytics-chart", "page_section": "dashboard"},
            {"x": 500, "y": 250, "element_id": "course-builder", "page_section": "lessons"}
        ]
        
        for i, position in enumerate(cursor_positions):
            cursor_data = {
                "workspace_id": self.workspace_id,
                "cursor_position": position,
                "page_url": f"/dashboard/test-page-{i+1}"
            }
            
            response = self.make_request('POST', '/websocket/update-cursor', cursor_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_test(f"Cursor Tracking {i+1}", True, f"Cursor position {i+1} tracked successfully")
                else:
                    self.log_test(f"Cursor Tracking {i+1}", False, f"Cursor tracking {i+1} failed")
            else:
                self.log_test(f"Cursor Tracking {i+1}", False, f"Cursor tracking {i+1} request failed")
    
    def test_document_collaboration(self):
        """Test real-time document updates"""
        print("\n=== Testing Document Collaboration ===")
        
        # Test document update
        document_data = {
            "workspace_id": self.workspace_id,
            "document_id": self.document_id,
            "document_type": "bio_site",
            "changes": {
                "operation": "insert",
                "position": 45,
                "content": "Welcome to my creator profile!",
                "element_type": "text",
                "style": {
                    "font_size": "18px",
                    "color": "#333333",
                    "font_weight": "bold"
                }
            }
        }
        
        response = self.make_request('POST', '/websocket/update-document', document_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                version = data.get('data', {}).get('version', 'unknown')
                self.log_test("Document Update", True, f"Document updated successfully - Version: {version}")
            else:
                self.log_test("Document Update", False, f"Document update failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Document Update", False, f"Document update request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test multiple document changes
        document_changes = [
            {
                "operation": "update",
                "element_id": "header-title",
                "content": "John Doe - Digital Creator",
                "element_type": "heading"
            },
            {
                "operation": "insert",
                "position": 120,
                "content": "Follow me on social media for daily updates!",
                "element_type": "text"
            },
            {
                "operation": "style_change",
                "element_id": "bio-description",
                "style": {
                    "background_color": "#f8f9fa",
                    "padding": "20px",
                    "border_radius": "8px"
                }
            }
        ]
        
        for i, change in enumerate(document_changes):
            document_data = {
                "workspace_id": self.workspace_id,
                "document_id": f"{self.document_id}-{i+1}",
                "document_type": "bio_site",
                "changes": change
            }
            
            response = self.make_request('POST', '/websocket/update-document', document_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_test(f"Document Change {i+1}", True, f"Document change {i+1} applied successfully")
                else:
                    self.log_test(f"Document Change {i+1}", False, f"Document change {i+1} failed")
            else:
                self.log_test(f"Document Change {i+1}", False, f"Document change {i+1} request failed")
    
    def test_notification_system(self):
        """Test real-time notification system"""
        print("\n=== Testing Notification System ===")
        
        # Test sending workspace notification
        notification_data = {
            "workspace_id": self.workspace_id,
            "notification_type": "collaboration_invite",
            "message": "You've been invited to collaborate on this bio site",
            "data": {
                "inviter_name": "Test User",
                "bio_site_name": "My Creator Profile",
                "permissions": ["edit", "comment"],
                "expires_at": "2024-12-31T23:59:59Z"
            }
        }
        
        response = self.make_request('POST', '/websocket/send-notification', notification_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                self.log_test("Send Notification", True, "Notification sent successfully")
            else:
                self.log_test("Send Notification", False, f"Notification failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Send Notification", False, f"Notification request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test different notification types
        notification_types = [
            {
                "type": "document_shared",
                "message": "A new document has been shared with you",
                "data": {"document_name": "Marketing Strategy", "shared_by": "Team Lead"}
            },
            {
                "type": "comment_added",
                "message": "New comment on your bio site",
                "data": {"comment": "Great design!", "commenter": "Collaborator"}
            },
            {
                "type": "version_conflict",
                "message": "Version conflict detected in document",
                "data": {"document_id": self.document_id, "conflict_type": "simultaneous_edit"}
            }
        ]
        
        for i, notif in enumerate(notification_types):
            notification_data = {
                "workspace_id": self.workspace_id,
                "notification_type": notif["type"],
                "message": notif["message"],
                "data": notif["data"]
            }
            
            response = self.make_request('POST', '/websocket/send-notification', notification_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_test(f"Notification {notif['type']}", True, f"Notification {notif['type']} sent successfully")
                else:
                    self.log_test(f"Notification {notif['type']}", False, f"Notification {notif['type']} failed")
            else:
                self.log_test(f"Notification {notif['type']}", False, f"Notification {notif['type']} request failed")
    
    def test_activity_feed(self):
        """Test workspace activity feed"""
        print("\n=== Testing Activity Feed ===")
        
        # Test getting activity feed
        activity_data = {
            "workspace_id": self.workspace_id,
            "limit": 20
        }
        
        response = self.make_request('GET', '/websocket/activity-feed', activity_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                activities = data.get('data', {}).get('activities', [])
                total = data.get('data', {}).get('total', 0)
                self.log_test("Activity Feed", True, f"Retrieved {total} activities from feed")
            else:
                self.log_test("Activity Feed", False, f"Activity feed failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Activity Feed", False, f"Activity feed request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test activity feed with different limits
        for limit in [5, 10, 50]:
            activity_data = {
                "workspace_id": self.workspace_id,
                "limit": limit
            }
            
            response = self.make_request('GET', '/websocket/activity-feed', activity_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    activities = data.get('data', {}).get('activities', [])
                    self.log_test(f"Activity Feed (limit {limit})", True, f"Retrieved {len(activities)} activities")
                else:
                    self.log_test(f"Activity Feed (limit {limit})", False, f"Activity feed with limit {limit} failed")
            else:
                self.log_test(f"Activity Feed (limit {limit})", False, f"Activity feed request with limit {limit} failed")
    
    def test_collaborative_sessions(self):
        """Test collaborative session management"""
        print("\n=== Testing Collaborative Sessions ===")
        
        # Test starting a collaborative session
        session_data = {
            "workspace_id": self.workspace_id,
            "session_type": "bio_site_editing",
            "session_data": {
                "bio_site_id": "bio-123",
                "editing_mode": "visual",
                "permissions": ["edit", "comment", "share"],
                "max_participants": 5
            }
        }
        
        response = self.make_request('POST', '/websocket/start-session', session_data)
        if response and response.status_code == 200:
            data = response.json()
            if data.get('success'):
                session_info = data.get('data', {})
                self.session_id = session_info.get('id')
                self.log_test("Start Session", True, f"Session started successfully - ID: {self.session_id}")
            else:
                self.log_test("Start Session", False, f"Session start failed: {data.get('message', 'Unknown error')}")
        else:
            self.log_test("Start Session", False, f"Session start request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test joining a session (if session was created)
        if self.session_id:
            join_session_data = {
                "workspace_id": self.workspace_id,
                "session_id": self.session_id
            }
            
            response = self.make_request('POST', '/websocket/join-session', join_session_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_test("Join Session", True, f"Successfully joined session {self.session_id}")
                else:
                    self.log_test("Join Session", False, f"Join session failed: {data.get('message', 'Unknown error')}")
            else:
                self.log_test("Join Session", False, f"Join session request failed - Status: {response.status_code if response else 'No response'}")
            
            # Test ending the session
            end_session_data = {
                "workspace_id": self.workspace_id,
                "session_id": self.session_id
            }
            
            response = self.make_request('POST', '/websocket/end-session', end_session_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    self.log_test("End Session", True, f"Session {self.session_id} ended successfully")
                else:
                    self.log_test("End Session", False, f"End session failed: {data.get('message', 'Unknown error')}")
            else:
                self.log_test("End Session", False, f"End session request failed - Status: {response.status_code if response else 'No response'}")
        
        # Test different session types
        session_types = [
            {
                "type": "social_media_planning",
                "data": {"platform": "instagram", "campaign_id": "camp-123"}
            },
            {
                "type": "course_creation",
                "data": {"course_id": "course-456", "lesson_count": 10}
            },
            {
                "type": "analytics_review",
                "data": {"date_range": "last_30_days", "metrics": ["engagement", "conversion"]}
            }
        ]
        
        for i, session_type in enumerate(session_types):
            session_data = {
                "workspace_id": self.workspace_id,
                "session_type": session_type["type"],
                "session_data": session_type["data"]
            }
            
            response = self.make_request('POST', '/websocket/start-session', session_data)
            if response and response.status_code == 200:
                data = response.json()
                if data.get('success'):
                    session_id = data.get('data', {}).get('id')
                    self.log_test(f"Session {session_type['type']}", True, f"Session {session_type['type']} started - ID: {session_id}")
                    
                    # End the session immediately
                    if session_id:
                        end_data = {
                            "workspace_id": self.workspace_id,
                            "session_id": session_id
                        }
                        self.make_request('POST', '/websocket/end-session', end_data)
                else:
                    self.log_test(f"Session {session_type['type']}", False, f"Session {session_type['type']} failed")
            else:
                self.log_test(f"Session {session_type['type']}", False, f"Session {session_type['type']} request failed")
    
    def test_error_handling(self):
        """Test error handling and validation"""
        print("\n=== Testing Error Handling ===")
        
        # Test missing workspace_id
        response = self.make_request('POST', '/websocket/join-workspace', {})
        if response and response.status_code == 400:
            self.log_test("Missing Workspace ID", True, "Properly handled missing workspace_id")
        else:
            self.log_test("Missing Workspace ID", False, "Did not properly handle missing workspace_id")
        
        # Test invalid cursor data
        invalid_cursor_data = {
            "workspace_id": self.workspace_id
            # Missing cursor_position
        }
        
        response = self.make_request('POST', '/websocket/update-cursor', invalid_cursor_data)
        if response and response.status_code == 400:
            self.log_test("Invalid Cursor Data", True, "Properly handled invalid cursor data")
        else:
            self.log_test("Invalid Cursor Data", False, "Did not properly handle invalid cursor data")
        
        # Test missing document data
        invalid_document_data = {
            "workspace_id": self.workspace_id,
            "document_id": self.document_id
            # Missing changes
        }
        
        response = self.make_request('POST', '/websocket/update-document', invalid_document_data)
        if response and response.status_code == 400:
            self.log_test("Missing Document Changes", True, "Properly handled missing document changes")
        else:
            self.log_test("Missing Document Changes", False, "Did not properly handle missing document changes")
        
        # Test invalid notification data
        invalid_notification_data = {
            "workspace_id": self.workspace_id
            # Missing notification_type and message
        }
        
        response = self.make_request('POST', '/websocket/send-notification', invalid_notification_data)
        if response and response.status_code == 400:
            self.log_test("Invalid Notification Data", True, "Properly handled invalid notification data")
        else:
            self.log_test("Invalid Notification Data", False, "Did not properly handle invalid notification data")
        
        # Test non-existent session
        invalid_session_data = {
            "workspace_id": self.workspace_id,
            "session_id": "non-existent-session-123"
        }
        
        response = self.make_request('POST', '/websocket/join-session', invalid_session_data)
        if response and response.status_code == 404:
            self.log_test("Non-existent Session", True, "Properly handled non-existent session")
        else:
            self.log_test("Non-existent Session", False, "Did not properly handle non-existent session")
    
    def test_authentication_requirements(self):
        """Test authentication requirements for WebSocket endpoints"""
        print("\n=== Testing Authentication Requirements ===")
        
        # Temporarily remove auth token
        original_token = self.auth_token
        self.auth_token = None
        
        # Test endpoints without authentication
        endpoints_to_test = [
            ('POST', '/websocket/join-workspace', {"workspace_id": self.workspace_id}),
            ('POST', '/websocket/update-cursor', {"workspace_id": self.workspace_id, "cursor_position": {"x": 100, "y": 200}}),
            ('POST', '/websocket/update-document', {"workspace_id": self.workspace_id, "document_id": self.document_id, "changes": {}}),
            ('POST', '/websocket/send-notification', {"workspace_id": self.workspace_id, "notification_type": "test", "message": "test"}),
            ('GET', '/websocket/activity-feed', {"workspace_id": self.workspace_id}),
            ('POST', '/websocket/start-session', {"workspace_id": self.workspace_id})
        ]
        
        for method, endpoint, data in endpoints_to_test:
            response = self.make_request(method, endpoint, data, auth_required=False)
            if response and response.status_code == 401:
                endpoint_name = endpoint.split('/')[-1].replace('-', ' ').title()
                self.log_test(f"Auth Required - {endpoint_name}", True, f"Properly requires authentication for {endpoint}")
            else:
                endpoint_name = endpoint.split('/')[-1].replace('-', ' ').title()
                self.log_test(f"Auth Required - {endpoint_name}", False, f"Does not properly require authentication for {endpoint}")
        
        # Restore auth token
        self.auth_token = original_token
    
    def run_all_tests(self):
        """Run all WebSocket collaboration tests"""
        print("🚀 Starting WebSocket Collaboration System Testing")
        print("=" * 60)
        
        start_time = time.time()
        
        # Run all test suites
        self.test_workspace_management()
        self.test_cursor_tracking()
        self.test_document_collaboration()
        self.test_notification_system()
        self.test_activity_feed()
        self.test_collaborative_sessions()
        self.test_error_handling()
        self.test_authentication_requirements()
        
        end_time = time.time()
        duration = end_time - start_time
        
        # Calculate results
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "=" * 60)
        print("🎯 WebSocket Collaboration System Test Results")
        print("=" * 60)
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        print(f"Duration: {duration:.2f} seconds")
        
        if failed_tests > 0:
            print("\n❌ Failed Tests:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  - {test_name}: {result['message']}")
        
        print("\n" + "=" * 60)
        
        return {
            'total_tests': total_tests,
            'passed_tests': passed_tests,
            'failed_tests': failed_tests,
            'success_rate': success_rate,
            'duration': duration,
            'test_results': self.test_results
        }

def main():
    """Main function to run WebSocket collaboration tests"""
    if len(sys.argv) > 1:
        base_url = sys.argv[1]
    else:
        base_url = "http://localhost:8001"
    
    print(f"Testing WebSocket Collaboration System at: {base_url}")
    
    tester = WebSocketCollaborationTester(base_url)
    results = tester.run_all_tests()
    
    # Exit with appropriate code
    if results['failed_tests'] > 0:
        sys.exit(1)
    else:
        sys.exit(0)

if __name__ == "__main__":
    main()