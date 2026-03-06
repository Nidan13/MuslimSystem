import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:hijri/hijri_calendar.dart';
import 'package:intl/intl.dart';
import '../services/calendar_service.dart';
import '../theme/premium_color.dart';

class CalendarScreen extends StatefulWidget {
  const CalendarScreen({super.key});

  @override
  State<CalendarScreen> createState() => _CalendarScreenState();
}

class _CalendarScreenState extends State<CalendarScreen> {
  DateTime _focusedDay = DateTime.now();
  DateTime _selectedDay = DateTime.now();
  final CalendarService _calendarService = CalendarService();
  List<Map<String, dynamic>> _events = [];
  bool _isLoadingEvents = false;

  final List<String> _pasaran = ['Legi', 'Pahing', 'Pon', 'Wage', 'Kliwon'];
  final List<String> _daysInIndonesian = [
    'Ahad',
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jumat',
    'Sabtu'
  ];

  @override
  void initState() {
    super.initState();
    _fetchEvents();
  }

  Future<void> _fetchEvents() async {
    if (!mounted) return;
    setState(() => _isLoadingEvents = true);

    final events = await _calendarService.getIslamicEvents(
        _focusedDay.year, _focusedDay.month);

    if (!mounted) return;
    setState(() {
      _events = events;
      _isLoadingEvents = false;
    });
  }

  String _getPasaran(DateTime date) {
    final difference = date.difference(DateTime(1970, 1, 1)).inDays;
    final index = (difference + 3) % 5;
    return _pasaran[index < 0 ? index + 5 : index];
  }

  String _getDayNameIndo(DateTime date) {
    final weekday = date.weekday % 7;
    return _daysInIndonesian[weekday];
  }

  void _onMonthChange(int offset) {
    setState(() {
      _focusedDay = DateTime(_focusedDay.year, _focusedDay.month + offset, 1);
    });
    _fetchEvents();
  }

