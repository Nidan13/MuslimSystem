enum MuadzinType { system, cultural, adhan }

class Muadzin {
  final int id;
  final String name;
  final String description;
  final String audioUrlBiasa;
  final String audioUrlShubuh;
  final String? avatar;
  final MuadzinType type;

  Muadzin({
    required this.id,
    required this.name,
    required this.description,
    required this.audioUrlBiasa,
    required this.audioUrlShubuh,
    this.avatar,
    this.type = MuadzinType.adhan,
  });

  // Backward combatibility or single URL usage
  String get audioUrl => audioUrlBiasa;

  factory Muadzin.fromJson(Map<String, dynamic> json) {
    return Muadzin(
      id: json['id'] ?? 0,
      name: json['name'] ?? 'Unknown',
      description: json['description'] ?? '',
      audioUrlBiasa: json['audio_url'] ?? '',
      audioUrlShubuh: json['audio_url_shubuh'] ?? json['audio_url'] ?? '',
      avatar: json['avatar'],
      type: _parseType(json['type']),
    );
  }

  static MuadzinType _parseType(String? type) {
    switch (type?.toLowerCase()) {
      case 'system':
        return MuadzinType.system;
      case 'cultural':
        return MuadzinType.cultural;
      default:
        return MuadzinType.adhan;
    }
  }
}

class MuadzinService {
  Future<List<Muadzin>> getAllMuadzins() async {
    return getDefaultMuadzins();
  }

  List<Muadzin> getDefaultMuadzins() {
    return [
      Muadzin(
        id: -3,
        name: 'Tidak Ada',
        description: 'Matikan notifikasi sepenuhnya',
        audioUrlBiasa: '',
        audioUrlShubuh: '',
        type: MuadzinType.system,
      ),
      Muadzin(
        id: -2,
        name: 'Senyap',
        description: 'Notifikasi muncul tanpa suara',
        audioUrlBiasa: '',
        audioUrlShubuh: '',
        type: MuadzinType.system,
      ),
      Muadzin(
        id: -1,
        name: 'Notifikasi Bawaan',
        description: 'Menggunakan suara sistem HP',
        audioUrlBiasa: '',
        audioUrlShubuh: '',
        type: MuadzinType.system,
      ),
      Muadzin(
        id: 1,
        name: 'Ghofar Zaen',
        description: 'Nada Rendah Merdu dan Indah',
        audioUrlBiasa: 'Adzan/adzan_1.mp3',
        audioUrlShubuh: 'AdzanShubuh/adzan_shubuh_1.mp3',
        type: MuadzinType.adhan,
      ),
      Muadzin(
        id: 2,
        name: 'Sheikh Ali Ahmed Mulla',
        description: 'Makkah',
        audioUrlBiasa: 'Adzan/adzan_2.mp3',
        audioUrlShubuh: 'AdzanShubuh/adzan_shubuh_2.mp3',
        type: MuadzinType.adhan,
      ),
      Muadzin(
        id: 3,
        name: 'Muzammil Hasballah',
        description: 'Adzan Nahawand',
        audioUrlBiasa: 'Adzan/adzan_3.mp3',
        audioUrlShubuh: 'AdzanShubuh/adzan_shubuh_3.mp3',
        type: MuadzinType.adhan,
      ),
      Muadzin(
        id: 4,
        name: 'Ahmed Al Zarouni',
        description: 'Merdu',
        audioUrlBiasa: 'Adzan/adzan_4.mp3',
        audioUrlShubuh: 'AdzanShubuh/adzan_shubuh_4.mp3',
        type: MuadzinType.adhan,
      ),
      Muadzin(
        id: 5,
        name: 'Mishary Rashid Alafasy',
        description: 'Maqam Hijaz HD',
        audioUrlBiasa: 'Adzan/adzan_5.mp3',
        audioUrlShubuh: 'AdzanShubuh/adzan_shubuh_5.mp3',
        type: MuadzinType.adhan,
      ),
      Muadzin(
        id: 6,
        name: 'Muhammad Marwan Qassas',
        description: 'Syahdu',
        audioUrlBiasa: 'Adzan/adzan_6.mp3',
        audioUrlShubuh: 'AdzanShubuh/adzan_shubuh_6.mp3',
        type: MuadzinType.adhan,
      ),
    ];
  }
}
