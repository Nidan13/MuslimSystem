import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/premium_color.dart';
import '../utils/template_data.dart';
import '../services/daily_task_service.dart';
import '../services/habit_service.dart';
import '../services/todo_service.dart';
import 'widgets/custom_background.dart';

import '../services/template_service.dart';

class TemplateSelectionScreen extends StatefulWidget {
  final String templateType; // 'task', 'habit', or 'todo'
  const TemplateSelectionScreen({super.key, required this.templateType});

  @override
  State<TemplateSelectionScreen> createState() =>
      _TemplateSelectionScreenState();
}

class _TemplateSelectionScreenState extends State<TemplateSelectionScreen> {
  final DailyTaskService _dailyService = DailyTaskService();
  final HabitService _habitService = HabitService();
  final TodoService _todoService = TodoService();
  final TemplateService _templateService = TemplateService();

  List<TaskTemplate> _availableTemplates = [];
  final Set<int> _selectedIndices = {};
  bool _isProcessing = false;
  bool _isLoadingTemplates = true;

  @override
  void initState() {
    super.initState();
    _fetchTemplates();
  }

  Future<void> _fetchTemplates() async {
    setState(() => _isLoadingTemplates = true);
    final templates = await _templateService.getTemplates(widget.templateType);
    if (mounted) {
      setState(() {
        _availableTemplates = templates;
        _isLoadingTemplates = false;
      });
    }
  }

