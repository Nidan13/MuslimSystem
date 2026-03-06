import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/quran.dart';
import '../services/quran_service.dart';
import '../theme/premium_color.dart';
import '../features/quran/core/utils/tajwid_parser.dart';

class QuranDetailScreen extends StatefulWidget {
  final Surah? surah;
  final int? juzNumber;
  final int? initialAyah;
  final int initialMode; // 0: Per Ayat, 1: Mushaf
  final ThemeMode initialTheme;

  const QuranDetailScreen({
    super.key,
    this.surah,
    this.juzNumber,
    this.initialAyah,
    required this.initialMode,
    this.initialTheme = ThemeMode.system,
  });

  @override
  State<QuranDetailScreen> createState() => _QuranDetailScreenState();
}

class _QuranDetailScreenState extends State<QuranDetailScreen> {
  final QuranService _quranService = QuranService();
  final AudioPlayer _audioPlayer = AudioPlayer();
  final ScrollController _scrollController = ScrollController();
  final PageController _pageController = PageController();

  List<Ayah> _ayahs = [];
  bool _isLoading = true;
  String? _errorMessage;
  int? _playingAyahIndex;
  int? _bookmarkedAyah;
  int _viewMode = 0;
  int _currentPage = 0; // Page index in Mushaf mode
  int _currentVisibleIndex = 0; // Topmost visible index in PerAyat mode

  List<List<Ayah>> _mushafPages = [];
  final int _ayahsPerPage = 8; // Fallback only

  // Theme state
  ThemeMode _currentTheme = ThemeMode.system;

  @override
  void initState() {
    super.initState();
    _currentTheme = widget.initialTheme;
    _viewMode = widget.initialMode;
    _scrollController.addListener(_handleScroll);
    _loadData();
    if (widget.surah != null) {
      _loadBookmark();
    }
  }

  @override
  void dispose() {
    if (_ayahs.isNotEmpty) {
      Ayah? activeAyah;
      if (_viewMode == 0) {
        if (_currentVisibleIndex >= 0 && _currentVisibleIndex < _ayahs.length) {
          activeAyah = _ayahs[_currentVisibleIndex];
        }
      } else {
        if (_mushafPages.isNotEmpty &&
            _currentPage >= 0 &&
            _currentPage < _mushafPages.length) {
          activeAyah = _mushafPages[_currentPage].first;
        } else {
          int startIndex = _currentPage * _ayahsPerPage;
          if (startIndex >= 0 && startIndex < _ayahs.length) {
            activeAyah = _ayahs[startIndex];
          }
        }
      }

      if (activeAyah != null) {
        _quranService.saveToHistory(
          activeAyah.surahNomor ?? widget.surah?.nomor ?? 1,
          activeAyah.nomorAyat,
          activeAyah.surahNamaLatin ?? widget.surah?.namaLatin ?? "Surah",
          widget.juzNumber,
        );
      }
    }

    _audioPlayer.dispose();
    _scrollController.removeListener(_handleScroll);
    _scrollController.dispose();
    _pageController.dispose();
    super.dispose();
  }

  void _handleScroll() {
    if (_viewMode != 0 || _ayahs.isEmpty) return;

    // Estimate visible index based on average item height (roughly 350-400px)
    // Ayah cards are quite tall now with Arabic text and translation
    double offset = _scrollController.offset;
    int estimatedIndex = (offset / 380).floor();

    if (estimatedIndex >= 0 && estimatedIndex < _ayahs.length) {
      if (estimatedIndex != _currentVisibleIndex) {
        setState(() {
          _currentVisibleIndex = estimatedIndex;
        });
      }
    }
  }

