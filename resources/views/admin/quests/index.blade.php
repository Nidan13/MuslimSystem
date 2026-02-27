@extends('layouts.admin')

@section('title', 'System Mission Protocols')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Mission Mandates</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Active System Directives & Hunter Tasks
            </p>
        </div>
        <a href="{{ route('admin.quests.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-plus text-cyan-400 icon-glow transition-transform group-hover:rotate-90"></i>
                Initialize Mandate
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Designation</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Mandate Details</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Authorization</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Yield (Rewards)</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Temporal Flow</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($quests as $quest)
                @php
                    $typeColors = [
                        'daily' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                        'hidden' => 'text-indigo-600 bg-indigo-50 border-indigo-100',
                        'special' => 'text-amber-600 bg-amber-50 border-amber-100',
                        'raid' => 'text-rose-600 bg-rose-50 border-rose-100',
                    ];
                    $slug = $quest->questType->slug ?? 'default';
                    $colorClass = $typeColors[$slug] ?? 'text-slate-600 bg-slate-50 border-slate-100';
                @endphp
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6 align-top">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl border-2 {{ $colorClass }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                            {{ $quest->questType->name ?? 'Protocol' }}
                        </span>
                    </td>
                    <td class="px-8 py-6 align-top">
                        <div class="max-w-md">
                            <h3 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors mb-1 leading-none">
                                {{ $quest->title }}
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed h-8">
                                {{ $quest->description }}
                            </p>
                            
                            @if(count($quest->requirements ?? []) > 0)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach(collect($quest->requirements)->take(4) as $type => $target)
                                <span class="inline-flex items-center gap-2 text-[8px] font-black text-slate-400 uppercase bg-white border border-slate-100 px-2.5 py-1 rounded-lg">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                    {{ $target }} {{ str_replace('_', ' ', $type) }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6 align-top text-center">
                        @if($quest->rankTier)
                            <div class="flex flex-col items-center">
                                <span class="text-2xl font-serif font-black {{ $quest->rankTier->color_code ?? 'text-teal-900' }} italic">
                                    {{ $quest->rankTier->slug }}
                                </span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Min. Clearance</span>
                            </div>
                        @else
                            <span class="text-[9px] font-black text-slate-300 uppercase italic">Unrestricted</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 align-top text-right">
                        <div class="space-y-1">
                            <p class="text-lg font-black text-gold-600 font-mono tracking-tighter leading-none">
                                +{{ number_format($quest->reward_soul_points) }} <span class="text-[8px] uppercase font-sans">SP</span>
                            </p>
                            <p class="text-[10px] font-black text-cyan-500 font-mono tracking-tighter uppercase">
                                +{{ number_format($quest->reward_exp) }} EXP
                            </p>
                        </div>
                    </td>
                    <td class="px-8 py-6 align-top">
                        <div class="flex flex-col gap-2">
                            @if($quest->start_time)
                                <div class="flex items-center gap-2 text-[9px] font-black text-emerald-600 uppercase tracking-wider bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100">
                                    <i class="fas fa-clock text-[10px] animate-pulse"></i>
                                    {{ \Carbon\Carbon::parse($quest->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($quest->end_time)->format('H:i') }}
                                </div>
                            @elseif($quest->expires_at)
                                <div class="text-[8px] font-bold text-slate-400 uppercase leading-tight space-y-1">
                                    <p class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Initiated: {{ $quest->created_at->format('d M') }}</p>
                                    <p class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Expires: {{ $quest->expires_at->format('d M') }}</p>
                                </div>
                            @else
                                <span class="inline-block px-3 py-1 bg-slate-100 rounded-lg text-[9px] font-black text-slate-400 uppercase italic">Eternal protocol</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right align-top whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.quests.edit', $quest) }}" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.quests.destroy', $quest) }}" method="POST" class="inline" onsubmit="return confirm('Suspend this mandate protocol?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $quests->links() }}
    </div>
</div>
@endsection
