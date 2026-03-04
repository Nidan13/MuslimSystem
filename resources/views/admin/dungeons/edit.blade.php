@extends('layouts.admin')

@section('title', 'Perbaiki Gerbang')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.dungeons.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-serif font-black text-teal-900 tracking-wide uppercase">Rekonfigurasi Gerbang</h1>
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.4em] mt-1 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Stabilisasi Manifestasi: {{ $dungeon->name }}
            </p>
        </div>
    </div>

    <div class="glass-panel p-8 rounded-[32px] relative overflow-hidden bg-white shadow-xl border-2 border-slate-50">
        
        <form action="{{ route('admin.dungeons.update', $dungeon) }}" method="POST" class="space-y-8 relative z-10">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-2 gap-8">
                <div class="col-span-2 text-center py-5 bg-slate-50 rounded-[24px] border-2 border-slate-100 mb-2">
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-2">Signature Gerbang</div>
                    <div class="text-2xl font-serif font-black {{ $dungeon->rankTier->color_code ?? 'text-teal-900' }} uppercase italic tracking-tighter">Tier {{ $dungeon->rankTier->slug ?? '?' }} // {{ $dungeon->name }}</div>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Penamaan Ulang Protokol</label>
                    <input type="text" name="name" value="{{ $dungeon->name }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[20px] text-teal-900 p-5 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-base uppercase shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Klasifikasi Gerbang</label>
                    <select name="dungeon_type_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-bold text-sm shadow-inner">
                        @foreach($dungeonTypes as $type)
                        <option value="{{ $type->id }}" {{ $dungeon->dungeon_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Rentang Otoritas</label>
                    <select name="rank_tier_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-sm shadow-inner">
                        @foreach($rankTiers as $tier)
                        <option value="{{ $tier->id }}" {{ $dungeon->rank_tier_id == $tier->id ? 'selected' : '' }}>TIER {{ $tier->slug }} - {{ $tier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Minimal Level Proxy</label>
                    <input type="number" name="min_level_requirement" value="{{ $dungeon->min_level_requirement }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-sm shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gold-600 uppercase mb-2 tracking-[0.3em] ml-1">Manifestasi Soul Points</label>
                    <div class="relative">
                         <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gold-500 text-lg font-black">★</span>
                        <input type="number" name="reward_soul_points" value="{{ $dungeon->reward_soul_points }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 pl-10 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-sm shadow-inner">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Dokumentasi Teologis (Deskripsi)</label>
                <textarea name="description" rows="4" 
                    class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-slate-700 p-6 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium text-sm shadow-inner">{{ $dungeon->description }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full group relative overflow-hidden bg-cyan-600 hover:bg-cyan-500 py-6 rounded-[20px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-cyan-600/20 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4 text-xs">
                        SINKRONISASI MATRIKS MANIFESTASI
                        <i class="fas fa-sync text-white text-sm"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
