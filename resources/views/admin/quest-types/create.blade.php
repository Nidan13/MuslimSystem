@extends('layouts.admin')

@section('title', 'Register Quest Category')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.quest-types.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">New Taxonomy</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em]">Initialize a new mission classification</p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.quest-types.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="grid grid-cols-2 gap-10">
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Category Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider placeholder-slate-200 shadow-inner" 
                        placeholder="e.g. SPECIAL MISSION">
                </div>

                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Logic Identifier (Slug)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-cyan-600 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-lg shadow-inner uppercase text-center" 
                        placeholder="special">
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Descriptive Parameters</label>
                    <textarea name="description" rows="5" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium placeholder-slate-200 shadow-inner" 
                        placeholder="Define the behavior and scope of this quest category...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        REGISTER TAXONOMY
                        <svg class="w-6 h-6 icon-glow text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-9 3a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
