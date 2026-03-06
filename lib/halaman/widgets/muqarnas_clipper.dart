import 'package:flutter/material.dart';

class MuqarnasClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    Path path = Path();
    path.lineTo(0, 0);
    path.lineTo(size.width, 0);
    path.lineTo(size.width, size.height * 0.85);

    double w = size.width;
    double h = size.height;

    // Simpler implementation based on the CSS polygon provided:
    // 0% 0%, 100% 0%, 100% 88%,
    // 90% 95%, 80% 88%, 70% 95%, 60% 88%, 50% 100%,
    // 40% 88%, 30% 95%, 20% 88%, 10% 95%, 0% 88%

    path = Path();
    path.moveTo(0, 0);
    path.lineTo(w, 0);
    path.lineTo(w, h * 0.88);

    path.lineTo(w * 0.90, h * 0.95);
    path.lineTo(w * 0.80, h * 0.88);
    path.lineTo(w * 0.70, h * 0.95);
    path.lineTo(w * 0.60, h * 0.88);
    path.lineTo(w * 0.50, h * 1.00); // Center tip
    path.lineTo(w * 0.40, h * 0.88);
    path.lineTo(w * 0.30, h * 0.95);
    path.lineTo(w * 0.20, h * 0.88);
    path.lineTo(w * 0.10, h * 0.95);
    path.lineTo(w * 0.00, h * 0.88);

    path.close();
    return path;
  }

  @override
  bool shouldReclip(covariant CustomClipper<Path> oldClipper) => false;
}
