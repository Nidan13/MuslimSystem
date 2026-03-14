@extends('layouts.admin')

@section('title', 'Landing Page Sections')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.landing-page.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-teal-900 shadow-sm hover:border-cyan-400 transition-all active:scale-95">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Landing Page Sections</h2>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Pengaturan Layout dan Urutan Section
                </p>
            </div>
        </div>
        
        <a href="{{ route('admin.landing-page.sections.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-serif shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.15em] text-[10px] font-black uppercase">
                <i class="fas fa-plus text-cyan-400 transition-transform group-hover:rotate-90"></i>
                Tambah Section
            </span>
        </a>
    </div>

    <!-- Table -->
    <div class="glass-panel p-0 rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 italic">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] w-16 text-center">#</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Judul Section</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tipe</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sections as $section)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4 text-center">
                            <span class="text-xs font-black text-teal-900/30 font-serif">#{{ $section->order }}</span>
                        </td>
                        <td class="py-6 px-4">
                            <div class="max-w-md">
                                <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors duration-300">
                                    {{ $section->title }}
                                </h3>
                                @if($section->subtitle)
                                <p class="text-[9px] text-slate-400 font-medium italic truncate mt-1 tracking-wide">
                                    {{ $section->subtitle }}
                                </p>
                                @endif
                            </div>
                        </td>
                        <td class="py-6 px-4">
                            <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-[8px] font-black uppercase tracking-widest">
                                {{ $section->type }}
                            </span>
                        </td>
                        <td class="py-6 px-4 text-center">
                            @if($section->is_active)
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 text-slate-300 border border-slate-100 text-[9px] font-black uppercase tracking-widest">
                                    Mati
                                </span>
                            @endif
                        </td>
                        <td class="py-6 px-4 text-right">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <a href="{{ route('admin.landing-page.sections.edit', $section) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-teal-900 hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.landing-page.sections.destroy', $section) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-red-400 hover:border-red-400 hover:text-red-600 transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <p class="text-slate-400 font-serif italic text-sm">Belum ada section yang diatur.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
