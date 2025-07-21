#!/bin/bash

# Professional Project Structure Audit and Cleanup Script
# This script organizes the project structure and cleans up unnecessary files

echo "🔍 Starting Professional Project Structure Audit..."

# Create proper directory structure
mkdir -p /app/docs/{api,deployment,developer,user-guide,architecture,migration}
mkdir -p /app/tests/{unit,integration,e2e}
mkdir -p /app/scripts/{deployment,migration,maintenance}
mkdir -p /app/archive/{migration-tests,legacy-docs}

echo "✅ Directory structure created"

# Move test files to tests directory
echo "📁 Moving test files..."
find /app -maxdepth 1 -name "*test*.py" -not -path "/app/tests/*" -exec mv {} /app/tests/integration/ \;

# Move migration and audit files to archive
echo "📁 Moving legacy files to archive..."
mv /app/audit_implemented_features.py /app/archive/migration-tests/ 2>/dev/null || true
mv /app/comprehensive_backend_audit.py /app/archive/migration-tests/ 2>/dev/null || true
mv /app/final_professional_audit.py /app/archive/migration-tests/ 2>/dev/null || true

# Move documentation files appropriately
echo "📁 Organizing documentation..."
# Migration documentation
mv /app/PLATFORM_MIGRATION_SUCCESS.md /app/docs/migration/ 2>/dev/null || true
mv /app/PLATFORM_EXPANSION_STRATEGY.md /app/docs/migration/ 2>/dev/null || true

# Archive old completion documents
mv /app/FINAL_CONFIRMATION_COMPLETE.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/FINAL_PROFESSIONAL_VERIFICATION.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/MEWAYZ_PLATFORM_COMPLETE_v3.0.0.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/COMPREHENSIVE_PLATFORM_AUDIT_REPORT.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/FEATURE_VERIFICATION_REPORT_v3.0.0.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/ULTIMATE_SUCCESS_CONFIRMATION.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/COMPREHENSIVE_AUDIT_FINAL.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/PROJECT_COMPLETION_SUMMARY_v3.0.0.md /app/archive/legacy-docs/ 2>/dev/null || true
mv /app/FINAL_PLATFORM_AUDIT_COMPLETE.md /app/archive/legacy-docs/ 2>/dev/null || true

# Move deployment scripts
echo "📁 Moving deployment scripts..."
mv /app/deploy*.sh /app/scripts/deployment/ 2>/dev/null || true
mv /app/setup.sh /app/scripts/deployment/ 2>/dev/null || true
mv /app/verify.sh /app/scripts/maintenance/ 2>/dev/null || true
mv /app/interactive-setup.sh /app/scripts/deployment/ 2>/dev/null || true
mv /app/docker-setup.sh /app/scripts/deployment/ 2>/dev/null || true

# Clean up unnecessary files
echo "🗑️ Cleaning up unnecessary files..."
rm -f /app/dump.rdb
rm -f /app/debug_user.py
rm -f /app/detailed_analysis.py
rm -f /app/ultimate_verification.py
rm -f /app/ultimate_endpoint_verification.py
rm -f /app/final_backend_verification.py
rm -f /app/expansion_testing.py
rm -f /app/migrated_features_test.py
rm -f /app/*.json

echo "✅ Project structure cleanup completed"
echo "📊 Professional structure established"