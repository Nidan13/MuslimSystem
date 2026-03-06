import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/quran.dart';
import '../services/quran_service.dart';
import 'quran_detail_screen.dart';
import '../theme/premium_color.dart';

class QuranListScreen extends StatefulWidget {
  const QuranListScreen({super.key});

  @override
  State<QuranListScreen> createState() => _QuranListScreenState();
}

class _QuranListScreenState extends State<QuranListScreen> {
  final QuranService _quranService = QuranService();
  List<Surah> _surahs = [];
  List<Surah> _filteredSurahs = [];
  bool _isLoading = true;
  final TextEditingController _searchController = TextEditingController();
  int _selectedTab = 0; // 0: Surah, 1: Juz, 2: Riwayat
  ThemeMode _currentTheme = ThemeMode.system;

  Map<String, dynamic>? _lastRead;
  List<Map<String, dynamic>> _readingHistory = [];
  final List<Map<String, dynamic>> _juzList = List.generate(
      30,
      (i) => {
            "nomor": i + 1,
            "nama": "Juz ${i + 1}",
            "deskripsi": _getJuzStartInfo(i + 1),
            "surahNo": _getJuzStartSurah(i + 1),
            "ayahNo": _getJuzStartAyah(i + 1)
          });

  @override
  void initState() {
    super.initState();
    _loadAllData();
  }

  Future<void> _loadAllData() async {
    setState(() => _isLoading = true);
    final surahs = await _quranService.getSurahs();
    final bookmark = await _quranService.getBookmark();
    final history = await _quranService.getHistory();
    if (mounted) {
      setState(() {
        _surahs = surahs;
        _filteredSurahs = surahs;
        _lastRead = bookmark;
        _readingHistory = history;
        _isLoading = false;
      });
    }
  }

  static String _getJuzStartInfo(int juz) {
    const infos = [
      "Al-Fatihah: 1",
      "Al-Baqarah: 142",
      "Al-Baqarah: 253",
      "Ali 'Imran: 93",
      "An-Nisa': 24",
      "An-Nisa': 148",
      "Al-Ma'idah: 82",
      "Al-A'nam: 111",
      "Al-A'raf: 88",
      "Al-Anfal: 41",
      "At-Taubah: 93",
      "Hud: 6",
      "Ar-Ra'd: 53",
      "Al-Hijr: 1",
      "An-Nahl: 129",
      "Al-Kahf: 75",
      "Al-Anbiya': 1",
      "Al-Mu'minun: 1",
      "Al-Furqan: 21",
      "An-Naml: 56",
      "Al-Ankabut: 45",
      "Al-Ahzab: 31",
      "Yasin: 28",
      "Az-Zumar: 32",
      "Fussilat: 47",
      "Al-Ahqaf: 1",
      "Ad-Dhariyat: 31",
      "Al-Mujadilah: 1",
      "Al-Mulk: 1",
      "An-Naba': 1"
    ];
    return infos[juz - 1];
  }

  static int _getJuzStartSurah(int juz) {
    const surahs = [
      1,
      2,
      2,
      3,
      4,
      4,
      5,
      6,
      7,
      8,
      9,
      11,
      12,
      15,
      16,
      18,
      21,
      23,
      25,
      27,
      29,
      33,
      36,
      39,
      41,
      46,
      51,
      58,
      67,
      78
    ];
    return surahs[juz - 1];
  }

  static int _getJuzStartAyah(int juz) {
    const ayahs = [
      1,
      142,
      253,
      93,
      24,
      148,
      82,
      111,
      88,
      41,
      93,
      6,
      53,
      1,
      129,
      75,
      1,
      1,
      21,
      56,
      45,
      31,
      28,
      32,
      47,
      1,
      31,
      1,
      1,
      1
    ];
    return ayahs[juz - 1];
  }

  void _filterSurahs(String query) {
    setState(() {
      _filteredSurahs = _surahs
          .where((s) =>
              s.namaLatin.toLowerCase().contains(query.toLowerCase()) ||
              s.arti.toLowerCase().contains(query.toLowerCase()))
          .toList();
    });
  }

