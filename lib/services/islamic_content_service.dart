import 'dart:convert';
import 'package:http/http.dart' as http;
import './api_client.dart';
import 'package:flutter/foundation.dart';
import '../models/islamic_content.dart';

class IslamicContentService {
  Future<List<Map<String, dynamic>>> getIslamicVideos() async {
    try {
      final response = await ApiClient().client.get('/islamic-videos');
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data['data'];
        return data
            .map((v) => {
                  'id': v['id'], // Database ID
                  'title': v['title'].toString(),
                  'channel': v['channel'].toString(),
                  'videoId': v['videoId'].toString(), // YouTube ID
                  'duration': v['duration'].toString(),
                })
            .toList();
      }
    } catch (e) {
      print('Error fetching islamic videos: $e');
    }
    return [];
  }

  Future<Map<String, dynamic>?> completeKajian(int dbId) async {
    try {
      final response =
          await ApiClient().client.post('/islamic-videos/complete', data: {
        'video_id': dbId,
      });
      return response.data as Map<String, dynamic>?;
    } catch (e) {
      debugPrint('Error logging kajian completion: $e');
      return null;
    }
  }

  Future<List<IslamicContent>> getProphets() async {
// ... existing code ...
    try {
      final response = await http.get(
          Uri.parse('https://islamic-api-zhirrr.vercel.app/api/kisahnabi'));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        return data.asMap().entries.map((entry) {
          final index = entry.key;
          final item = entry.value;
          return IslamicContent(
            id: 'p${index + 1}',
            category: 'prophet',
            title: item['name'] ?? '',
            subtitle:
                'Lahir: ${item['thn_kelahiran'] ?? '-'} | Usia: ${item['usia'] ?? '-'}',
            description: item['description'] ?? '',
            arabicName: item['name'],
            additionalInfo: 'Tempat: ${item['tmp'] ?? '-'}',
            imageUrl: item['image_url'],
          );
        }).toList();
      }
    } catch (e) {
      print('Error fetching prophets: $e');
    }
    // Fallback if API fails
    return _prophetsFallback.map((e) => IslamicContent.fromMap(e)).toList();
  }

  Future<List<IslamicContent>> getAngels() async {
    return _angelsData.map((e) => IslamicContent.fromMap(e)).toList();
  }

  Future<List<IslamicContent>> getCompanions() async {
    try {
      // 10.0.2.2 for Android Emulator, 192.168.18.72 for real device
      final response = await http
          .get(Uri.parse('http://192.168.18.72:8000/data/companions.json'));

      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        return data.map((e) => IslamicContent.fromMap(e)).toList();
      } else {
        print('Failed to load companions: ${response.statusCode}');
      }
    } catch (e) {
      print('Error fetching companions: $e');
    }

    // Fallback if API fails
    return _companionsData.map((e) => IslamicContent.fromMap(e)).toList();
  }

  Future<List<IslamicContent>> getKhalifas() async {
    return _khalifaData.map((e) => IslamicContent.fromMap(e)).toList();
  }

  Future<List<IslamicContent>> getFiqih() async {
    return _fiqihData.map((e) => IslamicContent.fromMap(e)).toList();
  }

  Future<List<IslamicContent>> getAkhlaq() async {
    return _akhlaqData.map((e) => IslamicContent.fromMap(e)).toList();
  }

  Future<List<IslamicContent>> getKajian() async {
    final videos = await getIslamicVideos();
    return videos
        .map((v) => IslamicContent(
              id: v['videoId'].toString(),
              category: 'kajian',
              title: v['title'].toString(),
              subtitle: v['channel'].toString(),
              description: 'Saksikan video kajian ini di YouTube.',
              imageUrl: "https://i.ytimg.com/vi/${v['videoId']}/hqdefault.jpg",
              metadata: {
                'videoId': v['videoId'],
                'dbId': v['id'], // Store database ID
              },
            ))
        .toList();
  }

  Future<List<IslamicContent>> getByCategory(String category) async {
    switch (category) {
      case 'prophet':
        return getProphets();
      case 'angel':
        return getAngels();
      case 'companion':
        return getCompanions();
      case 'khalifa':
        return getKhalifas();
      case 'fiqih':
        return getFiqih();
      case 'akhlaq':
        return getAkhlaq();
      case 'kajian':
        return getKajian();
      default:
        return [];
    }
  }

  final List<Map<String, dynamic>> _fiqihData = [
    {
      'id': 'f1',
      'category': 'fiqih',
      'title': 'Thaharah (Bersuci)',
      'subtitle': 'Kunci Utama Ibadah',
      'description':
          'Thaharah secara bahasa berarti bersih atau suci. Dalam fiqih, thaharah mencakup wudhu, mandi wajib, dan tayamum. Tanpa thaharah yang benar, sholat seseorang tidak akan sah.',
      'arabicName': 'الطهارة',
      'additionalInfo':
          'Syarat sah sholat salah satunya adalah suci dari hadats besar dan kecil.',
    },
    {
      'id': 'f2',
      'category': 'fiqih',
      'title': 'Rukun Sholat',
      'subtitle': 'Tiang Agama',
      'description':
          'Terdapat 13 rukun sholat yang harus dipenuhi, mulai dari niat, takbiratul ihram, hingga tertib. Meninggalkan salah satu rukun dengan sengaja akan membatalkan sholat.',
      'arabicName': 'أركان الصلاة',
      'additionalInfo':
          'Bedakan antara rukun (wajib ada) dan sunnah (penyempurna).',
    },
    {
      'id': 'f3',
      'category': 'fiqih',
      'title': 'Puasa Ramadhan',
      'subtitle': 'Zakat Jiwa',
      'description':
          'Menahan diri dari lapar, dahaga, dan hal-hal yang membatalkan mulai dari terbit fajar hingga terbenam matahari dengan niat karena Allah SWT.',
      'arabicName': 'صوم رمضان',
      'additionalInfo': 'Puasa melatih kesabaran dan empati terhadap sesama.',
    },
  ];

  final List<Map<String, dynamic>> _akhlaqData = [
    {
      'id': 'ak1',
      'category': 'akhlaq',
      'title': 'Birrul Walidain',
      'subtitle': 'Berbakti kepada Orang Tua',
      'description':
          'Berbakti kepada kedua orang tua adalah amalan yang paling dicintai Allah setelah sholat tepat waktu. Ridha Allah terletak pada ridha kedua orang tua.',
      'arabicName': 'بر الوالدين',
      'additionalInfo':
          'Adab berbicara lembut dan membantu mereka di masa tua.',
    },
    {
      'id': 'ak2',
      'category': 'akhlaq',
      'title': 'Kejujuran (Ash-Shidqu)',
      'subtitle': 'Mahkota Seorang Muslim',
      'description':
          'Kejujuran membawa kepada kebaikan, dan kebaikan membawa ke surga. Seorang muslim sejati dikenal dari perkataannya yang benar dan dapat dipercaya.',
      'arabicName': 'الصدق',
      'additionalInfo': 'Rasulullah SAW dijuluki Al-Amin karena kejujurannya.',
    },
    {
      'id': 'ak3',
      'category': 'akhlaq',
      'title': 'Sabar dan Syukur',
      'subtitle': 'Kunci Kebahagiaan',
      'description':
          'Sabar saat mendapat ujian dan bersyukur saat mendapat nikmat adalah dua sayap yang membawa seorang mukmin menuju ketenangan jiwa.',
      'arabicName': 'الصبر والشكر',
      'additionalInfo':
          'Innallaha ma\'ashobirin (Sesungguhnya Allah bersama orang-orang yang sabar).',
    },
  ];

  final List<Map<String, dynamic>> _prophetsFallback = [
    {
      'id': '1',
      'category': 'prophet',
      'title': 'Nabi Adam AS',
      'subtitle': 'Manusia Pertama',
      'description':
          'Nabi Adam AS adalah manusia pertama yang diciptakan Allah SWT dari tanah. Beliau adalah bapak dari seluruh umat manusia dan nabi pertama yang menerima wahyu.',
      'arabicName': 'آدم عليه السلام',
      'additionalInfo': 'Lokasi: Surga -> Bumi (Puncak Sri Lanka/Mekkah)',
    },
  ];

  final List<Map<String, dynamic>> _angelsData = [
    {
      'id': 'a1',
      'category': 'angel',
      'title': 'Jibril',
      'subtitle': 'Pemimpin Para Malaikat',
      'description':
          'Malaikat Jibril bertugas menyampaikan wahyu Allah SWT kepada para nabi dan rasul. Ia juga dikenal sebagai Ruh Al-Qudus.',
      'arabicName': 'جبريل',
    },
    {
      'id': 'a2',
      'category': 'angel',
      'title': 'Mikail',
      'subtitle': 'Pengatur Alam',
      'description':
          'Malaikat Mikail bertugas mengatur pembagian rezeki bagi seluruh makhluk, menurunkan hujan, dan mengatur tiupan angin.',
      'arabicName': 'ميكائيل',
    },
    {
      'id': 'a3',
      'category': 'angel',
      'title': 'Israfil',
      'subtitle': 'Peniup Sangkakala',
      'description':
          'Malaikat Israfil bertugas meniup sangkakala pada hari kiamat. Tiupan pertama mematikan semua makhluk, tiupan kedua membangkitkan mereka.',
      'arabicName': 'إسرافيل',
    },
    {
      'id': 'a4',
      'category': 'angel',
      'title': 'Izrail',
      'subtitle': 'Malaikat Maut',
      'description':
          'Malaikat Izrail bertugas mencabut nyawa seluruh makhluk hidup atas perintah Allah SWT tanpa terlambat sedikitpun.',
      'arabicName': 'عزرائيل',
    },
    {
      'id': 'a5',
      'category': 'angel',
      'title': 'Munkar & Nakir',
      'subtitle': 'Penanya di Alam Kubur',
      'description':
          'Bertugas menanyai manusia di dalam kubur tentang siapa Tuhannya, apa agamanya, dan siapa nabinya.',
      'arabicName': 'منكر ونكير',
    },
    {
      'id': 'a7',
      'category': 'angel',
      'title': 'Raqib & Atid',
      'subtitle': 'Pencatat Amal',
      'description':
          'Raqib mencatat amal baik dan Atid mencatat amal buruk. Tidak ada satu ucapan pun yang luput dari pengawasan mereka.',
      'arabicName': 'رقيب وعتيد',
    },
    {
      'id': 'a9',
      'category': 'angel',
      'title': 'Malik',
      'subtitle': 'Penjaga Neraka',
      'description':
          'Malaikat Malik memiliki wajah yang sangat tegas dan bertugas menjaga pintu neraka Jahannam.',
      'arabicName': 'مالك',
    },
    {
      'id': 'a10',
      'category': 'angel',
      'title': 'Ridwan',
      'subtitle': 'Penjaga Surga',
      'description':
          'Malaikat Ridwan bertugas menjaga dan menyambut penghuni surga dengan penuh keramahan dan keindahan.',
      'arabicName': 'رضوان',
    },
  ];

  final List<Map<String, dynamic>> _khalifaData = [
    {
      'id': 'k1',
      'category': 'khalifa',
      'title': 'Abu Bakar Ash-Shiddiq',
      'subtitle': 'Khalifah I (11-13 H / 632-634 M)',
      'description':
          'Sahabat terdekat Rasulullah SAW yang dikenal karena kejujurannya. Memimpin perang Riddah dan memulai pengumpulan Al-Quran dalam satu mushaf.',
      'arabicName': 'أبو بكر الصديق',
    },
    {
      'id': 'k2',
      'category': 'khalifa',
      'title': 'Umar bin Khattab',
      'subtitle': 'Khalifah II (13-23 H / 634-644 M)',
      'description':
          'Dikenal sebagai Al-Faruq (Pembeda antara yang hak dan batil). Di masanya Islam berkembang pesat ke Persia dan Romawi Timur.',
      'arabicName': 'عمر بن الخطاب',
    },
    {
      'id': 'k3',
      'category': 'khalifa',
      'title': 'Utsman bin Affan',
      'subtitle': 'Khalifah III (23-35 H / 644-656 M)',
      'description':
          'Dikenal sebagai Dzun Nurain (Pemilik Dua Cahaya). Beliau membukukan Al-Quran ke dalam dialek Quraisy (Mushaf Utsmani).',
      'arabicName': 'عثمان بن Affan',
    },
    {
      'id': 'k4',
      'category': 'khalifa',
      'title': 'Ali bin Abi Thalib',
      'subtitle': 'Khalifah IV (35-40 H / 656-661 M)',
      'description':
          'Sepupu sekaligus menantu Rasulullah. Seorang pemuda yang pemberani dan cerdas, dikenal sebagai pintu ilmu pengetahuan.',
      'arabicName': 'علي بن أبي طالب',
    },
  ];

  final List<Map<String, dynamic>> _companionsData = [
    {
      'id': 'c1',
      'category': 'companion',
      'title': 'Abdurrahman bin Auf',
      'subtitle': 'Bisnis Berkah',
      'description':
          'Seorang pebisnis ulung yang sangat dermawan. Beliau termasuk salah satu dari 10 sahabat yang dijamin masuk surga.',
      'arabicName': 'عبد الرحمن بن عوف',
    },
    {
      'id': 'c2',
      'category': 'companion',
      'title': 'Sa\'ad bin Abi Waqqash',
      'subtitle': 'Pemanah Ulung',
      'description':
          'Sahabat yang pertama kali melepaskan anak panah di jalan Allah. Penakluk kekaisaran Persia di perang Qadisiyah.',
      'arabicName': 'سعد بن أبي وقاص',
    },
  ];
}
