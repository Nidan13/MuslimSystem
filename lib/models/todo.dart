import '../utils/model_parser.dart';

class Todo {
  final int id;
  final String title;
  final String? notes;
  final String difficulty;
  final DateTime? dueDate;
  bool isCompleted;
  final DateTime? completedAt;
  final List<ChecklistItem> checklist;

  Todo({
    required this.id,
    required this.title,
    this.notes,
    required this.difficulty,
    this.dueDate,
    required this.isCompleted,
    this.completedAt,
    required this.checklist,
  });

  factory Todo.fromJson(Map<String, dynamic> json) {
    var list = json['checklist'] as List?;
    List<ChecklistItem> checklistItems =
        list != null ? list.map((i) => ChecklistItem.fromJson(i)).toList() : [];

    return Todo(
      id: ModelParser.parseInt(json['id']),
      title: json['title'] ?? '',
      notes: json['notes'],
      difficulty: json['difficulty'] ?? 'easy',
      dueDate:
          json['due_date'] != null ? DateTime.tryParse(json['due_date']) : null,
      isCompleted: ModelParser.parseBool(json['is_completed']),
      completedAt: json['completed_at'] != null
          ? DateTime.tryParse(json['completed_at'])
          : null,
      checklist: checklistItems,
    );
  }

  Map<String, int> get rewards {
    switch (difficulty.toLowerCase()) {
      case 'trivial':
        return {'xp': 5, 'gold': 1};
      case 'easy':
        return {'xp': 10, 'gold': 2};
      case 'medium':
        return {'xp': 20, 'gold': 5};
      case 'hard':
        return {'xp': 40, 'gold': 10};
      default:
        return {'xp': 10, 'gold': 2};
    }
  }
}

class ChecklistItem {
  final String title;
  bool isCompleted;

  ChecklistItem({
    required this.title,
    required this.isCompleted,
  });

  factory ChecklistItem.fromJson(Map<String, dynamic> json) {
    return ChecklistItem(
      title: json['title'] ?? '',
      isCompleted: ModelParser.parseBool(json['is_completed']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,
      'is_completed': isCompleted,
    };
  }
}
