import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../config/colors.dart';

class CustomAppBar extends StatelessWidget {
  final String title;
  final bool showBackButton;
  final List<Widget>? actions;
  final Widget? leading;

  const CustomAppBar({
    super.key,
    required this.title,
    this.showBackButton = true,
    this.actions,
    this.leading,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        border: Border(
          bottom: BorderSide(
            color: AppColors.secondaryBorder,
            width: 1,
          ),
        ),
      ),
      child: Row(
        children: [
          // Leading (back button or custom)
          if (showBackButton && context.canPop())
            leading ?? _buildBackButton(context)
          else if (leading != null)
            leading!
          else
            const SizedBox(width: 40), // Spacer for alignment
          
          // Title
          Expanded(
            child: Text(
              title,
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
              textAlign: TextAlign.center,
            ),
          ),
          
          // Actions
          if (actions != null)
            ...actions!
          else
            const SizedBox(width: 40), // Spacer for alignment
        ],
      ),
    );
  }

  Widget _buildBackButton(BuildContext context) {
    return GestureDetector(
      onTap: () => context.pop(),
      child: Container(
        width: 40,
        height: 40,
        decoration: BoxDecoration(
          color: AppColors.background,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: AppColors.secondaryBorder),
        ),
        child: Icon(
          Icons.arrow_back_ios_new,
          color: AppColors.textPrimary,
          size: 18,
        ),
      ),
    );
  }
}