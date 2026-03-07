@extends('layouts.admin')

@section('title', 'Edit Pangkat')

@section('content')
<div class="w-full animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex items-center gap-8 mb-12">
        <a href="{{ route('admin.rank-tiers.index') }}" class="group w-14 h-14 rounded-2xl border-2 border-slate-100 bg-white flex items-center justify-center text-slate-400 hover:text-cyan-500 hover:border-cyan-400/30 transition-all shadow-sm active:scale-90">
            <i class="fas fa-chevron-left text-lg transition-transform group-hover:-translate-x-1"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Edit Pangkat: {{ $rankTier->name }}</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Penyesuaian Parameter Hierarki Hunter
            </p>
        </div>
    </div>

    <!-- Main Configuration Panel -->
    <div class="glass-panel p-1 rounded-[45px] bg-white border-2 border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden relative">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none group-hover:bg-cyan-400/10 transition-colors"></div>
        
        <form action="{{ route('admin.rank-tiers.update', $rankTier) }}" method="POST" class="relative z-10">
            @csrf @method('PUT')
            
            <div class="p-12 space-y-12">
                <!-- Visual Identity Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 p-8 bg-slate-50/50 rounded-[32px] border border-slate-100 shadow-inner">
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Kode Pangkat (Slug)</label>
                        <div class="relative">
                            <input type="text" name="slug" value="{{ $rankTier->slug }}" required 
                                class="w-full bg-white border-2 border-slate-100 rounded-[24px] text-teal-900 p-8 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 outline-none transition-all font-serif font-black text-5xl text-center uppercase shadow-sm">
                            <div class="absolute -top-3 -right-3 w-10 h-10 bg-teal-900 rounded-full flex items-center justify-center text-cyan-400 shadow-lg border-2 border-white">
                                <i class="fas fa-layer-group text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Nama Pangkat</label>
                        <input type="text" name="name" value="{{ $rankTier->name }}" required 
                            class="w-full bg-white border-2 border-slate-100 rounded-[24px] text-teal-950 p-8 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 outline-none transition-all font-serif font-black text-2xl uppercase tracking-tighter shadow-sm">
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest px-4">Nama resmi identitas pangkat</p>
                    </div>
                </div>

                <!-- Parameters Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Syarat Minimum (XP)</label>
                        <div class="relative group">
                            <input type="number" name="min_xp_required" value="{{ $rankTier->min_xp_required }}" required 
                                class="w-full bg-slate-50/50 border-2 border-slate-100 rounded-[24px] text-teal-950 p-7 pl-16 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-2xl shadow-sm">
                            <i class="fas fa-bolt absolute left-7 top-1/2 -translate-y-1/2 text-cyan-500 opacity-30 group-focus-within:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Persyaratan Akses (Level)</label>
                        <div class="relative group">
                            <input type="number" name="min_level_requirement" value="{{ $rankTier->min_level_requirement }}" required 
                                class="w-full bg-slate-50/50 border-2 border-slate-100 rounded-[24px] text-teal-950 p-7 pl-16 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-2xl shadow-sm">
                            <i class="fas fa-layer-group absolute left-7 top-1/2 -translate-y-1/2 text-teal-900 opacity-30 group-focus-within:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                </div>

                <!-- Aesthetics & Logic -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                     <div class="md:col-span-1 space-y-6">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Warna Identitas</label>
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-[24px] border-2 border-slate-100 shadow-inner">
                            <input type="color" name="color_code" value="{{ $rankTier->color_code ?? '#093b48' }}" 
                                class="w-16 h-16 rounded-2xl border-2 border-white cursor-pointer bg-transparent overflow-hidden shadow-md">
                            <div class="flex-1">
                                <span class="block text-[10px] font-black text-teal-950 font-mono tracking-tighter">{{ $rankTier->color_code ?? '#093b48' }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Aksen Visual Pangkat</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-6">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-4 tracking-[0.3em] ml-2">Deskripsi Pangkat</label>
                        <textarea name="description" rows="3" 
                            class="w-full bg-slate-100/50 border-2 border-slate-100 rounded-[24px] text-slate-600 p-6 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium text-sm shadow-inner leading-relaxed">{{ $rankTier->description }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Ratification Section -->
            <div class="p-10 bg-teal-950/5 border-t-2 border-slate-50 flex items-center justify-between">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-sm italic">
                    Perubahan pada identitas ini akan langsung berdampak pada seluruh hunter di pangkat ini.
                </p>
                <button type="submit" class="group relative px-12 py-6 rounded-[24px] bg-teal-900 text-white font-black uppercase tracking-[0.2em] shadow-2xl shadow-teal-950/40 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="relative flex items-center gap-4">
                        SIMPAN PERUBAHAN
                        <i class="fas fa-check-circle text-cyan-400 group-hover:scale-125 transition-transform"></i>
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
</style>
@endsection

