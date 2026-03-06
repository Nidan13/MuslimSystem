import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'widgets/custom_background.dart';
import '../models/circle.dart';
import '../services/circle_service.dart';

// Sorting Enum
enum CircleSortOption { descending, ascending, random }

class AllCirclesScreen extends StatefulWidget {
  const AllCirclesScreen({super.key});

  @override
  State<AllCirclesScreen> createState() => _AllCirclesScreenState();
}

class _AllCirclesScreenState extends State<AllCirclesScreen> {
  final CircleService _circleService = CircleService();
  List<Circle> _allCircles = [];
  List<Circle> _displayedCircles = [];
  CircleSortOption _sortOption = CircleSortOption.descending;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchCircles();
  }

  Future<void> _fetchCircles() async {
    setState(() => _isLoading = true);
    final circles = await _circleService.getCircles();
    setState(() {
      _allCircles = circles;
      _displayedCircles = List.from(_allCircles);
      _isLoading = false;
      _sortCircles();
    });
  }

  void _sortCircles() {
    setState(() {
      switch (_sortOption) {
        case CircleSortOption.descending:
          _displayedCircles
              .sort((a, b) => b.membersCount.compareTo(a.membersCount));
          break;
        case CircleSortOption.ascending:
          _displayedCircles
              .sort((a, b) => a.membersCount.compareTo(b.membersCount));
          break;
        case CircleSortOption.random:
          _displayedCircles.shuffle();
          break;
      }
    });
  }

  void _onSortChanged(CircleSortOption? newValue) {
    if (newValue != null) {
      setState(() {
        _sortOption = newValue;
        _sortCircles();
      });
    }
  }

  Future<void> _toggleJoin(Circle circle) async {
    if (circle.isJoined) {
      final res = await _circleService.leaveCircle(circle.id);
      if (res['success'] == true) {
        _fetchCircles();
      }
    } else {
      final res = await _circleService.joinCircle(circle.id);
      if (res['success'] == true) {
        _fetchCircles();
      }
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

  Color _getColor(String? hex) {
    if (hex == null || hex.isEmpty) return PremiumColor.primary;
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (e) {
      return PremiumColor.primary;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),

          // Header Background (Shrunken as requested: 160 -> 120)
          const Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: 120,
            child: PremiumHeaderBackground(height: 120),
          ),

          SafeArea(
            child: Column(
              children: [
                // Custom App Bar
                Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Row(
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          shape: BoxShape.circle,
                          boxShadow: [
                            BoxShadow(
                                color: Colors.black.withOpacity(0.1),
                                blurRadius: 8),
                          ],
                        ),
                        child: IconButton(
                          icon: const Icon(Icons.arrow_back_rounded,
                              color: PremiumColor.primary),
                          onPressed: () => Navigator.pop(context),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Text(
                        "All Circles",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 20,
                          fontWeight: FontWeight.w800,
                          color: PremiumColor
                              .primary, // Darker text on light-ish background
                        ),
                      ),
                    ],
                  ),
                ),

                // Filter & Sort Bar
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        "${_displayedCircles.length} Circles Found",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: PremiumColor.slate500,
                        ),
                      ),
                      // Dropdown for Sort
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 12, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                              color: PremiumColor.primary.withOpacity(0.1)),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<CircleSortOption>(
                            value: _sortOption,
                            icon: const Icon(Icons.sort_rounded,
                                color: PremiumColor.primary, size: 20),
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                              color: PremiumColor.primary,
                            ),
                            items: const [
                              DropdownMenuItem(
                                value: CircleSortOption.descending,
                                child: Text("Most Members"),
                              ),
                              DropdownMenuItem(
                                value: CircleSortOption.ascending,
                                child: Text("Fewest Members"),
                              ),
                              DropdownMenuItem(
                                value: CircleSortOption.random,
                                child: Text("Random Shuffle"),
                              ),
                            ],
                            onChanged: _onSortChanged,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                // List
                Expanded(
                  child: _isLoading
                      ? const Center(
                          child: CircularProgressIndicator(
                            color: PremiumColor.primary,
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 20, vertical: 10),
                          itemCount: _displayedCircles.length,
                          itemBuilder: (context, index) {
                            final circle = _displayedCircles[index];
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 16),
                              child: _CircleListItem(
                                data: circle,
                                icon: _getIconData(circle.iconName),
                                color: _getColor(circle.colorHex),
                                onToggle: () => _toggleJoin(circle),
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

class _CircleListItem extends StatelessWidget {
  final Circle data;
  final IconData icon;
  final Color color;
  final VoidCallback onToggle;

  const _CircleListItem({
    required this.data,
    required this.icon,
    required this.color,
    required this.onToggle,
  });

  @override
  Widget build(BuildContext context) {
    final bool isLightColor = color.computeLuminance() > 0.7;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
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
          // Icon Box
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: isLightColor ? const Color(0xFFF1F5F9) : color,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(icon,
                color: isLightColor ? PremiumColor.primary : Colors.white,
                size: 30),
          ),
          const SizedBox(width: 16),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text(
                      data.name,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: PremiumColor.slate800,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: PremiumColor.accent.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        "LVL ${data.level}",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          fontWeight: FontWeight.w800,
                          color: PremiumColor.accent,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  data.description,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    color: PremiumColor.slate500,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.people_rounded,
                        size: 14, color: PremiumColor.slate400),
                    const SizedBox(width: 4),
                    Text(
                      "${data.membersCount >= 1000 ? (data.membersCount / 1000).toStringAsFixed(1) + 'k' : data.membersCount} members",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: PremiumColor.slate500,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Join Button Icon
          GestureDetector(
            onTap: onToggle,
            child: Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: data.isJoined
                    ? PremiumColor.primary
                    : PremiumColor.primary.withOpacity(0.05),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                data.isJoined ? Icons.check_rounded : Icons.add_rounded,
                color: data.isJoined ? Colors.white : PremiumColor.primary,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
