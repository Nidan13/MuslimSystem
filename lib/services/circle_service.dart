import 'package:dio/dio.dart';
import 'api_client.dart';
import '../models/circle.dart';
import '../models/dungeon.dart';

class CircleService {
  final ApiClient _apiClient = ApiClient();

  Future<List<Circle>> getCircles() async {
    try {
      final response = await _apiClient.client.get('/circles');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => Circle.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<List<Circle>> getMyCircles() async {
    try {
      final response = await _apiClient.client.get('/circles/my');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => Circle.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<Map<String, dynamic>?> getCircleDetails(int id) async {
    try {
      final response = await _apiClient.client.get('/circles/$id');
      if (response.statusCode == 200) {
        return response.data['data'];
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<Map<String, dynamic>> joinCircle(int circleId) async {
    try {
      final response = await _apiClient.client.post('/circles/$circleId/join');
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Terjadi kesalahan'};
    }
  }

  Future<Map<String, dynamic>> leaveCircle(int circleId) async {
    try {
      final response = await _apiClient.client.post('/circles/$circleId/leave');
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Terjadi kesalahan'};
    }
  }

  Future<Map<String, dynamic>> createCircle(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.client.post('/circles', data: data);
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Gagal membuat circle'};
    }
  }

  Future<List<Map<String, dynamic>>> searchUsers(String query) async {
    try {
      final response = await _apiClient.client
          .get('/circles/search-users', queryParameters: {'q': query});
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => json as Map<String, dynamic>).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<Map<String, dynamic>> promoteToCoLeader(
      int circleId, int userId) async {
    try {
      final response = await _apiClient.client.post(
        '/circles/$circleId/promote',
        data: {'user_id': userId, 'role': 'co-leader'},
      );
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Gagal mempromosikan member'};
    }
  }

  Future<List<Dungeon>> getCircleRaids(int circleId) async {
    try {
      final response = await _apiClient.client.get('/circles/$circleId/raids');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => Dungeon.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<Map<String, dynamic>> joinRaidLobby(
      int circleId, int dungeonId) async {
    try {
      final response = await _apiClient.client
          .post('/circles/$circleId/raids/$dungeonId/join');
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Gagal masuk ke antrean Hunter'};
    }
  }

  Future<Map<String, dynamic>> createCircleRaid(
      int circleId, Map<String, dynamic> data) async {
    try {
      final response =
          await _apiClient.client.post('/circles/$circleId/raids', data: data);
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Gagal membuat quest/misi'};
    }
  }

  Future<List<Map<String, dynamic>>> getClearedRaids(int circleId) async {
    try {
      final response =
          await _apiClient.client.get('/circles/$circleId/raids/cleared');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => json as Map<String, dynamic>).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<Map<String, dynamic>> claimReward(int circleId, int dungeonId) async {
    try {
      final response = await _apiClient.client
          .post('/circles/$circleId/raids/$dungeonId/claim');
      return response.data;
    } on DioException catch (e) {
      return e.response?.data ??
          {'success': false, 'message': 'Gagal klaim hadiah'};
    }
  }
}
