import 'package:dio/dio.dart';
import '../models/daily_task.dart';
import 'api_client.dart';

class DailyTaskService {
  final Dio _dio = ApiClient().client;

  /// Get all daily tasks
  Future<DailyTaskResponse> getDailyTasks({String? date}) async {
    try {
      final response = await _dio.get('/daily-tasks', queryParameters: {
        if (date != null) 'date': date,
      });

      print("DEBUG: Daily Tasks Response Data: ${response.data}"); // DEBUG LOG

      final data = response.data['data'];
      if (data == null) {
        print("DEBUG: Data is null");
        return DailyTaskResponse(tasks: [], summary: null);
      }

      try {
        return DailyTaskResponse.fromJson(data);
      } catch (e, stack) {
        print("DEBUG: Error parsing DailyTaskResponse: $e");
        print(stack);
        rethrow;
      }
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Mark task as complete
  Future<Map<String, dynamic>> completeTask(int taskId) async {
    try {
      final response = await _dio.post('/daily-tasks/$taskId/complete');
      return {
        'message': response.data['message'],
        'data': response.data['data']
      };
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Mark task as uncomplete
  Future<Map<String, dynamic>> uncompleteTask(int taskId) async {
    try {
      final response = await _dio.post('/daily-tasks/$taskId/uncomplete');
      return {
        'message': response.data['message'],
        'data': response.data['data']
      };
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Create custom task
  Future<DailyTask> createCustomTask({
    required String name,
    required int soulPoints,
    String? description,
    String? icon,
  }) async {
    try {
      final response = await _dio.post('/daily-tasks', data: {
        'name': name,
        'soul_points': soulPoints,
        'description': description,
        'icon': icon,
      });
      return DailyTask.fromJson(response.data['data']['task']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Delete a task
  Future<bool> deleteTask(int taskId) async {
    try {
      final response = await _dio.delete('/daily-tasks/$taskId');
      return response.statusCode == 200 || response.statusCode == 204;
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
