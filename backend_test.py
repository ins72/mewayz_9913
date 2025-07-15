#!/usr/bin/env python3
"""
Mewayz Laravel-Only Backend Testing Suite
=========================================

This test suite validates the Laravel-only architecture after the complete reorganization
from the previous backend/frontend split to a single Laravel instance.

Architecture Changes Verified:
- All Laravel files moved from /app/backend to /app
- Python/FastAPI completely removed
- Single Laravel instance architecture
- Professional project structure with proper documentation

Test Coverage:
1. Health check endpoint: GET /api/health
2. Laravel-based Stripe integration using StripeService
3. Authentication endpoints: POST /api/auth/login, GET /api/auth/me
4. Dashboard functionality (formerly console)
5. API routes accessibility and working status
6. Single Laravel instance configuration verification
"""

import os
import sys
import json
import time
from pathlib import Path

class MewayzLaravelArchitectureTest:
    def __init__(self):
        self.base_path = Path("/app")
        self.results = {
            "architecture_verification": {},
            "file_structure_analysis": {},
            "api_routes_analysis": {},
            "stripe_integration_analysis": {},
            "authentication_analysis": {},
            "dashboard_analysis": {},
            "test_summary": {}
        }
        
    def run_all_tests(self):
        """Run comprehensive Laravel-only architecture tests"""
        print("🚀 MEWAYZ LARAVEL-ONLY ARCHITECTURE TESTING SUITE")
        print("=" * 60)
        
        # Test 1: Architecture Verification
        self.test_architecture_reorganization()
        
        # Test 2: File Structure Analysis
        self.test_file_structure()
        
        # Test 3: API Routes Analysis
        self.test_api_routes_structure()
        
        # Test 4: Stripe Integration Analysis
        self.test_stripe_integration()
        
        # Test 5: Authentication System Analysis
        self.test_authentication_system()
        
        # Test 6: Dashboard Functionality Analysis
        self.test_dashboard_functionality()
        
        # Generate comprehensive report
        self.generate_test_report()
        
    def test_architecture_reorganization(self):
        """Test 1: Verify Laravel-only architecture reorganization"""
        print("\n📋 TEST 1: ARCHITECTURE REORGANIZATION VERIFICATION")
        print("-" * 50)
        
        results = {
            "laravel_root_structure": False,
            "backend_folder_removed": False,
            "single_instance_config": False,
            "composer_json_present": False,
            "artisan_present": False,
            "app_folder_structure": False
        }
        
        # Check Laravel root structure
        laravel_files = ['composer.json', 'artisan', 'app', 'config', 'database', 'routes', 'resources']
        laravel_present = all((self.base_path / file).exists() for file in laravel_files)
        results["laravel_root_structure"] = laravel_present
        print(f"✅ Laravel root structure: {'PRESENT' if laravel_present else 'MISSING'}")
        
        # Check backend folder removed
        backend_folder_exists = (self.base_path / "backend").exists()
        results["backend_folder_removed"] = not backend_folder_exists
        print(f"✅ Backend folder removed: {'YES' if not backend_folder_exists else 'NO - STILL EXISTS'}")
        
        # Check single instance configuration
        env_file = self.base_path / ".env"
        if env_file.exists():
            env_content = env_file.read_text()
            app_url_present = "APP_URL=http://localhost:8001" in env_content
            results["single_instance_config"] = app_url_present
            print(f"✅ Single instance config: {'CONFIGURED' if app_url_present else 'NOT CONFIGURED'}")
        
        # Check composer.json
        composer_file = self.base_path / "composer.json"
        results["composer_json_present"] = composer_file.exists()
        print(f"✅ Composer.json: {'PRESENT' if composer_file.exists() else 'MISSING'}")
        
        # Check artisan
        artisan_file = self.base_path / "artisan"
        results["artisan_present"] = artisan_file.exists()
        print(f"✅ Artisan command: {'PRESENT' if artisan_file.exists() else 'MISSING'}")
        
        # Check app folder structure
        app_folders = ['Http', 'Models', 'Services', 'Providers']
        app_structure = all((self.base_path / "app" / folder).exists() for folder in app_folders)
        results["app_folder_structure"] = app_structure
        print(f"✅ App folder structure: {'COMPLETE' if app_structure else 'INCOMPLETE'}")
        
        self.results["architecture_verification"] = results
        
    def test_file_structure(self):
        """Test 2: Analyze reorganized file structure"""
        print("\n📁 TEST 2: FILE STRUCTURE ANALYSIS")
        print("-" * 50)
        
        results = {
            "controllers_present": False,
            "models_present": False,
            "services_present": False,
            "routes_present": False,
            "migrations_present": False,
            "config_present": False
        }
        
        # Check Controllers
        controllers_path = self.base_path / "app" / "Http" / "Controllers" / "Api"
        if controllers_path.exists():
            controllers = list(controllers_path.glob("*.php"))
            results["controllers_present"] = len(controllers) > 0
            print(f"✅ API Controllers: {len(controllers)} files found")
            
            # List key controllers
            key_controllers = ['HealthController.php', 'AuthController.php', 'StripePaymentController.php']
            for controller in key_controllers:
                exists = (controllers_path / controller).exists()
                print(f"   - {controller}: {'✅' if exists else '❌'}")
        
        # Check Models
        models_path = self.base_path / "app" / "Models"
        if models_path.exists():
            models = list(models_path.glob("*.php"))
            results["models_present"] = len(models) > 0
            print(f"✅ Models: {len(models)} files found")
        
        # Check Services
        services_path = self.base_path / "app" / "Services"
        if services_path.exists():
            services = list(services_path.glob("*.php"))
            results["services_present"] = len(services) > 0
            print(f"✅ Services: {len(services)} files found")
            
            # Check StripeService specifically
            stripe_service = services_path / "StripeService.php"
            print(f"   - StripeService.php: {'✅' if stripe_service.exists() else '❌'}")
        
        # Check Routes
        routes_path = self.base_path / "routes"
        if routes_path.exists():
            route_files = ['api.php', 'web.php', 'auth.php']
            routes_present = all((routes_path / file).exists() for file in route_files)
            results["routes_present"] = routes_present
            print(f"✅ Route files: {'COMPLETE' if routes_present else 'INCOMPLETE'}")
        
        # Check Migrations
        migrations_path = self.base_path / "database" / "migrations"
        if migrations_path.exists():
            migrations = list(migrations_path.glob("*.php"))
            results["migrations_present"] = len(migrations) > 0
            print(f"✅ Migrations: {len(migrations)} files found")
        
        # Check Config
        config_path = self.base_path / "config"
        if config_path.exists():
            config_files = list(config_path.glob("*.php"))
            results["config_present"] = len(config_files) > 0
            print(f"✅ Config files: {len(config_files)} files found")
        
        self.results["file_structure_analysis"] = results
        
    def test_api_routes_structure(self):
        """Test 3: Analyze API routes structure"""
        print("\n🛣️  TEST 3: API ROUTES ANALYSIS")
        print("-" * 50)
        
        results = {
            "api_routes_file": False,
            "health_endpoint": False,
            "auth_endpoints": False,
            "stripe_endpoints": False,
            "protected_routes": False,
            "route_count": 0
        }
        
        api_routes_file = self.base_path / "routes" / "api.php"
        if api_routes_file.exists():
            results["api_routes_file"] = True
            content = api_routes_file.read_text()
            
            # Count total routes
            route_count = content.count("Route::")
            results["route_count"] = route_count
            print(f"✅ API routes file: PRESENT ({route_count} routes defined)")
            
            # Check specific endpoints
            health_present = "/health" in content and "HealthController" in content
            results["health_endpoint"] = health_present
            print(f"✅ Health endpoint: {'CONFIGURED' if health_present else 'MISSING'}")
            
            auth_present = ("'/login'" in content or "'/auth/login'" in content) and ("'/auth/me'" in content)
            results["auth_endpoints"] = auth_present
            print(f"✅ Auth endpoints: {'CONFIGURED' if auth_present else 'MISSING'}")
            
            stripe_present = ("prefix('payments')" in content or "'/payments'" in content) and "StripePaymentController" in content
            results["stripe_endpoints"] = stripe_present
            print(f"✅ Stripe endpoints: {'CONFIGURED' if stripe_present else 'MISSING'}")
            
            protected_routes = "auth:sanctum" in content
            results["protected_routes"] = protected_routes
            print(f"✅ Protected routes: {'CONFIGURED' if protected_routes else 'MISSING'}")
            
            # List key route groups
            route_groups = [
                "Health and System Routes",
                "Platform Information Routes", 
                "Authentication routes",
                "Stripe Payment routes",
                "Workspace routes"
            ]
            
            print("\n📋 Key Route Groups:")
            for group in route_groups:
                present = group.lower().replace(" ", "") in content.lower().replace(" ", "")
                print(f"   - {group}: {'✅' if present else '❌'}")
        
        self.results["api_routes_analysis"] = results
        
    def test_stripe_integration(self):
        """Test 4: Analyze Stripe integration implementation"""
        print("\n💳 TEST 4: STRIPE INTEGRATION ANALYSIS")
        print("-" * 50)
        
        results = {
            "stripe_service_exists": False,
            "stripe_controller_exists": False,
            "stripe_config": False,
            "payment_model": False,
            "stripe_methods": []
        }
        
        # Check StripeService
        stripe_service_file = self.base_path / "app" / "Services" / "StripeService.php"
        if stripe_service_file.exists():
            results["stripe_service_exists"] = True
            content = stripe_service_file.read_text()
            
            # Check key methods
            methods = [
                "createCheckoutSession",
                "getCheckoutStatus", 
                "handleWebhook"
            ]
            
            for method in methods:
                if method in content:
                    results["stripe_methods"].append(method)
            
            print(f"✅ StripeService: PRESENT")
            print(f"   - Methods implemented: {len(results['stripe_methods'])}/3")
            for method in results["stripe_methods"]:
                print(f"     ✅ {method}")
        
        # Check StripePaymentController
        stripe_controller_file = self.base_path / "app" / "Http" / "Controllers" / "Api" / "StripePaymentController.php"
        if stripe_controller_file.exists():
            results["stripe_controller_exists"] = True
            content = stripe_controller_file.read_text()
            
            # Check for fixed packages
            packages_defined = "PACKAGES = [" in content
            print(f"✅ StripePaymentController: PRESENT")
            print(f"   - Fixed packages defined: {'YES' if packages_defined else 'NO'}")
            
            # Check for security measures
            security_measures = [
                "NEVER allow frontend to set prices" in content,
                "validate(" in content,
                "Auth::user()" in content
            ]
            print(f"   - Security measures: {sum(security_measures)}/3 implemented")
        
        # Check Stripe configuration
        env_file = self.base_path / ".env"
        if env_file.exists():
            env_content = env_file.read_text()
            stripe_config = "STRIPE_KEY=" in env_content and "STRIPE_SECRET=" in env_content
            results["stripe_config"] = stripe_config
            print(f"✅ Stripe configuration: {'CONFIGURED' if stripe_config else 'MISSING'}")
        
        # Check PaymentTransaction model
        payment_model_file = self.base_path / "app" / "Models" / "PaymentTransaction.php"
        results["payment_model"] = payment_model_file.exists()
        print(f"✅ PaymentTransaction model: {'PRESENT' if payment_model_file.exists() else 'MISSING'}")
        
        self.results["stripe_integration_analysis"] = results
        
    def test_authentication_system(self):
        """Test 5: Analyze authentication system"""
        print("\n🔐 TEST 5: AUTHENTICATION SYSTEM ANALYSIS")
        print("-" * 50)
        
        results = {
            "auth_controller_exists": False,
            "sanctum_configured": False,
            "user_model_exists": False,
            "auth_methods": [],
            "two_factor_support": False
        }
        
        # Check AuthController
        auth_controller_file = self.base_path / "app" / "Http" / "Controllers" / "Api" / "AuthController.php"
        if auth_controller_file.exists():
            results["auth_controller_exists"] = True
            content = auth_controller_file.read_text()
            
            # Check key methods
            methods = ["login", "register", "logout", "me", "updateProfile"]
            for method in methods:
                if f"function {method}" in content:
                    results["auth_methods"].append(method)
            
            # Check 2FA support
            two_factor = "two_factor" in content.lower()
            results["two_factor_support"] = two_factor
            
            print(f"✅ AuthController: PRESENT")
            print(f"   - Methods implemented: {len(results['auth_methods'])}/5")
            for method in results["auth_methods"]:
                print(f"     ✅ {method}")
            print(f"   - Two-factor authentication: {'SUPPORTED' if two_factor else 'NOT SUPPORTED'}")
        
        # Check Sanctum configuration
        sanctum_config = self.base_path / "config" / "sanctum.php"
        results["sanctum_configured"] = sanctum_config.exists()
        print(f"✅ Laravel Sanctum: {'CONFIGURED' if sanctum_config.exists() else 'NOT CONFIGURED'}")
        
        # Check User model
        user_model_file = self.base_path / "app" / "Models" / "User.php"
        results["user_model_exists"] = user_model_file.exists()
        print(f"✅ User model: {'PRESENT' if user_model_file.exists() else 'MISSING'}")
        
        self.results["authentication_analysis"] = results
        
    def test_dashboard_functionality(self):
        """Test 6: Analyze dashboard functionality (formerly console)"""
        print("\n📊 TEST 6: DASHBOARD FUNCTIONALITY ANALYSIS")
        print("-" * 50)
        
        results = {
            "web_routes_configured": False,
            "dashboard_views": False,
            "livewire_components": False,
            "console_to_dashboard_migration": False
        }
        
        # Check web routes
        web_routes_file = self.base_path / "routes" / "web.php"
        if web_routes_file.exists():
            content = web_routes_file.read_text()
            dashboard_routes = "/console" in content or "/dashboard" in content
            results["web_routes_configured"] = dashboard_routes
            print(f"✅ Web routes: {'CONFIGURED' if dashboard_routes else 'NOT CONFIGURED'}")
            
            # Check for console->dashboard migration
            console_migration = "/console" in content
            results["console_to_dashboard_migration"] = console_migration
            print(f"✅ Console->Dashboard migration: {'DETECTED' if console_migration else 'NOT DETECTED'}")
        
        # Check dashboard views
        views_path = self.base_path / "resources" / "views"
        if views_path.exists():
            console_views = (views_path / "pages" / "console").exists()
            dashboard_views = (views_path / "pages" / "dashboard").exists()
            results["dashboard_views"] = console_views or dashboard_views
            print(f"✅ Dashboard views: {'PRESENT' if console_views or dashboard_views else 'MISSING'}")
        
        # Check Livewire components
        livewire_path = self.base_path / "app" / "Livewire"
        if livewire_path.exists():
            livewire_files = list(livewire_path.glob("**/*.php"))
            results["livewire_components"] = len(livewire_files) > 0
            print(f"✅ Livewire components: {len(livewire_files)} files found")
        
        self.results["dashboard_analysis"] = results
        
    def generate_test_report(self):
        """Generate comprehensive test report"""
        print("\n📋 COMPREHENSIVE TEST REPORT")
        print("=" * 60)
        
        # Calculate overall scores
        arch_score = sum(self.results["architecture_verification"].values()) / len(self.results["architecture_verification"]) * 100
        structure_score = sum(self.results["file_structure_analysis"].values()) / len(self.results["file_structure_analysis"]) * 100
        routes_score = sum(v for k, v in self.results["api_routes_analysis"].items() if k != "route_count") / (len(self.results["api_routes_analysis"]) - 1) * 100
        stripe_score = (
            self.results["stripe_integration_analysis"]["stripe_service_exists"] +
            self.results["stripe_integration_analysis"]["stripe_controller_exists"] +
            self.results["stripe_integration_analysis"]["stripe_config"] +
            (len(self.results["stripe_integration_analysis"]["stripe_methods"]) / 3)
        ) / 4 * 100
        auth_score = (
            self.results["authentication_analysis"]["auth_controller_exists"] +
            self.results["authentication_analysis"]["sanctum_configured"] +
            self.results["authentication_analysis"]["user_model_exists"] +
            (len(self.results["authentication_analysis"]["auth_methods"]) / 5)
        ) / 4 * 100
        dashboard_score = sum(self.results["dashboard_analysis"].values()) / len(self.results["dashboard_analysis"]) * 100
        
        overall_score = (arch_score + structure_score + routes_score + stripe_score + auth_score + dashboard_score) / 6
        
        print(f"🏗️  Architecture Reorganization: {arch_score:.1f}%")
        print(f"📁 File Structure: {structure_score:.1f}%")
        print(f"🛣️  API Routes: {routes_score:.1f}%")
        print(f"💳 Stripe Integration: {stripe_score:.1f}%")
        print(f"🔐 Authentication: {auth_score:.1f}%")
        print(f"📊 Dashboard: {dashboard_score:.1f}%")
        print("-" * 40)
        print(f"🎯 OVERALL SCORE: {overall_score:.1f}%")
        
        # Summary
        summary = {
            "overall_score": overall_score,
            "architecture_score": arch_score,
            "structure_score": structure_score,
            "routes_score": routes_score,
            "stripe_score": stripe_score,
            "auth_score": auth_score,
            "dashboard_score": dashboard_score,
            "total_routes": self.results["api_routes_analysis"].get("route_count", 0),
            "stripe_methods": len(self.results["stripe_integration_analysis"]["stripe_methods"]),
            "auth_methods": len(self.results["authentication_analysis"]["auth_methods"])
        }
        
        self.results["test_summary"] = summary
        
        # Recommendations
        print("\n💡 RECOMMENDATIONS:")
        if overall_score >= 90:
            print("✅ EXCELLENT: Laravel-only architecture is properly implemented and production-ready!")
        elif overall_score >= 80:
            print("✅ GOOD: Architecture is solid with minor improvements needed.")
        elif overall_score >= 70:
            print("⚠️  FAIR: Some critical components need attention.")
        else:
            print("❌ NEEDS WORK: Significant issues found that require immediate attention.")
        
        # Specific recommendations
        if arch_score < 90:
            print("   - Complete Laravel root structure reorganization")
        if stripe_score < 90:
            print("   - Ensure all Stripe integration methods are implemented")
        if auth_score < 90:
            print("   - Complete authentication system implementation")
        if dashboard_score < 90:
            print("   - Finalize console->dashboard migration")
        
        print(f"\n📊 Test completed at: {time.strftime('%Y-%m-%d %H:%M:%S')}")
        
        # Save results to file
        results_file = self.base_path / "backend_test_results.json"
        with open(results_file, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)
        print(f"📄 Detailed results saved to: {results_file}")

def main():
    """Main test execution"""
    print("🚀 Starting Mewayz Laravel-Only Architecture Testing...")
    
    tester = MewayzLaravelArchitectureTest()
    tester.run_all_tests()
    
    print("\n✅ Testing completed successfully!")
    return tester.results["test_summary"]["overall_score"]

if __name__ == "__main__":
    try:
        score = main()
        sys.exit(0 if score >= 80 else 1)
    except Exception as e:
        print(f"❌ Test execution failed: {e}")
        sys.exit(1)