#!/usr/bin/env python3
"""
Service Syntax Issues Fixer
Fixes the service method mapping issues and syntax errors identified in testing
"""

import os
import re
from pathlib import Path

def fix_customer_experience_service():
    """Fix customer experience service syntax issues"""
    service_path = Path('/app/backend/services/customer_experience_service.py')
    
    if not service_path.exists():
        print(f"❌ {service_path} not found")
        return
    
    with open(service_path, 'r') as f:
        content = f.read()
    
    # Fix the _estimate_responses method to be async
    content = content.replace(
        'def _estimate_responses(self, target_audience: str) -> int:',
        'async def _estimate_responses(self, target_audience: str) -> int:'
    )
    
    with open(service_path, 'w') as f:
        f.write(content)
    
    print("✅ Fixed customer_experience_service.py async method")

def fix_email_marketing_service():
    """Fix email marketing service syntax issues"""
    service_path = Path('/app/backend/services/email_marketing_service.py')
    
    if not service_path.exists():
        print(f"❌ {service_path} not found")
        return
    
    try:
        with open(service_path, 'r') as f:
            content = f.read()
        
        # Check for non-async methods using await
        lines = content.split('\n')
        fixed_lines = []
        
        for i, line in enumerate(lines):
            if re.match(r'\s*def\s+_\w+', line) and 'async' not in line:
                # Check if this method uses await in the body
                method_body_start = i + 1
                method_body = []
                indent_level = len(line) - len(line.lstrip())
                
                for j in range(method_body_start, len(lines)):
                    next_line = lines[j]
                    if next_line.strip() == '':
                        method_body.append(next_line)
                        continue
                    if len(next_line) - len(next_line.lstrip()) <= indent_level and next_line.strip():
                        break
                    method_body.append(next_line)
                
                # Check if method body contains await
                method_body_str = '\n'.join(method_body)
                if 'await ' in method_body_str:
                    line = line.replace('def ', 'async def ')
                    print(f"  ✅ Fixed method: {line.strip()}")
            
            fixed_lines.append(line)
        
        with open(service_path, 'w') as f:
            f.write('\n'.join(fixed_lines))
        
        print("✅ Fixed email_marketing_service.py async methods")
    
    except Exception as e:
        print(f"❌ Error fixing email_marketing_service.py: {e}")

def fix_social_email_service():
    """Fix social email service syntax issues"""
    service_path = Path('/app/backend/services/social_email_service.py')
    
    if not service_path.exists():
        print(f"❌ {service_path} not found")
        return
    
    try:
        with open(service_path, 'r') as f:
            content = f.read()
        
        # Check for non-async methods using await
        lines = content.split('\n')
        fixed_lines = []
        
        for i, line in enumerate(lines):
            if re.match(r'\s*def\s+_\w+', line) and 'async' not in line:
                # Check if this method uses await in the body
                method_body_start = i + 1
                method_body = []
                indent_level = len(line) - len(line.lstrip())
                
                for j in range(method_body_start, len(lines)):
                    next_line = lines[j]
                    if next_line.strip() == '':
                        method_body.append(next_line)
                        continue
                    if len(next_line) - len(next_line.lstrip()) <= indent_level and next_line.strip():
                        break
                    method_body.append(next_line)
                
                # Check if method body contains await
                method_body_str = '\n'.join(method_body)
                if 'await ' in method_body_str:
                    line = line.replace('def ', 'async def ')
                    print(f"  ✅ Fixed method: {line.strip()}")
            
            fixed_lines.append(line)
        
        with open(service_path, 'w') as f:
            f.write('\n'.join(fixed_lines))
        
        print("✅ Fixed social_email_service.py async methods")
    
    except Exception as e:
        print(f"❌ Error fixing social_email_service.py: {e}")

def fix_content_creation_service():
    """Fix content creation service syntax issues"""
    service_path = Path('/app/backend/services/content_creation_service.py')
    
    if not service_path.exists():
        print(f"❌ {service_path} not found")
        return
    
    try:
        with open(service_path, 'r') as f:
            content = f.read()
        
        # Check for non-async methods using await
        lines = content.split('\n')
        fixed_lines = []
        
        for i, line in enumerate(lines):
            if re.match(r'\s*def\s+_\w+', line) and 'async' not in line:
                # Check if this method uses await in the body
                method_body_start = i + 1
                method_body = []
                indent_level = len(line) - len(line.lstrip())
                
                for j in range(method_body_start, len(lines)):
                    next_line = lines[j]
                    if next_line.strip() == '':
                        method_body.append(next_line)
                        continue
                    if len(next_line) - len(next_line.lstrip()) <= indent_level and next_line.strip():
                        break
                    method_body.append(next_line)
                
                # Check if method body contains await
                method_body_str = '\n'.join(method_body)
                if 'await ' in method_body_str:
                    line = line.replace('def ', 'async def ')
                    print(f"  ✅ Fixed method: {line.strip()}")
            
            fixed_lines.append(line)
        
        with open(service_path, 'w') as f:
            f.write('\n'.join(fixed_lines))
        
        print("✅ Fixed content_creation_service.py async methods")
    
    except Exception as e:
        print(f"❌ Error fixing content_creation_service.py: {e}")

def main():
    print("🔧 FIXING SERVICE SYNTAX ISSUES")
    print("=" * 60)
    
    print("\n1. Fixing Customer Experience Service...")
    fix_customer_experience_service()
    
    print("\n2. Fixing Email Marketing Service...")
    fix_email_marketing_service()
    
    print("\n3. Fixing Social Email Service...")
    fix_social_email_service()
    
    print("\n4. Fixing Content Creation Service...")
    fix_content_creation_service()
    
    print("\n✅ SERVICE SYNTAX FIXES COMPLETED")
    print("=" * 60)
    print("🚀 Ready for testing with fixed async methods")

if __name__ == "__main__":
    main()