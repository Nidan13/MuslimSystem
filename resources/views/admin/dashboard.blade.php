@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<!-- High-End Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    
    <!-- Total Users -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-teal-500/5 rounded-full blur-2xl group-hover:bg-teal-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-users text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">User</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Total Pengguna</p>
            <h3 class="text-3xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                {{ number_format($stats['total_users']) }}
            </h3>
        </div>
    </div>

    <!-- Active Quests -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-scroll text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">Quests</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Misi Aktif</p>
            <h3 class="text-3xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                {{ number_format($stats['active_quests']) }}
            </h3>
        </div>
    </div>

    <!-- Dungeons (Rift Gates) -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-red-500 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-dungeon text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">Circles</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Misi Circle (Raid)</p>
            <h3 class="text-3xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                {{ number_format($stats['total_dungeons']) }}
            </h3>
        </div>
    </div>

    <!-- Pemasukan -->
    <div class="glass-panel p-6 rounded-[32px] relative overflow-hidden group hover:-translate-y-2 transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
        <div class="flex justify-between items-start mb-6 text-amber-500">
            <div class="w-12 h-12 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-amber-400 shadow-lg shadow-teal-950/40 group-hover:scale-110 transition-transform">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">Revenue</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">Total Pemasukan</p>
            <h3 class="text-2xl font-serif font-black text-teal-900 tracking-tight leading-none italic uppercase">
                Rp{{ number_format($stats['total_income'], 0, ',', '.') }}
            </h3>
        </div>
    </div>
</div>

<div class="space-y-10">
    
    <!-- Analysis Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <!-- User Growth Chart -->
        <div class="glass-panel p-8 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wider uppercase italic">User <span class="text-cyan-400 font-sans tracking-normal ml-1">Growth</span></h4>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Tren Akusisi 7 Hari Terakhir</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            
            <div class="h-[300px] w-full">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Financial Flow Chart -->
        <div class="glass-panel p-8 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h4 class="text-2xl font-serif font-black text-teal-900 tracking-wider uppercase italic">Financial <span class="text-amber-500 font-sans tracking-normal ml-1">Flow</span></h4>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Aliran Dana Masuk (7 Hari)</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-money-bill-trend-up"></i>
                </div>
            </div>
            
            <div class="h-[300px] w-full">
                <canvas id="financialFlowChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Recent Users Simplified -->
        <div class="glass-panel p-8 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl">
            <div class="flex justify-between items-center mb-8 border-b border-slate-50 pb-6">
                <h4 class="text-xs font-black text-slate-300 uppercase tracking-[0.3em]">Hunters Baru Masuk</h4>
                <a href="{{ route('admin.hunters.index') }}" class="text-[9px] font-black text-teal-900/40 hover:text-cyan-500 uppercase tracking-widest transition-all">Manajemen Database</a>
            </div>
            <div class="space-y-4">
                @foreach($recentUsers as $user)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg group-hover:bg-cyan-500 transition-colors">
                            {{ substr($user->username, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-black text-teal-950 uppercase tracking-tight">{{ $user->username }}</p>
                            <p class="text-[9px] text-slate-400 font-medium italic uppercase tracking-widest">{{ $user->rankTier->name ?? 'Open Rank' }}</p>
                        </div>
                    </div>
                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Income Simplified -->
        <div class="glass-panel p-8 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl">
            <div class="flex justify-between items-center mb-8 border-b border-slate-50 pb-6">
                <h4 class="text-xs font-black text-slate-300 uppercase tracking-[0.3em]">Transaksi Terkini</h4>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">Live Flow</span>
                </div>
            </div>
            <div class="space-y-4">
                @forelse($recentPayments as $pay)
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-amber-200 hover:bg-white transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-amber-500 flex items-center justify-center">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-teal-950 uppercase tracking-tight">{{ $pay->user->username ?? 'Unknown' }}</p>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">{{ $pay->created_at->format('H:i') }} • SN-{{ substr($pay->id, -5) }}</p>
                        </div>
                    </div>
                    <p class="text-xs font-black text-teal-900 tracking-tighter italic">+Rp{{ number_format($pay->amount) }}</p>
                </div>
                @empty
                <div class="py-10 text-center opacity-20 flex flex-col items-center">
                    <i class="fas fa-inbox text-3xl mb-4"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest">No Recent Stream</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared Chart Config
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d2d35',
                    titleFont: { family: 'Cinzel', size: 10, weight: 'bold' },
                    bodyFont: { size: 11, weight: 'black' },
                    padding: 12,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                }
            }
        };

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthData = @json($userGrowth);
        
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: userGrowthData.map(d => new Date(d.date).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})),
                datasets: [{
                    data: userGrowthData.map(d => d.count),
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34, 211, 238, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#22d3ee',
                    pointBorderWidth: 4,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: chartOptions
        });

        // Financial Flow Chart
        const financialFlowCtx = document.getElementById('financialFlowChart').getContext('2d');
        const financialFlowData = @json($financialFlow);
        
        new Chart(financialFlowCtx, {
            type: 'bar',
            data: {
                labels: financialFlowData.map(d => new Date(d.date).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})),
                datasets: [{
                    data: financialFlowData.map(d => d.total),
                    backgroundColor: '#f59e0b',
                    borderRadius: 15,
                    barThickness: 30,
                    hoverBackgroundColor: '#d97706'
                }]
            },
            options: chartOptions
        });
    });
</script>
<style>
    .animate-fadeIn { animation: fadeIn 0.8s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
@endsection
