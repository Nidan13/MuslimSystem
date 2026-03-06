import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/circle.dart';
import '../services/circle_service.dart';
import '../models/dungeon.dart';
import 'widgets/custom_background.dart';

class CircleDetailScreen extends StatefulWidget {
  final int circleId;
  final String? initialName;
  final bool isRoot;

  const CircleDetailScreen({
    super.key,
    required this.circleId,
    this.initialName,
    this.isRoot = false,
  });

  @override
  State<CircleDetailScreen> createState() => _CircleDetailScreenState();
}

class _CircleDetailScreenState extends State<CircleDetailScreen>
    with SingleTickerProviderStateMixin {
  final CircleService _circleService = CircleService();
  late TabController _tabController;
  bool _isLoading = true;
  Map<String, dynamic>? _circleData;
  List<dynamic> _members = [];
  List<Dungeon> _raids = [];
  bool _isLoadingRaids = true;
  List<Map<String, dynamic>> _clearedRaids = [];
  bool _isLoadingCleared = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _fetchDetails();
    _fetchRaids();
    _fetchClearedRaids();
  }

  Future<void> _fetchDetails() async {
    setState(() => _isLoading = true);
    try {
      final data = await _circleService.getCircleDetails(widget.circleId);
      if (mounted) {
        if (data != null) {
          setState(() {
            _circleData = data;
            _members = data['members'] ?? [];
            _isLoading = false;
          });
        } else {
          setState(() => _isLoading = false);
        }
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _handlePromote(int userId, String username) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text("Promote Co-Leader"),
        content: Text("Jadikan $username sebagai satu-satunya Co-Leader?"),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: Text("Batal")),
          TextButton(
              onPressed: () => Navigator.pop(context, true),
              child: Text("Gas!")),
        ],
      ),
    );

    if (confirm == true) {
      setState(() => _isLoading = true);
      final result =
          await _circleService.promoteToCoLeader(widget.circleId, userId);
      if (result['success'] == true) {
        await _fetchDetails();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text("$username sekarang jadi Co-Leader!")),
          );
        }
      } else {
        setState(() => _isLoading = false);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
                content: Text(result['message'] ?? "Gagal promosi"),
                backgroundColor: Colors.red),
          );
        }
      }
    }
  }

  Future<void> _fetchRaids() async {
    if (!mounted) return;
    setState(() => _isLoadingRaids = true);
    try {
      final raids = await _circleService.getCircleRaids(widget.circleId);
      if (mounted) {
        setState(() {
          _raids = raids;
          _isLoadingRaids = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoadingRaids = false);
    }
  }

  Future<void> _fetchClearedRaids() async {
    if (!mounted) return;
    setState(() => _isLoadingCleared = true);
    try {
      final cleared = await _circleService.getClearedRaids(widget.circleId);
      if (mounted) {
        setState(() {
          _clearedRaids = cleared;
          _isLoadingCleared = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoadingCleared = false);
    }
  }

  Future<void> _claimReward(int dungeonId) async {
    final result = await _circleService.claimReward(widget.circleId, dungeonId);
    if (mounted) {
      if (result['success'] == true) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Hadiah diklaim!'),
            backgroundColor: Colors.green,
          ),
        );
        _fetchClearedRaids();
        _fetchDetails(); // Refresh circle stats and user stats implicitly via home refresh if needed
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Gagal klaim hadiah'),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    }
  }

  Future<void> _joinRaid(int dungeonId) async {
    final result =
        await _circleService.joinRaidLobby(widget.circleId, dungeonId);

    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'Terjadi kesalahan'),
          backgroundColor:
              result['success'] == true ? Colors.green : Colors.redAccent,
        ),
      );

      if (result['success'] == true) {
        _fetchRaids(); // Refresh player counts
      }
    }
  }

  void _showLeaveConfirmation(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.red.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.warning_rounded, color: Colors.red),
            ),
            const SizedBox(width: 12),
            Text(
              "Leave Circle?",
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.bold,
                fontSize: 18,
              ),
            ),
          ],
        ),
        content: Text(
          "Are you sure you want to leave this circle? You will lose your contribution progress.",
          style: GoogleFonts.plusJakartaSans(color: PremiumColor.slate500),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              "Cancel",
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w600,
                color: PremiumColor.slate500,
              ),
            ),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context); // Close dialog
              setState(() => _isLoading = true);
              final result = await _circleService.leaveCircle(widget.circleId);
              if (result['success'] == true) {
                if (mounted) Navigator.pop(context, true);
              } else {
                if (mounted) {
                  setState(() => _isLoading = false);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(result['message'] ?? "Failed to leave"),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              elevation: 0,
            ),
            child: Text(
              "Leave",
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        backgroundColor: PremiumColor.background,
        body: Center(
            child: CircularProgressIndicator(color: PremiumColor.primary)),
      );
    }

    if (_circleData == null) {
      return Scaffold(
        backgroundColor: PremiumColor.background,
        appBar: AppBar(title: const Text("Error")),
        body: const Center(child: Text("Failed to load circle details")),
      );
    }

    final circle = Circle.fromJson(_circleData!);
    final Color themeColor = _getColor(circle.colorHex);
    final IconData iconData = _getIconData(circle.iconName);

    return Scaffold(
      backgroundColor: PremiumColor.background,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: widget.isRoot
            ? null
            : IconButton(
                icon: const Icon(Icons.arrow_back_ios_new_rounded,
                    color: Colors.white),
                onPressed: () => Navigator.pop(context),
              ),
        actions: [
          if (_circleData?['my_role'] == 'none' ||
              _circleData?['my_role'] == null)
            Padding(
              padding: const EdgeInsets.only(right: 16),
              child: ElevatedButton(
                onPressed: () async {
                  setState(() => _isLoading = true);
                  final result =
                      await _circleService.joinCircle(widget.circleId);
                  if (result['success'] == true) {
                    await _fetchDetails();
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text("Welcome to the Circle!")),
                      );
                    }
                  } else {
                    setState(() => _isLoading = false);
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(
                              result['message'] ?? "Failed to join circle"),
                          backgroundColor: Colors.red,
                        ),
                      );
                    }
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: PremiumColor.primary,
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                  ),
                ),
                child: Text(
                  "Join Circle",
                  style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            )
          else
            PopupMenuButton<String>(
              icon: const Icon(Icons.more_vert_rounded, color: Colors.white),
              onSelected: (value) {
                if (value == 'leave') {
                  _showLeaveConfirmation(context);
                }
              },
              itemBuilder: (context) => [
                const PopupMenuItem(
                  value: 'leave',
                  child: Row(
                    children: [
                      Icon(Icons.logout_rounded, color: Colors.red, size: 20),
                      SizedBox(width: 8),
                      Text("Out Clan", style: TextStyle(color: Colors.red)),
                    ],
                  ),
                ),
              ],
            ),
        ],
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),

          // Header
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: 350,
            child: ClipPath(
              clipper: HeaderEllipseClipper(), // Reusing the clipper
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      themeColor,
                      themeColor.withOpacity(0.8),
                    ],
                  ),
                ),
                child: Stack(
                  children: [
                    const Positioned.fill(
                        child: Opacity(
                            opacity: 0.1, child: IslamicPatternBackground())),
                    Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const SizedBox(height: 60),
                          // Emblem
                          Container(
                            width: 100,
                            height: 100,
                            decoration: BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.2),
                                  blurRadius: 20,
                                  offset: const Offset(0, 10),
                                )
                              ],
                            ),
                            child: Icon(iconData, size: 50, color: themeColor),
                          ),
                          const SizedBox(height: 16),
                          Text(
                            circle.name,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 24,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 12, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.black.withOpacity(0.3),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              "Level ${circle.level} • ${circle.membersCount} Members",
                              style: GoogleFonts.plusJakartaSans(
                                color: Colors.white,
                                fontWeight: FontWeight.w600,
                                fontSize: 12,
                              ),
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

          // Content
          Padding(
            padding: const EdgeInsets.fromLTRB(0, 320, 0, 0),
            child: Column(
              children: [
                // Tab Bar
                Container(
                  margin: const EdgeInsets.symmetric(horizontal: 24),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                            color: Colors.black.withOpacity(0.05),
                            blurRadius: 10,
                            offset: const Offset(0, 5))
                      ]),
                  child: TabBar(
                    controller: _tabController,
                    labelColor: themeColor,
                    unselectedLabelColor: PremiumColor.slate400,
                    indicatorColor: themeColor,
                    indicatorWeight: 3,
                    labelStyle: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w700),
                    tabs: const [
                      Tab(text: "Overview"),
                      Tab(text: "Members"),
                      Tab(text: "Quests"),
                      Tab(text: "Misi Sukses"),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                Expanded(
                  child: TabBarView(
                    controller: _tabController,
                    children: [
                      _buildOverviewTab(circle),
                      _buildMembersTab(),
                      _buildQuestsTab(circle),
                      _buildClearedMissionsTab(),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOverviewTab(Circle circle) {
    return ListView(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      children: [
        _buildCard(
          "Description",
          Text(
            circle.description.isEmpty
                ? "No description provided."
                : circle.description,
            style: GoogleFonts.plusJakartaSans(
                color: PremiumColor.slate600, height: 1.5),
          ),
        ),
        const SizedBox(height: 16),
        _buildCard(
          "Circle Stats",
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStat("XP Total", "${circle.xp}", Icons.bolt_rounded),
              _buildStat("Rank", "#${circle.rank}", Icons.leaderboard_rounded),
              _buildStat(
                  "Weekly", "${circle.weeklyXp}", Icons.trending_up_rounded),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildMembersTab() {
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      itemCount: _members.length,
      itemBuilder: (context, index) {
        final member = _members[index];
        final role = member['pivot']['role'] ?? 'member';

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
          ),
          child: Row(
            children: [
              CircleAvatar(
                backgroundColor: PremiumColor.primary.withOpacity(0.1),
                child: Text((member['username'] ?? '?')[0].toUpperCase(),
                    style: const TextStyle(color: PremiumColor.primary)),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      member['username'] ?? 'Unknown Hunter',
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w700,
                          color: PremiumColor.slate800),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 2),
                    Row(
                      children: [
                        Flexible(
                          child: Text(
                            "Lvl ${member['level'] ?? 1}",
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 12, color: PremiumColor.slate500),
                            overflow: TextOverflow.ellipsis,
                            maxLines: 1,
                          ),
                        ),
                        if (member['pivot'] != null &&
                            member['pivot']['xp_contribution'] != null) ...[
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(
                              color: Colors.amber.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              "+${member['pivot']['xp_contribution']} XP",
                              style: GoogleFonts.robotoMono(
                                fontSize: 10,
                                fontWeight: FontWeight.w800,
                                color: Colors.amber[800],
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              _buildRoleBadge(role),
              if (_isLeader && role == 'member') ...[
                const SizedBox(width: 8),
                IconButton(
                  onPressed: _hasCoLeader
                      ? null
                      : () => _handlePromote(member['id'], member['username']),
                  icon: Icon(
                    Icons.arrow_circle_up_rounded,
                    color: _hasCoLeader ? Colors.grey : PremiumColor.accent,
                    size: 24,
                  ),
                  tooltip: "Promote to Co-Leader",
                ),
              ],
            ],
          ),
        );
      },
    );
  }

  bool get _isLeader {
    if (_circleData == null) return false;
    return _circleData!['my_role'] == 'leader';
  }

  bool get _hasCoLeader {
    return _members
        .any((m) => m['pivot'] != null && m['pivot']['role'] == 'co-leader');
  }

  Widget _buildRoleBadge(String role) {
    Color badgeColor;
    String label;

    switch (role.toLowerCase()) {
      case 'leader':
        badgeColor = PremiumColor.accent;
        label = "LEADER";
        break;
      case 'co-leader':
      case 'coleader':
        badgeColor = Colors.orange;
        label = "CO-LEADER";
        break;
      case 'member':
      default:
        badgeColor = PremiumColor.slate400;
        label = "MEMBER";
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: badgeColor,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        label,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 10,
          color: Colors.white,
          fontWeight: FontWeight.w800,
        ),
      ),
    );
  }

  Widget _buildQuestsTab(Circle circle) {
    if (_isLoadingRaids) {
      return const Center(child: CircularProgressIndicator());
    }

    return Column(
      children: [
        if (_isLeader)
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () => _showCreateQuestModal(context),
                icon: const Icon(Icons.add_box_rounded),
                label: const Text("Create New Quest (Leader Only)"),
                style: ElevatedButton.styleFrom(
                  backgroundColor: PremiumColor.primary,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
              ),
            ),
          ),
        Expanded(
          child: _raids.isEmpty
              ? Center(
                  child: Text(
                    "No active dungeon gates found.",
                    style: GoogleFonts.plusJakartaSans(
                        color: PremiumColor.slate400),
                  ),
                )
              : _buildUnlockedQuests(circle),
        ),
      ],
    );
  }

  void _showCreateQuestModal(BuildContext context) async {
    final result = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => CreateQuestModal(circleId: widget.circleId),
    );
    if (result == true) {
      _fetchRaids();
    }
  }

  Widget _buildUnlockedQuests(Circle circle) {
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      itemCount: _raids.length,
      itemBuilder: (context, index) {
        final raid = _raids[index];
        return Container(
          margin: const EdgeInsets.only(bottom: 20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color: _getRankColor(raid.rank).withOpacity(0.2),
              width: 2,
            ),
            boxShadow: [
              BoxShadow(
                color: _getRankColor(raid.rank).withOpacity(0.05),
                blurRadius: 20,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(24),
            child: Stack(
              children: [
                // Inner content
                Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildRankInsignia(raid.rank),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  raid.name.toUpperCase(),
                                  style: GoogleFonts.plusJakartaSans(
                                    fontWeight: FontWeight.w900,
                                    fontSize: 18,
                                    letterSpacing: 0.5,
                                    color: PremiumColor.slate800,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Wrap(
                                  spacing: 8,
                                  runSpacing: 4,
                                  crossAxisAlignment: WrapCrossAlignment.center,
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 8, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: _getRankColor(raid.rank)
                                            .withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(4),
                                      ),
                                      child: Text(
                                        "Lvl ${raid.minLevel}+",
                                        style: GoogleFonts.robotoMono(
                                          fontSize: 10,
                                          fontWeight: FontWeight.w800,
                                          color: _getRankColor(raid.rank),
                                        ),
                                      ),
                                    ),
                                    Text(
                                      "Gate: ${raid.status.toUpperCase()}",
                                      style: GoogleFonts.robotoMono(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                        color: PremiumColor.slate400,
                                      ),
                                    ),
                                    if (raid.isParticipating)
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 8, vertical: 2),
                                        decoration: BoxDecoration(
                                          color: Colors.green.withOpacity(0.1),
                                          borderRadius:
                                              BorderRadius.circular(4),
                                          border: Border.all(
                                              color: Colors.green, width: 1),
                                        ),
                                        child: Text(
                                          "JOINED",
                                          style: GoogleFonts.robotoMono(
                                            fontSize: 10,
                                            fontWeight: FontWeight.w900,
                                            color: Colors.green,
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
                      const SizedBox(height: 20),
                      Text(
                        raid.description,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13,
                          color: PremiumColor.slate500,
                          height: 1.5,
                        ),
                      ),
                      const SizedBox(height: 24),
                      // New Info Bar
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: PremiumColor.slate50,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                              color: PremiumColor.slate200.withOpacity(0.5)),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            _buildQuestInfoItem(
                                "PARTY SIZE",
                                "${raid.currentPlayers}/${raid.requiredPlayers}",
                                Icons.groups_rounded,
                                _getRankColor(raid.rank)),
                            Container(
                                width: 1,
                                height: 24,
                                color: PremiumColor.slate200),
                            _buildQuestInfoItem(
                                "REWARD",
                                "${raid.rewardExp} EXP",
                                Icons.stars_rounded,
                                PremiumColor.highlight),
                            if (raid.objectiveType != null) ...[
                              Container(
                                  width: 1,
                                  height: 24,
                                  color: PremiumColor.slate200),
                              _buildQuestInfoItem(
                                  "MISI",
                                  "${_getObjectiveLabel(raid.objectiveType!)} ×${raid.objectiveTarget}",
                                  _getObjectiveIcon(raid.objectiveType!),
                                  const Color(0xFF0EA5E9)),
                            ],
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            "GATE CLARITY",
                            style: GoogleFonts.plusJakartaSans(
                              fontWeight: FontWeight.w800,
                              fontSize: 11,
                              letterSpacing: 1,
                              color: PremiumColor.slate400,
                            ),
                          ),
                          Text(
                            "${(raid.progress * 100).toInt()}%",
                            style: GoogleFonts.robotoMono(
                              fontWeight: FontWeight.w900,
                              fontSize: 13,
                              color: _getRankColor(raid.rank),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Stack(
                        children: [
                          Container(
                            height: 10,
                            decoration: BoxDecoration(
                              color: PremiumColor.slate200,
                              borderRadius: BorderRadius.circular(5),
                            ),
                          ),
                          FractionallySizedBox(
                            widthFactor: raid.progress.clamp(0.01, 1.0),
                            child: Container(
                              height: 10,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    _getRankColor(raid.rank),
                                    _getRankColor(raid.rank).withOpacity(0.6),
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(5),
                                boxShadow: [
                                  BoxShadow(
                                    color: _getRankColor(raid.rank)
                                        .withOpacity(0.3),
                                    blurRadius: 10,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: ElevatedButton(
                          onPressed: () {
                            if (raid.status == 'in_progress') {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                    content: Text(
                                        "Raid is in progress! Good luck Hunter!")),
                              );
                            } else {
                              _joinRaid(raid.id);
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: raid.status == 'in_progress'
                                ? Colors.grey[800]
                                : _getRankColor(raid.rank),
                            foregroundColor: Colors.white,
                            elevation: raid.status == 'in_progress' ? 0 : 8,
                            shadowColor:
                                _getRankColor(raid.rank).withOpacity(0.5),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                  raid.status == 'in_progress'
                                      ? Icons.security_rounded
                                      : raid.isParticipating
                                          ? Icons.check_circle_rounded
                                          : Icons.login_rounded,
                                  size: 20),
                              const SizedBox(width: 8),
                              Text(
                                raid.status == 'in_progress'
                                    ? "RAID IN PROGRESS"
                                    : raid.isParticipating
                                        ? "YOU ARE IN PARTY"
                                        : raid.status == 'waiting'
                                            ? "JOIN PARTY LOBBY"
                                            : "ENTER THE GATE",
                                style: GoogleFonts.plusJakartaSans(
                                  fontWeight: FontWeight.w900,
                                  fontSize: 15,
                                  letterSpacing: 1.2,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildQuestInfoItem(
      String label, String value, IconData icon, Color color) {
    return Column(
      children: [
        Row(
          children: [
            Icon(icon, size: 14, color: color),
            const SizedBox(width: 6),
            Text(
              label,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.w700,
                color: PremiumColor.slate400,
                letterSpacing: 0.5,
              ),
            ),
          ],
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: GoogleFonts.robotoMono(
            fontSize: 16,
            fontWeight: FontWeight.w900,
            color: PremiumColor.slate800,
          ),
        ),
      ],
    );
  }

  Widget _buildRankInsignia(String rank) {
    return Container(
      width: 50,
      height: 50,
      decoration: BoxDecoration(
        color: _getRankColor(rank).withOpacity(0.1),
        shape: BoxShape.circle,
        border: Border.all(color: _getRankColor(rank), width: 2),
      ),
      child: Center(
        child: Text(
          rank,
          style: GoogleFonts.oswald(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: _getRankColor(rank),
          ),
        ),
      ),
    );
  }

  Color _getRankColor(String rank) {
    switch (rank.toUpperCase()) {
      case 'S':
        return Colors.deepPurpleAccent;
      case 'A':
        return Colors.redAccent;
      case 'B':
        return Colors.orangeAccent;
      case 'C':
        return Colors.blueAccent;
      case 'D':
        return Colors.greenAccent;
      default:
        return PremiumColor.slate400;
    }
  }

  Widget _buildCard(String title, Widget content) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 10,
              offset: const Offset(0, 4)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  color: PremiumColor.primary)),
          const SizedBox(height: 12),
          content,
        ],
      ),
    );
  }

  Widget _buildStat(String label, String value, IconData icon) {
    return Column(
      children: [
        Icon(icon, color: PremiumColor.accent, size: 24),
        const SizedBox(height: 8),
        Text(value,
            style: GoogleFonts.plusJakartaSans(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: PremiumColor.slate800)),
        Text(label,
            style: GoogleFonts.plusJakartaSans(
                fontSize: 12, color: PremiumColor.slate500)),
      ],
    );
  }

  Color _getColor(String? hex) {
    if (hex == null || hex.isEmpty) return PremiumColor.primary;
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (e) {
      return PremiumColor.primary;
    }
  }

  IconData _getIconData(String? name) {
    switch (name) {
      case 'sunny':
        return Icons.wb_sunny_rounded;
      case 'night':
        return Icons.nights_stay_rounded;
      case 'charity':
        return Icons.volunteer_activism_rounded;
      case 'quran':
        return Icons.menu_book_rounded;
      case 'adhkar':
        return Icons.wb_twilight_rounded;
      case 'fasting':
        return Icons.restaurant_menu_rounded;
      case 'study':
        return Icons.library_books_rounded;
      case 'community':
        return Icons.handshake_rounded;
      default:
        return Icons.groups_rounded;
    }
  }

  String _getObjectiveLabel(String type) {
    switch (type.toLowerCase()) {
      case 'prayer':
        return 'Shalat';
      case 'quran':
        return 'Halaman';
      case 'habit':
        return 'Habit';
      case 'journal':
        return 'Jurnal';
      case 'custom':
        return 'Target';
      default:
        return type;
    }
  }

  IconData _getObjectiveIcon(String type) {
    switch (type.toLowerCase()) {
      case 'prayer':
        return Icons.mosque_rounded;
      case 'quran':
        return Icons.menu_book_rounded;
      case 'habit':
        return Icons.repeat_rounded;
      case 'journal':
        return Icons.edit_note_rounded;
      default:
        return Icons.flag_rounded;
    }
  }

  Widget _buildClearedMissionsTab() {
    if (_isLoadingCleared) {
      return const Center(
          child: CircularProgressIndicator(color: Color(0xFFD4AF37)));
    }

    if (_clearedRaids.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: const Color(0xFFD4AF37).withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.emoji_events_rounded,
                  size: 44, color: Color(0xFFD4AF37)),
            ),
            const SizedBox(height: 16),
            Text(
              "Belum Ada Misi Sukses",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: PremiumColor.slate600,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              "Selesaikan Rift Gate bersama tim untuk\nmelihat misi yang berhasil di sini.",
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, color: PremiumColor.slate400, height: 1.5),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 24),
      itemCount: _clearedRaids.length,
      itemBuilder: (context, index) {
        final mission = _clearedRaids[index];
        final rank = mission['rank'] as String? ?? 'E';
        final participants = mission['participants'] as List<dynamic>? ?? [];
        final totalContrib = mission['total_contribution'] ?? 0;
        final clearedAt = mission['cleared_at'] as String?;
        final rewardExp = mission['reward_exp'] ?? 0;
        final objType = mission['objective_type'] as String?;
        final objTarget = mission['objective_target'] ?? 0;

        return Container(
          margin: const EdgeInsets.only(bottom: 20),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [
                const Color(0xFFD4AF37).withOpacity(0.08),
                Colors.white,
              ],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color: const Color(0xFFD4AF37).withOpacity(0.4),
              width: 1.5,
            ),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFFD4AF37).withOpacity(0.08),
                blurRadius: 20,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Gold rank badge
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFFD4AF37), Color(0xFFFBD000)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFFD4AF37).withOpacity(0.3),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Center(
                        child: Text(
                          rank,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 18,
                            fontWeight: FontWeight.w900,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            (mission['name'] as String? ?? 'Unknown Mission')
                                .toUpperCase(),
                            style: GoogleFonts.plusJakartaSans(
                              fontWeight: FontWeight.w900,
                              fontSize: 14,
                              color: PremiumColor.slate800,
                              letterSpacing: 0.5,
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.visible,
                          ),
                          const SizedBox(height: 4),
                          Wrap(
                            spacing: 8,
                            runSpacing: 4,
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.check_circle_rounded,
                                      color: Color(0xFFD4AF37), size: 12),
                                  const SizedBox(width: 4),
                                  Text(
                                    "MISI SUKSES",
                                    style: GoogleFonts.robotoMono(
                                      fontSize: 9,
                                      fontWeight: FontWeight.w700,
                                      color: const Color(0xFFD4AF37),
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ],
                              ),
                              if (clearedAt != null)
                                Text(
                                  "• ${clearedAt.substring(0, 10)}",
                                  style: GoogleFonts.robotoMono(
                                    fontSize: 9,
                                    fontWeight: FontWeight.w700,
                                    color: PremiumColor.slate400,
                                  ),
                                ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Wrap(
                  spacing: 12,
                  runSpacing: 8,
                  children: [
                    // EXP reward badge
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        color: const Color(0xFFD4AF37).withOpacity(0.08),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: const Color(0xFFD4AF37).withOpacity(0.3),
                        ),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(Icons.bolt_rounded,
                              size: 14, color: Color(0xFFD4AF37)),
                          const SizedBox(width: 4),
                          Text(
                            "+$rewardExp EXP",
                            style: GoogleFonts.robotoMono(
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFFD4AF37),
                            ),
                          ),
                        ],
                      ),
                    ),
                    if (objType != null)
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: const Color(0xFF0EA5E9).withOpacity(0.08),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                              color: const Color(0xFF0EA5E9).withOpacity(0.2)),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(_getObjectiveIcon(objType),
                                size: 14, color: const Color(0xFF0EA5E9)),
                            const SizedBox(width: 6),
                            Text(
                              "${_getObjectiveLabel(objType)} ×$objTarget",
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: const Color(0xFF0EA5E9),
                              ),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 16),
                const Divider(height: 1),
                const SizedBox(height: 16),
                // Participants
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      "KONTRIBUTOR MISI",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: PremiumColor.slate400,
                        letterSpacing: 1.5,
                      ),
                    ),
                    if (totalContrib > 0)
                      Text(
                        "Total: $totalContrib",
                        style: GoogleFonts.robotoMono(
                          fontSize: 11,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xFFD4AF37),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 12),
                ...participants.map((p) {
                  final user = p['user'] as Map<String, dynamic>? ?? {};
                  final score = p['contribution_score'] ?? 0;
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 8),
                    child: Row(
                      children: [
                        CircleAvatar(
                          radius: 14,
                          backgroundColor:
                              const Color(0xFFD4AF37).withOpacity(0.15),
                          child: Text(
                            ((user['username'] as String?) ?? '?')[0]
                                .toUpperCase(),
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              color: Color(0xFFD4AF37),
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            user['username'] as String? ?? 'Unknown',
                            style: GoogleFonts.plusJakartaSans(
                              fontWeight: FontWeight.w600,
                              fontSize: 13,
                              color: PremiumColor.slate600,
                            ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: Colors.green.withOpacity(0.08),
                            borderRadius: BorderRadius.circular(6),
                            border: Border.all(
                                color: Colors.green.withOpacity(0.2)),
                          ),
                          child: Text(
                            "Score: $score",
                            style: GoogleFonts.robotoMono(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: Colors.green[700],
                            ),
                          ),
                        ),
                      ],
                    ),
                  );
                }),
                // Claim Reward Button
                if (mission['is_participating'] == true) ...[
                  const SizedBox(height: 16),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: mission['is_rewarded'] == true
                          ? null
                          : () => _claimReward(mission['id']),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFD4AF37),
                        disabledBackgroundColor: Colors.grey[200],
                        foregroundColor: Colors.white,
                        elevation: 0,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            mission['is_rewarded'] == true
                                ? Icons.card_giftcard_rounded
                                : Icons.emoji_events_rounded,
                            size: 20,
                          ),
                          const SizedBox(width: 10),
                          Text(
                            mission['is_rewarded'] == true
                                ? "HADIAH SUDAH DIKLAIM"
                                : "KLAIM HADIAH MISI",
                            style: GoogleFonts.plusJakartaSans(
                              fontWeight: FontWeight.w900,
                              fontSize: 14,
                              letterSpacing: 1.0,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
        );
      },
    );
  }
}

class CreateQuestModal extends StatefulWidget {
  final int circleId;
  const CreateQuestModal({super.key, required this.circleId});

  @override
  State<CreateQuestModal> createState() => _CreateQuestModalState();
}

class _CreateQuestModalState extends State<CreateQuestModal> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _descController = TextEditingController();
  final _minLevelController = TextEditingController(text: "1");
  final _rewardsController = TextEditingController(text: "100");
  final _requiredPlayersController = TextEditingController(text: "2");
  final CircleService _circleService = CircleService();

  String _selectedRank = 'E';
  String _selectedObjectiveType = 'prayer';
  final _objectiveTargetController = TextEditingController(text: "5");
  bool _isLoading = false;

  final List<String> _ranks = ['E', 'D', 'C', 'B', 'A', 'S'];
  final List<Map<String, dynamic>> _objectiveTypes = [
    {'value': 'prayer', 'label': 'Shalat', 'icon': Icons.mosque_rounded},
    {
      'value': 'quran',
      'label': 'Halaman Quran',
      'icon': Icons.menu_book_rounded
    },
    {'value': 'habit', 'label': 'Habit', 'icon': Icons.repeat_rounded},
    {'value': 'journal', 'label': 'Jurnal', 'icon': Icons.edit_note_rounded},
    {'value': 'custom', 'label': 'Custom', 'icon': Icons.flag_rounded},
  ];

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);
    final data = {
      'name': _nameController.text,
      'description': _descController.text,
      'rank': _selectedRank,
      'min_level': int.tryParse(_minLevelController.text) ?? 1,
      'required_players': int.tryParse(_requiredPlayersController.text) ?? 2,
      'reward_exp': int.tryParse(_rewardsController.text) ?? 100,
      'objective_type': _selectedObjectiveType,
      'objective_target': int.tryParse(_objectiveTargetController.text) ?? 5,
    };
    final result = await _circleService.createCircleRaid(widget.circleId, data);
    setState(() => _isLoading = false);
    if (mounted) {
      if (result['success'] == true) {
        Navigator.pop(context, true);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Quest created successfully!")),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? "Failed to create quest"),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Color _getRankColor(String rank) {
    switch (rank.toUpperCase()) {
      case 'S':
        return const Color(0xFFFFCC00);
      case 'A':
        return const Color(0xFFE91E63);
      case 'B':
        return const Color(0xFF2196F3);
      case 'C':
        return const Color(0xFF4CAF50);
      case 'D':
        return const Color(0xFFFF9800);
      default:
        return const Color(0xFF9E9E9E);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
      ),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              Text(
                "MANIFESTASI GERBANG",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF134E4A), // teal-900
                  letterSpacing: 1.5,
                ),
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  Container(
                    width: 6,
                    height: 6,
                    decoration: const BoxDecoration(
                      color: Color(0xFF22D3EE), // cyan-400
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    "MEMBUKA RIFT BARU DALAM MATRIKS SISTEM",
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 9,
                      fontWeight: FontWeight.w800,
                      color: PremiumColor.slate500,
                      letterSpacing: 2.0,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 32),
              Text(
                "IDENTIFIKASI MANIFESTASI (NAMA)",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF134E4A).withOpacity(0.5),
                  letterSpacing: 2.0,
                ),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _nameController,
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w800,
                  fontSize: 16,
                  color: const Color(0xFF134E4A),
                ),
                textCapitalization: TextCapitalization.characters,
                decoration: InputDecoration(
                  hintText: "CONTOH: THE ABYSSAL ECHO",
                  hintStyle: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w800,
                    color: Colors.grey[300],
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide:
                        BorderSide(color: Colors.grey.shade200, width: 2),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide:
                        BorderSide(color: Colors.grey.shade200, width: 2),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide:
                        const BorderSide(color: Color(0xFF22D3EE), width: 2),
                  ),
                  filled: true,
                  fillColor: Colors.grey[50],
                  contentPadding: const EdgeInsets.all(20),
                ),
                validator: (value) => (value == null || value.isEmpty)
                    ? "IDENTIFIKASI WAJIB DIISI"
                    : null,
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          "MINIMAL LEVEL PROXY",
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 10,
                            fontWeight: FontWeight.w900,
                            color: const Color(0xFF134E4A).withOpacity(0.5),
                            letterSpacing: 1.0,
                          ),
                        ),
                        const SizedBox(height: 8),
                        TextFormField(
                          controller: _minLevelController,
                          keyboardType: TextInputType.number,
                          style: GoogleFonts.robotoMono(
                            fontWeight: FontWeight.w900,
                            fontSize: 16,
                            color: const Color(0xFF134E4A),
                          ),
                          decoration: InputDecoration(
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(
                                  color: Colors.grey.shade200, width: 2),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(
                                  color: Colors.grey.shade200, width: 2),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: const BorderSide(
                                  color: Color(0xFF22D3EE), width: 2),
                            ),
                            filled: true,
                            fillColor: Colors.grey[50],
                            contentPadding: const EdgeInsets.all(16),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          "MANIFESTASI EXP",
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 10,
                            fontWeight: FontWeight.w900,
                            color: Colors.amber.shade700,
                            letterSpacing: 1.0,
                          ),
                        ),
                        const SizedBox(height: 8),
                        TextFormField(
                          controller: _rewardsController,
                          keyboardType: TextInputType.number,
                          style: GoogleFonts.robotoMono(
                            fontWeight: FontWeight.w900,
                            fontSize: 16,
                            color: const Color(0xFF134E4A),
                          ),
                          decoration: InputDecoration(
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(
                                  color: Colors.grey.shade200, width: 2),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(
                                  color: Colors.grey.shade200, width: 2),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(
                                  color: Colors.amber.shade400, width: 2),
                            ),
                            filled: true,
                            fillColor: Colors.grey[50],
                            prefixIcon: const Icon(Icons.arrow_upward_rounded,
                                color: Colors.amber, size: 18),
                            contentPadding: const EdgeInsets.all(16),
                          ),
                        ),
                      ],
                    ),
                  )
                ],
              ),
              const SizedBox(height: 24),
              Text(
                "DOKUMENTASI TEOLOGIS (DESKRIPSI)",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF134E4A).withOpacity(0.5),
                  letterSpacing: 2.0,
                ),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _descController,
                maxLines: 4,
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w600,
                  fontSize: 14,
                  color: Colors.grey[800],
                ),
                decoration: InputDecoration(
                  hintText: "Jelaskan parameter rift dan data historis...",
                  hintStyle: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w600,
                    color: Colors.grey[400],
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide:
                        BorderSide(color: Colors.grey.shade200, width: 2),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide:
                        BorderSide(color: Colors.grey.shade200, width: 2),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(20),
                    borderSide:
                        const BorderSide(color: Color(0xFF22D3EE), width: 2),
                  ),
                  filled: true,
                  fillColor: Colors.grey[50],
                  contentPadding: const EdgeInsets.all(20),
                ),
              ),
              const SizedBox(height: 24),
              Text(
                "RENTANG OTORITAS (RANK)",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF134E4A).withOpacity(0.5),
                  letterSpacing: 2.0,
                ),
              ),
              const SizedBox(height: 12),
              SizedBox(
                height: 60,
                child: ListView.separated(
                  scrollDirection: Axis.horizontal,
                  itemCount: _ranks.length,
                  separatorBuilder: (context, index) =>
                      const SizedBox(width: 12),
                  itemBuilder: (context, index) {
                    final rank = _ranks[index];
                    final color = _getRankColor(rank);
                    final isSelected = _selectedRank == rank;
                    return GestureDetector(
                      onTap: () => setState(() => _selectedRank = rank),
                      child: Container(
                        width: 60,
                        height: 60,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(
                          color: isSelected
                              ? color.withOpacity(0.15)
                              : Colors.grey[50],
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isSelected ? color : Colors.grey.shade200,
                            width: 2,
                          ),
                        ),
                        child: Text(
                          rank,
                          style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w900,
                            fontSize: 22,
                            color: isSelected ? color : Colors.grey[400],
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 24),
              // ─── Objective Section ────────────────────────────────────────────
              Text(
                "TIPE MISI (OBJEKTIF)",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF134E4A).withOpacity(0.5),
                  letterSpacing: 2.0,
                ),
              ),
              const SizedBox(height: 8),
              Container(
                decoration: BoxDecoration(
                  color: Colors.grey[50],
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.grey.shade200, width: 2),
                ),
                child: DropdownButtonFormField<String>(
                  value: _selectedObjectiveType,
                  decoration: const InputDecoration(
                    border: InputBorder.none,
                    contentPadding:
                        EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                  ),
                  style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w700,
                    fontSize: 14,
                    color: const Color(0xFF134E4A),
                  ),
                  dropdownColor: Colors.white,
                  items: _objectiveTypes
                      .map((type) => DropdownMenuItem<String>(
                            value: type['value'] as String,
                            child: Row(
                              children: [
                                Icon(type['icon'] as IconData,
                                    size: 18, color: const Color(0xFF0EA5E9)),
                                const SizedBox(width: 10),
                                Text(type['label'] as String),
                              ],
                            ),
                          ))
                      .toList(),
                  onChanged: (val) {
                    if (val != null) {
                      setState(() => _selectedObjectiveType = val);
                    }
                  },
                ),
              ),
              const SizedBox(height: 16),
              Text(
                "TARGET MISI (JUMLAH)",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF134E4A).withOpacity(0.5),
                  letterSpacing: 2.0,
                ),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _objectiveTargetController,
                keyboardType: TextInputType.number,
                style: GoogleFonts.robotoMono(
                  fontWeight: FontWeight.w900,
                  fontSize: 16,
                  color: const Color(0xFF134E4A),
                ),
                decoration: InputDecoration(
                  hintText: "Contoh: 5",
                  hintStyle: GoogleFonts.robotoMono(
                      color: Colors.grey[300], fontWeight: FontWeight.w700),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide:
                        BorderSide(color: Colors.grey.shade200, width: 2),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide:
                        BorderSide(color: Colors.grey.shade200, width: 2),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide:
                        const BorderSide(color: Color(0xFF0EA5E9), width: 2),
                  ),
                  filled: true,
                  fillColor: Colors.grey[50],
                  prefixIcon: const Icon(Icons.flag_rounded,
                      color: Color(0xFF0EA5E9), size: 20),
                  contentPadding: const EdgeInsets.all(16),
                ),
                validator: (val) {
                  if (val == null || val.isEmpty) return "Isi target misi";
                  if (int.tryParse(val) == null || int.parse(val) < 1) {
                    return "Target minimal 1";
                  }
                  return null;
                },
              ),
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity,
                height: 65,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF134E4A), // teal-900
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(20),
                    ),
                    elevation: 8,
                    shadowColor: const Color(0xFF134E4A).withOpacity(0.4),
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(
                            color: Color(0xFF22D3EE),
                            strokeWidth: 3,
                          ),
                        )
                      : Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              "BANGUN MANIFESTASI GERBANG",
                              style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.w900,
                                fontSize: 12,
                                letterSpacing: 2.0,
                                color: Colors.white,
                              ),
                            ),
                            const SizedBox(width: 12),
                            const Icon(
                              Icons.add_circle_outline_rounded,
                              color: Color(0xFF22D3EE),
                              size: 20,
                            ),
                          ],
                        ),
                ),
              ),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }
}
