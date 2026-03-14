<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationCampaign;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonationCampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = DonationCampaign::with(['category', 'organizer']);
        $this->applyFilters($query, $request);
        
        $campaigns = $query->latest($request->get('sort', 'created_at'))
            ->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return view('admin.donations.index', compact('campaigns'));
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
    }

    public function create()
    {
        $categories = Category::where('type', 'donation')->get();
        return view('admin.donations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'deadline' => 'nullable|date',
            'image' => 'nullable|string',
            'status' => 'required|in:pending,active,rejected,completed'
        ]);

        $data = $request->all();
        $data['organizer_id'] = auth()->id();
        $data['slug'] = Str::slug($request->title) . '-' . time();

        DonationCampaign::create($data);

        return redirect()->route('admin.donations.index')->with('success', 'Kampanye donasi berhasil ditambahkan.');
    }

    public function edit(DonationCampaign $donation)
    {
        $categories = Category::where('type', 'donation')->get();
        return view('admin.donations.edit', compact('donation', 'categories'));
    }

    public function update(Request $request, DonationCampaign $donation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'deadline' => 'nullable|date',
            'image' => 'nullable|string',
            'status' => 'required|in:pending,active,rejected,completed'
        ]);

        $donation->update($request->all());

        return redirect()->route('admin.donations.index')->with('success', 'Kampanye donasi berhasil diperbarui.');
    }

    public function destroy(DonationCampaign $donation)
    {
        $donation->delete();
        return redirect()->route('admin.donations.index')->with('success', 'Kampanye donasi berhasil dihapus.');
    }

    public function show(DonationCampaign $donation)
    {
        $donation->load([
            'category', 
            'organizer', 
            'donations' => function($q) {
                $q->where('status', 'completed')->latest();
            }, 
            'donations.user', 
            'reports'
        ]);
        return view('admin.donations.show', compact('donation'));
    }

    public function submissions(Request $request)
    {
        $query = DonationCampaign::with(['category', 'organizer'])->where('status', 'pending');
        $this->applyFilters($query, $request);

        $campaigns = $query->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return view('admin.donations.index', compact('campaigns'))->with('view_title', 'Pengajuan Donasi Baru');
    }

    public function myCampaigns(Request $request)
    {
        $query = DonationCampaign::with(['category', 'organizer'])->where('organizer_id', auth()->id());
        $this->applyFilters($query, $request);

        $campaigns = $query->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return view('admin.donations.index', compact('campaigns'))->with('view_title', 'Kampanye Saya');
    }

    public function organizers(Request $request)
    {
        $query = \App\Models\User::where('role', 'organizer');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $organizers = $query->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate($request->get('limit', 15))
            ->withQueryString();

        return view('admin.donations.organizers', compact('organizers'));
    }
}
