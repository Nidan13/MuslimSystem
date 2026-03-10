@extends('layouts.admin')

@section('title', 'Divine Artifact Repository')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Pasar Kuno</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shadow-[0_0_10px_#fbbf24]"></span>
            Tempa Alat Ilahi & Artefak Spiritual
        </p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 12); @endphp
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-shop px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-shop px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-shop px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>
        <a href="{{ route('admin.shop.create') }}" class="group relative px-6 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-xs font-serif uppercase">
                <i class="fas fa-hammer text-amber-400"></i>
                Tempa Artefak
            </span>
        </a>
    </div>
</div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8" id="shop-container">
        @forelse($items as $item)
        <div class="shop-card glass-panel p-0 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500 bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50 flex flex-col h-full" data-name="{{ strtolower($item->name) }}">
            <!-- Image / Icon Area -->
            <div class="relative w-full h-52 overflow-hidden bg-slate-50 px-4 pt-4">
                <div class="w-full h-full rounded-2xl overflow-hidden relative border border-slate-100 bg-white">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" alt="{{ $item->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 text-slate-200">
                            <i class="fas fa-gem text-5xl opacity-40 group-hover:scale-110 transition-transform duration-500"></i>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3 px-3 py-1 backdrop-blur-md rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm border" style="background-color: {{ ($item->category->color ?? '#ffffff') }}dd; color: {{ $item->category->color ? '#ffffff' : '#0f4c5c' }}; border-color: {{ ($item->category->color ?? '#e2e8f0') }}30">
                        {{ $item->category->name ?? 'Common' }}
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6 pt-5 flex flex-col flex-1">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xl font-serif font-black text-teal-900 leading-tight group-hover:text-cyan-600 transition-colors uppercase tracking-tight">{{ $item->name }}</h3>
                </div>
                
                <p class="text-[11px] font-medium text-slate-400 mb-6 line-clamp-2 leading-relaxed h-10 group-hover:text-slate-500 transition-colors">
                    {{ $item->description }}
                </p>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 shadow-inner group-hover:bg-white transition-colors">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Mahar (Harga)</p>
                        <p class="text-sm font-black text-gold-600 font-mono italic">★ {{ number_format($item->price) }} <span class="text-[8px] opacity-70">SP</span></p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 shadow-inner group-hover:bg-white transition-colors">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Stok Manifestasi</p>
                        <p class="text-sm font-black text-teal-900 uppercase italic">{{ $item->stock ?? '∞' }} UNIT</p>
                    </div>
                </div>

                <!-- Action Area with Premium Buttons -->
                <div class="mt-auto pt-5 border-t border-slate-50">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.shop.show', $item) }}" class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-slate-50 text-slate-600 border border-slate-200 text-[10px] font-black uppercase tracking-widest hover:bg-teal-900 hover:text-white hover:border-teal-900 transition-all shadow-sm active:scale-95 group/btn">
                            <i class="fas fa-eye text-cyan-500 group-hover/btn:text-cyan-300 transition-colors"></i> Audit
                        </a>
                        <a href="{{ route('admin.shop.edit', $item) }}" class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-white text-teal-900 border border-slate-200 text-[10px] font-black uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/btn">
                            <i class="fas fa-hammer text-amber-500 group-hover/btn:rotate-12 transition-transform"></i> Tempa
                        </a>
                        <form action="{{ route('admin.shop.destroy', $item) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete(this, '{{ $item->name }}')" class="w-10 h-10 rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white border border-red-100 transition-all flex items-center justify-center shadow-sm active:scale-95 group/del">
                                <i class="fas fa-trash-alt group-hover/del:animate-bounce"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hover Glow Effects -->
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-amber-400/5 rounded-full blur-[40px] pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        </div>
        @empty
        <div class="col-span-full py-32 text-center glass-panel border-dashed rounded-[40px] flex flex-col items-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-200">
                <i class="fas fa-store-slash text-4xl opacity-50"></i>
            </div>
            <p class="text-teal-950 font-serif font-black text-xl italic uppercase tracking-widest">Gudang Artefak Kosong</p>
            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-[0.5em] font-bold">Tidak ada manifestasi artefak ilahi saat ini</p>
        </div>
        @endforelse
    </div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Arsip Manifestasi Artefak Terdeteksi: {{ $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : $items->count() }}
    </div>
    <div class="flex items-center gap-3">
        @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($items->onFirstPage())
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $items->previousPageUrl() }}" class="w-10 h-10 rounded-xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif

            <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border-2 border-slate-100 shadow-inner">
                <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-200">{{ $items->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">DARI {{ $items->lastPage() }}</span>
            </div>

            @if($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}" class="w-10 h-10 rounded-xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        @else
            <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border-2 border-slate-100 shadow-inner">
                <span class="px-3 py-1 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-200">1</span>
                <span class="text-[9px] font-black text-slate-400 px-2 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
            </div>
        @endif
    </div>
</div>

<script>
    let currentLimit = 16;

    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function applyDisplay() {
        // Obsolete: Handled server-side
    }
        const cards = document.querySelectorAll('.shop-card');
        let visibleCount = 0;

        cards.forEach(card => {
            if (currentLimit === 'all' || visibleCount < currentLimit) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Initial load
    window.onload = () => applyDisplay();
</script>
</div>
@endsection