  void _onItemClicked({Surah? surah, int? juzNo, int? initialAyah}) {
    bool isDark = _currentTheme == ThemeMode.dark ||
        (_currentTheme == ThemeMode.system &&
            MediaQuery.of(context).platformBrightness == Brightness.dark);

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
          borderRadius: const BorderRadius.only(
              topLeft: Radius.circular(30), topRight: Radius.circular(30)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                    color: isDark ? Colors.white24 : Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(2))),
            const SizedBox(height: 24),
            Text(
              surah != null
                  ? "Pilih Mode Baca: ${surah.namaLatin}"
                  : "Pilih Mode Baca: Juz $juzNo",
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black87),
            ),
            const SizedBox(height: 24),
            _buildModeOption(
              isDark: isDark,
              icon: Icons.list_alt_rounded,
              title: "Baca Per Ayat",
              subtitle: "Teks Arab disertai terjemahan & audio",
              onTap: () {
                Navigator.pop(context);
                Navigator.push<ThemeMode>(
                    context,
                    MaterialPageRoute(
                        builder: (context) => QuranDetailScreen(
                            surah: surah,
                            juzNumber: juzNo,
                            initialAyah: initialAyah,
                            initialMode: 0,
                            initialTheme: _currentTheme))).then((newTheme) {
                  if (newTheme != null) {
                    setState(() => _currentTheme = newTheme);
                  }
                  _loadAllData();
                });
              },
            ),
            const SizedBox(height: 16),
            _buildModeOption(
              isDark: isDark,
              icon: Icons.chrome_reader_mode_outlined,
              title: "Baca Mushaf",
              subtitle: "Halaman penuh tanpa terjemahan",
              onTap: () {
                Navigator.pop(context);
                Navigator.push<ThemeMode>(
                    context,
                    MaterialPageRoute(
                        builder: (context) => QuranDetailScreen(
                            surah: surah,
                            juzNumber: juzNo,
                            initialAyah: initialAyah,
                            initialMode: 1,
                            initialTheme: _currentTheme))).then((newTheme) {
                  if (newTheme != null) {
                    setState(() => _currentTheme = newTheme);
                  }
                  _loadAllData();
                });
              },
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }

  Widget _buildModeOption(
      {required bool isDark,
      required IconData icon,
      required String title,
      required String subtitle,
      required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          border:
              Border.all(color: isDark ? Colors.white10 : Colors.grey.shade200),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                  color: isDark
                      ? Colors.white.withOpacity(0.05)
                      : PremiumColor.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(15)),
              child: Icon(icon,
                  color: isDark ? Colors.white : PremiumColor.primary,
                  size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title,
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: isDark ? Colors.white : Colors.black87)),
                  const SizedBox(height: 4),
                  Text(subtitle,
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          color: isDark ? Colors.white : Colors.grey,
                          fontWeight: FontWeight.w500)),
                ],
              ),
            ),
            Icon(Icons.arrow_forward_ios_rounded,
                size: 16, color: isDark ? Colors.white24 : Colors.grey),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    bool isDark = _currentTheme == ThemeMode.dark ||
        (_currentTheme == ThemeMode.system &&
            MediaQuery.of(context).platformBrightness == Brightness.dark);

    return Theme(
      data: isDark ? ThemeData.dark() : ThemeData.light(),
      child: Scaffold(
        backgroundColor: isDark ? const Color(0xFF121212) : Colors.white,
        body: Column(
          children: [
            _buildHeader(isDark),
            Expanded(
              child: _isLoading
                  ? Center(
                      child: CircularProgressIndicator(
                          color: PremiumColor.primary))
                  : _buildContent(isDark),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildContent(bool isDark) {
    switch (_selectedTab) {
      case 0:
        return Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 10, 20, 10),
              child: _buildSearchBar(isDark),
            ),
            Expanded(child: _buildSurahList(isDark)),
          ],
        );
      case 1:
        return _buildJuzList(isDark);
      case 2:
        return _buildRiwayatList(isDark);
      default:
        return _buildSurahList(isDark);
    }
  }

  Widget _buildHeader(bool isDark) {
    return Container(
      padding: const EdgeInsets.only(top: 60, bottom: 15),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Row(
              children: [
                Text(
                  "Al-Quran",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
                const Spacer(),
                Icon(Icons.bookmark_border,
                    color: isDark ? Colors.white : Colors.black87, size: 24),
                const SizedBox(width: 20),
                IconButton(
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                  icon: Icon(
                    Icons.nightlight_round,
                    color: isDark ? Colors.white : Colors.black87,
                    size: 24,
                  ),
                  onPressed: _showThemeDialog,
                ),
                const SizedBox(width: 20),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: isDark ? Colors.white12 : Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                        color: isDark ? Colors.white10 : Colors.grey.shade200),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.search,
                          color: isDark ? Colors.white70 : Colors.black54,
                          size: 18),
                      const SizedBox(width: 8),
                      Text(
                        "Cari",
                        style: GoogleFonts.plusJakartaSans(
                          color: isDark ? Colors.white70 : Colors.black54,
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 25),
          // Normal Tabs per Screenshot
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 20),
            height: 48,
            decoration: BoxDecoration(
              color: isDark
                  ? Colors.white.withOpacity(0.05)
                  : Colors.grey.shade100,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              children: [
                _buildTabItem(0, "Surah", isDark),
                _buildTabItem(1, "Juz", isDark),
                _buildTabItem(2, "Riwayat", isDark),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTabItem(int index, String label, bool isDark) {
    bool isSelected = _selectedTab == index;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _selectedTab = index),
        child: Container(
          margin: const EdgeInsets.all(4),
          alignment: Alignment.center,
          decoration: BoxDecoration(
            color: isSelected
                ? (isDark ? Colors.white.withOpacity(0.15) : Colors.white)
                : Colors.transparent,
            borderRadius: BorderRadius.circular(8),
            boxShadow: isSelected && !isDark
                ? [
                    BoxShadow(
                        color: Colors.black.withOpacity(0.05), blurRadius: 4)
                  ]
                : null,
          ),
          child: Text(
            label,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 14,
              fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
              color: isSelected
                  ? (isDark ? Colors.white : Colors.black87)
                  : (isDark ? Colors.white60 : Colors.black54),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSearchBar(bool isDark) {
    return Container(
      decoration: BoxDecoration(
        color: isDark ? Colors.white.withOpacity(0.05) : Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: isDark ? Colors.white.withOpacity(0.1) : PremiumColor.slate200,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: TextField(
        controller: _searchController,
        onChanged: _filterSurahs,
        style: GoogleFonts.plusJakartaSans(
          color: isDark ? Colors.white : PremiumColor.slate800,
          fontSize: 14,
          fontWeight: FontWeight.w600,
        ),
        decoration: InputDecoration(
          hintText: "Cari Nama Surah...",
          hintStyle: GoogleFonts.plusJakartaSans(
            color: isDark ? Colors.white38 : PremiumColor.slate400,
            fontSize: 14,
          ),
          prefixIcon: Icon(
            Icons.search_rounded,
            color: isDark ? Colors.white38 : PremiumColor.slate400,
            size: 20,
          ),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 14),
        ),
      ),
    );
  }

  Widget _buildSurahList(bool isDark) {
    return ListView.separated(
      padding: const EdgeInsets.symmetric(horizontal: 0),
      itemCount: _filteredSurahs.length,
      separatorBuilder: (context, index) => Divider(
          color: isDark
              ? Colors.white.withOpacity(0.05)
              : Colors.grey.withOpacity(0.1),
          height: 1),
      itemBuilder: (context, index) {
        final surah = _filteredSurahs[index];
        return ListTile(
          onTap: () => _onItemClicked(surah: surah),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
          leading: _buildNumberFrame(surah.nomor, isDark),
          title: Text(
            surah.namaLatin,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : Colors.black87,
            ),
          ),
          subtitle: Text(
            "${surah.arti} - ${surah.jumlahAyat} Ayat",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              color: isDark ? Colors.white54 : Colors.grey,
              fontWeight: FontWeight.w500,
            ),
          ),
          trailing: Text(
            surah.nama,
            style: GoogleFonts.amiri(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: PremiumColor.primary,
            ),
          ),
        );
      },
    );
  }

  Widget _buildJuzList(bool isDark) {
    return ListView.separated(
      padding: const EdgeInsets.all(0),
      itemCount: 30,
      separatorBuilder: (context, index) => Divider(
          color: isDark
              ? Colors.white.withOpacity(0.05)
              : Colors.grey.withOpacity(0.1),
          height: 1),
      itemBuilder: (context, index) {
        final juz = _juzList[index];
        return ListTile(
          onTap: () =>
              _onItemClicked(juzNo: juz['nomor'], initialAyah: juz['ayahNo']),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
          leading: _buildNumberFrame(juz['nomor'], isDark),
          title: Text(
            juz['nama'],
            style: GoogleFonts.plusJakartaSans(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : Colors.black87,
            ),
          ),
          subtitle: Text(
            "Mulai dari ${juz['deskripsi']}",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              color: isDark ? Colors.white54 : Colors.grey,
              fontWeight: FontWeight.w500,
            ),
          ),
          trailing: Icon(Icons.arrow_forward_ios_rounded,
              color: isDark ? Colors.white24 : Colors.grey, size: 14),
        );
      },
    );
  }

  Widget _buildRiwayatList(bool isDark) {
    return ListView(
      padding: const EdgeInsets.all(0),
      children: [
        _buildSectionTitle("Penanda Terakhir Dibaca", isDark),
        _buildLastReadSection(isDark),
        _buildSectionTitle("Riwayat Membaca", isDark),
        _buildReadingHistoryList(isDark),
        _buildSectionTitle("Populer", isDark),
        _buildPopularSection(isDark),
      ],
    );
  }

  Widget _buildSectionTitle(String title, bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      color: isDark ? Colors.white.withOpacity(0.03) : Colors.grey.shade50,
      child: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 13,
          fontWeight: FontWeight.bold,
          color: isDark ? Colors.white70 : Colors.black54,
        ),
      ),
    );
  }

  Widget _buildLastReadSection(bool isDark) {
    if (_lastRead == null) {
      return Padding(
        padding: const EdgeInsets.all(20),
        child: Text("Belum Ada",
            style: GoogleFonts.plusJakartaSans(
                color: isDark ? Colors.white54 : Colors.grey,
                fontWeight: FontWeight.bold,
                fontSize: 16)),
      );
    }
    return ListTile(
      onTap: () {
        final surahId = _lastRead!['surahNo'];
        final surah = _surahs.firstWhere((s) => s.nomor == surahId,
            orElse: () => _surahs.first);
        _onItemClicked(surah: surah, initialAyah: _lastRead!['ayahNo']);
      },
      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      leading: const Icon(Icons.history, color: Color(0xFF26A69A), size: 28),
      title: Text(
        "QS ${_lastRead!['surahName']}: Ayat ${_lastRead!['ayahNo']}",
        style: GoogleFonts.plusJakartaSans(
            color: isDark ? Colors.white : Colors.black87,
            fontWeight: FontWeight.bold),
      ),
      trailing: Icon(Icons.arrow_forward_ios_rounded,
          color: isDark ? Colors.white24 : Colors.grey, size: 14),
    );
  }

  Widget _buildReadingHistoryList(bool isDark) {
    if (_readingHistory.isEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
        child: Center(
          child: Text("Belum ada riwayat",
              style: GoogleFonts.plusJakartaSans(
                  color: isDark ? Colors.white54 : Colors.grey,
                  fontWeight: FontWeight.bold,
                  fontSize: 14)),
        ),
      );
    }

    return Column(
      children: _readingHistory.asMap().entries.map((entry) {
        int index = entry.key;
        var item = entry.value;
        String title = "QS ${item['surahName']}: Ayat ${item['ayahNo']}";
        if (item['juzNo'] != null) {
          title += " (Juz ${item['juzNo']})";
        }

        return Column(
          children: [
            ListTile(
              onTap: () {
                final surahId = item['surahNo'];
                final surah = _surahs.firstWhere((s) => s.nomor == surahId,
                    orElse: () => _surahs.first);
                _onItemClicked(
                    surah: surah,
                    initialAyah: item['ayahNo'],
                    juzNo: item['juzNo']);
              },
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
              leading: Text("${index + 1}",
                  style: GoogleFonts.plusJakartaSans(
                      color: const Color(0xFF26A69A),
                      fontWeight: FontWeight.bold,
                      fontSize: 16)),
              title: Text(title,
                  style: GoogleFonts.plusJakartaSans(
                      color: isDark ? Colors.white : Colors.black87,
                      fontWeight: FontWeight.bold,
                      fontSize: 15)),
              trailing: Icon(Icons.arrow_forward_ios_rounded,
                  color: isDark ? Colors.white24 : Colors.grey, size: 14),
            ),
            Divider(
                color: isDark
                    ? Colors.white.withOpacity(0.05)
                    : Colors.grey.withOpacity(0.1),
                height: 1,
                indent: 20,
                endIndent: 20),
          ],
        );
      }).toList(),
    );
  }

  Widget _buildPopularSection(bool isDark) {
    // Mock popular surahs based on screenshot
    return Column(
      children: [
        _buildPopularItem(
            1, "Al-Waq'iah", "Makkiyah - 96 Ayat", "الواقعة", isDark),
        _buildPopularItem(2, "Yasin", "Makkiyah - 83 Ayat", "يس", isDark),
      ],
    );
  }

  Widget _buildPopularItem(
      int index, String name, String desc, String arabic, bool isDark) {
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      leading: _buildNumberFrame(index, isDark),
      title: Text(name,
          style: GoogleFonts.plusJakartaSans(
              color: isDark ? Colors.white : Colors.black87,
              fontWeight: FontWeight.bold)),
      subtitle: Text(desc,
          style: GoogleFonts.plusJakartaSans(
              color: Colors.orange.shade300,
              fontSize: 12,
              fontWeight: FontWeight.w600)),
      trailing: Text(arabic,
          style: GoogleFonts.amiri(
              color: isDark ? Colors.white70 : Colors.black54,
              fontSize: 18,
              fontWeight: FontWeight.bold)),
      onTap: () {
        final surahIndex = _surahs.indexWhere((s) => s.namaLatin == name);
        if (surahIndex != -1) _onItemClicked(surah: _surahs[surahIndex]);
      },
    );
  }

  Widget _buildNumberFrame(int nomor, bool isDark) {
    Color borderColor = isDark ? Colors.white24 : Colors.grey.shade400;
    return Container(
      width: 40,
      height: 40,
      child: Stack(
        alignment: Alignment.center,
        children: [
          Transform.rotate(
            angle: 0.785398,
            child: Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                border: Border.all(color: borderColor, width: 1.2),
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),
          Container(
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              border: Border.all(color: borderColor, width: 1.2),
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          Text(
            nomor.toString(),
            style: GoogleFonts.plusJakartaSans(
              fontSize: 11,
              fontWeight: FontWeight.w800,
              color: isDark ? Colors.white : Colors.black87,
            ),
          ),
        ],
      ),
    );
  }

  void _showThemeDialog() {
    bool isDark = _currentTheme == ThemeMode.dark ||
        (_currentTheme == ThemeMode.system &&
            MediaQuery.of(context).platformBrightness == Brightness.dark);

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                      color: Colors.grey.shade300,
                      borderRadius: BorderRadius.circular(2))),
            ),
            const SizedBox(height: 24),
            Text("Pengaturan Tema",
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold,
                    fontSize: 18,
                    color: isDark ? Colors.white : Colors.black87)),
            const SizedBox(height: 20),
            _buildThemeOptionDialog(
                "Terang", ThemeMode.light, Icons.wb_sunny_rounded),
            _buildThemeOptionDialog(
                "Gelap", ThemeMode.dark, Icons.nightlight_round),
            _buildThemeOptionDialog("Sesuai Sistem", ThemeMode.system,
                Icons.brightness_auto_outlined),
          ],
        ),
      ),
    );
  }

  Widget _buildThemeOptionDialog(String title, ThemeMode mode, IconData icon) {
    bool isDark = _currentTheme == ThemeMode.dark ||
        (_currentTheme == ThemeMode.system &&
            MediaQuery.of(context).platformBrightness == Brightness.dark);
    bool isSelected = _currentTheme == mode;
    return ListTile(
      leading:
          Icon(icon, color: isSelected ? PremiumColor.primary : Colors.grey),
      title: Text(title,
          style: GoogleFonts.plusJakartaSans(
              color: isSelected
                  ? PremiumColor.primary
                  : (isDark ? Colors.white : Colors.black87),
              fontWeight: isSelected ? FontWeight.bold : FontWeight.normal)),
      trailing: isSelected
          ? const Icon(Icons.check_circle, color: PremiumColor.primary)
          : null,
      onTap: () {
        setState(() => _currentTheme = mode);
        Navigator.pop(context);
      },
    );
  }
}
