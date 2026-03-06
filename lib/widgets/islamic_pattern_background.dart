import 'package:flutter/material.dart';

class IslamicPatternBackground extends StatelessWidget {
  const IslamicPatternBackground({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        image: DecorationImage(
          image: NetworkImage(
              "https://www.transparenttextures.com/patterns/arabesque.png"),
          opacity: 0.05,
          repeat: ImageRepeat.repeat,
        ),
      ),
    );
  }
}
