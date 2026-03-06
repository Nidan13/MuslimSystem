import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'widgets/custom_background.dart';
import '../services/leaderboard_service.dart';
import '../services/storage_service.dart';

class RankingScreen extends StatefulWidget {
  const RankingScreen({super.key});

  @override
  State<RankingScreen> createState() => _RankingScreenState();
}

class _RankingScreenState extends State<RankingScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final LeaderboardService _leaderboardService = LeaderboardService();

  List<Map<String, dynamic>> _topUsers = [];
  List<Map<String, dynamic>> _topCircles = [];
  int? _myId;
  bool _isLoading = true;
  String _selectedGender = 'all';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _fetchData();
  }

  Future<void> _fetchData({String? gender}) async {
    setState(() {
      _isLoading = true;
      if (gender != null) _selectedGender = gender;
    });
    final results = await Future.wait([
      _leaderboardService.getUserLeaderboard(gender: _selectedGender),
      _leaderboardService.getCircleLeaderboard(),
      StorageService.getUserId(),
    ]);

    if (mounted) {
      final List<Map<String, dynamic>> users =
          results[0] as List<Map<String, dynamic>>;
      final List<Map<String, dynamic>> circles =
          results[1] as List<Map<String, dynamic>>;

      // Sort users by level descending, then current_exp descending
      users.sort((a, b) {
        final aLvl = a['level'] ?? 0;
        final bLvl = b['level'] ?? 0;
        if (bLvl != aLvl) return bLvl.compareTo(aLvl);

        final aExp = a['current_exp'] ?? 0;
        final bExp = b['current_exp'] ?? 0;
        return bExp.compareTo(aExp);
      });

      // Sort circles by level descending, then members_count
      circles.sort((a, b) {
        final aLvl = a['level'] ?? 0;
        final bLvl = b['level'] ?? 0;
        if (bLvl != aLvl) return bLvl.compareTo(aLvl);

        final aMemb = a['members_count'] ?? 0;
        final bMemb = b['members_count'] ?? 0;
        return bMemb.compareTo(aMemb);
      });

      setState(() {
        _topUsers = users;
        _topCircles = circles;
        _myId = results[2] as int?;
        _isLoading = false;
      });
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          NestedScrollView(
            headerSliverBuilder: (context, innerBoxIsScrolled) => [
              _buildSliverHeader(),
              SliverPersistentHeader(
                pinned: true,
                delegate: _SliverAppBarDelegate(
                  TabBar(
                    controller: _tabController,
                    labelColor: PremiumColor.primary,
                    unselectedLabelColor: Colors.grey[400],
                    indicatorColor: PremiumColor.primary,
                    indicatorWeight: 3,
                    labelStyle: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w900,
                        fontSize: 13,
                        letterSpacing: 0.5),
                    unselectedLabelStyle: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w700, fontSize: 13),
                    tabs: const [
                      Tab(text: "TOP MEMBERS"),
                      Tab(text: "TOP CIRCLES"),
                    ],
                  ),
                ),
              ),
            ],
            body: _isLoading
                ? const Center(
                    child:
                        CircularProgressIndicator(color: PremiumColor.primary))
                : TabBarView(
                    controller: _tabController,
                    children: [
                      _TopMembersList(
                        users: _topUsers,
                        myId: _myId,
                        selectedGender: _selectedGender, // Pass selected gender
                        onGenderChanged: (gender) {
                          _fetchData(gender: gender);
                        },
                      ),
                      _TopCirclesList(circles: _topCircles),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 200,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: BoxDecoration(
              color: PremiumColor.primary,
              gradient: RadialGradient(
                center: Alignment.topRight,
                radius: 1.5,
                colors: [
                  Colors.white.withOpacity(0.1),
                  PremiumColor.primary,
                ],
              ),
              image: const DecorationImage(
                image: NetworkImage(
                    "https://www.transparenttextures.com/patterns/handmade-paper.png"),
                opacity: 0.1,
                fit: BoxFit.cover,
              ),
            ),
            padding: const EdgeInsets.fromLTRB(24, 0, 24, 60),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Icon(Icons.emoji_events_rounded,
                          color: Color(0xFFFFD700), size: 32),
                    ),
                    const SizedBox(width: 16),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          "HALL OF FAME",
                          style: GoogleFonts.plusJakartaSans(
                            color: Colors.white.withOpacity(0.7),
                            fontSize: 12,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 2.0,
                          ),
                        ),
                        Text(
                          "Leaderboards",
                          style: GoogleFonts.playfairDisplay(
                            color: Colors.white,
                            fontSize: 32,
                            fontWeight: FontWeight.bold,
                            height: 1.0,
                          ),
                        ),
                      ],
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
}

