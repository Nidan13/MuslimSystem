@extends('layouts.admin')

@section('title', 'Initialize New Video Content')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-panel p-10 rounded-[40px] relative overflow-hidden">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-xl shadow-teal-950/40">
                <i class="fas fa-video text-xl icon-glow"></i>
            </div>
            <div>
                <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wide uppercase">Initialize Content Node</h4>
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.4em] mt-1">Deploying new Islamic media to the central database</p>
            </div>
        </div>

        <form action="{{ route('admin.islamic-videos.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Title -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Content Designation (Title)</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Keajaiban Sedekah Pagi" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 placeholder:text-slate-300 focus:border-cyan-400 outline-none transition-all">
                    @error('title') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Channel -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Origin Channel</label>
                    <input type="text" name="channel" value="{{ old('channel') }}" placeholder="e.g. Kajian Sunnah" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 placeholder:text-slate-300 focus:border-cyan-400 outline-none transition-all">
                    @error('channel') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Video URL -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Media Source Protocol (YouTube Link)</label>
                <div class="relative">
                    <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=..." required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 placeholder:text-slate-300 focus:border-cyan-400 outline-none transition-all">
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 text-cyan-400">
                        <i class="fab fa-youtube text-xl"></i>
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest ml-1 italic">Note: Only standard YouTube URLs are currently supported for auto-extraction.</p>
                @error('video_url') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Duration -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Runtime (Duration)</label>
                    <input type="text" name="duration" value="{{ old('duration') }}" placeholder="e.g. 15:45"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 placeholder:text-slate-300 focus:border-cyan-400 outline-none transition-all text-center font-mono">
                    @error('duration') <p class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Classification (Category)</label>
                    <select name="category" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-sm font-black text-teal-950 focus:border-cyan-400 outline-none transition-all appearance-none cursor-pointer">
                        <option value="General">General/Umum</option>
                        <option value="Kajian">Kajian Intensif</option>
                        <option value="Podcast">Islamic Podcast</option>
                        <option value="Sejarah">Sejarah Islam</option>
                        <option value="Amalan">Panduan Amalan</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Synchronization State</label>
                    <div class="flex items-center gap-4 bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 h-[58px]">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" checked
                            class="w-5 h-5 rounded-md border-2 border-slate-200 text-cyan-400 focus:ring-cyan-400 cursor-pointer">
                        <label for="is_active" class="text-xs font-black text-teal-900 uppercase cursor-pointer">Synchronize Live</label>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex gap-4">
                <button type="submit" class="flex-1 bg-teal-900 text-cyan-400 py-5 rounded-[24px] text-[12px] font-black uppercase tracking-[0.2em] hover:bg-teal-800 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-2xl shadow-teal-950/20">
                    Deploy Content Node
                </button>
                <a href="{{ route('admin.islamic-videos.index') }}" class="px-10 bg-slate-100 text-slate-600 py-5 rounded-[24px] text-[12px] font-black uppercase tracking-[0.2em] hover:bg-slate-200 transition-all">
                    Abort
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
