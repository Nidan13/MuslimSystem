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
        $campaign = DonationCampaign::with([
            'organizer', 
            'category', 
            'reports' => function($q) {
                $q->latest();
            },
            'donations' => function($q) {
                // Only show completed/paid donations to avoid duplicates/pending attempts
                $q->where('status', 'completed')->latest()->take(10);
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $campaign
        ]);
    }

    private function getConfig(): array

    {
        return [
            'merchant_id' => config('services.prisma.merchant_id'),
            'key_id'      => config('services.prisma.key_id'),
            'secret_key'  => config('services.prisma.secret_key'),
            'base_url'    => config('services.prisma.api_url', 'https://api-staging.plink.co.id/gateway/v2'),
            'backend_callback'  => config('services.prisma.backend_callback'),
            'frontend_callback' => config('services.prisma.frontend_callback'),
        ];
    }

    private function generateMac(string $rawJson, string $secretKey): string
    {
        return strtoupper(hash_hmac('sha256', $rawJson, trim($secretKey)));
    }

    private function getTimestamp(): string
    {
        return now('Asia/Jakarta')->format('Y-m-d H:i:s.v O');
    }

    private function formatPhone(?string $phone): string
    {
        if (!$phone) return '+628123456789';
        $cleaned = preg_replace('/\D/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '62')) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
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
            'message'              => 'nullable|string',
            'payment_method'       => 'nullable|string|in:VA,QR',
        ]);

        $cfg      = $this->getConfig();
        $user     = $request->user();
        $campaign = DonationCampaign::findOrFail($request->donation_campaign_id);

        if ($campaign->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kampanye ini sedang tidak aktif atau sudah selesai.'
            ], 400);
        }

        if (!\App\Models\Setting::get('payment_method_plink', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway pembayaran sedang dinonaktifkan sementara.'
            ], 403);
        }

        $amount    = (int) $request->amount;
        $method    = strtoupper($request->input('payment_method', 'QR'));
        $refNo     = 'DON-' . $user->id . '-' . time();
        $timestamp = $this->getTimestamp();

        $items = [[
            'item_code'  => 'DON-' . $campaign->id,
            'item_title' => 'Donasi: ' . substr($campaign->title, 0, 40),
            'quantity'   => 1,
            'total'      => (string) $amount,
            'currency'   => 'IDR',
        ]];

        $externalId  = 'EXT-' . $user->id . '-' . Str::random(6);

        $bankMap = [
            'BCA'     => '014', 'MANDIRI' => '008', 'BNI'     => '009',
            'BRI'     => '002', 'PERMATA' => '013', 'DANAMON' => '011',
            'CIMB'    => '022',
        ];

        $payload = [
            'merchant_key_id'        => $cfg['key_id'],
            'merchant_id'            => $cfg['merchant_id'],
            'merchant_ref_no'        => $refNo,
            'backend_callback_url'   => $cfg['backend_callback'],
            'frontend_callback_url'  => $cfg['frontend_callback'],
            'transaction_date_time'  => $timestamp,
            'transmission_date_time' => $timestamp,
            'transaction_currency'   => 'IDR',
            'transaction_amount'     => (int) $amount,
            'product_details'        => json_encode($items),
            'user_id'                => (string) $user->id,
            'user_name'              => substr($request->donator_name ?? $user->username ?? $user->name ?? $user->email, 0, 50),
            'user_email'             => substr($user->email, 0, 50),
            'user_phone_number'      => $this->formatPhone($user->phone ?? null),
            'remarks'                => 'Donasi: ' . $campaign->title,
            'user_device_id'         => 'DEVICE-' . $user->id,
            'user_ip_address'        => $request->ip() ?? '127.0.0.1',
            'shipping_details'       => json_encode([
                'address'         => '-',
                'telephoneNumber' => $this->formatPhone($user->phone ?? null),
                'handphoneNumber' => $this->formatPhone($user->phone ?? null),
            ]),
            'payment_method'         => $method,
            'other_bills'            => '[]',
            'invoice_number'         => $refNo,
            'integration_type'       => '02',
            'external_id'            => $externalId,
            'action_id'              => '01',
        ];

        if ($method === 'VA') {
            $payload['bank_id'] = $bankMap['BCA']; // Default BCA for now
        }


        DB::beginTransaction();
        try {
            $rawJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $mac = $this->generateMac($rawJson, $cfg['secret_key']);

            $endpoint = $cfg['base_url'] . '/payment/integration/transaction/api/submit-trx';
            $response = \Illuminate\Support\Facades\Http::timeout(20)->withHeaders([
                'Content-Type' => 'application/json',
                'mac'          => $mac,
            ])->withBody($rawJson, 'application/json')->post($endpoint);

            $respData = $response->json();
            
            if (($respData['response_code'] ?? '') === 'PL000' || ($respData['response_code'] ?? '') === '00') {
                $vaNumber = null;
                if (!empty($respData['va_number_list'])) {
                    $vaList = is_string($respData['va_number_list']) ? json_decode($respData['va_number_list'], true) : $respData['va_number_list'];
                    $vaNumber = $vaList[0]['va'] ?? null;
                }
                $qrString = $respData['qris_data'] ?? $respData['qr_string'] ?? null;
                $paymentUrl = $respData['payment_url'] ?? null;

                $payment = Payment::create([
                    'user_id'        => $user->id,
                    'external_id'    => $refNo,
                    'amount'         => $amount,
                    'status'         => 'pending',
                    'payment_url'    => $paymentUrl,
                    'qr_string'      => $qrString,
                    'va_number'      => $vaNumber,
                    'bank_code'      => ($method === 'VA') ? 'BCA' : null,
                    'payment_method' => $method,
                    'payload'        => json_encode(['type' => 'donation', 'campaign_id' => $campaign->id])
                ]);

                $donation = Donation::create([
                    'user_id'              => $user->id,
                    'donation_campaign_id' => $campaign->id,
                    'payment_id'           => $payment->id,
                    'amount'               => $amount,
                    'donator_name'         => $request->donator_name ?? $user->username,
                    'is_anonymous'         => $request->is_anonymous ?? false,
                    'message'              => $request->message,
                    'status'               => 'pending'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Donasi berhasil diinisialisasi.',
                    'data' => [
                        'donation'       => $donation,
                        'payment'        => $payment,
                        'payment_url'    => $paymentUrl,
                        'qr_string'      => $qrString,
                        'va_number'      => $vaNumber,
                        'payment_method' => $method,
                    ]
                ]);
            }

            throw new \Exception($respData['response_description'] ?? $respData['response_message'] ?? 'Gagal menghubungi gateway pembayaran');

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

        $campaigns = DonationCampaign::with(['reports', 'category'])
            ->where('organizer_id', $user->id)
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
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/campaigns'), $filename);
            $imagePath = url('uploads/campaigns/' . $filename);
        }

        $campaign = DonationCampaign::create([
            'organizer_id'  => $user->id,
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title) . '-' . time(),
            'description'   => $request->description,
            'target_amount' => $request->target_amount,
            'status'        => 'pending',
            'deadline'      => $request->deadline,
            'image'         => $imagePath ?? $request->image
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
            'images.*'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reports'), $filename);
                $imagePaths[] = url('uploads/reports/' . $filename);
            }
        }

        $report = DonationReport::create([
            'donation_campaign_id' => $campaign->id,
            'title'                => $request->title,
            'content'              => $request->content,
            'amount_spent'         => $request->amount_spent ?? 0,
            'images'               => !empty($imagePaths) ? $imagePaths : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan penyaluran berhasil ditambahkan.',
            'data' => $report
        ]);
    }

    /**
     * Organizer: Update campaign
     */
    public function updateCampaign(Request $request, $id)
    {
        $user = $request->user();
        $campaign = DonationCampaign::findOrFail($id);

        if ($campaign->organizer_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'         => 'sometimes|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'sometimes|string',
            'target_amount' => 'sometimes|numeric|min:1000',
            'deadline'      => 'nullable|date',
            'image'         => 'nullable' // could be string URL or file
        ]);

        $data = $request->only(['title', 'category_id', 'description', 'target_amount', 'deadline', 'status']);
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/campaigns'), $filename);
            $data['image'] = url('uploads/campaigns/' . $filename);
        }

        $campaign->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kampanye berhasil diperbarui.',
            'data' => $campaign
        ]);
    }

    /**
     * Organizer: Update report
     */
    public function updateReport(Request $request, $id)
    {
        $user = $request->user();
        $report = DonationReport::with('campaign')->findOrFail($id);

        if ($report->campaign->organizer_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'        => 'sometimes|string|max:255',
            'content'      => 'sometimes|string',
            'amount_spent' => 'nullable|numeric',
        ]);

        $data = $request->only(['title', 'content', 'amount_spent']);
        
        $imagePaths = is_array($report->images) ? $report->images : [];
        if ($request->hasFile('images')) {
            // If new images provided, we append or replace? Let's replace for simplicity in editing
            $imagePaths = []; 
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reports'), $filename);
                $imagePaths[] = url('uploads/reports/' . $filename);
            }
            $data['images'] = $imagePaths;
        }

        $report->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Laporan penyaluran berhasil diperbarui.',
            'data' => $report
        ]);
    }
}
