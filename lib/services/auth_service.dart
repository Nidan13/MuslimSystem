import 'package:dio/dio.dart';
import 'package:google_sign_in/google_sign_in.dart';
import '../models/user.dart';
import '../models/auth_response.dart';
import 'api_client.dart';
import 'storage_service.dart';

class AuthService {
  final Dio _dio = ApiClient().client;
  late final GoogleSignIn _googleSignIn = GoogleSignIn();

  /// Sign in with Google
  /// Returns AuthResponse with user data and token
  Future<AuthResponse?> signInWithGoogle() async {
    try {
      // Force account selection/picker by signing out first
      await _googleSignIn.signOut();

      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
      if (googleUser == null) {
        return null;
      }

      final GoogleSignInAuthentication googleAuth =
          await googleUser.authentication;
      final String? idToken = googleAuth.idToken;

      if (idToken == null) {
        throw Exception("Failed to get ID Token from Google");
      }

      final response = await _dio.post(
        '/auth/google',
        data: {'id_token': idToken},
      );

      final authResponse = AuthResponse.fromJson(response.data);

      await StorageService.saveToken(authResponse.token);
      await StorageService.saveUserId(authResponse.user.id);
      await StorageService.saveActiveStatus(authResponse.user.isActive);

      return authResponse;
    } on DioException catch (e) {
      throw _handleError(e);
    } catch (e) {
      throw e.toString();
    }
  }

  /// Update user profile
  Future<User> updateProfile({
    required String username,
    required String gender,
  }) async {
    try {
      final response = await _dio.post(
        '/profile/update',
        data: {
          'username': username,
          'gender': gender,
        },
      );

      return User.fromJson(response.data['data']['user']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Register new user
  /// Returns AuthResponse with user data and token
  Future<AuthResponse> register({
    required String username,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String gender,
    String? referralCode,
  }) async {
    try {
      final response = await _dio.post(
        '/register',
        data: {
          'username': username,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'gender': gender,
          'referral_code': referralCode,
        },
      );

      final authResponse = AuthResponse.fromJson(response.data);

      // Save token and user ID
      await StorageService.saveToken(authResponse.token);
      await StorageService.saveUserId(authResponse.user.id);
      await StorageService.saveActiveStatus(authResponse.user.isActive);

      return authResponse;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Login user
  /// Returns AuthResponse with user data and token
  Future<AuthResponse> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await _dio.post(
        '/login',
        data: {
          'email': email,
          'password': password,
        },
      );

      final authResponse = AuthResponse.fromJson(response.data);

      // Save token and user ID
      await StorageService.saveToken(authResponse.token);
      await StorageService.saveUserId(authResponse.user.id);
      await StorageService.saveActiveStatus(authResponse.user.isActive);

      return authResponse;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Logout user
  /// Revokes token on server and clears local storage
  Future<void> logout() async {
    try {
      await _dio.post('/logout');
      await StorageService.removeToken();
      await _googleSignIn.signOut();
    } on DioException catch (e) {
      // Even if API call fails, clear local token
      await StorageService.removeToken();
      await _googleSignIn.signOut();
      throw _handleError(e);
    }
  }

  /// Get user profile
  /// Returns User data
  Future<User> getProfile() async {
    try {
      final response = await _dio.get('/profile');
      final user = User.fromJson(response.data['data']['user']);
      await StorageService.saveActiveStatus(user.isActive);
      return user;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  /// Handle Dio errors and convert to readable messages
  String _handleError(DioException error) {
    if (error.response != null) {
      // Server returned an error response
      final data = error.response!.data;
      if (data is Map<String, dynamic>) {
        if (data.containsKey('message')) {
          return data['message'] as String;
        }
        if (data.containsKey('errors')) {
          // Laravel validation errors
          final errors = data['errors'] as Map<String, dynamic>;
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            return firstError.first as String;
          }
        }
      }
      return 'Server error: ${error.response!.statusCode}';
    } else if (error.type == DioExceptionType.connectionTimeout ||
        error.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Please check your internet.';
    } else if (error.type == DioExceptionType.connectionError) {
      return 'No internet connection. Please try again.';
    } else {
      return 'Something went wrong. Please try again.';
    }
  }
}
