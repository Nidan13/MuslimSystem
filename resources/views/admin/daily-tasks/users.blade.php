@extends('layouts.admin')

@section('title', 'Hunter Custom Discipline Matrix')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Custom Registry</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Monitoring Personalized Protocols Manifested by Hunters
            </p>
        </div>
        <a href="{{ route('admin.daily-tasks.index') }}" class="group relative px-8 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold shadow-xl shadow-slate-200/50 hover:border-cyan-400 hover:text-cyan-600 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-arrow-left text-teal-500 transition-transform group-hover:-translate-x-1"></i>
                System Rituals
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Hunter Identity</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Communication Node</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Protocol Count</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @foreach($users as $user)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-105 transition-transform duration-500">
                                 {{ substr($user->username, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                                    {{ $user->username }}
                                </h3>
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">Authorized Hunter</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <code class="text-[10px] font-black text-slate-400 lowercase font-mono">{{ $user->email }}</code>
                    </td>
                    <td class="px-8 py-6 text-center">
                         <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-xl bg-cyan-50 border-2 border-cyan-100 text-cyan-600 text-xs font-black shadow-sm">
                            {{ $user->daily_tasks_count }} <span class="ml-1 text-[8px] opacity-60">Tasks</span>
                         </span>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <a href="{{ route('admin.daily-tasks.user-tasks', $user) }}" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-teal-900 text-white font-serif font-black uppercase tracking-widest text-[10px] hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 group/btn active:scale-95">
                            Audit Matrix
                            <i class="fas fa-chevron-right text-cyan-400 group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
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
