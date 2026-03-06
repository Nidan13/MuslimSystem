import 'package:audioplayers/audioplayers.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_timezone/flutter_timezone.dart';
import 'package:timezone/data/latest_all.dart' as tz_data;
import 'package:timezone/timezone.dart' as tz;
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/muadzin_service.dart';

class AdzanService {
  final AudioPlayer _audioPlayer = AudioPlayer();
  final FlutterLocalNotificationsPlugin _notificationsPlugin =
      FlutterLocalNotificationsPlugin();

  static const String _channelId = 'adzan_channel_v4'; // Incremented version
  static const String _channelName = 'Adzan Notifications V4';
  static const String _channelDesc =
      'Notifications for prayer times with Adzan sound';

  // Singleton pattern
  static final AdzanService _instance = AdzanService._internal();
  factory AdzanService() => _instance;
  AdzanService._internal();

  Future<void> initialize() async {
    // Initialize timezone
    tz_data.initializeTimeZones();
    try {
      final timezoneInfo = await FlutterTimezone.getLocalTimezone();
      final String timeZoneName = timezoneInfo.identifier;
      tz.setLocalLocation(tz.getLocation(timeZoneName));
      debugPrint("AdzanService - Local Timezone set to: $timeZoneName");
    } catch (e) {
      debugPrint(
          "AdzanService - Could not get local timezone, defaulting to Asia/Jakarta: $e");
      tz.setLocalLocation(tz.getLocation('Asia/Jakarta'));
    }

    // Android initialization
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    // iOS initialization
    const DarwinInitializationSettings initializationSettingsDarwin =
        DarwinInitializationSettings(
      requestSoundPermission: true,
      requestBadgePermission: true,
      requestAlertPermission: true,
    );

    const InitializationSettings initializationSettings =
        InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsDarwin,
    );

    await _notificationsPlugin.initialize(
      settings: initializationSettings,
      onDidReceiveNotificationResponse: (details) {
        stopAdzan();
      },
    );

    // Create channel for Android
    const AndroidNotificationChannel channel = AndroidNotificationChannel(
      _channelId,
      _channelName,
      description: _channelDesc,
      importance: Importance.max,
      playSound: true,
      sound: const RawResourceAndroidNotificationSound('adzan'),
    );

    await _notificationsPlugin
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(channel);

    await _requestPermissions();
  }

  Future<void> _requestPermissions() async {
    final androidImplementation =
        _notificationsPlugin.resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>();

    if (androidImplementation != null) {
      await androidImplementation.requestNotificationsPermission();
      await androidImplementation.requestExactAlarmsPermission();
    }
  }

  // Play Adzan audio from assets based on selection
  Future<void> playAdzan({String prayerName = 'fajr'}) async {
    try {
      debugPrint("Attempting to play adzan audio from assets for $prayerName");

      final prefs = await SharedPreferences.getInstance();
      final selectedId =
          prefs.getInt('adzan_selection_${prayerName.toLowerCase()}');

      final muadzinService = MuadzinService();
      final muadzins = muadzinService.getDefaultMuadzins();

      Muadzin? selectedMuadzin;
      if (selectedId != null) {
        try {
          selectedMuadzin = muadzins.firstWhere((m) => m.id == selectedId);
        } catch (_) {}
      }

      // Default fallback
      selectedMuadzin ??=
          muadzins.firstWhere((m) => m.id == 1, orElse: () => muadzins.first);

      final urlToPlay = prayerName.toLowerCase() == 'fajr'
          ? selectedMuadzin.audioUrlShubuh
          : selectedMuadzin.audioUrlBiasa;

      if (urlToPlay.isEmpty) {
        debugPrint("Adzan set to Senyap / Tidak Ada");
        return;
      }

      await _audioPlayer.setVolume(1.0);

      if (urlToPlay.startsWith('http')) {
        await _audioPlayer.play(UrlSource(urlToPlay));
      } else {
        await _audioPlayer.play(AssetSource(urlToPlay));
      }

      debugPrint("Playback started successfully from $urlToPlay");
    } catch (e) {
      debugPrint("Failed to play adzan: $e");
    }
  }

  Future<void> stopAdzan() async {
    await _audioPlayer.stop();
  }

  Future<void> scheduleAdzan(DateTime scheduledTime, String prayerName) async {
    final now = DateTime.now();

    // If time has passed today, don't schedule
    if (scheduledTime.isBefore(now)) return;

    // Schedule notification with named parameters
    await _notificationsPlugin.zonedSchedule(
      id: prayerName.hashCode,
      title: 'Waktu $prayerName Telah Tiba',
      body: 'Mari laksanakan sholat $prayerName',
      scheduledDate: tz.TZDateTime.from(scheduledTime, tz.local),
      notificationDetails: NotificationDetails(
        android: AndroidNotificationDetails(
          _channelId,
          _channelName,
          channelDescription: _channelDesc,
          importance: Importance.max,
          priority: Priority.high,
          playSound: true,
          sound: const RawResourceAndroidNotificationSound('adzan'),
          ongoing: true,
          autoCancel: false, // Jangan ilang pas diklik, biar user stop manual
          fullScreenIntent: true,
          category: AndroidNotificationCategory.alarm,
          visibility: NotificationVisibility.public,
          actions: [
            const AndroidNotificationAction(
              'stop_adzan',
              'STOP ADZAN',
              showsUserInterface: true,
              cancelNotification: true,
            ),
          ],
        ),
      ),
      androidScheduleMode: AndroidScheduleMode.exactAllowWhileIdle,
      matchDateTimeComponents: DateTimeComponents.time,
    );

    debugPrint("Scheduled Adzan for $prayerName at $scheduledTime");
  }

  // Call this when prayer times are fetched
  Future<void> scheduleAllPrayers(Map<String, DateTime> prayerTimes) async {
    await _notificationsPlugin.cancelAll(); // Clear old schedules

    for (var entry in prayerTimes.entries) {
      await scheduleAdzan(entry.value, entry.key);
    }
  }
}
