import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/prayer_times.dart';
import '../services/prayer_times_service.dart';
import '../services/location_service.dart';
import '../theme/premium_color.dart';
import 'adzan_selection_screen.dart';
import 'qibla_screen.dart';
import 'calendar_screen.dart';
import 'package:flutter/services.dart';

class PrayerTimesScreen extends StatefulWidget {
  const PrayerTimesScreen({super.key});

  @override
  State<PrayerTimesScreen> createState() => _PrayerTimesScreenState();
}

class _PrayerTimesScreenState extends State<PrayerTimesScreen> {
  final PrayerTimesService _prayerTimesService = PrayerTimesService();
  final LocationService _locationService = LocationService();

  PrayerTimes? _prayerTimes;
  String _detailedLocation = "";
  bool _isLoading = true;
  DateTime _selectedDate = DateTime.now();
  Timer? _countdownTimer;
  Duration _timeLeft = Duration.zero;
  String _nextPrayerName = "";
  String _nextPrayerTime = "";

  @override
  void initState() {
    super.initState();
    _initData();
    _startTimer();
  }

  @override
  void dispose() {
    _countdownTimer?.cancel();
    super.dispose();
  }

  void _startTimer() {
    _countdownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_prayerTimes != null) {
        if (mounted) {
          setState(() {
            _timeLeft = _prayerTimes!.getTimeUntilNext();
            final next = _prayerTimes!.getNextPrayer();
            _nextPrayerName = _mapEnglishToIndo(next['name'] ?? "");
            _nextPrayerTime = next['time'] ?? "";
          });
        }
      }
    });
  }

  Future<void> _initData() async {
    if (mounted) setState(() => _isLoading = true);
    try {
      final pos = await _locationService.getLocationOrDefault();

      final address = await _locationService.getAddressFromLocation(
          pos.latitude, pos.longitude);
      if (mounted) {
        setState(() {
          _detailedLocation = address;
        });
      }

      final times = await _prayerTimesService.getPrayerTimes(
        latitude: pos.latitude,
        longitude: pos.longitude,
        date: _selectedDate,
      );

      if (mounted) {
        setState(() {
          _prayerTimes = times;
          _isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Error init prayer times: $e");
      if (mounted) setState(() => _isLoading = false);
    }
  }

  String _mapEnglishToIndo(String name) {
    switch (name.toLowerCase()) {
      case 'fajr':
        return 'Subuh';
      case 'dhuhr':
        return 'Zuhur';
      case 'asr':
        return 'Ashar';
      case 'maghrib':
        return 'Maghrib';
      case 'isha':
        return 'Isya\'';
      default:
        return name;
    }
  }

  String _formatDuration(Duration d) {
    String twoDigits(int n) => n.toString().padLeft(2, "0");
    String hours = twoDigits(d.inHours);
    String minutes = twoDigits(d.inMinutes.remainder(60));
    String seconds = twoDigits(d.inSeconds.remainder(60));
    return "- $hours : $minutes : $seconds";
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: PremiumColor.primary))
          : RefreshIndicator(
              onRefresh: _initData,
              color: PremiumColor.primary,
              child: CustomScrollView(
                slivers: [
                  _buildHeader(),
                  SliverToBoxAdapter(child: _buildDateSelector()),
                  SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    sliver: SliverList(
                      delegate: SliverChildListDelegate([
                        const SizedBox(height: 10),
                        _buildPrayerItem(
                            "Imsak",
                            _prayerTimes?.imsak ?? "--:--",
                            Icons.nights_stay_outlined),
                        _buildPrayerItem("Subuh", _prayerTimes?.fajr ?? "--:--",
                            Icons.cloud_outlined),
                        _buildPrayerItem(
                            "Terbit",
                            _prayerTimes?.sunrise ?? "--:--",
                            Icons.wb_sunny_outlined),
                        _buildPrayerItem(
                            "Dhuha",
                            _prayerTimes?.dhuha ?? "--:--",
                            Icons.wb_twilight_outlined),
                        _buildPrayerItem(
                            "Zuhur",
                            _prayerTimes?.dhuhr ?? "--:--",
                            Icons.wb_sunny_rounded),
                        _buildPrayerItem("Ashar", _prayerTimes?.asr ?? "--:--",
                            Icons.cloud_queue_rounded),
                        _buildPrayerItem(
                            "Maghrib",
                            _prayerTimes?.maghrib ?? "--:--",
                            Icons.wb_twilight_rounded,
                            isNext: _nextPrayerName == "Maghrib"),
                        _buildPrayerItem("Isya'", _prayerTimes?.isha ?? "--:--",
                            Icons.nightlight_round_outlined),
                        const SizedBox(height: 100),
                      ]),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildHeader() {
    const String headerImgPath =
        "C:\\Users\\ROG STRIX\\.gemini\\antigravity\\brain\\13a1c6ab-6b82-43a6-bbd0-1e70b992a3fd\\mosque_silhouette_header_1772182672857.png";

    return SliverAppBar(
      expandedHeight: 320,
      pinned: true,
      backgroundColor: PremiumColor.primary,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          fit: StackFit.expand,
          children: [
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [PremiumColor.primary, PremiumColor.accent],
                ),
              ),
            ),
            if (File(headerImgPath).existsSync())
              Positioned(
                bottom: 0,
                left: 0,
                right: 0,
                child: Opacity(
                  opacity: 0.4,
                  child: Image.file(
                    File(headerImgPath),
                    fit: BoxFit.cover,
                    height: 160,
                  ),
                ),
              ),
            SafeArea(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  children: [
                    const SizedBox(height: 10),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.arrow_back_ios_new_rounded,
                              color: Colors.white, size: 20),
                          onPressed: () => Navigator.pop(context),
                        ),
                        Text(
                          "Jadwal Shalat",
                          style: GoogleFonts.plusJakartaSans(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        IconButton(
                            icon: const Icon(Icons.share_outlined,
                                color: Colors.white, size: 22),
                            onPressed: () {}),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.location_on,
                            color: Colors.white70, size: 14),
                        const SizedBox(width: 4),
                        Flexible(
                          child: Text(
                            _detailedLocation.isEmpty
                                ? "Lokasi mendeteksi..."
                                : _detailedLocation,
                            textAlign: TextAlign.center,
                            overflow: TextOverflow.ellipsis,
                            maxLines: 1,
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white70,
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 15),
                    Text(
                      "$_nextPrayerName $_nextPrayerTime WIB",
                      style: GoogleFonts.plusJakartaSans(
                        color: Colors.white,
                        fontSize: 32,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _formatDuration(_timeLeft),
                      style: GoogleFonts.jetBrainsMono(
                        color: Colors.white.withOpacity(0.9),
                        fontSize: 22,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const Spacer(),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        _buildSmallHeaderBtn(Icons.explore_outlined, "Kiblat",
                            onTap: () {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (context) => const QiblaScreen()));
                        }),
                        const SizedBox(width: 15),
                        _buildSmallHeaderBtn(
                            Icons.event_note_outlined, "Kalender", onTap: () {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (context) =>
                                      const CalendarScreen()));
                        }),
                      ],
                    ),
                    const SizedBox(height: 25),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSmallHeaderBtn(IconData icon, String label,
      {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.15),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.white.withOpacity(0.1), width: 1),
        ),
        child: Row(
          children: [
            Icon(icon, color: Colors.white, size: 15),
            const SizedBox(width: 6),
            Text(label,
                style: GoogleFonts.plusJakartaSans(
                    color: Colors.white,
                    fontSize: 13,
                    fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }

  Widget _buildDateSelector() {
    return GestureDetector(
      onHorizontalDragEnd: (details) {
        if (details.primaryVelocity! > 0) {
          _changeDate(-1);
        } else if (details.primaryVelocity! < 0) {
          _changeDate(1);
        }
      },
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
        padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
        decoration: BoxDecoration(
          color: const Color(0xFFF5F7F9),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 10,
              offset: const Offset(0, 4),
            )
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            _buildDateArrow(Icons.chevron_left_rounded, () => _changeDate(-1)),
            Expanded(
              child: GestureDetector(
                onTap: _selectDateFromPicker,
                child: Column(
                  children: [
                    Text(
                      DateFormat('dd MMMM yyyy', 'id_ID').format(_selectedDate),
                      style: GoogleFonts.plusJakartaSans(
                          color: Colors.black87,
                          fontWeight: FontWeight.bold,
                          fontSize: 16),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      "${_prayerTimes?.hijriDate ?? ""} ${_prayerTimes?.hijriMonth ?? ""} 1447",
                      style: GoogleFonts.plusJakartaSans(
                          color: PremiumColor.primary.withOpacity(0.6),
                          fontSize: 13,
                          fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
              ),
            ),
            _buildDateArrow(Icons.chevron_right_rounded, () => _changeDate(1)),
          ],
        ),
      ),
    );
  }

  Widget _buildDateArrow(IconData icon, VoidCallback onTap) {
    return IconButton(
      onPressed: () {
        HapticFeedback.lightImpact();
        onTap();
      },
      icon: Icon(icon, color: PremiumColor.primary, size: 28),
      padding: EdgeInsets.zero,
      constraints: const BoxConstraints(),
    );
  }

  void _changeDate(int days) {
    setState(() {
      _selectedDate = _selectedDate.add(Duration(days: days));
      _initData();
    });
  }

  Future<void> _selectDateFromPicker() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) {
        return Theme(
          data: ThemeData.light().copyWith(
            colorScheme: const ColorScheme.light(
              primary: PremiumColor.primary,
              onPrimary: Colors.white,
              onSurface: Colors.black87,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
        _initData();
      });
    }
  }

  Widget _buildPrayerItem(String label, String time, IconData icon,
      {bool isNext = false}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 2),
      decoration: BoxDecoration(
        color:
            isNext ? PremiumColor.primary.withOpacity(0.1) : Colors.transparent,
        borderRadius: BorderRadius.circular(12),
      ),
      child: ListTile(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => AdzanSelectionScreen(prayerName: label),
            ),
          );
        },
        leading: Icon(icon,
            color: isNext ? PremiumColor.primary : Colors.black38, size: 22),
        title: Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            color: Colors.black87,
            fontSize: 15,
            fontWeight: isNext ? FontWeight.bold : FontWeight.w500,
          ),
        ),
        trailing: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              time,
              style: GoogleFonts.plusJakartaSans(
                color: Colors.black87,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(width: 12),
            Icon(
              isNext
                  ? Icons.volume_up_rounded
                  : Icons.notifications_none_rounded,
              color: isNext ? PremiumColor.primary : Colors.black12,
              size: 20,
            ),
          ],
        ),
      ),
    );
  }
}
