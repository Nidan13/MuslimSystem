import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../halaman/widgets/custom_background.dart';
import '../halaman/main_screen.dart';
import '../services/auth_service.dart';
import '../theme/premium_color.dart';
import 'widgets/auth_text_field.dart';
import 'login_screen.dart';
import 'complete_profile_screen.dart';
import '../halaman/payment_screen.dart';

class RegisterScreen extends StatefulWidget {
  final String? initialReferralCode;
  const RegisterScreen({super.key, this.initialReferralCode});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _referralController = TextEditingController();
  bool _obscurePassword = true;
  bool _isLoading = false;
  String _selectedGender = 'Laki-laki'; // Default gender
  final _authService = AuthService();

  @override
  void initState() {
    super.initState();
    if (widget.initialReferralCode != null) {
      _referralController.text = widget.initialReferralCode!;
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _referralController.dispose();
    super.dispose();
  }

  String? _validateName(String? value) {
    if (value == null || value.isEmpty) {
      return 'Player name is required';
    }
    if (value.length < 3) {
      return 'Name must be at least 3 characters';
    }
    return null;
  }

  String? _validateEmail(String? value) {
    if (value == null || value.isEmpty) {
      return 'Email is required';
    }
    final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
    if (!emailRegex.hasMatch(value)) {
      return 'Enter a valid email';
    }
    return null;
  }

  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) {
      return 'Password is required';
    }
    if (value.length < 8) {
      return 'Password must be at least 8 characters';
    }
    return null;
  }

  Future<void> _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      setState(() {
        _isLoading = true;
      });

      try {
        final response = await _authService.register(
          username: _nameController.text.trim(),
          email: _emailController.text.trim(),
          password: _passwordController.text,
          passwordConfirmation: _passwordController.text,
          gender: _selectedGender,
          referralCode: _referralController.text.trim().isEmpty
              ? null
              : _referralController.text.trim(),
        );

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                response.message,
                style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600),
              ),
              backgroundColor: PremiumColor.primary,
            ),
          );

          if (!response.user.isActive) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(
                builder: (context) => const PaymentScreen(isActivation: true),
              ),
            );
          } else {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const MainScreen()),
            );
          }
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                e.toString(),
                style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600),
              ),
              backgroundColor: Colors.red.shade600,
            ),
          );
        }
      } finally {
        if (mounted) {
          setState(() {
            _isLoading = false;
          });
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        fit: StackFit.expand,
        children: [
          // 1. Full Screen Image Background
          Positioned.fill(
            child: Image.asset(
              'assets/images/bglog.png',
              fit: BoxFit.cover,
              alignment: Alignment.center,
              filterQuality: FilterQuality.high,
            ),
          ),

          // 2. Sophisticated Overlay Gradient
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.black.withOpacity(0.2),
                    Colors.black.withOpacity(0.8),
                  ],
                ),
              ),
            ),
          ),

          // 3. Main Content
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding:
                    const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      // Premium Animated Logo Section
                      TweenAnimationBuilder<double>(
                        tween: Tween(begin: 0, end: 1),
                        duration: const Duration(seconds: 1),
                        builder: (context, value, child) {
                          return Opacity(
                            opacity: value,
                            child: Transform.translate(
                              offset: Offset(0, 20 * (1 - value)),
                              child: child,
                            ),
                          );
                        },
                        child: Column(
                          children: [
                            Container(
                              width: 64,
                              height: 64,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                  color: Colors.amber.withOpacity(0.5),
                                  width: 1.5,
                                ),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.amber.withOpacity(0.2),
                                    blurRadius: 20,
                                    spreadRadius: 2,
                                  )
                                ],
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(18),
                                child: Image.asset(
                                  'assets/images/logoapk.jpeg',
                                  fit: BoxFit.cover,
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              "NEW HUNTER REGISTRY",
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                color: Colors.white,
                                letterSpacing: 4.0,
                                shadows: [
                                  Shadow(
                                    color: Colors.black.withOpacity(0.5),
                                    blurRadius: 15,
                                    offset: const Offset(0, 5),
                                  )
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 32),

                      // Pro Glass Card
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(36),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.3),
                              blurRadius: 40,
                              offset: const Offset(0, 20),
                            )
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(36),
                          child: BackdropFilter(
                            filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
                            child: Container(
                              padding: const EdgeInsets.all(24),
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                  colors: [
                                    Colors.white.withOpacity(0.12),
                                    Colors.white.withOpacity(0.04),
                                  ],
                                ),
                                border: Border.all(
                                  color: Colors.white.withOpacity(0.15),
                                  width: 1.5,
                                ),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  AuthTextField(
                                    label: 'Codename',
                                    placeholder: 'X-Hunter',
                                    icon: Icons.person_outline_rounded,
                                    controller: _nameController,
                                    validator: _validateName,
                                  ),
                                  const SizedBox(height: 16),
                                  AuthTextField(
                                    label: 'Identification',
                                    placeholder: 'hunter@elite.com',
                                    icon: Icons.alternate_email_rounded,
                                    controller: _emailController,
                                    validator: _validateEmail,
                                  ),
                                  const SizedBox(height: 16),
                                  AuthTextField(
                                    label: 'Security Key',
                                    placeholder: '••••••••',
                                    icon: Icons.vpn_key_outlined,
                                    controller: _passwordController,
                                    isPassword: _obscurePassword,
                                    validator: _validatePassword,
                                    showPasswordToggle: true,
                                    onTogglePassword: () {
                                      setState(() {
                                        _obscurePassword = !_obscurePassword;
                                      });
                                    },
                                  ),
                                  const SizedBox(height: 24),

                                  // Gender Selector
                                  _buildSectionLabel("IDENTITY TYPE"),
                                  const SizedBox(height: 12),
                                  Row(
                                    children: [
                                      Expanded(
                                        child: _buildGenderOption(
                                          'Laki-laki',
                                          'male',
                                          Icons.male_rounded,
                                        ),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: _buildGenderOption(
                                          'Perempuan',
                                          'female',
                                          Icons.female_rounded,
                                        ),
                                      ),
                                    ],
                                  ),

                                  const SizedBox(height: 32),
                                  AuthTextField(
                                    label: 'Referral Token (Optional)',
                                    placeholder: 'REF-XXXX',
                                    icon: Icons.card_membership_rounded,
                                    controller: _referralController,
                                  ),

                                  const SizedBox(height: 32),

                                  // Action Button
                                  SizedBox(
                                    width: double.infinity,
                                    height: 54,
                                    child: Container(
                                      decoration: BoxDecoration(
                                        borderRadius: BorderRadius.circular(16),
                                        gradient: const LinearGradient(
                                          colors: [
                                            Color(0xFFF39221),
                                            Color(0xFFFFB347),
                                          ],
                                        ),
                                        boxShadow: [
                                          BoxShadow(
                                            color: const Color(0xFFF39221)
                                                .withOpacity(0.3),
                                            blurRadius: 15,
                                            offset: const Offset(0, 8),
                                          )
                                        ],
                                      ),
                                      child: ElevatedButton(
                                        onPressed:
                                            _isLoading ? null : _handleRegister,
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: Colors.transparent,
                                          shadowColor: Colors.transparent,
                                          shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(16),
                                          ),
                                        ),
                                        child: _isLoading
                                            ? const SizedBox(
                                                width: 20,
                                                height: 20,
                                                child:
                                                    CircularProgressIndicator(
                                                  strokeWidth: 2,
                                                  valueColor:
                                                      AlwaysStoppedAnimation(
                                                          Colors.white),
                                                ),
                                              )
                                            : Row(
                                                mainAxisAlignment:
                                                    MainAxisAlignment.center,
                                                children: [
                                                  Text(
                                                    "INITIATE ASCENSION",
                                                    style: GoogleFonts
                                                        .plusJakartaSans(
                                                      fontWeight:
                                                          FontWeight.w900,
                                                      fontSize: 12,
                                                      letterSpacing: 1.5,
                                                      color: Colors.white,
                                                    ),
                                                  ),
                                                  const SizedBox(width: 8),
                                                  const Icon(Icons.bolt_rounded,
                                                      color: Colors.white,
                                                      size: 18),
                                                ],
                                              ),
                                      ),
                                    ),
                                  ),

                                  const SizedBox(height: 24),

                                  // Quick Access
                                  Row(
                                    children: [
                                      Expanded(
                                          child: Divider(
                                              color: Colors.white
                                                  .withOpacity(0.1))),
                                      Padding(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 10),
                                        child: Text("QUICK REGISTRY",
                                            style: GoogleFonts.plusJakartaSans(
                                              color:
                                                  Colors.white.withOpacity(0.3),
                                              fontSize: 8,
                                              fontWeight: FontWeight.w800,
                                              letterSpacing: 1.0,
                                            )),
                                      ),
                                      Expanded(
                                          child: Divider(
                                              color: Colors.white
                                                  .withOpacity(0.1))),
                                    ],
                                  ),
                                  const SizedBox(height: 20),

                                  Row(
                                    children: [
                                      Expanded(
                                        child: _buildSocialBtn(
                                          icon: Icons.account_circle,
                                          onTap: () async {
                                            setState(() => _isLoading = true);
                                            try {
                                              final response =
                                                  await _authService
                                                      .signInWithGoogle();
                                              if (response != null && mounted) {
                                                if (response.isNewUser) {
                                                  Navigator.pushReplacement(
                                                      context,
                                                      MaterialPageRoute(
                                                          builder: (_) =>
                                                              CompleteProfileScreen(
                                                                  user: response
                                                                      .user)));
                                                } else {
                                                  if (!response.user.isActive) {
                                                    Navigator.pushReplacement(
                                                        context,
                                                        MaterialPageRoute(
                                                            builder: (_) =>
                                                                const PaymentScreen(
                                                                    isActivation:
                                                                        true)));
                                                  } else {
                                                    Navigator.pushReplacement(
                                                        context,
                                                        MaterialPageRoute(
                                                            builder: (_) =>
                                                                const MainScreen()));
                                                  }
                                                }
                                              }
                                            } catch (e) {
                                              if (mounted) {
                                                ScaffoldMessenger.of(context)
                                                    .showSnackBar(SnackBar(
                                                        content:
                                                            Text("Error: $e")));
                                              }
                                            } finally {
                                              if (mounted) {
                                                setState(
                                                    () => _isLoading = false);
                                              }
                                            }
                                          },
                                        ),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: _buildSocialBtn(
                                          icon: Icons.apple,
                                          onTap: () {
                                            // Apple ID Placeholder
                                          },
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 32),

                      // Footer
                      GestureDetector(
                        onTap: () => Navigator.pushReplacement(
                            context,
                            MaterialPageRoute(
                                builder: (_) => const LoginScreen())),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              vertical: 10, horizontal: 20),
                          decoration: BoxDecoration(
                            border: Border.all(
                                color: Colors.white.withOpacity(0.1)),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: RichText(
                            text: TextSpan(
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                                color: Colors.white.withOpacity(0.5),
                                letterSpacing: 1.0,
                              ),
                              children: [
                                const TextSpan(text: "ALREADY A HUNTER? "),
                                TextSpan(
                                  text: "ACCESS SYSTEM",
                                  style: TextStyle(
                                    color: Colors.amber.shade400,
                                    fontWeight: FontWeight.w900,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 32),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionLabel(String label) {
    return Text(
      label,
      style: GoogleFonts.plusJakartaSans(
        fontSize: 9,
        fontWeight: FontWeight.w800,
        color: Colors.white.withOpacity(0.4),
        letterSpacing: 2.0,
      ),
    );
  }

  Widget _buildGenderOption(String label, String value, IconData icon) {
    bool isSelected = _selectedGender == value;
    return GestureDetector(
      onTap: () => setState(() => _selectedGender = value),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
          color: isSelected
              ? Colors.amber.withOpacity(0.15)
              : Colors.white.withOpacity(0.05),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: isSelected ? Colors.amber : Colors.white.withOpacity(0.1),
            width: 1.5,
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              icon,
              size: 16,
              color: isSelected ? Colors.amber : Colors.white.withOpacity(0.5),
            ),
            const SizedBox(width: 8),
            Text(
              label.toUpperCase(),
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.w900 : FontWeight.w700,
                color:
                    isSelected ? Colors.amber : Colors.white.withOpacity(0.5),
                letterSpacing: 1.0,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSocialBtn(
      {required IconData icon, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      child: Container(
        height: 48,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.05),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.white.withOpacity(0.1)),
        ),
        child: Icon(icon, color: Colors.white, size: 24),
      ),
    );
  }
}
