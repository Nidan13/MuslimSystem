@extends('layouts.admin')

@section('title', 'Daftarkan Hunter Baru')

@section('content')
<div class="max-w-4xl mx-auto pb-20 animate-fadeIn">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.hunters.index') }}" class="group p-4 rounded-2xl bg-white border border-slate-200 hover:border-nu-teal transition-all shadow-sm">
            <i class="fas fa-chevron-left text-slate-400 group-hover:text-nu-teal"></i>
        </a>
        <div>
            <h1 class="text-4xl font-serif font-black text-slate-900 tracking-tight">Daftarkan Hunter Baru</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em] mt-1 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-nu-teal animate-pulse"></span>
                Inisialisasi Node Koneksi Baru
            </p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-nu-indigo to-nu-teal"></div>
        
        <form action="{{ route('admin.hunters.store') }}" method="POST" class="p-10 lg:p-14 space-y-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Username -->
                <div class="col-span-full">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest ml-1">Identitas Digital (Username)</label>
                    <input type="text" name="username" value="{{ old('username') }}" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 p-5 focus:border-nu-teal focus:ring-4 focus:ring-nu-teal/5 outline-none transition-all font-serif font-black text-xl shadow-inner" placeholder="misal: Sung Jin-Woo">
                    @error('username') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1 tracking-widest">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest ml-1">Tautan Komunikasi (Email)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-slate-700 p-5 focus:border-nu-teal focus:ring-4 focus:ring-nu-teal/5 outline-none transition-all font-mono font-bold text-sm shadow-inner" placeholder="hunter@system.com">
                    @error('email') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1 tracking-widest">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest ml-1">Protokol Akses (Kata Sandi)</label>
                    <input type="password" name="password" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-slate-700 p-5 focus:border-nu-teal focus:ring-4 focus:ring-nu-teal/5 outline-none transition-all font-mono font-bold text-sm shadow-inner" placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1 tracking-widest">{{ $message }}</p> @enderror
                </div>

                <!-- Rank Tier -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest ml-1">Otoritas Awal (Rank)</label>
                    <div class="relative group">
                        <select name="rank_tier_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 p-5 focus:border-nu-teal focus:ring-4 focus:ring-nu-teal/5 outline-none appearance-none cursor-pointer font-black transition-all shadow-inner">
                            @foreach($rankTiers as $tier)
                            <option value="{{ $tier->id }}">TIER {{ $tier->slug }} — {{ $tier->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none group-hover:text-nu-teal transition-colors"></i>
                    </div>
                </div>

                <!-- Job Class -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest ml-1">Node Spesialisasi (Kelas)</label>
                    <div class="relative group">
                        <select name="job_class" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 p-5 focus:border-nu-teal focus:ring-4 focus:ring-nu-teal/5 outline-none appearance-none cursor-pointer font-bold transition-all shadow-inner">
                            @foreach($jobClasses as $job)
                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none group-hover:text-nu-teal transition-colors"></i>
                    </div>
                </div>

                <!-- Gender -->
                <div class="col-span-full">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest ml-1">Identitas Biologis (Gender)</label>
                    <div class="flex gap-6">
                        <label class="relative flex-1 cursor-pointer group/opt">
                            <input type="radio" name="gender" value="Male" checked class="peer hidden">
                            <div class="p-6 rounded-2xl border-2 border-slate-100 bg-white text-slate-400 font-black text-center peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all uppercase tracking-widest text-[11px] shadow-sm hover:border-slate-300">
                                <i class="fas fa-mars mr-2"></i> Laki-laki
                            </div>
                        </label>
                        <label class="relative flex-1 cursor-pointer group/opt">
                            <input type="radio" name="gender" value="Female" class="peer hidden">
                            <div class="p-6 rounded-2xl border-2 border-slate-100 bg-white text-slate-400 font-black text-center peer-checked:border-pink-500 peer-checked:bg-pink-500 peer-checked:text-white transition-all uppercase tracking-widest text-[11px] shadow-sm hover:border-slate-300">
                                <i class="fas fa-venus mr-2"></i> Perempuan
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="pt-10">
                <button type="submit" class="w-full py-6 rounded-3xl bg-slate-900 border-b-4 border-slate-950 text-white font-serif font-black uppercase tracking-[0.4em] shadow-xl hover:bg-nu-indigo hover:border-nu-indigo transition-all active:scale-[0.98] active:border-b-0 flex items-center justify-center gap-4">
                    Validasi & Daftarkan Hunter
                    <i class="fas fa-plus-circle"></i>
                </button>
                <div class="mt-8 text-center">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em]">Autentikasi Level Sistem Diperlukan</p>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    body { background-color: #f8fafc; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.6s cubic-bezier(0.19, 1, 0.22, 1) forwards;
    }

    input:focus, select:focus {
        background-color: #fff !important;
    }
</style>
@endsection
