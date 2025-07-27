#!/usr/bin/env python3
"""
Check Database Schema
Check and fix database schema issues
"""

import sqlite3
from pathlib import Path

def check_schema():
    """Check database schema"""
    db_path = Path('databases/mewayz.db')
    db = sqlite3.connect(str(db_path))
    cursor = db.cursor()
    
    # Check users table
    cursor.execute("PRAGMA table_info(users)")
    users_columns = cursor.fetchall()
    print("Users table columns:")
    for col in users_columns:
        print(f"  {col[1]} ({col[2]})")
    
    # Check if full_name column exists
    has_full_name = any(col[1] == 'full_name' for col in users_columns)
    
    if not has_full_name:
        print("\nAdding full_name column to users table...")
        try:
            cursor.execute("ALTER TABLE users ADD COLUMN full_name TEXT")
            db.commit()
            print("✅ Added full_name column")
        except Exception as e:
            print(f"❌ Error adding column: {e}")
    
    # Check other tables
    tables = ['workspaces', 'analytics', 'products', 'crm_contacts', 'support_tickets', 'ai_usage', 'user_activities', 'marketing_analytics']
    
    for table in tables:
        try:
            cursor.execute(f"PRAGMA table_info({table})")
            columns = cursor.fetchall()
            print(f"\n{table} table columns:")
            for col in columns:
                print(f"  {col[1]} ({col[2]})")
        except Exception as e:
            print(f"❌ Error checking {table}: {e}")
    
    db.close()

if __name__ == "__main__":
    check_schema() 