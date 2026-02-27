@extends('layouts.admin')

@section('title', 'Authority Tier Audit')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <!-- Header -->
    <div class="flex items-center justify-between pb-10 border-b-2 border-slate-100">
        <div class="flex items-center gap-6">
             <a href="{{ route('admin.rank-tiers.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tight uppercase leading-none">Authority Protocol</h1>
                <div class="flex items-center gap-3 mt-3">
                    <span class="px-3 py-1 bg-teal-900 text-[10px] font-black text-cyan-400 uppercase tracking-widest rounded-lg border border-teal-800">
                        Tier {{ $rankTier->slug }} / {{ $rankTier->name }}
                    </span>
                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Protocol ID: RT-{{ str_pad($rankTier->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
             <a href="{{ route('admin.rank-tiers.edit', $rankTier) }}" class="px-10 py-4 bg-teal-900 hover:bg-teal-800 rounded-2xl text-white font-serif font-black uppercase tracking-widest text-xs shadow-xl shadow-teal-950/20 transition-all group active:scale-95">
                <i class="fas fa-sliders-h mr-3 text-cyan-400 transition-transform group-hover:rotate-90"></i>
                Recalibrate Node
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Visual Signature Card -->
        <div class="glass-panel p-2 rounded-[50px] relative overflow-hidden group border-2 border-slate-100 bg-white shadow-2xl">
            <div class="p-10 pb-12 rounded-[40px] bg-slate-50/50 border-2 border-slate-50 text-center relative overflow-hidden">
                <!-- Rank Glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-transparent to-slate-200/20 pointer-events-none"></div>
                <div class="absolute -top-20 -right-20 w-64 h-64 opacity-10 rounded-full blur-[100px]" style="background-color: {{ $rankTier->color_code ?? '#22d3ee' }}"></div>
                
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-12 relative z-10">Global Signature</h3>
                
                <div class="relative z-10 mb-12">
                    <div class="text-[12rem] font-serif font-black {{ $rankTier->color_code ? '' : 'text-teal-900' }} leading-none italic tracking-tighter drop-shadow-2xl group-hover:scale-105 transition-transform duration-700" style="{{ $rankTier->color_code ? 'color: ' . $rankTier->color_code : '' }}">
                        {{ $rankTier->slug }}
                    </div>
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-teal-900 rounded-2xl border border-white/10 shadow-xl">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span class="text-xs font-black text-white uppercase tracking-[0.2em] font-serif">{{ $rankTier->name }} Authority</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- content Info -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Lore & Requirement -->
            <div class="glass-panel p-12 rounded-[50px] relative overflow-hidden flex flex-col h-full bg-white shadow-2xl border-2 border-slate-100">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-slate-50 rounded-full blur-[100px] pointer-events-none"></div>
                
                <div class="flex-1">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-10">Protocol Directives</h3>
                    <p class="text-3xl text-slate-700 font-serif leading-relaxed italic mb-12 relative z-10">
                        "{{ $rankTier->description ?? 'No historical directives has been recorded for this rank. The narrative protocol remains unwritten in the system matrix.' }}"
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100 group/attr hover:bg-white transition-all">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                             <i class="fas fa-bolt text-cyan-500 opacity-60"></i>
                             Energy Threshold
                        </p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-teal-900 font-mono tracking-tighter">{{ number_format($rankTier->min_xp_required) }}</span>
                            <span class="text-[10px] font-black text-slate-400 uppercase italic">Accumulated XP</span>
                        </div>
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100 group/attr hover:bg-white transition-all">
                         <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                             <i class="fas fa-shield-halved text-teal-600 opacity-60"></i>
                             Manifestation Proxy
                        </p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-teal-900 font-mono tracking-tighter">{{ $rankTier->min_level_requirement }}</span>
                            <span class="text-[10px] font-black text-slate-400 uppercase italic">Evolution Stage</span>
                        </div>
                    </div>
                </div>

                <!-- Active Nodes List -->
                <div class="mt-12 pt-10 border-t-2 border-slate-100">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bonded Nodes (Hunters)</h4>
                        <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-widest bg-slate-50 px-4 py-1.5 rounded-xl border border-slate-100">{{ $rankTier->users_count ?? $rankTier->users()->count() }} ACTIVE</span>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        @forelse($rankTier->users->take(10) as $user)
                        <div class="w-10 h-10 rounded-xl bg-teal-900 flex items-center justify-center font-serif font-black text-white text-[10px] shadow-lg hover:scale-110 transition-transform cursor-help border border-white/10" title="{{ $user->username }}">
                             {{ substr($user->username, 0, 1) }}
                        </div>
                        @empty
                        <p class="text-xs text-slate-300 italic py-4">No hunters currently Manifested at this authority tier...</p>
                        @endforelse
                        
                        @if(($rankTier->users_count ?? $rankTier->users()->count()) > 10)
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 border-2 border-slate-50">
                            +{{ ($rankTier->users_count ?? $rankTier->users()->count()) - 10 }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
