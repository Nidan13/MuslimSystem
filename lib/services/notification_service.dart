import 'api_client.dart';
import '../models/notification.dart';

class NotificationService {
  final ApiClient _apiClient = ApiClient();

  Future<List<NotificationModel>> getNotifications() async {
    try {
      final response = await _apiClient.client.get('/notifications');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => NotificationModel.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<int> getUnreadCount() async {
    try {
      final response =
          await _apiClient.client.get('/notifications/unread-count');
      if (response.statusCode == 200) {
        return response.data['data']['count'] ?? 0;
      }
      return 0;
    } catch (e) {
      return 0;
    }
  }

  Future<bool> markAsRead(int id) async {
    try {
      final response =
          await _apiClient.client.post('/notifications/mark-read/$id');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> markAllAsRead() async {
    try {
      final response =
          await _apiClient.client.post('/notifications/mark-all-read');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}
