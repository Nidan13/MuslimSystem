import 'package:dio/dio.dart';
import 'dart:math';
import '../models/hadith.dart';

class HadithService {
  final Dio _dio = Dio();
  final String _baseUrl = 'https://api.hadith.gading.dev';

  final List<Map<String, dynamic>> _books = [
    {'id': 'bukhari', 'name': 'Bukhari', 'max': 6638},
    {'id': 'muslim', 'name': 'Muslim', 'max': 4930},
    {'id': 'tirmidzi', 'name': 'Tirmidzi', 'max': 3625},
    {'id': 'abu-daud', 'name': 'Abu Daud', 'max': 4419},
    {'id': 'nasai', 'name': 'Nasai', 'max': 5364},
    {'id': 'ibnu-majah', 'name': 'Ibnu Majah', 'max': 4285},
    {
      'id': 'ahmad',
      'name': 'Ahmad',
      'max': 4305
    }, // Reduced from real max to ensure safety
  ];

  Future<Hadith?> getRandomHadith() async {
    try {
      final random = Random();
      final book = _books[random.nextInt(_books.length)];
      final number = random.nextInt(book['max']) + 1;

      final url = '$_baseUrl/books/${book['id']}?range=$number-$number';
      final response = await _dio.get(url);

      if (response.statusCode == 200 && response.data['code'] == 200) {
        return Hadith.fromJson(response.data['data'], book['name']);
      }
    } catch (e) {
      print('Error fetching random hadith: $e');
    }
    return null;
  }
}
