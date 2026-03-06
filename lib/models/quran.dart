class Surah {
  final int nomor;
  final String nama;
  final String namaLatin;
  final int jumlahAyat;
  final String tempatTurun;
  final String arti;
  final String deskripsi;

  Surah({
    required this.nomor,
    required this.nama,
    required this.namaLatin,
    required this.jumlahAyat,
    required this.tempatTurun,
    required this.arti,
    required this.deskripsi,
  });

  factory Surah.fromJson(Map<String, dynamic> json) {
    return Surah(
      nomor: json['nomor'],
      nama: json['nama'],
      namaLatin: json['namaLatin'],
      jumlahAyat: json['jumlahAyat'],
      tempatTurun: json['tempatTurun'],
      arti: json['arti'],
      deskripsi: json['deskripsi'],
    );
  }
}

class Ayah {
  final int nomorAyat;
  final String teksArab;
  final String teksLatin;
  final String teksIndonesia;
  final String audio;
  final int? surahNomor;
  final String? surahNamaLatin;
  final int? halaman;

  Ayah({
    required this.nomorAyat,
    required this.teksArab,
    required this.teksLatin,
    required this.teksIndonesia,
    required this.audio,
    this.surahNomor,
    this.surahNamaLatin,
    this.halaman,
  });

  factory Ayah.fromJson(Map<String, dynamic> json) {
    return Ayah(
      nomorAyat: json['nomorAyat'] ?? json['number']['inSurah'],
      teksArab: json['teksArab'] ?? json['text']['arab'],
      teksLatin:
          json['teksLatin'] ?? (json['text']?['transliteration']?['en'] ?? ''),
      teksIndonesia: json['teksIndonesia'] ?? json['translation']?['id'] ?? '',
      audio: json['audio'] is Map
          ? (json['audio']['05'] ?? json['audio']['01'])
          : (json['audio']?['primary'] ?? json['audio'] ?? ''),
      surahNomor: json['surahNomor'],
      surahNamaLatin: json['surahNamaLatin'],
      halaman: json['meta']?['page'] ?? json['halaman'],
    );
  }
}

class SurahDetail {
  final int nomor;
  final String nama;
  final String namaLatin;
  final int jumlahAyat;
  final String tempatTurun;
  final String arti;
  final String deskripsi;
  final List<Ayah> ayat;

  SurahDetail({
    required this.nomor,
    required this.nama,
    required this.namaLatin,
    required this.jumlahAyat,
    required this.tempatTurun,
    required this.arti,
    required this.deskripsi,
    required this.ayat,
  });

  factory SurahDetail.fromJson(Map<String, dynamic> json) {
    return SurahDetail(
      nomor: json['nomor'],
      nama: json['nama'],
      namaLatin: json['namaLatin'],
      jumlahAyat: json['jumlahAyat'],
      tempatTurun: json['tempatTurun'],
      arti: json['arti'],
      deskripsi: json['deskripsi'],
      ayat: (json['ayat'] as List).map((i) => Ayah.fromJson(i)).toList(),
    );
  }
}
