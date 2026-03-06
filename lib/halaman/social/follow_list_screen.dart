import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../services/social_service.dart';
import '../../theme/premium_color.dart';
import 'hunter_profile_screen.dart';

class FollowListScreen extends StatefulWidget {
  final int userId;
  final String type; // 'followers' or 'following' (used for initial tab)
  final String title;

  const FollowListScreen({
    super.key,
    required this.userId,
    required this.type,
    required this.title,
  });

  @override
  State<FollowListScreen> createState() => _FollowListScreenState();
}

class _FollowListScreenState extends State<FollowListScreen>
    with SingleTickerProviderStateMixin {
  final SocialService _socialService = SocialService();
  late TabController _tabController;

  List<Map<String, dynamic>> _followers = [];
  List<Map<String, dynamic>> _following = [];

  List<Map<String, dynamic>> _filteredFollowers = [];
  List<Map<String, dynamic>> _filteredFollowing = [];

  bool _isLoading = true;
  String _searchQuery = "";

  @override
  void initState() {
    super.initState();
    _tabController = TabController(
      length: 2,
      vsync: this,
      initialIndex: widget.type == 'followers' ? 0 : 1,
    );
    _tabController.addListener(() {
      setState(() {
        _searchQuery = "";
        _filteredFollowers = List.from(_followers);
        _filteredFollowing = List.from(_following);
      });
    });
    _fetchAllData();
  }

  Future<void> _fetchAllData() async {
    setState(() => _isLoading = true);
    final futures = await Future.wait([
      _socialService.getFollowers(widget.userId),
      _socialService.getFollowing(widget.userId),
    ]);

    if (mounted) {
      setState(() {
        _followers = futures[0];
        _following = futures[1];
        _filteredFollowers = List.from(_followers);
        _filteredFollowing = List.from(_following);
        _isLoading = false;
      });
    }
  }

  void _onSearch(String query) {
    setState(() {
      _searchQuery = query.toLowerCase();
      if (_tabController.index == 0) {
        _filteredFollowers = _followers.where((user) {
          return user['username']
              .toString()
              .toLowerCase()
              .contains(_searchQuery);
        }).toList();
      } else {
        _filteredFollowing = _following.where((user) {
          return user['username']
              .toString()
              .toLowerCase()
              .contains(_searchQuery);
        }).toList();
      }
    });
  }

  void _toggleFollowUser(
      Map<String, dynamic> user, bool isFollowingList) async {
    final bool currentlyFollowing = user['is_following'] ?? false;
    final int targetUserId = user['id'];

    // Optimistic UI update
    setState(() {
      user['is_following'] = !currentlyFollowing;
    });

    bool success;
    if (currentlyFollowing) {
      success = await _socialService.unfollowUser(targetUserId);
    } else {
      success = await _socialService.followUser(targetUserId);
    }

    if (!success && mounted) {
      // Revert on failure
      setState(() {
        user['is_following'] = currentlyFollowing;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Gagal mengubah status ikuti.')),
      );
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
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          widget.title,
          style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w800,
            fontSize: 16,
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        foregroundColor: Colors.black, // IG style
        bottom: TabBar(
          controller: _tabController,
          labelColor: Colors.black,
          unselectedLabelColor: Colors.grey,
          indicatorColor: Colors.black,
          indicatorWeight: 1,
          labelStyle: GoogleFonts.plusJakartaSans(
              fontWeight: FontWeight.w700, fontSize: 13),
          tabs: [
            Tab(text: "${_followers.length} Pengikut"),
            Tab(text: "${_following.length} Mengikuti"),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: PremiumColor.primary))
          : Column(
              children: [
                // Search Bar IG Style
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Container(
                    height: 36,
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: TextField(
                      onChanged: _onSearch,
                      decoration: InputDecoration(
                        hintText: "Cari",
                        hintStyle:
                            TextStyle(color: Colors.grey[600], fontSize: 14),
                        prefixIcon: Icon(Icons.search,
                            color: Colors.grey[600], size: 20),
                        border: InputBorder.none,
                        contentPadding:
                            const EdgeInsets.symmetric(vertical: 10),
                      ),
                    ),
                  ),
                ),
                Expanded(
                  child: TabBarView(
                    controller: _tabController,
                    children: [
                      // Followers Tab
                      _buildUserList(_filteredFollowers, false),
                      // Following Tab
                      _buildUserList(_filteredFollowing, true),
                    ],
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildUserList(List<Map<String, dynamic>> users, bool isFollowingTab) {
    if (users.isEmpty) {
      return Center(
        child: Text(
          _searchQuery.isNotEmpty
              ? "Tidak ada hasil."
              : "Belum ada hunter di sini.",
          style: GoogleFonts.plusJakartaSans(color: Colors.grey[500]),
        ),
      );
    }

    return ListView.builder(
      itemCount: users.length,
      padding: EdgeInsets.zero,
      itemBuilder: (context, index) {
        final user = users[index];
        final bool isFollowing = user['is_following'] ?? false;

        return InkWell(
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => HunterProfileScreen(userId: user['id']),
              ),
            ).then((_) {
              // Refresh on return to get updated follow status
              _fetchAllData();
            });
          },
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Row(
              children: [
                // Avatar
                Container(
                  width: 50,
                  height: 50,
                  decoration: BoxDecoration(
                    color: Colors.grey[200],
                    shape: BoxShape.circle,
                  ),
                  child: ClipOval(
                    child: user['avatar'] != null &&
                            user['avatar'].toString().isNotEmpty
                        ? Image.network(user['avatar'],
                            fit: BoxFit.cover,
                            errorBuilder: (c, e, s) =>
                                _buildInitials(user['username']))
                        : _buildInitials(user['username']),
                  ),
                ),
                const SizedBox(width: 12),

                // Username and info
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        user['username'],
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w700,
                          fontSize: 14,
                          color: Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        "Lvl ${user['level']}",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                ),

                // IG Style Button
                GestureDetector(
                  onTap: () => _toggleFollowUser(user, isFollowingTab),
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    decoration: BoxDecoration(
                      color:
                          isFollowing ? Colors.grey[200] : PremiumColor.primary,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      isFollowing ? "Mengikuti" : "Ikuti",
                      style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        color: isFollowing ? Colors.black87 : Colors.white,
                      ),
                    ),
                  ),
                ),

                // Optional Close icon logic for your Followers only if it's the current user profile,
                // but for now, we leave IG structure. We can add a more button if needed.
                const SizedBox(width: 10),
                if (!isFollowingTab)
                  Icon(Icons.more_vert, color: Colors.grey[400], size: 20),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildInitials(String username) {
    return Center(
      child: Text(
        username.isNotEmpty ? username[0].toUpperCase() : '?',
        style: TextStyle(
          fontWeight: FontWeight.bold,
          color: Colors.grey[600],
          fontSize: 18,
        ),
      ),
    );
  }
}
