@extends('layouts.admin')

@section('title', 'Divine Salat Configuration')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Salat Protocols</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Defining Spiritual Rewards & Core Devotion Parameters
            </p>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Identity</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Protocol Name</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Logic Key</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Reward Yield</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Directive</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($prayers as $prayer)
                @php
                    $prayerIcons = [
                        'subuh' => 'fa-feather-pointed',
                        'dhuhur' => 'fa-sun',
                        'ashar' => 'fa-cloud-sun',
                        'maghrib' => 'fa-moon',
                        'isya' => 'fa-stars',
                    ];
                    $icon = $prayerIcons[$prayer->slug] ?? 'fa-pray';
                @endphp
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $icon }} text-xl icon-glow"></i>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">
                            {{ $prayer->name }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <code class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-cyan-600 uppercase tracking-wider">
                            {{ $prayer->slug }}
                        </code>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="inline-flex flex-col items-center">
                            <p class="text-2xl font-black text-gold-600 font-mono tracking-tighter leading-none mb-1">
                                +{{ number_format($prayer->soul_points) }}
                            </p>
                            <span class="text-[8px] font-black text-gold-400 uppercase tracking-widest">SP YIELD</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-xs text-slate-500 font-medium max-w-xs leading-relaxed">
                            {{ $prayer->description }}
                        </p>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.prayers.edit', $prayer) }}" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-sliders text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
