import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../theme/premium_color.dart';

class CustomBottomNavBar extends StatelessWidget {
  final int selectedIndex;
  final Function(int) onItemTapped;

  const CustomBottomNavBar({
    super.key,
    required this.selectedIndex,
    required this.onItemTapped,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 90,
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, -5),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment:
            MainAxisAlignment.spaceAround, // Even spacing for 5 items
        children: [
          _buildItem(0, Icons.home_rounded, 'Beranda'),
          _buildItem(1, Icons.checklist_rounded, 'Produktivitas'),
          _buildItem(2, Icons.calendar_month_rounded, 'Kalender'),
          _buildItem(3, Icons.group_rounded, 'Lingkaran'),
          _buildItem(4, Icons.person_rounded, 'Profil'),
        ],
      ),
    );
  }

  Widget _buildItem(int index, IconData icon, String label) {
    return Expanded(
      child: NavBarItem(
        icon: icon,
        label: label,
        isSelected: selectedIndex == index,
        onTap: () => onItemTapped(index),
      ),
    );
  }
}

class NavBarItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const NavBarItem({
    super.key,
    required this.icon,
    required this.label,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final color = isSelected ? PremiumColor.primary : Colors.grey;
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 4),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              label,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                color: color,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