  Future<void> _loadBookmark() async {
    final bookmark = await _quranService.getBookmark();
    if (bookmark != null && bookmark['surahNo'] == widget.surah?.nomor) {
      if (mounted) {
        setState(() {
          _bookmarkedAyah = bookmark['ayahNo'];
        });
      }
    }
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });
    try {
      List<Ayah> fetchedAyahs = [];
      if (widget.surah != null) {
        final detail = await _quranService.getSurahDetail(widget.surah!.nomor);
        if (detail != null) {
          fetchedAyahs = detail.ayat;
        }
      } else if (widget.juzNumber != null) {
        fetchedAyahs = await _quranService.getJuzDetail(widget.juzNumber!);
      }

      if (mounted) {
        if (fetchedAyahs.isNotEmpty) {
          setState(() {
            _ayahs = fetchedAyahs;
            _isLoading = false;

            // Group ayahs by standard Mushaf page number
            _mushafPages.clear();
            if (_ayahs.isNotEmpty) {
              int? currentPageNum;
              List<Ayah> currentPageAyahs = [];

              for (var ayah in _ayahs) {
                // Determine page context. Default to sequential chunking if needed
                int ayahPage = ayah.halaman ??
                    ((_ayahs.indexOf(ayah) / _ayahsPerPage).floor() + 1);

                if (currentPageNum == null) {
                  currentPageNum = ayahPage;
                }

                if (ayahPage != currentPageNum) {
                  _mushafPages.add(currentPageAyahs);
                  currentPageAyahs = [];
                  currentPageNum = ayahPage;
                }
                currentPageAyahs.add(ayah);
              }
              if (currentPageAyahs.isNotEmpty) {
                _mushafPages.add(currentPageAyahs);
              }
            }
          });
          if (widget.initialAyah != null && _viewMode == 0) {
            WidgetsBinding.instance.addPostFrameCallback((_) {
              _scrollToAyah(widget.initialAyah!);
            });
          }
        } else {
          setState(() {
            _isLoading = false;
            _errorMessage = "Gagal memuat data ayat. Silakan coba lagi.";
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage = "Terjadi kesalahan: ${e.toString()}";
        });
      }
    }
  }

  void _scrollToAyah(int ayahNo) {
    int index = _ayahs.indexWhere((a) => a.nomorAyat == ayahNo);
    if (index >= 0) {
      _scrollController.animateTo(
        index * 250.0,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeInOut,
      );
    }
  }

  Future<void> _playAyah(Ayah ayah, int index) async {
    try {
      if (_playingAyahIndex == index) {
        await _audioPlayer.stop();
        setState(() => _playingAyahIndex = null);
        return;
      }
      setState(() => _playingAyahIndex = index);
      await _audioPlayer.play(UrlSource(ayah.audio));
      _audioPlayer.onPlayerComplete.listen((event) {
        if (mounted) {
          if (index + 1 < _ayahs.length) {
            _playAyah(_ayahs[index + 1], index + 1);
          } else {
            setState(() => _playingAyahIndex = null);
          }
        }
      });
    } catch (e) {
      debugPrint("Audio Error: $e");
    }
  }

  Future<void> _saveBookmark(Ayah ayah) async {
    if (widget.surah != null) {
      await _quranService.saveBookmark(
          widget.surah!.nomor, ayah.nomorAyat, widget.surah!.namaLatin);
      if (mounted) {
        setState(() => _bookmarkedAyah = ayah.nomorAyat);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("Berhasil ditandai: Ayat ${ayah.nomorAyat}"),
            backgroundColor: PremiumColor.primary,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    bool isDark = _currentTheme == ThemeMode.dark ||
        (_currentTheme == ThemeMode.system &&
            MediaQuery.of(context).platformBrightness == Brightness.dark);

    return WillPopScope(
      onWillPop: () async {
        Navigator.pop(context, _currentTheme);
        return false;
      },
      child: Theme(
        data: isDark ? ThemeData.dark() : ThemeData.light(),
        child: Scaffold(
          backgroundColor: isDark
              ? const Color(0xFF0F172A)
              : Colors.white, // NU Online Dark Blue
          appBar: _buildAppBar(isDark),
          body: _isLoading
              ? Center(
                  child: CircularProgressIndicator(color: PremiumColor.primary))
              : _errorMessage != null
                  ? _buildErrorPlaceholder()
                  : Column(
                      children: [
                        Expanded(
                          child: _viewMode == 0
                              ? _buildPerAyatView(isDark)
                              : _buildMushafPageView(isDark),
                        ),
                        _buildBottomPlayer(isDark),
                      ],
                    ),
        ),
      ),
    );
  }

  Widget _buildErrorPlaceholder() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.error_outline, size: 64, color: Colors.redAccent),
            const SizedBox(height: 16),
            Text(_errorMessage!,
                textAlign: TextAlign.center,
                style:
                    GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600)),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _loadData,
              style: ElevatedButton.styleFrom(
                  backgroundColor: PremiumColor.primary,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12))),
              child: Text("Coba Lagi",
                  style: GoogleFonts.plusJakartaSans(
                      color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  PreferredSizeWidget _buildAppBar(bool isDark) {
    Ayah? activeAyah;
    if (_ayahs.isNotEmpty) {
      if (_viewMode == 0) {
        activeAyah = _ayahs[_currentVisibleIndex];
      } else {
        if (_mushafPages.isNotEmpty &&
            _currentPage >= 0 &&
            _currentPage < _mushafPages.length) {
          activeAyah = _mushafPages[_currentPage].first;
        } else {
          // Fallback
          int startIndex = _currentPage * _ayahsPerPage;
          if (startIndex < _ayahs.length) {
            activeAyah = _ayahs[startIndex];
          }
        }
      }
    }

    String surahName =
        widget.surah?.namaLatin ?? activeAyah?.surahNamaLatin ?? "Al-Quran";
    int displayPage = activeAyah?.halaman ?? (_currentPage + 1);

    String infoText = widget.surah != null
        ? "Halaman $displayPage"
        : "Halaman $displayPage, Juz ${widget.juzNumber ?? '?'}";

    return AppBar(
      backgroundColor: isDark
          ? const Color(0xFF1E293B)
          : PremiumColor.primary, // Soft Slate for AppBar
      elevation: 0,
      centerTitle: true,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios, color: Colors.white, size: 20),
        onPressed: () => Navigator.pop(context, _currentTheme),
      ),
      titleSpacing: 0,
      title: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.1),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  infoText,
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 10,
                      color: Colors.white.withOpacity(0.8),
                      fontWeight: FontWeight.w600),
                ),
                Text(
                  surahName,
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                      color: Colors.white),
                ),
              ],
            ),
            const SizedBox(width: 4),
            const Icon(Icons.keyboard_arrow_down,
                color: Colors.white, size: 16),
          ],
        ),
      ),
      actions: [
        IconButton(
          icon: const Icon(Icons.color_lens_outlined, color: Colors.white),
          onPressed: _showTajwidGuide,
          tooltip: "Panduan Tajwid",
        ),
        IconButton(
          icon: const Icon(Icons.list, color: Colors.white),
          onPressed: _showViewModeDialog,
        ),
        IconButton(
          icon: const Icon(Icons.nightlight_round, color: Colors.white),
          onPressed: _showThemeDialog,
        ),
      ],
    );
  }

  Widget _buildPerAyatView(bool isDark) {
    return ListView.builder(
      controller: _scrollController,
      physics: const BouncingScrollPhysics(),
      itemCount: _ayahs.length,
      itemBuilder: (context, index) {
        final ayah = _ayahs[index];
        bool showHeader = false;
        bool showBismillah = false;

        int surahRef = ayah.surahNomor ?? widget.surah?.nomor ?? 0;

        // Determine if Surah Header is needed (Juz View)
        if (widget.surah == null) {
          if (index == 0) {
            showHeader = true;
          } else if (_ayahs[index].surahNomor != _ayahs[index - 1].surahNomor) {
            showHeader = true;
          }
        }

        // Determine if Bismillah is needed
        if (ayah.nomorAyat == 1) {
          // Bismillah is not needed for Al-Fatihah (1) or At-Tawbah (9)
          if (surahRef != 1 && surahRef != 9) {
            showBismillah = true;
          }
        }

        return Column(
          children: [
            if (showHeader) _buildSectionHeader(ayah, isDark),
            if (showBismillah) _buildBismillahHeader(isDark),
            _buildAyahItemNU(ayah, index, isDark),
          ],
        );
      },
    );
  }

  Widget _buildSectionHeader(Ayah ayah, bool isDark) {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.fromLTRB(20, 24, 20, 12),
      padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: isDark
              ? [const Color(0xFF1E1E1E), const Color(0xFF252525)]
              : [PremiumColor.primary.withOpacity(0.05), Colors.white],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isDark
              ? Colors.white.withOpacity(0.05)
              : PremiumColor.primary.withOpacity(0.1),
        ),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: PremiumColor.primary.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Text(
              "${ayah.surahNomor ?? ''}",
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.bold,
                color: PremiumColor.primary,
                fontSize: 14,
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "SURAH",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    fontWeight: FontWeight.w800,
                    color: PremiumColor.primary,
                    letterSpacing: 2,
                  ),
                ),
                Text(
                  ayah.surahNamaLatin ?? "Al-Quran",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
              ],
            ),
          ),
          const Icon(Icons.auto_awesome, color: PremiumColor.primary, size: 20),
        ],
      ),
    );
  }

  Widget _buildBismillahHeader(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 24),
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      decoration: BoxDecoration(
        color: isDark ? Colors.white.withOpacity(0.02) : Colors.grey.shade50,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isDark ? Colors.white.withOpacity(0.05) : Colors.grey.shade100,
        ),
      ),
      child: Center(
        child: Text(
          "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
          style: GoogleFonts.amiri(
            fontSize: 32,
            fontWeight: FontWeight.bold,
            color: isDark ? Colors.white : const Color(0xFF1A1A1A),
            height: 1.4,
          ),
          textAlign: TextAlign.center,
          textDirection: TextDirection.rtl,
        ),
      ),
    );
  }

  Widget _buildAyahItemNU(Ayah ayah, int index, bool isDark) {
    final isPlaying = _playingAyahIndex == index;

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: isPlaying
            ? const Color(0xFF1E293B).withOpacity(0.8)
            : (isDark ? const Color(0xFF1E293B) : Colors.white),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(
          color: isPlaying
              ? PremiumColor.primary.withOpacity(0.5)
              : (isDark
                  ? Colors.white.withOpacity(0.06)
                  : Colors.grey.shade200),
          width: 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: isDark
                ? Colors.black.withOpacity(0.2)
                : Colors.black.withOpacity(0.03),
            blurRadius: 15,
            offset: const Offset(0, 8),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Control Row
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            decoration: BoxDecoration(
              color:
                  isDark ? Colors.white.withOpacity(0.03) : Colors.grey.shade50,
              borderRadius:
                  const BorderRadius.vertical(top: Radius.circular(28)),
            ),
            child: Row(
              children: [
                _buildNumberBadge(ayah, isDark),
                const Spacer(),
                _buildActionIcon(
                  icon: isPlaying
                      ? Icons.pause_circle_filled
                      : Icons.play_circle_filled,
                  color: PremiumColor.primary,
                  onTap: () => _playAyah(ayah, index),
                  isLarge: true,
                ),
                const SizedBox(width: 12),
                _buildActionIcon(
                  icon: _bookmarkedAyah == ayah.nomorAyat
                      ? Icons.bookmark
                      : Icons.bookmark_border,
                  color: _bookmarkedAyah == ayah.nomorAyat
                      ? PremiumColor.primary
                      : Colors.grey,
                  onTap: () => _saveBookmark(ayah),
                ),
                const SizedBox(width: 12),
                _buildActionIcon(
                    icon: Icons.share_outlined,
                    color: Colors.grey,
                    onTap: () {}),
              ],
            ),
          ),

          // Arabic Text
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 28, 24, 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                RichText(
                  textAlign: TextAlign.right,
                  textDirection: TextDirection.rtl,
                  text: TextSpan(
                    children: TajwidParser.parse(
                      ayah.teksArab,
                      fontSize: 34,
                      lineHeight: 2.4,
                      defaultColor: isDark
                          ? Colors.white.withOpacity(0.95)
                          : const Color(0xFF1A1A1A),
                      isDarkMode: isDark,
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    _buildAyahEndMarker(ayah.nomorAyat, isDark),
                  ],
                ),
              ],
            ),
          ),

          // Indonesian Translation
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 0, 24, 28),
            child: Text(
              ayah.teksIndonesia,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 15,
                color: isDark
                    ? Colors.white.withOpacity(0.65)
                    : Colors.grey.shade800,
                height: 1.7,
                fontWeight: FontWeight.w500,
                letterSpacing: 0.2,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionIcon(
      {required IconData icon,
      required Color color,
      required VoidCallback onTap,
      bool isLarge = false}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Padding(
        padding: const EdgeInsets.all(4),
        child: Icon(icon, color: color, size: isLarge ? 28 : 22),
      ),
    );
  }

  Widget _buildNumberBadge(Ayah ayah, bool isDark) {
    int surahNo = widget.surah?.nomor ?? ayah.surahNomor ?? 0;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
      decoration: BoxDecoration(
        color: isDark
            ? Colors.white.withOpacity(0.05)
            : PremiumColor.primary.withOpacity(0.08),
        borderRadius: BorderRadius.circular(30),
        border: Border.all(
          color: isDark
              ? Colors.white.withOpacity(0.1)
              : PremiumColor.primary.withOpacity(0.2),
          width: 0.8,
        ),
      ),
      child: Text(
        "$surahNo:${ayah.nomorAyat}",
        style: GoogleFonts.plusJakartaSans(
          fontSize: 11,
          fontWeight: FontWeight.w800,
          color: isDark ? Colors.white.withOpacity(0.8) : PremiumColor.primary,
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Widget _buildMushafPageView(bool isDark) {
    int pageCount = _mushafPages.isNotEmpty
        ? _mushafPages.length
        : (_ayahs.length / _ayahsPerPage).ceil();

    return PageView.builder(
      controller: _pageController,
      reverse: true,
      onPageChanged: (idx) => setState(() => _currentPage = idx),
      itemCount: pageCount,
      itemBuilder: (context, index) {
        List<Ayah> pageAyahs = [];
        int pageNumber = index + 1;

        if (_mushafPages.isNotEmpty && index < _mushafPages.length) {
          pageAyahs = _mushafPages[index];
          pageNumber = pageAyahs.first.halaman ?? (index + 1);
        } else {
          // Fallback
          int start = index * _ayahsPerPage;
          int end = (start + _ayahsPerPage > _ayahs.length)
              ? _ayahs.length
              : start + _ayahsPerPage;
          if (start < _ayahs.length) {
            pageAyahs = _ayahs.sublist(start, end);
          }
        }

        if (pageAyahs.isEmpty) return const SizedBox();

        return Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                child: RichText(
                  textAlign: TextAlign.center,
                  textDirection: TextDirection.rtl,
                  text: TextSpan(
                    children: pageAyahs.expand((ayah) {
                      bool showBismillah = false;
                      int surahRef =
                          ayah.surahNomor ?? widget.surah?.nomor ?? 0;
                      if (ayah.nomorAyat == 1 &&
                          surahRef != 1 &&
                          surahRef != 9) {
                        showBismillah = true;
                      }

                      List<InlineSpan> spans = [];

                      if (showBismillah) {
                        spans.add(WidgetSpan(
                          alignment: PlaceholderAlignment.middle,
                          child: Container(
                            width: double.infinity,
                            margin: const EdgeInsets.only(bottom: 16, top: 12),
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            decoration: BoxDecoration(
                              color: isDark
                                  ? const Color(0xFF1E293B)
                                  : Colors.grey.shade50,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                  color: isDark
                                      ? Colors.white.withOpacity(0.05)
                                      : Colors.grey.shade200),
                            ),
                            child: Text(
                              "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
                              style: GoogleFonts.amiri(
                                fontSize: 26,
                                fontWeight: FontWeight.bold,
                                color: isDark
                                    ? Colors.white
                                    : const Color(0xFF1E1E1E),
                                height: 1.3,
                              ),
                              textAlign: TextAlign.center,
                              textDirection: TextDirection.rtl,
                            ),
                          ),
                        ));
                      }

                      spans.addAll(TajwidParser.parse(
                        ayah.teksArab + " ",
                        fontSize: 28,
                        lineHeight: 2.5,
                        defaultColor: isDark
                            ? Colors.white.withOpacity(0.9)
                            : Colors.black87,
                        isDarkMode: isDark,
                      ));

                      spans.add(TextSpan(
                        text: "\uFD3F${_toArabicDigits(ayah.nomorAyat)}\uFD3E ",
                        style: GoogleFonts.amiri(
                          fontSize:
                              24, // Make identical to main font size to perfectly align baselines!
                          fontWeight: FontWeight.bold,
                          color: PremiumColor.primary
                              .withOpacity(isDark ? 0.8 : 1.0),
                          height: 2.3,
                        ),
                      ));

                      return spans;
                    }).toList(),
                  ),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(bottom: 20),
              child: Text(
                "$pageNumber",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black87,
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildAyahEndMarker(int number, bool isDark) {
    Color primaryColor =
        isDark ? const Color(0xFF64B5F6) : PremiumColor.primary;
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 4),
      child: Stack(
        alignment: Alignment.center,
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(
                color: primaryColor.withOpacity(0.3),
                width: 1.5,
              ),
            ),
          ),
          Container(
            width: 30,
            height: 30,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: primaryColor.withOpacity(0.1),
            ),
          ),
          Text(
            _toArabicDigits(number),
            style: GoogleFonts.amiri(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : Colors.black87,
            ),
          ),
        ],
      ),
    );
  }

  String _toArabicDigits(int n) {
    const digits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    return n.toString().split('').map((e) => digits[int.parse(e)]).join();
  }

  Widget _buildBottomPlayer(bool isDark) {
    String currentSurah = widget.surah?.namaLatin ?? "Al-Quran";
    int totalAyat =
        widget.surah?.jumlahAyat ?? (_ayahs.isNotEmpty ? _ayahs.length : 0);

    int displayAyatNo = 1;
    int playingIdx = 0;

    if (_viewMode == 0) {
      playingIdx = _playingAyahIndex ?? 0;
      displayAyatNo = _ayahs.isNotEmpty ? _ayahs[playingIdx].nomorAyat : 1;
    } else {
      if (_playingAyahIndex != null) {
        playingIdx = _playingAyahIndex!;
        displayAyatNo = _ayahs[playingIdx].nomorAyat;
      } else {
        playingIdx = _currentPage * _ayahsPerPage;
        if (playingIdx < _ayahs.length) {
          displayAyatNo = _ayahs[playingIdx].nomorAyat;
        }
      }
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1E293B) : Colors.white,
        border: Border(
            top: BorderSide(
                color: isDark
                    ? Colors.white.withOpacity(0.08)
                    : Colors.grey.shade200)),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, -2))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            "Putar ayat",
            style: GoogleFonts.plusJakartaSans(
                fontSize: 12,
                color: isDark ? Colors.white70 : Colors.black87,
                fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 4),
          Row(
            children: [
              Expanded(
                child: Text(
                  "$currentSurah $displayAyatNo/$totalAyat",
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 13,
                      color: isDark ? Colors.white : Colors.black87,
                      fontWeight: FontWeight.w600),
                ),
              ),
              Icon(Icons.fast_rewind,
                  color: isDark ? Colors.white : Colors.black87, size: 30),
              const SizedBox(width: 20),
              GestureDetector(
                onTap: () {
                  if (_ayahs.isNotEmpty) {
                    if (_playingAyahIndex != null) {
                      _audioPlayer.stop();
                      setState(() => _playingAyahIndex = null);
                    } else {
                      _playAyah(_ayahs[playingIdx], playingIdx);
                    }
                  }
                },
                child: Icon(
                    _playingAyahIndex != null
                        ? Icons.pause_circle_filled
                        : Icons.play_circle_filled,
                    color: PremiumColor.primary,
                    size: 42),
              ),
              const SizedBox(width: 20),
              GestureDetector(
                onTap: () {
                  _audioPlayer.stop();
                  setState(() => _playingAyahIndex = null);
                },
                child: const Icon(Icons.stop, color: Colors.red, size: 30),
              ),
            ],
          ),
        ],
      ),
    );
  }

  void _showViewModeDialog() {
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
                      color: isDark ? Colors.white24 : Colors.grey.shade300,
                      borderRadius: BorderRadius.circular(2))),
            ),
            const SizedBox(height: 24),
            Text(
              "Ganti Tampilan Qur'an",
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.bold,
                fontSize: 18,
                color: isDark ? Colors.white : Colors.black87,
              ),
            ),
            const SizedBox(height: 24),
            _buildDialogOption(
              isDark: isDark,
              icon: Icons.menu_book_rounded,
              title: "Baca Mushaf (Per Halaman)",
              selected: _viewMode == 1,
              onTap: () {
                setState(() => _viewMode = 1);
                Navigator.pop(context);
              },
            ),
            _buildDialogOption(
              isDark: isDark,
              icon: Icons.format_list_bulleted_rounded,
              title: "Baca Per Ayat",
              selected: _viewMode == 0,
              onTap: () {
                setState(() => _viewMode = 0);
                Navigator.pop(context);
              },
            ),
            const SizedBox(height: 12),
          ],
        ),
      ),
    );
  }

  Widget _buildDialogOption(
      {required bool isDark,
      required IconData icon,
      required String title,
      required bool selected,
      required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 12),
        child: Row(
          children: [
            Icon(icon,
                color: selected ? PremiumColor.primary : Colors.grey, size: 24),
            const SizedBox(width: 16),
            Expanded(
              child: Text(
                title,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 15,
                  fontWeight: selected ? FontWeight.bold : FontWeight.w500,
                  color: selected
                      ? PremiumColor.primary
                      : (isDark ? Colors.white : Colors.black87),
                ),
              ),
            ),
            Container(
              width: 22,
              height: 22,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: selected ? PremiumColor.primary : Colors.grey.shade400,
                  width: selected ? 6 : 2,
                ),
              ),
            ),
          ],
        ),
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
            _buildThemeOption(
                "Terang", ThemeMode.light, Icons.wb_sunny_rounded),
            _buildThemeOption("Gelap", ThemeMode.dark, Icons.nightlight_round),
            _buildThemeOption("Sesuai Sistem", ThemeMode.system,
                Icons.brightness_auto_outlined),
          ],
        ),
      ),
    );
  }

  Widget _buildThemeOption(String title, ThemeMode mode, IconData icon) {
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

  void _showTajwidGuide() {
    bool isDark = _currentTheme == ThemeMode.dark ||
        (_currentTheme == ThemeMode.system &&
            MediaQuery.of(context).platformBrightness == Brightness.dark);

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) => DraggableScrollableSheet(
        initialChildSize: 0.6,
        minChildSize: 0.4,
        maxChildSize: 0.9,
        builder: (_, scrollController) => Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF1E293B) : Colors.white,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(32)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: isDark ? Colors.white24 : Colors.grey.shade300,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Icon(Icons.menu_book_rounded,
                      color: PremiumColor.primary, size: 28),
                  const SizedBox(width: 12),
                  Text(
                    "Panduan Tajwid Detail",
                    style: GoogleFonts.plusJakartaSans(
                      fontWeight: FontWeight.bold,
                      fontSize: 20,
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Expanded(
                child: ListView(
                  controller: scrollController,
                  children: [
                    _buildTajwidDetailItem(
                      "Qalaqah (Pantulan)",
                      "Suara memantul ketika sukun atau waqaf pada huruf (ق, ط, ب, ج, د).",
                      const Color(0xFFF44336),
                      isDark,
                    ),
                    _buildTajwidDetailItem(
                      "Ghunnah (Dengung)",
                      "Suara mendengung pada huruf Nun (ن) dan Mim (م) bertasydid.",
                      const Color(0xFFFFB74D),
                      isDark,
                    ),
                    _buildTajwidDetailItem(
                      "Ikhafa (Samar)",
                      "Menyamarkan suara Nun Sukun atau Tanwin ketika bertemu huruf-huruf tertentu.",
                      const Color(0xFFCE93D8),
                      isDark,
                    ),
                    _buildTajwidDetailItem(
                      "Mad (Panjang)",
                      "Memanjangkan suara pada huruf Alif, Wawu, atau Ya sesuai hukumnya.",
                      const Color(0xFF4FC3F7),
                      isDark,
                    ),
                    _buildTajwidDetailItem(
                      "Idgham (Melebur)",
                      "Memasukkan suara Nun Sukun atau Tanwin ke huruf berikutnya.",
                      const Color(0xFF81C784),
                      isDark,
                    ),
                    _buildTajwidDetailItem(
                      "Iqlab (Tukar)",
                      "Mengubah suara Nun Sukun atau Tanwin menjadi Mim jika bertemu Ba (ب).",
                      const Color(0xFF26C6DA),
                      isDark,
                    ),
                    _buildTajwidDetailItem(
                      "Wasl / Silent",
                      "Huruf yang tertulis dalam Rasm Utsmani namun tidak dibaca/dilafalkan.",
                      const Color(0xFF90A4AE),
                      isDark,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTajwidDetailItem(
      String title, String desc, Color color, bool isDark) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isDark ? Colors.white.withOpacity(0.03) : Colors.grey.shade50,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: color.withOpacity(0.2),
          width: 1,
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 14,
            height: 14,
            margin: const EdgeInsets.only(top: 4),
            decoration: BoxDecoration(
              color: color,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: color.withOpacity(0.3),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                )
              ],
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold,
                    fontSize: 15,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  desc,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13,
                    color: isDark ? Colors.white60 : Colors.black54,
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
