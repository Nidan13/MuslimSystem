import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/social_service.dart';
import 'widgets/custom_background.dart';
import 'social/hunter_profile_screen.dart';

class SearchFriendScreen extends StatefulWidget {
  const SearchFriendScreen({super.key});

  @override
  State<SearchFriendScreen> createState() => _SearchFriendScreenState();
}

class _SearchFriendScreenState extends State<SearchFriendScreen> {
  final SocialService _socialService = SocialService();
  final TextEditingController _searchController = TextEditingController();
  List<Map<String, dynamic>> _results = [];
  bool _isLoading = false;

  void _onSearch(String value) async {
    if (value.isEmpty) {
      setState(() => _results = []);
      return;
    }

    setState(() => _isLoading = true);
    final users = await _socialService.searchUsers(value);
    if (mounted) {
      setState(() {
        _results = users;
        _isLoading = false;
      });
    }
  }

  void _toggleFollow(Map<String, dynamic> user) async {
    final bool isFollowing = user['is_following'] ?? false;
    final int userId = user['id'];

    bool success;
    if (isFollowing) {
      success = await _socialService.unfollowUser(userId);
    } else {
      success = await _socialService.followUser(userId);
    }

    if (success && mounted) {
      setState(() {
        user['is_following'] = !isFollowing;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          const MuqarnasHeaderBackground(height: 250),
          SafeArea(
            child: Column(
              children: [
                // Header
                Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                  child: Row(
                    children: [
                      IconButton(
                        onPressed: () => Navigator.pop(context),
                        icon: const Icon(Icons.arrow_back_ios_new_rounded,
                            color: PremiumColor.primary),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        "CARI TEMAN",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 18,
                          fontWeight: FontWeight.w800,
                          color: PremiumColor.primary,
                          letterSpacing: 2,
                        ),
                      ),
                    ],
                  ),
                ),

                // Search Bar
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.05),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: TextField(
                      controller: _searchController,
                      onChanged: _onSearch,
                      decoration: InputDecoration(
                        hintText: "Cari username...",
                        hintStyle: GoogleFonts.plusJakartaSans(
                            color: PremiumColor.slate400),
                        border: InputBorder.none,
                        icon: const Icon(Icons.search_rounded,
                            color: PremiumColor.primary),
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 24),

                // Results
                Expanded(
                  child: _isLoading
                      ? const Center(
                          child: CircularProgressIndicator(
                              color: PremiumColor.primary))
                      : _results.isEmpty
                          ? Center(
                              child: Text(
                                _searchController.text.isEmpty
                                    ? "Ketuk untuk mencari teman"
                                    : "Tidak ada teman ditemukan",
                                style: GoogleFonts.plusJakartaSans(
                                    color: PremiumColor.slate400),
                              ),
                            )
                          : ListView.builder(
                              padding:
                                  const EdgeInsets.symmetric(horizontal: 24),
                              itemCount: _results.length,
                              itemBuilder: (context, index) {
                                final user = _results[index];
                                final isFollowing =
                                    user['is_following'] ?? false;

                                return GestureDetector(
                                  onTap: () {
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute(
                                        builder: (context) =>
                                            HunterProfileScreen(
                                                userId: user['id']),
                                      ),
                                    );
                                  },
                                  child: Container(
                                    margin: const EdgeInsets.only(bottom: 12),
                                    padding: const EdgeInsets.all(16),
                                    decoration: BoxDecoration(
                                      color: Colors.white,
                                      borderRadius: BorderRadius.circular(20),
                                      border: Border.all(
                                          color:
                                              Colors.black.withOpacity(0.05)),
                                    ),
                                    child: Row(
                                      children: [
                                        CircleAvatar(
                                          backgroundColor: PremiumColor.primary
                                              .withOpacity(0.1),
                                          child: Text(
                                            user['username'][0].toUpperCase(),
                                            style: const TextStyle(
                                                color: PremiumColor.primary,
                                                fontWeight: FontWeight.bold),
                                          ),
                                        ),
                                        const SizedBox(width: 16),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                user['username'],
                                                style:
                                                    GoogleFonts.plusJakartaSans(
                                                  fontSize: 16,
                                                  fontWeight: FontWeight.w700,
                                                  color: PremiumColor.slate800,
                                                ),
                                              ),
                                              Text(
                                                "Lvl ${user['level']} • ${user['job_class'] ?? 'Hunter'}",
                                                style:
                                                    GoogleFonts.plusJakartaSans(
                                                  fontSize: 12,
                                                  color: PremiumColor.slate500,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        ElevatedButton(
                                          onPressed: () => _toggleFollow(user),
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: isFollowing
                                                ? Colors.white
                                                : PremiumColor.primary,
                                            foregroundColor: isFollowing
                                                ? PremiumColor.primary
                                                : Colors.white,
                                            elevation: 0,
                                            side: isFollowing
                                                ? const BorderSide(
                                                    color: PremiumColor.primary)
                                                : null,
                                            shape: RoundedRectangleBorder(
                                                borderRadius:
                                                    BorderRadius.circular(12)),
                                            padding: const EdgeInsets.symmetric(
                                                horizontal: 16),
                                          ),
                                          child: Text(
                                            isFollowing ? "Unfollow" : "Follow",
                                            style: GoogleFonts.plusJakartaSans(
                                                fontWeight: FontWeight.w800,
                                                fontSize: 12),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                );
                              },
                            ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
