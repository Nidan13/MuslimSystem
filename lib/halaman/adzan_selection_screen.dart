import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:audioplayers/audioplayers.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/muadzin_service.dart';
import '../theme/premium_color.dart';

class AdzanSelectionScreen extends StatefulWidget {
  final String prayerName;
  const AdzanSelectionScreen({super.key, required this.prayerName});

  @override
  State<AdzanSelectionScreen> createState() => _AdzanSelectionScreenState();
}

class _AdzanSelectionScreenState extends State<AdzanSelectionScreen> {
  final MuadzinService _muadzinService = MuadzinService();
  final AudioPlayer _audioPlayer = AudioPlayer();

  List<Muadzin> _muadzins = [];
  int _selectedId = -1;
  int? _playingId;
  bool _isBuffering = false;

  @override
  void initState() {
    super.initState();
    _loadData();
    _setupAudioListeners();
  }

  void _setupAudioListeners() {
    _audioPlayer.onPlayerStateChanged.listen((state) {
      if (state == PlayerState.completed || state == PlayerState.stopped) {
        if (mounted) {
          setState(() {
            _playingId = null;
            _isBuffering = false;
          });
        }
      }
      if (state == PlayerState.playing) {
        if (mounted) setState(() => _isBuffering = false);
      }
    });
  }

  @override
  void dispose() {
    _audioPlayer.stop();
    _audioPlayer.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() {
      _muadzins = _muadzinService.getDefaultMuadzins();
    });

    final prefs = await SharedPreferences.getInstance();
    final savedId =
        prefs.getInt('adzan_selection_${widget.prayerName.toLowerCase()}');
    if (savedId != null) {
      if (mounted) setState(() => _selectedId = savedId);
    }

