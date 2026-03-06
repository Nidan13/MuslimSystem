import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter/services.dart';
import 'widgets/custom_background.dart';
import 'all_circles_screen.dart';
import '../models/circle.dart';
import '../services/circle_service.dart';
import '../services/profile_service.dart';
import '../models/user_profile.dart';
import '../theme/premium_color.dart';
import 'circle_detail_screen.dart';

class CircleScreen extends StatefulWidget {
  final bool shouldRefresh;
  const CircleScreen({super.key, this.shouldRefresh = false});

  @override
  State<CircleScreen> createState() => _CircleScreenState();
}

class _CircleScreenState extends State<CircleScreen> {
  final CircleService _circleService = CircleService();
  final ProfileService _profileService = ProfileService();
  List<Circle> _featuredCircles = [];
  List<Circle> _myCircles = [];
  UserProfile? _userProfile;
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  @override
  void didUpdateWidget(CircleScreen oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.shouldRefresh && !oldWidget.shouldRefresh) {
      _fetchData();
    }
  }

  Future<void> _fetchData() async {
    if (!mounted) return;
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });
    try {
      final results = await Future.wait([
        _circleService.getCircles().catchError((e) {
          debugPrint("Error fetching all circles: $e");
          return <Circle>[];
        }),
        _circleService.getMyCircles().catchError((e) {
          debugPrint("Error fetching my circles: $e");
          return <Circle>[];
        }),
        _profileService.getProfile().catchError((e) {
          debugPrint("Error fetching profile: $e");
          return <UserProfile?>[null].first;
        }),
      ]);

      if (mounted) {
        setState(() {
          _featuredCircles = results[0] as List<Circle>;
          _myCircles = results[1] as List<Circle>;
          _userProfile = results[2] as UserProfile?;
          _isLoading = false;

          if (_featuredCircles.isEmpty &&
              _myCircles.isEmpty &&
              _userProfile == null) {
            _errorMessage = "Gagal memuat data. Cek koneksi lo wok!";
          }
        });
      }
    } catch (e) {
      debugPrint("Global error in _fetchData: $e");
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage = "Terjadi kesalahan sistem. Coba lagi ya!";
        });
      }
    }
  }

  void _showCreateModal() async {
    if (_userProfile == null) return;

    final result = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => CreateCircleModal(userProfile: _userProfile!),
    );

    if (result == true) {
      _fetchData();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      floatingActionButton: FloatingActionButton(
        onPressed: _showCreateModal,
        backgroundColor: PremiumColor.primary,
        foregroundColor: Colors.white,
        elevation: 6,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: const Icon(Icons.add_rounded, size: 32),
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              _buildSliverHeader(),
              _isLoading
                  ? const SliverFillRemaining(
                      child: Center(
                        child: CircularProgressIndicator(
                          color: PremiumColor.primary,
                        ),
                      ),
                    )
                  : _errorMessage != null
                      ? SliverFillRemaining(
                          child: Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                const Icon(
                                  Icons.cloud_off_rounded,
                                  size: 64,
                                  color: PremiumColor.primary,
                                ),
                                const SizedBox(height: 16),
                                Text(
                                  _errorMessage!,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w600,
                                    color: PremiumColor.primary,
                                  ),
                                ),
                                const SizedBox(height: 24),
                                ElevatedButton(
                                  onPressed: _fetchData,
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: PremiumColor.primary,
                                    foregroundColor: Colors.white,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                  ),
                                  child: const Text("Coba Lagi"),
                                ),
                              ],
                            ),
                          ),
                        )
                      : SliverToBoxAdapter(child: _buildBody()),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 280,
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
                colors: [Color(0xFF0F172A), PremiumColor.primary],
              ),
              image: DecorationImage(
                image: NetworkImage(
                  "https://www.transparenttextures.com/patterns/arabesque.png",
                ),
                opacity: 0.05,
                repeat: ImageRepeat.repeat,
              ),
            ),
            child: Stack(
              children: [
                const Positioned.fill(child: IslamicPatternBackground()),
                Positioned(
                  top: -60,
                  right: -60,
                  child: Container(
                    width: 250,
                    height: 250,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: PremiumColor.highlight.withOpacity(0.1),
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.fromLTRB(26, 0, 26, 40),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: Colors.white.withOpacity(0.1),
                          ),
                        ),
                        child: const Icon(
                          Icons.hub_rounded,
                          color: PremiumColor.highlight,
                          size: 32,
                        ),
                      ),
                      const SizedBox(height: 20),
                      Text(
                        "COMMUNITY NETWORK",
                        style: GoogleFonts.plusJakartaSans(
                          color: PremiumColor.highlight,
                          fontSize: 11,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 3.0,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        "Circle Hub",
                        style: GoogleFonts.playfairDisplay(
                          color: Colors.white,
                          fontSize: 40,
                          fontWeight: FontWeight.w900,
                          height: 1.1,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        "Bangun ekosistem kebaikan bersama para Hunter",
                        style: GoogleFonts.plusJakartaSans(
                          color: Colors.white.withOpacity(0.6),
                          fontSize: 14,
                          fontWeight: FontWeight.w500,
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

  Widget _buildBody() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 24, 24, 120),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _SectionTitle(
            title: "Featured Circles",
            action: "View All",
            icon: Icons.auto_awesome_rounded,
            onAction: () async {
              await Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const AllCirclesScreen(),
                ),
              );
              _fetchData();
            },
          ),
          const SizedBox(height: 16),
          _FeaturedCirclesList(circles: _featuredCircles),
          const SizedBox(height: 40),
          _SectionTitle(
            title: "My Circles",
            badge: "${_myCircles.length} Joined",
            icon: Icons.shield_rounded,
          ),
          const SizedBox(height: 16),
          _MyCirclesList(circles: _myCircles),
        ],
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String title;
  final String? action;
  final VoidCallback? onAction;
  final String? badge;
  final IconData icon;

  const _SectionTitle({
    required this.title,
    this.action,
    this.onAction,
    this.badge,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: PremiumColor.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(icon, size: 16, color: PremiumColor.primary),
            ),
            const SizedBox(width: 10),
            Text(
              title,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: PremiumColor.slate800,
              ),
            ),
            if (badge != null) ...[
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: PremiumColor.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  badge!,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: PremiumColor.primary,
                  ),
                ),
              ),
            ],
          ],
        ),
        if (action != null)
          InkWell(
            onTap: onAction,
            child: Row(
              children: [
                Text(
                  action!,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: PremiumColor.primary,
                  ),
                ),
                const SizedBox(width: 4),
                const Icon(
                  Icons.arrow_forward_rounded,
                  size: 14,
                  color: PremiumColor.primary,
                ),
              ],
            ),
          ),
      ],
    );
  }
}

