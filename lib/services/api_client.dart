import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'storage_service.dart';

class ApiClient {
  static final ApiClient _instance = ApiClient._internal();
  late Dio dio;

  // Base URL via ngrok - accessible from any device/network
  // Ngrok URL: update this if ngrok restarts (new URL will be generated)
  static const String baseUrl =
      'https://nonmanipulative-annamae-mythopoeic.ngrok-free.dev/api';

  factory ApiClient() {
    return _instance;
  }

  ApiClient._internal() {
    dio = Dio(
      BaseOptions(
        baseUrl: baseUrl,
        connectTimeout: const Duration(seconds: 90),
        receiveTimeout: const Duration(seconds: 90),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'ngrok-skip-browser-warning': 'true', // bypass ngrok browser warning
        },
      ),
    );

    // Add interceptors
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Add auth token to every request if available
          final token = await StorageService.getToken();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onResponse: (response, handler) {
          // Handle successful responses
          return handler.next(response);
        },
        onError: (DioException error, handler) async {
          // Handle errors
          if (error.response?.statusCode == 401) {
            // Unauthorized - clear token and redirect to login
            await StorageService.removeToken();
          }
          return handler.next(error);
        },
      ),
    );

    // Add logging interceptor for debugging
    dio.interceptors.add(LogInterceptor(
      request: true,
      requestHeader: true,
      requestBody: true,
      responseHeader: true,
      responseBody: true,
      error: true,
      logPrint: (object) {
        debugPrint('DIO LOG: $object');
      },
    ));
  }

  Dio get client => dio;
}
