@extends('layouts.admin')

@section('title', 'Edit Kategori Kajian')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 animate-fadeIn">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Modifikasi Kategori Kajian</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_#22d3ee]"></span>
                Penyesuaian Parameter Video Islami
            </p>
        </div>
        <a href="{{ route('admin.islamic-video-categories.index') }}" class="text-[10px] font-black text-slate-400 hover:text-teal-900 uppercase tracking-widest transition-colors flex items-center gap-2 pb-1">
            <i class="fas fa-arrow-left"></i> Kembali ke Arsip
        </a>
    </div>

    <!-- Form Panel -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <form action="{{ route('admin.islamic-video-categories.update', $category) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900"
                        placeholder="Misal: Fiqih, Aqidah, Motivasi">
                </div>

                <!-- Icon -->
                <div class="space-y-2 md:col-span-2">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Icon (FontAwesome)</label>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon ?? 'fas fa-book-open') }}"
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900"
                        placeholder="fas fa-play">
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Deskripsi Kategori</label>
                <textarea name="description" rows="4"
                    class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900"
                    placeholder="Jelaskan tujuan kategori kajian ini...">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-4 p-6 bg-slate-50 rounded-3xl border-2 border-slate-100">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-6 after:transition-all peer-checked:bg-teal-900"></div>
                </label>
                <div>
                    <span class="block text-[10px] font-black text-teal-900 uppercase tracking-widest">Status Aktif</span>
                    <span class="text-[9px] text-slate-400 uppercase font-bold">Kategori akan langsung tersedia di menu pemiliihan Kajian</span>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6">
                <button type="submit" class="w-full py-5 rounded-2xl bg-teal-900 text-white font-serif font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-[0.98]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> main
