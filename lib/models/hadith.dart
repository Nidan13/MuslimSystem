class Hadith {
  final String narrator;
  final String bookName;
  final int number;
  final String arab;
  final String
      id; // This is the translation content according to gading.dev API

  Hadith({
    required this.narrator,
    required this.bookName,
    required this.number,
    required this.arab,
    required this.id,
  });

  factory Hadith.fromJson(Map<String, dynamic> json, String bookName) {
    // Handling the structure from https://api.hadith.gading.dev/books/bukhari?range=1-1
    final hadithData = json['hadiths'][0];
    return Hadith(
      narrator: json['name'] ?? 'Unknown',
      bookName: bookName,
      number: hadithData['number'],
      arab: hadithData['arab'],
      id: hadithData['id'],
    );
  }
}
