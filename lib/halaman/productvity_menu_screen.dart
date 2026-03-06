import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';

import 'daily_task_screen.dart';
import 'habit_screen.dart';
import 'todo_screen.dart';
import 'quest_screen.dart';

class ProductivityMenuScreen extends StatefulWidget {
  final bool showBack;
  const ProductivityMenuScreen({super.key, this.showBack = false});

  @override
  State<ProductivityMenuScreen> createState() => _ProductivityMenuScreenState();
}

class _ProductivityMenuScreenState extends State<ProductivityMenuScreen> {
  final List<Map<String, String>> _motivationQuotes = [
    {
      "quote":
          "Amalan yang paling dicintai Allah adalah yang terus menerus meski sedikit.",
      "source": "HR. Muslim"
    },
    {
      "quote":
          "Manfaatkan lima perkara sebelum datang lima perkara: Masa mudamu sebelum masa tuamu...",
      "source": "HR. Al-Hakim"
    },
    {
      "quote":
          "Waktu itu seperti pedang, jika kamu tidak menggunakannya untuk memotong, ia akan memotongmu.",
      "source": "Imam Syafi'i"
    },
    {
      "quote":
          "Sebaik-baik manusia adalah yang paling bermanfaat bagi orang lain.",
      "source": "HR. Ahmad"
    },
    {
      "quote":
          "Hari ini harus lebih baik dari kemarin, dan esok harus lebih baik dari hari ini.",
      "source": "Hikmah Islami"
    },
  ];

  late Map<String, String> _currentQuote;

