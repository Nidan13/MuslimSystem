import 'dart:convert';
import 'package:dio/dio.dart';
import '../models/quest.dart';
import 'api_client.dart';

class QuestService {
  final Dio _dio = ApiClient().client;

  /// Get quests by type (default: daily) and optional date
  Future<List<Quest>> getQuests({String type = 'daily', String? date}) async {
    try {
      final response = await _dio.get('/quests', queryParameters: {
        'type': type,
        if (date != null) 'date': date,
      });
      print('Quest API Response [$type]: ${response.data}');
      var responseData = response.data;
      if (responseData is String) {
        responseData = jsonDecode(responseData);
      }

      final List data = responseData['data'] ?? [];

      return data.map((e) {
        return Quest.fromJson(e);
      }).toList();
    } on DioException catch (e) {
      throw _handleError(e);
    } catch (e) {
      throw 'Parsing error: $e';
    }
  }

  /// Accept a quest
  Future<void> acceptQuest(int questId) async {
    print('QuestService: Sending POST request to accept quest ID: $questId');
    try {
      final response = await _dio.post('/quests/$questId/accept');
      print('QuestService: Accept Quest Response: ${response.data}');
    } on DioException catch (e) {
      print(
          'QuestService: Accept Quest Error (Dio): ${e.message} - ${e.response?.data}');
      throw _handleError(e);
    } catch (e) {
      print('QuestService: Accept Quest Error (General): $e');
      rethrow;
    }
  }

  /// Update quest progress
  Future<void> updateProgress(int questId, String key, int increment) async {
    try {
      await _dio.post('/quests/$questId/progress', data: {
        'progress_key': key,
        'increment': increment,
      });
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Complete quest and claim reward
  Future<Map<String, dynamic>> completeQuest(int questId) async {
    try {
      final response = await _dio.post('/quests/$questId/complete');
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Handle Dio errors
  String _handleError(DioException error) {
    if (error.response != null) {
      final data = error.response!.data;
      if (data is Map<String, dynamic> && data.containsKey('message')) {
        return data['message'] as String;
      }
      return 'Server error: ${error.response!.statusCode}';
    }
    return 'Connection error. Please try again.';
  }
}
