import 'package:flutter/foundation.dart';

class PrayerTimes {
  final String imsak;
  final String fajr;
  final String sunrise;
  final String dhuhr;
  final String asr;
  final String maghrib;
  final String isha;
  final String dhuha;
  final String date;
  final String hijriDate;
  final String hijriMonth;

  PrayerTimes({
    required this.imsak,
    required this.fajr,
    required this.sunrise,
    required this.dhuhr,
    required this.asr,
    required this.maghrib,
    required this.isha,
    required this.dhuha,
    required this.date,
    required this.hijriDate,
    required this.hijriMonth,
  });

  factory PrayerTimes.fromJson(Map<String, dynamic> json) {
    try {
      final timings = (json['timings'] as Map<String, dynamic>?) ?? {};
      final date = (json['date'] as Map<String, dynamic>?) ?? {};
      final hijri = (date['hijri'] as Map<String, dynamic>?) ?? {};
      final hijriMonth = (hijri['month'] as Map<String, dynamic>?) ?? {};

      return PrayerTimes(
        imsak: _cleanTime((timings['Imsak'] as String?) ?? '00:00'),
        fajr: _cleanTime((timings['Fajr'] as String?) ?? '00:00'),
        sunrise: _cleanTime((timings['Sunrise'] as String?) ?? '00:00'),
        dhuhr: _cleanTime((timings['Dhuhr'] as String?) ?? '00:00'),
        asr: _cleanTime((timings['Asr'] as String?) ?? '00:00'),
        maghrib: _cleanTime((timings['Maghrib'] as String?) ?? '00:00'),
        isha: _cleanTime((timings['Isha'] as String?) ?? '00:00'),
        dhuha: _cleanTime((timings['Sunrise'] as String?) ??
            '00:00'), // Placeholder for calculation
        date: (date['readable'] as String?) ?? '',
        hijriDate: (hijri['date'] as String?) ?? '',
        hijriMonth: (hijriMonth['en'] as String?) ?? '',
      );
    } catch (e) {
      if (kDebugMode) {
        print('Error parsing PrayerTimes JSON: $e');
      }
      return PrayerTimes(
        imsak: '00:00',
        fajr: '00:00',
        sunrise: '00:00',
        dhuhr: '00:00',
        asr: '00:00',
        maghrib: '00:00',
        isha: '00:00',
        date: '',
        hijriDate: '',
        hijriMonth: '',
        dhuha: '00:00',
      );
    }
  }

  Map<String, dynamic> toJson() {
    return {
      'imsak': imsak,
      'fajr': fajr,
      'sunrise': sunrise,
      'dhuhr': dhuhr,
      'asr': asr,
      'maghrib': maghrib,
      'isha': isha,
      'dhuha': dhuha,
      'date': date,
      'hijriDate': hijriDate,
      'hijriMonth': hijriMonth,
    };
  }

  // Remove timezone suffix from time (e.g., "04:45 (WIB)" -> "04:45")
  static String _cleanTime(String time) {
    return time.split(' ').first;
  }

  // Get next prayer name and time
  Map<String, String> getNextPrayer() {
    final now = DateTime.now();
    final prayers = [
      {'name': 'Fajr', 'time': fajr},
      {'name': 'Dhuhr', 'time': dhuhr},
      {'name': 'Asr', 'time': asr},
      {'name': 'Maghrib', 'time': maghrib},
      {'name': 'Isha', 'time': isha},
    ];

    for (var prayer in prayers) {
      final timeStr = prayer['time'];
      if (timeStr == null) continue;

      final prayerTime = _parseTime(timeStr);
      if (now.isBefore(prayerTime)) {
        return prayer;
      }
    }

    // If all prayers passed, next is Fajr tomorrow
    return {'name': 'Fajr', 'time': fajr};
  }

  /// Get DateTime for a specific prayer name (case-insensitive)
  DateTime? getPrayerTimeByName(String prayerName) {
    final prayerLower = prayerName.toLowerCase();
    if (prayerLower.contains('fajr') || prayerLower.contains('subuh')) {
      return _parseTime(fajr);
    }
    if (prayerLower.contains('dhuhr') || prayerLower.contains('dzuhur')) {
      return _parseTime(dhuhr);
    }
    if (prayerLower.contains('asr') || prayerLower.contains('ashar')) {
      return _parseTime(asr);
    }
    if (prayerLower.contains('maghrib')) return _parseTime(maghrib);
    if (prayerLower.contains('isha') || prayerLower.contains('isya')) {
      return _parseTime(isha);
    }
    return null;
  }

  // Get duration until next prayer
  Duration getTimeUntilNext() {
    final now = DateTime.now();
    final nextPrayer = getNextPrayer();
    final timeStr = nextPrayer['time'];

    if (timeStr == null) return Duration.zero;

    final prayerTime = _parseTime(timeStr);

    if (now.isAfter(prayerTime)) {
      // Next prayer is tomorrow
      final tomorrow = prayerTime.add(const Duration(days: 1));
      return tomorrow.difference(now);
    }

    return prayerTime.difference(now);
  }

  DateTime _parseTime(String time) {
    try {
      // Clean time string: remove anything that's not part of the time (e.g., "(WIB)")
      final cleanedTime = time.split(' ').first;
      final parts = cleanedTime.split(':');
      final hour = int.parse(parts[0]);
      final minute = int.parse(parts[1]);
      final now = DateTime.now();
      return DateTime(now.year, now.month, now.day, hour, minute);
    } catch (e) {
      if (kDebugMode) {
        print('Error parsing time "$time": $e');
      }
      // Return current time as fallback to prevent crash
      return DateTime.now();
    }
  }
}
