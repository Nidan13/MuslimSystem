@extends('layouts.admin')

@section('title', 'Grand Sanctuary Command')

@section('content')
<!-- High-Tech Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
    
    <!-- Registered Hunters -->
    <div class="glass-panel p-8 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-teal-500/5 rounded-full blur-2xl group-hover:bg-teal-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-users-cog text-xl icon-glow"></i>
            </div>
            <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-[0.3em] bg-slate-100 px-3 py-1.5 rounded-full">Registry</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hunter Population</p>
            <h3 class="text-4xl font-serif font-black text-teal-900 tracking-tight leading-none">
                {{ number_format($stats['total_users']) }}
                <span class="text-xs font-black text-cyan-400 align-top ml-1 tracking-tighter">SOULS</span>
            </h3>
        </div>
    </div>

    <!-- Active Missions -->
    <div class="glass-panel p-8 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-scroll text-xl icon-glow"></i>
            </div>
            <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-[0.3em] bg-slate-100 px-3 py-1.5 rounded-full">Protocols</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Mandates</p>
            <h3 class="text-4xl font-serif font-black text-teal-900 tracking-tight leading-none">
                {{ number_format($stats['active_quests']) }}
                <span class="text-xs font-black text-cyan-400 align-top ml-1 tracking-tighter">NODES</span>
            </h3>
        </div>
    </div>

    <!-- Rift Management -->
    <div class="glass-panel p-8 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-red-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-dungeon text-xl icon-glow"></i>
            </div>
            <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-[0.3em] bg-slate-100 px-3 py-1.5 rounded-full">Anomalies</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Detected Gates</p>
            <h3 class="text-4xl font-serif font-black text-teal-900 tracking-tight leading-none">
                {{ number_format($stats['total_dungeons']) }}
                <span class="text-xs font-black text-red-500 align-top ml-1 tracking-tighter">RIFTS</span>
            </h3>
        </div>
    </div>

    <!-- Economy Matrix -->
    <div class="glass-panel p-8 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-gold-500/5 rounded-full blur-2xl group-hover:bg-gold-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-gold-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-coins text-xl icon-glow"></i>
            </div>
            <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-[0.3em] bg-gold-50 px-3 py-1.5 rounded-full">Economy</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Soul Points Flow</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-4xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">8.4k</h3>
                <span class="text-[10px] font-black text-gold-600 tracking-widest uppercase">Steady</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    
    <!-- Tactical Monitor Section -->
    <div class="lg:col-span-2 space-y-10">
        <div class="glass-panel p-10 rounded-[40px] relative overflow-hidden">
            <!-- Background Matrix Pattern -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, #0f4c5c 1px, transparent 0); background-size: 24px 24px;"></div>
            
            <div class="flex justify-between items-center mb-10 relative z-10">
                <div>
                    <h4 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Raid Monitor <span class="text-cyan-400 font-sans tracking-normal ml-2">Active</span></h4>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                        Scanning system for ongoing anomalies
                    </p>
                </div>
                <button class="px-6 py-2 rounded-xl bg-slate-50 border-2 border-slate-100 text-[10px] font-black text-teal-900 hover:border-cyan-400 transition-all uppercase tracking-widest">Global Scan</button>
            </div>
            
            <div class="relative py-20 bg-slate-50/50 rounded-[32px] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center group overflow-hidden">
                <!-- Decorative Circles -->
                <div class="absolute w-64 h-64 border border-cyan-400/20 rounded-full animate-[spin_20s_linear_infinite]"></div>
                <div class="absolute w-48 h-48 border border-slate-200 rounded-full"></div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 rounded-full bg-white shadow-xl flex items-center justify-center mb-6 text-slate-300 group-hover:text-cyan-400 group-hover:scale-110 transition-all duration-500">
                        <i class="fas fa-satellite-dish text-3xl animate-pulse"></i>
                    </div>
                    <p class="text-teal-950 font-serif font-black text-xl italic uppercase tracking-wider mb-2">Omni-Silent State</p>
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-[0.5em]">No active combat nodes detected</p>
                </div>
            </div>
        </div>

        <!-- Quick Access Nodes -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ route('admin.hunters.create') }}" class="glass-panel p-6 rounded-3xl text-center group transition-all hover:bg-teal-900 active:scale-95">
                <i class="fas fa-user-plus text-teal-900 text-xl mb-3 group-hover:text-cyan-400 transition-colors"></i>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white transition-colors">New Hunter</p>
            </a>
            <a href="{{ route('admin.quests.create') }}" class="glass-panel p-6 rounded-3xl text-center group transition-all hover:bg-teal-900 active:scale-95">
                <i class="fas fa-scroll text-teal-900 text-xl mb-3 group-hover:text-cyan-400 transition-colors"></i>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white transition-colors">Initialize Quest</p>
            </a>
            <a href="{{ route('admin.shop.create') }}" class="glass-panel p-6 rounded-3xl text-center group transition-all hover:bg-teal-900 active:scale-95">
                <i class="fas fa-plus-circle text-teal-900 text-xl mb-3 group-hover:text-cyan-400 transition-colors"></i>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white transition-colors">Add Item</p>
            </a>
            <a href="{{ route('admin.activity-logs.index') }}" class="glass-panel p-6 rounded-3xl text-center group transition-all hover:bg-teal-900 active:scale-95">
                <i class="fas fa-terminal text-teal-900 text-xl mb-3 group-hover:text-cyan-400 transition-colors"></i>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white transition-colors">Access Logs</p>
            </a>
        </div>
    </div>

    <!-- System Pulse (Logs) -->
    <div class="glass-panel p-10 rounded-[40px] h-full relative overflow-hidden flex flex-col">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-cyan-400/10 flex items-center justify-center text-cyan-600">
                <i class="fas fa-wave-square animate-pulse"></i>
            </div>
            <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wide uppercase">System Pulse</h4>
        </div>

        <div class="space-y-10 relative flex-1">
            <!-- Timeline Line -->
            <div class="absolute left-[7px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-cyan-400/30 via-slate-100 to-transparent"></div>

            <!-- Log Item: Live -->
            <div class="flex gap-6 relative group cursor-crosshair">
                <div class="w-3.5 h-3.5 rounded-full bg-cyan-400 ring-4 ring-white shadow-xl z-10 mt-1 animate-pulse"></div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                         <span class="text-[9px] font-black text-cyan-500 uppercase tracking-widest bg-cyan-50 px-2 py-0.5 rounded-lg border border-cyan-100">Synchronized</span>
                         <span class="text-[8px] text-slate-300 font-bold uppercase">Now</span>
                    </div>
                    <p class="text-sm font-black text-teal-950 uppercase tracking-tight">Access Protocol Validated</p>
                    <p class="text-[10px] text-slate-400 font-medium font-mono">NODE_AUTH::SUCCESS for root_admin</p>
                </div>
            </div>

            <!-- Log Item -->
            <div class="flex gap-6 relative group">
                <div class="w-3.5 h-3.5 rounded-full bg-slate-200 ring-4 ring-white z-10 mt-1 group-hover:bg-teal-900 transition-colors"></div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                         <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-0.5 rounded-lg">Routine</span>
                         <span class="text-[8px] text-slate-300 font-bold uppercase">4h ago</span>
                    </div>
                    <p class="text-sm font-black text-teal-900 uppercase tracking-tight opacity-70 group-hover:opacity-100 transition-all">Matrix Reset Protocol</p>
                    <p class="text-[10px] text-slate-400 font-medium font-mono">CRON_JOB::SYSTEM_FLUSH completed</p>
                </div>
            </div>

            <!-- Log Item -->
            <div class="flex gap-6 relative group">
                <div class="w-3.5 h-3.5 rounded-full bg-slate-200 ring-4 ring-white z-10 mt-1 group-hover:bg-teal-900 transition-colors"></div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                         <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-0.5 rounded-lg">Backup</span>
                         <span class="text-[8px] text-slate-300 font-bold uppercase">12h ago</span>
                    </div>
                    <p class="text-sm font-black text-teal-900 uppercase tracking-tight opacity-70 group-hover:opacity-100 transition-all">Snapshot Manifested</p>
                    <p class="text-[10px] text-slate-400 font-medium font-mono">DB_SHIELD::STABILIZED version 2.1.0</p>
                </div>
            </div>

            <!-- More Status Placeholder -->
            <div class="pt-8 text-center">
                <a href="{{ route('admin.activity-logs.index') }}" class="text-[9px] font-black text-slate-300 hover:text-cyan-500 uppercase tracking-[0.4em] transition-all bg-slate-50/50 px-6 py-2 rounded-full border border-slate-100">Analyze Full Matrix</a>
            </div>
        </div>
    </div>
</div>
@endsection
