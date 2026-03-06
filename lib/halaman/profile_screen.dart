import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:flutter/services.dart';
import 'dart:ui';
import '../models/activity_log.dart';
import 'package:share_plus/share_plus.dart';
import '../models/user_profile.dart';
import '../models/daily_task.dart';
import '../services/profile_service.dart';

import 'dart:async';
import '../models/prayer_times.dart';
import '../services/prayer_times_service.dart';
import '../services/location_service.dart';
import 'settings_screen.dart';

import '../services/prayer_service.dart';
import '../services/daily_task_service.dart';
import 'widgets/custom_background.dart';
import 'widgets/radar_chart.dart';
import 'ranking_screen.dart';
import 'widgets/character_avatar.dart';
import 'hunter_journey_screen.dart';
import 'search_friend_screen.dart';

import 'withdrawal_screen.dart';
import 'commission_screen.dart';
import 'social/follow_list_screen.dart';

class ProfileScreen extends StatefulWidget {
  final bool shouldRefresh;
  const ProfileScreen({super.key, this.shouldRefresh = false});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen>
    with SingleTickerProviderStateMixin {
  final ProfileService _profileService = ProfileService();
  final PrayerService _prayerService = PrayerService();
  final DailyTaskService _dailyTaskService = DailyTaskService();
  final PrayerTimesService _prayerTimesService = PrayerTimesService();
  final LocationService _locationService = LocationService();

  late TabController _tabController;
  UserProfile? _userProfile;
  List<ActivityLog> _activities = [];
  bool _isLoading = true;
  String? _errorMessage;

  // Real-time aggregates for Radar Chart
  List<DailyTask> _tasks = []; // Added for negect check

  // Dynamic Expression State
  CharacterExpression _currentExpression = CharacterExpression.normal;
  Timer? _expressionTimer;
  Timer? _animationTimer;
  Timer? _neglectCheckTimer;
  PrayerTimes? _prayerTimes;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _fetchData();
    _fetchPrayerTimes();

    // Check character expression every minute
    _neglectCheckTimer = Timer.periodic(const Duration(minutes: 1), (timer) {
      _updateCharacterExpression();
    });

    // Initial check
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _updateCharacterExpression();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _expressionTimer?.cancel();
    _animationTimer?.cancel();
    _neglectCheckTimer?.cancel();
    super.dispose();
  }

