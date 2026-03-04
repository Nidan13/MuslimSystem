@extends('layouts.admin')

@section('title', 'Sinkronisasi Protokol Sholat')

@section('content')
<div class="max-w-4xl mx-auto animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-8 mb-12">
        <a href="{{ route('admin.prayers.index') }}" class="w-14 h-14 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 hover:border-teal-200 transition-all shadow-sm active:scale-95 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tight uppercase">Perbarui Protokol</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target: Protokol {{ $prayer->name }} Parameters
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.prayers.update', $prayer) }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Name -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-teal-900 uppercase tracking-[0.3em] ml-2 block">Nama Ibadah</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none shadow-inner">
                            <i class="fas fa-pray text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name', $prayer->name) }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-base font-black transition-all placeholder-slate-300 shadow-inner font-serif uppercase tracking-tight"
                            placeholder="Contoh: Subuh">
                    </div>
                </div>

                <!-- Soul Points -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gold-600 uppercase tracking-[0.3em] ml-2 block">Pahala SP (Soul Points)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <i class="fas fa-gem text-slate-300 group-focus-within:text-gold-500 transition-colors"></i>
                        </div>
                        <input type="number" name="soul_points" value="{{ old('soul_points', $prayer->soul_points) }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-base font-black transition-all placeholder-slate-300 shadow-inner font-mono text-gold-600"
                            placeholder="Jumlah perolehan Soul Points">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-4">
                <label class="text-[10px] font-black text-teal-900 uppercase tracking-[0.3em] ml-2 block">Direktif & Filosofi Protokol</label>
                <div class="relative group">
                    <textarea name="description" rows="4" required
                        class="w-full px-8 py-6 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-medium transition-all placeholder-slate-300 shadow-inner leading-relaxed resize-none italic text-slate-500"
                        placeholder="Jelaskan esensi dari protokol ibadah ini...">{{ old('description', $prayer->description) }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-8">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-cyan-500/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <span class="relative flex items-center justify-center gap-4">
                        SINKRONKAN PROTOKOL
                        <i class="fas fa-sync text-cyan-400 icon-glow transition-transform group-hover:rotate-180"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endsection
