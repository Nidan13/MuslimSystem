import 'package:dio/dio.dart';
import '../models/ayah_model.dart';

class QuranService {
  final Dio _dio = Dio();
  final String _baseUrl = 'https://api.alquran.cloud/v1';

  Future<List<AyahModel>> getAyahsInSurah(int surahNumber) async {
    try {
      // Use 'quran-tajweed' edition for tajwid markers
      // Use 'id.indonesian' for translation
      final response = await _dio.get(
          '$_baseUrl/surah/$surahNumber/editions/quran-tajweed,id.indonesian');

      if (response.statusCode == 200) {
        final List<dynamic> tajweedData = response.data['data'][0]['ayahs'];
        final List<dynamic> translationData = response.data['data'][1]['ayahs'];

        List<AyahModel> ayahs = [];
        for (int i = 0; i < tajweedData.length; i++) {
          ayahs.add(AyahModel(
            number: tajweedData[i]['number'],
            text: tajweedData[i]
                ['text'], // This text now contains [id:idx[text]] markers
            translation: translationData[i]['text'],
            numberInSurah: tajweedData[i]['numberInSurah'],
            audio:
                'https://cdn.islamic.network/quran/audio/128/ar.alafasy/${tajweedData[i]['number']}.mp3',
          ));
        }
        return ayahs;
      }
      throw Exception('Failed to load surah');
    } catch (e) {
      throw Exception('Error fetching Quran data (Tajweed edition): $e');
    }
  }
}
