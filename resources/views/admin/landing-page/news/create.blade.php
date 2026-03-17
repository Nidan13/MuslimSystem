@extends('layouts.admin')

@section('title', 'Buat Warta')

@section('content')
<div class="max-w-5xl animate-fadeIn">
    <div class="flex items-center gap-6 mb-12">
        <a href="{{ route('admin.landing-page.news.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-nu-indigo shadow-sm hover:border-nu-teal transition-all active:scale-95">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-3xl font-serif font-black text-nu-indigo tracking-tight uppercase">Buat Warta Baru</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Inisialisasi Publikasi Konten Landing Page</p>
        </div>
    </div>

    <form action="{{ route('admin.landing-page.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- Two column: Title + Category --}}
        <div class="bg-white rounded-[2.5rem] border-2 border-slate-50 shadow-xl p-10 space-y-8">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Informasi Utama</h3>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Judul Warta</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul utama..." class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-nu-teal focus:bg-white transition-all outline-none font-serif text-nu-indigo shadow-inner" required>
                    @error('title') <p class="text-red-400 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kategori</label>
                    <select name="category_id" class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-nu-teal focus:bg-white transition-all outline-none font-serif text-nu-indigo shadow-inner appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-400 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Ringkasan (Summary)</label>
                <textarea name="summary" rows="3" placeholder="Tulis ringkasan singkat untuk tampilan kartu..." class="w-full px-6 py-4 rounded-3xl bg-slate-50 border-2 border-slate-50 focus:border-nu-teal focus:bg-white transition-all outline-none font-serif text-nu-indigo shadow-inner" required>{{ old('summary') }}</textarea>
                @error('summary') <p class="text-red-400 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Konten Lengkap</label>
                <textarea name="content" rows="12" placeholder="Tulis isi warta di sini..." class="w-full px-6 py-4 rounded-3xl bg-slate-50 border-2 border-slate-50 focus:border-nu-teal focus:bg-white transition-all outline-none font-serif text-nu-indigo shadow-inner" required>{{ old('content') }}</textarea>
                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-2">Gunakan Baris Baru Ganda (Double Enter) untuk menyisipkan foto tambahan di antara paragraf.</p>
                @error('content') <p class="text-red-400 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Photo Upload Section: 3 Kotak Berjajar --}}
        <div class="bg-white rounded-[2.5rem] border-2 border-slate-50 shadow-xl p-10 space-y-8">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Foto & Media (3 Kotak)</h3>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Box 1: Cover --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Foto Utama (Cover)</label>
                    <div class="relative aspect-square">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-full rounded-[2rem] bg-slate-50 border-2 border-dashed border-slate-200 cursor-pointer hover:border-nu-teal hover:bg-slate-50/70 transition-all group overflow-hidden">
                            <div id="cover-preview" class="hidden w-full h-full rounded-[2rem] overflow-hidden absolute inset-0">
                                <img id="cover-preview-img" src="" class="w-full h-full object-cover" />
                            </div>
                            <div id="cover-placeholder" class="flex flex-col items-center pointer-events-none">
                                <i class="fas fa-image text-3xl text-slate-300 mb-2"></i>
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Utama</p>
                            </div>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImg(this, 'cover-preview-img', 'cover-preview', 'cover-placeholder')">
                    </div>
                </div>

                {{-- Box 2: Extra 1 --}}
                @for($i = 1; $i <= 2; $i++)
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Foto Tambahan {{ $i }}</label>
                    <div class="relative aspect-square">
                        <label for="extra_image_{{ $i }}" class="flex flex-col items-center justify-center w-full h-full rounded-[2rem] bg-slate-50 border-2 border-dashed border-slate-200 cursor-pointer hover:border-nu-teal hover:bg-slate-50/70 transition-all group overflow-hidden">
                            <div id="extra-preview-{{ $i }}" class="hidden w-full h-full rounded-[2rem] overflow-hidden absolute inset-0">
                                <img id="extra-preview-img-{{ $i }}" src="" class="w-full h-full object-cover" />
                            </div>
                            <div id="extra-placeholder-{{ $i }}" class="flex flex-col items-center pointer-events-none text-center">
                                <i class="fas fa-plus text-2xl text-slate-300 mb-2"></i>
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Ekstra {{ $i }}</p>
                            </div>
                        </label>
                        <input type="file" id="extra_image_{{ $i }}" name="extra_image_{{ $i }}" accept="image/*" class="hidden" onchange="previewImg(this, 'extra-preview-img-{{ $i }}', 'extra-preview-{{ $i }}', 'extra-placeholder-{{ $i }}')">
                    </div>
                </div>
                @endfor
            </div>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-4 text-center">Semua foto akan dioptimasi otomatis untuk tampilan terbaik.</p>
        </div>

        {{-- Toggles --}}
        <div class="bg-white rounded-[2.5rem] border-2 border-slate-50 shadow-xl p-10">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Visibilitas & Status</h3>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['name' => 'is_active', 'label' => 'Status Aktif', 'desc' => 'Warta bisa diakses publik', 'default' => true, 'color' => 'nu-teal'],
                    ['name' => 'is_for_user', 'label' => 'Tampil di App', 'desc' => 'Muncul di dashboard mobile', 'default' => false, 'color' => 'nu-indigo'],
                    ['name' => 'is_for_landing_page', 'label' => 'Tampil di Web', 'desc' => 'Tampil di landing page', 'default' => true, 'color' => 'nu-teal'],
                ] as $toggle)
                <label class="p-6 bg-slate-50 rounded-2xl flex items-center justify-between shadow-inner hover:bg-white border-2 border-transparent peer-checked:border-{{ $toggle['color'] }} transition-all group cursor-pointer has-[:checked]:bg-white has-[:checked]:border-{{ $toggle['color'] == 'nu-teal' ? 'teal-400' : 'indigo-400' }} has-[:checked]:shadow-none">
                    <div>
                        <span class="text-[10px] font-black {{ $toggle['color'] == 'nu-teal' ? 'text-teal-600' : 'text-indigo-600' }} uppercase tracking-widest block">{{ $toggle['label'] }}</span>
                        <span class="text-[8px] text-slate-400 font-bold mt-1 block">{{ $toggle['desc'] }}</span>
                    </div>
                    <div class="relative inline-flex items-center flex-shrink-0">
                        <input type="checkbox" name="{{ $toggle['name'] }}" value="1" class="sr-only peer" {{ old($toggle['name'], $toggle['default']) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all {{ $toggle['color'] == 'nu-teal' ? 'peer-checked:bg-teal-500' : 'peer-checked:bg-indigo-600' }}"></div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div>
            <button type="submit" class="w-full py-6 bg-nu-indigo text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.3em] hover:bg-slate-900 transition-all shadow-xl shadow-nu-indigo/20">
                Publikasikan Warta
            </button>
        </div>
    </form>
</div>

<script>
    function previewImg(input, imgId, previewId, placeholderId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(imgId).src = e.target.result;
                document.getElementById(previewId).classList.remove('hidden');
                document.getElementById(placeholderId).classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection