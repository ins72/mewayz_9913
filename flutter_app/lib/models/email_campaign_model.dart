class EmailCampaign {
  final String id;
  final String name;
  final String subject;
  final String content;
  final String status;
  final List<String> audienceIds;
  final DateTime? scheduledAt;
  final DateTime? sentAt;
  final int? sentCount;
  final int? openCount;
  final int? clickCount;
  final DateTime createdAt;
  final DateTime updatedAt;

  EmailCampaign({
    required this.id,
    required this.name,
    required this.subject,
    required this.content,
    required this.status,
    required this.audienceIds,
    this.scheduledAt,
    this.sentAt,
    this.sentCount,
    this.openCount,
    this.clickCount,
    required this.createdAt,
    required this.updatedAt,
  });

  factory EmailCampaign.fromJson(Map<String, dynamic> json) {
    return EmailCampaign(
      id: json['id'].toString(),
      name: json['name'] ?? '',
      subject: json['subject'] ?? '',
      content: json['content'] ?? '',
      status: json['status'] ?? 'draft',
      audienceIds: json['audience_ids'] != null ? List<String>.from(json['audience_ids']) : [],
      scheduledAt: json['scheduled_at'] != null ? DateTime.tryParse(json['scheduled_at']) : null,
      sentAt: json['sent_at'] != null ? DateTime.tryParse(json['sent_at']) : null,
      sentCount: json['sent_count'],
      openCount: json['open_count'],
      clickCount: json['click_count'],
      createdAt: DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
      updatedAt: DateTime.tryParse(json['updated_at'] ?? '') ?? DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'subject': subject,
      'content': content,
      'status': status,
      'audience_ids': audienceIds,
      'scheduled_at': scheduledAt?.toIso8601String(),
      'sent_at': sentAt?.toIso8601String(),
      'sent_count': sentCount,
      'open_count': openCount,
      'click_count': clickCount,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  double get openRate {
    if (sentCount == null || sentCount == 0) return 0.0;
    return ((openCount ?? 0) / sentCount!) * 100;
  }

  double get clickRate {
    if (sentCount == null || sentCount == 0) return 0.0;
    return ((clickCount ?? 0) / sentCount!) * 100;
  }
}