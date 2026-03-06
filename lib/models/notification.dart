class NotificationModel {
  final int id;
  final int userId;
  final int? actorId;
  final String type;
  final Map<String, dynamic>? data;
  final DateTime? readAt;
  final DateTime createdAt;
  final NotificationActor? actor;

  NotificationModel({
    required this.id,
    required this.userId,
    this.actorId,
    required this.type,
    this.data,
    this.readAt,
    required this.createdAt,
    this.actor,
  });

  bool get isRead => readAt != null;

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    return NotificationModel(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      actorId: json['actor_id'],
      type: json['type'] ?? 'system',
      data: json['data'],
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
      actor: json['actor'] != null
          ? NotificationActor.fromJson(json['actor'])
          : null,
    );
  }
}

class NotificationActor {
  final int id;
  final String username;
  final int level;

  NotificationActor({
    required this.id,
    required this.username,
    required this.level,
  });

  factory NotificationActor.fromJson(Map<String, dynamic> json) {
    return NotificationActor(
      id: json['id'] ?? 0,
      username: json['username'] ?? 'Anonymous',
      level: json['level'] ?? 1,
    );
  }
}
