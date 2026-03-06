import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:hijri/hijri_calendar.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter/services.dart';

import 'widgets/character_avatar.dart';
import 'widgets/custom_background.dart';

import '../models/user_profile.dart';
import '../models/prayer_times.dart';
import '../models/daily_task.dart';
import '../services/profile_service.dart';
import '../services/daily_task_service.dart';
import '../services/prayer_times_service.dart';
import '../services/location_service.dart';
import '../services/prayer_service.dart';
import '../services/home_service.dart';
import 'quest_screen.dart';
import 'prayer_task_screen.dart';
import 'quran_list_screen.dart';
import 'notification_screen.dart';
import '../services/adzan_service.dart';
import 'qibla_screen.dart';
import 'doa_list_screen.dart';
import 'explore_list_screen.dart';
import 'payment_screen.dart';
import 'zakat_calculator_screen.dart';
import '../services/islamic_content_service.dart';
import '../models/islamic_content.dart';
import 'kajian_detail_screen.dart';
import 'prayer_times_screen.dart';
import 'hadith_screen.dart';
import 'tasbih_screen.dart';
import 'main_screen.dart';

import '../theme/premium_color.dart';

class HomeScreen extends StatefulWidget {
  final bool shouldRefresh;
  const HomeScreen({super.key, this.shouldRefresh = false});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final DailyTaskService _dailyTaskService = DailyTaskService();
  final ProfileService _profileService = ProfileService();
  final PrayerTimesService _prayerTimesService = PrayerTimesService();
  final LocationService _locationService = LocationService();
  final PrayerService _prayerService = PrayerService();
  final AdzanService _adzanService = AdzanService();
  final HomeService _homeService = HomeService();

  List<DailyTask> _tasks = [];
  UserProfile? _userProfile;
  PrayerTimes? _prayerTimes;
  int _unreadNotificationsCount = 0;
  String? _locationName;

  // Expression & Update Logic
  Timer? _expressionTimer;
  Timer? _animationTimer;
  Timer? _minuteTimer; // Timer for real-time countdown updates
  CharacterExpression _currentExpression = CharacterExpression.normal;
  Timer? _neglectCheckTimer;

  @override
  void initState() {
    super.initState();
    _initializeServices();
    // Check prayer neglect every minute
    _neglectCheckTimer = Timer.periodic(const Duration(minutes: 1), (timer) {
      _checkPrayerNeglect();
    });
  }

