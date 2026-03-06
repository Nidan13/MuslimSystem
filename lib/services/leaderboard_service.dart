import 'api_client.dart';

class LeaderboardService {
  final ApiClient _apiClient = ApiClient();

  Future<List<Map<String, dynamic>>> getUserLeaderboard(
      {String? gender}) async {
    try {
      final queryParams = gender != null && gender != 'all'
          ? '?gender=${gender.toLowerCase()}'
          : '';
      final response =
          await _apiClient.client.get('/leaderboard/users$queryParams');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'] ?? [];
        return data.map((json) => json as Map<String, dynamic>).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<List<Map<String, dynamic>>> getCircleLeaderboard() async {
    try {
      final response = await _apiClient.client.get('/leaderboard/circles');
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
