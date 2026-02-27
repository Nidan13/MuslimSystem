@extends('layouts.admin')

@section('title', $user->username . '\'s Ritual Manifestations')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">{{ $user->username }} <span class="text-cyan-500 font-sans tracking-tight ml-2">Node</span></h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Inspecting Localized Daily Protocols
            </p>
        </div>
        <a href="{{ route('admin.daily-tasks.users') }}" class="group relative px-8 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold shadow-xl shadow-slate-200/50 hover:border-cyan-400 hover:text-cyan-600 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-arrow-left text-teal-500 transition-transform group-hover:-translate-x-1"></i>
                Custom Registry
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Core</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Ritual Name</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Directive</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">SP Yield</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Manifested Date</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @forelse($tasks as $task)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 border-2 border-slate-50 flex items-center justify-center text-slate-400 shadow-inner group-hover:bg-white group-hover:border-cyan-100 transition-all duration-500">
                            @if(Str::startsWith($task->icon, 'fa') || Str::contains($task->icon, 'fa-'))
                                <i class="{{ $task->icon }} text-lg group-hover:text-cyan-500 transition-colors"></i>
                            @else
                                <span class="text-xl opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all">{{ $task->icon }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1">
                            {{ $task->name }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-xs text-slate-500 font-medium max-w-xs leading-relaxed italic">
                            {{ $task->description }}
                        </p>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="inline-flex flex-col items-end">
                            <p class="text-2xl font-black text-gold-600 font-mono tracking-tighter leading-none mb-1">
                                +{{ number_format($task->soul_points) }}
                            </p>
                            <span class="text-[8px] font-black text-gold-400 uppercase tracking-widest leading-none">SP</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 {{ $task->is_active ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-slate-400 bg-slate-50 border-slate-200' }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $task->is_active ? 'bg-emerald-400 shadow-[0_0_5px_rgba(52,211,153,0.5)]' : 'bg-slate-300' }}"></span>
                            {{ $task->is_active ? 'Active' : 'Inactive' }}
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                         <span class="text-xs text-slate-400 font-mono font-bold tracking-tighter">{{ $task->created_at->format('Y.m.d') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center">
                         <div class="flex flex-col items-center opacity-40">
                            <i class="fas fa-ghost text-5xl text-slate-200 mb-4"></i>
                            <p class="text-teal-900 font-serif font-black text-lg italic uppercase tracking-widest">No Personal Rituals Found</p>
                            <p class="text-[9px] text-slate-400 mt-2 uppercase tracking-[0.4em]">This hunter has not manifested any localized protocols</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
