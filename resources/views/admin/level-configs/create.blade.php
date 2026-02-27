@extends('layouts.admin')

@section('title', 'Extend Progres Matrix')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.level-configs.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Extend Horizon</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Defining New Evolution Threshold
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.level-configs.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Evolution Level</label>
                        <input type="number" name="level" value="{{ old('level') }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gold-500 uppercase mb-3 tracking-[0.3em] ml-1">Stat points yield (AP)</label>
                        <input type="number" name="stat_points_reward" value="{{ old('stat_points_reward', 5) }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-gold-600 p-6 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Experience required (XP)</label>
                    <div class="relative">
                        <input type="number" name="xp_required" value="{{ old('xp_required') }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-cyan-600 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                        <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">Points</span>
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        RATIFY EVOLUTION STEP
                        <i class="fas fa-arrow-trend-up text-cyan-400 icon-glow transition-all group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
