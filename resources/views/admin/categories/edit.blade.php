@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 animate-fadeIn">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Rekonfigurasi Kategori</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400 shadow-[0_0_10px_#fbbf24]"></span>
                Modifikasi Protokol #{{ $category->slug }}
            </p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-[10px] font-black text-slate-400 hover:text-teal-900 uppercase tracking-widest transition-colors flex items-center gap-2 pb-1">
            <i class="fas fa-arrow-left"></i> Kembali ke Arsip
        </a>
    </div>

    <!-- Form Panel -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-8">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900">
                </div>

                <!-- Type -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Tipe Sistem</label>
                    <select name="type" required
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900 appearance-none">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $category->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Color -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Aksen Warna</label>
                    <div class="flex gap-3">
                        <input type="color" name="color" value="{{ old('color', $category->color ?? '#0f4c5c') }}"
                            class="w-16 h-14 p-1 rounded-xl bg-slate-50 border-2 border-slate-100 cursor-pointer">
                        <input type="text" id="color_text" value="{{ old('color', $category->color ?? '#0f4c5c') }}" readonly
                            class="flex-1 px-6 py-4 rounded-2xl bg-slate-100 border-2 border-slate-100 font-mono text-sm text-slate-500">
                    </div>
                </div>

                <!-- Icon -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Icon (FontAwesome)</label>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900">
                </div>
            </div>

            <!-- Metadata Fields (Dynamic) -->
            @php 
                $metadata = is_array($category->metadata) ? $category->metadata : json_decode($category->metadata, true) ?? [];
            @endphp
            <div id="metadata-fields" class="grid grid-cols-1 md:grid-cols-2 gap-8 hidden">
                <!-- Rank: Minimum Level -->
                <div id="rank-fields" class="space-y-2 hidden">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Level Minimum (Rank)</label>
                    <input type="number" name="metadata[min_level]" value="{{ old('metadata.min_level', $metadata['min_level'] ?? '') }}"
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900"
                        placeholder="1-99">
                </div>

                <!-- Dungeon: Max Participants -->
                <div id="dungeon-fields" class="space-y-2 hidden">
                    <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Kapasitas Maksimal (Dungeon)</label>
                    <input type="number" name="metadata[max_participants]" value="{{ old('metadata.max_participants', $metadata['max_participants'] ?? '') }}"
                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900"
                        placeholder="Jumlah pemain">
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-teal-900/40 uppercase tracking-[0.2em] ml-1">Deskripsi Protokol</label>
                <textarea name="description" rows="4"
                    class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-cyan-400 focus:bg-white transition-all outline-none font-bold text-teal-900">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-4 p-6 bg-slate-50 rounded-3xl border-2 border-slate-100">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $category->is_active ? 'checked' : '' }}>
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-6 after:transition-all peer-checked:bg-teal-900"></div>
                </label>
                <div>
                    <span class="block text-[10px] font-black text-teal-900 uppercase tracking-widest">Status Aktif</span>
                    <span class="text-[9px] text-slate-400 uppercase font-bold">Kategori saat ini {{ $category->is_active ? 'terdeteksi' : 'terisolasi' }} di sistem</span>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6">
                <button type="submit" class="w-full py-5 rounded-2xl bg-teal-900 text-white font-serif font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-[0.98]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('input[name="color"]').addEventListener('input', function(e) {
        document.getElementById('color_text').value = e.target.value.toUpperCase();
    });

    const typeSelect = document.querySelector('select[name="type"]');
    const metadataFields = document.getElementById('metadata-fields');
    const rankFields = document.getElementById('rank-fields');
    const dungeonFields = document.getElementById('dungeon-fields');

    function toggleMetadataFields() {
        const type = typeSelect.value;
        metadataFields.classList.add('hidden');
        rankFields.classList.add('hidden');
        dungeonFields.classList.add('hidden');

        if (type === 'rank' || type === 'dungeon') {
            metadataFields.classList.remove('hidden');
            if (type === 'rank') rankFields.classList.remove('hidden');
            if (type === 'dungeon') dungeonFields.classList.remove('hidden');
        }
    }

    typeSelect.addEventListener('change', toggleMetadataFields);
    window.addEventListener('DOMContentLoaded', toggleMetadataFields);
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
