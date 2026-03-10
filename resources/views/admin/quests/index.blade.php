@extends('layouts.admin')

@section('title', 'Arsip Misi (Quest)')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fadeIn">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Arsip Misi</h2>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Kelola tugas harian dan misi khusus sistem</p>
        </div>
        
<<<<<<< HEAD
        </div>
        
        <!-- Category Filtration Tabs -->
        <div class="flex items-center gap-4 mt-4 overflow-x-auto no-scrollbar py-2">
            <a href="{{ route('admin.quests.index') }}" class="px-4 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all {{ !request('category_id') ? 'bg-teal-900 text-white shadow-md' : 'bg-slate-50 text-slate-400 hover:text-teal-900 border border-slate-100' }}">SEMUA PROTOKOL</a>
            @foreach($categories as $cat)
                <a href="{{ route('admin.quests.index', ['category_id' => $cat->id]) }}" class="px-4 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all {{ request('category_id') == $cat->id ? 'text-white' : 'bg-slate-50 text-slate-400 hover:text-teal-900 border border-slate-100' }}" style="{{ request('category_id') == $cat->id ? 'background-color: ' . ($cat->color ?? '#0f4c5c') . '; box-shadow: 0 4px 10px ' . ($cat->color ?? '#0f4c5c') . '40' : '' }}">
                    {{ strtoupper($cat->name) }}
                </a>
            @endforeach
        </div>
=======
        <a href="{{ route('admin.quests.create') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
            <i class="fas fa-plus text-cyan-400 transition-transform group-hover:rotate-90"></i>
            Tambah Misi Baru
        </a>
>>>>>>> origin/main
    </div>

    <!-- Filters & Table -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter Bar -->
        <div class="p-8 border-b border-slate-50 flex flex-wrap items-center justify-between gap-6 bg-slate-50/30">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <button onclick="filterByRank('ALL')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap bg-teal-900 text-white shadow-md active-tab">SEMUA</button>
                <button onclick="filterByRank('E')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white">E-RANK</button>
                <button onclick="filterByRank('D')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white">D-RANK</button>
                <button onclick="filterByRank('C')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white">C-RANK</button>
                <button onclick="filterByRank('B')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white">B-RANK</button>
                <button onclick="filterByRank('A')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white">A-RANK</button>
                <button onclick="filterByRank('S')" class="rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white">S-RANK</button>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Tampilkan:</span>
                <div class="flex bg-white rounded-lg p-1 border border-slate-100">
                    <button onclick="setRowLimit(10)" class="px-3 py-1 text-[9px] font-black rounded-md {{ request('limit', 10) == 10 ? 'bg-teal-900 text-white' : 'text-slate-400' }}">10</button>
                    <button onclick="setRowLimit(25)" class="px-3 py-1 text-[9px] font-black rounded-md {{ request('limit') == 25 ? 'bg-teal-900 text-white' : 'text-slate-400' }}">25</button>
                    <button onclick="setRowLimit(50)" class="px-3 py-1 text-[9px] font-black rounded-md {{ request('limit') == 50 ? 'bg-teal-900 text-white' : 'text-slate-400' }}">50</button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left" id="quest-table">
                <thead>
                    <tr class="bg-slate-50/50 italic border-b border-slate-100">
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">ID</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest cursor-pointer group" onclick="sortTable(1)">
                            Tipe <i class="fas fa-sort ml-1 opacity-20 group-hover:opacity-100" id="sort-icon-1"></i>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest cursor-pointer group" onclick="sortTable(2)">
                            Judul & Misi <i class="fas fa-sort ml-1 opacity-20 group-hover:opacity-100" id="sort-icon-2"></i>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Rank</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right cursor-pointer group" onclick="sortTable(4)">
                            EXP <i class="fas fa-sort ml-1 opacity-20 group-hover:opacity-100" id="sort-icon-4"></i>
                        </th>
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="quest-body">
                    @forelse($quests as $quest)
                    @php
<<<<<<< HEAD
                        $slug = $quest->category->slug ?? 'default';
                        $color = $quest->category->color ?? '#0f4c5c';
                    @endphp
                    <tr class="quest-row group hover:bg-slate-50/50 transition-colors" data-rank="{{ $quest->rankCategory->slug ?? 'OPEN' }}">
                        <td class="py-6 px-4">
                            <span class="text-[10px] font-black text-slate-300 font-mono tracking-tighter uppercase whitespace-nowrap">#QST-{{ str_pad($quest->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-6 px-6">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl border-2 text-[9px] font-black uppercase tracking-widest shadow-sm" style="background-color: {{ $color }}10; color: {{ $color }}; border-color: {{ $color }}30">
                                {{ $quest->category->name ?? 'Protocol' }}
