import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'widgets/custom_background.dart';
import '../models/habit.dart';
import '../services/habit_service.dart';
import '../theme/premium_color.dart';
import 'template_selection_screen.dart';

class HabitScreen extends StatefulWidget {
  const HabitScreen({super.key});

  @override
  State<HabitScreen> createState() => _HabitScreenState();
}

class _HabitScreenState extends State<HabitScreen> {
  final HabitService _habitService = HabitService();

  List<Habit> _habits = [];

  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() => _isLoading = true);
    await _fetchHabits(resetLoading: false);
    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _fetchHabits({bool resetLoading = true}) async {
    if (resetLoading) setState(() => _isLoading = true);
    final habits = await _habitService.getHabits();
    if (mounted) {
      setState(() {
        _habits = habits;
        if (resetLoading) _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildSliverHeader(),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(24, 20, 24, 120),
              child: _isLoading
                  ? const Center(
                      child: CircularProgressIndicator(
                          color: PremiumColor.primary))
                  : _habits.isEmpty
                      ? _buildEmptyState()
                      : Column(
                          children: _habits
                              .asMap()
                              .entries
                              .map((entry) =>
                                  _buildHabitTile(entry.value, entry.key))
                              .toList(),
                        ),
            ),
          ),
        ],
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: _buildPremiumFAB(),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 220,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false, // Remove default back button
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: const BoxDecoration(
              color: PremiumColor.primary,
              image: DecorationImage(
                image: NetworkImage(
                    "https://www.transparenttextures.com/patterns/handmade-paper.png"),
                opacity: 0.1,
                fit: BoxFit.cover,
              ),
            ),
            padding: const EdgeInsets.fromLTRB(24, 80, 24, 40),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "KEBIASAAN",
                  style: GoogleFonts.plusJakartaSans(
                    color: Colors.white,
                    fontSize: 12,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 2.0,
                  ),
                ),
                Text(
                  "Disiplin Diri",
                  style: GoogleFonts.playfairDisplay(
                    color: Colors.white,
                    fontSize: 32,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    _buildHeaderStat(
                        "${_habits.length} AKTIF", Icons.auto_awesome_rounded),
                    const SizedBox(width: 8),
                    _buildHeaderStat(
                        "${_habits.fold(0, (sum, item) => sum + item.count)} TOTAL",
                        Icons.bolt_rounded),
                    const Spacer(),
                    GestureDetector(
                      onTap: () async {
                        final result = await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const TemplateSelectionScreen(
                                templateType: 'habit'),
                          ),
                        );
                        if (result == true) _fetchHabits();
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 8),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.white24),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.auto_awesome_motion_rounded,
                                color: Colors.white, size: 16),
                            const SizedBox(width: 6),
                            Text("TEMPLATE",
                                style: GoogleFonts.plusJakartaSans(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.white,
                                    letterSpacing: 0.5)),
                          ],
                        ),
                      ),
                    ),
                  ],
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
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.15),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: PremiumColor.highlight, size: 14),
          const SizedBox(width: 6),
          Text(
            text,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.white,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(32),
            decoration: BoxDecoration(
              color: PremiumColor.primary.withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.auto_awesome_rounded,
                size: 64, color: PremiumColor.primary.withOpacity(0.2)),
          ),
          const SizedBox(height: 24),
          Text(
            "Belum ada kebiasaan",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: PremiumColor.primary,
            ),
          ),
          const SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 40),
            child: Text(
              "Mulailah hari ini dengan satu langkah kecil menuju pribadi yang lebih baik.",
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(
                color: Colors.grey.shade600,
                fontSize: 14,
                height: 1.5,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPremiumFAB() {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.3),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: FloatingActionButton.extended(
        onPressed: _showAddHabitModal,
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        highlightElevation: 0,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        icon: const Icon(Icons.add_rounded, color: Colors.white, size: 24),
        label: Text(
          "HABIT BARU",
          style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w900,
            letterSpacing: 1.2,
            color: Colors.white,
          ),
        ),
      ),
    );
  }

  void _showAddHabitModal() {
    final titleController = TextEditingController();
    final notesController = TextEditingController();
    bool isPos = true;
    bool isNeg = false;
    String difficulty = 'easy';
    String frequency = 'daily';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => Container(
          height: MediaQuery.of(context).size.height * 0.9,
          decoration: const BoxDecoration(
            color: PremiumColor.background,
            borderRadius: BorderRadius.vertical(top: Radius.circular(36)),
          ),
          child: Column(
            children: [
              // Premium Modal Header
              Container(
                padding: const EdgeInsets.fromLTRB(28, 20, 28, 20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius:
                      const BorderRadius.vertical(top: Radius.circular(36)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.03),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    )
                  ],
                ),
                child: Column(
                  children: [
                    Container(
                      width: 48,
                      height: 5,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade200,
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                    const SizedBox(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "KEBIASAAN BARU",
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 12,
                                fontWeight: FontWeight.w900,
                                color: PremiumColor.primary.withOpacity(0.5),
                                letterSpacing: 1.5,
                              ),
                            ),
                            Text(
                              "Mulai Perjalanan",
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 24,
                                fontWeight: FontWeight.w900,
                                color: PremiumColor.primary,
                              ),
                            ),
                          ],
                        ),
                        Container(
                          decoration: BoxDecoration(
                            color: PremiumColor.primary.withOpacity(0.05),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: IconButton(
                            icon: const Icon(Icons.close_rounded, size: 22),
                            onPressed: () => Navigator.pop(context),
                          ),
                        )
                      ],
                    ),
                  ],
                ),
              ),

              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(28),
                  physics: const BouncingScrollPhysics(),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildSectionHeader(
                          "Informasi Utama", Icons.edit_rounded),
                      const SizedBox(height: 16),
                      _buildPremiumInput(
                        controller: titleController,
                        hint: "Apa yang ingin Anda biasakan?",
                        icon: Icons.auto_awesome_outlined,
                      ),
                      const SizedBox(height: 16),
                      _buildPremiumInput(
                        controller: notesController,
                        hint: "Tambahkan catatan motivasi...",
                        icon: Icons.notes_rounded,
                        maxLines: 3,
                      ),
                      const SizedBox(height: 32),
                      _buildSectionHeader(
                          "Tipe Aktivitas", Icons.layers_rounded),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          _buildTypeSelector(
                            label: "Positif",
                            subtitle: "Build Habit",
                            icon: Icons.add_rounded,
                            isActive: isPos,
                            activeColor: Colors.teal,
                            onTap: () => setModalState(() => isPos = !isPos),
                          ),
                          const SizedBox(width: 16),
                          _buildTypeSelector(
                            label: "Negatif",
                            subtitle: "Break Habit",
                            icon: Icons.remove_rounded,
                            isActive: isNeg,
                            activeColor: Colors.red,
                            onTap: () => setModalState(() => isNeg = !isNeg),
                          ),
                        ],
                      ),
                      const SizedBox(height: 32),
                      _buildSectionHeader(
                          "Tingkat Tantangan", Icons.bolt_rounded),
                      const SizedBox(height: 16),
                      Wrap(
                        spacing: 10,
                        runSpacing: 10,
                        children:
                            ['trivial', 'easy', 'medium', 'hard'].map((d) {
                          return _buildChallengeChip(
                            id: d,
                            current: difficulty,
                            onSelect: (val) =>
                                setModalState(() => difficulty = val),
                          );
                        }).toList(),
                      ),
                      const SizedBox(height: 32),
                      _buildSectionHeader(
                          "Periode Reset", Icons.update_rounded),
                      const SizedBox(height: 16),
                      Row(
                        children: ['daily', 'weekly', 'monthly'].map((f) {
                          return Padding(
                            padding: const EdgeInsets.only(right: 10),
                            child: _buildPeriodChip(
                              id: f,
                              current: frequency,
                              onSelect: (val) =>
                                  setModalState(() => frequency = val),
                            ),
                          );
                        }).toList(),
                      ),
                      const SizedBox(height: 48),
                    ],
                  ),
                ),
              ),

              // Bottom Action Button
              Container(
                padding: const EdgeInsets.fromLTRB(28, 20, 28, 40),
                decoration: BoxDecoration(
                  color: Colors.white,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.04),
                      blurRadius: 20,
                      offset: const Offset(0, -10),
                    )
                  ],
                ),
                child: SizedBox(
                  width: double.infinity,
                  height: 64,
                  child: ElevatedButton(
                    onPressed: () async {
                      if (titleController.text.isNotEmpty && (isPos || isNeg)) {
                        final newHabit = await _habitService.createHabit(
                          title: titleController.text,
                          notes: notesController.text,
                          difficulty: difficulty,
                          isPositive: isPos,
                          isNegative: isNeg,
                          frequency: frequency,
                        );

                        if (newHabit != null) {
                          _fetchHabits();
                          if (context.mounted) {
                            Navigator.pop(context);
                          }
                        }
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PremiumColor.primary,
                      foregroundColor: Colors.white,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          "MULAI SEKARANG",
                          style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w900,
                            fontSize: 16,
                            letterSpacing: 1,
                          ),
                        ),
                        const SizedBox(width: 8),
                        const Icon(Icons.arrow_forward_rounded, size: 20),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: PremiumColor.primary.withOpacity(0.5)),
        const SizedBox(width: 8),
        Text(
          title.toUpperCase(),
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11,
            fontWeight: FontWeight.w900,
            color: PremiumColor.primary.withOpacity(0.5),
            letterSpacing: 1.2,
          ),
        ),
      ],
    );
  }

  Widget _buildPremiumInput({
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    int maxLines = 1,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade100, width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: TextField(
        controller: controller,
        maxLines: maxLines,
        style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w600, fontSize: 16),
        decoration: InputDecoration(
          prefixIcon: Icon(icon,
              color: PremiumColor.primary.withOpacity(0.3), size: 22),
          hintText: hint,
          hintStyle: GoogleFonts.plusJakartaSans(
            color: Colors.grey.shade400,
            fontWeight: FontWeight.w500,
            fontSize: 15,
          ),
          border: InputBorder.none,
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
        ),
      ),
    );
  }

  Widget _buildTypeSelector({
    required String label,
    required String subtitle,
    required IconData icon,
    required bool isActive,
    required Color activeColor,
    required VoidCallback onTap,
  }) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(24),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 250),
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: isActive ? activeColor.withOpacity(0.08) : Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color: isActive ? activeColor : Colors.grey.shade100,
              width: isActive ? 2 : 1.5,
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: isActive ? activeColor : Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(icon,
                    color: isActive ? Colors.white : Colors.grey.shade400,
                    size: 20),
              ),
              const SizedBox(height: 16),
              Text(
                label,
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w900,
                  fontSize: 16,
                  color: isActive ? activeColor : Colors.black87,
                ),
              ),
              Text(
                subtitle,
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w600,
                  fontSize: 11,
                  color: isActive
                      ? activeColor.withOpacity(0.6)
                      : Colors.grey.shade500,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildChallengeChip({
    required String id,
    required String current,
    required Function(String) onSelect,
  }) {
    final bool isSelected = id == current;
    final Color color = _getDifficultyColor(id);
    final String label = id == 'trivial'
        ? 'Santai'
        : id == 'easy'
            ? 'Mudah'
            : id == 'medium'
                ? 'Sedang'
                : 'Berat';

    return InkWell(
      onTap: () => onSelect(id),
      borderRadius: BorderRadius.circular(16),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        decoration: BoxDecoration(
          color: isSelected ? color : Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected ? color : Colors.grey.shade100,
            width: 1.5,
          ),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: color.withOpacity(0.3),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  )
                ]
              : [],
        ),
        child: Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w800,
            fontSize: 13,
            color: isSelected ? Colors.white : Colors.grey.shade600,
          ),
        ),
      ),
    );
  }

  Widget _buildPeriodChip({
    required String id,
    required String current,
    required Function(String) onSelect,
  }) {
    final bool isSelected = id == current;
    final String label = id == 'daily'
        ? 'Harian'
        : id == 'weekly'
            ? 'Mingguan'
            : 'Bulanan';

    return InkWell(
      onTap: () => onSelect(id),
      borderRadius: BorderRadius.circular(16),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        decoration: BoxDecoration(
          color: isSelected ? PremiumColor.primary : Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected ? PremiumColor.primary : Colors.grey.shade100,
            width: 1.5,
          ),
        ),
        child: Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w800,
            fontSize: 13,
            color: isSelected ? Colors.white : Colors.grey.shade600,
          ),
        ),
      ),
    );
  }

  Color _getDifficultyColor(String diff) {
    switch (diff.toLowerCase()) {
      case 'trivial':
        return Colors.grey;
      case 'easy':
        return Colors.blue;
      case 'medium':
        return Colors.orange;
      case 'hard':
        return Colors.red;
      default:
        return PremiumColor.primary;
    }
  }

  Widget _buildHabitTile(Habit habit, int index) {
    final diffColor = _getDifficultyColor(habit.difficulty);
    final hasPos = habit.isPositive;
    final hasNeg = habit.isNegative;
    final rewards = habit.rewards;

    return Dismissible(
      key: Key('habit_${habit.id}'),
      direction: DismissDirection.endToStart,
      background: Container(
        margin: const EdgeInsets.only(bottom: 16),
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 24),
        decoration: BoxDecoration(
          color: Colors.red.withOpacity(0.1),
          borderRadius: BorderRadius.circular(24),
        ),
        child: const Icon(Icons.delete_outline_rounded,
            color: Colors.redAccent, size: 28),
      ),
      onDismissed: (_) async {
        setState(() {
          _habits.removeWhere((h) => h.id == habit.id);
        });
        final success = await _habitService.deleteHabit(habit.id);
        if (success) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text("Kebiasaan dihapus")));
          }
        } else {
          _fetchHabits();
        }
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 20,
              offset: const Offset(0, 8),
            ),
          ],
          border: Border.all(
            color: PremiumColor.primary.withOpacity(0.05),
            width: 1.5,
          ),
        ),
        child: GestureDetector(
          onLongPress: () => _showDeleteConfirmation(habit),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(24),
            child: IntrinsicHeight(
              child: Row(
                children: [
                  // Left Accent & Icon
                  Container(
                    width: 64,
                    decoration: BoxDecoration(
                      color: diffColor.withOpacity(0.08),
                      border: Border(
                        right: BorderSide(
                          color: diffColor.withOpacity(0.1),
                        ),
                      ),
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          "${habit.count}",
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: diffColor,
                          ),
                        ),
                        Text(
                          "KALI",
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 8,
                            fontWeight: FontWeight.w800,
                            color: diffColor.withOpacity(0.5),
                            letterSpacing: 0.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                  // Content
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  habit.title,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontWeight: FontWeight.w800,
                                    fontSize: 16,
                                    color: PremiumColor.primary,
                                    height: 1.2,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              _buildDifficultyBadge(
                                  habit.difficulty, diffColor),
                            ],
                          ),
                          if (habit.notes != null && habit.notes!.isNotEmpty)
                            Padding(
                              padding: const EdgeInsets.only(top: 6),
                              child: Text(
                                habit.notes!,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 12,
                                  color: PremiumColor.slate500,
                                  height: 1.4,
                                ),
                              ),
                            ),
                          const SizedBox(height: 16),
                          Row(
                            children: [
                              if (hasPos)
                                _buildRewardStat("+${rewards['xp']} EXP",
                                    "EXP Gain", Colors.blue),
                              const Spacer(),
                              Row(
                                children: [
                                  if (hasNeg)
                                    _buildActionCircle(
                                      icon: Icons.remove_rounded,
                                      color: Colors.red,
                                      onTap: () => _handleScore(habit, 'down'),
                                    ),
                                  if (hasNeg && hasPos)
                                    const SizedBox(width: 12),
                                  if (hasPos)
                                    _buildActionCircle(
                                      icon: Icons.add_rounded,
                                      color: Colors.green,
                                      onTap: () => _handleScore(habit, 'up'),
                                    ),
                                ],
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRewardStat(String value, String label, Color color) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          value,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13,
            fontWeight: FontWeight.w900,
            color: color,
          ),
        ),
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 8,
            fontWeight: FontWeight.w800,
            color: color.withOpacity(0.5),
            letterSpacing: 0.5,
          ),
        ),
      ],
    );
  }

  Widget _buildActionCircle(
      {required IconData icon,
      required Color color,
      required VoidCallback onTap}) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: color.withOpacity(0.2), width: 1.5),
          ),
          child: Icon(icon, color: color, size: 20),
        ),
      ),
    );
  }

  Widget _buildDifficultyBadge(String diff, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        diff.toUpperCase(),
        style: GoogleFonts.plusJakartaSans(
          fontSize: 9,
          fontWeight: FontWeight.w900,
          color: color,
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Future<void> _showDeleteConfirmation(Habit habit) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text("Hapus Kebiasaan?",
            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        content: Text("Kebiasaan '${habit.title}' bakal dihapus selamanya."),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text("Batal"),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text("Hapus"),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      final success = await _habitService.deleteHabit(habit.id);
      if (success) {
        _fetchHabits();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Kebiasaan dihapus")),
          );
        }
      }
    }
  }

  Future<void> _handleScore(Habit habit, String direction) async {
    final result = await _habitService.scoreHabit(habit.id, direction);
    if (result != null) {
      if (mounted) {
        setState(() {
          habit.count = result['habit_count'];
        });

        // Show custom reward/penalty notification
        _showAchievementToast(
          context,
          direction == 'up',
          direction == 'up' ? "KEBIASAAN POSITIF!" : "PERHATIAN!",
          result['message'] ??
              (direction == 'up'
                  ? "Luar biasa! +${result['xp_gained']} EXP untuk Anda."
                  : "Tetap semangat! Teruslah berusaha untuk yang terbaik."),
          direction == 'up' ? Icons.stars_rounded : Icons.warning_amber_rounded,
        );
      }
    }
  }

  void _showAchievementToast(BuildContext context, bool isSuccess, String title,
      String message, IconData icon) {
    final overlay = Overlay.of(context);
    late OverlayEntry entry;

    entry = OverlayEntry(
      builder: (context) => _AchievementToastWidget(
        title: title,
        message: message,
        icon: icon,
        isSuccess: isSuccess,
        onDismiss: () => entry.remove(),
      ),
    );

    overlay.insert(entry);
  }
}

