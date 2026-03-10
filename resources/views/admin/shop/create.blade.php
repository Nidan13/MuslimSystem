@extends('layouts.admin')

@section('title', 'Protokol Tempa Artefak')

@section('content')
<div class="w-full">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.shop.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Registri Tempa</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Memanifestasikan Alat Ilahi Baru ke Eksistensi
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-gold-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.shop.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Designasi Artefak (Nama)</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider placeholder-slate-200 shadow-inner">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Kategori Kelas</label>
                        <div class="relative">
                            <select name="category" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none appearance-none cursor-pointer font-black text-sm uppercase tracking-widest shadow-inner">
                                <option value="potion">Protokol Penyembuhan (Potion)</option>
                                <option value="equipment">Perlengkapan Tempur (Equipment)</option>
                                <option value="skill_book">Gulungan Kuno (Skill Book)</option>
                                <option value="misc">Materi Eksotis (Misc)</option>
                            </select>
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-cyan-500">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gold-500 uppercase mb-3 tracking-[0.3em] ml-1">Valuasi Pasar (SP)</label>
                        <div class="relative">
                            <input type="number" name="price" value="{{ old('price', 100) }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-gold-600 p-6 focus:border-gold-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                            <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black text-gold-400 uppercase">Soul Points</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Manifestasi Visual</label>
                    <div class="relative group">
                        <input type="file" name="image" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div class="w-full bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] p-8 text-center group-hover:border-cyan-400 transition-all shadow-inner">
                            <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 mb-2 group-hover:text-cyan-400 transition-colors"></i>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Data Visual (JPG, PNG, WEBP)</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Lore & Direktif Artefak</label>
                    <textarea name="description" rows="4" required
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium placeholder-slate-200 shadow-inner leading-relaxed" placeholder="Jelaskan asal-usul kuno dan penggunaan taktis..."></textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        MULAI PENEMPAAN
                        <i class="fas fa-hammer text-gold-400 icon-glow transition-all group-hover:-rotate-45"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
