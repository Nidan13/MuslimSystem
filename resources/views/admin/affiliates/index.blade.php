@extends('layouts.admin')

@section('title', 'Daftar Afiliasi')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Daftar Afiliasi</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.5)]"></span>
                Monitoring Pendapatan & Jaringan Hunter
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
            <!-- Search Bar -->
            <div class="relative w-full sm:w-72 group">
                <input type="text" id="hunterSearch" placeholder="Cari Hunter..." 
                    class="w-full bg-white border-2 border-slate-100 rounded-2xl py-3 pl-12 pr-6 text-sm font-bold text-teal-900 placeholder:text-slate-300 focus:border-cyan-400 outline-none transition-all shadow-sm">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
            </div>

            <!-- Row Limit Controls -->
            <div class="flex items-center bg-white p-1 rounded-2xl border-2 border-slate-100 shadow-sm">
                @php $currentLimit = request('limit', 16); @endphp
                <div class="flex gap-1">
                    <button type="button" onclick="setRowLimit(8)" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-slate-50 text-slate-400' }}">8</button>
                    <button type="button" onclick="setRowLimit(16)" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-slate-50 text-slate-400' }}">16</button>
                    <button type="button" onclick="setRowLimit('all')" class="px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-slate-50 text-slate-400' }}">ALL</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[32px] border-2 border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="affiliate-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="py-6 px-8 text-[10px] font-black text-slate-400 uppercase tracking-widest cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(0)">
                            Hunter <i class="fas fa-sort ml-2 opacity-20 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Referral Code</th>
                        <th class="py-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(2)">
                            Total Reff <i class="fas fa-sort ml-2 opacity-20 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                            Accumulated Commission <i class="fas fa-sort ml-2 opacity-20 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="affiliate-body">
                    @forelse($affiliates as $affiliate)
                    <tr class="affiliate-row group hover:bg-slate-50/30 transition-colors">
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-teal-950 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-105 transition-transform duration-500">
                                     {{ substr($affiliate->username, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1.5">
                                        {{ $affiliate->username }}
                                    </h3>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest font-mono leading-none">SN-{{ str_pad($affiliate->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-4">
                            <code class="px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-[11px] font-black text-cyan-600 tracking-wider font-mono">
                                {{ $affiliate->referral_code }}
                            </code>
                        </td>
                        <td class="py-6 px-4 text-center">
                            <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">
                                {{ number_format($affiliate->referrals_count) }}
                            </span>
                        </td>
                        <td class="py-6 px-4 text-right">
                            <div class="inline-flex flex-col items-end">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-[9px] font-black text-cyan-500 italic uppercase">Rp</span>
                                    <p class="text-xl font-black text-teal-950 font-mono tracking-tighter leading-none">
                                        {{ number_format($affiliate->commissions_sum_amount ?? 0) }}
                                    </p>
                                </div>
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-1">Total Yield</span>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-right whitespace-nowrap">
                            <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black text-teal-900 uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/btn">
                                <i class="fas fa-eye text-cyan-400"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-folder-open text-6xl mb-4"></i>
                                <span class="text-[10px] font-black uppercase tracking-[0.3em]">No Data Found</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Area -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
            Total Entry: {{ $affiliates instanceof \Illuminate\Pagination\LengthAwarePaginator ? $affiliates->total() : $affiliates->count() }}
        </div>
        
        <div class="flex items-center gap-3">
            @if($affiliates instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @if($affiliates->onFirstPage())
                    <span class="px-5 py-3 rounded-xl bg-slate-50 text-slate-200 border border-slate-100 cursor-not-allowed font-black text-[10px] uppercase tracking-widest italic">PREV</span>
                @else
                    <a href="{{ $affiliates->previousPageUrl() }}" class="px-5 py-3 rounded-xl bg-white text-teal-900 border border-slate-100 hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm font-black text-[10px] uppercase tracking-widest italic">PREV</a>
                @endif

                <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-100 shadow-sm">
                    <span class="px-4 py-2 text-[10px] font-black bg-teal-900 text-white rounded-lg shadow-md">{{ $affiliates->currentPage() }}</span>
                    <span class="text-[9px] font-black text-slate-300 px-3 uppercase">OF {{ $affiliates->lastPage() }}</span>
                </div>

                @if($affiliates->hasMorePages())
                    <a href="{{ $affiliates->nextPageUrl() }}" class="px-5 py-3 rounded-xl bg-teal-900 text-white hover:bg-teal-800 transition-all shadow-sm font-black text-[10px] uppercase tracking-widest italic">NEXT</a>
                @else
                    <span class="px-5 py-3 rounded-xl bg-slate-50 text-slate-200 border border-slate-100 cursor-not-allowed font-black text-[10px] uppercase tracking-widest italic">NEXT</span>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    // Search logic
    document.getElementById('hunterSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.affiliate-row');
        
        rows.forEach(row => {
            const context = row.innerText.toLowerCase();
            row.style.display = context.includes(term) ? '' : 'none';
        });
    });

    let sortOrders = { 0: 'asc', 2: 'asc', 3: 'asc' };

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('affiliate-body');
        const rows = Array.from(document.querySelectorAll('.affiliate-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            if (columnIndex === 2 || columnIndex === 3) {
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
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

