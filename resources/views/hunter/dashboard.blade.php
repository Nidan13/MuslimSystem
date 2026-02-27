@extends('layouts.hunter')

@section('content')
<!-- Header Section: Player Identity -->
<div class="flex flex-col md:flex-row items-center gap-8 mb-12">
    <div class="relative group">
        <div class="w-40 h-40 bg-slate-900 border-4 border-orange-500 rounded-3xl flex items-center justify-center text-8xl font-black text-white glow-orange transition-transform duration-500 group-hover:scale-105">
            {{ substr($user->username, 0, 1) }}
        </div>
        <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-orange-600 px-6 py-2 rounded-full border-2 border-slate-950 text-sm font-black italic tracking-widest shadow-xl">
            LVL. {{ $user->level }}
        </div>
    </div>
    
    <div class="text-center md:text-left">
        <div class="flex items-center justify-center md:justify-start gap-4 mb-2">
            <h2 class="text-5xl font-black uppercase tracking-tighter text-white text-glow">{{ $user->username }}</h2>
            <div class="px-4 py-1 bg-slate-800 border border-slate-700 rounded text-xs font-bold {{ $user->rankTier->color_code ?? 'text-orange-400' }} uppercase italic tracking-widest">
                {{ $user->rankTier->name ?? 'Rank E' }}
            </div>
        </div>
        <p class="text-orange-500 font-mono tracking-[0.5em] uppercase text-sm mb-6">Class: {{ $user->job_class ?? 'Unawakened' }}</p>
        
        <!-- Exp Bar -->
        <div class="w-full md:w-96 space-y-2">
            <div class="flex justify-between items-end">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Experience Points</span>
                <span class="text-xs font-bold text-slate-300">{{ number_format($user->current_exp) }} / {{ number_format($expNeeded) }}</span>
            </div>
            <div class="h-1.5 w-full bg-slate-900 rounded-full overflow-hidden border border-slate-800">
                <div class="h-full bg-gradient-to-r from-orange-600 to-orange-400 progress-bar" style="width: {{ $expPercent }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <!-- Attributes Left Column -->
    <div class="space-y-4">
        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-500 mb-6 flex items-center gap-4">
            Ability Statistics 
            <div class="flex-1 h-px bg-slate-800"></div>
        </h3>
        
        <div class="grid grid-cols-2 gap-4">
            @php
                $stats = [
                    ['name' => 'Strength', 'key' => 'str', 'icon' => '💪', 'desc' => 'Physical Discipline', 'color' => 'from-red-600/20 to-orange-600/20'],
                    ['name' => 'Intelligence', 'key' => 'int', 'icon' => '🧠', 'desc' => 'Sacred Knowledge', 'color' => 'from-blue-600/20 to-cyan-600/20'],
                    ['name' => 'Wisdom', 'key' => 'wis', 'icon' => '👁️', 'desc' => 'Reflective Mind', 'color' => 'from-purple-600/20 to-pink-600/20'],
                    ['name' => 'Vitality', 'key' => 'vit', 'icon' => '⚡', 'desc' => 'Body Resilience', 'color' => 'from-green-600/20 to-emerald-600/20'],
                ];
            @endphp

            @foreach($stats as $stat)
            <div class="p-6 bg-slate-900/40 border border-slate-800 rounded-2xl hover:border-orange-500/50 transition-all group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br {{ $stat['color'] }} opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-500 mb-1 tracking-widest italic">{{ $stat['name'] }}</p>
                        <h4 class="text-4xl font-black text-white">{{ $user->userStat ? $user->userStat->{$stat['key']} : 0 }}</h4>
                    </div>
                    <span class="text-2xl">{{ $stat['icon'] }}</span>
                </div>
                <p class="relative z-10 text-[9px] font-bold text-slate-600 mt-2 uppercase tracking-tight">{{ $stat['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right Column: Soul Energy & Fatigue -->
    <div class="space-y-6">
        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-500 mb-6 flex items-center gap-4">
            System Resources 
            <div class="flex-1 h-px bg-slate-800"></div>
        </h3>

        <!-- Soul Points Card -->
        <div class="p-8 bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150"></div>
            <p class="text-[10px] font-black uppercase text-orange-500 mb-2 tracking-[0.2em] italic">Soul Essence Balance</p>
            <div class="flex items-center gap-4">
                <span class="text-5xl">★</span>
                <h4 class="text-5xl font-black text-white text-glow">{{ number_format($user->soul_points) }}</h4>
            </div>
            <div class="mt-6 flex gap-2">
                <button class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-orange-500 hover:text-white hover:border-orange-400 transition-all">Exchange Shop</button>
            </div>
        </div>

        <!-- Fatigue / Status Buffs -->
        <div class="p-8 bg-slate-900/40 border border-slate-800 rounded-3xl">
            <div class="flex justify-between items-center mb-4">
                <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest italic">Current Status</p>
                <span class="text-[10px] font-bold text-green-500 px-2 py-0.5 bg-green-500/10 rounded uppercase">Peak Condition</span>
            </div>
            <div class="flex gap-4">
                <div class="w-12 h-12 bg-slate-800 border border-slate-700 rounded-xl flex items-center justify-center" title="No fatigue penalties">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-300">Fatigue: 0%</p>
                    <p class="text-[10px] text-slate-600 uppercase font-bold mt-1">Ready for missions. EXP gain at 100%.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Mission Quick Access -->
<div class="bg-blue-600/5 border border-blue-500/20 p-8 rounded-3xl">
    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex gap-6 items-center">
            <div class="w-16 h-16 bg-blue-600/20 rounded-2xl flex items-center justify-center border border-blue-500/50">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <h4 class="text-xl font-black text-white uppercase tracking-tighter italic">Mandatory Quest: The Preparation to be Great</h4>
                <p class="text-xs text-blue-400 font-bold uppercase tracking-widest mt-1">Status: In Progress | Goal: 0/7 Missions</p>
            </div>
        </div>
        <button class="w-full md:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-black uppercase tracking-widest text-sm rounded-2xl shadow-lg shadow-blue-600/20 transition-all scale-100 active:scale-95">VIEW QUEST LOG</button>
    </div>
</div>
@endsection
