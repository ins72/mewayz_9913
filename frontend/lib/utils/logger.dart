import 'package:flutter/foundation.dart';

class Logger {
  static const String _appName = 'Mewayz';
  
  static void info(String message, [String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toIso8601String();
      final logTag = tag ?? 'INFO';
      debugPrint('[$timestamp] [$_appName] [$logTag] $message');
    }
  }
  
  static void error(String message, [dynamic error, StackTrace? stackTrace, String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toIso8601String();
      final logTag = tag ?? 'ERROR';
      debugPrint('[$timestamp] [$_appName] [$logTag] $message');
      if (error != null) {
        debugPrint('[$timestamp] [$_appName] [$logTag] Error: $error');
      }
      if (stackTrace != null) {
        debugPrint('[$timestamp] [$_appName] [$logTag] Stack trace: $stackTrace');
      }
    }
  }
  
  static void warning(String message, [String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toIso8601String();
      final logTag = tag ?? 'WARNING';
      debugPrint('[$timestamp] [$_appName] [$logTag] $message');
    }
  }
  
  static void debug(String message, [String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toIso8601String();
      final logTag = tag ?? 'DEBUG';
      debugPrint('[$timestamp] [$_appName] [$logTag] $message');
    }
  }
}