// -----------------------------------------------------------------------------
// Top Members List
// -----------------------------------------------------------------------------
class _TopMembersList extends StatefulWidget {
  final List<Map<String, dynamic>> users;
  final int? myId;
  final String selectedGender; // Added this
  final Function(String) onGenderChanged;
  const _TopMembersList({
    required this.users,
    this.myId,
    required this.selectedGender,
    required this.onGenderChanged,
  });

  @override
  State<_TopMembersList> createState() => _TopMembersListState();
}

class _TopMembersListState extends State<_TopMembersList> {
  // Use widget.selectedGender for selection logic

  @override
  Widget build(BuildContext context) {
    if (widget.users.isEmpty && widget.selectedGender == 'all') {
      return Center(
          child: Text("No users found",
              style:
                  GoogleFonts.plusJakartaSans(color: PremiumColor.slate500)));
    }

    final top3 = widget.users.take(3).toList();
    final remaining = widget.users.skip(3).toList();

    return CustomScrollView(
      slivers: [
        // Gender Filter Chips
        SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 0),
            child: Row(
              children: [
                _buildFilterChip("ALL", 'all'),
                const SizedBox(width: 8),
                _buildFilterChip("MALE", 'male'),
                const SizedBox(width: 8),
                _buildFilterChip("FEMALE", 'female'),
              ],
            ),
          ),
        ),
        SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: _PodiumSection(isMember: true, data: top3),
          ),
        ),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(24, 0, 24, 100),
          sliver: remaining.isEmpty && top3.isEmpty
              ? SliverFillRemaining(
                  hasScrollBody: false,
                  child: Center(
                    child: Text(
                      "No members found in this category",
                      style: GoogleFonts.plusJakartaSans(
                          color: PremiumColor.slate400),
                    ),
                  ),
                )
              : SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (context, index) {
                      final user = remaining[index];
                      final rank = index + 4; // 1,2,3 taken
                      return _RankListItem(
                        rank: rank,
                        name: user['username'],
                        subtitle:
                            "Lvl ${user['level']} • ${user['current_exp']} XP",
                        isMember: true,
                        isMe: user['id'] == widget.myId,
                      );
                    },
                    childCount: remaining.length,
                  ),
                ),
        ),
      ],
    );
  }

  Widget _buildFilterChip(String label, String value) {
    bool isSelected = widget.selectedGender == value; // Use widget value
    return GestureDetector(
      onTap: () {
        widget.onGenderChanged(value);
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
}

// -----------------------------------------------------------------------------
// Top Circles List
// -----------------------------------------------------------------------------
class _TopCirclesList extends StatelessWidget {
  final List<Map<String, dynamic>> circles;
  const _TopCirclesList({required this.circles});

  @override
  Widget build(BuildContext context) {
    if (circles.isEmpty) {
      return Center(
          child: Text("No circles found",
              style:
                  GoogleFonts.plusJakartaSans(color: PremiumColor.slate500)));
    }

    final top3 = circles.take(3).toList();
    final remaining = circles.skip(3).toList();

    return CustomScrollView(
      slivers: [
        SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: _PodiumSection(isMember: false, data: top3),
          ),
        ),
        SliverPadding(
          padding: const EdgeInsets.fromLTRB(24, 0, 24, 100),
          sliver: SliverList(
            delegate: SliverChildBuilderDelegate(
              (context, index) {
                final circle = remaining[index];
                final rank = index + 4;
                return _RankListItem(
                  rank: rank,
                  name: circle['name'],
                  subtitle:
                      "${circle['members_count']} Members • Lvl ${circle['level']}",
                  isMember: false,
                );
              },
              childCount: remaining.length,
            ),
          ),
        ),
      ],
    );
  }
}

