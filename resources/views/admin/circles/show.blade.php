@extends('layouts.admin')

@section('title', 'Covenant Audit: ' . $circle->name)

@section('content')
<div class="w-full space-y-10 animate-fadeIn pb-20">
    
    <!-- Header Navigation -->
    <div class="flex items-center gap-6 mb-6">
        <a href="{{ route('admin.circles.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Covenant Registry / Circle Details</h2>
        </div>
    </div>

    <!-- Main Header Card -->
    <div class="relative glass-panel rounded-[50px] overflow-hidden bg-white border-2 border-slate-50 shadow-2xl p-12">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-900/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            <div class="w-40 h-40 rounded-[45px] bg-teal-900 border-4 border-slate-100 flex items-center justify-center text-6xl font-serif font-black text-white shadow-2xl shadow-teal-900/20 group relative overflow-hidden transition-transform hover:scale-105 duration-500">
                @if($circle->icon)
                    <img src="{{ asset('storage/' . $circle->icon) }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($circle->name) }}&background=134e4a&color=22d3ee';">
                @else
                    <i class="fas fa-users-rays text-5xl text-cyan-400 opacity-20 group-hover:opacity-100 transition-opacity"></i>
                @endif
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/40 to-transparent py-4 flex items-center justify-center">
                    <span class="text-[9px] font-black uppercase tracking-[0.3em] text-cyan-300">Level {{ $circle->level }}</span>
                </div>
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-3 text-center md:text-left">
                    <h1 class="text-5xl font-serif font-black text-teal-950 tracking-tighter italic uppercase">{{ $circle->name }}</h1>
                    <span class="px-6 py-2 rounded-2xl bg-teal-900/5 border border-teal-900/10 text-[10px] font-black uppercase tracking-widest text-teal-900 shadow-sm w-fit mx-auto md:mx-0">
                        Sacred Covenant
                    </span>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] flex justify-center md:justify-start items-center gap-3">
                    <i class="fas fa-fingerprint text-cyan-500"></i>
                    INDEX: <span class="text-teal-900 font-mono">CIR-{{ str_pad($circle->id, 5, '0', STR_PAD_LEFT) }}</span>
                </p>
                <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-3">
                    <span class="px-5 py-2 rounded-xl bg-slate-50 border border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest shadow-sm">
                        <i class="fas fa-users-line mr-2 text-teal-900"></i> {{ $circle->members_count ?? $circle->members()->count() }} Hunter Terjalin
                    </span>
                    <span class="px-5 py-2 rounded-xl bg-cyan-50 border border-cyan-100 text-[10px] font-black text-cyan-700 uppercase tracking-widest shadow-sm">
                        <i class="fas fa-calendar-alt mr-2"></i> Manifesto: {{ $circle->created_at->format('d M Y') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-4 w-full md:w-auto">
                <a href="{{ route('admin.circles.edit', $circle) }}" class="px-10 py-5 bg-teal-900 text-white rounded-[25px] flex items-center justify-center gap-4 text-xs font-serif font-black uppercase tracking-widest hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 active:scale-95 group">
                    <i class="fas fa-sliders-h group-hover:rotate-12 transition-transform"></i>
                    Reface Node
                </a>
                <form action="{{ route('admin.circles.destroy', $circle) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete(this, '{{ $circle->name }}')" class="w-full px-10 py-5 bg-white border-2 border-slate-100 rounded-[25px] flex items-center justify-center gap-4 text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] hover:text-red-500 hover:border-red-400 transition-all active:scale-95 group/del">
                        <i class="fas fa-trash-alt group-hover/del:animate-bounce"></i>
                        Eliminate Loop
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Body Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Sidebar: Leader & Description -->
        <div class="space-y-10">
            <!-- Lore Card -->
            <div class="glass-panel p-10 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] mb-8 border-b border-slate-50 pb-6 flex items-center gap-3 font-serif">
                    <i class="fas fa-scroll text-cyan-500"></i> Deskripsi Kolektif
                </h3>
                <p class="text-sm text-slate-500 leading-loose italic font-medium opacity-80 border-l-4 border-slate-100 pl-6 py-2">
                    "{{ $circle->description ?? 'Tidak ada catatan historis yang termanifestasi dalam rekaman sistem untuk aliansi kolektif ini.' }}"
                </p>
            </div>

            <!-- Leader Authorized Card -->
            <div class="p-10 rounded-[50px] bg-teal-900 border-2 border-slate-800 text-white relative overflow-hidden shadow-2xl shadow-teal-950/50 group">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-400/20 rounded-full blur-[80px] group-hover:scale-110 transition-transform duration-1000"></div>
                <h3 class="text-[10px] font-black text-white/20 uppercase tracking-[0.5em] mb-10 border-b border-white/5 pb-6 flex items-center gap-3 font-serif italic">
                    <i class="fas fa-crown text-cyan-400"></i> Authorized Leader
                </h3>
                
                <div class="flex items-center gap-6 relative z-10 transition-all group-hover:translate-x-1">
                    <div class="w-16 h-16 rounded-[22px] bg-white/5 border border-white/10 flex items-center justify-center text-3xl font-serif font-black text-cyan-400 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                        {{ substr($circle->leader->username ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xl font-serif font-black text-white tracking-tight uppercase leading-none">{{ $circle->leader->username ?? 'PROTOCOL VOID' }}</p>
                        <p class="text-[10px] font-black text-cyan-400/60 uppercase tracking-[0.3em] mt-3 italic">Founder of Matrix</p>
                    </div>
                </div>

                @if($circle->leader)
                <div class="mt-10 pt-8 border-t border-white/5 flex justify-between items-center relative z-10">
                    <div class="text-center flex-1">
                        <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Rank Tier</p>
                        <p class="text-xs font-black text-white italic">{{ $circle->leader->rankTier->name ?? 'UNKNOWN' }}</p>
                    </div>
                    <div class="w-[1px] h-8 bg-white/5 mx-4"></div>
                    <div class="text-center flex-1">
                        <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Auth ID</p>
                        <p class="text-xs font-black text-cyan-400 font-mono">SN-{{ str_pad($circle->leader->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Main: Bonded Members List -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-panel p-12 rounded-[50px] bg-white border-2 border-slate-50 shadow-xl min-h-[500px]">
                <div class="flex justify-between items-center mb-12 border-b border-slate-50 pb-8">
                    <div>
                        <h3 class="text-xs font-black text-teal-900 uppercase tracking-[0.4em] flex items-center gap-3 font-serif">
                            <i class="fas fa-users-line text-cyan-500"></i> Bonded Members Matrix
                        </h3>
                        <p class="text-[9px] text-slate-400 uppercase tracking-[0.2em] font-bold mt-2 italic italic">Real-time Synchronization Active</p>
                    </div>
                    <div class="bg-slate-50 px-5 py-2 rounded-xl border border-slate-100">
                         <span class="text-[10px] font-black text-teal-900 uppercase tracking-widest">{{ $circle->members->count() }} Count</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($circle->members as $member)
                    <div class="flex items-center gap-5 p-5 bg-slate-50 border-2 border-slate-100 rounded-[30px] group/member hover:bg-white hover:border-teal-900/10 hover:shadow-xl hover:shadow-teal-900/5 transition-all duration-500 cursor-default flex-shrink-0 animate-slideUp">
                        <div class="w-14 h-14 rounded-2xl bg-teal-900/5 border-2 border-white flex items-center justify-center font-serif font-black text-teal-900 text-lg shadow-sm group-hover/member:bg-teal-900 group-hover/member:text-white group-hover/member:scale-110 transition-all duration-500">
                             {{ substr($member->username, 0, 1) }}
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-base font-serif font-black text-teal-950 group-hover/member:text-cyan-600 transition-colors truncate">{{ $member->username }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Bond Type: Regular Node</span>
                                @if($member->rankTier)
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $member->rankTier->color_code ?? '#22d3ee' }}"></span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.hunters.show', $member) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-300 hover:text-teal-900 hover:border-teal-900 transition-all opacity-0 group-hover/member:opacity-100">
                            <i class="fas fa-expand-alt text-[10px]"></i>
                        </a>
                    </div>
                    @empty
                    <div class="col-span-full py-32 text-center">
                        <div class="opacity-10 flex flex-col items-center">
                            <i class="fas fa-cubes-stacked text-7xl mb-6 text-teal-900"></i>
                            <p class="text-[10px] font-black uppercase tracking-[0.5em] text-teal-900">Collective logic is currently empty</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s ease-out forwards; }
    .animate-slideUp { animation: slideUp 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); scale: 0.95; }
        to { opacity: 1; transform: translateY(0); scale: 1; }
    }
</style>
@endsection
