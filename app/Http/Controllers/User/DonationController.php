<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DonationCampaign;
use App\Models\Donation;
use App\Models\DonationReport;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    /**
     * List all active campaigns for users
     */
    public function index()
    {
        $campaigns = DonationCampaign::with(['organizer', 'category'])
            ->where('status', 'active')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Get campaign details and its reports
     */
    public function show($id)
    {
        $campaign = DonationCampaign::with(['organizer', 'category', 'reports' => function($q) {
            $q->latest();
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $campaign
        ]);
    }

    /**
     * Process a donation - this initiates a payment
     */
    public function donate(Request $request)
    {
        $request->validate([
            'donation_campaign_id' => 'required|exists:donation_campaigns,id',
            'amount'               => 'required|numeric|min:1000',
            'donator_name'         => 'nullable|string',
            'is_anonymous'         => 'boolean',
            'message'              => 'nullable|string'
        ]);

        $user = $request->user();
        $campaign = DonationCampaign::findOrFail($request->donation_campaign_id);

        if ($campaign->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kampanye ini sedah tidak aktif atau sudah selesai.'
            ], 400);
        }

        // We use the same Plink integration as PaymentController
        // For simplicity, we create a 'pending' donation and a 'pending' payment
        // The actual payment creation logic (MAC generation, API call) 
        // should ideally be in a Service, but for now, we'll mimic the link creation
        // or let the frontend call separate endpoints?
        // Actually, let's keep it simple: 
        // 1. Create Donation & Payment entry
        // 2. Return Payment info for frontend to proceed
        
        $refNo = 'DON-' . $user->id . '-' . time();

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'user_id'        => $user->id,
                'external_id'    => $refNo,
                'amount'         => $request->amount,
                'status'         => 'pending',
                'payment_method' => $request->input('payment_method', 'QR'),
                'payload'        => json_encode(['type' => 'donation', 'campaign_id' => $campaign->id])
            ]);

            $donation = Donation::create([
                'user_id'              => $user->id,
                'donation_campaign_id' => $campaign->id,
                'payment_id'           => $payment->id,
                'amount'               => $request->amount,
                'donator_name'         => $request->donator_name ?? $user->username,
                'is_anonymous'         => $request->is_anonymous ?? false,
                'message'              => $request->message,
                'status'               => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Donasi berhasil diinisialisasi. Silakan selesaikan pembayaran.',
                'data' => [
                    'donation' => $donation,
                    'payment'  => $payment
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses donasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Organizer: List their own campaigns
     */
    public function organizerIndex(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'organizer' && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $campaigns = DonationCampaign::where('organizer_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Organizer: Create new campaign submission
     */
    public function storeCampaign(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'organizer' && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'required|string',
            'target_amount' => 'required|numeric|min:10000',
            'deadline'      => 'nullable|date',
            'image'         => 'nullable|string' // Usually file upload in real app
        ]);

        $campaign = DonationCampaign::create([
            'organizer_id'  => $user->id,
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title) . '-' . time(),
            'description'   => $request->description,
            'target_amount' => $request->target_amount,
            'status'        => 'pending', // Needs admin approval
            'deadline'      => $request->deadline,
            'image'         => $request->image
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan kampanye berhasil dikirim dan menunggu verifikasi.',
            'data' => $campaign
        ]);
    }

    /**
     * Organizer: Store report update
     */
    public function storeReport(Request $request, $campaignId)
    {
        $user = $request->user();
        $campaign = DonationCampaign::findOrFail($campaignId);

        if ($campaign->organizer_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'amount_spent' => 'nullable|numeric',
            'images'       => 'nullable|array'
        ]);

        $report = DonationReport::create([
            'donation_campaign_id' => $campaign->id,
            'title'                => $request->title,
            'content'              => $request->content,
            'amount_spent'         => $request->amount_spent ?? 0,
            'images'               => $request->images
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan penyaluran berhasil ditambahkan.',
            'data' => $report
        ]);
    }
}