class _AchievementToastWidget extends StatefulWidget {
  final String title;
  final String message;
  final IconData icon;
  final bool isSuccess;
  final VoidCallback onDismiss;

  const _AchievementToastWidget({
    required this.title,
    required this.message,
    required this.icon,
    required this.isSuccess,
    required this.onDismiss,
  });

  @override
  State<_AchievementToastWidget> createState() =>
      _AchievementToastWidgetState();
}

class _AchievementToastWidgetState extends State<_AchievementToastWidget>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<Offset> _offsetAnimation;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 500),
    );

    _offsetAnimation = Tween<Offset>(
      begin: const Offset(0, 0.1),
      end: Offset.zero,
    ).animate(CurvedAnimation(
      parent: _controller,
      curve: Curves.easeOutBack,
    ));

    _fadeAnimation = CurvedAnimation(
      parent: _controller,
      curve: Curves.easeIn,
    );

    _controller.forward();

    Future.delayed(const Duration(seconds: 3), () {
      if (mounted) {
        _controller.reverse().then((_) => widget.onDismiss());
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final color = widget.isSuccess ? const Color(0xFFD4AF37) : Colors.redAccent;
    final bgGradient = LinearGradient(
      colors: [
        Colors.white,
        Colors.white.withOpacity(0.95),
      ],
      begin: Alignment.topLeft,
      end: Alignment.bottomRight,
    );

    return SafeArea(
      child: Stack(
        children: [
          // Backdrop blur behavior (approximate with a darkened background)
          Positioned.fill(
            child: GestureDetector(
              onTap: widget.onDismiss,
              child: Container(color: Colors.black.withOpacity(0.4)),
            ),
          ),
          Align(
            alignment: Alignment.center,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 40),
              child: ScaleTransition(
                scale: _fadeAnimation,
                child: SlideTransition(
                  position: _offsetAnimation,
                  child: FadeTransition(
                    opacity: _fadeAnimation,
                    child: Material(
                      color: Colors.transparent,
                      child: Container(
                        width: double.infinity,
                        constraints: const BoxConstraints(maxWidth: 320),
                        padding: const EdgeInsets.all(28),
                        decoration: BoxDecoration(
                          gradient: bgGradient,
                          borderRadius: BorderRadius.circular(36),
                          boxShadow: [
                            BoxShadow(
                              color: color.withOpacity(0.4),
                              blurRadius: 30,
                              offset: const Offset(0, 15),
                            )
                          ],
                          border: Border.all(
                              color: color.withOpacity(0.5), width: 2),
                        ),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(20),
                              decoration: BoxDecoration(
                                color: color.withOpacity(0.1),
                                shape: BoxShape.circle,
                              ),
                              child: Icon(widget.icon, color: color, size: 48),
                            ),
                            const SizedBox(height: 24),
                            Text(
                              widget.title,
                              textAlign: TextAlign.center,
                              style: GoogleFonts.plusJakartaSans(
                                color: color,
                                fontSize: 14,
                                fontWeight: FontWeight.w900,
                                letterSpacing: 2.0,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              widget.message,
                              textAlign: TextAlign.center,
                              style: GoogleFonts.plusJakartaSans(
                                color: PremiumColor.slate800,
                                fontSize: 15,
                                fontWeight: FontWeight.w700,
                                height: 1.4,
                              ),
                            ),
                            const SizedBox(height: 12),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
