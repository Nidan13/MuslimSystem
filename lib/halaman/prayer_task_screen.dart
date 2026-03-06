import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/prayer_log.dart';
import '../services/prayer_service.dart';
import 'widgets/custom_background.dart';
import '../theme/premium_color.dart';
import '../models/prayer_times.dart';
import '../services/prayer_times_service.dart';
import '../services/location_service.dart';
import '../services/profile_service.dart';

class PrayerTaskScreen extends StatefulWidget {
  const PrayerTaskScreen({super.key});

  @override
  State<PrayerTaskScreen> createState() => _PrayerTaskScreenState();
}

class _PrayerTaskScreenState extends State<PrayerTaskScreen> {
  final PrayerService _prayerService = PrayerService();
  final PrayerTimesService _prayerTimesService = PrayerTimesService();
  final LocationService _locationService = LocationService();
  final ProfileService _profileService = ProfileService();

  List<PrayerLog> _prayers = [];
  PrayerTimes? _prayerTimes;
  bool _isLoading = true;
  bool _isMenstruating = false;
  bool _isFemale = false;

  @override
  void initState() {
    super.initState();
    _fetchProfile(); // Add this
    _fetchPrayerTasks();
    _fetchPrayerTimes();
  }

  Future<void> _fetchProfile() async {
    final profile = await _profileService.getProfile();
    if (profile != null && mounted) {
      setState(() {
        _isMenstruating = profile.isMenstruating;
        _isFemale = profile.gender.toLowerCase() == 'female';
      });
    }
  }

  Future<void> _fetchPrayerTimes() async {
    try {
      final cached = await _prayerTimesService.getCachedPrayerTimes();
      if (cached != null && mounted) {
        setState(() => _prayerTimes = cached);
      }

      final pos = await _locationService.getLocationOrDefault();
      final times = await _prayerTimesService.getPrayerTimes(
        latitude: pos.latitude,
        longitude: pos.longitude,
      );
      if (mounted) {
        setState(() => _prayerTimes = times);
      }
    } catch (e) {
      debugPrint('Error fetching prayer times: $e');
    }
  }

