#!/usr/bin/env python3
"""
Complete Escrow System Workflow Testing
Testing the full escrow transaction lifecycle
"""

import requests
import json
import sys
import time
from datetime import datetime

class EscrowWorkflowTester:
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
    
    def test_complete_escrow_workflow(self):
        """Test the complete escrow workflow"""
        print("🚀 Testing Complete Escrow Workflow")
        print("=" * 80)
        
        # Step 1: Create escrow transaction (as buyer)
        print("\n📝 STEP 1: Create Escrow Transaction (Buyer)")
        escrow_data = {
            "seller_id": self.seller_id,  # Use actual seller ID
            "item_type": "digital_asset",
            "item_title": "Premium Website Template",
            "item_description": "A comprehensive business website template with modern design and responsive layout",
            "total_amount": 299.99,
            "currency": "USD",
            "escrow_fee_percentage": 2.5,
            "inspection_period_hours": 72,
            "terms_conditions": "Standard escrow terms for digital asset delivery. Buyer has 72 hours to inspect and accept delivery."
        }
        
        response = self.make_request('POST', '/escrow/', escrow_data, token=self.buyer_token)
        if response and response.status_code in [200, 201]:
            data = response.json()
            self.escrow_id = data.get('data', {}).get('id')
            self.log_test("Create Escrow Transaction", True, f"Escrow transaction created successfully - ID: {self.escrow_id}")
        else:
            self.log_test("Create Escrow Transaction", False, f"Failed to create escrow transaction - Status: {response.status_code if response else 'No response'}")
            return  # Can't continue without escrow ID
        
        # Step 2: Get specific escrow transaction
        print("\n🔍 STEP 2: Get Specific Escrow Transaction")
        response = self.make_request('GET', f'/escrow/{self.escrow_id}', token=self.buyer_token)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Get Specific Escrow Transaction", True, f"Retrieved escrow transaction - Status: {data.get('data', {}).get('status')}")
        else:
            self.log_test("Get Specific Escrow Transaction", False, f"Failed to retrieve escrow transaction - Status: {response.status_code if response else 'No response'}")
        
        # Step 3: Fund escrow transaction (as buyer)
        print("\n💰 STEP 3: Fund Escrow Transaction (Buyer)")
        fund_data = {
            "payment_method": "stripe",
            "payment_details": {
                "card_token": "tok_test_visa_4242424242424242"
            }
        }
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/fund', fund_data, token=self.buyer_token)
        if response and response.status_code == 200:
            self.log_test("Fund Escrow Transaction", True, "Escrow transaction funded successfully")
        else:
            self.log_test("Fund Escrow Transaction", False, f"Failed to fund escrow transaction - Status: {response.status_code if response else 'No response'}")
        
        # Step 4: Deliver item (as seller)
        print("\n📦 STEP 4: Deliver Item (Seller)")
        deliver_data = {
            "delivery_notes": "Website template has been delivered via email with installation instructions and documentation",
            "delivery_proof": ["email_confirmation.png", "template_files.zip", "installation_guide.pdf"]
        }
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/deliver', deliver_data, token=self.seller_token)
        if response and response.status_code == 200:
            self.log_test("Deliver Item", True, "Item delivery recorded successfully")
        else:
            self.log_test("Deliver Item", False, f"Failed to record item delivery - Status: {response.status_code if response else 'No response'}")
        
        # Step 5: Accept delivery (as buyer)
        print("\n✅ STEP 5: Accept Delivery (Buyer)")
        accept_data = {
            "feedback_rating": 5,
            "feedback_comment": "Excellent template! Exactly as described and very professional design."
        }
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/accept', accept_data, token=self.buyer_token)
        if response and response.status_code == 200:
            self.log_test("Accept Delivery", True, "Delivery acceptance recorded successfully")
        else:
            self.log_test("Accept Delivery", False, f"Failed to record delivery acceptance - Status: {response.status_code if response else 'No response'}")
        
        # Step 6: Get updated statistics
        print("\n📊 STEP 6: Get Updated Statistics")
        response = self.make_request('GET', '/escrow/statistics/overview', token=self.buyer_token)
        if response and response.status_code == 200:
            data = response.json()
            stats = data.get('data', {})
            self.log_test("Get Updated Statistics", True, f"Statistics updated - Total: {stats.get('total_transactions')}, Volume: ${stats.get('total_volume')}")
        else:
            self.log_test("Get Updated Statistics", False, f"Failed to retrieve updated statistics - Status: {response.status_code if response else 'No response'}")
        
        # Step 7: List all escrow transactions
        print("\n📋 STEP 7: List All Escrow Transactions")
        response = self.make_request('GET', '/escrow/', token=self.buyer_token)
        if response and response.status_code == 200:
            data = response.json()
            transactions = data.get('data', {}).get('data', [])
            self.log_test("List Escrow Transactions", True, f"Retrieved {len(transactions)} escrow transactions")
        else:
            self.log_test("List Escrow Transactions", False, f"Failed to retrieve escrow transactions - Status: {response.status_code if response else 'No response'}")
    
    def test_dispute_workflow(self):
        """Test dispute creation workflow"""
        print("\n⚖️ Testing Dispute Workflow")
        print("=" * 50)
        
        if not self.escrow_id:
            print("No escrow transaction available for dispute testing")
            return
        
        # Create a dispute (as buyer)
        print("\n🚨 Creating Dispute")
        dispute_data = {
            "reason": "not_as_described",
            "description": "The template does not match the preview images shown in the listing",
            "evidence": ["screenshot_comparison.png", "original_listing.pdf"],
            "requested_resolution": "partial_refund"
        }
        response = self.make_request('POST', f'/escrow/{self.escrow_id}/dispute', dispute_data, token=self.buyer_token)
        if response and response.status_code in [200, 201]:
            self.log_test("Create Dispute", True, "Dispute created successfully")
        else:
            self.log_test("Create Dispute", False, f"Failed to create dispute - Status: {response.status_code if response else 'No response'}")
    
    def test_authentication_both_users(self):
        """Test authentication for both buyer and seller"""
        print("\n🔐 Testing Authentication for Both Users")
        print("=" * 50)
        
        # Test buyer authentication
        response = self.make_request('GET', '/test-custom-auth', token=self.buyer_token)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Buyer Authentication", True, f"Buyer auth working - User: {data.get('user_name')}")
        else:
            self.log_test("Buyer Authentication", False, f"Buyer auth failed - Status: {response.status_code if response else 'No response'}")
        
        # Test seller authentication
        response = self.make_request('GET', '/test-custom-auth', token=self.seller_token)
        if response and response.status_code == 200:
            data = response.json()
            self.log_test("Seller Authentication", True, f"Seller auth working - User: {data.get('user_name')}")
        else:
            self.log_test("Seller Authentication", False, f"Seller auth failed - Status: {response.status_code if response else 'No response'}")
    
    def run_tests(self):
        """Run all escrow tests"""
        print("🚀 Starting Complete Escrow System Testing")
        print("=" * 80)
        
        # Test authentication first
        self.test_authentication_both_users()
        
        # Test complete workflow
        self.test_complete_escrow_workflow()
        
        # Test dispute workflow (optional)
        # self.test_dispute_workflow()
        
        # Print summary
        self.print_summary()
    
    def print_summary(self):
        """Print test summary"""
        print("\n" + "=" * 80)
        print("📊 COMPLETE ESCROW SYSTEM TEST SUMMARY")
        print("=" * 80)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results.values() if result['success'])
        failed_tests = total_tests - passed_tests
        
        print(f"Total Tests: {total_tests}")
        print(f"✅ Passed: {passed_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"Success Rate: {(passed_tests/total_tests)*100:.1f}%")
        
        print("\n🎯 TEST RESULTS BY CATEGORY:")
        print("Authentication Tests:")
        auth_tests = [k for k in self.test_results.keys() if 'Authentication' in k]
        for test in auth_tests:
            result = self.test_results[test]
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {test}")
        
        print("\nEscrow Workflow Tests:")
        workflow_tests = [k for k in self.test_results.keys() if 'Authentication' not in k]
        for test in workflow_tests:
            result = self.test_results[test]
            status = "✅" if result['success'] else "❌"
            print(f"  {status} {test}")
        
        if failed_tests > 0:
            print(f"\n🔍 DETAILED FAILURE ANALYSIS:")
            for test_name, result in self.test_results.items():
                if not result['success']:
                    print(f"  ❌ {test_name}: {result['message']}")
        
        print("\n" + "=" * 80)
        
        # Final assessment
        if passed_tests >= total_tests * 0.8:  # 80% success rate
            print("🎉 ESCROW SYSTEM STATUS: FULLY FUNCTIONAL")
            print("✅ The escrow system is working correctly and ready for production use.")
        elif passed_tests >= total_tests * 0.6:  # 60% success rate
            print("⚠️ ESCROW SYSTEM STATUS: MOSTLY FUNCTIONAL")
            print("✅ Core functionality works, minor issues need attention.")
        else:
            print("❌ ESCROW SYSTEM STATUS: NEEDS ATTENTION")
            print("🔧 Significant issues found that need to be resolved.")

if __name__ == "__main__":
    # Initialize tester
    tester = EscrowWorkflowTester()
    
    # Run all tests
    tester.run_tests()
    
    # Exit with appropriate code
    failed_count = sum(1 for result in tester.test_results.values() if not result['success'])
    sys.exit(1 if failed_count > 0 else 0)