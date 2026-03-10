@extends('layouts.admin')

@section('title', 'Tambah Kategori Gerbang Baru')

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-8 mb-12">
        <a href="{{ route('admin.dungeon-types.index') }}" class="w-14 h-14 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 hover:border-teal-200 transition-all shadow-sm active:scale-95 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tight uppercase">Tambah Kategori Baru</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Definisikan Kategori Baru & Kapasitas Hunter
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.dungeon-types.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Name -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-teal-900 uppercase tracking-[0.3em] ml-2 block">Nama Kategori</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center shadow-inner pointer-events-none">
                            <i class="fas fa-tags text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-base font-black transition-all placeholder-slate-300 shadow-inner font-serif uppercase tracking-tight"
                            placeholder="Contoh: Kategori Solo">
                    </div>
                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Slug -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-teal-900 uppercase tracking-[0.3em] ml-2 block">Kode Kategori (Slug)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center shadow-inner pointer-events-none">
                            <i class="fas fa-code text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        </div>
                        <input type="text" name="slug" value="{{ old('slug') }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-base font-black transition-all placeholder-slate-300 shadow-inner font-mono text-cyan-600 uppercase"
                            placeholder="solo">
                    </div>
                    @error('slug') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Max Participants -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-teal-900 uppercase tracking-[0.3em] ml-2 block">Kapasitas Maksimum (Hunter)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <i class="fas fa-users-gear text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        </div>
                        <input type="number" name="max_participants" value="{{ old('max_participants', 1) }}" required
                            class="w-full pl-14 pr-8 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-teal-900 focus:outline-none text-base font-black transition-all placeholder-slate-300 shadow-inner font-mono text-cyan-600"
                            placeholder="Jumlah peserta">
                    </div>
                    @error('max_participants') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-8">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-cyan-500/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <span class="relative flex items-center justify-center gap-4">
                        SIMPAN KATEGORI BARU
                        <i class="fas fa-arrow-right text-cyan-400 icon-glow transition-transform group-hover:translate-x-2"></i>
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
