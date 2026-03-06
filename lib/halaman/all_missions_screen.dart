import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';
import 'widgets/custom_background.dart';

class AllMissionsScreen extends StatefulWidget {
  const AllMissionsScreen({super.key});

  @override
  State<AllMissionsScreen> createState() => _AllMissionsScreenState();
}

class _AllMissionsScreenState extends State<AllMissionsScreen> {
  String _selectedFilter = 'all';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildSliverHeader(),

          // --- Filter Sections ---
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(24, 24, 24, 0),
              child: Row(
                children: [
                  _buildFilterChip("ALL", 'all'),
                  const SizedBox(width: 8),
                  _buildFilterChip("WAJIB", 'wajib'),
                  const SizedBox(width: 8),
                  _buildFilterChip("TUGAS", 'tugas'),
                ],
              ),
            ),
          ),

          SliverPadding(
            padding: const EdgeInsets.fromLTRB(24, 24, 24, 100),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                if (_selectedFilter == 'all' || _selectedFilter == 'wajib') ...[
                  // --- Mandatory Quests Section ---
                  const _SectionHeader(
                    title: "Ibadah Wajib",
                    icon: Icons.verified_rounded,
                    badgeText: "WAJIB",
                  ),
                  const SizedBox(height: 16),

                  const _MandatoryQuestItem(
                    title: "Sholat Subuh",
                    reward: "+20 Iman",
                    icon: Icons.wb_twilight_rounded,
                    iconBg: Color(0xFFE0F2F1),
                    iconColor: PremiumColor.primary,
                    status: QuestStatus.completed,
                  ),
                  const SizedBox(height: 12),

                  const _MandatoryQuestItem(
                    title: "Sholat Dzuhur",
                    reward: "+20 Iman",
                    icon: Icons.sunny,
                    iconBg: Color(0xFFFFF7ED),
                    iconColor: PremiumColor.accent,
                    status: QuestStatus.upcoming,
                    progress: 0.33,
                  ),
                  const SizedBox(height: 12),

                  const _MandatoryQuestItem(
                    title: "Sholat Ashar",
                    reward: "+20 Iman",
                    icon: Icons.nights_stay_rounded,
                    iconBg: Color(0xFFF1F5F9),
                    iconColor: PremiumColor.slate400,
                    status: QuestStatus.locked,
                  ),
                  if (_selectedFilter == 'all') const SizedBox(height: 32),
                ],
                if (_selectedFilter == 'all' || _selectedFilter == 'tugas') ...[
                  // --- Side Quests Section ---
                  const _SectionHeader(
                    title: "Tugas Tambahan",
                    icon: Icons.auto_awesome_rounded,
                    badgeText: "OPTIONAL",
                  ),
                  const SizedBox(height: 16),

                  const _SideQuestItem(
                    title: "Baca 1 Halaman Qur'an",
                    reward: "+50 EXP",
                    icon: Icons.menu_book_rounded,
                    progress: 0,
                    total: 1,
                    progressText: "0/1",
                    btnText: "Claim",
                  ),
                  const SizedBox(height: 12),

                  const _SideQuestItem(
                    title: "Dzikir Pagi",
                    reward: "+100 EXP",
                    icon: Icons.self_improvement_rounded,
                    progress: 80,
                    total: 100,
                    progressText: "80/100",
                    btnText: "Check",
                  ),
                  const SizedBox(height: 12),

                  const _SideQuestItem(
                    title: "Telepon Orang Tua",
                    reward: "+150 EXP",
                    icon: Icons.edit_note_rounded,
                    iconColor: PremiumColor.slate400,
                    progress: 0,
                    total: 1,
                    btnText: "Claim",
                  ),
                  const SizedBox(height: 32),

                  // Add Custom Task Button
                  GestureDetector(
                    onTap: () {},
                    child: Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(vertical: 20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        border: Border.all(
                          color: PremiumColor.primary.withOpacity(0.1),
                          width: 2,
                        ),
                        borderRadius: BorderRadius.circular(24),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.add_circle_outline_rounded,
                              color: PremiumColor.primary, size: 24),
                          const SizedBox(width: 12),
                          Text(
                            "Tambah Tugas Kustom",
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 15,
                              fontWeight: FontWeight.w800,
                              color: PremiumColor.primary,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ]),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    bool isSelected = _selectedFilter == value;
    return GestureDetector(
      onTap: () {
        setState(() => _selectedFilter = value);
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? PremiumColor.primary : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isSelected ? PremiumColor.primary : PremiumColor.slate200,
            width: 1.5,
          ),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: PremiumColor.primary.withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 4),
                  )
                ]
              : [],
        ),
        child: Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11,
            fontWeight: FontWeight.w800,
            color: isSelected ? Colors.white : PremiumColor.slate500,
            letterSpacing: 0.5,
          ),
        ),
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
                const Positioned.fill(child: IslamicPatternBackground()),

                // Content
                Padding(
                  padding: const EdgeInsets.fromLTRB(24, 60, 24, 30),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "Bismillahir-Rahmanir-Rahim",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          color: Colors.white.withOpacity(0.8),
                          letterSpacing: 1.0,
                        ),
                      ),
                      Text(
                        "All Missions",
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
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
}

