import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/quran.dart';
import 'api_client.dart';

class QuranService {
  // External API Client (Equran.id)
  final Dio _externalDio = Dio(BaseOptions(
    baseUrl: 'https://equran.id/api/v2',
    connectTimeout: const Duration(seconds: 15),
    receiveTimeout: const Duration(seconds: 15),
  ));

  // Backend API Client (Your Laravel)
  final Dio _backendClient = ApiClient().client;

  static const String _surahCacheKey = 'cached_surahs_list';

  Future<List<Surah>> getSurahs() async {
    try {
      final prefs = await SharedPreferences.getInstance();

      if (prefs.containsKey(_surahCacheKey)) {
        final cachedData = prefs.getString(_surahCacheKey);
        if (cachedData != null) {
          debugPrint('Loading Surahs from cache...');
          final List dynamicList = json.decode(cachedData);
          return dynamicList.map((item) => Surah.fromJson(item)).toList();
        }
      }

      debugPrint('Fetching Surahs from API...');
      final response = await _externalDio.get('/surat');

      if (response.statusCode == 200) {
        final List data = response.data['data'];
        await prefs.setString(_surahCacheKey, json.encode(data));
        return data.map((item) => Surah.fromJson(item)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching surahs: $e');
      return [];
    }
  }

  Future<SurahDetail?> getSurahDetail(int nomor) async {
    try {
      // Fetch from Al-Quran Cloud for Tajweed and Indonesian Translation
      final response = await Dio().get(
        'https://api.alquran.cloud/v1/surah/$nomor/editions/quran-tajweed,id.indonesian',
      );

      if (response.statusCode == 200) {
        final data = response.data['data'];
        final arabicData = data[0];
        final translationData = data[1];

        List<Ayah> ayahs = [];
        for (int i = 0; i < arabicData['ayahs'].length; i++) {
          final aAyah = arabicData['ayahs'][i];
          final tAyah = translationData['ayahs'][i];

          ayahs.add(Ayah(
            nomorAyat: aAyah['numberInSurah'],
            teksArab: aAyah['text'], // Contains Tajweed markers
            teksLatin:
                '', // Al-Quran Cloud doesn't provide this easily in the same call
            teksIndonesia: tAyah['text'],
            audio:
                'https://cdn.islamic.network/quran/audio/128/ar.alafasy/${aAyah['number']}.mp3',
            surahNomor: nomor,
            surahNamaLatin: arabicData['englishName'],
            halaman: aAyah['page'],
          ));
        }

        return SurahDetail(
          nomor: nomor,
          nama: arabicData['name'],
          namaLatin: arabicData['englishName'],
          jumlahAyat: arabicData['numberOfAyahs'],
          tempatTurun: arabicData['revelationType'],
          arti: arabicData['englishNameTranslation'],
          deskripsi: '',
          ayat: ayahs,
        );
      }
      return null;
    } catch (e) {
      debugPrint('Error fetching surah detail (Tajweed): $e');
      return null;
    }
  }

  Future<List<Ayah>> getJuzDetail(int juz) async {
    final dio = Dio(BaseOptions(
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 10),
    ));

    try {
      final surahs = await getSurahs();
      List<int> surahBoundaries = [0];
      int totalCount = 0;
      for (var s in surahs) {
        totalCount += s.jumlahAyat;
        surahBoundaries.add(totalCount);
      }

      // Primary API: api.quran.gading.dev
      debugPrint('Fetching Juz $juz from api.quran.gading.dev...');
      final response = await dio.get('https://api.quran.gading.dev/juz/$juz');

      if (response.statusCode == 200) {
        final List versesData = response.data['data']['verses'] ?? [];
        return versesData.map((item) {
          int inQuran = item['number']['inQuran'];

          // Find Surah
          int surahIdx = 0;
          for (int i = 0; i < surahBoundaries.length - 1; i++) {
            if (inQuran > surahBoundaries[i] &&
                inQuran <= surahBoundaries[i + 1]) {
              surahIdx = i;
              break;
            }
          }

          final surah = surahs[surahIdx];

          return Ayah(
            nomorAyat: item['number']['inSurah'],
            teksArab: item['text']['arab'],
            teksLatin: item['text']['transliteration'] != null
                ? item['text']['transliteration']['en']
                : '',
            teksIndonesia: item['translation']['id'],
            audio: item['audio']['primary'],
            surahNomor: surah.nomor,
            surahNamaLatin: surah.namaLatin,
            halaman: item['meta']['page'],
          );
        }).toList();
      }
    } catch (e) {
      debugPrint('Juz Detail Error: $e');
    }

    return [];
  }

  // --- Backend User Progress Methods ---

  Future<List<int>> getCompletedSurahs() async {
    try {
      final response = await _backendClient.get('/quran/progress');
      if (response.statusCode == 200 && response.data['success']) {
        return List<int>.from(response.data['data']);
      }
      return [];
    } catch (e) {
      debugPrint('Error getting completed surahs: $e');
      return [];
    }
  }

  Future<bool> toggleSurahCompletion(int surahId) async {
    try {
      final response = await _backendClient.post(
        '/quran/progress/toggle',
        data: {'surah_id': surahId},
      );
      return response.statusCode == 200 && response.data['success'];
    } catch (e) {
      debugPrint('Error toggling surah: $e');
      return false;
    }
  }

  // --- Bookmark (Last Read) Methods ---

  static const String _bookmarkSurahKey = 'quran_last_read_surah';
  static const String _bookmarkAyahKey = 'quran_last_read_ayah';
  static const String _bookmarkSurahNameKey = 'quran_last_read_surah_name';

  Future<void> saveBookmark(int surahNo, int ayahNo, String surahName) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(_bookmarkSurahKey, surahNo);
    await prefs.setInt(_bookmarkAyahKey, ayahNo);
    await prefs.setString(_bookmarkSurahNameKey, surahName);
  }

  Future<Map<String, dynamic>?> getBookmark() async {
    final prefs = await SharedPreferences.getInstance();
    if (!prefs.containsKey(_bookmarkSurahKey)) return null;
    return {
      'surahNo': prefs.getInt(_bookmarkSurahKey),
      'ayahNo': prefs.getInt(_bookmarkAyahKey),
      'surahName': prefs.getString(_bookmarkSurahNameKey),
    };
  }

  // --- Khatam Target Methods ---

  static const String _khatamTargetDateKey = 'quran_khatam_target_date';

  Future<void> setKhatamTarget(DateTime targetDate) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_khatamTargetDateKey, targetDate.toIso8601String());
  }

  Future<DateTime?> getKhatamTarget() async {
    final prefs = await SharedPreferences.getInstance();
    final dateStr = prefs.getString(_khatamTargetDateKey);
    return dateStr != null ? DateTime.parse(dateStr) : null;
  }

  // --- Reading History ---

  Future<void> saveToHistory(
      int surahNo, int ayahNo, String surahName, int? juzNo) async {
    try {
      await _backendClient.post('/quran/history/save', data: {
        'surah_no': surahNo,
        'ayah_no': ayahNo,
        'surah_name': surahName,
        'juz_no': juzNo,
      });
    } catch (e) {
      debugPrint("Error saving history to server: $e");
    }
  }

  Future<List<Map<String, dynamic>>> getHistory() async {
    try {
      final response = await _backendClient.get('/quran/history');
      if (response.statusCode == 200 && response.data['success']) {
        return List<Map<String, dynamic>>.from(response.data['data']);
      }
      return [];
    } catch (e) {
      debugPrint("Error fetching history from server: $e");
      return [];
    }
  }
}