  @override
  void didUpdateWidget(HomeScreen oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.shouldRefresh && !oldWidget.shouldRefresh) {
      _fetchHomeData(); // Trigger refresh when landing back on this tab
    }
  }

  @override
  void dispose() {
    _expressionTimer?.cancel();
    _animationTimer?.cancel();
    _neglectCheckTimer?.cancel();
    _minuteTimer?.cancel();
    super.dispose();
  }

  Future<void> _initializeServices() async {
    await _adzanService.initialize();

    // 1. Get Prayer Times first to handle Shubuh Reset Logic correctly
    await _fetchPrayerTimes();

    // 2. Then fetch Home Data (passing prayer times context if needed internally)
    await _fetchHomeData();

    // RE-ENABLE CLIENT-SIDE PENALTY CHECK - Adjusted for Shubuh-to-Shubuh cycle
    _checkDailyPenalty();
    // _checkHistoricalPenalty(); // Keep one-time check disabled or handled by backend

    // Initial neglect check
    _checkPrayerNeglect();

    // --- NEW: Start real-time minute timer (Fix for "Menit ga berubah") ---
    _minuteTimer?.cancel();
    _minuteTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) {
        setState(() {
          // Triggering setState will recalculate countdowns in _GlassHeaderCard
        });

        // Cek kalau pas masuk waktu sholat buat auto-play Foreground:
        _checkAndAutoPlayAdzan();
      }
    });
  }

  String? _lastPlayedPrayer;

  void _checkAndAutoPlayAdzan() {
    if (_prayerTimes == null) return;

    final now = DateTime.now();
    final times = {
      'Fajr': _parseTime(_prayerTimes!.fajr),
      'Dhuhr': _parseTime(_prayerTimes!.dhuhr),
      'Asr': _parseTime(_prayerTimes!.asr),
      'Maghrib': _parseTime(_prayerTimes!.maghrib),
      'Isha': _parseTime(_prayerTimes!.isha),
    };

    for (var entry in times.entries) {
      final diff = entry.value.difference(now).inMinutes;
      // Triggers if time is exactly now or within the current minute AND hasn't played yet
      if (diff == 0 && _lastPlayedPrayer != entry.key) {
        _lastPlayedPrayer = entry.key;
        debugPrint("Auto playing Custom Adzan for ${entry.key} in foreground!");

        // Dont block the main thread for playing audio
        Future.microtask(() {
          _adzanService.playAdzan(prayerName: entry.key);
        });
        break;
      }
    }
  }

  Future<void> _fetchHomeData() async {
    try {
      final oldLevel = _userProfile?.level ?? 1;
      final oldHP = _userProfile?.hp.current ?? 0;

      final data = await _homeService.getHomeData();
      if (data != null && mounted) {
        setState(() {
          _userProfile = data.user;
          _tasks = data.dailyTasks;
          _unreadNotificationsCount = data.unreadNotificationsCount;
        });

        // --- NEW: Shubuh Reset Logic ---
        // If it's before Shubuh, we should show YESTERDAY'S tasks so users can mark Isya etc.
        if (_prayerTimes != null) {
          final now = DateTime.now();
          final subuhTime = _parseTime(_prayerTimes!.fajr);

          if (now.isBefore(subuhTime)) {
            debugPrint(
                "Hunter Mode: Masking today's tasks with Yesterday's until Subuh reset.");
            final yesterday = now.subtract(const Duration(days: 1));
            final dateStr = DateFormat('yyyy-MM-dd').format(yesterday);
            final yesterdayTasksResp =
                await _dailyTaskService.getDailyTasks(date: dateStr);
            if (mounted) {
              setState(() {
                _tasks = yesterdayTasksResp.tasks;
              });
            }
          }
        }

        // Check for Level Up
        final newLevel = data.user?.level ?? 1;
        if (newLevel > oldLevel) {
          _triggerHappyExpression();
        }

        // Check for HP Loss (NEW)
        final newHP = data.user?.hp.current ?? 0;
        if (newHP < oldHP && oldHP > 0) {
          _triggerSadExpression();
        }
      }
    } catch (e) {
      debugPrint("Error fetching home data: $e");
    }
  }

  void _triggerHappyExpression() {
    _setExpression(CharacterExpression.happy);
    _expressionTimer?.cancel();
    _expressionTimer = Timer(const Duration(seconds: 10), () {
      _resetExpression();
    });
  }

  void _triggerSadExpression() {
    _setExpression(CharacterExpression.sad);
    _expressionTimer?.cancel();
    _expressionTimer = Timer(const Duration(seconds: 10), () {
      _resetExpression();
    });
  }

  void _setExpression(CharacterExpression exp) {
    if (mounted) {
      setState(() {
        _currentExpression = exp;
      });
    }
  }

  void _resetExpression() {
    _animationTimer?.cancel(); // Stop any ongoing animation
    _setExpression(CharacterExpression.normal);
    // Re-check neglect to see if we should resume anger
    _checkPrayerNeglect();
  }

  void _checkPrayerNeglect() {
    // Jangan nimpa kalau lagi ada timer ekspresi khusus (nangis 10 detik / senang)
    if (_expressionTimer != null && _expressionTimer!.isActive) return;

    if (_prayerTimes == null) return;

    final now = DateTime.now();
    final nextPrayer = _getNextPrayerTime();

    if (nextPrayer != null) {
      final diff = nextPrayer.difference(now);
      // If < 30 mins to next prayer AND current prayer not done (assumed logical check)
      // Since we don't have "current prayer status" easily without mapping time to task...
      // Simplification: logic to check if we are "late" for the CURRENT active prayer interval.

      // Better logic: If we are close to next prayer (e.g. Asr is in 20 mins),
      // check if Dhuhr is completed.

      final currentPrayerName = _getCurrentPrayerName();
      if (currentPrayerName != null &&
          diff.inMinutes <= 30 &&
          diff.inMinutes > 0) {
        final isCompleted = _isPrayerCompleted(currentPrayerName);
        if (!isCompleted) {
          _startAngryAnimation();
          return;
        }
      }
    }

    // If not neglecting, stop animation if running (and not handled by other states)
    if (_animationTimer != null && _animationTimer!.isActive) {
      _animationTimer?.cancel();
      _animationTimer = null;
      if (_currentExpression != CharacterExpression.happy &&
          _currentExpression != CharacterExpression.sad) {
        _setExpression(CharacterExpression.normal);
      }
    }
  }

  void _startAngryAnimation() {
    if (_animationTimer != null && _animationTimer!.isActive) return;

    // Toggle every 5 seconds
    _animationTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      if (_currentExpression == CharacterExpression.happy ||
          _currentExpression == CharacterExpression.sad) return;

      if (_currentExpression == CharacterExpression.angry) {
        _setExpression(CharacterExpression.normal);
      } else {
        _setExpression(CharacterExpression.angry);
      }
    });
  }

  DateTime? _getNextPrayerTime() {
    // text time to DateTime
    if (_prayerTimes == null) return null;
    final now = DateTime.now();
    final times = {
      'Fajr': _parseTime(_prayerTimes!.fajr),
      'Dhuhr': _parseTime(_prayerTimes!.dhuhr),
      'Asr': _parseTime(_prayerTimes!.asr),
      'Maghrib': _parseTime(_prayerTimes!.maghrib),
      'Isha': _parseTime(_prayerTimes!.isha),
    };

    // Sort times
    final sorted = times.entries.toList()
      ..sort((a, b) => a.value.compareTo(b.value));

    for (var entry in sorted) {
      if (entry.value.isAfter(now)) {
        return entry.value;
      }
    }

    // If we are after Isha, the next prayer is Fajr tomorrow
    return sorted.first.value.add(const Duration(days: 1));
  }

  String? _getCurrentPrayerName() {
    if (_prayerTimes == null) return null;
    final now = DateTime.now();
    final times = {
      'Fajr': _parseTime(_prayerTimes!.fajr),
      'Dhuhr': _parseTime(_prayerTimes!.dhuhr),
      'Asr': _parseTime(_prayerTimes!.asr),
      'Maghrib': _parseTime(_prayerTimes!.maghrib),
      'Isha': _parseTime(_prayerTimes!.isha),
    };

    // Find the prayer we are currently IN (after its start time, before next start time)
    // Simple reverse check
    final sorted = times.entries.toList()
      ..sort((a, b) => a.value.compareTo(b.value));

    for (int i = sorted.length - 1; i >= 0; i--) {
      if (now.isAfter(sorted[i].value)) {
        return sorted[i].key;
      }
    }
    return 'Isha'; // Technically if after Isha, we are in Isha time until Fajr (or midnight)
  }

  DateTime _parseTime(String time) {
    try {
      // Regex to safely extract HH:mm from strings like "04:30 (WIB)"
      final match = RegExp(r'(\d{1,2}):(\d{2})').firstMatch(time);
      if (match != null) {
        final now = DateTime.now();
        return DateTime(
          now.year,
          now.month,
          now.day,
          int.parse(match.group(1)!),
          int.parse(match.group(2)!),
        );
      }
    } catch (_) {}
    // Fallback far in the past instead of 'now' to prevent false triggers
    return DateTime(2000, 1, 1);
  }

  bool _isPrayerCompleted(String prayerName) {
    // Check _tasks list for task with name containing prayerName
    // Map names to Indonesian if needed or fuzzy match
    // Prayers in tasks are usually: Subuh, Dzuhur, Ashar, Maghrib, Isya

    String target = "";
    switch (prayerName) {
      case 'Fajr':
        target = 'Subuh';
        break;
      case 'Dhuhr':
        target = 'Dzuhur';
        break; // or Zuhur
      case 'Asr':
        target = 'Ashar';
        break;
      case 'Maghrib':
        target = 'Maghrib';
        break;
      case 'Isha':
        target = 'Isya';
        break;
    }

    final task = _tasks.firstWhere(
      (t) =>
          t.name.contains(target) ||
          t.name.toLowerCase().contains(prayerName.toLowerCase()),
      orElse: () => DailyTask(
        id: -1,
        name: '',
        description: '',
        soulPoints: 0,
        icon: '',
        isCompleted: true,
        isCustom: false,
      ), // Dummy completed
    );

    return task.isCompleted;
  }

  Future<void> _fetchQuranProgress() async {
    // Refresh all home data to ensure stats are up-to-date
    debugPrint("Refreshing Home Data after Quran update...");
    await _fetchHomeData();
  }

  Future<void> _checkHistoricalPenalty() async {
    final prefs = await SharedPreferences.getInstance();
    final hasCheckedHistory =
        prefs.getBool('has_checked_historical_penalty') ?? false;

    if (hasCheckedHistory) return;

    final result = await _profileService.checkHistoricalPenalty();
    if (result != null && result['success'] == true) {
      final data = result['data'];
      if (data == null) return;

      final hpLoss = data['hp_loss'] ?? 0;

      if (hpLoss > 0 && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              "Darah berkurang -$hpLoss HP dari total sholat yang ditinggalkan sejak akun dibuat!",
            ),
            backgroundColor: Colors.black,
            duration: const Duration(seconds: 8),
          ),
        );
        _fetchHomeData(); // Refresh UI to see updated HP
        _triggerSadExpression();
      }

      // Save locally to avoid repeated expensive calculations
      await prefs.setBool('has_checked_historical_penalty', true);
    }
  }

  Future<void> _checkDailyPenalty() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final now = DateTime.now();
      final subuhTime = _parseTime(_prayerTimes!.fajr);

      // Determine "Prayer Day" (Ends at Subuh next day)
      final prayerTodayDate =
          now.isBefore(subuhTime) ? now.subtract(const Duration(days: 1)) : now;

      final todayStr = DateFormat('yyyy-MM-dd').format(prayerTodayDate);
      final lastChecked = prefs.getString('last_penalty_check');

      if (lastChecked == todayStr) {
        debugPrint('Already checked penalty for Prayer Day ($todayStr)');
        return;
      }

      // Check penalty for the PREVIOUS prayer day
      final yesterday = prayerTodayDate.subtract(const Duration(days: 1));
      final dateStr = DateFormat('yyyy-MM-dd').format(yesterday);
      debugPrint('Checking missed prayers for Prayer Day: $dateStr');
      final response = await _dailyTaskService.getDailyTasks(date: dateStr);

      final missedPrayers = response.tasks.where((t) {
        final name = t.name.toLowerCase();
        // Punishment covers all 5 prayers: Subuh to Isya
        final isTargetPrayer = (name.contains('subuh') ||
            name.contains('fajr') ||
            name.contains('dzuhur') ||
            name.contains('dhuhr') ||
            name.contains('ashar') ||
            name.contains('asr') ||
            name.contains('maghrib') ||
            name.contains('isya') ||
            name.contains('isha'));

        return isTargetPrayer && !t.isCompleted;
      }).toList();

      if (missedPrayers.isNotEmpty) {
        final hpLoss = missedPrayers.length * 10;
        final reason =
            "Meninggalkan sholat: ${missedPrayers.map((e) => e.name).join(', ')}";

        debugPrint('!! PENALTY TRIGGERED !! -$hpLoss HP ($reason)');
        await _profileService.applyPenalty(hpLoss: hpLoss, reason: reason);

        // Refresh profile to see new HP
        await _fetchProfile();
        _triggerSadExpression();

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                "Darah berkurang -$hpLoss HP karena meninggalkan sholat kemarin!",
              ),
              backgroundColor: Colors.redAccent,
              duration: const Duration(seconds: 4),
            ),
          );
        }
      } else {
        debugPrint('No missed target prayers yesterday.');
      }

      // Mark as checked for this Prayer Day
      await prefs.setString('last_penalty_check', todayStr);
    } catch (e) {
      debugPrint('Error checking penalty: $e');
    }
  }

  Future<void> _fetchPrayerTimes() async {
    try {
      // 1. Initial State: Load from cache for instant display
      final cached = await _prayerTimesService.getCachedPrayerTimes();
      final cachedAddress = await _locationService.getCachedAddress();

      if (mounted) {
        setState(() {
          if (cached != null) _prayerTimes = cached;
          if (cachedAddress != null) _locationName = cachedAddress;
        });
      }

      // 2. Real-time update: Get location first
      final position = await _locationService.getLocationOrDefault();

      // 3. Parallel fetch: Address and Prayer Times
      final results = await Future.wait([
        _locationService.getAddressFromLocation(
          position.latitude,
          position.longitude,
        ),
        _prayerTimesService.getPrayerTimes(
          latitude: position.latitude,
          longitude: position.longitude,
        ),
      ]);

      final String address = results[0] as String;
      final PrayerTimes times = results[1] as PrayerTimes;

      if (mounted) {
        setState(() {
          _prayerTimes = times;
          _locationName = address;
        });
      }

      _scheduleAdzan(times);
      _syncSchedulesToBackend(times);
    } catch (e) {
      debugPrint('Failed to load prayer times: $e');
    }
  }

  Future<void> _syncSchedulesToBackend(PrayerTimes times) async {
    try {
      await _prayerService.syncSchedule(DateTime.now(), {
        'subuh': times.fajr,
        'dzuhur': times.dhuhr,
        'ashar': times.asr,
        'maghrib': times.maghrib,
        'isya': times.isha,
      });
      debugPrint("Prayer schedules synced to backend successfully.");
    } catch (e) {
      debugPrint("Failed to sync prayer schedules: $e");
    }
  }

  void _scheduleAdzan(PrayerTimes times) {
    debugPrint("Scheduling Adzan for fetched times...");

    DateTime parse(String time) {
      try {
        final match = RegExp(r'(\d{1,2}):(\d{2})').firstMatch(time);
        if (match != null) {
          final now = DateTime.now();
          return DateTime(
            now.year,
            now.month,
            now.day,
            int.parse(match.group(1)!),
            int.parse(match.group(2)!),
          );
        }
      } catch (_) {}
      return DateTime(2000, 1, 1);
    }

    _adzanService.scheduleAllPrayers({
      'Fajr': parse(times.fajr),
      'Dhuhr': parse(times.dhuhr),
      'Asr': parse(times.asr),
      'Maghrib': parse(times.maghrib),
      'Isha': parse(times.isha),
    });
  }

  Future<void> _fetchProfile() async {
    try {
      final profile = await _profileService.getProfile();
      if (mounted) {
        setState(() {
          _userProfile = profile;
        });
      }
    } catch (e) {
      debugPrint('Failed to load profile for home: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // 1. Unified Background Header (Background Layer)
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: 320, // Reduced from 350 to fix "kelebaran"
            child: ClipPath(
              clipper: DomeClipper(),
              child: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [PremiumColor.primary, PremiumColor.accent],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
              ),
            ),
          ),

          // 2. Main Content (Interactive Layer)
          SafeArea(
            bottom: false,
            child: RefreshIndicator(
              onRefresh: _fetchHomeData,
              color: PremiumColor.primary,
              child: CustomScrollView(
                physics: const BouncingScrollPhysics(),
                slivers: [
                  // 2.1 Interactive Header Section
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 24, vertical: 8),
                      child: Column(
                        children: [
                          _IntegratedHeader(
                            userProfile: _userProfile,
                            prayerTimes: _prayerTimes,
                            unreadNotificationsCount: _unreadNotificationsCount,
                            expression: _currentExpression,
                            onNotificationTap: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) =>
                                    const NotificationScreen(),
                              ),
                            ),
                            onProfileTap: () {
                              final mainState = context
                                  .findAncestorStateOfType<MainScreenState>();
                              if (mainState != null) {
                                mainState
                                    .onItemTapped(4); // Switch to Profile Tab
                              }
                            },
                            translateHijri: _translateHijriMonth,
                          ),
                          const SizedBox(height: 24),
                          GestureDetector(
                            onTap: () {
                              HapticFeedback.mediumImpact();
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => const PrayerTimesScreen(),
                                ),
                              );
                            },
                            child: Container(
                              color: Colors
                                  .transparent, // Ensures area is clickable
                              child: _buildMainPrayerStatus(),
                            ),
                          ),
                          const SizedBox(height: 24),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 12),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.search_rounded,
                                    color: Colors.white70, size: 20),
                                const SizedBox(width: 12),
                                Text(
                                  "Cari doa, berita, kajian...",
                                  style: GoogleFonts.manrope(
                                    color: Colors.white70,
                                    fontSize: 13,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 48), // Dome curves allowance
                        ],
                      ),
                    ),
                  ),

                  // 2.2 Main White Content Area
                  SliverToBoxAdapter(
                    child: Container(
                      color: Colors.white,
                      child: Column(
                        children: [
                          if (_userProfile != null && !_userProfile!.isActive)
                            _buildInactiveBanner(),
                          const SizedBox(height: 32),
                          _MainMenuGrid(
                              onQuranProgressUpdate: _fetchQuranProgress),
                          const SizedBox(height: 32),
                          const _NewsCarousel(),
                          const SizedBox(height: 32),
                          const _IslamicVideoNews(),
                          const SizedBox(height: 32),
                          // 3. Target Ibadah (Tasks) - Moved Down
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 24),
                            child: _SectionHeader(
                              title: "Target Ibadah",
                              onTap: () => Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (_) =>
                                          const PrayerTaskScreen())),
                            ),
                          ),
                          const SizedBox(height: 12),
                          _buildTaskList(),
                          const SizedBox(height: 100),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMainPrayerStatus() {
    final next = _prayerTimes?.getNextPrayer();
    final name = _translatePrayerName(next?['name'] ?? 'Subuh');
    final time = next?['time'] ?? '04:30';
    final remaining = _prayerTimes?.getTimeUntilNext();
    final remainingStr =
        remaining != null ? _formatCountdown(remaining) : "00:00:00";

    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.location_on_rounded,
                  color: Colors.white70, size: 14),
              const SizedBox(width: 4),
              Flexible(
                child: Text(
                  _locationName ?? "Mendeteksi Lokasi...",
                  style: GoogleFonts.manrope(
                      color: Colors.white70,
                      fontSize: 11,
                      fontWeight: FontWeight.w600),
                  overflow: TextOverflow.ellipsis,
                  maxLines: 1,
                ),
              ),
              const SizedBox(width: 12),
              const Icon(Icons.calendar_today_rounded,
                  color: Colors.white70, size: 12),
              const SizedBox(width: 4),
              Text(
                _getHijriDate(),
                style: GoogleFonts.manrope(
                    color: Colors.white70,
                    fontSize: 11,
                    fontWeight: FontWeight.w600),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),
        Text(
          "$name $time WIB",
          style: GoogleFonts.manrope(
            fontSize: 28, // Reduced from 36 to prevent overflow
            fontWeight: FontWeight.w900,
            color: Colors.white,
            letterSpacing: -0.5,
          ),
        ),
        Text(
          remainingStr,
          style: GoogleFonts.manrope(
            fontSize: 13,
            fontWeight: FontWeight.w700,
            color: Colors.white.withOpacity(0.8),
            letterSpacing: 1,
          ),
        ),
      ],
    );
  }

  String _translatePrayerName(String name) {
    if (name == 'Fajr') return 'Subuh';
    if (name == 'Isha') return "Isya'";
    return name;
  }

  String _formatCountdown(Duration d) {
    if (d.isNegative) return "- 00:00:00";
    String hours = d.inHours.toString().padLeft(2, '0');
    String minutes = (d.inMinutes % 60).toString().padLeft(2, '0');
    String seconds = (d.inSeconds % 60).toString().padLeft(2, '0');
    return "- $hours:$minutes:$seconds";
  }

  String _getHijriDate() {
    try {
      HijriCalendar.setLocal('en');
    } catch (_) {}
    final nowHijri = HijriCalendar.now();
    return "${nowHijri.hDay} ${_translateHijriMonth(nowHijri.longMonthName)} ${nowHijri.hYear} H";
  }

  String _translateHijriMonth(String monthEn) {
    final Map<String, String> months = {
      'Muharram': 'Muharram',
      'Safar': 'Safar',
      'Rabi\' al-awwal': 'Rabiul Awal',
      'Rabi\' ath-thani': 'Rabiul Akhir',
      'Jumada al-ula': 'Jumadil Ula',
      'Jumada al-akhira': 'Jumadil Akhir',
      'Rajab': 'Rajab',
      'Sha\'ban': 'Sya\'ban',
      'Ramadan': 'Ramadhan',
      'Shawwal': 'Syawal',
      'Dhu al-Qi\'dah': 'Dzulqa\'dah',
      'Dhu al-Hijjah': 'Dzulhijjah',
      'Rabi\' al-Awwal': 'Rabiul Awal',
      'Rabi\' al-Thani': 'Rabiul Akhir',
      'Jumada al-Ula': 'Jumadil Ula',
      'Jumada al-Akhira': 'Jumadil Akhir',
      'Shaban': 'Sya\'ban',
      'Dhul-Qi\'dah': 'Dzulqa\'dah',
      'Dhul-Hijjah': 'Dzulhijjah',
    };

    return months[monthEn] ?? monthEn;
  }

  Widget _buildInactiveBanner() {
    return Container(
      margin: const EdgeInsets.only(bottom: 24),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.redAccent.withOpacity(0.9),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.redAccent.withOpacity(0.3),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          const Icon(
            Icons.warning_amber_rounded,
            color: Colors.white,
            size: 28,
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Akun Belum Aktif Wok!",
                  style: GoogleFonts.manrope(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 14,
                  ),
                ),
                Text(
                  "Bayar infaq seikhlasnya biar bisa lanjut.",
                  style: GoogleFonts.manrope(
                    color: Colors.white.withOpacity(0.9),
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const PaymentScreen()),
              ).then((_) => _fetchHomeData());
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: Colors.redAccent,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: Text(
              "BAYAR",
              style: GoogleFonts.manrope(
                fontWeight: FontWeight.w900,
                fontSize: 12,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTaskList() {
    if (_tasks.isEmpty) return const SizedBox.shrink();
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: _tasks.length > 5 ? 5 : _tasks.length,
      itemBuilder: (context, index) {
        final task = _tasks[index];
        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: const Color(0xFFF8FAFC),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: const Color(0xFFF1F5F9)),
          ),
          child: Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: task.isCompleted
                      ? const Color(0xFFF0FDFA)
                      : const Color(0xFFF1F5F9),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  task.isCompleted
                      ? Icons.check_circle_rounded
                      : Icons.radio_button_unchecked_rounded,
                  color: task.isCompleted
                      ? const Color(0xFF0D9488)
                      : const Color(0xFF94A3B8),
                  size: 24,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      task.name,
                      style: GoogleFonts.manrope(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: const Color(0xFF1E293B),
                        decoration: task.isCompleted
                            ? TextDecoration.lineThrough
                            : null,
                      ),
                    ),
                    Text(
                      task.description ?? '',
                      style: GoogleFonts.manrope(
                        fontSize: 11,
                        color: const Color(0xFF64748B),
                      ),
                    ),
                  ],
                ),
              ),
              Text(
                "+${task.soulPoints}",
                style: GoogleFonts.manrope(
                  fontSize: 13,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF0D9488),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

class _IntegratedHeader extends StatelessWidget {
  final UserProfile? userProfile;
  final PrayerTimes? prayerTimes;
  final int unreadNotificationsCount;
  final CharacterExpression expression;
  final VoidCallback? onNotificationTap;
  final VoidCallback? onProfileTap;
  final String Function(String) translateHijri;

  const _IntegratedHeader({
    this.userProfile,
    this.prayerTimes,
    required this.unreadNotificationsCount,
    this.expression = CharacterExpression.normal,
    this.onNotificationTap,
    this.onProfileTap,
    required this.translateHijri,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        GestureDetector(
          onTap: onProfileTap,
          child: RubElHizbAvatar(
            imageUrl: userProfile?.avatar,
            level: (userProfile?.level ?? 1).toString(),
            hpProgress: (userProfile?.hp.progress ?? 100).toDouble() / 100.0,
            size: 60,
            gender: userProfile?.gender ?? 'male',
            expression: expression,
            isCircle: true,
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: GestureDetector(
            onTap: onProfileTap,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  "Assalamu'alaikum,",
                  style: GoogleFonts.manrope(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: Colors.white.withOpacity(0.8),
                  ),
                ),
                Text(
                  userProfile?.username ?? "Hunter",
                  style: GoogleFonts.manrope(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                    height: 1.1,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ),
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            _CircleTopButton(
              icon: Icons.explore_rounded,
              onTap: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const QiblaScreen())),
            ),
            const SizedBox(width: 12),
            _CircleTopButton(
              icon: Icons.notifications_none_rounded,
              onTap: onNotificationTap,
              badgeCount: unreadNotificationsCount,
            ),
          ],
        ),
      ],
    );
  }
}

