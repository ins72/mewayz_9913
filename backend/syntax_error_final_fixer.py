#!/usr/bin/env python3
"""
Final Syntax Error Fixer
Fixes the remaining syntax errors in service files
"""

import re
from pathlib import Path

class FinalSyntaxFixer:
    """Fix remaining syntax errors"""
    
    def __init__(self):
        self.services_dir = Path('/app/backend/services')
        self.fixed_files = []
    
    def fix_all_syntax_errors(self):
        """Fix all remaining syntax errors"""
        print("🔧 FINAL SYNTAX ERROR FIXER")
        print("=" * 50)
        
        # Files with syntax errors from the comprehensive fixer
        problem_files = [
            'template_marketplace_service.py',
            'webhook_service.py', 
            'compliance_service.py',
            'ai_content_service.py',
            'advanced_ai_service.py',
            'advanced_financial_analytics_service.py',
            'onboarding_service.py',
            'advanced_financial_service.py',
            'escrow_service.py'
        ]
        
        for filename in problem_files:
            file_path = self.services_dir / filename
            if file_path.exists():
                print(f"\n🔧 Fixing {filename}...")
                success = self._fix_syntax_error(file_path)
                if success:
                    print(f"  ✅ Fixed successfully")
                    self.fixed_files.append(filename)
                else:
                    print(f"  ❌ Could not fix automatically")
        
        print(f"\n🎉 FIXED {len(self.fixed_files)} FILES")
        for filename in self.fixed_files:
            print(f"  ✅ {filename}")
    
    def _fix_syntax_error(self, file_path: Path) -> bool:
        """Fix syntax errors in a file"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            
            # Fix common indentation issues
            content = self._fix_indentation_issues(content)
            
            # Fix misplaced methods
            content = self._fix_misplaced_methods(content)
            
            # Fix f-string issues
            content = self._fix_fstring_issues(content)
            
            # Fix unmatched parentheses
            content = self._fix_unmatched_parens(content)
            
            if content != original_content:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                return True
            
            return False
            
        except Exception as e:
            print(f"    ❌ Error: {str(e)}")
            return False
    
    def _fix_indentation_issues(self, content: str) -> str:
        """Fix indentation issues"""
        lines = content.split('\n')
        fixed_lines = []
        in_class = False
        class_indent = 0
        
        for i, line in enumerate(lines):
            if line.strip().startswith('class ') and 'Service' in line:
                in_class = True
                class_indent = len(line) - len(line.lstrip())
                fixed_lines.append(line)
                continue
            
            # Fix methods that should be inside class
            if in_class and line.strip().startswith('async def _get_') and len(line) - len(line.lstrip()) <= class_indent:
                # This method should be indented inside the class
                fixed_line = ' ' * (class_indent + 4) + line.lstrip()
                fixed_lines.append(fixed_line)
                continue
            
            # Fix other lines that are part of the misplaced method
            if in_class and i > 0 and lines[i-1].strip().startswith('async def _get_'):
                current_indent = len(line) - len(line.lstrip())
                if current_indent <= class_indent and line.strip():
                    # This line is part of the method, indent it properly
                    if line.strip().startswith(('"""', 'try:', 'db =', 'if ', 'return', 'except')):
                        fixed_line = ' ' * (class_indent + 8) + line.lstrip()
                    else:
                        fixed_line = ' ' * (class_indent + 12) + line.lstrip()
                    fixed_lines.append(fixed_line)
                    continue
            
            # Check for end of class
            if in_class and line.strip() and not line.startswith(' ') and not line.startswith('\t'):
                if not any(marker in line for marker in ['#', '"""', "'''"]):
                    in_class = False
            
            fixed_lines.append(line)
        
        return '\n'.join(fixed_lines)
    
    def _fix_misplaced_methods(self, content: str) -> str:
        """Fix methods that are outside of classes"""
        lines = content.split('\n')
        fixed_lines = []
        
        # Find class definitions
        class_positions = []
        for i, line in enumerate(lines):
            if line.strip().startswith('class ') and 'Service' in line:
                class_positions.append((i, len(line) - len(line.lstrip())))
        
        if not class_positions:
            return content
        
        # Find the last class and its indentation
        last_class_line, last_class_indent = class_positions[-1]
        
        i = 0
        while i < len(lines):
            line = lines[i]
            
            # Check if this is a misplaced method
            if (line.strip().startswith('async def _get_') and 
                len(line) - len(line.lstrip()) <= last_class_indent):
                
                # This method should be inside the last class
                method_lines = []
                method_indent = len(line) - len(line.lstrip())
                
                # Collect all lines of this method
                j = i
                while j < len(lines):
                    current_line = lines[j]
                    if (j > i and current_line.strip() and 
                        len(current_line) - len(current_line.lstrip()) <= method_indent and
                        not current_line.strip().startswith(('#', '"""', "'''"))):
                        break
                    
                    # Re-indent the line to be inside the class
                    if current_line.strip():
                        if j == i:  # Method definition line
                            new_line = ' ' * (last_class_indent + 4) + current_line.lstrip()
                        else:  # Method body lines
                            original_indent = len(current_line) - len(current_line.lstrip())
                            new_indent = original_indent - method_indent + last_class_indent + 8
                            new_line = ' ' * max(0, new_indent) + current_line.lstrip()
                    else:
                        new_line = current_line
                    
                    method_lines.append(new_line)
                    j += 1
                
                # Skip the original method lines and add the fixed ones
                fixed_lines.extend(method_lines)
                i = j
                continue
            
            fixed_lines.append(line)
            i += 1
        
        return '\n'.join(fixed_lines)
    
    def _fix_fstring_issues(self, content: str) -> str:
        """Fix f-string syntax issues"""
        # Common f-string fixes
        fixes = [
            (r'f"([^"]*\([^)]*)"', r'f"\1"'),  # Fix unmatched parens in f-strings
            (r"f'([^']*\([^)]*)''", r"f'\1'"),  # Fix unmatched parens in f-strings with single quotes
        ]
        
        for pattern, replacement in fixes:
            content = re.sub(pattern, replacement, content)
        
        return content
    
    def _fix_unmatched_parens(self, content: str) -> str:
        """Fix unmatched parentheses"""
        lines = content.split('\n')
        fixed_lines = []
        
        for line in lines:
            if 'f"' in line or "f'" in line:
                # Count parentheses in f-strings
                open_parens = line.count('(')
                close_parens = line.count(')')
                
                if open_parens > close_parens:
                    # Add missing closing parentheses
                    line += ')' * (open_parens - close_parens)
                elif close_parens > open_parens:
                    # Remove extra closing parentheses
                    for _ in range(close_parens - open_parens):
                        line = line.rsplit(')', 1)[0] + line.rsplit(')', 1)[1]
            
            fixed_lines.append(line)
        
        return '\n'.join(fixed_lines)

if __name__ == "__main__":
    fixer = FinalSyntaxFixer()
    fixer.fix_all_syntax_errors()