// -----------------------------------------------------------------------------
// Podium (Top 3)
// -----------------------------------------------------------------------------
class _PodiumSection extends StatelessWidget {
  final bool isMember;
  final List<Map<String, dynamic>> data;
  const _PodiumSection({required this.isMember, required this.data});

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) return const SizedBox.shrink();

    // Map data to positions (1st, 2nd, 3rd)
    // data is already sorted [0=1st, 1=2nd, 2=3rd]
    final first = data.length > 0 ? data[0] : null;
    final second = data.length > 1 ? data[1] : null;
    final third = data.length > 2 ? data[2] : null;

    return Stack(
      alignment: Alignment.bottomCenter,
      children: [
        // Glow effect
        Positioned(
          bottom: 0,
          child: Container(
            width: 300,
            height: 100,
            decoration: BoxDecoration(
              color: const Color(0xFFFFD700).withOpacity(0.15),
              borderRadius: BorderRadius.circular(100),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFFFFD700).withOpacity(0.2),
                  blurRadius: 50,
                  spreadRadius: 10,
                ),
              ],
            ),
          ),
        ),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            // 2nd Place
            if (second != null)
              _PodiumItem(
                rank: 2,
                name: second['username'] ?? second['name'],
                score: isMember
                    ? "${second['current_exp']} XP"
                    : "${second['members_count']} Memb.",
                height: 140,
                color: const Color(0xFFC0C0C0), // Silver
                icon: isMember ? Icons.person : _getCircleIcon(second['icon']),
              )
            else
              const SizedBox(width: 96),

            // 1st Place
            if (first != null)
              _PodiumItem(
                rank: 1,
                name: first['username'] ?? first['name'],
                score: isMember
                    ? "${first['current_exp']} XP"
                    : "${first['members_count']} Memb.",
                height: 170,
                color: const Color(0xFFFFD700), // Gold
                icon: isMember ? Icons.person : _getCircleIcon(first['icon']),
                isFirst: true,
              ),

            // 3rd Place
            if (third != null)
              _PodiumItem(
                rank: 3,
                name: third['username'] ?? third['name'],
                score: isMember
                    ? "${third['current_exp']} XP"
                    : "${third['members_count']} Memb.",
                height: 120,
                color: const Color(0xFFCD7F32), // Bronze
                icon: isMember ? Icons.person : _getCircleIcon(third['icon']),
              )
            else
              const SizedBox(width: 96),
          ],
        ),
      ],
    );
  }

  IconData _getCircleIcon(String? name) {
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
}

class _PodiumItem extends StatelessWidget {
  final int rank;
  final String name;
  final String score;
  final double height;
  final Color color;
  final IconData icon;
  final bool isFirst;

