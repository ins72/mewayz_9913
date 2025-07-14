class Workspace {
  final String id;
  final String name;
  final String? description;
  final String? logo;
  final String ownerId;
  final Map<String, dynamic>? settings;
  final DateTime createdAt;
  final DateTime updatedAt;

  Workspace({
    required this.id,
    required this.name,
    this.description,
    this.logo,
    required this.ownerId,
    this.settings,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Workspace.fromJson(Map<String, dynamic> json) {
    return Workspace(
      id: json['id'].toString(),
      name: json['name'] ?? '',
      description: json['description'],
      logo: json['logo'],
      ownerId: json['user_id'].toString(),
      settings: json['settings'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'logo': logo,
      'user_id': ownerId,
      'settings': settings,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}