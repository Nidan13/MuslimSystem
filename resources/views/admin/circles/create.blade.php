@extends('layouts.admin')

@section('title', 'Pembentukan Lingkaran Baru')

@section('content')
<div class="max-w-4xl mx-auto animate-fadeIn">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.circles.index') }}" class="group p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-cyan-400 transition-all shadow-sm">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Pembentukan Lingkaran</h1>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Membangun Node Kolektif Baru ke dalam Matriks
            </p>
        </div>
    </div>

    <div class="glass-panel p-12 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-400/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <form action="{{ route('admin.circles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Nama Lingkaran</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-serif font-black text-xl uppercase tracking-wider placeholder-slate-200 shadow-inner" placeholder="misal: ORDO CAHAYA SUCI">
                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Ketua Lingkaran (Leader)</label>
                        <select name="leader_id" required class="w-full bg-slate-50 border-2 border-slate-200 rounded-[24px] text-teal-900 p-6 focus:border-cyan-400 focus:bg-white outline-none transition-all font-bold shadow-inner appearance-none">
                            <option value="">Pilih Hunter...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('leader_id') == $user->id ? 'selected' : '' }}>{{ $user->username }} ({{ $user->rankTier->slug ?? 'F' }}-Rank)</option>
                            @endforeach
                        </select>
                        @error('leader_id') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Level Awal</label>
                        <input type="number" name="level" value="1" readonly class="w-full bg-slate-100 border-2 border-slate-200 rounded-[24px] text-slate-400 p-6 outline-none font-black shadow-inner">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Lambang Lingkaran (Data Visual)</label>
                    <div class="relative group">
                        <input type="file" name="icon" id="circle-icon-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div class="w-full bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] p-8 text-center group-hover:border-cyan-400 transition-all shadow-inner relative overflow-hidden">
                            <div id="preview-container" class="hidden absolute inset-0 z-10 bg-white">
                                <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-teal-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <i class="fas fa-sync-alt text-white text-2xl animate-spin-slow"></i>
                                </div>
                            </div>
                            <div id="placeholder-content">
                                <i class="fas fa-upload text-3xl text-slate-300 mb-2 group-hover:text-cyan-400 transition-colors"></i>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Vektor Lambang (JPG, PNG, WEBP)</p>
                            </div>
                        </div>
                    </div>
                    @error('icon') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-teal-900/40 uppercase mb-3 tracking-[0.3em] ml-1">Visi & Instruksi Lingkaran</label>
                    <textarea name="description" rows="5" 
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-[32px] text-slate-700 p-8 outline-none focus:border-cyan-400 focus:bg-white transition-all font-medium placeholder-slate-200 shadow-inner leading-relaxed" placeholder="Jelaskan tujuan suci lingkaran dan protokol kolektif..."></textarea>
                    @error('description') <p class="text-red-500 text-[10px] font-bold uppercase mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full group relative overflow-hidden bg-teal-900 hover:bg-teal-800 py-6 rounded-[24px] font-serif font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-teal-950/30 transition-all active:scale-[0.99] border-t border-white/10">
                    <span class="relative flex items-center justify-center gap-4">
                        OTORISASI PEMBENTUKAN LINGKARAN
                        <i class="fas fa-stamp text-cyan-400 icon-glow transition-all group-hover:rotate-12"></i>
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
    .animate-spin-slow { animation: spin 3s linear infinite; }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
