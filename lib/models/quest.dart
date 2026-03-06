class Quest {
  final int id;
  final String title;
  final String description;
  final String rank;
  final String type; // 'daily', 'hidden', 'trial'
  final int rewardExp;
  final int rewardSoulPoints;
  final Map<String, dynamic> requirements;
  final String status; // 'available', 'pending', 'completed', 'failed'
  final Map<String, dynamic>? progress;
  final bool hasTaken;
  final bool isMandatory;
  final DateTime? startsAt;
  final DateTime? expiresAt;
  final int? timeLimit;
  final String? startTime;
  final String? endTime;

  Quest({
    required this.id,
    required this.title,
    required this.description,
    required this.rank,
    this.type = 'daily',
    required this.rewardExp,
    required this.rewardSoulPoints,
    required this.requirements,
    required this.status,
    this.progress,
    required this.hasTaken,
    this.isMandatory = false,
    this.startsAt,
    this.expiresAt,
    this.timeLimit,
    this.startTime,
    this.endTime,
  });

  factory Quest.fromJson(Map<String, dynamic> json) {
    try {
      bool parseBool(dynamic value) {
        if (value is bool) return value;
        if (value is int) return value == 1;
        if (value is String)
          return value.toLowerCase() == 'true' || value == '1';
        return false;
      }

      final q = Quest(
        id: json['id'] as int? ?? 0,
        title: json['title'] as String? ?? 'Unknown Quest',
        description: json['description'] as String? ?? '',
        rank: json['rank'] as String? ?? 'Novice',
        type: json['type'] as String? ?? 'daily',
        rewardExp: json['reward_exp'] as int? ?? 0,
        rewardSoulPoints: json['reward_soul_points'] as int? ?? 0,
        requirements: json['requirements'] is Map
            ? Map<String, dynamic>.from(json['requirements'])
            : {},
        status: json['status'] as String? ?? 'available',
        progress: json['progress'] is Map
            ? Map<String, dynamic>.from(json['progress'])
            : null,
        hasTaken: parseBool(json['has_taken']),
        isMandatory: parseBool(json['is_mandatory']),
        startsAt: json['starts_at'] != null
            ? DateTime.tryParse(json['starts_at'].toString())
            : null,
        expiresAt: json['expires_at'] != null
            ? DateTime.tryParse(json['expires_at'].toString())
            : null,
        timeLimit: json['time_limit'] as int?,
        startTime: json['start_time'] as String?,
        endTime: json['end_time'] as String?,
      );
      return q;
    } catch (e) {
      rethrow;
    }
  }

  // Helper to calculate total progress percentage
  double get progressPercentage {
    if (requirements.isEmpty) return 0.0;

    int totalReq = 0;
    int currentProg = 0;

    requirements.forEach((key, val) {
      int target = val is int ? val : int.tryParse(val.toString()) ?? 1;
      int current = 0;
      if (progress != null && progress!.containsKey(key)) {
        current = progress![key] is int
            ? progress![key]
            : int.tryParse(progress![key].toString()) ?? 0;
      }
      totalReq += target;
      currentProg += current > target ? target : current;
    });

    if (totalReq == 0) return 0.0;
    return currentProg / totalReq;
  }
}
