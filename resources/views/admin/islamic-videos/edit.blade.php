@extends('layouts.admin')

@section('title', 'Reconfigure Content Node')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-panel p-10 rounded-[40px] relative overflow-hidden">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-xl shadow-teal-950/40">
                <i class="fas fa-edit text-xl icon-glow"></i>
            </div>
            <div>
                <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wide uppercase">Reconfigure Node <span class="text-cyan-400">#{{ $islamicVideo->id }}</span></h4>
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.4em] mt-1">Updating metadata for existing Islamic media</p>
            </div>
        </div>

        @if($islamicVideo->video_id)
        <div class="mb-10 rounded-3xl overflow-hidden border-4 border-slate-50 shadow-2xl relative aspect-video group">
            <img src="https://i.ytimg.com/vi/{{ $islamicVideo->video_id }}/maxresdefault.jpg" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-teal-900/40 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-10 text-center">
                <i class="fab fa-youtube text-5xl mb-4 text-red-500"></i>
                <p class="text-xl font-serif font-black italic">{{ $islamicVideo->title }}</p>
                <p class="text-[10px] font-black uppercase tracking-widest mt-2">Node Preview Protocol Active</p>
            </div>
        </div>
        @endif

        <form action="{{ route('admin.islamic-videos.update', $islamicVideo) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Title -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Content Designation (Title)</label>
                    <input type="text" name="title" value="{{ old('title', $islamicVideo->title) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 focus:border-cyan-400 outline-none transition-all">
                    @error('title') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Channel -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Origin Channel</label>
                    <input type="text" name="channel" value="{{ old('channel', $islamicVideo->channel) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 focus:border-cyan-400 outline-none transition-all">
                    @error('channel') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Video URL -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Media Source Protocol (YouTube Link)</label>
                <div class="relative">
                    <input type="url" name="video_url" value="{{ old('video_url', $islamicVideo->video_url) }}" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 focus:border-cyan-400 outline-none transition-all">
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 text-red-500">
                        <i class="fab fa-youtube text-xl"></i>
                    </div>
                </div>
                @error('video_url') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Duration -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Runtime (Duration)</label>
                    <input type="text" name="duration" value="{{ old('duration', $islamicVideo->duration) }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 focus:border-cyan-400 outline-none transition-all text-center font-mono">
                    @error('duration') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Classification (Category)</label>
                    <select name="category" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 focus:border-cyan-400 outline-none transition-all appearance-none cursor-pointer">
                        <option value="General" {{ $islamicVideo->category == 'General' ? 'selected' : '' }}>General/Umum</option>
                        <option value="Kajian" {{ $islamicVideo->category == 'Kajian' ? 'selected' : '' }}>Kajian Intensif</option>
                        <option value="Podcast" {{ $islamicVideo->category == 'Podcast' ? 'selected' : '' }}>Islamic Podcast</option>
                        <option value="Sejarah" {{ $islamicVideo->category == 'Sejarah' ? 'selected' : '' }}>Sejarah Islam</option>
                        <option value="Amalan" {{ $islamicVideo->category == 'Amalan' ? 'selected' : '' }}>Panduan Amalan</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Synchronization State</label>
                    <div class="flex items-center gap-4 bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 h-[58px]">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" {{ $islamicVideo->is_active ? 'checked' : '' }}
                            class="w-5 h-5 rounded-md border-2 border-slate-200 text-cyan-400 focus:ring-cyan-400 cursor-pointer">
                        <label for="is_active" class="text-xs font-black text-teal-900 uppercase cursor-pointer">Synchronize Live</label>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex gap-4">
                <button type="submit" class="flex-1 bg-teal-900 text-cyan-400 py-5 rounded-[24px] text-[12px] font-black uppercase tracking-[0.2em] hover:bg-teal-800 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-2xl shadow-teal-950/20">
                    Apply Modifications
                </button>
                <a href="{{ route('admin.islamic-videos.index') }}" class="px-10 bg-slate-100 text-slate-600 py-5 rounded-[24px] text-[12px] font-black uppercase tracking-[0.2em] hover:bg-slate-200 transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
