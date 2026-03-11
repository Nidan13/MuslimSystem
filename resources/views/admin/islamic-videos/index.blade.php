@extends('layouts.admin')

@section('title', 'Manajemen Arsip Media')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Arsip Media</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shadow-[0_0_10px_#fbbf24]"></span>
            Pusat Arsip Konten Islami Terkurasi
        </p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 16); @endphp
            <div class="flex gap-1" id="row-limit-container">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-video px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-video px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-video px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.islamic-video-categories.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95 group" title="Kategori Kajian">
                <i class="fas fa-tags text-sm group-hover:scale-110 transition-transform"></i>
            </a>
            <a href="{{ route('admin.islamic-videos.create') }}" class="group relative px-6 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
                <span class="relative flex items-center gap-3 tracking-[0.1em] text-xs font-serif uppercase">
                    <i class="fas fa-video text-amber-400"></i>
                    Inisialisasi Konten
                </span>
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl text-[11px] font-black uppercase tracking-widest animate-pulse">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pratinjau</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Judul & Kanal <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(2)">
                        Kategori <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50" id="video-table-body">
                @forelse($videos as $video)
                <tr class="video-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 pr-4">
                        <div class="w-24 h-14 rounded-xl bg-slate-100 overflow-hidden relative border border-slate-200">
                            @if($video->video_id)
                            <img src="https://i.ytimg.com/vi/{{ $video->video_id }}/mqdefault.jpg" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-play-circle text-white/80 text-xl group-hover:scale-110 transition-transform"></i>
                            </div>
                            @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-video-slash"></i>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="py-6 pr-4">
                        <p class="text-sm font-black text-teal-950 uppercase tracking-tight">{{ $video->title }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">
                            <i class="fas fa-user-circle mr-1 text-cyan-400"></i> {{ $video->channel }}
                        </p>
                    </td>
                    <td class="py-6 pr-4">
                        <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border shadow-sm" style="background-color: {{ ($video->category->color ?? '#f1f5f9') }}10; color: {{ $video->category->color ?? '#64748b' }}; border-color: {{ ($video->category->color ?? '#e2e8f0') }}30">
                            {{ $video->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="py-6 pr-4">
                        @if($video->is_active)
                        <span class="flex items-center gap-2 text-[10px] font-black text-emerald-500 uppercase tracking-tighter">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></span>
                            Tersinkronisasi
                        </span>
                        @else
                        <span class="flex items-center gap-2 text-[10px] font-black text-slate-300 uppercase tracking-tighter">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="py-6 text-right">
                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.islamic-videos.edit', $video) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-teal-900 hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/btn">
                                <i class="fas fa-edit group-hover/btn:rotate-12 transition-transform"></i>
                            </a>
                            <form action="{{ route('admin.islamic-videos.destroy', $video) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this, '{{ $video->title }}')" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-red-400 hover:border-red-400 hover:text-red-600 transition-all shadow-sm active:scale-95 group/del">
                                    <i class="fas fa-trash-alt group-hover/del:animate-bounce"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-slate-50 border-2 border-dashed border-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                <i class="fas fa-video-slash text-3xl"></i>
                            </div>
                            <p class="text-teal-950 font-serif font-black text-lg italic uppercase tracking-widest">Arsip Kosong</p>
                            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-[0.5em] font-bold">Tidak ada media yang diarsipkan saat ini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Total Arsip Konten Terdeteksi: {{ $videos instanceof \Illuminate\Pagination\LengthAwarePaginator ? $videos->total() : $videos->count() }}
    </div>
    <div class="flex items-center gap-3">
        @if($videos instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($videos->onFirstPage())
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $videos->previousPageUrl() }}" class="w-10 h-10 rounded-xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $videos->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">DARI {{ $videos->lastPage() }}</span>
            </div>

            @if($videos->hasMorePages())
                <a href="{{ $videos->nextPageUrl() }}" class="w-10 h-10 rounded-xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        @else
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">1</span>
                <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
            </div>
        @endif
    </div>
</div>

<script>
    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    let sortOrders = { 1: 'asc', 2: 'asc' }; // 1=Judul, 2=Kategori

    function applyDisplay() {
        // Obsolete
    }

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('video-table-body');
        const rows = Array.from(document.querySelectorAll('.video-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        // Toggle sort order
        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';

        // Re-append sorted rows
        rows.forEach(row => tableBody.appendChild(row));
        
        applyDisplay();
    }

    // Initial load
    window.onload = () => {};
</script>
@endsection