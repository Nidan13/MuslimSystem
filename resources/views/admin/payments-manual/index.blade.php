@extends('layouts.admin')

@section('title', 'Manajemen Infaq Manual')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Infaq Manual</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Validasi Kontribusi Manual Hunter
            </p>
        </div>
        
        <div class="flex items-center gap-3 p-1.5 bg-slate-100 rounded-2xl border border-slate-200">
            <a href="{{ route('admin.payments.manual.index') }}" class="px-6 py-2.5 {{ !request('status') || request('status') == 'pending' ? 'bg-white text-teal-900 shadow-md' : 'text-slate-400 hover:text-teal-900' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Pending</a>
            <a href="{{ route('admin.payments.manual.index', ['status' => 'paid']) }}" class="px-6 py-2.5 {{ request('status') == 'paid' ? 'bg-white text-emerald-600 shadow-md' : 'text-slate-400 hover:text-emerald-600' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Approved</a>
            <a href="{{ route('admin.payments.manual.index', ['status' => 'rejected']) }}" class="px-6 py-2.5 {{ request('status') == 'rejected' ? 'bg-white text-red-600 shadow-md' : 'text-slate-400 hover:text-red-600' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Rejected</a>
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
                <input type="text" id="paymentSearch" placeholder="Cari nama hunter atau Ref ID..." 
                    class="block w-80 pl-12 pr-4 py-3 bg-white border-2 border-slate-100 rounded-2xl text-[11px] font-black text-teal-900 uppercase tracking-widest placeholder-slate-300 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 transition-all outline-none">
            </div>

            <div class="flex items-center gap-3 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                <i class="fas fa-filter text-cyan-400"></i>
                Status Filter: <span class="text-teal-900 not-italic">{{ ucfirst(request('status', 'Pending')) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400">
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(0)">
                            Identitas Hunter <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            Nominal Infaq <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(2)">
                            Status Node <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Opsi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="paymentTableBody">
                    @forelse($payments as $p)
                    @php 
                        $payload = json_decode($p->payload, true);
                        $proof = $payload['proof_image'] ?? null;
                    @endphp
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-105 transition-transform duration-500">
                                    {{ substr($p->user->username ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">
                                        {{ $p->user->username ?? 'Unknown' }}
                                    </h3>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1 flex items-center gap-2">
                                        <i class="fas fa-fingerprint text-cyan-400"></i>
                                        {{ $p->external_id }}
                                    </p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase mt-1 italic tracking-widest">
                                        {{ $p->created_at->format('d M Y • H:i') }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8" data-sort-val="{{ $p->amount }}">
                            <div class="flex flex-col">
                                <span class="text-xl font-black text-teal-900 font-mono tracking-tighter leading-none mb-1">
                                    Rp {{ number_format($p->amount) }}
                                </span>
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em] italic">Manual Transfer</span>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center" data-sort-val="{{ $p->status }}">
                            <div class="flex flex-col items-center">
                                @if($p->status == 'pending')
                                    <span class="px-5 py-2 bg-amber-50 border border-amber-100 text-amber-600 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($p->status == 'paid')
                                    <span class="px-5 py-2 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Approved
                                    </span>
                                @else
                                    <span class="px-5 py-2 bg-red-50 border border-red-100 text-red-600 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-sm flex items-center gap-2 cursor-help" title="{{ $payload['rejection_reason'] ?? 'Tanpa alasan' }}">
                                        <i class="fas fa-times-circle"></i>
                                        Rejected
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-6 px-8 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if($proof)
                                <button onclick="viewProof('{{ asset('storage/'.$proof) }}')" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-cyan-600 rounded-xl hover:bg-cyan-50 transition-all shadow-sm active:scale-90" title="Lihat Bukti">
                                    <i class="fas fa-image text-sm"></i>
                                </button>
                                @endif

                                @if($p->status == 'pending')
                                <button onclick="openRejectModal({{ $p }})" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 text-red-500 rounded-xl hover:bg-red-50 transition-all shadow-sm active:scale-90" title="Tolak">
                                    <i class="fas fa-ban text-sm"></i>
                                </button>
                                <form action="{{ route('admin.payments.manual.approve', $p) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" onclick="confirmApprove(this, '{{ $p->user->username ?? 'User' }}')" class="px-6 py-2.5 bg-teal-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-teal-800 transition-all shadow-xl shadow-teal-900/10 active:scale-95 flex items-center gap-2">
                                        <i class="fas fa-shield-check text-cyan-400"></i>
                                        Approve
                                    </button>
                                </form>
                                @else
                                <span class="text-[9px] font-black text-slate-200 uppercase tracking-widest border border-slate-100 bg-slate-50/50 px-4 py-2 rounded-xl italic">
                                    Archived Node
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-receipt text-5xl mb-4 text-teal-900"></i>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-teal-900">Tidak ada log kontribusi terdeteksi</p>
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
            Total Inbound Log: {{ $payments instanceof \Illuminate\Pagination\LengthAwarePaginator ? $payments->total() : $payments->count() }}
        </p>
        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $payments->links() }}
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-teal-950/40 backdrop-blur-sm flex items-center justify-center p-6">
    <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg border-4 border-slate-50 overflow-hidden animate-slideUp">
        <div class="p-10">
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tighter mb-2 uppercase italic leading-none">Diskualifikasi <span class="text-red-500 font-sans tracking-normal not-italic mx-1">Node</span></h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-10 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                Inisialisasi Protokol Penolakan Matriks
            </p>
            
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.3em] mb-3 block px-1">Direktif Alasan (Internal Log)</label>
                        <textarea name="rejection_reason" required 
                            class="w-full h-32 p-6 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:border-red-400 focus:bg-white outline-none text-sm transition-all shadow-inner font-medium placeholder-slate-200" 
                            placeholder="Sebutkan anomali yang ditemukan..."></textarea>
                    </div>
                </div>

                <div class="flex gap-4 mt-12">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 p-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-teal-900 transition-all">Batalkan</button>
                    <button type="submit" class="group flex-2 px-10 py-5 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-red-600/20 active:scale-95 transition-all flex items-center justify-center gap-3">
                        EKSEKUSI PENOLAKAN
                        <i class="fas fa-ban text-white/50 group-hover:text-white transition-colors"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Proof Modal -->
<div id="proofModal" class="fixed inset-0 z-50 hidden bg-teal-950/80 backdrop-blur-md flex items-center justify-center p-6" onclick="closeProofModal()">
    <div class="max-w-4xl w-full animate-zoomIn" onclick="event.stopPropagation()">
        <img id="proofImage" src="" class="w-full h-auto rounded-[40px] shadow-2xl border-4 border-white/10">
        <button onclick="closeProofModal()" class="absolute top-10 right-10 w-12 h-12 bg-white rounded-full flex items-center justify-center text-teal-900 shadow-xl border-2 border-teal-50">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(p) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/admin/payments/manual/${p.id}/reject`;
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function viewProof(src) {
        const modal = document.getElementById('proofModal');
        const img = document.getElementById('proofImage');
        img.src = src;
        modal.classList.remove('hidden');
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.add('hidden');
    }

    document.getElementById('paymentSearch')?.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        document.querySelectorAll('#paymentTableBody tr').forEach(row => {
            let user = row.querySelector('h3')?.innerText.toLowerCase() || '';
            let ref = row.querySelector('.text-\\[9px\\]')?.innerText.toLowerCase() || '';
            row.style.display = (user.includes(val) || ref.includes(val)) ? '' : 'none';
        });
    });

    let sortOrders = {};
    function sortTable(columnIndex) {
        const tableBody = document.getElementById('paymentTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        if (rows.length <= 1 && rows[0].cells.length === 1) return;

        const isAsc = sortOrders[columnIndex] === 'asc';
        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';

        document.querySelectorAll('th i.fas').forEach((icon, idx) => {
            if (idx === columnIndex) {
                icon.className = isAsc ? 'fas fa-sort-down ml-2 text-cyan-500' : 'fas fa-sort-up ml-2 text-cyan-500';
            } else {
                icon.className = 'fas fa-sort ml-2 opacity-30';
            }
        });

        rows.sort((a, b) => {
            let valA, valB;
            if (columnIndex === 1) { // Numeric nominal
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
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    .animate-slideUp { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-zoomIn { animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes zoomIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
@endpush
@endsection
