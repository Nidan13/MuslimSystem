@extends('layouts.admin')

@section('title', 'System Progres Matrix')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Progression Matrix</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Defining Experience Thresholds & Hunter Evolution
            </p>
        </div>
        <a href="{{ route('admin.level-configs.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-stairs text-cyan-400 icon-glow transition-transform group-hover:translate-y-[-2px]"></i>
                Extend Horizon
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Stage</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Required XP</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Cumulative Load</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Attribute Bonus</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($configs as $config)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-xl bg-teal-900 border border-teal-800 flex items-center justify-center font-mono font-black text-white text-lg shadow-lg group-hover:scale-110 transition-transform">
                                {{ $config->level }}
                            </div>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Evolution Stage</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                         <div class="inline-flex items-center gap-3 px-4 py-2 bg-white border-2 border-slate-100 rounded-2xl shadow-sm">
                            <i class="fas fa-bolt text-[10px] text-cyan-500"></i>
                            <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">{{ number_format($config->xp_required) }}</span>
                            <span class="text-[8px] font-black text-slate-300 uppercase">XP TO NEXT</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-[11px] font-bold text-slate-500 font-mono">
                            Σ {{ number_format($config->xp_total_cumulative ?? 0) }} Total
                        </p>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black border border-emerald-100">
                                +{{ $config->stat_points_reward ?? 5 }} AP
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.level-configs.edit', $config) }}" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-sliders text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $configs->links() }}
    </div>
</div>
@endsection