  @override
  void didUpdateWidget(ProfileScreen oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.shouldRefresh && !oldWidget.shouldRefresh) {
      _fetchData();
    }
  }

  Future<void> _fetchData() async {
    await Future.wait([
      _fetchProfile(),
      _fetchActivities(),
      _fetchPrayerAndQuestStats(),
    ]);
    if (mounted) {
      setState(() => _isLoading = false);
      _updateCharacterExpression();
    }
  }

  void _showComingSoon() {
    showDialog(
      context: context,
      barrierDismissible: true,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: 40),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(32),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.1),
                blurRadius: 20,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: PremiumColor.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.construction_rounded,
                  color: PremiumColor.primary,
                  size: 40,
                ),
              ),
              const SizedBox(height: 20),
              Text(
                "Coming Soon",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                  color: PremiumColor.slate800,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                "Fitur ini sedang dalam tahap pengembangan. Nantikan di update selanjutnya!",
                textAlign: TextAlign.center,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14,
                  color: PremiumColor.slate600,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(context),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PremiumColor.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    elevation: 0,
                  ),
                  child: Text(
                    "Siap, Tunggu!",
                    style: GoogleFonts.plusJakartaSans(
                      fontWeight: FontWeight.w800,
                      fontSize: 15,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _fetchPrayerAndQuestStats() async {
    try {
      final now = DateTime.now();
      String? targetDate;

      // If we have prayer times cached or loaded, handle Subuh reset logic
      if (_prayerTimes != null) {
        final subuhTime = _parseTime(_prayerTimes!.fajr);
        if (now.isBefore(subuhTime)) {
          targetDate = DateFormat('yyyy-MM-dd')
              .format(now.subtract(const Duration(days: 1)));
        }
      }

      final results = await Future.wait([
        _prayerService.getPrayerLogs(
            date: targetDate != null ? DateTime.parse(targetDate) : null),
        _dailyTaskService.getDailyTasks(date: targetDate),
      ]);

      if (mounted) {
        final DailyTaskResponse taskResp = results[1] as DailyTaskResponse;

        setState(() {
          _tasks = taskResp.tasks;
        });
      }
    } catch (e) {
      debugPrint("Error fetching extra stats: $e");
    }
  }

  Future<void> _fetchProfile() async {
    try {
      final oldHP = _userProfile?.hp.current ?? 0;
      final profile = await _profileService.getProfile();
      if (mounted) {
        setState(() {
          _userProfile = profile;
          if (profile == null) {
            _errorMessage = "Gagal memuat profil Hunter lo wok!";
          }
        });

        // Trigger sad expression if HP decreased
        if (profile != null && profile.hp.current < oldHP && oldHP > 0) {
          _triggerSadExpression();
        } else {
          // Hanya update ekspresi jika tidak sedang dalam durasi ekspresi khusus (nangis/senang)
          if (_expressionTimer == null || !_expressionTimer!.isActive) {
            _updateCharacterExpression();
          }
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = e.toString();
        });
      }
    }
  }

  void _triggerSadExpression() {
    _setExpression(CharacterExpression.sad);
    _expressionTimer?.cancel();
    _expressionTimer = Timer(const Duration(seconds: 10), () {
      _resetExpression();
    });
  }

  void _resetExpression() {
    _animationTimer?.cancel();
    _setExpression(CharacterExpression.normal);
    _updateCharacterExpression();
  }

  Future<void> _fetchActivities() async {
    try {
      final logs = await _profileService.getActivities();
      if (mounted) {
        setState(() {
          _activities = logs;
        });
      }
    } catch (e) {
      debugPrint("Error fetching activities: $e");
    }
  }

  // --- Dynamic Expression Logic ---

  Future<void> _fetchPrayerTimes() async {
    try {
      // 1. Try to load from cache first
      final cached = await _prayerTimesService.getCachedPrayerTimes();
      if (cached != null && mounted) {
        setState(() => _prayerTimes = cached);
      }

      // 2. Fetch real-time
      final position = await _locationService.getLocationOrDefault();
      final times = await _prayerTimesService.getPrayerTimes(
        latitude: position.latitude,
        longitude: position.longitude,
      );

      if (mounted) {
        setState(() => _prayerTimes = times);
      }
    } catch (e) {
      debugPrint('Failed to load prayer times for profile: $e');
    }
  }

  void _updateCharacterExpression() {
    if (_userProfile == null) return;

    // Jangan nimpa kalau lagi ada timer ekspresi khusus (nangis/senang)
    if (_expressionTimer != null && _expressionTimer!.isActive) return;

    final hpProgress = _userProfile!.hp.progress;

    // 1. Check Prayer Neglect (Angry)
    if (_isCurrentlyNeglecting()) {
      _startAngryAnimation();
      return;
    }

    // 2. Low HP Condition (Sad)
    if (hpProgress < 30) {
      _setExpression(CharacterExpression.sad);
      return;
    }

    // 3. High HP Condition (Happy)
    if (hpProgress > 80) {
      _setExpression(CharacterExpression.happy);
      return;
    }

    // 4. Default Condition
    _setExpression(CharacterExpression.normal);
  }

  void _startAngryAnimation() {
    if (_animationTimer != null && _animationTimer!.isActive) return;

    // Toggle every 5 seconds to simulate breathing/reactive state
    _animationTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      if (_currentExpression == CharacterExpression.happy ||
          _currentExpression == CharacterExpression.sad) {
        timer.cancel();
        _animationTimer = null;
        return;
      }

      if (_currentExpression == CharacterExpression.angry) {
        _setExpression(CharacterExpression.normal);
      } else {
        _setExpression(CharacterExpression.angry);
      }
    });
  }

  bool _isCurrentlyNeglecting() {
    if (_prayerTimes == null) return false;
    final now = DateTime.now();
    final nextTime = _getNextPrayerTime();
    if (nextTime == null) return false;

    final diff = nextTime.difference(now);
    final currentPrayer = _getCurrentPrayerName();

    return currentPrayer != null &&
        diff.inMinutes <= 30 &&
        !_isPrayerCompleted(currentPrayer);
  }

  void _setExpression(CharacterExpression expression) {
    if (mounted && _currentExpression != expression) {
      setState(() {
        _currentExpression = expression;
      });
    }
  }

  DateTime? _getNextPrayerTime() {
    if (_prayerTimes == null) return null;
    final now = DateTime.now();
    final times = {
      'Fajr': _parseTime(_prayerTimes!.fajr),
      'Dhuhr': _parseTime(_prayerTimes!.dhuhr),
      'Asr': _parseTime(_prayerTimes!.asr),
      'Maghrib': _parseTime(_prayerTimes!.maghrib),
      'Isha': _parseTime(_prayerTimes!.isha),
    };

    final sorted = times.entries.toList()
      ..sort((a, b) => a.value.compareTo(b.value));

    for (var entry in sorted) {
      if (entry.value.isAfter(now)) {
        return entry.value;
      }
    }

    // After Isha, next is tomorrow's Fajr
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

    final sorted = times.entries.toList()
      ..sort((a, b) => a.value.compareTo(b.value));

    for (int i = sorted.length - 1; i >= 0; i--) {
      if (now.isAfter(sorted[i].value)) {
        return sorted[i].key;
      }
    }
    return 'Isha';
  }

  DateTime _parseTime(String time) {
    try {
      final parts = time.split(':');
      final now = DateTime.now();
      return DateTime(
        now.year,
        now.month,
        now.day,
        int.parse(parts[0]),
        int.parse(parts[1]),
      );
    } catch (e) {
      return DateTime.now();
    }
  }

  bool _isPrayerCompleted(String prayerName) {
    String target = "";
    switch (prayerName) {
      case 'Fajr':
        target = 'Subuh';
        break;
      case 'Dhuhr':
        target = 'Dzuhur';
        break;
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

    // Check if task exists and is completed
    // We check _tasks which is populated from API
    try {
      final task = _tasks.firstWhere(
        (t) =>
            t.name.contains(target) ||
            t.name.toLowerCase().contains(prayerName.toLowerCase()),
      );
      return task.isCompleted;
    } catch (e) {
      return true; // Default to true if task not found (assume done or not tracked) to avoid angry loop
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: PremiumColor.background,
        body: const Stack(
          children: [
            Positioned.fill(child: IslamicPatternBackground()),
            MuqarnasHeaderBackground(height: 400),
            Center(
              child: CircularProgressIndicator(color: PremiumColor.primary),
            ),
          ],
        ),
      );
    }

    if (_errorMessage != null || _userProfile == null) {
      return Scaffold(
        backgroundColor: PremiumColor.background,
        body: Stack(
          children: [
            const Positioned.fill(child: IslamicPatternBackground()),
            const MuqarnasHeaderBackground(height: 400),
            Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.person_off_rounded,
                    size: 64,
                    color: PremiumColor.primary,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    _errorMessage ?? "Profil tidak ditemukan",
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: PremiumColor.primary,
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: _fetchData,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PremiumColor.accent,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text("Coba Lagi"),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          NestedScrollView(
            headerSliverBuilder: (context, innerBoxIsScrolled) => [
              _buildSliverHeader(),
              SliverPersistentHeader(
                pinned: true,
                delegate: _SliverAppBarDelegate(
                  TabBar(
                    controller: _tabController,
                    labelColor: PremiumColor.primary,
                    unselectedLabelColor: Colors.grey[400],
                    indicatorColor: PremiumColor.primary,
                    indicatorWeight: 3,
                    labelStyle: GoogleFonts.plusJakartaSans(
                      fontWeight: FontWeight.w900,
                      fontSize: 13,
                      letterSpacing: 0.5,
                    ),
                    unselectedLabelStyle: GoogleFonts.plusJakartaSans(
                      fontWeight: FontWeight.w700,
                      fontSize: 13,
                    ),
                    tabs: const [
                      Tab(text: "OVERVIEW"),
                      Tab(text: "STATISTIK"),
                      Tab(text: "ACTIVITY"),
                    ],
                  ),
                ),
              ),
            ],
            body: TabBarView(
              controller: _tabController,
              children: [
                _buildOverviewTab(),
                _buildJourneyTab(),
                _buildActivityTab(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 250,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      actions: [
        _buildGlassIconBtn(
          Icons.settings_rounded,
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) =>
                    SettingsScreen(userProfile: _userProfile!),
              ),
            ).then((_) {
              _fetchData(); // Refresh data on back
            });
          },
        ),
        const SizedBox(width: 20),
      ],
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: BoxDecoration(
              color: PremiumColor.primary,
              gradient: RadialGradient(
                center: Alignment.topRight,
                radius: 1.5,
                colors: [Colors.white.withOpacity(0.1), PremiumColor.primary],
              ),
              image: const DecorationImage(
                image: NetworkImage(
                  "https://www.transparenttextures.com/patterns/handmade-paper.png",
                ),
                opacity: 0.1,
                fit: BoxFit.cover,
              ),
            ),
            padding: const EdgeInsets.fromLTRB(24, 0, 24, 85),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "RANK HUNTER",
                  style: GoogleFonts.plusJakartaSans(
                    color: Colors.white.withOpacity(0.8),
                    fontSize: 11,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 3.0,
                  ),
                ),
                Text(
                  _userProfile!.rank.toString().toUpperCase(),
                  style: GoogleFonts.plusJakartaSans(
                    color: Colors.white,
                    fontSize: 32,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    _buildHeaderStat(
                      "${_userProfile!.followersCount} Followers",
                      Icons.group_rounded,
                      () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => FollowListScreen(
                              userId: _userProfile!.id,
                              type: 'followers',
                              title: _userProfile!.username,
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(width: 12),
                    _buildHeaderStat(
                      "${_userProfile!.followingCount} Following",
                      Icons.person_add_rounded,
                      () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => FollowListScreen(
                              userId: _userProfile!.id,
                              type: 'following',
                              title: _userProfile!.username,
                            ),
                          ),
                        );
                      },
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderStat(String text, IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.15),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: Colors.white, size: 14),
            const SizedBox(width: 6),
            Text(
              text,
              style: GoogleFonts.plusJakartaSans(
                color: Colors.white,
                fontSize: 12,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildOverviewTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(horizontal: 0, vertical: 24),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: _buildHUDCard(),
          ),
          const SizedBox(height: 32),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Row(
              children: [
                Expanded(
                  child: _buildCompactStatTile(
                    Icons.local_fire_department_rounded,
                    "${_userProfile!.stats.streak} Hari",
                    "Streak Harian",
                    Colors.orange,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildCompactStatTile(
                    Icons.auto_graph_rounded,
                    _userProfile!.rank,
                    "Rank Hunter",
                    PremiumColor.primary,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 48),
          _buildMenuSection("KARAKTER & SKILL", Icons.shield_outlined, [
            _MenuOption(
              icon: Icons.auto_stories_rounded,
              title: "Jejak Hunter",
              color: Colors.blue,
              onTap: () {
                if (_userProfile != null) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) =>
                          HunterJourneyScreen(profile: _userProfile!),
                    ),
                  );
                }
              },
            ),
            _MenuOption(
              icon: Icons.inventory_2_rounded,
              title: "Inventory",
              color: Colors.orange,
              onTap: _showComingSoon,
            ),
            _MenuOption(
              icon: Icons.shield_rounded,
              title: "Equipment",
              color: Colors.teal,
              onTap: _showComingSoon,
            ),
            _MenuOption(
              icon: Icons.auto_awesome_rounded,
              title: "Active Skills",
              color: Colors.purple,
              onTap: _showComingSoon,
            ),
          ]),
          _buildMenuSection("AFFILIATE & PENGHASILAN", Icons.wallet_rounded, [
            _MenuOption(
              icon: Icons.account_balance_wallet_rounded,
              title:
                  "Saldo: Rp ${NumberFormat.decimalPattern('id').format(_userProfile!.balance)}",
              color: Colors.green,
              onTap: () {
                if (_userProfile != null) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) =>
                          WithdrawalScreen(profile: _userProfile!),
                    ),
                  ).then((_) => _fetchData());
                }
              },
            ),
            _MenuOption(
              icon: Icons.payments_rounded,
              title: "Daftar Komisi",
              color: Colors.orange,
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const CommissionScreen(),
                  ),
                ).then((_) => _fetchData());
              },
            ),
            _MenuOption(
              icon: Icons.account_balance_rounded,
              title: "Tarik Saldo (WD)",
              color: Colors.blue,
              onTap: () {
                if (_userProfile != null) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) =>
                          WithdrawalScreen(profile: _userProfile!),
                    ),
                  ).then((_) => _fetchData());
                }
              },
            ),
            _MenuOption(
              icon: Icons.share_rounded,
              title: "Salin Kode Referral",
              color: Colors.teal,
              onTap: () {
                Clipboard.setData(
                  ClipboardData(text: _userProfile!.referralCode),
                );
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text("Kode Referral berhasil disalin!"),
                  ),
                );
              },
            ),
          ]),
          const SizedBox(height: 48),
          _buildMenuSection("HUBUNGAN SOSIAL", Icons.groups_outlined, [
            _MenuOption(
              icon: Icons.groups_rounded,
              title: "Party (Grup)",
              color: Colors.indigo,
              onTap: _showComingSoon,
            ),
            _MenuOption(
              icon: Icons.emoji_events_rounded,
              title: "Leaderboard",
              color: PremiumColor.accent,
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const RankingScreen(),
                  ),
                );
              },
            ),
            _MenuOption(
              icon: Icons.coffee_rounded,
              title: "Tavern",
              color: Colors.brown,
              onTap: _showComingSoon,
            ),
            _MenuOption(
              icon: Icons.campaign_rounded,
              title: "Challenges",
              color: Colors.red,
              onTap: _showComingSoon,
            ),
          ]),
          const SizedBox(height: 48),
          _buildMenuSection("PENGATURAN SISTEM", Icons.settings_outlined, [
            _MenuOption(
              icon: Icons.person_search_rounded,
              title: "Cari Teman",
              color: Colors.blueGrey,
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const SearchFriendScreen(),
                  ),
                ).then((_) => _fetchData());
              },
            ),
            _MenuOption(
              icon: Icons.settings_rounded,
              title: "Pengaturan",
              color: Colors.blueGrey,
              onTap: () async {
                if (_userProfile != null) {
                  final result = await Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) =>
                          SettingsScreen(userProfile: _userProfile!),
                    ),
                  );
                  if (result == true) {
                    _fetchProfile(); // Reload if updated
                  }
                }
              },
            ),
          ]),
          const SizedBox(height: 60),
        ],
      ),
    );
  }

  Widget _buildJourneyTab() {
    // Aggregated normalization logic for Radar Chart (Now using ALL-TIME stats)
    final stats = _userProfile!.stats;
    final int totalSholat = stats.attributes?.strength ?? 0;
    final int totalNgaji = stats.totalQuranSessions;
    final int totalMisiSelesai = stats.totalMissionsCompleted;
    final int totalMisiDiambil = stats.totalMissionsTaken;
    final int totalJurnal = stats.totalJournals;
    final int totalHabit = stats.totalHabits > 0
        ? stats.totalHabits
        : (stats.attributes?.vitality ?? 0);
    final int totalKajian = stats.totalLectures;

    // 1. IBADAH: Mix of consistency and total sholat (Benchmark: 100 prayers for full bar)
    final double sholatConsistency =
        (stats.salahConsistency / 100).clamp(0.0, 1.0);
    final double sholatFactor = (totalSholat / 100).clamp(0.0, 1.0);
    final double ibadahVal =
        (sholatConsistency * 0.4 + sholatFactor * 0.6).clamp(0.0, 1.0);

    // 2. ILMU: Strictly Quran sessions. Benchmark: 40 Sessions for full bar.
    final double ilmuVal = (totalNgaji / 40).clamp(0.0, 1.0);

    // 3. KARAKTER: Habits (Vitality) + Adab score
    final double habitFactor = (totalHabit / 30).clamp(0.0, 1.0);
    final double adabFactor =
        ((stats.attributes?.wisdom ?? 0) / 50).clamp(0.0, 1.0);
    final double karakterVal =
        (habitFactor * 0.7 + adabFactor * 0.3).clamp(0.0, 1.0);

    // 4. AMAL: Strictly Completed Quests (all-time). Benchmark: 30 Quests for full bar.
    final double amalVal = (totalMisiSelesai / 30.0).clamp(0.0, 1.0);

    // 5. ISTIQOMAH: Streak (40 days goal)
    final double streakVal = (stats.streak / 40.0).clamp(0.0, 1.0);

    // 6. WAWASAN: Video completions. Benchmark: 20 Videos for full bar.
    final double wawasanVal = (totalKajian / 20.0).clamp(0.0, 1.0);

    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _SectionHeader(
            title: "SPIRITUAL ATTRIBUTE CHART",
            icon: Icons.auto_awesome_rounded,
          ),
          const SizedBox(height: 24),
          Center(
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(32),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.03),
                    blurRadius: 30,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: RadarChart(
                size: 240,
                values: {
                  'IBADAH': ibadahVal,
                  'ILMU': ilmuVal,
                  'WAWASAN': wawasanVal,
                  'KARAKTER': karakterVal,
                  'AMAL': amalVal,
                  'ISTIQOMAH': streakVal,
                },
              ),
            ),
          ),
          const SizedBox(height: 40),
          _SectionHeader(
              title: "HUNTER STATS SUMMARY", icon: Icons.insights_rounded),
          const SizedBox(height: 16),
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            childAspectRatio: 2.1,
            mainAxisSpacing: 12,
            crossAxisSpacing: 12,
            children: [
              _buildMiniSummaryCard(
                "Total Sholat",
                "$totalSholat Selesai",
                Colors.redAccent,
              ),
              _buildMiniSummaryCard(
                "Total Ngaji",
                "$totalNgaji Sesi",
                Colors.blueAccent,
              ),
              _buildMiniSummaryCard(
                "Misi Diambil",
                "$totalMisiDiambil Diambil",
                Colors.orange,
              ),
              _buildMiniSummaryCard(
                "Misi Selesai",
                "$totalMisiSelesai Selesai",
                Colors.orangeAccent,
              ),
              _buildMiniSummaryCard(
                "Total Jurnal",
                "$totalJurnal Entri",
                Colors.indigoAccent,
              ),
              _buildMiniSummaryCard(
                "Total Habit",
                "$totalHabit Point",
                Colors.teal,
              ),
              _buildMiniSummaryCard(
                "Total Kajian",
                "$totalKajian Video",
                Colors.purpleAccent,
              ),
              _buildMiniSummaryCard(
                "Streak",
                "${stats.streak} Hari",
                Colors.deepOrangeAccent,
              ),
            ],
          ),
          const SizedBox(height: 40),
          _SectionHeader(
            title: "PROGRESS SUMMARY",
            icon: Icons.insights_rounded,
          ),
          const SizedBox(height: 20),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: Colors.black.withOpacity(0.05)),
            ),
            child: Column(
              children: [
                _buildSummaryRow(
                  "Konsistensi Sholat",
                  "${_userProfile!.stats.salahConsistency}%",
                  Colors.green,
                ),
                const Divider(height: 32),
                _buildSummaryRow(
                  "Progres Al-Quran",
                  _userProfile!.stats.quranProgress,
                  Colors.blue,
                ),
                const Divider(height: 32),
                _buildSummaryRow(
                  "Total Sesi Tadarus",
                  "${_userProfile!.stats.totalQuranSessions} Sesi",
                  Colors.orange,
                ),
              ],
            ),
          ),
          const SizedBox(height: 40),
          _SectionHeader(title: "HUNTER AFFILIATE", icon: Icons.share_rounded),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  PremiumColor.primary,
                  PremiumColor.primary.withOpacity(0.8),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(24),
              boxShadow: [
                BoxShadow(
                  color: PremiumColor.primary.withOpacity(0.3),
                  blurRadius: 12,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              children: [
                Text(
                  "KODE REFERRAL ANDA",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    fontWeight: FontWeight.w900,
                    color: Colors.white.withOpacity(0.7),
                    letterSpacing: 2.0,
                  ),
                ),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Flexible(
                      child: Text(
                        _userProfile!.referralCode,
                        style: GoogleFonts.oswald(
                          fontSize: 28,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                          letterSpacing: 4.0,
                        ),
                        overflow: TextOverflow.ellipsis,
                        textAlign: TextAlign.center,
                      ),
                    ),
                    const SizedBox(width: 8),
                    IconButton(
                      icon: const Icon(Icons.copy_rounded, color: Colors.white),
                      onPressed: () {
                        Clipboard.setData(
                          ClipboardData(text: _userProfile!.referralCode),
                        );
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: Text(
                              "Kode Referral disalin!",
                              style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            backgroundColor: PremiumColor.primary,
                            behavior: SnackBarBehavior.floating,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(10),
                            ),
                          ),
                        );
                      },
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: () {
                      final String shareLink =
                          "https://muslimapp.com/register?ref=${_userProfile!.referralCode}";
                      Share.share(
                        "Ayo bergabung dengan saya di Muslim Hunter! Gunakan kode referral saya: ${_userProfile!.referralCode}\n\nDaftar di sini: $shareLink",
                        subject: "Undangan Bergabung Muslim Hunter",
                      );
                    },
                    icon: const Icon(Icons.share_rounded, size: 18),
                    label: Text(
                      "BAGIKAN LINK REFERRAL",
                      style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w900,
                        fontSize: 12,
                        letterSpacing: 1.0,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: PremiumColor.primary,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(15),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  "Bagikan kode atau link ini untuk mengundang Hunter baru!",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                    color: Colors.white.withOpacity(0.9),
                  ),
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          ),
          const SizedBox(height: 60),
        ],
      ),
    );
  }

  Widget _buildActivityTab() {
    if (_activities.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          children: [
            _SectionHeader(
              title: "RIWAYAT AKTIVITAS",
              icon: Icons.history_rounded,
            ),
            const SizedBox(height: 40),
            Center(
              child: Column(
                children: [
                  Icon(
                    Icons.history_toggle_off_rounded,
                    size: 64,
                    color: PremiumColor.slate200,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    "Belum ada aktivitas tercatat.",
                    style: GoogleFonts.plusJakartaSans(
                      color: PremiumColor.slate400,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
    }

    // Group activities by date
    final Map<String, List<ActivityLog>> grouped = {};
    for (var act in _activities) {
      final dateKey = _getDateKey(act.createdAt);
      if (!grouped.containsKey(dateKey)) {
        grouped[dateKey] = [];
      }
      grouped[dateKey]!.add(act);
    }

    final sortedDates = grouped.keys.toList();

    return ListView.builder(
      padding: const EdgeInsets.all(24),
      itemCount: sortedDates.length + 1,
      itemBuilder: (context, index) {
        if (index == 0) {
          return Padding(
            padding: const EdgeInsets.only(bottom: 24.0),
            child: _SectionHeader(
              title: "RIWAYAT AKTIVITAS",
              icon: Icons.history_rounded,
            ),
          );
        }

        final dateStr = sortedDates[index - 1];
        final dayActivities = grouped[dateStr]!;

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildDateDivider(dateStr),
            const SizedBox(height: 16),
            ...dayActivities.asMap().entries.map((entry) {
              final isLast = entry.key == dayActivities.length - 1;
              return _buildActivityTile(entry.value, isLast: isLast);
            }),
            const SizedBox(height: 24),
          ],
        );
      },
    );
  }

  String _getDateKey(DateTime date) {
    // Standardize to local time before grouping
    final localDate = date.toLocal();
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final yesterday = today.subtract(const Duration(days: 1));
    final activityDate =
        DateTime(localDate.year, localDate.month, localDate.day);

    if (activityDate == today) return "Hari Ini";
    if (activityDate == yesterday) return "Kemarin";
    return DateFormat('EEEE, d MMMM yyyy', 'id').format(localDate);
  }

  Widget _buildDateDivider(String date) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
          decoration: BoxDecoration(
            color: PremiumColor.primary.withOpacity(0.05),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Text(
            date.toUpperCase(),
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w800,
              color: PremiumColor.primary,
              letterSpacing: 1.0,
            ),
          ),
        ),
        const SizedBox(width: 8),
        Expanded(child: Divider(color: PremiumColor.slate200, thickness: 1)),
      ],
    );
  }

  Widget _buildHUDCard() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Colors.grey.withOpacity(0.1)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 25,
            spreadRadius: -5,
            offset: const Offset(0, 10),
          ),
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.04),
            blurRadius: 10,
            spreadRadius: 2,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(28),
        child: Stack(
          children: [
            // Decorative background elements
            Positioned(
              right: -30,
              top: -30,
              child: Container(
                padding: const EdgeInsets.all(40),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(
                    colors: [
                      PremiumColor.primary.withOpacity(0.08),
                      Colors.transparent,
                    ],
                    center: Alignment.center,
                    radius: 0.8,
                  ),
                ),
                child: Icon(
                  Icons.workspace_premium_rounded,
                  size: 160,
                  color: PremiumColor.primary.withOpacity(0.05),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(24),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  RubElHizbAvatar(
                    imageUrl: _userProfile!.avatar,
                    level: "LVL ${_userProfile!.level}",
                    hpProgress: _userProfile!.hp.progress / 100,
                    gender: _userProfile!.gender,
                    expression: _currentExpression,
                    isCircle: true,
                  ),
                  const SizedBox(width: 24),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                _userProfile!.username,
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 22,
                                  fontWeight: FontWeight.w900,
                                  letterSpacing: -0.5,
                                  color: PremiumColor.slate800,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          crossAxisAlignment: WrapCrossAlignment.center,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 6,
                              ),
                              decoration: BoxDecoration(
                                color: PremiumColor.primary.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                  color: PremiumColor.primary.withOpacity(0.2),
                                ),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(
                                    Icons.military_tech_rounded,
                                    size: 14,
                                    color: PremiumColor.primary,
                                  ),
                                  const SizedBox(width: 4),
                                  Flexible(
                                    child: Text(
                                      "Lvl ${_userProfile!.level}",
                                      style: GoogleFonts.plusJakartaSans(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w800,
                                        color: PremiumColor.primary,
                                      ),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 6,
                              ),
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [
                                    PremiumColor.primary,
                                    PremiumColor.accent,
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(8),
                                boxShadow: [
                                  BoxShadow(
                                    color: PremiumColor.primary.withOpacity(
                                      0.3,
                                    ),
                                    blurRadius: 8,
                                    offset: const Offset(0, 4),
                                  ),
                                ],
                              ),
                              child: Text(
                                "LVL ${_userProfile!.level}",
                                style: GoogleFonts.oswald(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                  letterSpacing: 1.0,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 24),
                        _buildMiniBar(
                          label: "HEALTH",
                          current: _userProfile!.hp.current,
                          max: _userProfile!.hp.max,
                          progress: _userProfile!.hp.progress / 100,
                          color: const Color(0xFFEF4444), // Tailwind Red-500
                          icon: Icons.favorite_rounded,
                        ),
                        const SizedBox(height: 14),
                        _buildMiniBar(
                          label: "EXPERIENCE",
                          current: _userProfile!.xp.current,
                          max: _userProfile!.xp.max > 0
                              ? _userProfile!.xp.max
                              : 1000,
                          progress: (_userProfile!.xp.max > 0)
                              ? (_userProfile!.xp.current /
                                  _userProfile!.xp.max)
                              : 0.0,
                          color: const Color(0xFF3B82F6), // Tailwind Blue-500
                          icon: Icons.flash_on_rounded,
                        ),
                        const SizedBox(height: 14),
                        // SOUL bar removed as per user request (Only EXP)
                      ],
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

  Widget _buildMiniBar({
    required String label,
    required int current,
    required int max,
    required double progress,
    required Color color,
    required IconData icon,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(
              child: Row(
                children: [
                  Icon(icon, size: 12, color: color.withOpacity(0.9)),
                  const SizedBox(width: 4),
                  Flexible(
                    child: Text(
                      label,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                        color: PremiumColor.slate500,
                        letterSpacing: 0.5,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 4),
            Text(
              "$current / $max",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.w800,
                color: PremiumColor.slate800,
              ),
            ),
          ],
        ),
        const SizedBox(height: 6),
        Container(
          height: 8,
          decoration: BoxDecoration(
            color: Colors.grey.withOpacity(0.15),
            borderRadius: BorderRadius.circular(4),
          ),
          child: Stack(
            children: [
              AnimatedContainer(
                duration: const Duration(milliseconds: 800),
                curve: Curves.easeOutCubic,
                width: MediaQuery.of(context).size.width *
                    0.45 *
                    progress.clamp(0.0, 1.0),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [color.withOpacity(0.8), color],
                  ),
                  borderRadius: BorderRadius.circular(4),
                  boxShadow: [
                    BoxShadow(
                      color: color.withOpacity(0.3),
                      blurRadius: 4,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildCompactStatTile(
    IconData icon,
    String value,
    String label,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 15,
            spreadRadius: -2,
            offset: const Offset(0, 4),
          ),
          BoxShadow(
            color: color.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [color.withOpacity(0.15), color.withOpacity(0.05)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: color.withOpacity(0.1)),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 14),
          Flexible(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  value,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 15,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.3,
                    color: PremiumColor.slate800,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 2),
                Text(
                  label,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    letterSpacing: 0.2,
                    color: PremiumColor.slate500,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMenuSection(
    String title,
    IconData icon,
    List<_MenuOption> options,
  ) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _SectionHeader(title: title, icon: icon),
          const SizedBox(height: 16),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              childAspectRatio: 2.3, // Pro Max aspect ratio for text overflow
              crossAxisSpacing: 14,
              mainAxisSpacing: 14,
            ),
            itemCount: options.length,
            itemBuilder: (context, index) {
              final opt = options[index];
              return InkWell(
                onTap: opt.onTap,
                borderRadius: BorderRadius.circular(16),
                splashColor: opt.color.withOpacity(0.1),
                highlightColor: Colors.transparent,
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 12,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.grey.shade200),
                    boxShadow: [
                      BoxShadow(
                        color: opt.color.withOpacity(0.03),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                      BoxShadow(
                        color: Colors.black.withOpacity(0.02),
                        blurRadius: 4,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              opt.color.withOpacity(0.12),
                              opt.color.withOpacity(0.03),
                            ],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: opt.color.withOpacity(0.05),
                          ),
                        ),
                        child: Icon(opt.icon, color: opt.color, size: 20),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          opt.title,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12.5,
                            fontWeight: FontWeight.w800,
                            color: PremiumColor.slate800,
                            height: 1.1,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildGlassIconBtn(
    IconData icon, {
    VoidCallback? onTap,
    Color? color,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.8),
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Icon(icon, color: color ?? PremiumColor.primary, size: 22),
      ),
    );
  }

  Widget _buildActivityTile(ActivityLog log, {bool isLast = false}) {
    IconData icon;
    Color color;
    String title;

    switch (log.type) {
      case 'xp_gain':
        icon = Icons.bolt_rounded;
        color = PremiumColor.accent;
        title = "Energi Terkumpul";
        break;
      case 'level_up':
        icon = Icons.stars_rounded;
        color = Colors.purple;
        title = "Level Naik!";
        break;
      case 'quest_completion':
        icon = Icons.assignment_turned_in_rounded;
        color = Colors.green;
        title = "Quest Selesai";
        break;
      case 'habit_score':
        icon = Icons.check_circle_rounded;
        color = Colors.blue;
        title = "Ibadah Terlaksana";
        break;
      case 'social_follow':
        icon = Icons.person_add_rounded;
        color = Colors.indigo;
        title = log.description;
        break;
      case 'penalty':
        icon = Icons.warning_amber_rounded;
        color = Colors.redAccent;
        title = "Penalti Diterima";
        break;
      default:
        icon = Icons.info_outline_rounded;
        color = Colors.grey;
        title = log.description;
    }

    final localTime = log.createdAt.toLocal();
    final now = DateTime.now();
    final diff = now.difference(localTime);

    String timeStr;
    if (diff.inMinutes < 1) {
      timeStr = "Baru saja";
    } else if (diff.inMinutes < 60) {
      timeStr = "${diff.inMinutes} menit lalu";
    } else if (diff.inHours < 24 &&
        localTime.day == now.day &&
        localTime.month == now.month &&
        localTime.year == now.year) {
      timeStr = "${diff.inHours} jam lalu";
    } else {
      timeStr = DateFormat('HH:mm').format(localTime);
    }

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Timeline indicator
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0),
            child: Column(
              children: [
                Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.1),
                    shape: BoxShape.circle,
                    border: Border.all(color: color.withOpacity(0.2), width: 1),
                  ),
                  child: Icon(icon, color: color, size: 16),
                ),
                if (!isLast)
                  Expanded(
                    child: Container(
                      width: 2,
                      color: PremiumColor.slate200.withOpacity(0.5),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          // Content
          Expanded(
            child: Padding(
              padding: const EdgeInsets.only(bottom: 24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          log.description.isEmpty ? title : log.description,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                            color: PremiumColor.slate800,
                          ),
                        ),
                      ),
                      Text(
                        timeStr,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: PremiumColor.slate400,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      if (log.type == 'xp_gain') ...[
                        Text(
                          "+${log.amount} XP",
                          style: GoogleFonts.outfit(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: PremiumColor.accent,
                          ),
                        ),
                        const SizedBox(width: 8),
                      ],
                      if (log.type == 'level_up') ...[
                        Text(
                          "LVL ${log.amount}",
                          style: GoogleFonts.outfit(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: Colors.purple,
                          ),
                        ),
                        const SizedBox(width: 8),
                      ],
                      if (log.type == 'penalty') ...[
                        Text(
                          "-${log.amount} HP",
                          style: GoogleFonts.outfit(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: Colors.redAccent,
                          ),
                        ),
                        const SizedBox(width: 8),
                      ],
                      // Tag for type
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 6,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: PremiumColor.slate200.withOpacity(0.3),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          log.type.replaceAll('_', ' ').toUpperCase(),
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 8,
                            fontWeight: FontWeight.w800,
                            color: PremiumColor.slate400,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMiniSummaryCard(String label, String value, Color color) {
    final bool isStreak = label == "Streak";
    final bool isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color:
            isStreak ? null : (isDark ? const Color(0xFF1E293B) : Colors.white),
        gradient: isStreak
            ? LinearGradient(
                colors: [
                  const Color(0xFFFF4D00), // TikTok Red-Orange
                  const Color(0xFFFFB300), // TikTok Yellow
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              )
            : null,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isStreak
              ? Colors.white.withOpacity(0.2)
              : (isDark
                  ? Colors.white.withOpacity(0.05)
                  : Colors.black.withOpacity(0.05)),
        ),
        boxShadow: [
          BoxShadow(
            color: isStreak
                ? Colors.orange.withOpacity(0.3)
                : Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          if (isStreak)
            const Icon(
              Icons.local_fire_department_rounded,
              color: Colors.white,
              size: 20,
            )
          else
            Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(color: color, shape: BoxShape.circle),
            ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  label,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: isStreak ? Colors.white70 : PremiumColor.slate400,
                  ),
                ),
                Text(
                  value,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13,
                    fontWeight: FontWeight.w900,
                    color: isStreak
                        ? Colors.white
                        : (isDark ? Colors.white : PremiumColor.slate800),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, Color color) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: PremiumColor.slate600,
          ),
        ),
        Text(
          value,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 15,
            fontWeight: FontWeight.w800,
            color: color,
          ),
        ),
      ],
    );
  }
}

class _SectionHeader extends StatelessWidget {
  final String title;
  final IconData icon;

  const _SectionHeader({required this.title, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(icon, size: 16, color: PremiumColor.slate400),
        const SizedBox(width: 8),
        Text(
          title,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12,
            fontWeight: FontWeight.w900,
            color: PremiumColor.slate400,
            letterSpacing: 1.5,
          ),
        ),
      ],
    );
  }
}

class _SliverAppBarDelegate extends SliverPersistentHeaderDelegate {
  _SliverAppBarDelegate(this._tabBar);

  final TabBar _tabBar;

  @override
  double get minExtent => _tabBar.preferredSize.height;
  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  Widget build(
    BuildContext context,
    double shrinkOffset,
    bool overlapsContent,
  ) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(
          bottom: BorderSide(color: Colors.black.withOpacity(0.05), width: 1),
        ),
      ),
      child: _tabBar,
    );
  }

  @override
  bool shouldRebuild(_SliverAppBarDelegate oldDelegate) {
    return false;
  }
}

class _MenuOption {
  final IconData icon;
  final String title;
  final Color color;
  final VoidCallback onTap;

  _MenuOption({
    required this.icon,
    required this.title,
    required this.color,
    required this.onTap,
  });
}
