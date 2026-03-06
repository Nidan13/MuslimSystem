import 'package:dio/dio.dart';
import '../models/prayer_log.dart';
import 'api_client.dart';
import 'package:intl/intl.dart';

class PrayerService {
  final Dio _dio = ApiClient().client;

  DateTime get todayLogical {
    final now = DateTime.now();
    if (now.hour < 4) {
      return now.subtract(const Duration(days: 1));
    }
    return now;
  }

  Future<PrayerResponse> getPrayerLogs({DateTime? date}) async {
    try {
      final dateStr = DateFormat('yyyy-MM-dd').format(date ?? todayLogical);
      final response =
          await _dio.get('/prayers', queryParameters: {'date': dateStr});
      return PrayerResponse.fromJson(response.data);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Future<void> syncSchedule(
      DateTime date, Map<String, String> schedules) async {
    try {
      final dateStr = DateFormat('yyyy-MM-dd').format(date);
      await _dio.post('/prayers/sync-schedule', data: {
        'date': dateStr,
        'schedules': schedules,
      });
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Future<Map<String, dynamic>> completePrayer(String prayerKey,
      {DateTime? date}) async {
    try {
      final dateStr = DateFormat('yyyy-MM-dd').format(date ?? todayLogical);
      final response = await _dio.post('/prayers/complete', data: {
        'prayer_name': prayerKey,
        'date': dateStr,
      });
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  Future<Map<String, dynamic>> uncompletePrayer(String prayerKey,
      {DateTime? date}) async {
    try {
      final dateStr = DateFormat('yyyy-MM-dd').format(date ?? todayLogical);
      final response = await _dio.post('/prayers/uncomplete', data: {
        'prayer_name': prayerKey,
        'date': dateStr,
      });
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  String _handleError(DioException e) {
    if (e.response != null) {
      final data = e.response!.data;
      if (data is Map<String, dynamic> && data.containsKey('message')) {
        return data['message'] as String;
      }
      return 'Server error: ${e.response!.statusCode}';
    }
    return 'Connection error. Please try again.';
  }
}
