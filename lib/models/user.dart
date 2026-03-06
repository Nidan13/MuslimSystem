class User {
  final int id;
  final String username;
  final String email;
  final String gender;
  final int level;
  final int currentExp;
  final int? overflowExp;
  final int soulPoints;
  final int fatigue;
  final int? rankTierId;
  final bool isActive;
  final String? referralCode;

  User({
    required this.id,
    required this.username,
    required this.email,
    required this.gender,
    required this.level,
    required this.currentExp,
    this.overflowExp,
    required this.soulPoints,
    required this.fatigue,
    this.rankTierId,
    this.isActive = false,
    this.referralCode,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] as int,
      username: json['username'] as String,
      email: json['email'] as String,
      gender: json['gender'] as String,
      level: json['level'] as int,
      currentExp: json['current_exp'] as int? ?? 0,
      overflowExp: json['overflow_exp'] as int?,
      soulPoints: json['soul_points'] as int? ?? 0,
      fatigue: json['fatigue'] as int? ?? 0,
      rankTierId: json['rank_tier_id'] as int?,
      isActive: json['is_active'] as bool? ?? false,
      referralCode: json['referral_code'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'username': username,
      'email': email,
      'gender': gender,
      'level': level,
      'current_exp': currentExp,
      'overflow_exp': overflowExp,
      'soul_points': soulPoints,
      'fatigue': fatigue,
      'rank_tier_id': rankTierId,
      'is_active': isActive,
    };
  }
}
