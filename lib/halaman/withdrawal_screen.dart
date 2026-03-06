import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/user_profile.dart';
import '../services/affiliate_service.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';

class WithdrawalScreen extends StatefulWidget {
  final UserProfile profile;
  const WithdrawalScreen({super.key, required this.profile});

  @override
  State<WithdrawalScreen> createState() => _WithdrawalScreenState();
}

class _WithdrawalScreenState extends State<WithdrawalScreen> {
  final AffiliateService _affiliateService = AffiliateService();
  final _formKey = GlobalKey<FormState>();
  final _amountController = TextEditingController();
  final _bankController = TextEditingController();
  final _noRekController = TextEditingController();
  final _namaController = TextEditingController();
  bool _isLoading = false;

  Future<void> _submitWd() async {
    if (!_formKey.currentState!.validate()) return;

    final amount = int.tryParse(_amountController.text);
    if (amount == null || amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Nominal harus valid wok!")),
      );
      return;
    }

    if (amount > widget.profile.balance) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Saldo lu kaga cukup wok!")),
      );
      return;
    }

    setState(() => _isLoading = true);
    try {
      final result = await _affiliateService.withdraw(
        amount: amount,
        bankName: _bankController.text,
        accountNumber: _noRekController.text,
        accountName: _namaController.text,
      );

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message']),
            backgroundColor: result['success'] ? Colors.green : Colors.red,
          ),
        );
        if (result['success']) {
          Navigator.pop(context, true);
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content: Text("Gagal: ${e.toString()}"),
              backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      appBar: AppBar(
        title: Text("Tarik Saldo Komisi",
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w900,
                fontSize: 18,
                color: Colors.white)),
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon:
              const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [PremiumColor.primary, PremiumColor.accent],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: PremiumColor.primary.withOpacity(0.3),
                          blurRadius: 15,
                          offset: const Offset(0, 8),
                        )
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text("DOMPET KOMISI",
                                style: GoogleFonts.plusJakartaSans(
                                    color: Colors.white70,
                                    fontSize: 10,
                                    fontWeight: FontWeight.w900,
                                    letterSpacing: 2)),
                            const Icon(Icons.wallet_rounded,
                                color: Colors.white30),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          "Rp ${NumberFormat.decimalPattern('id').format(widget.profile.balance)}",
                          style: GoogleFonts.plusJakartaSans(
                            color: Colors.white,
                            fontSize: 32,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text("Dapatkan komisi dari setiap referral aktif",
                            style: GoogleFonts.plusJakartaSans(
                                color: Colors.white.withOpacity(0.6),
                                fontSize: 11)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 32),
                  _buildSectionTitle(
                      "Informasi Penarikan", Icons.payments_rounded),
                  const SizedBox(height: 16),
                  _buildLabel("Nominal Penarikan"),
                  _buildInput(_amountController, "Minimal Rp 10.000",
                      isNumber: true, icon: Icons.money_rounded),
                  const SizedBox(height: 20),
                  _buildLabel("Tujuan Pengiriman"),
                  _buildInput(
                      _bankController, "Nama Bank / E-Wallet (BCA, Dana, dll)",
                      icon: Icons.account_balance_rounded),
                  const SizedBox(height: 20),
                  _buildLabel("Nomor Rekening"),
                  _buildInput(_noRekController, "Masukkan nomor rekening/HP",
                      icon: Icons.numbers_rounded),
                  const SizedBox(height: 20),
                  _buildLabel("Atas Nama"),
                  _buildInput(_namaController, "Nama lengkap penerima",
                      icon: Icons.person_rounded),
                  const SizedBox(height: 48),
                  SizedBox(
                    width: double.infinity,
                    height: 60,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _submitWd,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: PremiumColor.primary,
                        foregroundColor: Colors.white,
                        elevation: 4,
                        shadowColor: PremiumColor.primary.withOpacity(0.4),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16)),
                      ),
                      child: _isLoading
                          ? const CircularProgressIndicator(color: Colors.white)
                          : Text("KONFIRMASI PENARIKAN",
                              style: GoogleFonts.plusJakartaSans(
                                  fontWeight: FontWeight.w900,
                                  fontSize: 14,
                                  letterSpacing: 1.5)),
                    ),
                  ),
                  const SizedBox(height: 24),
                  Center(
                    child: Text(
                      "Proses penarikan memakan waktu 1-3 hari kerja.",
                      style: GoogleFonts.plusJakartaSans(
                          color: PremiumColor.slate400, fontSize: 11),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 20, color: PremiumColor.primary),
        const SizedBox(width: 8),
        Text(title,
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w800,
                fontSize: 16,
                color: PremiumColor.slate800)),
      ],
    );
  }

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0, left: 4),
      child: Text(text,
          style: GoogleFonts.plusJakartaSans(
              fontWeight: FontWeight.w700,
              fontSize: 13,
              color: PremiumColor.slate600)),
    );
  }

  Widget _buildInput(TextEditingController controller, String hint,
      {bool isNumber = false, IconData? icon}) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: TextFormField(
        controller: controller,
        keyboardType: isNumber ? TextInputType.number : TextInputType.text,
        style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w600, fontSize: 15),
        decoration: InputDecoration(
          prefixIcon: icon != null
              ? Icon(icon, color: PremiumColor.slate400, size: 20)
              : null,
          hintText: hint,
          hintStyle: GoogleFonts.plusJakartaSans(
              color: PremiumColor.slate200, fontSize: 14),
          border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: BorderSide.none),
          contentPadding: const EdgeInsets.all(20),
        ),
        validator: (v) => v!.isEmpty ? "Bidang ini wajib diisi wok!" : null,
      ),
    );
  }
}
