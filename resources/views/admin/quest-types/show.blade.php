@extends('layouts.admin')

@section('title', 'Taxonomy Detail: ' . $questType->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <div class="flex justify-between items-end">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.quest-types.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">{{ $questType->name }}</h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                    Classification: {{ $questType->slug }} <span class="text-slate-200">|</span> ID: TYPE-{{ str_pad($questType->id, 3, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.quest-types.edit', $questType) }}" class="px-8 py-4 bg-teal-900 text-white rounded-2xl font-serif font-black uppercase tracking-widest text-xs shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all group active:scale-95">
                <i class="fas fa-edit mr-3 text-cyan-400 group-hover:rotate-12 transition-transform"></i>
                Modify Taxonomy
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Definition -->
        <div class="lg:col-span-2 space-y-10">
            <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50 relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-50 rounded-full blur-[100px] pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em] mb-10">Taxonomy Definition</h3>
                <p class="text-xl font-serif font-black text-teal-900 leading-relaxed italic border-l-4 border-cyan-400 pl-8">
                    "{{ $questType->description ?: 'No operational parameters defined for this mission classification.' }}"
                </p>
            </div>

            <!-- Linked Protocols -->
            <div class="glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
                <div class="p-10 border-b-2 border-slate-50 flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em]">Bonded Protocols</h3>
                    <span class="px-4 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-[10px] font-black text-teal-900 tracking-widest">{{ $questType->quests->count() }} ACTIVE MISSIONS</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 uppercase">
                                <th class="px-10 py-6 text-[9px] font-black text-teal-900/40 tracking-widest">Mission Signature</th>
                                <th class="px-10 py-6 text-[9px] font-black text-teal-900/40 tracking-widest text-center">Reward Yield</th>
                                <th class="px-10 py-6 text-[9px] font-black text-teal-900/40 tracking-widest text-right">Synchronization</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-50">
                            @forelse($questType->quests as $quest)
                            <tr class="group hover:bg-slate-50/50 transition-all">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_rgba(34,211,238,0.5)]"></div>
                                        <span class="text-lg font-serif font-black text-teal-900 tracking-tight group-hover:text-cyan-600 transition-colors uppercase italic">{{ $quest->title }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                     <div class="flex flex-col">
                                        <span class="text-xs font-black text-teal-900 font-mono">{{ number_format($quest->reward_exp) }} EXP</span>
                                        <span class="text-[8px] font-bold text-slate-300 uppercase">Growth Factor</span>
                                     </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600 text-[10px] font-black uppercase tracking-widest">
                                        <i class="fas fa-check-double mr-2"></i>
                                        Active
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-10 py-20 text-center opacity-30 italic font-medium uppercase tracking-[0.2em] text-teal-900">
                                    No active protocols registered under this taxonomy.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Metric Sidebar -->
        <div class="space-y-10">
            <div class="glass-panel p-12 rounded-[50px] bg-teal-900 border-2 border-white/5 relative overflow-hidden group flex flex-col items-center justify-center text-center">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-400/10 rounded-full blur-[80px]"></div>
                <h3 class="text-[9px] font-black text-cyan-400/60 uppercase tracking-[0.5em] mb-10">Total Utilization</h3>
                <div class="text-8xl font-serif font-black text-white tracking-tighter mb-4 drop-shadow-2xl group-hover:scale-110 transition-transform duration-700">
                    {{ $questType->quests->count() }}
                </div>
                <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em]">Protocols Manifested</p>
            </div>
            
            <div class="p-8 rounded-[40px] bg-white border-2 border-slate-100 shadow-sm text-center">
                <i class="fas fa-microchip text-3xl text-teal-900 mb-4 opacity-10"></i>
                <p class="text-[9px] font-black text-slate-300 uppercase italic">System logic stabilized at node level 0.04</p>
            </div>
        </div>
    </div>
</div>
@endsection
