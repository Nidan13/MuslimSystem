@extends('layouts.admin')

@section('title', 'Manajemen Afiliasi')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Daftar Afiliasi</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Monitoring Performa & Komisi Hunter
            </p>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px] bg-white border-2 border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Hunter</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Kode Referral</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Total Referral</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Total Komisi</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($affiliates as $affiliate)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-xl bg-teal-900 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg">
                                {{ substr($affiliate->username, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1">
                                    {{ $affiliate->username }}
                                </h3>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider font-mono">UID: SN-{{ str_pad($affiliate->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <code class="px-3 py-1 bg-cyan-50 border border-cyan-100 rounded-lg text-xs font-black text-cyan-700 tracking-widest">{{ $affiliate->referral_code }}</code>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="text-lg font-black text-teal-900 font-mono">{{ $affiliate->referrals_count }}</span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="inline-flex flex-col items-end">
                            <p class="text-xl font-black text-gold-600 font-mono tracking-tighter leading-none mb-1">
                                SP {{ number_format($affiliate->commissions_sum_amount ?? 0) }}
                            </p>
                            <span class="text-[8px] font-black text-gold-400 uppercase tracking-[0.2em]">Total Earned</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-cyan-600 hover:border-cyan-200 rounded-xl transition-all shadow-sm">
                            <i class="fas fa-eye text-xs"></i>
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $affiliates->links() }}
    </div>
</div>
@endsection
