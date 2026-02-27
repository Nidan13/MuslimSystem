@extends('layouts.admin')

@section('title', 'Mission Briefing: ' . $quest->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <div class="flex justify-between items-end">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.quests.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">{{ $quest->title }}</h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                    Classification: {{ $quest->questType->name }} <span class="text-slate-200">|</span> ID: QST-{{ str_pad($quest->id, 5, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.quests.edit', $quest) }}" class="px-8 py-4 bg-teal-900 text-white rounded-2xl font-serif font-black uppercase tracking-widest text-xs shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all group active:scale-95">
                <i class="fas fa-edit mr-3 text-cyan-400 group-hover:rotate-12 transition-transform"></i>
                Modify Briefing
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main Briefing -->
        <div class="lg:col-span-2 space-y-10">
            <div class="glass-panel p-12 rounded-[50px] relative overflow-hidden bg-white border-2 border-slate-50">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-50 rounded-full blur-[100px] pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Primary Directive</h3>
                <p class="text-xl font-serif font-black text-teal-900 leading-relaxed italic border-l-4 border-cyan-400 pl-8">
                    "{{ $quest->description ?: 'No operational data provided for this mission protocol.' }}"
                </p>
            </div>

            <!-- Parameters -->
            <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50">
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Mission Requirements Matrix</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($quest->requirements ?? [] as $type => $target)
                    <div class="flex items-center justify-between p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100 group hover:border-cyan-200 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-3 h-3 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
                            <span class="text-[10px] font-black text-teal-900/60 uppercase tracking-widest">{{ str_replace('_', ' ', $type) }}</span>
                        </div>
                        <span class="text-2xl font-black text-teal-900 font-mono tracking-tighter">{{ $target }}</span>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center border-2 border-dashed border-slate-100 rounded-[40px] opacity-40">
                        <p class="text-sm font-black text-teal-900 uppercase tracking-[0.4em]">No Operational Requirements Found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Briefing Stats -->
        <div class="space-y-10">
            <!-- Rewards -->
            <div class="glass-panel p-10 rounded-[50px] bg-teal-900 border-2 border-white/5 relative overflow-hidden group">
                 <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-400/10 rounded-full blur-[80px]"></div>
                 <h3 class="text-[9px] font-black text-cyan-400/60 uppercase tracking-[0.5em] mb-10 text-center">Protocol Yield</h3>
                 
                 <div class="space-y-8">
                    <div class="flex justify-between items-center bg-white/5 p-6 rounded-[32px] border border-white/10 group-hover:bg-white/10 transition-colors">
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Energy Matrix</p>
                            <span class="text-[10px] font-black text-white uppercase">Experience</span>
                        </div>
                        <span class="text-3xl font-black text-cyan-400 font-mono tracking-tighter">{{ number_format($quest->reward_exp) }}</span>
                    </div>

                    <div class="flex justify-between items-center bg-white/5 p-6 rounded-[32px] border border-white/10 group-hover:bg-white/10 transition-colors">
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Soul Currency</p>
                            <span class="text-[10px] font-black text-white uppercase">Soul Points (SP)</span>
                        </div>
                        <span class="text-3xl font-black text-gold-400 font-mono tracking-tighter">{{ number_format($quest->reward_soul_points) }}</span>
                    </div>

                    <div class="flex justify-between items-center bg-white/5 p-6 rounded-[32px] border border-white/10 group-hover:bg-white/10 transition-colors">
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Biological Cost</p>
                            <span class="text-[10px] font-black text-white uppercase">Fatigue Load</span>
                        </div>
                        <span class="text-3xl font-black text-red-400 font-mono tracking-tighter">{{ $quest->penalty_fatigue }}</span>
                    </div>
                 </div>
            </div>

            <!-- Authority Check -->
            <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-100 flex flex-col items-center justify-center text-center">
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-6">Authority Validation</h3>
                <div class="text-5xl font-serif font-black {{ $quest->rankTier->color_code ? '' : 'text-teal-900' }} uppercase italic mb-4" style="{{ $quest->rankTier->color_code ? 'color: ' . $quest->rankTier->color_code : '' }}">
                    TIER {{ $quest->rankTier->slug ?? 'FREE' }}
                </div>
                <div class="px-4 py-1 bg-slate-100 rounded-lg text-[9px] font-black text-slate-500 uppercase tracking-widest">
                     {{ $quest->rankTier->name ?? 'Open Registration' }}
                </div>
            </div>

            <!-- Mandatory Toggle -->
            <div class="p-8 rounded-[40px] {{ $quest->is_mandatory ? 'bg-red-50 border-2 border-red-100' : 'bg-cyan-50 border-2 border-cyan-100' }} text-center">
                 <p class="text-[10px] font-black {{ $quest->is_mandatory ? 'text-red-600' : 'text-cyan-600' }} uppercase tracking-[0.3em] mb-2">{{ $quest->is_mandatory ? 'MANDATORY PROTOCOL' : 'OPTIONAL ENGAGEMENT' }}</p>
                 <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Priority Sequence 0{{ $quest->is_mandatory ? '1' : '2' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
