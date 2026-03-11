@extends('layouts.admin')

@section('title', 'Matriks Disiplin: Ritual Harian')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Matriks <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Disiplin</span> <span class="text-teal-900 font-serif">Harian</span></h2>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                Protokol Ritual Penyucian & Peningkatan Kapasitas Hunter
            </p>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.daily-task-categories.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95 group" title="Master Kategori">
                <i class="fas fa-tags text-sm group-hover:scale-110 transition-transform"></i>
            </a>

            <a href="{{ route('admin.daily-tasks.users') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 shadow-xl shadow-slate-200/50 hover:border-cyan-400 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
                <i class="fas fa-users-viewfind text-teal-500 transition-transform group-hover:scale-110"></i>
                Tugas Kustom Hunter
            </a>
            <a href="{{ route('admin.daily-tasks.create') }}" class="group flex items-center gap-3 px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 font-serif uppercase tracking-widest text-[10px] font-black">
                <i class="fas fa-plus text-cyan-400 transition-transform group-hover:rotate-90"></i>
                Bangun Ritual Baru
            </a>
        </div>
    </div>

    <!-- Stats & Filters -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter Bar -->
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex flex-wrap justify-between items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                </div>
                <input type="text" id="taskSearch" placeholder="Cari protokol ritual..." 
                    class="block w-80 pl-12 pr-4 py-3 bg-white border-2 border-slate-100 rounded-2xl text-[11px] font-black text-teal-900 uppercase tracking-widest placeholder-slate-300 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 transition-all outline-none">
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2 italic">Tampilkan:</span>
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    @foreach([8, 16, 32] as $lim)
                    <button onclick="setLimit({{ $lim }})" class="px-4 py-1.5 rounded-lg text-[10px] font-black transition-all {{ request('limit', 16) == $lim ? 'bg-teal-900 text-white shadow-md' : 'text-slate-400 hover:text-teal-900' }}">
                        {{ $lim }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="daily-task-table">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400">
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em]">Inti</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            Protokol <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em]">Kategori</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em]">Direktif</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(4)">
                            Hasil SP <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="taskTableBody">
                    @forelse($tasks as $task)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-8">
                            <div class="w-14 h-14 rounded-2xl bg-teal-900 border-2 border-slate-100 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-900/20 group-hover:scale-110 transition-transform overflow-hidden relative">
                                @if(Str::startsWith($task->icon, 'fa') || Str::contains($task->icon, 'fa-'))
                                    <i class="{{ $task->icon }} text-xl icon-glow"></i>
                                @else
                                    <span class="text-2xl">{{ $task->icon }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <p class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors whitespace-nowrap">{{ $task->name }}</p>
                        </td>
                        <td class="py-6 px-8">
                            <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border shadow-sm" style="background-color: {{ ($task->category->color ?? '#f1f5f9') }}10; color: {{ $task->category->color ?? '#64748b' }}; border-color: {{ ($task->category->color ?? '#e2e8f0') }}30">
                                {{ $task->category->name ?? 'Matriks' }}
                            </span>
                        </td>
                        <td class="py-6 px-8">
                            <p class="text-[10px] text-slate-400 font-medium italic line-clamp-2 max-w-xs leading-relaxed">{{ $task->description }}</p>
                        </td>
                        <td class="py-6 px-8 text-right" data-sort-val="{{ $task->soul_points }}">
                            <div class="inline-flex flex-col items-end">
                                <p class="text-lg font-black text-amber-600 font-mono tracking-tighter leading-none mb-1">+{{ number_format($task->soul_points) }}</p>
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic leading-none">Soul Points</span>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 {{ $task->is_active ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-slate-300 bg-slate-50 border-slate-100' }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full {{ $task->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300' }}"></span>
                                {{ $task->is_active ? 'Aktif' : 'Arsip' }}
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <a href="{{ route('admin.daily-tasks.edit', $task) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-teal-900 hover:bg-teal-900 hover:text-white rounded-xl transition-all shadow-sm active:scale-90">
                                    <i class="fas fa-sliders text-[11px]"></i>
                                </a>
                                <form action="{{ route('admin.daily-tasks.destroy', $task) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, '{{ $task->name ?? 'Ritual #'.$task->id }}')" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-300 hover:bg-red-500 hover:text-white rounded-xl transition-all shadow-sm active:scale-90">
                                        <i class="fas fa-trash-alt text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-clock-rotate-left text-5xl mb-4 text-teal-900"></i>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-teal-900">Matriks sistem kosong dari protokol ritual harian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginator -->
    @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-8 flex justify-between items-center px-4 font-sans">
        <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
            Total Ritual Terdaftar: {{ $tasks->total() }}
        </div>
        <div class="flex items-center gap-2">
            @if(!$tasks->onFirstPage())
                <a href="{{ $tasks->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            <div class="px-4 py-2 bg-teal-900 rounded-xl text-white text-[10px] font-black shadow-lg italic">
                P{{ $tasks->currentPage() }} / {{ $tasks->lastPage() }}
            </div>
            @if($tasks->hasMorePages())
                <a href="{{ $tasks->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-xl text-teal-900 hover:border-cyan-400 transition-all shadow-sm"><i class="fas fa-chevron-right text-xs"></i></a>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
    function setLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    document.getElementById('taskSearch')?.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        document.querySelectorAll('#taskTableBody tr').forEach(row => {
            let name = row.querySelector('.text-sm')?.innerText.toLowerCase() || '';
            let desc = row.querySelector('.text-\\[10px\\]')?.innerText.toLowerCase() || '';
            row.style.display = (name.includes(val) || desc.includes(val)) ? '' : 'none';
        });
    });

    let sortOrders = {};
    function sortTable(columnIndex) {
        const tableBody = document.getElementById('taskTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        if (rows.length <= 1 && rows[0].cells.length === 1) return;

        const isAsc = sortOrders[columnIndex] === 'asc';
        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (columnIndex === 4) {
                valA = parseFloat(a.cells[columnIndex].getAttribute('data-sort-val')) || 0;
                valB = parseFloat(b.cells[columnIndex].getAttribute('data-sort-val')) || 0;
            } else {
                valA = a.cells[columnIndex].innerText.trim().toLowerCase();
                valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            }
            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tableBody.appendChild(row));
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 8px rgba(34, 211, 238, 0.4)); }
</style>
@endsection