import 'dart:ui';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';
import '../models/quest.dart';
import '../services/quest_service.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';
import 'widgets/level_up_dialog.dart';
import 'widgets/quest_invitation_dialog.dart';

class QuestScreen extends StatefulWidget {
  const QuestScreen({super.key});

  @override
  State<QuestScreen> createState() => _QuestScreenState();
}

class _QuestScreenState extends State<QuestScreen> {
  final QuestService _questService = QuestService();
  List<Quest> _quests = [];
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchQuests();
  }

  Future<void> _fetchQuests() async {
    try {
      setState(() {
        _isLoading = true;
        _errorMessage = null;
      });

      List<Quest> allFetched = [];
      bool hasError = false;
      String errorMsg = "";

      // Daily
      try {
        final daily = await _questService.getQuests(type: 'daily');
        allFetched.addAll(daily);
      } catch (e) {
        print('QuestScreen: Daily fetch error: $e');
        hasError = true;
        errorMsg += "Daily: $e\n";
      }

      // Hidden
      try {
        final hidden = await _questService.getQuests(type: 'hidden');
        allFetched.addAll(hidden);
      } catch (e) {
        print('QuestScreen: Hidden fetch error: $e');
        hasError = true;
        errorMsg += "Hidden: $e\n";
      }

      // Trial
      try {
        final trial = await _questService.getQuests(type: 'trial');
        allFetched.addAll(trial);
      } catch (e) {
        print('QuestScreen: Trial fetch error: $e');
        hasError = true;
        errorMsg += "Trial: $e\n";
      }

      if (mounted) {
        setState(() {
          _quests = allFetched;
          _isLoading = false;
          if (allFetched.isEmpty && hasError) {
            _errorMessage =
                "Gagal mengambil misi. Silakan coba lagi.\n$errorMsg";
          }
        });

        _triggerInvitationIfAvailable();
      }
    } catch (e) {
      debugPrint('QuestScreen Global Error: $e');
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage = "Terjadi kesalahan sistem: $e";
        });
      }
    }
  }

  Future<void> _acceptQuest(int questId) async {
    print('QuestScreen: Attempting to accept quest ID: $questId');
    try {
      await _questService.acceptQuest(questId);
      print('QuestScreen: Successfully accepted quest ID: $questId');
      _fetchQuests(); // Refresh to update status
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Quest Accepted!')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to accept: $e')),
        );
      }
    }
  }

  Future<void> _simulateProgress(Quest quest) async {
    if (quest.requirements.isEmpty) return;

    // For demo: increment the first requirement found
    String key = quest.requirements.keys.first;

    try {
      await _questService.updateProgress(quest.id, key, 1);
      _fetchQuests(); // Refresh
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to update progress: $e')),
        );
      }
    }
  }

  Future<void> _completeQuest(int questId) async {
    try {
      final response = await _questService.completeQuest(questId);
      final bool leveledUp = response['leveled_up'] ?? false;

      _fetchQuests(); // Refresh
      if (mounted) {
        final String? msg = response['message'];
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(msg ?? 'Quest Completed & Rewards Claimed!')),
        );

        if (leveledUp) {
          showDialog(
            context: context,
            barrierDismissible: false,
            builder: (context) => LevelUpDialog(
              newLevel:
                  response['user_stats']?['level'] ?? (response['level'] ?? 1),
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to complete: $e')),
        );
      }
    }
  }

  void _triggerInvitationIfAvailable() {
    print(
        'QuestScreen: Checking for available quests for popup. Total: ${_quests.length}');
    for (var q in _quests) {
      print('QuestScreen: Quest ID ${q.id} status is "${q.status}"');
    }

    if (_quests.isEmpty) {
      print('QuestScreen: Quest list is empty, skipping invitation.');
      return;
    }

    // Look for first 'available' quest (not yet taken)
    try {
      final availableQuest = _quests.firstWhere(
        (q) => q.status == 'available',
      );
      print(
          'QuestScreen: Found available quest for popup: ${availableQuest.title}');

      // Show dialog with a slight delay to ensure UI is ready
      Future.delayed(const Duration(milliseconds: 500), () {
        if (!mounted) return;

        showDialog(
          context: context,
          barrierDismissible: true,
          builder: (context) => QuestInvitationDialog(
            quest: availableQuest,
            onAccept: () {
              Navigator.pop(context);
              _acceptQuest(availableQuest.id);
            },
            onDecline: () => Navigator.pop(context),
          ),
        );
      });
    } catch (e) {
      // No available quests found, do nothing
      debugPrint("No available quests for invitation pop-up.");
    }
  }

  @override
  Widget build(BuildContext context) {
    int totalQuests = _quests.length;
    int completedQuests = _quests.where((q) => q.status == 'completed').length;
    double overallProgress =
        totalQuests > 0 ? completedQuests / totalQuests : 0;

    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: _buildMisiTab(completedQuests, totalQuests, overallProgress),
    );
  }

  Widget _buildMisiTab(
      int completedQuests, int totalQuests, double overallProgress) {
    return CustomScrollView(
      physics: const BouncingScrollPhysics(),
      slivers: [
        _buildSliverHeader(completedQuests, totalQuests, overallProgress),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(24, 24, 24, 120),
          sliver: _isLoading
              ? const SliverFillRemaining(
                  hasScrollBody: false,
                  child: Center(
                      child: CircularProgressIndicator(
                          color: PremiumColor.primary)),
                )
              : _errorMessage != null
                  ? SliverToBoxAdapter(child: _buildErrorState(_errorMessage!))
                  : _quests.isEmpty
                      ? SliverToBoxAdapter(child: _buildEmptyState())
                      : SliverList(
                          delegate: SliverChildListDelegate([
                            // Daily Quests Section
                            if (_quests.any((q) =>
                                q.type.toLowerCase().contains('daily') ||
                                q.isMandatory)) ...[
                              const _SectionHeader(
                                  title: "Misi Harian", badgeText: "WAJIB"),
                              const SizedBox(height: 16),
                              ..._quests
                                  .where((q) =>
                                      q.type.toLowerCase().contains('daily') ||
                                      q.isMandatory)
                                  .map((q) => _buildAnimatedQuestCard(q)),
                              const SizedBox(height: 32),
                            ],

                            // Hidden Quests Section
                            if (_quests.any((q) =>
                                q.type.toLowerCase().contains('hidden'))) ...[
                              const _SectionHeader(
                                  title: "Misi Rahasia", badgeText: "RAHASIA"),
                              const SizedBox(height: 16),
                              ..._quests
                                  .where((q) =>
                                      q.type.toLowerCase().contains('hidden'))
                                  .map((q) => _buildAnimatedQuestCard(q)),
                              const SizedBox(height: 32),
                            ],

                            // Trial Quests Section
                            if (_quests.any((q) =>
                                q.type.toLowerCase().contains('trial') ||
                                q.type.toLowerCase().contains('rank'))) ...[
                              const _SectionHeader(
                                  title: "Ujian Naik Rank", badgeText: "TRIAL"),
                              const SizedBox(height: 16),
                              ..._quests
                                  .where((q) =>
                                      q.type.toLowerCase().contains('trial') ||
                                      q.type.toLowerCase().contains('rank'))
                                  .map((q) => _buildAnimatedQuestCard(q)),
                              const SizedBox(height: 32),
                            ],
                          ]),
                        ),
        ),
      ],
    );
  }

  Widget _buildAnimatedQuestCard(Quest quest) {
    return _StaggeredAnimation(
      index: _quests.indexOf(quest),
      child: Padding(
        padding: const EdgeInsets.only(bottom: 12),
        child: _QuestCard(
          quest: quest,
          onAccept: () => _acceptQuest(quest.id),
          onProgress: () => _simulateProgress(quest),
          onClaim: () => _completeQuest(quest.id),
        ),
      ),
    );
  }

  Widget _buildSliverHeader(int completed, int total, double progress) {
    return SliverAppBar(
      expandedHeight: 250,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      // Safe removed BackButton since it is globally handled by QuestScreen's Stack now.
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
                        "MISSION LOG",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.w900,
                          color: Colors.white.withOpacity(0.8),
                          letterSpacing: 2.0,
                        ),
                      ),
                      Text(
                        "Pusat Misi",
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          _buildHeaderStat(
                              "$completed SELESAI", Icons.task_alt_rounded),
                          const SizedBox(width: 8),
                          _buildHeaderStat(
                              "${_quests.where((q) => q.status != 'available').length} DIAMBIL",
                              Icons.assignment_ind_rounded),
                          const Spacer(),
                          Text(
                            "${(progress * 100).toInt()}%",
                            style: GoogleFonts.plusJakartaSans(
                              color: Colors.white.withOpacity(0.9),
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
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
          Text(
            text,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.white,
              fontSize: 10,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.5,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.8),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        children: [
          const Icon(Icons.sentiment_satisfied_rounded,
              size: 48, color: PremiumColor.slate400),
          const SizedBox(height: 16),
          Text(
            "Belum ada misi tersedia saat ini.",
            style: GoogleFonts.plusJakartaSans(
              color: PremiumColor.slate800,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildErrorState(String message) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.red.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.red.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          const Icon(Icons.error_outline_rounded, size: 48, color: Colors.red),
          const SizedBox(height: 16),
          Text(
            message,
            textAlign: TextAlign.center,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.red.shade800,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 16),
          ElevatedButton(
            onPressed: _fetchQuests,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: const Text("Coba Lagi"),
          ),
        ],
      ),
    );
  }
}

// -----------------------------------------------------------------------------
// Header
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// Section Header
// -----------------------------------------------------------------------------
class _SectionHeader extends StatelessWidget {
  final String title;
  final String badgeText;

  const _SectionHeader({required this.title, required this.badgeText});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          title.toUpperCase(),
          style: GoogleFonts.plusJakartaSans(
            fontSize: 18,
            fontWeight: FontWeight.w800,
            color: PremiumColor.slate800,
          ),
        ),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          decoration: BoxDecoration(
            color: PremiumColor.primary.withOpacity(0.05),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Text(
            badgeText.toUpperCase(),
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w700,
              color: PremiumColor.primary.withOpacity(0.6),
              letterSpacing: 1.0,
            ),
          ),
        ),
      ],
    );
  }
}

