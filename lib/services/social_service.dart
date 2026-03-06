import 'api_client.dart';

class SocialService {
  final ApiClient _apiClient = ApiClient();

  Future<List<Map<String, dynamic>>> searchUsers(String query) async {
    try {
      final response = await _apiClient.client
          .get('/social/search', queryParameters: {'q': query});
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => json as Map<String, dynamic>).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<bool> followUser(int id) async {
    try {
      final response = await _apiClient.client.post('/social/follow/$id');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> unfollowUser(int id) async {
    try {
      final response = await _apiClient.client.post('/social/unfollow/$id');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<List<Map<String, dynamic>>> getFollowers(int userId) async {
    try {
      final response = await _apiClient.client.get('/social/$userId/followers');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => json as Map<String, dynamic>).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<List<Map<String, dynamic>>> getFollowing(int userId) async {
    try {
      final response = await _apiClient.client.get('/social/$userId/following');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => json as Map<String, dynamic>).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }
}
