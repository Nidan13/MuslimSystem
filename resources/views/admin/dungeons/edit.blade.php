@extends('layouts.admin')

@section('title', 'Refine Gate')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.dungeons.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Reconfigure Gate</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em]">Stabilizing Manifestation: {{ $dungeon->name }}</p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        
        <form action="{{ route('admin.dungeons.update', $dungeon) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-2 gap-10">
                <div class="col-span-2 text-center py-6 bg-slate-50 rounded-[28px] border-2 border-slate-100 mb-4">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em] mb-2">Gate Signature</div>
                    <div class="text-3xl font-serif font-black {{ $dungeon->rankTier->color_code ?? 'text-teal-900' }} uppercase italic tracking-tighter">Tier {{ $dungeon->rankTier->slug ?? '?' }} // {{ $dungeon->name }}</div>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Rename Protocol</label>
                    <input type="text" name="name" value="{{ $dungeon->name }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Gate Classification</label>
                    <select name="dungeon_type_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-bold shadow-inner">
                        @foreach($dungeonTypes as $type)
                        <option value="{{ $type->id }}" {{ $dungeon->dungeon_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Authorization Range</label>
                    <select name="rank_tier_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black shadow-inner">
                        @foreach($rankTiers as $tier)
                        <option value="{{ $tier->id }}" {{ $dungeon->rank_tier_id == $tier->id ? 'selected' : '' }}>TIER {{ $tier->slug }} - {{ $tier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Min Level Proxy</label>
                    <input type="number" name="min_level_requirement" value="{{ $dungeon->min_level_requirement }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-lg shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gold-600 uppercase mb-3 tracking-[0.3em] ml-1">Soul Points manifest</label>
                    <div class="relative">
                         <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gold-500 text-xl font-black">★</span>
                        <input type="number" name="reward_soul_points" value="{{ $dungeon->reward_soul_points }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 pl-12 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-lg shadow-inner">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Theological Documentation</label>
                <textarea name="description" rows="5" 
                    class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium shadow-inner">{{ $dungeon->description }}</textarea>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-cyan-600 hover:bg-cyan-500 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-cyan-600/20 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        SYNC MANIFESTATION MATRIX
                        <svg class="w-6 h-6 text-white icon-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
