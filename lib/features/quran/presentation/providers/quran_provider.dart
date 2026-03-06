import 'package:flutter/material.dart';
import '../../data/models/ayah_model.dart';
import '../../data/services/quran_service.dart';

class QuranProvider with ChangeNotifier {
  final QuranService _service = QuranService();

  List<AyahModel> _ayahs = [];
  bool _isLoading = false;
  bool _showTranslation = true;
  String? _errorMessage;

  List<AyahModel> get ayahs => _ayahs;
  bool get isLoading => _isLoading;
  bool get showTranslation => _showTranslation;
  String? get errorMessage => _errorMessage;

  void toggleTranslation() {
    _showTranslation = !_showTranslation;
    notifyListeners();
  }

  Future<void> fetchSurah(int surahNumber) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _ayahs = await _service.getAyahsInSurah(surahNumber);
    } catch (e) {
      _errorMessage = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  // Optimize: only update specific ayah if needed
  void updateAyah(int index, AyahModel updatedAyah) {
    _ayahs[index] = updatedAyah;
    notifyListeners();
  }
}
