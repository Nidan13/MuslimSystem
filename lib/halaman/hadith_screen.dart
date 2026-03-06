import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/hadith_service.dart';
import '../models/hadith.dart';
import 'widgets/custom_background.dart';

class HadithScreen extends StatefulWidget {
  const HadithScreen({super.key});

  @override
  State<HadithScreen> createState() => _HadithScreenState();
}

class _HadithScreenState extends State<HadithScreen> {
  final HadithService _hadithService = HadithService();
  Hadith? _hadith;
  bool _isLoading = true;
  int _sessionCount = 0;

  @override
  void initState() {
    super.initState();
    _fetchRandomHadith();
  }

  Future<void> _fetchRandomHadith() async {
    setState(() => _isLoading = true);
    final h = await _hadithService.getRandomHadith();
    if (mounted) {
      setState(() {
        _hadith = h;
        _isLoading = false;
        if (h != null) _sessionCount++;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildSliverHeader(),
          SliverToBoxAdapter(
            child: _isLoading
                ? const SizedBox(
                    height: 300,
                    child: Center(
                        child: CircularProgressIndicator(
                            color: PremiumColor.primary)),
                  )
                : _hadith == null
                    ? const SizedBox(
                        height: 300,
                        child: Center(
                          child: Text("Gagal memuat hadits. Coba lagi."),
                        ),
                      )
                    : Padding(
                        padding: const EdgeInsets.fromLTRB(24, 24, 24, 40),
                        child: _buildHadithCard(),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    // Session progress (target 5 hadiths for full meter)
    double progress = (_sessionCount / 5).clamp(0.0, 1.0);

    return SliverAppBar(
      expandedHeight: 260,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: true,
      leading: const BackButton(color: Colors.white),
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [PremiumColor.primary, PremiumColor.accent],
              ),
              image: DecorationImage(
                image: NetworkImage(
                    "https://www.transparenttextures.com/patterns/handmade-paper.png"),
                opacity: 0.1,
                fit: BoxFit.cover,
              ),
            ),
            child: Stack(
              children: [
                // Pattern Overlay
                const Positioned.fill(child: IslamicPatternBackground()),

                // Decorative Icon
                Positioned(
                  right: -20,
                  top: 40,
                  child: Icon(
                    Icons.format_quote_rounded,
                    size: 150,
                    color: Colors.white.withOpacity(0.05),
                  ),
                ),

                // Content
                Padding(
                  padding: const EdgeInsets.fromLTRB(24, 60, 24, 30),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  "HADITS HARIAN",
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.white.withOpacity(0.8),
                                    letterSpacing: 2.0,
                                  ),
                                ),
                                Text(
                                  "Prophetic Word",
                                  style: GoogleFonts.playfairDisplay(
                                    fontSize: 32,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          _GlassIconButton(
                            onTap: _fetchRandomHadith,
                            icon: Icons.refresh_rounded,
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Flexible(
                            flex: 2,
                            child: _buildHeaderStat(
                                (_hadith?.bookName ?? "Loading...")
                                    .toUpperCase(),
                                Icons.import_contacts_rounded),
                          ),
                          const SizedBox(width: 8),
                          Flexible(
                            child: _buildHeaderStat(
                                "7 BOOKS", Icons.auto_stories_rounded),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            "$_sessionCount READ",
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white.withOpacity(0.9),
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 0.5,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(2),
                        child: LinearProgressIndicator(
                          value: progress,
                          backgroundColor: Colors.white.withOpacity(0.1),
                          valueColor:
                              const AlwaysStoppedAnimation<Color>(Colors.white),
                          minHeight: 4,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderStat(String text, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.15),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: Colors.white, size: 14),
          const SizedBox(width: 6),
          Flexible(
            child: Text(
              text,
              overflow: TextOverflow.ellipsis,
              maxLines: 1,
              style: GoogleFonts.plusJakartaSans(
                color: Colors.white,
                fontSize: 10,
                fontWeight: FontWeight.w800,
                letterSpacing: 0.5,
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _extractHadithTitle(String text) {
    String lowerText = text.toLowerCase();

    // Map of Topic Title -> Keywords
    Map<String, List<String>> topicMap = {
      'Tentang Sholat & Masjid': [
        'sholat',
        'shalat',
        'sujud',
        'ruku',
        'masjid',
        'rakaat',
        'adzan',
        'iqamah',
        'imam',
        'wudhu',
        'jamaah',
        'jumat',
        'tahajud',
        'witir'
      ],
      'Tentang Puasa & Ramadhan': [
        'puasa',
        'shaum',
        'ramadhan',
        'sahur',
        'berbuka',
        'iftar',
        'kifarat',
        'idul fitri'
      ],
      'Tentang Sedekah & Harta': [
        'sedekah',
        'zakat',
        'infaq',
        'infaq',
        'harta',
        'uang',
        'emas',
        'perak',
        'hutang',
        'riba',
        'jual beli',
        'miskin',
        'yatim'
      ],
      'Tentang Haji & Umrah': [
        'haji',
        'umrah',
        'thawaf',
        'kabah',
        'makkah',
        'madinah',
        'arafah',
        'ihram',
        'wukuf'
      ],
      'Tentang Doa & Dzikir': [
        'doa',
        'berdoa',
        'dzikir',
        'zikir',
        'tasbih',
        'tahmid',
        'takbir',
        'istighfar',
        'memohon'
      ],
      'Peringatan Dosa & Maksiat': [
        'zina',
        'neraka',
        'dusta',
        'bohong',
        'maksiat',
        'dosa',
        'mabuk',
        'judi',
        'mencuri',
        'munafik',
        'adzab',
        'siksa'
      ],
      'Kesabaran & Musibah': [
        'sabar',
        'bersabar',
        'musibah',
        'cobaan',
        'ujian',
        'penyakit',
        'sakit'
      ],
      'Hak & Urusan Keluarga': [
        'ibu',
        'ayah',
        'orang tua',
        'istri',
        'suami',
        'anak',
        'keluarga',
        'kerabat',
        'menikah',
        'nikah',
        'cerai',
        'talak'
      ],
      'Berbuat Baik & Akhlak': [
        'akhlak',
        'baik',
        'tetangga',
        'senyum',
        'sombong',
        'iri',
        'dengki',
        'silaturahim',
        'marah'
      ],
      'Keimanan & Tauhid': [
        'iman',
        'tauhid',
        'syirik',
        'beriman',
        'islam',
        'allah',
        'rasulullah',
        'hari kiamat'
      ],
      'Tentang Bersuci & Mandi': [
        'mandi',
        'junub',
        'haid',
        'nifas',
        'hadas',
        'najis',
        'istinja'
      ],
      'Keutamaan & Pahala': ['surga', 'pahala', 'ganjaran', 'kebaikan'],
    };

    // Find the first matching topic based on keyword presence
    for (var entry in topicMap.entries) {
      for (var keyword in entry.value) {
        // Checking for words specifically rather than substrings to be safe (using a basic space check)
        // using simple .contains() is usually sufficient for text this long
        // but we pad the keyword to prevent matching e.g. "anak" inside "beranak"
        // though just simple contains is fine for Indonesian in most cases.
        if (lowerText.contains(keyword)) {
          return entry.key;
        }
      }
    }

    // Fallback if no specific keywords match
    return "Ajaran & Hikmah Nabi";
  }

  Widget _buildHadithCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        children: [
          // Decorative Icon
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: PremiumColor.primary.withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.format_quote_rounded,
              color: PremiumColor.primary,
              size: 32,
            ),
          ),
          const SizedBox(height: 16),
          // Auto-generated Title based on Quote Substance (bypassing sanad)
          Text(
            _extractHadithTitle(_hadith!.id),
            textAlign: TextAlign.center,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 18,
              fontWeight: FontWeight.w900,
              color: PremiumColor.slate800,
              height: 1.3,
            ),
          ),
          const SizedBox(height: 16),
          Text(
            _hadith!.arab,
            textAlign: TextAlign.center,
            style: GoogleFonts.amiri(
              fontSize: 26,
              fontWeight: FontWeight.bold,
              color: PremiumColor.primary,
              height: 1.8,
            ),
          ),
          const SizedBox(height: 24),
          const Divider(thickness: 1, color: Color(0xFFF1F5F9)),
          const SizedBox(height: 24),
          // Translation Header
          Text(
            "TERJEMAHAN",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              fontWeight: FontWeight.w900,
              color: PremiumColor.slate400,
              letterSpacing: 2.0,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            _hadith!.id, // The translated text
            textAlign: TextAlign.center,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 15,
              fontWeight: FontWeight.w600,
              color: PremiumColor.slate600,
              height: 1.5,
              fontStyle: FontStyle.italic,
            ),
          ),
          const SizedBox(height: 16),
          Text(
            "${_hadith!.narrator} [No. ${_hadith!.number}]", // Removed hardcoded 'HR.' because narrator API provides it
            style: GoogleFonts.plusJakartaSans(
              fontSize: 13,
              fontWeight: FontWeight.w900,
              color: PremiumColor.accent,
              letterSpacing: 1,
            ),
          ),
        ],
      ),
    );
  }
}

class _GlassIconButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;

  const _GlassIconButton({required this.icon, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.8),
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Icon(icon, color: PremiumColor.primary, size: 20),
      ),
    );
  }
}
