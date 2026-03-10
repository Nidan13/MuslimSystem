@extends('layouts.admin')

@section('title', 'Rekalibrasi Lingkaran: ' . $circle->name)

@section('content')
<div class="w-full animate-fadeIn">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.circles.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Rekalibrasi <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Lingkaran</span> <span class="text-teal-900 font-serif">Node</span></h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Target: Parameter untuk {{ $circle->name }} Berhasil Dilacak
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.circles.update', $circle) }}" method="POST" enctype="multipart/form-data" class="space-y-10 relative z-10">
            @csrf @method('PUT')
            
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Nama Lingkaran</label>
                    <input type="text" name="name" value="{{ old('name', $circle->name) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider shadow-inner">
                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Ketua Lingkaran (Leader)</label>
                        <select name="leader_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-bold shadow-inner appearance-none">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('leader_id', $circle->leader_id) == $user->id ? 'selected' : '' }}>{{ $user->username }} ({{ $user->rankTier->slug ?? 'F' }}-Rank)</option>
                            @endforeach
                        </select>
                        @error('leader_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Level Lingkaran</label>
                        <input type="number" name="level" value="{{ old('level', $circle->level) }}" required min="1" class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-black shadow-inner">
                        @error('level') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Lambang Lingkaran (Perbarui Visual)</label>
                    <div class="flex flex-col md:flex-row items-center gap-8 p-6 bg-slate-50/50 border-2 border-slate-100 rounded-[32px]">
                        <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-md overflow-hidden shrink-0 border border-slate-100 relative">
                            <div id="preview-container" class="{{ $circle->icon ? '' : 'hidden' }} absolute inset-0 z-10 bg-white">
                                <img id="image-preview" 
                                     src="{{ $circle->icon ? asset('storage/' . $circle->icon) : '#' }}" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($circle->name) }}&background=134e4a&color=22d3ee';" 
                                     alt="Preview" class="w-full h-full object-cover rounded-xl">
                            </div>
                            <div id="placeholder-content" class="{{ $circle->icon ? 'hidden' : '' }} w-full h-full bg-slate-50 flex items-center justify-center text-slate-200 rounded-xl">
                                <i class="fas fa-users-rays text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 w-full relative group">
                            <input type="file" name="icon" id="circle-icon-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                            <div class="w-full bg-white border-2 border-dashed border-slate-200 rounded-[24px] p-6 text-center group-hover:border-cyan-400 transition-all shadow-sm">
                                <i class="fas fa-sync-alt text-xl text-slate-300 mb-1 group-hover:text-cyan-500 transition-colors"></i>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ganti Data Lambang</p>
                            </div>
                        </div>
                    </div>
                    @error('icon') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Visi & Instruksi Lingkaran</label>
                    <textarea name="description" rows="5" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium shadow-inner leading-relaxed">{{ old('description', $circle->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        SINKRONKAN PEMBARUAN
                        <i class="fas fa-magic text-cyan-400 icon-glow transition-all group-hover:rotate-12"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('circle-icon-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
                document.getElementById('placeholder-content').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
