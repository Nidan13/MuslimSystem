@extends('layouts.admin')

@section('title', 'Spiritual Circles Registry')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Covenant Registry</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Managing Hunter Collectives & Sacred Bonds
            </p>
        </div>
        <a href="{{ route('admin.circles.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-circle-nodes text-cyan-400 icon-glow transition-transform group-hover:scale-110"></i>
                Form Covenant
            </span>
        </a>
    </div>

    <div class="glass-panel overflow-hidden rounded-[40px]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-100 bg-slate-50/50 uppercase">
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Emblem</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Covenant Name</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em]">Collective Info</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-center">Membership</th>
                    <th class="px-8 py-6 text-[10px] font-black text-teal-900/40 tracking-[0.3em] text-right">Synchronization</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 font-sans">
                @forelse($circles as $circle)
                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                    <td class="px-8 py-6">
                        <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-200 overflow-hidden shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform">
                            @if($circle->icon_path)
                                <img src="{{ asset('storage/' . $circle->icon_path) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-users-rays text-xl opacity-40 group-hover:opacity-100 transition-opacity"></i>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                            {{ $circle->name }}
                        </span>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">ID: CIR-{{ str_pad($circle->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-xs text-slate-500 font-medium max-w-xs leading-relaxed italic line-clamp-1">
                            {{ $circle->description ?? 'No historical record for this covenant...' }}
                        </p>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 border-slate-100 bg-white text-teal-900 text-xs font-black shadow-sm">
                            <i class="fas fa-id-badge text-[10px] text-cyan-500"></i>
                            {{ $circle->members_count ?? $circle->members()->count() }}
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.circles.show', $circle) }}" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-teal-600 hover:border-teal-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('admin.circles.edit', $circle) }}" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm">
                                <i class="fas fa-sliders text-sm"></i>
                            </a>
                            <form action="{{ route('admin.circles.destroy', $circle) }}" method="POST" class="inline" onsubmit="return confirm('Dismantle this covenant protocol?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center opacity-40">
                            <i class="fas fa-users-slash text-5xl text-slate-200 mb-4"></i>
                            <p class="text-teal-900 font-serif font-black text-lg italic uppercase tracking-widest">No Covenants Manifested</p>
                            <p class="text-[9px] text-slate-400 mt-2 uppercase tracking-[0.4em]">System matrix is void of hunter collectives</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-10 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $circles->links() }}
    </div>
</div>
@endsection
