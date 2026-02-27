@extends('layouts.admin')

@section('title', 'Recalibrate Hunter: ' . $user->username)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.hunters.show', $user) }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Recalibrate Profile</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target: {{ $user->username }} Identity Parameters
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.hunters.update', $user) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="col-span-full">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Username Identifier</label>
                    <input type="text" name="username" value="{{ $user->username }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl tracking-wider shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Communication Node (Email)</label>
                    <input type="email" name="email" value="{{ $user->email }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-bold text-sm shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Reset Access (Password - Optional)</label>
                    <input type="password" name="password" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-bold text-sm shadow-inner" placeholder="Leave blank to maintain protocol">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Authority Rank</label>
                    <select name="rank_tier_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-lg transition-all shadow-inner">
                        @foreach($rankTiers as $tier)
                        <option value="{{ $tier->id }}" {{ $user->rank_tier_id == $tier->id ? 'selected' : '' }}>TIER {{ $tier->slug }} - {{ $tier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Specialization (Job Class)</label>
                    <select name="job_class_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-bold transition-all shadow-inner">
                        @foreach($jobClasses as $job)
                        <option value="{{ $job->id }}" {{ $user->job_class_id == $job->id ? 'selected' : '' }}>{{ $job->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gold-500 uppercase mb-3 tracking-[0.3em] ml-1">Soul Points Flow</label>
                    <input type="number" name="soul_points" value="{{ $user->soul_points }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-gold-600 p-6 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-cyan-600 uppercase mb-3 tracking-[0.3em] ml-1">Experience Level</label>
                    <input type="number" name="experience" value="{{ $user->experience }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-cyan-700 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        SYNC RECALIBRATION
                        <i class="fas fa-sync text-cyan-400 icon-glow transition-all group-hover:rotate-180"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
