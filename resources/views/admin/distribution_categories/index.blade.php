@extends('layouts.admin')

@section('title', 'Master Alokasi SHU')

@section('content')
<div class="space-y-8 animate-fadeIn pb-20">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-1.5 h-12 bg-gradient-to-b from-[#0E5F71] to-[#2C9EB0] rounded-full mr-4 shadow-[0_0_15px_rgba(14,95,113,0.3)]"></div>
            <div>
                <h1 class="text-3xl font-serif font-black text-[#0E5F71] tracking-tight uppercase">Manajemen SHU Platform</h1>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Kelola pembagian internal dari total pendapatan platform</p>
            </div>
        </div>
        
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="px-6 py-3 rounded-xl bg-[#0E5F71] text-white shadow-lg shadow-teal-900/20 hover:scale-105 transition-all font-serif uppercase tracking-widest text-[10px] font-black flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Tambah Kategori
        </button>
    </div>

    @if(session('success'))
        <div class="px-6 py-4 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 flex items-center gap-4 animate-bounce-in">
            <i class="fas fa-check-circle"></i>
            <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    <div class="glass-panel rounded-[40px] bg-white border-2 border-slate-50 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-50">
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori Pengeluaran</th>
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Porsi Alokasi (%)</th>
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-10 py-6">
                            <span class="text-sm font-black text-[#0E5F71] uppercase tracking-tight">{{ $cat->name }}</span>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 bg-[#2C9EB0]/10 text-[#0E5F71] rounded-full text-xs font-black">{{ $cat->percentage }}%</span>
                        </td>
                        <td class="px-10 py-6">
                            @if($cat->is_active)
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase border border-emerald-100 italic">Aktif</span>
                            @else
                                <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[9px] font-black uppercase border border-slate-100 italic">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-10 py-6 text-right space-x-2">
                            <button onclick="editCategory({{ $cat }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form action="{{ route('admin.distribution-categories.destroy', $cat->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus kategori ini?')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-10 py-20 text-center opacity-20 capitalize">
                            <i class="fas fa-folder-open text-5xl mb-4 block"></i>
                            <span class="text-[10px] font-black tracking-widest">Belum ada data kategori</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 bg-teal-950/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] w-full max-w-lg shadow-2xl animate-bounce-in overflow-hidden">
        <div class="px-10 py-8 border-b border-slate-50">
            <h3 class="text-lg font-serif font-black text-[#0E5F71] uppercase tracking-tight">Tambah Kategori SHU</h3>
        </div>
        <form action="{{ route('admin.distribution-categories.store') }}" method="POST" class="p-10 space-y-6">
            @csrf
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Nama Kategori</label>
                <input type="text" name="name" required class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold outline-none focus:border-[#2C9EB0] transition-all" placeholder="Contoh: Operasional Tim">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Porsi Net Alokasi (%)</label>
                <input type="number" step="0.1" name="percentage" required class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold outline-none focus:border-[#2C9EB0] transition-all" placeholder="0.0">
            </div>
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="this.closest('#modal-add').classList.add('hidden')" class="flex-1 px-8 py-4 rounded-2xl bg-slate-100 text-slate-500 font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 px-8 py-4 rounded-2xl bg-[#0E5F71] text-white font-black uppercase text-[10px] tracking-widest shadow-lg shadow-teal-900/20">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 bg-teal-950/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] w-full max-w-lg shadow-2xl animate-bounce-in overflow-hidden">
        <div class="px-10 py-8 border-b border-slate-50">
            <h3 class="text-lg font-serif font-black text-[#0E5F71] uppercase tracking-tight">Edit Kategori SHU</h3>
        </div>
        <form id="form-edit" method="POST" class="p-10 space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Nama Kategori</label>
                <input type="text" name="name" id="edit-name" required class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold outline-none focus:border-[#2C9EB0] transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Porsi Net Alokasi (%)</label>
                <input type="number" step="0.1" name="percentage" id="edit-percentage" required class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm font-bold outline-none focus:border-[#2C9EB0] transition-all">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="edit-active" class="w-5 h-5 rounded border-2 border-slate-200 text-[#0E5F71] focus:ring-[#2C9EB0]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori Aktif</label>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="flex-1 px-8 py-4 rounded-2xl bg-slate-100 text-slate-500 font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 px-8 py-4 rounded-2xl bg-[#0E5F71] text-white font-black uppercase text-[10px] tracking-widest shadow-lg shadow-teal-900/20">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('edit-name').value = cat.name;
    document.getElementById('edit-percentage').value = cat.percentage;
    document.getElementById('edit-active').checked = cat.is_active;
    document.getElementById('form-edit').action = `/admin/distribution-categories/${cat.id}`;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
