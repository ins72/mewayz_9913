#!/usr/bin/env python3
"""
Random Data Audit Script
Identifies files using random data generation and provides recommendations
"""

import os
import re
import subprocess
from pathlib import Path

def analyze_file_for_random_usage(file_path):
    """Analyze a Python file for random data usage"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Count different types of random usage
        random_patterns = {
            'randint': len(re.findall(r'random\.randint\([^)]+\)', content)),
            'uniform': len(re.findall(r'random\.uniform\([^)]+\)', content)),
            'choice': len(re.findall(r'random\.choice\([^)]+\)', content)),
            'random': len(re.findall(r'random\.random\(\)', content))
        }
        
        total_random = sum(random_patterns.values())
        
        # Check if file has database imports
        has_database_imports = bool(re.search(r'from.*database.*import|import.*database', content))
        
        # Check if file has async/await patterns (suggesting database operations)
        has_async_patterns = bool(re.search(r'async def|await', content))
        
        return {
            'file': file_path,
            'random_patterns': random_patterns,
            'total_random': total_random,
            'has_database_imports': has_database_imports,
            'has_async_patterns': has_async_patterns,
            'line_count': len(content.split('\n'))
        }
    except Exception as e:
        return {'file': file_path, 'error': str(e)}

def main():
    backend_dir = Path('/app/backend')
    
    print("🔍 RANDOM DATA AUDIT REPORT")
    print("=" * 80)
    
    # Analyze services directory
    services_dir = backend_dir / 'services'
    service_files = []
    
    for file_path in services_dir.glob('*.py'):
        if file_path.name != '__init__.py':
            analysis = analyze_file_for_random_usage(file_path)
            if analysis.get('total_random', 0) > 0:
                service_files.append(analysis)
    
    # Sort by number of random usages
    service_files.sort(key=lambda x: x.get('total_random', 0), reverse=True)
    
    print(f"📊 SERVICES ANALYSIS:")
    print(f"   Total service files analyzed: {len(list(services_dir.glob('*.py'))) - 1}")
    print(f"   Files using random data: {len(service_files)}")
    print()
    
    print("🚨 CRITICAL FILES REQUIRING DATABASE INTEGRATION:")
    print("-" * 60)
    
    critical_files = []
    for analysis in service_files[:10]:  # Top 10 files
        if analysis.get('total_random', 0) > 5:
            critical_files.append(analysis)
            print(f"📄 {analysis['file'].name}")
            print(f"   Random usages: {analysis['total_random']}")
            print(f"   - randint: {analysis['random_patterns']['randint']}")
            print(f"   - uniform: {analysis['random_patterns']['uniform']}")
            print(f"   - choice: {analysis['random_patterns']['choice']}")
            print(f"   Database imports: {'✅' if analysis['has_database_imports'] else '❌'}")
            print(f"   Async patterns: {'✅' if analysis['has_async_patterns'] else '❌'}")
            print()
    
    # Analyze API directory
    api_dir = backend_dir / 'api'
    api_files = []
    
    for file_path in api_dir.glob('*.py'):
        if file_path.name != '__init__.py':
            analysis = analyze_file_for_random_usage(file_path)
            if analysis.get('total_random', 0) > 0:
                api_files.append(analysis)
    
    api_files.sort(key=lambda x: x.get('total_random', 0), reverse=True)
    
    print(f"📊 API FILES ANALYSIS:")
    print(f"   Total API files analyzed: {len(list(api_dir.glob('*.py'))) - 1}")
    print(f"   API files using random data: {len(api_files)}")
    print()
    
    print("🔧 RECOMMENDED FIXES:")
    print("-" * 40)
    
    print("1. **Database Collections Needed:**")
    print("   - ai_usage (for AI service analytics)")
    print("   - user_activities (for dashboard and user tracking)")
    print("   - page_visits (for analytics)")
    print("   - user_actions (for conversion tracking)")
    print("   - user_sessions (for session management)")
    print("   - projects (for project management)")
    print("   - email_campaigns (for email marketing)")
    print("   - social_media_posts (for social media analytics)")
    print("   - financial_transactions (for financial analytics)")
    print()
    
    print("2. **Priority Services to Fix:**")
    for i, analysis in enumerate(critical_files[:5], 1):
        print(f"   {i}. {analysis['file'].name} - {analysis['total_random']} random calls")
    print()
    
    print("3. **Implementation Strategy:**")
    print("   ✅ COMPLETED: dashboard_service.py - Fixed random data")
    print("   ✅ COMPLETED: advanced_ai_service.py - Fixed usage analytics")
    print("   🔄 IN PROGRESS: Replace random metrics with database queries")
    print("   📋 TODO: Add proper data tracking in all service methods")
    print("   📋 TODO: Create database collections for real data storage")
    print()
    
    print("4. **Database Schema Requirements:**")
    print("""
   Collections needed:
   - ai_usage: { user_id, service_type, tokens_used, cost, status, created_at }
   - user_activities: { user_id, type, message, timestamp, metadata }
   - analytics: { user_id, event_type, properties, timestamp }
   - projects: { user_id, name, status, created_at, updated_at }
   - campaigns: { user_id, type, status, metrics, created_at }
   """)
    
    total_random_calls = sum(f.get('total_random', 0) for f in service_files + api_files)
    print(f"📈 TOTAL RANDOM DATA CALLS TO REPLACE: {total_random_calls}")
    print()
    print("💡 RECOMMENDATION: Focus on services with >10 random calls first")
    print("   These represent the core business logic that needs real data.")

if __name__ == "__main__":
    main()