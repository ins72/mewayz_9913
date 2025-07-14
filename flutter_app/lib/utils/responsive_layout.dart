import 'package:flutter/material.dart';

class ResponsiveLayout extends StatelessWidget {
  final Widget mobile;
  final Widget? tablet;
  final Widget? desktop;

  const ResponsiveLayout({
    super.key,
    required this.mobile,
    this.tablet,
    this.desktop,
  });

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        if (constraints.maxWidth >= 1200) {
          return desktop ?? tablet ?? mobile;
        } else if (constraints.maxWidth >= 768) {
          return tablet ?? mobile;
        } else {
          return mobile;
        }
      },
    );
  }
}

class ResponsiveHelper {
  static bool isMobile(BuildContext context) {
    return MediaQuery.of(context).size.width < 768;
  }

  static bool isTablet(BuildContext context) {
    return MediaQuery.of(context).size.width >= 768 && 
           MediaQuery.of(context).size.width < 1200;
  }

  static bool isDesktop(BuildContext context) {
    return MediaQuery.of(context).size.width >= 1200;
  }

  static double getScreenWidth(BuildContext context) {
    return MediaQuery.of(context).size.width;
  }

  static double getScreenHeight(BuildContext context) {
    return MediaQuery.of(context).size.height;
  }

  static int getCrossAxisCount(BuildContext context) {
    if (isDesktop(context)) return 4;
    if (isTablet(context)) return 2;
    return 1;
  }

  static int getGridCrossAxisCount(BuildContext context) {
    if (isDesktop(context)) return 3;
    if (isTablet(context)) return 2;
    return 1;
  }

  static double getCardPadding(BuildContext context) {
    if (isDesktop(context)) return 24.0;
    if (isTablet(context)) return 20.0;
    return 16.0;
  }

  static double getSidePadding(BuildContext context) {
    if (isDesktop(context)) return 32.0;
    if (isTablet(context)) return 24.0;
    return 16.0;
  }
}