class _CircleTopButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback? onTap;
  final int badgeCount;

  const _CircleTopButton({required this.icon, this.onTap, this.badgeCount = 0});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.15),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: Colors.white, size: 22),
          ),
          if (badgeCount > 0)
            Positioned(
              top: -2,
              right: -2,
              child: Container(
                padding: const EdgeInsets.all(4),
                decoration: const BoxDecoration(
                    color: Colors.redAccent, shape: BoxShape.circle),
                constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                child: Text(
                  badgeCount.toString(),
                  style: const TextStyle(
                      color: Colors.white,
                      fontSize: 8,
                      fontWeight: FontWeight.bold),
                  textAlign: TextAlign.center,
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class _MainMenuGrid extends StatelessWidget {
  final VoidCallback? onQuranProgressUpdate;
  const _MainMenuGrid({this.onQuranProgressUpdate});

  @override
  Widget build(BuildContext context) {
    final services = [
      {
        'title': 'Al-Qur\'an',
        'image': 'assets/images/qur\'an.jpeg',
        'color': const Color(0xFFF0FDFA),
        'onTap': () => Navigator.push(
            context, MaterialPageRoute(builder: (_) => const QuranListScreen()))
      },
      {
        'title': 'Shalat',
        'image': 'assets/images/sholat.jpeg',
        'color': const Color(0xFFECFDF5),
        'onTap': () => Navigator.push(context,
            MaterialPageRoute(builder: (_) => const PrayerTaskScreen()))
      },
      {
        'title': 'Kiblat',
        'image': 'assets/images/kiblah.jpeg',
        'color': const Color(0xFFEEF2FF),
        'onTap': () => Navigator.push(
            context, MaterialPageRoute(builder: (_) => const QiblaScreen()))
      },
      {
        'title': 'Tasbih',
        'image': 'assets/images/tasbih.jpeg',
        'color': const Color(0xFFFFFBEB),
        'onTap': () => Navigator.push(
            context, MaterialPageRoute(builder: (_) => const TasbihScreen()))
      },
      {
        'title': 'Doa',
        'image': 'assets/images/doa.jpeg',
        'color': const Color(0xFFF0F9FF),
        'onTap': () => Navigator.push(
            context, MaterialPageRoute(builder: (_) => const DoaListScreen()))
      },
      {
        'title': 'Zakat',
        'image': 'assets/images/zakat.jpeg',
        'color': const Color(0xFFFFF1F2),
        'onTap': () => Navigator.push(context,
            MaterialPageRoute(builder: (_) => const ZakatCalculatorScreen()))
      },
      {
        'title': 'Hadits',
        'icon': Icons.auto_stories_rounded,
        'color': const Color(0xFFF5F3FF),
        'textColor': const Color(0xFF7C3AED),
        'onTap': () => Navigator.push(
            context, MaterialPageRoute(builder: (_) => const HadithScreen()))
      },
      {
        'title': 'Lainnya',
        'icon': Icons.grid_view_rounded,
        'color': const Color(0xFFF1F5F9),
        'textColor': const Color(0xFF475569),
        'onTap': () => Navigator.push(
            context, MaterialPageRoute(builder: (_) => const QuestScreen()))
      },
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: GridView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 4,
          mainAxisSpacing: 24,
          crossAxisSpacing: 12,
          childAspectRatio: 0.75,
        ),
        itemCount: services.length,
        itemBuilder: (context, index) {
          final s = services[index];
          return _MainMenuIcon(
            title: s['title'] as String,
            image: s['image'] as String?,
            icon: s['icon'] as IconData?,
            color: (s['color'] as Color?) ?? const Color(0xFFF8FAFC),
            iconColor: (s['textColor'] as Color?) ?? const Color(0xFF64748B),
            onTap: s['onTap'] as VoidCallback,
          );
        },
      ),
    );
  }
}

class _MainMenuIcon extends StatelessWidget {
  final String title;
  final IconData? icon;
  final String? image;
  final Color color;
  final Color iconColor;
  final VoidCallback onTap;

  const _MainMenuIcon({
    required this.title,
    this.icon,
    this.image,
    this.color = Colors.white,
    this.iconColor = Colors.grey,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        onTap();
      },
      child: Column(
        children: [
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(
              color: color,
              shape: BoxShape.circle,
            ),
            alignment: Alignment.center,
            child: image != null
                ? Image.asset(
                    image!,
                    width: 44,
                    height: 44,
                    fit: BoxFit.contain,
                  )
                : Icon(icon, color: iconColor, size: 28),
          ),
          const SizedBox(height: 8),
          Text(
            title,
            textAlign: TextAlign.center,
            style: GoogleFonts.manrope(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF334155),
              height: 1.1,
            ),
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }
}

class DomeClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    final path = Path();
    path.lineTo(0, size.height - 40); // Convex bottom
    path.quadraticBezierTo(
        size.width * 0.5, size.height, size.width, size.height - 40);
    path.lineTo(size.width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}

// _Badge removed as it is no longer used.

// -----------------------------------------------------------------------------
// Islamic Exploration Section
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// News & Insight Banner Carousel (Tokopedia Style)
// -----------------------------------------------------------------------------
class _NewsCarousel extends StatefulWidget {
  const _NewsCarousel();

  @override
  State<_NewsCarousel> createState() => _NewsCarouselState();
}

class _NewsCarouselState extends State<_NewsCarousel> {
  final PageController _pageController = PageController(viewportFraction: 0.9);
  int _currentPage = 0;
  Timer? _timer;

  final List<Map<String, String>> _banners = [
    {
      'title': 'Kisah Nabi Musa AS',
      'subtitle': 'Keteguhan Hati Menghadapi Firaun',
      'tag': 'KISAH NABI',
      'image': 'https://illustrations.popsy.co/teal/white-collared-shirt.svg',
    },
    {
      'title': 'Keutamaan Sedekah',
      'subtitle': 'Sedekah Tidak Mengurangi Harta',
      'tag': 'FIQIH',
      'image': 'https://illustrations.popsy.co/teal/energy-drink.svg',
    },
    {
      'title': 'Kajian Terbaru',
      'subtitle': 'Perdalam Ilmu Agama lewat Video',
      'tag': 'KAJIAN',
      'image': 'https://illustrations.popsy.co/teal/academic-studies.svg',
    },
  ];

  @override
  void initState() {
    super.initState();
    _timer = Timer.periodic(const Duration(seconds: 5), (timer) {
      if (_pageController.hasClients) {
        int next = (_currentPage + 1) % _banners.length;
        _pageController.animateToPage(
          next,
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeInOutQuart,
        );
      }
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const _SectionHeader(
                title: "Wawasan Islami",
              ),
              Row(
                children: List.generate(_banners.length, (index) {
                  return AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    margin: const EdgeInsets.only(left: 4),
                    width: _currentPage == index ? 16 : 6,
                    height: 6,
                    decoration: BoxDecoration(
                      color: _currentPage == index
                          ? PremiumColor.primary
                          : PremiumColor.primary.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(3),
                    ),
                  );
                }),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),
        const SizedBox(height: 12),
        SizedBox(
          height: 110,
          child: PageView.builder(
            controller: _pageController,
            padEnds: false,
            onPageChanged: (idx) => setState(() => _currentPage = idx),
            itemCount: _banners.length,
            itemBuilder: (context, index) {
              final b = _banners[index];
              return Padding(
                padding: const EdgeInsets.only(left: 24),
                child: _BannerItem(
                  title: b['title']!,
                  subtitle: b['subtitle']!,
                  tag: b['tag']!,
                  imageUrl: b['image']!,
                  color: index % 2 == 0
                      ? PremiumColor.primary
                      : const Color(0xFF0369A1),
                  onTap: () {
                    String category = 'prophet';
                    if (b['tag'] == 'FIQIH') category = 'fiqih';
                    if (b['tag'] == 'KAJIAN') category = 'kajian';

                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => ExploreListScreen(
                          category: category,
                          title: b['tag']!,
                        ),
                      ),
                    );
                  },
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}

class _BannerItem extends StatelessWidget {
  final String title;
  final String subtitle;
  final String tag;
  final String? imageUrl;
  final Color color;
  final VoidCallback onTap;

  const _BannerItem({
    required this.title,
    required this.subtitle,
    required this.tag,
    this.imageUrl,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: color,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.15),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Stack(
          children: [
            // Pattern Overlay
            Positioned(
              right: -20,
              top: -10,
              bottom: -10,
              child: Opacity(
                opacity: 0.15,
                child: Icon(Icons.mosque, size: 140, color: Colors.white),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            tag,
                            style: GoogleFonts.manrope(
                              color: Colors.white,
                              fontSize: 9,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          title,
                          style: GoogleFonts.manrope(
                            color: Colors.white,
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            height: 1.2,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 2),
                        Text(
                          subtitle,
                          style: GoogleFonts.manrope(
                            color: Colors.white.withOpacity(0.8),
                            fontSize: 11,
                            fontWeight: FontWeight.w500,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  if (imageUrl != null) const SizedBox(width: 12),
                  if (imageUrl != null)
                    ClipRRect(
                      borderRadius: BorderRadius.circular(12),
                      child: Image.network(
                        imageUrl!,
                        width: 70,
                        height: 70,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => Container(
                            width: 70, height: 70, color: Colors.white10),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// -----------------------------------------------------------------------------
// Islamic Video News Section
// -----------------------------------------------------------------------------

class _IslamicVideoNews extends StatelessWidget {
  const _IslamicVideoNews();

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: _SectionHeader(
            title: "Kajian Trending",
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const ExploreListScreen(
                    category: 'kajian',
                    title: 'Kajian Trending',
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 12),
        FutureBuilder<List<Map<String, dynamic>>>(
          future: IslamicContentService().getIslamicVideos(),
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return SizedBox(
                height: 110,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  itemCount: 3,
                  itemBuilder: (context, index) => Container(
                    width: 280,
                    margin: const EdgeInsets.only(right: 16),
                    decoration: BoxDecoration(
                      color: PremiumColor.primary.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                ),
              );
            }
            if (!snapshot.hasData || snapshot.data!.isEmpty)
              return const SizedBox();

            final videos = snapshot.data!;
            return SizedBox(
              height: 110,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                physics: const BouncingScrollPhysics(),
                itemCount: videos.length,
                padding: const EdgeInsets.symmetric(horizontal: 24),
                itemBuilder: (context, index) {
                  final video = videos[index];
                  return GestureDetector(
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => KajianDetailScreen(
                            content: IslamicContent(
                              id: video['videoId'].toString(),
                              category: 'kajian',
                              title: video['title'].toString(),
                              subtitle: video['channel'].toString(),
                              description:
                                  'Saksikan video kajian ini di YouTube.',
                              imageUrl: video['thumbnailUrl']?.toString() ??
                                  "https://i.ytimg.com/vi/${video['videoId']}/hqdefault.jpg",
                              metadata: {
                                'videoId': video['videoId'],
                                'dbId': video['id'],
                              },
                            ),
                          ),
                        ),
                      );
                    },
                    child: Container(
                      width: 280,
                      margin: const EdgeInsets.only(right: 16),
                      child: Material(
                        color: const Color(0xFF0369A1).withOpacity(0.05),
                        borderRadius: BorderRadius.circular(16),
                        clipBehavior: Clip.antiAlias,
                        child: Stack(
                          children: [
                            // Subtle Decor
                            Positioned(
                              right: -10,
                              bottom: -20,
                              child: Opacity(
                                opacity: 0.05,
                                child: Icon(Icons.play_circle_fill_rounded,
                                    size: 100, color: const Color(0xFF0369A1)),
                              ),
                            ),
                            Padding(
                              padding: const EdgeInsets.all(12.0),
                              child: Row(
                                children: [
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                              horizontal: 6, vertical: 2),
                                          decoration: BoxDecoration(
                                            color: const Color(0xFF0369A1)
                                                .withOpacity(0.1),
                                            borderRadius:
                                                BorderRadius.circular(4),
                                          ),
                                          child: Text(
                                            "Video Kajian",
                                            style: GoogleFonts.manrope(
                                              color: const Color(0xFF0369A1),
                                              fontSize: 9,
                                              fontWeight: FontWeight.w800,
                                            ),
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        Text(
                                          video['title'].toString(),
                                          style: GoogleFonts.manrope(
                                            fontSize: 14,
                                            fontWeight: FontWeight.w800,
                                            color: const Color(0xFF1E293B),
                                            height: 1.2,
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          video['channel'].toString(),
                                          style: GoogleFonts.manrope(
                                            fontSize: 10,
                                            fontWeight: FontWeight.w600,
                                            color: const Color(0xFF64748B),
                                          ),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Stack(
                                    alignment: Alignment.center,
                                    children: [
                                      ClipRRect(
                                        borderRadius: BorderRadius.circular(10),
                                        child: Image.network(
                                          video['thumbnailUrl']?.toString() ??
                                              "https://i.ytimg.com/vi/${video['videoId']}/hqdefault.jpg",
                                          width: 80,
                                          height: 80,
                                          fit: BoxFit.cover,
                                        ),
                                      ),
                                      Container(
                                        padding: const EdgeInsets.all(4),
                                        decoration: const BoxDecoration(
                                          color: Colors.black38,
                                          shape: BoxShape.circle,
                                        ),
                                        child: const Icon(
                                            Icons.play_arrow_rounded,
                                            color: Colors.white,
                                            size: 16),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  );
                },
              ),
            );
          },
        ),
      ],
    );
  }
}

// _HorizontalVideoCard removed as it's now inlined with dynamic type support

class _SectionHeader extends StatelessWidget {
  final String title;
  final VoidCallback? onTap;

  const _SectionHeader({required this.title, this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.fromLTRB(
            16, 8, 16, 8), // Matching ml-4 and pl-2 feel
        decoration: const BoxDecoration(
          border: Border(
            left: BorderSide(
              color: PremiumColor.primary,
              width: 4,
            ),
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              title,
              style: GoogleFonts.manrope(
                fontSize: 15,
                fontWeight: FontWeight.w800,
                color: const Color(0xFF1E293B),
              ),
            ),
            if (onTap != null)
              Row(
                children: [
                  Text(
                    "Lihat Semua",
                    style: GoogleFonts.manrope(
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                      color: PremiumColor.primary,
                    ),
                  ),
                  const SizedBox(width: 2),
                  const Icon(
                    Icons.chevron_right_rounded,
                    size: 14,
                    color: PremiumColor.primary,
                  ),
                ],
              ),
          ],
        ),
      ),
    );
  }
}
