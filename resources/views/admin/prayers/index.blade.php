@extends('layouts.admin')

@section('title', 'Protokol Ibadah')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fadeIn">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Protokol Ibadah</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
            Parameter Pahala Spiritual & Konfigurasi Sholat
        </p>
    </div>
    
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-white hover:text-cyan-500">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all bg-teal-900 text-white shadow-lg">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-white hover:text-cyan-500">Semua</button>
            </div>
        </div>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left" id="prayer-table">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Identitas</th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Nama Protokol <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Kunci Logika</th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group text-center" onclick="sortTable(3)">
                        Pahala (SP) <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Direktif</th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Sinkronisasi</th>
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
                        'isya' => 'fa-stars',
                    ];
                    $icon = $prayerIcons[$prayer->slug] ?? 'fa-pray';
                @endphp
                <tr class="prayer-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 px-6">
                        <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform duration-500">
                            <i class="fas {{ $icon }} text-xl icon-glow"></i>
                        </div>
                    </td>
                    <td class="py-6 px-6">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors">
                            {{ $prayer->name }}
                        </span>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">Protokol Suci</p>
                    </td>
                    <td class="py-6 px-6">
                        <code class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-cyan-600 uppercase tracking-wider border border-slate-200">
                            {{ $prayer->slug }}
                        </code>
                    </td>
                    <td class="py-6 px-6 text-center">
                        <div class="inline-flex flex-col items-center p-3 bg-white border-2 border-slate-100 rounded-2xl shadow-sm group-hover:border-gold-200 transition-colors min-w-[100px]">
                            <p class="text-2xl font-black text-gold-600 font-mono tracking-tighter leading-none mb-1">
                                +{{ number_format($prayer->soul_points) }}
                            </p>
                            <span class="text-[8px] font-black text-gold-400 uppercase tracking-widest">SP POTENCIAL</span>
                        </div>
                    </td>
                    <td class="py-6 px-6">
                        <p class="text-xs text-slate-500 font-medium max-w-xs leading-relaxed italic">
                            {{ $prayer->description }}
                        </p>
                    </td>
                    <td class="py-6 px-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.prayers.edit', $prayer) }}" class="p-4 bg-white border-2 border-slate-100 text-teal-900 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none">
                                <i class="fas fa-sliders text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Status Protokol Sholat Terafiliasi
    </div>
    
    <div class="flex items-center gap-4">
        <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
            <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
        </span>

        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
            <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">1</span>
            <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">PROTOKOL INTI</span>
        </div>

        <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
            Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
        </span>
    </div>
</div>

<script>
    let currentLimit = 16;
    let sortOrders = { 1: 'asc', 3: 'asc' };

    function setRowLimit(limit) {
        currentLimit = limit;
        
        document.querySelectorAll('.row-btn-prayer').forEach(btn => {
            if (btn.innerText === (limit === 'all' ? 'Semua' : limit.toString())) {
                btn.className = 'row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all bg-teal-900 text-white shadow-lg';
            } else {
                btn.className = 'row-btn-prayer px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-white hover:text-cyan-500';
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
        const rows = Array.from(document.querySelectorAll('.prayer-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            // Numerical sorting for SP
            if (columnIndex === 3) {
                valA = parseInt(valA.replace(/[^0-9]/g, '')) || 0;
                valB = parseInt(valB.replace(/[^0-9]/g, '')) || 0;
            }
            
            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';
        rows.forEach(row => tableBody.appendChild(row));
        applyDisplay();
    }

    window.onload = () => applyDisplay();
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endsection
