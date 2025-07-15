import 'dart:async';
import 'dart:convert';
import 'dart:html' as html;
import 'dart:indexed_db';
import 'package:flutter/foundation.dart';

class OfflineStorageService {
  static final OfflineStorageService _instance = OfflineStorageService._internal();
  factory OfflineStorageService() => _instance;
  OfflineStorageService._internal();

  static const String _dbName = 'mewayz_offline_db';
  static const int _dbVersion = 1;
  
  Database? _database;
  bool _isInitialized = false;

  // Object store names
  static const String _userStore = 'users';
  static const String _workspaceStore = 'workspaces';
  static const String _socialMediaStore = 'social_media';
  static const String _bioSiteStore = 'bio_sites';
  static const String _analyticsStore = 'analytics';
  static const String _crmStore = 'crm';
  static const String _marketingStore = 'marketing';
  static const String _courseStore = 'courses';
  static const String _draftsStore = 'drafts';
  static const String _pendingActionsStore = 'pending_actions';
  static const String _cacheStore = 'cache';

  // Initialize offline storage
  Future<void> initialize() async {
    if (!kIsWeb) return;

    try {
      final request = html.window.indexedDB!.open(_dbName, version: _dbVersion);

      request.onUpgradeNeeded.listen((event) {
        _database = (event.target as IdbOpenDBRequest).result as Database;
        _createObjectStores();
      });

      request.onSuccess.listen((event) {
        _database = (event.target as IdbOpenDBRequest).result as Database;
        _isInitialized = true;
        print('Offline storage initialized successfully');
      });

      request.onError.listen((event) {
        print('Error initializing offline storage: ${event.target}');
      });

      // Wait for initialization
      await request.future;
    } catch (e) {
      print('Error initializing offline storage: $e');
    }
  }

  // Create object stores
  void _createObjectStores() {
    if (_database == null) return;

    // Create object stores with indexes
    _createStore(_userStore, keyPath: 'id');
    _createStore(_workspaceStore, keyPath: 'id');
    _createStore(_socialMediaStore, keyPath: 'id');
    _createStore(_bioSiteStore, keyPath: 'id');
    _createStore(_analyticsStore, keyPath: 'id');
    _createStore(_crmStore, keyPath: 'id');
    _createStore(_marketingStore, keyPath: 'id');
    _createStore(_courseStore, keyPath: 'id');
    _createStore(_draftsStore, keyPath: 'id');
    _createStore(_pendingActionsStore, keyPath: 'id');
    _createStore(_cacheStore, keyPath: 'key');
  }

  // Create individual store
  void _createStore(String storeName, {String? keyPath}) {
    if (_database == null) return;

    try {
      if (!_database!.objectStoreNames.contains(storeName)) {
        final store = _database!.createObjectStore(storeName, keyPath: keyPath);
        
        // Add common indexes
        store.createIndex('timestamp', 'timestamp', unique: false);
        store.createIndex('updated_at', 'updated_at', unique: false);
        
        // Add specific indexes based on store
        switch (storeName) {
          case _userStore:
            store.createIndex('email', 'email', unique: true);
            break;
          case _workspaceStore:
            store.createIndex('user_id', 'user_id', unique: false);
            break;
          case _socialMediaStore:
            store.createIndex('platform', 'platform', unique: false);
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            break;
          case _bioSiteStore:
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            store.createIndex('slug', 'slug', unique: true);
            break;
          case _analyticsStore:
            store.createIndex('type', 'type', unique: false);
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            break;
          case _crmStore:
            store.createIndex('type', 'type', unique: false);
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            break;
          case _marketingStore:
            store.createIndex('campaign_id', 'campaign_id', unique: false);
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            break;
          case _courseStore:
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            break;
          case _draftsStore:
            store.createIndex('type', 'type', unique: false);
            store.createIndex('workspace_id', 'workspace_id', unique: false);
            break;
          case _pendingActionsStore:
            store.createIndex('action_type', 'action_type', unique: false);
            store.createIndex('status', 'status', unique: false);
            break;
          case _cacheStore:
            store.createIndex('expiry', 'expiry', unique: false);
            break;
        }
      }
    } catch (e) {
      print('Error creating store $storeName: $e');
    }
  }

  // Generic store operations
  Future<T?> get<T>(String storeName, String key) async {
    if (!_isInitialized || _database == null) return null;

    try {
      final transaction = _database!.transaction([storeName], 'readonly');
      final store = transaction.objectStore(storeName);
      final request = store.getObject(key);
      
      await request.future;
      
      if (request.result != null) {
        return request.result as T;
      }
      return null;
    } catch (e) {
      print('Error getting data from $storeName: $e');
      return null;
    }
  }

  Future<void> put(String storeName, Map<String, dynamic> data) async {
    if (!_isInitialized || _database == null) return;

    try {
      data['updated_at'] = DateTime.now().millisecondsSinceEpoch;
      
      final transaction = _database!.transaction([storeName], 'readwrite');
      final store = transaction.objectStore(storeName);
      
      await store.put(data);
      await transaction.completed;
    } catch (e) {
      print('Error putting data to $storeName: $e');
    }
  }

