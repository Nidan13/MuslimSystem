import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../models/habit.dart';
import 'api_client.dart';

class HabitService {
  final ApiClient _apiClient = ApiClient();

  Future<List<Habit>> getHabits() async {
    try {
      final response = await _apiClient.dio.get('/habits');
      if (response.data['success'] == true) {
        final List data = response.data['data'] ?? [];
        return data.map((json) => Habit.fromJson(json)).toList();
      }
      return [];
    } on DioException catch (e) {
      debugPrint('Error fetching habits: ${e.message}');
      return [];
    }
  }

  Future<Habit?> createHabit({
    required String title,
    String? notes,
    required String difficulty,
    required bool isPositive,
    required bool isNegative,
    required String frequency,
  }) async {
    try {
      final response = await _apiClient.dio.post('/habits', data: {
        'title': title,
        'notes': notes,
        'difficulty': difficulty,
        'is_positive': isPositive,
        'is_negative': isNegative,
        'frequency': frequency,
      });

      if (response.data['success'] == true) {
        return Habit.fromJson(response.data['data']);
      }
      return null;
    } on DioException catch (e) {
      debugPrint('Error creating habit: ${e.message}');
      return null;
    }
  }

  Future<Map<String, dynamic>?> scoreHabit(
      int habitId, String direction) async {
    try {
      final response =
          await _apiClient.dio.post('/habits/$habitId/score', data: {
        'direction': direction,
      });

      if (response.data['success'] == true) {
        return response.data['data'];
      }
      return null;
    } on DioException catch (e) {
      debugPrint('Error scoring habit: ${e.message}');
      return null;
    }
  }

  Future<bool> deleteHabit(int habitId) async {
    try {
      final response = await _apiClient.dio.delete('/habits/$habitId');
      return response.data['success'] == true;
    } on DioException catch (e) {
      debugPrint('Error deleting habit: ${e.message}');
      return false;
    }
  }
}
