<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prayer;
use Illuminate\Http\Request;

class PrayerController extends Controller
{
    /**
     * Display a listing of the master prayers.
     */
    public function index()
    {
        $prayers = Prayer::all();
        return view('admin.prayers.index', compact('prayers'));
    }

    /**
     * Show the form for editing the specified master prayer.
     */
    public function edit(Prayer $prayer)
    {
        return view('admin.prayers.edit', compact('prayer'));
    }

    /**
     * Update the specified master prayer in storage.
     */
    public function update(Request $request, Prayer $prayer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'soul_points' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        $prayer->update($validated);

        return redirect()->route('admin.prayers.index')
            ->with('success', 'Master data sholat berhasil diperbarui!');
    }
}
