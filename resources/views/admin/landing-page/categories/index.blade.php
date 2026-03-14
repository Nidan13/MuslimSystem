@extends('layouts.admin')

@section('title', 'Kategori Warta')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.landing-page.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-nu-indigo shadow-sm hover:border-nu-teal transition-all active:scale-95">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-serif font-black text-nu-indigo tracking-tight uppercase">Master Kategori</h2>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-nu-teal animate-pulse"></span>
                    Data Master Kategori Warta Landing Page
                </p>
            </div>
        </div>
        
        <a href="{{ route('admin.landing-page.categories.create') }}" class="group relative px-8 py-4 rounded-2xl bg-nu-indigo text-white font-serif shadow-xl shadow-nu-indigo/20 hover:bg-slate-900 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.15em] text-[10px] font-black uppercase">
                <i class="fas fa-plus text-nu-teal transition-transform group-hover:rotate-90"></i>
                Tambah Kategori
            </span>
        </a>
    </div>

    <!-- Table -->
    <div class="max-w-4xl glass-panel p-0 rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 italic">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nama Kategori</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Slug</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Jumlah Warta</th>
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($categories as $category)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4">
                            <h3 class="text-sm font-black text-nu-indigo uppercase tracking-tight group-hover:text-nu-teal transition-colors duration-300">
                                {{ $category->name }}
                            </h3>
                        </td>
                        <td class="py-6 px-4">
                            <code class="text-[9px] bg-slate-50 text-slate-400 px-2 py-1 rounded-md">{{ $category->slug }}</code>
                        </td>
                        <td class="py-6 px-4 text-center">
                            <span class="text-xs font-black text-nu-indigo/30 font-serif">{{ $category->news_count }} Artikel</span>
                        </td>
                        <td class="py-6 px-4 text-right">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <a href="{{ route('admin.landing-page.categories.edit', $category) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-nu-indigo hover:border-nu-teal transition-all shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.landing-page.categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-red-400 hover:border-red-400 hover:text-red-600 transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center text-slate-400 italic font-serif">Belum ada kategori terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
