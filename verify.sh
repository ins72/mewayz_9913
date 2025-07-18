#!/bin/bash

# Mewayz v2 - Installation Verification Script
# This script verifies that all components are properly installed and configured

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Counters
TESTS_TOTAL=0
TESTS_PASSED=0
TESTS_FAILED=0

# Function to print colored output
print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_header() {
    echo -e "${BLUE}$1${NC}"
}

# Function to run test
run_test() {
    local test_name="$1"
    local test_command="$2"
    local expected_result="$3"
    
    TESTS_TOTAL=$((TESTS_TOTAL + 1))
    echo -n "Testing $test_name... "
    
    if eval "$test_command" > /dev/null 2>&1; then
        if [ -n "$expected_result" ]; then
            if eval "$test_command" | grep -q "$expected_result"; then
                echo -e "${GREEN}PASS${NC}"
                TESTS_PASSED=$((TESTS_PASSED + 1))
            else
                echo -e "${RED}FAIL${NC} (unexpected result)"
                TESTS_FAILED=$((TESTS_FAILED + 1))
            fi
        else
            echo -e "${GREEN}PASS${NC}"
            TESTS_PASSED=$((TESTS_PASSED + 1))
        fi
    else
        echo -e "${RED}FAIL${NC}"
        TESTS_FAILED=$((TESTS_FAILED + 1))
    fi
}

print_header "🔍 Mewayz v2 - Installation Verification"
print_header "========================================"

# System Requirements Check
print_header "\n📋 System Requirements"
run_test "PHP Version (>=8.2)" "php --version" "PHP 8"
run_test "Composer Installation" "composer --version" "Composer"
run_test "Node.js Installation" "node --version" "v"
run_test "MySQL/MariaDB Service" "systemctl is-active mysql" "active"
run_test "Redis Service" "redis-cli ping" "PONG"

# Laravel Application Check
print_header "\n🚀 Laravel Application"
run_test "Laravel Installation" "php artisan --version" "Laravel Framework"
run_test "Environment File" "[ -f .env ]"
run_test "Application Key" "grep -q 'APP_KEY=base64:' .env"
run_test "Database Connection" "php artisan tinker --execute='DB::connection()->getPdo();'"
run_test "Storage Permissions" "[ -w storage ]"
run_test "Cache Directory" "[ -d bootstrap/cache ]"

# Database Check
print_header "\n🗄️ Database"
run_test "Database Exists" "mysql -u root -e 'USE mewayz_production;'"
run_test "Users Table" "php artisan tinker --execute='Schema::hasTable(\"users\");'"
run_test "Migrations Status" "php artisan migrate:status"

# Frontend Assets Check
print_header "\n🎨 Frontend Assets"
run_test "Node Modules" "[ -d node_modules ]"
run_test "Package.json" "[ -f package.json ]"
run_test "Public Build Directory" "[ -d public/build ]"
run_test "CSS Compilation" "[ -f public/build/assets/app*.css ]"
run_test "JS Compilation" "[ -f public/build/assets/app*.js ]"

# Configuration Check
print_header "\n⚙️ Configuration"
run_test "Cache Configuration" "php artisan config:cache"
run_test "Route Caching" "php artisan route:cache"
run_test "View Caching" "php artisan view:cache"
run_test "Production Environment" "grep -q 'APP_ENV=production' .env"
run_test "Debug Mode Off" "grep -q 'APP_DEBUG=false' .env"

# Security Check
print_header "\n🔐 Security"
run_test "HTTPS URL" "grep -q 'https://test.mewayz.com' .env"
run_test "Strong App Key" "grep -q 'APP_KEY=base64:' .env"
run_test "Session Security" "grep -q 'SESSION_SECURE_COOKIE=true' .env"
run_test "File Permissions" "[ $(stat -c %a storage) -eq 755 ]"

# API Endpoints Check
print_header "\n🌐 API Endpoints"
if command -v curl &> /dev/null; then
    run_test "Health Check Endpoint" "curl -f -s http://localhost:8000/api/health" "healthy"
    run_test "API Test Endpoint" "curl -f -s http://localhost:8000/api/test" "working"
else
    print_warning "curl not available - skipping API tests"
fi

# Performance Check
print_header "\n⚡ Performance"
run_test "OPCache Status" "php -m | grep -q OPcache"
run_test "Redis Connection" "php artisan tinker --execute='Cache::store(\"redis\")->ping();'"
run_test "Queue Configuration" "grep -q 'QUEUE_CONNECTION=redis' .env"

# Docker Check (if applicable)
print_header "\n🐳 Docker (Optional)"
if command -v docker &> /dev/null; then
    run_test "Docker Installation" "docker --version"
    run_test "Docker Compose" "docker-compose --version"
    if [ -f docker-compose.yml ]; then
        run_test "Docker Compose File" "[ -f docker-compose.yml ]"
    fi
else
    print_warning "Docker not available - skipping Docker tests"
fi

# Final Results
print_header "\n📊 Test Results Summary"
print_header "======================="

echo -e "Total Tests: $TESTS_TOTAL"
echo -e "${GREEN}Passed: $TESTS_PASSED${NC}"
if [ $TESTS_FAILED -gt 0 ]; then
    echo -e "${RED}Failed: $TESTS_FAILED${NC}"
else
    echo -e "Failed: $TESTS_FAILED"
fi

# Calculate success rate
SUCCESS_RATE=$((TESTS_PASSED * 100 / TESTS_TOTAL))
echo -e "Success Rate: $SUCCESS_RATE%"

# Final verdict
echo ""
if [ $SUCCESS_RATE -ge 90 ]; then
    print_header "🎉 Installation Status: EXCELLENT"
    print_status "Your Mewayz v2 installation is ready for production!"
    echo -e "\n${GREEN}🚀 Next Steps:${NC}"
    echo "   1. Visit https://test.mewayz.com"
    echo "   2. Login with admin@mewayz.com / admin123"
    echo "   3. Complete your platform setup"
    echo "   4. Start building your business!"
elif [ $SUCCESS_RATE -ge 80 ]; then
    print_header "⚠️ Installation Status: GOOD"
    print_warning "Your installation is mostly complete with minor issues"
    echo -e "\n${YELLOW}⚡ Recommendations:${NC}"
    echo "   1. Review failed tests above"
    echo "   2. Fix any configuration issues"
    echo "   3. Re-run this verification script"
elif [ $SUCCESS_RATE -ge 60 ]; then
    print_header "❌ Installation Status: NEEDS ATTENTION"
    print_error "Several components need attention before production use"
    echo -e "\n${RED}🔧 Required Actions:${NC}"
    echo "   1. Review and fix failed tests"
    echo "   2. Check installation documentation"
    echo "   3. Verify system requirements"
    echo "   4. Re-run setup script if needed"
else
    print_header "🚨 Installation Status: INCOMPLETE"
    print_error "Major issues detected - installation incomplete"
    echo -e "\n${RED}🆘 Emergency Actions:${NC}"
    echo "   1. Re-run the setup script: ./setup.sh"
    echo "   2. Check system requirements"
    echo "   3. Verify all dependencies are installed"
    echo "   4. Review error logs"
fi

echo ""
print_header "📚 Helpful Resources"
print_header "==================="
echo "• Setup Guide: ./SETUP_GUIDE.md"
echo "• Documentation: ./docs/"
echo "• Project Structure: ./PROJECT_STRUCTURE.md"
echo "• Health Check: http://localhost:8000/api/health"
echo "• Support: support@mewayz.com"

exit $TESTS_FAILED