  @override
  Widget build(BuildContext context) {
    final hijriSelected = HijriCalendar.fromDate(_selectedDay);

    return Scaffold(
      backgroundColor: Colors.white,
      body: Column(
        children: [
          _buildTopHeader(hijriSelected),
          _buildMonthNavigation(),
          _buildWeekdayLabels(),
          Expanded(
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              child: Column(
                children: [
                  _buildCalendarGrid(),
                  const Divider(thickness: 1, height: 1),
                  _buildSelectedDayDetailHeader(hijriSelected),
                  _buildEventsSection(),
                  const SizedBox(height: 80),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTopHeader(HijriCalendar hijri) {
    return Container(
      width: double.infinity,
      color: PremiumColor.primary,
      padding: const EdgeInsets.fromLTRB(20, 50, 20, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    "${_getDayNameIndo(_selectedDay)} ${_getPasaran(_selectedDay)}, ${DateFormat('d MMMM yyyy', 'id_ID').format(_selectedDay)}",
                    style: GoogleFonts.plusJakartaSans(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 13),
                  ),
                  Text(
                    "${hijri.hDay} ${hijri.longMonthName} ${hijri.hYear}",
                    style: GoogleFonts.plusJakartaSans(
                        color: Colors.white.withOpacity(0.9), fontSize: 12),
                  ),
                ],
              ),
              Row(
                children: [
                  IconButton(
                      onPressed: () {},
                      icon: const Icon(Icons.info_outline_rounded,
                          color: Colors.white, size: 24)),
                  IconButton(
                      onPressed: () {},
                      icon: const Icon(Icons.settings_outlined,
                          color: Colors.white, size: 24)),
                ],
              )
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMonthNavigation() {
    final first = DateTime(_focusedDay.year, _focusedDay.month, 1);
    final last = DateTime(_focusedDay.year, _focusedDay.month + 1, 0);
    final hijriFirst = HijriCalendar.fromDate(first);
    final hijriLast = HijriCalendar.fromDate(last);

    String hijriSpan = hijriFirst.longMonthName == hijriLast.longMonthName
        ? "${hijriFirst.longMonthName} ${hijriFirst.hYear}"
        : "${hijriFirst.longMonthName} - ${hijriLast.longMonthName} ${hijriLast.hYear}";

    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          IconButton(
              onPressed: () => _onMonthChange(-1),
              icon:
                  const Icon(Icons.chevron_left, color: PremiumColor.primary)),
          Column(
            children: [
              Text(
                DateFormat('MMMM yyyy', 'id_ID').format(_focusedDay),
                style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                    color: Colors.black87),
              ),
              Text(
                hijriSpan,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    color: Colors.black54,
                    fontWeight: FontWeight.w600),
              ),
            ],
          ),
          IconButton(
              onPressed: () => _onMonthChange(1),
              icon:
                  const Icon(Icons.chevron_right, color: PremiumColor.primary)),
        ],
      ),
    );
  }

  Widget _buildWeekdayLabels() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8),
      decoration: BoxDecoration(
          border: Border(bottom: BorderSide(color: Colors.grey.shade100))),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: _daysInIndonesian
            .map((day) => Expanded(
                  child: Center(
                    child: Text(
                      day,
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87),
                    ),
                  ),
                ))
            .toList(),
      ),
    );
  }

  Widget _buildCalendarGrid() {
    final daysInMonth =
        DateTime(_focusedDay.year, _focusedDay.month + 1, 0).day;
    final firstDayWeekday =
        DateTime(_focusedDay.year, _focusedDay.month, 1).weekday % 7;

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 4),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 7,
        childAspectRatio: 0.9,
      ),
      itemCount: 42,
      itemBuilder: (context, index) {
        final day = index - firstDayWeekday + 1;
        if (day < 1 || day > daysInMonth) return const SizedBox.shrink();

        final date = DateTime(_focusedDay.year, _focusedDay.month, day);
        final hDate = HijriCalendar.fromDate(date);
        final isSelected = date.year == _selectedDay.year &&
            date.month == _selectedDay.month &&
            date.day == _selectedDay.day;
        final isToday = date.year == DateTime.now().year &&
            date.month == DateTime.now().month &&
            date.day == DateTime.now().day;

        final pasaran = _getPasaran(date);

        final eventForDay = _events.firstWhere(
          (e) => e['day'] == day && e['month'] == _focusedDay.month,
          orElse: () => {},
        );
        final bool isSpecialHoliday =
            eventForDay.isNotEmpty && (eventForDay['isHoliday'] == true);
        final bool isSunday = date.weekday == 7;

        // Determine day color
        Color dayColor = Colors.black87;
        if (isSunday || (eventForDay['color'] == 'red')) {
          dayColor = Colors.red;
        } else if (eventForDay['color'] == 'orange') {
          dayColor = Colors.deepOrange;
        } else if (isToday) {
          dayColor = PremiumColor.primary;
        }

        return InkWell(
          onTap: () => setState(() => _selectedDay = date),
          child: Container(
            margin: const EdgeInsets.all(2),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              color: isSelected
                  ? PremiumColor.primary.withOpacity(0.1)
                  : Colors.transparent,
              border: isSelected
                  ? Border.all(color: PremiumColor.primary, width: 1.5)
                  : null,
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  _toArabicDigits(hDate.hDay),
                  style: GoogleFonts.notoKufiArabic(
                      fontSize: 10,
                      color: (isSunday || isSpecialHoliday)
                          ? Colors.red
                          : PremiumColor.primary,
                      fontWeight: FontWeight.bold),
                ),
                Text(
                  day.toString(),
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: dayColor,
                  ),
                ),
                Text(
                  pasaran,
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 7,
                      color: Colors.black38,
                      fontWeight: FontWeight.w500),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  String _toArabicDigits(int n) {
    const digits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    return n.toString().split('').map((e) => digits[int.parse(e)]).join();
  }

  Widget _buildSelectedDayDetailHeader(HijriCalendar hijri) {
    return Container(
      width: double.infinity,
      color: Colors.grey.shade50,
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 20),
      child: Center(
        child: Text(
          "${_getDayNameIndo(_selectedDay)}, ${DateFormat('d MMMM yyyy', 'id_ID').format(_selectedDay)} / ${hijri.hDay} ${hijri.longMonthName} ${hijri.hYear}",
          style: GoogleFonts.plusJakartaSans(
              fontSize: 12, color: Colors.black54, fontWeight: FontWeight.w600),
        ),
      ),
    );
  }

  Widget _buildEventsSection() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      child: Column(
        children: [
          if (_isLoadingEvents)
            const Center(
                child: Padding(
              padding: EdgeInsets.all(40.0),
              child: CircularProgressIndicator(color: PremiumColor.primary),
            ))
          else if (_events.isEmpty)
            Padding(
              padding: const EdgeInsets.symmetric(vertical: 40),
              child: Center(
                  child: Text("Tidak ada hari besar di bulan ini",
                      style: GoogleFonts.plusJakartaSans(
                          color: Colors.grey, fontSize: 13))),
            )
          else
            ..._events.map((e) {
              final date = DateTime(e['year'], e['month'], e['day']);
              final hDate = e['hijri_day'] != ''
                  ? "${e['hijri_day']} ${e['hijri_month']} ${e['hijri_year']}"
                  : "";

              // Get Color based on event data
              Color boxColor;
              switch (e['color']) {
                case 'red':
                  boxColor = Colors.red;
                  break;
                case 'orange':
                  boxColor = Colors.deepOrange;
                  break;
                case 'green':
                  boxColor = Colors.teal;
                  break;
                case 'blue':
                  boxColor = const Color(0xFF2C9EB0);
                  break;
                default:
                  boxColor = PremiumColor.primary;
              }

              return _buildEventItem(
                DateFormat('MMM', 'id_ID').format(date),
                e['day'].toString(),
                e['title'].toString(),
                "${DateFormat('EEEE, d MMMM yyyy', 'id_ID').format(date)}${hDate != '' ? ' / $hDate' : ''}",
                type: e['type'] ?? 'Event',
                color: boxColor,
              );
            }),
        ],
      ),
    );
  }

  Widget _buildEventItem(
      String month, String day, String title, String subtitle,
      {required String type, required Color color}) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 50,
                padding: const EdgeInsets.symmetric(vertical: 8),
                decoration: BoxDecoration(
                  border: Border.all(color: color.withOpacity(0.3)),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Column(
                  children: [
                    Text(month,
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 10,
                            color: Colors.grey,
                            fontWeight: FontWeight.w500)),
                    Text(day,
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: color)),
                  ],
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                          color: Colors.black87,
                          height: 1.3),
                    ),
                    const SizedBox(height: 4),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        type.toUpperCase(),
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 8,
                            color: color,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 0.5),
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      subtitle,
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          color: Colors.grey.shade600,
                          fontWeight: FontWeight.w500),
                    ),
                  ],
                ),
              ),
              IconButton(
                onPressed: () {},
                icon: Icon(Icons.info_outline,
                    color: Colors.grey.shade400, size: 20),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
        ),
        const Divider(height: 1),
      ],
    );
  }
}
