@extends('layouts.admin')

@section('title', 'Manajemen Rift Gates')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fadeIn">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Rift <span class="text-red-500 font-sans tracking-normal not-italic mx-1">Gates</span> <span class="text-teal-900 font-serif">Registry</span></h2>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse shadow-[0_0_10px_rgba(248,113,113,0.5)]"></span>
                Manajemen Dungeon & Raid Aktif dalam Sistem
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dungeon-types.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95 group" title="Tipe Gerbang">
                <i class="fas fa-tags text-sm group-hover:scale-110 transition-transform"></i>
            </a>
            <a href="{{ route('admin.dungeons.create') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
                <i class="fas fa-plus text-cyan-400 transition-transform group-hover:rotate-90"></i>
                Buka Rift Gate Baru
            </a>
        </div>
    </div>

    <!-- Stats & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-3 animate-fadeIn">
        <button onclick="filterByRank('all')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-teal-900 text-white shadow-md active-tab uppercase">Semua Gate</button>
        <button onclick="filterByRank('S')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-red-400 hover:text-red-500 uppercase">S-Rank</button>
        <button onclick="filterByRank('A')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-orange-400 hover:text-orange-500 uppercase">A-Rank</button>
        <button onclick="filterByRank('B')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-purple-400 hover:text-purple-500 uppercase">B-Rank</button>
        <button onclick="filterByRank('C')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-blue-400 hover:text-blue-500 uppercase">C-Rank</button>
        <button onclick="filterByRank('D')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-green-400 hover:text-green-500 uppercase">D-Rank</button>
        <button onclick="filterByRank('E')" class="rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-slate-400 hover:text-slate-600 uppercase">E-Rank</button>
    </div>

    <!-- Manifestation Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mt-12" id="rift-container">
        @forelse($dungeons as $d)
        @php
            $rankSlug = $d->rankTier->slug ?? 'OPEN';
            $rankColor = match(strtoupper($rankSlug)) {
                'S' => 'red',
                'A' => 'orange',
                'B' => 'purple',
                'C' => 'blue',
                'D' => 'green',
                'E' => 'slate',
                'OPEN' => 'cyan',
                default => 'slate'
            };
        @endphp
        <div class="dungeon-card rift-card group h-full" data-rank="{{ $rankSlug }}">
            <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl hover:shadow-2xl transition-all duration-700 h-full flex flex-col relative overflow-hidden">
                <!-- Rank Glow -->
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-{{ $rankColor }}-400/10 rounded-full blur-[40px] pointer-events-none group-hover:bg-{{ $rankColor }}-400/20 transition-all"></div>
                
                <!-- Category/ID Icon Row -->
                <div class="flex justify-between items-start mb-6 shrink-0">
                    <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform">
                        <i class="fas {{ ($d->dungeonType->slug ?? '') == 'raid' ? 'fa-skull-crossbones' : 'fa-dungeon' }} text-lg"></i>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-lg bg-{{ $rankColor }}-50 text-{{ $rankColor }}-600 border border-{{ $rankColor }}-100 text-[10px] font-black uppercase tracking-widest italic group-hover:bg-{{ $rankColor }}-500 group-hover:text-white transition-colors">
                            {{ $d->rankTier->name ?? 'RANK ' . $rankSlug }}
                        </span>
                        <p class="text-[8px] font-black text-slate-300 mt-2 uppercase tracking-widest">GATE #{{ str_pad($d->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <!-- Title & Body -->
                <div class="flex-1">
                    <h4 class="text-lg font-serif font-black text-teal-950 uppercase tracking-tight leading-tight group-hover:text-red-500 transition-colors mb-3">
                        {{ $d->name }}
                    </h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide line-clamp-2 italic mb-6">
                        {{ $d->description ?: 'Parameter manifestasi tidak terdokumentasi dalam arsip sistem.' }}
                    </p>

                    <!-- Stats Table -->
                    <div class="bg-slate-50/50 rounded-3xl p-5 space-y-3 mb-6 border border-slate-100 group-hover:bg-white transition-colors">
                        <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                            <span class="text-slate-300 italic">Level Reqs</span>
                            <span class="text-teal-900">LVL {{ $d->min_level_requirement }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                            <span class="text-slate-300 italic">HP / Target</span>
                            <span class="text-red-500">
                                @if($d->objective_type == 'quran') 📖 {{ number_format($d->objective_target) }} Hal
                                @elseif($d->objective_type == 'sholat') 🕌 {{ number_format($d->objective_target) }} Kali
                                @elseif($d->objective_type == 'kajian') 🎧 {{ number_format($d->objective_target) }} Menit
                                @else 🌀 {{ number_format($d->objective_target) }} @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                            <span class="text-slate-300 italic">Reward EXP</span>
                            <span class="text-teal-900">+{{ number_format($d->reward_exp) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                            <span class="text-slate-300 italic">Personel</span>
                            <span class="text-teal-900">{{ $d->required_players }} HUNTER</span>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex gap-2 pt-2 shrink-0">
                    <a href="{{ route('admin.dungeons.edit', $d->id) }}" class="flex-1 bg-white border border-slate-100 text-teal-900 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-teal-900 hover:text-white transition-all text-center shadow-sm">
                        Edit Gate
                    </a>
                    <form action="{{ route('admin.dungeons.destroy', $d->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete(this, '{{ $d->title ?? $d->name ?? 'Misi #'.$d->id }}')" class="w-11 h-11 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-red-500 hover:text-white rounded-xl transition-all shadow-sm">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center italic">
            <i class="fas fa-satellite-dish text-4xl text-slate-200 mb-4 animate-pulse"></i>
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Gerbang Rift Tidak Terdeteksi</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($dungeons instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-8 flex justify-between items-center px-4">
        <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic leading-none">
            Registry Rift: {{ $dungeons->total() }} Gate Aktif
        </div>
        <div class="flex items-center gap-2">
            @if(!$dungeons->onFirstPage())
                <a href="{{ $dungeons->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            <div class="px-4 py-2 bg-teal-900 rounded-xl text-white text-[10px] font-black shadow-lg italic">
                P{{ $dungeons->currentPage() }}
            </div>
            @if($dungeons->hasMorePages())
                <a href="{{ $dungeons->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm"><i class="fas fa-chevron-right text-xs"></i></a>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
    let currentRank = 'all';

    function filterByRank(rank) {
        currentRank = rank;
        const tabs = document.querySelectorAll('.rank-tab-rift');
        const cards = document.querySelectorAll('.rift-card');
        
        tabs.forEach(tab => {
            const tabText = tab.innerText.toLowerCase();
            const targetText = rank === 'all' ? 'semua gate' : rank.toLowerCase() + '-rank';
            
            if (tabText === targetText) {
                tab.className = 'rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-teal-900 text-white shadow-md active-tab uppercase';
            } else {
                tab.className = 'rank-tab-rift px-4 py-3 rounded-xl text-[9px] font-black tracking-widest transition-all bg-white text-slate-400 border border-slate-100 hover:border-teal-900 hover:text-teal-900 uppercase';
            }
        });

        cards.forEach(card => {
            const cardRank = card.getAttribute('data-rank');
            if (rank === 'all' || cardRank === rank) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
</style>
@endsection