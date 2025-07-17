#!/usr/bin/env python3
"""
Escrow Dispute Testing
Testing the dispute functionality of the escrow system
"""

import requests
import json
import sys
import time
from datetime import datetime

class EscrowDisputeTester:
    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        # Buyer token
        self.buyer_token = "6|p896WtfIk5xpt8EIdVZxzHBBRmxZX0C3eWbxevzHc5980fb8"
        self.buyer_id = 4
        # Seller token  
        self.seller_token = "7|sM5JCtKSXGxDFa9ZxlAqFtrj02xd6Timw8XBYMXH717e69b4"
        self.seller_id = 5
        self.test_results = {}
        self.session = requests.Session()
        self.escrow_id = None
        
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
        
    def make_request(self, method, endpoint, data=None, headers=None, token=None):
        """Make HTTP request with proper headers"""
        url = f"{self.api_url}{endpoint}"
        
        # Set default headers
        default_headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        
        if headers:
            default_headers.update(headers)
            
        # Add auth token
        if token:
            default_headers['Authorization'] = f'Bearer {token}'
            
        try:
            print(f"\nMaking {method} request to: {url}")
            if data:
                print(f"Request data: {json.dumps(data, indent=2)}")
                
            if method.upper() == 'GET':
                response = self.session.get(url, headers=default_headers, params=data, timeout=30)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=default_headers, json=data, timeout=30)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=default_headers, json=data, timeout=30)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=default_headers, timeout=30)
            else:
                raise ValueError(f"Unsupported HTTP method: {method}")
                
            print(f"Response status: {response.status_code}")
            if response.text:
                try:
                    response_json = response.json()
                    print(f"Response data: {json.dumps(response_json, indent=2)}")
                except:
                    print(f"Response text: {response.text[:500]}...")
                    
            return response
            
        except requests.exceptions.Timeout:
            print(f"Request timeout for {url}")
            return None
        except requests.exceptions.RequestException as e:
            print(f"Request failed for {url}: {e}")
            return None
    
    def create_escrow_for_dispute_testing(self):
        """Create an escrow transaction for dispute testing"""
        print("📝 Creating Escrow Transaction for Dispute Testing")
        
        escrow_data = {
            "seller_id": self.seller_id,
            "item_type": "service",
            "item_title": "Custom Logo Design",
            "item_description": "Professional logo design with 3 revisions included",
            "total_amount": 150.00,
            "currency": "USD",
            "escrow_fee_percentage": 2.5,
            "inspection_period_hours": 48,
            "terms_conditions": "Logo design with 3 revisions. Final files delivered in multiple formats."
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data, token=self.buyer_token)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.escrow_id = data.get('data', {}).get('id')
            self.log_test("Create Escrow for Dispute Test", True, f"Escrow created - ID: {self.escrow_id}")
            
            # Fund the transaction
            fund_data = {
                "payment_method": "stripe",
                "payment_details": {"card_token": "tok_test_visa"}
            }
            response = self.make_request('POST', f'/escrow/{self.escrow_id}/fund', fund_data, token=self.buyer_token)
            if response and response.status_code == 200:
                self.log_test("Fund Escrow for Dispute Test", True, "Escrow funded successfully")
                return True
            else:
                self.log_test("Fund Escrow for Dispute Test", False, "Failed to fund escrow")
                return False
        else:
            self.log_test("Create Escrow for Dispute Test", False, "Failed to create escrow")
            return False
    
    def test_dispute_workflow(self):
        """Test the complete dispute workflow"""
        print("\n⚖️ Testing Dispute Workflow")
        print("=" * 60)
        
        if not self.escrow_id:
            print("No escrow transaction available for dispute testing")
            return
        
        # Test 1: Create dispute (as buyer)
        print("\n🚨 STEP 1: Create Dispute (Buyer)")
        dispute_data = {
            "reason": "not_as_described",
            "description": "The logo design does not match the specifications discussed. The colors are wrong and the style is completely different from what was requested.",
            "evidence": ["original_brief.pdf", "received_design.png", "comparison_screenshot.png"],
            "requested_resolution": "partial_refund"
        }
        
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/dispute', dispute_data, token=self.buyer_token)
        if response and response.status_code in [200, 201]:
            data = response.json()
            dispute_id = data.get('data', {}).get('id')
            self.log_test("Create Dispute", True, f"Dispute created successfully - ID: {dispute_id}")
        else:
            self.log_test("Create Dispute", False, f"Failed to create dispute - Status: {response.status_code if response else 'No response'}")
        
        # Test 2: Try to create another dispute (should fail)
        print("\n🚫 STEP 2: Try to Create Duplicate Dispute (Should Fail)")
        duplicate_dispute_data = {
            "reason": "damaged",
            "description": "Another dispute reason",
            "evidence": ["evidence.png"],
            "requested_resolution": "full_refund"
        }
        
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/dispute', duplicate_dispute_data, token=self.buyer_token)
        if response and response.status_code == 400:
            self.log_test("Prevent Duplicate Dispute", True, "Correctly prevented duplicate dispute creation")
        else:
            self.log_test("Prevent Duplicate Dispute", False, f"Should have prevented duplicate dispute - Status: {response.status_code if response else 'No response'}")
        
        # Test 3: Check escrow status after dispute
        print("\n🔍 STEP 3: Check Escrow Status After Dispute")
        response = self.make_request('GET', f'/escrow/{self.escrow_id}', token=self.buyer_token)
        if response and response.status_code == 200:
            data = response.json()
            status = data.get('data', {}).get('status')
            disputes = data.get('data', {}).get('disputes', [])
            if status == 'disputed' and len(disputes) > 0:
                self.log_test("Escrow Status After Dispute", True, f"Escrow status correctly updated to 'disputed' with {len(disputes)} dispute(s)")
            else:
                self.log_test("Escrow Status After Dispute", False, f"Escrow status not updated correctly - Status: {status}, Disputes: {len(disputes)}")
        else:
            self.log_test("Escrow Status After Dispute", False, "Failed to retrieve escrow after dispute")
        
        # Test 4: Test dispute creation by seller
        print("\n🔄 STEP 4: Test Dispute Creation by Seller (Different Transaction)")
        # Create another escrow for seller dispute test
        escrow_data = {
            "seller_id": self.seller_id,
            "item_type": "digital_asset",
            "item_title": "Website Development",
            "item_description": "Custom website development",
            "total_amount": 500.00,
            "currency": "USD"
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data, token=self.buyer_token)
        if response and response.status_code in [200, 201]:
            data = response.json()
            escrow_id_2 = data.get('data', {}).get('id')
            
            # Fund it
            fund_data = {"payment_method": "stripe", "payment_details": {"card_token": "tok_test"}}
            self.make_request('POST', f'/escrow/{escrow_id_2}/fund', fund_data, token=self.buyer_token)
            
            # Seller creates dispute
            seller_dispute_data = {
                "reason": "unauthorized_charges",
                "description": "Buyer requested additional work beyond the original scope without agreeing to additional payment",
                "evidence": ["original_contract.pdf", "additional_requests.png"],
                "requested_resolution": "completion"
            }
            
            response = self.make_request('POST', f'/escrow/{escrow_id_2}/dispute', seller_dispute_data, token=self.seller_token)
            if response and response.status_code in [200, 201]:
                self.log_test("Seller Create Dispute", True, "Seller successfully created dispute")
            else:
                self.log_test("Seller Create Dispute", False, f"Seller failed to create dispute - Status: {response.status_code if response else 'No response'}")
        else:
            self.log_test("Seller Create Dispute", False, "Failed to create escrow for seller dispute test")
    
    def test_dispute_validation(self):
        """Test dispute validation"""
        print("\n✅ Testing Dispute Validation")
        print("=" * 40)
        
        if not self.escrow_id:
            return
        
        # Test invalid reason
        print("\n❌ Testing Invalid Dispute Reason")
        invalid_dispute = {
            "reason": "invalid_reason",
            "description": "Test description",
            "evidence": [],
            "requested_resolution": "full_refund"
        }
        
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/dispute', invalid_dispute, token=self.buyer_token)
        if response and response.status_code == 422:
            self.log_test("Invalid Dispute Reason Validation", True, "Correctly rejected invalid dispute reason")
        else:
            self.log_test("Invalid Dispute Reason Validation", False, f"Should have rejected invalid reason - Status: {response.status_code if response else 'No response'}")
        
        # Test missing required fields
        print("\n❌ Testing Missing Required Fields")
        incomplete_dispute = {
            "reason": "not_delivered"
            # Missing description and requested_resolution
        }
        
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/dispute', incomplete_dispute, token=self.buyer_token)
        if response and response.status_code == 422:
            self.log_test("Missing Fields Validation", True, "Correctly rejected dispute with missing fields")
        else:
            self.log_test("Missing Fields Validation", False, f"Should have rejected incomplete dispute - Status: {response.status_code if response else 'No response'}")
    
    def run_tests(self):
        """Run all dispute tests"""
        print("🚀 Starting Escrow Dispute System Testing")
        print("=" * 80)
        
        # Create escrow for testing
        if not self.create_escrow_for_dispute_testing():
            print("❌ Failed to create escrow for testing - cannot proceed")
            return
        
        # Test dispute workflow
        self.test_dispute_workflow()
        
        # Test validation
        self.test_dispute_validation()
        
        # Print summary
        self.print_summary()
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("📊 ESCROW DISPUTE SYSTEM TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        if failed_tests > 0:
            print(f"\n🔍 FAILED TESTS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("\n✅ PASSED TESTS:")
        for test_name, result in self.test_results.items():
            if result['success']:
                print(f"  ✅ {test_name}: {result['message']}")
        
        print("\n" + "=" * 80)
        
        # Final assessment
        if passed_tests >= total_tests * 0.8:
            print("🎉 DISPUTE SYSTEM STATUS: FULLY FUNCTIONAL")
        elif passed_tests >= total_tests * 0.6:
            print("⚠️ DISPUTE SYSTEM STATUS: MOSTLY FUNCTIONAL")
        else:
            print("❌ DISPUTE SYSTEM STATUS: NEEDS ATTENTION")

if __name__ == "__main__":
    tester = EscrowDisputeTester()
    tester.run_tests()
    
    failed_count = sum(1 for result in tester.test_results.values() if not result['success'])
    sys.exit(1 if failed_count > 0 else 0)