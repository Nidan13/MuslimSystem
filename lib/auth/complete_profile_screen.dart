import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../halaman/widgets/custom_background.dart';
import '../halaman/main_screen.dart';
import '../services/auth_service.dart';
import '../theme/premium_color.dart';
import '../models/user.dart';
import 'widgets/auth_text_field.dart';
import 'widgets/gender_option.dart';
import '../halaman/payment_screen.dart';

class CompleteProfileScreen extends StatefulWidget {
  final User user;

  const CompleteProfileScreen({super.key, required this.user});

  @override
  State<CompleteProfileScreen> createState() => _CompleteProfileScreenState();
}

class _CompleteProfileScreenState extends State<CompleteProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  bool _isLoading = false;
  late String _selectedGender;
  final _authService = AuthService();

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.user.username);
    // Default gender based on google logic or fallback
    _selectedGender =
        widget.user.gender == 'Male' || widget.user.gender == 'Female'
            ? widget.user.gender.toLowerCase()
            : 'male';
  }

  @override
  void dispose() {
    _nameController.dispose();
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

  Future<void> _handleCompleteProfile() async {
    if (_formKey.currentState!.validate()) {
      setState(() {
        _isLoading = true;
      });

      try {
        await _authService.updateProfile(
          username: _nameController.text.trim(),
          gender: _selectedGender,
        );

        if (mounted) {
          final updatedUser = await _authService.getProfile();

          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  "Profile Completed! Welcome, Hunter!",
                  style:
                      GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600),
                ),
                backgroundColor: PremiumColor.primary,
              ),
            );

            if (!updatedUser.isActive) {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(
                    builder: (context) =>
                        const PaymentScreen(isActivation: true)),
              );
            } else {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (context) => const MainScreen()),
              );
            }
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
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // Background removed for clean white look

          // 3. Content
          SafeArea(
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      SizedBox(height: size.height * 0.05),

                      // Header Section
                      Column(
                        children: [
                          Container(
                            width: 80,
                            height: 80,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              boxShadow: [
                                BoxShadow(
                                  color: PremiumColor.primary.withOpacity(0.2),
                                  blurRadius: 20,
                                  offset: const Offset(0, 10),
                                )
                              ],
                            ),
                            child: ClipOval(
                              child: Image.asset(
                                'assets/images/logoapk.jpeg',
                                fit: BoxFit.cover,
                              ),
                            ),
                          ),
                          const SizedBox(height: 24),
                          Text(
                            "COMPLETE PROFILE",
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 22,
                              fontWeight: FontWeight.w900,
                              color: PremiumColor.primary,
                              letterSpacing: 3.0,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            "FINALIZE YOUR IDENTITY",
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: PremiumColor.slate500,
                              letterSpacing: 2.0,
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 48),

                      // Form Glass Card
                      Container(
                        padding: const EdgeInsets.all(24),
                        width: double.infinity,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.8),
                          borderRadius: BorderRadius.circular(32),
                          border: Border.all(
                            color: Colors.white,
                            width: 1.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.05),
                              blurRadius: 30,
                              offset: const Offset(0, 15),
                            )
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(32),
                          child: BackdropFilter(
                            filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                AuthTextField(
                                  label: 'Hunter Name',
                                  placeholder: 'Your adventurer name',
                                  icon: Icons.person_outline_rounded,
                                  controller: _nameController,
                                  validator: _validateName,
                                ),
                                const SizedBox(height: 32),

                                // Gender Selector
                                Text(
                                  "IDENTITY TYPE",
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w800,
                                    color: PremiumColor.slate500,
                                    letterSpacing: 2.0,
                                  ),
                                ),
                                const SizedBox(height: 12),
                                Row(
                                  children: [
                                    Expanded(
                                      child: GenderOption(
                                        label: 'Male',
                                        value: 'male',
                                        groupValue: _selectedGender,
                                        onChanged: (val) => setState(
                                            () => _selectedGender = val!),
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: GenderOption(
                                        label: 'Female',
                                        value: 'female',
                                        groupValue: _selectedGender,
                                        onChanged: (val) => setState(
                                            () => _selectedGender = val!),
                                      ),
                                    ),
                                  ],
                                ),

                                const SizedBox(height: 40),

                                SizedBox(
                                  width: double.infinity,
                                  height: 56,
                                  child: ElevatedButton(
                                    onPressed: _isLoading
                                        ? null
                                        : _handleCompleteProfile,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: PremiumColor.primary,
                                      foregroundColor: Colors.white,
                                      elevation: 0,
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(20),
                                      ),
                                    ),
                                    child: _isLoading
                                        ? const SizedBox(
                                            width: 20,
                                            height: 20,
                                            child: CircularProgressIndicator(
                                              strokeWidth: 2,
                                              valueColor:
                                                  AlwaysStoppedAnimation<Color>(
                                                      Colors.white),
                                            ),
                                          )
                                        : Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.center,
                                            children: [
                                              Text(
                                                "START ADVENTURE",
                                                style:
                                                    GoogleFonts.plusJakartaSans(
                                                  fontWeight: FontWeight.w900,
                                                  fontSize: 12,
                                                  letterSpacing: 1.5,
                                                ),
                                              ),
                                              const SizedBox(width: 8),
                                              const Icon(
                                                  Icons.arrow_forward_rounded,
                                                  size: 18),
                                            ],
                                          ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 48),
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
}
