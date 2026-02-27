@extends('layouts.admin')

@section('title', 'Dungeon Audit')

@section('content')
<div class="space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-10 border-b-2 border-slate-100">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.dungeons.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase leading-none">{{ $dungeon->name }}</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em] mt-3 flex items-center gap-3">
                    <span class="{{ $dungeon->rankTier->color_code ?? 'text-teal-900' }}">Tier {{ $dungeon->rankTier->slug ?? '?' }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                    <span>Gate Registry Node: {{ $dungeon->dungeonType->name ?? 'VOID' }}</span>
                </p>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.dungeons.edit', $dungeon) }}" class="px-8 py-3.5 bg-white border-2 border-slate-200 rounded-2xl text-teal-900 font-serif font-black uppercase tracking-widest text-xs hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm">
                RECONFIGURE PROTOCOL
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Info Column -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Lore Card -->
            <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                <div class="relative z-10">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-8">Historical Records & Metadata</h3>
                    <p class="text-slate-600 leading-loose text-xl font-serif font-medium italic">
                        "{{ $dungeon->description ?: 'Magical signature is stable but contains no recorded history in the system archives.' }}"
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="glass-panel p-10 rounded-[40px] border-l-4 border-l-cyan-400">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-8">Access Credentials</h3>
                    <div class="space-y-6">
                        <div class="flex justify-between items-end border-b-2 border-slate-50 pb-4">
                            <span class="text-xs font-bold text-slate-500 uppercase">Min. Authority</span>
                            <span class="{{ $dungeon->rankTier->color_code ?? 'text-teal-900' }} font-black text-2xl font-serif">TIER {{ $dungeon->rankTier->slug ?? 'E' }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b-2 border-slate-50 pb-4">
                            <span class="text-xs font-bold text-slate-500 uppercase">Min. Experience</span>
                            <span class="text-teal-900 font-black text-2xl font-mono">LVL {{ $dungeon->min_level_requirement }}</span>
                        </div>
                        <div class="flex justify-between items-end pb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase">classification</span>
                            <span class="text-cyan-600 font-black text-lg uppercase tracking-wider">{{ $dungeon->dungeonType->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-10 rounded-[40px] border-l-4 border-l-teal-900 bg-slate-50/50">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-8">Capacity Parameters</h3>
                    <div class="flex flex-col items-center justify-center py-4">
                        <div class="text-6xl font-black text-teal-900 mb-2 tracking-tighter">{{ $dungeon->dungeonType->max_participants ?? 1 }}</div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">Maximum Hunters</div>
                        <div class="mt-8 w-24 h-1 bg-slate-200 rounded-full relative overflow-hidden">
                             <div class="absolute inset-0 bg-teal-900 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reward Column -->
        <div class="space-y-10">
            <div class="glass-panel p-10 rounded-[40px] border-t-8 border-t-gold-500 bg-gradient-to-br from-white to-gold-50/20">
                <h3 class="text-[10px] font-black text-gold-600 uppercase tracking-[0.4em] mb-10 text-center">Manifest Rewards</h3>
                <div class="text-center py-10 border-y-2 border-gold-100 mb-10 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gold-400/5 blur-3xl rounded-full"></div>
                    <div class="relative z-10">
                        <div class="text-[10px] font-black text-gold-600/60 uppercase tracking-widest mb-3">Base System Credit</div>
                        <div class="text-6xl font-black text-gold-600 font-mono tracking-tighter">★ {{ number_format($dungeon->reward_soul_points) }}</div>
                        <div class="text-[10px] font-black text-gold-500/40 uppercase tracking-[0.3em] mt-3">Soul Points (SP)</div>
                    </div>
                </div>
                <div>
                    <h4 class="text-[9px] font-black text-slate-400 uppercase mb-4 tracking-widest">Loot Pool Extraction</h4>
                    <div class="p-4 bg-white border-2 border-slate-100 rounded-[20px] text-[11px] text-slate-500 font-medium italic leading-relaxed">
                        Scanning regional metadata for drop probabilities... Matrix synchronization required for exact data.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
