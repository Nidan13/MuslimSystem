@extends('layouts.admin')

@section('title', 'Amanah Penarikan (WD)')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fadeIn">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Amanah Penarikan</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse shadow-[0_0_10px_#eab308]"></span>
            Validasi & Eksekusi Perpindahan Yield Hunter
        </p>
    </div>
    
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 10); @endphp
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-wd px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-wd px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-wd px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>

        <div class="flex items-center gap-2 p-1.5 bg-slate-100 rounded-2xl border-2 border-slate-50 shadow-inner">
            <a href="{{ route('admin.withdrawals.index') }}" class="px-5 py-2 {{ !request('status') ? 'bg-white text-teal-900 shadow-sm border border-slate-100' : 'text-slate-400' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Semua</a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="px-5 py-2 {{ request('status') == 'pending' ? 'bg-white text-yellow-600 shadow-sm border border-slate-100' : 'text-slate-400' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Tertunda</a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}" class="px-5 py-2 {{ request('status') == 'completed' ? 'bg-white text-emerald-600 shadow-sm border border-slate-100' : 'text-slate-400' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Berhasil</a>
        </div>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left" id="withdrawal-table">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(0)">
                        Hunter <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Jumlah (SP) <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Rincian Bank</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center">Status</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-sans" id="withdrawal-body">
                @foreach($withdrawals as $wd)
                <tr class="withdrawal-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 px-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg group-hover:scale-105 transition-transform duration-500">
                                {{ substr($wd->user->username, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none block mb-1">
                                    {{ $wd->user->username }}
                                </h3>
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">{{ $wd->created_at->format('d M Y | H:i') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-6 px-4 text-center">
                        <span class="text-lg font-black text-teal-900 font-mono tracking-tighter shadow-sm bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                            SP {{ number_format($wd->amount) }}
                        </span>
                    </td>
                    <td class="py-6 px-4">
                        <div class="flex flex-col">
                            <p class="text-[11px] font-black text-teal-950 uppercase tracking-tighter mb-0.5">{{ $wd->bank_name }}</p>
                            <p class="text-[9px] text-slate-400 font-mono tracking-widest leading-none">{{ $wd->account_number }}</p>
                            <p class="text-[8px] text-slate-300 font-bold uppercase mt-1">A/N: {{ $wd->account_name }}</p>
                        </div>
                    </td>
                    <td class="py-6 px-4 text-center">
                        @if($wd->status == 'pending')
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 text-yellow-600 bg-yellow-50 border-yellow-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                                Tertunda
                            </div>
                        @elseif($wd->status == 'completed')
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 text-emerald-600 bg-emerald-50 border-emerald-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Berhasil
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 text-red-600 bg-red-50 border-red-100 text-[9px] font-black uppercase tracking-widest shadow-sm" title="{{ $wd->rejection_reason }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                Ditolak
                            </div>
                        @endif
                    </td>
                    <td class="py-6 text-right px-4">
                        @if($wd->status == 'pending')
                        <button onclick="openWdModal({{ $wd->toJson() }}, '{{ $wd->user->username }}')" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-teal-900 text-white font-serif font-black uppercase tracking-widest text-[9px] hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 group/btn active:scale-95">
                            Proses WD
                            <i class="fas fa-bolt text-cyan-400 group-hover/btn:animate-pulse transition-transform"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Total Transmisi Penarikan: {{ $withdrawals instanceof \Illuminate\Pagination\LengthAwarePaginator ? $withdrawals->total() : $withdrawals->count() }}
    </div>
    
    <div class="flex items-center gap-4">
        @if($withdrawals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($withdrawals->onFirstPage())
                <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $withdrawals->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </a>
            @endif

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $withdrawals->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $withdrawals->lastPage() }}</span>
            </div>

            @if($withdrawals->hasMorePages())
                <a href="{{ $withdrawals->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
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

<!-- WD Processing Modal -->
<div id="wdModal" class="fixed inset-0 z-[100] hidden bg-teal-950/60 backdrop-blur-md flex items-center justify-center p-6 animate-fadeIn">
    <div class="bg-white rounded-[50px] shadow-2xl w-full max-w-xl border-4 border-slate-50 overflow-hidden relative">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="p-12 relative z-10">
            <div class="flex justify-between items-start mb-10">
                <div>
                    <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tighter mb-2 uppercase">Proses Penarikan</h2>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Validasi Transfer Dana Hunter</p>
                </div>
                <button onclick="closeWdModal()" class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-red-500 hover:border-red-100 transition-all active:scale-95">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <form id="wdForm" method="POST" action="" class="space-y-10">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-2 gap-6 p-2 bg-slate-50 rounded-[40px] border-2 border-slate-100/50 shadow-inner">
                    <div class="p-8 bg-white rounded-[32px] border border-slate-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-3">Nilai Transmisi</p>
                        <p id="modalAmount" class="text-2xl font-black text-teal-900 font-mono tracking-tighter">SP 0</p>
                    </div>
                    <div class="p-8 bg-white rounded-[32px] border border-slate-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-3">Node Tujuan</p>
                        <p id="modalUser" class="text-sm font-black text-teal-950 uppercase tracking-tight leading-none pt-1">-</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.3em] mb-4 block px-2">Keputusan Validasi</label>
                        <div class="grid grid-cols-2 gap-6">
                            <button type="button" onclick="setStatus('completed')" id="btnApprove" class="group p-6 rounded-3xl border-2 border-slate-100 bg-white font-black text-[11px] uppercase tracking-widest transition-all flex flex-col items-center justify-center gap-3 hover:border-emerald-200">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 transition-all group-hover:scale-110 group-[.decision-active]:bg-emerald-500 group-[.decision-active]:text-white">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                                <span class="text-slate-400 group-[.decision-active]:text-teal-900">Setujui</span>
                            </button>
                            <button type="button" onclick="setStatus('rejected')" id="btnReject" class="group p-6 rounded-3xl border-2 border-slate-100 bg-white font-black text-[11px] uppercase tracking-widest transition-all flex flex-col items-center justify-center gap-3 hover:border-red-200">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 transition-all group-hover:scale-110 group-[.decision-active]:bg-red-500 group-[.decision-active]:text-white">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </div>
                                <span class="text-slate-400 group-[.decision-active]:text-teal-900">Tolak</span>
                            </button>
                        </div>
                        <input type="hidden" name="status" id="inputStatus" required>
                    </div>

                    <div id="rejectionSection" class="hidden animate-slideUp">
                        <label class="text-[10px] font-black text-teal-900 uppercase tracking-[0.2em] mb-3 block px-2">Alasan Penolakan</label>
                        <textarea name="rejection_reason" class="w-full h-32 p-8 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:border-cyan-400 focus:bg-white outline-none text-sm transition-all font-medium shadow-inner placeholder-slate-300" placeholder="Jelaskan alasan integritas gagal..."></textarea>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                        <span class="relative flex items-center justify-center gap-4">
                            KONFIRMASI EKSEKUSI
                            <i class="fas fa-paper-plane text-cyan-400 icon-glow transition-all group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentLimit = 16;
    let sortOrders = { 0: 'asc', 1: 'asc' };

    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function applyDisplay() {
        // Obsolete: Handled server-side
    }
        const rows = document.querySelectorAll('.withdrawal-row');
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
        const tableBody = document.getElementById('withdrawal-body');
        const rows = Array.from(document.querySelectorAll('.withdrawal-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            if (columnIndex === 1) {
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

    function openWdModal(wd, username) {
        const modal = document.getElementById('wdModal');
        const form = document.getElementById('wdForm');
        
        form.action = `/admin/withdrawals/${wd.id}`;
        document.getElementById('modalAmount').innerText = 'SP ' + new Intl.NumberFormat().format(wd.amount);
        document.getElementById('modalUser').innerText = username;
        
        modal.classList.remove('hidden');
    }

    function closeWdModal() {
        document.getElementById('wdModal').classList.add('hidden');
    }

    function setStatus(status) {
        document.getElementById('inputStatus').value = status;
        const btnApprove = document.getElementById('btnApprove');
        const btnReject = document.getElementById('btnReject');
        const rejectionSection = document.getElementById('rejectionSection');

        if (status === 'completed') {
            btnApprove.classList.add('decision-active', 'bg-emerald-50', 'border-emerald-200');
            btnReject.classList.remove('decision-active', 'bg-red-50', 'border-red-200');
            rejectionSection.classList.add('hidden');
        } else {
            btnReject.classList.add('decision-active', 'bg-red-50', 'border-red-200');
            btnApprove.classList.remove('decision-active', 'bg-emerald-50', 'border-emerald-200');
            rejectionSection.classList.remove('hidden');
        }
    }

    window.onload = () => applyDisplay();
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slideUp { animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards; }

    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endsection
