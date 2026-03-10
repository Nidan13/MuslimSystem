@extends('layouts.admin')

@section('title', 'Buat Headline Baru')

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.headlines.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Buat Headline Baru</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Tambahkan berita atau pengumuman penting sistem</p>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        <form action="{{ route('admin.headlines.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <!-- Title -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Judul Headline</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-black transition-all placeholder-slate-200 uppercase tracking-tight"
                        placeholder="MISAL: UPDATE SISTEM RAMADHAN">
                </div>

                <!-- Grid Tag & Category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tag (Short Label)</label>
                        <input type="text" name="tag" value="{{ old('tag', 'NEWS') }}" required
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all uppercase tracking-widest text-teal-900">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori (Optional)</label>
                        <input type="text" name="category" value="{{ old('category') }}"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all uppercase tracking-widest text-teal-900">
                    </div>
                </div>

                <!-- Image URL -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">URL Gambar (Banner)</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-[10px] font-mono transition-all placeholder-slate-300"
                        placeholder="https://example.com/banner.jpg">
                </div>

                <!-- Content -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Isi Konten (Optional)</label>
                    <textarea name="content" rows="6" 
                        class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-medium transition-all placeholder-slate-300 italic leading-relaxed"
                        placeholder="Tuliskan berita lengkap di sini...">{{ old('content') }}</textarea>
                </div>

                <!-- Status Switch -->
                <div class="flex items-center gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <label class="flex items-center cursor-pointer gap-4">
                        <div class="relative">
                            <input type="checkbox" name="is_active" class="sr-only peer" checked>
                            <div class="w-14 h-8 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-cyan-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-teal-900"></div>
                        </div>
                        <span class="text-[10px] font-black text-teal-900 uppercase tracking-[0.2em]">Aktifkan Segera</span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-8 border-t border-slate-50">
                <button type="submit" class="w-full bg-teal-900 hover:bg-teal-800 py-6 rounded-3xl font-serif font-black text-white uppercase tracking-[0.4em] shadow-xl shadow-teal-950/20 transition-all active:scale-[0.98]">
                    Konfirmasi Headline Baru
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