    try {
      final list = await _muadzinService.getAllMuadzins();
      if (mounted && list.isNotEmpty) {
        setState(() {
          _muadzins = list;
        });
      }
    } catch (e) {
      debugPrint("Error loading muadzins: $e");
    }
  }

  Future<void> _saveSelection(int id) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(
        'adzan_selection_${widget.prayerName.toLowerCase()}', id);
    if (mounted) setState(() => _selectedId = id);

    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("Suara Adzan ${widget.prayerName} Berhasil Diterapkan"),
          backgroundColor: PremiumColor.primary,
          behavior: SnackBarBehavior.floating,
          duration: const Duration(seconds: 2),
        ),
      );
    }
  }

  Future<void> _togglePlay(Muadzin m) async {
    try {
      if (_playingId == m.id) {
        await _audioPlayer.stop();
        if (mounted) setState(() => _playingId = null);
      } else {
        await _audioPlayer.stop();
        final urlToPlay = widget.prayerName.toLowerCase() == 'fajr'
            ? m.audioUrlShubuh
            : m.audioUrlBiasa;

        if (urlToPlay.isNotEmpty) {
          if (mounted) {
            setState(() {
              _playingId = m.id;
              _isBuffering = true;
            });
          }
          if (urlToPlay.startsWith('http')) {
            await _audioPlayer.play(UrlSource(urlToPlay));
          } else {
            await _audioPlayer.play(AssetSource(urlToPlay));
          }
          await Future.delayed(const Duration(milliseconds: 500));
          if (mounted && _audioPlayer.state == PlayerState.playing) {
            setState(() => _isBuffering = false);
          }
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _playingId = null;
          _isBuffering = false;
        });
      }
    }
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
          "Notifikasi Waktu ${widget.prayerName}",
          style: GoogleFonts.plusJakartaSans(
            color: Colors.white,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: Column(
        children: [
          _buildReminderSection(),
          const Divider(height: 1, thickness: 8, color: Color(0xFFF5F7F9)),
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 10),
                  child: Text(
                    "Pilih Notifikasi Waktu ${widget.prayerName}",
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 15,
                      fontWeight: FontWeight.w800,
                      color: Colors.black,
                    ),
                  ),
                ),
                ..._muadzins.map((m) => _buildOptionRow(m)),
                const SizedBox(height: 120),
              ],
            ),
          ),
        ],
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20),
        width: double.infinity,
        child: ElevatedButton(
          onPressed: () => Navigator.pop(context),
          style: ElevatedButton.styleFrom(
            backgroundColor: PremiumColor.primary,
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            elevation: 4,
          ),
          child: Text(
            "Terapkan Pilihan",
            style: GoogleFonts.plusJakartaSans(
                color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
          ),
        ),
      ),
    );
  }

  Widget _buildReminderSection() {
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      title: Text(
        "Pengingat Sebelum Adzan",
        style: GoogleFonts.plusJakartaSans(
          fontSize: 15,
          fontWeight: FontWeight.w800,
          color: Colors.black,
        ),
      ),
      subtitle: Padding(
        padding: const EdgeInsets.only(top: 16.0),
        child: Row(
          children: [
            const Icon(Icons.notifications_outlined,
                color: Colors.black87, size: 24),
            const SizedBox(width: 16),
            Text(
              "10 Menit",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 15,
                fontWeight: FontWeight.w500,
                color: Colors.black87,
              ),
            ),
            const Spacer(),
            const Icon(Icons.chevron_right_rounded,
                color: Colors.black26, size: 28),
          ],
        ),
      ),
    );
  }

  Widget _buildOptionRow(Muadzin m) {
    final isSelected = _selectedId == m.id;
    final isPlaying = _playingId == m.id;
    final hasAudio = m.audioUrl.isNotEmpty;

    return Column(
      children: [
        InkWell(
          onTap: () => _saveSelection(m.id),
          child: Container(
            color: isSelected
                ? PremiumColor.primary.withOpacity(0.05)
                : Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            child: Row(
              children: [
                _getIconForMuadzin(m),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        m.name,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14,
                          fontWeight:
                              isSelected ? FontWeight.bold : FontWeight.w500,
                          color: Colors.black,
                        ),
                      ),
                      if (isSelected)
                        Text(
                          "Dipilih",
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 11,
                            color: PremiumColor.primary,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                    ],
                  ),
                ),
                if (hasAudio)
                  SizedBox(
                    width: 40,
                    height: 40,
                    child: (isPlaying && _isBuffering)
                        ? const Padding(
                            padding: EdgeInsets.all(10.0),
                            child: CircularProgressIndicator(
                                strokeWidth: 2, color: PremiumColor.primary),
                          )
                        : IconButton(
                            iconSize: 28,
                            padding: EdgeInsets.zero,
                            constraints: const BoxConstraints(),
                            icon: Icon(
                              isPlaying
                                  ? Icons.pause_circle_filled_rounded
                                  : Icons.play_circle_fill_rounded,
                              color: PremiumColor.primary,
                            ),
                            onPressed: () => _togglePlay(m),
                          ),
                  ),
                const SizedBox(width: 16),
                Icon(
                  isSelected
                      ? Icons.radio_button_checked_rounded
                      : Icons.radio_button_off_rounded,
                  color: isSelected ? PremiumColor.primary : Colors.black12,
                  size: 24,
                ),
              ],
            ),
          ),
        ),
        const Padding(
          padding: EdgeInsets.only(left: 56.0),
          child: Divider(height: 1, color: Color(0xFFF5F7F9)),
        ),
      ],
    );
  }

  Widget _getIconForMuadzin(Muadzin m) {
    IconData iconData;
    switch (m.type) {
      case MuadzinType.system:
        if (m.id == -3)
          iconData = Icons.block_rounded;
        else if (m.id == -2)
          iconData = Icons.volume_off_rounded;
        else
          iconData = Icons.notifications_none_rounded;
        break;
      case MuadzinType.cultural:
      case MuadzinType.adhan:
        iconData = Icons.volume_up_outlined;
        break;
    }
    return Icon(iconData, color: Colors.black38, size: 20);
  }
}