  Future<void> _applyTemplates() async {
    if (_selectedIndices.isEmpty) return;

    setState(() => _isProcessing = true);
    int successCount = 0;

    try {
      for (int index in _selectedIndices) {
        final template = _availableTemplates[index];
        bool success = false;

        if (widget.templateType == 'task') {
          await _dailyService.createCustomTask(
            name: template.name,
            soulPoints: template.soulPoints,
            description: template.description,
            icon: template.icon,
          );
          success = true;
        } else if (widget.templateType == 'habit') {
          final h = await _habitService.createHabit(
            title: template.name,
            notes: template.description,
            difficulty: 'medium',
            isPositive: true,
            isNegative: false,
            frequency: 'daily',
          );
          if (h != null) success = true;
        } else if (widget.templateType == 'todo') {
          final t = await _todoService.createTodo(
            title: template.name,
            notes: template.description,
            difficulty: 'medium',
          );
          if (t != null) success = true;
        }

        if (success) successCount++;
      }

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("$successCount template berhasil diterapkan wok!"),
            backgroundColor: PremiumColor.primary,
          ),
        );
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Gagal menerapkan template: $e")),
        );
      }
    } finally {
      if (mounted) setState(() => _isProcessing = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    String title = widget.templateType == 'task'
        ? "Template Jurnal"
        : widget.templateType == 'habit'
            ? "Template Habit"
            : "Template To-Do";

    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          const Positioned.fill(child: IslamicPatternBackground()),
          CustomScrollView(
            slivers: [
              _buildSliverAppBar(title),
              if (_isLoadingTemplates)
                const SliverFillRemaining(
                  child: Center(
                      child: CircularProgressIndicator(
                          color: PremiumColor.primary)),
                )
              else if (_availableTemplates.isEmpty)
                SliverFillRemaining(
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.layers_clear_rounded,
                            size: 64, color: Colors.grey.shade300),
                        const SizedBox(height: 16),
                        Text("Belum ada template wok!",
                            style: GoogleFonts.plusJakartaSans(
                                color: Colors.grey)),
                      ],
                    ),
                  ),
                )
              else
                SliverPadding(
                  padding: const EdgeInsets.all(24),
                  sliver: SliverList(
                    delegate: SliverChildBuilderDelegate(
                      (context, index) {
                        final template = _availableTemplates[index];
                        final isSelected = _selectedIndices.contains(index);
                        return _buildTemplateItem(template, index, isSelected);
                      },
                      childCount: _availableTemplates.length,
                    ),
                  ),
                ),
              const SliverToBoxAdapter(child: SizedBox(height: 100)),
            ],
          ),
          if (_isProcessing)
            Container(
              color: Colors.black26,
              child: const Center(
                  child:
                      CircularProgressIndicator(color: PremiumColor.primary)),
            ),
        ],
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: _selectedIndices.isEmpty
          ? null
          : Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: _isProcessing ? null : _applyTemplates,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: PremiumColor.primary,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16)),
                    elevation: 8,
                  ),
                  child: Text("GUNAKAN ${_selectedIndices.length} TEMPLATE",
                      style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.bold, color: Colors.white)),
                ),
              ),
            ),
    );
  }

  Widget _buildSliverAppBar(String title) {
    return SliverAppBar(
      expandedHeight: 180,
      pinned: true,
      backgroundColor: PremiumColor.primary,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
        onPressed: () => Navigator.pop(context),
      ),
      flexibleSpace: FlexibleSpaceBar(
        background: ClipPath(
          clipper: MuqarnasClipper(),
          child: Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  PremiumColor.primary,
                  PremiumColor.primary.withOpacity(0.8)
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            padding: const EdgeInsets.fromLTRB(24, 80, 24, 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(title,
                    style: GoogleFonts.playfairDisplay(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        color: Colors.white)),
                Text("Pilih template biar lu kaga ribet ngetik wok!",
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 14, color: Colors.white70)),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildTemplateItem(TaskTemplate template, int index, bool isSelected) {
    return GestureDetector(
      onTap: () {
        setState(() {
          if (isSelected) {
            _selectedIndices.remove(index);
          } else {
            _selectedIndices.add(index);
          }
        });
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeOutCubic,
        margin: const EdgeInsets.only(bottom: 16),
        decoration: BoxDecoration(
          color: isSelected
              ? PremiumColor.primary.withOpacity(0.08)
              : Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(
            color: isSelected
                ? PremiumColor.primary
                : Colors.grey.withOpacity(0.15),
            width: isSelected ? 2 : 1,
          ),
          boxShadow: [
            BoxShadow(
              color: isSelected
                  ? PremiumColor.primary.withOpacity(0.2)
                  : Colors.black.withOpacity(0.04),
              blurRadius: isSelected ? 20 : 12,
              spreadRadius: isSelected ? 2 : 0,
              offset: Offset(0, isSelected ? 8 : 4),
            )
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(24),
          child: Stack(
            children: [
              if (isSelected)
                Positioned(
                  right: -20,
                  top: -20,
                  child: Icon(
                    Icons.check_circle_rounded,
                    size: 100,
                    color: PremiumColor.primary.withOpacity(0.05),
                  ),
                ),
              Padding(
                padding: const EdgeInsets.all(20),
                child: Row(
                  children: [
                    Container(
                      width: 56,
                      height: 56,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: isSelected
                              ? [PremiumColor.primary, PremiumColor.accent]
                              : [Colors.grey.shade100, Colors.grey.shade200],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: isSelected
                            ? [
                                BoxShadow(
                                  color: PremiumColor.primary.withOpacity(0.3),
                                  blurRadius: 8,
                                  offset: const Offset(0, 4),
                                )
                              ]
                            : [],
                      ),
                      child: Center(
                        child: Text(
                          template.icon,
                          style: const TextStyle(fontSize: 26),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Flexible(
                                child: Text(
                                  template.name,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w900,
                                    color: PremiumColor.slate800,
                                    letterSpacing: -0.3,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 8, vertical: 4),
                                decoration: BoxDecoration(
                                  color: isSelected
                                      ? PremiumColor.primary.withOpacity(0.1)
                                      : Colors.grey.shade100,
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  template.category.toUpperCase(),
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 9,
                                    fontWeight: FontWeight.w800,
                                    color: isSelected
                                        ? PremiumColor.primary
                                        : PremiumColor.slate500,
                                    letterSpacing: 0.5,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            template.description,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 13,
                              color: PremiumColor.slate500,
                              height: 1.4,
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 12),
                    AnimatedContainer(
                      duration: const Duration(milliseconds: 200),
                      width: 28,
                      height: 28,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: isSelected
                            ? PremiumColor.primary
                            : Colors.transparent,
                        border: Border.all(
                          color: isSelected
                              ? PremiumColor.primary
                              : Colors.grey.shade300,
                          width: 2,
                        ),
                      ),
                      child: isSelected
                          ? const Icon(Icons.check,
                              color: Colors.white, size: 16)
                          : null,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
