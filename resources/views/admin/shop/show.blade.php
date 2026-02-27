@extends('layouts.admin')

@section('title', 'Artifact Manifestation Detail')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-6">
             <a href="{{ route('admin.shop.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tight uppercase leading-none">{{ $item->name }}</h1>
                <div class="flex items-center gap-3 mt-3">
                    <span class="px-3 py-1 bg-teal-900 text-[10px] font-black text-cyan-400 uppercase tracking-widest rounded-lg border border-teal-800">
                        {{ str_replace('_', ' ', $item->category) }} Class
                    </span>
                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Protocol ID: AR-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
             <a href="{{ route('admin.shop.edit', $item) }}" class="px-8 py-3 bg-white border-2 border-slate-100 rounded-2xl text-teal-900 font-serif font-black uppercase tracking-widest text-xs shadow-sm hover:border-teal-400 hover:text-teal-600 transition-all group">
                <i class="fas fa-pen-nib mr-2 text-gold-500 transition-transform group-hover:rotate-12"></i>
                Modify Archive
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
        <!-- Visual Section -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-panel p-6 rounded-[40px] relative overflow-hidden group">
                <div class="w-full h-[400px] bg-slate-50 rounded-[32px] overflow-hidden relative border-2 border-slate-100 shadow-inner">
                    @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-200 bg-gradient-to-br from-slate-50 to-slate-100">
                        <i class="fas fa-cube text-7xl mb-6 opacity-30 shadow-2xl"></i>
                        <span class="text-[10px] uppercase tracking-[0.5em] font-black text-slate-300">Visuals Not Manifested</span>
                    </div>
                    @endif
                    
                    <!-- Floating Tier Label -->
                    <div class="absolute top-6 left-6 px-4 py-2 bg-teal-900/90 backdrop-blur rounded-2xl border border-white/10 shadow-2xl">
                        <p class="text-[8px] font-black text-cyan-400 uppercase tracking-[0.3em] mb-0.5">Rarity Index</p>
                        <p class="text-xs font-black text-white italic tracking-widest uppercase">{{ $item->category == 'equipment' ? 'Relic' : 'Common' }}</p>
                    </div>
                </div>

                <!-- Valuation Badge -->
                <div class="mt-8 p-8 bg-teal-900 rounded-[32px] border border-white/5 shadow-2xl relative overflow-hidden group/price">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-gold-400/10 rounded-full blur-2xl group-hover/price:bg-gold-400/20 transition-colors"></div>
                    <p class="text-[10px] font-black text-cyan-400 uppercase tracking-[0.4em] mb-2">Market Valuation</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-4xl font-serif font-black text-white tracking-widest font-mono">{{ number_format($item->price) }}</p>
                        <span class="text-lg font-black text-gold-500 tracking-tighter uppercase italic">SOUL POINTS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- content Info Section -->
        <div class="lg:col-span-3 space-y-8">
            <div class="glass-panel p-10 rounded-[40px] relative overflow-hidden h-full flex flex-col">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
                
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.4em] mb-8 flex items-center gap-4">
                    <span class="w-12 h-[2px] bg-slate-100"></span>
                    Artifact Lore & Directives
                </h3>
                
                <p class="text-2xl text-slate-700 font-serif leading-relaxed italic mb-12 flex-1 relative z-10">
                    <i class="fas fa-quote-left text-teal-100 text-6xl absolute -top-4 -left-6 -z-10"></i>
                    {{ $item->description }}
                </p>

                <div class="grid grid-cols-2 gap-6 relative z-10">
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 group/meta hover:bg-white transition-all">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 group-hover/meta:text-teal-900 transition-colors">Sync Initialization</p>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-microchip text-teal-900 opacity-20 group-hover/meta:opacity-100 transition-opacity"></i>
                            <p class="font-mono text-sm font-bold text-slate-600">{{ $item->created_at->format('Y.m.d H:i') }}</p>
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 group/meta hover:bg-white transition-all">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 group-hover/meta:text-teal-900 transition-colors">Last Modification</p>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-history text-teal-900 opacity-20 group-hover/meta:opacity-100 transition-opacity"></i>
                            <p class="font-mono text-sm font-bold text-slate-600">{{ $item->updated_at->format('Y.m.d H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Status -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center opacity-40">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest font-mono">NODE_STATUS::OPTIMIZED</p>
                    <div class="flex items-center gap-2">
                         <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                         <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Secured Archive</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
