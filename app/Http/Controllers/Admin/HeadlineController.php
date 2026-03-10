<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Headline;
use App\Http\Requests\Admin\HeadlineRequest;
use Illuminate\Http\Request;

class HeadlineController extends Controller
{
    public function index()
    {
        $headlines = Headline::latest()->paginate(10);
        return view('admin.headlines.index', compact('headlines'));
    }

    public function create()
    {
        return view('admin.headlines.create');
    }

    public function store(HeadlineRequest $request)
    {
        $validated = $request->validated();
        
        // Handle boolean cast for checkbox
        $validated['is_active'] = $request->has('is_active');

        Headline::create($validated);

        return redirect()->route('admin.headlines.index')->with('success', 'Headline baru berhasil ditambahkan!');
    }

    public function edit(Headline $headline)
    {
        return view('admin.headlines.edit', compact('headline'));
    }

    public function update(HeadlineRequest $request, Headline $headline)
    {
        $validated = $request->validated();
        
        // Handle boolean cast for checkbox
        $validated['is_active'] = $request->has('is_active');

        $headline->update($validated);

        return redirect()->route('admin.headlines.index')->with('success', 'Headline berhasil diupdate!');
    }

    public function destroy(Headline $headline)
    {
        $headline->delete();
        return redirect()->route('admin.headlines.index')->with('success', 'Headline berhasil dihapus!');
    }
}