  const _PodiumItem({
    required this.rank,
    required this.name,
    required this.score,
    required this.height,
    required this.color,
    required this.icon,
    this.isFirst = false,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 8),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          // Avatar/Icon
          Stack(
            alignment: Alignment.topCenter,
            children: [
              Container(
                margin: const EdgeInsets.only(bottom: 12),
                padding: const EdgeInsets.all(3),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: color, width: 2),
                  boxShadow: [
                    BoxShadow(color: color.withOpacity(0.4), blurRadius: 15),
                  ],
                  color: Colors.white,
                ),
                child: CircleAvatar(
                  radius: isFirst ? 32 : 24,
                  backgroundColor: color.withOpacity(0.1),
                  child: Icon(icon,
                      color: PremiumColor.primary, size: isFirst ? 32 : 24),
                ),
              ),
              if (isFirst)
                Positioned(
                  top: -16,
                  child: Icon(Icons.emoji_events_rounded,
                      color: const Color(0xFFFFD700),
                      size: 32,
                      shadows: [
                        BoxShadow(
                            color: Colors.orange.withOpacity(0.5),
                            blurRadius: 10)
                      ]),
                ),
            ],
          ),

          SizedBox(
            width: 80,
            child: Text(
              name,
              style: GoogleFonts.plusJakartaSans(
                fontSize: isFirst ? 14 : 12,
                fontWeight: FontWeight.w800,
                color: PremiumColor.slate800,
              ),
              overflow: TextOverflow.ellipsis,
              textAlign: TextAlign.center,
              maxLines: 1,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            score,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w700,
              color: PremiumColor.slate500,
            ),
          ),
          const SizedBox(height: 12),

          // Podium Box
          Container(
            width: isFirst ? 90 : 80,
            height: height - 60,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  color.withOpacity(0.3),
                  color.withOpacity(0.0),
                ],
              ),
              borderRadius:
                  const BorderRadius.vertical(top: Radius.circular(16)),
              border: Border(top: BorderSide(color: color, width: 4)),
            ),
            alignment: Alignment.topCenter,
            padding: const EdgeInsets.only(top: 12),
            child: Text(
              "$rank",
              style: GoogleFonts.outfit(
                fontSize: 32,
                fontWeight: FontWeight.w900,
                color: color,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// -----------------------------------------------------------------------------
// Rank List Item (4+)
// -----------------------------------------------------------------------------
class _RankListItem extends StatelessWidget {
  final int rank;
  final String name;
  final String subtitle;
  final bool isMember;
  final bool isMe;

  const _RankListItem({
    required this.rank,
    required this.name,
    required this.subtitle,
    required this.isMember,
    this.isMe = false,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: isMe ? PremiumColor.accent.withOpacity(0.05) : Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: isMe ? PremiumColor.accent : Colors.transparent,
          width: 1.5,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          // Rank Number
          SizedBox(
            width: 32,
            child: Text(
              "#$rank",
              style: GoogleFonts.outfit(
                fontSize: 16,
                fontWeight: FontWeight.w900,
                color: PremiumColor.slate400,
              ),
            ),
          ),
          const SizedBox(width: 8),

          // Avatar
          Container(
            padding: const EdgeInsets.all(2),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(
                  color: isMe
                      ? PremiumColor.accent
                      : PremiumColor.slate200.withOpacity(0.5)),
            ),
            child: CircleAvatar(
              radius: 20,
              backgroundColor: const Color(0xFFF1F5F9),
              child: Icon(isMember ? Icons.person_rounded : Icons.group_rounded,
                  color: isMe ? PremiumColor.accent : PremiumColor.slate400,
                  size: 20),
            ),
          ),
          const SizedBox(width: 16),

          // Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: PremiumColor.slate800,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  subtitle,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: PremiumColor.slate500,
                  ),
                ),
              ],
            ),
          ),

          // Tag
          if (isMe)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: PremiumColor.accent,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: PremiumColor.accent.withOpacity(0.4),
                    blurRadius: 8,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Text(
                "YOU",
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                  letterSpacing: 1.0,
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class _SliverAppBarDelegate extends SliverPersistentHeaderDelegate {
  final TabBar _tabBar;

  _SliverAppBarDelegate(this._tabBar);

  @override
  double get minExtent => _tabBar.preferredSize.height;
  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  Widget build(
      BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFF9FAFB),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: _tabBar,
    );
  }

  @override
  bool shouldRebuild(_SliverAppBarDelegate oldDelegate) {
    return false;
  }
}
