@extends('layouts.admin')

@section('title', 'Arsitektur Protokol Ibadah')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Pengaturan Sholat</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Manajemen Parameter Waktu & EXP Ibadah
            </p>
        </div>
        
        <div class="flex items-center bg-white p-1.5 rounded-2xl border-2 border-slate-50 shadow-sm">
            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest px-4 italic">Density Control:</span>
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-slate-50 text-slate-400">08</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all bg-teal-900 text-white shadow-md">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-slate-50 text-slate-400">All Nodes</button>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="glass-panel rounded-[40px] overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
        <!-- Top Utility Bar -->
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex flex-wrap justify-between items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                </div>
                <input type="text" id="prayerSearch" placeholder="Cari protokol (Subuh, Dhuhur...)" 
                    class="block w-96 pl-14 pr-4 py-4 bg-white border-2 border-slate-100 rounded-2xl text-[11px] font-black text-teal-900 uppercase tracking-widest placeholder-slate-300 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/5 transition-all outline-none shadow-sm">
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-600 border border-cyan-500/20">
                    <i class="fas fa-microchip"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Core Logic Synchronized</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="prayer-table">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400">
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] w-24 text-center">Node</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(1)">
                            Nama Protokol <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em]">Kunci Matriks</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-center cursor-pointer hover:text-teal-900 transition-colors group" onclick="sortTable(3)">
                            Pahala (EXP) <i class="fas fa-sort ml-2 opacity-30 group-hover:opacity-100"></i>
                        </th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em]">Direktif Spiritual</th>
                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.2em] text-right">Integrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans" id="prayer-body">
                    @foreach($prayers as $prayer)
                    @php
                        $prayerIcons = [
                            'subuh' => 'fa-feather-pointed',
                            'dhuhur' => 'fa-sun',
                            'ashar' => 'fa-cloud-sun',
                            'maghrib' => 'fa-moon',
                            'isya' => 'fa-star-and-crescent',
                        ];
                        $icon = $prayerIcons[$prayer->slug] ?? 'fa-pray';
                    @endphp
                    <tr class="prayer-row group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-8 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform duration-500 mx-auto">
                                <i class="fas {{ $icon }} text-xl icon-glow"></i>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <h3 class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1">
                                {{ $prayer->name }}
                            </h3>
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-bolt text-cyan-400"></i>
                                Protokol Inti Sistem
                            </p>
                        </td>
                        <td class="py-6 px-8">
                            <code class="px-4 py-2 bg-slate-100 rounded-xl text-[10px] font-black text-cyan-600 border border-slate-200 shadow-inner italic">
                                {{ $prayer->slug }}
                            </code>
                        </td>
                        <td class="py-6 px-8 text-center" data-sort-val="{{ $prayer->soul_points }}">
                            <div class="flex flex-col items-center">
                                <span class="text-2xl font-black bg-gradient-to-br from-blue-600 to-slate-950 bg-clip-text text-transparent font-mono tracking-tighter leading-none mb-1">
                                    +{{ number_format($prayer->soul_points) }}
                                </span>
                                <span class="text-[8px] font-black text-blue-600/40 uppercase tracking-widest italic">Experience Nodes</span>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <p class="text-[11px] text-slate-500 font-medium max-w-xs leading-relaxed italic opacity-70 group-hover:opacity-100 transition-opacity">
                                "{{ $prayer->description }}"
                            </p>
                        </td>
                        <td class="py-6 px-8 text-right">
                            <div class="flex items-center justify-end">
                                <a href="{{ route('admin.prayers.edit', $prayer) }}" class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-teal-900 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/btn">
                                    <i class="fas fa-sliders-h transition-transform group-hover/btn:rotate-90"></i>
                                    Modify
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer Stats -->
    <div class="mt-8 flex justify-between items-center px-4">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">
            Total Prayer Nodes Synchronized: {{ $prayers->count() }}
        </p>
        
        <div class="flex items-center bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
            <span class="px-5 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100 italic">SYSTEM MASTER CONTROL</span>
        </div>
    </div>
</div>

<script>
    let currentLimit = 16;
    let sortOrders = {};

    function setRowLimit(limit) {
        currentLimit = limit;
        document.querySelectorAll('.row-btn-prayer').forEach(btn => {
            const btnText = btn.innerText.replace('0', '');
            const normalizedLimit = limit === 'all' ? 'All Nodes' : limit.toString();
            if (btn.innerText === normalizedLimit || (limit < 10 && btn.innerText === '0' + limit)) {
                btn.className = 'row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all bg-teal-900 text-white shadow-md';
            } else {
                btn.className = 'row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-slate-50 text-slate-400';
            }
        });
        applyDisplay();
    }

    function applyDisplay() {
        const rows = document.querySelectorAll('.prayer-row');
        let visibleCount = 0;
        rows.forEach(row => {
            if (currentLimit === 'all' || visibleCount < currentLimit) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('prayer-body');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
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
            if (columnIndex === 3) { // Numeric SP
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

    document.getElementById('prayerSearch')?.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        document.querySelectorAll('.prayer-row').forEach(row => {
            let name = row.querySelector('h3')?.innerText.toLowerCase() || '';
            row.style.display = name.includes(val) ? '' : 'none';
        });
    });

    window.onload = () => applyDisplay();
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endsection
