@extends('layouts.admin')

@section('title', 'Konfigurasi Pembayaran')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header -->
    <div class="flex items-center">
        <div class="w-1.5 h-12 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full mr-4 shadow-[0_0_15px_rgba(14,95,113,0.3)]"></div>
        <div>
            <h1 class="text-3xl font-serif font-black text-[#0E5F71] tracking-tight uppercase">Sistem Pembayaran</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Aktifkan atau nonaktifkan channel payment untuk pengguna</p>
        </div>
    </div>

    @if(session('success'))
        <div class="px-6 py-4 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 flex items-center gap-4 animate-bounce-in">
            <i class="fas fa-check-circle"></i>
            <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <h3 class="text-xs font-black text-teal-950 uppercase tracking-[0.2em] border-b border-slate-50 pb-4 mb-8">
                Pilih Gateway Pembayaran Aktif
            </h3>

            <!-- Toggles Group -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Plink (Payment Gateway) -->
                <label class="flex items-center justify-between p-6 rounded-3xl border-2 {{ $settings['payment_method_plink'] ? 'border-[#2C9EB0] bg-teal-50/50' : 'border-slate-50 bg-slate-50/50' }} hover:border-[#2C9EB0]/50 transition-all cursor-pointer group">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-[#2C9EB0] shadow-sm border border-slate-100 group-hover:scale-110 transition-transform">
                            <i class="fas fa-satellite-dish text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-[#0E5F71] uppercase tracking-tight">API Gateway</h4>
                            <p class="text-[9px] font-bold text-slate-400 tracking-widest uppercase mt-0.5">VA & QRIS (Plink)</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" name="payment_method_plink" class="sr-only peer" {{ $settings['payment_method_plink'] ? 'checked' : '' }}>
                        <div class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2C9EB0] shadow-inner"></div>
                    </div>
                </label>

                <!-- Transfer Manual -->
                <label class="flex items-center justify-between p-6 rounded-3xl border-2 {{ $settings['payment_method_manual'] ? 'border-[#F59E0B] bg-amber-50/50' : 'border-slate-50 bg-slate-50/50' }} hover:border-[#F59E0B]/50 transition-all cursor-pointer group">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-[#F59E0B] shadow-sm border border-slate-100 group-hover:scale-110 transition-transform">
                            <i class="fas fa-money-bill-transfer text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-[#0E5F71] uppercase tracking-tight">Manual Transfer</h4>
                            <p class="text-[9px] font-bold text-slate-400 tracking-widest uppercase mt-0.5">Verifikasi Admin</p>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" name="payment_method_manual" class="sr-only peer" {{ $settings['payment_method_manual'] ? 'checked' : '' }}>
                        <div class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#F59E0B] shadow-inner"></div>
                    </div>
                </label>
            </div>

            <div class="mt-8 space-y-4">
                <h3 class="text-xs font-black text-teal-950 uppercase tracking-[0.2em] border-b border-slate-50 pb-4 mb-4">
                    Biaya Platform
                </h3>
                <div class="space-y-3 max-w-xs">
                    <label class="text-[10px] font-black text-[#0E5F71] uppercase tracking-widest block">Total Potongan Platform (%)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <i class="fas fa-percent text-slate-300 group-focus-within:text-[#0E5F71] transition-colors"></i>
                        </div>
                        <input type="number" name="total_system_fee_percentage" step="0.1" value="{{ $settings['total_system_fee_percentage'] ?? 0 }}" 
                               class="w-full pl-12 pr-12 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 outline-none focus:border-[#0E5F71] focus:bg-white transition-all shadow-inner" placeholder="0.0">
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Ini adalah potongan otomatis per transaksi yang masuk ke sistem.</p>
                </div>
            </div>

            <div class="pt-8">
                <button type="submit" class="px-10 py-4 rounded-2xl bg-[#0E5F71] text-white shadow-xl shadow-teal-900/20 hover:bg-[#0f4c5c] hover:scale-105 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black flex items-center gap-3">
                    <i class="fas fa-save"></i>
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
