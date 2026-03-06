import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../models/prayer_times.dart';

class PrayerTimesCard extends StatefulWidget {
  final PrayerTimes? prayerTimes;
  final VoidCallback? onRefresh;

  const PrayerTimesCard({
    Key? key,
    this.prayerTimes,
    this.onRefresh,
  }) : super(key: key);

  @override
  State<PrayerTimesCard> createState() => _PrayerTimesCardState();
}

class _PrayerTimesCardState extends State<PrayerTimesCard> {
  Timer? _timer;
  Duration? _timeUntilNext;
  String? _nextPrayerName;

  @override
  void initState() {
    super.initState();
    _updateCountdown();
    _timer =
        Timer.periodic(const Duration(seconds: 1), (_) => _updateCountdown());
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  void _updateCountdown() {
    if (widget.prayerTimes != null && mounted) {
      setState(() {
        _timeUntilNext = widget.prayerTimes!.getTimeUntilNext();
        _nextPrayerName = widget.prayerTimes!.getNextPrayer()['name'];
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (widget.prayerTimes == null) {
      return _buildLoadingCard();
    }

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0E5F71), Color(0xFF1A3A52)],
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0E5F71).withOpacity(0.3),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header with Hijri date
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'WAKTU SHOLAT',
                    style: GoogleFonts.plusJakartaSans(
                      color: const Color(0xFF4ECDC4),
                      fontSize: 12,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 1.5,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${widget.prayerTimes!.hijriDate} ${widget.prayerTimes!.hijriMonth}',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.white70,
                      fontSize: 11,
                    ),
                  ),
                ],
              ),
              if (widget.onRefresh != null)
                IconButton(
                  icon: const Icon(Icons.refresh,
                      color: Color(0xFF4ECDC4), size: 20),
                  onPressed: widget.onRefresh,
                ),
            ],
          ),
          const SizedBox(height: 16),

          // Countdown to next prayer
          if (_nextPrayerName != null && _timeUntilNext != null)
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Next: $_nextPrayerName',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.white,
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  Text(
                    _formatDuration(_timeUntilNext!),
                    style: GoogleFonts.plusJakartaSans(
                      color: const Color(0xFF4ECDC4),
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              ),
            ),
          const SizedBox(height: 16),

          // Prayer times grid
          GridView.count(
            crossAxisCount: 3,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            childAspectRatio: 1.5,
            mainAxisSpacing: 12,
            crossAxisSpacing: 12,
            children: [
              _buildPrayerTime('Fajr', widget.prayerTimes!.fajr),
              _buildPrayerTime('Dhuhr', widget.prayerTimes!.dhuhr),
              _buildPrayerTime('Asr', widget.prayerTimes!.asr),
              _buildPrayerTime('Maghrib', widget.prayerTimes!.maghrib),
              _buildPrayerTime('Isha', widget.prayerTimes!.isha),
              _buildPrayerTime('Sunrise', widget.prayerTimes!.sunrise,
                  isSpecial: true),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPrayerTime(String name, String time, {bool isSpecial = false}) {
    final isNext = name == _nextPrayerName;

    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: isNext
            ? const Color(0xFF4ECDC4).withOpacity(0.2)
            : Colors.white.withOpacity(0.05),
        borderRadius: BorderRadius.circular(12),
        border: isNext
            ? Border.all(color: const Color(0xFF4ECDC4), width: 2)
            : null,
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            name,
            style: GoogleFonts.plusJakartaSans(
              color: isSpecial
                  ? Colors.white60
                  : isNext
                      ? const Color(0xFF4ECDC4)
                      : Colors.white,
              fontSize: 11,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            time,
            style: GoogleFonts.plusJakartaSans(
              color: isSpecial
                  ? Colors.white60
                  : isNext
                      ? Colors.white
                      : Colors.white70,
              fontSize: 14,
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLoadingCard() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0E5F71), Color(0xFF1A3A52)],
        ),
        borderRadius: BorderRadius.circular(20),
      ),
      child: const Center(
        child: CircularProgressIndicator(color: Color(0xFF4ECDC4)),
      ),
    );
  }

  String _formatDuration(Duration duration) {
    final hours = duration.inHours;
    final minutes = duration.inMinutes.remainder(60);
    final seconds = duration.inSeconds.remainder(60);

    if (hours > 0) {
      return '${hours}h ${minutes}m';
    } else {
      return '${minutes}m ${seconds}s';
    }
  }
}
