@extends('layouts.admin')

@section('title', 'Manajemen Hunter')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fadeIn">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Daftar Hunter</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Manajemen Seluruh Pengguna & Otoritas Sistem
            </p>
        </div>
        
        <a href="{{ route('admin.hunters.create') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
            <i class="fas fa-user-plus text-cyan-400 transition-transform group-hover:scale-110"></i>
            Daftarkan Hunter Baru
        </a>
    </div>

    <!-- Main Table Container -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter/Utility Bar -->
        <div class="p-8 border-b border-slate-50 flex flex-wrap items-center justify-between gap-6 bg-slate-50/30">
            <div class="flex items-center gap-4">
                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Urutkan:</span>
                <div class="flex bg-white rounded-lg p-1 border border-slate-100">
                    <button onclick="sortTable(1)" class="px-4 py-1.5 text-[9px] font-black rounded-md text-slate-400 hover:text-teal-900 transition-colors uppercase">Username</button>
                    <button onclick="sortTable(2)" class="px-4 py-1.5 text-[9px] font-black rounded-md text-slate-400 hover:text-teal-900 transition-colors uppercase">Rank</button>
                    <button onclick="sortTable(3)" class="px-4 py-1.5 text-[9px] font-black rounded-md text-slate-400 hover:text-teal-900 transition-colors uppercase">Level</button>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Limit Page:</span>
                <select onchange="setRowLimit(this.value)" class="bg-white border border-slate-100 rounded-lg px-3 py-1 text-[9px] font-black text-teal-900 focus:outline-none focus:border-cyan-400">
                    <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left" id="hunter-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">Identitas</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Pangkat</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest">Progress & Level</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Saldo (IDR)</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="hunter-table-body">
                    @foreach($users as $user)
                    <tr class="hunter-row group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-2xl border-2 border-slate-100 shadow-sm group-hover:scale-110 transition-transform" alt="Avatar">
                                <div>
                                    <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                                        {{ $user->username }}
                                    </h3>
                                    <p class="text-[9px] font-medium text-slate-400 lowercase mt-1.5">{{ $user->email }}</p>
                                    <p class="text-[8px] font-black text-slate-200 uppercase tracking-tighter mt-0.5 font-mono">ID: SN-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6 text-center">
                            @if($user->rankTier)
                                <span class="text-2xl font-serif font-black italic {{ $user->rankTier->color_code ? '' : 'text-teal-900' }}" style="{{ $user->rankTier->color_code ? 'color: ' . $user->rankTier->color_code : '' }}">
                                    {{ $user->rankTier->slug }}
                                </span>
                            @else
                                <span class="text-xl font-serif font-black text-slate-200">?</span>
                            @endif
                        </td>
                        <td class="py-6 px-6">
                            <div class="max-w-[140px] space-y-2">
                                <div class="flex justify-between items-end">
                                    <span class="text-[9px] font-black text-teal-900 uppercase">LVL {{ $user->level }}</span>
                                    <span class="text-[8px] font-black text-slate-300 uppercase italic">{{ number_format($user->current_exp) }} XP</span>
                                </div>
                                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200 shadow-inner">
                                    @php 
                                        $nextExp = $user->next_level_exp ?: 1000;
                                        $progress = min(($user->current_exp / $nextExp) * 100, 100);
                                    @endphp
                                    <div class="h-full bg-gradient-to-r from-teal-900 via-cyan-500 to-teal-900 bg-[length:200%_auto] animate-gradientMove transition-all duration-1000" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6 text-right">
                            <span class="text-xs font-black text-teal-900 font-mono tracking-tighter">Rp{{ number_format($user->balance ?? 0) }}</span>
                        </td>
                        <td class="py-6 px-6 text-center">
                            @if($user->is_active)
                                <span class="inline-flex px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 text-[8px] font-black uppercase tracking-widest">AKTIF</span>
                            @else
                                <span class="inline-flex px-3 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[8px] font-black uppercase tracking-widest">NONAKTIF</span>
                            @endif
                        </td>
                        <td class="py-6 px-8 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.hunters.show', $user) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-teal-900 hover:bg-teal-900 hover:text-white rounded-xl transition-all shadow-sm">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.hunters.destroy', $user) }}" method="POST" class="inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $user->username }}')" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-red-500 hover:text-white rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 px-4">
        <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic leading-none text-center md:text-left">
            Total Populasi Hunter: {{ $users instanceof \Illuminate\Pagination\LengthAwarePaginator ? $users->total() : $users->count() }}
        </div>
        
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex items-center gap-3">
            @if(!$users->onFirstPage())
                <a href="{{ $users->previousPageUrl() }}" class="px-5 py-2.5 bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm text-[9px] font-black uppercase italic tracking-widest">Prev</a>
            @endif

            <div class="px-5 py-2.5 bg-teal-900 rounded-xl text-white text-[9px] font-black shadow-lg italic">
                P{{ $users->currentPage() }}
            </div>

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="px-5 py-2.5 bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm text-[9px] font-black uppercase italic tracking-widest">Next</a>
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

    let sortOrders = { 1: 'asc', 2: 'asc', 3: 'asc' };
    function sortTable(columnIndex) {
        const tableBody = document.getElementById('hunter-table-body');
        const rows = Array.from(document.querySelectorAll('.hunter-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            if (columnIndex === 3) { // Level/XP parsing
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
    .animate-gradientMove {
        background-size: 200% auto;
        animation: gradientMove 3s linear infinite;
    }
    @keyframes gradientMove {
        to { background-position: 200% center; }
    }
</style>
@endsection
