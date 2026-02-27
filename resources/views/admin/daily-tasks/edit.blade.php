@extends('layouts.admin')

@section('title', 'Refine Daily Ritual')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.daily-tasks.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Calibrate Ritual</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target Node: {{ $task->name }}
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.daily-tasks.update', $task) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Ritual Designation (Name)</label>
                    <input type="text" name="name" value="{{ $task->name }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider shadow-inner">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-gold-500 uppercase mb-3 tracking-[0.3em] ml-1">Yield Value (SP)</label>
                        <div class="relative">
                            <input type="number" name="soul_points" value="{{ $task->soul_points }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-gold-600 p-6 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                            <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black text-gold-400 uppercase">Spirit Points</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Icon Sequence</label>
                        <div class="relative">
                            <input type="text" name="icon" value="{{ $task->icon }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-bold text-sm shadow-inner">
                            <div class="absolute right-16 top-1/2 -translate-y-1/2 text-teal-950 opacity-40">
                                <i class="{{ $task->icon }} text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Ritual Protocol (Description)</label>
                    <textarea name="description" rows="4" required
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium shadow-inner leading-relaxed">{{ $task->description }}</textarea>
                </div>

                <div class="pt-4">
                    <label class="group relative flex items-center justify-between p-6 bg-slate-50/50 border-2 border-slate-100 rounded-[24px] cursor-pointer hover:border-cyan-400 hover:bg-white transition-all shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white border-2 border-slate-100 flex items-center justify-center text-emerald-500 transition-colors group-hover:border-emerald-100 shadow-sm">
                                <i class="fas fa-power-off text-lg"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black text-teal-900 uppercase tracking-widest">Active Protocol</span>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mt-1">Status of this mandate in the global grid</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $task->is_active ? 'checked' : '' }} class="peer hidden">
                            <div class="w-12 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-400 transition-colors relative after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:w-4 after:h-4 after:rounded-full after:transition-all peer-checked:after:translate-x-6 shadow-inner"></div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        RE-ESTABLISH MANDATE
                        <i class="fas fa-rotate text-cyan-400 icon-glow transition-all group-hover:rotate-180"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
