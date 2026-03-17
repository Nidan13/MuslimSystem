@extends('layouts.admin')

@section('title', 'Detail Hunter: ' . $user->username)

@section('content')
<div class="w-full space-y-10 animate-fadeIn pb-20">
    
    <!-- Header Navigation -->
    <div class="flex items-center gap-6 mb-6">
        <a href="{{ route('admin.hunters.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Registry / Hunter Details</h2>
        </div>
    </div>
    
    <!-- Header Stats (Identity) -->
    <div class="relative glass-panel rounded-[50px] overflow-hidden bg-white border-2 border-slate-50 shadow-2xl p-12">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-900/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            <div class="w-32 h-32 rounded-[40px] bg-teal-900 border-2 border-slate-100 flex items-center justify-center text-5xl font-serif font-black text-white shadow-2xl shadow-teal-900/10 transition-transform hover:scale-105 duration-500">
                {{ substr($user->username, 0, 1) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-2">
                    <h1 class="text-4xl font-serif font-black text-teal-950 tracking-tighter">{{ $user->username }}</h1>
                    @if($user->rankTier)
                    <span class="px-4 py-1.5 rounded-xl bg-teal-900/5 border border-teal-900/10 text-[9px] font-black uppercase tracking-widest shadow-sm w-fit mx-auto md:mx-0" style="color: {{ $user->rankTier->color_code ?? '#00F2FF' }}">
                        {{ $user->rankTier->name }}
                    </span>
                    @else
                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-black uppercase tracking-widest shadow-sm w-fit mx-auto md:mx-0 text-slate-400">
                        Otoritas: Open Rank
                    </span>
                    @endif
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] flex justify-center md:justify-start items-center gap-3">
                    <i class="fas fa-barcode text-cyan-500"></i>
                    SERIAL: <span class="text-teal-900 font-mono">SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                </p>
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest shadow-sm">{{ $user->email }}</span>
                    <span class="px-4 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-[9px] font-black text-emerald-600 uppercase tracking-widest shadow-sm">{{ $user->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}</span>
                    <span class="px-4 py-1.5 rounded-xl bg-amber-50 border border-amber-100 text-[9px] font-black text-amber-600 uppercase tracking-widest shadow-sm">Saldo: Rp{{ number_format($user->balance) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full md:w-auto">
                <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100/50 text-center shadow-inner group hover:border-cyan-400 transition-all">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pencapaian XP</p>
                    <p class="text-2xl font-black text-teal-900 font-mono tracking-tighter">{{ number_format($user->current_exp) }}</p>
                    <div class="mt-2 h-1 w-12 bg-cyan-400 mx-auto rounded-full opacity-0 group-hover:opacity-100 transition-all"></div>
                </div>
                <div class="p-6 bg-[#0E5F71] rounded-[32px] border-2 border-slate-800 text-center text-white shadow-2xl shadow-teal-900/30 group hover:border-[#00F2FF] transition-all">
                    <p class="text-[10px] font-black text-[#00F2FF] uppercase tracking-widest mb-2">Tier Otoritas</p>
                    <p class="text-2xl font-black font-serif italic tracking-tighter" style="{{ $user->rankTier ? 'color: ' . ($user->rankTier->color_code ?? '#00F2FF') : '' }}">
                        {{ $user->rankTier->slug ?? 'E' }}
                    </p>
                    <div class="mt-2 h-1 w-12 bg-white mx-auto rounded-full opacity-20 group-hover:opacity-100 transition-all"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tab -->
    <div class="flex flex-wrap items-center gap-3 p-2 bg-slate-100/50 backdrop-blur-md rounded-[30px] border-2 border-slate-50 w-fit mx-auto md:mx-0 shadow-sm">
        <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn active px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all">Profil Statistik</button>
        <button onclick="switchTab('protocols')" id="tab-protocols" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Task Log</button>
        <button onclick="switchTab('patterns')" id="tab-patterns" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Habit Node</button>
        <button onclick="switchTab('activity')" id="tab-activity" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Interest & Activity</button>
        <button onclick="switchTab('objectives')" id="tab-objectives" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Objective</button>
    </div>

    <!-- Tab Content Container -->
    <div id="content-profile" class="tab-content space-y-10 animate-slideUp">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @php
                $statGrid = [
                    ['val' => $stats['totalSurah'], 'label' => 'Total Surah', 'icon' => 'fa-quran', 'color' => 'cyan'],
                    ['val' => $stats['totalSholat'], 'label' => 'Sholat', 'icon' => 'fa-pray', 'color' => 'emerald'],
                    ['val' => $stats['totalMisi'], 'label' => 'Misi Selesai', 'icon' => 'fa-scroll', 'color' => 'amber'],
                    ['val' => $stats['totalKajian'], 'label' => 'Kajian', 'icon' => 'fa-video', 'color' => 'indigo'],
                    ['val' => $stats['totalHabit'], 'label' => 'Habit', 'icon' => 'fa-fingerprint', 'color' => 'rose'],
                    ['val' => $stats['totalDailyTask'], 'label' => 'Daily Task', 'icon' => 'fa-calendar-check', 'color' => 'teal', 'dark' => true]
                ];
            @endphp

            @foreach($statGrid as $s)
            <div class="{{ isset($s['dark']) ? 'bg-teal-900 text-white shadow-lg shadow-teal-900/30' : 'bg-white text-slate-900' }} glass-panel p-6 rounded-[35px] border border-slate-100 flex flex-col items-center justify-center text-center group hover:border-{{ $s['color'] }}-400 transition-all hover:-translate-y-1 shadow-sm">
                <div class="w-12 h-12 rounded-2xl {{ isset($s['dark']) ? 'bg-white/10' : 'bg-'.$s['color'].'-50' }} text-{{ isset($s['dark']) ? 'cyan-400' : $s['color'].'-500' }} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas {{ $s['icon'] }} text-xl"></i>
                </div>
                <p class="text-[9px] font-black {{ isset($s['dark']) ? 'text-white/40' : 'text-slate-400' }} uppercase tracking-widest mb-1">{{ $s['label'] }}</p>
                <p class="text-2xl font-serif font-black">{{ number_format($s['val']) }}</p>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Ability Matrix -->
            <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl overflow-hidden relative">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-6 border-b border-slate-50 pb-6 flex items-center gap-3 relative z-10 font-serif">
                    <i class="fas fa-hexagon-nodes text-cyan-500"></i> Ability Matrix
                </h3>
                
                <div class="relative h-[300px] w-full flex items-center justify-center">
                    <canvas id="radarChart"></canvas>
                </div>

                <div class="grid grid-cols-5 gap-2 mt-8">
                    @foreach($radarData['labels'] as $index => $label)
                    <div class="text-center">
                        <p class="text-[8px] font-black text-slate-300 uppercase mb-1">{{ $label }}</p>
                        <p class="text-xs font-bold text-teal-900">{{ round($radarData['values'][$index]) }}%</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Progression -->
            <div class="glass-panel p-12 rounded-[50px] bg-teal-900 border-2 border-slate-800 text-white relative overflow-hidden shadow-2xl shadow-teal-950/50 flex flex-col justify-between">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-cyan-400/10 rounded-full blur-[100px] pointer-events-none"></div>
                <div>
                    <h3 class="text-[10px] font-black text-white/20 uppercase tracking-[0.5em] mb-10 border-b border-white/5 pb-6 flex items-center gap-3 font-serif">
                        <i class="fas fa-chart-line text-cyan-400"></i> Evolusi Hunter
                    </h3>
                    <div class="grid gap-10 relative z-10">
                        <div class="flex items-center gap-8 group">
                            <div class="w-20 h-20 bg-white/5 border border-white/10 rounded-[30px] flex items-center justify-center text-cyan-400 shadow-inner group-hover:scale-110 transition-all duration-500">
                                <i class="fas fa-layer-group text-3xl"></i>
                            </div>
                            <div>
                                <p class="text-5xl font-black font-serif leading-none tracking-tighter">LVL {{ $user->level }}</p>
                                <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] mt-3 italic">Manifestasi Hub Saat Ini</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex justify-between text-[11px] font-black uppercase tracking-widest">
                                <span class="text-white/40 italic">EXP Progression</span>
                                <span class="text-cyan-400 font-mono tracking-tighter">{{ number_format($user->current_exp) }} <span class="text-white/20 mx-1">/</span> {{ number_format($user->next_level_exp ?: 1000) }}</span>
                            </div>
                            <div class="h-4 bg-white/5 rounded-full border border-white/5 p-1 shadow-inner overflow-hidden relative">
                                @php $expPercent = min(($user->current_exp / ($user->next_level_exp ?: 1000)) * 100, 100); @endphp
                                <div class="h-full bg-gradient-to-r from-teal-800 via-cyan-500 to-teal-800 rounded-full shadow-[0_0_15px_rgba(34,211,238,0.3)] transition-all duration-1000" style="width:{{ $expPercent }}%"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent w-20 h-full animate-progress-shine"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-10 relative z-10">
                    <div class="flex-1 p-6 bg-white/5 border border-white/10 rounded-3xl text-center backdrop-blur-sm group hover:bg-white/10 transition-all">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-2 italic">Misi Cleared</p>
                        <p class="text-3xl font-black font-mono text-white">{{ array_sum($questStats) }}</p>
                    </div>
                    <div class="flex-1 p-6 bg-cyan-400/20 border border-cyan-400/30 rounded-3xl text-center backdrop-blur-sm group hover:bg-cyan-400/30 transition-all">
                        <p class="text-[9px] font-black text-cyan-400 uppercase tracking-widest mb-2 italic">Rank Otoritas</p>
                        <p class="text-2xl font-serif italic text-white leading-none mt-1">{{ $user->rankTier->name ?? 'Open Rank' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tasks Tab -->
    <div id="content-protocols" class="tab-content hidden animate-slideUp">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3 font-serif">
                <i class="fas fa-calendar-check text-teal-900"></i> Daily Task Log
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($userDailyTasks as $udt)
                <div class="p-8 bg-slate-50 border border-slate-100 rounded-[35px] flex items-center justify-between group hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 text-teal-900 flex items-center justify-center text-2xl shadow-sm transition-transform group-hover:scale-110">
                            {!! $udt->dailyTask->icon ?? '💠' !!}
                        </div>
                        <div>
                            <p class="text-xl font-serif font-black text-teal-950 tracking-tight">{{ $udt->dailyTask->name }}</p>
                            <p class="text-[9px] font-black text-cyan-600 uppercase tracking-widest flex items-center gap-2 mt-1 italic">
                                Selesai: {{ $udt->completed_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center italic">
                    <div class="opacity-20 flex flex-col items-center">
                        <i class="fas fa-ghost text-5xl mb-6 text-teal-900"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">Belum ada aktivitas hari ini</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Habits Tab -->
    <div id="content-patterns" class="tab-content hidden animate-slideUp">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3 font-serif">
                <i class="fas fa-fingerprint text-teal-900"></i> Habit Nodes
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($habits as $habit)
                <div class="p-8 bg-slate-50 border border-slate-100 rounded-[40px] group hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-16 h-16 rounded-[25px] bg-white border border-slate-100 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-transform">
                             {!! $habit->icon ?? '🌀' !!}
                        </div>
                        <div class="px-5 py-2 rounded-xl bg-teal-900 text-[9px] font-black text-white uppercase tracking-widest shadow-lg italic">
                            {{ $habit->frequency }}
                        </div>
                    </div>
                    
                    <h4 class="text-2xl font-serif font-black text-teal-950 uppercase tracking-tight mb-2">{{ $habit->title }}</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium mb-8 italic opacity-70 h-10 line-clamp-2">{{ $habit->notes ?: 'Hanya deskripsi manifestasi standar.' }}</p>
                    
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Akumulasi Node</span>
                        <span class="text-3xl font-serif font-black text-teal-900">{{ $habit->count }}</span>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center italic">
                    <div class="opacity-20 flex flex-col items-center">
                        <i class="fas fa-dna text-5xl mb-6 text-teal-900"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">Tidak ada node habit terdeteksi</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Interest & Activity Tab -->
    <div id="content-activity" class="tab-content hidden animate-slideUp">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Top Interests -->
            <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl overflow-hidden relative lg:col-span-1">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3 font-serif">
                    <i class="fas fa-bullseye text-teal-900"></i> Top Interests
                </h3>
                
                <div class="space-y-8">
                    @forelse($topPages as $page)
                    @php
                        $percentage = ($stats['totalSeconds'] > 0) ? ($page->total_seconds / $stats['totalSeconds']) * 100 : 0;
                        $color = ['teal', 'cyan', 'indigo', 'emerald', 'amber'][$loop->index % 5];
                    @endphp
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-black text-slate-600 uppercase tracking-widest">{{ $page->page_name }}</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ round($page->total_seconds / 60, 1) }} min</span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $color }}-500 rounded-full transition-all duration-1000" style="width:{{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-[10px] font-black text-slate-300 uppercase text-center py-10">Data Minat Terbatas</p>
                    @endforelse
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4">
                    <div class="p-6 bg-slate-50 rounded-[30px] border border-slate-100 text-center">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-2">Rata-rata / Hari</p>
                        <p class="text-3xl font-serif font-black text-teal-900 italic tracking-tighter">{{ round($stats['avgDailySeconds'] / 60, 1) }}m</p>
                    </div>
                    <div class="p-6 bg-teal-900 rounded-[30px] border border-slate-100 text-center text-white shadow-lg shadow-teal-900/20">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-2">Total Jam Aktif</p>
                        <p class="text-3xl font-serif font-black italic tracking-tighter">{{ round($stats['totalSeconds'] / 3600, 1) }}h</p>
                    </div>
                </div>

                <!-- Last 7 Days Activity Trend -->
                <div class="mt-8 border-t border-slate-50 pt-8">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-4 italic">7-Day Consistency</p>
                    <div class="flex items-end justify-between gap-1 h-12">
                        @foreach(range(6, 0) as $i)
                            @php
                                $date = now()->subDays($i)->toDateString();
                                $dayStat = $dailyActivityTrend->firstWhere('active_date', $date);
                                $seconds = $dayStat ? $dayStat->total_seconds : 0;
                                $maxPossible = 3600; // 1hr max for visualization
                                $height = min(100, ($seconds / $maxPossible) * 100);
                            @endphp
                            <div class="flex-1 bg-slate-100 rounded-t-lg relative group h-full">
                                <div class="absolute bottom-0 left-0 right-0 bg-cyan-500 rounded-t-lg transition-all duration-500 group-hover:bg-teal-900" style="height: {{ $height }}%"></div>
                                <!-- Tooltip -->
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[8px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                                    {{ round($seconds / 60) }} min ({{ date('D', strtotime($date)) }})
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl overflow-hidden relative lg:col-span-2">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3 font-serif">
                    <i class="fas fa-shoe-prints text-teal-900"></i> Activity Footprints
                </h3>

                <div class="overflow-hidden bg-slate-50 rounded-[35px] border border-slate-100">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Halaman / Fitur</th>
                                <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Durasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($userActivities as $act)
                            <tr class="group hover:bg-white transition-all">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <p class="text-[10px] font-bold text-slate-400">{{ \Carbon\Carbon::parse($act->updated_at)->format('d M Y') }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="w-1 h-1 rounded-full bg-cyan-400 animate-pulse"></span>
                                            <p class="text-[9px] font-black text-cyan-600 font-mono italic">LAST: {{ \Carbon\Carbon::parse($act->updated_at)->format('H:i') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-xs font-black text-teal-900 group-hover:text-cyan-500 transition-colors uppercase tracking-widest">
                                    {{ $act->page_name }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black font-mono text-slate-500">
                                        @if($act->seconds_spent < 60)
                                            {{ $act->seconds_spent }}s
                                        @else
                                            {{ round($act->seconds_spent / 60, 1) }}m
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-20 text-center italic text-slate-300">
                                    <i class="fas fa-radar text-3xl mb-4 block"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em]">Belum ada data jejak digital</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radarCtx = document.getElementById('radarChart').getContext('2d');
        const radarData = @json($radarData);

        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: radarData.labels,
                datasets: [{
                    label: 'Mastery',
                    data: radarData.values,
                    backgroundColor: 'rgba(34, 211, 238, 0.2)',
                    borderColor: '#22d3ee',
                    borderWidth: 3,
                    pointBackgroundColor: '#22d3ee',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: 'rgba(0,0,0,0.05)' },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        pointLabels: {
                            font: { family: 'Cinzel', size: 10, weight: '900' },
                            color: '#0d2d35'
                        },
                        ticks: { display: false },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });

    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById('content-' + tabId).classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-slate-400');
        });
        document.getElementById('tab-' + tabId).classList.add('active');
        document.getElementById('tab-' + tabId).classList.remove('text-slate-400');
    }
</script>
<style>
    .tab-btn.active {
        background: #0d2d35;
        color: #22d3ee;
        box-shadow: 0 10px 20px -5px rgba(13, 45, 53, 0.3);
    }
    .animate-progress-shine {
        animation: progress-shine 3s infinite linear;
    }
    @keyframes progress-shine {
        to { transform: translateX(500%); }
    }
</style>
@endpush
@endsection
