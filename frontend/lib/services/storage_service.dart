import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'user_data';
  
  static SharedPreferences? _preferences;
  
  static Future<void> init() async {
    _preferences = await SharedPreferences.getInstance();
  }
  
  static SharedPreferences get preferences {
    if (_preferences == null) {
      throw Exception('StorageService not initialized. Call init() first.');
    }
    return _preferences!;
  }

  // Token management
  static Future<bool> saveToken(String token) async {
    return await preferences.setString(_tokenKey, token);
  }

  static Future<String?> getToken() async {
    return preferences.getString(_tokenKey);
  }

  static Future<bool> removeToken() async {
    return await preferences.remove(_tokenKey);
  }

  static Future<bool> hasToken() async {
    return preferences.containsKey(_tokenKey);
  }

  // User data management
  static Future<bool> saveUserData(String userData) async {
    return await preferences.setString(_userKey, userData);
  }

  static Future<String?> getUserData() async {
    return preferences.getString(_userKey);
  }

  static Future<bool> removeUserData() async {
    return await preferences.remove(_userKey);
  }

  // Clear all data
  static Future<bool> clearAll() async {
    return await preferences.clear();
  }

  // Generic storage methods
  static Future<bool> setString(String key, String value) async {
    return await preferences.setString(key, value);
  }

  static String? getString(String key) {
    return preferences.getString(key);
  }

  static Future<bool> setBool(String key, bool value) async {
    return await preferences.setBool(key, value);
  }

  static bool? getBool(String key) {
    return preferences.getBool(key);
  }

  static Future<bool> setInt(String key, int value) async {
    return await preferences.setInt(key, value);
  }

  static int? getInt(String key) {
    return preferences.getInt(key);
  }

  static Future<bool> setDouble(String key, double value) async {
    return await preferences.setDouble(key, value);
  }

  static double? getDouble(String key) {
    return preferences.getDouble(key);
  }

  static Future<bool> setStringList(String key, List<String> value) async {
    return await preferences.setStringList(key, value);
  }

  static List<String>? getStringList(String key) {
    return preferences.getStringList(key);
  }

  static Future<bool> remove(String key) async {
    return await preferences.remove(key);
  }

  static bool containsKey(String key) {
    return preferences.containsKey(key);
  }
}