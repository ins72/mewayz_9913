import 'dart:convert';
import 'package:http/http.dart' as http;
import 'storage_service.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api'; // Update with your Laravel API URL
  
  static Future<Map<String, String>> _getHeaders() async {
    final token = await StorageService.getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // Auth endpoints
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> register(String name, String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/register'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': password,
      }),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> logout() async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/logout'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> forgotPassword(String email) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/forgot-password'),
      headers: await _getHeaders(),
      body: jsonEncode({'email': email}),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>?> getUser() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/user'),
        headers: await _getHeaders(),
      );
      
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  // Workspace endpoints
  static Future<Map<String, dynamic>> getWorkspaces() async {
    final response = await http.get(
      Uri.parse('$baseUrl/workspaces'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createWorkspace(String name, String description) async {
    final response = await http.post(
      Uri.parse('$baseUrl/workspaces'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'name': name,
        'description': description,
      }),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> inviteTeamMember(String workspaceId, String email, String role) async {
    final response = await http.post(
      Uri.parse('$baseUrl/workspaces/$workspaceId/invite'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'email': email,
        'role': role,
      }),
    );
    
    return jsonDecode(response.body);
  }

  // Social Media endpoints
  static Future<Map<String, dynamic>> getSocialMediaAccounts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/social-media/accounts'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> scheduleSocialMediaPost(Map<String, dynamic> postData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/social-media/schedule'),
      headers: await _getHeaders(),
      body: jsonEncode(postData),
    );
    
    return jsonDecode(response.body);
  }

  // Bio Link endpoints
  static Future<Map<String, dynamic>> getBioSites() async {
    final response = await http.get(
      Uri.parse('$baseUrl/bio-sites'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createBioSite(Map<String, dynamic> siteData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/bio-sites'),
      headers: await _getHeaders(),
      body: jsonEncode(siteData),
    );
    
    return jsonDecode(response.body);
  }

  // CRM endpoints
  static Future<Map<String, dynamic>> getLeads() async {
    final response = await http.get(
      Uri.parse('$baseUrl/crm/leads'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createLead(Map<String, dynamic> leadData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/crm/leads'),
      headers: await _getHeaders(),
      body: jsonEncode(leadData),
    );
    
    return jsonDecode(response.body);
  }

  // Email Marketing endpoints
  static Future<Map<String, dynamic>> getEmailCampaigns() async {
    final response = await http.get(
      Uri.parse('$baseUrl/email-marketing/campaigns'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createEmailCampaign(Map<String, dynamic> campaignData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/email-marketing/campaigns'),
      headers: await _getHeaders(),
      body: jsonEncode(campaignData),
    );
    
    return jsonDecode(response.body);
  }

  // E-commerce endpoints
  static Future<Map<String, dynamic>> getProducts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/products'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createProduct(Map<String, dynamic> productData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/ecommerce/products'),
      headers: await _getHeaders(),
      body: jsonEncode(productData),
    );
    
    return jsonDecode(response.body);
  }

  // Courses endpoints
  static Future<Map<String, dynamic>> getCourses() async {
    final response = await http.get(
      Uri.parse('$baseUrl/courses'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createCourse(Map<String, dynamic> courseData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/courses'),
      headers: await _getHeaders(),
      body: jsonEncode(courseData),
    );
    
    return jsonDecode(response.body);
  }

  // Analytics endpoints
  static Future<Map<String, dynamic>> getAnalytics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/analytics'),
      headers: await _getHeaders(),
    );
    
    return jsonDecode(response.body);
  }
}