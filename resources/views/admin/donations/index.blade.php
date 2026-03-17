@extends('layouts.admin')

@section('title', 'Manajemen Donasi')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fadeIn">
        <div class="flex items-center">
            <div class="w-1.5 h-12 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full mr-4 shadow-[0_0_15px_rgba(14,95,113,0.3)]"></div>
            <div>
                <h2 class="text-3xl font-serif font-black text-[#0E5F71] tracking-tight uppercase">{{ $view_title ?? 'Daftar Kampanye' }}</h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">{{ isset($view_title) ? 'Filter sistem: ' . $view_title : 'Kelola kampanye bantuan dan dukungan finansial komunitas' }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.donations.create') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#0E5F71] text-white shadow-xl shadow-teal-950/20 hover:bg-[#0f4c5c] transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
                <i class="fas fa-plus text-[#00F2FF] transition-transform group-hover:rotate-90"></i>
                Buat Kampanye Baru
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glass-panel p-6 rounded-[32px] border-2 border-slate-50 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#0E5F71]/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Kampanye</p>
            <h3 class="text-3xl font-serif font-black text-[#0E5F71]">{{ $campaigns->total() }}</h3>
        </div>
        <div class="glass-panel p-6 rounded-[32px] border-2 border-slate-50 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#2C9EB0]/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Aktif</p>
            <h3 class="text-3xl font-serif font-black text-[#2C9EB0]">{{ $campaigns->where('status', 'active')->count() }}</h3>
        </div>
        <div class="glass-panel p-6 rounded-[32px] border-2 border-slate-50 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F59E0B]/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Selesai</p>
            <h3 class="text-3xl font-serif font-black text-[#F59E0B]">{{ $campaigns->where('status', 'completed')->count() }}</h3>
        </div>
        <div class="glass-panel p-6 rounded-[32px] border-2 border-slate-50 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-400/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Menunggu</p>
            <h3 class="text-3xl font-serif font-black text-rose-600">{{ $campaigns->where('status', 'pending')->count() }}</h3>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-6 items-end">
            <div class="flex-1 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Kampanye</label>
                <div class="relative group">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2C9EB0] transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik judul atau deskripsi..." 
                        class="w-full pl-14 pr-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#2C9EB0] focus:bg-white transition-all outline-none text-sm font-bold text-[#0E5F71] placeholder:text-slate-300">
                </div>
            </div>

            <div class="w-full md:w-48 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</label>
                <select name="status" onchange="this.form.submit()" 
                    class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-[#2C9EB0] focus:bg-white transition-all outline-none text-xs font-black text-[#0E5F71] uppercase tracking-widest appearance-none">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-8 py-4 rounded-2xl bg-[#0E5F71] text-white shadow-lg shadow-teal-900/10 hover:bg-[#0f4c5c] transition-all font-black text-[10px] uppercase tracking-widest">
                    Filter Data
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ url()->current() }}" class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 hover:bg-slate-200 transition-all font-black text-[10px] uppercase tracking-widest">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Campaigns List -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 italic border-b border-slate-100">
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                ID Log
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'id' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                Informasi Kampanye
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'title' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'collected_amount', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 group">
                                Pencapaian Dana
                                <i class="fas fa-sort text-[7px] opacity-20 group-hover:opacity-100 {{ request('sort') == 'collected_amount' ? 'opacity-100 text-[#2C9EB0]' : '' }}"></i>
                            </a>
                        </th>
                        <th class="py-5 px-6 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="py-5 px-8 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($campaigns as $campaign)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-8 px-8">
                            <span class="text-[10px] font-black text-slate-300 font-mono italic">#DON-{{ str_pad($campaign->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-8 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl border-2 border-slate-100 overflow-hidden shrink-0 shadow-sm group-hover:scale-110 transition-transform duration-500">
                                    <img src="{{ $campaign->image ?? 'https://ui-avatars.com/api/?name='.urlencode($campaign->title).'&background=0e5f71&color=fff' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="max-w-md">
                                    <h3 class="text-sm font-black text-[#0E5F71] uppercase tracking-tight leading-tight transition-colors group-hover:text-[#2C9EB0]">{{ $campaign->title }}</h3>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-50 text-[8px] font-black text-slate-400 uppercase tracking-[.2em] border border-slate-100">{{ $campaign->category->name ?? 'Zakat/Infaq' }}</span>
                                        <span class="text-slate-200 text-xs italic">/</span>
                                        <span class="text-[9px] font-black text-[#2C9EB0] uppercase tracking-widest">{{ $campaign->organizer->username ?? 'System Administrative' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-8 px-6">
                            <div class="space-y-3 max-w-[180px]">
                                <div class="flex justify-between items-end text-[10px] font-black uppercase tracking-tighter">
                                    <span class="text-[#0E5F71]">Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }}</span>
                                    <span class="text-[#2C9EB0] bg-[#2C9EB0]/5 px-2 py-0.5 rounded-lg border border-[#2C9EB0]/10">{{ number_format(($campaign->collected_amount / max($campaign->target_amount, 1)) * 100, 1) }}%</span>
                                </div>
                                <div class="h-2 w-full bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                                    <div class="h-full bg-gradient-to-r from-[#0E5F71] to-[#2C9EB0] transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(44,158,176,0.3)]" style="width: {{ min(($campaign->collected_amount / max($campaign->target_amount, 1)) * 100, 100) }}%"></div>
                                </div>
                                <div class="text-[8px] font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1">
                                    <i class="fas fa-bullseye text-[7px]"></i> Goal: Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </td>
                        <td class="py-8 px-6 text-center">
                            @php
                                $statusStyles = [
                                    'active' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/10 shadow-emerald-500/5 shadow-sm',
                                    'pending' => 'bg-amber-500/10 text-amber-600 border-amber-500/10 shadow-amber-500/5 shadow-sm',
                                    'completed' => 'bg-[#0E5F71] text-white border-[#0E5F71] shadow-lg shadow-teal-900/10',
                                    'rejected' => 'bg-rose-500/10 text-rose-600 border-rose-500/10 shadow-rose-500/5 shadow-sm',
                                ];
                            @endphp
                            <span class="px-5 py-2.5 rounded-2xl border text-[9px] font-black uppercase tracking-[.2em] {{ $statusStyles[$campaign->status] ?? 'bg-slate-50 text-slate-400 border-slate-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $campaign->status == 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-current opacity-30' }} inline-block"></span>
                                {{ $campaign->status }}
                            </span>
                        </td>
                        <td class="py-8 px-8 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                @if($campaign->status == 'pending')
                                    <form action="{{ route('admin.donations.approve', $campaign) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="button" onclick="confirmApprove(this, '{{ $campaign->organizer->username ?? 'Organizer' }}')" class="w-11 h-11 flex items-center justify-center bg-emerald-500 text-white rounded-2xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20" title="Setujui">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.donations.reject', $campaign) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-11 h-11 flex items-center justify-center bg-rose-500 text-white rounded-2xl hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/20" title="Tolak">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.donations.show', $campaign) }}" class="w-11 h-11 flex items-center justify-center bg-white border border-slate-100 text-[#0E5F71] hover:bg-[#0E5F71] hover:text-white rounded-2xl transition-all shadow-sm group/btn relative overflow-hidden">
                                     <div class="absolute inset-0 bg-gradient-to-tr from-[#0E5F71] to-[#2C9EB0] opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                    <i class="fas fa-eye text-xs relative z-10"></i>
                                </a>
                                <a href="{{ route('admin.donations.edit', $campaign) }}" class="w-11 h-11 flex items-center justify-center bg-white border border-slate-100 text-slate-400 hover:bg-[#2C9EB0] hover:text-white rounded-2xl transition-all shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.donations.destroy', $campaign) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $campaign->title }}')" class="w-11 h-11 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-rose-500 hover:text-white rounded-2xl transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-32 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <div class="w-24 h-24 bg-slate-50 rounded-[40px] flex items-center justify-center mb-6">
                                     <i class="fas fa-hand-holding-heart text-4xl text-slate-300"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400">Belum Ada Kampanye Donasi</span>
                                <a href="{{ route('admin.donations.create') }}" class="mt-6 text-[9px] font-black text-[#0E5F71] underline tracking-widest">BUAT KAMPANYE PERTAMA</a>
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
            Menampilkan {{ $campaigns->count() }} Entitas dari {{ $campaigns->total() }} Kampanye Aktif
        </div>
        {{ $campaigns->links() }}
    </div>
</div>
@endsection
