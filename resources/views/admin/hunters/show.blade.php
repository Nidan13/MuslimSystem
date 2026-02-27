@extends('layouts.admin')

@section('title', 'Hunter Identity: ' . $user->username)

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    
    <!-- Identity Header (The ID Card Feel) -->
    <div class="relative glass-panel rounded-[50px] overflow-hidden group shadow-2xl">
        <div class="absolute top-0 left-0 w-full h-48 bg-teal-900 border-b-4 border-cyan-400/30">
             <!-- Technical pattern overlay -->
             <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, #fff 1px, transparent 0); background-size: 24px 24px;"></div>
        </div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-400/20 blur-[120px] pointer-events-none group-hover:bg-cyan-400/30 transition-colors duration-1000"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-end gap-10 mt-24 px-12 pb-12">
            <div class="relative group">
                <div class="w-40 h-40 rounded-[40px] bg-white p-2 shadow-2xl ring-4 ring-white/10 overflow-hidden">
                    <div class="w-full h-full rounded-[32px] bg-gradient-to-br from-teal-900 to-teal-800 flex items-center justify-center text-6xl font-serif font-black text-white shadow-inner group-hover:scale-110 transition-transform duration-700">
                        {{ substr($user->username, 0, 1) }}
                    </div>
                </div>
                <div class="absolute -bottom-2 -right-2 px-4 py-1.5 bg-cyan-400 text-teal-950 text-[10px] font-black uppercase rounded-xl border-4 border-white shadow-lg">
                    Online
                </div>
            </div>
            
            <div class="flex-1 pb-4">
                <div class="flex items-center gap-4 mb-2">
                    <h1 class="text-5xl font-serif font-black text-teal-900 tracking-tighter leading-none">{{ $user->username }}</h1>
                    <span class="px-3 py-1 bg-teal-50 border border-teal-100 rounded-lg text-[10px] font-black text-teal-700 uppercase tracking-widest">Authorized Hunter</span>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] flex items-center gap-3">
                    <i class="fas fa-fingerprint text-cyan-500"></i>
                    Registry Node: SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }} <span class="text-slate-200">|</span> {{ $user->email }}
                </p>
            </div>
            
            <div class="flex gap-4 pb-4">
                 <a href="{{ route('admin.hunters.edit', $user) }}" class="px-8 py-4 bg-teal-900 text-white rounded-2xl font-serif font-black uppercase tracking-widest text-xs shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all group active:scale-95">
                    <i class="fas fa-sliders-h mr-3 text-cyan-400 group-hover:rotate-180 transition-transform duration-500"></i>
                    Recalibrate Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Core Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Rank Node -->
        <div class="glass-panel p-8 rounded-[40px] flex flex-col items-center justify-center text-center relative overflow-hidden group border-2 border-slate-100 bg-white">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-transparent"></div>
            <div class="relative z-10">
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-6">Authority Tier</h3>
                <div class="text-7xl font-serif font-black {{ $user->rankTier->color_code ? '' : 'text-teal-900' }} uppercase italic mb-4 drop-shadow-xl group-hover:scale-110 transition-transform duration-700" style="{{ $user->rankTier->color_code ? 'color: ' . $user->rankTier->color_code : '' }}">
                    {{ $user->rankTier->slug ?? 'E' }}
                </div>
                <div class="inline-flex px-4 py-1.5 bg-teal-900 border border-white/10 rounded-xl text-[9px] font-black text-cyan-400 uppercase tracking-widest shadow-2xl">
                    {{ $user->rankTier->name ?? 'Novice Rank' }}
                </div>
            </div>
        </div>

        <!-- evolution stage -->
        <div class="md:col-span-2 glass-panel p-10 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-100">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-[9px] font-black text-teal-900/40 uppercase tracking-[0.5em]">Evolution Status</h3>
                <span class="text-[10px] font-black text-teal-900 font-mono italic">STAGE {{ $user->level }}</span>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="w-20 h-20 rounded-3xl bg-slate-50 border-2 border-slate-100 flex items-center justify-center text-teal-900 shadow-inner group">
                    <i class="fas fa-shield-halved text-3xl group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="flex justify-between items-end">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight">{{ $user->jobClass->name ?? 'Initial Class' }}</span>
                        <p class="text-sm font-black text-cyan-600 font-mono">{{ number_format($user->experience) }} <span class="text-[9px] text-slate-300">/ 5,000 XP</span></p>
                    </div>
                    <div class="relative h-4 bg-slate-100 rounded-full overflow-hidden p-1 shadow-inner">
                        @php $expPercent = min(($user->experience / 5000) * 100, 100); @endphp
                        <div class="h-full bg-gradient-to-r from-teal-900 to-cyan-500 rounded-full shadow-[0_0_15px_rgba(34,211,238,0.3)] transition-all duration-1000 ease-out" style="width: {{ $expPercent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Economy Node -->
        <div class="glass-panel p-8 rounded-[40px] bg-teal-900 border-2 border-white/5 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-gold-400/10 rounded-full blur-[80px]"></div>
            <h3 class="text-[9px] font-black text-cyan-400/60 uppercase tracking-[0.5em] mb-8">Wealth Matrix</h3>
            <div class="flex flex-col items-center">
                <div class="text-4xl font-black text-gold-400 font-mono tracking-tighter mb-2 group-hover:scale-110 transition-transform">
                    {{ number_format($user->soul_points) }}
                </div>
                <p class="text-[9px] font-black text-white/40 uppercase tracking-[0.4em]">Soul Points (SP)</p>
                
                <div class="mt-8 flex gap-3">
                    <div class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-[8px] font-black text-cyan-400 tracking-widest uppercase">Verified Flow</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Hub -->
    <div class="flex items-center gap-3 p-2 bg-slate-100/50 backdrop-blur-md rounded-[30px] border-2 border-slate-100 w-fit">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-btn active px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all">Overview</button>
        <button onclick="switchTab('daily')" id="tab-daily" class="tab-btn px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Protocols</button>
        <button onclick="switchTab('habits')" id="tab-habits" class="tab-btn px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Patterns</button>
        <button onclick="switchTab('todos')" id="tab-todos" class="tab-btn px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Deadlines</button>
    </div>

    <!-- Tab Contents -->
    <div id="content-overview" class="tab-content space-y-10 animate-fadeIn">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Accomplishments -->
            <div class="lg:col-span-2 glass-panel p-12 rounded-[50px] relative overflow-hidden flex flex-col h-full bg-white shadow-xl border-2 border-slate-50">
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Protocol Success Rate</h3>
                <div class="grid grid-cols-2 gap-8 flex-1">
                    <div class="p-8 bg-slate-50 rounded-[40px] border-2 border-slate-100 group/item hover:bg-white hover:border-cyan-200 transition-all">
                        <div class="w-14 h-14 bg-teal-900 rounded-2xl flex items-center justify-center text-cyan-400 mb-6 shadow-xl shadow-teal-950/20 group-hover/item:scale-110 transition-transform">
                            <i class="fas fa-check-double text-xl icon-glow"></i>
                        </div>
                        <p class="text-4xl font-black text-teal-900 font-mono tracking-tighter">{{ $completedCount }}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2">Missions Finalized</p>
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[40px] border-2 border-slate-100 group/item hover:bg-white hover:border-teal-200 transition-all">
                         <div class="w-14 h-14 bg-cyan-400 rounded-2xl flex items-center justify-center text-white mb-6 shadow-xl shadow-cyan-400/20 group-hover/item:scale-110 transition-transform">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                        <p class="text-4xl font-black text-teal-900 font-mono tracking-tighter">{{ number_format($user->experience) }}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2">Energy (XP) Harvested</p>
                    </div>
                </div>
            </div>

            <!-- Attributes Breakdown (Placeholder or actual stats) -->
            <div class="glass-panel p-10 rounded-[50px] bg-slate-50/50 border-2 border-slate-100">
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-8">Attribute Tuning</h3>
                <div class="space-y-6">
                    @php
                        $attrs = [
                            ['name' => 'Strength', 'val' => 12, 'color' => 'red'],
                            ['name' => 'Intelligence', 'val' => 18, 'color' => 'blue'],
                            ['name' => 'Agility', 'val' => 14, 'color' => 'yellow'],
                            ['name' => 'Vitality', 'val' => 15, 'color' => 'emerald'],
                        ];
                    @endphp
                    @foreach($attrs as $a)
                    <div class="group">
                        <div class="flex justify-between items-center mb-2 px-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $a['name'] }}</span>
                            <span class="text-sm font-black text-teal-900 font-mono">{{ $a['val'] }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200 shadow-inner">
                            <div class="h-full bg-{{ $a['color'] }}-400 rounded-full group-hover:opacity-80 transition-all" style="width: {{ ($a['val'] / 20) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="pt-8 border-t border-slate-200 mt-6">
                        <p class="text-[9px] font-black text-slate-300 uppercase italic text-center">Data synchronized with physical manifestations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Protocols Tab -->
    <div id="content-daily" class="tab-content hidden animate-fadeIn">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50">
            <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Today's Executed Protocols</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($userDailyTasks as $udt)
                <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100 flex items-center justify-between group hover:bg-white hover:border-emerald-200 transition-all shadow-sm">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 text-teal-600 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                            {!! $udt->dailyTask->icon ?? '🎯' !!}
                        </div>
                        <div>
                            <p class="text-lg font-serif font-black text-teal-900 tracking-tight leading-none mb-1">{{ $udt->dailyTask->name }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-slate-400 uppercase font-mono tracking-tighter">Verified:</span>
                                <span class="text-[9px] font-black text-emerald-500 font-mono uppercase">{{ $udt->completed_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check text-[10px]"></i>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center border-2 border-dashed border-slate-100 rounded-[40px] opacity-40">
                    <i class="fas fa-satellite-dish text-5xl mb-6 text-slate-200"></i>
                    <p class="text-sm font-black text-teal-900 uppercase tracking-[0.4em]">No Daily Protocols Detected Today</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Patterns Tab -->
    <div id="content-habits" class="tab-content hidden animate-fadeIn">
         <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50">
            <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Observed Behavioral Patterns</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($habits as $habit)
                <div class="p-8 bg-slate-50 rounded-[40px] border-2 border-slate-100 relative overflow-hidden group hover:bg-white hover:border-{{ $habit->is_positive ? 'emerald' : 'red' }}-200 transition-all">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-{{ $habit->is_positive ? 'emerald' : 'red' }}-400/5 rounded-full blur-2xl"></div>
                    
                    <div class="flex items-start justify-between mb-8">
                        <div class="w-16 h-16 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform">
                             {!! $habit->icon ?? '🌀' !!}
                        </div>
                        <div class="px-4 py-1.5 rounded-xl bg-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest border border-slate-200/50">
                            {{ $habit->frequency }} Cycle
                        </div>
                    </div>
                    
                    <h4 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight mb-2">{{ $habit->title }}</h4>
                    <p class="text-xs text-slate-500 leading-relaxed italic line-clamp-2 h-10 mb-8">{{ $habit->notes ?? 'Observation log is minimal for this pattern.' }}</p>
                    
                    <div class="pt-6 border-t border-slate-200/50 flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Intensity Profile</span>
                        <div class="flex flex-col items-end">
                            <span class="text-2xl font-black text-teal-900 font-mono tracking-tighter leading-none">{{ $habit->count }}x</span>
                            <span class="text-[8px] font-bold text-{{ $habit->is_positive ? 'emerald' : 'red' }}-500 uppercase">Registered</span>
                        </div>
                    </div>
                </div>
                @empty
                 <div class="col-span-full py-24 text-center border-2 border-dashed border-slate-100 rounded-[40px] opacity-40">
                    <i class="fas fa-dna text-5xl mb-6 text-slate-200"></i>
                    <p class="text-sm font-black text-teal-900 uppercase tracking-[0.4em]">Environmental Patterns Stabilized</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Todo Tab -->
    <div id="content-todos" class="tab-content hidden animate-fadeIn">
        <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50">
            <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Pending Objectives Matrix</h3>
            <div class="space-y-6">
                @forelse($todos as $todo)
                <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 group hover:bg-white hover:border-cyan-200 transition-all shadow-sm">
                    <div class="flex items-center gap-8">
                        <div class="w-10 h-10 rounded-xl border-4 {{ $todo->is_completed ? 'bg-cyan-400 border-cyan-100' : 'bg-white border-slate-100' }} flex items-center justify-center transition-all shadow-inner">
                            @if($todo->is_completed)
                                <i class="fas fa-check text-white text-xs"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-2xl font-serif font-black text-teal-900 tracking-tight {{ $todo->is_completed ? 'line-through opacity-40' : '' }}">{{ $todo->title }}</h4>
                            @if($todo->due_date)
                                <div class="flex items-center gap-2 mt-2">
                                    <i class="far fa-clock text-[10px] text-red-400"></i>
                                    <span class="text-[9px] font-black text-red-400 uppercase tracking-widest">DEADLINE Node: {{ $todo->due_date->format('Y.m.d') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($todo->checklist && count($todo->checklist) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($todo->checklist as $item)
                        <div class="px-4 py-1.5 rounded-xl border {{ $item['is_completed'] ? 'bg-cyan-50 border-cyan-100 text-cyan-600' : 'bg-white border-slate-200 text-slate-400' }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                            {{ $item['title'] ?? 'Generic Task' }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                 <div class="py-24 text-center border-2 border-dashed border-slate-100 rounded-[40px] opacity-40">
                    <i class="fas fa-scroll-old text-5xl mb-6 text-slate-200"></i>
                    <p class="text-sm font-black text-teal-900 uppercase tracking-[0.4em]">All Objectives Manifested</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('scripts')
<style>
    .tab-btn.active {
        background: #093b48;
        color: #22d3ee;
        box-shadow: 0 15px 30px -5px rgba(9, 59, 72, 0.4);
        transform: translateY(-2px);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.getElementById('content-' + tabId).classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-cyan-400', 'bg-teal-900');
            btn.classList.add('text-slate-400');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-slate-400');
    }
</script>
@endpush
@endsection
