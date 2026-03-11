@extends('layouts.admin')

@section('title', 'Edit Headline')

@section('content')
<div class="w-full animate-fadeIn">
    <!-- Header -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('admin.headlines.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-teal-900 transition-all shadow-sm active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Edit Headline</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Perbarui informasi headline ID #{{ $headline->id }}</p>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl relative overflow-hidden">
        <form action="{{ route('admin.headlines.update', $headline) }}" method="POST" enctype="multipart/form-data" class="space-y-10 relative z-10">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <!-- Title -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Judul Headline</label>
                    <input type="text" name="title" value="{{ old('title', $headline->title) }}" required
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-lg font-black transition-all placeholder-slate-200 uppercase tracking-tight"
                        placeholder="MISAL: UPDATE SISTEM RAMADHAN">
                    @error('title') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Grid Tag & Category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tag (Short Label)</label>
                        <input type="text" name="tag" value="{{ old('tag', $headline->tag) }}" required
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all uppercase tracking-widest text-teal-900">
                        @error('tag') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori (Optional)</label>
                        <select name="category_id"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-black transition-all uppercase tracking-widest text-teal-900 appearance-none">
                            <option value="">-- TANPA KATEGORI --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $headline->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Upload Gambar Banner -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Upload Gambar Banner</label>
                    <div class="relative group">
                        <!-- Clickable Area (Shown only if no image) -->
                        <div id="drop-zone-edit" onclick="document.getElementById('image-input-edit').click()" 
                            class="w-full h-48 bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl flex flex-col items-center justify-center transition-all hover:border-cyan-400 hover:bg-cyan-50/20 group-hover:shadow-lg cursor-pointer {{ $headline->image_url ? 'invisible' : '' }}">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3 group-hover:text-cyan-400 transition-colors"></i>
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Klik atau seret gambar ke sini</p>
                            <p class="text-[9px] text-slate-200 mt-1">JPG, PNG, WEBP • Maks 2MB</p>
                        </div>
                        
                        <!-- Preview Container -->
                        <div id="preview-container-edit" class="{{ $headline->image_url ? '' : 'hidden' }} absolute inset-0">
                            <img id="image-preview-edit" src="{{ $headline->image_url }}" alt="Preview" class="w-full h-48 object-cover rounded-3xl border-2 border-cyan-400 shadow-lg">
                            <!-- Change Button overlay -->
                            <button type="button" onclick="document.getElementById('image-input-edit').click()" class="absolute inset-0 w-full h-full bg-black/20 opacity-0 hover:opacity-100 transition-opacity rounded-3xl flex items-center justify-center">
                                <span class="bg-white/90 text-teal-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl">Ganti Gambar</span>
                            </button>
                            <!-- Remove button -->
                            <button type="button" id="remove-image-edit" onclick="removeImage('edit')" class="absolute top-3 right-3 w-10 h-10 bg-red-500 text-white rounded-2xl flex items-center justify-center hover:bg-red-600 transition-all shadow-lg text-xs z-20">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <input type="file" id="image-input-edit" name="image" accept="image/*" class="hidden" onchange="previewImage(this, 'edit')">
                    @error('image') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Content / Deskripsi -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Isi Berita / Deskripsi Utama</label>
                    <textarea name="content" rows="6" 
                        class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-cyan-400 focus:outline-none text-sm font-medium transition-all placeholder-slate-300 italic leading-relaxed"
                        placeholder="Tuliskan berita lengkap di sini...">{{ old('content', $headline->content) }}</textarea>
                    @error('content') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status Switch -->
                <div class="flex items-center gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <label class="flex items-center cursor-pointer gap-4">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $headline->is_active ? 'checked' : '' }}>
                            <div class="w-14 h-8 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-cyan-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-teal-900"></div>
                        </div>
                        <span class="text-[10px] font-black text-teal-900 uppercase tracking-[0.2em]">Aktifkan Berita Ini</span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-8 border-t border-slate-50">
                <button type="submit" class="w-full bg-teal-900 hover:bg-teal-800 py-6 rounded-3xl font-serif font-black text-white uppercase tracking-[0.4em] shadow-xl shadow-teal-950/20 transition-all active:scale-[0.98]">
                    Simpan Perubahan Headline
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input, key) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview-' + key);
            const container = document.getElementById('preview-container-' + key);
            const dropZone = document.getElementById('drop-zone-' + key);
            
            preview.src = e.target.result;
            container.classList.remove('hidden');
            dropZone.classList.add('invisible');
        };
        reader.readAsDataURL(file);
    }

    function removeImage(key) {
        const input = document.getElementById('image-input-' + key);
        const container = document.getElementById('preview-container-' + key);
        const dropZone = document.getElementById('drop-zone-' + key);
        
        input.value = '';
        container.classList.add('hidden');
        dropZone.classList.remove('invisible');
    }
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection