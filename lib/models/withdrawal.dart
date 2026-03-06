import '../utils/model_parser.dart';

class Withdrawal {
  final int id;
  final double amount;
  final String status;
  final String bankName;
  final String accountNumber;
  final String accountName;
  final String? note;
  final DateTime createdAt;

  Withdrawal({
    required this.id,
    required this.amount,
    required this.status,
    required this.bankName,
    required this.accountNumber,
    required this.accountName,
    this.note,
    required this.createdAt,
  });

  factory Withdrawal.fromJson(Map<String, dynamic> json) {
    return Withdrawal(
      id: ModelParser.parseInt(json['id']),
      amount: ModelParser.parseDouble(json['amount']),
      status: json['status'] ?? 'pending',
      bankName: json['bank_name'] ?? '',
      accountNumber: json['account_number'] ?? '',
      accountName: json['account_name'] ?? '',
      note: json['note'],
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : DateTime.now(),
    );
  }
}
