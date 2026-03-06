import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_client.dart';
import '../services/storage_service.dart';
import 'widgets/custom_background.dart';
import 'main_screen.dart';

class PaymentScreen extends StatefulWidget {
  final bool isActivation;
  const PaymentScreen({super.key, this.isActivation = false});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  final TextEditingController _amountController = TextEditingController();
  bool _isLoading = false;
  bool _isCheckingStatus = false;
  String _selectedMethod = 'QR'; // QR or VA
  String _selectedBank = 'BCA';
  String _paymentGate = 'Prismalink'; // Prismalink or Manual

  // Payment result state
  String? _vaNumber;
  String? _bankCode;
  String? _qrString;
  String? _refNo;
  String? _expiredAt;
  int? _paymentAmount;
  bool _paymentCreated = false;

  final List<Map<String, String>> _banks = [
    {'code': 'BCA', 'name': 'BCA'},
    {'code': 'MANDIRI', 'name': 'Mandiri'},
    {'code': 'BNI', 'name': 'BNI'},
    {'code': 'BRI', 'name': 'BRI'},
    {'code': 'PERMATA', 'name': 'Permata'},
  ];

  Future<void> _checkActivationStatus() async {
    setState(() => _isCheckingStatus = true);
    try {
      final response = await ApiClient().client.get('/payments/status');
      if (response.data['success']) {
        final rawActive = response.data['data']['is_active'];
        final isActive = (rawActive == 1 || rawActive == true);

        if (isActive) {
          await StorageService.saveActiveStatus(true);
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text("Akun lu udah aktif wok! Selamat berburu!"),
                backgroundColor: Colors.green,
              ),
            );
            Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (context) => const MainScreen()),
              (route) => false,
            );
          }
        } else {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                  content: Text("Belum masuk wok pembayarannya, sabar ya!")),
            );
          }
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Gagal ngecek status: ${e.toString()}")),
        );
      }
    } finally {
      if (mounted) setState(() => _isCheckingStatus = false);
    }
  }

  Future<void> _createPaymentLink() async {
    if (_amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Masukkan nominal seikhlasnya wok!")),
      );
      return;
    }
    final amount = int.tryParse(_amountController.text);
    if (amount == null || amount < 1000) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Minimal infaq Rp 1.000 ya wok!")),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final response =
          await ApiClient().client.post('/payments/create-link', data: {
        'amount': amount,
        'payment_method': _selectedMethod,
        'bank_code': _selectedBank,
      });

      if (response.data['success'] == true) {
        final data = response.data['data'] as Map<String, dynamic>;
        setState(() {
          _paymentCreated = true;
          _refNo = data['ref_no'];
          _paymentAmount = data['amount'] is int
              ? data['amount']
              : int.tryParse(data['amount'].toString());
          _expiredAt = data['expired_at'];
          _vaNumber = data['va_number'];
          _bankCode = data['bank_code'];
          _qrString = data['qr_string'];
        });
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
                content: Text(
                    "Gagal: ${response.data['message'] ?? 'Error tidak diketahui'}")),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Error: ${e.toString()}")),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _copyToClipboard(String text) {
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
          content: Text("Disalin ke clipboard!"),
          backgroundColor: Colors.green),
    );
  }

  void _launchWA() async {
    // 1. Catat manual payment di database auth API
    setState(() => _isLoading = true);
    try {
      final amount = int.tryParse(_amountController.text) ?? 0;
      if (amount < 1000) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Minimal infaq Rp 1.000 ya wok!")),
          );
        }
        return;
      }

      final response =
          await ApiClient().client.post('/payments/manual-notify', data: {
        'amount': amount,
      });

      if (!response.data['success']) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
                content: Text(
                    "Gagal mencatat pembayaran: ${response.data['message']}")),
          );
        }
        return;
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Error mencatat pembayaran: $e")),
        );
      }
      return;
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }

    // 2. Lanjut buka WhatsApp
    const phone = "6281234567890"; // Dummy admin
    const String message =
        "Halo Admin, saya mau konfirmasi pembayaran aktivasi akun Muslim App (Manual Transfer). Email saya: [isikan email]";
    final url =
        Uri.parse("https://wa.me/$phone?text=${Uri.encodeComponent(message)}");
    if (await canLaunchUrl(url)) {
      await launchUrl(url);
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Tidak dapat membuka WhatsApp")));
      }
    }
  }

  Widget _gateCard(String value, String title, IconData icon, String subtitle) {
    final selected = _paymentGate == value;
    return GestureDetector(
      onTap: () => setState(() => _paymentGate = value),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: selected ? const Color(0xFF0D8ABC) : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: selected ? const Color(0xFF0D8ABC) : Colors.grey.shade300,
              width: 2),
          boxShadow: selected
              ? [
                  BoxShadow(
                      color: const Color(0xFF0D8ABC).withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4))
                ]
              : [],
        ),
        child: Column(
          children: [
            Icon(icon,
                color: selected ? Colors.white : const Color(0xFF0D8ABC),
                size: 32),
            const SizedBox(height: 6),
            Text(title,
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold,
                    color: selected ? Colors.white : Colors.black87,
                    fontSize: 13)),
            Text(subtitle,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    color: selected ? Colors.white70 : Colors.grey[600]),
                textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  Widget _buildBankManualInfo(String bank, String acc, String name) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: Colors.orange.withOpacity(0.3)),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(bank,
                    style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.bold,
                        color: Colors.orange[800])),
                Text(acc,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1.5)),
                Text(name,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 12, color: Colors.grey[600])),
              ],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.copy, color: Colors.orange),
            onPressed: () => _copyToClipboard(acc),
          )
        ],
      ),
    );
  }

  Widget _buildPaymentResult() {
    return Column(
      children: [
        // Header sukses
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.green.withOpacity(0.1),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Colors.green.withOpacity(0.4)),
          ),
          child: Row(
            children: [
              const Icon(Icons.check_circle, color: Colors.green, size: 28),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text("Link Pembayaran Dibuat!",
                        style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.bold,
                            color: Colors.green[800])),
                    Text(
                      "Nominal: Rp ${_paymentAmount != null ? _formatNumber(_paymentAmount!) : '-'}",
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 13, color: Colors.green[700]),
                    ),
                    if (_expiredAt != null)
                      Text("Expired: $_expiredAt",
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 11, color: Colors.orange[700])),
                  ],
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 20),

        // VA Number
        if (_vaNumber != null) ...[
          Text("Nomor Virtual Account",
              style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700, fontSize: 15)),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.blue.withOpacity(0.3)),
              boxShadow: [
                BoxShadow(
                    color: Colors.blue.withOpacity(0.08),
                    blurRadius: 12,
                    offset: const Offset(0, 4))
              ],
            ),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.account_balance,
                        color: Color(0xFF0D8ABC), size: 20),
                    const SizedBox(width: 8),
                    Text("Bank $_bankCode",
                        style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w600,
                            color: Colors.grey[700])),
                  ],
                ),
                const SizedBox(height: 12),
                Text(_vaNumber!,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 26,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 2,
                        color: const Color(0xFF0D8ABC))),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton.icon(
                    onPressed: () => _copyToClipboard(_vaNumber!),
                    icon: const Icon(Icons.copy, size: 18),
                    label: Text("Salin Nomor VA",
                        style: GoogleFonts.plusJakartaSans()),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: Color(0xFF0D8ABC)),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 8),
          Text("Transfer tepat sesuai nominal. Jangan lebih, jangan kurang.",
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 12, color: Colors.orange[700]),
              textAlign: TextAlign.center),
        ],

        // QR Code
        if (_qrString != null) ...[
          Text("QR Code QRIS",
              style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700, fontSize: 15)),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.blue.withOpacity(0.3)),
            ),
            child: Column(
              children: [
                const Icon(Icons.qr_code_2, size: 80, color: Color(0xFF0D8ABC)),
                const SizedBox(height: 12),
                Text("Scan QR dengan aplikasi apapun",
                    style: GoogleFonts.plusJakartaSans(
                        color: Colors.grey[600], fontSize: 13)),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton.icon(
                    onPressed: () => _copyToClipboard(_qrString!),
                    icon: const Icon(Icons.copy, size: 18),
                    label: Text("Salin QR String",
                        style: GoogleFonts.plusJakartaSans()),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: Color(0xFF0D8ABC)),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],

        const SizedBox(height: 24),

        // Tombol cek status
        SizedBox(
          width: double.infinity,
          height: 52,
          child: ElevatedButton.icon(
            onPressed: _isCheckingStatus ? null : _checkActivationStatus,
            icon: _isCheckingStatus
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                        strokeWidth: 2, color: Colors.white))
                : const Icon(Icons.refresh),
            label: Text("Cek Status Aktivasi",
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold, fontSize: 16)),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.green,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12)),
            ),
          ),
        ),

        const SizedBox(height: 12),

        // Tombol buat ulang
        SizedBox(
          width: double.infinity,
          height: 44,
          child: OutlinedButton(
            onPressed: () => setState(() {
              _paymentCreated = false;
              _vaNumber = _qrString = _refNo = _expiredAt = _bankCode = null;
            }),
            style: OutlinedButton.styleFrom(
              side: const BorderSide(color: Color(0xFF0D8ABC)),
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12)),
            ),
            child: Text("Ganti Metode / Nominal",
                style: GoogleFonts.plusJakartaSans(
                    color: const Color(0xFF0D8ABC))),
          ),
        ),
      ],
    );
  }

  Widget _buildPaymentForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Info box
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.blue.withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.blue.withOpacity(0.3)),
          ),
          child: Row(
            children: [
              const Icon(Icons.info_outline, color: Colors.blue),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  widget.isActivation
                      ? "Infaq seikhlasnya buat aktivasi akun hunter lu. Minimal Rp 1.000."
                      : "Infaq untuk operasional server & pengembangan aplikasi.",
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 13, color: Colors.blue[900]),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 24),

        // Tipe Pembayaran (Gateway)
        Text("Tipe Pembayaran",
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700, fontSize: 15)),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: _gateCard(
                'Prismalink',
                'Otomatis',
                Icons.bolt,
                'Verifikasi Instan',
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _gateCard(
                'Manual',
                'Manual',
                Icons.handshake,
                'Konfirmasi Admin',
              ),
            ),
          ],
        ),
        // Nominal
        Text("Nominal Infaq (Rp)",
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700, fontSize: 15)),
        const SizedBox(height: 10),
        TextField(
          controller: _amountController,
          keyboardType: TextInputType.number,
          decoration: InputDecoration(
            hintText: "Contoh: 10000",
            filled: true,
            fillColor: Colors.white,
            border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide.none),
            prefixIcon: const Icon(Icons.money),
          ),
        ),
        const SizedBox(height: 24),

        if (_paymentGate == 'Manual') ...[
          Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.orange.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.orange.withOpacity(0.3)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text("Transfer Manual",
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: Colors.orange[900])),
                  const SizedBox(height: 8),
                  Text("Silakan transfer seikhlasnya ke rekening berikut:",
                      style: GoogleFonts.plusJakartaSans(fontSize: 13)),
                  const SizedBox(height: 12),
                  _buildBankManualInfo("BCA", "1234567890", "A/N Muslim App"),
                  const SizedBox(height: 8),
                  _buildBankManualInfo(
                      "MANDIRI", "0987654321", "A/N Muslim App"),
                  const SizedBox(height: 8),
                  _buildBankManualInfo(
                      "DANA / GOPAY", "081234567890", "A/N Muslim App"),
                  const SizedBox(height: 16),
                  Text(
                      "Setelah transfer, konfirmasi ke Admin via WhatsApp beserta bukti transfer dan email akun lu.",
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 12, color: Colors.orange[800])),
                  const SizedBox(height: 16),
                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton.icon(
                      onPressed: _launchWA,
                      icon: const Icon(Icons.send, color: Colors.white),
                      label: Text("Konfirmasi via WhatsApp",
                          style: GoogleFonts.plusJakartaSans(
                              fontWeight: FontWeight.bold,
                              color: Colors.white)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                  )
                ],
              ))
        ] else ...[
          // Metode pembayaran
          Text("Metode Pembayaran",
              style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700, fontSize: 15)),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: _methodCard(
                  'QR',
                  'QRIS',
                  Icons.qr_code,
                  'Scan QR berbagai bank',
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _methodCard(
                  'VA',
                  'Virtual Account',
                  Icons.account_balance,
                  'Transfer via VA bank',
                ),
              ),
            ],
          ),

          if (_selectedMethod == 'VA') ...[
            const SizedBox(height: 16),
            Text("Pilih Bank",
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w700, fontSize: 15)),
            const SizedBox(height: 10),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _banks.map((bank) {
                final selected = _selectedBank == bank['code'];
                return GestureDetector(
                  onTap: () => setState(() => _selectedBank = bank['code']!),
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 10),
                    decoration: BoxDecoration(
                      color: selected ? const Color(0xFF0D8ABC) : Colors.white,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                          color: selected
                              ? const Color(0xFF0D8ABC)
                              : Colors.grey.shade300),
                    ),
                    child: Text(bank['name']!,
                        style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w600,
                            color: selected ? Colors.white : Colors.black87)),
                  ),
                );
              }).toList(),
            ),
          ],

          const SizedBox(height: 32),

          SizedBox(
            width: double.infinity,
            height: 56,
            child: ElevatedButton(
              onPressed: _isLoading ? null : _createPaymentLink,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF0D8ABC),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12)),
              ),
              child: _isLoading
                  ? const CircularProgressIndicator(color: Colors.white)
                  : Text(
                      _selectedMethod == 'QR'
                          ? "Generate QR QRIS"
                          : "Generate Nomor VA",
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold, fontSize: 16)),
            ),
          ),
        ],

        if (widget.isActivation) ...[
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
            height: 48,
            child: OutlinedButton(
              onPressed: _isCheckingStatus ? null : _checkActivationStatus,
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: Color(0xFF0D8ABC)),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12)),
              ),
              child: _isCheckingStatus
                  ? const CircularProgressIndicator()
                  : Text("Cek Status Aktivasi",
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold,
                          fontSize: 15,
                          color: const Color(0xFF0D8ABC))),
            ),
          ),
        ],
      ],
    );
  }

  Widget _methodCard(
      String value, String title, IconData icon, String subtitle) {
    final selected = _selectedMethod == value;
    return GestureDetector(
      onTap: () => setState(() => _selectedMethod = value),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: selected ? const Color(0xFF0D8ABC) : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: selected ? const Color(0xFF0D8ABC) : Colors.grey.shade300,
              width: 2),
          boxShadow: selected
              ? [
                  BoxShadow(
                      color: const Color(0xFF0D8ABC).withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4))
                ]
              : [],
        ),
        child: Column(
          children: [
            Icon(icon,
                color: selected ? Colors.white : const Color(0xFF0D8ABC),
                size: 32),
            const SizedBox(height: 6),
            Text(title,
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold,
                    color: selected ? Colors.white : Colors.black87,
                    fontSize: 13)),
            Text(subtitle,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    color: selected ? Colors.white70 : Colors.grey[600]),
                textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  String _formatNumber(int n) {
    return n
        .toString()
        .replaceAllMapped(RegExp(r'(\d)(?=(\d{3})+(?!\d))'), (m) => '${m[1]}.');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      appBar: AppBar(
        title: Text(
          widget.isActivation ? "Aktivasi Akun Hunter" : "Infaq Seikhlasnya",
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        elevation: 0,
        automaticallyImplyLeading: true,
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          SingleChildScrollView(
            padding: const EdgeInsets.all(24.0),
            child:
                _paymentCreated ? _buildPaymentResult() : _buildPaymentForm(),
          ),
        ],
      ),
    );
  }
}
