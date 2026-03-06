import 'dart:async';
import 'dart:math';

import 'package:adhan/adhan.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_compass/flutter_compass.dart';
import 'package:geolocator/geolocator.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:permission_handler/permission_handler.dart';

import 'widgets/custom_background.dart';

class QiblaScreen extends StatefulWidget {
  const QiblaScreen({super.key});

  @override
  State<QiblaScreen> createState() => _QiblaScreenState();
}

class _QiblaScreenState extends State<QiblaScreen> {
  bool _hasPermissions = false;
  CompassEvent? _lastRead;
  DateTime? _lastReadAt;
  double? _qiblaDirection; // Qibla direction from TRUE North (0-360)
  StreamSubscription<CompassEvent>? _compassSubscription;
  Coordinates? _userCoordinates;

  // Calibration & Smoothing
  double _declination = 0; // Magnetic Declination (True North Offset)
  double _smoothedHeading = 0; // Internal smoothed value
  static const platform = MethodChannel('com.example.muslim/compass');

  @override
  void initState() {
    super.initState();
    _checkPermissions();
  }

  @override
  void dispose() {
    _compassSubscription?.cancel();
    super.dispose();
  }

  Future<void> _checkPermissions() async {
    final locationStatus = await Permission.locationWhenInUse.request();
    if (locationStatus.isGranted) {
      try {
        // 1. Get High Accuracy Location
        final position = await Geolocator.getCurrentPosition(
          desiredAccuracy: LocationAccuracy.bestForNavigation, // BEST accuracy
        );
        _userCoordinates = Coordinates(position.latitude, position.longitude);

        // 2. Get Magnetic Declination (Native Android)
        await _getDeclination(position);

        // 3. Calculate Qibla Direction (Relative to True North)
        final qibla = Qibla(_userCoordinates!);

        if (mounted) {
          setState(() {
            _hasPermissions = true;
            _qiblaDirection = qibla.direction;
          });

          // 4. Listen to compass events
          _compassSubscription = FlutterCompass.events?.listen((event) {
            if (mounted) {
              setState(() {
                _lastRead = event;
                _lastReadAt = DateTime.now();
              });
            }
          });
        }
      } catch (e) {
        debugPrint("Error: $e");
      }
    } else {
      if (mounted) {
        setState(() {
          _hasPermissions = false;
        });
      }
    }
  }

