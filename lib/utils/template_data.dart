class TaskTemplate {
  final int? id;
  final String category;
  final String name;
  final String description;
  final String icon;
  final int soulPoints;
  final String type; // 'task', 'habit', 'todo'

  TaskTemplate({
    this.id,
    required this.category,
    required this.name,
    required this.description,
    required this.icon,
    required this.soulPoints,
    required this.type,
  });

  factory TaskTemplate.fromJson(Map<String, dynamic> json) {
    return TaskTemplate(
      id: int.tryParse(json['id']?.toString() ?? '0'),
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      category: json['category'] ?? '',
      icon: json['icon'] ?? '⭐',
      soulPoints: int.tryParse(json['soul_points']?.toString() ?? '10') ?? 10,
      type: json['type'] ?? 'task',
    );
  }
}

// TaskTemplate Class moved to backend database.
// Static lists removed to reduce app weight.
// Use TemplateService to fetch current templates.
