import '../utils/model_parser.dart';

class DailyTask {
  final int id;
  final String name;
  final String? description;
  final int soulPoints;
  final String icon;
  bool isCompleted;
  final String? completedAt;
  final bool isCustom;

  DailyTask({
    required this.id,
    required this.name,
    this.description,
    required this.soulPoints,
    required this.icon,
    required this.isCompleted,
    this.completedAt,
    required this.isCustom,
  });

  DailyTask copyWith({
    int? id,
    String? name,
    String? description,
    int? soulPoints,
    String? icon,
    bool? isCompleted,
    String? completedAt,
    bool? isCustom,
  }) {
    return DailyTask(
      id: id ?? this.id,
      name: name ?? this.name,
      description: description ?? this.description,
      soulPoints: soulPoints ?? this.soulPoints,
      icon: icon ?? this.icon,
      isCompleted: isCompleted ?? this.isCompleted,
      completedAt: completedAt ?? this.completedAt,
      isCustom: isCustom ?? this.isCustom,
    );
  }

  factory DailyTask.fromJson(Map<String, dynamic> json) {
    return DailyTask(
      id: ModelParser.parseInt(json['id']),
      name: json['name'] as String? ?? 'Unknown Task',
      description: json['description'] as String?,
      soulPoints: ModelParser.parseInt(json['soul_points']),
      icon: json['icon'] as String? ?? '⭐',
      isCompleted: ModelParser.parseBool(json['is_completed']),
      completedAt: json['completed_at'] as String?,
      isCustom: ModelParser.parseBool(json['is_custom']),
    );
  }
}

class DailyTaskSummary {
  final int completedCount;
  final int totalCount;
  final int earnedPoints;
  final int totalPoints;
  final double progressPercentage;

  DailyTaskSummary({
    required this.completedCount,
    required this.totalCount,
    required this.earnedPoints,
    required this.totalPoints,
    required this.progressPercentage,
  });

  factory DailyTaskSummary.fromJson(Map<String, dynamic> json) {
    return DailyTaskSummary(
      completedCount: ModelParser.parseInt(json['completed_count']),
      totalCount: ModelParser.parseInt(json['total_count']),
      earnedPoints: ModelParser.parseInt(json['earned_points']),
      totalPoints: ModelParser.parseInt(json['total_points']),
      progressPercentage: ModelParser.parseDouble(json['progress_percentage']),
    );
  }
}

class DailyTaskResponse {
  final List<DailyTask> tasks;
  final DailyTaskSummary? summary;

  DailyTaskResponse({
    required this.tasks,
    this.summary,
  });

  factory DailyTaskResponse.fromJson(Map<String, dynamic> json) {
    return DailyTaskResponse(
      tasks: (json['tasks'] as List?)
              ?.map((e) => DailyTask.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      summary: json['summary'] is Map<String, dynamic>
          ? DailyTaskSummary.fromJson(json['summary'] as Map<String, dynamic>)
          : null,
    );
  }
}