  Future<void> _fetchPrayerTasks() async {
    setState(() => _isLoading = true);
    try {
      final response = await _prayerService.getPrayerLogs();
      if (mounted) {
        setState(() {
          _prayers = response.prayers;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _toggleTask(PrayerLog prayer) async {
    if (prayer.isCompleted) return;

    bool isLate = false;
    // 1 Hour Late Check
    if (_prayerTimes != null) {
      final prayerTime = _prayerTimes!.getPrayerTimeByName(prayer.name);
      if (prayerTime != null) {
        final diff = DateTime.now().difference(prayerTime);
        if (diff.inMinutes >= 60) {
          isLate = true;
          // Show late reminder popup
          await showDialog(
            context: context,
            builder: (context) => AlertDialog(
              backgroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(24)),
              contentPadding: const EdgeInsets.all(24),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: PremiumColor.highlight.withOpacity(0.1),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.access_time_rounded,
                        color: PremiumColor.highlight, size: 48),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    "Agak Terlambat Ya?",
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: PremiumColor.primary,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    "Alhamdulillah kamu tetap menunaikan kewajiban. Namun, ingatlah bahwa sholat tepat waktu adalah amalan yang paling dicintai Allah SWT.\n\nSemoga besok bisa lebih awal ya! 🌟",
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
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16)),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                      ),
                      child: Text(
                        "Insya Allah",
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  )
                ],
              ),
            ),
          );
        }
      }
    }

    // Optimistic Update
    setState(() {
      final index = _prayers.indexWhere((p) => p.key == prayer.key);
      if (index != -1) {
        _prayers[index] = PrayerLog(
          name: prayer.name,
          key: prayer.key,
          isCompleted: true,
          completedAt: DateTime.now()
              .toUtc()
              .toIso8601String(), // Send UTC to prevent drift
        );
      }
    });

    try {
      final response = await _prayerService.completePrayer(prayer.key);
      if (mounted && !isLate) {
        // Assume API gives points, we fallback to generous values if not provided
        int xp = response['data']?['xp_gained'] ?? 10;
        int sp = response['data']?['soul_points_earned'] ?? 5;
        _showRewardDialog("Sholat Tepat Waktu!", xp, sp);
      }
      _fetchPrayerTasks(); // Refresh to get server confirmed data
    } catch (e) {
      // Revert on error
      _fetchPrayerTasks();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Gagal mencatat sholat: $e")),
        );
      }
    }
  }

  void _showRewardDialog(String message, int xp, int sp) {
    showDialog(
      context: context,
      barrierColor: Colors.black54,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        elevation: 0,
        child: TweenAnimationBuilder<double>(
          duration: const Duration(milliseconds: 500),
          tween: Tween(begin: 0.8, end: 1.0),
          curve: Curves.elasticOut,
          builder: (context, value, child) {
            return Transform.scale(scale: value, child: child);
          },
          child: Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              boxShadow: [
                BoxShadow(
                  color: PremiumColor.neonTeal.withOpacity(0.5),
                  blurRadius: 20,
                  spreadRadius: 2,
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("✨", style: TextStyle(fontSize: 40)),
                const SizedBox(height: 16),
                Text(message,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: PremiumColor.primary)),
                const SizedBox(height: 8),
                Text("Cahaya hatimu bertambah terang hari ini.",
                    textAlign: TextAlign.center,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 14, color: Colors.grey)),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.amber.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(12),
                        border:
                            Border.all(color: Colors.amber.withOpacity(0.3)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.flash_on_rounded,
                              size: 16, color: Colors.amber),
                          const SizedBox(width: 8),
                          Text("+$xp XP",
                              style: GoogleFonts.plusJakartaSans(
                                  fontWeight: FontWeight.bold,
                                  color: Colors.amber)),
                        ],
                      ),
                    ),
                    const SizedBox(width: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: PremiumColor.neonTeal.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                            color: PremiumColor.neonTeal.withOpacity(0.3)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.auto_awesome_rounded,
                              size: 16, color: PremiumColor.neonTeal),
                          const SizedBox(width: 8),
                          Text("+$sp SP",
                              style: GoogleFonts.plusJakartaSans(
                                  fontWeight: FontWeight.bold,
                                  color: PremiumColor.neonTeal)),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PremiumColor.primary,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text("Alhamdulillah",
                        style: TextStyle(color: Colors.white)),
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _toggleMenstruation() async {
    setState(() => _isLoading = true);
    final success = await _profileService.toggleMenstruation();
    if (success) {
      await _fetchProfile();
      await _fetchPrayerTasks();
    }
    if (mounted) {
      setState(() => _isLoading = false);
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(_isMenstruating
                ? "Status: Sedang Berhalangan. HP kamu aman!"
                : "Status: Sudah Suci. Semangat Ibadah Lagi!"),
            backgroundColor:
                _isMenstruating ? PremiumColor.highlight : PremiumColor.primary,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildSliverHeader(),

          // Prayer List
          _isLoading
              ? const SliverFillRemaining(
                  child: Center(
                    child:
                        CircularProgressIndicator(color: PremiumColor.primary),
                  ),
                )
              : _prayers.isEmpty
                  ? SliverFillRemaining(child: _buildEmptyState())
                  : SliverPadding(
                      padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
                      sliver: SliverList(
                        delegate: SliverChildBuilderDelegate(
                          (context, index) {
                            return _buildPrayerCard(_prayers[index]);
                          },
                          childCount: _prayers.length,
                        ),
                      ),
                    ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    int total = _prayers.length;
    int completed = _prayers.where((p) => p.isCompleted).length;
    double progress = total > 0 ? completed / total : 0;

    return SliverAppBar(
      expandedHeight: 250,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: true,
      leading: const BackButton(color: Colors.white),
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [PremiumColor.primary, PremiumColor.accent],
              ),
              image: DecorationImage(
                image: NetworkImage(
                    "https://www.transparenttextures.com/patterns/handmade-paper.png"),
                opacity: 0.1,
                fit: BoxFit.cover,
              ),
            ),
            child: Stack(
              children: [
                // Pattern Overlay
                const Positioned.fill(child: IslamicPatternBackground()),

                // Content
                Padding(
                  padding: const EdgeInsets.fromLTRB(24, 60, 24, 30),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            "IBADAH SHOLAT",
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              fontWeight: FontWeight.w900,
                              color: Colors.white.withOpacity(0.8),
                              letterSpacing: 2.0,
                            ),
                          ),
                          if (_isFemale)
                            GestureDetector(
                              onTap: _toggleMenstruation,
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 6),
                                decoration: BoxDecoration(
                                  color: _isMenstruating
                                      ? Colors.white
                                      : Colors.white.withOpacity(0.15),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Icon(
                                      _isMenstruating
                                          ? Icons.female_rounded
                                          : Icons.spa_rounded,
                                      color: _isMenstruating
                                          ? PremiumColor.primary
                                          : Colors.white,
                                      size: 14,
                                    ),
                                    const SizedBox(width: 6),
                                    Text(
                                      _isMenstruating
                                          ? "BERHALANGAN"
                                          : "LAPOR HAID",
                                      style: GoogleFonts.plusJakartaSans(
                                        color: _isMenstruating
                                            ? PremiumColor.primary
                                            : Colors.white,
                                        fontSize: 9,
                                        fontWeight: FontWeight.w900,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                        ],
                      ),
                      Text(
                        "Mission Hub",
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          _buildHeaderStat(
                              "$completed/$total SHOLAT", Icons.mosque_rounded),
                          const SizedBox(width: 12),
                          _buildHeaderStat(
                              "HARIAN", Icons.calendar_today_rounded),
                          const Spacer(),
                          Text(
                            "${(progress * 100).toInt()}%",
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white.withOpacity(0.9),
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(2),
                        child: LinearProgressIndicator(
                          value: progress,
                          backgroundColor: Colors.white.withOpacity(0.1),
                          valueColor:
                              const AlwaysStoppedAnimation<Color>(Colors.white),
                          minHeight: 4,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderStat(String text, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.15),
        borderRadius: BorderRadius.circular(12),
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
              fontSize: 10,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.5,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.mosque_outlined,
              size: 64, color: PremiumColor.primary.withOpacity(0.1)),
          const SizedBox(height: 16),
          Text(
            "Misi sholat tidak tersedia.",
            style: GoogleFonts.plusJakartaSans(
              color: PremiumColor.slate400,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  DateTime _parseTime(String timeStr) {
    try {
      final parts = timeStr.split(':');
      final now = DateTime.now();
      return DateTime(now.year, now.month, now.day, int.parse(parts[0]),
          int.parse(parts[1]));
    } catch (e) {
      return DateTime.now();
    }
  }

  int _getPrayerIndex(String name) {
    final n = name.toLowerCase();
    if (n.contains('fajr') || n.contains('subuh')) return 0;
    if (n.contains('dhuhr') || n.contains('dzuhur')) return 1;
    if (n.contains('asr') || n.contains('ashar')) return 2;
    if (n.contains('maghrib')) return 3;
    if (n.contains('isha') || n.contains('isya')) return 4;
    return -1;
  }

  Widget _buildPrayerCard(PrayerLog prayer) {
    final bool isDone = prayer.isCompleted;
    final bool isExcused = _isMenstruating;

    // Time Check
    bool isLock = false; // future lock
    bool isMissedLock = false; // past lock
    String? prayerTimeStr;

    if (!isDone && _prayerTimes != null) {
      final prayerTime = _prayerTimes!.getPrayerTimeByName(prayer.name);
      if (prayerTime != null) {
        prayerTimeStr = DateFormat('HH:mm').format(prayerTime);
        final now = DateTime.now();

        if (now.isBefore(prayerTime)) {
          isLock = true;
        } else {
          // Check if the NEXT prayer time has already started
          final timesInOrder = [
            _parseTime(_prayerTimes!.fajr),
            _parseTime(_prayerTimes!.dhuhr),
            _parseTime(_prayerTimes!.asr),
            _parseTime(_prayerTimes!.maghrib),
            _parseTime(_prayerTimes!.isha),
          ];

          int currentIdx = _getPrayerIndex(prayer.name);
          if (currentIdx != -1 && currentIdx < timesInOrder.length - 1) {
            final nextPrayerTime = timesInOrder[currentIdx + 1];
            if (now.isAfter(nextPrayerTime)) {
              isMissedLock = true;
            }
          }
        }
      }
    }

    return GestureDetector(
      onTap: () {
        if (isExcused) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Kamu sedang berhalangan ibadah.")),
          );
          return;
        }
        if (isMissedLock) {
          _showMissedAlert();
        } else if (isLock) {
          _showLockedAlert();
        } else if (!isDone) {
          _toggleTask(prayer);
        }
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(28),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
          border: Border.all(
            color: isDone
                ? PremiumColor.accent.withOpacity(0.4)
                : isMissedLock
                    ? Colors.red.withOpacity(0.2)
                    : isLock
                        ? Colors.black.withOpacity(0.03)
                        : PremiumColor.primary.withOpacity(0.08),
            width: isDone ? 2 : 1,
          ),
        ),
        child: Row(
          children: [
            // Icon
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: isDone
                    ? PremiumColor.accent.withOpacity(0.1)
                    : isMissedLock
                        ? Colors.red.withOpacity(0.05)
                        : isLock
                            ? PremiumColor.slate50
                            : PremiumColor.primary.withOpacity(0.06),
                borderRadius: BorderRadius.circular(18),
              ),
              child: Icon(
                isMissedLock
                    ? Icons.block_rounded
                    : isLock
                        ? Icons.lock_clock_rounded
                        : Icons.mosque_rounded,
                color: isDone
                    ? PremiumColor.accent
                    : isMissedLock
                        ? Colors.red.shade300
                        : isLock
                            ? PremiumColor.slate200
                            : isExcused
                                ? PremiumColor.slate400
                                : PremiumColor.primary,
                size: 24,
              ),
            ),
            const SizedBox(width: 18),
            // Middle Content
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    prayer.name,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: isLock || isExcused
                          ? PremiumColor.slate400
                          : PremiumColor.primary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  if (isDone)
                    Text(
                      "Completed at ${DateFormat('HH:mm').format(DateTime.parse(prayer.completedAt!).toLocal())}",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: PremiumColor.accent,
                      ),
                    )
                  else if (isLock)
                    Text(
                      "Unlocks at $prayerTimeStr",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: PremiumColor.slate400,
                      ),
                    )
                  else if (isExcused)
                    Text(
                      "Masa Dispensasi Ibadah (Haid)",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: PremiumColor.highlight,
                      ),
                    )
                  else
                    Text(
                      "Tap to complete mission",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: PremiumColor.slate400,
                      ),
                    ),
                ],
              ),
            ),
            // Trailing
            if (isDone)
              const Icon(Icons.check_circle_rounded,
                  color: PremiumColor.accent, size: 28)
            else if (isLock)
              Icon(Icons.lock_rounded,
                  color: PremiumColor.slate200.withOpacity(0.5), size: 24)
            else
              Container(
                width: 24,
                height: 24,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(
                      color: PremiumColor.primary.withOpacity(0.2), width: 2),
                ),
              ),
          ],
        ),
      ),
    );
  }

  void _showLockedAlert() {
    ScaffoldMessenger.of(context).clearSnackBars();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.warning_amber_rounded, color: Colors.white),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                "Ayo jujur, waktu sholatnya belum mulai loh! 😉",
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700,
                  fontSize: 13,
                ),
              ),
            ),
          ],
        ),
        backgroundColor: PremiumColor.highlight,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        margin: const EdgeInsets.all(20),
        duration: const Duration(seconds: 2),
      ),
    );
  }

  void _showMissedAlert() {
    ScaffoldMessenger.of(context).clearSnackBars();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.error_outline_rounded, color: Colors.white),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                "Waktunya sudah lewat, jangan sampai ninggalin sholat yaa besok! 🥺",
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700,
                  fontSize: 13,
                ),
              ),
            ),
          ],
        ),
        backgroundColor: Colors.red.shade400,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        margin: const EdgeInsets.all(20),
        duration: const Duration(seconds: 3),
      ),
    );
  }
}
