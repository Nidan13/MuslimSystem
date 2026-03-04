@extends('layouts.admin')

@section('title', 'Detail Afiliasi: ' . $user->username)

@section('content')
<div class="max-w-6xl mx-auto space-y-10 animate-fadeIn">
    
    <!-- Header Stats -->
    <div class="relative glass-panel rounded-[50px] overflow-hidden bg-white border-2 border-slate-50 shadow-2xl p-12">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            <div class="w-32 h-32 rounded-[40px] bg-teal-900 border-2 border-teal-800 flex items-center justify-center text-5xl font-serif font-black text-white shadow-2xl shadow-teal-950/40 transition-transform hover:scale-105 duration-500">
                {{ substr($user->username, 0, 1) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tighter mb-2">{{ $user->username }}</h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] flex justify-center md:justify-start items-center gap-3">
                    <i class="fas fa-barcode text-cyan-500 icon-glow"></i>
                    KODE REFF: <span class="text-cyan-600 font-mono">{{ $user->referral_code }}</span>
                </p>
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest shadow-sm">Identitas: SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="px-4 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-[9px] font-black text-emerald-600 uppercase tracking-widest shadow-sm">Status: Afiliasi Aktif</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full md:w-auto">
                <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100/50 text-center shadow-inner group hover:border-cyan-200 transition-all">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Saldo Saat Ini</p>
                    <p class="text-2xl font-black text-teal-900 font-mono tracking-tighter">SP {{ number_format($balance) }}</p>
                    <div class="mt-2 h-1 w-12 bg-cyan-400 mx-auto rounded-full opacity-0 group-hover:opacity-100 transition-all"></div>
                </div>
                <div class="p-6 bg-teal-900 rounded-[32px] border-2 border-teal-800 text-center text-white shadow-2xl shadow-teal-950/30 group hover:border-cyan-400 transition-all">
                    <p class="text-[10px] font-black text-cyan-400 uppercase tracking-widest mb-2">Total Referral</p>
                    <p class="text-2xl font-black font-mono tracking-tighter">{{ $referrals->count() }}</p>
                    <div class="mt-2 h-1 w-12 bg-white mx-auto rounded-full opacity-20 group-hover:opacity-100 transition-all"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tab -->
    <div class="flex items-center gap-3 p-2 bg-slate-100/50 backdrop-blur-md rounded-[30px] border-2 border-slate-50 w-fit mx-auto md:mx-0 shadow-sm">
        <button onclick="switchTab('detail')" id="tab-detail" class="tab-btn active px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all">Profil</button>
        <button onclick="switchTab('referral')" id="tab-referral" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Pasukan</button>
        <button onclick="switchTab('komisi')" id="tab-komisi" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Hasil Komisi</button>
        <button onclick="switchTab('wd')" id="tab-wd" class="tab-btn px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all text-slate-400 hover:text-teal-900">Riwayat WD</button>
    </div>

    <!-- Tab Contents -->
    <div id="content-detail" class="tab-content space-y-10 animate-slideUp">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
                <h3 class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.5em] mb-10 border-b border-slate-50 pb-6 flex items-center gap-3">
                    <i class="fas fa-info-circle text-cyan-500"></i> Informasi Afiliasi
                </h3>
                <div class="grid gap-10">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Tanggal Bergabung</span>
                        <span class="text-lg font-black text-teal-900 font-serif uppercase tracking-tight">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Total Komisi Didapat</span>
                        <span class="text-lg font-black text-gold-600 font-mono">+SP {{ number_format($totalCommission) }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Total Telah Ditarik</span>
                        <span class="text-lg font-black text-red-500 font-mono">-SP {{ number_format($totalWithdrawn) }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Referral Aktif</span>
                        <span class="text-lg font-black text-cyan-600 font-serif uppercase tracking-tight">{{ $referrals->where('payments_count', '>', 0)->count() }} Hunter</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-12 rounded-[50px] bg-teal-900 border-2 border-teal-800 text-white relative overflow-hidden shadow-2xl shadow-teal-950/50">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-cyan-400/10 rounded-full blur-[100px] pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-cyan-400/40 uppercase tracking-[0.5em] mb-10 border-b border-white/5 pb-6 flex items-center gap-3">
                    <i class="fas fa-chart-line text-cyan-400"></i> Ringkasan Aktivitas
                </h3>
                <div class="grid gap-12 relative z-10">
                    <div class="flex items-center gap-8 group">
                        <div class="w-16 h-16 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-cyan-400 shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-4xl font-black font-mono leading-none tracking-tighter">{{ $user->commissions_count }}</p>
                            <p class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] mt-2">Transaksi Komisi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-8 group">
                        <div class="w-16 h-16 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-cyan-400 shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fas fa-wallet text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-4xl font-black font-mono leading-none tracking-tighter">{{ $user->withdrawals_count }}</p>
                            <p class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] mt-2">Penarikan Dana</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Tab -->
    <div id="content-referral" class="tab-content hidden animate-slideUp">
        <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <div class="overflow-x-auto p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 uppercase">
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Hunter</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Email Node</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center">Tgl Inisiasi</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-sans">
                        @forelse($referrals as $reff)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 px-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-black text-cyan-400 shadow-lg group-hover:scale-110 transition-transform">
                                        {{ substr($reff->username, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">{{ $reff->username }}</span>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <code class="text-[10px] font-black text-slate-400 lowercase font-mono bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">{{ $reff->email }}</code>
                            </td>
                            <td class="py-6 px-4 text-sm text-center text-slate-500 font-mono tracking-tighter font-bold uppercase">{{ $reff->created_at->format('d.m.Y') }}</td>
                            <td class="py-6 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.hunters.show', $reff) }}" class="inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-white border-2 border-slate-100 text-[10px] font-black text-teal-900 uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm group/btn active:scale-95">
                                    <i class="fas fa-eye text-teal-500 transition-transform group-hover/btn:scale-110"></i>
                                    Profil
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-24 text-center">
                                <div class="opacity-20 flex flex-col items-center">
                                    <i class="fas fa-users-slash text-5xl mb-6"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-teal-950">Pasukan referral belum terdaftar dalam matriks</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Komisi Tab -->
    <div id="content-komisi" class="tab-content hidden animate-slideUp">
        <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <div class="overflow-x-auto p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 uppercase">
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Sumber Protokol (User)</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Node Waktu</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Yield Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-sans">
                        @forelse($commissions as $comm)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 px-4">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 border border-cyan-200 flex items-center justify-center font-black text-cyan-600 shadow-sm group-hover:animate-pulse">
                                        {{ substr($comm->referredUser->username ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none block mb-1">
                                            {{ $comm->referredUser->username ?? 'Anomali' }}
                                        </span>
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">ID Pembayaran: #{{ $comm->payment_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <span class="text-[10px] font-black text-slate-500 font-mono tracking-tighter bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                    {{ $comm->created_at->format('d/m/Y') }} <span class="opacity-30 mx-1">|</span> {{ $comm->created_at->format('H:i') }}
                                </span>
                            </td>
                            <td class="py-6 px-4 text-right">
                                <span class="text-lg font-black text-amber-600 font-mono tracking-tighter">+SP {{ number_format($comm->amount) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-24 text-center">
                                <div class="opacity-20 flex flex-col items-center">
                                    <i class="fas fa-file-invoice-dollar text-5xl mb-6"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-teal-950">Matriks komisi belum mencatat yield transaksi</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Withdrawal Tab -->
    <div id="content-wd" class="tab-content hidden animate-slideUp">
        <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <div class="overflow-x-auto p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 uppercase">
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Nilai Transmisi</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Stempel Waktu</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center">Integritas</th>
                            <th class="pb-8 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Node Perbankan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-sans">
                        @forelse($withdrawals as $wd)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 px-4">
                                <span class="text-lg font-black text-red-500 font-mono tracking-tighter">-SP {{ number_format($wd->amount) }}</span>
                            </td>
                            <td class="py-6 px-4">
                                <span class="text-[10px] font-black text-slate-400 font-mono tracking-tighter">
                                    {{ $wd->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td class="py-6 px-4 text-center">
                                @if($wd->status == 'pending')
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 text-yellow-600 bg-yellow-50 border-yellow-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                                        PROSES
                                    </div>
                                @elseif($wd->status == 'approved')
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 text-emerald-600 bg-emerald-50 border-emerald-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        BERHASIL
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 text-red-600 bg-red-50 border-red-100 text-[9px] font-black uppercase tracking-widest shadow-sm" title="{{ $wd->rejection_reason }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        GAGAL
                                    </div>
                                @endif
                            </td>
                            <td class="py-6 px-4 text-right">
                                <div class="inline-flex flex-col items-end">
                                    <p class="text-[11px] font-black text-teal-950 uppercase tracking-tighter mb-0.5">{{ $wd->bank_name }}</p>
                                    <p class="text-[9px] text-slate-400 font-mono tracking-widest leading-none">{{ $wd->account_number }}</p>
                                    <p class="text-[8px] text-slate-300 font-bold uppercase mt-1">A/N: {{ $wd->account_name }}</p>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-24 text-center">
                                <div class="opacity-20 flex flex-col items-center">
                                    <i class="fas fa-hand-holding-usd text-5xl mb-6"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-teal-950">Belum ada riwayat transmisi dana yang tercatat</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<style>
    .tab-btn.active {
        background: #0d2d35;
        color: #22d3ee;
        box-shadow: 0 15px 35px -5px rgba(13, 45, 53, 0.4);
        transform: translateY(-2px);
        border: 2px solid rgba(34, 211, 238, 0.2);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slideUp {
        animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .icon-glow {
        filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.4));
    }
</style>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        const activeContent = document.getElementById('content-' + tabId);
        activeContent.classList.remove('hidden');
        
        // Re-trigger animation
        activeContent.style.animation = 'none';
        activeContent.offsetHeight; /* trigger reflow */
        activeContent.style.animation = null;

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-teal-900', 'bg-teal-900');
            btn.classList.add('text-slate-400');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-slate-400');
    }
</script>
@endpush
@endsection
