import '../utils/model_parser.dart';

class UserProfile {
  final int id;
  final String username;
  final String email;
  final String avatar;
  final String gender;
  final String rank;
  final int level;
  final UserHp hp;
  final UserXp xp;
  final UserStats stats;
  final int soulPoints;
  final int maxSp;
  final int followersCount;
  final int followingCount;
  final String referralCode;
  final int? rankTierId;
  final bool isMenstruating;
  final bool isActive;
  final double balance;

  UserProfile({
    required this.id,
    required this.username,
    required this.email,
    required this.avatar,
    required this.gender,
    required this.rank,
    required this.level,
    required this.hp,
    required this.xp,
    required this.stats,
    required this.soulPoints,
    required this.maxSp,
    required this.followersCount,
    required this.followingCount,
    required this.referralCode,
    this.rankTierId,
    required this.isMenstruating,
    required this.isActive,
    required this.balance,
  });

  factory UserProfile.fromJson(Map<String, dynamic> json) {
    return UserProfile(
      id: ModelParser.parseInt(json['id']),
      username: json['username'] ?? 'Hunter',
      email: json['email'] ?? '',
      avatar: json['avatar'] ?? '',
      gender: json['gender'] ?? 'male',
      rank: json['rank'] ?? 'Novice',
      level: ModelParser.parseInt(json['level']),
      hp: _parseHp(json),
      xp: _parseXp(json),
      stats: UserStats.fromJson(json['user_stat'] ?? json['stats']),
      soulPoints: ModelParser.parseInt(json['soul_points']),
      maxSp: ModelParser.parseInt(json['max_sp'] ?? 1000),
      followersCount: ModelParser.parseInt(json['followers_count']),
      followingCount: ModelParser.parseInt(json['following_count']),
      referralCode: json['referral_code'] ?? '',
      rankTierId: json['rank_tier_id'] != null
          ? ModelParser.parseInt(json['rank_tier_id'])
          : null,
      isMenstruating: ModelParser.parseBool(json['is_menstruating']),
      isActive: ModelParser.parseBool(json['is_active']),
      balance: ModelParser.parseDouble(json['balance']),
    );
  }

  static UserHp _parseHp(Map<String, dynamic> json) {
    if (json['hp'] is Map) {
      return UserHp.fromJson(json['hp']);
    }
    // Flat structure fallback
    int current = ModelParser.parseInt(json['hp']);
    int max = ModelParser.parseInt(json['max_hp'] ?? 100);
    int progress = (max > 0) ? ((current / max) * 100).toInt() : 0;

    return UserHp(current: current, max: max, progress: progress);
  }

  static UserXp _parseXp(Map<String, dynamic> json) {
    if (json['xp'] is Map) {
      return UserXp.fromJson(json['xp']);
    }
    // Flat structure fallback
    int current = ModelParser.parseInt(json['current_exp']);
    int max = ModelParser.parseInt(json['next_level_exp'] ?? 1000);
    int progress = (max > 0) ? ((current / max) * 100).toInt() : 0;

    return UserXp(current: current, max: max, progress: progress);
  }

  int get rankWeight {
    final r = rank.toUpperCase().trim();
    if (r.contains('S')) return 100;
    if (r.contains('A')) return 80;
    if (r.contains('B')) return 60;
    // Handle "Novice" separately because it contains 'C'
    if (r.contains('NOVICE')) return 0;
    if (r.contains('C')) return 40;
    if (r.contains('D')) return 20;
    if (r.contains('E')) return 10;
    return 0; // Unknown or others
  }

  bool get canCreateCircle => true;
}

class UserHp {
  final int current;
  final int max;
  final int progress;

  UserHp({required this.current, required this.max, required this.progress});

  factory UserHp.fromJson(Map<String, dynamic>? json) {
    if (json == null) return UserHp(current: 0, max: 100, progress: 0);
    return UserHp(
      current: (json['current'] as num? ?? 0).toInt(),
      max: (json['max'] as num? ?? 100).toInt(),
      progress: (json['progress'] as num? ?? 0).toInt(),
    );
  }
}