  Future<void> delete(String storeName, String key) async {
    if (!_isInitialized || _database == null) return;

    try {
      final transaction = _database!.transaction([storeName], 'readwrite');
      final store = transaction.objectStore(storeName);
      
      await store.delete(key);
      await transaction.completed;
    } catch (e) {
      print('Error deleting data from $storeName: $e');
    }
  }

  Future<List<Map<String, dynamic>>> getAll(String storeName) async {
    if (!_isInitialized || _database == null) return [];

    try {
      final transaction = _database!.transaction([storeName], 'readonly');
      final store = transaction.objectStore(storeName);
      final request = store.getAll();
      
      await request.future;
      
      return List<Map<String, dynamic>>.from(request.result ?? []);
    } catch (e) {
      print('Error getting all data from $storeName: $e');
      return [];
    }
  }

  Future<List<Map<String, dynamic>>> getByIndex(String storeName, String indexName, dynamic value) async {
    if (!_isInitialized || _database == null) return [];

    try {
      final transaction = _database!.transaction([storeName], 'readonly');
      final store = transaction.objectStore(storeName);
      final index = store.index(indexName);
      final request = index.getAll(value);
      
      await request.future;
      
      return List<Map<String, dynamic>>.from(request.result ?? []);
    } catch (e) {
      print('Error getting data by index from $storeName: $e');
      return [];
    }
  }

  Future<void> clear(String storeName) async {
    if (!_isInitialized || _database == null) return;

    try {
      final transaction = _database!.transaction([storeName], 'readwrite');
      final store = transaction.objectStore(storeName);
      
      await store.clear();
      await transaction.completed;
    } catch (e) {
      print('Error clearing store $storeName: $e');
    }
  }

  // Specific data operations
  
  // User data
  Future<void> saveUser(Map<String, dynamic> userData) async {
    await put(_userStore, userData);
  }

  Future<Map<String, dynamic>?> getUser(String userId) async {
    return await get(_userStore, userId);
  }

  // Workspace data
  Future<void> saveWorkspace(Map<String, dynamic> workspaceData) async {
    await put(_workspaceStore, workspaceData);
  }

  Future<List<Map<String, dynamic>>> getUserWorkspaces(String userId) async {
    return await getByIndex(_workspaceStore, 'user_id', userId);
  }

  // Social media data
  Future<void> saveSocialMediaAccount(Map<String, dynamic> accountData) async {
    await put(_socialMediaStore, accountData);
  }

  Future<List<Map<String, dynamic>>> getWorkspaceSocialAccounts(String workspaceId) async {
    return await getByIndex(_socialMediaStore, 'workspace_id', workspaceId);
  }

  // Bio site data
  Future<void> saveBioSite(Map<String, dynamic> bioSiteData) async {
    await put(_bioSiteStore, bioSiteData);
  }

  Future<List<Map<String, dynamic>>> getWorkspaceBioSites(String workspaceId) async {
    return await getByIndex(_bioSiteStore, 'workspace_id', workspaceId);
  }

  // Analytics data
  Future<void> saveAnalytics(Map<String, dynamic> analyticsData) async {
    await put(_analyticsStore, analyticsData);
  }

  Future<List<Map<String, dynamic>>> getWorkspaceAnalytics(String workspaceId) async {
    return await getByIndex(_analyticsStore, 'workspace_id', workspaceId);
  }

  // CRM data
  Future<void> saveCrmData(Map<String, dynamic> crmData) async {
    await put(_crmStore, crmData);
  }

  Future<List<Map<String, dynamic>>> getWorkspaceCrmData(String workspaceId) async {
    return await getByIndex(_crmStore, 'workspace_id', workspaceId);
  }

  // Marketing data
  Future<void> saveMarketingData(Map<String, dynamic> marketingData) async {
    await put(_marketingStore, marketingData);
  }

  Future<List<Map<String, dynamic>>> getWorkspaceMarketingData(String workspaceId) async {
    return await getByIndex(_marketingStore, 'workspace_id', workspaceId);
  }

  // Course data
  Future<void> saveCourse(Map<String, dynamic> courseData) async {
    await put(_courseStore, courseData);
  }

  Future<List<Map<String, dynamic>>> getWorkspaceCourses(String workspaceId) async {
    return await getByIndex(_courseStore, 'workspace_id', workspaceId);
  }

  // Draft data
  Future<void> saveDraft(Map<String, dynamic> draftData) async {
    draftData['id'] = draftData['id'] ?? DateTime.now().millisecondsSinceEpoch.toString();
    draftData['timestamp'] = DateTime.now().millisecondsSinceEpoch;
    await put(_draftsStore, draftData);
  }

  Future<List<Map<String, dynamic>>> getDraftsByType(String type) async {
    return await getByIndex(_draftsStore, 'type', type);
  }

  Future<List<Map<String, dynamic>>> getAllDrafts() async {
    return await getAll(_draftsStore);
  }

  // Pending actions
  Future<void> savePendingAction(Map<String, dynamic> action) async {
    action['id'] = action['id'] ?? DateTime.now().millisecondsSinceEpoch.toString();
    action['timestamp'] = DateTime.now().millisecondsSinceEpoch;
    action['status'] = action['status'] ?? 'pending';
    await put(_pendingActionsStore, action);
  }

