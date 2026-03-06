class Dungeon {
  final int id;
  final String name;
  final String description;
  final String rank;
  final int minLevel;
  final int rewardExp;
  final String status; // 'open', 'in_progress', 'cleared'
  final double progress;
  final int requiredPlayers;
  final int currentPlayers;
  final List<dynamic>? lootPool;
  final String?
      objectiveType; // nullable — e.g. 'prayer', 'quran', 'habit', 'custom'
  final int objectiveTarget; // target count for the objective
  final bool isParticipating;

  Dungeon({
    required this.id,
    required this.name,
    required this.description,
    required this.rank,
    required this.minLevel,
    required this.rewardExp,
    required this.status,
    required this.progress,
    this.requiredPlayers = 1,
    this.currentPlayers = 0,
    this.lootPool,
    this.objectiveType,
    this.objectiveTarget = 0,
    this.isParticipating = false,
  });

  factory Dungeon.fromJson(Map<String, dynamic> json) {
    return Dungeon(
      id: json['id'] as int? ?? 0,
      name: json['name'] as String? ?? 'Unknown Dungeon',
      description: json['description'] as String? ?? '',
      rank: json['rank'] as String? ?? (json['rank_tier']?['name'] ?? 'E'),
      minLevel: json['min_level_requirement'] as int? ?? 1,
      rewardExp: json['reward_exp'] as int? ?? 0,
      status: json['status'] as String? ?? 'open',
      progress: (json['progress'] as num? ?? 0.0).toDouble(),
      requiredPlayers: json['required_players'] as int? ?? 1,
      currentPlayers: json['current_players'] as int? ?? 0,
      lootPool: json['loot_pool'] as List<dynamic>?,
      objectiveType: json['objective_type'] as String?,
      objectiveTarget: json['objective_target'] as int? ?? 0,
      isParticipating: json['is_participating'] == true ||
          json['is_participating'] == 1 ||
          (json['is_participating'] is String &&
              json['is_participating'].toString().toLowerCase() == 'true'),
    );
  }
}
