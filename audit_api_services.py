#!/usr/bin/env python3

"""
API-Service Mapping Audit Script
Identifies mismatches between API modules and Service modules
"""

import os
import re

def get_module_names(directory):
    """Get list of Python modules in a directory, excluding __init__.py"""
    modules = []
    for file in os.listdir(directory):
        if file.endswith('.py') and file != '__init__.py':
            modules.append(file[:-3])  # Remove .py extension
    return sorted(modules)

def normalize_name(name):
    """Normalize module name for comparison"""
    # Remove common suffixes and normalize naming patterns
    normalized = name.lower()
    normalized = re.sub(r'_system$', '', normalized)  # Remove _system suffix
    normalized = re.sub(r'_service$', '', normalized)  # Remove _service suffix  
    normalized = re.sub(r'_management$', '', normalized)  # Remove _management suffix
    return normalized

def main():
    # Get API and service modules
    api_modules = get_module_names('/app/backend/api')
    service_modules = get_module_names('/app/backend/services')
    
    print("🔍 API-SERVICE MAPPING AUDIT REPORT")
    print("=" * 80)
    print(f"📊 SUMMARY:")
    print(f"   API Modules: {len(api_modules)}")
    print(f"   Service Modules: {len(service_modules)}")
    print(f"   Difference: {len(api_modules) - len(service_modules)}")
    print()
    
    # Create mapping dictionaries
    api_normalized = {normalize_name(module): module for module in api_modules}
    service_normalized = {normalize_name(module): module for module in service_modules}
    
    print("📋 DETAILED ANALYSIS:")
    print("-" * 80)
    
    # API modules without corresponding services
    missing_services = []
    for normalized, api_module in api_normalized.items():
        if normalized not in service_normalized:
            missing_services.append(api_module)
    
    # Service modules without corresponding APIs
    missing_apis = []
    for normalized, service_module in service_normalized.items():
        if normalized not in api_normalized:
            missing_apis.append(service_module)
    
    # Matched pairs
    matched_pairs = []
    for normalized in api_normalized:
        if normalized in service_normalized:
            matched_pairs.append((api_normalized[normalized], service_normalized[normalized]))
    
    print(f"✅ MATCHED PAIRS ({len(matched_pairs)}):")
    for api_module, service_module in sorted(matched_pairs):
        print(f"   {api_module:30} ↔ {service_module}")
    print()
    
    print(f"❌ API MODULES MISSING SERVICES ({len(missing_services)}):")
    for module in sorted(missing_services):
        print(f"   {module}")
    print()
    
    print(f"❌ SERVICE MODULES MISSING APIS ({len(missing_apis)}):")
    for module in sorted(missing_apis):
        print(f"   {module}")
    print()
    
    # Generate recommendations
    print("💡 RECOMMENDATIONS:")
    print("-" * 80)
    
    if missing_services:
        print("🔧 CREATE MISSING SERVICE MODULES:")
        for api_module in sorted(missing_services):
            service_name = api_module
            if not service_name.endswith('_service'):
                if service_name.endswith('_system'):
                    service_name = service_name.replace('_system', '_service')
                elif service_name.endswith('_management'):
                    service_name = service_name.replace('_management', '_service')
                else:
                    service_name += '_service'
            print(f"   Create: /app/backend/services/{service_name}.py")
    
    if missing_apis:
        print("🔧 CREATE MISSING API MODULES:")
        for service_module in sorted(missing_apis):
            api_name = service_module
            if api_name.endswith('_service'):
                api_name = api_name.replace('_service', '')
            print(f"   Create: /app/backend/api/{api_name}.py")

if __name__ == "__main__":
    main()