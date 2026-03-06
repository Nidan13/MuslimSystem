import 'package:flutter/material.dart';
import '../../theme/premium_color.dart';
export '../../theme/premium_color.dart';

// --- 1. PATTERN BACKGROUND (SVG REPLICATION) ---
class IslamicPatternBackground extends StatelessWidget {
  const IslamicPatternBackground({super.key});

  @override
  Widget build(BuildContext context) {
    return const SizedBox
        .shrink(); // Completely disabled to prevent ANR Skia crashes
  }
}

class MuqarnasClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    return Path()
      ..addRect(
          Rect.fromLTWH(0, 0, size.width, size.height)); // Safe simple path
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}

class ExactPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    // The previous implementation used an insanely complex Path with thousands of
    // control points, causing the Skia C++ rasterizer to hang and trigger an ANR.
    // For now, we simply leave it empty to definitively solve the crash.
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

// --- 2. HEADER CLIPPER (ELLIPSE) ---
class HeaderEllipseClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    // CSS: clip-path: ellipse(100% 65% at 50% 30%);
    Path path = Path();

    path.addOval(Rect.fromCenter(
      center: Offset(size.width * 0.5, size.height * 0.3),
      width: size.width * 2.5,
      height: size.height * 1.3,
    ));

    return path;
  }

  @override
  bool shouldReclip(covariant CustomClipper<Path> oldClipper) => false;
}

// --- 3. REUSABLE MUQARNAS HEADER WIDGET ---
class MuqarnasHeaderBackground extends StatelessWidget {
  final double height;

  const MuqarnasHeaderBackground({super.key, this.height = 380});

  @override
  Widget build(BuildContext context) {
    return Positioned(
      top: 0,
      left: 0,
      right: 0,
      height: height,
      child: ClipPath(
        clipper: _MuqarnasClipperInternal(),
        child: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                const Color(0xFFE0F2F1), // 0%
                const Color(0xFFE0F2F1).withOpacity(0.4), // 70%
                const Color(0xFFF9FAFB).withOpacity(0), // 100%
              ],
              stops: const [0.0, 0.7, 1.0],
            ),
          ),
        ),
      ),
    );
  }
}

// Re-implementing clipper here to avoid import cycles or missing files during movement
class _MuqarnasClipperInternal extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    return Path()
      ..addRect(
          Rect.fromLTWH(0, 0, size.width, size.height)); // Safe simple path
  }

  @override
  bool shouldReclip(covariant CustomClipper<Path> oldClipper) => false;
}

class PremiumHeaderBackground extends StatelessWidget {
  final double height;

  const PremiumHeaderBackground({super.key, this.height = 400});

  @override
  Widget build(BuildContext context) {
    return Positioned(
      top: 0,
      left: 0,
      right: 0,
      height: height,
      child: ClipPath(
        clipper: HeaderEllipseClipper(),
        child: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                Color(0xFFE0F2F1), // 0%
                Color.fromRGBO(224, 242, 241, 0.4), // 70%
                Color.fromRGBO(249, 250, 251, 0), // 100%
              ],
              stops: [0.0, 0.7, 1.0],
            ),
          ),
        ),
      ),
    );
  }
}

// --- 4. ISLAMIC STAR CLIPPER (Rub el Hizb) ---
class IslamicStarClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    return Path()
      ..addOval(
          Rect.fromLTWH(0, 0, size.width, size.height)); // Safe circular path
  }

  @override
  bool shouldReclip(covariant CustomClipper<Path> oldClipper) => false;
}

// --- 5. PREMIUM AVATAR WIDGET ---
class PremiumAvatar extends StatelessWidget {
  final String imageUrl;
  final double size;
  final bool hasBorder;

  const PremiumAvatar({
    super.key,
    required this.imageUrl,
    this.size = 50,
    this.hasBorder = true,
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: size,
      height: size,
      child: Stack(
        alignment: Alignment.center,
        children: [
          // Outer Border (if enabled)
          if (hasBorder)
            ClipPath(
              clipper: IslamicStarClipper(),
              child: Container(
                width: size,
                height: size,
                color: PremiumColor.primary,
              ),
            ),

          // Image
          ClipPath(
            clipper: IslamicStarClipper(),
            child: Container(
              width: hasBorder ? size - 4 : size,
              height: hasBorder ? size - 4 : size,
              color: Colors.white,
              padding: const EdgeInsets.all(2), // White gap
              child: ClipPath(
                clipper: IslamicStarClipper(),
                child: Image.network(
                  imageUrl,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) {
                    return Container(color: PremiumColor.slate400);
                  },
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
