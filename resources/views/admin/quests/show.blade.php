@extends('layouts.admin')

@section('title', 'Detail Misi: ' . $quest->title)

@section('content')
<div class="w-full space-y-8 animate-fadeIn pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="flex items-center gap-5">
            <a href="{{ route('admin.quests.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 hover:border-teal-200 transition-all shadow-sm active:scale-95 group">
                <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            </a>
            <div>
                <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">{{ $quest->title }}</h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-1 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Kategori: {{ $quest->questType->name }} <span class="text-slate-300 mx-1">|</span> ID Misi: #{{ str_pad($quest->id, 5, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.quests.edit', $quest) }}" class="px-6 py-3 bg-white border-2 border-slate-100 text-teal-900 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-sm hover:border-teal-300 hover:text-teal-700 transition-all active:scale-95 flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit Misi
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- BARIS 1: Deskripsi (Kiri 2/3) & Info Status (Kanan 1/3) -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[30px] border-2 border-slate-50 shadow-sm flex flex-col">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Deskripsi & Perintah Misi</h3>
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 flex-1">
                <p class="text-base font-medium text-slate-700 leading-relaxed">
                    {{ $quest->description ?: 'Tidak ada deskripsi rinci untuk misi ini.' }}
                </p>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <div class="bg-white p-6 rounded-[30px] border-2 border-slate-50 shadow-sm flex-1 flex flex-col items-center justify-center text-center">
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Syarat Pangkat</h3>
                <div class="text-2xl font-serif font-black {{ $quest->rankTier->color_code ? '' : 'text-teal-900' }} uppercase mb-2" style="{{ $quest->rankTier->color_code ? 'color: ' . $quest->rankTier->color_code : '' }}">
                    {{ $quest->rankTier->slug ?? 'FREE' }}
                </div>
                <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                      {{ $quest->rankTier->name ?? 'Semua Pangkat' }}
                </div>
            </div>
            
            <div class="p-6 rounded-[30px] {{ $quest->is_mandatory ? 'bg-red-50 border border-red-100' : 'bg-teal-50 border border-teal-100' }} flex flex-col items-center justify-center text-center shadow-sm">
                <h3 class="text-[9px] font-black {{ $quest->is_mandatory ? 'text-red-400' : 'text-teal-400' }} uppercase tracking-widest mb-3">Sifat Misi</h3>
                <p class="text-[11px] font-black {{ $quest->is_mandatory ? 'text-red-600' : 'text-teal-600' }} uppercase tracking-wider mb-1">
                    {{ $quest->is_mandatory ? 'Misi Wajib' : 'Opsional' }}
                </p>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                    {{ $quest->is_mandatory ? '(Harian)' : '(Bebas)' }}
                </span>
            </div>
        </div>

        <!-- BARIS 2: Persyaratan (Kiri 2/3) & Hadiah (Kanan 1/3) -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[30px] border-2 border-slate-50 shadow-sm flex flex-col">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-4">Persyaratan Penyelesaian Misi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1 content-start">
                @forelse($quest->requirements ?? [] as $type => $target)
                <div class="flex items-center justify-between p-5 bg-white border-2 border-slate-100 rounded-2xl hover:border-teal-200 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center">
                            <i class="fas fa-tasks text-teal-600 text-xs"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">{{ str_replace('_', ' ', $type) }}</span>
                    </div>
                    <span class="text-lg font-black text-teal-900 font-mono">{{ $target }}</span>
                </div>
                @empty
                <div class="col-span-full py-12 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 h-full flex flex-col items-center justify-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tidak Ada Syarat Khusus</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-8 rounded-[30px] border-2 border-slate-50 shadow-sm flex flex-col">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-4 text-center">Hadiah & Kompensasi</h3>
            <div class="space-y-4 flex-1 content-start">
                <div class="flex justify-between items-center bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-star text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Experience</p>
                            <span class="text-[11px] font-black text-slate-700 uppercase">EXP Reward</span>
                        </div>
                    </div>
                    <span class="text-xl font-black text-blue-600 font-mono">{{ number_format($quest->reward_exp) }}</span>
                </div>

                <div class="flex justify-between items-center bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                            <i class="fas fa-coins text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Mata Uang</p>
                            <span class="text-[11px] font-black text-slate-700 uppercase">Soul Points</span>
                        </div>
                    </div>
                    <span class="text-xl font-black text-amber-600 font-mono">{{ number_format($quest->reward_soul_points) }}</span>
                </div>

                <div class="flex justify-between items-center bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="fas fa-heartbeat text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Beban Tubuh</p>
                            <span class="text-[11px] font-black text-slate-700 uppercase">Kelelahan</span>
                        </div>
                    </div>
                    <span class="text-xl font-black text-red-500 font-mono">{{ $quest->penalty_fatigue }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
