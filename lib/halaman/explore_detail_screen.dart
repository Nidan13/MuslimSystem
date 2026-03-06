import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/islamic_content.dart';
import 'widgets/custom_background.dart';

class ExploreDetailScreen extends StatelessWidget {
  final IslamicContent content;

  const ExploreDetailScreen({super.key, required this.content});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // Background Pattern
          const Positioned.fill(child: IslamicPatternBackground()),

          CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              _buildHeader(context),
              SliverToBoxAdapter(
                child: Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Arabic Name Hero
                      if (content.arabicName != null) ...[
                        Center(
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 32, vertical: 40),
                            decoration: BoxDecoration(
                              color: PremiumColor.primary.withOpacity(0.04),
                              borderRadius: BorderRadius.circular(32),
                            ),
                            child: Text(
                              content.arabicName!,
                              style: GoogleFonts.amiri(
                                fontSize: 56,
                                fontWeight: FontWeight.bold,
                                color: PremiumColor.primary,
                                height: 1.2,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ),
                        ),
                        const SizedBox(height: 48),
                      ],

                      _buildSectionLabel("RIWAYAT & KISAH"),
                      const SizedBox(height: 20),

                      Text(
                        content.description,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                          color: PremiumColor.slate800.withOpacity(0.9),
                          height: 1.9,
                        ),
                      ),

                      if (content.additionalInfo != null) ...[
                        const SizedBox(height: 48),
                        _buildSectionLabel("DETAIL TAMBAHAN"),
                        const SizedBox(height: 20),
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(28),
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              colors: [
                                PremiumColor.primary.withOpacity(0.06),
                                PremiumColor.primary.withOpacity(0.02),
                              ],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            borderRadius: BorderRadius.circular(32),
                            border: Border.all(
                                color: PremiumColor.primary.withOpacity(0.1)),
                          ),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Icon(Icons.info_outline_rounded,
                                  color: PremiumColor.primary, size: 20),
                              const SizedBox(width: 16),
                              Expanded(
                                child: Text(
                                  content.additionalInfo!,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                    color: PremiumColor.primary,
                                    height: 1.6,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                      const SizedBox(height: 120),
                    ],
                  ),
                ),
              ),
            ],
          ),

          // Custom Back Button
          Positioned(
            top: 50,
            left: 20,
            child: _CircleBackButton(onTap: () => Navigator.pop(context)),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return SliverAppBar(
      expandedHeight: 280,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          children: [
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [PremiumColor.primary, PremiumColor.accent],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
            ),
            const Positioned.fill(child: IslamicPatternBackground()),
            Positioned(
              right: -40,
              bottom: -40,
              child: Opacity(
                opacity: 0.1,
                child: Icon(Icons.auto_stories_rounded,
                    size: 280, color: Colors.white),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 0, 24, 40),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.end,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      content.subtitle.toUpperCase(),
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: Colors.white70,
                        letterSpacing: 2,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    content.title,
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 38,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionLabel(String label) {
    return Row(
      children: [
        Container(
          width: 24,
          height: 3,
          decoration: BoxDecoration(
            color: PremiumColor.highlight,
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 12),
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12,
            fontWeight: FontWeight.w900,
            color: PremiumColor.primary.withOpacity(0.4),
            letterSpacing: 1.5,
          ),
        ),
      ],
    );
  }
}

class _CircleBackButton extends StatelessWidget {
  final VoidCallback onTap;
  const _CircleBackButton({required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 45,
        height: 45,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.2),
          shape: BoxShape.circle,
          border: Border.all(color: Colors.white.withOpacity(0.3)),
        ),
        child:
            const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 22),
      ),
    );
  }
}
