import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class TajwidStyles {
  // Mapping identifiers from alquran.cloud to vibrant colors
  static const Map<String, Color> colorMap = {
    'h': Color(0xFF9E9E9E), // Hamza-Wasl (Grey)
    's': Color(0xFF9E9E9E), // Silent (Grey)
    'l': Color(0xFF9E9E9E), // Laam-Shamsiyah (Grey)
    'n': Color(0xFF2196F3), // Normal Prolongation (Blue)
    'p': Color(0xFF1976D2), // Permissible Prolongation (Darker Blue)
    'm': Color(0xFF0D47A1), // Necessary Prolongation (Deep Blue)
    'q': Color(0xFFF44336), // Qalaqah (Red)
    'o': Color(0xFF0288D1), // Obligatory Prolongation (Light Blue)
    'c': Color(0xFF9C27B0), // Ikhafa' Shafawi (Purple)
    'f': Color(0xFFE91E63), // Ikhafa (Pinkish Purple)
    'w': Color(0xFF4CAF50), // Idgham Shafawi (Green)
    'i': Color(0xFF00BCD4), // Iqlab (Cyan)
    'a': Color(0xFF009688), // Idgham - With Ghunnah (Teal)
    'u': Color(0xFF388E3C), // Idgham - Without Ghunnah (Dark Green)
    'd': Color(0xFF757575), // Idgham - Mutajanisayn (Grey)
    'b': Color(0xFF757575), // Idgham - Mutaqaribayn (Grey)
    'g': Color(0xFFFF9800), // Ghunnah (Orange)
  };

  static TextStyle getStyle(String identifier,
      {required double fontSize,
      required double lineHeight,
      required Color defaultColor}) {
    Color? color = colorMap[identifier.toLowerCase()];

    return GoogleFonts.amiri(
      color: color ?? defaultColor,
      fontSize: fontSize,
      height: lineHeight,
      fontWeight: color != null ? FontWeight.bold : FontWeight.normal,
    );
  }

  static TextStyle getPlainStyle(
      {required double fontSize,
      required double lineHeight,
      required Color defaultColor}) {
    return GoogleFonts.amiri(
      fontSize: fontSize,
      height: lineHeight,
      color: defaultColor,
    );
  }
}
