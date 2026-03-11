@extends('layouts.admin')

@section('title', 'Buat Kampanye Donasi')

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.donations.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Buat Kampanye</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Konfigurasi misi bantuan sosial dan kemanusiaan</p>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        
        <form action="{{ route('admin.donations.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <!-- Title -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Kampanye / Misi Donasi</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-black transition-all placeholder-slate-200 uppercase tracking-tight"
                        placeholder="MISAL: WAKAF MASJID CIREBON">
                </div>

                <!-- Category & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Klasifikasi Donasi</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                <i class="fas fa-tags text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                            </div>
                            <select name="category_id" required 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-8 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-teal-900/40 uppercase tracking-[0.4em] ml-2 block">Status Publikasi</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                <i class="fas fa-toggle-on text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                            </div>
                            <select name="status" required 
                                class="w-full pl-18 pr-12 py-6 bg-slate-50 border-2 border-slate-100 rounded-[28px] focus:bg-white focus:border-teal-900 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer shadow-inner uppercase tracking-[0.2em] text-teal-900">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>PENDING (MENUNGGU)</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>ACTIVE (AKTIF)</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>COMPLETED (SELESAI)</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>REJECTED (DITOLAK)</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-8 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Detail Rincian Kampanye</label>
                    <textarea name="description" rows="5" required
                        class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-medium transition-all placeholder-slate-300 italic leading-relaxed"
                        placeholder="Jelaskan tujuan dan rincian penggunaan dana...">{{ old('description') }}</textarea>
                </div>

                <!-- Financial Configuration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-8 bg-slate-50 rounded-[32px] border border-slate-100">
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-4 tracking-[0.3em] text-center">Target Dana (Rp)</label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-xs font-black text-slate-400">RP</span>
                            </div>
                            <input type="number" name="target_amount" value="{{ old('target_amount', 10000000) }}" required
                                class="w-full bg-white border border-slate-100 rounded-2xl py-6 pl-14 pr-6 text-center text-3xl font-mono font-black text-teal-900 focus:border-blue-400 focus:outline-none transition-all shadow-sm">
                        </div>
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border border-slate-100">
                        <label class="block text-[10px] font-black text-emerald-600 uppercase mb-4 tracking-[0.3em] text-center">Batas Waktu (Opsional)</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" 
                            class="w-full bg-white border border-slate-100 rounded-2xl p-6 text-center text-xl font-black text-teal-900 focus:border-emerald-400 focus:outline-none transition-all shadow-sm">
                    </div>
                </div>

                <!-- Image URL -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">URL Gambar Kampanye (Opsional)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <i class="fas fa-image text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        </div>
                        <input type="url" name="image" value="{{ old('image') }}"
                            class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-medium transition-all placeholder-slate-200"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-8 border-t border-slate-50">
                <button type="submit" class="w-full bg-teal-900 hover:bg-teal-800 py-6 rounded-3xl font-serif font-black text-white uppercase tracking-[0.4em] shadow-xl shadow-teal-950/20 transition-all active:scale-[0.98]">
                    Luncurkan Kampanye
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
