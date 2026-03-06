class Habit {
  final int id;
  final String title;
  final String? notes;
  final String difficulty;
  final bool isPositive;
  final bool isNegative;
  final String frequency;
  int count;
  final String? icon;
  final String? color;

  Habit({
    required this.id,
    required this.title,
    this.notes,
    required this.difficulty,
    required this.isPositive,
    required this.isNegative,
    required this.frequency,
    required this.count,
    this.icon,
    this.color,
  });

  factory Habit.fromJson(Map<String, dynamic> json) {
    return Habit(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      title: json['title'] ?? '',
      notes: json['notes'],
      difficulty: json['difficulty'] ?? 'easy',
      isPositive: json['is_positive'] == true ||
          json['is_positive'] == 1 ||
          (json['is_positive'] is String &&
              json['is_positive'].toString().toLowerCase() == 'true'),
      isNegative: json['is_negative'] == true ||
          json['is_negative'] == 1 ||
          (json['is_negative'] is String &&
              json['is_negative'].toString().toLowerCase() == 'true'),
      frequency: json['frequency'] ?? 'daily',
      count: int.tryParse(json['count']?.toString() ?? '0') ?? 0,
      icon: json['icon'],
      color: json['color'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'notes': notes,
      'difficulty': difficulty,
      'is_positive': isPositive,
      'is_negative': isNegative,
      'frequency': frequency,
      'count': count,
      'icon': icon,
    };
  }

  Map<String, int> get rewards {
    switch (difficulty.toLowerCase()) {
      case 'trivial':
        return {'xp': 5};
      case 'easy':
        return {'xp': 10};
      case 'medium':
        return {'xp': 20};
      case 'hard':
        return {'xp': 40};
      default:
        return {'xp': 10};
    }
  }

  int get hpPenalty {
    switch (difficulty.toLowerCase()) {
      case 'trivial':
        return 1;
      case 'easy':
        return 2;
      case 'medium':
        return 4;
      case 'hard':
        return 8;
      default:
        return 2;
    }
  }
}
