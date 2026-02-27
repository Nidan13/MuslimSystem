@extends('layouts.admin')

@section('title', 'Refine Spiritual Covenant')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.circles.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Refine Covenant</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target: {{ $circle->name }} Protocol
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.circles.update', $circle) }}" method="POST" enctype="multipart/form-data" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Covenant Designation (Name)</label>
                    <input type="text" name="name" value="{{ $circle->name }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Covenant Emblem (Visual Data)</label>
                    <div class="flex items-center gap-8 p-4 bg-slate-50/50 border-2 border-slate-100 rounded-[32px]">
                        <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-md overflow-hidden shrink-0">
                            @if($circle->icon_path)
                                <img src="{{ asset('storage/' . $circle->icon_path) }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200 rounded-xl">
                                    <i class="fas fa-users-rays text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 relative group">
                            <input type="file" name="icon" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                            <div class="w-full bg-white border-2 border-dashed border-slate-200 rounded-[24px] p-6 text-center group-hover:border-cyan-400 transition-all">
                                <i class="fas fa-sync-alt text-xl text-slate-300 mb-1"></i>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Swap Emblem Data</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Covenant Lore & Directives</label>
                    <textarea name="description" rows="5" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium shadow-inner leading-relaxed">{{ $circle->description }}</textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        RE-FINALIZE COVENANT
                        <i class="fas fa-magic text-cyan-400 icon-glow transition-all group-hover:rotate-12"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
