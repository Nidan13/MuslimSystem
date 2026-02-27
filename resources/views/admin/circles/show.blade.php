@extends('layouts.admin')

@section('title', 'Covenant Audit: ' . $circle->name)

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-6">
             <a href="{{ route('admin.circles.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
                <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-4xl font-serif font-black text-teal-900 tracking-tight uppercase leading-none">{{ $circle->name }}</h1>
                <div class="flex items-center gap-3 mt-3">
                    <span class="px-3 py-1 bg-teal-900 text-[10px] font-black text-cyan-400 uppercase tracking-widest rounded-lg border border-teal-800">
                        Sacred Covenant Node
                    </span>
                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Protocol Index: CIR-{{ str_pad($circle->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
             <a href="{{ route('admin.circles.edit', $circle) }}" class="px-8 py-3 bg-white border-2 border-slate-100 rounded-2xl text-teal-900 font-serif font-black uppercase tracking-widest text-xs shadow-sm hover:border-teal-400 hover:text-teal-600 transition-all group">
                <i class="fas fa-sliders-h mr-2 text-gold-500 transition-transform group-hover:rotate-12"></i>
                Reface Covenant
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
        <!-- Visual & Stats Column -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-panel p-6 rounded-[40px] relative overflow-hidden group">
                <div class="w-full h-64 bg-teal-900 rounded-[32px] flex items-center justify-center relative overflow-hidden shadow-2xl">
                    @if($circle->icon_path)
                        <img src="{{ asset('storage/' . $circle->icon_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <i class="fas fa-users-rays text-7xl text-cyan-400 opacity-20 icon-glow transition-transform group-hover:scale-110"></i>
                    @endif
                    
                    <div class="absolute bottom-6 right-6">
                        <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/20">
                            <p class="text-[8px] font-black text-cyan-400 uppercase tracking-widest mb-0.5 whitespace-nowrap">Node Integrity</p>
                            <p class="text-xs font-black text-white uppercase tracking-widest">Stable Collective</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Membership</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-teal-900 font-mono tracking-tighter">{{ $circle->members_count ?? $circle->members()->count() }}</span>
                            <span class="text-[10px] font-black text-teal-900/40 uppercase">Hunters</span>
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Manifested</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-sm font-black text-slate-600 font-mono">{{ $circle->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="glass-panel p-8 rounded-[40px] bg-teal-900 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-400/10 rounded-full blur-3xl"></div>
                <h3 class="text-[10px] font-black text-cyan-400 uppercase tracking-[0.4em] mb-4">Covenant Leader</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center font-serif font-black text-cyan-100">
                        L
                    </div>
                    <div>
                        <p class="text-lg font-bold font-serif leading-none tracking-tight">Lead Authorized Node</p>
                        <p class="text-[9px] font-black text-cyan-400/60 uppercase tracking-widest mt-1 italic">Circle Founder Identity</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- content & Members Column -->
        <div class="lg:col-span-3 space-y-10">
            <div class="glass-panel p-10 rounded-[40px] relative overflow-hidden">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-6">Collective Lore</h3>
                <p class="text-2xl text-slate-700 font-serif leading-relaxed italic">
                    "{{ $circle->description ?? 'No historical data has been recorded for this holy alliance. The narrative remains unwritten in the system archive.' }}"
                </p>
            </div>

            <div class="glass-panel p-10 rounded-[40px]">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Bonded Members</h3>
                    <span class="text-[9px] font-black text-teal-900/40 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-lg">Real-time Sync</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($circle->members as $member)
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border-2 border-slate-100 group/member hover:bg-white hover:border-cyan-200 transition-all cursor-default">
                        <div class="w-10 h-10 rounded-xl bg-teal-900 flex items-center justify-center font-serif font-black text-white text-xs shadow-lg group-hover/member:scale-110 transition-transform">
                             {{ substr($member->username, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-teal-900 group-hover/member:text-cyan-600 transition-colors">{{ $member->username }}</p>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Bonded Member</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-12 text-center opacity-30">
                        <i class="fas fa-ghost text-4xl mb-4"></i>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Matrix empty of bonded nodes</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
