import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:percent_indicator/percent_indicator.dart';
import 'package:audioplayers/audioplayers.dart';
import '../theme/premium_color.dart';

class TasbihScreen extends StatefulWidget {
  const TasbihScreen({super.key});

  @override
  State<TasbihScreen> createState() => _TasbihScreenState();
}

class _TasbihScreenState extends State<TasbihScreen> {
  int _counter = 0;
  int _target = 33;
  bool _isSoundOn = true;
  bool _isVibrateOn = true;
  final AudioPlayer _audioPlayer = AudioPlayer();

  @override
  void initState() {
    super.initState();
    _initAudio();
  }

  Future<void> _initAudio() async {
    try {
      await _audioPlayer.setSource(
          UrlSource('https://www.soundjay.com/buttons/button-16.mp3'));
      await _audioPlayer.setReleaseMode(ReleaseMode.stop);
      await _audioPlayer.setVolume(1.0);
    } catch (e) {
      debugPrint("Audio init error: $e");
    }
  }

  void _incrementCounter() {
    try {
      setState(() {
        _counter++;
        if (_counter > _target) {
          _counter = 1;
        }
      });

      if (_isVibrateOn) {
        HapticFeedback.lightImpact();
      }

      if (_isSoundOn) {
        // Gabungkan suara sistem dan audio player biar makin mantap
        SystemSound.play(SystemSoundType.click);
        _audioPlayer.play(
          UrlSource('https://www.soundjay.com/buttons/button-16.mp3'),
          volume: 1.0,
        );
      }

      if (_counter == _target) {
        if (_isVibrateOn) {
          HapticFeedback.vibrate();
        }
      }
    } catch (e) {
      debugPrint("Increment error: $e");
    }
  }

  void _decrementCounter() {
    if (_counter > 0) {
      setState(() {
        _counter--;
      });
      if (_isVibrateOn) HapticFeedback.lightImpact();
    }
  }

  void _resetCounter() {
    setState(() {
      _counter = 0;
    });
    if (_isVibrateOn) HapticFeedback.heavyImpact();
  }

  void _toggleTarget() {
    setState(() {
      if (_target == 33)
        _target = 99;
      else if (_target == 99)
        _target = 100;
      else
        _target = 33;
    });
  }

  @override
  void dispose() {
    _audioPlayer.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: PremiumColor.primary,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded,
              color: Colors.white, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          "Tasbih",
          style: GoogleFonts.plusJakartaSans(
            color: Colors.white,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.checklist_rtl_rounded, color: Colors.white),
            onPressed: _toggleTarget,
          ),
          IconButton(
            icon: const Icon(Icons.edit_note_rounded, color: Colors.white),
            onPressed: () {},
          ),
        ],
      ),
      body: GestureDetector(
        onTap: _incrementCounter,
        behavior: HitTestBehavior.opaque,
        child: SizedBox.expand(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularPercentIndicator(
                radius: 140.0,
                lineWidth: 12.0,
                percent: (_counter / _target).clamp(0.0, 1.0),
                center: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      "$_counter",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 80,
                        fontWeight: FontWeight.bold,
                        color: Colors.black,
                      ),
                    ),
                    Text(
                      "/ $_target",
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 24,
                        fontWeight: FontWeight.w600,
                        color: Colors.black38,
                      ),
                    ),
                  ],
                ),
                circularStrokeCap: CircularStrokeCap.round,
                backgroundColor: const Color(0xFFF5F5F5),
                progressColor: PremiumColor.primary,
                animation: true,
                animateFromLastPercent: true,
                animationDuration: 150,
              ),
              const SizedBox(height: 100),
            ],
          ),
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 10),
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
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: [
            _buildBottomAction(
              icon: _isSoundOn
                  ? Icons.volume_up_rounded
                  : Icons.volume_off_rounded,
              label: "Suara: ${_isSoundOn ? 'On' : 'Off'}",
              onTap: () => setState(() => _isSoundOn = !_isSoundOn),
            ),
            _buildBottomAction(
              icon: _isVibrateOn
                  ? Icons.vibration_rounded
                  : Icons.do_not_disturb_on_total_silence_rounded,
              label: "Getar: ${_isVibrateOn ? 'On' : 'Off'}",
              onTap: () => setState(() => _isVibrateOn = !_isVibrateOn),
            ),
            _buildBottomAction(
              icon: Icons.remove_circle_outline_rounded,
              label: "Kurangi",
              onTap: _decrementCounter,
            ),
            _buildBottomAction(
              icon: Icons.refresh_rounded,
              label: "Reset",
              onTap: _resetCounter,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBottomAction(
      {required IconData icon,
      required String label,
      required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: PremiumColor.primary, size: 24),
          const SizedBox(height: 6),
          Text(
            label,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 11,
              fontWeight: FontWeight.w600,
              color: Colors.black54,
            ),
          ),
        ],
      ),
    );
  }
}
