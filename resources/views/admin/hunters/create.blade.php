@extends('layouts.admin')

@section('title', 'Daftarkan Hunter Baru')

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.hunters.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Daftar Hunter Baru</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Inisialisasi akun hunter baru ke dalam sistem</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        
        <form action="{{ route('admin.hunters.store') }}" method="POST" class="space-y-10 relative z-10">
            @csrf
            
            <div class="space-y-8">
                <!-- Username -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Username Hunter</label>
                    <input type="text" name="username" value="{{ old('username') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-black transition-all placeholder-slate-200 uppercase tracking-tight" 
                        placeholder="MISAL: SUNG JIN-WOO">
                    @error('username') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1 tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Email -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-bold transition-all placeholder-slate-200" 
                            placeholder="hunter@example.com">
                        @error('email') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1 tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kata Sandi</label>
                        <input type="password" name="password" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-bold transition-all" 
                            placeholder="••••••••">
                        @error('password') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1 tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    <!-- Rank Tier -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pangkat Awal (Rank)</label>
                        <select name="rank_tier_id" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all appearance-none cursor-pointer uppercase tracking-widest text-teal-900">
                            @foreach($rankTiers as $tier)
                            <option value="{{ $tier->id }}">TIER {{ $tier->slug }} — {{ $tier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">Gender</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="gender" value="Male" checked class="hidden peer">
                                <div class="py-4 rounded-xl border border-slate-100 bg-slate-50 text-slate-400 font-black text-center peer-checked:bg-teal-900 peer-checked:text-white transition-all uppercase tracking-widest text-[9px]">
                                    <i class="fas fa-mars mr-1"></i> Laki-laki
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="gender" value="Female" class="hidden peer">
                                <div class="py-4 rounded-xl border border-slate-100 bg-slate-50 text-slate-400 font-black text-center peer-checked:bg-pink-500 peer-checked:text-white transition-all uppercase tracking-widest text-[9px]">
                                    <i class="fas fa-venus mr-1"></i> Perempuan
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Level, EXP, Soul Points -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Level Hunter</label>
                        <input type="number" name="level" value="{{ old('level', 1) }}" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-mono font-black text-teal-900 transition-all">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Current EXP</label>
                        <input type="number" name="current_exp" value="{{ old('current_exp', 0) }}" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-mono font-black text-teal-900 transition-all">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Soul Points (SP)</label>
                        <input type="number" name="soul_points" value="{{ old('soul_points', 0) }}" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-mono font-black text-teal-900 transition-all">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-8 border-t border-slate-50">
                <button type="submit" class="w-full bg-teal-900 hover:bg-teal-800 py-6 rounded-3xl font-serif font-black text-white uppercase tracking-[0.4em] shadow-xl shadow-teal-950/20 transition-all active:scale-[0.98]">
                    Konfirmasi Hunter Baru
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
