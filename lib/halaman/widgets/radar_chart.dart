import 'dart:math';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../theme/premium_color.dart';

class RadarChart extends StatelessWidget {
  final Map<String, double> values; // Values between 0.0 and 1.0
  final double size;
  final Color color;

  const RadarChart({
    super.key,
    required this.values,
    this.size = 280,
    this.color = PremiumColor.primary,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      padding: const EdgeInsets.all(16),
      child: CustomPaint(
        size: Size(size, size),
        painter: RadarChartPainter(
          values: values,
          color: color,
        ),
      ),
    );
  }
}

class RadarChartPainter extends CustomPainter {
  final Map<String, double> values;
  final Color color;

  RadarChartPainter({required this.values, required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    final centerX = size.width / 2;
    final centerY = size.height / 2;
    final radius = min(centerX, centerY) * 0.8;
    final axes = values.keys.toList();
    final angleStep = (2 * pi) / axes.length;

    final paintGrid = Paint()
      ..color = Colors.grey.withOpacity(0.15)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1;

    final paintAxis = Paint()
      ..color = Colors.grey.withOpacity(0.3)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.5;

    // 1. Draw Background Grid (5 rings)
    for (int i = 1; i <= 5; i++) {
      final ringRadius = radius * (i / 5);
      final path = Path();
      for (int j = 0; j < axes.length; j++) {
        final angle = j * angleStep - pi / 2;
        final x = centerX + ringRadius * cos(angle);
        final y = centerY + ringRadius * sin(angle);
        if (j == 0) {
          path.moveTo(x, y);
        } else {
          path.lineTo(x, y);
        }
      }
      path.close();
      canvas.drawPath(path, paintGrid);
    }

    // 2. Draw Axes and Labels
    for (int i = 0; i < axes.length; i++) {
      final angle = i * angleStep - pi / 2;
      final x = centerX + radius * cos(angle);
      final y = centerY + radius * sin(angle);

      // Draw axis line
      canvas.drawLine(Offset(centerX, centerY), Offset(x, y), paintAxis);

      // Draw labels
      final labelX = centerX + (radius + 24) * cos(angle);
      final labelY = centerY + (radius + 24) * sin(angle);

      _drawText(canvas, axes[i], Offset(labelX, labelY), size.width);
    }

    // 3. Draw Data Area
    final paintData = Paint()
      ..color = color.withOpacity(0.4)
      ..style = PaintingStyle.fill;

    final paintDataStroke = Paint()
      ..color = color
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2.5
      ..strokeCap = StrokeCap.round;

    final dataPath = Path();
    for (int i = 0; i < axes.length; i++) {
      final val = (values[axes[i]] ?? 0).clamp(0.0, 1.0);
      final angle = i * angleStep - pi / 2;
      final x = centerX + radius * val * cos(angle);
      final y = centerY + radius * val * sin(angle);

      if (i == 0) {
        dataPath.moveTo(x, y);
      } else {
        dataPath.lineTo(x, y);
      }
    }
    dataPath.close();

    // Draw shadow/glow under data
    canvas.drawShadow(dataPath, color.withOpacity(0.2), 6, true);
    canvas.drawPath(dataPath, paintData);
    canvas.drawPath(dataPath, paintDataStroke);

    // 4. Draw Data Points (Dots)
    final paintPoint = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.fill;
    final paintPointStroke = Paint()
      ..color = color
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;

    for (int i = 0; i < axes.length; i++) {
      final val = (values[axes[i]] ?? 0).clamp(0.0, 1.0);
      final angle = i * angleStep - pi / 2;
      final x = centerX + radius * val * cos(angle);
      final y = centerY + radius * val * sin(angle);

      canvas.drawCircle(Offset(x, y), 4, paintPoint);
      canvas.drawCircle(Offset(x, y), 4, paintPointStroke);
    }
  }

  void _drawText(
      Canvas canvas, String text, Offset position, double totalWidth) {
    final textPainter = TextPainter(
      text: TextSpan(
        text: text,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 10,
          fontWeight: FontWeight.w900,
          color: PremiumColor.slate500,
          letterSpacing: 1.0,
        ),
      ),
      textDirection: TextDirection.ltr,
      textAlign: TextAlign.center,
    )..layout();

    final offset = Offset(
      position.dx - textPainter.width / 2,
      position.dy - textPainter.height / 2,
    );

    textPainter.paint(canvas, offset);
  }

  @override
  bool shouldRepaint(covariant RadarChartPainter oldDelegate) {
    return oldDelegate.values != values || oldDelegate.color != color;
  }
}
