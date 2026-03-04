@extends('layouts.admin')

@section('title', 'Manajemen Infaq Manual')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Manual Infaq</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Verifikasi Pembayaran Manual User
            </p>
        </div>
        
        <div class="flex items-center gap-3 p-1.5 bg-slate-100 rounded-2xl border border-slate-200">
            <a href="{{ route('admin.payments.manual.index') }}" class="px-5 py-2 {{ !request('status') || request('status') == 'pending' ? 'bg-white text-teal-900 border border-slate-200 shadow-sm' : 'text-slate-400' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Pending</a>
            <a href="{{ route('admin.payments.manual.index', ['status' => 'paid']) }}" class="px-5 py-2 {{ request('status') == 'paid' ? 'bg-white text-emerald-600 border border-slate-200 shadow-sm' : 'text-slate-400' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Approved</a>
            <a href="{{ route('admin.payments.manual.index', ['status' => 'rejected']) }}" class="px-5 py-2 {{ request('status') == 'rejected' ? 'bg-white text-red-600 border border-slate-200 shadow-sm' : 'text-slate-400' }} rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Rejected</a>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px] bg-white border-2 border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Hunter / User</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Nominal</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Ref ID</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @forelse($payments as $p)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-teal-900 flex items-center justify-center font-serif font-black text-white">
                                {{ substr($p->user->username ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-tight">{{ $p->user->username ?? 'Unknown' }}</h3>
                                <p class="text-[9px] font-bold text-slate-400">{{ $p->user->email ?? '-' }}</p>
                                <p class="text-[9px] font-bold text-slate-400">{{ $p->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">Rp {{ number_format($p->amount) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-[10px] text-slate-400 font-mono">{{ $p->external_id }}</p>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @if($p->status == 'pending')
                            <span class="px-4 py-1.5 bg-yellow-50 border border-yellow-200 text-yellow-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">Pending</span>
                        @elseif($p->status == 'paid')
                            <span class="px-4 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">Paid</span>
                        @else
                            <span class="px-4 py-1.5 bg-red-50 border border-red-200 text-red-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm" title="{{ json_decode($p->payload)->rejection_reason ?? '' }}">Rejected</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-right">
                        @if($p->status == 'pending')
                        <div class="flex justify-end gap-2">
                            <button onclick="openRejectModal({{ $p }})" class="p-2.5 bg-red-50 text-red-600 rounded-xl border border-red-100 hover:bg-red-500 hover:text-white transition-all active:scale-95" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                            <form action="{{ route('admin.payments.manual.approve', $p) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin meng-approve pembayaran ini dan mengaktifkan user?')">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-teal-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-teal-900/20 hover:bg-teal-800 transition-all active:scale-95">
                                    Approve
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">
                        Tidak ada data pembayaran manual.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $payments->links() }}
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-teal-950/40 backdrop-blur-sm flex items-center justify-center p-6">
    <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg border-4 border-slate-50 overflow-hidden animate-fadeIn">
        <div class="p-10">
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tighter mb-2 uppercase">Tolak Pembayaran</h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-10">Berikan alasan penolakan pembayaran</p>
            
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-teal-900 uppercase tracking-widest mb-3 block px-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" required class="w-full h-32 p-6 bg-slate-50 border-2 border-slate-100 rounded-[32px] focus:border-teal-900 focus:outline-none text-sm transition-all" placeholder="Misal: Bukti transfer tidak valid/palsu..."></textarea>
                    </div>
                </div>

                <div class="flex gap-4 mt-12">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 p-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-teal-900 transition-all">Batalkan</button>
                    <button type="submit" class="flex-2 px-10 py-5 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-red-600/20 active:scale-95 transition-all">Tolak Sekarang</button>
                </div>
            </form>
        </div>
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
</script>
@endpush
@endsection
