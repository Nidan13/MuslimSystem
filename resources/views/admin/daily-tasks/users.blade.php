@extends('layouts.admin')

@section('title', 'Registri Kustom Hunter')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
             <a href="{{ route('admin.daily-tasks.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
                <i class="fas fa-chevron-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase italic leading-none">Registri <span class="text-cyan-500 font-sans tracking-normal not-italic mx-1">Kustom</span> <span class="text-teal-900 font-serif">Hunter</span></h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(34,211,238,0.5)]"></span>
                    Monitoring Protokol Personal yang Dimanifestasikan Hunter
                </p>
            </div>
        </div>

        <div class="bg-teal-900 px-6 py-3 rounded-2xl text-white shadow-xl shadow-teal-950/20 flex items-center gap-4">
            <i class="fas fa-satellite-dish text-cyan-400 animate-pulse"></i>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] italic">Scanning Active Nodes</span>
        </div>
    </div>

    <!-- main Table Container -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Filter/Utility Bar -->
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                </div>
                <input type="text" id="userSearch" placeholder="Cari nama hunter..." 
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
                            Identitas Hunter <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            E-Mail Node <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(2)">
                            Kontribusi Ritual <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Opsi Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="userTableBody">
                    @foreach($users as $user)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300 font-sans">
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-105 transition-transform duration-500">
                                     {{ substr($user->username, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                                        {{ $user->username }}
                                    </h3>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-2 flex items-center gap-1">
                                        <i class="fas fa-shield-halved text-cyan-500"></i>
                                        Authorized Hunter
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <code class="text-[10px] font-black text-slate-400 lowercase font-mono bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">{{ $user->email }}</code>
                        </td>
                        <td class="py-6 px-8 text-center" data-sort-val="{{ $user->daily_tasks_count }}">
                             <span class="inline-flex items-center justify-center px-5 py-2 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 text-[10px] font-black shadow-sm group-hover:border-cyan-100 transition-colors">
                                <i class="fas fa-file-invoice text-cyan-400 mr-2"></i>
                                {{ $user->daily_tasks_count }} <span class="ml-1 text-[8px] opacity-40 uppercase">Tugas</span>
                             </span>
                        </td>
                        <td class="py-6 text-right px-8">
                            <a href="{{ route('admin.daily-tasks.user-tasks', $user) }}" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-teal-900 text-white font-serif font-black uppercase tracking-widest text-[9px] hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 group/btn active:scale-95">
                                Audit Matriks
                                <i class="fas fa-chevron-right text-cyan-400 group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-between items-center px-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            Total Node Hunter: {{ $users instanceof \Illuminate\Pagination\LengthAwarePaginator ? $users->total() : $users->count() }}
        </p>
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $users->links() }}
        @endif
    </div>
</div>

<script>
    function setLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        window.location.href = url.toString();
    }

    document.getElementById('userSearch')?.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        document.querySelectorAll('#userTableBody tr').forEach(row => {
            let name = row.querySelector('h3')?.innerText.toLowerCase() || '';
            let email = row.querySelector('code')?.innerText.toLowerCase() || '';
            row.style.display = (name.includes(val) || email.includes(val)) ? '' : 'none';
        });
    });

    let sortOrders = {};
    function sortTable(columnIndex) {
        const tableBody = document.getElementById('userTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        if (rows.length <= 1) return;

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
