class AyahModel {
  final int number;
  final String text;
  final String translation;
  final String? audio;
  final int numberInSurah;
  final String? tajwidTags; // Optional: stored as string to be parsed

  AyahModel({
    required this.number,
    required this.text,
    required this.translation,
    this.audio,
    required this.numberInSurah,
    this.tajwidTags,
  });

  factory AyahModel.fromJson(Map<String, dynamic> json) {
    return AyahModel(
      number: json['number'],
      text: json['text'],
      translation: json['translation'] ?? '',
      audio: json['audio'],
      numberInSurah: json['numberInSurah'] ?? 0,
      tajwidTags: json['tajwidTags'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'number': number,
      'text': text,
      'translation': translation,
      'audio': audio,
      'numberInSurah': numberInSurah,
      'tajwidTags': tajwidTags,
    };
  }
}
