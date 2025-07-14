import 'package:flutter/foundation.dart';
import '../models/workspace_model.dart';
import '../services/api_service.dart';

class WorkspaceProvider extends ChangeNotifier {
  List<Workspace> _workspaces = [];
  Workspace? _currentWorkspace;
  bool _isLoading = false;
  String? _error;

  List<Workspace> get workspaces => _workspaces;
  Workspace? get currentWorkspace => _currentWorkspace;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> loadWorkspaces() async {
    _setLoading(true);
    try {
      final response = await ApiService.getWorkspaces();
      _workspaces = (response['data'] as List)
          .map((json) => Workspace.fromJson(json))
          .toList();
      
      if (_workspaces.isNotEmpty && _currentWorkspace == null) {
        _currentWorkspace = _workspaces.first;
      }
    } catch (e) {
      _error = e.toString();
    }
    _setLoading(false);
  }

  Future<bool> createWorkspace(String name, String description) async {
    _setLoading(true);
    try {
      final response = await ApiService.createWorkspace(name, description);
      if (response['success'] == true) {
        final workspace = Workspace.fromJson(response['data']);
        _workspaces.add(workspace);
        _currentWorkspace = workspace;
        _setLoading(false);
        return true;
      }
    } catch (e) {
      _error = e.toString();
    }
    _setLoading(false);
    return false;
  }

  void switchWorkspace(Workspace workspace) {
    _currentWorkspace = workspace;
    notifyListeners();
  }

  Future<bool> inviteTeamMember(String email, String role) async {
    if (_currentWorkspace == null) return false;
    
    try {
      final response = await ApiService.inviteTeamMember(
        _currentWorkspace!.id,
        email,
        role,
      );
      return response['success'] == true;
    } catch (e) {
      _error = e.toString();
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