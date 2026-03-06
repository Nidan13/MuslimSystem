class IslamicContent {
  final String id;
  final String category; // 'prophet', 'angel', 'companion'
  final String title;
  final String subtitle;
  final String description;
  final String? arabicName;
  final String? additionalInfo;
  final String? imageUrl;
  final Map<String, dynamic>? metadata;

  IslamicContent({
    required this.id,
    required this.category,
    required this.title,
    required this.subtitle,
    required this.description,
    this.arabicName,
    this.additionalInfo,
    this.imageUrl,
    this.metadata,
  });

  factory IslamicContent.fromMap(Map<String, dynamic> map) {
    return IslamicContent(
      id: map['id']?.toString() ?? '',
      category: map['category'] ?? 'companion',
      title: map['title'] ?? map['name'] ?? '',
      subtitle: map['subtitle'] ??
          map['title'] ??
          '', // Map 'title' from JSON to subtitle if 'subtitle' missing
      description: map['description'] ?? map['story'] ?? '',
      arabicName: map['arabicName'] ?? map['arabic_name'],
      additionalInfo: map['additionalInfo'] ?? map['snippet'],
      imageUrl: map['imageUrl'],
      metadata: map['metadata'],
    );
  }
}
