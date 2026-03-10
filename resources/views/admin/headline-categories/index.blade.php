@extends('layouts.admin')

@section('title', 'Kategori Berita')

@section('content')
<div class="space-y-10">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fadeIn">
        <div>
            <h2 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Kategori Berita</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Master Data Kategori Berita Utama
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- Add Button -->
            <a href="{{ route('admin.headline-categories.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden font-serif uppercase tracking-widest text-[10px] font-black">
                <span class="relative flex items-center gap-3">
                    <i class="fas fa-plus text-cyan-400 icon-glow transition-transform group-hover:rotate-90"></i>
                    Tambah Kategori Berita
                </span>
            </a>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-panel p-0 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6 pt-5">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b-2 border-slate-100 uppercase">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] w-20">ID</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">NAMA</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">DESKRIPSI</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center">STATUS</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans">
                    @forelse($categories as $category)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4">
                            <span class="text-[10px] font-black text-slate-300 font-mono tracking-tighter uppercase whitespace-nowrap">#BRT-{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-6 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center border-2 border-slate-100 shadow-sm" style="background-color: {{ $category->color ?? '#f1f5f9' }}">
                                    <i class="{{ $category->icon ?? 'fas fa-newspaper' }} text-[10px] {{ $category->color ? 'text-white' : 'text-slate-400' }}"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-teal-900 uppercase tracking-tight">{{ $category->name }}</span>
                                    <span class="text-[9px] font-mono text-slate-400">{{ $category->slug }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                            <span class="text-xs text-slate-500 line-clamp-2 max-w-xs">{{ $category->description ?? '-' }}</span>
                        </td>
                        <td class="py-6 px-6 text-center">
                            @if($category->is_active)
                                <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest">AKTIF</span>
                            @else
                                <span class="bg-slate-50 text-slate-400 border border-slate-100 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest">NON-AKTIF</span>
                            @endif
                        </td>
                        <td class="py-6 px-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3 transition-all duration-300">
                                <a href="{{ route('admin.headline-categories.edit', $category) }}" class="p-3 bg-white border-2 border-slate-100 text-teal-900 hover:text-cyan-500 hover:border-cyan-200 rounded-xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50" title="Edit Kategori">
                                    <i class="fas fa-sliders text-xs"></i>
                                </a>
                                <form action="{{ route('admin.headline-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-3 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50" title="Hapus Kategori">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-tags text-slate-100 text-6xl mb-4"></i>
                                <span class="text-slate-300 text-[10px] font-black uppercase tracking-[0.3em]">Belum ada kategori yang tersedia</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $categories->links() }}
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
    .glass-panel { backdrop-filter: blur(16px); }
</style>
@endsection
