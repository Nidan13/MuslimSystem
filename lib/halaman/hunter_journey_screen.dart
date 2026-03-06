import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../models/user_profile.dart';
import 'widgets/custom_background.dart';

class HunterJourneyScreen extends StatelessWidget {
  final UserProfile profile;

  const HunterJourneyScreen({super.key, required this.profile});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          const MuqarnasHeaderBackground(height: 300),
          CustomScrollView(
            slivers: [
              _buildAppBar(context),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: 24),
                      _buildNarrativeHeader(),
                      const SizedBox(height: 32),
                      _buildSectionTitle("VICTORIES HARI INI"),
                      const SizedBox(height: 16),
                      _buildDailyVictories(),
                      const SizedBox(height: 32),
                      _buildSectionTitle("LEGACY & MILESTONES"),
                      const SizedBox(height: 16),
                      _buildMilestones(),
                      const SizedBox(height: 100),
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
    return SliverAppBar(
      backgroundColor: Colors.transparent,
      elevation: 0,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new_rounded,
            color: PremiumColor.primary),
        onPressed: () => Navigator.pop(context),
      ),
      title: Text(
        "JEJAK HUNTER",
        style: GoogleFonts.plusJakartaSans(
          fontSize: 16,
          fontWeight: FontWeight.w800,
          color: PremiumColor.primary,
          letterSpacing: 2,
        ),
      ),
      centerTitle: true,
      floating: true,
    );
  }

  Widget _buildNarrativeHeader() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 20,
            offset: const Offset(0, 10),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: PremiumColor.primary.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.auto_stories_rounded,
                    color: PremiumColor.primary, size: 28),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      "Perjalanan Spiritual",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        color: PremiumColor.slate400,
                      ),
                    ),
                    Text(
                      profile.username,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                        color: PremiumColor.slate800,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          Text(
            "\"Setiap langkah kecil dalam ketaatan adalah kemenangan besar di hadapan Sang Khalik. Teruskan perjuanganmu, wahai Hunter!\"",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 15,
              fontWeight: FontWeight.w500,
              color: PremiumColor.slate600,
              fontStyle: FontStyle.italic,
              height: 1.6,
            ),
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _buildHeroStat("STREAK", "${profile.stats.streak}", "Hari"),
              _buildHeroStat("LEVEL", "${profile.level}", "Hunter"),
              _buildHeroStat("RANK", profile.rank, ""),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildHeroStat(String label, String value, String unit) {
    return Column(
      children: [
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 10,
            fontWeight: FontWeight.w800,
            color: PremiumColor.slate400,
            letterSpacing: 1,
          ),
        ),
        const SizedBox(height: 4),
        RichText(
          text: TextSpan(
            children: [
              TextSpan(
                text: value,
                style: GoogleFonts.robotoMono(
                  fontSize: 20,
                  fontWeight: FontWeight.w900,
                  color: PremiumColor.primary,
                ),
              ),
              if (unit.isNotEmpty)
                TextSpan(
                  text: " $unit",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: PremiumColor.slate400,
                  ),
                ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: GoogleFonts.plusJakartaSans(
        fontSize: 12,
        fontWeight: FontWeight.w800,
        color: PremiumColor.primary,
        letterSpacing: 1.5,
      ),
    );
  }

  Widget _buildDailyVictories() {
    // Mock data for narrative victories based on stats
    final victories = [
      if (profile.stats.attributes?.strength != null &&
          profile.stats.attributes!.strength > 0)
        _VictoryItem(
          title: "Penjaga Tiang Agama",
          description:
              "Menyelesaikan ${profile.stats.attributes!.strength} Sholat Fardhu secara konsisten.",
          icon: Icons.mosque_rounded,
          color: Colors.redAccent,
        ),
      if (profile.stats.streak > 0)
        _VictoryItem(
          title: "Hunter yang Gigih",
          description:
              "Mempertahankan streak selama ${profile.stats.streak} hari berturut-turut.",
          icon: Icons.local_fire_department_rounded,
          color: Colors.orange,
        ),
      _VictoryItem(
        title: "Pencari Ilmu",
        description: "Aktif meningkatkan pemahaman agama melalui tugas harian.",
        icon: Icons.menu_book_rounded,
        color: Colors.blueAccent,
      ),
    ];

    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: victories.length,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (context, index) {
        final item = victories[index];
        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.black.withOpacity(0.05)),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: item.color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(item.icon, color: item.color, size: 24),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      item.title,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                        color: PremiumColor.slate800,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      item.description,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w500,
                        color: PremiumColor.slate500,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildMilestones() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: PremiumColor.primary.withOpacity(0.05),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: PremiumColor.primary.withOpacity(0.1)),
      ),
      child: Column(
        children: [
          _buildMilestoneRow("RANK UP", "Warrior of Faith",
              "150 XP lagi untuk naik rank!", Icons.trending_up_rounded, true),
          const Divider(height: 32),
          _buildMilestoneRow("ACHIEVEMENT", "First Week Streak",
              "Selesaikan 7 hari streak pertama.", Icons.stars_rounded, false),
          const Divider(height: 32),
          _buildMilestoneRow(
              "QUEST",
              "Al-Kahfi Friday",
              "Menyelesaikan surah Al-Kahfi di hari Jumat.",
              Icons.bookmark_rounded,
              false),
        ],
      ),
    );
  }

  Widget _buildMilestoneRow(String tag, String title, String subtitle,
      IconData icon, bool isAchieved) {
    return Row(
      children: [
        Icon(icon,
            color: isAchieved ? PremiumColor.accent : PremiumColor.slate400,
            size: 28),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                tag,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color:
                      isAchieved ? PremiumColor.accent : PremiumColor.slate400,
                  letterSpacing: 1,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                title,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: PremiumColor.slate800,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                subtitle,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  color: PremiumColor.slate500,
                ),
              ),
            ],
          ),
        ),
        if (isAchieved)
          const Icon(Icons.check_circle_rounded, color: Colors.green, size: 20),
      ],
    );
  }
}

class _VictoryItem {
  final String title;
  final String description;
  final IconData icon;
  final Color color;

  _VictoryItem({
    required this.title,
    required this.description,
    required this.icon,
    required this.color,
  });
}
