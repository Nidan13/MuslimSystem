@extends('layouts.admin')

@section('title', 'Hunter Registry: ' . $user->username)

@section('content')
<div class="max-w-6xl mx-auto space-y-10 animate-fadeIn pb-20">
    
    <!-- Header Stats (Identity) -->
    <div class="relative glass-panel rounded-[50px] overflow-hidden bg-white border-2 border-slate-50 shadow-2xl p-12">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-nu-teal/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            <div class="w-32 h-32 rounded-[40px] bg-nu-indigo border-2 border-slate-100 flex items-center justify-center text-5xl font-serif font-black text-white shadow-2xl shadow-slate-900/10 transition-transform hover:scale-105 duration-500">
                {{ substr($user->username, 0, 1) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-2">
                    <h1 class="text-4xl font-serif font-black text-slate-900 tracking-tighter">{{ $user->username }}</h1>
                    <span class="px-4 py-1.5 rounded-xl bg-nu-indigo/5 border border-nu-indigo/10 text-[9px] font-black text-nu-indigo uppercase tracking-widest shadow-sm w-fit mx-auto md:mx-0">
                        {{ $user->rankTier->name ?? 'Novice Hunter' }}
                    </span>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] flex justify-center md:justify-start items-center gap-3">
                    <i class="fas fa-barcode text-nu-teal icon-glow"></i>
                    SERIAL: <span class="text-nu-teal font-mono">SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                </p>
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest shadow-sm">{{ $user->email }}</span>
                    <span class="px-4 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-[9px] font-black text-emerald-600 uppercase tracking-widest shadow-sm">Status: Aktif</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full md:w-auto">
                <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100/50 text-center shadow-inner group hover:border-nu-teal transition-all">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Soul Points</p>
                    <p class="text-2xl font-black text-nu-indigo font-mono tracking-tighter">SP {{ number_format($user->soul_points) }}</p>
                    <div class="mt-2 h-1 w-12 bg-nu-teal mx-auto rounded-full opacity-0 group-hover:opacity-100 transition-all"></div>
                </div>
                <div class="p-6 bg-slate-900 rounded-[32px] border-2 border-slate-800 text-center text-white shadow-2xl shadow-slate-900/30 group hover:border-nu-teal transition-all">
                    <p class="text-[10px] font-black text-nu-teal uppercase tracking-widest mb-2">Authority Level</p>
                    <p class="text-2xl font-black font-serif italic tracking-tighter" style="{{ $user->rankTier->color_code ? 'color: ' . $user->rankTier->color_code : '' }}">
                        {{ $user->rankTier->slug ?? 'E' }}
                    </p>
                    <div class="mt-2 h-1 w-12 bg-white mx-auto rounded-full opacity-20 group-hover:opacity-100 transition-all"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tab -->
    <div class="flex flex-wrap items-center gap-3 p-2 bg-slate-100/50 backdrop-blur-md rounded-[30px] border-2 border-slate-50 w-fit mx-auto md:mx-0 shadow-sm">
        <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn active px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all">Profil Matrix</button>
        <button onclick="switchTab('protocols')" id="tab-protocols" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-nu-indigo">Protocols</button>
        <button onclick="switchTab('patterns')" id="tab-patterns" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-nu-indigo">Patterns</button>
        <button onclick="switchTab('objectives')" id="tab-objectives" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-nu-indigo">Objectives</button>
    </div>

    <!-- Profile Tab Content -->
    <!-- Profile Tab Content -->
    <div id="content-profile" class="tab-content space-y-10 animate-slideUp">
        
        <!-- Quick Stats Matrix (Updated) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <div class="glass-panel p-6 rounded-[35px] bg-white border border-slate-100 flex flex-col items-center justify-center text-center group hover:border-cyan-400 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-quran text-xl"></i>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Surah</p>
                <p class="text-2xl font-serif font-black text-slate-900">{{ number_format($stats['totalSurah']) }}</p>
            </div>
            
            <div class="glass-panel p-6 rounded-[35px] bg-white border border-slate-100 flex flex-col items-center justify-center text-center group hover:border-emerald-400 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-pray text-xl"></i>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sholat</p>
                <p class="text-2xl font-serif font-black text-slate-900">{{ number_format($stats['totalSholat']) }}</p>
            </div>

            <div class="glass-panel p-6 rounded-[35px] bg-white border border-slate-100 flex flex-col items-center justify-center text-center group hover:border-amber-400 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-scroll text-xl"></i>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Ambil Misi</p>
                <p class="text-2xl font-serif font-black text-slate-900">{{ number_format($stats['totalMisi']) }}</p>
            </div>

            <div class="glass-panel p-6 rounded-[35px] bg-white border border-slate-100 flex flex-col items-center justify-center text-center group hover:border-indigo-400 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-video text-xl"></i>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Kajian</p>
                <p class="text-2xl font-serif font-black text-slate-900">{{ number_format($stats['totalKajian']) }}</p>
            </div>

            <div class="glass-panel p-6 rounded-[35px] bg-white border border-slate-100 flex flex-col items-center justify-center text-center group hover:border-rose-400 transition-all hover:-translate-y-1 shadow-sm hover:shadow-xl">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-fingerprint text-xl"></i>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Habit Matrix</p>
                <p class="text-2xl font-serif font-black text-slate-900">{{ number_format($stats['totalHabit']) }}</p>
            </div>

            <div class="glass-panel p-6 rounded-[35px] bg-slate-900 border border-slate-800 flex flex-col items-center justify-center text-center group hover:bg-nu-teal transition-all hover:-translate-y-1 shadow-lg hover:shadow-nu-teal/30 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-0.5 rounded-lg bg-white/10 text-[7px] font-black text-white/50 uppercase tracking-widest">Today: {{ number_format($stats['dailyTaskToday']) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 text-nu-teal flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Daily Task</p>
                <p class="text-2xl font-serif font-black text-white">{{ number_format($stats['totalDailyTask']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Pentagon Stats Board (Radar Chart) -->
            <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl overflow-hidden relative">
                <div class="absolute top-0 left-0 w-32 h-32 bg-nu-teal/5 rounded-full -ml-16 -mt-16 pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-slate-900/30 uppercase tracking-[0.5em] mb-6 border-b border-slate-50 pb-6 flex items-center gap-3 relative z-10">
                    <i class="fas fa-hexagon-nodes text-nu-teal"></i> Ability Matrix
                </h3>
                
                <div class="relative h-[300px] w-full flex items-center justify-center">
                    <canvas id="radarChart"></canvas>
                </div>

                <div class="grid grid-cols-5 gap-2 mt-8">
                    @foreach($radarData['labels'] as $index => $label)
                    <div class="text-center">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-1">{{ $label }}</p>
                        <p class="text-xs font-bold text-nu-indigo">{{ round($radarData['values'][$index]) }}%</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Progression & Level -->
            <div class="glass-panel p-12 rounded-[50px] bg-slate-900 border-2 border-slate-800 text-white relative overflow-hidden shadow-2xl shadow-slate-950/50 flex flex-col justify-between">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-nu-teal/10 rounded-full blur-[100px] pointer-events-none"></div>
                <div>
                    <h3 class="text-[10px] font-black text-nu-teal/40 uppercase tracking-[0.5em] mb-10 border-b border-white/5 pb-6 flex items-center gap-3">
                        <i class="fas fa-chart-line text-nu-teal"></i> Evolution Progress
                    </h3>
                    <div class="grid gap-10 relative z-10">
                        <div class="flex items-center gap-8 group">
                            <div class="w-20 h-20 bg-white/5 border border-white/10 rounded-[30px] flex items-center justify-center text-nu-teal shadow-inner group-hover:scale-110 transition-all duration-500">
                                <i class="fas fa-layer-group text-3xl"></i>
                            </div>
                            <div>
                                <p class="text-5xl font-black font-serif leading-none tracking-tighter">LVL {{ $user->level }}</p>
                                <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em] mt-3">Current Manifestation</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex justify-between text-[11px] font-black uppercase tracking-widest">
                                <span class="text-white/40">XP Progression</span>
                                <span class="text-nu-teal font-mono tracking-tighter">{{ number_format($user->current_exp) }} <span class="text-white/20 mx-1">/</span> {{ number_format($user->next_level_exp ?: 1000) }}</span>
                            </div>
                            <div class="h-4 bg-white/5 rounded-full border border-white/5 p-1 shadow-inner overflow-hidden relative">
                                @php $expPercent = min(($user->current_exp / ($user->next_level_exp ?: 1000)) * 100, 100); @endphp
                                <div class="h-full bg-gradient-to-r from-nu-indigo via-nu-teal to-cyan-400 rounded-full shadow-[0_0_15px_rgba(34,211,238,0.3)] transition-all duration-1000" style="width:{{ $expPercent }}%"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent w-20 h-full animate-progress-shine"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-10 relative z-10">
                    <div class="flex-1 p-6 bg-white/5 border border-white/10 rounded-3xl text-center backdrop-blur-sm group hover:bg-white/10 transition-all">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-2">Quest Cleared</p>
                        <p class="text-3xl font-black font-mono text-white">{{ array_sum($questStats) }}</p>
                    </div>
                    <div class="flex-1 p-6 bg-nu-teal/20 border border-nu-teal/30 rounded-3xl text-center backdrop-blur-sm group hover:bg-nu-teal/30 transition-all">
                        <p class="text-[9px] font-black text-nu-teal uppercase tracking-widest mb-2">Class Rank</p>
                        <p class="text-2xl font-serif italic text-white leading-none mt-1">{{ $user->rankTier->name ?? 'None' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Protocols Tab Content -->
    <div id="content-protocols" class="tab-content hidden animate-slideUp">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <h3 class="text-[10px] font-black text-slate-900/30 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3">
                <i class="fas fa-microchip text-nu-teal"></i> Active Daily Protocols
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($userDailyTasks as $udt)
                <div class="p-8 bg-slate-50 border border-slate-100 rounded-[35px] flex items-center justify-between group hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 text-nu-teal flex items-center justify-center text-2xl shadow-sm transition-transform group-hover:scale-110">
                            {!! $udt->dailyTask->icon ?? '💠' !!}
                        </div>
                        <div>
                            <p class="text-xl font-serif font-black text-slate-800 tracking-tight">{{ $udt->dailyTask->name }}</p>
                            <p class="text-[9px] font-black text-nu-indigo uppercase tracking-widest flex items-center gap-2 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-nu-teal animate-pulse"></span>
                                CLR {{ $udt->completed_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center">
                    <div class="opacity-20 flex flex-col items-center">
                        <i class="fas fa-ghost text-5xl mb-6 text-nu-indigo"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">Zero protocols logged today</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Patterns Tab Content -->
    <div id="content-patterns" class="tab-content hidden animate-slideUp">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <h3 class="text-[10px] font-black text-slate-900/30 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3">
                <i class="fas fa-brain text-nu-teal"></i> behavioral patterns
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($habits as $habit)
                <div class="p-8 bg-slate-50 border border-slate-100 rounded-[40px] group hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-16 h-16 rounded-[25px] bg-white border border-slate-100 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-transform">
                             {!! $habit->icon ?? '🌀' !!}
                        </div>
                        <div class="px-5 py-2 rounded-xl bg-nu-indigo text-[9px] font-black text-white uppercase tracking-widest shadow-lg">
                            {{ $habit->frequency }}
                        </div>
                    </div>
                    
                    <h4 class="text-2xl font-serif font-black text-slate-900 uppercase tracking-tight mb-2">{{ $habit->title }}</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium mb-8 italic opacity-70 h-10 line-clamp-2">{{ $habit->notes ?: 'No description logs.' }}</p>
                    
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Accumulation</span>
                        <span class="text-3xl font-serif font-black text-nu-indigo">{{ $habit->count }}<span class="text-xs ml-1 text-nu-teal font-sans italic opacity-40 capitalize">node</span></span>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center">
                    <div class="opacity-20 flex flex-col items-center">
                        <i class="fas fa-fingerprint text-5xl mb-6 text-nu-indigo"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">No pattern nodes discovered</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Objectives Tab Content -->
    <div id="content-objectives" class="tab-content hidden animate-slideUp">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <h3 class="text-[10px] font-black text-slate-900/30 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3">
                <i class="fas fa-bullseye text-nu-teal"></i> Objective Matrix
            </h3>
            <div class="space-y-6">
                @forelse($todos as $todo)
                <div class="p-8 bg-slate-50 border 2 border-slate-100 rounded-[35px] flex flex-col md:flex-row md:items-center justify-between gap-8 hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-center gap-8">
                        <div class="w-14 h-14 rounded-2xl border-4 {{ $todo->is_completed ? 'bg-nu-teal border-white shadow-lg' : 'bg-white border-slate-100' }} flex items-center justify-center transition-all">
                            @if($todo->is_completed)
                                <i class="fas fa-check text-white text-lg"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-3xl font-serif font-black text-slate-800 tracking-tighter {{ $todo->is_completed ? 'line-through opacity-30 italic' : '' }} mb-1">{{ $todo->title }}</h4>
                            @if($todo->due_date)
                                <div class="flex items-center gap-3 text-red-500">
                                    <i class="far fa-clock text-xs"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Expire: {{ $todo->due_date->format('Y.m.d') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-8 py-3 bg-white border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm {{ $todo->is_completed ? 'text-nu-teal' : 'text-slate-400' }}">
                        {{ $todo->is_completed ? 'Task Finalized' : 'Operational Status' }}
                    </div>
                </div>
                @empty
                <div class="py-24 text-center">
                    <div class="opacity-20 flex flex-col items-center">
                        <i class="fas fa-check-double text-5xl mb-6 text-nu-indigo"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">Objective manifest is clear</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="flex justify-center pt-10">
        <a href="{{ route('admin.hunters.edit', $user) }}" class="inline-flex items-center gap-4 px-12 py-5 bg-nu-indigo text-white rounded-[25px] font-serif font-black uppercase tracking-widest text-xs hover:bg-nu-teal transition-all shadow-2xl shadow-nu-indigo/30 active:scale-95 group">
            <i class="fas fa-pen-nib transition-transform group-hover:-rotate-12"></i>
            Buka Registry Control
        </a>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        --nu-teal-rgb: 34, 211, 238;
        --nu-indigo-rgb: 13, 45, 53;
    }
    .glass-panel {
        backdrop-filter: blur(20px);
    }
    
    .tab-btn.active {
        background: #0d2d35;
        color: #22d3ee;
        box-shadow: 0 15px 35px -5px rgba(13, 45, 53, 0.4);
        transform: translateY(-2px);
        border: 2px solid rgba(34, 211, 238, 0.2);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slideUp {
        animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes progress-shine {
        0% { transform: translateX(-100%) skewX(-15deg); }
        100% { transform: translateX(500%) skewX(-15deg); }
    }
    .animate-progress-shine {
        animation: progress-shine 3s infinite ease-in-out;
    }

    .icon-glow {
        filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.4));
    }
    
    .italic { font-style: italic; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- RADAR CHART (PENTAGON) ---
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
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#22d3ee',
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

        const activeContent = document.getElementById('content-' + tabId);
        activeContent.classList.remove('hidden');
        
        // Re-trigger animation
        activeContent.style.animation = 'none';
        activeContent.offsetHeight; /* trigger reflow */
        activeContent.style.animation = null;

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-slate-400');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-slate-400');
    }
</script>
@endpush
@endsection
