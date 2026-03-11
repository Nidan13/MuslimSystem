<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'payment_method_manual' => Setting::get('payment_method_manual', true),
            'payment_method_plink' => Setting::get('payment_method_plink', true),
            'total_system_fee_percentage' => Setting::get('total_system_fee_percentage', 0),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'payment_method_manual' => 'nullable',
            'payment_method_plink' => 'nullable',
            'total_system_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        Setting::set('payment_method_manual', $request->has('payment_method_manual'), 'boolean');
        Setting::set('payment_method_plink', $request->has('payment_method_plink'), 'boolean');
        Setting::set('total_system_fee_percentage', $request->total_system_fee_percentage, 'string');

        return redirect()->back()->with('success', 'Pengaturan metode pembayaran berhasil disimpan.');
    }
}