=======
                        $typeColors = [
                            'daily' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                            'hidden' => 'text-indigo-600 bg-indigo-50 border-indigo-100',
                            'special' => 'text-amber-600 bg-amber-50 border-amber-100',
                            'raid' => 'text-rose-600 bg-rose-50 border-rose-100',
                        ];
                        $slugType = $quest->questType->slug ?? 'default';
                        $colorClass = $typeColors[$slugType] ?? 'text-slate-600 bg-slate-50 border-slate-100';
                    @endphp
                    <tr class="quest-row group hover:bg-slate-50/50 transition-colors" data-rank="{{ $quest->rankTier->slug ?? 'OPEN' }}">
                        <td class="py-6 px-8">
                            <span class="text-[10px] font-black text-slate-300 font-mono tracking-tighter">#{{ str_pad($quest->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-6 px-6">
                            <span class="inline-flex px-3 py-1 rounded-lg border {{ $colorClass }} text-[8px] font-black uppercase tracking-widest">
                                {{ $quest->questType->name ?? 'QUEST' }}
>>>>>>> origin/main
                            </span>
                        </td>
                        <td class="py-6 px-6">
                            <div class="max-w-md">
                                <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">
                                    {{ $quest->title }}
                                </h3>
                                <p class="text-[9px] text-slate-400 font-medium italic truncate mt-0.5">
                                    {{ $quest->description }}
                                </p>
                            </div>
                        </td>
                        <td class="py-6 px-6 text-center">
<<<<<<< HEAD
                            @if($quest->rankCategory)
                                @php $rankSlug = str_replace('-rank', '', $quest->rankCategory->slug); @endphp
                                <span class="text-xl font-serif font-black {{ $quest->rankCategory->metadata['color'] ?? 'text-teal-900' }} italic">
                                    {{ strtoupper($rankSlug) }}
=======
                            @if($quest->rankTier)
                                <span class="text-lg font-serif font-black {{ $quest->rankTier->color_code ?? 'text-teal-900' }} italic">
                                    {{ $quest->rankTier->slug }}
>>>>>>> origin/main
                                </span>
                            @else
                                <span class="text-[9px] font-black text-slate-200 uppercase tracking-widest">OPEN</span>
                            @endif
                        </td>
                        <td class="py-6 px-6 text-right">
                             <span class="text-sm font-black bg-gradient-to-br from-blue-600 to-slate-900 bg-clip-text text-transparent font-mono italic">{{ $quest->reward_exp }} EXP</span>
                        </td>
                        <td class="py-6 px-8 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.quests.edit', $quest) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-teal-900 hover:bg-teal-900 hover:text-white rounded-xl transition-all shadow-sm">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.quests.destroy', $quest) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $quest->title }}')" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-red-500 hover:text-white rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-scroll text-5xl mb-3"></i>
                                <span class="text-[10px] font-black uppercase tracking-[0.3em]">Arsip Misi Masih Kosong</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center px-4">
        <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">
            Menampilkan {{ $quests->count() }} dari {{ $quests instanceof \Illuminate\Pagination\LengthAwarePaginator ? $quests->total() : $quests->count() }} Misi
        </div>
        
        @if($quests instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex items-center gap-2">
            @if(!$quests->onFirstPage())
                <a href="{{ $quests->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif

            <div class="px-4 py-2 bg-teal-900 rounded-xl text-white text-[10px] font-black shadow-lg">
                {{ $quests->currentPage() }}
            </div>

            @if($quests->hasMorePages())
                <a href="{{ $quests->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm"><i class="fas fa-chevron-right text-xs"></i></a>
            @endif
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

    function filterByRank(rank) {
        const rows = document.querySelectorAll('.quest-row');
        const tabs = document.querySelectorAll('.rank-tab-quest');
        
        tabs.forEach(tab => {
            if (tab.innerText === rank || (rank === 'ALL' && tab.innerText === 'SEMUA')) {
                tab.className = 'rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap bg-teal-900 text-white shadow-md active-tab';
            } else {
                tab.className = 'rank-tab-quest px-5 py-2 rounded-xl text-[9px] font-black tracking-widest transition-all whitespace-nowrap text-slate-400 hover:text-teal-900 hover:bg-white';
            }
        });

        rows.forEach(row => {
            if (rank === 'ALL' || row.dataset.rank === rank) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    let sortOrders = { 1: 'asc', 2: 'asc', 4: 'asc' };
    function sortTable(columnIndex) {
        const tableBody = document.getElementById('quest-body');
        const rows = Array.from(document.querySelectorAll('.quest-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        // Reset Icons
        document.querySelectorAll('th i').forEach(icon => icon.className = 'fas fa-sort ml-1 opacity-20');
        const activeIcon = document.getElementById(`sort-icon-${columnIndex}`);
        activeIcon.className = `fas fa-sort-${isAsc ? 'up' : 'down'} ml-1 opacity-100 text-cyan-400`;

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            if (columnIndex === 4) { // Price sorting
                valA = parseInt(valA.replace(/[^0-9]/g, '')) || 0;
                valB = parseInt(valB.replace(/[^0-9]/g, '')) || 0;
            }
            
            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';
        rows.forEach(row => tableBody.appendChild(row));
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
