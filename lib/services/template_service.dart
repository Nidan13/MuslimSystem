import 'package:dio/dio.dart';
import 'api_client.dart';
import '../utils/template_data.dart';

class TemplateService {
  final ApiClient _apiClient = ApiClient();

  Future<List<TaskTemplate>> getTemplates(String type) async {
    try {
      final response = await _apiClient.dio
          .get('/templates', queryParameters: {'type': type});
      if (response.data['success'] == true) {
        final List data = response.data['data'] ?? [];
        return data.map((json) => TaskTemplate.fromJson(json)).toList();
      }
      return [];
    } on DioException catch (_) {
      return [];
    }
  }
}
