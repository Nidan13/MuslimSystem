@extends('layouts.admin')

@section('title', 'Kategori Tugas Harian')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Kategori Tugas Harian</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Manajemen Klasifikasi Ritual Harian
            </p>
        </div>
        <a href="{{ route('admin.daily-task-categories.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-serif shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.15em] text-[10px] font-black uppercase">
                <i class="fas fa-plus text-cyan-400 transition-transform group-hover:rotate-90"></i>
                Inisialisasi Kategori
            </span>
        </a>
    </div>

    <div class="glass-panel p-0 rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 italic">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Icon</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Informasi Kategori</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($categories as $category)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center border-2 border-slate-100/50 group-hover:bg-white transition-colors">
                                <i class="{{ $category->icon ?? 'fas fa-clipboard-check' }} text-xl bg-gradient-to-br from-teal-400 to-teal-900 bg-clip-text text-transparent transform group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        </td>
                        <td class="py-6 px-4">
                            <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors duration-300">{{ $category->name }}</h3>
                            @if($category->description)
                            <p class="text-[9px] text-slate-400 font-medium italic truncate mt-1 tracking-wide">{{ Str::limit($category->description, 70) }}</p>
                            @endif
                        </td>
                        <td class="py-6 px-4 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></span>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 text-slate-300 border border-slate-100 text-[9px] font-black uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>Off
                                </span>
                            @endif
                        </td>
                        <td class="py-6 px-4 text-right whitespace-nowrap">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                                <a href="{{ route('admin.daily-task-categories.edit', $category) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-teal-900 hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/edit">
                                    <i class="fas fa-edit text-xs transition-transform group-hover/edit:rotate-12"></i>
                                </a>
                                <form action="{{ route('admin.daily-task-categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $category->name }}')" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-red-400 hover:border-red-400 hover:text-red-600 transition-all shadow-sm active:scale-95 group/del">
                                        <i class="fas fa-trash-alt text-xs transition-transform group-hover/del:scale-110"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 border-2 border-dashed border-slate-100 rounded-full flex items-center justify-center mb-6 text-slate-200">
                                    <i class="fas fa-clipboard-list text-4xl"></i>
                                </div>
                                <p class="text-teal-950 font-serif font-black text-xl italic uppercase tracking-widest">Kategori Kosong</p>
                                <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-[0.5em] font-bold font-sans">Belum ada kategori tugas yang dibuat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
        <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">Total: {{ $categories->total() }}</div>
        <div class="flex items-center gap-3">
            @if($categories->onFirstPage())
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed shadow-inner"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $categories->previousPageUrl() }}" class="w-10 h-10 rounded-xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 transition-all shadow-sm active:scale-95"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $categories->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">DARI {{ $categories->lastPage() }}</span>
            </div>
            @if($categories->hasMorePages())
                <a href="{{ $categories->nextPageUrl() }}" class="w-10 h-10 rounded-xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed shadow-inner"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .glass-panel { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); }
</style>
@endsection
