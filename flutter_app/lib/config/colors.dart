import 'package:flutter/material.dart';

/// App color constants following the Mewayz design specification
class AppColors {
  // Private constructor to prevent instantiation
  AppColors._();

  /// Background Colors (Must match specification exactly)
  static const Color background = Color(0xFF101010); // All screen backgrounds
  static const Color surface = Color(0xFF191919); // Cards, modals, containers

  /// Button Colors
  static const Color primary = Color(0xFFEEEEEE); // Primary button background (off white)
  static const Color onPrimary = Color(0xFF141414); // Primary button text

  /// Border Colors
  static const Color secondaryBorder = Color(0xFF282828); // Secondary button stroke

  /// Text Colors
  static const Color textPrimary = Color(0xFFF1F1F1); // Primary text
  static const Color textSecondary = Color(0xFF7B7B7B); // Secondary text

  /// Status Colors
  static const Color success = Color(0xFF26DE81); // Green
  static const Color error = Color(0xFFFF3838); // Red
  static const Color warning = Color(0xFFF9CA24); // Orange
  static const Color info = Color(0xFF45B7D1); // Blue

  /// Quick Action Icon Colors (Must match exactly)
  static const Color instagramSearch = Color(0xFFFF6B6B); // Red/Pink
  static const Color postScheduler = Color(0xFF4ECDC4); // Blue
  static const Color linkBuilder = Color(0xFF45B7D1); // Green
  static const Color courseCreator = Color(0xFFF9CA24); // Orange
  static const Color storeManager = Color(0xFF6C5CE7); // Purple
  static const Color crmHub = Color(0xFFFF3838); // Red
  static const Color emailMarketing = Color(0xFF26DE81); // Green
  static const Color contentCalendar = Color(0xFFFD79A8); // Pink/Purple
  static const Color qrGenerator = Color(0xFFA0A0A0); // Gray/White

  /// Additional Helper Colors
  static const Color divider = Color(0xFF2A2A2A);
  static const Color shimmer = Color(0xFF2C2C2C);
  static const Color overlay = Color(0x80000000);

  /// Gradients
  static const LinearGradient primaryGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFFFFFFFF), Color(0xFFF5F5F5)],
  );

  static const LinearGradient surfaceGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [Color(0xFF1A1A1A), Color(0xFF161616)],
  );

  /// Material Color Swatches
  static const MaterialColor primarySwatch = MaterialColor(
    0xFFEEEEEE,
    <int, Color>{
      50: Color(0xFFFAFAFA),
      100: Color(0xFFF5F5F5),
      200: Color(0xFFEEEEEE),
      300: Color(0xFFE0E0E0),
      400: Color(0xFFBDBDBD),
      500: Color(0xFF9E9E9E),
      600: Color(0xFF757575),
      700: Color(0xFF616161),
      800: Color(0xFF424242),
      900: Color(0xFF212121),
    },
  );

  /// Shadow Colors
  static Color get shadowLight => Colors.black.withOpacity(0.1);
  static Color get shadowMedium => Colors.black.withOpacity(0.2);
  static Color get shadowHeavy => Colors.black.withOpacity(0.3);

  /// State Colors with Opacity
  static Color get primaryWithOpacity => primary.withOpacity(0.1);
  static Color get surfaceWithOpacity => surface.withOpacity(0.8);
  static Color get textPrimaryWithOpacity => textPrimary.withOpacity(0.7);

  /// Platform Specific Colors
  static const Color androidRipple = Color(0xFF2A2A2A);
  static const Color iosOverlay = Color(0x1A000000);

  /// Theme Mode Helpers
  static bool isDarkMode(BuildContext context) {
    return Theme.of(context).brightness == Brightness.dark;
  }

  /// Color Getters for Dynamic Usage
  static Color getIconColor(String platform) {
    switch (platform.toLowerCase()) {
      case 'instagram':
        return instagramSearch;
      case 'facebook':
        return postScheduler;
      case 'twitter':
        return linkBuilder;
      case 'linkedin':
        return courseCreator;
      case 'tiktok':
        return storeManager;
      case 'youtube':
        return crmHub;
      default:
        return textSecondary;
    }
  }

  /// Validation Colors
  static const Color validationSuccess = success;
  static const Color validationError = error;
  static const Color validationWarning = warning;

  /// Chart Colors
  static const List<Color> chartColors = [
    Color(0xFF4ECDC4),
    Color(0xFF45B7D1),
    Color(0xFFF9CA24),
    Color(0xFF26DE81),
    Color(0xFF6C5CE7),
    Color(0xFFFF6B6B),
    Color(0xFFFD79A8),
    Color(0xFFA0A0A0),
  ];
}