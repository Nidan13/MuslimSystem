@extends('layouts.admin')

@section('title', 'System Matrix Event Logs')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Environmental Logs</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Tracking Sub-system Interactions & Admin Directives
            </p>
        </div>
        <div class="flex gap-4">
             <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Search Matrix Event..." class="px-6 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold placeholder-slate-300 outline-none focus:border-cyan-400 transition-all text-xs w-64 shadow-sm">
                <button type="submit" class="p-4 bg-teal-900 text-cyan-400 rounded-2xl hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20">
                    <i class="fas fa-search text-sm"></i>
                </button>
             </form>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Temporal Node</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Subject Identifier</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Event Directive</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Target Matrix</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Authorization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @forelse($logs as $log)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6 text-slate-400 font-mono text-[10px] font-bold">
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-50 flex items-center justify-center text-teal-900 text-[10px] font-black uppercase">
                                {{ substr($log->causer->username ?? 'SYS', 0, 2) }}
                            </div>
                            <span class="text-xs font-black text-teal-900 uppercase tracking-tight">{{ $log->causer->username ?? 'Master System' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 rounded-lg {{ str_contains($log->description, 'delete') ? 'bg-red-50 text-red-600 border-red-100' : (str_contains($log->description, 'update') ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-cyan-50 text-cyan-600 border-cyan-100') }} border text-[9px] font-black uppercase tracking-widest">
                            {{ $log->description }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                         <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-tighter">{{ class_basename($log->subject_type) }}</span>
                            <span class="text-[9px] font-mono text-slate-300">#NODE-{{ $log->subject_id }}</span>
                         </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                         <div class="flex items-center justify-end gap-2">
                             @if($log->properties && count($log->properties) > 0)
                                <button onclick="alert('Raw Data: ' + JSON.stringify({{ json_encode($log->properties) }}))" class="p-2.5 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-xl transition-all shadow-sm">
                                    <i class="fas fa-terminal text-[10px]"></i>
                                </button>
                             @endif
                         </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                         <div class="flex flex-col items-center opacity-40">
                            <i class="fas fa-satellite-dish text-5xl text-slate-200 mb-4"></i>
                            <p class="text-teal-900 font-serif font-black text-lg italic uppercase tracking-widest">No Event Data Manifested</p>
                            <p class="text-[9px] text-slate-400 mt-2 uppercase tracking-[0.4em]">System environmental logs are empty</p>
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
