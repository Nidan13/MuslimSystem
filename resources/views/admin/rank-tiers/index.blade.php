@extends('layouts.admin')

@section('title', 'Authority Rank Registry')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Authority Tiers</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Defining Global Rank Structures & Power Scaling
            </p>
        </div>
        <a href="{{ route('admin.rank-tiers.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-medal text-cyan-400 icon-glow transition-transform group-hover:scale-110"></i>
                Forge Tier
            </span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($rankTiers as $tier)
        <div class="glass-panel p-1 rounded-[40px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500 border-2 {{ $tier->name == 'S' ? 'border-gold-400/30' : 'border-slate-100' }}">
            <!-- Card Body -->
            <div class="p-8 pb-10">
                <!-- Header -->
                <div class="flex justify-between items-start mb-10">
                    <div class="p-4 rounded-2xl bg-slate-50 border-2 border-slate-100 flex items-center justify-center font-mono font-black text-xs text-slate-400 shadow-inner">
                        NODE-{{ str_pad($tier->id, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1 bg-teal-900 text-cyan-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-teal-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        Authorized
                    </div>
                </div>

                <!-- Rank Visual -->
                <div class="flex flex-col items-center mb-10">
                    <div class="text-7xl font-serif font-black {{ $tier->color_code ?? 'text-teal-900' }} leading-none italic tracking-tighter drop-shadow-sm group-hover:scale-110 transition-transform duration-700">
                        {{ $tier->slug }}
                    </div>
                    <div class="mt-4 text-[11px] font-black text-teal-900 uppercase tracking-[0.6em] ml-2">
                        {{ $tier->name }}
                    </div>
                </div>

                <!-- Stats -->
                <div class="space-y-4 px-2">
                    <div class="flex justify-between items-center py-3 border-y-2 border-slate-50">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Min. Req</span>
                        <span class="text-sm font-black text-teal-900 font-mono tracking-tighter">{{ number_format($tier->min_xp_required) }} XP</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Hunters</span>
                        <span class="px-2.5 py-0.5 bg-teal-50 text-teal-700 rounded-lg text-[10px] font-black italic">{{ $tier->users_count ?? $tier->users()->count() }} Nodes</span>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="absolute bottom-0 left-0 right-0 p-6 bg-slate-50/50 backdrop-blur-sm border-t-2 border-slate-50 translate-y-full group-hover:translate-y-0 transition-transform duration-500 flex justify-between gap-3">
                <a href="{{ route('admin.rank-tiers.show', $tier) }}" class="flex-1 py-3 bg-white border-2 border-slate-100 rounded-2xl text-center text-[10px] font-black text-teal-900 uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm">
                    Inspect
                </a>
                <a href="{{ route('admin.rank-tiers.edit', $tier) }}" class="p-3 bg-teal-900 text-cyan-400 rounded-2xl hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20">
                    <i class="fas fa-sliders text-xs"></i>
                </a>
            </div>

            <!-- Background Elements -->
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-slate-100 rounded-full blur-3xl opacity-50 pointer-events-none group-hover:bg-cyan-100 transition-colors"></div>
        </div>
        @endforeach

        <!-- Create Placeholder -->
        <a href="{{ route('admin.rank-tiers.create') }}" class="group p-1 rounded-[40px] border-2 border-dashed border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/30 transition-all duration-500 flex items-center justify-center min-h-[300px]">
             <div class="flex flex-col items-center text-slate-300 group-hover:text-cyan-500 transition-colors">
                 <div class="w-16 h-16 rounded-full border-2 border-dashed border-current flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                     <i class="fas fa-plus text-2xl"></i>
                 </div>
                 <span class="text-[10px] font-black uppercase tracking-[0.4em]">Initialize New Tier</span>
             </div>
        </a>
    </div>
</div>
@endsection
