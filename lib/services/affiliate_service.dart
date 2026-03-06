import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../models/commission.dart';
import '../models/withdrawal.dart';
import 'api_client.dart';

class AffiliateService {
  final ApiClient _apiClient = ApiClient();

  Future<Map<String, dynamic>> getStats() async {
    try {
      final response = await _apiClient.dio.get('/affiliate/stats');
      if (response.statusCode == 200 && response.data != null) {
        return response.data['data'] ?? {};
      }
      return {};
    } catch (e) {
      debugPrint('Error fetching affiliate stats: $e');
      return {};
    }
  }

  Future<List<Commission>> getCommissions() async {
    try {
      final response = await _apiClient.dio.get('/affiliate/commissions');
      if (response.statusCode == 200 && response.data != null) {
        final responseData = response.data['data'];
        final List<dynamic> data =
            responseData is Map && responseData.containsKey('data')
                ? responseData['data']
                : (responseData ?? []);
        return data.map((json) => Commission.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching commissions: $e');
      return [];
    }
  }

  Future<List<Withdrawal>> getWithdrawals() async {
    try {
      final response = await _apiClient.dio.get('/affiliate/withdrawals');
      if (response.statusCode == 200 && response.data != null) {
        // Handle Laravel pagination wrapper
        final responseData = response.data['data'];
        final List<dynamic> data =
            responseData is Map && responseData.containsKey('data')
                ? responseData['data']
                : (responseData ?? []);
        return data.map((json) => Withdrawal.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching withdrawals: $e');
      return [];
    }
  }

  Future<Map<String, dynamic>> withdraw({
    required int amount,
    required String bankName,
    required String accountNumber,
    required String accountName,
  }) async {
    try {
      final response = await _apiClient.dio.post('/affiliate/withdraw', data: {
        'amount': amount,
        'bank_name': bankName,
        'account_number': accountNumber,
        'account_name': accountName,
      });

      return {
        'success': response.statusCode == 200,
        'message':
            response.data['message'] ?? 'Permintaan penarikan berhasil dikirim',
      };
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ??
            'Gagal mengajukan penarikan: ${e.message}',
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'Terjadi kesalahan: $e',
      };
    }
  }
}