class _FeaturedCirclesList extends StatelessWidget {
  final List<Circle> circles;
  const _FeaturedCirclesList({required this.circles});

  @override
  Widget build(BuildContext context) {
    if (circles.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.grey.withOpacity(0.1)),
        ),
        child: Column(
          children: [
            Icon(
              Icons.dashboard_customize_rounded,
              size: 40,
              color: Colors.grey[300],
            ),
            const SizedBox(height: 8),
            Text(
              "No featured circles yet",
              style: GoogleFonts.plusJakartaSans(
                color: Colors.grey[400],
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      );
    }
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      clipBehavior: Clip.none,
      child: Row(
        children: circles.map((circle) {
          return Padding(
            padding: const EdgeInsets.only(right: 16),
            child: _FeaturedCard(circle: circle),
          );
        }).toList(),
      ),
    );
  }
}

class _FeaturedCard extends StatelessWidget {
  final Circle circle;
  const _FeaturedCard({required this.circle});

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
        return Icons.mosque_rounded;
    }
  }

  Color _getColor(String? hex) {
    if (hex == null || hex.isEmpty) return Colors.teal;
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (e) {
      return Colors.teal;
    }
  }

  @override
  Widget build(BuildContext context) {
    final color = _getColor(circle.colorHex);
    final icon = _getIconData(circle.iconName);

    return GestureDetector(
      onTap: () {
        HapticFeedback.mediumImpact();
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => CircleDetailScreen(
              circleId: circle.id,
              initialName: circle.name,
            ),
          ),
        );
      },
      child: Container(
        width: 220,
        height:
            260, // FIX: Need fixed height for Spacer() inside Column inside Horizontal ScrollView
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(32),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.08),
              blurRadius: 30,
              offset: const Offset(0, 15),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(32),
          child: Stack(
            children: [
              Positioned(
                top: -20,
                right: -20,
                child: Container(
                  width: 100,
                  height: 100,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [color.withOpacity(0.1), color.withOpacity(0)],
                    ),
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [
                            color.withOpacity(0.2),
                            color.withOpacity(0.05),
                          ],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Icon(icon, color: color, size: 28),
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        "LVL ${circle.level}",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 9,
                          fontWeight: FontWeight.w900,
                          color: color,
                          letterSpacing: 1,
                        ),
                      ),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      circle.name,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 18,
                        fontWeight: FontWeight.w900,
                        color: PremiumColor.slate800,
                        letterSpacing: -0.5,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.people_alt_rounded,
                          size: 12,
                          color: PremiumColor.slate400,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          "${circle.membersCount} Members",
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
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
        ),
      ),
    );
  }
}

