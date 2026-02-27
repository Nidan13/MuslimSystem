@extends('layouts.admin')

@section('title', 'Manage Islamic Videos')

@section('content')
<div class="glass-panel p-10 rounded-[40px] relative overflow-hidden">
    <div class="flex justify-between items-center mb-10 relative z-10">
        <div>
            <h4 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Video Library <span class="text-cyan-400 font-sans tracking-normal ml-2">Archive</span></h4>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                Managing curated Islamic content for the mobile sector
            </p>
        </div>
        <a href="{{ route('admin.islamic-videos.create') }}" class="px-8 py-3 rounded-2xl bg-teal-900 text-cyan-400 text-[11px] font-black uppercase tracking-widest hover:bg-teal-800 hover:scale-105 active:scale-95 transition-all shadow-xl shadow-teal-950/20">
            <i class="fas fa-plus mr-2"></i> Initialize New Content
        </a>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl text-[11px] font-black uppercase tracking-widest animate-pulse">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Preview</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Title & Channel</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Category</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($videos as $video)
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6">
                        <div class="w-24 h-14 rounded-xl bg-slate-100 overflow-hidden relative border border-slate-200">
                            @if($video->video_id)
                            <img src="https://i.ytimg.com/vi/{{ $video->video_id }}/mqdefault.jpg" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-play-circle text-white/80 text-xl"></i>
                            </div>
                            @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-video-slash"></i>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="py-6">
                        <p class="text-sm font-black text-teal-950 uppercase tracking-tight">{{ $video->title }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">
                            <i class="fas fa-user-circle mr-1 text-cyan-400"></i> {{ $video->channel }}
                        </p>
                    </td>
                    <td class="py-6">
                        <span class="text-[9px] font-black text-teal-800 uppercase tracking-widest bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                            {{ $video->category }}
                        </span>
                    </td>
                    <td class="py-6">
                        @if($video->is_active)
                        <span class="flex items-center gap-2 text-[10px] font-black text-emerald-500 uppercase tracking-tighter">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            Synchronized
                        </span>
                        @else
                        <span class="flex items-center gap-2 text-[10px] font-black text-slate-300 uppercase tracking-tighter">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            Offline
                        </span>
                        @endif
                    </td>
                    <td class="py-6 text-right">
                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.islamic-videos.edit', $video) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-teal-900 hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.islamic-videos.destroy', $video) }}" method="POST" onsubmit="return confirm('Initiate deletion protocol?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-red-400 hover:border-red-400 hover:text-red-600 transition-all shadow-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10">
        {{ $videos->links() }}
    </div>
</div>
@endsection
