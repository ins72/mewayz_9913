class BioSite {
  final String id;
  final String title;
  final String slug;
  final String? description;
  final String status;
  final Map<String, dynamic>? content;
  final String? templateId;
  final DateTime createdAt;
  final DateTime updatedAt;

  BioSite({
    required this.id,
    required this.title,
    required this.slug,
    this.description,
    required this.status,
    this.content,
    this.templateId,
    required this.createdAt,
    required this.updatedAt,
  });

  factory BioSite.fromJson(Map<String, dynamic> json) {
    return BioSite(
      id: json['id'].toString(),
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      description: json['description'],
      status: json['status'] ?? 'draft',
      content: json['content'],
      templateId: json['template_id']?.toString(),
      createdAt: DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
      updatedAt: DateTime.tryParse(json['updated_at'] ?? '') ?? DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'slug': slug,
      'description': description,
      'status': status,
      'content': content,
      'template_id': templateId,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}