<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionCategory;
use Illuminate\Http\Request;

class DistributionCategoryController extends Controller
{
    public function index()
    {
        $categories = DistributionCategory::latest()->get();
        return view('admin.distribution_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        DistributionCategory::create($request->all());

        return redirect()->back()->with('success', 'Kategori SHU berhasil ditambahkan.');
    }

    public function update(Request $request, DistributionCategory $distributionCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $distributionCategory->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Kategori SHU berhasil diperbarui.');
    }

    public function destroy(DistributionCategory $distributionCategory)
    {
        $distributionCategory->delete();
        return redirect()->back()->with('success', 'Kategori SHU berhasil dihapus.');
    }
}
