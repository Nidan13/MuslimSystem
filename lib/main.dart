import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'halaman/main_screen.dart';
import 'auth/login_screen.dart';
import 'auth/register_screen.dart';
import 'services/storage_service.dart';
import 'theme/premium_color.dart';
import 'halaman/widgets/custom_background.dart';

import 'package:provider/provider.dart';
import 'features/quran/presentation/providers/quran_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID', null);
  await initializeDateFormatting('en_US', null);

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => QuranProvider()),
      ],
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Muslim App',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF00695C)),
        useMaterial3: true,
        textTheme: GoogleFonts.plusJakartaSansTextTheme(),
      ),
      initialRoute: '/',
      onGenerateRoute: (settings) {
        if (settings.name != null && settings.name!.startsWith('/register')) {
          final uri = Uri.parse(settings.name!);
          final ref = uri.queryParameters['ref'];
          return MaterialPageRoute(
            builder: (context) => RegisterScreen(initialReferralCode: ref),
          );
        }

        switch (settings.name) {
          case '/':
            return MaterialPageRoute(builder: (context) => const AuthWrapper());
          case '/login':
            return MaterialPageRoute(builder: (context) => const LoginScreen());
          case '/main':
            return MaterialPageRoute(builder: (context) => const MainScreen());
          default:
            return MaterialPageRoute(builder: (context) => const AuthWrapper());
        }
      },
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  Future<Map<String, bool>> _checkStatus() async {
    final isLoggedIn = await StorageService.isLoggedIn();
    final isActive = await StorageService.isActive();

    // REQUEST USER: Kalau belum aktif, hapus session biar bisa register ulang
    if (isLoggedIn && !isActive) {
      await StorageService.removeToken();
      return {
        'isLoggedIn': false,
        'isActive': false,
      };
    }

    return {
      'isLoggedIn': isLoggedIn,
      'isActive': isActive,
    };
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, bool>>(
      future: _checkStatus(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return Scaffold(
            backgroundColor: PremiumColor.background,
            body: Stack(
              children: [
                Positioned.fill(child: Container(color: Colors.white)),
                Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      TweenAnimationBuilder<double>(
                        tween: Tween(begin: 0, end: 1),
                        duration: const Duration(seconds: 2),
                        builder: (context, value, child) {
                          return Opacity(
                            opacity: value,
                            child: Transform.scale(
                              scale: 0.8 + (0.2 * value),
                              child: child,
                            ),
                          );
                        },
                        child: Container(
                          width: 120,
                          height: 120,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            boxShadow: [
                              BoxShadow(
                                color: PremiumColor.primary.withOpacity(0.2),
                                blurRadius: 40,
                                offset: const Offset(0, 20),
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
                      ),
                      const SizedBox(height: 32),
                      Text(
                        "ELITE ACCESS",
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 28,
                          fontWeight: FontWeight.w900,
                          color: PremiumColor.primary,
                          letterSpacing: 8.0,
                        ),
                      ),
                      const SizedBox(height: 48),
                      const CircularProgressIndicator(
                        color: PremiumColor.primary,
                        strokeWidth: 3,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        }

        final isLoggedIn = snapshot.data?['isLoggedIn'] ?? false;
        final isActive = snapshot.data?['isActive'] ?? false;

        if (isLoggedIn && isActive) {
          return const MainScreen();
        }

        // Kalau belum login atau belum aktif (udah dihapus di _checkStatus)
        // Langsung lempar ke Register sesuai permintaan
        return const RegisterScreen();
      },
    );
  }
}
