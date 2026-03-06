import '../utils/model_parser.dart';
import 'user_profile.dart';
import 'daily_task.dart';
import 'prayer_log.dart';

class HomeData {
  final UserProfile? user;
  final List<DailyTask> dailyTasks;
  final DailyTaskSummary? dailyTaskSummary;
  final PrayerSummary? prayerSummary;
  final int unreadNotificationsCount;
  final int quranCompletedCount;

  HomeData({
    this.user,
    required this.dailyTasks,
    this.dailyTaskSummary,
    this.prayerSummary,
    required this.unreadNotificationsCount,
    required this.quranCompletedCount,
  });

  factory HomeData.fromJson(Map<String, dynamic> json) {
    var tasks = <DailyTask>[];
    if (json['daily_tasks'] != null && json['daily_tasks']['tasks'] != null) {
      if (json['daily_tasks']['tasks'] is List) {
        json['daily_tasks']['tasks'].forEach((v) {
          tasks.add(DailyTask.fromJson(v));
        });
      }
    }

    DailyTaskSummary? summary;
    if (json['daily_tasks'] != null &&
        json['daily_tasks']['summary'] != null &&
        json['daily_tasks']['summary'] is Map) {
      try {
        summary = DailyTaskSummary.fromJson(json['daily_tasks']['summary']);
      } catch (e) {
        print("Error parsing task summary: $e");
      }
    }

    return HomeData(
      user: json['user'] != null ? UserProfile.fromJson(json['user']) : null,
      dailyTasks: tasks,
      dailyTaskSummary: summary,
      prayerSummary: json['prayer_summary'] != null
          ? PrayerSummary.fromJson(json['prayer_summary'])
          : null,
      unreadNotificationsCount:
          ModelParser.parseInt(json['notifications']?['unread_count']),
      quranCompletedCount:
          ModelParser.parseInt(json['quran']?['completed_surah_count']),
    );
  }
}
