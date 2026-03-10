@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<!-- High-End Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    
    <!-- Total Users -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-teal-500/5 rounded-full blur-2xl group-hover:bg-teal-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-users text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">User</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Total Pengguna</p>
            <h3 class="text-3xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                {{ number_format($stats['total_users']) }}
            </h3>
        </div>
    </div>

    <!-- Active Quests -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-scroll text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">Quests</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Misi Aktif</p>
            <h3 class="text-3xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                {{ number_format($stats['active_quests']) }}
            </h3>
        </div>
    </div>

    <!-- Dungeons (Rift Gates) -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-red-500 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-dungeon text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">Circles</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Misi Circle (Raid)</p>
            <h3 class="text-3xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                {{ number_format($stats['total_dungeons']) }}
            </h3>
        </div>
    </div>

    <!-- Pemasukan -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6 text-amber-500">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-amber-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">Revenue</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Total Pemasukan</p>
            <h3 class="text-2xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                Rp{{ number_format($stats['total_income'], 0, ',', '.') }}
            </h3>
        </div>
    </div>
</div>

<div class="space-y-10">
    
    <!-- Tables -->
    <div class="space-y-10">
        
        <!-- Recent Users Table -->
        <div class="glass-panel p-8 rounded-[40px] relative overflow-hidden">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wider uppercase italic">Pengguna <span class="text-cyan-400 font-sans tracking-normal ml-1">Baru</span></h4>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Status Keanggotaan Terkini</p>
                </div>
                <a href="{{ route('admin.hunters.index') }}" class="px-5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-[9px] font-black text-teal-900/60 hover:text-cyan-500 hover:border-cyan-400/30 transition-all uppercase tracking-widest">Semua Data</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 italic">
                            <th class="pb-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Profil</th>
                            <th class="pb-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Pangkat</th>
                            <th class="pb-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="pb-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentUsers as $user)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-teal-900 text-white flex items-center justify-center font-serif font-bold text-lg shadow-sm">
                                        {{ substr($user->username, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-teal-950 uppercase tracking-tight">{{ $user->username }}</p>
                                        <p class="text-[9px] text-slate-400 font-medium">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="px-3 py-1 rounded-lg bg-teal-50 text-teal-900 border border-teal-100 text-[8px] font-black uppercase tracking-widest">
                                    {{ $user->rankTier->name ?? 'NEWBIE' }}
                                </span>
                            </td>
                            <td class="py-4">
                                @if($user->is_active)
                                    <span class="flex items-center gap-1.5 text-[8px] font-black text-emerald-500 uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Aktif
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-[8px] font-black text-slate-300 uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $user->created_at->diffForHumans() }}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments Table -->
        <div class="glass-panel p-8 rounded-[40px] relative overflow-hidden">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wider uppercase italic">Pemasukan <span class="text-amber-500 font-sans tracking-normal ml-1">Terakhir</span></h4>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Log Pembayaran Berhasil Masuk</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em]">Live Stream</span>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($recentPayments as $pay)
                <div class="flex items-center justify-between p-5 bg-slate-50/50 rounded-3xl border border-slate-100 hover:border-amber-200 hover:bg-white transition-all cursor-default">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg border border-amber-100 shadow-sm">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-teal-950 uppercase tracking-tight">{{ $pay->user->username ?? 'User' }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Pembayaran #{{ substr($pay->id, -6) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-amber-600 tracking-tight italic">+Rp{{ number_format($pay->amount, 0, ',', '.') }}</p>
                        <p class="text-[9px] text-slate-400 font-medium uppercase">{{ $pay->paid_at->format('H:i') }} WIB</p>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-slate-300 font-bold uppercase tracking-[0.3em] text-[10px] bg-slate-50 rounded-[32px] border-2 border-dashed border-slate-100 italic">
                    Belum ada aliran koin terdeteksi hari ini
                </div>
                @endforelse
            </div>
        </div>

@endsection
