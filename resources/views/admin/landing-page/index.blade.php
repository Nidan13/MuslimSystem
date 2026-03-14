@extends('layouts.admin')

@section('title', 'Landing Page CMS')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Landing Page CMS</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Pusat Kendali Konten Antarmuka Utama
            </p>
        </div>
    </div>

    <!-- Hub Cards -->
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Sections Hub -->
        <a href="{{ route('admin.landing-page.sections.index') }}" class="group relative overflow-hidden bg-white rounded-[40px] border-2 border-slate-50 shadow-xl shadow-slate-200/50 p-10 hover:shadow-2xl hover:shadow-cyan-900/10 transition-all duration-500 transform hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-teal-50 rounded-bl-[80px] -mr-8 -mt-8 opacity-40 group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-teal-900 flex items-center justify-center text-cyan-400 mb-8 shadow-lg shadow-teal-900/20">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
                <h3 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight mb-2">Sections</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-loose">Kelola urutan dan konten section pada landing page (Hero, Features, dll).</p>
                <div class="mt-8 flex items-center justify-between">
                    <span class="text-2xl font-black text-teal-900/20">{{ $sectionCount }}</span>
                    <span class="text-[10px] font-black text-teal-900 group-hover:text-cyan-600 transition-colors uppercase tracking-widest flex items-center gap-2">Kelola <i class="fas fa-arrow-right text-[8px]"></i></span>
                </div>
            </div>
        </a>

        <!-- News Hub -->
        <a href="{{ route('admin.landing-page.news.index') }}" class="group relative overflow-hidden bg-white rounded-[40px] border-2 border-slate-50 shadow-xl shadow-slate-200/50 p-10 hover:shadow-2xl hover:shadow-cyan-900/10 transition-all duration-500 transform hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-50 rounded-bl-[80px] -mr-8 -mt-8 opacity-40 group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-nu-indigo flex items-center justify-center text-nu-teal mb-8 shadow-lg shadow-nu-indigo/20">
                    <i class="fas fa-newspaper text-2xl"></i>
                </div>
                <h3 class="text-xl font-serif font-black text-nu-indigo uppercase tracking-tight mb-2">Master Berita</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-loose">Publikasikan berita, artikel, dan update terbaru untuk pengguna dan Landing Page.</p>
                <div class="mt-8 flex items-center justify-between">
                    <span class="text-2xl font-black text-nu-indigo/20">{{ $newsCount }}</span>
                    <span class="text-[10px] font-black text-nu-indigo group-hover:text-nu-teal transition-colors uppercase tracking-widest flex items-center gap-2">Kelola <i class="fas fa-arrow-right text-[8px]"></i></span>
                </div>
            </div>
        </a>

        <!-- Categories Hub -->
        <a href="{{ route('admin.headline-categories.index') }}" class="group relative overflow-hidden bg-white rounded-[40px] border-2 border-slate-50 shadow-xl shadow-slate-200/50 p-10 hover:shadow-2xl hover:shadow-cyan-900/10 transition-all duration-500 transform hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-bl-[80px] -mr-8 -mt-8 opacity-40 group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-8 shadow-lg shadow-slate-200/20 group-hover:bg-nu-teal group-hover:text-white transition-colors">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <h3 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight mb-2">Master Kategori</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-loose">Data master kategori untuk pengelompokan warta ummah.</p>
                <div class="mt-8 flex items-center justify-between">
                    <span class="text-2xl font-black text-teal-900/20">{{ $categoryCount }}</span>
                    <span class="text-[10px] font-black text-teal-900 group-hover:text-cyan-600 transition-colors uppercase tracking-widest flex items-center gap-2">Kelola <i class="fas fa-arrow-right text-[8px]"></i></span>
                </div>
            </div>
        </a>
    </div>

    <!-- Theme Settings -->
    <div class="p-10 bg-white rounded-[40px] border-2 border-slate-50 shadow-xl shadow-slate-200/50 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-32 -mt-32 opacity-50"></div>
        <div class="relative z-10">
            <h3 class="text-2xl font-serif font-black text-teal-900 uppercase tracking-tight mb-6">Visual Branding Landing Page</h3>
            <form action="{{ route('admin.landing-page.update-theme') }}" method="POST" class="grid md:grid-cols-2 gap-8">
                @csrf
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Primer</label>
                    <div class="flex items-center gap-4">
                        <input type="color" name="primary_color" value="{{ $theme['primary'] }}" class="w-20 h-20 rounded-2xl cursor-pointer border-4 border-slate-100 p-1">
                        <div>
                            <span class="text-sm font-black text-teal-900 block font-mono">{{ $theme['primary'] }}</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase mt-1">Gunakan untuk Action & Highlights</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Navbar</label>
                    <div class="flex items-center gap-4">
                        <input type="color" name="navbar_color" value="{{ $theme['navbar'] }}" class="w-20 h-20 rounded-2xl cursor-pointer border-4 border-slate-100 p-1">
                        <div>
                            <span class="text-sm font-black text-teal-900 block font-mono">{{ $theme['navbar'] }}</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase mt-1">Warna Background Navbar Navigasi</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Footer</label>
                    <div class="flex items-center gap-4">
                        <input type="color" name="footer_color" value="{{ $theme['footer'] }}" class="w-20 h-20 rounded-2xl cursor-pointer border-4 border-slate-100 p-1">
                        <div>
                            <span class="text-sm font-black text-teal-900 block font-mono">{{ $theme['footer'] }}</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase mt-1">Warna Background Footer Bawah</span>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-8 py-4 bg-teal-900 text-cyan-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-800 transition-all shadow-lg shadow-teal-900/20">
                        Simpan Perubahan Tema
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Section -->
    <div class="p-10 bg-nu-indigo rounded-[40px] text-white relative overflow-hidden">
        <div class="absolute right-0 bottom-0 opacity-10 scale-150 rotate-12">
            <i class="fas fa-mosque text-[200px]"></i>
        </div>
        <div class="relative z-10 max-w-2xl">
            <h4 class="text-2xl font-serif font-black uppercase mb-4 tracking-tighter">Manajemen Konten Publik</h4>
            <p class="text-white/60 text-xs font-medium leading-relaxed mb-8 italic">Semua perubahan yang Anda lakukan di sini akan langsung berdampak pada tampilan Landing Page. Pastikan gambar dan narasi yang digunakan tetap menjaga estetika premium Muslim Level Up.</p>
            <div class="flex gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl border border-white/10 text-[8px] font-black uppercase tracking-widest">
                    <i class="fas fa-check-circle text-nu-teal"></i> Real-time Update
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl border border-white/10 text-[8px] font-black uppercase tracking-widest">
                    <i class="fas fa-check-circle text-nu-teal"></i> SEO Optimized
                </div>
            </div>
        </div>
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
