import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:percent_indicator/circular_percent_indicator.dart';
import '../../models/prayer_times.dart';
import '../../theme/premium_color.dart';

class DetailedPrayerCard extends StatefulWidget {
  final PrayerTimes? prayerTimes;
  final String? locationName;
  final String? hijriDate;
  final String? gregorianDate;

  const DetailedPrayerCard({
    Key? key,
    this.prayerTimes,
    this.locationName,
    this.hijriDate,
    this.gregorianDate,
  }) : super(key: key);

  @override
  State<DetailedPrayerCard> createState() => _DetailedPrayerCardState();
}

class _DetailedPrayerCardState extends State<DetailedPrayerCard> {
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) setState(() {});
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.prayerTimes == null) {
      return _buildLoadingState();
    }

    final nextPrayer = widget.prayerTimes!.getNextPrayer();
    final nextName = _mapEnglishToIndo(nextPrayer['name'] ?? 'Fajr');
    final nextTime = nextPrayer['time'] ?? '00:00';
    final timeUntil = widget.prayerTimes!.getTimeUntilNext();

    // Smooth progress calculation
    double progress = _calculateProgress(nextPrayer);

    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            PremiumColor.primary,
            PremiumColor.primary
                .withRed(PremiumColor.primary.red + 15)
                .withBlue(PremiumColor.primary.blue + 25),
          ],
        ),
        borderRadius: BorderRadius.circular(36),
        boxShadow: [
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.25),
            blurRadius: 25,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: Stack(
        children: [
          // Elegant Pattern Overlay
          Positioned(
            right: -40,
            bottom: -30,
            child: Opacity(
              opacity: 0.08,
              child: Icon(
                Icons.mosque,
                size: 280,
                color: Colors.white,
              ),
            ),
          ),

          Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Info Row (Location & Dates)
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Location Info
                    Flexible(
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.location_on,
                                color: PremiumColor.highlight, size: 12),
                            const SizedBox(width: 4),
                            Flexible(
                              child: Text(
                                widget.locationName ?? "Mendeteksi...",
                                style: GoogleFonts.plusJakartaSans(
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.w800,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),

                    // Date Info
                    Flexible(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            widget.hijriDate ?? "-",
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white,
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                            ),
                            overflow: TextOverflow.ellipsis,
                            maxLines: 1,
                          ),
                          const SizedBox(height: 2),
                          Text(
                            widget.gregorianDate ?? "-",
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white.withOpacity(0.6),
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                            ),
                            overflow: TextOverflow.ellipsis,
                            maxLines: 1,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Berlanjut Ke",
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white.withOpacity(0.6),
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                              letterSpacing: 0.5,
                            ),
                          ),
                          const SizedBox(height: 4),
                          FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Text(
                              nextName,
                              style: GoogleFonts.playfairDisplay(
                                color: Colors.white,
                                fontSize: 44,
                                fontWeight: FontWeight.w900,
                                height: 1.0,
                              ),
                            ),
                          ),
                          const SizedBox(height: 20),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: PremiumColor.highlight,
                              borderRadius: BorderRadius.circular(14),
                              boxShadow: [
                                BoxShadow(
                                  color:
                                      PremiumColor.highlight.withOpacity(0.3),
                                  blurRadius: 8,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(Icons.access_time_filled,
                                    color: Colors.white, size: 16),
                                const SizedBox(width: 6),
                                Text(
                                  _formatToAMPM(nextTime),
                                  style: GoogleFonts.plusJakartaSans(
                                    color: Colors.white,
                                    fontSize: 14,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),

                    // Circular Progress
                    SizedBox(
                      height: 110,
                      width: 110,
                      child: CircularPercentIndicator(
                        radius: 54.0,
                        lineWidth: 10.0,
                        percent: progress,
                        center: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              "Sisa",
                              style: GoogleFonts.plusJakartaSans(
                                color: Colors.white.withOpacity(0.7),
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            Text(
                              _formatDurationCompact(timeUntil),
                              style: GoogleFonts.plusJakartaSans(
                                color: Colors.white,
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                height: 1.2,
                              ),
                            ),
                          ],
                        ),
                        circularStrokeCap: CircularStrokeCap.round,
                        backgroundColor: Colors.white.withOpacity(0.12),
                        progressColor: PremiumColor.highlight,
                        animation: true,
                        animateFromLastPercent: true,
                        curve: Curves.easeOutCubic,
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 32),
                Container(
                  height: 1,
                  color: Colors.white.withOpacity(0.1),
                ),
                const SizedBox(height: 18),

                // Horizontal list of prayers
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                        child: _buildBottomTime(
                            "Subuh", widget.prayerTimes!.fajr,
                            isActive: nextName == "Subuh")),
                    Expanded(
                        child: _buildBottomTime(
                            "Dzuhur", widget.prayerTimes!.dhuhr,
                            isActive: nextName == "Dzuhur")),
                    Expanded(
                        child: _buildBottomTime(
                            "Ashar", widget.prayerTimes!.asr,
                            isActive: nextName == "Ashar")),
                    Expanded(
                        child: _buildBottomTime(
                            "Maghrib", widget.prayerTimes!.maghrib,
                            isActive: nextName == "Maghrib")),
                    Expanded(
                        child: _buildBottomTime(
                            "Isya", widget.prayerTimes!.isha,
                            isActive: nextName == "Isya")),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBottomTime(String label, String time, {bool isActive = false}) {
    return Column(
      children: [
        FittedBox(
          fit: BoxFit.scaleDown,
          child: Text(
            label,
            style: GoogleFonts.plusJakartaSans(
              color: isActive ? Colors.white : Colors.white.withOpacity(0.5),
              fontSize: 11,
              fontWeight: isActive ? FontWeight.w800 : FontWeight.w600,
              letterSpacing: 0.2,
            ),
          ),
        ),
        const SizedBox(height: 4),
        FittedBox(
          fit: BoxFit.scaleDown,
          child: Text(
            time,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.white,
              fontSize: 14,
              fontWeight: isActive ? FontWeight.w900 : FontWeight.w700,
            ),
          ),
        ),
        const SizedBox(height: 4),
        AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          width: isActive ? 6 : 0,
          height: 6,
          decoration: const BoxDecoration(
            color: PremiumColor.highlight,
            shape: BoxShape.circle,
          ),
        ),
      ],
    );
  }

  double _calculateProgress(Map<String, String?> nextPrayer) {
    return 0.7; // Visual placeholder
  }

  String _mapEnglishToIndo(String name) {
    switch (name.toLowerCase()) {
      case 'fajr':
        return 'Subuh';
      case 'dhuhr':
        return 'Dzuhur';
      case 'asr':
        return 'Ashar';
      case 'maghrib':
        return 'Maghrib';
      case 'isha':
        return 'Isya';
      default:
        return name;
    }
  }

  String _formatToAMPM(String time24) {
    try {
      final parts = time24.split(':');
      final hour = int.parse(parts[0]);
      final minute = int.parse(parts[1]);
      final dt = DateTime(0, 0, 0, hour, minute);
      return DateFormat('HH:mm').format(dt);
    } catch (e) {
      return time24;
    }
  }

  String _formatDurationCompact(Duration d) {
    if (d.inHours > 0) {
      return "${d.inHours}j ${d.inMinutes.remainder(60)}m";
    }
    return "${d.inMinutes}m";
  }

  Widget _buildLoadingState() {
    return Container(
      height: 220,
      width: double.infinity,
      decoration: BoxDecoration(
        color: PremiumColor.primary.withOpacity(0.1),
        borderRadius: BorderRadius.circular(32),
      ),
      child: const Center(
          child: CircularProgressIndicator(color: PremiumColor.primary)),
    );
  }
}
