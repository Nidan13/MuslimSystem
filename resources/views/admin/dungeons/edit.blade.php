@extends('layouts.admin')

@section('title', 'Kalibrasi Rift Gate: ' . $dungeon->name)

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.dungeons.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Matriks <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Kalibrasi</span> <span class="text-teal-900 font-serif">Gate</span></h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Menyesuaikan parameter rift: {{ $dungeon->name }}</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        
        <form action="{{ route('admin.dungeons.update', $dungeon->id) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-8">
                <div class="text-center py-5 bg-slate-50 rounded-[24px] border-2 border-slate-100 mb-2">
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-2">Signature Gerbang</div>
                    <div class="text-2xl font-serif font-black text-teal-900 uppercase italic tracking-tighter">Tier {{ str_replace('-rank', '', $dungeon->rankCategory->slug ?? 'Open') }} // {{ $dungeon->name }}</div>
                </div>

                <!-- Identitas -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Penamaan Ulang Protokol (Nama)</label>
                    <input type="text" name="name" value="{{ old('name', $dungeon->name) }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-red-400 focus:outline-none text-xl font-serif font-black text-teal-950 transition-all placeholder-slate-200 uppercase tracking-tight" 
                        placeholder="MANDAT OPERASIONAL AKTIF...">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Tipe & Rank -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Klasifikasi Gate</label>
                        <select name="category_id" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer uppercase tracking-widest text-teal-900">
                            @foreach($dungeonCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $dungeon->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rank Otoritas</label>
                        <select name="rank_category_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer uppercase tracking-widest text-teal-900">
                            <option value="">OPEN-RANK (SEMUA TIER)</option>
                            @foreach($rankCategories as $rank)
                                <option value="{{ $rank->id }}" {{ $dungeon->rank_category_id == $rank->id ? 'selected' : '' }}>TIER {{ strtoupper(str_replace('-rank', '', $rank->slug)) }} - {{ $rank->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level & Capacity -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Minimal Level Hub</label>
                        <input type="number" name="min_level_requirement" value="{{ old('min_level_requirement', $dungeon->min_level_requirement) }}" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-mono font-black text-teal-900 transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kapasitas Personel (Hunter)</label>
                        <input type="number" name="required_players" value="{{ old('required_players', $dungeon->required_players) }}" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-mono font-black text-teal-900 transition-all">
                    </div>
                </div>

                <!-- Mission Parameters -->
                <div class="p-8 bg-slate-50 rounded-[32px] border border-slate-100 space-y-8">
                    <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] italic">Matriks Objektif Raid</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Tipe Objektif</label>
                            <select name="objective_type" required class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-xl focus:border-cyan-400 focus:outline-none text-[11px] font-black transition-all appearance-none cursor-pointer uppercase tracking-widest text-teal-900 shadow-sm">
                                <option value="">-- PILIH TIPE MISI --</option>
                                <option value="quran" {{ (old('objective_type') ?? $dungeon->objective_type) == 'quran' ? 'selected' : '' }}>📖 Tadaruz Qur'an (Halaman)</option>
                                <option value="prayer" {{ (old('objective_type') ?? $dungeon->objective_type) == 'prayer' ? 'selected' : '' }}>🕌 Sholat Berjamaah (Waktu)</option>
                                <option value="kajian" {{ (old('objective_type') ?? $dungeon->objective_type) == 'kajian' ? 'selected' : '' }}>🎧 Kajian Bersama (Menit)</option>
                                <option value="habit" {{ (old('objective_type') ?? $dungeon->objective_type) == 'habit' ? 'selected' : '' }}>🌙 Kebiasaan Baik (Count)</option>
                                <option value="journal" {{ (old('objective_type') ?? $dungeon->objective_type) == 'journal' ? 'selected' : '' }}>📝 Jurnal Harian (Count)</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Target HP Boss (Total)</label>
                            <input type="number" name="objective_target" value="{{ old('objective_target', $dungeon->objective_target) }}" required 
                                class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-xl focus:border-red-400 focus:outline-none text-lg font-mono font-black text-red-600 transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[9px] font-black text-amber-600 uppercase tracking-widest ml-1">Reward EXP Manifestasi</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-amber-500 font-black">⬆️</span>
                            <input type="number" name="reward_exp" value="{{ old('reward_exp', $dungeon->reward_exp) }}" required 
                                class="w-full px-14 py-4 bg-white border border-slate-200 rounded-2xl focus:border-amber-400 focus:outline-none text-xl font-mono font-black text-teal-900 transition-all shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Briefing Dokumentasi (Deskripsi)</label>
                    <textarea name="description" rows="4" 
                        class="w-full p-6 bg-slate-50 border border-slate-100 rounded-3xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-medium transition-all placeholder-slate-200 italic leading-relaxed">{{ old('description', $dungeon->description) }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-8 border-t border-slate-50">
                <button type="submit" class="w-full bg-teal-900 hover:bg-teal-800 py-6 rounded-3xl font-serif font-black text-white uppercase tracking-[0.4em] shadow-xl shadow-teal-950/20 transition-all active:scale-[0.98]">
                    Simpan Kalibrasi Gate
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
