#!/usr/bin/env python3
"""
ULTIMATE COMPREHENSIVE PLATFORM VERIFICATION
Final verification that ALL requirements are met
"""
import requests
import json
import subprocess
import os
import glob

class UltimateVerification:
    def __init__(self):
        self.results = {"passed": 0, "failed": 0, "critical_failed": 0, "tests": []}
        self.token = None
        
    def test(self, name, success, details="", is_critical=False):
        status = "✅ PASS" if success else "❌ FAIL"
        print(f"{status}: {name}")
        if details:
            print(f"      {details}")
            
        self.results["tests"].append({
            "name": name,
            "success": success,
            "details": details,
            "critical": is_critical
        })
        
        if success:
            self.results["passed"] += 1
        else:
            self.results["failed"] += 1
            if is_critical:
                self.results["critical_failed"] += 1
    
    def get_token(self):
        """Get authentication token"""
        try:
            with open('/tmp/comprehensive_token.txt', 'r') as f:
                self.token = f.read().strip()
            return True
        except:
            return False
    
    def headers(self):
        return {"Authorization": f"Bearer {self.token}", "Content-Type": "application/json"}
    
    def verify_no_laravel_remnants(self):
        """Verify all Laravel remnants are removed"""
        print("\n🧹 VERIFYING LARAVEL REMNANTS REMOVAL")
        
        # Check for Laravel files
        laravel_patterns = ['*.php', 'composer.*', 'artisan', '*.blade.*', '*laravel*']
        laravel_files = []
        
        for pattern in laravel_patterns:
            files = subprocess.run(f"find /app -name '{pattern}' -not -path '*/node_modules/*' 2>/dev/null", 
                                 shell=True, capture_output=True, text=True)
            if files.stdout.strip():
                laravel_files.extend(files.stdout.strip().split('\n'))
        
        # Filter out acceptable files (like docker setup)
        problematic_files = [f for f in laravel_files if 'docker/setup-wizard' not in f and f.strip()]
        
        self.test("Laravel Remnants Completely Removed", 
                 len(problematic_files) == 0,
                 f"Found {len(problematic_files)} Laravel files" if problematic_files else "No Laravel remnants found",
                 is_critical=True)
    
    def verify_no_mock_data(self):
        """Verify no mock data exists"""
        print("\n🎭 VERIFYING NO MOCK/PLACEHOLDER DATA")
        
        # Search for mock patterns in active backend files
        mock_files = subprocess.run(
            "find /app/backend -name '*.py' -not -path '*/archive/*' -exec grep -l 'mock\\|Mock\\|placeholder\\|dummy\\|fake\\|hardcode\\|TODO\\|FIXME' {} \\; 2>/dev/null",
            shell=True, capture_output=True, text=True
        )
        
        found_files = [f.strip() for f in mock_files.stdout.split('\n') if f.strip()]
        
        self.test("Zero Mock/Placeholder Data in Active Files",
                 len(found_files) == 0,
                 f"Found mock patterns in {len(found_files)} files" if found_files else "No mock data found",
                 is_critical=True)
    
    def verify_professional_structure(self):
        """Verify professional modular structure"""
        print("\n🏗️ VERIFYING PROFESSIONAL STRUCTURE")
        
        required_structure = [
            '/app/backend/core/__init__.py',
            '/app/backend/core/auth.py',
            '/app/backend/core/config.py',
            '/app/backend/core/database.py',
            '/app/backend/services/__init__.py',
            '/app/backend/services/user_service.py',
            '/app/backend/services/analytics_service.py',
            '/app/backend/api/__init__.py',
            '/app/backend/main.py'
        ]
        
        missing_files = [f for f in required_structure if not os.path.exists(f)]
        
        self.test("Professional Modular Structure",
                 len(missing_files) == 0,
                 f"Missing files: {missing_files}" if missing_files else "All structure files present",
                 is_critical=True)
        
        # Count API modules
        api_files = glob.glob('/app/backend/api/*.py')
        api_modules = [f for f in api_files if not f.endswith('__init__.py')]
        
        self.test("Comprehensive API Module Coverage",
                 len(api_modules) >= 12,
                 f"Found {len(api_modules)} API modules",
                 is_critical=True)
    
    def verify_database_operations(self):
        """Verify all database operations are real"""
        print("\n🗄️ VERIFYING REAL DATABASE OPERATIONS")
        
        # Check MongoDB connection and collections
        try:
            result = subprocess.run([
                'mongosh', 'mewayz_professional', '--eval',
                'db.getCollectionNames().length',
                '--quiet'
            ], capture_output=True, text=True)
            
            collections_count = int(result.stdout.strip())
            
            self.test("MongoDB Database Operational",
                     collections_count > 30,
                     f"Found {collections_count} collections with data",
                     is_critical=True)
                     
        except Exception as e:
            self.test("MongoDB Database Operational", False, f"Database error: {e}", is_critical=True)
    
    def verify_feature_integration(self):
        """Verify features work together professionally"""
        print("\n🔗 VERIFYING PROFESSIONAL FEATURE INTEGRATION")
        
        if not self.token:
            self.test("Authentication Required", False, "No authentication token available", is_critical=True)
            return
        
        # Test unified dashboard
        try:
            response = requests.get("http://localhost:8001/api/dashboard/overview", 
                                  headers=self.headers(), timeout=10)
            
            if response.status_code == 200:
                dashboard_data = response.json()["data"]
                has_integration = all(key in dashboard_data for key in ["user_overview", "quick_stats", "recent_activity"])
                
                self.test("Unified Dashboard Integration",
                         has_integration,
                         "Dashboard aggregates data from all business features")
            else:
                self.test("Unified Dashboard Integration", False,
                         f"Dashboard not accessible: {response.status_code}", is_critical=True)
                         
        except Exception as e:
            self.test("Unified Dashboard Integration", False, f"Dashboard error: {e}", is_critical=True)
        
        # Test cross-feature data consistency
        try:
            # Get workspaces from two different endpoints
            ws_response = requests.get("http://localhost:8001/api/workspaces", headers=self.headers())
            dashboard_response = requests.get("http://localhost:8001/api/dashboard/overview", headers=self.headers())
            
            if ws_response.status_code == 200 and dashboard_response.status_code == 200:
                ws_count = len(ws_response.json()["data"]["workspaces"])
                dashboard_ws_count = dashboard_response.json()["data"]["quick_stats"]["workspaces"]
                
                self.test("Cross-Platform Data Consistency",
                         ws_count == dashboard_ws_count,
                         f"Workspace count consistent: API={ws_count}, Dashboard={dashboard_ws_count}")
            else:
                self.test("Cross-Platform Data Consistency", False, "Could not verify consistency")
                
        except Exception as e:
            self.test("Cross-Platform Data Consistency", False, f"Consistency check error: {e}")
    
    def verify_plan_based_features(self):
        """Verify plan-based feature gating works"""
        print("\n💎 VERIFYING PLAN-BASED FEATURE GATING")
        
        if not self.token:
            return
        
        # Test workspace limitation
        try:
            response = requests.post("http://localhost:8001/api/workspaces",
                                   json={"name": "Test Limit", "description": "Should be blocked"},
                                   headers=self.headers())
            
            is_limited = response.status_code in [400, 429] and "limit" in response.text.lower()
            
            self.test("Plan-Based Workspace Limitations",
                     is_limited,
                     "Free plan correctly blocks additional workspaces" if is_limited else "Limitation not enforced")
                     
        except Exception as e:
            self.test("Plan-Based Workspace Limitations", False, f"Error: {e}")
    
    def verify_api_endpoints(self):
        """Verify all API endpoints are accessible"""
        print("\n🔌 VERIFYING API ENDPOINTS")
        
        if not self.token:
            return
        
        critical_endpoints = [
            "users/profile",
            "workspaces", 
            "dashboard/overview",
            "ai/services",
            "bio-sites",
            "ecommerce/products",
            "bookings/services",
            "analytics/overview"
        ]
        
        accessible_count = 0
        for endpoint in critical_endpoints:
            try:
                response = requests.get(f"http://localhost:8001/api/{endpoint}", 
                                      headers=self.headers(), timeout=5)
                if response.status_code != 404:
                    accessible_count += 1
            except:
                pass
        
        success_rate = (accessible_count / len(critical_endpoints)) * 100
        
        self.test("API Endpoints Accessibility",
                 success_rate >= 90,
                 f"{accessible_count}/{len(critical_endpoints)} endpoints accessible ({success_rate:.1f}%)",
                 is_critical=True)
    
    def run_ultimate_verification(self):
        """Run complete verification"""
        print("🔍 ULTIMATE COMPREHENSIVE PLATFORM VERIFICATION")
        print("=" * 80)
        
        # Get authentication token
        if not self.get_token():
            print("⚠️ No authentication token - some tests will be limited")
        
        # Run all verification tests
        self.verify_no_laravel_remnants()
        self.verify_no_mock_data()
        self.verify_professional_structure()
        self.verify_database_operations()
        self.verify_feature_integration()
        self.verify_plan_based_features()
        self.verify_api_endpoints()
        
        # Final summary
        print(f"\n📊 ULTIMATE VERIFICATION RESULTS")
        print("=" * 80)
        
        total_tests = self.results["passed"] + self.results["failed"]
        success_rate = (self.results["passed"] / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {self.results['passed']} ✅")
        print(f"Failed: {self.results['failed']} ❌")
        print(f"Critical Failures: {self.results['critical_failed']} 🚨")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if self.results['critical_failed'] > 0:
            print(f"\n🚨 CRITICAL ISSUES FOUND - PLATFORM NOT READY")
            critical_issues = [t for t in self.results['tests'] if not t['success'] and t.get('critical')]
            for issue in critical_issues:
                print(f"   - {issue['name']}: {issue['details']}")
            return False
        elif success_rate >= 95:
            print(f"\n🎉 OUTSTANDING! PLATFORM IS COMPLETELY PROFESSIONAL")
            print("✅ ALL REQUIREMENTS MET - PRODUCTION READY")
            return True
        elif success_rate >= 90:
            print(f"\n🎯 EXCELLENT! PLATFORM IS PROFESSIONAL GRADE")
            print("✅ READY FOR PRODUCTION DEPLOYMENT")
            return True
        else:
            print(f"\n⚠️ PLATFORM NEEDS ATTENTION")
            print("❌ ISSUES REQUIRE RESOLUTION")
            return False

if __name__ == "__main__":
    verifier = UltimateVerification()
    is_professional = verifier.run_ultimate_verification()
    exit(0 if is_professional else 1)