@extends('layouts.admin')

@section('title', 'Kalibrasi Ritual: ' . $task->name)

@section('content')
<div class="w-full animate-fadeIn">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.daily-tasks.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Rekalibrasi <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Ritual</span> <span class="text-teal-900 font-serif">Node</span></h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target Node: Identifikasi {{ $task->name }} Berhasil
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.daily-tasks.update', $task) }}" method="POST" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Nama Ritual</label>
                    <input type="text" name="name" value="{{ old('name', $task->name) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider shadow-inner">
                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div>
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-3 tracking-[0.3em] ml-1">Nilai Hasil (EXP)</label>
                        <div class="relative">
                            <input type="number" name="soul_points" value="{{ old('soul_points', $task->soul_points) }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-blue-600 p-6 focus:border-blue-400 focus:bg-white outline-none transition-all font-mono font-black text-xl shadow-inner">
                            <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[10px] font-black bg-gradient-to-r from-blue-600 to-slate-900 bg-clip-text text-transparent uppercase tracking-widest">Experience</span>
                        </div>
                        @error('soul_points') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Kategori Matriks</label>
                        <select name="category_id" required 
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-black text-xs uppercase tracking-widest appearance-none cursor-pointer shadow-inner">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $task->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Urutan Ikon</label>
                        <div class="relative">
                            <input type="text" name="icon" value="{{ old('icon', $task->icon) }}" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-mono font-bold text-sm shadow-inner">
                            <div class="absolute right-16 top-1/2 -translate-y-1/2 text-teal-950 opacity-40">
                                <i class="{{ $task->icon }} text-lg"></i>
                            </div>
                        </div>
                        @error('icon') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-4 italic">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Protokol Ritual (Deskripsi)</label>
                    <textarea name="description" rows="4" required
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium shadow-inner leading-relaxed">{{ old('description', $task->description) }}</textarea>
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
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mt-1">Status mandat ini dalam grid global</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $task->is_active) ? 'checked' : '' }} class="peer hidden">
                            <div class="w-12 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-400 transition-colors relative after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:w-4 after:h-4 after:rounded-full after:transition-all peer-checked:after:translate-x-6 shadow-inner"></div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        SINKRONISASI PEMBARUAN
                        <i class="fas fa-rotate text-cyan-400 icon-glow transition-all group-hover:rotate-180"></i>
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