// -----------------------------------------------------------------------------
// Quest Card
// -----------------------------------------------------------------------------
class _QuestCard extends StatelessWidget {
  final Quest quest;
  final VoidCallback onAccept;
  final VoidCallback onProgress;
  final VoidCallback onClaim;

  const _QuestCard({
    required this.quest,
    required this.onAccept,
    required this.onProgress,
    required this.onClaim,
  });

  @override
  Widget build(BuildContext context) {
    final double progressPercent = quest.progressPercentage;
    final bool isCompleted = quest.status == 'completed';
    final bool isPending = quest.status == 'pending';
    final bool isAvailable = quest.status == 'available';

    Color typeColor = quest.type.contains('hidden')
        ? Colors.green.shade600
        : (quest.type.contains('trial') || quest.type.contains('rank'))
            ? Colors.deepPurple.shade700
            : PremiumColor.primary;

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: PremiumColor.primary.withOpacity(0.08),
          width: 1.5,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Row
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Icon/Badge based on type
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: typeColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(
                  quest.type.contains('hidden')
                      ? Icons.auto_fix_high_rounded
                      : (quest.type.contains('trial') ||
                              quest.type.contains('rank'))
                          ? Icons.military_tech_rounded
                          : Icons.task_alt_rounded,
                  color: typeColor,
                  size: 24,
                ),
              ),
              const SizedBox(width: 16),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      quest.title,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                        color: typeColor,
                      ),
                    ),
                    Text(
                      quest.description,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        color: PremiumColor.slate600,
                        height: 1.4,
                      ),
                    ),
                    // Time Constraints & Timer Display
                    if ((quest.startTime != null && quest.endTime != null) ||
                        quest.timeLimit != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 6),
                        child: Wrap(
                          spacing: 12,
                          runSpacing: 4,
                          children: [
                            if (quest.startTime != null &&
                                quest.endTime != null)
                              Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.calendar_today_rounded,
                                      size: 11, color: Color(0xFF2E7D32)),
                                  const SizedBox(width: 4),
                                  Text(
                                    "SETIAP HARI: ",
                                    style: GoogleFonts.plusJakartaSans(
                                      fontSize: 9,
                                      fontWeight: FontWeight.w800,
                                      color: const Color(0xFF2E7D32),
                                    ),
                                  ),
                                  Text(
                                    (() {
                                      String s = quest.startTime!;
                                      String e = quest.endTime!;
                                      if (s.contains(' '))
                                        s = s.split(' ').last;
                                      if (e.contains(' '))
                                        e = e.split(' ').last;
                                      // If it doesn't look like a time (no colon), it might be a malformed date
                                      if (!s.contains(':')) return "DAILY";
                                      return "${s.substring(0, 5)} - ${e.substring(0, 5)}";
                                    })(),
                                    style: GoogleFonts.plusJakartaSans(
                                      fontSize: 10,
                                      fontWeight: FontWeight.w700,
                                      color: const Color(0xFF2E7D32)
                                          .withOpacity(0.8),
                                    ),
                                  ),
                                ],
                              ),
                            if (quest.timeLimit != null)
                              Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.timer_outlined,
                                      size: 12, color: Color(0xFFF39221)),
                                  const SizedBox(width: 4),
                                  Text(
                                    "TIMER: ${quest.timeLimit} MENIT",
                                    style: GoogleFonts.plusJakartaSans(
                                      fontSize: 10,
                                      fontWeight: FontWeight.w800,
                                      color: const Color(0xFFF39221),
                                    ),
                                  ),
                                ],
                              ),
                          ],
                        ),
                      ),
                    if (quest.expiresAt != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 4),
                        child: Row(
                          children: [
                            const Icon(Icons.event_busy_rounded,
                                size: 12, color: Colors.redAccent),
                            const SizedBox(width: 4),
                            Text(
                              "Expires: ${quest.expiresAt!.day}/${quest.expiresAt!.month}",
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                                color: Colors.redAccent,
                              ),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
              ),

              // Rewards Badge
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                decoration: BoxDecoration(
                  color: typeColor.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: typeColor.withOpacity(0.2)),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      "+${quest.rewardExp}",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                        color: typeColor,
                      ),
                    ),
                    Text(
                      "EXP",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 8,
                        fontWeight: FontWeight.w800,
                        color: typeColor.withOpacity(0.7),
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Progress Row (Only if not available)
          if (!isAvailable) ...[
            Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      isCompleted ? "MISI SELESAI" : "PROGRES",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        fontWeight: FontWeight.w900,
                        color: isCompleted ? Colors.green : typeColor,
                        letterSpacing: 1.0,
                      ),
                    ),
                    Text(
                      "${(progressPercent * 100).toInt()}%",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 14,
                        fontWeight: FontWeight.w900,
                        color: isCompleted ? Colors.green : typeColor,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                LinearPercentIndicator(
                  lineHeight: 8.0,
                  percent: progressPercent > 1.0 ? 1.0 : progressPercent,
                  padding: EdgeInsets.zero,
                  backgroundColor: PremiumColor.slate400.withOpacity(0.2),
                  progressColor:
                      isCompleted ? Colors.green : PremiumColor.highlight,
                  barRadius: const Radius.circular(99),
                ),
              ],
            ),
          ],

          // Requirements List
          if (quest.requirements.isNotEmpty) ...[
            const SizedBox(height: 16),
            Divider(color: PremiumColor.slate400.withOpacity(0.2)),
            const SizedBox(height: 8),
            ...quest.requirements.entries.map((entry) {
              final key = entry.key;
              final target = entry.value;

              int current = 0;
              if (quest.progress != null && quest.progress!.containsKey(key)) {
                current = quest.progress![key] is int
                    ? quest.progress![key]
                    : int.tryParse(quest.progress![key].toString()) ?? 0;
              }

              String formattedKey =
                  key.replaceAll('_', ' ').split(' ').map((str) {
                return str.isNotEmpty
                    ? '${str[0].toUpperCase()}${str.substring(1)}'
                    : '';
              }).join(' ');

              final bool isReqComplete = current >=
                  (target is int
                      ? target
                      : int.tryParse(target.toString()) ?? 1);

              return Padding(
                padding: const EdgeInsets.symmetric(vertical: 6),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(2),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: isReqComplete
                            ? Colors.green.withOpacity(0.1)
                            : Colors.transparent,
                      ),
                      child: Icon(
                        isReqComplete
                            ? Icons.check_circle_rounded
                            : Icons.radio_button_unchecked_rounded,
                        size: 18,
                        color: isReqComplete
                            ? Colors.green
                            : PremiumColor.slate400,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        formattedKey,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13,
                          fontWeight:
                              isReqComplete ? FontWeight.w600 : FontWeight.w700,
                          color: isReqComplete
                              ? PremiumColor.slate400
                              : PremiumColor.slate800,
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: isReqComplete
                            ? Colors.green.withOpacity(0.05)
                            : PremiumColor.slate500.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        "$current/$target",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: isReqComplete
                              ? Colors.green
                              : PremiumColor.slate600,
                        ),
                      ),
                    ),
                  ],
                ),
              );
            }).toList(),
          ],

          // Actions
          const SizedBox(height: 16),
          if (isAvailable)
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: onAccept,
                style: _buttonStyle(PremiumColor.primary),
                child: Text("Accept Quest", style: _buttonTextStyle()),
              ),
            )
          else if (isPending)
            Row(
              children: [
                if (progressPercent < 1.0)
                  Expanded(
                    child: OutlinedButton(
                      onPressed: onProgress,
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16)),
                        side: BorderSide(
                            color: PremiumColor.primary.withOpacity(0.2)),
                      ),
                      child: Text("Do Task",
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 14,
                              fontWeight: FontWeight.w700,
                              color: PremiumColor.primary)),
                    ),
                  ),
                if (progressPercent >= 1.0)
                  Expanded(
                    child: ElevatedButton(
                      onPressed: onClaim,
                      style: _buttonStyle(PremiumColor.highlight),
                      child: Text("Claim Reward", style: _buttonTextStyle()),
                    ),
                  ),
              ],
            )
          else if (isCompleted)
            SizedBox(
              width: double.infinity,
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 14),
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  color: Colors.green.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.green.withOpacity(0.2)),
                ),
                child: Text("✅ Finished",
                    style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w800, color: Colors.green)),
              ),
            ),
        ],
      ),
    );
  }

  ButtonStyle _buttonStyle(Color color) {
    return ElevatedButton.styleFrom(
      backgroundColor: color,
      foregroundColor: Colors.white,
      padding: const EdgeInsets.symmetric(vertical: 14),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      elevation: 0,
    );
  }

  TextStyle _buttonTextStyle() {
    return GoogleFonts.plusJakartaSans(
      fontSize: 14,
      fontWeight: FontWeight.w800,
      letterSpacing: 0.5,
    );
  }
}

// -----------------------------------------------------------------------------
// Staggered Animation Wrapper
// -----------------------------------------------------------------------------
class _StaggeredAnimation extends StatelessWidget {
  final int index;
  final Widget child;

  const _StaggeredAnimation({required this.index, required this.child});

  @override
  Widget build(BuildContext context) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: const Duration(milliseconds: 600),
      curve: Curves.easeOutQuint,
      // Add delay based on index
      builder: (context, value, child) {
        return Opacity(
          opacity: value,
          child: Transform.translate(
            offset: Offset(0, (1 - value) * 30),
            child: child,
          ),
        );
      },
      child: child,
    );
  }
}
