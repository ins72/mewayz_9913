import 'package:flutter/material.dart';
import '../../config/colors.dart';

class CreateBioSiteForm extends StatefulWidget {
  const CreateBioSiteForm({super.key});

  @override
  State<CreateBioSiteForm> createState() => _CreateBioSiteFormState();
}

class _CreateBioSiteFormState extends State<CreateBioSiteForm> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _urlController = TextEditingController();
  final _descriptionController = TextEditingController();
  String _selectedTheme = 'modern';

  @override
  void dispose() {
    _titleController.dispose();
    _urlController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 500,
      padding: const EdgeInsets.all(24),
      child: Form(
        key: _formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Create New Bio Site',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 24),
            
            // Title field
            TextFormField(
              controller: _titleController,
              decoration: const InputDecoration(
                labelText: 'Site Title',
                hintText: 'Enter your bio site title',
                border: OutlineInputBorder(),
                labelStyle: TextStyle(color: AppColors.textSecondary),
                hintStyle: TextStyle(color: AppColors.textSecondary),
              ),
              style: const TextStyle(color: AppColors.textPrimary),
              validator: (value) {
                if (value == null || value.isEmpty) {
                  return 'Please enter a title';
                }
                return null;
              },
            ),
            
            const SizedBox(height: 16),
            
            // URL field
            TextFormField(
              controller: _urlController,
              decoration: const InputDecoration(
                labelText: 'Custom URL',
                hintText: 'your-custom-url',
                prefixText: 'mewayz.com/',
                border: OutlineInputBorder(),
                labelStyle: TextStyle(color: AppColors.textSecondary),
                hintStyle: TextStyle(color: AppColors.textSecondary),
              ),
              style: const TextStyle(color: AppColors.textPrimary),
              validator: (value) {
                if (value == null || value.isEmpty) {
                  return 'Please enter a custom URL';
                }
                if (!RegExp(r'^[a-zA-Z0-9-]+$').hasMatch(value)) {
                  return 'URL can only contain letters, numbers, and hyphens';
                }
                return null;
              },
            ),
            
            const SizedBox(height: 16),
            
            // Description field
            TextFormField(
              controller: _descriptionController,
              maxLines: 3,
              decoration: const InputDecoration(
                labelText: 'Description',
                hintText: 'Tell people about yourself...',
                border: OutlineInputBorder(),
                labelStyle: TextStyle(color: AppColors.textSecondary),
                hintStyle: TextStyle(color: AppColors.textSecondary),
              ),
              style: const TextStyle(color: AppColors.textPrimary),
            ),
            
            const SizedBox(height: 24),
            
            // Theme selection
            const Text(
              'Choose Theme',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 12),
            _buildThemeSelector(),
            
            const SizedBox(height: 24),
            
            // Action buttons
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text(
                    'Cancel',
                    style: TextStyle(color: AppColors.textSecondary),
                  ),
                ),
                const SizedBox(width: 16),
                ElevatedButton(
                  onPressed: _createBioSite,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                  ),
                  child: const Text('Create Bio Site'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildThemeSelector() {
    final themes = [
      _ThemeOption('modern', 'Modern', const Color(0xFF4ECDC4)),
      _ThemeOption('classic', 'Classic', const Color(0xFF45B7D1)),
      _ThemeOption('minimal', 'Minimal', const Color(0xFF26DE81)),
      _ThemeOption('dark', 'Dark', const Color(0xFF2C2C2C)),
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 3,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
      ),
      itemCount: themes.length,
      itemBuilder: (context, index) {
        final theme = themes[index];
        final isSelected = _selectedTheme == theme.id;
        
        return GestureDetector(
          onTap: () {
            setState(() {
              _selectedTheme = theme.id;
            });
          },
          child: Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: isSelected ? theme.color.withOpacity(0.1) : AppColors.background,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(
                color: isSelected ? theme.color : AppColors.border,
              ),
            ),
            child: Row(
              children: [
                Container(
                  width: 24,
                  height: 24,
                  decoration: BoxDecoration(
                    color: theme.color,
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
                const SizedBox(width: 12),
                Text(
                  theme.name,
                  style: TextStyle(
                    fontSize: 14,
                    color: isSelected ? theme.color : AppColors.textSecondary,
                    fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  void _createBioSite() {
    if (_formKey.currentState!.validate()) {
      // TODO: Implement bio site creation
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Bio site created successfully!'),
          backgroundColor: Color(0xFF26DE81),
        ),
      );
    }
  }
}

class _ThemeOption {
  final String id;
  final String name;
  final Color color;

  _ThemeOption(this.id, this.name, this.color);
}