@extends('layouts.admin')

@section('title', 'Spiritual Sync Logs')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Ritual Activity Logs</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Monitoring Sacred Synchronizations & Hunter Petitions
            </p>
        </div>
        <div class="flex gap-4">
             <div class="px-6 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold shadow-sm">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Logs</span>
                <span class="text-xl font-mono">{{ number_format($logs->total()) }}</span>
            </div>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Temporal Node</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Hunter Identity</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Ritual Protocol</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Reward Yield</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Verification</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @forelse($logs as $log)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-teal-900 font-mono">{{ $log->created_at->format('H:i:s') }}</span>
                            <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter">{{ $log->created_at->format('Y.m.d') }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-teal-900 flex items-center justify-center font-serif font-black text-white text-[10px] shadow-lg group-hover:scale-110 transition-transform">
                                 {{ substr($log->user->username ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                                    {{ $log->user->username ?? 'LOST NODE' }}
                                </h3>
                                <code class="text-[8px] font-black text-slate-300 lowercase mt-1 block">{{ $log->user->email ?? 'unknown@matrix' }}</code>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-50 flex items-center justify-center text-slate-400">
                                <i class="fas {{ $log->prayer->icon ?? 'fa-pray' }} text-xs"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">{{ $log->prayer->name ?? 'UNKNOWN RITUAL' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                         <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-gold-50 border-2 border-gold-100 text-gold-600 text-[10px] font-black shadow-sm">
                            +{{ number_format($log->points_earned) }} SP
                         </span>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                         <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-[8px] font-black text-emerald-600 uppercase tracking-widest">
                            <i class="fas fa-check-double text-[8px]"></i>
                            Authorized
                         </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                         <div class="flex flex-col items-center opacity-40">
                            <i class="fas fa-clock-rotate-left text-5xl text-slate-200 mb-4"></i>
                            <p class="text-teal-900 font-serif font-black text-lg italic uppercase tracking-widest">No Ritual Data Found</p>
                            <p class="text-[9px] text-slate-400 mt-2 uppercase tracking-[0.4em]">System logs are clean of recent spiritual synchronizations</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $logs->links() }}
    </div>
</div>
@endsection