class _MyCirclesList extends StatelessWidget {
  final List<Circle> circles;
  const _MyCirclesList({required this.circles});

  @override
  Widget build(BuildContext context) {
    if (circles.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(32),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: Colors.grey.withOpacity(0.1)),
        ),
        child: Column(
          children: [
            Icon(
              Icons.diversity_3_rounded,
              size: 48,
              color: PremiumColor.primary.withOpacity(0.2),
            ),
            const SizedBox(height: 16),
            Text(
              "You haven't joined any circles yet",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: PremiumColor.slate500,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            OutlinedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const AllCirclesScreen(),
                  ),
                );
              },
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: PremiumColor.primary),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: Text(
                "Find a Circle",
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700,
                  color: PremiumColor.primary,
                ),
              ),
            ),
          ],
        ),
      );
    }
    return Column(
      children: circles.map((circle) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 12),
          child: GestureDetector(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => CircleDetailScreen(
                    circleId: circle.id,
                    initialName: circle.name,
                  ),
                ),
              );
            },
            child: _MyCircleCard(circle: circle),
          ),
        );
      }).toList(),
    );
  }
}

class _MyCircleCard extends StatelessWidget {
  final Circle circle;
  const _MyCircleCard({required this.circle});

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
    final color = _getColor(circle.colorHex);
    final icon = _getIconData(circle.iconName);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [color.withOpacity(0.12), color.withOpacity(0.04)],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: color.withOpacity(0.1)),
            ),
            child: Icon(icon, color: color, size: 28),
          ),
          const SizedBox(width: 18),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  circle.name,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 17,
                    fontWeight: FontWeight.w800,
                    color: PremiumColor.slate800,
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 6,
                        vertical: 2,
                      ),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        "LVL ${circle.level}",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 9,
                          fontWeight: FontWeight.w900,
                          color: color,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Container(
                      width: 3,
                      height: 3,
                      decoration: const BoxDecoration(
                        color: Color(0xFFE2E8F0),
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      "${circle.membersCount} Members",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: PremiumColor.slate400,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: PremiumColor.primary.withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.arrow_forward_ios_rounded,
              size: 14,
              color: PremiumColor.primary,
            ),
          ),
        ],
      ),
    );
  }
}

class CreateCircleModal extends StatefulWidget {
  final UserProfile userProfile;
  const CreateCircleModal({super.key, required this.userProfile});

  @override
  State<CreateCircleModal> createState() => _CreateCircleModalState();
}

