import 'package:flutter/material.dart';

enum CharacterExpression { normal, happy, sad, angry }

class RubElHizbAvatar extends StatelessWidget {
  final String? imageUrl;
  final String level;
  final double hpProgress;
  final double size;
  final String gender; // 'male' or 'female'
  final CharacterExpression expression;
  final bool isCircle; // New parameter

  const RubElHizbAvatar({
    super.key,
    required this.imageUrl,
    required this.level,
    this.hpProgress = 1.0,
    this.size = 140,
    this.gender = 'male',
    this.expression = CharacterExpression.normal,
    this.isCircle = false, // Default to false (Star)
  });

  @override
  Widget build(BuildContext context) {
    // 1. Determine Asset Path based on Gender & Expression
    String assetName;
    final String g = gender.toLowerCase();
    final isMale = g == 'male' || g == 'laki-laki' || g == 'pria';

    switch (expression) {
      case CharacterExpression.happy:
        assetName = isMale ? 'cowo_senang.jpeg' : 'cewe_biasa.jpeg';
        break;
      case CharacterExpression.sad:
        assetName = isMale ? 'cowo_nangis.jpeg' : 'cewe_sedih.jpeg';
        break;
      case CharacterExpression.angry:
        assetName = isMale ? 'cowo_marah.jpeg' : 'cewe_marah.jpeg';
        break;
      case CharacterExpression.normal:
        assetName = isMale ? 'cowo_biasa.jpeg' : 'cewe_biasa.jpeg';
        break;
    }

    final assetPath = 'assets/images/$assetName';

    if (isCircle) {
      return SizedBox(
        width: size,
        height: size,
        child: Container(
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            border: Border.all(color: Colors.white, width: 4),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.1),
                blurRadius: 10,
                offset: const Offset(0, 5),
              ),
            ],
          ),
          child: CircleAvatar(
            backgroundImage: AssetImage(assetPath),
            radius: size / 2,
            backgroundColor: Colors.grey[200],
          ),
        ),
      );
    }

    return SizedBox(
      width: size,
      height: size,
      child: Stack(
        alignment: Alignment.center,
        children: [
          ClipPath(
            clipper: IslamicStarClipper(),
            child: Container(
              width: size * 0.9,
              height: size * 0.9,
              color: const Color(0xFF005B71),
              padding: const EdgeInsets.all(4),
              child: ClipPath(
                clipper: IslamicStarClipper(),
                child: Container(
                  color: Colors.white,
                  padding: const EdgeInsets.all(6),
                  child: ClipPath(
                    clipper: IslamicStarClipper(),
                    child: Image.asset(
                      assetPath,
                      fit: BoxFit.cover,
                      errorBuilder: (c, o, s) => Image.network(
                        "https://api.dicebear.com/7.x/pixel-art/png?seed=Hunter&mood[]=happy",
                        fit: BoxFit.cover,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class IslamicStarClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    double w = size.width;
    double h = size.height;
    Path path = Path();
    path.moveTo(w * 0.50, h * 0.00);
    path.lineTo(w * 0.65, h * 0.15);
    path.lineTo(w * 0.85, h * 0.15);
    path.lineTo(w * 0.85, h * 0.35);
    path.lineTo(w * 1.00, h * 0.50);
    path.lineTo(w * 0.85, h * 0.65);
    path.lineTo(w * 0.85, h * 0.85);
    path.lineTo(w * 0.65, h * 0.85);
    path.lineTo(w * 0.50, h * 1.00);
    path.lineTo(w * 0.35, h * 0.85);
    path.lineTo(w * 0.15, h * 0.85);
    path.lineTo(w * 0.15, h * 0.65);
    path.lineTo(w * 0.00, h * 0.50);
    path.lineTo(w * 0.15, h * 0.35);
    path.lineTo(w * 0.15, h * 0.15);
    path.lineTo(w * 0.35, h * 0.15);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(covariant CustomClipper<Path> oldClipper) => false;
}
