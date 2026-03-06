import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../models/commission.dart';
import '../models/withdrawal.dart';
import '../services/affiliate_service.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';

class CommissionScreen extends StatefulWidget {
  const CommissionScreen({super.key});

  @override
  State<CommissionScreen> createState() => _CommissionScreenState();
}

class _CommissionScreenState extends State<CommissionScreen>
    with SingleTickerProviderStateMixin {
  final AffiliateService _affiliateService = AffiliateService();
  late TabController _tabController;
  List<Commission> _commissions = [];
  List<Withdrawal> _withdrawals = [];
  Map<String, dynamic> _stats = {};
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        _affiliateService.getCommissions(),
        _affiliateService.getWithdrawals(),
        _affiliateService.getStats(),
      ]);
      if (mounted) {
        setState(() {
          _commissions = results[0] as List<Commission>;
          _withdrawals = results[1] as List<Withdrawal>;
          _stats = results[2] as Map<String, dynamic>;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          NestedScrollView(
            headerSliverBuilder: (context, innerBoxIsScrolled) => [
              SliverAppBar(
                expandedHeight: 220,
                pinned: true,
                backgroundColor: PremiumColor.primary,
                title: Text("Affiliate Center",
                    style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.bold, color: Colors.white)),
                centerTitle: true,
                leading: IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded,
                      color: Colors.white),
                  onPressed: () => Navigator.pop(context),
                ),
                flexibleSpace: FlexibleSpaceBar(
                  background: Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [PremiumColor.primary, PremiumColor.accent],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                    padding: const EdgeInsets.fromLTRB(24, 80, 24, 40),
                    child: SingleChildScrollView(
                      physics: const NeverScrollableScrollPhysics(),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text("Total Komisi Terkumpul",
                              style: GoogleFonts.plusJakartaSans(
                                  color: Colors.white70, fontSize: 13)),
                          const SizedBox(height: 4),
                          FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Text(
                              "Rp ${NumberFormat.decimalPattern('id').format(_stats['total_earned'] ?? 0)}",
                              style: GoogleFonts.plusJakartaSans(
                                color: Colors.white,
                                fontSize: 32,
                                fontWeight: FontWeight.w900,
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),
                          Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 16, vertical: 8),
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Flexible(
                                    child: Text(
                                      "Kode Referral: ${_stats['referral_code'] ?? 'Belum Ada'}",
                                      style: GoogleFonts.plusJakartaSans(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                        letterSpacing: 1.0,
                                      ),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  GestureDetector(
                                    onTap: () {
                                      if (_stats['referral_code'] != null) {
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(const SnackBar(
                                                content: Text(
                                                    "Kode Referral disalin!")));
                                      }
                                    },
                                    child: const Icon(Icons.copy,
                                        color: Colors.white, size: 16),
                                  )
                                ],
                              )),
                        ],
                      ),
                    ),
                  ),
                ),
                bottom: TabBar(
                  controller: _tabController,
                  indicatorColor: Colors.white,
                  indicatorWeight: 4,
                  labelStyle: GoogleFonts.plusJakartaSans(
                      fontWeight: FontWeight.bold, fontSize: 13),
                  tabs: const [
                    Tab(text: "DAFTAR KOMISI"),
                    Tab(text: "RIWAYAT WD"),
                  ],
                ),
              ),
            ],
            body: TabBarView(
              controller: _tabController,
              children: [
                _buildCommissionList(),
                _buildWithdrawalList(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCommissionList() {
    if (_isLoading) {
      return const Center(
          child: CircularProgressIndicator(color: PremiumColor.primary));
    }
    if (_commissions.isEmpty) {
      return _buildEmptyState(
          "Belum ada komisi masuk wok!", Icons.money_off_rounded);
    }

    return ListView.builder(
      padding: const EdgeInsets.all(20),
      itemCount: _commissions.length,
      itemBuilder: (context, index) {
        final commission = _commissions[index];
        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                  color: Colors.black.withOpacity(0.03),
                  blurRadius: 10,
                  offset: const Offset(0, 4))
            ],
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.green.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.trending_up,
                    color: Colors.green, size: 24),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(commission.description,
                        style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.bold, fontSize: 15)),
                    Text(
                        DateFormat('dd MMM yyyy, HH:mm')
                            .format(commission.createdAt),
                        style: GoogleFonts.plusJakartaSans(
                            color: PremiumColor.slate500, fontSize: 11)),
                  ],
                ),
              ),
              Text(
                "+Rp${NumberFormat.decimalPattern('id').format(commission.amount)}",
                style: GoogleFonts.plusJakartaSans(
                    color: Colors.green,
                    fontWeight: FontWeight.w900,
                    fontSize: 15),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildWithdrawalList() {
    if (_isLoading) {
      return const Center(
          child: CircularProgressIndicator(color: PremiumColor.primary));
    }
    if (_withdrawals.isEmpty) {
      return _buildEmptyState(
          "Belum ada riwayat penarikan.", Icons.history_rounded);
    }

    return ListView.builder(
      padding: const EdgeInsets.all(20),
      itemCount: _withdrawals.length,
      itemBuilder: (context, index) {
        final wd = _withdrawals[index];
        final bool isSuccess = wd.status.toLowerCase() == 'success';
        final bool isPending = wd.status.toLowerCase() == 'pending';

        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                  color: Colors.black.withOpacity(0.03),
                  blurRadius: 10,
                  offset: const Offset(0, 4))
            ],
          ),
          child: Column(
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: (isSuccess ? Colors.blue : Colors.orange)
                          .withOpacity(0.1),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                        isSuccess
                            ? Icons.file_download_done_rounded
                            : Icons.schedule_rounded,
                        color: isSuccess ? Colors.blue : Colors.orange,
                        size: 24),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("Penarikan Saldo",
                            style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.bold, fontSize: 15)),
                        Text(
                            DateFormat('dd MMM yyyy, HH:mm')
                                .format(wd.createdAt),
                            style: GoogleFonts.plusJakartaSans(
                                color: PremiumColor.slate500, fontSize: 11)),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        "-Rp${NumberFormat.decimalPattern('id').format(wd.amount)}",
                        style: GoogleFonts.plusJakartaSans(
                            color: PremiumColor.slate800,
                            fontWeight: FontWeight.w900,
                            fontSize: 15),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: (isSuccess
                                  ? Colors.blue
                                  : isPending
                                      ? Colors.orange
                                      : Colors.red)
                              .withOpacity(0.1),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          wd.status.toUpperCase(),
                          style: GoogleFonts.plusJakartaSans(
                              color: isSuccess
                                  ? Colors.blue
                                  : isPending
                                      ? Colors.orange
                                      : Colors.red,
                              fontWeight: FontWeight.w800,
                              fontSize: 9),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              if (wd.note != null && wd.note!.isNotEmpty) ...[
                const Divider(height: 24),
                Row(
                  children: [
                    const Icon(Icons.info_outline,
                        size: 14, color: PremiumColor.slate400),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(wd.note!,
                          style: GoogleFonts.plusJakartaSans(
                              color: PremiumColor.slate500, fontSize: 12)),
                    ),
                  ],
                ),
              ],
            ],
          ),
        );
      },
    );
  }

  Widget _buildEmptyState(String msg, IconData icon) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, size: 64, color: PremiumColor.slate200),
          const SizedBox(height: 16),
          Text(msg,
              style: GoogleFonts.plusJakartaSans(
                  color: PremiumColor.slate400,
                  fontSize: 14,
                  fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
