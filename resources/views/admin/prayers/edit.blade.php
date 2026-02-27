@extends('layouts.admin')

@section('title', 'Calibrate Salat Protocol')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.prayers.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Calibrate Protocol</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em]">Updating spiritual nodes: {{ $prayer->name }}</p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.prayers.update', $prayer) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div class="md:col-span-1">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Protocol Icon</label>
                        <input type="text" name="icon" value="{{ $prayer->icon }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all text-center text-2xl shadow-inner">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Salat Designation</label>
                        <input type="text" name="name" value="{{ $prayer->name }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider placeholder-slate-200 shadow-inner">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Reward Yield Optimization (SP)</label>
                    <div class="relative">
                        <input type="number" name="soul_points" value="{{ $prayer->soul_points }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-gold-600 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                        <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">Spirit Points</span>
                    </div>
                    <p class="mt-3 text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-loose ml-1">Total SP and Exp granted upon successful synchronization of this salat protocol.</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Spiritual Directive (Description)</label>
                    <textarea name="description" rows="4" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium placeholder-slate-200 shadow-inner leading-relaxed">{{ $prayer->description }}</textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        STABILIZE PROTOCOL
                        <i class="fas fa-check-double text-cyan-400 icon-glow transition-transform group-hover:scale-125"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
