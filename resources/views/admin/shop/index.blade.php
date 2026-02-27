@extends('layouts.admin')

@section('title', 'Divine Artifact Repository')

@section('content')
<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Ancient Marketplace</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse shadow-[0_0_10px_#fbbf24]"></span>
                Forging Divine Tools & Spiritual Artifacts
            </p>
        </div>
        <a href="{{ route('admin.shop.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                <i class="fas fa-hammer text-gold-400 icon-glow transition-transform group-hover:-rotate-45"></i>
                Forge Artifact
            </span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($items as $item)
        <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500 flex flex-col h-full hover:shadow-2xl hover:shadow-teal-900/10">
            <!-- Image / Icon Area -->
            <div class="w-full h-48 bg-slate-50 rounded-2xl mb-6 overflow-hidden relative border border-slate-100">
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $item->name }}">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 text-slate-200">
                        <i class="fas fa-gem text-5xl opacity-40 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                @endif
                <div class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur-md rounded-lg text-[9px] font-black text-teal-900 uppercase tracking-[0.2em] shadow-sm border border-white/20">
                    {{ $item->category }}
                </div>
                <!-- Price Glow Overlay -->
                <div class="absolute bottom-4 left-4">
                    <span class="px-3 py-1.5 rounded-xl bg-teal-900 text-gold-400 font-mono font-black text-sm shadow-lg shadow-teal-950/40 border border-white/10">
                        {{ number_format($item->price) }} <span class="text-[8px] opacity-70">SP</span>
                    </span>
                </div>
            </div>

            <!-- Item Content -->
            <div class="flex-1 flex flex-col px-1">
                <h3 class="text-xl font-serif font-black text-teal-900 mb-2 leading-tight group-hover:text-cyan-600 transition-colors uppercase tracking-tight">{{ $item->name }}</h3>
                <p class="text-xs font-medium text-slate-500 mb-6 line-clamp-2 leading-relaxed h-8 italic">
                    {{ $item->description }}
                </p>
                
                <div class="mt-auto flex items-center justify-between border-t border-slate-50 pt-6">
                    <div class="flex items-center gap-1 opacity-40 group-hover:opacity-100 transition-opacity">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Stock: {{ $item->stock ?? '∞' }}</span>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('admin.shop.show', $item) }}" class="p-3 rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-cyan-500 hover:border-cyan-200 transition-all shadow-sm">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('admin.shop.edit', $item) }}" class="p-3 rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-teal-900 hover:border-teal-200 transition-all shadow-sm">
                            <i class="fas fa-pen-nib text-xs"></i>
                        </a>
                        <form action="{{ route('admin.shop.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Dismantle this artifact protocol?')">
                            @csrf @method('DELETE')
                            <button class="p-3 rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rose-500 hover:border-rose-200 transition-all shadow-sm">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-32 text-center glass-panel border-dashed rounded-[40px] flex flex-col items-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-200">
                <i class="fas fa-store-slash text-4xl opacity-50"></i>
            </div>
            <p class="text-teal-950 font-serif font-black text-xl italic uppercase tracking-widest">Artifact Vault Empty</p>
            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-[0.5em] font-bold">No divine items currently manifestations</p>
        </div>
        @endforelse
    </div>

    <div class="mt-12 px-8 py-6 glass-panel rounded-3xl border-2 border-slate-50">
        {{ $items->links() }}
    </div>
</div>
@endsection
