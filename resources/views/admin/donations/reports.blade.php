@extends('layouts.admin')

@section('title', 'Laporan Penyaluran')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fadeIn">
        <div class="flex items-center">
            <div class="w-1.5 h-12 bg-gradient-to-b from-[#F59E0B] to-amber-600 rounded-full mr-4 shadow-[0_0_15px_rgba(245,158,11,0.3)]"></div>
            <div>
                <h2 class="text-3xl font-serif font-black text-[#0E5F71] tracking-tight uppercase">Laporan Penyaluran</h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Update penyaluran dana dari berbagai kampanye aktif</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-6 items-end">
            <div class="flex-1 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Laporan</label>
                <div class="relative group">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2C9EB0] transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik judul laporan atau isi dokumentasi..." 
                        class="w-full pl-14 pr-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#2C9EB0] focus:bg-white transition-all outline-none text-sm font-bold text-[#0E5F71] placeholder:text-slate-300">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-8 py-4 rounded-2xl bg-[#0E5F71] text-white shadow-lg shadow-teal-900/10 hover:bg-[#0f4c5c] transition-all font-black text-[10px] uppercase tracking-widest">
                    Cari Laporan
                </button>
                @if(request()->filled('search'))
                    <a href="{{ url()->current() }}" class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 hover:bg-slate-200 transition-all font-black text-[10px] uppercase tracking-widest">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Reports List -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 italic border-b border-slate-100">
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                Update ID
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'id' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest">Kampanye Terkait</th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                Detail Dokumentasi
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'title' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount_spent', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group justify-center">
                                Nominal
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'amount_spent' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reports as $report)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-8 px-8">
                            <span class="text-[10px] font-black text-slate-300 font-mono italic">#UPD-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-8 px-6">
                            <div class="max-w-[220px]">
                                <h4 class="text-[11px] font-black text-[#0E5F71] uppercase leading-tight truncate tracking-tight">{{ $report->campaign->title ?? 'Campaign Deleted' }}</h4>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#2C9EB0]"></span>
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $report->campaign->organizer->username ?? 'Unknown Vendor' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-8 px-6">
                            <div class="max-w-md">
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-[#0E5F71] transition-colors">{{ $report->title }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold italic mt-1 leading-relaxed line-clamp-2 uppercase tracking-tighter">{{ $report->content }}</p>
                            </div>
                        </td>
                        <td class="py-8 px-6 text-center">
                            <span class="px-5 py-2.5 bg-emerald-500/10 text-emerald-600 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-emerald-500/10 shadow-sm shadow-emerald-500/5">
                                Rp {{ number_format($report->amount_spent, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-8 px-8 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.donations.show', $report->donation_campaign_id) }}" class="w-11 h-11 flex items-center justify-center bg-white border border-slate-100 text-[#0E5F71] hover:bg-[#0E5F71] hover:text-white rounded-2xl transition-all shadow-sm">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                                <form action="{{ route('admin.donation-reports.destroy', $report) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $report->title }}')" class="w-11 h-11 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-rose-500 hover:text-white rounded-2xl transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                     <i class="fas fa-file-signature text-3xl text-slate-400"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-[0.4em]">Belum Ada Laporan Terdaftar</span>
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
            Menampilkan {{ $reports->count() }} Laporan Penyaluran
        </div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
