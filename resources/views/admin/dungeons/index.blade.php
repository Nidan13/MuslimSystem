@extends('layouts.admin')

@section('title', 'Dungeon Gates')

@section('content')
<div class="flex justify-between items-end mb-12">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">System Dungeons</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse shadow-[0_0_10px_rgba(248,113,113,0.5)]"></span>
            Anomaly Detection & Raid Protocols
        </p>
    </div>
    <a href="{{ route('admin.dungeons.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
        <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif">
            <svg class="w-5 h-5 icon-glow text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            OPEN GATE PROTOCOL
        </span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse($dungeons as $dungeon)
    <div class="glass-panel p-8 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <!-- Tier Overlay -->
        <div class="absolute -right-4 -top-6 text-8xl font-serif font-black {{ $dungeon->rankTier->color_code ?? 'text-slate-200' }} opacity-10 italic select-none">
            {{ $dungeon->rankTier->slug ?? '?' }}
        </div>
        
        <div class="flex justify-between items-start mb-8 relative z-10">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-slate-100 border-2 border-slate-200 text-[10px] font-black text-teal-900 uppercase tracking-widest">
                {{ $dungeon->dungeonType->name ?? 'UNKNOWN' }}
            </span>
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">REQ. LVL</span>
                <span class="text-xl font-bold font-mono text-teal-900 tracking-tighter">{{ $dungeon->min_level_requirement }}</span>
            </div>
        </div>

        <h3 class="text-2xl font-serif font-black text-teal-900 mb-2 tracking-tight group-hover:text-cyan-600 transition-colors uppercase leading-none">{{ $dungeon->name }}</h3>
        <p class="text-xs font-medium text-slate-500 mb-8 leading-relaxed line-clamp-2 h-8">{{ $dungeon->description }}</p>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="p-4 bg-slate-50 rounded-2xl border-2 border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Authorization</p>
                <p class="text-lg font-black {{ $dungeon->rankTier->color_code ?? 'text-teal-900' }} uppercase italic">Tier {{ $dungeon->rankTier->slug ?? 'E' }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border-2 border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Manifest</p>
                <p class="text-lg font-black text-gold-600 font-mono italic">★ {{ number_format($dungeon->reward_soul_points) }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between mt-auto pt-6 border-t-2 border-slate-50">
            <div class="flex gap-4">
                <a href="{{ route('admin.dungeons.show', $dungeon) }}" class="text-[10px] font-black text-slate-400 hover:text-teal-900 uppercase tracking-widest transition-all">Audit</a>
                <a href="{{ route('admin.dungeons.edit', $dungeon) }}" class="text-[10px] font-black text-cyan-600 hover:text-cyan-700 uppercase tracking-widest transition-all">Modify</a>
            </div>
            <form action="{{ route('admin.dungeons.destroy', $dungeon) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this dungeon manifestation?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-[10px] font-black text-red-400 hover:text-red-500 uppercase tracking-widest transition-all">Terminate</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center glass-panel border-dashed rounded-[40px]">
        <p class="text-slate-400 font-serif font-bold text-lg italic uppercase tracking-widest">No Dungeon Manifestations Detected</p>
    </div>
    @endforelse
</div>

<div class="mt-12 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
    {{ $dungeons->links() }}
</div>
@endsection
