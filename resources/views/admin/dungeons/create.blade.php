@extends('layouts.admin')

@section('title', 'Establish Gate')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.dungeons.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Manifest Dungeon</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em]">Establish a new rift in the system Matrix</p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.dungeons.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="grid grid-cols-2 gap-10">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Manifestation Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider placeholder-slate-200 shadow-inner" 
                        placeholder="e.g. THE ABYSSAL ECHO">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Gate Classification</label>
                    <div class="relative group">
                        <select name="dungeon_type_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-bold transition-all shadow-inner">
                            @foreach($dungeonTypes as $type)
                            <option value="{{ $type->id }}" {{ old('dungeon_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }} (Cap: {{ $type->max_participants }})</option>
                            @endforeach
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-teal-500">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Authorization Range</label>
                    <div class="relative group">
                        <select name="rank_tier_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black transition-all shadow-inner">
                            @foreach($rankTiers as $tier)
                            <option value="{{ $tier->id }}" {{ old('rank_tier_id') == $tier->id ? 'selected' : '' }}>TIER {{ $tier->slug }} - {{ $tier->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-teal-500">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Min Level Proxy</label>
                    <input type="number" name="min_level_requirement" value="{{ old('min_level_requirement', 1) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-black text-lg shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gold-600 uppercase mb-3 tracking-[0.3em] ml-1">Soul Points manifest</label>
                    <div class="relative">
                         <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gold-500 text-xl font-black">★</span>
                        <input type="number" name="reward_soul_points" value="{{ old('reward_soul_points', 500) }}" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 pl-12 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-lg shadow-inner">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Theological Documentation</label>
                <textarea name="description" rows="5" 
                    class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium placeholder-slate-200 shadow-inner" 
                    placeholder="Describe the rift parameters and historical data..."></textarea>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        ESTABLISH MANIFESTATION
                        <svg class="w-6 h-6 icon-glow text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-9 3a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
