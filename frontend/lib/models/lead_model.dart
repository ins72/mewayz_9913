class Lead {
  final String id;
  final String name;
  final String email;
  final String? phone;
  final String status;
  final String? source;
  final String? notes;
  final DateTime createdAt;
  final DateTime updatedAt;

  Lead({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.status,
    this.source,
    this.notes,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Lead.fromJson(Map<String, dynamic> json) {
    return Lead(
      id: json['id'].toString(),
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      status: json['status'] ?? 'cold',
      source: json['source'],
      notes: json['notes'],
      createdAt: DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
      updatedAt: DateTime.tryParse(json['updated_at'] ?? '') ?? DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'status': status,
      'source': source,
      'notes': notes,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}