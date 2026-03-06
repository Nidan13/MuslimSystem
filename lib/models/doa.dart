class Doa {
  final String id;
  final String judul;
  final String arab;
  final String latin;
  final String terjemah;

  Doa({
    required this.id,
    required this.judul,
    required this.arab,
    required this.latin,
    required this.terjemah,
  });

  factory Doa.fromJson(Map<String, dynamic> json) {
    return Doa(
      id: json['id'] ?? '',
      judul: json['judul'] ?? 'Doa',
      arab: json['doa'] ?? '',
      latin: json['latin'] ?? '',
      terjemah: json['artinya'] ?? '',
    );
  }
}
