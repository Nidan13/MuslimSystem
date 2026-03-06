import 'user.dart';

class AuthResponse {
  final bool success;
  final String message;
  final User user;
  final String token;

  final bool isNewUser;

  AuthResponse({
    required this.success,
    required this.message,
    required this.user,
    required this.token,
    this.isNewUser = false,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) {
    return AuthResponse(
      success: json['success'] as bool,
      message: json['message'] as String,
      user: User.fromJson(json['data']['user'] as Map<String, dynamic>),
      token: json['data']['token'] as String,
      isNewUser: json['data']['is_new_user'] ?? false,
    );
  }
}
