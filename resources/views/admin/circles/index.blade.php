@extends('layouts.admin')

@section('title', 'Manajemen Lingkaran (Circles)')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Registri <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Lingkaran</span> <span class="text-teal-900 font-serif">Sistem</span></h2>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Otoritas Kolektif & Matriks Persaudaraan Hunter
            </p>
        </div>
        
        <a href="{{ route('admin.circles.create') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
            <i class="fas fa-plus-circle text-cyan-400 transition-transform group-hover:rotate-90"></i>
            Bentuk Lingkaran Baru
        </a>
    </div>

    <!-- Main Table Container -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter/Utility Bar -->
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex flex-wrap justify-between items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                </div>
                <input type="text" id="circleSearch" placeholder="Cari nama lingkaran atau ID node..." 
                    class="block w-80 pl-12 pr-4 py-3 bg-white border-2 border-slate-100 rounded-2xl text-[11px] font-black text-teal-900 uppercase tracking-widest placeholder-slate-300 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 transition-all outline-none">
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2 italic">Tampilkan:</span>
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    @foreach([8, 16, 32] as $lim)
                    <button onclick="setLimit({{ $lim }})" class="px-4 py-1.5 rounded-lg text-[10px] font-black transition-all {{ request('limit', 16) == $lim ? 'bg-teal-900 text-white shadow-md' : 'text-slate-400 hover:text-teal-900' }}">
                        {{ $lim }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400">
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(0)">
                            Identitas Node <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            Otoritas Leader <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(2)">
                            Keanggotaan <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(3)">
                            Level Node <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Opsi Sinkron</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="circleTableBody">
                    @forelse($circles as $circle)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-[24px] bg-teal-900 border-2 border-slate-100 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-900/20 group-hover:scale-105 transition-transform overflow-hidden relative">
                                    @if($circle->icon)
                                        <img src="{{ asset('storage/' . $circle->icon) }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($circle->name) }}&background=134e4a&color=22d3ee';">
                                    @else
                                        <i class="fas fa-users-rays text-2xl animate-pulse opacity-40"></i>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors whitespace-nowrap">{{ $circle->name }}</p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] italic flex items-center gap-2">
                                        <i class="fas fa-fingerprint text-cyan-400"></i>
                                        ID: CIR-{{ str_pad($circle->id, 4, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-900 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($circle->leader->username ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-teal-900 uppercase italic whitespace-nowrap">{{ $circle->leader->username ?? 'NO LEADER' }}</p>
                                    @if($circle->leader && $circle->leader->rankTier)
                                        <p class="text-[8px] font-bold text-cyan-500 uppercase tracking-widest">{{ $circle->leader->rankTier->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center" data-sort-val="{{ $circle->members_count ?? $circle->members()->count() }}">
                            <div class="inline-flex flex-col items-center">
                                <span class="px-4 py-1.5 rounded-full bg-white border border-slate-100 text-[10px] font-black text-teal-900 shadow-sm flex items-center gap-2">
                                    <i class="fas fa-users-line text-cyan-500"></i>
                                    {{ $circle->members_count ?? $circle->members()->count() }} Hunter
                                </span>
                                <div class="flex mt-2 -space-x-2">
                                    @foreach($circle->members->take(5) as $mem)
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[8px] font-black text-slate-600" title="{{ $mem->username }}">
                                            {{ substr($mem->username, 0, 1) }}
                                        </div>
                                    @endforeach
                                    @if($circle->members()->count() > 5)
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[7px] font-black text-slate-400">
                                            +{{ $circle->members()->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center" data-sort-val="{{ $circle->level }}">
                            <div class="space-y-2">
                                <div class="text-xs font-serif font-black text-teal-900 italic uppercase">Level {{ $circle->level }}</div>
                                <div class="w-24 h-1.5 bg-slate-100 rounded-full mx-auto p-0.5 border border-slate-50 overflow-hidden shadow-inner relative">
                                    @php $percent = $circle->xp ? ($circle->xp / 1000) * 100 : 0; @endphp
                                    <div class="h-full bg-gradient-to-r from-teal-900 to-cyan-400 rounded-full transition-all duration-1000" style="width: {{ min(100, $percent) }}%"></div>
                                </div>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">{{ number_format($circle->xp ?? 0) }} XP</p>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.circles.show', $circle) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-teal-900 hover:bg-teal-900 hover:text-white rounded-xl transition-all shadow-sm active:scale-90">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                                <a href="{{ route('admin.circles.edit', $circle) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-teal-900 hover:bg-teal-900 hover:text-white rounded-xl transition-all shadow-sm active:scale-90">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.circles.destroy', $circle) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $circle->name }}')" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-red-500 hover:text-white rounded-xl transition-all shadow-sm active:scale-90">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-ghost text-5xl mb-4 text-teal-900"></i>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-teal-900">Tidak ada lingkaran terdeteksi dalam matriks</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-between items-center px-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            Total Nodes: {{ $circles instanceof \Illuminate\Pagination\LengthAwarePaginator ? $circles->total() : $circles->count() }}
        </p>
        @if($circles instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $circles->links() }}
        @endif
    </div>
</div>

<script>
    function setLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        window.location.href = url.toString();
    }

    document.getElementById('circleSearch')?.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        document.querySelectorAll('#circleTableBody tr').forEach(row => {
            let name = row.querySelector('.text-sm')?.innerText.toLowerCase() || '';
            let id = row.querySelector('.text-\\[9px\\]')?.innerText.toLowerCase() || '';
            row.style.display = (name.includes(val) || id.includes(val)) ? '' : 'none';
        });
    });

    let sortOrders = {};

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('circleTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        if (rows.length <= 1 && rows[0].cells.length === 1) return; // Empty table

        const isAsc = sortOrders[columnIndex] === 'asc';
        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';

        // Update UI Icons
        document.querySelectorAll('th i.fas').forEach((icon, idx) => {
            if (idx === columnIndex) {
                icon.className = isAsc ? 'fas fa-sort-down ml-2 text-cyan-500' : 'fas fa-sort-up ml-2 text-cyan-500';
            } else {
                icon.className = 'fas fa-sort ml-2 opacity-30';
            }
        });

        rows.sort((a, b) => {
            let valA, valB;

            if (columnIndex === 2 || columnIndex === 3) {
                // Numeric sorting for members and level
                valA = parseFloat(a.cells[columnIndex].getAttribute('data-sort-val')) || 0;
                valB = parseFloat(b.cells[columnIndex].getAttribute('data-sort-val')) || 0;
            } else {
                // Text sorting for identity and leader
                valA = a.cells[columnIndex].innerText.trim().toLowerCase();
                valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            }

            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tableBody.appendChild(row));
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