// -----------------------------------------------------------------------------
// Helper Widgets
// -----------------------------------------------------------------------------

class _SectionHeader extends StatelessWidget {
  final String title;
  final IconData icon;
  final String badgeText;

  const _SectionHeader({
    required this.title,
    required this.icon,
    required this.badgeText,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Icon(icon, color: PremiumColor.primary, size: 24),
            const SizedBox(width: 8),
            Text(
              title,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: PremiumColor.slate800,
              ),
            ),
          ],
        ),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: PremiumColor.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Text(
            badgeText,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w700,
              color: PremiumColor.primary,
              letterSpacing: 0.5,
            ),
          ),
        ),
      ],
    );
  }
}

enum QuestStatus { completed, upcoming, locked }

class _MandatoryQuestItem extends StatelessWidget {
  final String title;
  final String reward;
  final IconData icon;
  final Color iconBg;
  final Color iconColor;
  final QuestStatus status;
  final double? progress;

  const _MandatoryQuestItem({
    required this.title,
    required this.reward,
    required this.icon,
    required this.iconBg,
    required this.iconColor,
    required this.status,
    this.progress,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFF1F5F9)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          // Icon
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: iconBg,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(icon, color: iconColor, size: 24),
          ),
          const SizedBox(width: 16),

          // Content
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: PremiumColor.slate800,
                      ),
                    ),
                    Text(
                      reward,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: PremiumColor.accent,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),

                // Progress Bar or Status
                Row(
                  children: [
                    Expanded(
                      child: LinearPercentIndicator(
                        lineHeight: 6.0,
                        percent: status == QuestStatus.completed
                            ? 1.0
                            : (progress ?? 0.0),
                        padding: EdgeInsets.zero,
                        backgroundColor: const Color(0xFFF1F5F9),
                        progressColor: status == QuestStatus.completed
                            ? PremiumColor.primary
                            : PremiumColor.accent,
                        barRadius: const Radius.circular(99),
                      ),
                    ),
                    const SizedBox(width: 8),
                    if (status == QuestStatus.completed)
                      Text(
                        "Completed",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          fontStyle: FontStyle.italic,
                          color: PremiumColor.slate400,
                        ),
                      )
                    else if (status == QuestStatus.upcoming)
                      Text(
                        "Upcoming",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          fontStyle: FontStyle.italic,
                          color: PremiumColor.slate400,
                        ),
                      ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),

          // Action Button
          if (status == QuestStatus.completed)
            const Icon(Icons.check_circle_rounded,
                color: PremiumColor.primary, size: 28)
          else if (status == QuestStatus.upcoming)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                  color: PremiumColor.primary,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [
                    BoxShadow(
                        color: PremiumColor.primary.withOpacity(0.2),
                        blurRadius: 4,
                        offset: Offset(0, 2))
                  ]),
              child: Text("Check",
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 10,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                      letterSpacing: 0.5)),
            )
          else
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: const Color(0xFFF1F5F9),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text("Locked",
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 10,
                      fontWeight: FontWeight.w700,
                      color: PremiumColor.slate400,
                      letterSpacing: 0.5)),
            )
        ],
      ),
    );
  }
}

class _SideQuestItem extends StatelessWidget {
  final String title;
  final String reward;
  final IconData icon;
  final double progress;
  final int total;
  final String? progressText;
  final String btnText;
  final Color? iconColor;

  const _SideQuestItem({
    required this.title,
    required this.reward,
    required this.icon,
    required this.progress,
    required this.total,
    this.progressText,
    required this.btnText,
    this.iconColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFF1F5F9)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          // Icon
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: PremiumColor.primary.withOpacity(0.05),
              borderRadius: BorderRadius.circular(16),
            ),
            child:
                Icon(icon, color: iconColor ?? PremiumColor.primary, size: 24),
          ),
          const SizedBox(width: 16),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: PremiumColor.slate800,
                      ),
                    ),
                    Text(
                      reward,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: PremiumColor.accent,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    Expanded(
                      child: LinearPercentIndicator(
                        lineHeight: 6.0,
                        percent: progress / total,
                        padding: EdgeInsets.zero,
                        backgroundColor: const Color(0xFFF1F5F9),
                        progressColor: PremiumColor.accent,
                        barRadius: const Radius.circular(99),
                      ),
                    ),
                    if (progressText != null) ...[
                      const SizedBox(width: 8),
                      Text(
                        progressText!,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          color: PremiumColor.slate400,
                        ),
                      ),
                    ]
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),

          // Button
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: PremiumColor.primary,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(btnText,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    letterSpacing: 0.5)),
          )
        ],
      ),
    );
  }
}
