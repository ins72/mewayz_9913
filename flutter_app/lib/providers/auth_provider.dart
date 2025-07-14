import 'package:flutter/foundation.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';

class AuthProvider extends ChangeNotifier {
  User? _user;
  bool _isAuthenticated = false;
  bool _isLoading = false;
  String? _error;

  User? get user => _user;
  bool get isAuthenticated => _isAuthenticated;
  bool get isLoading => _isLoading;
  String? get error => _error;

  AuthProvider() {
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    _setLoading(true);
    try {
      final token = await StorageService.getToken();
      if (token != null) {
        final userData = await ApiService.getUser();
        if (userData != null) {
          _user = User.fromJson(userData);
          _isAuthenticated = true;
        }
      }
    } catch (e) {
      _error = e.toString();
    }
    _setLoading(false);
  }

  Future<bool> login(String email, String password) async {
    _setLoading(true);
    _error = null;
    
    try {
      final response = await ApiService.login(email, password);
      if (response['success'] == true) {
        final token = response['token'];
        final userData = response['user'];
        
        await StorageService.setToken(token);
        _user = User.fromJson(userData);
        _isAuthenticated = true;
        
        _setLoading(false);
        return true;
      } else {
        _error = response['message'] ?? 'Login failed';
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _error = e.toString().replaceAll('Exception: ', '');
      _setLoading(false);
      return false;
    }
  }

  Future<bool> register(String name, String email, String password) async {
    _setLoading(true);
    _error = null;
    
    try {
      final response = await ApiService.register(name, email, password);
      if (response['success'] == true) {
        final token = response['token'];
        final userData = response['user'];
        
        await StorageService.setToken(token);
        _user = User.fromJson(userData);
        _isAuthenticated = true;
        
        _setLoading(false);
        return true;
      } else {
        _error = response['message'] ?? 'Registration failed';
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _error = e.toString().replaceAll('Exception: ', '');
      _setLoading(false);
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await ApiService.logout();
    } catch (e) {
      // Handle error silently for logout
    }
    
    await StorageService.removeToken();
    _user = null;
    _isAuthenticated = false;
    notifyListeners();
  }

  Future<bool> forgotPassword(String email) async {
    _setLoading(true);
    _error = null;
    
    try {
      final response = await ApiService.forgotPassword(email);
      _setLoading(false);
      return response['success'] == true;
    } catch (e) {
      _error = e.toString();
      _setLoading(false);
      return false;
    }
  }

  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void clearError() {
    _error = null;
    notifyListeners();
  }
}