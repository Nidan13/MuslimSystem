@extends('layouts.admin')

@section('title', 'Kalibrasi Protokol Sholat')

@section('content')
<div class="w-full animate-fadeIn pb-20">
    <!-- Header -->
    <div class="flex items-center gap-8 mb-12">
        <a href="{{ route('admin.prayers.index') }}" class="w-14 h-14 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-slate-400 hover:text-teal-900 hover:border-cyan-200 transition-all shadow-sm active:scale-95 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tight uppercase italic drop-shadow-sm">Kalibrasi <span class="text-cyan-500 font-sans tracking-normal not-italic">Protokol</span></h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Modifikasi Parameter Node Spiritual: {{ $prayer->name }}
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-60 h-60 bg-teal-900/5 rounded-full blur-[80px] pointer-events-none"></div>
        
        <form action="{{ route('admin.prayers.update', $prayer) }}" method="POST" class="space-y-12 relative z-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Name -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.3em] ml-2 block italic">Identitas Protokol</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <i class="fas fa-pray text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name', $prayer->name) }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-base font-black transition-all placeholder-slate-200 shadow-inner font-serif uppercase tracking-tight text-teal-900"
                            placeholder="NAMA PROTOKOL">
                    </div>
                </div>

                <!-- Experience Nodes (EXP) -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-blue-600/40 uppercase tracking-[0.3em] ml-2 block italic">Yield Experience (EXP)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <i class="fas fa-bolt text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="number" name="soul_points" value="{{ old('soul_points', $prayer->soul_points) }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-blue-500 focus:outline-none text-xl font-black transition-all placeholder-slate-200 shadow-inner font-mono bg-gradient-to-br from-blue-600 to-slate-950 bg-clip-text text-transparent"
                            placeholder="0">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-4">
                <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.3em] ml-2 block italic">Direktif & Esensi Spiritual</label>
                <div class="relative group">
                    <div class="absolute top-6 left-6 pointer-events-none">
                        <i class="fas fa-quote-left text-slate-200 group-focus-within:text-cyan-400 transition-colors"></i>
                    </div>
                    <textarea name="description" rows="4" required
                        class="w-full pl-14 pr-8 py-6 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-medium transition-all placeholder-slate-300 shadow-inner leading-relaxed resize-none italic text-slate-500"
                        placeholder="Deskripsikan filosofi protokol ini...">{{ old('description', $prayer->description) }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-8">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[28px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-cyan-500/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <span class="relative flex items-center justify-center gap-4">
                        Sinkronisasi Node
                        <i class="fas fa-sync-alt text-cyan-400 icon-glow transition-transform group-hover:rotate-180"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endsection
