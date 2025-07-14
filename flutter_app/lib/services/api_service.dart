import 'dart:convert';
import 'package:http/http.dart' as http;
import 'storage_service.dart';

class ApiService {
  static const String baseUrl = 'https://c4c7d68b-2eee-41b3-a4bf-950a861ee229.preview.emergentagent.com/api'; // Production API URL
  
  static Future<Map<String, String>> _getHeaders() async {
    final token = await StorageService.getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Future<Map<String, dynamic>> _handleResponse(http.Response response) async {
    final Map<String, dynamic> data = jsonDecode(response.body);
    
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return data;
    } else {
      throw Exception(data['message'] ?? 'An error occurred');
    }
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
    
    return await _handleResponse(response);
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
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> logout() async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/logout'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> forgotPassword(String email) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/forgot-password'),
      headers: await _getHeaders(),
      body: jsonEncode({'email': email}),
    );
    
    return await _handleResponse(response);
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
    
    return await _handleResponse(response);
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
    
    return await _handleResponse(response);
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
    
    return await _handleResponse(response);
  }

  // Social Media endpoints
  static Future<Map<String, dynamic>> getSocialMediaAccounts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/social-media/accounts'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> connectSocialAccount(String platform, String accessToken) async {
    final response = await http.post(
      Uri.parse('$baseUrl/social-media/accounts/connect'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'platform': platform,
        'access_token': accessToken,
      }),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> scheduleSocialMediaPost(Map<String, dynamic> postData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/social-media/schedule'),
      headers: await _getHeaders(),
      body: jsonEncode(postData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getSocialMediaAnalytics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/social-media/analytics'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> searchInstagramAccounts(Map<String, dynamic> filters) async {
    final response = await http.get(
      Uri.parse('$baseUrl/social-media/instagram/search?${Uri(queryParameters: filters.map((k, v) => MapEntry(k, v.toString()))).query}'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  // Bio Link endpoints
  static Future<Map<String, dynamic>> getBioSites() async {
    final response = await http.get(
      Uri.parse('$baseUrl/bio-sites'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createBioSite(Map<String, dynamic> siteData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/bio-sites'),
      headers: await _getHeaders(),
      body: jsonEncode(siteData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateBioSite(String siteId, Map<String, dynamic> siteData) async {
    final response = await http.put(
      Uri.parse('$baseUrl/bio-sites/$siteId'),
      headers: await _getHeaders(),
      body: jsonEncode(siteData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getBioSiteAnalytics(String siteId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/bio-sites/$siteId/analytics'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  // CRM endpoints
  static Future<Map<String, dynamic>> getLeads({int page = 1}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/crm/leads?page=$page'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createLead(Map<String, dynamic> leadData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/crm/leads'),
      headers: await _getHeaders(),
      body: jsonEncode(leadData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateLead(String leadId, Map<String, dynamic> leadData) async {
    final response = await http.put(
      Uri.parse('$baseUrl/crm/leads/$leadId'),
      headers: await _getHeaders(),
      body: jsonEncode(leadData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> importContacts(String filePath) async {
    // TODO: Implement file upload for contact import
    return {'success': true, 'message': 'Import started'};
  }

  // Email Marketing endpoints
  static Future<Map<String, dynamic>> getEmailCampaigns({int page = 1}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/email-marketing/campaigns?page=$page'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createEmailCampaign(Map<String, dynamic> campaignData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/email-marketing/campaigns'),
      headers: await _getHeaders(),
      body: jsonEncode(campaignData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getEmailTemplates() async {
    final response = await http.get(
      Uri.parse('$baseUrl/email-marketing/templates'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getEmailAnalytics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/email-marketing/analytics'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  // E-commerce endpoints
  static Future<Map<String, dynamic>> getProducts({int page = 1}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/products?page=$page'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createProduct(Map<String, dynamic> productData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/ecommerce/products'),
      headers: await _getHeaders(),
      body: jsonEncode(productData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateProduct(String productId, Map<String, dynamic> productData) async {
    final response = await http.put(
      Uri.parse('$baseUrl/ecommerce/products/$productId'),
      headers: await _getHeaders(),
      body: jsonEncode(productData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getOrders({int page = 1}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/orders?page=$page'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateOrderStatus(String orderId, String status) async {
    final response = await http.put(
      Uri.parse('$baseUrl/ecommerce/orders/$orderId/status'),
      headers: await _getHeaders(),
      body: jsonEncode({'status': status}),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getEcommerceAnalytics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/ecommerce/analytics'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  // Courses endpoints
  static Future<Map<String, dynamic>> getCourses({int page = 1}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/courses?page=$page'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createCourse(Map<String, dynamic> courseData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/courses'),
      headers: await _getHeaders(),
      body: jsonEncode(courseData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> updateCourse(String courseId, Map<String, dynamic> courseData) async {
    final response = await http.put(
      Uri.parse('$baseUrl/courses/$courseId'),
      headers: await _getHeaders(),
      body: jsonEncode(courseData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getCourseLessons(String courseId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/courses/$courseId/lessons'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> createLesson(String courseId, Map<String, dynamic> lessonData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/courses/$courseId/lessons'),
      headers: await _getHeaders(),
      body: jsonEncode(lessonData),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getCourseAnalytics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/courses/analytics'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  // Analytics endpoints
  static Future<Map<String, dynamic>> getAnalyticsOverview() async {
    final response = await http.get(
      Uri.parse('$baseUrl/analytics'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getTrafficAnalytics({String? period}) async {
    final queryParams = period != null ? '?period=$period' : '';
    final response = await http.get(
      Uri.parse('$baseUrl/analytics/traffic$queryParams'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getRevenueAnalytics({String? period}) async {
    final queryParams = period != null ? '?period=$period' : '';
    final response = await http.get(
      Uri.parse('$baseUrl/analytics/revenue$queryParams'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> getReports() async {
    final response = await http.get(
      Uri.parse('$baseUrl/analytics/reports'),
      headers: await _getHeaders(),
    );
    
    return await _handleResponse(response);
  }

  static Future<Map<String, dynamic>> generateReport(Map<String, dynamic> reportData) async {
    final response = await http.post(
      Uri.parse('$baseUrl/analytics/reports/generate'),
      headers: await _getHeaders(),
      body: jsonEncode(reportData),
    );
    
    return await _handleResponse(response);
  }
}