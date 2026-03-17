@extends('layouts.admin')

@section('title', 'Edit Section')

@section('content')
<div class="max-w-5xl animate-fadeIn">
    <div class="flex items-center gap-6 mb-12">
        <a href="{{ route('admin.landing-page.sections.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-teal-900 shadow-sm hover:border-cyan-400 transition-all active:scale-95">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-3xl font-serif font-black text-teal-900 tracking-tight uppercase">Edit Section</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Modifikasi Komponen Antarmuka Utama</p>
        </div>
    </div>

    <form action="{{ route('admin.landing-page.sections.update', $section) }}" method="POST" enctype="multipart/form-data" class="glass-panel p-12 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50 space-y-10">
        @csrf @method('PUT')
        
        <div class="grid md:grid-cols-2 gap-10">
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Judul Utama (Title)</label>
                <input type="text" name="title" value="{{ old('title', $section->title) }}" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner" required>
            </div>

            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Sub-judul (Subtitle)</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $section->subtitle) }}" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-10">
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Tipe Komponen</label>
                <select name="type" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner appearance-none cursor-pointer" required>
                    <option value="hero" {{ $section->type == 'hero' ? 'selected' : '' }}>Hero Section (Bisa Slider/Tunggal)</option>
                    <option value="feature_cards" {{ $section->type == 'feature_cards' ? 'selected' : '' }}>Evolution Cards (3 Kolom Grid)</option>
                    <option value="human_centric_grid" {{ $section->type == 'human_centric_grid' ? 'selected' : '' }}>Feature Grid (4 Kolom Mini)</option>
                    <option value="about" {{ $section->type == 'about' ? 'selected' : '' }}>About / Text with Image</option>
                    <option value="cta" {{ $section->type == 'cta' ? 'selected' : '' }}>Call to Action Box</option>
                </select>
            </div>

            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Variasi Visual (Style)</label>
                <select name="style" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner appearance-none cursor-pointer" required>
                    <option value="default" {{ $section->style == 'default' ? 'selected' : '' }}>Default (Kiri ke Kanan)</option>
                    <option value="reversed" {{ $section->style == 'reversed' ? 'selected' : '' }}>Terbalik (Kanan ke Kiri)</option>
                    <option value="dark" {{ $section->style == 'dark' ? 'selected' : '' }}>Tema Gelap (Dark Mode)</option>
                    <option value="cards" {{ $section->style == 'cards' ? 'selected' : '' }}>Format Kartu (Cards)</option>
                </select>
            </div>
        </div>

        <!-- Live Preview Box -->
        <div class="space-y-4">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Live Visual Preview</label>
            <div id="preview-box" class="w-full h-48 bg-slate-100 rounded-[2.5rem] border-2 border-slate-200 overflow-hidden relative flex items-center justify-center p-4 transition-all duration-500">
                <!-- JS will inject wireframe HTML here -->
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-10">
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Urutan Tampilan (Order)</label>
                <input type="number" name="order" value="{{ old('order', $section->order) }}" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner" required>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-end mb-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Gambar Pendukung (Opsional)</label>
                    @if($section->image_url)
                        <span class="text-[10px] font-bold text-emerald-500 uppercase flex items-center gap-1"><i class="fas fa-check-circle"></i> Gambar Terpasang</span>
                    @endif
                </div>
                <input type="file" name="image_url" accept="image/*" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-slate-500 shadow-inner file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:bg-teal-900 file:text-white hover:file:bg-teal-800">
                @if($section->image_url)
                <div class="mt-4 w-32 h-32 rounded-2xl overflow-hidden border-4 border-slate-100 shadow-md">
                    <img src="{{ asset($section->image_url) }}" alt="Preview" class="w-full h-full object-cover">
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Konten Narasi (JSON/Text)</label>
            <textarea name="content" rows="6" placeholder="Masukkan konten narasi..." class="w-full px-8 py-5 rounded-[2.5rem] bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner">{{ old('content', $section->content) }}</textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-10">
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Teks Tombol Aksi (Opsional)</label>
                <input type="text" name="button_text" value="{{ old('button_text', $section->button_text) }}" placeholder="Contoh: Gabung Sekarang" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner">
            </div>

            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Link Tombol Aksi (Opsional)</label>
                <input type="text" name="button_url" value="{{ old('button_url', $section->button_url) }}" placeholder="Contoh: /register atau https://..." class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-cyan-400 focus:bg-white transition-all outline-none font-serif text-teal-900 shadow-inner">
            </div>
        </div>

        <!-- Items Repeater (JSON) -->
        <div class="pt-10 border-t border-slate-100 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-serif font-black text-teal-900 text-xl">Data Sub-Items <span class="text-xs text-slate-400 font-sans tracking-normal bg-slate-100 px-2 py-1 rounded-md ml-2">Untuk Hero Slider / Feature Cards</span></h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Kosongkan jika tidak memakai Slider atau Grid.</p>
                </div>
                <button type="button" id="btn-add-item" class="px-6 py-3 bg-teal-50 text-teal-900 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-teal-100 transition-colors flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>
            <div id="items-container" class="space-y-4">
                @if(is_array($section->items))
                    @foreach($section->items as $index => $item)
                    <div class="item-row bg-slate-50 p-6 rounded-3xl border border-slate-200 relative mb-4 animate-fadeIn">
                        <button type="button" class="btn-remove-item absolute top-4 right-4 text-red-400 hover:text-red-600 font-bold text-xs"><i class="fas fa-times"></i></button>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Judul Item</label>
                                <input type="text" name="items[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Deskripsi Pendek</label>
                                <input type="text" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Icon (Emoji/Teks)</label>
                                <input type="text" name="items[{{ $index }}][icon]" value="{{ $item['icon'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Upload Gambar Item</label>
                                <input type="file" accept="image/*" name="items[{{ $index }}][image]" class="w-full px-4 py-2 rounded-xl border border-slate-200 mt-2 text-xs file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:bg-teal-900 file:text-white">
                                @if(!empty($item['image_url']))
                                    <input type="hidden" name="items[{{ $index }}][old_image]" value="{{ $item['image_url'] }}">
                                    <p class="text-xs text-teal-600 mt-2"><i class="fas fa-check-circle"></i> Gambar Terpasang</p>
                                @endif
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Teks Tombol</label>
                                <input type="text" name="items[{{ $index }}][button_text]" value="{{ $item['button_text'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Link Tombol</label>
                                <input type="text" name="items[{{ $index }}][button_url]" value="{{ $item['button_url'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="pt-10">
            <button type="submit" class="w-full py-6 bg-teal-900 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.3em] hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20">
                Perbarui Maklumat Section
            </button>
        </div>
    </form>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('items-container');
        const btnAdd = document.getElementById('btn-add-item');
        let itemIndex = {{ is_array($section->items) ? count($section->items) : 0 }};

        btnAdd.addEventListener('click', function() {
            const html = `
                <div class="item-row bg-slate-50 p-6 rounded-3xl border border-slate-200 relative mb-4 animate-fadeIn">
                    <button type="button" class="btn-remove-item absolute top-4 right-4 text-red-400 hover:text-red-600 font-bold text-xs"><i class="fas fa-times"></i></button>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Judul Item</label>
                            <input type="text" name="items[${itemIndex}][title]" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Deskripsi Pendek</label>
                            <input type="text" name="items[${itemIndex}][description]" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Icon (Emoji/Teks)</label>
                            <input type="text" name="items[${itemIndex}][icon]" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Upload Gambar Item</label>
                            <input type="file" accept="image/*" name="items[${itemIndex}][image]" class="w-full px-4 py-2 rounded-xl border border-slate-200 mt-2 text-xs file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:bg-teal-900 file:text-white">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Teks Tombol</label>
                            <input type="text" name="items[${itemIndex}][button_text]" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Link Tombol</label>
                            <input type="text" name="items[${itemIndex}][button_url]" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm focus:border-cyan-400 outline-none">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            itemIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-item')) {
                e.target.closest('.item-row').remove();
            }
        });
        
        // Live Preview Logic
        const typeSelect = document.querySelector('select[name="type"]');
        const styleSelect = document.querySelector('select[name="style"]');
        const previewBox = document.getElementById('preview-box');

        function updatePreview() {
            const type = typeSelect.value;
            const style = styleSelect.value;
            let html = '';
            
            const isDark = style === 'dark';
            const isReversed = style === 'reversed';
            const bgClass = isDark ? 'bg-slate-900 text-white' : 'bg-teal-50 text-teal-900';
            const wireClass = isDark ? 'bg-white opacity-20' : 'bg-teal-900 opacity-20';

            if (type === 'hero' || type === 'about') {
                html = `<div class="w-full h-full flex items-center p-8 gap-8 ${isReversed ? 'flex-row-reverse' : ''} ${bgClass}">
                            <div class="w-1/2 space-y-3">
                                <div class="w-20 form-h-4 rounded-full ${wireClass} opacity-40"></div>
                                <div class="w-full h-8 rounded-xl ${wireClass} opacity-80"></div>
                                <div class="w-3/4 h-8 rounded-xl ${wireClass} opacity-80"></div>
                                <div class="w-full h-12 rounded-xl ${wireClass} opacity-20 mt-4"></div>
                                <div class="w-32 h-10 rounded-full ${wireClass} opacity-100 mt-4"></div>
                            </div>
                            <div class="w-1/2 h-full rounded-[2rem] flex items-center justify-center font-black uppercase tracking-widest text-[9px] ${wireClass} opacity-10 border border-current">
                                Image / Slider Area
                            </div>
                        </div>`;
            } else if (type === 'feature_cards' || type === 'human_centric_grid') {
                 let cols = type === 'feature_cards' ? 'grid-cols-3' : 'grid-cols-4';
                 html = `<div class="w-full h-full flex flex-col items-center justify-center p-8 gap-6 ${isDark ? 'bg-slate-900 text-white' : 'bg-slate-50 text-teal-900'}">
                            <div class="w-1/3 h-8 rounded-xl ${wireClass} opacity-80 mb-2"></div>
                            <div class="grid ${cols} gap-4 w-full max-w-lg">
                                <div class="aspect-square rounded-2xl ${wireClass} opacity-10 border border-current flex flex-col items-center justify-center p-2"><div class="w-6 h-6 rounded-full bg-current opacity-20 mb-2"></div><div class="w-full h-2 bg-current opacity-20 rounded"></div></div>
                                <div class="aspect-square rounded-2xl ${wireClass} opacity-10 border border-current flex flex-col items-center justify-center p-2"><div class="w-6 h-6 rounded-full bg-current opacity-20 mb-2"></div><div class="w-full h-2 bg-current opacity-20 rounded"></div></div>
                                <div class="aspect-square rounded-2xl ${wireClass} opacity-10 border border-current flex flex-col items-center justify-center p-2"><div class="w-6 h-6 rounded-full bg-current opacity-20 mb-2"></div><div class="w-full h-2 bg-current opacity-20 rounded"></div></div>
                                ${type === 'human_centric_grid' ? `<div class="aspect-square rounded-2xl ${wireClass} opacity-10 border border-current flex flex-col items-center justify-center p-2"><div class="w-6 h-6 rounded-full bg-current opacity-20 mb-2"></div><div class="w-full h-2 bg-current opacity-20 rounded"></div></div>` : ''}
                            </div>
                        </div>`;
            } else if (type === 'cta') {
                 html = `<div class="w-full h-full flex items-center justify-center p-8 ${style === 'dark' ? 'bg-slate-900 text-white' : 'bg-teal-900 text-white'}">
                            <div class="max-w-xs text-center space-y-4">
                                <div class="w-full h-8 rounded-xl bg-white opacity-80 mx-auto"></div>
                                <div class="w-3/4 h-4 rounded-xl bg-white opacity-40 mx-auto"></div>
                                <div class="w-32 h-10 rounded-full bg-white opacity-100 mx-auto mt-4"></div>
                            </div>
                        </div>`;
            } else {
                 html = `<div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400 text-xs font-black uppercase tracking-widest select-none">Preview Layout ${type}</div>`;
            }
            previewBox.innerHTML = html;
        }

        typeSelect.addEventListener('change', updatePreview);
        styleSelect.addEventListener('change', updatePreview);
        updatePreview();
    });
</script>
@endpush
@endsection