import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../models/todo.dart';
import 'api_client.dart';

class TodoService {
  final ApiClient _apiClient = ApiClient();

  Future<Map<String, List<Todo>>> getTodos() async {
    try {
      final response = await _apiClient.dio.get('/todos');
      if (response.data['success'] == true) {
        final Map<String, dynamic> data = response.data['data'];
        final List activeJson = data['active'] ?? [];
        final List completedJson = data['completed'] ?? [];

        return {
          'active': activeJson.map((json) => Todo.fromJson(json)).toList(),
          'completed':
              completedJson.map((json) => Todo.fromJson(json)).toList(),
        };
      }
      return {'active': [], 'completed': []};
    } on DioException catch (e) {
      debugPrint('Error fetching todos: ${e.message}');
      return {'active': [], 'completed': []};
    }
  }

  Future<Todo?> createTodo({
    required String title,
    String? notes,
    required String difficulty,
    DateTime? dueDate,
    List<ChecklistItem>? checklist,
  }) async {
    try {
      final response = await _apiClient.dio.post('/todos', data: {
        'title': title,
        'notes': notes,
        'difficulty': difficulty,
        'due_date': dueDate?.toIso8601String(),
        'checklist': checklist?.map((e) => e.toJson()).toList(),
      });

      if (response.data['success'] == true) {
        return Todo.fromJson(response.data['data']);
      }
      return null;
    } on DioException catch (e) {
      debugPrint('Error creating todo: ${e.message}');
      return null;
    }
  }

  Future<Map<String, dynamic>?> completeTodo(int id) async {
    try {
      final response = await _apiClient.dio.post('/todos/$id/complete');
      if (response.data['success'] == true) {
        return response.data['data'];
      }
      return null;
    } on DioException catch (e) {
      debugPrint('Error completing todo: ${e.message}');
      return null;
    }
  }

  Future<bool> deleteTodo(int id) async {
    try {
      final response = await _apiClient.dio.delete('/todos/$id');
      return response.data['success'] == true;
    } on DioException catch (e) {
      debugPrint('Error deleting todo: ${e.message}');
      return false;
    }
  }
}
