@extends('layouts.admin')

@section('title', 'Amanah Penarikan (WD)')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Penarikan Dana</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Validasi & Eksekusi Transmisi EXP Hunter
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex items-center gap-2 p-1.5 bg-white rounded-2xl border-2 border-slate-50 shadow-sm">
                <a href="{{ route('admin.withdrawals.index') }}" class="px-5 py-2 {{ !request('status') ? 'bg-teal-900 text-white shadow-md' : 'text-slate-400' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Semua</a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="px-5 py-2 {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Tertunda</a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}" class="px-5 py-2 {{ request('status') == 'completed' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-400' }} rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Berhasil</a>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter/Utility Bar -->
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex flex-wrap justify-between items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                </div>
                <input type="text" id="wdSearch" placeholder="Cari nama hunter..." 
                    class="block w-80 pl-12 pr-4 py-3 bg-white border-2 border-slate-100 rounded-2xl text-[11px] font-black text-teal-900 uppercase tracking-widest placeholder-slate-300 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 transition-all outline-none">
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2 italic">Tampilkan:</span>
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    @foreach([8, 16, 32] as $lim)
                    <button onclick="setRowLimit({{ $lim }})" class="px-4 py-1.5 rounded-lg text-[10px] font-black transition-all {{ request('limit', 16) == $lim ? 'bg-teal-900 text-white shadow-md' : 'text-slate-400 hover:text-teal-900' }}">
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
                            Identitas Hunter <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            Jumlah Transmisi (EXP) <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em]">Destinasi Logistik</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center">Status Transaksi</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Protokol Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans" id="withdrawal-body">
                    @forelse($withdrawals as $wd)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-105 transition-transform duration-500">
                                    {{ substr($wd->user->username, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors block mb-1">
                                        {{ $wd->user->username }}
                                    </h3>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                        <i class="fas fa-fingerprint text-cyan-400"></i>
                                        WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}
                                    </p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase mt-1 italic tracking-widest">
                                        {{ $wd->created_at->format('d M Y • H:i') }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center" data-sort-val="{{ $wd->amount }}">
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black bg-gradient-to-br from-blue-600 to-slate-950 bg-clip-text text-transparent font-mono tracking-tighter leading-none mb-1">
                                    {{ number_format($wd->amount) }}
                                 </span>
                                 <span class="text-[8px] font-black text-blue-600/40 uppercase tracking-widest italic">Rupiah (IDR)</span>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-teal-900 border border-slate-200">
                                    <i class="fas fa-university text-sm"></i>
                                </div>
                                <div class="flex flex-col">
                                    <p class="text-[11px] font-black text-teal-950 uppercase tracking-tighter mb-0.5">{{ $wd->bank_name }}</p>
                                    <p class="text-[10px] text-slate-500 font-mono tracking-widest leading-none font-bold italic">{{ $wd->account_number }}</p>
                                    <p class="text-[8px] text-slate-300 font-bold uppercase mt-1 tracking-widest">AN: {{ $wd->account_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center">
                            <div class="flex flex-col items-center">
                                @if($wd->status == 'pending')
                                    <span class="px-5 py-2 bg-amber-50 border border-amber-100 text-amber-600 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($wd->status == 'completed')
                                    <span class="px-5 py-2 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-5 py-2 bg-red-50 border border-red-100 text-red-600 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2 cursor-help" title="{{ $wd->rejection_reason }}">
                                        <i class="fas fa-times-circle"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-6 px-8 text-right">
                            @if($wd->status == 'pending')
                            <button onclick="openWdModal({{ $wd->toJson() }}, '{{ $wd->user->username }}')" class="px-6 py-3 bg-teal-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-teal-800 transition-all shadow-xl shadow-teal-900/10 active:scale-95 flex items-center gap-2 ml-auto">
                                <i class="fas fa-shield-check text-cyan-400"></i>
                                Proses Transmisi
                            </button>
                            @else
                            <div class="flex items-center justify-end">
                                <span class="text-[9px] font-black text-slate-200 uppercase tracking-widest border border-slate-100 bg-slate-50/50 px-4 py-2 rounded-xl italic">
                                    Archived Transaction
                                </span>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-file-invoice-dollar text-5xl mb-4 text-teal-900"></i>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-teal-900">Tidak ada log penarikan terdeteksi</p>
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
            Total Inbound Log: {{ $withdrawals instanceof \Illuminate\Pagination\LengthAwarePaginator ? $withdrawals->total() : $withdrawals->count() }}
        </p>
        @if($withdrawals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $withdrawals->links() }}
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
                    <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tighter mb-2 uppercase italic leading-none">Proses <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Transmisi</span></h2>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Validasi Integrasi Dana Hunter</p>
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
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Nilai Hasil</p>
                        <p id="modalAmount" class="text-2xl font-black bg-gradient-to-br from-blue-600 to-slate-950 bg-clip-text text-transparent font-mono tracking-tighter">0 EXP</p>
                    </div>
                    <div class="p-8 bg-white rounded-[32px] border border-slate-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Identik Hunter</p>
                        <p id="modalUser" class="text-sm font-black text-teal-950 uppercase tracking-tight leading-none pt-1">-</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.3em] mb-4 block px-2 italic">Keputusan Jalur Transmisi</label>
                        <div class="grid grid-cols-2 gap-6">
                            <button type="button" onclick="setStatus('completed')" id="btnApprove" class="group p-6 rounded-3xl border-2 border-slate-100 bg-white font-black text-[11px] uppercase tracking-widest transition-all flex flex-col items-center justify-center gap-3 hover:border-emerald-200">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 transition-all group-hover:scale-110 group-[.decision-active]:bg-emerald-500 group-[.decision-active]:text-white">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                                <span class="text-slate-400 group-[.decision-active]:text-teal-900">Validasi Sukses</span>
                            </button>
                            <button type="button" onclick="setStatus('rejected')" id="btnReject" class="group p-6 rounded-3xl border-2 border-slate-100 bg-white font-black text-[11px] uppercase tracking-widest transition-all flex flex-col items-center justify-center gap-3 hover:border-red-200">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 transition-all group-hover:scale-110 group-[.decision-active]:bg-red-500 group-[.decision-active]:text-white">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </div>
                                <span class="text-slate-400 group-[.decision-active]:text-teal-900">Gagalkan Log</span>
                            </button>
                        </div>
                        <input type="hidden" name="status" id="inputStatus" required>
                    </div>

                    <div id="rejectionSection" class="hidden animate-slideUp">
                        <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.3em] mb-3 block px-2">Direktif Kegagalan (Internal Log)</label>
                        <textarea name="rejection_reason" class="w-full h-32 p-8 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:border-red-400 focus:bg-white outline-none text-sm transition-all font-medium shadow-inner placeholder-slate-200" placeholder="Jelaskan anomali yang ditemukan..."></textarea>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                        <span class="relative flex items-center justify-center gap-4">
                            EKSEKUSI TRANSMISI
                            <i class="fas fa-paper-plane text-cyan-400 icon-glow transition-all group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('withdrawal-body');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        if (rows.length <= 1 && rows[0].cells.length === 1) return;

        const isAsc = window.sortOrders && window.sortOrders[columnIndex] === 'asc';
        if(!window.sortOrders) window.sortOrders = {};
        window.sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';

        document.querySelectorAll('th i.fas').forEach((icon, idx) => {
            if (idx === columnIndex) {
                icon.className = isAsc ? 'fas fa-sort-down ml-2 text-cyan-500' : 'fas fa-sort-up ml-2 text-cyan-500';
            } else {
                icon.className = 'fas fa-sort ml-2 opacity-30';
            }
        });

        rows.sort((a, b) => {
            let valA, valB;
            if (columnIndex === 1) { // Numeric
                valA = parseFloat(a.cells[columnIndex].getAttribute('data-sort-val')) || 0;
                valB = parseFloat(b.cells[columnIndex].getAttribute('data-sort-val')) || 0;
            } else {
                valA = a.cells[columnIndex].innerText.trim().toLowerCase();
                valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            }
            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tableBody.appendChild(row));
    }

    function openWdModal(wd, username) {
        const modal = document.getElementById('wdModal');
        const form = document.getElementById('wdForm');
        form.action = `/admin/withdrawals/${wd.id}`;
        document.getElementById('modalAmount').innerText = new Intl.NumberFormat().format(wd.amount) + ' EXP';
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

    document.getElementById('wdSearch')?.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        document.querySelectorAll('#withdrawal-body tr').forEach(row => {
            let user = row.querySelector('h3')?.innerText.toLowerCase() || '';
            row.style.display = user.includes(val) ? '' : 'none';
        });
    });
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    .animate-slideUp { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endpush
@endsection