class _CreateCircleModalState extends State<CreateCircleModal> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _descController = TextEditingController();
  final CircleService _circleService = CircleService();

  String _selectedIcon = 'mosque';
  String _selectedColor = '#0E5E6F';
  bool _isLoading = false;

  final List<String> _icons = [
    'mosque',
    'sunny',
    'night',
    'charity',
    'quran',
    'community',
    'study',
    'fasting',
  ];
  final List<String> _colors = [
    '#0E5E6F',
    '#D4AF37',
    '#10B981',
    '#3B82F6',
    '#EF4444',
    '#8B5CF6',
    '#F59E0B',
    '#EC4899',
  ];

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);
    final data = {
      'name': _nameController.text,
      'description': _descController.text,
      'icon': _selectedIcon,
      'color': _selectedColor,
    };
    final result = await _circleService.createCircle(data);
    setState(() => _isLoading = false);
    if (mounted) {
      if (result['success'] == true) {
        Navigator.pop(context, true);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Circle created successfully!")),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? "Failed to create circle"),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  IconData _getIconData(String name) {
    switch (name) {
      case 'sunny':
        return Icons.wb_sunny_rounded;
      case 'night':
        return Icons.nights_stay_rounded;
      case 'charity':
        return Icons.volunteer_activism_rounded;
      case 'quran':
        return Icons.menu_book_rounded;
      case 'community':
        return Icons.handshake_rounded;
      case 'study':
        return Icons.library_books_rounded;
      case 'fasting':
        return Icons.restaurant_menu_rounded;
      default:
        return Icons.mosque_rounded;
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
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
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
              const SizedBox(height: 16),
              Text(
                "Create New Circle",
                style: GoogleFonts.playfairDisplay(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: PremiumColor.primary,
                ),
              ),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 6,
                ),
                decoration: BoxDecoration(
                  color: Colors.redAccent.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(
                      Icons.bolt_rounded,
                      color: Colors.redAccent,
                      size: 16,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      "Biaya: 1000 EXP (Bisa potong Level)",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: Colors.redAccent,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              TextFormField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: "Circle Name",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  filled: true,
                  fillColor: Colors.grey[50],
                ),
                validator: (value) => (value == null || value.isEmpty)
                    ? "Please enter a name"
                    : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _descController,
                decoration: InputDecoration(
                  labelText: "Description",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  filled: true,
                  fillColor: Colors.grey[50],
                ),
                maxLines: 2,
              ),
              const SizedBox(height: 24),
              Text(
                "Choose Icon",
                style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 12),
              SizedBox(
                height: 60,
                child: ListView.separated(
                  scrollDirection: Axis.horizontal,
                  itemCount: _icons.length,
                  separatorBuilder: (context, index) =>
                      const SizedBox(width: 12),
                  itemBuilder: (context, index) {
                    final iconName = _icons[index];
                    final isSelected = _selectedIcon == iconName;
                    return GestureDetector(
                      onTap: () => setState(() => _selectedIcon = iconName),
                      child: Container(
                        width: 60,
                        height: 60,
                        decoration: BoxDecoration(
                          color: isSelected
                              ? PremiumColor.primary.withOpacity(0.1)
                              : Colors.grey[100],
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: isSelected
                                ? PremiumColor.primary
                                : Colors.transparent,
                            width: 2,
                          ),
                        ),
                        child: Icon(
                          _getIconData(iconName),
                          color: isSelected
                              ? PremiumColor.primary
                              : Colors.grey[400],
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 24),
              Text(
                "Choose Color",
                style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 12),
              SizedBox(
                height: 50,
                child: ListView.separated(
                  scrollDirection: Axis.horizontal,
                  itemCount: _colors.length,
                  separatorBuilder: (context, index) =>
                      const SizedBox(width: 12),
                  itemBuilder: (context, index) {
                    final colorHex = _colors[index];
                    final color = Color(
                      int.parse(colorHex.replaceFirst('#', '0xFF')),
                    );
                    final isSelected = _selectedColor == colorHex;
                    return GestureDetector(
                      onTap: () => setState(() => _selectedColor = colorHex),
                      child: Container(
                        width: 50,
                        height: 50,
                        decoration: BoxDecoration(
                          color: color,
                          shape: BoxShape.circle,
                          border: isSelected
                              ? Border.all(color: Colors.white, width: 3)
                              : null,
                        ),
                        child: isSelected
                            ? const Icon(
                                Icons.check,
                                color: Colors.white,
                                size: 20,
                              )
                            : null,
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PremiumColor.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 0,
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : Text(
                          "Create Circle (Potong EXP/Level)",
                          style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w700,
                            fontSize: 16,
                          ),
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
