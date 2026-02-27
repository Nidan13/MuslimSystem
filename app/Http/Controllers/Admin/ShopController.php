<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $items = ShopItem::latest()->paginate(12);
        return view('admin.shop.index', compact('items'));
    }

    public function create()
    {
        return view('admin.shop.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_soul_points' => 'required|integer|min:0',
            'category' => 'required|in:border,title,name_color,consumable',
            'asset_path' => 'nullable|string',
        ]);

        ShopItem::create($validated);

        return redirect()->route('admin.shop-items.index')->with('success', 'A new artifact has been added to the shop.');
    }

    public function show(ShopItem $shopItem)
    {
        return view('admin.shop.show', ['item' => $shopItem]);
    }

    public function edit(ShopItem $shopItem)
    {
        return view('admin.shop.edit', ['item' => $shopItem]);
    }

    public function update(Request $request, ShopItem $shopItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_soul_points' => 'required|integer|min:0',
            'category' => 'required|in:border,title,name_color,consumable',
            'asset_path' => 'nullable|string',
        ]);

        $shopItem->update($validated);

        return redirect()->route('admin.shop-items.index')->with('success', 'Item profile synchronized.');
    }

    public function destroy(ShopItem $shopItem)
    {
        $shopItem->delete();
        return redirect()->route('admin.shop-items.index')->with('success', 'Artifact removed from inventory.');
    }
}
