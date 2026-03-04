@extends('layouts.admin')

@section('title', 'Registri Afiliasi')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fadeIn">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Registri Afiliasi</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
            Monitoring Performa & Komisi Hunter
        </p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 16); @endphp
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-affiliate px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-affiliate px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-affiliate px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left" id="affiliate-table">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(0)">
                        Hunter <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Kode Referral</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(2)">
                        Total Referral <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                        Total Komisi <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-sans" id="affiliate-body">
                @foreach($affiliates as $affiliate)
                <tr class="affiliate-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 px-4">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-105 transition-transform duration-500">
                                 {{ substr($affiliate->username, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1">
                                    {{ $affiliate->username }}
                                </h3>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider font-mono">UID: SN-{{ str_pad($affiliate->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-6 px-4">
                        <code class="px-3 py-1.5 bg-cyan-50 border border-cyan-100 rounded-lg text-[11px] font-black text-cyan-700 tracking-widest font-mono">{{ $affiliate->referral_code }}</code>
                    </td>
                    <td class="py-6 px-4 text-center">
                        <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">{{ $affiliate->referrals_count }}</span>
                    </td>
                    <td class="py-6 px-4 text-right">
                        <div class="inline-flex flex-col items-end">
                            <p class="text-lg font-black text-gold-600 font-mono tracking-tighter leading-none mb-1">
                                SP {{ number_format($affiliate->commissions_sum_amount ?? 0) }}
                            </p>
                            <span class="text-[8px] font-black text-gold-400 uppercase tracking-[0.2em]">Total Pendapatan</span>
                        </div>
                    </td>
                    <td class="py-6 text-right px-4 whitespace-nowrap">
                        <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white border-2 border-slate-100 text-[10px] font-black text-teal-900 uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/btn">
                            <i class="fas fa-eye text-teal-500 transition-transform group-hover/btn:scale-110"></i>
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Total Afiliasi Terdaftar: {{ $affiliates instanceof \Illuminate\Pagination\LengthAwarePaginator ? $affiliates->total() : $affiliates->count() }}
    </div>
    
    <div class="flex items-center gap-4">
        @if($affiliates instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($affiliates->onFirstPage())
                <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $affiliates->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </a>
            @endif

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $affiliates->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $affiliates->lastPage() }}</span>
            </div>

            @if($affiliates->hasMorePages())
                <a href="{{ $affiliates->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                </a>
            @else
                <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                    Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                </span>
            @endif
        @else
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">1</span>
                <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
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

    function applyDisplay() {
        // Obsolete: Handled server-side
    }
        const rows = document.querySelectorAll('.affiliate-row');
        let visibleCount = 0;

        rows.forEach(row => {
            if (currentLimit === 'all' || visibleCount < currentLimit) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('affiliate-body');
        const rows = Array.from(document.querySelectorAll('.affiliate-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            // Handle numeric values for total referral and total commission
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
        applyDisplay();
    }

    window.onload = () => applyDisplay();
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
