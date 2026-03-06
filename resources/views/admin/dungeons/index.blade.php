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
                Buka Gerbang Rift
            </span>
        </a>
    </div>
</div>

<!-- Classification Tabs -->
<div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-4 -mx-1 px-1">
    <button type="button" onclick="filterByRank('all')" class="rank-tab active px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-teal-900 text-white shadow-lg shadow-teal-900/20 border-2 border-teal-900">
        SEMUA RIFT
    </button>
    <button type="button" onclick="filterByRank('S')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-red-200 hover:text-red-500 shadow-sm">
        S-RANK
    </button>
    <button type="button" onclick="filterByRank('A')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-orange-200 hover:text-orange-500 shadow-sm">
        A-RANK
    </button>
    <button type="button" onclick="filterByRank('B')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-purple-200 hover:text-purple-500 shadow-sm">
        B-RANK
    </button>
    <button type="button" onclick="filterByRank('C')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-blue-200 hover:text-blue-500 shadow-sm">
        C-RANK
    </button>
    <button type="button" onclick="filterByRank('D')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-green-200 hover:text-green-500 shadow-sm">
        D-RANK
    </button>
    <button type="button" onclick="filterByRank('E')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-slate-200 hover:text-slate-500 shadow-sm">
        E-RANK
    </button>
    <button type="button" onclick="filterByRank('OPEN')" class="rank-tab px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] transition-all bg-white text-slate-400 border-2 border-slate-100 hover:border-cyan-200 hover:text-cyan-500 shadow-sm">
        OPEN-RANK
    </button>
</div>

<!-- Manifestation Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-12">
    @forelse($dungeons as $d)
    <div class="dungeon-card group" data-rank="{{ $d->rankTier->slug ?? 'OPEN' }}">
        <div class="glass-panel p-6 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl hover:shadow-2xl transition-all duration-700 relative overflow-hidden h-full flex flex-col justify-between">
            <!-- Rank Glow -->
            @php
                $rankColor = match($d->rankTier->slug ?? 'OPEN') {
                    'S' => 'red',
                    'A' => 'orange',
                    'B' => 'purple',
                    'C' => 'blue',
                    'D' => 'green',
                    'OPEN' => 'cyan',
                    default => 'slate'
                };
            @endphp
            <div class="absolute -right-16 -top-16 w-32 h-32 bg-{{ $rankColor }}-400/10 rounded-full blur-[40px] pointer-events-none group-hover:bg-{{ $rankColor }}-400/20 transition-all"></div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-teal-900 group-hover:bg-teal-900 group-hover:text-white transition-all duration-500">
                        <i class="fas {{ $d->dungeonType->slug == 'raid' ? 'fa-skull-crossbones' : ($d->dungeonType->slug == 'solo' ? 'fa-user-ghost' : 'fa-users-cog') }} text-xl"></i>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="px-4 py-1.5 rounded-lg bg-{{ $rankColor }}-50 text-[10px] font-black text-{{ $rankColor }}-500 uppercase tracking-widest border border-{{ $rankColor }}-100">{{ $d->rankTier->name ?? 'OPEN RANK' }}</span>
                        <span class="text-[8px] font-black text-slate-300 uppercase mt-2 tracking-widest">{{ $d->dungeonType->name }}</span>
                    </div>
                </div>

                <h4 class="text-xl font-serif font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-500 transition-all leading-tight mb-4">
                    {{ $d->name }}
                </h4>
                
                <p class="text-[10px] font-medium text-slate-400 line-clamp-2 uppercase tracking-wide mb-6">
                    {{ $d->description ?: 'Parameter manifestasi tidak terdokumentasi dalam arsip sistem.' }}
                </p>

                <div class="space-y-3 pt-6 border-t border-slate-50">
                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                        <span class="text-slate-300">Level Minimal</span>
                        <span class="text-teal-900">LVL {{ $d->min_level_requirement }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                        <span class="text-slate-300">Reward EXP</span>
                        <span class="text-amber-500">⬆️ {{ number_format($d->reward_exp) }} EXP</span>
                    </div>
                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                        <span class="text-slate-300">Target Misi</span>
                        <span class="text-teal-900">
                            @if($d->objective_type == 'quran') 📖 {{ number_format($d->objective_target) }} Hal
                            @elseif($d->objective_type == 'sholat') 🕌 {{ number_format($d->objective_target) }} Kali
                            @elseif($d->objective_type == 'kajian') 🎧 {{ number_format($d->objective_target) }} Menit
                            @else N/A @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                        <span class="text-slate-300">Kapasitas</span>
                        <span class="text-teal-900">{{ $d->required_players }} PERSONEL</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex gap-2">
                <a href="{{ route('admin.dungeons.edit', $d->id) }}" class="flex-1 py-4 rounded-2xl bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-400 hover:bg-cyan-50 hover:text-cyan-500 border border-slate-100 transition-all text-center">
                    Konfigurasi
                </a>
                <form action="{{ route('admin.dungeons.destroy', $d->id) }}" method="POST" class="inline" onsubmit="return confirm('Tutup gerbang secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-4 rounded-2xl bg-slate-50 text-slate-300 hover:bg-red-50 hover:text-red-500 border border-slate-100 transition-all">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 bg-slate-50 rounded-[50px] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 rounded-full bg-white border border-slate-100 flex items-center justify-center mb-6 shadow-sm">
            <i class="fas fa-satellite-dish text-2xl text-slate-200"></i>
        </div>
        <p class="text-sm font-serif font-black text-slate-300 uppercase tracking-[0.4em]">Gerbang Rift Tidak Terdeteksi</p>
        <p class="text-[9px] font-bold text-slate-400 bg-white px-6 py-2 rounded-full border border-slate-100 mt-4 uppercase">Arsip Manifestasi Kosong</p>
    </div>
    @endforelse
</div>

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
