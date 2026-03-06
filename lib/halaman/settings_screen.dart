import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/user_profile.dart';
import '../services/profile_service.dart';
import '../services/auth_service.dart';
import '../auth/login_screen.dart';
import 'widgets/custom_background.dart';

class SettingsScreen extends StatefulWidget {
  final UserProfile userProfile;

  const SettingsScreen({super.key, required this.userProfile});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  bool _profileUpdated = false;

  Future<void> _logout() async {
    final bool? confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text("Konfirmasi Keluar",
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w800, color: PremiumColor.primary)),
        content: Text("Apakah Anda yakin ingin keluar dari aplikasi?",
            style: GoogleFonts.plusJakartaSans(color: PremiumColor.slate600)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text("Batal",
                style: GoogleFonts.plusJakartaSans(
                    color: PremiumColor.slate400, fontWeight: FontWeight.w700)),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              foregroundColor: Colors.white,
              elevation: 0,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12)),
            ),
            child: Text("Keluar",
                style:
                    GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      if (mounted) {
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (context) => const Center(
            child: CircularProgressIndicator(color: PremiumColor.primary),
          ),
        );
      }

      try {
        final AuthService authService = AuthService();
        await authService.logout();
      } catch (e) {
        debugPrint("Logout error: \$e");
      } finally {
        if (mounted) {
          Navigator.of(context).pushAndRemoveUntil(
            MaterialPageRoute(builder: (context) => const LoginScreen()),
            (route) => false,
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) {
        if (didPop) return;
        Navigator.pop(context, _profileUpdated);
      },
      child: Scaffold(
        backgroundColor: PremiumColor.background,
        appBar: AppBar(
          backgroundColor: PremiumColor.primary,
          elevation: 0,
          title: Text(
            "Pengaturan",
            style: GoogleFonts.plusJakartaSans(
              fontWeight: FontWeight.w800,
              color: Colors.white,
              letterSpacing: -0.5,
            ),
          ),
          leading: IconButton(
            icon: const Icon(Icons.arrow_back_ios_new_rounded,
                color: Colors.white),
            onPressed: () => Navigator.pop(context, _profileUpdated),
          ),
        ),
        body: Stack(
          children: [
            const Positioned.fill(child: IslamicPatternBackground()),
            ListView(
              padding: const EdgeInsets.all(24),
              children: [
                _buildSectionTitle("Akun"),
                const SizedBox(height: 12),
                _buildSettingsCard([
                  _buildSettingsTile(
                    icon: Icons.person_rounded,
                    title: "Informasi Profil",
                    color: PremiumColor.primary,
                    onTap: () async {
                      final updated = await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => _EditProfileDetailScreen(
                              userProfile: widget.userProfile),
                        ),
                      );
                      if (updated == true) {
                        setState(() {
                          _profileUpdated = true;
                        });
                      }
                    },
                  ),
                  _buildDivider(),
                  _buildSettingsTile(
                    icon: Icons.security_rounded,
                    title: "Keamanan (Kata Sandi)",
                    color: Colors.orange,
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const _SecurityDetailScreen(),
                        ),
                      );
                    },
                  ),
                ]),
                const SizedBox(height: 32),
                _buildSectionTitle("Sistem"),
                const SizedBox(height: 12),
                _buildSettingsCard([
                  _buildSettingsTile(
                    icon: Icons.logout_rounded,
                    title: "Keluar (Logout)",
                    color: Colors.redAccent,
                    onTap: _logout,
                    isDestructive: true,
                  ),
                ]),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4),
      child: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 14,
          fontWeight: FontWeight.w800,
          color: PremiumColor.slate500,
          letterSpacing: 1.0,
        ),
      ),
    );
  }

  Widget _buildSettingsCard(List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.withOpacity(0.1)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Column(children: children),
    );
  }

  Widget _buildSettingsTile({
    required IconData icon,
    required String title,
    required Color color,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return ListTile(
      onTap: onTap,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      leading: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Icon(icon, color: color, size: 22),
      ),
      title: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontWeight: FontWeight.w700,
          color: isDestructive ? color : PremiumColor.slate800,
        ),
      ),
      trailing:
          const Icon(Icons.chevron_right_rounded, color: PremiumColor.slate400),
    );
  }

  Widget _buildDivider() {
    return Divider(
      height: 1,
      thickness: 1,
      color: Colors.grey.withOpacity(0.1),
      indent: 60,
    );
  }
}

// ---------------------------------------------------------
// EDIT PROFILE SECRETS SECTION
// ---------------------------------------------------------

class _EditProfileDetailScreen extends StatefulWidget {
  final UserProfile userProfile;
  const _EditProfileDetailScreen({required this.userProfile});

  @override
  State<_EditProfileDetailScreen> createState() =>
      _EditProfileDetailScreenState();
}

