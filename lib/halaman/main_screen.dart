import 'package:flutter/material.dart';
import 'home_screen.dart';
import 'productvity_menu_screen.dart';
import 'circle_screen.dart';
import 'profile_screen.dart';
import 'widgets/buttom_navbar.dart'; // Correct typo in filename if strictly following existing
import 'calendar_screen.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => MainScreenState();
}

class MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;

  void onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBody: false, // Solid navbar doesn't need extendBody
      backgroundColor: const Color(0xFFF8F9FA), // Base color
      body: IndexedStack(
        index: _selectedIndex,
        children: [
          HomeScreen(shouldRefresh: _selectedIndex == 0),
          const ProductivityMenuScreen(showBack: false),
          const CalendarScreen(), // Index 2
          CircleScreen(shouldRefresh: _selectedIndex == 3), // Index 3
          ProfileScreen(shouldRefresh: _selectedIndex == 4), // Index 4
        ],
      ),
      bottomNavigationBar: CustomBottomNavBar(
        selectedIndex: _selectedIndex,
        onItemTapped: onItemTapped,
      ),
    );
  }
}
