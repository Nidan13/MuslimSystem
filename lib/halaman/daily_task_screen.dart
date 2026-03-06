import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:hijri/hijri_calendar.dart';
import 'widgets/custom_background.dart';
import '../theme/premium_color.dart';

import '../models/daily_task.dart';
import '../models/prayer_times.dart';
import '../services/daily_task_service.dart';
import '../services/prayer_times_service.dart';
import '../services/location_service.dart';
import 'template_selection_screen.dart';

class DailyTaskScreen extends StatefulWidget {
  const DailyTaskScreen({super.key});

  @override
  State<DailyTaskScreen> createState() => _DailyTaskScreenState();
}

class _DailyTaskScreenState extends State<DailyTaskScreen> {
  final DailyTaskService _dailyTaskService = DailyTaskService();
  final PrayerTimesService _prayerTimesService = PrayerTimesService();
  final LocationService _locationService = LocationService();

  List<DailyTask> _dailyTasks = [];
  DailyTaskSummary? _summary;
  PrayerTimes? _prayerTimes;
  DateTime _selectedDate = DateTime.now();

  // Creation States
  final TextEditingController _titleController = TextEditingController();
  final TextEditingController _notesController = TextEditingController();
  String _difficulty = 'easy';
  DateTime _startDate = DateTime.now();
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    try {
      HijriCalendar.setLocal('id');
    } catch (_) {}
    _fetchDailyTasks();
    _fetchPrayerTimes();
  }

  @override
  void dispose() {
    _titleController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  // Helper Methods for Premium UI
  Widget _buildSectionLabel(String label, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 16, color: PremiumColor.primary.withOpacity(0.4)),
        const SizedBox(width: 8),
        Text(
          label.toUpperCase(),
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11,
            fontWeight: FontWeight.w900,
            color: PremiumColor.primary.withOpacity(0.4),
            letterSpacing: 1.2,
          ),
        ),
      ],
    );
  }

  Widget _buildPremiumTextField({
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
      ),
      child: TextField(
        controller: controller,
        maxLines: maxLines,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 15,
          fontWeight: FontWeight.w600,
          color: PremiumColor.slate800,
        ),
        decoration: InputDecoration(
          hintText: hint,
          prefixIcon: Icon(icon, color: PremiumColor.primary, size: 20),
          hintStyle: GoogleFonts.plusJakartaSans(
            color: Colors.grey.shade400,
            fontSize: 14,
            fontWeight: FontWeight.w500,
          ),
          border: InputBorder.none,
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        ),
      ),
    );
  }

  Color _getDifficultyColor(String diff) {
    switch (diff) {
      case 'hard':
        return PremiumColor.primary;
      case 'medium':
        return PremiumColor.highlight;
      case 'easy':
        return PremiumColor.neonTeal;
      default:
        return Colors.grey.shade400;
    }
  }

  int _getEXPValue(String diff) {
    switch (diff) {
      case 'hard':
        return 25;
      case 'medium':
        return 15;
      case 'easy':
        return 10;
      default:
        return 5;
    }
  }

  Widget _buildDateTile({
    required String label,
    required String value,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.grey.shade100, width: 1.5),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: PremiumColor.primary.withOpacity(0.05),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.calendar_month_rounded,
                  size: 20, color: PremiumColor.primary),
            ),
            const SizedBox(width: 16),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: Colors.grey.shade500,
                  ),
                ),
                Text(
                  value,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                    color: PremiumColor.primary,
                  ),
                ),
              ],
            ),
            const Spacer(),
            Icon(Icons.chevron_right_rounded, color: Colors.grey.shade300),
          ],
        ),
      ),
    );
  }

  Future<void> _fetchPrayerTimes() async {
    try {
      final cached = await _prayerTimesService.getCachedPrayerTimes();
      if (cached != null && mounted) {
        setState(() => _prayerTimes = cached);
      }

      final pos = await _locationService.getLocationOrDefault();
      final times = await _prayerTimesService.getPrayerTimes(
        latitude: pos.latitude,
        longitude: pos.longitude,
      );
      if (mounted) {
        setState(() => _prayerTimes = times);
      }
    } catch (e) {
      debugPrint('Error fetching prayer times: $e');
    }
  }

  Future<void> _fetchDailyTasks() async {
    if (!mounted) return;
    setState(() => _isLoading = true);

    try {
      final dateStr = DateFormat('yyyy-MM-dd').format(_selectedDate);
      final response = await _dailyTaskService.getDailyTasks(date: dateStr);

      if (mounted) {
        setState(() {
          // Filter only custom tasks for this screen
          _dailyTasks = response.tasks.where((t) => t.isCustom).toList();
          _summary = response.summary;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        debugPrint('Error fetching tasks: $e');
      }
    }
  }

  bool get _isToday {
    final now = DateTime.now();
    return _selectedDate.year == now.year &&
        _selectedDate.month == now.month &&
        _selectedDate.day == now.day;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          Column(
            children: [
              _buildHeader(),
              Expanded(
                child: _isLoading
                    ? const Center(
                        child: CircularProgressIndicator(
                            color: PremiumColor.primary))
                    : _buildTaskList(),
              ),
            ],
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showAddDailyModal(),
        backgroundColor: PremiumColor.primary,
        icon: const Icon(Icons.add, color: Colors.white),
        label: Text("TAMBAH JURNAL",
            style: GoogleFonts.plusJakartaSans(
                color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }

  Widget _buildHeader() {
    double totalProgress = _summary?.progressPercentage ?? 0.0;

    // Hijri Date - Defensive & API Fallback
    String hijriString = "";
    if (_prayerTimes != null && _prayerTimes!.hijriDate.isNotEmpty) {
      // Use API Hijri date if available (more reliable)
      hijriString = "${_prayerTimes!.hijriDate} ${_prayerTimes!.hijriMonth} H";
    } else {
      try {
        final hijriDate = HijriCalendar.fromDate(_selectedDate);
        hijriString =
            "${hijriDate.hDay} ${hijriDate.longMonthName} ${hijriDate.hYear} H";
      } catch (e) {
        debugPrint('Hijri conversion error: $e');
        hijriString = "Hijri Date Loading...";
      }
    }

    // Gregorian Date (Full Date as requested)
    final gregorianString =
        DateFormat('EEEE, d MMMM yyyy', 'id_ID').format(_selectedDate);

    return ClipPath(
      clipper: MuqarnasClipper(),
      child: Container(
        height: 310, // Increased height for date strip
        padding: const EdgeInsets.only(top: 60, left: 24, right: 24),
        decoration: BoxDecoration(
          color: PremiumColor.primary,
          image: const DecorationImage(
            image: NetworkImage(
                "https://www.transparenttextures.com/patterns/handmade-paper.png"),
            opacity: 0.1,
            fit: BoxFit.cover,
          ),
        ),
        child: Column(
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
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          "JURNAL HARIAN",
                          style: GoogleFonts.plusJakartaSans(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 1.5,
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Text(gregorianString,
                          style: GoogleFonts.playfairDisplay(
                              fontSize: 26,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                              height: 1.1)),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          const Icon(Icons.calendar_month_rounded,
                              color: Colors.white70, size: 14),
                          const SizedBox(width: 6),
                          Text(hijriString,
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.white70)),
                        ],
                      ),
                    ],
                  ),
                ),
                // Template Button & Progress Ring
                Row(
                  children: [
                    GestureDetector(
                      onTap: () async {
                        final result = await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const TemplateSelectionScreen(
                                templateType: 'task'),
                          ),
                        );
                        if (result == true) _fetchDailyTasks();
                      },
                      child: Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.white24),
                        ),
                        child: Column(
                          children: [
                            const Icon(Icons.auto_awesome_motion_rounded,
                                color: Colors.white, size: 20),
                            const SizedBox(height: 2),
                            Text("TEMPLATE",
                                style: GoogleFonts.plusJakartaSans(
                                    fontSize: 8,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.white,
                                    letterSpacing: 0.5)),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    SizedBox(
                      width: 55,
                      height: 55,
                      child: CustomPaint(
                        painter: _ProgressRingPainter(
                          progress: totalProgress / 100,
                          bgColor: Colors.white24,
                          progressColor: PremiumColor.neonTeal,
                          width: 5,
                        ),
                        child: Center(
                          child: Text("${totalProgress.toInt()}%",
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 13,
                                  fontWeight: FontWeight.bold,
                                  color: PremiumColor.neonTeal)),
                        ),
                      ),
                    ),
                  ],
                )
              ],
            ),

            const SizedBox(height: 30),

            // restored Date Strip (Kalender)
            SizedBox(
              height: 70,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: 14, // 2 weeks
                itemBuilder: (context, index) {
                  final date = DateTime.now()
                      .subtract(const Duration(days: 3))
                      .add(Duration(days: index));
                  final isSelected = date.year == _selectedDate.year &&
                      date.month == _selectedDate.month &&
                      date.day == _selectedDate.day;
                  final isTodayDate = date.year == DateTime.now().year &&
                      date.month == DateTime.now().month &&
                      date.day == DateTime.now().day;

                  return GestureDetector(
                    onTap: () {
                      setState(() {
                        _selectedDate = date;
                      });
                      _fetchDailyTasks();
                    },
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 300),
                      width: 55,
                      margin: const EdgeInsets.symmetric(horizontal: 6),
                      decoration: BoxDecoration(
                        color: isSelected ? Colors.white : Colors.white10,
                        borderRadius: BorderRadius.circular(20),
                        border: isTodayDate && !isSelected
                            ? Border.all(color: Colors.white38, width: 1.5)
                            : null,
                        boxShadow: isSelected
                            ? [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.1),
                                  blurRadius: 10,
                                  offset: const Offset(0, 4),
                                )
                              ]
                            : [],
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(DateFormat('E').format(date).toUpperCase(),
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w800,
                                  color: isSelected
                                      ? PremiumColor.primary
                                      : Colors.white60)),
                          const SizedBox(height: 4),
                          Text(date.day.toString(),
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w900,
                                  color: isSelected
                                      ? PremiumColor.primary
                                      : Colors.white)),
                        ],
                      ),
                    ),
                  );
                },
              ),
            )
          ],
        ),
      ),
    );
  }

  void _showAddDailyModal() {
    _titleController.clear();
    _notesController.clear();
    _difficulty = 'easy';
    _startDate = DateTime.now();

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
              // Premium Header
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
                              "PENCATATAN BARU",
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 11,
                                fontWeight: FontWeight.w900,
                                color: PremiumColor.primary.withOpacity(0.5),
                                letterSpacing: 1.5,
                              ),
                            ),
                            Text(
                              "Refleksi Diri",
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
                      _buildSectionLabel("Informasi Utama", Icons.edit_rounded),
                      const SizedBox(height: 16),
                      _buildPremiumTextField(
                        controller: _titleController,
                        hint: "Apa yang ingin Anda catat hari ini?",
                        icon: Icons.title_rounded,
                      ),
                      const SizedBox(height: 16),
                      _buildPremiumTextField(
                        controller: _notesController,
                        hint: "Tambahkan rincian atau hikmah...",
                        icon: Icons.notes_rounded,
                        maxLines: 4,
                      ),
                      const SizedBox(height: 32),
                      _buildSectionLabel("Poin EXP", Icons.bolt_rounded),
                      const SizedBox(height: 16),
                      Wrap(
                        spacing: 12,
                        runSpacing: 12,
                        children:
                            ['trivial', 'easy', 'medium', 'hard'].map((diff) {
                          final isSelected = _difficulty == diff;
                          final color = _getDifficultyColor(diff);
                          return InkWell(
                            onTap: () =>
                                setModalState(() => _difficulty = diff),
                            borderRadius: BorderRadius.circular(16),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 200),
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 20, vertical: 14),
                              decoration: BoxDecoration(
                                color: isSelected ? color : Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(
                                  color:
                                      isSelected ? color : Colors.grey.shade100,
                                  width: 1.5,
                                ),
                              ),
                              child: Text(
                                diff.toUpperCase(),
                                style: GoogleFonts.plusJakartaSans(
                                  fontWeight: FontWeight.w900,
                                  fontSize: 12,
                                  color: isSelected
                                      ? Colors.white
                                      : Colors.grey.shade600,
                                ),
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                      const SizedBox(height: 32),
                      _buildSectionLabel(
                          "Pengaturan Waktu", Icons.schedule_rounded),
                      const SizedBox(height: 16),
                      _buildDateTile(
                        label: "Mulai Tanggal",
                        value: DateFormat('d MMMM yyyy', 'id_ID')
                            .format(_startDate),
                        onTap: () async {
                          final date = await showDatePicker(
                            context: context,
                            initialDate: _startDate,
                            firstDate: DateTime.now()
                                .subtract(const Duration(days: 365)),
                            lastDate:
                                DateTime.now().add(const Duration(days: 365)),
                          );
                          if (date != null)
                            setModalState(() => _startDate = date);
                        },
                      ),
                      const SizedBox(height: 48),
                    ],
                  ),
                ),
              ),

              // Bottom Save Button
              Padding(
                padding: const EdgeInsets.fromLTRB(28, 0, 28, 40),
                child: SizedBox(
                  width: double.infinity,
                  height: 60,
                  child: ElevatedButton(
                    onPressed: () async {
                      if (_titleController.text.isEmpty) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                              content: Text("Judul jurnal wajib diisi")),
                        );
                        return;
                      }

                      final messenger = ScaffoldMessenger.of(this.context);
                      Navigator.pop(context);

                      try {
                        await _dailyTaskService.createCustomTask(
                          name: _titleController.text,
                          soulPoints: _getEXPValue(_difficulty),
                          description: _notesController.text.isNotEmpty
                              ? _notesController.text
                              : null,
                          icon: '📝',
                        );
                        _fetchDailyTasks();
                        messenger.showSnackBar(const SnackBar(
                            content: Text("Jurnal berhasil disimpan!")));
                      } catch (e) {
                        messenger
                            .showSnackBar(SnackBar(content: Text("Gagal: $e")));
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: PremiumColor.primary,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20)),
                      elevation: 8,
                      shadowColor: PremiumColor.primary.withOpacity(0.4),
                    ),
                    child: Text(
                      "SIMPAN CATATAN",
                      style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                        letterSpacing: 1.2,
                      ),
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

  // --- Header Section ---
  // Unused _buildHeader removed

  Widget _buildTaskList() {
    return ListView(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
      children: [
        // Task List Title
        Text(
          _isToday ? "Jurnal Hari Ini" : "Jurnal Tanggal Ini",
          style: GoogleFonts.plusJakartaSans(
              color: Colors.black87, fontSize: 18, fontWeight: FontWeight.w800),
        ),
        const SizedBox(height: 20),

        if (_isLoading)
          const Padding(
            padding: EdgeInsets.all(40),
            child: Center(child: CircularProgressIndicator()),
          )
        else if (_dailyTasks.isEmpty)
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 40),
            child: Center(
              child: Column(
                children: [
                  Icon(Icons.event_available_rounded,
                      size: 60, color: Colors.grey.shade300),
                  const SizedBox(height: 16),
                  Text("Jurnal masih kosong",
                      style: GoogleFonts.plusJakartaSans(
                          color: Colors.grey, fontSize: 16)),
                ],
              ),
            ),
          )
        else
          ..._dailyTasks.map((task) => _buildTaskTile(task)).toList(),

        const SizedBox(height: 40),
      ],
    );
  }

  Widget _buildTaskTile(DailyTask task) {
    bool isCompleted = task.isCompleted;
    String description = task.description ?? "";
    String? completedAt = task.completedAt;

    String timeStr = "";
    if (completedAt != null) {
      try {
        final date = DateTime.parse(completedAt).toLocal();
        timeStr = DateFormat('HH:mm').format(date);
      } catch (_) {}
    }

    return Dismissible(
        key: Key('task_${task.id}'),
        direction: DismissDirection.endToStart,
        background: Container(
          margin: const EdgeInsets.only(bottom: 16),
          alignment: Alignment.centerRight,
          padding: const EdgeInsets.only(right: 24),
          decoration: BoxDecoration(
            color: Colors.red.withOpacity(0.1),
            borderRadius: BorderRadius.circular(20),
          ),
          child: const Icon(Icons.delete_outline_rounded,
              color: Colors.redAccent, size: 28),
        ),
        onDismissed: (_) async {
          setState(() {
            _dailyTasks.removeWhere((t) => t.id == task.id);
          });
          try {
            await _dailyTaskService.deleteTask(task.id);
            if (mounted) {
              ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("Jurnal dihapus")));
            }
          } catch (e) {
            _fetchDailyTasks();
            if (mounted) {
              ScaffoldMessenger.of(context)
                  .showSnackBar(SnackBar(content: Text("Gagal menghapus: $e")));
            }
          }
        },
        child: Container(
          margin: const EdgeInsets.only(bottom: 16),
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: isCompleted
                ? PremiumColor.neonTeal.withOpacity(0.04)
                : Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color: isCompleted
                  ? PremiumColor.neonTeal.withOpacity(0.25)
                  : PremiumColor.primary.withOpacity(0.08),
              width: 1.5,
            ),
            boxShadow: [
              BoxShadow(
                color: isCompleted
                    ? PremiumColor.neonTeal.withOpacity(0.05)
                    : Colors.black.withOpacity(0.04),
                blurRadius: 20,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          child: Row(
            children: [
              // Checkbox / Icon
              GestureDetector(
                onTap: () => _toggleTask(task),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: isCompleted ? PremiumColor.neonTeal : Colors.white,
                    shape: BoxShape.circle,
                    boxShadow: isCompleted
                        ? [
                            BoxShadow(
                                color: PremiumColor.neonTeal.withOpacity(0.3),
                                blurRadius: 8,
                                offset: const Offset(0, 4))
                          ]
                        : [],
                    border: Border.all(
                      color: isCompleted
                          ? PremiumColor.neonTeal
                          : Colors.grey.shade300,
                      width: 2,
                    ),
                  ),
                  child: isCompleted
                      ? const Icon(Icons.check_rounded,
                          color: Colors.white, size: 18)
                      : Center(
                          child: Text(task.icon,
                              style: const TextStyle(fontSize: 14)),
                        ),
                ),
              ),
              const SizedBox(width: 16),
              // Content
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      task.name,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: isCompleted
                            ? PremiumColor.primary.withOpacity(0.6)
                            : PremiumColor.primary,
                      ),
                    ),
                    if (description.isNotEmpty)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Text(
                          description,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                            color: isCompleted
                                ? Colors.grey.shade400
                                : Colors.grey.shade500,
                          ),
                        ),
                      ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              // Time/Meta or Points
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                        color: isCompleted
                            ? PremiumColor.neonTeal.withOpacity(0.12)
                            : PremiumColor.primary.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(12)),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                            isCompleted
                                ? Icons.verified_rounded
                                : Icons.bolt_rounded,
                            size: 12,
                            color: isCompleted
                                ? PremiumColor.neonTeal
                                : PremiumColor.primary),
                        const SizedBox(width: 4),
                        Text(
                          isCompleted ? "DONE" : "+${task.soulPoints} EXP",
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              color: isCompleted
                                  ? PremiumColor.neonTeal
                                  : PremiumColor.primary),
                        ),
                      ],
                    ),
                  ),
                  if (timeStr.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 6, right: 4),
                      child: Text(
                        timeStr,
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 10,
                            fontWeight: FontWeight.w700,
                            color: Colors.grey.shade400),
                      ),
                    ),
                ],
              )
            ],
          ),
        ));
  }

  Future<void> _toggleTask(DailyTask task) async {
    // Optimistic Update
    final index = _dailyTasks.indexWhere((t) => t.id == task.id);
    if (index != -1) {
      setState(() {
        _dailyTasks[index] = task.copyWith(isCompleted: !task.isCompleted);
      });
    }

    try {
      if (!task.isCompleted) {
        final result = await _dailyTaskService.completeTask(task.id);
        if (mounted) {
          final data = result['data'];
          final int exp = data['xp_gained'] ?? data['soul_points_earned'] ?? 0;

          // Show Rich Reward Feedback
          showDialog(
            context: context,
            barrierColor: Colors.black54,
            builder: (context) => Dialog(
              backgroundColor: Colors.transparent,
              elevation: 0,
              child: TweenAnimationBuilder<double>(
                duration: const Duration(milliseconds: 600),
                tween: Tween(begin: 0.0, end: 1.0),
                curve: Curves.elasticOut,
                builder: (context, value, child) {
                  return Transform.scale(
                    scale: value,
                    child: Container(
                      padding: const EdgeInsets.all(32),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(32),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 30,
                            offset: const Offset(0, 15),
                          ),
                        ],
                      ),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              color: PremiumColor.neonTeal.withOpacity(0.1),
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(Icons.auto_awesome_rounded,
                                color: PremiumColor.neonTeal, size: 48),
                          ),
                          const SizedBox(height: 24),
                          Text(
                            "JURNAL SELESAI!",
                            textAlign: TextAlign.center,
                            style: GoogleFonts.plusJakartaSans(
                              color: PremiumColor.neonTeal,
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 2.0,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            "Refleksi hari ini telah tercatat. Teruslah tumbuh!",
                            textAlign: TextAlign.center,
                            style: GoogleFonts.plusJakartaSans(
                              color: PremiumColor.slate800,
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              height: 1.4,
                            ),
                          ),
                          const SizedBox(height: 24),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 10),
                            decoration: BoxDecoration(
                              color: PremiumColor.primary.withOpacity(0.05),
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(Icons.bolt_rounded,
                                    color: PremiumColor.primary, size: 20),
                                const SizedBox(width: 8),
                                Text(
                                  "+$exp EXP",
                                  style: GoogleFonts.plusJakartaSans(
                                    color: PremiumColor.primary,
                                    fontSize: 12,
                                    fontWeight: FontWeight.w900,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 32),
                          SizedBox(
                            width: double.infinity,
                            height: 56,
                            child: ElevatedButton(
                              onPressed: () => Navigator.pop(context),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: PremiumColor.primary,
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16)),
                                elevation: 0,
                              ),
                              child: Text(
                                "ALHAMDULILLAH",
                                style: GoogleFonts.plusJakartaSans(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w900,
                                  letterSpacing: 1.2,
                                ),
                              ),
                            ),
                          )
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          );
        }
      } else {
        await _dailyTaskService.uncompleteTask(task.id);
      }
      _fetchDailyTasks();
    } catch (e) {
      _fetchDailyTasks();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Gagal memperbarui jurnal: $e")),
        );
      }
    }
  }
}

class _ProgressRingPainter extends CustomPainter {
  final double progress;
  final Color bgColor;
  final Color progressColor;
  final double width;

  _ProgressRingPainter({
    required this.progress,
    required this.bgColor,
    required this.progressColor,
    required this.width,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = (size.width - width) / 2;

    final bgPaint = Paint()
      ..color = bgColor
      ..strokeWidth = width
      ..style = PaintingStyle.stroke;

    final progressPaint = Paint()
      ..color = progressColor
      ..strokeWidth = width
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    canvas.drawCircle(center, radius, bgPaint);
    canvas.drawArc(
      Rect.fromCircle(center: center, radius: radius),
      -1.570796, // -90 degrees
      6.283185 * progress,
      false,
      progressPaint,
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;
}