class UserXp {
  final int current;
  final int max;
  final int progress;

  UserXp({required this.current, required this.max, required this.progress});

  int get nextLevelExp => max;

  factory UserXp.fromJson(Map<String, dynamic>? json) {
    if (json == null) return UserXp(current: 0, max: 1000, progress: 0);
    return UserXp(
      current: (json['current'] as num? ?? 0).toInt(),
      max: (json['max'] as num? ?? 1000).toInt(),
      progress: (json['progress'] as num? ?? 0).toInt(),
    );
  }
}

class UserStats {
  final int streak;
  final int salahConsistency;
  final String quranProgress;
  final int totalQuranSessions;
  final int totalMissions; // Kept for backward compatibility (completed)
  final int totalMissionsTaken;
  final int totalMissionsCompleted;
  final int totalJournals;
  final int totalHabits;
  final int totalLectures;
  final Attributes? attributes;

  UserStats({
    required this.streak,
    required this.salahConsistency,
    required this.quranProgress,
    required this.totalQuranSessions,
    required this.totalMissions,
    required this.totalMissionsTaken,
    required this.totalMissionsCompleted,
    required this.totalJournals,
    required this.totalHabits,
    required this.totalLectures,
    this.attributes,
  });

  factory UserStats.fromJson(Map<String, dynamic>? json) {
    if (json == null) {
      return UserStats(
        streak: 0,
        salahConsistency: 0,
        quranProgress: 'Juz 1',
        totalQuranSessions: 0,
        totalMissions: 0,
        totalMissionsTaken: 0,
        totalMissionsCompleted: 0,
        totalJournals: 0,
        totalHabits: 0,
        totalLectures: 0,
      );
    }

    final int completed = ModelParser.parseInt(
        json['total_missions_completed'] ?? json['total_missions'] ?? 0);
    final int taken = ModelParser.parseInt(
        json['total_missions_taken'] ?? json['total_quests'] ?? 0);

    return UserStats(
      streak: ModelParser.parseInt(json['streak']),
      salahConsistency: ModelParser.parseInt(json['salah_consistency']),
      quranProgress: json['quran_progress'] ?? 'Juz 1',
      totalQuranSessions: ModelParser.parseInt(json['total_quran_sessions']),
      totalMissions: completed,
      totalMissionsTaken: taken,
      totalMissionsCompleted: completed,
      totalJournals: ModelParser.parseInt(json['total_journals'] ?? 0),
      totalHabits: ModelParser.parseInt(json['total_habits'] ?? 0),
      totalLectures: ModelParser.parseInt(json['total_lectures'] ?? 0),
      attributes: json['attributes'] != null
          ? Attributes.fromJson(json['attributes'])
          : null,
    );
  }
}

class Attributes {
  final int strength;
  final int intelligence;
  final int wawasan;
  final int wisdom;
  final int vitality;

  Attributes({
    required this.strength,
    required this.intelligence,
    required this.wawasan,
    required this.wisdom,
    required this.vitality,
  });

  factory Attributes.fromJson(Map<String, dynamic> json) {
    return Attributes(
      strength: ModelParser.parseInt(json['sholat']),
      intelligence: ModelParser.parseInt(json['ilmu']),
      wawasan: ModelParser.parseInt(json['wawasan'] ?? 0),
      wisdom: ModelParser.parseInt(json['adab']),
      vitality: ModelParser.parseInt(json['istiqomah']),
    );
  }
}

class UserProfileResponse {
  final bool success;
  final UserProfile data;

  UserProfileResponse({required this.success, required this.data});

  factory UserProfileResponse.fromJson(Map<String, dynamic> json) {
    final data = json['data'];
    if (data == null || data['user'] == null) {
      throw Exception('Invalid profile data structure');
    }
    return UserProfileResponse(
      success: json['success'] ?? false,
      data: UserProfile.fromJson(data['user']),
    );
  }
}
