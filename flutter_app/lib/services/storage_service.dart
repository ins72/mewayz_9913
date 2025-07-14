import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static SharedPreferences? _prefs;

  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Auth token management
  static Future<void> setToken(String token) async {
    await _prefs?.setString('auth_token', token);
  }

  static Future<String?> getToken() async {
    return _prefs?.getString('auth_token');
  }

  static Future<void> removeToken() async {
    await _prefs?.remove('auth_token');
  }

  // Theme management
  static Future<void> setTheme(String theme) async {
    await _prefs?.setString('theme', theme);
  }

  static Future<String?> getTheme() async {
    return _prefs?.getString('theme');
  }

  // Workspace management
  static Future<void> setCurrentWorkspaceId(String workspaceId) async {
    await _prefs?.setString('current_workspace_id', workspaceId);
  }

  static Future<String?> getCurrentWorkspaceId() async {
    return _prefs?.getString('current_workspace_id');
  }

  // User preferences
  static Future<void> setBool(String key, bool value) async {
    await _prefs?.setBool(key, value);
  }

  static Future<bool?> getBool(String key) async {
    return _prefs?.getBool(key);
  }

  static Future<void> setString(String key, String value) async {
    await _prefs?.setString(key, value);
  }

  static Future<String?> getString(String key) async {
    return _prefs?.getString(key);
  }

  static Future<void> setInt(String key, int value) async {
    await _prefs?.setInt(key, value);
  }

  static Future<int?> getInt(String key) async {
    return _prefs?.getInt(key);
  }

  static Future<void> remove(String key) async {
    await _prefs?.remove(key);
  }

  static Future<void> clear() async {
    await _prefs?.clear();
  }
}