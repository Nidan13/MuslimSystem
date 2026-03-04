@extends('layouts.admin')

@section('title', 'Kalibrasi Matriks Progres')

@section('content')
<div class="max-w-5xl mx-auto animate-fadeIn">
    <!-- Breadcrumb & Title -->
    <div class="flex items-center gap-6 mb-12">
        <a href="{{ route('admin.level-configs.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-slate-400 group-hover:text-cyan-500 transition-colors"></i>
        </a>
        <div>
            <h2 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Kalibrasi Evolusi</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Menyesuaikan Parameter Stage {{ $config->level }}
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.level-configs.update', $config) }}" method="POST" class="space-y-12 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Level (ReadOnly) -->
                    <div class="group opacity-70">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Level Evolusi (Terkunci)</label>
                        <div class="relative">
                            <input type="number" value="{{ $config->level }}" disabled 
                                class="w-full bg-slate-100 border-2 border-slate-200 rounded-[28px] text-teal-900/50 p-7 outline-none font-mono font-black text-2xl shadow-inner cursor-not-allowed">
                            <i class="fas fa-lock absolute right-8 top-1/2 -translate-y-1/2 text-slate-300 text-xl pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Stat Points -->
                    <div class="group">
                        <label class="block text-[10px] font-black text-gold-500 uppercase mb-4 tracking-[0.3em] ml-2 group-focus-within:text-amber-500 transition-colors">Hadiah Poin Atribut (AP)</label>
                        <div class="relative">
                            <input type="number" name="stat_points_reward" value="{{ old('stat_points_reward', $config->stat_points_reward) }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[28px] text-gold-600 p-7 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-2xl shadow-inner group-hover:border-slate-300">
                            <i class="fas fa-plus-circle absolute right-8 top-1/2 -translate-y-1/2 text-slate-200 text-xl pointer-events-none transition-colors group-focus-within:text-gold-400"></i>
                        </div>
                    </div>
                </div>

                <!-- XP Required -->
                <div class="group">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2 group-focus-within:text-cyan-500 transition-colors">XP Dibutuhkan untuk Evolusi</label>
                    <div class="relative">
                        <input type="number" name="xp_required" value="{{ old('xp_required', $config->xp_required) }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[28px] text-cyan-600 p-7 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-2xl shadow-inner group-hover:border-slate-300">
                        <div class="absolute right-8 top-1/2 -translate-y-1/2 flex items-center gap-3">
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">XP GROWTH</span>
                            <i class="fas fa-bolt text-cyan-400 text-xl icon-glow"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-8">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-7 rounded-[30px] font-serif font-black text-white uppercase tracking-[0.4em] text-xs shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.98] border-t border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <span class="relative flex items-center justify-center gap-4">
                        SINKRONISASI MATRIKS EVOLUSI
                        <i class="fas fa-sync-alt text-cyan-400 icon-glow transition-all group-hover:rotate-180 duration-500"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.6)); }
    .glass-panel { backdrop-filter: blur(16px); }
</style>
@endsection
