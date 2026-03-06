import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/islamic_content.dart';
import '../services/islamic_content_service.dart';
import 'widgets/custom_background.dart';
import 'explore_detail_screen.dart';
import 'kajian_detail_screen.dart';
import '../theme/premium_color.dart';

class ExploreListScreen extends StatefulWidget {
  final String category;
  final String title;

  const ExploreListScreen({
    super.key,
    required this.category,
    required this.title,
  });

  @override
  State<ExploreListScreen> createState() => _ExploreListScreenState();
}

class _ExploreListScreenState extends State<ExploreListScreen> {
  final IslamicContentService _service = IslamicContentService();
  List<IslamicContent> _items = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final data = await _service.getByCategory(widget.category);
    if (mounted) {
      setState(() {
        _items = data;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // Background Pattern
          const Positioned.fill(child: IslamicPatternBackground()),

          CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              _buildHeader(context),
              if (_isLoading)
                const SliverFillRemaining(
                  child: Center(
                    child:
                        CircularProgressIndicator(color: PremiumColor.primary),
                  ),
                )
              else if (_items.isEmpty)
                const SliverFillRemaining(
                  child: Center(child: Text("Data tidak ditemukan")),
                )
              else
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(20, 24, 20, 100),
                  sliver: SliverList(
                    delegate: SliverChildBuilderDelegate(
                      (context, index) => _buildItemCard(_items[index]),
                      childCount: _items.length,
                    ),
                  ),
                ),
            ],
          ),

          // Custom Back Button
          Positioned(
            top: 50,
            left: 20,
            child: _CircleBackButton(onTap: () => Navigator.pop(context)),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return SliverAppBar(
      expandedHeight: 240,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          children: [
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [PremiumColor.primary, Color(0xFF1B2E35)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
            ),
            const Positioned.fill(child: IslamicPatternBackground()),
            Positioned(
              right: -50,
              top: -20,
              child: Opacity(
                opacity: 0.1,
                child: Icon(Icons.star_rounded, size: 250, color: Colors.white),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 0, 24, 40),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.end,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      widget.title.toUpperCase(),
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: Colors.white70,
                        letterSpacing: 2,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    "Eksplorasi Wawasan",
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 34,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    "Perdalam ilmu dan kecintaan pada agama",
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 14,
                      fontWeight: FontWeight.w500,
                      color: Colors.white.withOpacity(0.6),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildItemCard(IslamicContent item) {
    bool isVideo =
        item.category == 'kajian' && item.metadata?['videoId'] != null;

    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.06),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
        border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () {
            if (isVideo) {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => KajianDetailScreen(content: item),
                ),
              );
              return;
            }
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => ExploreDetailScreen(content: item),
              ),
            );
          },
          borderRadius: BorderRadius.circular(28),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(18),
                  child: Container(
                    width: isVideo ? 100 : 70,
                    height: 70,
                    decoration: BoxDecoration(
                      color: PremiumColor.primary.withOpacity(0.08),
                    ),
                    child: item.imageUrl != null
                        ? Stack(
                            fit: StackFit.expand,
                            children: [
                              Image.network(
                                item.imageUrl!,
                                fit: BoxFit.cover,
                                errorBuilder: (c, e, s) => Center(
                                  child: Icon(
                                      isVideo
                                          ? Icons.play_circle_fill_rounded
                                          : Icons.image_not_supported_rounded,
                                      color: PremiumColor.primary,
                                      size: 32),
                                ),
                              ),
                              if (isVideo)
                                Center(
                                  child: Container(
                                    padding: const EdgeInsets.all(4),
                                    decoration: BoxDecoration(
                                      color: Colors.black.withOpacity(0.3),
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(Icons.play_arrow_rounded,
                                        color: Colors.white, size: 24),
                                  ),
                                ),
                            ],
                          )
                        : Center(
                            child: Text(
                              item.title.isNotEmpty
                                  ? item.title.substring(0, 1)
                                  : "?",
                              style: GoogleFonts.playfairDisplay(
                                fontSize: 24,
                                fontWeight: FontWeight.w900,
                                color: PremiumColor.primary,
                              ),
                            ),
                          ),
                  ),
                ),
                const SizedBox(width: 20),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item.title,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: PremiumColor.primary,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 6),
                      Text(
                        item.subtitle,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: PremiumColor.slate500,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                Icon(Icons.arrow_forward_ios_rounded,
                    color: PremiumColor.primary.withOpacity(0.2), size: 14),
              ],
            ),
          ),
        ),
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
        width: 45,
        height: 45,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.2),
          shape: BoxShape.circle,
          border: Border.all(color: Colors.white.withOpacity(0.3)),
        ),
        child:
            const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 22),
      ),
    );
  }
}
