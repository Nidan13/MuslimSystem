@extends('layouts.admin')

@section('title', 'Revenue Analysis')

@section('content')
<div class="space-y-10 animate-fadeIn pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center">
            <div class="w-2 h-14 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full mr-5 shadow-lg shadow-teal-500/20"></div>
            <div>
                <h1 class="text-4xl font-serif font-black text-[#0E5F71] tracking-tight uppercase leading-none">Revenue Insights</h1>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.4em] mt-2">Analisis pendapatan aktivasi & pembagian SHU</p>
            </div>
        </div>

        <!-- Main Total Card (Compact) -->
        <div class="bg-white px-8 py-5 rounded-[32px] border-2 border-slate-50 shadow-sm flex items-center gap-6 group hover:border-[#2C9EB0]/30 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-[#0E5F71]/5 text-[#0E5F71] flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-vault"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Total Transaksi Bruto</p>
                <p class="text-2xl font-black text-[#0E5F71]">Rp {{ number_format($stats['total_bruto'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Core Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Platform Earnings (Admin) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#0E5F71] to-[#154652] rounded-[48px] p-10 text-white shadow-2xl shadow-teal-900/30 group">
            <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-cyan-400/5 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-xl text-cyan-300">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-cyan-400/80">Platform Earnings</h3>
                </div>
                
                <div class="space-y-1">
                    <p class="text-5xl font-black tracking-tight">Rp {{ number_format($stats['total_system_fee'], 0, ',', '.') }}</p>
                    <p class="text-cyan-400/60 text-[10px] font-bold uppercase tracking-widest mt-2">Akumulasi Jatah Admin dari Aktivasi Hunter</p>
                </div>

                <div class="mt-12 flex items-center gap-6">
                    <div class="px-5 py-3 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10">
                        <p class="text-[8px] font-black uppercase tracking-widest text-cyan-400/50 mb-1">Source</p>
                        <p class="text-[10px] font-black uppercase tracking-tight text-white/90">Potongan % Aktivasi</p>
                    </div>
                    <div class="px-5 py-3 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10">
                        <p class="text-[8px] font-black uppercase tracking-widest text-cyan-400/50 mb-1">Status</p>
                        <p class="text-[10px] font-black uppercase tracking-tight text-white/90 italic">Ready to SHU</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hunter Commissions -->
        <div class="bg-white rounded-[48px] p-10 border-2 border-slate-50 shadow-xl shadow-slate-200/50 flex flex-col justify-between group hover:border-amber-100 transition-all">
            <div>
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Hunter Commissions</h3>
                </div>
                
                <div class="space-y-1">
                    <p class="text-5xl font-black tracking-tight text-[#0E5F71]">Rp {{ number_format($stats['total_affiliate_fee'], 0, ',', '.') }}</p>
                    <p class="text-amber-500 text-[10px] font-bold uppercase tracking-widest mt-2 italic px-3 py-1 bg-amber-50 rounded-full inline-block">Bonus Referral Hunter</p>
                </div>
            </div>

            <div class="mt-12 p-6 bg-slate-50/50 rounded-3xl border border-slate-100/50 flex items-center justify-between">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Komisi Keluar</p>
                    <p class="text-sm font-black text-slate-700">Hunter Referral System</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-amber-500 flex items-center justify-center shadow-sm">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- SHU Internal Breakdown Section -->
    <div class="space-y-6">
        <div class="flex items-center gap-4 px-2">
            <div class="w-1.5 h-6 bg-amber-500 rounded-full shadow-[0_0_10px_rgba(245,158,11,0.5)]"></div>
            <h3 class="text-xs font-black text-[#0E5F71] uppercase tracking-[0.3em]">Alokasi SHU (Internal Admin)</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($shu_breakdown as $item)
            <div class="bg-white p-7 rounded-[40px] border-2 border-slate-50 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fas fa-folder-tree"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-tight">{{ $item->name }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-xl font-black text-slate-800 tracking-tight">Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                    <p class="text-[8px] font-bold text-teal-500 bg-teal-50 px-3 py-1 rounded-full inline-block uppercase tracking-widest italic">Dana Terakumulasi</p>
                </div>
            </div>
            @empty
            <!-- Placeholder if no SHU categories yet -->
            <div class="col-span-full py-12 bg-slate-50/50 rounded-[40px] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center opacity-40">
                <i class="fas fa-layer-group text-4xl mb-4 text-slate-300"></i>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Belum ada kategori alokasi aktif</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Transaction Logs Table -->
    <div class="space-y-6">
        <div class="flex items-center gap-4 px-2">
            <div class="w-1.5 h-6 bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
            <h3 class="text-xs font-black text-[#0E5F71] uppercase tracking-[0.3em]">Log Pendapatan Terakhir</h3>
        </div>

        <div class="bg-white rounded-[48px] border-2 border-slate-50 shadow-2xl shadow-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-10 py-8 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu Transaksi</th>
                            <th class="px-10 py-8 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Aktivator (Hunter)</th>
                            <th class="px-10 py-8 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Gross Amount</th>
                            <th class="px-10 py-8 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest text-[#0E5F71]">Income Admin</th>
                            <th class="px-10 py-8 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-10 py-8">
                                <span class="text-sm font-black text-slate-700 block mb-0.5">{{ $log->paid_at->format('d M Y') }}</span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] italic">{{ $log->paid_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-900 to-[#154652] text-white flex items-center justify-center font-serif font-black text-base shadow-lg shadow-teal-900/10">
                                        {{ substr($log->user->username ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-black text-[#0E5F71] uppercase tracking-tight">{{ $log->user->username ?? 'Anonym' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <p class="text-sm font-bold text-slate-400">Rp {{ number_format($log->amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-black text-[#2C9EB0]">Rp {{ number_format($log->system_fee, 0, ',', '.') }}</span>
                                    @php
                                        $percent = ($log->amount > 0) ? ($log->system_fee / $log->amount * 100) : 0;
                                    @endphp
                                    <span class="px-2 py-0.5 bg-teal-50 text-[#0E5F71] rounded-lg text-[9px] font-black border border-teal-100/50">
                                        {{ round($percent, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <span class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-2xl text-[9px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm italic">
                                    Processed
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-10 py-32 text-center opacity-20 capitalize">
                                <i class="fas fa-receipt text-6xl mb-6 block"></i>
                                <span class="text-[10px] font-black tracking-[0.3em] uppercase">Arsip pendapatan masih kosong</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.8s ease-out forwards;
}
</style>
@endsection

