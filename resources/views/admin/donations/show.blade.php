@extends('layouts.admin')

@section('title', 'Detail Kampanye Donasi')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.donations.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-[#0E5F71] transition-all shadow-sm active:scale-95">
                <i class="fas fa-chevron-left text-sm"></i>
            </a>
            <div class="flex items-center">
                <div class="w-1.5 h-12 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full mr-4 shadow-[0_0_15px_rgba(14,95,113,0.3)]"></div>
                <div>
                    <h1 class="text-3xl font-serif font-black text-[#0E5F71] tracking-tight uppercase">{{ $donation->title }}</h1>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">ID: #DON-{{ str_pad($donation->id, 4, '0', STR_PAD_LEFT) }} • Penyelenggara: {{ $donation->organizer->username ?? 'System' }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('admin.donations.edit', $donation) }}" class="flex items-center gap-3 px-6 py-4 rounded-2xl bg-white border border-slate-100 text-[#0E5F71] shadow-sm hover:bg-slate-50 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
                <i class="fas fa-edit text-[#2C9EB0]"></i>
                Edit Kampanye
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl space-y-8">
                <div class="aspect-video w-full rounded-[32px] overflow-hidden border-4 border-slate-50 shadow-xl relative group">
                    <img src="{{ $donation->image ?? 'https://ui-avatars.com/api/?name='.urlencode($donation->title).'&background=0f4c5c&color=fff&size=512' }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-8 left-8">
                        <span class="px-4 py-2 bg-[#0E5F71] text-white rounded-xl text-[10px] font-black uppercase tracking-widest">
                            {{ $donation->category->name ?? 'Sosial & Keagamaan' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 py-10 border-y border-slate-50">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Status Sistem</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $donation->status == 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            <p class="text-sm font-black text-[#0E5F71] uppercase tracking-tight">{{ $donation->status }}</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Terkumpul</p>
                        <p class="text-sm font-black text-[#2C9EB0] uppercase tracking-tight">Rp {{ number_format($donation->collected_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Target Dana</p>
                        <p class="text-sm font-black text-[#0E5F71] uppercase tracking-tight">Rp {{ number_format($donation->target_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Deadline</p>
                        <p class="text-sm font-black text-[#F59E0B] uppercase tracking-tight">{{ $donation->deadline ? \Carbon\Carbon::parse($donation->deadline)->format('d M Y') : 'Tanpa Batas' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-black text-teal-950 uppercase tracking-[0.2em] flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full"></div>
                        Informasi Kampanye
                    </h3>
                    <div class="prose prose-slate max-w-none text-base text-slate-600 leading-relaxed font-medium">
                        {!! nl2br(e($donation->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Distribution Reports -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-teal-950 uppercase tracking-[0.2em] flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-gradient-to-b from-[#F59E0B] to-amber-600 rounded-full"></div>
                        Laporan Penyaluran (Update Dokumentasi)
                    </h3>
                    <button class="px-5 py-2.5 bg-white border border-slate-100 rounded-xl text-[9px] font-black uppercase text-[#0E5F71] tracking-widest hover:bg-[#0E5F71] hover:text-white transition-all shadow-sm">
                         Tambah Laporan Baru
                    </button>
                </div>
                
                <div class="grid grid-cols-1 gap-6">
                    @forelse($donation->reports as $report)
                    <div class="glass-panel p-8 rounded-[32px] bg-white border-2 border-slate-50 relative overflow-hidden group hover:border-[#2C9EB0]/30 transition-all">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#2C9EB0]/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#0E5F71]/5 flex items-center justify-center text-[#0E5F71]">
                                    <i class="fas fa-file-contract text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-black text-[#0E5F71] uppercase tracking-tight">{{ $report->title }}</h4>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">{{ $report->created_at->format('d F Y') }}</p>
                                </div>
                            </div>
                            <div class="bg-emerald-500 text-white px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                                Disalurkan: Rp {{ number_format($report->amount_spent, 0, ',', '.') }}
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed font-medium">{{ $report->content }}</p>
                    </div>
                    @empty
                    <div class="py-20 bg-white rounded-[40px] border-2 border-dashed border-slate-100 text-center opacity-40">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-invoice text-3xl text-slate-300"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Belum ada laporan penyaluran</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Progress and Donations -->
        <div class="space-y-8">
            <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl space-y-8">
                 <h3 class="text-xs font-black text-teal-950 uppercase tracking-[0.2em] text-center">Progres Donasi</h3>
                 
                 <div class="relative w-52 h-52 mx-auto">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="104" cy="104" r="92" stroke="currentColor" stroke-width="14" fill="transparent" class="text-slate-50" />
                        <circle cx="104" cy="104" r="92" stroke="url(#tealGradient)" stroke-width="14" fill="transparent" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * pi() * 92 }}" 
                                stroke-dashoffset="{{ (1 - min($donation->collected_amount / max($donation->target_amount, 1), 1)) * (2 * pi() * 92) }}" 
                                class="transition-all duration-1000 ease-out" />
                        <defs>
                            <linearGradient id="tealGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0E5F71" />
                                <stop offset="100%" stop-color="#2C9EB0" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-serif font-black text-[#0E5F71]">{{ number_format(($donation->collected_amount / max($donation->target_amount, 1)) * 100, 1) }}%</span>
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">Tercapai</span>
                    </div>
                 </div>

                 <div class="space-y-5 pt-8 border-t border-slate-50">
                    <div class="flex justify-between items-center">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest text-left">Terkumpul</p>
                            <p class="text-lg font-black text-[#0E5F71] tracking-tight text-left">Rp {{ number_format($donation->collected_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right space-y-1">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Target Akhir</p>
                            <p class="text-lg font-black text-slate-800 tracking-tight">Rp {{ number_format($donation->target_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                        <p class="text-[10px] font-black text-[#F59E0B] uppercase tracking-widest">
                            Dibutuhkan Rp {{ number_format($donation->target_amount - $donation->collected_amount, 0, ',', '.') }} Lagi
                        </p>
                    </div>
                 </div>
            </div>

            <!-- Recent Donations -->
            <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl space-y-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-teal-950 uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="fas fa-hand-holding-heart text-[#0E5F71]"></i>
                        Donatur Baru
                    </h3>
                    <span class="text-[9px] font-black text-slate-300 uppercase bg-slate-50 px-3 py-1 rounded-full">LIVE</span>
                </div>
                <div class="space-y-2">
                    @forelse($donation->donations as $log)
                    <div class="flex items-center gap-4 py-4 group cursor-pointer hover:bg-slate-50/50 rounded-2xl px-2 transition-all">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#0E5F71] to-[#2C9EB0] flex items-center justify-center text-white font-serif font-black text-base shadow-lg shadow-teal-900/10 shrink-0">
                            {{ substr($log->donator_name ?? 'H', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-black text-[#0E5F71] truncate uppercase tracking-tight">{{ $log->donator_name ?? 'Hunter Anonymous' }}</h4>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black text-[#2C9EB0] italic tracking-tight">Rp {{ number_format($log->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="py-10 text-center opacity-30">
                        <i class="fas fa-ghost text-2xl mb-2"></i>
                        <p class="text-[9px] font-black uppercase tracking-widest">Belum ada donatur</p>
                    </div>
                    @endforelse
                </div>
                
                <button class="w-full py-4 border-2 border-slate-50 rounded-2xl text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] hover:bg-slate-50 hover:text-[#0E5F71] transition-all">
                    Lihat Seluruh Riwayat
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
