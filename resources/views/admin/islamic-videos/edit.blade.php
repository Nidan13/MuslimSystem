@extends('layouts.admin')

@section('title', 'Konfigurasi Ulang Konten Video')

@section('content')
<div class="w-full">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.islamic-videos.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Konfigurasi Ulang Node <span class="text-cyan-400">#{{ $islamicVideo->id }}</span></h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Memperbarui Metadata Untuk Media Islami
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>

        @if($islamicVideo->video_id)
        <div class="mb-10 rounded-[32px] overflow-hidden border-4 border-slate-50 shadow-2xl relative aspect-video group">
            <img src="https://i.ytimg.com/vi/{{ $islamicVideo->video_id }}/maxresdefault.jpg" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-teal-900/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center text-white p-10 text-center">
                <i class="fab fa-youtube text-6xl mb-6 text-red-500 drop-shadow-[0_0_15px_rgba(239,68,68,0.5)]"></i>
                <p class="text-2xl font-serif font-black italic tracking-wide">{{ $islamicVideo->title }}</p>
                <p class="text-[10px] font-black uppercase tracking-[0.4em] mt-3 text-cyan-400">Protokol Pratinjau Node Aktif</p>
            </div>
        </div>
        @endif

        <form action="{{ route('admin.islamic-videos.update', $islamicVideo) }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Title -->
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Penandaan Konten (Judul)</label>
                        <input type="text" name="title" value="{{ old('title', $islamicVideo->title) }}" required
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-black text-sm uppercase tracking-wider placeholder-slate-200 shadow-inner">
                        @error('title') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Channel -->
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Kanal Asal</label>
                        <input type="text" name="channel" value="{{ old('channel', $islamicVideo->channel) }}" required
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-black text-sm uppercase tracking-wider placeholder-slate-200 shadow-inner">
                        @error('channel') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Video URL -->
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Protokol Sumber Media (Tautan YouTube)</label>
                    <div class="relative">
                        <input type="url" name="video_url" value="{{ old('video_url', $islamicVideo->video_url) }}" required
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 pr-14 focus:border-cyan-400 focus:bg-white outline-none transition-all text-sm font-black placeholder-slate-200 shadow-inner">
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-cyan-400">
                            <i class="fab fa-youtube text-2xl"></i>
                        </div>
                    </div>
                    @error('video_url') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Duration -->
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Waktu Tayang (Durasi)</label>
                        <input type="text" name="duration" value="{{ old('duration', $islamicVideo->duration) }}"
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all text-center font-mono font-black text-xl shadow-inner">
                        @error('duration') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Klasifikasi (Kategori)</label>
                        <div class="relative">
                            <select name="category" class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-sm uppercase tracking-widest shadow-inner transition-all">
                                <option value="General" {{ $islamicVideo->category == 'General' ? 'selected' : '' }}>Umum</option>
                                <option value="Kajian" {{ $islamicVideo->category == 'Kajian' ? 'selected' : '' }}>Kajian Intensif</option>
                                <option value="Podcast" {{ $islamicVideo->category == 'Podcast' ? 'selected' : '' }}>Siniar Islami</option>
                                <option value="Sejarah" {{ $islamicVideo->category == 'Sejarah' ? 'selected' : '' }}>Sejarah Islam</option>
                                <option value="Amalan" {{ $islamicVideo->category == 'Amalan' ? 'selected' : '' }}>Panduan Amalan</option>
                            </select>
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-cyan-500">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Status Sinkronisasi</label>
                        <div class="flex items-center gap-4 bg-slate-50 border-2 border-slate-200 rounded-[24px] px-6 py-[22px] shadow-inner">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active" {{ $islamicVideo->is_active ? 'checked' : '' }}
                                class="w-6 h-6 rounded-lg border-2 border-slate-300 text-cyan-400 focus:ring-cyan-400 cursor-pointer">
                            <label for="is_active" class="text-[11px] font-black text-teal-900 uppercase cursor-pointer tracking-widest">Langsung Aktif</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 text-center">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        TERAPKAN MODIFIKASI NODE
                        <i class="fas fa-hammer text-cyan-400 icon-glow transition-all group-hover:scale-110"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
