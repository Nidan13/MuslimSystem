@extends('layouts.admin')

@section('title', 'Recalibrate Rank Protocol')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.rank-tiers.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Recalibrate Tier</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Authorized Node: Tier {{ $rankTier->slug }} recalibration
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.rank-tiers.update', $rankTier) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Rank Slug Identifier</label>
                        <input type="text" name="slug" value="{{ $rankTier->slug }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-3xl text-center uppercase shadow-inner">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Authority Designation</label>
                        <input type="text" name="name" value="{{ $rankTier->name }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider shadow-inner">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Threshold XP Required</label>
                        <input type="number" name="min_xp_required" value="{{ $rankTier->min_xp_required }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Dungeon Access Authorization</label>
                        <div class="relative">
                            <input type="number" name="min_level_requirement" value="{{ $rankTier->min_level_requirement }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                            <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">Min Level</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Visual Signature (Color Code)</label>
                    <div class="relative flex items-center gap-4 p-4 bg-slate-50 border-2 border-slate-100 rounded-[24px] shadow-inner">
                        <input type="color" name="color_code" value="{{ $rankTier->color_code ?? '#093b48' }}" 
                            class="w-16 h-16 rounded-xl border-2 border-white shadow-md cursor-pointer bg-transparent overflow-hidden">
                        <div class="flex-1">
                            <input type="text" value="{{ $rankTier->color_code ?? '#093b48' }}" readonly
                                class="w-full bg-transparent border-none text-teal-900 font-mono font-black text-lg focus:ring-0">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-3">Hexadecimal node identifier</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Rank Directive (Description)</label>
                    <textarea name="description" rows="4" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium shadow-inner leading-relaxed">{{ $rankTier->description }}</textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        RATIFY CALIBRATION
                        <i class="fas fa-check-double text-cyan-400 icon-glow transition-all group-hover:scale-125"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