  @override
  void initState() {
    super.initState();
    _currentQuote = (_motivationQuotes..shuffle()).first;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAF9),
      body: CustomScrollView(
        slivers: [
          _buildSliverHeader(),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 32),
                  // Motivation Quote Banner (Glassmorphism)
                  _buildMotivationBanner(),
                  const SizedBox(height: 32),
                  _buildHeaderHero(),
                  const SizedBox(height: 40),

                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      _buildSectionLabel("DATA PRODUKTIVITAS"),
                      _buildLevelBadge(),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Main Grid (Bento Style)
                  Row(
                    children: [
                      Expanded(
                        child: _buildBentoCard(
                          context,
                          title: "Misi Harian",
                          desc: "Raih EXP & naik level",
                          icon: Icons.auto_awesome_rounded,
                          color: const Color(0xFFF97316),
                          height: 220,
                          onTap: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => const QuestScreen())),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: _buildBentoCard(
                          context,
                          title: "Habit",
                          desc: "Bangun disiplin diri",
                          icon: Icons.cached_rounded,
                          color: const Color(0xFF10B981),
                          height: 220,
                          onTap: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => const HabitScreen())),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  _buildWideCard(
                    context,
                    title: "To-Do List",
                    subtitle: "Strategi Harian",
                    desc: "Atur target harianmu agar terukur",
                    icon: Icons.check_circle_outline_rounded,
                    color: const Color(0xFF0EA5E9),
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const ToDoScreen())),
                  ),
                  const SizedBox(height: 16),
                  _buildWideCard(
                    context,
                    title: "Jurnal Harian",
                    subtitle: "Spiritual Log",
                    desc: "Dokumentasikan progres spiritualmu",
                    icon: Icons.auto_stories_rounded,
                    color: const Color(0xFF8B5CF6),
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => const DailyTaskScreen())),
                  ),
                  const SizedBox(height: 100),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 220,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  Color(0xFF0F172A),
                  PremiumColor.primary,
                ],
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
                const Positioned.fill(child: IslamicPatternBackground()),

                // Decorative Glows
                Positioned(
                  top: -50,
                  right: -50,
                  child: Container(
                    width: 200,
                    height: 200,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: PremiumColor.highlight.withOpacity(0.15),
                    ),
                  ),
                ),

                Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          if (widget.showBack)
                            GestureDetector(
                              onTap: () => Navigator.pop(context),
                              child: Container(
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: Colors.white.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(color: Colors.white12),
                                ),
                                child: const Icon(
                                    Icons.arrow_back_ios_new_rounded,
                                    size: 16,
                                    color: Colors.white),
                              ),
                            ),
                          Text(
                            "PRODUKTIVITAS",
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              fontWeight: FontWeight.w900,
                              color: PremiumColor.highlight,
                              letterSpacing: 3,
                            ),
                          ),
                          const SizedBox(width: 40),
                        ],
                      ),
                      const SizedBox(height: 24),
                      Text(
                        "Pusat Produktivitas",
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 32,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        "Optimalkan manajemen amalan & targetmu",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: Colors.white.withOpacity(0.7),
                          letterSpacing: 0.5,
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

  Widget _buildMotivationBanner() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.9),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.white, width: 2),
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
              const Icon(Icons.format_quote_rounded,
                  color: PremiumColor.primary, size: 24),
              const SizedBox(width: 8),
              Text(
                "SPIRITUAL BOOST",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: PremiumColor.primary.withOpacity(0.6),
                  letterSpacing: 2,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            _currentQuote['quote']!,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 14,
              fontWeight: FontWeight.w700,
              color: PremiumColor.primary,
              height: 1.5,
              fontStyle: FontStyle.italic,
            ),
          ),
          const SizedBox(height: 8),
          Align(
            alignment: Alignment.bottomRight,
            child: Text(
              "- ${_currentQuote['source']}",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.w800,
                color: PremiumColor.primary.withOpacity(0.4),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLevelBadge() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            PremiumColor.primary,
            PremiumColor.primary.withOpacity(0.8),
          ],
        ),
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.2),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.military_tech_rounded,
              color: PremiumColor.highlight, size: 16),
          const SizedBox(width: 6),
          Text(
            "NOVICE RANK",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              color: Colors.white,
              letterSpacing: 1,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHeaderHero() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          "Master Your\nFocus",
          style: GoogleFonts.playfairDisplay(
            fontSize: 38,
            fontWeight: FontWeight.w900,
            color: PremiumColor.primary,
            height: 1.1,
          ),
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Container(
              height: 4,
              width: 40,
              decoration: BoxDecoration(
                color: PremiumColor.highlight,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(width: 8),
            Container(
              height: 4,
              width: 12,
              decoration: BoxDecoration(
                color: PremiumColor.highlight.withOpacity(0.3),
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        Text(
          "Sistem manajemen terpadu untuk mengoptimalkan amalan spiritual dan pencapaian duniawimu.",
          style: GoogleFonts.plusJakartaSans(
            fontSize: 15,
            color: Colors.black54,
            height: 1.6,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Widget _buildSectionLabel(String text) {
    return Text(
      text,
      style: GoogleFonts.plusJakartaSans(
        fontSize: 11,
        fontWeight: FontWeight.w900,
        color: PremiumColor.primary.withOpacity(0.3),
        letterSpacing: 1.5,
      ),
    );
  }

  Widget _buildBentoCard(
    BuildContext context, {
    required String title,
    required String desc,
    required IconData icon,
    required Color color,
    required double height,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: () {
        HapticFeedback.mediumImpact();
        onTap();
      },
      child: Container(
        height: height,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(32),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.08),
              blurRadius: 30,
              offset: const Offset(0, 15),
            )
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(32),
          child: Stack(
            children: [
              // Decorative background circle
              Positioned(
                bottom: -30,
                right: -30,
                child: Container(
                  width: 120,
                  height: 120,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: LinearGradient(
                      colors: [
                        color.withOpacity(0.0),
                        color.withOpacity(0.08),
                      ],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                  ),
                ),
              ),

              Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [
                            color.withOpacity(0.15),
                            color.withOpacity(0.05),
                          ],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: color.withOpacity(0.1)),
                      ),
                      child: Icon(icon, color: color, size: 28),
                    ),
                    const Spacer(),
                    Text(
                      title,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 20,
                        fontWeight: FontWeight.w900,
                        color: PremiumColor.primary,
                        letterSpacing: -0.5,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      desc,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        color: Colors.black.withOpacity(0.4),
                        fontWeight: FontWeight.w600,
                        height: 1.4,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Container(
                      height: 3,
                      width: 24,
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.3),
                        borderRadius: BorderRadius.circular(2),
                      ),
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

  Widget _buildWideCard(
    BuildContext context, {
    required String title,
    required String subtitle,
    required String desc,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        onTap();
      },
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(30),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 25,
              offset: const Offset(0, 5),
            )
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(30),
          child: Stack(
            children: [
              // Subtle side gradient
              Positioned(
                left: 0,
                top: 0,
                bottom: 0,
                width: 4,
                child: Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [color, color.withOpacity(0.5)],
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                    ),
                  ),
                ),
              ),

              Padding(
                padding: const EdgeInsets.all(22),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: color.withOpacity(0.05)),
                      ),
                      child: Icon(icon, color: color, size: 24),
                    ),
                    const SizedBox(width: 20),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Text(
                                subtitle.toUpperCase(),
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 9,
                                  fontWeight: FontWeight.w900,
                                  color: color,
                                  letterSpacing: 1.5,
                                ),
                              ),
                              const SizedBox(width: 10),
                              Container(
                                width: 4,
                                height: 4,
                                decoration: BoxDecoration(
                                  color: color.withOpacity(0.2),
                                  shape: BoxShape.circle,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            title,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 18,
                              fontWeight: FontWeight.w900,
                              color: PremiumColor.primary,
                              letterSpacing: -0.5,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            desc,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              color: Colors.black.withOpacity(0.4),
                              fontWeight: FontWeight.w500,
                              height: 1.3,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.05),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(Icons.arrow_forward_ios_rounded,
                          size: 12, color: color.withOpacity(0.5)),
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
