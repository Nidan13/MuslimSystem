import 'package:flutter/material.dart';
import 'dart:math' as math;

// -----------------------------------------------------------------------------
// Seamless Geometric Background (8-Fold Rosette / Octagram Lattice)
// -----------------------------------------------------------------------------
class GeometricBackground extends StatelessWidget {
  const GeometricBackground({super.key});

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        // Layer 1: Clean White/Grey Base
        Container(
          color: const Color(0xFFF8F9FA),
        ),

        // Layer 2: Geometric Line Pattern with Shadow
        SizedBox(
          width: double.infinity,
          height: double.infinity,
          child: CustomPaint(
            painter: _GeometricPatternPainter(),
          ),
        ),

        // Layer 3: Soft Fade (Gradient)
        Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                const Color(0xFFF8F9FA).withOpacity(0.0),
                const Color(0xFFF8F9FA).withOpacity(0.9),
              ],
              stops: const [0.7, 1.0],
            ),
          ),
        ),
      ],
    );
  }
}

class _GeometricPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    const double cellSize = 60.0;

    // 1. Construct the Path for the entire grid
    final Path patternPath = Path();
    for (double y = 0; y < size.height + cellSize; y += cellSize) {
      for (double x = 0; x < size.width + cellSize; x += cellSize) {
        _addRosettePath(patternPath, x, y, cellSize);
      }
    }

    // 2. Draw Shadow (Depth - "Not Flat")
    final Paint shadowPaint = Paint()
      ..color = Colors.black.withOpacity(0.05)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.5
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 2);

    canvas.drawPath(patternPath.shift(const Offset(1, 2)), shadowPaint);

    // 3. Draw Main Pattern (Clean Grey)
    final Paint mainPaint = Paint()
      ..color = Colors.blueGrey.withOpacity(0.15) // Clean Grey
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.5
      ..isAntiAlias = true;

    canvas.drawPath(patternPath, mainPaint);
  }

  void _addRosettePath(Path path, double x, double y, double size) {
    // Add tile geometry to the path
    final double r = size / 2;
    final double cx = x + r;
    final double cy = y + r;

    // 1. Tilted Square (Diamond)
    // Manually adding polygon to path
    path.addPolygon([
      Offset(cx, cy - size * 0.25),
      Offset(cx + size * 0.25, cy),
      Offset(cx, cy + size * 0.25),
      Offset(cx - size * 0.25, cy),
    ], true);

    // 2. Base Square
    path.addRect(Rect.fromCenter(
        center: Offset(cx, cy), width: size * 0.35, height: size * 0.35));

    // 3. Rub el Hizb Icon (Outline)
    _addRubElHizbToPath(path, cx, cy, size * 0.12);
  }

  void _addRubElHizbToPath(Path path, double cx, double cy, double radius) {
    final double side = radius * 1.5;

    // Square 1
    path.addRect(
        Rect.fromCenter(center: Offset(cx, cy), width: side, height: side));

    // Square 2 (Rotated 45 deg)
    // We need to calculate vertices for rotated rect to add to path
    List<Offset> rotatedSquare = [];
    for (int i = 0; i < 4; i++) {
      double angle = (i * 90.0 + 45.0) * (math.pi / 180.0);
      // Distance from center to corner of square is side * sqrt(2) / 2
      double dist = (side * 1.414) / 2;
      rotatedSquare.add(
          Offset(cx + dist * math.cos(angle), cy + dist * math.sin(angle)));
    }
    path.addPolygon(rotatedSquare, true);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
