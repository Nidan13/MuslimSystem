@extends('layouts.admin')

@section('title', 'Manifestasi Ritual Harian')

@section('content')
<div class="w-full animate-fadeIn">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.daily-tasks.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Konstruksi <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Ritual</span> <span class="text-teal-900 font-serif">Harian</span></h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Inisialisasi Protokol Ritual Baru ke dalam Matriks
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.daily-tasks.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Nama Ritual</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider placeholder-slate-200 shadow-inner" placeholder="Misal: Sinkronisasi Shalat Subuh">
                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-3 tracking-[0.3em] ml-1">Nilai Hasil (EXP)</label>
                        <div class="relative">
                            <input type="number" name="soul_points" value="{{ old('soul_points', 10) }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-blue-600 p-6 focus:border-blue-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                            <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black bg-gradient-to-r from-blue-600 to-slate-900 bg-clip-text text-transparent uppercase tracking-widest">Experience</span>
                        </div>
                        @error('soul_points') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Urutan Ikon</label>
                        <div class="relative">
                            <input type="text" name="icon" value="{{ old('icon', 'fas fa-star') }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-bold text-sm shadow-inner" placeholder="fas fa-sun">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 italic text-[8px] font-black uppercase pointer-events-none">Vektor FontAwesome</div>
                        </div>
                        @error('icon') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Protokol Ritual (Deskripsi)</label>
                    <textarea name="description" rows="4" required
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium placeholder-slate-200 shadow-inner leading-relaxed" placeholder="Direktif mendetail untuk penyucian harian ini..."></textarea>
                    @error('description') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <label class="group relative flex items-center justify-between p-6 bg-slate-50/50 border-2 border-slate-100 rounded-[24px] cursor-pointer hover:border-cyan-400 hover:bg-white transition-all shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white border-2 border-slate-100 flex items-center justify-center text-emerald-500 transition-colors group-hover:border-emerald-100 shadow-sm">
                                <i class="fas fa-power-off text-lg"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black text-teal-900 uppercase tracking-widest">Protokol Aktif</span>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mt-1">Aktifkan mandat ini untuk seluruh hunter segera</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="peer hidden">
                            <div class="w-12 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-400 transition-colors relative after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:w-4 after:h-4 after:rounded-full after:transition-all peer-checked:after:translate-x-6 shadow-inner"></div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        SAHKAN MANDAT RITUAL
                        <i class="fas fa-feather-pointed text-cyan-400 icon-glow transition-all group-hover:rotate-12"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
