@extends('layouts.admin')

@section('title', 'Hunter Registry Matrix')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Global Registry</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse shadow-[0_0_10px_rgba(20,184,166,0.5)]"></span>
                Official Identification & Authority Clearance
            </p>
        </div>
        <a href="{{ route('admin.hunters.create') }}" class="group relative px-8 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold shadow-xl shadow-slate-200/50 hover:border-cyan-400 hover:text-cyan-600 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-id-card text-teal-500 icon-glow transition-transform group-hover:scale-110"></i>
                Register Node
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Hunter Identity</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Authorization</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Combat Statistics</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Soul Yield</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($users as $user)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="w-16 h-16 rounded-2xl bg-teal-900 border-2 border-teal-800 flex items-center justify-center font-serif font-black text-white text-2xl shadow-lg shadow-teal-950/30 group-hover:scale-105 transition-transform duration-500">
                                    {{ substr($user->username, 0, 1) }}
                                </div>
                                <div class="absolute -bottom-2 -right-2 w-6 h-6 rounded-full bg-cyan-400 border-4 border-white shadow-sm flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1">
                                    {{ $user->username }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <code class="text-[9px] font-black text-slate-400 uppercase tracking-wider font-mono">UID: SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</code>
                                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                    <span class="text-[9px] font-bold text-slate-400 lowercase">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="inline-flex flex-col items-center">
                            <span class="text-3xl font-serif font-black {{ $user->rankTier->color_code ?? 'text-slate-400' }} italic leading-none">
                                {{ $user->rankTier->slug ?? '?' }}
                            </span>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-100 px-3 py-1 rounded-lg mt-1 border border-slate-200/50">
                                {{ $user->jobClass->name ?? 'Initiate' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="w-48 space-y-3">
                            <div class="flex justify-between items-end">
                                <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-widest leading-none">Power Level</span>
                                <span class="text-lg font-black text-teal-900 font-mono leading-none">{{ $user->level }}</span>
                            </div>
                            <div class="relative h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50">
                                @php $expProgress = min(($user->experience / 5000) * 100, 100); @endphp
                                <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-cyan-400 to-teal-500 shadow-[0_0_10px_rgba(34,211,238,0.5)] transition-all duration-1000 ease-out" style="width: {{ $expProgress }}%"></div>
                                <!-- Subtle pulse effect on progress -->
                                <div class="absolute inset-0 bg-white/10 animate-[pulse_2s_infinite]"></div>
                            </div>
                            <p class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter text-right">{{ number_format($user->experience) }} / 5,000 EXP Mapped</p>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="inline-flex flex-col items-end">
                            <p class="text-2xl font-black text-gold-600 font-mono tracking-tighter leading-none mb-1">
                                {{ number_format($user->soul_points) }}
                            </p>
                            <span class="text-[9px] font-black text-gold-400 uppercase tracking-[0.3em]">Soul Points Flow</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.hunters.show', $user) }}" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-id-badge text-sm"></i>
                            </a>
                            <a href="{{ route('admin.hunters.edit', $user) }}" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-teal-900 hover:border-teal-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-user-edit text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $users->links() }}
    </div>
</div>
@endsection
