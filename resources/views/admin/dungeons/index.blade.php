@extends('layouts.admin')

@section('title', 'Gerbang Dungeon')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Gerbang Rift</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse shadow-[0_0_10px_rgba(248,113,113,0.5)]"></span>
            Deteksi Anomali & Protokol Raid Sistem
        </p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 12); @endphp
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(6)" class="row-btn-dg px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 6 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">6</button>
                <button type="button" onclick="setRowLimit(12)" class="row-btn-dg px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 12 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">12</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-dg px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>
        <a href="{{ route('admin.dungeons.create') }}" class="group relative px-6 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-xs font-serif uppercase">
                <i class="fas fa-plus-circle text-cyan-400"></i>
                Buka Protokol Gerbang
            </span>
        </a>
    </div>
</div>

<!-- ... (rank tabs remain the same) ... -->

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Arsip Manifestasi Gerbang Terdeteksi: {{ $dungeons instanceof \Illuminate\Pagination\LengthAwarePaginator ? $dungeons->total() : $dungeons->count() }}
    </div>
    <div class="flex items-center gap-3">
        @if($dungeons instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($dungeons->onFirstPage())
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $dungeons->previousPageUrl() }}" class="w-10 h-10 rounded-xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif

            <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border-2 border-slate-100 shadow-inner">
                <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-200">{{ $dungeons->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">DARI {{ $dungeons->lastPage() }}</span>
            </div>

            @if($dungeons->hasMorePages())
                <a href="{{ $dungeons->nextPageUrl() }}" class="w-10 h-10 rounded-xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        @else
            <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border-2 border-slate-100 shadow-inner">
                 <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-200">1</span>
                 <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
            </div>
        @endif
    </div>
</div>

<script>
    let currentRank = 'all';

    function filterByRank(rank) {
        currentRank = rank;
        
        // Update Tabs UI
        document.querySelectorAll('.rank-tab').forEach(tab => {
            if (tab.innerText.includes(rank === 'all' ? 'ALL' : rank + '-RANK')) {
                tab.className = 'rank-tab active px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-teal-900 text-white shadow-lg shadow-teal-900/20 border-2 border-teal-900';
            } else {
                tab.className = 'rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-cyan-200 hover:text-cyan-500 shadow-sm';
            }
        });

        applyDisplay();
    }

    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function applyDisplay() {
        const cards = document.querySelectorAll('.dungeon-card');

        cards.forEach(card => {
            const rank = card.getAttribute('data-rank');
            const matchesRank = currentRank === 'all' || rank === currentRank;
            
            if (matchesRank) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Initial load
    window.onload = () => applyDisplay();
</script>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    .rank-tab.active {
        transform: translateY(-2px);
    }
</style>
@endsection
