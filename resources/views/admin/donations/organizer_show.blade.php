@extends('layouts.admin')

@section('title', 'Profil Mitra: ' . $organizer->username)

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Breadcrumb & Nav -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.donations.organizers') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-[#0E5F71] transition-all shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Manajemen Donasi / Profil Mitra</h2>
        </div>
    </div>

    <!-- Header Profile -->
    <div class="glass-panel rounded-[40px] p-10 bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#0E5F71]/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            <div class="w-28 h-28 rounded-[35px] bg-gradient-to-br from-[#0E5F71] to-[#2C9EB0] flex items-center justify-center text-4xl font-serif font-black text-white shadow-xl shadow-teal-900/20">
                {{ substr($organizer->username, 0, 1) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-4 mb-2">
                    <h1 class="text-4xl font-serif font-black text-[#0E5F71] tracking-tight">{{ $organizer->username }}</h1>
                    <span class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 text-[9px] font-black uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Mitra Terverifikasi
                    </span>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] mb-4">{{ $organizer->email }}</p>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <div class="px-5 py-2.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
                        <i class="fas fa-wallet text-[#2C9EB0] text-sm"></i>
                        <span class="text-[10px] font-black text-slate-400 uppercase">Saldo Dompet:</span>
                        <span class="text-xs font-black text-[#0E5F71] font-mono tracking-tighter">Rp{{ number_format($organizer->balance ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="text-center p-6 bg-slate-50 rounded-3xl border border-slate-100 min-w-[120px]">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Total Dana</p>
                    <p class="text-xl font-serif font-black text-[#0E5F71]">Rp{{ number_format($stats['total_funds'] / 1000000, 1) }}jt</p>
                </div>
                <div class="text-center p-6 bg-[#0E5F71] rounded-3xl text-white min-w-[120px] shadow-lg shadow-teal-900/20">
                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Kampanye</p>
                    <p class="text-xl font-serif font-black">{{ $stats['total_campaigns'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="glass-panel p-8 rounded-[40px] bg-white border border-slate-50 shadow-lg">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-6 border-b border-slate-50 pb-4">Informasi Kontak</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-[#2C9EB0]">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-0.5">Email Address</p>
                            <p class="text-xs font-black text-slate-700">{{ $organizer->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-[#2C9EB0]">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-0.5">Bergabung Sejak</p>
                            <p class="text-xs font-black text-slate-700">{{ $organizer->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-8 rounded-[40px] bg-slate-900 border border-slate-800 shadow-lg text-white">
                <h3 class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] mb-6 border-b border-white/5 pb-4">Actions</h3>
                <div class="grid grid-cols-1 gap-3">
                    <button class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition-all">Hubungi Mitra</button>
                    <button class="w-full py-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[10px] font-black uppercase tracking-widest hover:bg-amber-500/20 transition-all">Nonaktifkan Mitra</button>
                </div>
            </div>
        </div>

        <!-- Main Content (Campaigns) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-4">
                <h3 class="text-[11px] font-black text-[#0E5F71] uppercase tracking-[0.4em]">Kampanye Kelolaan ({{ $stats['total_campaigns'] }})</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($campaigns as $campaign)
                <div class="glass-panel rounded-[35px] bg-white border border-slate-100 shadow-md group hover:border-[#2C9EB0] transition-all overflow-hidden">
                    <div class="h-32 bg-slate-100 relative overflow-hidden">
                        <img src="{{ $campaign->image ?? 'https://ui-avatars.com/api/?name='.urlencode($campaign->title).'&background=0e5f71&color=fff' }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 rounded-lg bg-black/50 backdrop-blur-md text-[8px] font-black text-white uppercase tracking-widest border border-white/20">
                                {{ $campaign->status }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <h4 class="text-sm font-black text-[#0E5F71] uppercase leading-tight tracking-tight h-10 line-clamp-2">{{ $campaign->title }}</h4>
                        
                        <div class="space-y-2">
                             <div class="flex justify-between text-[9px] font-black uppercase">
                                <span class="text-slate-400 italic">Progress Dana</span>
                                <span class="text-[#0E5F71]">{{ number_format(($campaign->collected_amount / max($campaign->target_amount, 1)) * 100, 1) }}%</span>
                            </div>
                            <div class="h-1.5 bg-slate-50 rounded-full border border-slate-100 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-[#0E5F71] to-[#2C9EB0] transition-all" style="width: {{ ($campaign->collected_amount / max($campaign->target_amount, 1)) * 100 }}%"></div>
                            </div>
                            <p class="text-[8px] font-bold text-slate-300 uppercase tracking-widest">Terkumpul: Rp{{ number_format($campaign->collected_amount) }}</p>
                        </div>
                        
                        <a href="{{ route('admin.donations.show', $campaign) }}" class="flex items-center justify-center w-full py-3 rounded-xl bg-slate-50 border border-slate-100 text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:bg-[#0E5F71] group-hover:text-white transition-all">
                             Lihat Detail Kampanye
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center glass-panel rounded-[40px] border-2 border-dashed border-slate-100">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Belum ada kampanye terdaftar</p>
                </div>
                @endforelse
            </div>
        </div>
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
