import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../models/activity_log.dart';
import '../models/user_profile.dart';
import 'api_client.dart';

class ProfileService {
  final ApiClient _apiClient = ApiClient();

  Future<List<ActivityLog>> getActivities() async {
    try {
      final response = await _apiClient.dio.get('/profile/activities');
      if (response.statusCode == 200 && response.data != null) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => ActivityLog.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching activities: $e');
      return [];
    }
  }

  Future<UserProfile?> getProfile() async {
    try {
      final response = await _apiClient.dio.get('/profile');

      if (response.statusCode == 200 && response.data != null) {
        final data = response.data['data'];
        if (data != null && data['user'] != null) {
          return UserProfile.fromJson(data['user']);
        }
        debugPrint('User data not found in response');
        return null;
      }
      return null;
    } on DioException catch (e) {
      debugPrint('Failed to load profile details: ${e.message}');
      return null;
    } catch (e) {
      debugPrint('Unexpected error loading profile: $e');
      return null;
    }
  }

  Future<Map<String, dynamic>?> getHunterProfile(int id) async {
    try {
      final response = await _apiClient.dio.get('/profile/$id');

      if (response.statusCode == 200 && response.data != null) {
        return response.data['data'];
      }
      return null;
    } catch (e) {
      debugPrint('Error loading hunter profile: $e');
      return null;
    }
  }

  /// Alias for getProfile to match HabitScreen usage
  Future<UserProfile?> getUserProfile() => getProfile();

  Future<bool> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.dio.post('/profile/update', data: data);
      return response.statusCode == 200 && response.data['success'] == true;
    } on DioException catch (e) {
      debugPrint('Update profile error: ${e.message}');
      return false;
    }
  }

  Future<bool> updatePassword(String oldPassword, String newPassword) async {
    try {
      final response =
          await _apiClient.dio.post('/profile/change-password', data: {
        'old_password': oldPassword,
        'new_password': newPassword,
        'new_password_confirmation': newPassword,
      });
      return response.statusCode == 200 && response.data['success'] == true;
    } on DioException catch (e) {
      debugPrint('Change password error: ${e.message}');
      return false;
    }
  }

  /// Apply health penalty for missed prayers
  Future<void> applyPenalty(
      {required int hpLoss, required String reason}) async {
    try {
      debugPrint('Sending penalty request: -$hpLoss HP, Reason: $reason');
      final response = await _apiClient.dio.post('/profile/penalty', data: {
        'hp_loss': hpLoss,
        'reason': reason,
      });
      debugPrint('Penalty response: ${response.data}');
    } on DioException catch (e) {
      debugPrint('Penalty error: ${e.message}');
      debugPrint('Penalty error data: ${e.response?.data}');
    }
  }

  /// One-time check for historical missed prayers
  Future<Map<String, dynamic>?> checkHistoricalPenalty() async {
    try {
      debugPrint('Triggering historical penalty check...');
      final response = await _apiClient.dio.post('/profile/history-check');
      debugPrint('Historical penalty response: ${response.data}');
      if (response.data is Map<String, dynamic>) {
        return response.data;
      }
      return null;
    } on DioException catch (e) {
      debugPrint('Historical penalty error: ${e.message}');
      debugPrint('Historical penalty error data: ${e.response?.data}');
      return null;
    } catch (e) {
      debugPrint('Unexpected historical penalty error: $e');
      return null;
    }
  }

  Future<bool> toggleMenstruation() async {
    try {
      final response = await _apiClient.dio.post('/toggle-menstruation');
      return response.statusCode == 200 && response.data['success'] == true;
    } on DioException catch (e) {
      debugPrint('Toggle menstruation error: ${e.message}');
      return false;
    } catch (e) {
      debugPrint('Unexpected error toggling menstruation: $e');
      return false;
    }
  }
}
