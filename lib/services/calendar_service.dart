import 'dart:convert';
import 'package:http/http.dart' as http;

class CalendarService {
  final String _aladhanBaseUrl = "https://api.aladhan.com/v1";

  Future<Map<String, dynamic>> getMonthCalendar(int year, int month) async {
    try {
      final response = await http.get(
        Uri.parse("$_aladhanBaseUrl/gregorianCalendar/$month/$year"),
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (e) {
      print("Error fetching Aladhan calendar: $e");
    }
    return {};
  }

  Future<List<Map<String, dynamic>>> getIslamicEvents(
      int year, int month) async {
    List<Map<String, dynamic>> events = [];

    // Indonesian National Holidays API (Secondary)
    try {
      final response = await http.get(Uri.parse(
          "https://day-off-api.vercel.app/api/holidays?year=$year&month=$month"));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        for (var item in data) {
          // Normalize API data to our format
          events.add({
            'day': DateTime.parse(item['date']).day,
            'month': DateTime.parse(item['date']).month,
            'year': year,
            'title': item['name'],
            'isHoliday': true,
            'type': 'Libur Nasional',
            'color': 'red',
            'hijri_day': '',
            'hijri_month': '',
            'hijri_year': '',
          });
        }
      }
    } catch (e) {
      print("Error fetching national holidays: $e");
    }

    final List<Map<String, dynamic>> masterHolidays = [
      // 2025 - Libur Nasional
      {
        'd': 1,
        'm': 1,
        'y': 2025,
        't': 'Tahun Baru 2025 Masehi',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 27,
        'm': 1,
        'y': 2025,
        't': 'Isra Mikraj Nabi Muhammad SAW',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 29,
        'm': 1,
        'y': 2025,
        't': 'Tahun Baru Imlek 2576 Kongzili',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 29,
        'm': 3,
        'y': 2025,
        't': 'Hari Suci Nyepi (Tahun Baru Saka 1947)',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 31,
        'm': 3,
        'y': 2025,
        't': 'Hari Raya Idul Fitri 1446 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 1,
        'm': 4,
        'y': 2025,
        't': 'Hari Raya Idul Fitri 1446 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 18,
        'm': 4,
        'y': 2025,
        't': 'Wafat Yesus Kristus',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 20,
        'm': 4,
        'y': 2025,
        't': 'Hari Kebangkitan Yesus Kristus (Paskah)',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 1,
        'm': 5,
        'y': 2025,
        't': 'Hari Buruh Internasional',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 12,
        'm': 5,
        'y': 2025,
        't': 'Hari Raya Waisak 2569 BE',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 29,
        'm': 5,
        'y': 2025,
        't': 'Kenaikan Yesus Kristus',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 1,
        'm': 6,
        'y': 2025,
        't': 'Hari Lahir Pancasila',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 6,
        'm': 6,
        'y': 2025,
        't': 'Hari Raya Idul Adha 1446 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 27,
        'm': 6,
        'y': 2025,
        't': '1 Muharam Tahun Baru Islam 1447 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 17,
        'm': 8,
        'y': 2025,
        't': 'Hari Kemerdekaan RI ਕੇ-80',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 5,
        'm': 9,
        'y': 2025,
        't': 'Maulid Nabi Muhammad SAW',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 25,
        'm': 12,
        'y': 2025,
        't': 'Hari Raya Natal',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },

      // 2025 - Cuti Bersama
      {
        'd': 28,
        'm': 1,
        'y': 2025,
        't': 'Cuti Bersama Tahun Baru Imlek',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 28,
        'm': 3,
        'y': 2025,
        't': 'Cuti Bersama Hari Suci Nyepi',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 2,
        'm': 4,
        'y': 2025,
        't': 'Cuti Bersama Idul Fitri 1446 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 3,
        'm': 4,
        'y': 2025,
        't': 'Cuti Bersama Idul Fitri 1446 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 4,
        'm': 4,
        'y': 2025,
        't': 'Cuti Bersama Idul Fitri 1446 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 7,
        'm': 4,
        'y': 2025,
        't': 'Cuti Bersama Idul Fitri 1446 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 13,
        'm': 5,
        'y': 2025,
        't': 'Cuti Bersama Hari Raya Waisak',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 30,
        'm': 5,
        'y': 2025,
        't': 'Cuti Bersama Kenaikan Yesus Kristus',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 9,
        'm': 6,
        'y': 2025,
        't': 'Cuti Bersama Hari Raya Idul Adha 1446 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 26,
        'm': 12,
        'y': 2025,
        't': 'Cuti Bersama Hari Raya Natal',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },

      // 2026 - Libur Nasional
      {
        'd': 1,
        'm': 1,
        'y': 2026,
        't': 'Tahun Baru 2026 Masehi',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 16,
        'm': 1,
        'y': 2026,
        't': 'Isra Mikraj Nabi Muhammad SAW',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 17,
        'm': 2,
        'y': 2026,
        't': 'Tahun Baru Imlek 2577 Kongzili',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 19,
        'm': 3,
        'y': 2026,
        't': 'Hari Suci Nyepi (Tahun Baru Saka 1948)',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 21,
        'm': 3,
        'y': 2026,
        't': 'Hari Raya Idul Fitri 1447 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 22,
        'm': 3,
        'y': 2026,
        't': 'Hari Raya Idul Fitri 1447 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 3,
        'm': 4,
        'y': 2026,
        't': 'Wafat Yesus Kristus',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 5,
        'm': 4,
        'y': 2026,
        't': 'Hari Kebangkitan Yesus Kristus (Paskah)',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 1,
        'm': 5,
        'y': 2026,
        't': 'Hari Buruh Internasional',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 14,
        'm': 5,
        'y': 2026,
        't': 'Kenaikan Yesus Kristus',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 27,
        'm': 5,
        'y': 2026,
        't': 'Hari Raya Idul Adha 1447 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 31,
        'm': 5,
        'y': 2026,
        't': 'Hari Raya Waisak 2570 BE',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 1,
        'm': 6,
        'y': 2026,
        't': 'Hari Lahir Pancasila',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 16,
        'm': 6,
        'y': 2026,
        't': '1 Muharam Tahun Baru Islam 1448 Hijriah',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 17,
        'm': 8,
        'y': 2026,
        't': 'Hari Kemerdekaan RI ਕੇ-81',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 25,
        'm': 8,
        'y': 2026,
        't': 'Maulid Nabi Muhammad SAW',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },
      {
        'd': 25,
        'm': 12,
        'y': 2026,
        't': 'Hari Raya Natal',
        'h': true,
        'type': 'Libur Nasional',
        'color': 'red'
      },

      // 2026 - Cuti Bersama
      {
        'd': 16,
        'm': 2,
        'y': 2026,
        't': 'Cuti Bersama Tahun Baru Imlek',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 18,
        'm': 3,
        'y': 2026,
        't': 'Cuti Bersama Hari Suci Nyepi',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 20,
        'm': 3,
        'y': 2026,
        't': 'Cuti Bersama Idul Fitri 1447 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 23,
        'm': 3,
        'y': 2026,
        't': 'Cuti Bersama Idul Fitri 1447 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 24,
        'm': 3,
        'y': 2026,
        't': 'Cuti Bersama Idul Fitri 1447 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 15,
        'm': 5,
        'y': 2026,
        't': 'Cuti Bersama Kenaikan Yesus Kristus',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 28,
        'm': 5,
        'y': 2026,
        't': 'Cuti Bersama Idul Adha 1447 H',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },
      {
        'd': 24,
        'm': 12,
        'y': 2026,
        't': 'Cuti Bersama Hari Raya Natal',
        'h': true,
        'type': 'Cuti Bersama',
        'color': 'orange'
      },

      // Islamic Events Markers (Non-Holiday)
      {
        'd': 3,
        'm': 2,
        'y': 2026,
        't': 'Malam Nisfu Sya\'ban',
        'h': false,
        'type': 'Momen Islam',
        'color': 'green',
        'hd': 15,
        'hm': 'Sya\'ban'
      },
      {
        'd': 18,
        'm': 2,
        'y': 2026,
        't': 'Awal Bulan Ramadhan 1447 H',
        'h': false,
        'type': 'Momen Islam',
        'color': 'green',
        'hd': 1,
        'hm': 'Ramadhan'
      },
      {
        'd': 6,
        'm': 3,
        'y': 2026,
        't': 'Nuzulul Qur\'an 1447 H',
        'h': false,
        'type': 'Momen Islam',
        'color': 'green',
        'hd': 17,
        'hm': 'Ramadhan'
      },
      {
        'd': 1,
        'm': 2,
        'y': 2025,
        't': 'Malam Nisfu Sya\'ban (Est)',
        'h': false,
        'type': 'Momen Islam',
        'color': 'green',
        'hd': 15,
        'hm': 'Sya\'ban'
      },
      {
        'd': 1,
        'm': 3,
        'y': 2025,
        't': 'Awal Ramadhan 1446 H (Est)',
        'h': false,
        'type': 'Momen Islam',
        'color': 'green',
        'hd': 1,
        'hm': 'Ramadhan'
      },
    ];

    for (var ev in masterHolidays) {
      if (ev['y'] == year && ev['m'] == month) {
        // Prevent duplicates from API by checking day & month
        if (!events.any((e) => e['day'] == ev['d'] && e['title'] == ev['t'])) {
          events.add({
            'day': ev['d'],
            'month': ev['m'],
            'year': ev['y'],
            'title': ev['t'],
            'isHoliday': ev['h'] ?? false,
            'type': ev['type'] ?? 'Event',
            'color': ev['color'] ?? 'blue',
            'hijri_day': ev['hd'] ?? '',
            'hijri_month': ev['hm'] ?? '',
            'hijri_year': (ev['y'] == 2025
                ? ((ev['m'] ?? 0) < 7 ? 1446 : 1447)
                : ((ev['m'] ?? 0) < 7 ? 1447 : 1448)),
          });
        }
      }
    }

    events.sort((a, b) => (a['day'] as int).compareTo(b['day'] as int));
    return events;
  }
}
