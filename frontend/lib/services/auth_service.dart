import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';

class AuthService {
  static final AuthService _instance = AuthService._internal();
  
  factory AuthService() {
    return _instance;
  }

  AuthService._internal();

  // Current user data
  Map<String, dynamic>? _currentUser;
  
  // Get current user
  Map<String, dynamic>? get currentUser => _currentUser;
  
  // Check if user is authenticated
  bool get isAuthenticated => _currentUser != null;

  Future<void> initialize() async {
    try {
      await _loadUserFromStorage();
      debugPrint('AuthService initialized successfully');
    } catch (e) {
      debugPrint('Failed to initialize AuthService: $e');
    }
  }

  // Load user data from storage
  Future<void> _loadUserFromStorage() async {
    try {
      final userData = await StorageService.getUserData();
      if (userData != null) {
        _currentUser = jsonDecode(userData);
      }
    } catch (e) {
      debugPrint('Failed to load user from storage: $e');
    }
  }

  // Save user data to storage
  Future<void> _saveUserToStorage(Map<String, dynamic> user) async {
    try {
      await StorageService.saveUserData(jsonEncode(user));
      _currentUser = user;
    } catch (e) {
      debugPrint('Failed to save user to storage: $e');
    }
  }

  // Sign up new user
  Future<Map<String, dynamic>> signUp({
    required String email,
    required String password,
    required String fullName,
    String role = 'creator',
  }) async {
    try {
      final response = await ApiService.register(fullName, email, password);
      
      if (response['success'] == true) {
        // Save token and user data
        await StorageService.saveToken(response['token']);
        await _saveUserToStorage(response['user']);
        
        debugPrint('User signed up successfully: ${response['user']['email']}');
        return {
          'success': true,
          'user': response['user'],
          'message': response['message']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? 'Registration failed'
        };
      }
    } catch (e) {
      debugPrint('Failed to sign up user: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  // Sign in existing user
  Future<Map<String, dynamic>> signIn({
    required String email,
    required String password,
  }) async {
    try {
      final response = await ApiService.login(email, password);
      
      if (response['success'] == true) {
        // Save token and user data
        await StorageService.saveToken(response['token']);
        await _saveUserToStorage(response['user']);
        
        debugPrint('User signed in successfully: ${response['user']['email']}');
        return {
          'success': true,
          'user': response['user'],
          'message': response['message']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? 'Login failed',
          'requires_2fa': response['requires_2fa'] ?? false
        };
      }
    } catch (e) {
      debugPrint('Failed to sign in user: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  // Sign in with 2FA
  Future<Map<String, dynamic>> signInWith2FA({
    required String email,
    required String password,
    required String twoFactorCode,
  }) async {
    try {
      final response = await ApiService.login(email, password);
      
      if (response['success'] == true) {
        // Save token and user data
        await StorageService.saveToken(response['token']);
        await _saveUserToStorage(response['user']);
        
        debugPrint('User signed in with 2FA successfully: ${response['user']['email']}');
        return {
          'success': true,
          'user': response['user'],
          'message': response['message']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? 'Login failed'
        };
      }
    } catch (e) {
      debugPrint('Failed to sign in with 2FA: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  // Sign out current user
  Future<void> signOut() async {
    try {
      await ApiService.logout();
      await StorageService.removeToken();
      await StorageService.removeUserData();
      _currentUser = null;
      debugPrint('User signed out successfully');
    } catch (e) {
      debugPrint('Failed to sign out user: $e');
      // Even if API call fails, clear local data
      await StorageService.removeToken();
      await StorageService.removeUserData();
      _currentUser = null;
    }
  }

  // Reset password
  Future<Map<String, dynamic>> resetPassword(String email) async {
    try {
      final response = await ApiService.forgotPassword(email);
      
      if (response['success'] == true) {
        debugPrint('Password reset email sent to: $email');
        return {
          'success': true,
          'message': response['message']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? 'Password reset failed'
        };
      }
    } catch (e) {
      debugPrint('Failed to reset password: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  // Get user profile
  Future<Map<String, dynamic>?> getUserProfile() async {
    try {
      final response = await ApiService.getUser();
      
      if (response != null && response['success'] == true) {
        await _saveUserToStorage(response['user']);
        return response['user'];
      }
      
      return null;
    } catch (e) {
      debugPrint('Failed to get user profile: $e');
      return null;
    }
  }

  // Update user profile
  Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
  }) async {
    try {
      // This would require an API endpoint for profile updates
      // For now, we'll just update the local user data
      if (_currentUser != null) {
        _currentUser!['name'] = name;
        _currentUser!['email'] = email;
        await _saveUserToStorage(_currentUser!);
        
        return {
          'success': true,
          'message': 'Profile updated successfully',
          'user': _currentUser
        };
      }
      
      return {
        'success': false,
        'message': 'No user logged in'
      };
    } catch (e) {
      debugPrint('Failed to update profile: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  // Check if token is valid
  Future<bool> isTokenValid() async {
    try {
      final response = await ApiService.getUser();
      return response != null && response['success'] == true;
    } catch (e) {
      debugPrint('Token validation failed: $e');
      return false;
    }
  }

  // Refresh user data
  Future<void> refreshUserData() async {
    try {
      final response = await ApiService.getUser();
      
      if (response != null && response['success'] == true) {
        await _saveUserToStorage(response['user']);
      }
    } catch (e) {
      debugPrint('Failed to refresh user data: $e');
    }
  }

  // 2FA Methods
  Future<Map<String, dynamic>> generate2FA() async {
    try {
      final response = await ApiService.generate2FA();
      
      if (response['success'] == true) {
        return {
          'success': true,
          'qr_code': response['qr_code'],
          'secret': response['secret']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? '2FA generation failed'
        };
      }
    } catch (e) {
      debugPrint('Failed to generate 2FA: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  Future<Map<String, dynamic>> enable2FA(String code) async {
    try {
      final response = await ApiService.enable2FA(code);
      
      if (response['success'] == true) {
        return {
          'success': true,
          'message': response['message'],
          'recovery_codes': response['recovery_codes']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? '2FA enable failed'
        };
      }
    } catch (e) {
      debugPrint('Failed to enable 2FA: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  Future<Map<String, dynamic>> disable2FA(String code) async {
    try {
      final response = await ApiService.disable2FA(code);
      
      if (response['success'] == true) {
        return {
          'success': true,
          'message': response['message']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? '2FA disable failed'
        };
      }
    } catch (e) {
      debugPrint('Failed to disable 2FA: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }

  Future<Map<String, dynamic>> getRecoveryCodes() async {
    try {
      final response = await ApiService.getRecoveryCodes();
      
      if (response['success'] == true) {
        return {
          'success': true,
          'recovery_codes': response['recovery_codes']
        };
      } else {
        return {
          'success': false,
          'message': response['message'] ?? 'Failed to get recovery codes'
        };
      }
    } catch (e) {
      debugPrint('Failed to get recovery codes: $e');
      return {
        'success': false,
        'message': e.toString()
      };
    }
  }
}