  Future<List<Map<String, dynamic>>> getPendingActions() async {
    return await getByIndex(_pendingActionsStore, 'status', 'pending');
  }

  Future<void> markActionCompleted(String actionId) async {
    final action = await get<Map<String, dynamic>>(_pendingActionsStore, actionId);
    if (action != null) {
      action['status'] = 'completed';
      action['completed_at'] = DateTime.now().millisecondsSinceEpoch;
      await put(_pendingActionsStore, action);
    }
  }

  Future<void> markActionFailed(String actionId, String error) async {
    final action = await get<Map<String, dynamic>>(_pendingActionsStore, actionId);
    if (action != null) {
      action['status'] = 'failed';
      action['error'] = error;
      action['failed_at'] = DateTime.now().millisecondsSinceEpoch;
      await put(_pendingActionsStore, action);
    }
  }

  // Cache operations
  Future<void> cacheData(String key, Map<String, dynamic> data, {Duration? ttl}) async {
    final cacheData = {
      'key': key,
      'data': data,
      'timestamp': DateTime.now().millisecondsSinceEpoch,
      'expiry': ttl != null ? DateTime.now().add(ttl).millisecondsSinceEpoch : null,
    };
    await put(_cacheStore, cacheData);
  }

  Future<Map<String, dynamic>?> getCachedData(String key) async {
    final cacheData = await get<Map<String, dynamic>>(_cacheStore, key);
    
    if (cacheData == null) return null;
    
    // Check if expired
    if (cacheData['expiry'] != null && 
        DateTime.now().millisecondsSinceEpoch > cacheData['expiry']) {
      await delete(_cacheStore, key);
      return null;
    }
    
    return cacheData['data'];
  }

  Future<void> clearExpiredCache() async {
    final allCache = await getAll(_cacheStore);
    final now = DateTime.now().millisecondsSinceEpoch;
    
    for (final cache in allCache) {
      if (cache['expiry'] != null && now > cache['expiry']) {
        await delete(_cacheStore, cache['key']);
      }
    }
  }

  // Utility methods
  Future<int> getStorageSize() async {
    if (!_isInitialized || _database == null) return 0;

    try {
      int totalSize = 0;
      final storeNames = [
        _userStore, _workspaceStore, _socialMediaStore, _bioSiteStore,
        _analyticsStore, _crmStore, _marketingStore, _courseStore,
        _draftsStore, _pendingActionsStore, _cacheStore
      ];

      for (final storeName in storeNames) {
        final data = await getAll(storeName);
        totalSize += jsonEncode(data).length;
      }

      return totalSize;
    } catch (e) {
      print('Error calculating storage size: $e');
      return 0;
    }
  }

  Future<void> clearAllData() async {
    if (!_isInitialized || _database == null) return;

    final storeNames = [
      _userStore, _workspaceStore, _socialMediaStore, _bioSiteStore,
      _analyticsStore, _crmStore, _marketingStore, _courseStore,
      _draftsStore, _pendingActionsStore, _cacheStore
    ];

    for (final storeName in storeNames) {
      await clear(storeName);
    }

    print('All offline data cleared');
  }

  // Export data for backup
  Future<Map<String, dynamic>> exportData() async {
    final data = <String, dynamic>{};
    
    final storeNames = [
      _userStore, _workspaceStore, _socialMediaStore, _bioSiteStore,
      _analyticsStore, _crmStore, _marketingStore, _courseStore,
      _draftsStore, _pendingActionsStore, _cacheStore
    ];

    for (final storeName in storeNames) {
      data[storeName] = await getAll(storeName);
    }

    return data;
  }

  // Import data from backup
  Future<void> importData(Map<String, dynamic> data) async {
    for (final storeName in data.keys) {
      final storeData = data[storeName] as List<dynamic>;
      
      for (final item in storeData) {
        if (item is Map<String, dynamic>) {
          await put(storeName, item);
        }
      }
    }

    print('Data imported successfully');
  }

  // Dispose
  void dispose() {
    _database?.close();
    _database = null;
    _isInitialized = false;
  }
}

// Helper class for offline operations
class OfflineDataSync {
  static final OfflineStorageService _storage = OfflineStorageService();

  // Sync pending actions when online
  static Future<void> syncPendingActions() async {
    final pendingActions = await _storage.getPendingActions();
    
    for (final action in pendingActions) {
      try {
        await _processPendingAction(action);
        await _storage.markActionCompleted(action['id']);
      } catch (e) {
        await _storage.markActionFailed(action['id'], e.toString());
      }
    }
  }

  static Future<void> _processPendingAction(Map<String, dynamic> action) async {
    // Process the action based on its type
    switch (action['action_type']) {
      case 'create_post':
        // Handle social media post creation
        break;
      case 'update_bio_site':
        // Handle bio site update
        break;
      case 'create_contact':
        // Handle CRM contact creation
        break;
      case 'send_email':
        // Handle email sending
        break;
      default:
        print('Unknown action type: ${action['action_type']}');
    }
  }
}