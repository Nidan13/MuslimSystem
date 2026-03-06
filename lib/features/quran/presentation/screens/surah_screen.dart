import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/quran_provider.dart';
import '../widgets/ayah_card.dart';

class SurahScreen extends StatefulWidget {
  final int surahNumber;
  final String surahName;

  const SurahScreen(
      {super.key, required this.surahNumber, required this.surahName});

  @override
  State<SurahScreen> createState() => _SurahScreenState();
}

class _SurahScreenState extends State<SurahScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _controller.forward();

    Future.microtask(
        () => context.read<QuranProvider>().fetchSurah(widget.surahNumber));
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      backgroundColor:
          isDark ? const Color(0xFF121212) : const Color(0xFFF8F6F1),
      appBar: AppBar(
        title: Column(
          children: [
            Text(widget.surahName,
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold, fontSize: 18)),
            Text("Surah ke-${widget.surahNumber}",
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: Colors.grey)),
          ],
        ),
        elevation: 0,
        backgroundColor: isDark ? const Color(0xFF1E1E1E) : Colors.white,
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.settings_input_component_outlined),
            onPressed: () => context.read<QuranProvider>().toggleTranslation(),
            tooltip: 'Tampilkan Terjemahan',
          ),
        ],
      ),
      body: Consumer<QuranProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.errorMessage != null) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(32.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.cloud_off_rounded,
                        size: 64, color: Colors.grey),
                    const SizedBox(height: 16),
                    Text(
                      "Gagal Memuat Data",
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      provider.errorMessage!,
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: Colors.grey),
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton(
                      onPressed: () => provider.fetchSurah(widget.surahNumber),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 32, vertical: 12),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('Coba Lagi'),
                    ),
                  ],
                ),
              ),
            );
          }

          return ListView.builder(
            itemCount: provider.ayahs.length,
            padding: const EdgeInsets.symmetric(vertical: 16),
            itemBuilder: (context, index) {
              final ayah = provider.ayahs[index];

              // Animasi bertahap untuk setiap item
              final animation = CurvedAnimation(
                parent: _controller,
                curve: Interval((0.1 + (index * 0.05)).clamp(0.0, 1.0), 1.0,
                    curve: Curves.easeOut),
              );

              return AyahCard(
                ayah: ayah,
                showTranslation: provider.showTranslation,
                onPlayAudio: () {},
                onBookmark: () {},
                animation: animation,
              );
            },
          );
        },
      ),
    );
  }
}
