class Circle {
  final int id;
  final String name;
  final String description;
  final int level;
  final int membersCount;
  final String? iconName;
  final String? colorHex;
  final int leaderId;
  final int xp;
  final bool isJoined;
  final String rank;
  final String weeklyXp;

  Circle({
    required this.id,
    required this.name,
    required this.description,
    required this.level,
    required this.membersCount,
    this.iconName,
    this.colorHex,
    required this.leaderId,
    this.xp = 0,
    this.isJoined = false,
    this.rank = '-',
    this.weeklyXp = '0',
  });

  factory Circle.fromJson(Map<String, dynamic> json) {
    return Circle(
      id: json['id'] as int? ?? 0,
      name: json['name'] as String? ?? 'Unnamed Circle',
      description: json['description'] as String? ?? '',
      level: json['level'] as int? ?? 1,
      membersCount: json['members_count'] as int? ?? 0,
      iconName: json['icon'] as String?,
      colorHex: json['color'] as String?,
      leaderId: json['leader_id'] as int? ?? 0,
      xp: json['xp'] as int? ?? 0,
      isJoined: json['is_joined'] as bool? ?? false,
      rank: json['rank']?.toString() ?? '-',
      weeklyXp: json['weekly_xp']?.toString() ?? '0',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'level': level,
      'members_count': membersCount,
      'icon': iconName,
      'color': colorHex,
      'leader_id': leaderId,
      'xp': xp,
      'is_joined': isJoined,
    };
  }
}
