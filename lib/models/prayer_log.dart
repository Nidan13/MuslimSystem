import '../utils/model_parser.dart';

class PrayerLog {
  final String name;
  final String key;
  final bool isCompleted;
  final String? completedAt;

  PrayerLog({
    required this.name,
    required this.key,
    required this.isCompleted,
    this.completedAt,
  });

  factory PrayerLog.fromJson(Map<String, dynamic> json) {
    return PrayerLog(
      name: json['name'] as String? ?? '',
      key: json['key'] as String? ?? '',
      isCompleted: ModelParser.parseBool(json['is_completed']),
      completedAt: json['completed_at'] as String?,
    );
  }
}

class PrayerSummary {
  final int completedCount;
  final int totalCount;

  PrayerSummary({
    required this.completedCount,
    required this.totalCount,
  });

  factory PrayerSummary.fromJson(Map<String, dynamic> json) {
    return PrayerSummary(
      completedCount: ModelParser.parseInt(json['completed_count']),
      totalCount: ModelParser.parseInt(json['total_count']),
    );
  }
}

class PrayerResponse {
  final List<PrayerLog> prayers;
  final PrayerSummary summary;

  PrayerResponse({
    required this.prayers,
    required this.summary,
  });

  factory PrayerResponse.fromJson(Map<String, dynamic> json) {
    return PrayerResponse(
      prayers: (json['data'] as List?)
              ?.map((e) => PrayerLog.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      summary: PrayerSummary.fromJson(
          (json['summary'] ?? {}) as Map<String, dynamic>),
    );
  }
}
