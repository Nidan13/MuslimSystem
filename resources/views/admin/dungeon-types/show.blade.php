@extends('layouts.admin')

@section('title', 'Classification Detail: ' . $dungeonType->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <div class="flex justify-between items-end">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.dungeon-types.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">{{ $dungeonType->name }}</h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                    Matrix Classification <span class="text-slate-200">|</span> ID: TYPE-{{ str_pad($dungeonType->id, 3, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.dungeon-types.edit', $dungeonType) }}" class="px-8 py-4 bg-teal-900 text-white rounded-2xl font-serif font-black uppercase tracking-widest text-xs shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all group active:scale-95">
                <i class="fas fa-cog mr-3 text-cyan-400 group-hover:rotate-90 transition-transform"></i>
                Modify Logic
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Capacity Stat -->
        <div class="glass-panel p-12 rounded-[50px] bg-teal-900 border-2 border-white/5 relative overflow-hidden group flex flex-col items-center justify-center text-center">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-400/10 rounded-full blur-[80px]"></div>
            <h3 class="text-[9px] font-black text-cyan-400/60 uppercase tracking-[0.5em] mb-10">Participant Threshold</h3>
            <div class="text-8xl font-serif font-black text-white tracking-tighter mb-4 drop-shadow-2xl group-hover:scale-110 transition-transform duration-700">
                {{ $dungeonType->max_participants }}
            </div>
            <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em]">Authorized Units Per Raid</p>
        </div>

        <!-- Associated Instances -->
        <div class="lg:col-span-2 glass-panel overflow-hidden rounded-[50px] bg-white border-2 border-slate-50 shadow-xl">
            <div class="p-10 border-b-2 border-slate-50 flex justify-between items-center">
                <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em]">Manifestations Bonded</h3>
                <span class="px-4 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-[10px] font-black text-teal-900 tracking-widest">{{ $dungeonType->dungeons->count() }} ACTIVE NODES</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 uppercase">
                            <th class="px-10 py-6 text-[9px] font-black text-teal-900/40 tracking-widest">Dungeon Signature</th>
                            <th class="px-10 py-6 text-[9px] font-black text-teal-900/40 tracking-widest text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-50">
                        @forelse($dungeonType->dungeons as $dungeon)
                        <tr class="group hover:bg-slate-50/50 transition-all">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_rgba(34,211,238,0.5)]"></div>
                                    <span class="text-lg font-serif font-black text-teal-900 tracking-tight group-hover:text-cyan-600 transition-colors uppercase italic">{{ $dungeon->name }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest">
                                    <i class="fas fa-check-double mr-2"></i>
                                    Online
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-10 py-20 text-center opacity-30 italic font-medium uppercase tracking-[0.2em] text-teal-900">
                                No active dungeon nodes manifested for this classification.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
