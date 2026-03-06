import '../utils/model_parser.dart';

class Commission {
  final int id;
  final double amount;
  final String description;
  final String status;
  final DateTime createdAt;

  Commission({
    required this.id,
    required this.amount,
    required this.description,
    required this.status,
    required this.createdAt,
  });

  factory Commission.fromJson(Map<String, dynamic> json) {
    return Commission(
      id: ModelParser.parseInt(json['id']),
      amount: ModelParser.parseDouble(json['amount']),
      description: json['description'] ?? '',
      status: json['status'] ?? 'pending',
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : DateTime.now(),
    );
  }
}
