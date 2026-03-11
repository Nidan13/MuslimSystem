@extends('layouts.admin')

@section('title', 'Daftar Penyelenggara')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fadeIn">
        <div class="flex items-center">
            <div class="w-1.5 h-12 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full mr-4 shadow-[0_0_15px_rgba(14,95,113,0.3)]"></div>
            <div>
                <h2 class="text-3xl font-serif font-black text-[#0E5F71] tracking-tight uppercase">Penyelenggara Donasi</h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Daftar partner dan amil yang mengelola kampanye aktif</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-6 items-end">
            <div class="flex-1 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Penyelenggara</label>
                <div class="relative group">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2C9EB0] transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau email mitra..." 
                        class="w-full pl-14 pr-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#2C9EB0] focus:bg-white transition-all outline-none text-sm font-bold text-[#0E5F71] placeholder:text-slate-300">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-8 py-4 rounded-2xl bg-[#0E5F71] text-white shadow-lg shadow-teal-900/10 hover:bg-[#0f4c5c] transition-all font-black text-[10px] uppercase tracking-widest">
                    Cari Mitra
                </button>
                @if(request()->filled('search'))
                    <a href="{{ url()->current() }}" class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 hover:bg-slate-200 transition-all font-black text-[10px] uppercase tracking-widest">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Organizers List -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 italic border-b border-slate-100">
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                Organizer ID
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'id' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                Partner Informasi
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'username' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Status Keanggotaan</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Kampanye Kelolaan</th>
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($organizers as $org)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-8 px-8">
                            <span class="text-[10px] font-black text-slate-300 font-mono italic">#ORG-{{ str_pad($org->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-8 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0E5F71] to-[#2C9EB0] flex items-center justify-center text-white font-serif font-black text-xl shadow-lg shadow-teal-900/10 group-hover:scale-110 transition-transform">
                                    {{ substr($org->username, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-[#0E5F71] uppercase tracking-tight">{{ $org->username }}</h3>
                                    <p class="text-[10px] font-bold text-slate-400 lowercase tracking-widest mt-0.5">{{ $org->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-8 px-6 text-center">
                            <span class="px-4 py-1.5 rounded-full bg-[#0E5F71]/5 border border-[#0E5F71]/10 text-[9px] font-black text-[#0E5F71] uppercase tracking-widest">
                                <i class="fas fa-verified mr-1"></i> Mitra Terverifikasi
                            </span>
                        </td>
                        <td class="py-8 px-6 text-center font-serif font-black text-[#0E5F71] text-xl italic">
                            {{ \App\Models\DonationCampaign::where('organizer_id', $org->id)->count() }}
                        </td>
                        <td class="py-8 px-8 text-right whitespace-nowrap">
                            <a href="{{ route('admin.hunters.show', $org->id) }}" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl border-2 border-slate-50 text-[9px] font-black uppercase tracking-[.2em] text-slate-400 hover:bg-[#0E5F71] hover:text-white transition-all shadow-sm group-hover:border-[#0E5F71]/30">
                                <i class="fas fa-id-card-clip text-xs"></i>
                                Kelola Mitra
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users-slash text-3xl text-slate-400"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-[0.4em]">Belum Ada Mitra Terdaftar</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center px-4">
         <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">
            Total {{ $organizers->total() }} Penyelenggara Aktif
        </div>
        {{ $organizers->links() }}
    </div>
</div>
@endsection
