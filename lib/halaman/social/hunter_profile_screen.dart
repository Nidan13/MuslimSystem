import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../models/activity_log.dart';
import '../../models/user_profile.dart';
import '../../services/profile_service.dart';
import '../../services/social_service.dart';
import '../../theme/premium_color.dart';
import '../widgets/custom_background.dart';
import '../widgets/character_avatar.dart';
import '../widgets/radar_chart.dart';
import 'follow_list_screen.dart';

class HunterProfileScreen extends StatefulWidget {
  final int userId;
  const HunterProfileScreen({super.key, required this.userId});

  @override
  State<HunterProfileScreen> createState() => _HunterProfileScreenState();
}

class _HunterProfileScreenState extends State<HunterProfileScreen>
    with SingleTickerProviderStateMixin {
  final ProfileService _profileService = ProfileService();
  final SocialService _socialService = SocialService();

  late TabController _tabController;
  UserProfile? _userProfile;
  List<ActivityLog> _activities = [];
  bool _isLoading = true;
  bool _isFollowing = false;
  bool _isProcessingFollow = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _fetchHunterData();
  }

  Future<void> _fetchHunterData() async {
    setState(() => _isLoading = true);
    final data = await _profileService.getHunterProfile(widget.userId);
    if (data != null && mounted) {
      setState(() {
        _userProfile = UserProfile.fromJson(data['user']);
        _isFollowing = data['user']['is_following'] ?? false;
        final List<dynamic> logs = data['activities'] ?? [];
        _activities = logs.map((json) => ActivityLog.fromJson(json)).toList();
        _isLoading = false;
      });
    }
  }

  Future<void> _toggleFollow() async {
    if (_userProfile == null || _isProcessingFollow) return;

    setState(() => _isProcessingFollow = true);
    bool success;
    if (_isFollowing) {
      success = await _socialService.unfollowUser(_userProfile!.id);
    } else {
      success = await _socialService.followUser(_userProfile!.id);
    }

    if (success && mounted) {
      setState(() {
        _isFollowing = !_isFollowing;
        _isProcessingFollow = false;
      });
      // Refresh to update follower counts
      _fetchHunterData();
    } else if (mounted) {
      setState(() => _isProcessingFollow = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: Colors.white,
        body: const Center(
            child: CircularProgressIndicator(color: PremiumColor.primary)),
      );
    }

    if (_userProfile == null) {
      return Scaffold(
        appBar: AppBar(
            elevation: 0,
            backgroundColor: Colors.white,
            foregroundColor: PremiumColor.primary),
        body: Center(child: Text("Hunter tidak ditemukan wok!")),
      );
    }

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
                        fontWeight: FontWeight.w900, fontSize: 13),
                    tabs: const [
                      Tab(text: "OVERVIEW"),
                      Tab(text: "ACTIVITY"),
                    ],
                  ),
                ),
              ),
            ],
            body: TabBarView(
              controller: _tabController,
              children: [
                _buildOverviewTab(),
                _buildActivityTab(),
              ],
            ),
          ),
          // Back Button
          Positioned(
            top: 50,
            left: 20,
            child: _CircleBackButton(onTap: () => Navigator.pop(context)),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 300,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: Container(
          decoration: BoxDecoration(
            color: PremiumColor.primary,
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                PremiumColor.primary,
                PremiumColor.primary.withOpacity(0.8)
              ],
            ),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const SizedBox(height: 40),
              RubElHizbAvatar(
                imageUrl: _userProfile!.avatar,
                size: 100,
                level: "LVL ${_userProfile!.level}",
                hpProgress: _userProfile!.hp.progress / 100,
                gender: _userProfile!.gender,
              ),
              const SizedBox(height: 16),
              Text(
                _userProfile!.username,
                style: GoogleFonts.plusJakartaSans(
                  color: Colors.white,
                  fontSize: 24,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 20),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  _buildFollowStat(
                      "${_userProfile!.followersCount}", "Followers", () {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (context) => FollowListScreen(
                                userId: _userProfile!.id,
                                type: 'followers',
                                title: _userProfile!.username)));
                  }),
                  const SizedBox(width: 32),
                  _buildFollowStat(
                      "${_userProfile!.followingCount}", "Following", () {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (context) => FollowListScreen(
                                userId: _userProfile!.id,
                                type: 'following',
                                title: _userProfile!.username)));
                  }),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildFollowStat(String count, String label, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Column(
        children: [
          Text(
            count,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.w900,
            ),
          ),
          Text(
            label,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.white.withOpacity(0.6),
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOverviewTab() {
    final int totalSholat = _userProfile!.stats.attributes?.strength ?? 0;
    final int totalNgaji = _userProfile!.stats.totalQuranSessions;
    final int totalQuest = _userProfile!.stats.attributes?.wisdom ?? 0;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildFollowButton(),
          const SizedBox(height: 24),
          Text(
            "STATISTIK HUNTER",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              fontWeight: FontWeight.w900,
              color: PremiumColor.primary.withOpacity(0.4),
              letterSpacing: 2,
            ),
          ),
          const SizedBox(height: 16),
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            childAspectRatio: 2.2,
            mainAxisSpacing: 12,
            crossAxisSpacing: 12,
            children: [
              _buildStatCard("Total Sholat", "$totalSholat Selesai",
                  Icons.favorite_outline_rounded, Colors.redAccent),
              _buildStatCard("Total Ngaji", "$totalNgaji Sesi",
                  Icons.menu_book_rounded, Colors.blueAccent),
              _buildStatCard("Total Misi", "$totalQuest Selesai",
                  Icons.task_alt_rounded, Colors.orangeAccent),
              _buildStatCard("Streak", "${_userProfile!.stats.streak} Hari",
                  Icons.local_fire_department_rounded, Colors.teal),
            ],
          ),
          const SizedBox(height: 24),
          _buildRadarSection(),
        ],
      ),
    );
  }

  Widget _buildFollowButton() {
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: _isProcessingFollow ? null : _toggleFollow,
        style: ElevatedButton.styleFrom(
          backgroundColor: _isFollowing ? Colors.white : PremiumColor.primary,
          foregroundColor: _isFollowing ? PremiumColor.primary : Colors.white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
            side: _isFollowing
                ? BorderSide(color: PremiumColor.primary.withOpacity(0.2))
                : BorderSide.none,
          ),
        ),
        child: _isProcessingFollow
            ? const SizedBox(
                width: 20,
                height: 20,
                child: CircularProgressIndicator(strokeWidth: 2))
            : Text(
                _isFollowing ? " FOLLOWING" : "FOLLOW HUNTER",
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w900, letterSpacing: 1),
              ),
      ),
    );
  }

  Widget _buildStatCard(
      String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 15,
            spreadRadius: -2,
            offset: const Offset(0, 4),
          ),
          BoxShadow(
            color: color.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  color.withOpacity(0.15),
                  color.withOpacity(0.05),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: color.withOpacity(0.1)),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 14),
          Flexible(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(value,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 15,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.3,
                        color: PremiumColor.slate800),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis),
                const SizedBox(height: 2),
                Text(label,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: PremiumColor.slate500,
                        letterSpacing: 0.5),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRadarSection() {
    final attrs = _userProfile!.stats.attributes;
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
      ),
      child: Column(
        children: [
          SizedBox(
            height: 220,
            child: RadarChart(
              values: {
                'SHOLAT': (attrs?.strength ?? 0).toDouble() / 100,
                'ILMU': (attrs?.intelligence ?? 0).toDouble() / 100,
                'ADAB': (attrs?.wisdom ?? 0).toDouble() / 100,
                'ISTIQOMAH': (attrs?.vitality ?? 0).toDouble() / 100,
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActivityTab() {
    if (_activities.isEmpty) {
      return Center(child: Text("Belum ada riwayat pengerjaan wok!"));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(24),
      itemCount: _activities.length,
      itemBuilder: (context, index) {
        final log = _activities[index];
        return _ActivityItem(log: log);
      },
    );
  }
}

class _ActivityItem extends StatelessWidget {
  final ActivityLog log;
  const _ActivityItem({required this.log});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: PremiumColor.primary.withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.check_circle_rounded,
                color: PremiumColor.primary, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(log.description,
                    style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w700, fontSize: 13)),
                Text(DateFormat('dd MMM yyyy, HH:mm').format(log.createdAt),
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 11, color: Colors.grey)),
              ],
            ),
          ),
          if (log.amount > 0)
            Text("+${log.amount} XP",
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w900,
                    color: PremiumColor.accent,
                    fontSize: 12)),
        ],
      ),
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
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.2),
            shape: BoxShape.circle,
            border: Border.all(color: Colors.white.withOpacity(0.3))),
        child:
            const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 22),
      ),
    );
  }
}

class _SliverAppBarDelegate extends SliverPersistentHeaderDelegate {
  _SliverAppBarDelegate(this._tabBar);
  final TabBar _tabBar;

  @override
  double get minExtent => _tabBar.preferredSize.height;
  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  Widget build(
      BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(color: Colors.white, child: _tabBar);
  }

  @override
  bool shouldRebuild(_SliverAppBarDelegate oldDelegate) => false;
}
