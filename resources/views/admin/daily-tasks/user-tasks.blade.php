@extends('layouts.admin')

@section('title', 'Audit Matriks: ' . $user->username)

@section('content')
<div class="space-y-10 animate-fadeIn pb-20">
    <!-- Header Navigation -->
    <div class="flex items-center gap-6">
        <a href="{{ route('admin.daily-tasks.users') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-1">Registri Kustom / Audit Matriks</h2>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">{{ $user->username }} <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Node</span></h1>
        </div>
    </div>

    <!-- Stats & Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-panel p-8 rounded-[40px] bg-teal-900 text-white relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-cyan-400/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <p class="text-[9px] font-black text-cyan-400 uppercase tracking-[0.4em] mb-6">Protokol Terdeteksi</p>
            <div class="flex items-baseline gap-3">
                <span class="text-5xl font-mono font-black tracking-tight italic">{{ $tasks->count() }}</span>
                <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none">Total Tugas</span>
            </div>
        </div>
        <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50 flex flex-col justify-between">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.4em]">Node Email</p>
            <code class="text-xs font-black text-teal-900 lowercase font-mono bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100 w-fit">{{ $user->email }}</code>
        </div>
        <div class="glass-panel p-8 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50 flex flex-col justify-between">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.4em]">Audit Status</p>
            <div class="flex items-center gap-3">
                 <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.5)]"></span>
                 <span class="text-xs font-black text-teal-900 uppercase tracking-[0.2em]">Synchronization Active</span>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter/Utility Bar -->
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                </div>
                <input type="text" id="taskSearch" placeholder="Cari protokol ritual hunter..." 
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
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400">
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(0)">
                            Inti Ritual <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            Protokol & Direktif <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(2)">
                            Hasil EXP <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Manifestasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="taskTableBody">
                    @forelse($tasks as $task)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-8">
                            <div class="w-16 h-16 rounded-[24px] bg-slate-100 border-2 border-slate-50 flex items-center justify-center text-slate-400 shadow-inner group-hover:bg-white group-hover:border-cyan-100 group-hover:text-cyan-500 transition-all duration-500 overflow-hidden relative">
                                @if(Str::startsWith($task->icon, 'fa') || Str::contains($task->icon, 'fa-'))
                                    <i class="{{ $task->icon }} text-xl group-hover:scale-110 transition-transform"></i>
                                @else
                                    <span class="text-2xl group-hover:scale-110 transition-transform">{{ $task->icon }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <p class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors whitespace-nowrap">{{ $task->name }}</p>
                            <p class="text-[10px] text-slate-400 font-medium italic mt-1 line-clamp-1 max-w-xs">{{ $task->description }}</p>
                        </td>
                        <td class="py-6 px-8 text-right" data-sort-val="{{ $task->soul_points }}">
                            <div class="inline-flex flex-col items-end">
                                <p class="text-xl font-black bg-gradient-to-br from-blue-600 to-slate-950 bg-clip-text text-transparent font-mono tracking-tighter leading-none mb-1">+{{ number_format($task->soul_points) }}</p>
                                <span class="text-[8px] font-black text-blue-600/40 uppercase tracking-widest leading-none italic">Experience</span>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 {{ $task->is_active ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-slate-300 bg-slate-50 border-slate-100' }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full {{ $task->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300' }}"></span>
                                {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                            </div>
                        </td>
                        <td class="py-6 px-8 text-right">
                             <div class="inline-flex flex-col items-end">
                                <span class="text-[10px] text-slate-600 font-mono font-black tracking-tighter bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 uppercase italic">
                                    {{ $task->created_at->format('d.M.Y') }}
                                </span>
                                <span class="text-[8px] font-bold text-slate-300 uppercase tracking-widest mt-1">Date Logged</span>
                             </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fas fa-ghost text-5xl mb-4 text-teal-900"></i>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-teal-900">Hunter ini belum memanifestasikan protokol personal</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-between items-center px-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            Total Ritual Hunter: {{ $tasks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tasks->total() : $tasks->count() }}
        </p>
        @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $tasks->links() }}
        @endif
    </div>
</div>

<script>
    function setLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
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

        document.querySelectorAll('th i.fas').forEach((icon, idx) => {
            if (idx === columnIndex) {
                icon.className = isAsc ? 'fas fa-sort-down ml-2 text-cyan-500' : 'fas fa-sort-up ml-2 text-cyan-500';
            } else {
                icon.className = 'fas fa-sort ml-2 opacity-30';
            }
        });

        rows.sort((a, b) => {
            let valA, valB;
            if (columnIndex === 2) {
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
</style>
@endsection
