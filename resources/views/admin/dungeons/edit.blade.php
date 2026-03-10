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
                    <div class="text-2xl font-serif font-black {{ $dungeon->rankCategory->color ?? 'text-teal-900' }} uppercase italic tracking-tighter">Tier {{ str_replace('-rank', '', $dungeon->rankCategory->slug ?? 'Open') }} // {{ $dungeon->name }}</div>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Penamaan Ulang Protokol</label>
                    <input type="text" name="name" value="{{ $dungeon->name }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[20px] text-teal-900 p-5 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-base uppercase shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Klasifikasi Gerbang</label>
                    <select name="category_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-bold text-sm shadow-inner">
                        @foreach($dungeonCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $dungeon->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Tingkat Otoritas</label>
                    <select name="rank_category_id" class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-sm shadow-inner">
                        <option value="">-- SEMUA RANK (OPEN) --</option>
                        @foreach($rankCategories as $rank)
                            <option value="{{ $rank->id }}" {{ $dungeon->rank_category_id == $rank->id ? 'selected' : '' }}>TIER {{ str_replace('-rank', '', $rank->slug) }} - {{ $rank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Minimal Level Hunter</label>
                    <input type="number" name="min_level_requirement" value="{{ $dungeon->min_level_requirement }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-sm shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Kapasitas Raid (Personel)</label>
                    <input type="number" name="required_players" value="{{ $dungeon->required_players ?? 1 }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-sm shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Tipe Objektif (Misi Raid)</label>
                    <div class="relative group">
                        <select name="objective_type" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-sm transition-all shadow-inner">
                            <option value="">-- PILIH TIPE MISI --</option>
                            <option value="quran" {{ (old('objective_type') ?? $dungeon->objective_type) == 'quran' ? 'selected' : '' }}>📖 Tadaruz Qur'an (Halaman)</option>
                            <option value="prayer" {{ (old('objective_type') ?? $dungeon->objective_type) == 'prayer' ? 'selected' : '' }}>🕌 Sholat Berjamaah (Waktu)</option>
                            <option value="kajian" {{ (old('objective_type') ?? $dungeon->objective_type) == 'kajian' ? 'selected' : '' }}>🎧 Kajian Bersama (Menit)</option>
                            <option value="habit" {{ (old('objective_type') ?? $dungeon->objective_type) == 'habit' ? 'selected' : '' }}>🌙 Kebiasaan Baik (Count)</option>
                            <option value="journal" {{ (old('objective_type') ?? $dungeon->objective_type) == 'journal' ? 'selected' : '' }}>📝 Jurnal Harian (Count)</option>
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-teal-500">
                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Target Objektif (Total HP Boss)</label>
                    <input type="number" name="objective_target" value="{{ old('objective_target') ?? $dungeon->objective_target }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-sm shadow-inner" placeholder="Contoh: 500">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-amber-500 uppercase mb-2 tracking-[0.3em] ml-1">Manifestasi EXP</label>
                    <div class="relative">
                         <span class="absolute left-5 top-1/2 -translate-y-1/2 text-amber-400 text-lg font-black">⬆️</span>
                        <input type="number" name="reward_exp" value="{{ $dungeon->reward_exp }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[16px] text-teal-900 p-4 pl-12 focus:border-amber-400 focus:bg-white outline-none transition-all font-mono font-black text-sm shadow-inner">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-2 tracking-[0.3em] ml-1">Deskripsi Misi</label>
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
