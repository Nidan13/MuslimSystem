import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../theme/premium_color.dart';

class ZakatDetailScreen extends StatefulWidget {
  final String title;
  final String icon;
  final String type; // agricultural, trade, gold, cattle, profession, etc.

  const ZakatDetailScreen({
    super.key,
    required this.title,
    required this.icon,
    required this.type,
  });

  @override
  State<ZakatDetailScreen> createState() => _ZakatDetailScreenState();
}

class _ZakatDetailScreenState extends State<ZakatDetailScreen> {
  final TextEditingController _primaryController = TextEditingController();
  final TextEditingController _secondaryController = TextEditingController();
  final TextEditingController _goldPriceController =
      TextEditingController(text: "1300000"); // Approx gold price

  double _result = 0;
  bool _isNisabReached = false;
  String _nisabInfo = "";

  // Agricultural specific
  bool _isIrrigated = true;

  void _calculate() {
    double primaryValue =
        double.tryParse(_primaryController.text.replaceAll('.', '')) ?? 0;
    double secondaryValue =
        double.tryParse(_secondaryController.text.replaceAll('.', '')) ?? 0;
    double goldPrice =
        double.tryParse(_goldPriceController.text.replaceAll('.', '')) ?? 0;

    setState(() {
      switch (widget.type) {
        case 'profession':
          double monthlyNisab = (85 * goldPrice) / 12;
          _isNisabReached = primaryValue >= monthlyNisab;
          _result = _isNisabReached ? primaryValue * 0.025 : 0;
          _nisabInfo =
              "Nisab: Rp ${NumberFormat('#,###', 'id_ID').format(monthlyNisab)} / bulan";
          break;
        case 'gold':
          _isNisabReached = primaryValue >= 85;
          _result = _isNisabReached ? (primaryValue * goldPrice) * 0.025 : 0;
          _nisabInfo = "Nisab: 85 Gram Emas";
          break;
        case 'trade':
          double totalHarta = primaryValue + secondaryValue;
          double annualNisab = 85 * goldPrice;
          _isNisabReached = totalHarta >= annualNisab;
          _result = _isNisabReached ? totalHarta * 0.025 : 0;
          _nisabInfo =
              "Nisab: Rp ${NumberFormat('#,###', 'id_ID').format(annualNisab)} (85g Emas)";
          break;
        case 'agricultural':
          // Nisab: 5 Wasaq = 653 kg beras
          _isNisabReached = primaryValue >= 653;
          double rate = _isIrrigated ? 0.05 : 0.10;
          _result = _isNisabReached ? primaryValue * rate : 0;
          _nisabInfo = "Nisab: 653 Kg Beras";
          break;
        case 'cattle':
          // Simplify for UI: using Goat as default or Sapi logic
          if (_isIrrigated) {
            // Using this flag as "Sapi" toggle for simplicity or add another
            _isNisabReached = primaryValue >= 30;
            _result = _isNisabReached
                ? (primaryValue >= 40 ? 40 : 30)
                : 0; // Returning threshold for info
            _nisabInfo = "Nisab Sapi: 30 Ekor";
          } else {
            _isNisabReached = primaryValue >= 40;
            _result = _isNisabReached ? (primaryValue >= 121 ? 121 : 40) : 0;
            _nisabInfo = "Nisab Kambing: 40 Ekor";
          }
          break;
        default:
          _result = primaryValue * 0.025;
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        title: Text(widget.title.replaceAll('\n', ' '),
            style: GoogleFonts.plusJakartaSans(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: Colors.white)),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              size: 20, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildHeaderCard(),
            const SizedBox(height: 32),
            _buildInputSection(),
            const SizedBox(height: 40),
            _buildResultSection(),
            if (_result > 0 ||
                (_primaryController.text.isNotEmpty && !_isNisabReached))
              _buildCalculationDetails(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeaderCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: PremiumColor.primary.withOpacity(0.05),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: PremiumColor.primary.withOpacity(0.1)),
      ),
      child: Row(
        children: [
          Text(widget.icon, style: const TextStyle(fontSize: 40)),
          const SizedBox(width: 20),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Ketentuan Zakat",
                  style: GoogleFonts.plusJakartaSans(
                      fontWeight: FontWeight.bold, color: PremiumColor.primary),
                ),
                const SizedBox(height: 4),
                Text(
                  _nisabInfo.isEmpty
                      ? "Sesuai Fatwa & Ketentuan NU Online"
                      : _nisabInfo,
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, color: Colors.black54),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInputSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (widget.type == 'profession') ...[
          _buildTextField("Penghasilan Per Bulan", "Contoh: 10.000.000",
              _primaryController),
        ] else if (widget.type == 'gold') ...[
          _buildTextField("Berat Emas (Gram)", "Contoh: 85", _primaryController,
              prefix: "gr"),
          const SizedBox(height: 20),
          _buildTextField("Harga Emas Per Gram", "Harga pasar saat ini",
              _goldPriceController),
        ] else if (widget.type == 'trade') ...[
          _buildTextField("Modal & Keuntungan Untung", "Uang/Barang dagangan",
              _primaryController),
          const SizedBox(height: 20),
          _buildTextField("Piutang Lancar", "Piutang yang akan dibayar",
              _secondaryController),
        ] else if (widget.type == 'agricultural' ||
            widget.type == 'cattle') ...[
          _buildTextField(
              widget.type == 'cattle'
                  ? "Jumlah Hewan Ternak"
                  : "Hasil Panen (Kg Beras/Gabah)",
              "Contoh: ${widget.type == 'cattle' ? '40' : '1000'}",
              _primaryController,
              prefix: widget.type == 'cattle' ? "ekor" : "kg"),
          const SizedBox(height: 24),
          Text(widget.type == 'cattle' ? "Jenis Hewan:" : "Sistem Pengairan:",
              style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.bold, fontSize: 13)),
          Column(
            children: [
              RadioListTile<bool>(
                title: Text(
                    widget.type == 'cattle' ? "Sapi/Kerbau" : "Irigasi (5%)",
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 13, fontWeight: FontWeight.w600)),
                value: true,
                groupValue: _isIrrigated,
                onChanged: (v) => setState(() => _isIrrigated = v!),
                activeColor: PremiumColor.primary,
                contentPadding: EdgeInsets.zero,
                dense: true,
              ),
              RadioListTile<bool>(
                title: Text(
                    widget.type == 'cattle'
                        ? "Kambing/Domba"
                        : "Tadah Hujan (10%)",
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 13, fontWeight: FontWeight.w600)),
                value: false,
                groupValue: _isIrrigated,
                onChanged: (v) => setState(() => _isIrrigated = v!),
                activeColor: PremiumColor.primary,
                contentPadding: EdgeInsets.zero,
                dense: true,
              ),
            ],
          ),
        ] else ...[
          _buildTextField(
              "Total Harta", "Masukkan nilai rupiah", _primaryController),
        ],
        const SizedBox(height: 32),
        SizedBox(
          width: double.infinity,
          height: 56,
          child: ElevatedButton(
            onPressed: _calculate,
            style: ElevatedButton.styleFrom(
              backgroundColor: PremiumColor.primary,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16)),
              elevation: 0,
            ),
            child: Text("Hitung Zakat",
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold, color: Colors.white)),
          ),
        ),
      ],
    );
  }

  Widget _buildTextField(
      String label, String hint, TextEditingController controller,
      {String prefix = "Rp"}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label,
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.bold, fontSize: 14)),
        const SizedBox(height: 12),
        TextField(
          controller: controller,
          keyboardType: TextInputType.number,
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
          decoration: InputDecoration(
            hintText: hint,
            prefixText: "$prefix ",
            filled: true,
            fillColor: Colors.grey.shade50,
            border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(16),
                borderSide: BorderSide.none),
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          ),
        ),
      ],
    );
  }

  Widget _buildResultSection() {
    if (_result == 0 && !_isNisabReached) {
      if (_primaryController.text.isNotEmpty) {
        return Center(
          child: Text(
            "Harta belum mencapai Nisab",
            style: GoogleFonts.plusJakartaSans(
                color: Colors.redAccent, fontWeight: FontWeight.bold),
          ),
        );
      }
      return const SizedBox.shrink();
    }

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient:
            LinearGradient(colors: [PremiumColor.primary, PremiumColor.accent]),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: PremiumColor.primary.withOpacity(0.3),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        children: [
          Text(
            "Zakat yang Wajib Ditunaikan",
            style: GoogleFonts.plusJakartaSans(
                color: Colors.white70,
                fontSize: 12,
                fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 12),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              widget.type == 'agricultural'
                  ? "${NumberFormat('#,###', 'id_ID').format(_result)} Kg Beras"
                  : widget.type == 'cattle'
                      ? "Wajib: 1 Ekor ${_isIrrigated ? 'Sapi' : 'Kambing'}"
                      : "Rp ${NumberFormat('#,###', 'id_ID').format(_result)}",
              style: GoogleFonts.plusJakartaSans(
                  color: Colors.white,
                  fontSize: 32,
                  fontWeight: FontWeight.w900),
            ),
          ),
          const SizedBox(height: 20),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.2),
                borderRadius: BorderRadius.circular(10)),
            child: Text(
              "Alhamdulillah, Anda telah mencapai Nisab",
              style: GoogleFonts.plusJakartaSans(
                  color: Colors.white,
                  fontSize: 10,
                  fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCalculationDetails() {
    return Container(
      margin: const EdgeInsets.only(top: 32),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.analytics_outlined,
                  color: Colors.black87, size: 20),
              const SizedBox(width: 12),
              Text(
                "Rincian Perhitungan",
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                  color: Colors.black87,
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          _buildDetailRow("Kategori", widget.title.replaceAll('\n', ' ')),
          _buildDetailDivider(),
          if (widget.type == 'profession') ...[
            _buildDetailRow("Penghasilan",
                "Rp ${NumberFormat('#,###', 'id_ID').format(double.tryParse(_primaryController.text.replaceAll('.', '')) ?? 0)}"),
            _buildDetailDivider(),
            _buildDetailRow("Ketentuan", "2.5% dari Penghasilan"),
          ] else if (widget.type == 'gold') ...[
            _buildDetailRow("Berat Emas", "${_primaryController.text} gr"),
            _buildDetailDivider(),
            _buildDetailRow("Estimasi Nilai Harta",
                "Rp ${NumberFormat('#,###', 'id_ID').format((double.tryParse(_primaryController.text.replaceAll('.', '')) ?? 0) * (double.tryParse(_goldPriceController.text.replaceAll('.', '')) ?? 0))}"),
            _buildDetailDivider(),
            _buildDetailRow("Ketentuan", "2.5% dari Nilai Emas"),
          ] else if (widget.type == 'trade') ...[
            _buildDetailRow("Total Harta",
                "Rp ${NumberFormat('#,###', 'id_ID').format((double.tryParse(_primaryController.text.replaceAll('.', '')) ?? 0) + (double.tryParse(_secondaryController.text.replaceAll('.', '')) ?? 0))}"),
            _buildDetailDivider(),
            _buildDetailRow("Ketentuan", "2.5% dari Harta Lancar"),
          ] else if (widget.type == 'agricultural') ...[
            _buildDetailRow("Total Panen", "${_primaryController.text} kg"),
            _buildDetailDivider(),
            _buildDetailRow("Sistem Pengairan",
                _isIrrigated ? "Irigasi (5%)" : "Tadah Hujan (10%)"),
          ],
          _buildDetailDivider(),
          _buildDetailRow("Status Nisab",
              _isNisabReached ? "Tercapai ✅" : "Belum Tercapai ❌"),
          if (_isNisabReached) ...[
            _buildDetailDivider(),
            _buildDetailRow(
                "Total Zakat",
                widget.type == 'agricultural'
                    ? "${NumberFormat('#,###', 'id_ID').format(_result)} kg"
                    : widget.type == 'cattle'
                        ? "1 Ekor ${_isIrrigated ? 'Sapi' : 'Kambing'}"
                        : "Rp ${NumberFormat('#,###', 'id_ID').format(_result)}",
                isBold: true,
                color: PremiumColor.primary),
          ],
        ],
      ),
    );
  }

  Widget _buildDetailRow(String label, String value,
      {bool isBold = false, Color color = Colors.black54}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Text(label,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 13, color: Colors.black45)),
          ),
          const SizedBox(width: 8),
          Flexible(
            child: Text(value,
                textAlign: TextAlign.end,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13,
                  fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
                  color: color,
                )),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailDivider() =>
      Divider(height: 24, color: Colors.grey.shade100);
}
