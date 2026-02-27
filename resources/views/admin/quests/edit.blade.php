@extends('layouts.admin')

@section('title', 'Recalibrate Mission: ' . $quest->title)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.quests.show', $quest) }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Recalibrate Matrix</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target: {{ $quest->title }} [QST-{{ str_pad($quest->id, 5, '0', STR_PAD_LEFT) }}]
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[50px] relative overflow-hidden bg-white shadow-2xl border-2 border-slate-50">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-50 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.quests.update', $quest) }}" method="POST" class="space-y-12 relative z-10">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="col-span-full">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Protocol Designation (Title)</label>
                    <input type="text" name="title" value="{{ $quest->title }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-[28px] text-teal-900 p-8 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-2xl uppercase tracking-wider placeholder-slate-200 shadow-inner">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Quest Classification</label>
                    <div class="relative group">
                        <select name="quest_type_id" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-lg transition-all shadow-inner">
                            @foreach($questTypes as $type)
                            <option value="{{ $type->id }}" {{ $quest->quest_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Authority Clearance</label>
                    <div class="relative group">
                        <select name="rank_tier_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-lg transition-all shadow-inner">
                            <option value="">RESTRICTION: NONE</option>
                            @foreach($rankTiers as $tier)
                            <option value="{{ $tier->id }}" {{ $quest->rank_tier_id == $tier->id ? 'selected' : '' }}>TIER {{ $tier->slug }} - {{ $tier->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none group-hover:rotate-180 transition-transform"></i>
                    </div>
                </div>

                <div class="col-span-full">
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Mission Briefing (Description)</label>
                    <textarea name="description" rows="5" class="w-full bg-slate-50 border-2 border-slate-100 rounded-[28px] text-teal-900 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-serif font-black text-lg shadow-inner placeholder-slate-200">{{ $quest->description }}</textarea>
                </div>
                
                <div class="col-span-full grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <label class="block text-[10px] font-black text-gold-500 uppercase mb-4 tracking-[0.3em] text-center">Soul Energy (SP)</label>
                        <input type="number" name="reward_soul_points" value="{{ $quest->reward_soul_points }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-gold-600 p-4 text-center text-3xl font-mono font-black focus:border-gold-400 outline-none transition-all shadow-inner">
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <label class="block text-[10px] font-black text-cyan-500 uppercase mb-4 tracking-[0.3em] text-center">Growth Factor (EXP)</label>
                        <input type="number" name="reward_exp" value="{{ $quest->reward_exp }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-cyan-600 p-4 text-center text-3xl font-mono font-black focus:border-cyan-400 outline-none transition-all shadow-inner">
                    </div>
                    <div class="p-8 bg-slate-50 rounded-[32px] border-2 border-slate-100">
                        <label class="block text-[10px] font-black text-red-500 uppercase mb-4 tracking-[0.3em] text-center">Fatigue Strain</label>
                        <input type="number" name="penalty_fatigue" value="{{ $quest->penalty_fatigue }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-red-600 p-4 text-center text-3xl font-mono font-black focus:border-red-400 outline-none transition-all shadow-inner">
                    </div>
                </div>

                @php $isRecurring = !is_null($quest->start_time); @endphp
                <div class="col-span-full space-y-8 bg-slate-50 p-10 rounded-[40px] border-2 border-slate-100 shadow-inner">
                    <div class="flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em]">Temporal Anchoring</h3>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="schedule_type" value="specific" {{ !$isRecurring ? 'checked' : '' }} onchange="toggleScheduleFields()" class="w-5 h-5 text-teal-900 focus:ring-cyan-400 border-slate-300">
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-teal-900 uppercase tracking-widest transition-all">Specific Node</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="schedule_type" value="recurring" {{ $isRecurring ? 'checked' : '' }} onchange="toggleScheduleFields()" class="w-5 h-5 text-teal-900 focus:ring-cyan-400 border-slate-300">
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-teal-900 uppercase tracking-widest transition-all">Recurring Cycle</span>
                            </label>
                        </div>
                    </div>

                    <div id="specific-fields" class="grid grid-cols-2 gap-10 {{ $isRecurring ? 'hidden' : '' }} animate-fadeIn">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-[0.3em] ml-1">Starts At</label>
                            <input type="datetime-local" name="starts_at" value="{{ $quest->starts_at?->format('Y-m-d\TH:i') }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-5 text-sm font-black focus:border-cyan-400 outline-none shadow-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-[0.3em] ml-1">Expires At</label>
                            <input type="datetime-local" name="expires_at" value="{{ $quest->expires_at?->format('Y-m-d\TH:i') }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-5 text-sm font-black focus:border-red-400 outline-none shadow-sm transition-all">
                        </div>
                    </div>

                    <div id="recurring-fields" class="grid grid-cols-2 gap-10 {{ !$isRecurring ? 'hidden' : '' }} animate-fadeIn">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-[0.3em] ml-1">Daily Activation</label>
                            <input type="time" name="start_time" value="{{ $quest->start_time?->format('H:i') }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-5 text-sm font-black focus:border-cyan-400 outline-none shadow-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-[0.3em] ml-1">Daily Deactivation</label>
                            <input type="time" name="end_time" value="{{ $quest->end_time?->format('H:i') }}" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-5 text-sm font-black focus:border-red-400 outline-none shadow-sm transition-all">
                        </div>
                    </div>
                </div>

                <div class="col-span-full">
                    <div class="flex justify-between items-center mb-8 px-4">
                        <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.5em]">Critical Parameters</label>
                        <button type="button" onclick="addRequirement()" class="text-[10px] font-black text-cyan-500 hover:text-cyan-600 uppercase tracking-[0.3em] flex items-center gap-3 transition-all border-b border-dashed border-cyan-400 pb-1">
                            <i class="fas fa-plus-circle"></i>
                            Inject Parameter
                        </button>
                    </div>
                    <div id="requirements-container" class="space-y-6">
                        @forelse($quest->requirements ?? [] as $key => $value)
                        <div class="grid grid-cols-12 gap-6 items-center animate-fadeIn p-4 bg-slate-50 rounded-[28px] border-2 border-white shadow-sm">
                             <div class="col-span-7">
                                <input type="text" name="req_keys[]" value="{{ $key }}" placeholder="e.g. protocol_id" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-4 font-black uppercase tracking-widest text-xs focus:border-cyan-400 outline-none shadow-inner">
                             </div>
                             <div class="col-span-3">
                                <input type="number" name="req_values[]" value="{{ $value }}" placeholder="QTY" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-4 font-black text-center focus:border-cyan-400 outline-none shadow-inner">
                             </div>
                             <div class="col-span-2 flex justify-end">
                                <button type="button" onclick="this.closest('.grid').remove()" class="w-12 h-12 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                             </div>
                        </div>
                        @empty
                        <!-- Row template if empty -->
                        <div class="grid grid-cols-12 gap-6 items-center animate-fadeIn p-4 bg-slate-50 rounded-[28px] border-2 border-white shadow-sm">
                             <div class="col-span-7">
                                <input type="text" name="req_keys[]" placeholder="e.g. protocol_id" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-4 font-black uppercase tracking-widest text-xs focus:border-cyan-400 outline-none shadow-inner">
                             </div>
                             <div class="col-span-3">
                                <input type="number" name="req_values[]" placeholder="QTY" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-4 font-black text-center focus:border-cyan-400 outline-none shadow-inner">
                             </div>
                             <div class="col-span-2 flex justify-end">
                                <button type="button" onclick="this.closest('.grid').remove()" class="w-12 h-12 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                             </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t-2 border-slate-50">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-8 rounded-[32px] font-serif font-black text-white uppercase tracking-[0.4em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.98] border-t border-white/10">
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

@push('scripts')
<script>
function toggleScheduleFields() {
    const type = document.querySelector('input[name="schedule_type"]:checked').value;
    const specific = document.getElementById('specific-fields');
    const recurring = document.getElementById('recurring-fields');
    
    if (type === 'specific') {
        specific.classList.remove('hidden');
        recurring.classList.add('hidden');
    } else {
        specific.classList.add('hidden');
        recurring.classList.remove('hidden');
    }
}

function addRequirement() {
    const container = document.getElementById('requirements-container');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-6 items-center animate-fadeIn p-4 bg-slate-50 rounded-[28px] border-2 border-white shadow-sm';
    row.innerHTML = `
        <div class="col-span-7">
            <input type="text" name="req_keys[]" placeholder="e.g. protocol_id" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-4 font-black uppercase tracking-widest text-xs focus:border-cyan-400 outline-none shadow-inner">
        </div>
        <div class="col-span-3">
            <input type="number" name="req_values[]" placeholder="QTY" class="w-full bg-white border-2 border-slate-100 rounded-2xl text-teal-900 p-4 font-black text-center focus:border-cyan-400 outline-none shadow-inner">
        </div>
        <div class="col-span-2 flex justify-end">
            <button type="button" onclick="this.closest('.grid').remove()" class="w-12 h-12 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
}
</script>
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
</style>
@endpush
