import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class TajwidParser {
  /// God-tier stack-based parser for Al-Quran Cloud Tajweed markers.
  /// Handles nested tags like [h:2[ٱ][l[ل]]] and clean up empty markers.
  static List<TextSpan> parse(
    String text, {
    required double fontSize,
    required double lineHeight,
    required Color defaultColor,
    bool isDarkMode = false,
  }) {
    final List<TextSpan> spans = [];
    final List<String> stack = [];
    String buffer = "";

    void flushBuffer() {
      if (buffer.isNotEmpty) {
        String? colorId = stack.isNotEmpty ? stack.last : null;
        spans.add(_createTextSpan(
            buffer, fontSize, lineHeight, defaultColor, colorId, isDarkMode));
        buffer = "";
      }
    }

    for (int i = 0; i < text.length; i++) {
      if (text[i] == '[') {
        int j = i + 1;
        while (j < text.length &&
            text[j] != '[' &&
            text[j] != ']' &&
            text[j] != ' ' &&
            (j - i) < 10) {
          j++;
        }

        if (j < text.length && text[j] == '[') {
          String tagIdPart = text.substring(i + 1, j);
          if (_isTajwidId(tagIdPart)) {
            flushBuffer();
            stack.add(tagIdPart.split(':').first.toLowerCase());
            i = j;
            continue;
          }
        } else if (j < text.length && text[j] == ']') {
          String tagIdPart = text.substring(i + 1, j);
          if (_isTajwidId(tagIdPart)) {
            flushBuffer();
            i = j;
            continue;
          }
        }
      }

      if (text[i] == ']') {
        if (stack.isNotEmpty) {
          flushBuffer();
          stack.removeLast();
          continue;
        }
      }

      buffer += text[i];
    }

    flushBuffer();

    if (spans.isEmpty && text.isNotEmpty) {
      spans.add(_createTextSpan(
          text, fontSize, lineHeight, defaultColor, null, isDarkMode));
    }

    return spans;
  }

  static bool _isTajwidId(String s) {
    if (s.isEmpty) return false;
    return RegExp(r'^[a-z](?::\d+)?$', caseSensitive: false).hasMatch(s);
  }

  static TextSpan _createTextSpan(
    String text,
    double fontSize,
    double lineHeight,
    Color defaultColor,
    String? identifier,
    bool isDarkMode,
  ) {
    Color color = defaultColor;
    bool isHighlighted = false;

    if (identifier != null) {
      isHighlighted = true;
      final id = identifier.toLowerCase();

      // Premium Glow Palette for Dark Mode (More Readability & Vibe)
      if (isDarkMode) {
        switch (id) {
          case 'q':
            color = const Color(0xFFFF5252);
            break; // Red (Qalaqah)
          case 'g':
            color = const Color(0xFFFFB74D);
            break; // Orange (Ghunnah)
          case 'f':
            color = const Color(0xFFCE93D8);
            break; // Purple (Ikhafa)
          case 'n':
          case 'p':
          case 'm':
          case 'o':
            color = const Color(0xFF4FC3F7);
            break; // Blue (Mad)
          case 'a':
          case 'u':
          case 'w':
            color = const Color(0xFF81C784);
            break; // Green (Idgham)
          case 'i':
            color = const Color(0xFF26C6DA);
            break; // Cyan (Iqlab)
          case 'h':
          case 's':
          case 'l':
            color = const Color(0xFF90A4AE);
            break; // Grey (Silent)
          default:
            color = defaultColor;
            isHighlighted = false;
        }
      } else {
        // High Contrast for Light Mode
        switch (id) {
          case 'q':
            color = const Color(0xFFD32F2F);
            break;
          case 'g':
            color = const Color(0xFFEF6C00);
            break;
          case 'f':
            color = const Color(0xFF9C27B0);
            break;
          case 'n':
          case 'p':
          case 'm':
          case 'o':
            color = const Color(0xFF1976D2);
            break;
          case 'a':
          case 'u':
          case 'w':
            color = const Color(0xFF2E7D32);
            break;
          case 'i':
            color = const Color(0xFF00838F);
            break;
          case 'h':
          case 's':
          case 'l':
            color = const Color(0xFF607D8B);
            break;
          default:
            color = defaultColor;
            isHighlighted = false;
        }
      }
    }

    return TextSpan(
      text: text,
      style: GoogleFonts.amiri(
        color: color,
        fontSize: fontSize,
        height: lineHeight,
        fontWeight: isHighlighted ? FontWeight.w700 : FontWeight.w500,
        decoration: TextDecoration.none,
      ),
    );
  }
}
