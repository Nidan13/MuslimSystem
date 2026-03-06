import 'package:flutter/material.dart';

class IconMapper {
  static IconData getIcon(String? iconName) {
    if (iconName == null) return Icons.star_rounded;

    switch (iconName.toLowerCase()) {
      case 'mosque':
      case 'sholat':
      case 'pray':
        return Icons.mosque_rounded;
      case 'quran':
      case 'ngaji':
      case 'read':
      case 'book':
        return Icons.menu_book_rounded;
      case 'charity':
      case 'sadaqah':
      case 'sedekah':
        return Icons.volunteer_activism_rounded;
      case 'dzikir':
      case 'tasbih':
        return Icons.self_improvement_rounded;
      case 'fasting':
      case 'puasa':
        return Icons.no_food_rounded;
      case 'study':
      case 'kajian':
        return Icons.school_rounded;
      case 'fajr':
      case 'subuh':
        return Icons.wb_twilight_rounded;
      case 'dhuha':
        return Icons.sunny;
      case 'tahajjud':
        return Icons.nights_stay_rounded;
      default:
        return Icons.task_alt_rounded;
    }
  }

  static Color getIconColor(String? iconName) {
    // Optional: Return a color based on the task type if needed
    return Colors.teal;
  }
}
