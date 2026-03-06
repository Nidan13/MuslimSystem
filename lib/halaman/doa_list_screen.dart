import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/doa.dart';
import '../services/doa_service.dart';
import 'doa_detail_screen.dart';
import '../theme/premium_color.dart';
import 'widgets/custom_background.dart';

class DoaListScreen extends StatefulWidget {
  const DoaListScreen({super.key});

  @override
  State<DoaListScreen> createState() => _DoaListScreenState();
}

class _DoaListScreenState extends State<DoaListScreen> {
  final DoaService _doaService = DoaService();
  List<Doa> _doaList = [];
  List<Doa> _filteredDoaList = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchDoa();
  }

  Future<void> _fetchDoa() async {
    try {
      final list = await _doaService.getDoas();
      setState(() {
        _doaList = list;
        _filteredDoaList = list;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal mengambil data doa: $e')),
        );
      }
    }
  }

  void _filterDoa(String query) {
    setState(() {
      _filteredDoaList = _doaList
          .where((doa) => doa.judul.toLowerCase().contains(query.toLowerCase()))
          .toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildSliverHeader(),
          SliverToBoxAdapter(
            child: _buildSearchField(),
          ),
          _isLoading
              ? const SliverFillRemaining(
                  child: Center(
                    child:
                        CircularProgressIndicator(color: PremiumColor.primary),
                  ),
                )
              : _filteredDoaList.isEmpty
                  ? SliverToBoxAdapter(child: _buildEmptyState())
                  : SliverPadding(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 20, vertical: 12),
                      sliver: SliverList(
                        delegate: SliverChildBuilderDelegate(
                          (context, index) {
                            final doa = _filteredDoaList[index];
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 12),
                              child: _buildDoaCard(doa),
                            );
                          },
                          childCount: _filteredDoaList.length,
                        ),
                      ),
                    ),
        ],
      ),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 220,
      backgroundColor: PremiumColor.primary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: true,
      leading: const BackButton(color: Colors.white),
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [PremiumColor.primary, PremiumColor.accent],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            child: Stack(
              children: [
                const Positioned.fill(child: IslamicPatternBackground()),
                Padding(
                  padding: const EdgeInsets.fromLTRB(24, 0, 24, 40),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "PILIHAN",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.w900,
                          color: Colors.white.withOpacity(0.6),
                          letterSpacing: 2,
                        ),
                      ),
                      Text(
                        "Doa Pilihan",
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSearchField() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      color: Colors.white,
      child: TextField(
        onChanged: _filterDoa,
        decoration: InputDecoration(
          hintText: "Cari doa...",
          hintStyle: GoogleFonts.plusJakartaSans(
              color: Colors.grey.shade400, fontSize: 14),
          prefixIcon: const Icon(Icons.search_rounded,
              color: PremiumColor.primary, size: 22),
          filled: true,
          fillColor: const Color(0xFFF1F5F9),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: BorderSide.none,
          ),
          contentPadding: const EdgeInsets.symmetric(vertical: 0),
        ),
      ),
    );
  }

  Widget _buildDoaCard(Doa doa) {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => DoaDetailScreen(doa: doa),
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
          border: Border.all(color: PremiumColor.primary.withOpacity(0.05)),
        ),
        child: Row(
          children: [
            Container(
              width: 45,
              height: 45,
              decoration: BoxDecoration(
                color: PremiumColor.primary.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Center(
                child: Text(
                  doa.id,
                  style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w900,
                    color: PremiumColor.primary,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Text(
                doa.judul,
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700,
                  fontSize: 15,
                  color: PremiumColor.slate800,
                ),
              ),
            ),
            const Icon(Icons.arrow_forward_ios_rounded,
                size: 14, color: PremiumColor.slate600),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.search_off_rounded, size: 60, color: Colors.grey.shade300),
          const SizedBox(height: 16),
          Text(
            "Doa tidak ditemukan",
            style: GoogleFonts.plusJakartaSans(
              color: Colors.grey.shade500,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}
