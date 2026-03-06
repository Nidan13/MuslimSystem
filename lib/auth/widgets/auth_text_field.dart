import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../theme/premium_color.dart';

class AuthTextField extends StatefulWidget {
  final String label;
  final String placeholder;
  final IconData icon;
  final TextEditingController? controller;
  final bool isPassword;
  final TextInputType? keyboardType;
  final String? Function(String?)? validator;
  final VoidCallback? onTogglePassword;
  final bool showPasswordToggle;

  const AuthTextField({
    super.key,
    required this.label,
    required this.placeholder,
    required this.icon,
    this.controller,
    this.isPassword = false,
    this.keyboardType,
    this.validator,
    this.onTogglePassword,
    this.showPasswordToggle = false,
  });

  @override
  State<AuthTextField> createState() => _AuthTextFieldState();
}

class _AuthTextFieldState extends State<AuthTextField> {
  bool _isFocused = false;
  final FocusNode _focusNode = FocusNode();

  @override
  void initState() {
    super.initState();
    _focusNode.addListener(() {
      setState(() {
        _isFocused = _focusNode.hasFocus;
      });
    });
  }

  @override
  void dispose() {
    _focusNode.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 4, bottom: 8),
          child: Text(
            widget.label.toUpperCase(),
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w800,
              color: _isFocused ? PremiumColor.primary : PremiumColor.slate500,
              letterSpacing: 2.0,
            ),
          ),
        ),
        AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.05),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: _isFocused
                  ? PremiumColor.primary
                  : Colors.white.withOpacity(0.1),
              width: 1.5,
            ),
          ),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: Row(
              children: [
                Icon(
                  widget.icon,
                  color: _isFocused
                      ? PremiumColor.primary
                      : Colors.white.withOpacity(0.5),
                  size: 20,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: TextFormField(
                    controller: widget.controller,
                    focusNode: _focusNode,
                    obscureText: widget.isPassword,
                    keyboardType: widget.keyboardType,
                    validator: widget.validator,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                    ),
                    decoration: InputDecoration(
                      hintText: widget.placeholder,
                      hintStyle: GoogleFonts.plusJakartaSans(
                        fontSize: 14,
                        color: Colors.white.withOpacity(0.3),
                      ),
                      border: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(vertical: 16),
                    ),
                  ),
                ),
                if (widget.showPasswordToggle)
                  IconButton(
                    onPressed: widget.onTogglePassword,
                    icon: Icon(
                      widget.isPassword
                          ? Icons.visibility_outlined
                          : Icons.visibility_off_outlined,
                      color: PremiumColor.slate400,
                      size: 20,
                    ),
                  ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