class _EditProfileDetailScreenState extends State<_EditProfileDetailScreen> {
  final _formKey = GlobalKey<FormState>();
  final ProfileService _profileService = ProfileService();

  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late String _selectedGender;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.userProfile.username);
    _emailController = TextEditingController(text: widget.userProfile.email);

    _selectedGender = widget.userProfile.gender;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);

    final profileData = {
      'username': _nameController.text,
      'gender': _selectedGender,
    };

    bool success = await _profileService.updateProfile(profileData);
    setState(() => _isLoading = false);

    if (mounted) {
      if (success) {
        Navigator.pop(context, true);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Profil berhasil diperbarui!")),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
              content: Text("Gagal memperbarui profil"),
              backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      appBar: AppBar(
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        title: Text("Informasi Profil",
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w800,
                color: Colors.white,
                letterSpacing: -0.5)),
        leading: IconButton(
          icon:
              const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          TextButton(
            onPressed: _isLoading ? null : _save,
            child: _isLoading
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                        color: PremiumColor.accent, strokeWidth: 2))
                : Text("Simpan",
                    style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w700,
                        color: PremiumColor.accent)),
          )
        ],
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  _buildTextField(
                    controller: _nameController,
                    label: "Nama Lengkap",
                    icon: Icons.person_rounded,
                    validator: (v) =>
                        v!.isEmpty ? "Nama tidak boleh kosong" : null,
                  ),
                  const SizedBox(height: 16),
                  _buildTextField(
                    controller: _emailController,
                    label: "Email",
                    icon: Icons.email_rounded,
                    readOnly: true,
                  ),
                  const SizedBox(height: 16),
                  const SizedBox(height: 32),
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                        color: PremiumColor.accent.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                            color: PremiumColor.accent.withOpacity(0.2))),
                    child: Row(
                      children: [
                        const Icon(Icons.info_outline_rounded,
                            color: PremiumColor.accent),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            "Rank dan Level murni diatur oleh progres aplikasi dan tidak dapat diubah secara manual.",
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 12,
                                color: PremiumColor.slate600,
                                height: 1.5),
                          ),
                        )
                      ],
                    ),
                  )
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool readOnly = false,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      readOnly: readOnly,
      validator: validator,
      style: GoogleFonts.plusJakartaSans(
        fontWeight: FontWeight.w600,
        color: readOnly ? PremiumColor.slate500 : PremiumColor.slate800,
      ),
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: PremiumColor.primary.withOpacity(0.5)),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: Colors.grey.withOpacity(0.2)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: Colors.grey.withOpacity(0.2)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: PremiumColor.primary, width: 2),
        ),
      ),
    );
  }
}

// ---------------------------------------------------------
// SECURITY SECRETS SECTION
// ---------------------------------------------------------

class _SecurityDetailScreen extends StatefulWidget {
  const _SecurityDetailScreen();

  @override
  State<_SecurityDetailScreen> createState() => _SecurityDetailScreenState();
}

class _SecurityDetailScreenState extends State<_SecurityDetailScreen> {
  final _formKey = GlobalKey<FormState>();
  final ProfileService _profileService = ProfileService();

  final _oldPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _isLoading = false;

  @override
  void dispose() {
    _oldPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;

    if (_newPasswordController.text != _confirmPasswordController.text) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
            content: Text("Kata sandi baru tidak cocok!"),
            backgroundColor: Colors.red),
      );
      return;
    }

    setState(() => _isLoading = true);
    bool success = await _profileService.updatePassword(
      _oldPasswordController.text,
      _newPasswordController.text,
    );
    setState(() => _isLoading = false);

    if (mounted) {
      if (success) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Kata sandi berhasil diperbarui!")),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
              content:
                  Text("Gagal memperbarui kata sandi, periksa sandi lama."),
              backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      appBar: AppBar(
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        title: Text("Keamanan",
            style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w800,
                color: Colors.white,
                letterSpacing: -0.5)),
        leading: IconButton(
          icon:
              const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          TextButton(
            onPressed: _isLoading ? null : _save,
            child: _isLoading
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                        color: PremiumColor.accent, strokeWidth: 2))
                : Text("Simpan",
                    style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w700,
                        color: PremiumColor.accent)),
          )
        ],
      ),
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  _buildTextField(
                    controller: _oldPasswordController,
                    label: "Kata Sandi Saat Ini",
                    icon: Icons.lock_open_rounded,
                    validator: (v) =>
                        v!.isEmpty ? "Harap isi sandi saat ini" : null,
                  ),
                  const SizedBox(height: 16),
                  _buildTextField(
                    controller: _newPasswordController,
                    label: "Kata Sandi Baru",
                    icon: Icons.vpn_key_rounded,
                    validator: (v) {
                      if (v!.isEmpty) return "Sandi baru tidak boleh kosong";
                      if (v.length < 6) return "Minimal 6 karakter";
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  _buildTextField(
                    controller: _confirmPasswordController,
                    label: "Konfirmasi Sandi Baru",
                    icon: Icons.check_circle_rounded,
                    validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      obscureText: true,
      validator: validator,
      style: GoogleFonts.plusJakartaSans(
          fontWeight: FontWeight.w600, color: PremiumColor.slate800),
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: PremiumColor.primary.withOpacity(0.5)),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: Colors.grey.withOpacity(0.2)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: Colors.grey.withOpacity(0.2)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: PremiumColor.primary, width: 2),
        ),
      ),
    );
  }
}
