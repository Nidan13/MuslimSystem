import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../data/models/ayah_model.dart';
import '../../core/utils/tajwid_parser.dart';

class AyahCard extends StatelessWidget {
  final AyahModel ayah;
  final bool showTranslation;
  final VoidCallback onPlayAudio;
  final VoidCallback onBookmark;
  final Animation<double> animation;

  const AyahCard({
    super.key,
    required this.ayah,
    required this.showTranslation,
    required this.onPlayAudio,
    required this.onBookmark,
    required this.animation,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final primaryColor = Theme.of(context).primaryColor;

    return FadeTransition(
      opacity: animation,
      child: SlideTransition(
        position: animation.drive(
          Tween<Offset>(
            begin: const Offset(0, 0.05),
            end: Offset.zero,
          ).chain(CurveTween(curve: Curves.easeOutCubic)),
        ),
        child: Container(
          width: double.infinity,
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF1E1E1E) : Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(isDark ? 0.3 : 0.04),
                blurRadius: 20,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Header Section with Light Background
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                decoration: BoxDecoration(
                  color: primaryColor.withOpacity(0.05),
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(20),
                    topRight: Radius.circular(20),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    // Ayah Number
                    Container(
                      width: 32,
                      height: 32,
                      decoration: BoxDecoration(
                        color: primaryColor,
                        shape: BoxShape.circle,
                      ),
                      child: Center(
                        child: Text(
                          '${ayah.numberInSurah}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),

                    // Top Actions
                    Row(
                      children: [
                        IconButton(
                          icon: Icon(Icons.share_rounded,
                              size: 20,
                              color:
                                  isDark ? Colors.white54 : Colors.grey[600]),
                          onPressed: () {}, // Share logic
                        ),
                        IconButton(
                          icon: Icon(Icons.bookmark_border_rounded,
                              size: 20,
                              color:
                                  isDark ? Colors.white54 : Colors.grey[600]),
                          onPressed: onBookmark,
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              Padding(
                padding: const EdgeInsets.fromLTRB(24, 24, 24, 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Arabic Text
                    Directionality(
                      textDirection: TextDirection.rtl,
                      child: RichText(
                        textAlign: TextAlign.right,
                        text: TextSpan(
                          children: TajwidParser.parse(
                            ayah.text,
                            fontSize: 32,
                            lineHeight: 2.3,
                            defaultColor: isDark
                                ? Colors.white.withOpacity(0.9)
                                : Colors.black87,
                            isDarkMode: isDark,
                          ),
                        ),
                      ),
                    ),

                    if (showTranslation) ...[
                      const SizedBox(height: 24),
                      const Divider(height: 1),
                      const SizedBox(height: 20),
                      Text(
                        ayah.translation,
                        style: GoogleFonts.inter(
                          fontSize: 15,
                          height: 1.6,
                          color: isDark ? Colors.white70 : Colors.black54,
                          fontWeight: FontWeight.w400,
                        ),
                      ),
                    ],

                    const SizedBox(height: 16),

                    // Audio Button at bottom
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        InkWell(
                          onTap: onPlayAudio,
                          borderRadius: BorderRadius.circular(30),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 8),
                            decoration: BoxDecoration(
                              border: Border.all(
                                  color: primaryColor.withOpacity(0.2)),
                              borderRadius: BorderRadius.circular(30),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(Icons.play_arrow_rounded,
                                    color: primaryColor, size: 20),
                                const SizedBox(width: 4),
                                Text("Play Audio",
                                    style: TextStyle(
                                        color: primaryColor,
                                        fontSize: 12,
                                        fontWeight: FontWeight.w600)),
                              ],
                            ),
                          ),
                        ),
                      ],
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
}
