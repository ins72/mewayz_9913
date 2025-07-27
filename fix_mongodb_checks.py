#!/usr/bin/env python3
"""
Fix MongoDB Database Checks
Replace 'if not mongo_db:' with 'if mongo_db is None:'
"""

def fix_mongodb_checks():
    """Fix MongoDB database checks in the main file"""
    
    file_path = "backend/main_mongodb_production.py"
    
    # Read the file
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Replace all instances
    fixed_content = content.replace("if not mongo_db:", "if mongo_db is None:")
    
    # Write back
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(fixed_content)
    
    print("✅ Fixed all MongoDB database checks")
    print(f"  - Replaced 'if not mongo_db:' with 'if mongo_db is None:'")
    print(f"  - File: {file_path}")

if __name__ == "__main__":
    fix_mongodb_checks() 