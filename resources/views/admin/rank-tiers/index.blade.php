@extends('layouts.admin')

@section('title', 'Manajemen Pangkat')

@section('content')
<div class="space-y-12 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Manajemen Pangkat</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Konfigurasi Tingkat Level Hunter
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="hidden lg:flex items-center bg-white/50 backdrop-blur-sm px-6 py-4 rounded-2xl border-2 border-slate-100 shadow-sm">
                <div class="text-right mr-4 border-r border-slate-200 pr-4">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total Pangkat</p>
                    <p class="text-[10px] font-black text-teal-900 uppercase">{{ $rankTiers->count() }} Pangkat Terdaftar</p>
                </div>
                <i class="fas fa-layer-group text-cyan-500 text-xl"></i>
            </div>
            
            <a href="{{ route('admin.rank-tiers.create') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-[20px] bg-teal-900 text-white font-black uppercase tracking-[0.2em] shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 group">
                <i class="fas fa-plus text-cyan-400 group-hover:rotate-90 transition-transform"></i>
                Tambah Pangkat Baru
            </a>
        </div>
    </div>

    <!-- Tier Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($rankTiers as $tier)
        @php
            $rankSlug = strtoupper($tier->slug);
            $theme = match($rankSlug) {
                'S' => ['text' => 'text-amber-500', 'border' => 'border-amber-400', 'bg' => 'bg-amber-400', 'glow' => 'shadow-amber-400/40', 'gradient' => 'from-amber-400/20 to-orange-500/5', 'badge' => 'bg-amber-50 text-amber-600 border-amber-200', 'dot' => 'bg-amber-500', 'hover' => 'group-hover:border-amber-400 group-hover:shadow-amber-400/30'],
                'A' => ['text' => 'text-purple-500', 'border' => 'border-purple-500', 'bg' => 'bg-purple-500', 'glow' => 'shadow-purple-500/40', 'gradient' => 'from-purple-500/20 to-fuchsia-600/5', 'badge' => 'bg-purple-50 text-purple-600 border-purple-200', 'dot' => 'bg-purple-500', 'hover' => 'group-hover:border-purple-400 group-hover:shadow-purple-500/30'],
                'B' => ['text' => 'text-blue-500', 'border' => 'border-blue-500', 'bg' => 'bg-blue-500', 'glow' => 'shadow-blue-500/40', 'gradient' => 'from-blue-500/20 to-indigo-600/5', 'badge' => 'bg-blue-50 text-blue-600 border-blue-200', 'dot' => 'bg-blue-500', 'hover' => 'group-hover:border-blue-400 group-hover:shadow-blue-500/30'],
                'C' => ['text' => 'text-emerald-500', 'border' => 'border-emerald-500', 'bg' => 'bg-emerald-500', 'glow' => 'shadow-emerald-500/40', 'gradient' => 'from-emerald-500/20 to-teal-600/5', 'badge' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'dot' => 'bg-emerald-500', 'hover' => 'group-hover:border-emerald-400 group-hover:shadow-emerald-500/30'],
                'D' => ['text' => 'text-slate-500', 'border' => 'border-slate-400', 'bg' => 'bg-slate-400', 'glow' => 'shadow-slate-400/40', 'gradient' => 'from-slate-400/20 to-gray-500/5', 'badge' => 'bg-slate-50 text-slate-600 border-slate-200', 'dot' => 'bg-slate-500', 'hover' => 'group-hover:border-slate-400 group-hover:shadow-slate-400/30'],
                'E' => ['text' => 'text-orange-700', 'border' => 'border-orange-600', 'bg' => 'bg-orange-600', 'glow' => 'shadow-orange-600/40', 'gradient' => 'from-orange-600/20 to-orange-800/5', 'badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'dot' => 'bg-orange-600', 'hover' => 'group-hover:border-orange-500 group-hover:shadow-orange-600/30'],
                default => ['text' => 'text-cyan-500', 'border' => 'border-cyan-500', 'bg' => 'bg-cyan-500', 'glow' => 'shadow-cyan-500/40', 'gradient' => 'from-cyan-400/20 to-blue-500/5', 'badge' => 'bg-cyan-50 text-cyan-600 border-cyan-200', 'dot' => 'bg-cyan-500', 'hover' => 'group-hover:border-cyan-400 group-hover:shadow-cyan-500/30'],
            };
        @endphp
        <div class="relative group h-full flex flex-col">
            <!-- Decorative Glow Background -->
            <div class="absolute inset-0 bg-gradient-to-br {{ $theme['gradient'] }} rounded-[40px] shadow-xl {{ $theme['glow'] }} transform group-hover:-translate-y-2 transition-all duration-500 border-2 border-slate-100/50 {{ $theme['hover'] }}"></div>
            
            <div class="relative p-1 h-full flex flex-col">
                <!-- Header Badge -->
                <div class="flex justify-between items-start p-6 pb-0">
                    <div class="px-4 py-2 bg-white/80 backdrop-blur-md rounded-xl border border-slate-200/50 text-[9px] font-black text-slate-400 uppercase tracking-widest font-mono shadow-sm">
                        ID-{{ str_pad($tier->id, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 {{ $theme['badge'] }} rounded-lg text-[8px] font-black uppercase tracking-widest bg-white/80 backdrop-blur-md shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full {{ $theme['dot'] }} animate-pulse shadow-[0_0_8px_currentColor]"></span>
                        Rank {{ $tier->slug }}
                    </div>
                </div>

                <!-- Main Identity Content -->
                <div class="flex flex-col items-center flex-1 px-8 py-4 mt-2">
                    <div class="relative w-32 h-32 flex items-center justify-center mb-6">
                        <!-- Colored Halo -->
                        <div class="absolute inset-0 rounded-full opacity-20 group-hover:opacity-40 transition-opacity blur-2xl {{ $theme['bg'] }}"></div>
                        <!-- Hexagon or badge background -->
                        <div class="absolute inset-2 rounded-[32px] rotate-45 border-[3px] opacity-30 group-hover:rotate-90 group-hover:scale-110 transition-all duration-700 {{ $theme['border'] }}"></div>
                        
                        <!-- Large Letter -->
                        <div class="text-8xl font-serif font-black italic tracking-tighter drop-shadow-[0_0_15px_rgba(0,0,0,0.1)] group-hover:scale-125 transition-transform duration-700 select-none z-10 {{ $theme['text'] }}">
                            {{ $tier->slug }}
                        </div>
                    </div>
                    
                    <h3 class="text-sm font-black {{ $theme['text'] }} uppercase tracking-[0.4em] leading-none text-center drop-shadow-sm">{{ $tier->name }}</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-2">Identitas Pangkat</p>
                </div>

                <!-- Metrics & Requirements -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="p-4 bg-white/80 backdrop-blur-sm rounded-2xl border border-slate-100 flex flex-col items-center text-center shadow-sm">
                            <i class="fas fa-bolt {{ $theme['text'] }} mb-2 opacity-60"></i>
                            <span class="text-sm font-mono font-black text-teal-950 tracking-tighter leading-none">{{ number_format($tier->min_xp_required) }}</span>
                            <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest mt-1">Min. XP</span>
                        </div>
                        <div class="p-4 bg-white/80 backdrop-blur-sm rounded-2xl border border-slate-100 flex flex-col items-center text-center shadow-sm">
                            <i class="fas fa-layer-group text-teal-900 mb-2 opacity-30"></i>
                            <span class="text-sm font-mono font-black text-teal-950 tracking-tighter leading-none">LVL {{ $tier->min_level_requirement }}</span>
                            <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest mt-1">Akses Rift</span>
                        </div>
                    </div>

                    <!-- Hunter Count -->
                    <div class="flex items-center justify-between p-3 bg-white/90 backdrop-blur-md border border-slate-100 rounded-xl shadow-sm">
                        <div class="flex -space-x-2">
                             @for($i = 0; $i < min(3, $tier->users_count ?? $tier->users()->count()); $i++)
                                <div class="w-6 h-6 rounded-lg bg-teal-900 border-2 border-white flex items-center justify-center text-[7px] text-white shadow-sm">
                                    <i class="fas fa-user text-[6px]"></i>
                                </div>
                             @endfor
                             @if(($tier->users_count ?? $tier->users()->count()) > 3)
                                <div class="w-6 h-6 rounded-lg bg-slate-100 border-2 border-white flex items-center justify-center text-[7px] text-slate-400 font-black shadow-sm">+{{ ($tier->users_count ?? $tier->users()->count()) - 3 }}</div>
                             @endif
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $tier->users_count ?? $tier->users()->count() }} Hunter</span>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="mt-auto p-4 flex gap-3 z-10">
                    <a href="{{ route('admin.rank-tiers.edit', $tier) }}" class="flex-1 py-3.5 bg-white/80 backdrop-blur-md border border-slate-200 rounded-2xl text-[9px] text-center font-black text-teal-900 uppercase tracking-[0.2em] hover:bg-white hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95">
                        Edit
                    </a>
                    <form action="{{ route('admin.rank-tiers.destroy', $tier) }}" method="POST" class="inline flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete(this, '{{ $tier->name }}')" class="w-full py-3.5 bg-rose-50 border border-rose-100 rounded-2xl text-[9px] text-center font-black text-rose-500 uppercase tracking-[0.2em] hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Add Rank Action Placeholder -->
        <a href="{{ route('admin.rank-tiers.create') }}" class="group p-1 rounded-[40px] border-2 border-dashed border-slate-200 bg-slate-50/30 hover:border-cyan-400 hover:bg-cyan-50/20 transition-all duration-500 flex items-center justify-center min-h-[400px]">
             <div class="flex flex-col items-center text-slate-300 group-hover:text-cyan-500 transition-all">
                 <div class="w-20 h-20 rounded-full bg-white border-2 border-slate-100 flex items-center justify-center font-black text-3xl mb-6 shadow-sm group-hover:bg-cyan-50 group-hover:border-cyan-100 transition-colors">
                    <i class="fas fa-plus drop-shadow-sm"></i>
                </div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.5em] text-center leading-relaxed">Tambah<br>Pangkat Baru</h4>
             </div>
        </a>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
