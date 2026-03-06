import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class NavBarItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const NavBarItem({
    super.key,
    required this.icon,
    required this.label,
    required this.onTap,
    this.isSelected = false,
  });

  @override
  Widget build(BuildContext context) {
    // Luxurious Teal Palette
    final activeColor = const Color(0xFF005B71);
    final inactiveColor = const Color(0xFF94A3B8);
    final color = isSelected ? activeColor : inactiveColor;

    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: AnimatedScale(
        scale: isSelected ? 1.1 : 1.0,
        duration: const Duration(milliseconds: 300),
        curve: Curves.elasticOut,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Icon with Glow effect if selected
              AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                decoration: isSelected
                    ? BoxDecoration(shape: BoxShape.circle, boxShadow: [
                        BoxShadow(
                            color: activeColor.withOpacity(0.15),
                            blurRadius: 15,
                            spreadRadius: 2)
                      ])
                    : null,
                child: Icon(icon, color: color, size: 28),
              ),

              const SizedBox(height: 4),

              // Animated Text Label
              AnimatedDefaultTextStyle(
                duration: const Duration(milliseconds: 300),
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
                  color: color,
                ),
                child: Text(label),
              ),

              if (isSelected)
                const SizedBox(height: 4)
              else
                const SizedBox.shrink(),

              // Dot Indicator
              AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                width: isSelected ? 4 : 0,
                height: 4,
                decoration: BoxDecoration(
                  color: const Color(0xFFF39221), // Highlight Orange
                  shape: BoxShape.circle,
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}
