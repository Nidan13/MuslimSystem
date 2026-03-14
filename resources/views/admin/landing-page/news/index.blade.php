@extends('layouts.admin')

@section('title', 'Berita & Warta')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.landing-page.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-nu-indigo shadow-sm hover:border-nu-teal transition-all active:scale-95">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-serif font-black text-nu-indigo tracking-tight uppercase">Warta Ummah</h2>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-nu-teal animate-pulse"></span>
                    Manajemen Konten Berita Landing Page
                </p>
            </div>
        </div>
        
        <a href="{{ route('admin.landing-page.news.create') }}" class="group relative px-8 py-4 rounded-2xl bg-nu-indigo text-white font-serif shadow-xl shadow-nu-indigo/20 hover:bg-slate-900 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.15em] text-[10px] font-black uppercase">
                <i class="fas fa-plus text-nu-teal transition-transform group-hover:rotate-90"></i>
                Buat Warta Baru
            </span>
        </a>
    </div>

    <!-- Table -->
    <div class="glass-panel p-0 rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 italic">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pratinjau</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Informasi Warta</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kategori</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($news as $item)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4">
                            <div class="w-24 h-14 rounded-xl bg-slate-100 overflow-hidden border-2 border-slate-50">
                                @if($item->image_url)
                                <img src="{{ $item->image_url }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-slate-200">
                                    <i class="fas fa-image"></i>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-6 px-4">
                            <div class="max-w-md">
                                <h3 class="text-sm font-black text-nu-indigo uppercase tracking-tight group-hover:text-nu-teal transition-colors duration-300">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-[9px] text-slate-400 font-medium italic truncate mt-1 tracking-wide">
                                    {{ Str::limit($item->summary, 70) }}
                                </p>
                            </div>
                        </td>
                        <td class="py-6 px-4">
                            <span class="px-2.5 py-1 rounded-lg bg-teal-50 border border-teal-100 text-teal-600 text-[8px] font-black uppercase tracking-widest">
                                {{ $item->category ? $item->category->name : 'N/A' }}
                            </span>
                        </td>
                        <td class="py-6 px-4 text-center">
                            <div class="flex flex-col gap-2 items-center">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 text-slate-300 border border-slate-100 text-[9px] font-black uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        Draft
                                    </span>
                                @endif
                                <div class="flex gap-1 justify-center mt-1">
                                    @if($item->is_for_user)
                                        <span class="px-2 py-0.5 rounded bg-nu-indigo/10 text-nu-indigo text-[7px] font-black uppercase tracking-wider" title="Visible in App">App</span>
                                    @endif
                                    @if($item->is_for_landing_page)
                                        <span class="px-2 py-0.5 rounded bg-nu-teal/10 text-nu-teal text-[7px] font-black uppercase tracking-wider" title="Visible on Landing Page">Web</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-4 text-right">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <a href="{{ route('admin.landing-page.news.edit', $item) }}" class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.landing-page.news.destroy', $item) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center text-slate-400 italic font-serif">Belum ada warta yang diterbitkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-8">
        {{ $news->links() }}
    </div>
</div>
@endsection
