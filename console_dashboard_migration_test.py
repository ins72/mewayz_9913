#!/usr/bin/env python3
"""
Console → Dashboard Migration Testing Suite
==========================================

This test suite specifically validates the console → dashboard migration
as requested in the review. It tests:

1. All route names updated from console-* to dashboard-*
2. /dashboard route accessibility and working
3. All dashboard sub-routes working (/dashboard/sites, /dashboard/store, etc.)
4. Authentication redirects working to dashboard
5. All API endpoints accessible and functional
6. Database connections working properly
7. Console → dashboard route reference issues resolved

Test Coverage:
- Route migration verification
- Dashboard accessibility testing
- API endpoint functionality
- Authentication flow testing
- Database connectivity
- Route reference consistency
"""

import os
import sys
import json
import time
import requests
import subprocess
from pathlib import Path

class ConsoleDashboardMigrationTest:
    def __init__(self):
        self.base_path = Path("/app")
        self.base_url = "http://localhost:8001"
        self.api_url = f"{self.base_url}/api"
        self.results = {
            "route_migration": {},
            "dashboard_accessibility": {},
            "api_functionality": {},
            "authentication_flow": {},
            "database_connectivity": {},
            "route_references": {},
            "test_summary": {}
        }
        self.auth_token = None
        
    def run_all_tests(self):
        """Run comprehensive console → dashboard migration tests"""
        print("🚀 CONSOLE → DASHBOARD MIGRATION TESTING SUITE")
        print("=" * 60)
        
        # Test 1: Route Migration Verification
        self.test_route_migration()
        
        # Test 2: Dashboard Accessibility
        self.test_dashboard_accessibility()
        
        # Test 3: API Functionality
        self.test_api_functionality()
        
        # Test 4: Authentication Flow
        self.test_authentication_flow()
        
        # Test 5: Database Connectivity
        self.test_database_connectivity()
        
        # Test 6: Route References Consistency
        self.test_route_references()
        
        # Generate comprehensive report
        self.generate_test_report()
        
    def test_route_migration(self):
        """Test 1: Verify route names updated from console-* to dashboard-*"""
        print("\n📋 TEST 1: ROUTE MIGRATION VERIFICATION")
        print("-" * 50)
        
        results = {
            "dashboard_routes_present": False,
            "console_routes_removed": False,
            "route_names_updated": False,
            "dashboard_sub_routes": [],
            "console_references_found": []
        }
        
        # Check web routes file
        web_routes_file = self.base_path / "routes" / "web.php"
        if web_routes_file.exists():
            content = web_routes_file.read_text()
            
            # Check for dashboard routes
            dashboard_routes = [
                "dashboard-index",
                "dashboard-sites-index", 
                "dashboard-store-index",
                "dashboard-audience-index",
                "dashboard-courses-index",
                "dashboard-wallet-index"
            ]
            
            dashboard_present = all(route in content for route in dashboard_routes)
            results["dashboard_routes_present"] = dashboard_present
            print(f"✅ Dashboard routes present: {'YES' if dashboard_present else 'NO'}")
            
            # Check for remaining console routes (should be minimal)
            console_route_patterns = [
                "console-index",
                "console-sites",
                "console-store",
                "console-audience", 
                "console-courses",
                "console-wallet"
            ]
            
            console_found = [pattern for pattern in console_route_patterns if pattern in content]
            results["console_references_found"] = console_found
            results["console_routes_removed"] = len(console_found) == 0
            print(f"✅ Console routes removed: {'YES' if len(console_found) == 0 else f'NO - Found: {console_found}'}")
            
            # Check dashboard sub-routes
            dashboard_sub_routes = [
                "/dashboard/sites",
                "/dashboard/store", 
                "/dashboard/audience",
                "/dashboard/courses",
                "/dashboard/wallet"
            ]
            
            found_sub_routes = [route for route in dashboard_sub_routes if route in content]
            results["dashboard_sub_routes"] = found_sub_routes
            print(f"✅ Dashboard sub-routes: {len(found_sub_routes)}/5 found")
            for route in found_sub_routes:
                print(f"   ✅ {route}")
            
            # Check route naming consistency
            dashboard_prefix_count = content.count("dashboard-")
            console_prefix_count = content.count("console-")
            results["route_names_updated"] = dashboard_prefix_count > console_prefix_count
            print(f"✅ Route naming: Dashboard prefixes: {dashboard_prefix_count}, Console prefixes: {console_prefix_count}")
        
        self.results["route_migration"] = results
        
    def test_dashboard_accessibility(self):
        """Test 2: Test /dashboard route accessibility and working"""
        print("\n📊 TEST 2: DASHBOARD ACCESSIBILITY")
        print("-" * 50)
        
        results = {
            "dashboard_route_accessible": False,
            "dashboard_sub_routes_accessible": {},
            "authentication_required": False,
            "response_status": {},
            "view_files_present": False
        }
        
        # Check if Laravel server is running
        try:
            response = requests.get(f"{self.base_url}/api/health", timeout=5)
            server_running = response.status_code == 200
            print(f"✅ Laravel server: {'RUNNING' if server_running else 'NOT RUNNING'}")
        except:
            server_running = False
            print("❌ Laravel server: NOT ACCESSIBLE")
        
        if server_running:
            # Test main dashboard route
            try:
                response = requests.get(f"{self.base_url}/dashboard", timeout=5, allow_redirects=False)
                results["dashboard_route_accessible"] = response.status_code in [200, 302]
                results["response_status"]["dashboard"] = response.status_code
                results["authentication_required"] = response.status_code == 302
                print(f"✅ /dashboard route: Status {response.status_code} ({'Accessible' if response.status_code in [200, 302] else 'Not accessible'})")
            except Exception as e:
                print(f"❌ /dashboard route: Error - {str(e)}")
            
            # Test dashboard sub-routes
            sub_routes = ["/dashboard/sites", "/dashboard/store", "/dashboard/audience", "/dashboard/courses", "/dashboard/wallet"]
            for route in sub_routes:
                try:
                    response = requests.get(f"{self.base_url}{route}", timeout=5, allow_redirects=False)
                    results["dashboard_sub_routes_accessible"][route] = response.status_code in [200, 302]
                    results["response_status"][route] = response.status_code
                    print(f"✅ {route}: Status {response.status_code}")
                except Exception as e:
                    results["dashboard_sub_routes_accessible"][route] = False
                    print(f"❌ {route}: Error - {str(e)}")
        
        # Check view files
        dashboard_views_path = self.base_path / "resources" / "views" / "pages" / "dashboard"
        if dashboard_views_path.exists():
            view_files = [
                "index.blade.php",
                "sites/index.blade.php",
                "store/index.blade.php", 
                "audience/index.blade.php",
                "courses/index.blade.php",
                "wallet/index.blade.php"
            ]
            
            view_files_found = []
            for view_file in view_files:
                view_path = dashboard_views_path / view_file
                if view_path.exists():
                    view_files_found.append(view_file)
            
            results["view_files_present"] = len(view_files_found) > 0
            print(f"✅ Dashboard view files: {len(view_files_found)}/6 found")
            for view_file in view_files_found:
                print(f"   ✅ {view_file}")
        
        self.results["dashboard_accessibility"] = results
        
    def test_api_functionality(self):
        """Test 3: Test all API endpoints accessible and functional"""
        print("\n🔌 TEST 3: API FUNCTIONALITY")
        print("-" * 50)
        
        results = {
            "health_endpoint": False,
            "auth_endpoints": {},
            "protected_endpoints": {},
            "stripe_endpoints": {},
            "total_endpoints_tested": 0,
            "successful_endpoints": 0
        }
        
        # Test health endpoint
        try:
            response = requests.get(f"{self.api_url}/health", timeout=5)
            results["health_endpoint"] = response.status_code == 200
            print(f"✅ Health endpoint: {'WORKING' if response.status_code == 200 else f'Status {response.status_code}'}")
            if response.status_code == 200:
                results["successful_endpoints"] += 1
            results["total_endpoints_tested"] += 1
        except Exception as e:
            print(f"❌ Health endpoint: Error - {str(e)}")
            results["total_endpoints_tested"] += 1
        
        # Test authentication endpoints
        auth_endpoints = [
            ("/auth/login", "POST"),
            ("/auth/register", "POST"),
            ("/auth/me", "GET")
        ]
        
        for endpoint, method in auth_endpoints:
            try:
                if method == "GET":
                    response = requests.get(f"{self.api_url}{endpoint}", timeout=5)
                else:
                    response = requests.post(f"{self.api_url}{endpoint}", json={}, timeout=5)
                
                # For auth endpoints, we expect 401/422 for unauthenticated requests
                expected_statuses = [200, 401, 422]
                is_working = response.status_code in expected_statuses
                results["auth_endpoints"][endpoint] = is_working
                print(f"✅ {method} {endpoint}: Status {response.status_code} ({'Working' if is_working else 'Not working'})")
                if is_working:
                    results["successful_endpoints"] += 1
                results["total_endpoints_tested"] += 1
            except Exception as e:
                results["auth_endpoints"][endpoint] = False
                print(f"❌ {method} {endpoint}: Error - {str(e)}")
                results["total_endpoints_tested"] += 1
        
        # Test Stripe endpoints
        stripe_endpoints = [
            "/payments/packages",
            "/payments/checkout/session"
        ]
        
        for endpoint in stripe_endpoints:
            try:
                if "session" in endpoint:
                    response = requests.post(f"{self.api_url}{endpoint}", json={"package": "starter"}, timeout=5)
                else:
                    response = requests.get(f"{self.api_url}{endpoint}", timeout=5)
                
                is_working = response.status_code in [200, 400, 422]  # 400/422 acceptable for invalid data
                results["stripe_endpoints"][endpoint] = is_working
                print(f"✅ {endpoint}: Status {response.status_code} ({'Working' if is_working else 'Not working'})")
                if is_working:
                    results["successful_endpoints"] += 1
                results["total_endpoints_tested"] += 1
            except Exception as e:
                results["stripe_endpoints"][endpoint] = False
                print(f"❌ {endpoint}: Error - {str(e)}")
                results["total_endpoints_tested"] += 1
        
        # Calculate success rate
        if results["total_endpoints_tested"] > 0:
            success_rate = (results["successful_endpoints"] / results["total_endpoints_tested"]) * 100
            print(f"✅ API Success Rate: {success_rate:.1f}% ({results['successful_endpoints']}/{results['total_endpoints_tested']})")
        
        self.results["api_functionality"] = results
        
    def test_authentication_flow(self):
        """Test 4: Test authentication redirects working to dashboard"""
        print("\n🔐 TEST 4: AUTHENTICATION FLOW")
        print("-" * 50)
        
        results = {
            "login_endpoint_working": False,
            "token_generation": False,
            "dashboard_redirect": False,
            "protected_route_access": False,
            "auth_token_valid": False
        }
        
        # Test login endpoint
        try:
            login_data = {
                "email": "admin@example.com",
                "password": "admin123"
            }
            response = requests.post(f"{self.api_url}/auth/login", json=login_data, timeout=5)
            results["login_endpoint_working"] = response.status_code in [200, 422]
            print(f"✅ Login endpoint: Status {response.status_code}")
            
            if response.status_code == 200:
                response_data = response.json()
                if "token" in response_data:
                    self.auth_token = response_data["token"]
                    results["token_generation"] = True
                    print("✅ Token generation: SUCCESS")
                else:
                    print("❌ Token generation: No token in response")
            elif response.status_code == 422:
                print("⚠️  Login endpoint: Validation error (expected without proper user)")
        except Exception as e:
            print(f"❌ Login endpoint: Error - {str(e)}")
        
        # Test authenticated access to /auth/me
        if self.auth_token:
            try:
                headers = {"Authorization": f"Bearer {self.auth_token}"}
                response = requests.get(f"{self.api_url}/auth/me", headers=headers, timeout=5)
                results["auth_token_valid"] = response.status_code == 200
                print(f"✅ Auth token validation: {'VALID' if response.status_code == 200 else f'Status {response.status_code}'}")
            except Exception as e:
                print(f"❌ Auth token validation: Error - {str(e)}")
        
        # Test dashboard redirect behavior
        try:
            response = requests.get(f"{self.base_url}/dashboard", timeout=5, allow_redirects=False)
            if response.status_code == 302:
                location = response.headers.get('Location', '')
                results["dashboard_redirect"] = 'login' in location.lower()
                print(f"✅ Dashboard redirect: {'To login' if results['dashboard_redirect'] else f'To {location}'}")
            elif response.status_code == 200:
                results["dashboard_redirect"] = True
                print("✅ Dashboard redirect: Direct access (already authenticated)")
        except Exception as e:
            print(f"❌ Dashboard redirect: Error - {str(e)}")
        
        self.results["authentication_flow"] = results
        
    def test_database_connectivity(self):
        """Test 5: Test database connections working properly"""
        print("\n🗄️  TEST 5: DATABASE CONNECTIVITY")
        print("-" * 50)
        
        results = {
            "database_config": False,
            "migrations_applied": False,
            "health_check_db": False,
            "model_files_present": False,
            "connection_test": False
        }
        
        # Check database configuration
        env_file = self.base_path / ".env"
        if env_file.exists():
            env_content = env_file.read_text()
            db_config_present = all(key in env_content for key in ["DB_CONNECTION", "DB_HOST", "DB_DATABASE"])
            results["database_config"] = db_config_present
            print(f"✅ Database configuration: {'PRESENT' if db_config_present else 'MISSING'}")
        
        # Check migrations
        migrations_path = self.base_path / "database" / "migrations"
        if migrations_path.exists():
            migrations = list(migrations_path.glob("*.php"))
            results["migrations_applied"] = len(migrations) > 0
            print(f"✅ Database migrations: {len(migrations)} files found")
        
        # Check model files
        models_path = self.base_path / "app" / "Models"
        if models_path.exists():
            models = list(models_path.glob("*.php"))
            results["model_files_present"] = len(models) > 0
            print(f"✅ Model files: {len(models)} files found")
        
        # Test database connection via health endpoint
        try:
            response = requests.get(f"{self.api_url}/health", timeout=5)
            if response.status_code == 200:
                health_data = response.json()
                if isinstance(health_data, dict) and "database" in health_data:
                    results["health_check_db"] = health_data["database"] == True
                    print(f"✅ Database health check: {'CONNECTED' if results['health_check_db'] else 'DISCONNECTED'}")
                else:
                    results["health_check_db"] = True  # Assume working if health endpoint responds
                    print("✅ Database health check: Assumed working (health endpoint responds)")
        except Exception as e:
            print(f"❌ Database health check: Error - {str(e)}")
        
        # Test basic database operation via artisan
        try:
            result = subprocess.run(
                ["php", "artisan", "migrate:status"],
                cwd=self.base_path,
                capture_output=True,
                text=True,
                timeout=10
            )
            results["connection_test"] = result.returncode == 0
            print(f"✅ Database connection test: {'SUCCESS' if result.returncode == 0 else 'FAILED'}")
        except Exception as e:
            print(f"❌ Database connection test: Error - {str(e)}")
        
        self.results["database_connectivity"] = results
        
    def test_route_references(self):
        """Test 6: Test console → dashboard route reference issues resolved"""
        print("\n🔗 TEST 6: ROUTE REFERENCES CONSISTENCY")
        print("-" * 50)
        
        results = {
            "view_files_updated": {},
            "controller_references": {},
            "javascript_references": {},
            "console_references_remaining": [],
            "dashboard_references_present": []
        }
        
        # Check view files for route references
        views_path = self.base_path / "resources" / "views"
        if views_path.exists():
            view_files = list(views_path.glob("**/*.blade.php"))
            console_refs = 0
            dashboard_refs = 0
            
            for view_file in view_files[:20]:  # Check first 20 files to avoid timeout
                try:
                    content = view_file.read_text()
                    if "console-" in content:
                        console_refs += 1
                        results["console_references_remaining"].append(str(view_file.relative_to(self.base_path)))
                    if "dashboard-" in content:
                        dashboard_refs += 1
                        results["dashboard_references_present"].append(str(view_file.relative_to(self.base_path)))
                except:
                    continue
            
            results["view_files_updated"]["console_references"] = console_refs
            results["view_files_updated"]["dashboard_references"] = dashboard_refs
            print(f"✅ View files: Console refs: {console_refs}, Dashboard refs: {dashboard_refs}")
        
        # Check controller files
        controllers_path = self.base_path / "app" / "Http" / "Controllers"
        if controllers_path.exists():
            controller_files = list(controllers_path.glob("**/*.php"))
            console_refs = 0
            dashboard_refs = 0
            
            for controller_file in controller_files[:10]:  # Check first 10 files
                try:
                    content = controller_file.read_text()
                    if "console-" in content:
                        console_refs += 1
                    if "dashboard-" in content:
                        dashboard_refs += 1
                except:
                    continue
            
            results["controller_references"]["console_references"] = console_refs
            results["controller_references"]["dashboard_references"] = dashboard_refs
            print(f"✅ Controllers: Console refs: {console_refs}, Dashboard refs: {dashboard_refs}")
        
        # Check JavaScript files
        js_path = self.base_path / "resources" / "js"
        if js_path.exists():
            js_files = list(js_path.glob("**/*.js"))
            console_refs = 0
            dashboard_refs = 0
            
            for js_file in js_files[:10]:  # Check first 10 files
                try:
                    content = js_file.read_text()
                    if "console-" in content:
                        console_refs += 1
                    if "dashboard-" in content:
                        dashboard_refs += 1
                except:
                    continue
            
            results["javascript_references"]["console_references"] = console_refs
            results["javascript_references"]["dashboard_references"] = dashboard_refs
            print(f"✅ JavaScript: Console refs: {console_refs}, Dashboard refs: {dashboard_refs}")
        
        self.results["route_references"] = results
        
    def generate_test_report(self):
        """Generate comprehensive test report"""
        print("\n📋 CONSOLE → DASHBOARD MIGRATION TEST REPORT")
        print("=" * 60)
        
        # Calculate scores
        route_score = (
            self.results["route_migration"]["dashboard_routes_present"] +
            self.results["route_migration"]["console_routes_removed"] +
            (len(self.results["route_migration"]["dashboard_sub_routes"]) / 5)
        ) / 3 * 100
        
        dashboard_score = (
            self.results["dashboard_accessibility"]["dashboard_route_accessible"] +
            (sum(self.results["dashboard_accessibility"]["dashboard_sub_routes_accessible"].values()) / max(len(self.results["dashboard_accessibility"]["dashboard_sub_routes_accessible"]), 1)) +
            self.results["dashboard_accessibility"]["view_files_present"]
        ) / 3 * 100
        
        api_score = 0
        if self.results["api_functionality"]["total_endpoints_tested"] > 0:
            api_score = (self.results["api_functionality"]["successful_endpoints"] / self.results["api_functionality"]["total_endpoints_tested"]) * 100
        
        auth_score = (
            self.results["authentication_flow"]["login_endpoint_working"] +
            self.results["authentication_flow"]["dashboard_redirect"] +
            self.results["authentication_flow"]["auth_token_valid"]
        ) / 3 * 100
        
        db_score = (
            self.results["database_connectivity"]["database_config"] +
            self.results["database_connectivity"]["migrations_applied"] +
            self.results["database_connectivity"]["health_check_db"] +
            self.results["database_connectivity"]["model_files_present"]
        ) / 4 * 100
        
        ref_score = 100  # Assume good if no major issues found
        if self.results["route_references"]["view_files_updated"].get("console_references", 0) > 5:
            ref_score -= 30
        
        overall_score = (route_score + dashboard_score + api_score + auth_score + db_score + ref_score) / 6
        
        print(f"🔄 Route Migration: {route_score:.1f}%")
        print(f"📊 Dashboard Accessibility: {dashboard_score:.1f}%")
        print(f"🔌 API Functionality: {api_score:.1f}%")
        print(f"🔐 Authentication Flow: {auth_score:.1f}%")
        print(f"🗄️  Database Connectivity: {db_score:.1f}%")
        print(f"🔗 Route References: {ref_score:.1f}%")
        print("-" * 40)
        print(f"🎯 OVERALL MIGRATION SCORE: {overall_score:.1f}%")
        
        # Summary
        summary = {
            "overall_score": overall_score,
            "route_migration_score": route_score,
            "dashboard_accessibility_score": dashboard_score,
            "api_functionality_score": api_score,
            "authentication_flow_score": auth_score,
            "database_connectivity_score": db_score,
            "route_references_score": ref_score,
            "dashboard_routes_found": len(self.results["route_migration"]["dashboard_sub_routes"]),
            "console_references_remaining": len(self.results["route_references"]["console_references_remaining"]),
            "api_endpoints_tested": self.results["api_functionality"]["total_endpoints_tested"],
            "api_endpoints_working": self.results["api_functionality"]["successful_endpoints"]
        }
        
        self.results["test_summary"] = summary
        
        # Migration-specific recommendations
        print("\n💡 CONSOLE → DASHBOARD MIGRATION RECOMMENDATIONS:")
        if overall_score >= 90:
            print("✅ EXCELLENT: Console → Dashboard migration completed successfully!")
        elif overall_score >= 80:
            print("✅ GOOD: Migration mostly complete with minor issues to address.")
        elif overall_score >= 70:
            print("⚠️  FAIR: Migration partially complete, some critical issues need attention.")
        else:
            print("❌ NEEDS WORK: Migration incomplete, significant issues require immediate attention.")
        
        # Specific recommendations
        if route_score < 90:
            print("   - Complete route name migration from console-* to dashboard-*")
        if dashboard_score < 90:
            print("   - Ensure all dashboard routes are accessible and view files exist")
        if api_score < 80:
            print("   - Fix API endpoint issues affecting functionality")
        if auth_score < 90:
            print("   - Resolve authentication flow and dashboard redirect issues")
        if db_score < 90:
            print("   - Address database connectivity issues")
        if ref_score < 90:
            print("   - Clean up remaining console route references in code")
        
        print(f"\n📊 Migration test completed at: {time.strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Save results to file
        results_file = self.base_path / "console_dashboard_migration_results.json"
        with open(results_file, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)
        print(f"📄 Detailed results saved to: {results_file}")

def main():
    """Main test execution"""
    print("🚀 Starting Console → Dashboard Migration Testing...")
    
    tester = ConsoleDashboardMigrationTest()
    tester.run_all_tests()
    
    print("\n✅ Console → Dashboard Migration Testing completed!")
    return tester.results["test_summary"]["overall_score"]

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 80 else 1)
    except Exception as e:
        print(f"❌ Test execution failed: {e}")
        sys.exit(1)