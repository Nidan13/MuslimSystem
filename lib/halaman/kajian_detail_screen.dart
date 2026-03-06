import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
import '../services/islamic_content_service.dart';
import '../models/islamic_content.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';

class KajianDetailScreen extends StatefulWidget {
  final IslamicContent content;

  const KajianDetailScreen({super.key, required this.content});

  @override
  State<KajianDetailScreen> createState() => _KajianDetailScreenState();
}

class _KajianDetailScreenState extends State<KajianDetailScreen> {
  late YoutubePlayerController _controller;
  bool _isPlayerReady = false;

  @override
  void initState() {
    super.initState();
    final String videoId = widget.content.metadata?['videoId'] ?? "";
    _controller = YoutubePlayerController(
      initialVideoId: videoId,
      flags: const YoutubePlayerFlags(
        autoPlay: true,
        mute: false,
        enableCaption: true,
        forceHD: true,
      ),
    )..addListener(_onPlayerStateChange);
  }

  void _onPlayerStateChange() {
    if (mounted && _controller.value.isReady && !_isPlayerReady) {
      setState(() {
        _isPlayerReady = true;
      });
    }

    // Handle video completion
    if (mounted && _controller.value.playerState == PlayerState.ended) {
      _handleVideoCompleted();
    }
  }

  void _handleVideoCompleted() async {
    final rawId = widget.content.metadata?['dbId'];
    if (rawId == null) {
      debugPrint("Error: dbId is null in metadata");
      return;
    }

    final int? dbId = int.tryParse(rawId.toString());
    if (dbId == null) {
      debugPrint("Error: Could not parse dbId: $rawId");
      return;
    }

    try {
      final response = await IslamicContentService().completeKajian(dbId);

      if (mounted) {
        final String message = response?['message'] ??
            'Gagal mencatat kajian. Coba lagi nanti ya wok!';
        final bool isSuccess = response?['success'] ?? false;

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              message,
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
            backgroundColor:
                isSuccess ? PremiumColor.primary : Colors.redAccent,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(10),
            ),
          ),
        );

        // If success, refresh global profile so stats increase immediately
        if (isSuccess) {
          // We don't have a direct "refreshAll" but the next time
          // user opens Profile it should fetch if not using cache only.
        }
      }
    } catch (e) {
      debugPrint("Error completion: $e");
    }
  }

  @override
  void deactivate() {
    _controller.pause();
    super.deactivate();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return YoutubePlayerBuilder(
      player: YoutubePlayer(
        controller: _controller,
        showVideoProgressIndicator: true,
        progressIndicatorColor: PremiumColor.primary,
        progressColors: const ProgressBarColors(
          playedColor: PremiumColor.primary,
          handleColor: PremiumColor.accent,
        ),
        onReady: () {
          _isPlayerReady = true;
        },
      ),
      builder: (context, player) {
        return Scaffold(
          backgroundColor: Colors.white,
          body: Stack(
            children: [
              const Positioned.fill(child: IslamicPatternBackground()),
              CustomScrollView(
                physics: const BouncingScrollPhysics(),
                slivers: [
                  SliverAppBar(
                    expandedHeight: 250,
                    backgroundColor: Colors.black,
                    pinned: true,
                    automaticallyImplyLeading: false,
                    flexibleSpace: FlexibleSpaceBar(
                      background: player,
                    ),
                  ),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 24, vertical: 32),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildHeaderInfo(),
                          const SizedBox(height: 32),
                          _buildSectionLabel("DESKRIPSI KAJIAN"),
                          const SizedBox(height: 16),
                          Text(
                            widget.content.description,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                              color: PremiumColor.slate800,
                              height: 1.8,
                            ),
                          ),
                          const SizedBox(height: 100),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              Positioned(
                top: 50,
                left: 20,
                child: _CircleBackButton(onTap: () => Navigator.pop(context)),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildHeaderInfo() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
          decoration: BoxDecoration(
            color: PremiumColor.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Text(
            "VIDEO KAJIAN",
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              color: PremiumColor.primary,
              letterSpacing: 1.5,
            ),
          ),
        ),
        const SizedBox(height: 16),
        Text(
          widget.content.title,
          style: GoogleFonts.playfairDisplay(
            fontSize: 28,
            fontWeight: FontWeight.w900,
            color: PremiumColor.primary,
            height: 1.2,
          ),
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            const Icon(Icons.account_circle_outlined,
                size: 18, color: PremiumColor.slate500),
            const SizedBox(width: 8),
            Text(
              widget.content.subtitle,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: PremiumColor.slate500,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildSectionLabel(String label) {
    return Row(
      children: [
        Container(
            width: 24,
            height: 3,
            decoration: BoxDecoration(
                color: PremiumColor.accent,
                borderRadius: BorderRadius.circular(2))),
        const SizedBox(width: 12),
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12,
            fontWeight: FontWeight.w900,
            color: PremiumColor.primary.withOpacity(0.4),
            letterSpacing: 1.5,
          ),
        ),
      ],
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
          color: Colors.black.withOpacity(0.3),
          shape: BoxShape.circle,
          border: Border.all(color: Colors.white.withOpacity(0.3)),
        ),
        child:
            const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 22),
      ),
    );
  }
}
