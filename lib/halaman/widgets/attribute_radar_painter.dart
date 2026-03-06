import 'dart:math';
import 'package:flutter/material.dart';

class AttributeRadarPainter extends CustomPainter {
  final List<double> values; // Normalized 0.0 to 1.0
  final List<String> labels;
  final Color color;

  AttributeRadarPainter({
    required this.values,
    required this.labels,
    required this.color,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.width / 2;
    final angleStep = (2 * pi) / labels.length;

    final linePaint = Paint()
      ..color = Colors.grey.withOpacity(0.2)
      ..strokeWidth = 1.0
      ..style = PaintingStyle.stroke;

    final fillPaint = Paint()
      ..color = color.withOpacity(0.3)
      ..style = PaintingStyle.fill;

    final borderPaint = Paint()
      ..color = color
      ..strokeWidth = 2.0
      ..style = PaintingStyle.stroke;

    // 1. Draw Background Circles/Polygons
    for (var i = 1; i <= 5; i++) {
      final r = radius * (i / 5);
      final path = Path();
      for (var j = 0; j < labels.length; j++) {
        final angle = angleStep * j - pi / 2;
        final x = center.dx + r * cos(angle);
        final y = center.dy + r * sin(angle);
        if (j == 0) {
          path.moveTo(x, y);
        } else {
          path.lineTo(x, y);
        }
      }
      path.close();
      canvas.drawPath(path, linePaint);
    }

    // 2. Draw Axis Lines
    for (var j = 0; j < labels.length; j++) {
      final angle = angleStep * j - pi / 2;
      canvas.drawLine(
        center,
        Offset(
            center.dx + radius * cos(angle), center.dy + radius * sin(angle)),
        linePaint,
      );
    }

    // 3. Draw Value Area
    final valuePath = Path();
    for (var j = 0; j < values.length; j++) {
      final angle = angleStep * j - pi / 2;
      final val = values[j].clamp(0.1, 1.0); // Minimum visibility
      final x = center.dx + (radius * val) * cos(angle);
      final y = center.dy + (radius * val) * sin(angle);
      if (j == 0) {
        valuePath.moveTo(x, y);
      } else {
        valuePath.lineTo(x, y);
      }
    }
    valuePath.close();
    canvas.drawPath(valuePath, fillPaint);
    canvas.drawPath(valuePath, borderPaint);

    // 4. Draw Dots
    final dotPaint = Paint()..color = color;
    for (var j = 0; j < values.length; j++) {
      final angle = angleStep * j - pi / 2;
      final val = values[j].clamp(0.1, 1.0);
      canvas.drawCircle(
        Offset(center.dx + (radius * val) * cos(angle),
            center.dy + (radius * val) * sin(angle)),
        4,
        dotPaint,
      );
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;
}
