@extends('layouts.admin')

@section('title', 'Detail Afiliasi: ' . $user->username)

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    
    <!-- Header Stats -->
    <div class="relative glass-panel rounded-[50px] overflow-hidden bg-white border-2 border-slate-100 shadow-2xl p-12">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <div class="w-32 h-32 rounded-[40px] bg-teal-900 flex items-center justify-center text-5xl font-serif font-black text-white shadow-xl">
                {{ substr($user->username, 0, 1) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tighter mb-2">{{ $user->username }}</h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] flex justify-center md:justify-start items-center gap-3">
                    <i class="fas fa-barcode text-cyan-500"></i>
                    REFF CODE: <span class="text-cyan-600">{{ $user->referral_code }}</span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Saldo Saat Ini</p>
                    <p class="text-xl font-black text-teal-900 font-mono">SP {{ number_format($balance) }}</p>
                </div>
                <div class="p-4 bg-teal-900 rounded-3xl border border-teal-800 text-center text-white">
                    <p class="text-[9px] font-black text-cyan-400 uppercase tracking-widest mb-1">Total Referral</p>
                    <p class="text-xl font-black font-mono">{{ $referrals->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tab -->
    <div class="flex items-center gap-3 p-2 bg-slate-100/50 backdrop-blur-md rounded-[30px] border-2 border-slate-100 w-fit">
        <button onclick="switchTab('detail')" id="tab-detail" class="tab-btn active px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all">Detail</button>
        <button onclick="switchTab('referral')" id="tab-referral" class="tab-btn px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Referral</button>
        <button onclick="switchTab('komisi')" id="tab-komisi" class="tab-btn px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Komisi</button>
        <button onclick="switchTab('wd')" id="tab-wd" class="tab-btn px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Riwayat WD</button>
    </div>

    <!-- Tab Contents -->
    <div id="content-detail" class="tab-content space-y-10 animate-fadeIn">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-100">
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-8">Informasi Afiliasi</h3>
                <div class="space-y-6">
                    <div class="flex justify-between items-center py-4 border-b border-slate-50">
                        <span class="text-xs font-bold text-slate-500">Tanggal Bergabung</span>
                        <span class="text-sm font-black text-teal-900">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-slate-50">
                        <span class="text-xs font-bold text-slate-500">Total Komisi Didapat</span>
                        <span class="text-sm font-black text-gold-600">SP {{ number_format($totalCommission) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-slate-50">
                        <span class="text-xs font-bold text-slate-500">Total Telah Ditarik</span>
                        <span class="text-sm font-black text-red-500">SP {{ number_format($totalWithdrawn) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4">
                        <span class="text-xs font-bold text-slate-500">Referral Aktif</span>
                        <span class="text-sm font-black text-teal-900">{{ $referrals->where('payments_count', '>', 0)->count() }} User</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-10 rounded-[50px] bg-teal-900 border-2 border-teal-800 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-400/10 rounded-full blur-[80px]"></div>
                <h3 class="text-[10px] font-black text-cyan-400/60 uppercase tracking-[0.5em] mb-8">Ringkasan Aktivitas</h3>
                <div class="space-y-8">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-cyan-400">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-black font-mono leading-none">{{ $user->commissions_count }}</p>
                            <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mt-1">Transaksi Komisi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-cyan-400">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-black font-mono leading-none">{{ $user->withdrawals_count }}</p>
                            <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mt-1">Permintaan Withdrawal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Tab -->
    <div id="content-referral" class="tab-content hidden animate-fadeIn">
        <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">User</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Email</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Tgl Join</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-100 font-sans">
                    @forelse($referrals as $reff)
                    <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center font-black text-teal-900">
                                    {{ substr($reff->username, 0, 1) }}
                                </div>
                                <span class="text-sm font-black text-teal-900 uppercase tracking-tight">{{ $reff->username }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-sm text-slate-500 font-medium">{{ $reff->email }}</td>
                        <td class="px-8 py-6 text-sm text-center text-slate-500 font-mono">{{ $reff->created_at->format('d/m/Y') }}</td>
                        <td class="px-8 py-6 text-right whitespace-nowrap">
                            <a href="{{ route('admin.hunters.show', $reff) }}" class="p-2 border border-slate-100 rounded-lg hover:border-cyan-200 hover:text-cyan-600 transition-all">
                                <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center opacity-30">
                            <i class="fas fa-users-slash text-4xl mb-4"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest text-teal-900">Belum ada user yang terdaftar melalui referral ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Komisi Tab -->
    <div id="content-komisi" class="tab-content hidden animate-fadeIn">
        <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Referral User</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Tgl Transaksi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Jumlah Komisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-100 font-sans">
                    @forelse($commissions as $comm)
                    <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center font-black text-cyan-900">
                                    {{ substr($comm->referredUser->username ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <span class="text-sm font-black text-teal-900 uppercase tracking-tight">{{ $comm->referredUser->username ?? 'Unknown' }}</span>
                                    <p class="text-[9px] font-bold text-slate-400">Payment ID: #{{ $comm->payment_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-sm text-slate-500 font-mono">{{ $comm->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-base font-black text-gold-600 font-mono">SP {{ number_format($comm->amount) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-20 text-center opacity-30">
                            <i class="fas fa-file-invoice-dollar text-4xl mb-4"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest text-teal-900">Belum ada riwayat komisi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Withdrawal Tab -->
    <div id="content-wd" class="tab-content hidden animate-fadeIn">
        <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Jumlah</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Tgl Request</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Bank Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-slate-100 font-sans">
                    @forelse($withdrawals as $wd)
                    <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                        <td class="px-8 py-6">
                            <span class="text-base font-black text-teal-900 font-mono">SP {{ number_format($wd->amount) }}</span>
                        </td>
                        <td class="px-8 py-6 text-sm text-slate-500 font-mono">{{ $wd->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-8 py-6 text-center">
                            @if($wd->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-black uppercase">Pending</span>
                            @elseif($wd->status == 'approved')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase">Success</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase" title="{{ $wd->rejection_reason }}">Rejected</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <p class="text-xs font-black text-teal-900 uppercase mb-1">{{ $wd->bank_name }}</p>
                            <p class="text-[10px] text-slate-400 font-mono tracking-wider">{{ $wd->account_number }}</p>
                            <p class="text-[9px] text-slate-400 font-bold">{{ $wd->account_name }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center opacity-30">
                            <i class="fas fa-hand-holding-usd text-4xl mb-4"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest text-teal-900">Belum ada riwayat penarikan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<style>
    .tab-btn.active {
        background: #093b48;
        color: #22d3ee;
        box-shadow: 0 15px 30px -5px rgba(9, 59, 72, 0.4);
        transform: translateY(-2px);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.getElementById('content-' + tabId).classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-cyan-400', 'bg-teal-900');
            btn.classList.add('text-slate-400');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-slate-400');
    }
</script>
@endpush
@endsection
