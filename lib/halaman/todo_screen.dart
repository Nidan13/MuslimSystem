import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'widgets/muqarnas_clipper.dart';
import '../models/todo.dart';
import '../services/todo_service.dart';
import 'template_selection_screen.dart';

class ToDoScreen extends StatefulWidget {
  const ToDoScreen({super.key});

  @override
  State<ToDoScreen> createState() => _ToDoScreenState();
}

class _ToDoScreenState extends State<ToDoScreen> with TickerProviderStateMixin {
  final TodoService _todoService = TodoService();
  List<Todo> _activeTodos = [];
  List<Todo> _completedTodos = [];
  bool _isLoading = true;

  final Color _colPrimary = const Color(0xFF0E5F71);
  final Color _colGold = const Color(0xFFFFD700);

  @override
  void initState() {
    super.initState();
    _fetchTodos();
  }

  Future<void> _fetchTodos() async {
    setState(() => _isLoading = true);
    try {
      final result = await _todoService.getTodos();
      if (mounted) {
        setState(() {
          _activeTodos = result['active'] ?? [];
          _completedTodos = result['completed'] ?? [];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor:
          const Color(0xFFF8F9FA), // Reverted to original light background
      body: Stack(
        children: [
          // Background Header with original Muqarnas style
          CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              _buildSliverHeader(),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
                  child: _isLoading
                      ? _buildLoadingState()
                      : Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            if (_activeTodos.isEmpty && _completedTodos.isEmpty)
                              _buildEmptyState()
                            else ...[
                              if (_activeTodos.isNotEmpty) ...[
                                _buildSectionTitle("RENCANA AKTIF",
                                    Icons.track_changes_rounded),
                                const SizedBox(height: 16),
                                ..._activeTodos.map((todo) =>
                                    _buildDismissibleToDoTile(todo, false)),
                              ],
                              if (_completedTodos.isNotEmpty) ...[
                                const SizedBox(height: 40),
                                _buildSectionTitle("TARGET TERCAPAI",
                                    Icons.verified_user_rounded),
                                const SizedBox(height: 16),
                                ..._completedTodos.map((todo) =>
                                    _buildDismissibleToDoTile(todo, true)),
                              ],
                            ],
                          ],
                        ),
                ),
              ),
            ],
          ),
        ],
      ),
      floatingActionButton: _buildFab(),
    );
  }

  Widget _buildSliverHeader() {
    return SliverAppBar(
      expandedHeight: 220,
      backgroundColor: _colPrimary,
      elevation: 0,
      pinned: true,
      automaticallyImplyLeading: false,
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: BoxDecoration(
              color: _colPrimary,
              image: const DecorationImage(
                image: NetworkImage(
                    "https://www.transparenttextures.com/patterns/handmade-paper.png"),
                opacity: 0.1,
                fit: BoxFit.cover,
              ),
            ),
            padding: const EdgeInsets.fromLTRB(24, 80, 24, 40),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Target & Rencana",
                  style: GoogleFonts.playfairDisplay(
                    color: Colors.white,
                    fontSize: 32,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    _buildHeaderStat("${_activeTodos.length} Rencana",
                        Icons.calendar_today_rounded),
                    const SizedBox(width: 8),
                    _buildHeaderStat("${_completedTodos.length} Tercapai",
                        Icons.task_alt_rounded),
                    const Spacer(),
                    GestureDetector(
                      onTap: () async {
                        final result = await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const TemplateSelectionScreen(
                                templateType: 'todo'),
                          ),
                        );
                        if (result == true) _fetchTodos();
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 8),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.white24),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.auto_awesome_motion_rounded,
                                color: Colors.white, size: 16),
                            const SizedBox(width: 6),
                            Text("TEMPLATE",
                                style: GoogleFonts.plusJakartaSans(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.white,
                                    letterSpacing: 0.5)),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderStat(String text, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.15),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: _colGold, size: 14),
          const SizedBox(width: 6),
          Text(
            text,
            style: GoogleFonts.plusJakartaSans(
              color: Colors.white,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, color: _colPrimary.withOpacity(0.3), size: 18),
        const SizedBox(width: 8),
        Text(
          title,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12,
            fontWeight: FontWeight.w900,
            color: _colPrimary.withOpacity(0.4),
            letterSpacing: 2.0,
          ),
        ),
      ],
    );
  }

  Widget _buildDismissibleToDoTile(Todo todo, bool isCompleted) {
    return Dismissible(
      key: Key('todo_${todo.id}'),
      direction: DismissDirection.endToStart,
      background: Container(
        margin: const EdgeInsets.only(bottom: 16),
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 24),
        decoration: BoxDecoration(
          color: Colors.red.withOpacity(0.1),
          borderRadius: BorderRadius.circular(24),
        ),
        child: const Icon(Icons.delete_outline_rounded,
            color: Colors.redAccent, size: 28),
      ),
      onDismissed: (_) {
        _todoService.deleteTodo(todo.id);
        setState(() {
          if (isCompleted) {
            _completedTodos.remove(todo);
          } else {
            _activeTodos.remove(todo);
          }
        });
      },
      child: _buildToDoTile(todo, isCompleted),
    );
  }

  Widget _buildToDoTile(Todo todo, bool isCompleted) {
    final Color diffColor = _getDifficultyColor(todo.difficulty);
    final totalChecklist = todo.checklist.length;
    final completedChecklist =
        todo.checklist.where((e) => e.isCompleted).length;
    final progress =
        totalChecklist > 0 ? (completedChecklist / totalChecklist) : 0.0;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            onLongPress: () => _showEditTodoModal(todo),
            child: IntrinsicHeight(
              child: Row(
                children: [
                  // Progress Ring / Check Button
                  _buildCheckButton(todo, isCompleted, diffColor),

                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(4, 20, 20, 20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  todo.title,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 16,
                                    color: isCompleted
                                        ? Colors.grey.shade400
                                        : const Color(0xFF1F2937),
                                    decoration: isCompleted
                                        ? TextDecoration.lineThrough
                                        : null,
                                  ),
                                ),
                              ),
                              _buildDifficultyIcon(todo.difficulty),
                              const SizedBox(width: 8),
                              _buildRewardBadge(
                                  "+${todo.rewards['xp']} EXP", Colors.blue),
                            ],
                          ),
                          if (todo.notes != null && todo.notes!.isNotEmpty) ...[
                            const SizedBox(height: 6),
                            Text(
                              todo.notes!,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 13,
                                color: Colors.grey.shade500,
                              ),
                            ),
                          ],

                          // Sub-task progress
                          if (totalChecklist > 0) ...[
                            const SizedBox(height: 16),
                            _buildSubTaskProgress(
                                completedChecklist,
                                totalChecklist,
                                progress,
                                diffColor,
                                isCompleted),
                          ],

                          const SizedBox(height: 16),
                          Row(
                            children: [
                              if (todo.dueDate != null)
                                _buildIconInfo(
                                    Icons.event_note_rounded,
                                    DateFormat('dd MMM yyyy')
                                        .format(todo.dueDate!),
                                    Colors.blue.shade700),
                              const Spacer(),
                              if (isCompleted)
                                _buildIconInfo(Icons.check_circle_rounded,
                                    "TERLAKSANA", Colors.green),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildCheckButton(Todo todo, bool isCompleted, Color diffColor) {
    return InkWell(
      onTap: isCompleted ? null : () => _handleComplete(todo),
      child: Container(
        width: 72,
        alignment: Alignment.center,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          width: 42,
          height: 42,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: isCompleted ? Colors.green.shade50 : Colors.white,
            border: Border.all(
              color: isCompleted ? Colors.green : diffColor.withOpacity(0.2),
              width: 2,
            ),
            boxShadow: [
              BoxShadow(
                color:
                    (isCompleted ? Colors.green : diffColor).withOpacity(0.1),
                blurRadius: 6,
              )
            ],
          ),
          child: isCompleted
              ? const Icon(Icons.check_rounded, color: Colors.green, size: 24)
              : Icon(Icons.radio_button_unchecked_rounded,
                  color: diffColor.withOpacity(0.4), size: 20),
        ),
      ),
    );
  }

  Widget _buildSubTaskProgress(
      int current, int total, double progress, Color color, bool isCompleted) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              "PROGRES RENCANA",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.w900,
                color: Colors.grey.shade400,
                letterSpacing: 1.0,
              ),
            ),
            Text(
              "$current/$total Langkah",
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: isCompleted ? Colors.green : _colPrimary,
              ),
            ),
          ],
        ),
        const SizedBox(height: 6),
        Container(
          height: 4,
          width: double.infinity,
          decoration: BoxDecoration(
            color: Colors.grey.shade100,
            borderRadius: BorderRadius.circular(2),
          ),
          child: FractionallySizedBox(
            alignment: Alignment.centerLeft,
            widthFactor: progress,
            child: Container(
              decoration: BoxDecoration(
                color: isCompleted ? Colors.green : color,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildIconInfo(IconData icon, String text, Color color) {
    return Row(
      children: [
        Icon(icon, size: 14, color: color.withOpacity(0.5)),
        const SizedBox(width: 4),
        Text(
          text,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: color.withOpacity(0.7),
          ),
        ),
      ],
    );
  }

  Widget _buildRewardBadge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: color.withOpacity(0.15)),
      ),
      child: Text(
        text,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 10,
          fontWeight: FontWeight.w900,
          color: color,
        ),
      ),
    );
  }

  Widget _buildDifficultyIcon(String diff) {
    IconData icon;
    Color color;
    switch (diff.toLowerCase()) {
      case 'trivial':
        icon = Icons.lightbulb_outline_rounded;
        color = Colors.grey;
        break;
      case 'easy':
        icon = Icons.assignment_rounded;
        color = Colors.green;
        break;
      case 'medium':
        icon = Icons.trending_up_rounded;
        color = Colors.orange;
        break;
      case 'hard':
        icon = Icons.auto_graph_rounded;
        color = Colors.blue;
        break;
      default:
        icon = Icons.info_outline_rounded;
        color = Colors.blue;
    }
    return Icon(icon, color: color, size: 16);
  }

  Color _getDifficultyColor(String diff) {
    switch (diff.toLowerCase()) {
      case 'trivial':
        return Colors.grey;
      case 'easy':
        return Colors.green;
      case 'medium':
        return Colors.orange;
      case 'hard':
        return Colors.blue;
      default:
        return _colPrimary;
    }
  }

  Widget _buildLoadingState() {
    return Padding(
      padding: const EdgeInsets.only(top: 100),
      child: Column(
        children: [
          CircularProgressIndicator(color: _colPrimary.withOpacity(0.1)),
          const SizedBox(height: 16),
          Text("Memuat data rencana...",
              style: GoogleFonts.plusJakartaSans(
                  color: _colPrimary.withOpacity(0.3))),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 80),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: _colPrimary.withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.edit_calendar_rounded,
                size: 64, color: _colPrimary.withOpacity(0.1)),
          ),
          const SizedBox(height: 24),
          Text(
            "Belum ada rencana",
            style: GoogleFonts.playfairDisplay(
              color: _colPrimary,
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            "Tuliskan target masa depan Anda hari ini.",
            style: GoogleFonts.plusJakartaSans(
              color: _colPrimary.withOpacity(0.4),
              fontSize: 14,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFab() {
    return FloatingActionButton.extended(
      onPressed: _showAddTodoModal,
      backgroundColor: _colPrimary,
      foregroundColor: Colors.white,
      elevation: 4,
      icon: const Icon(Icons.add_rounded, size: 24),
      label: Text(
        "BUAT TARGET",
        style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w900, letterSpacing: 1.0),
      ),
    );
  }

  void _showAddTodoModal() {
    final titleController = TextEditingController();
    final notesController = TextEditingController();
    final subTaskController = TextEditingController();
    List<String> subTasks = [];
    String difficulty = 'easy';
    DateTime? selectedDate;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => Container(
          height: MediaQuery.of(context).size.height * 0.9,
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(40)),
          ),
          child: Column(
            children: [
              // Modal Header
              Container(
                padding: const EdgeInsets.fromLTRB(28, 20, 20, 20),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("BUAT RENCANA",
                            style: GoogleFonts.plusJakartaSans(
                              color: _colPrimary,
                              fontSize: 12,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 2.0,
                            )),
                        Text("Target Masa Depan",
                            style: GoogleFonts.playfairDisplay(
                              color: const Color(0xFF1F2937),
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            )),
                      ],
                    ),
                    IconButton(
                      icon: Icon(Icons.close_rounded,
                          color: _colPrimary.withOpacity(0.2)),
                      onPressed: () => Navigator.pop(context),
                    )
                  ],
                ),
              ),

              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: 28),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildModalLabel("APA TARGET ANDA?"),
                      _buildModalField(
                          titleController,
                          "Contoh: Tabungan buat resto...",
                          Icons.insights_rounded),
                      const SizedBox(height: 24),
                      _buildModalLabel("DETAIL RENCANA"),
                      _buildModalField(notesController, "Keterangan detail...",
                          Icons.notes_rounded,
                          maxLines: 2),
                      const SizedBox(height: 32),
                      _buildModalLabel("TINGKAT PRIORITAS"),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          _buildDiffOption('trivial', 'RENDAH', difficulty,
                              (v) => setModalState(() => difficulty = v)),
                          _buildDiffOption('easy', 'SEDANG', difficulty,
                              (v) => setModalState(() => difficulty = v)),
                          _buildDiffOption('medium', 'TINGGI', difficulty,
                              (v) => setModalState(() => difficulty = v)),
                          _buildDiffOption('hard', 'UTAMA', difficulty,
                              (v) => setModalState(() => difficulty = v)),
                        ],
                      ),
                      const SizedBox(height: 32),
                      _buildModalLabel("TAHAPAN PENCAPAIAN"),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade50,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.grey.shade100),
                        ),
                        child: Column(
                          children: [
                            ...subTasks.asMap().entries.map((entry) => Padding(
                                  padding: const EdgeInsets.only(bottom: 8),
                                  child: Row(
                                    children: [
                                      Icon(Icons.arrow_right_alt_rounded,
                                          color: _colPrimary.withOpacity(0.3),
                                          size: 16),
                                      const SizedBox(width: 12),
                                      Expanded(
                                          child: Text(entry.value,
                                              style:
                                                  GoogleFonts.plusJakartaSans(
                                                      color: const Color(
                                                          0xFF4B5563)))),
                                      IconButton(
                                        icon: const Icon(
                                            Icons.remove_circle_outline_rounded,
                                            color: Colors.redAccent,
                                            size: 20),
                                        onPressed: () => setModalState(
                                            () => subTasks.removeAt(entry.key)),
                                      )
                                    ],
                                  ),
                                )),
                            Row(
                              children: [
                                Expanded(
                                  child: TextField(
                                    controller: subTaskController,
                                    style: GoogleFonts.plusJakartaSans(
                                        color: const Color(0xFF1F2937),
                                        fontSize: 13),
                                    decoration: InputDecoration(
                                      hintText: "Tambah langkah...",
                                      hintStyle: GoogleFonts.plusJakartaSans(
                                          color: Colors.grey.shade400),
                                      border: InputBorder.none,
                                    ),
                                    onSubmitted: (val) {
                                      if (val.isNotEmpty) {
                                        setModalState(() {
                                          subTasks.add(val);
                                          subTaskController.clear();
                                        });
                                      }
                                    },
                                  ),
                                ),
                                IconButton(
                                  icon: Icon(Icons.add_circle_outline_rounded,
                                      color: _colPrimary),
                                  onPressed: () {
                                    if (subTaskController.text.isNotEmpty) {
                                      setModalState(() {
                                        subTasks.add(subTaskController.text);
                                        subTaskController.clear();
                                      });
                                    }
                                  },
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 32),
                      _buildModalLabel("TARGET TENGGAT"),
                      _buildDatePicker(selectedDate,
                          (date) => setModalState(() => selectedDate = date)),
                      const SizedBox(height: 40),
                    ],
                  ),
                ),
              ),

              // Action Button
              Container(
                padding: const EdgeInsets.all(28),
                decoration: BoxDecoration(
                  color: Colors.white,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.05),
                      blurRadius: 20,
                      offset: const Offset(0, -5),
                    )
                  ],
                ),
                child: SizedBox(
                  width: double.infinity,
                  height: 64,
                  child: ElevatedButton(
                    onPressed: () async {
                      if (titleController.text.isNotEmpty) {
                        final res = await _todoService.createTodo(
                          title: titleController.text,
                          notes: notesController.text,
                          difficulty: difficulty,
                          dueDate: selectedDate,
                          checklist: subTasks
                              .map((t) =>
                                  ChecklistItem(title: t, isCompleted: false))
                              .toList(),
                        );
                        if (res != null) {
                          _fetchTodos();
                          if (mounted) Navigator.pop(context);
                        }
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: _colPrimary,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20)),
                      elevation: 0,
                    ),
                    child: Text("SIMPAN RENCANA",
                        style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w900, fontSize: 16)),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildModalLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(text,
          style: GoogleFonts.plusJakartaSans(
              fontSize: 11,
              fontWeight: FontWeight.w900,
              color: Colors.grey.shade400,
              letterSpacing: 2.0)),
    );
  }

  Widget _buildModalField(
      TextEditingController controller, String hint, IconData icon,
      {int maxLines = 1}) {
    return TextField(
      controller: controller,
      maxLines: maxLines,
      style: GoogleFonts.plusJakartaSans(color: const Color(0xFF1F2937)),
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: GoogleFonts.plusJakartaSans(color: Colors.grey.shade300),
        prefixIcon: Icon(icon, color: _colPrimary.withOpacity(0.3), size: 20),
        filled: true,
        fillColor: Colors.grey.shade50,
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(20),
          borderSide: BorderSide(color: Colors.grey.shade100),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(20),
          borderSide: BorderSide(color: _colPrimary.withOpacity(0.1)),
        ),
      ),
    );
  }

  Widget _buildDiffOption(
      String value, String label, String current, Function(String) onTap) {
    bool sel = current == value;
    Color color = _getDifficultyColor(value);
    return GestureDetector(
      onTap: () => onTap(value),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: sel ? color.withOpacity(0.1) : Colors.grey.shade50,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: sel ? color : Colors.grey.shade100),
        ),
        child: Text(label,
            style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                fontWeight: FontWeight.w900,
                color: sel ? color : Colors.grey.shade400)),
      ),
    );
  }

  Widget _buildDatePicker(DateTime? selectedDate, Function(DateTime) onSelect) {
    return InkWell(
      onTap: () async {
        final date = await showDatePicker(
          context: context,
          initialDate: DateTime.now(),
          firstDate: DateTime.now(),
          lastDate: DateTime.now().add(const Duration(days: 365)),
          builder: (context, child) => Theme(
            data: ThemeData.light().copyWith(
              colorScheme: ColorScheme.light(
                  primary: _colPrimary,
                  onPrimary: Colors.white,
                  surface: Colors.white),
            ),
            child: child!,
          ),
        );
        if (date != null) onSelect(date);
      },
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.grey.shade50,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.grey.shade100),
        ),
        child: Row(
          children: [
            Icon(Icons.event_available_rounded,
                color: _colPrimary.withOpacity(0.3), size: 20),
            const SizedBox(width: 16),
            Text(
              selectedDate == null
                  ? "Pilih target tanggal..."
                  : DateFormat('EEEE, dd MMM yyyy').format(selectedDate),
              style: GoogleFonts.plusJakartaSans(
                  color: selectedDate == null
                      ? Colors.grey.shade400
                      : const Color(0xFF1F2937),
                  fontWeight: FontWeight.bold),
            ),
          ],
        ),
      ),
    );
  }

  void _showEditTodoModal(Todo todo) {
    // Basic edit modal placeholder or direct edit logic
    // For now, let's just show a toast or simplified edit
  }

  Future<void> _handleComplete(Todo todo) async {
    final res = await _todoService.completeTodo(todo.id);
    if (res != null) {
      if (mounted) {
        _showRewardDialog("Target Berhasil Dicapai!", res['xp_gained'] ?? 0);
        _fetchTodos();
      }
    }
  }

  void _showRewardDialog(String message, int xp) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(30),
            side: BorderSide(color: _colGold.withOpacity(0.5), width: 2)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.stars_rounded, color: Colors.amber, size: 64),
            const SizedBox(height: 20),
            Text(message,
                textAlign: TextAlign.center,
                style: GoogleFonts.playfairDisplay(
                    color: const Color(0xFF1F2937),
                    fontSize: 20,
                    fontWeight: FontWeight.bold)),
            const SizedBox(height: 12),
            Text("Pencapaian luar biasa untuk masa depan Anda.",
                textAlign: TextAlign.center,
                style: GoogleFonts.plusJakartaSans(
                    color: Colors.grey.shade500, fontSize: 13)),
            const SizedBox(height: 24),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [_buildRewardBadge("+$xp EXP", _colPrimary)],
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: _colPrimary,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(15)),
                ),
                child: const Text("ALHAMDULILLAH",
                    style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            )
          ],
        ),
      ),
    );
  }
}
