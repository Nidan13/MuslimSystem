@extends('layouts.admin')

@section('title', 'Tambah Misi Baru')

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.quests.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Buat Misi Baru</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Konfigurasi misi dan hadiah untuk hunter</p>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        
        <form action="{{ route('admin.quests.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <!-- Title -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Judul Misi</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-black transition-all placeholder-slate-200 uppercase tracking-tight"
                        placeholder="MISAL: SHOLAT BERJAMAAH DI MASJID">
                </div>

                <!-- Type and Rank Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Klasifikasi Strategis</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                <i class="fas fa-layer-group text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                            </div>
<<<<<<< HEAD
                            <select name="category_id" required 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
=======
                            <select name="quest_type_id" required 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('quest_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
>>>>>>> main
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-8 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Batas Otoritas (Rank)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                <i class="fas fa-shield-halved text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                            </div>
<<<<<<< HEAD
                            <select name="rank_category_id" 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                <option value="">OTORITAS TERBUKA</option>
                                @foreach($rankCategories as $rank)
                                @php $rankSlug = str_replace('-rank', '', $rank->slug); @endphp
                                <option value="{{ $rank->id }}">TIER {{ strtoupper($rankSlug) }} - {{ $rank->name }}</option>
=======
                            <select name="rank_tier_id" 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                <option value="">OTORITAS TERBUKA</option>
                                @foreach($rankTiers as $rank)
                                <option value="{{ $rank->id }}" {{ old('rank_tier_id') == $rank->id ? 'selected' : '' }}>TIER {{ $rank->slug }} - {{ $rank->name }}</option>
>>>>>>> main
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-8 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deskripsi & Instruksi</label>
                    <textarea name="description" rows="4" 
                        class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-medium transition-all placeholder-slate-300 italic leading-relaxed"
                        placeholder="Detail instruksi untuk hunter...">{{ old('description') }}</textarea>
                </div>
                </div>

                <!-- Rewards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-8 bg-slate-50 rounded-[32px] border border-slate-100 group hover:border-blue-300 transition-colors">
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-4 tracking-[0.3em] text-center">Reward EXP</label>
                        <input type="number" name="reward_exp" value="{{ old('reward_exp', 50) }}" 
                            class="w-full bg-white border border-slate-100 rounded-2xl p-6 text-center text-3xl font-mono font-black bg-gradient-to-br from-blue-600 to-slate-900 bg-clip-text text-transparent focus:border-blue-400 focus:outline-none transition-all shadow-sm">
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border border-slate-100 group hover:border-red-300 transition-colors">
                        <label class="block text-[10px] font-black text-red-600 uppercase mb-4 tracking-[0.3em] text-center">Beban Fatigue</label>
                        <input type="number" name="penalty_fatigue" value="{{ old('penalty_fatigue', 5) }}" 
                            class="w-full bg-white border border-slate-100 rounded-2xl p-6 text-center text-3xl font-mono font-black text-teal-900 focus:border-red-400 focus:outline-none transition-all shadow-sm">
                    </div>
                </div>

                <!-- Schedule Section -->
                <div class="p-8 bg-slate-50 rounded-[32px] border border-slate-100 space-y-8">
                    <div class="flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Penjadwalan</h3>
                        <div class="flex bg-white rounded-lg p-1 border border-slate-200">
                            <label class="flex items-center gap-2 px-3 py-1 cursor-pointer rounded-md transition-all has-[:checked]:bg-teal-900 has-[:checked]:text-white">
                                <input type="radio" name="schedule_type" value="specific" checked onchange="toggleScheduleFields()" class="hidden">
                                <span class="text-[9px] font-black uppercase tracking-widest">Spesifik</span>
                            </label>
                            <label class="flex items-center gap-2 px-3 py-1 cursor-pointer rounded-md transition-all has-[:checked]:bg-teal-900 has-[:checked]:text-white">
                                <input type="radio" name="schedule_type" value="recurring" onchange="toggleScheduleFields()" class="hidden">
                                <span class="text-[9px] font-black uppercase tracking-widest">Rutin</span>
                            </label>
                        </div>
                    </div>

                    <div id="specific-fields" class="grid grid-cols-2 gap-6 animate-fadeIn">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Tgl Mulai</label>
                            <input type="datetime-local" name="starts_at" class="w-full bg-white border border-slate-100 rounded-xl p-4 text-xs font-black text-teal-900 focus:border-cyan-400 focus:outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Tgl Berakhir</label>
                            <input type="datetime-local" name="expires_at" class="w-full bg-white border border-slate-100 rounded-xl p-4 text-xs font-black text-red-600 focus:border-red-400 focus:outline-none">
                        </div>
                    </div>

                    <div id="recurring-fields" class="grid grid-cols-2 gap-6 hidden animate-fadeIn">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jam Aktif</label>
                            <input type="time" name="start_time" class="w-full bg-white border border-slate-100 rounded-xl p-4 text-lg font-black text-teal-900 focus:border-cyan-400 focus:outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jam Selesai</label>
                            <input type="time" name="end_time" class="w-full bg-white border border-slate-100 rounded-xl p-4 text-lg font-black text-red-600 focus:border-red-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Parameters (Requirements) -->
                <div class="space-y-6">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Parameter Quest</label>
                        <button type="button" onclick="addRequirement()" class="text-[8px] font-black uppercase tracking-widest px-4 py-2 bg-cyan-50 text-cyan-600 rounded-lg border border-cyan-100 hover:bg-cyan-100 transition-all">
                            + Tambah Parameter
                        </button>
                    </div>
                    <div id="requirements-container" class="space-y-4">
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 animate-fadeIn items-center group">
                            <input type="text" name="req_keys[]" placeholder="CONTOH: TARGET_ID" class="flex-1 bg-white border border-slate-200 rounded-xl px-5 py-3 text-[10px] font-black uppercase tracking-widest focus:border-teal-900 focus:outline-none shadow-inner">
                            <input type="number" name="req_values[]" placeholder="VAL" class="w-24 bg-white border border-slate-200 rounded-xl px-4 py-3 text-center font-black focus:border-teal-900 focus:outline-none shadow-inner">
                            <button type="button" onclick="this.closest('.flex').remove()" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-8 border-t border-slate-50">
                <button type="submit" class="w-full bg-teal-900 hover:bg-teal-800 py-6 rounded-3xl font-serif font-black text-white uppercase tracking-[0.4em] shadow-xl shadow-teal-950/20 transition-all active:scale-[0.98]">
                    Konfirmasi Quest Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleScheduleFields() {
        const type = document.querySelector('input[name="schedule_type"]:checked').value;
        const specific = document.getElementById('specific-fields');
        const recurring = document.getElementById('recurring-fields');
        
        if (type === 'specific') {
            specific.classList.remove('hidden');
            recurring.classList.add('hidden');
        } else {
            specific.classList.add('hidden');
            recurring.classList.remove('hidden');
        }
    }

    function addRequirement() {
        const container = document.getElementById('requirements-container');
        const row = document.createElement('div');
        row.className = 'flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 animate-fadeIn items-center group';
        row.innerHTML = `
            <input type="text" name="req_keys[]" placeholder="CONTOH: TARGET_ID" class="flex-1 bg-white border border-slate-200 rounded-xl px-5 py-3 text-[10px] font-black uppercase tracking-widest focus:border-teal-900 focus:outline-none shadow-inner">
            <input type="number" name="req_values[]" placeholder="VAL" class="w-24 bg-white border border-slate-200 rounded-xl px-4 py-3 text-center font-black focus:border-teal-900 focus:outline-none shadow-inner">
            <button type="button" onclick="this.closest('.flex').remove()" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 transition-colors">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        container.appendChild(row);
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection