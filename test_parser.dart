class TextSpan {
  final String text;
  final String? id;
  TextSpan(this.text, this.id);
  @override
  String toString() => '($text, $id)';
}

List<TextSpan> parse(String text) {
  final List<TextSpan> spans = [];
  // Regex to match:
  // 1. [h:1][text]
  // 2. [h:1[text]]
  // 3. [h][text]
  // 4. [h[text]]
  final RegExp tagRegExp = RegExp(
      r'\[([a-z])(?::\d+)?(?:\]\[|\[)([^\]]+)\](?:\])?',
      caseSensitive: false);

  int lastMatchEnd = 0;
  for (final match in tagRegExp.allMatches(text)) {
    if (match.start > lastMatchEnd) {
      String plain = text.substring(lastMatchEnd, match.start);
      // Clean up empty tags like [h:1] or [h] from plain text
      plain = plain.replaceAll(
          RegExp(r'\[[a-z](?::\d+)?\]', caseSensitive: false), "");
      if (plain.isNotEmpty) spans.add(TextSpan(plain, null));
    }

    spans.add(TextSpan(match.group(2)!, match.group(1)));
    lastMatchEnd = match.end;
  }

  if (lastMatchEnd < text.length) {
    String plain = text.substring(lastMatchEnd);
    plain = plain.replaceAll(
        RegExp(r'\[[a-z](?::\d+)?\]', caseSensitive: false), "");
    if (plain.isNotEmpty) spans.add(TextSpan(plain, null));
  }

  return spans;
}

void main() {
  var test1 = "بِسْمِ [h:1[ٱ]]للَّهِ";
  var test2 = "بِسْمِ [h:1][ا]للَّهِ";
  var test3 = "[h:2[ٱ][l[ل]]]رَّح[p[ِي]m]نِ";

  print("Test 1: ${parse(test1)}");
  print("Test 2: ${parse(test2)}");
  print("Test 3: ${parse(test3)}");
}
