import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../models/home_data.dart';
import 'api_client.dart';
import 'storage_service.dart';

class HomeService {
  final Dio _dio = ApiClient().client;

  Future<HomeData?> getHomeData() async {
    try {
      final token = await StorageService.getToken();
      if (token == null) return null;

      final response = await _dio.get('/home');

      if (response.statusCode == 200 && response.data['success']) {
        debugPrint('Home Data URL: ${response.realUri}');
        debugPrint('Home Data Raw: ${response.data}');
        return HomeData.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      print('Error fetching home data: $e');
      return null;
    }
  }
}
