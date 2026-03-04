@extends('layouts.admin')

@section('title', 'Inisialisasi Mandat Baru')

@section('content')
<div class="max-w-5xl mx-auto animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-8 mb-12">
        <a href="{{ route('admin.quests.index') }}" class="w-16 h-16 rounded-3xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 hover:border-teal-200 transition-all shadow-xl shadow-slate-200/50 active:scale-95 group">
            <i class="fas fa-arrow-left text-xl transition-transform group-hover:-translate-x-1"></i>
        </a>
        <div>
            <h1 class="text-5xl font-serif font-black text-teal-900 tracking-tight uppercase">Manifestasi Mandat</h1>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-[0.4em] mt-3 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_12px_#22d3ee]"></span>
                Injeksi Direktif Baru ke Lingkup Sistem
            </p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="glass-panel p-12 rounded-[56px] bg-white/90 backdrop-blur-xl border-2 border-slate-50 shadow-3xl relative overflow-hidden">
        <div class="absolute -right-32 -top-32 w-96 h-96 bg-cyan-400/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -left-32 -bottom-32 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.quests.store') }}" method="POST" class="space-y-12 relative z-10">
            @csrf
            
            <div class="space-y-10">
                <!-- Title Field -->
                <div class="space-y-4">
                    <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Identitas Protokol (Judul)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                            <i class="fas fa-scroll text-slate-300 group-focus-within:text-cyan-500 transition-colors text-lg"></i>
                        </div>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full pl-20 pr-10 py-7 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:bg-white focus:border-teal-900 focus:outline-none text-xl font-black transition-all placeholder-slate-200 shadow-inner font-serif uppercase tracking-wider"
                            placeholder="MANDAT OPERASIONAL AKTIF...">
                    </div>
                </div>

                <!-- Type and Rank Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Klasifikasi Strategis</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                <i class="fas fa-layer-group text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                            </div>
                            <select name="quest_type_id" required 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                @foreach($questTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                            <select name="rank_tier_id" 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                <option value="">OTORITAS TERBUKA</option>
                                @foreach($rankTiers as $tier)
                                <option value="{{ $tier->id }}">TIER {{ $tier->slug }} - {{ $tier->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-8 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                        </div>
                    </div>
                </div>

                <!-- Description Area -->
                <div class="space-y-4">
                    <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Briefing Operasional (Deskripsi)</label>
                    <textarea name="description" rows="5" 
                        class="w-full p-8 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:bg-white focus:border-teal-900 focus:outline-none text-base font-medium transition-all placeholder-slate-200 shadow-inner italic leading-relaxed"
                        placeholder="MASUKKAN DETAIL MANDAT SISTEM...">{{ old('description') }}</textarea>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100 shadow-inner group hover:border-gold-300 transition-colors">
                        <label class="block text-[10px] font-black text-gold-600 uppercase mb-4 tracking-[0.3em] text-center">Energi Jiwa (SP)</label>
                        <input type="number" name="reward_soul_points" value="{{ old('reward_soul_points', 100) }}" 
                            class="w-full bg-white border-2 border-slate-100 rounded-2xl p-5 text-center text-3xl font-mono font-black text-gold-600 focus:border-gold-400 focus:outline-none transition-all shadow-sm">
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100 shadow-inner group hover:border-cyan-300 transition-colors">
                        <label class="block text-[10px] font-black text-cyan-600 uppercase mb-4 tracking-[0.3em] text-center">Faktor Pertumbuhan (EXP)</label>
                        <input type="number" name="reward_exp" value="{{ old('reward_exp', 50) }}" 
                            class="w-full bg-white border-2 border-slate-100 rounded-2xl p-5 text-center text-3xl font-mono font-black text-cyan-600 focus:border-cyan-400 focus:outline-none transition-all shadow-sm">
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100 shadow-inner group hover:border-red-300 transition-colors">
                        <label class="block text-[10px] font-black text-red-600 uppercase mb-4 tracking-[0.3em] text-center">Beban Kelelahan</label>
                        <input type="number" name="penalty_fatigue" value="{{ old('penalty_fatigue', 5) }}" 
                            class="w-full bg-white border-2 border-slate-100 rounded-2xl p-5 text-center text-3xl font-mono font-black text-red-600 focus:border-red-400 focus:outline-none transition-all shadow-sm">
                    </div>
                </div>

                <!-- Time Matrix Selection -->
                <div class="p-10 bg-slate-50 rounded-[48px] border-2 border-slate-100 shadow-inner space-y-10">
                    <div class="flex justify-between items-center px-4">
                        <h3 class="text-[11px] font-black text-teal-900/30 uppercase tracking-[0.5em]">Matriks Sinkronisasi Waktu</h3>
                        <div class="flex gap-10">
                            <label class="flex items-center gap-4 cursor-pointer group">
                                <input type="radio" name="schedule_type" value="specific" checked onchange="toggleScheduleFields()" class="w-6 h-6 text-teal-900 focus:ring-cyan-400 border-slate-200">
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-teal-900 uppercase tracking-widest transition-colors">Node Spesifik</span>
                            </label>
                            <label class="flex items-center gap-4 cursor-pointer group">
                                <input type="radio" name="schedule_type" value="recurring" onchange="toggleScheduleFields()" class="w-6 h-6 text-teal-900 focus:ring-cyan-400 border-slate-200">
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-teal-900 uppercase tracking-widest transition-colors">Siklus Rutin</span>
                            </label>
                        </div>
                    </div>

                    <div id="specific-fields" class="grid grid-cols-2 gap-10 animate-fadeIn">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Inisiasi Node</label>
                            <input type="datetime-local" name="starts_at" class="w-full bg-white border-2 border-slate-100 rounded-[20px] p-5 text-sm font-black text-teal-900 focus:border-cyan-400 focus:outline-none shadow-sm transition-all font-mono uppercase">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Terminasi Node</label>
                            <input type="datetime-local" name="expires_at" class="w-full bg-white border-2 border-slate-100 rounded-[20px] p-5 text-sm font-black text-red-600 focus:border-red-400 focus:outline-none shadow-sm transition-all font-mono uppercase">
                        </div>
                    </div>

                    <div id="recurring-fields" class="grid grid-cols-2 gap-10 hidden animate-fadeIn">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Aktivasi Harian</label>
                            <input type="time" name="start_time" class="w-full bg-white border-2 border-slate-100 rounded-[20px] p-5 text-lg font-black text-teal-900 focus:border-cyan-400 focus:outline-none shadow-sm transition-all font-mono">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deaktivasi Harian</label>
                            <input type="time" name="end_time" class="w-full bg-white border-2 border-slate-100 rounded-[20px] p-5 text-lg font-black text-red-600 focus:border-red-400 focus:outline-none shadow-sm transition-all font-mono">
                        </div>
                    </div>
                </div>

                <!-- Parameters Injection -->
                <div class="space-y-8">
                    <div class="flex justify-between items-center px-4">
                        <label class="text-[11px] font-black text-teal-900/30 uppercase tracking-[0.5em]">Parameter Kritis</label>
                        <button type="button" onclick="addRequirement()" class="px-8 py-3 rounded-2xl bg-cyan-50 text-cyan-600 text-[10px] font-black uppercase tracking-widest hover:bg-cyan-100 transition-all flex items-center gap-3 border-2 border-cyan-100 shadow-sm">
                            <i class="fas fa-plus"></i> Injeksi Parameter
                        </button>
                    </div>
                    <div id="requirements-container" class="space-y-6">
                        <div class="grid grid-cols-12 gap-6 items-center animate-fadeIn p-6 bg-slate-50 rounded-[32px] border-2 border-white shadow-sm group">
                             <div class="col-span-8">
                                <input type="text" name="req_keys[]" placeholder="e.g. TARGET_ID" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-8 py-5 font-black uppercase tracking-widest text-[11px] focus:border-teal-900 focus:outline-none shadow-inner transition-all">
                             </div>
                             <div class="col-span-3">
                                <input type="number" name="req_values[]" placeholder="VAL" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-5 font-black text-center focus:border-teal-900 focus:outline-none shadow-inner transition-all font-mono text-lg">
                             </div>
                             <div class="col-span-1 flex justify-end">
                                <button type="button" onclick="this.closest('.grid').remove()" class="w-14 h-14 rounded-2xl text-slate-200 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="pt-12 border-t-2 border-slate-50">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-8 rounded-[32px] font-serif font-black text-white uppercase tracking-[0.5em] shadow-3xl shadow-teal-950/40 transition-all active:scale-[0.98] border-t border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-cyan-500/20 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <span class="relative flex items-center justify-center gap-6 text-lg">
                        RATIFIKASI MANDAT KE SISTEM
                        <i class="fas fa-microchip text-cyan-400 transition-all group-hover:rotate-180 text-xl"></i>
                    </span>
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
        row.className = 'grid grid-cols-12 gap-6 items-center animate-fadeIn p-6 bg-slate-50 rounded-[32px] border-2 border-white shadow-sm group';
        row.innerHTML = `
            <div class="col-span-8">
                <input type="text" name="req_keys[]" placeholder="e.g. TARGET_ID" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-8 py-5 font-black uppercase tracking-widest text-[11px] focus:border-teal-900 focus:outline-none shadow-inner transition-all">
            </div>
            <div class="col-span-3">
                <input type="number" name="req_values[]" placeholder="VAL" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-5 font-black text-center focus:border-teal-900 focus:outline-none shadow-inner transition-all font-mono text-lg">
            </div>
            <div class="col-span-1 flex justify-end">
                <button type="button" onclick="this.closest('.grid').remove()" class="w-14 h-14 rounded-2xl text-slate-200 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center">
                    <i class="fas fa-trash-alt text-lg"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .shadow-3xl { shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
    .glass-panel { backdrop-filter: blur(20px); }
</style>
@endsection
