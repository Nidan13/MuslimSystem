@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-4xl animate-fadeIn">
    <div class="flex items-center gap-6 mb-12">
        <a href="{{ route('admin.landing-page.categories.index') }}" class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-50 flex items-center justify-center text-nu-indigo shadow-sm hover:border-nu-teal transition-all active:scale-95">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-3xl font-serif font-black text-nu-indigo tracking-tight uppercase">Edit Kategori</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Modifikasi Master Data Kategori Warta</p>
        </div>
    </div>

    <form action="{{ route('admin.landing-page.categories.update', $category) }}" method="POST" class="glass-panel p-10 rounded-[40px] bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50 space-y-8">
        @csrf @method('PUT')
        
        <div class="space-y-4">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Nama Kategori</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full px-8 py-5 rounded-2xl bg-slate-50 border-2 border-slate-50 focus:border-nu-teal focus:bg-white transition-all outline-none font-serif text-nu-indigo shadow-inner" required>
            @error('name') <p class="text-red-400 text-[10px] font-bold mt-2 ml-2 uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 pt-6">
            <button type="submit" class="flex-grow py-6 bg-nu-indigo text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.3em] hover:bg-slate-900 transition-all shadow-xl shadow-nu-indigo/20">
                Perbarui Maklumat Data
            </button>
        </div>
    </form>
</div>
@endsection
