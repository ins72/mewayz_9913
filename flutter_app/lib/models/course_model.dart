class Course {
  final String id;
  final String name;
  final String? description;
  final double price;
  final String? category;
  final String? thumbnail;
  final String level;
  final String status;
  final int lessonsCount;
  final int studentsCount;
  final DateTime createdAt;
  final DateTime updatedAt;

  Course({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    this.category,
    this.thumbnail,
    required this.level,
    required this.status,
    this.lessonsCount = 0,
    this.studentsCount = 0,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Course.fromJson(Map<String, dynamic> json) {
    return Course(
      id: json['id'].toString(),
      name: json['name'] ?? '',
      description: json['description'],
      price: (json['price'] ?? 0).toDouble(),
      category: json['category'],
      thumbnail: json['thumbnail'],
      level: json['level'] ?? 'beginner',
      status: json['status'] ?? 'draft',
      lessonsCount: json['lessons_count'] ?? 0,
      studentsCount: json['students_count'] ?? 0,
      createdAt: DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
      updatedAt: DateTime.tryParse(json['updated_at'] ?? '') ?? DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'price': price,
      'category': category,
      'thumbnail': thumbnail,
      'level': level,
      'status': status,
      'lessons_count': lessonsCount,
      'students_count': studentsCount,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}