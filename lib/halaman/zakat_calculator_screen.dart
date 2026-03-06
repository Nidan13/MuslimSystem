import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/premium_color.dart';
import 'zakat_detail_screen.dart';

class ZakatCalculatorScreen extends StatelessWidget {
  const ZakatCalculatorScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final List<Map<String, dynamic>> zakatTypes = [
      {
        'title': 'Zakat Pertanian\nTanaman Pangan',
        'icon': '🌾',
        'type': 'agricultural',
      },
      {
        'title': 'Zakat Perdagangan',
        'icon': '🏪',
        'type': 'trade',
      },
      {
        'title': 'Zakat Simpanan\nEmas, Perak, dan\nPerhiasan',
        'icon': '💰',
        'type': 'gold',
      },
      {
        'title': 'Zakat Tambak',
        'icon': '🦐',
        'type': 'trade',
      },
      {
        'title': 'Zakat Tanaman\nProduktif',
        'icon': '🌶️',
        'type': 'agricultural',
      },
      {
        'title': 'Zakat Peternakan',
        'icon': '🐔',
        'type': 'cattle',
      },
      {
        'title': 'Zakat Perusahaan',
        'icon': '💼',
        'type': 'trade',
      },
      {
        'title': 'Zakat Properti',
        'icon': '🏢',
        'type': 'trade',
      },
      {
        'title': 'Zakat Profesi',
        'icon': '👨‍⚕️',
        'type': 'profession',
      },
    ];

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              color: Colors.white, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          "Kalkulator Zakat",
          style: GoogleFonts.plusJakartaSans(
            color: Colors.white,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: GridView.builder(
        padding: const EdgeInsets.all(20),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 15,
          mainAxisSpacing: 15,
          childAspectRatio: 1.0,
        ),
        itemCount: zakatTypes.length,
        itemBuilder: (context, index) {
          final item = zakatTypes[index];
          return _buildZakatCard(context, item);
        },
      ),
    );
  }

  Widget _buildZakatCard(BuildContext context, Map<String, dynamic> item) {
    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => ZakatDetailScreen(
              title: item['title'],
              icon: item['icon'],
              type: item['type'],
            ),
          ),
        );
      },
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
          border: Border.all(color: Colors.black.withOpacity(0.02)),
        ),
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Text(
                item['title'],
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  color: Colors.black87,
                  height: 1.3,
                ),
                maxLines: 3,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(height: 8),
            Align(
              alignment: Alignment.bottomRight,
              child: Text(
                item['icon'],
                style: const TextStyle(fontSize: 32),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
