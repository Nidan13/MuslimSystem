@extends('layouts.admin')

@section('title', 'Dungeon Classifications')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Gate Classifications</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Defining Rift Parameters & Participant Limits
            </p>
        </div>
        <a href="{{ route('admin.dungeon-types.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <svg class="w-5 h-5 icon-glow text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Initialize Classification
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black uppercase text-teal-900/40 tracking-[0.3em]">ID Node</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase text-teal-900/40 tracking-[0.3em]">Classification Name</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase text-teal-900/40 tracking-[0.3em]">Slug Identifier</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase text-teal-900/40 tracking-[0.3em]">Participant Capacity</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase text-teal-900/40 tracking-[0.3em]">Active Instances</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100">
                @foreach($types as $type)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6 text-slate-400 font-mono text-xs">#DT-0{{ $type->id }}</td>
                    <td class="px-8 py-6">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">
                            {{ $type->name }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <code class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-cyan-600 uppercase tracking-wider">
                            {{ $type->slug }}
                        </code>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-black text-teal-900 font-mono">{{ $type->max_participants }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Hunters</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-teal-50 border-2 border-teal-100 text-[9px] font-black text-teal-700 uppercase tracking-widest shadow-sm">
                            {{ $type->dungeons_count ?? $type->dungeons()->count() }} Dungeons
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.dungeon-types.edit', $type) }}" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.dungeon-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this classification node?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
