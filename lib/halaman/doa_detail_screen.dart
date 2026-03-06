import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:share_plus/share_plus.dart';
import '../models/doa.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';

class DoaDetailScreen extends StatelessWidget {
  final Doa doa;

  const DoaDetailScreen({super.key, required this.doa});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: Stack(
        children: [
          // 1. Pattern Background
          Positioned.fill(child: const IslamicPatternBackground()),

          // 2. Header Background
          MuqarnasHeaderBackground(height: 300),

          // 3. Content
          Column(
            children: [
              _buildAppBar(context),
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.fromLTRB(24, 0, 24, 40),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      _buildDoaHeader(),
                      const SizedBox(height: 32),
                      if (doa.arab.isNotEmpty) ...[
                        _buildArabicCard(),
                        const SizedBox(height: 24),
                      ],
                      if (doa.latin.isNotEmpty) ...[
                        _buildTranslationSection("Latin", doa.latin,
                            italic: true),
                        const SizedBox(height: 24),
                      ],
                      if (doa.terjemah.isNotEmpty)
                        _buildTranslationSection("Terjemahan", doa.terjemah),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAppBar(BuildContext context) {
    return SafeArea(
      bottom: false,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded,
                  color: PremiumColor.slate800, size: 20),
              onPressed: () => Navigator.pop(context),
            ),
            IconButton(
              icon: const Icon(Icons.share_rounded,
                  color: PremiumColor.slate800, size: 20),
              onPressed: () {
                Share.share("${doa.judul}\n\n${doa.arab}\n\n${doa.terjemah}");
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDoaHeader() {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: PremiumColor.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Text(
            "DOA PILIHAN",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              color: PremiumColor.primary,
              letterSpacing: 2,
            ),
          ),
        ),
        const SizedBox(height: 20),
        Text(
          doa.judul,
          textAlign: TextAlign.center,
          style: GoogleFonts.playfairDisplay(
            fontSize: 28,
            fontWeight: FontWeight.w900,
            color: PremiumColor.slate800,
            height: 1.2,
          ),
        ),
      ],
    );
  }

  Widget _buildArabicCard() {
    return Container(
      padding: const EdgeInsets.all(32),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.08),
            blurRadius: 40,
            offset: const Offset(0, 20),
          ),
        ],
        border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
      ),
      child: Text(
        doa.arab,
        textAlign: TextAlign.center,
        textDirection: TextDirection.rtl,
        style: GoogleFonts.amiri(
          fontSize: 30, // Slightly bigger
          fontWeight: FontWeight.bold,
          color: PremiumColor.primary,
          height: 1.8,
        ),
      ),
    );
  }

  Widget _buildTranslationSection(String title, String content,
      {bool italic = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              width: 4,
              height: 16,
              decoration: BoxDecoration(
                color: PremiumColor.highlight,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(width: 8),
            Text(
              title.toUpperCase(),
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                fontWeight: FontWeight.w900,
                color: PremiumColor.slate600,
                letterSpacing: 1.5,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
            border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
          ),
          child: Text(
            content,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 15,
              fontWeight: FontWeight.w600,
              color: PremiumColor.slate800,
              height: 1.6,
              fontStyle: italic ? FontStyle.italic : FontStyle.normal,
            ),
          ),
        ),
      ],
    );
  }
}
