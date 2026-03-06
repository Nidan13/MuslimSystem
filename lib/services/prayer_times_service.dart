import 'dart:convert';
import 'package:adhan/adhan.dart' as adhan;
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:hijri/hijri_calendar.dart';
import '../models/prayer_times.dart';

class PrayerTimesService {
  static const String _cacheKey = 'cached_prayer_times_detailed';
  static const String _cacheDateKey = 'cached_prayer_times_date';

  /// Fetch prayer times using local calculation for high precision
  Future<PrayerTimes> getPrayerTimes({
    required double latitude,
    required double longitude,
    DateTime? date,
  }) async {
    try {
      final targetDate = date ?? DateTime.now();

      // 1. Coordinates
      final coordinates = adhan.Coordinates(latitude, longitude);

      // 2. Parameters for Indonesia (KEMENAG)
      // Standard: Subuh 20°, Isha 18°
      final params = adhan.CalculationParameters(fajrAngle: 20, ishaAngle: 18);
      params.madhab = adhan.Madhab.shafi;

      // 3. Calculation
      final pt = adhan.PrayerTimes(
        coordinates,
        adhan.DateComponents.from(targetDate),
        params,
      );

      // 4. Hijri Date
      final hijri = HijriCalendar.fromDate(targetDate);

      // 5. Create Model
      final prayerTimes = PrayerTimes(
        imsak: DateFormat('HH:mm')
            .format(pt.fajr.subtract(const Duration(minutes: 10))),
        fajr: DateFormat('HH:mm').format(pt.fajr),
        sunrise: DateFormat('HH:mm').format(pt.sunrise),
        dhuhr: DateFormat('HH:mm').format(pt.dhuhr),
        asr: DateFormat('HH:mm').format(pt.asr),
        maghrib: DateFormat('HH:mm').format(pt.maghrib),
        isha: DateFormat('HH:mm').format(pt.isha),
        dhuha: DateFormat('HH:mm').format(
            pt.sunrise.add(const Duration(minutes: 20))), // Approximation
        date: DateFormat('dd MMMM yyyy', 'id_ID').format(targetDate),
        hijriDate: '${hijri.hDay}',
        hijriMonth: hijri.longMonthName,
      );

      // Cache the result
      await cachePrayerTimes(prayerTimes);

      return prayerTimes;
    } catch (e) {
      print('Error calculating prayer times: $e');
      throw Exception('Failed to calculate prayer times locally');
    }
  }

  /// Cache prayer times to SharedPreferences
  Future<void> cachePrayerTimes(PrayerTimes times) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final jsonString = jsonEncode(times.toJson());
      await prefs.setString(_cacheKey, jsonString);
      await prefs.setString(_cacheDateKey, DateTime.now().toIso8601String());
    } catch (e) {
      print('Failed to cache prayer times: $e');
    }
  }

  /// Get cached prayer times (only if from today)
  Future<PrayerTimes?> getCachedPrayerTimes() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final jsonString = prefs.getString(_cacheKey);
      final cacheDateString = prefs.getString(_cacheDateKey);

      if (jsonString == null || cacheDateString == null) {
        return null;
      }

      // Check if cache is from today
      final cacheDate = DateTime.parse(cacheDateString);
      final now = DateTime.now();
      final isToday = cacheDate.year == now.year &&
          cacheDate.month == now.month &&
          cacheDate.day == now.day;

      if (!isToday) {
        return null; // Cache expired
      }

      final json = jsonDecode(jsonString) as Map<String, dynamic>;
      return PrayerTimes(
        imsak: json['imsak'] ?? '00:00',
        fajr: json['fajr'] ?? '00:00',
        sunrise: json['sunrise'] ?? '00:00',
        dhuhr: json['dhuhr'] ?? '00:00',
        asr: json['asr'] ?? '00:00',
        maghrib: json['maghrib'] ?? '00:00',
        isha: json['isha'] ?? '00:00',
        date: json['date'] ?? '',
        hijriDate: json['hijriDate'] ?? '',
        hijriMonth: json['hijriMonth'] ?? '',
        dhuha: json['dhuha'] ?? '00:00',
      );
    } catch (e) {
      print('Failed to get cached prayer times: $e');
      return null;
    }
  }
}
