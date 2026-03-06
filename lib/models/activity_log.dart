class ActivityLog {
  final int id;
  final String type;
  final int amount;
  final String description;
  final DateTime createdAt;

  ActivityLog({
    required this.id,
    required this.type,
    required this.amount,
    required this.description,
    required this.createdAt,
  });

  factory ActivityLog.fromJson(Map<String, dynamic> json) {
    return ActivityLog(
      id: json['id'] ?? 0,
      type: json['type'] ?? 'unknown',
      amount: json['amount'] ?? 0,
      description: json['description'] ?? '',
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : DateTime.now(),
    );
  }
}
