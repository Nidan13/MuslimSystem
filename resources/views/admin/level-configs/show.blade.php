@extends('layouts.admin')

@section('title', 'Evolution Matrix Audit')

@section('content')
<div class="space-y-10">
    <div class="flex items-center gap-6 pb-10 border-b-2 border-slate-100">
        <a href="{{ route('admin.level-configs.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Evolution Stage {{ $config->level }}</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                System Core Progression Parameters
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Level Visual -->
        <div class="glass-panel p-2 rounded-[50px] bg-white shadow-2xl border-2 border-slate-100">
            <div class="p-10 pb-12 rounded-[40px] bg-slate-50/50 border-2 border-slate-50 text-center relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-cyan-400/5 rounded-full blur-[80px]"></div>
                
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-12">Stage Proxy</h3>
                
                <div class="text-[10rem] font-serif font-black text-teal-900 leading-none italic tracking-tighter drop-shadow-sm mb-12">
                    {{ $config->level }}
                </div>

                <div class="inline-flex items-center gap-3 px-6 py-2 bg-teal-900 rounded-2xl border border-white/10 shadow-xl">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span class="text-xs font-black text-white uppercase tracking-[0.2em]">Authorized Level</span>
                </div>
            </div>
        </div>

        <!-- Parameters -->
        <div class="lg:col-span-2 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="glass-panel p-10 rounded-[40px] border-l-4 border-l-cyan-400 bg-white shadow-xl">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt text-cyan-500 opacity-60"></i>
                        Experience Threshold
                    </p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-teal-900 font-mono tracking-tighter">{{ number_format($config->xp_required) }}</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase italic">XP Point</span>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 text-[10px] text-slate-400 leading-relaxed italic">
                        The matrix requires this much spiritual energy to transcend to stage {{ $config->level + 1 }}.
                    </div>
                </div>

                <div class="glass-panel p-10 rounded-[40px] border-l-4 border-l-gold-500 bg-white shadow-xl">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-plus text-gold-500 opacity-60"></i>
                        Ability Points Yield
                    </p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-teal-900 font-mono tracking-tighter">+{{ $config->stat_points_reward }}</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase italic">AP Reward</span>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 text-[10px] text-slate-400 leading-relaxed italic">
                         Hunters manifesting this stage receive localized attribute enhancements.
                    </div>
                </div>
            </div>

            <!-- Management -->
            <div class="glass-panel p-10 rounded-[40px] bg-slate-50/50 border-2 border-slate-100">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">Node Maintenance</h4>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.level-configs.edit', $config->level) }}" class="px-8 py-3.5 bg-white border-2 border-slate-200 rounded-2xl text-teal-900 font-serif font-black uppercase tracking-widest text-xs hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm">
                        RECALIBRATE STEP
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
