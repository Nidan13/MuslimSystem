@extends('layouts.admin')

@section('title', 'Detail Afiliasi: ' . $user->username)

@section('content')
<div class="w-full space-y-8 animate-fadeIn pb-20">
    
    <!-- Clean Header Profile -->
    <div class="bg-white rounded-[32px] border-2 border-slate-100 shadow-xl shadow-slate-200/40 p-10 lg:p-12">
        <div class="flex flex-col lg:flex-row items-center gap-10">
            <!-- Simplified Avatar -->
            <div class="relative">
                <div class="w-32 h-32 rounded-3xl bg-teal-950 flex items-center justify-center text-5xl font-serif font-black text-white shadow-lg">
                    {{ substr($user->username, 0, 1) }}
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-xl border-4 border-white flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-check text-[10px]"></i>
                </div>
            </div>
            
            <div class="flex-1 text-center lg:text-left">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-2">
                    <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">{{ $user->username }}</h1>
                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest w-fit mx-auto lg:mx-0 shadow-sm leading-none">ID: SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex justify-center lg:justify-start items-center gap-3 mb-6">
                    <i class="fas fa-fingerprint text-cyan-500"></i>
                    REFERRAL CODE: <code class="text-teal-900 font-mono text-sm bg-slate-50 px-3 py-1 rounded-lg border border-slate-100">{{ $user->referral_code }}</code>
                </p>

                <div class="flex flex-wrap justify-center lg:justify-start gap-3">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                        <i class="fas fa-calendar-alt text-cyan-500"></i>
                        Bergabung: {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            <!-- Header Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full lg:w-auto">
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-center min-w-[180px]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Saldo Aktif</p>
                    <div class="flex items-baseline justify-center gap-1">
                        <span class="text-[10px] font-black text-cyan-500 italic">Rp</span>
                        <p class="text-2xl font-black text-teal-950 font-mono tracking-tighter">{{ number_format($balance) }}</p>
                    </div>
                </div>
                <div class="p-6 bg-teal-950 rounded-3xl text-center text-white shadow-lg min-w-[180px]">
                    <p class="text-[9px] font-black text-cyan-400/60 uppercase tracking-widest mb-2">Total Referral</p>
                    <p class="text-2xl font-black font-mono tracking-tighter">{{ $referrals->count() }} <span class="text-[10px] font-serif italic text-cyan-400">Hunter</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 p-1.5 bg-white rounded-2xl border border-slate-100 w-fit mx-auto lg:mx-0 shadow-sm">
        <button onclick="switchTab('detail')" id="tab-detail" class="tab-btn active px-8 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
            <i class="fas fa-user-shield text-xs"></i> PROFIL
        </button>
        <button onclick="switchTab('referral')" id="tab-referral" class="tab-btn px-8 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400 hover:text-teal-950 flex items-center gap-2">
            <i class="fas fa-users text-xs"></i> REFERRAL
        </button>
        <button onclick="switchTab('komisi')" id="tab-komisi" class="tab-btn px-8 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400 hover:text-teal-950 flex items-center gap-2">
            <i class="fas fa-bolt text-xs"></i> KOMISI
        </button>
        <button onclick="switchTab('wd')" id="tab-wd" class="tab-btn px-8 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400 hover:text-teal-950 flex items-center gap-2">
            <i class="fas fa-history text-xs"></i> RIWAYAT WD
        </button>
    </div>

    <!-- Tab Contents -->
    <div id="content-detail" class="tab-content space-y-8 animate-slideUp">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Summary Panel -->
            <div class="lg:col-span-2 bg-white p-10 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden">
                <h3 class="text-[11px] font-black text-teal-950 uppercase tracking-widest mb-10 flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-cyan-500 rounded-full"></span>
                    Ringkasan Statistik
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Total Komisi</span>
                        <div class="flex items-baseline gap-1.5">
                             <span class="text-[10px] font-black text-cyan-600 italic">Rp</span>
                             <span class="text-2xl font-black text-teal-900 font-mono tracking-tighter">{{ number_format($totalCommission) }}</span>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Total Penarikan</span>
                        <div class="flex items-baseline gap-1.5">
                             <span class="text-[10px] font-black text-red-500 italic">Rp</span>
                             <span class="text-2xl font-black text-teal-900 font-mono tracking-tighter">{{ number_format($totalWithdrawn) }}</span>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Referral Aktif</span>
                        <div class="flex items-baseline gap-2">
                             <span class="text-2xl font-black text-teal-900 font-mono tracking-tighter">{{ $referrals->where('payments_count', '>', 0)->count() }}</span>
                             <span class="text-[9px] font-black text-teal-500 uppercase italic">HUB TERKONEKSI</span>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tanggal Bergabung</span>
                        <span class="text-xl font-black text-teal-950 font-serif uppercase leading-none block pt-1">{{ $user->created_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="bg-teal-950 p-10 rounded-[32px] text-white flex flex-col justify-between shadow-lg">
                <div>
                   <h3 class="text-[11px] font-black text-cyan-400 uppercase tracking-widest mb-10 border-b border-white/5 pb-6">
                       AKSES LOGS
                   </h3>
                   
                   <div class="space-y-10">
                       <div class="flex items-center gap-6">
                           <div class="w-14 h-14 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-cyan-400">
                               <i class="fas fa-file-invoice-dollar text-xl"></i>
                           </div>
                           <div>
                               <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Log Komisi</p>
                               <div class="flex items-baseline gap-1.5">
                                   <p class="text-3xl font-black font-mono tracking-tighter">{{ $user->commissions_count }}</p>
                                   <span class="text-[9px] font-black text-cyan-400 italic">ENTRY</span>
                               </div>
                           </div>
                       </div>

                       <div class="flex items-center gap-6">
                           <div class="w-14 h-14 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-cyan-400">
                               <i class="fas fa-wallet text-xl"></i>
                           </div>
                           <div>
                               <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Riwayat WD</p>
                               <div class="flex items-baseline gap-1.5">
                                   <p class="text-3xl font-black font-mono tracking-tighter">{{ $user->withdrawals_count }}</p>
                                   <span class="text-[9px] font-black text-cyan-400 italic">ENTRY</span>
                               </div>
                           </div>
                       </div>
                   </div>
                </div>
                <div class="mt-auto pt-8 border-t border-white/5 flex items-center justify-between text-white/20">
                    <span class="text-[9px] font-black uppercase tracking-widest font-mono">SECURED_NODE</span>
                    <i class="fas fa-shield-alt text-xs"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Tab -->
    <div id="content-referral" class="tab-content hidden animate-slideUp">
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
             <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                 <h3 class="text-[11px] font-black text-teal-950 uppercase tracking-widest flex items-center gap-3">
                     <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                     Daftar Referal Hunter
                 </h3>
                 <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Total: {{ $referrals->count() }}</span>
             </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">Hunter</th>
                            <th class="py-5 px-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                            <th class="py-5 px-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Tgl Join</th>
                            <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($referrals as $reff)
                        <tr class="group hover:bg-slate-50/30 transition-all">
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-xl bg-teal-900 text-white flex items-center justify-center font-black shadow-sm">
                                        {{ substr($reff->username, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">{{ $reff->username }}</span>
                                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest font-mono">UID: SN-{{ $reff->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <span class="text-xs font-medium text-slate-500 lowercase">{{ $reff->email }}</span>
                            </td>
                            <td class="py-6 px-4 text-center">
                                <span class="text-[10px] font-black text-slate-400 font-mono tracking-tight bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">{{ $reff->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-6 px-8 text-right whitespace-nowrap">
                                <a href="{{ route('admin.hunters.show', $reff) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-[9px] font-black text-teal-900 uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm">
                                    DETAIL
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center text-slate-300">
                                <i class="fas fa-users-slash text-4xl mb-4 opacity-20"></i>
                                <p class="text-[9px] font-black uppercase tracking-widest">Belum ada pasukkan yang terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- History Tab (Commissions) -->
    <div id="content-komisi" class="tab-content hidden animate-slideUp">
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
             <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                 <h3 class="text-[11px] font-black text-teal-950 uppercase tracking-widest flex items-center gap-3">
                     <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                     Daftar Yield Komisi
                 </h3>
                 <span class="text-[9px] font-black text-cyan-600 uppercase tracking-widest font-mono px-3 py-1.5 bg-cyan-50 rounded-lg">TOTAL: Rp {{ number_format($totalCommission) }}</span>
             </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">Hunter (Sumber)</th>
                            <th class="py-5 px-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                            <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($commissions as $comm)
                        <tr class="group hover:bg-cyan-50/5 transition-all">
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-black border border-cyan-100">
                                        {{ substr($comm->referredUser->username ?? '?', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors font-serif">
                                            {{ $comm->referredUser->username ?? 'Unknown' }}
                                        </span>
                                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">PAY_ID: #{{ $comm->payment_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <span class="text-[10px] font-black text-slate-400 font-mono tracking-tight bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                    {{ $comm->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td class="py-6 px-8 text-right">
                                <div class="inline-flex items-baseline gap-1">
                                    <span class="text-[9px] font-black text-cyan-500 italic uppercase">+Rp</span>
                                    <span class="text-xl font-black text-teal-950 font-mono tracking-tighter">{{ number_format($comm->amount) }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-20 text-center text-slate-300">
                                <i class="fas fa-bolt text-4xl mb-4 opacity-20"></i>
                                <p class="text-[9px] font-black uppercase tracking-widest">Belum ada data komisi masuk</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Withdrawal History Tab -->
    <div id="content-wd" class="tab-content hidden animate-slideUp">
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
             <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                 <h3 class="text-[11px] font-black text-teal-950 uppercase tracking-widest flex items-center gap-3">
                     <span class="w-2 h-2 rounded-full bg-red-500"></span>
                     Riwayat Penarikan Saldo
                 </h3>
                 <span class="text-[9px] font-black text-red-500 uppercase tracking-widest font-mono px-3 py-1.5 bg-red-50 rounded-lg">TOTAL WD: Rp {{ number_format($totalWithdrawn) }}</span>
             </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                            <th class="py-5 px-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                            <th class="py-5 px-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Tujuan Bank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($withdrawals as $wd)
                        <tr class="group hover:bg-red-50/5 transition-all">
                            <td class="py-6 px-8">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-[9px] font-black text-red-500 italic uppercase">-Rp</span>
                                    <span class="text-xl font-black text-teal-950 font-mono tracking-tighter">{{ number_format($wd->amount) }}</span>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <span class="text-[10px] font-black text-slate-400 font-mono tracking-tight bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                    {{ $wd->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-6 px-4 text-center">
                                @if($wd->status == 'pending')
                                    <span class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 text-[8px] font-black uppercase tracking-widest">PENDING</span>
                                @elseif($wd->status == 'approved')
                                    <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 text-[8px] font-black uppercase tracking-widest">APPROVED</span>
                                @else
                                    <span class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[8px] font-black uppercase tracking-widest">REJECTED</span>
                                @endif
                            </td>
                            <td class="py-6 px-8 text-right">
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-black text-teal-950 uppercase tracking-tight">{{ $wd->bank_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $wd->account_number }}</span>
                                    <span class="text-[8px] text-slate-300 uppercase tracking-widest mt-0.5">A/N: {{ $wd->account_name }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center text-slate-300">
                                <i class="fas fa-history text-4xl mb-4 opacity-20"></i>
                                <p class="text-[9px] font-black uppercase tracking-widest">Belum ada riwayat penarikan</p>
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
        box-shadow: 0 10px 20px -5px rgba(13, 45, 53, 0.2);
        border: 1px solid rgba(34, 211, 238, 0.2);
        transform: translateY(-1px);
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
        animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        const activeContent = document.getElementById('content-' + tabId);
        activeContent.classList.remove('hidden');
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-teal-950');
            btn.classList.add('text-slate-400');
        });

        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-slate-400');
    }
</script>
@endpush
@endsection
