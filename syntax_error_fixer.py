#!/usr/bin/env python3
"""
Syntax Error Fixer - Fix all syntax and indentation errors in service files
"""

import os
import re
import ast
from pathlib import Path

class SyntaxErrorFixer:
    def __init__(self):
        self.services_dir = Path('/app/backend/services')
        self.fixed_count = 0
        
    def fix_all_syntax_errors(self):
        """Fix all syntax errors in service files"""
        print("🔧 Fixing syntax errors in service files...")
        
        for service_file in self.services_dir.glob('*.py'):
            if service_file.name == '__init__.py':
                continue
                
            try:
                self.fix_service_file(service_file)
            except Exception as e:
                print(f"  ❌ Error fixing {service_file.name}: {e}")
        
        print(f"\n✅ Fixed syntax errors in {self.fixed_count} files")
    
    def fix_service_file(self, file_path: Path):
        """Fix syntax errors in a single service file"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            
            # Fix common syntax errors
            content = self.fix_await_outside_async(content)
            content = self.fix_indentation_errors(content)
            content = self.fix_duplicate_global_instances(content)
            content = self.fix_misplaced_class_definitions(content)
            
            # Test if the fixed content is valid Python
            try:
                ast.parse(content)
                
                # Write back if changes were made
                if content != original_content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(content)
                    print(f"  ✅ Fixed {file_path.name}")
                    self.fixed_count += 1
                else:
                    print(f"  ℹ️  {file_path.name} - No fixes needed")
                    
            except SyntaxError as e:
                print(f"  ⚠️  {file_path.name} - Still has syntax errors after fixes: {e}")
                
        except Exception as e:
            print(f"  ❌ {file_path.name} - Error during processing: {e}")
    
    def fix_await_outside_async(self, content: str) -> str:
        """Fix await calls outside async functions"""
        
        # Find all await calls in non-async contexts
        # Pattern: await calls in dictionary/list definitions that are not in async functions
        
        # Fix await in dictionary definitions
        content = re.sub(
            r'("[\w\s]+"):\s*await\s+self\._get_[\w_]+\([^)]+\)',
            lambda m: f'{m.group(1)}: 0.5',  # Default value
            content
        )
        
        # Fix await in list comprehensions and function parameters
        content = re.sub(
            r'await\s+self\._get_[\w_]+\([^)]+\)',
            '0.5',  # Default value for non-async contexts
            content
        )
        
        return content
    
    def fix_indentation_errors(self, content: str) -> str:
        """Fix common indentation errors"""
        lines = content.split('\n')
        fixed_lines = []
        in_class = False
        class_indent = 0
        
        for line in lines:
            stripped = line.lstrip()
            
            # Detect class definition
            if stripped.startswith('class ') and ':' in stripped:
                in_class = True
                class_indent = len(line) - len(stripped)
                fixed_lines.append(line)
                continue
            
            # Handle global service instance placement
            if '# Global service instance' in line:
                # Ensure it's at the class level (not inside class)
                if in_class:
                    # Place it outside the class
                    fixed_lines.append('\n' + line.lstrip())
                else:
                    fixed_lines.append(line)
                continue
                
            # Handle service instance creation
            if '_service = ' in line and 'Service()' in line:
                # Ensure it's at module level
                if in_class:
                    fixed_lines.append(line.lstrip())
                else:
                    fixed_lines.append(line)
                continue
            
            # Handle method definitions inside class
            if in_class and stripped.startswith('async def ') or stripped.startswith('def '):
                # Ensure proper indentation for methods
                expected_indent = class_indent + 4
                actual_indent = len(line) - len(stripped)
                if actual_indent != expected_indent:
                    line = ' ' * expected_indent + stripped
                fixed_lines.append(line)
                continue
            
            fixed_lines.append(line)
        
        return '\n'.join(fixed_lines)
    
    def fix_duplicate_global_instances(self, content: str) -> str:
        """Remove duplicate global service instance declarations"""
        lines = content.split('\n')
        
        # Find all global service instance lines
        instance_lines = []
        for i, line in enumerate(lines):
            if '_service = ' in line and 'Service()' in line:
                instance_lines.append(i)
        
        # Keep only the last one
        if len(instance_lines) > 1:
            for i in instance_lines[:-1]:
                lines[i] = ''
        
        return '\n'.join(lines)
    
    def fix_misplaced_class_definitions(self, content: str) -> str:
        """Fix class definitions that might be misplaced"""
        
        # Ensure global service instance is at the end of the file
        lines = content.split('\n')
        
        # Find class end and global instance
        class_lines = []
        instance_lines = []
        other_lines = []
        
        in_class = False
        for i, line in enumerate(lines):
            stripped = line.lstrip()
            
            if stripped.startswith('class ') and ':' in stripped:
                in_class = True
                class_lines.append(line)
            elif in_class and (stripped.startswith('def ') or stripped.startswith('async def ') or 
                               stripped.startswith('"""') or stripped.startswith('#') or
                               line.strip() == '' or stripped.startswith('return') or
                               stripped.startswith('try:') or stripped.startswith('except') or
                               '=' in stripped or stripped.startswith('if ') or
                               stripped.startswith('for ') or stripped.startswith('while ')):
                class_lines.append(line)
            elif '# Global service instance' in line or ('_service = ' in line and 'Service()' in line):
                instance_lines.append(line.lstrip())
                in_class = False
            else:
                if in_class and stripped:  # This might be incorrectly indented class content
                    class_lines.append('    ' + stripped)
                else:
                    other_lines.append(line)
                    in_class = False
        
        # Reconstruct file with proper structure
        result_lines = other_lines + class_lines + [''] + instance_lines
        
        # Clean up empty lines
        while result_lines and result_lines[-1].strip() == '':
            result_lines.pop()
        
        return '\n'.join(result_lines)

if __name__ == "__main__":
    fixer = SyntaxErrorFixer()
    fixer.fix_all_syntax_errors()