  Future<void> _getDeclination(Position position) async {
    try {
      final double result = await platform.invokeMethod('getDeclination', {
        'latitude': position.latitude,
        'longitude': position.longitude,
        'altitude': position.altitude,
      });
      if (mounted) {
        setState(() {
          _declination = result;
        });
      }
    } catch (e) {
      debugPrint("Failed to get declination: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: PremiumColor.background,
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),

          // Header
          Positioned(
            top: 60,
            left: 24,
            child: GestureDetector(
              onTap: () => Navigator.pop(context),
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                        color: Colors.black.withOpacity(0.05), blurRadius: 10),
                  ],
                ),
                child: const Icon(Icons.arrow_back_ios_new_rounded,
                    color: PremiumColor.primary, size: 20),
              ),
            ),
          ),

          // Title
          Positioned(
            top: 130,
            left: 0,
            right: 0,
            child: Column(
              children: [
                Text(
                  "Arah Kiblat",
                  style: GoogleFonts.playfairDisplay(
                    fontSize: 32,
                    fontWeight: FontWeight.bold,
                    color: PremiumColor.primary,
                  ),
                ),
                Text(
                  "Gerakkan HP membentuk angka 8 untuk kalibrasi",
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    color: PremiumColor.slate500,
                  ),
                ),
              ],
            ),
          ),

          // Compass
          Center(
            child:
                _hasPermissions ? _buildCompass() : _buildPermissionWarning(),
          ),

          // Debug Overlay (Bottom)
          if (_hasPermissions)
            Positioned(
              bottom: 20,
              left: 0,
              right: 0,
              child: Center(
                child: Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                      color: Colors.black.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(12)),
                  child: Text(
                    "Mag: ${_smoothedHeading.toStringAsFixed(1)}° | Decl: ${_declination.toStringAsFixed(1)}° | Acc: ${_lastRead?.accuracy?.toStringAsFixed(0) ?? '-'}",
                    style: GoogleFonts.robotoMono(
                        fontSize: 10, color: PremiumColor.slate500),
                  ),
                ),
              ),
            )
        ],
      ),
    );
  }

  Widget _buildCompass() {
    if (_lastRead == null) {
      return const CircularProgressIndicator(color: PremiumColor.primary);
    }

    // Determine heading (Magnetic)
    final double? rawHeading = _lastRead!.heading;

    if (rawHeading == null) {
      return const Text("Sensor tidak tersedia.");
    }

    // --- SMOOTHING LOGIC (EMA) ---
    if (_smoothedHeading == 0) _smoothedHeading = rawHeading;
    double diff = rawHeading - _smoothedHeading;
    if (diff > 180) diff -= 360;
    if (diff < -180) diff += 360;
    const double alpha = 0.15;
    _smoothedHeading += diff * alpha;
    if (_smoothedHeading < 0) _smoothedHeading += 360;
    if (_smoothedHeading >= 360) _smoothedHeading -= 360;

    // --- APPLY DECLINATION ---
    double trueHeading = _smoothedHeading + _declination;
    if (trueHeading < 0) trueHeading += 360;
    if (trueHeading >= 360) trueHeading -= 360;

    // 1. Calculate Turns for Animation
    final double headingTurns = -1 * (trueHeading / 360);

    // 2. Qibla Needle Angle
    final double qiblaAngle = (_qiblaDirection ?? 0);

    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        SizedBox(
          width: 320,
          height: 320,
          child: Stack(
            alignment: Alignment.center,
            children: [
              // Fixed Outer Ring/Pivot
              Container(
                width: 310,
                height: 310,
                decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(color: PremiumColor.slate200, width: 1)),
              ),

              // --- ROTATING WORLD (Compass + Needle) ---
              AnimatedRotation(
                turns: headingTurns,
                duration: const Duration(milliseconds: 100),
                curve: Curves.linear,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    // 1. Base Compass Rose
                    Container(
                      width: 300,
                      height: 300,
                      decoration: const BoxDecoration(shape: BoxShape.circle),
                      child: CustomPaint(
                        painter: _CompassPainter(),
                      ),
                    ),

                    // 2. Qibla Needle (CLASSIC CENTER NEEDLE)
                    // Rotated relative to the Compass Rose (North)
                    Transform.rotate(
                      angle: qiblaAngle * (pi / 180),
                      child: Stack(
                        alignment: Alignment.center,
                        children: [
                          // The Needle Itself
                          Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              // Arrow Head pointing UP (North/Qibla)
                              Container(
                                width: 20,
                                height: 110,
                                decoration: BoxDecoration(
                                    gradient: LinearGradient(
                                        colors: [
                                          PremiumColor.primary,
                                          PremiumColor.primary.withOpacity(0.1)
                                        ],
                                        begin: Alignment.topCenter,
                                        end: Alignment.bottomCenter),
                                    borderRadius: BorderRadius.circular(10)),
                                child: const Column(
                                  children: [
                                    Icon(Icons.keyboard_arrow_up_rounded,
                                        color: Colors.white, size: 24),
                                    Expanded(child: SizedBox()),
                                  ],
                                ),
                              ),
                              // Counterweight (Tail)
                              Container(
                                  width: 6,
                                  height: 110,
                                  color: Colors.transparent)
                            ],
                          ),

                          // Mosque Icon at Tip for Clarity
                          Transform.translate(
                            offset: const Offset(0, -90),
                            child: Container(
                                padding: const EdgeInsets.all(4),
                                decoration: const BoxDecoration(
                                    color: Colors.white,
                                    shape: BoxShape.circle),
                                child: const Icon(Icons.mosque,
                                    size: 20, color: PremiumColor.primary)),
                          )
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // --- FIXED MARKERS ---
              // Top Triangle (Your Facing Direction)
              Positioned(
                top: 5,
                child: Icon(Icons.arrow_drop_down,
                    size: 50, color: PremiumColor.accent),
              ),

              // Center Pivot
              Container(
                width: 16,
                height: 16,
                decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    border: Border.all(color: PremiumColor.primary, width: 3),
                    boxShadow: [
                      BoxShadow(color: Colors.black26, blurRadius: 4)
                    ]),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // ... _getCardinalDirection, _getAccuracyColor, _getAccuracyText, _buildPermissionWarning ...

  String _getCardinalDirection(double heading) {
    if (heading >= 337.5 || heading < 22.5) return "U";
    if (heading >= 22.5 && heading < 67.5) return "TL";
    if (heading >= 67.5 && heading < 112.5) return "T";
    if (heading >= 112.5 && heading < 157.5) return "TG";
    if (heading >= 157.5 && heading < 202.5) return "S";
    if (heading >= 202.5 && heading < 247.5) return "BD";
    if (heading >= 247.5 && heading < 292.5) return "B";
    if (heading >= 292.5 && heading < 337.5) return "BL";
    return "";
  }

  Widget _buildPermissionWarning() {
    return Center(child: Text("Izin Lokasi Diperlukan"));
  }
}

class _CompassPainter extends CustomPainter {
  // ... (Same Painter Logic as before, essential for the Rose) ...
  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.width / 2;
    final bgPaint = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.fill;
    canvas.drawCircle(center, radius, bgPaint);

    final tickPaint = Paint()
      ..strokeCap = StrokeCap.round
      ..style = PaintingStyle.stroke;

    final textPainter = TextPainter(textDirection: TextDirection.ltr);
    final double textRadius = radius - 35;

    for (int i = 0; i < 360; i += 2) {
      final double angleRad = (i - 90) * (pi / 180);
      bool isCardinal = (i % 90 == 0);
      bool isMajor = (i % 10 == 0);

      double tickLen = isCardinal ? 16 : (isMajor ? 12 : 6);
      Color tickColor = isCardinal
          ? PremiumColor.primary
          : (isMajor ? PremiumColor.slate400 : PremiumColor.slate200);
      double tickWidth = isCardinal ? 3 : (isMajor ? 2 : 1);
      if (!isMajor) continue;

      final p1 = Offset(center.dx + (radius - 10) * cos(angleRad),
          center.dy + (radius - 10) * sin(angleRad));
      final p2 = Offset(center.dx + (radius - 10 - tickLen) * cos(angleRad),
          center.dy + (radius - 10 - tickLen) * sin(angleRad));

      tickPaint.color = tickColor;
      tickPaint.strokeWidth = tickWidth;
      canvas.drawLine(p1, p2, tickPaint);

      if (isCardinal) {
        String label = "";
        switch (i) {
          case 0:
            label = "N";
            break;
          case 90:
            label = "E";
            break;
          case 180:
            label = "S";
            break;
          case 270:
            label = "W";
            break;
        }
        final textSpan = TextSpan(
          text: label,
          style: GoogleFonts.plusJakartaSans(
              color: i == 0 ? Colors.red : PremiumColor.primary,
              fontWeight: FontWeight.bold,
              fontSize: 16),
        );
        textPainter.text = textSpan;
        textPainter.layout();
        final tp = Offset(
            center.dx + textRadius * cos(angleRad) - textPainter.width / 2,
            center.dy + textRadius * sin(angleRad) - textPainter.height / 2);
        textPainter.paint(canvas, tp);
      }
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
