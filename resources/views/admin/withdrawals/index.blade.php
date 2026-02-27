@extends('layouts.admin')

@section('title', 'Manajemen Penarikan Saldo')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Permintaan WD</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse shadow-[0_0_10px_rgba(234,179,8,0.5)]"></span>
                Manajemen Pencairan Komisi Afiliasi
            </p>
        </div>
        
        <div class="flex items-center gap-3 p-1.5 bg-slate-100 rounded-2xl border border-slate-200">
            <a href="{{ route('admin.withdrawals.index') }}" class="px-5 py-2 {{ !request('status') ? 'bg-white text-teal-900 border border-slate-200 shadow-sm' : 'text-slate-400' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Semua</a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="px-5 py-2 {{ request('status') == 'pending' ? 'bg-white text-yellow-600 border border-slate-200 shadow-sm' : 'text-slate-400' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Pending</a>
            <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}" class="px-5 py-2 {{ request('status') == 'completed' ? 'bg-white text-emerald-600 border border-slate-200 shadow-sm' : 'text-slate-400' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Success</a>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px] bg-white border-2 border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Hunter</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Jumlah (SP)</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Bank Detail</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($withdrawals as $wd)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-teal-900 flex items-center justify-center font-serif font-black text-white">
                                {{ substr($wd->user->username, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-tight">{{ $wd->user->username }}</h3>
                                <p class="text-[9px] font-bold text-slate-400">{{ $wd->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">SP {{ number_format($wd->amount) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-xs font-black text-slate-600 uppercase">{{ $wd->bank_name }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">{{ $wd->account_number }}</p>
                        <p class="text-[9px] text-slate-400 font-bold">{{ $wd->account_name }}</p>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @if($wd->status == 'pending')
                            <span class="px-4 py-1.5 bg-yellow-50 border border-yellow-200 text-yellow-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">Pending</span>
                        @elseif($wd->status == 'completed')
                            <span class="px-4 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">Success</span>
                        @else
                            <span class="px-4 py-1.5 bg-red-50 border border-red-200 text-red-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm" title="{{ $wd->rejection_reason }}">Rejected</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-right">
                        @if($wd->status == 'pending')
                        <button onclick="openWdModal({{ $wd }})" class="px-5 py-2.5 bg-teal-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-teal-900/20 hover:bg-teal-800 transition-all active:scale-95">
                            Proses WD
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $withdrawals->links() }}
    </div>
</div>

<!-- WD Processing Modal -->
<div id="wdModal" class="fixed inset-0 z-50 hidden bg-teal-950/40 backdrop-blur-sm flex items-center justify-center p-6">
    <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg border-4 border-slate-50 overflow-hidden animate-fadeIn">
        <div class="p-10">
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tighter mb-2 uppercase">Proses Penarikan</h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-10">Validasi Transfer Dana Hunter</p>
            
            <form id="wdForm" method="POST" action="">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-2 gap-6 mb-10">
                    <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Jumlah WD</p>
                        <p id="modalAmount" class="text-2xl font-black text-teal-900 font-mono tracking-tighter">SP 0</p>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Penerima</p>
                        <p id="modalUser" class="text-sm font-black text-teal-900 uppercase tracking-tight">-</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-teal-900 uppercase tracking-widest mb-3 block px-1">Keputusan</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="setStatus('completed')" id="btnApprove" class="p-4 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-check-circle"></i> Setujui
                            </button>
                            <button type="button" onclick="setStatus('rejected')" id="btnReject" class="p-4 rounded-2xl border-2 border-slate-100 font-black text-[11px] uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-times-circle"></i> Tolak
                            </button>
                        </div>
                        <input type="hidden" name="status" id="inputStatus" required>
                    </div>

                    <div id="rejectionSection" class="hidden">
                        <label class="text-[10px] font-black text-teal-900 uppercase tracking-widest mb-3 block px-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" class="w-full h-32 p-6 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:border-teal-900 focus:outline-none text-sm transition-all" placeholder="Misal: Nomor rekening tidak valid..."></textarea>
                    </div>
                </div>

                <div class="flex gap-4 mt-12">
                    <button type="button" onclick="closeWdModal()" class="flex-1 p-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-teal-900 transition-all">Batalkan</button>
                    <button type="submit" class="flex-2 px-10 py-5 bg-teal-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-teal-900/20 active:scale-95 transition-all">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openWdModal(wd) {
        const modal = document.getElementById('wdModal');
        const form = document.getElementById('wdForm');
        
        form.action = `/admin/withdrawals/${wd.id}`;
        document.getElementById('modalAmount').innerText = 'SP ' + new Intl.NumberFormat().format(wd.amount);
        document.getElementById('modalUser').innerText = wd.user.username;
        
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
            btnApprove.classList.add('bg-emerald-50', 'border-emerald-400', 'text-emerald-600');
            btnReject.classList.remove('bg-red-50', 'border-red-400', 'text-red-600');
            rejectionSection.classList.add('hidden');
        } else {
            btnReject.classList.add('bg-red-50', 'border-red-400', 'text-red-600');
            btnApprove.classList.remove('bg-emerald-50', 'border-emerald-400', 'text-emerald-600');
            rejectionSection.classList.remove('hidden');
        }
    }
</script>
@endpush
@endsection
