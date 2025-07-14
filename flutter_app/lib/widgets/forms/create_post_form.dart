import 'package:flutter/material.dart';
import '../../config/colors.dart';

class CreatePostForm extends StatefulWidget {
  const CreatePostForm({super.key});

  @override
  State<CreatePostForm> createState() => _CreatePostFormState();
}

class _CreatePostFormState extends State<CreatePostForm> {
  final _contentController = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  List<String> _selectedPlatforms = [];
  DateTime? _scheduledTime;

  @override
  void dispose() {
    _contentController.dispose();
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
              'Create New Post',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 24),
            TextFormField(
              controller: _contentController,
              maxLines: 5,
              decoration: const InputDecoration(
                labelText: 'Post Content',
                hintText: 'What\'s happening?',
                border: OutlineInputBorder(),
                labelStyle: TextStyle(color: AppColors.textSecondary),
                hintStyle: TextStyle(color: AppColors.textSecondary),
              ),
              style: const TextStyle(color: AppColors.textPrimary),
              validator: (value) {
                if (value == null || value.isEmpty) {
                  return 'Please enter post content';
                }
                return null;
              },
            ),
            const SizedBox(height: 24),
            const Text(
              'Select Platforms',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 12),
            _buildPlatformSelector(),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _selectScheduleTime(context),
                    icon: const Icon(Icons.schedule),
                    label: Text(_scheduledTime == null
                        ? 'Schedule Post'
                        : 'Scheduled for ${_scheduledTime!.toString().split('.')[0]}'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: AppColors.textPrimary,
                      side: const BorderSide(color: AppColors.border),
                    ),
                  ),
                ),
                const SizedBox(width: 16),
                OutlinedButton.icon(
                  onPressed: () {
                    // TODO: Attach image
                  },
                  icon: const Icon(Icons.image),
                  label: const Text('Add Image'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.textPrimary,
                    side: const BorderSide(color: AppColors.border),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 24),
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
                  onPressed: _createPost,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                  ),
                  child: Text(_scheduledTime == null ? 'Post Now' : 'Schedule Post'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPlatformSelector() {
    final platforms = [
      _PlatformOption('Instagram', Icons.camera_alt, const Color(0xFFE4405F)),
      _PlatformOption('Facebook', Icons.facebook, const Color(0xFF1877F2)),
      _PlatformOption('Twitter', Icons.alternate_email, const Color(0xFF1DA1F2)),
      _PlatformOption('LinkedIn', Icons.business, const Color(0xFF0A66C2)),
    ];

    return Wrap(
      spacing: 12,
      runSpacing: 8,
      children: platforms.map((platform) {
        final isSelected = _selectedPlatforms.contains(platform.name);
        return GestureDetector(
          onTap: () {
            setState(() {
              if (isSelected) {
                _selectedPlatforms.remove(platform.name);
              } else {
                _selectedPlatforms.add(platform.name);
              }
            });
          },
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: isSelected ? platform.color.withOpacity(0.1) : AppColors.surface,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(
                color: isSelected ? platform.color : AppColors.border,
              ),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  platform.icon,
                  color: isSelected ? platform.color : AppColors.textSecondary,
                  size: 16,
                ),
                const SizedBox(width: 8),
                Text(
                  platform.name,
                  style: TextStyle(
                    fontSize: 14,
                    color: isSelected ? platform.color : AppColors.textSecondary,
                    fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                  ),
                ),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }

  Future<void> _selectScheduleTime(BuildContext context) async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (date != null) {
      final time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
      );

      if (time != null) {
        setState(() {
          _scheduledTime = DateTime(
            date.year,
            date.month,
            date.day,
            time.hour,
            time.minute,
          );
        });
      }
    }
  }

  void _createPost() {
    if (_formKey.currentState!.validate()) {
      if (_selectedPlatforms.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Please select at least one platform'),
            backgroundColor: Color(0xFFFF4757),
          ),
        );
        return;
      }

      // TODO: Implement post creation
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Post created successfully!'),
          backgroundColor: Color(0xFF26DE81),
        ),
      );
    }
  }
}

class _PlatformOption {
  final String name;
  final IconData icon;
  final Color color;

  _PlatformOption(this.name, this.icon, this.color);
}