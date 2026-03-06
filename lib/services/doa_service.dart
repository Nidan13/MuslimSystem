import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/doa.dart';

class DoaService {
  // Using MyQuran API for a more detailed and comprehensive Doa list
  final String _baseUrl = 'https://api.myquran.com/v2/doa/semua';

  Future<List<Doa>> getDoas() async {
    try {
      final response = await http.get(Uri.parse(_baseUrl));

      if (response.statusCode == 200) {
        final Map<String, dynamic> decoded = json.decode(response.body);
        final List<dynamic> data =
            decoded['data']; // The Doa array is nested inside 'data'
        return data.asMap().entries.map((entry) {
          final index = entry.key;
          final item = entry.value;
          // Assign ID from index if missing in API
          if (item['id'] == null || item['id'].toString().isEmpty) {
            item['id'] = (index + 1).toString();
          }
          return Doa.fromJson(item);
        }).toList();
      } else {
        throw Exception('Gagal memuat daftar doa');
      }
    } catch (e) {
      throw Exception('Terjadi kesalahan: $e');
    }
  }
}
