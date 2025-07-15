class Product {
  final String id;
  final String name;
  final String? description;
  final double price;
  final int stockQuantity;
  final String? sku;
  final String? category;
  final List<String>? images;
  final bool isDigital;
  final String status;
  final DateTime createdAt;
  final DateTime updatedAt;

  Product({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    required this.stockQuantity,
    this.sku,
    this.category,
    this.images,
    required this.isDigital,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'].toString(),
      name: json['name'] ?? '',
      description: json['description'],
      price: (json['price'] ?? 0).toDouble(),
      stockQuantity: json['stock_quantity'] ?? 0,
      sku: json['sku'],
      category: json['category'],
      images: json['images'] != null ? List<String>.from(json['images']) : null,
      isDigital: json['is_digital'] == true,
      status: json['status'] ?? 'active',
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
      'stock_quantity': stockQuantity,
      'sku': sku,
      'category': category,
      'images': images,
      'is_digital': isDigital,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}