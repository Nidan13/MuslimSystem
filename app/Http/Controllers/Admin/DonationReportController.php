<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationReport;
use Illuminate\Http\Request;

class DonationReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DonationReport::with('campaign.organizer');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return view('admin.donations.reports', compact('reports'));
    }

    public function destroy(DonationReport $donation_report)
    {
        $donation_report->delete();
        return redirect()->back()->with('success', 'Laporan penyaluran berhasil dihapus.');
    }
}
