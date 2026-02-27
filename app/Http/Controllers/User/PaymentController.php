<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function getConfig(): array
    {
        return [
            'merchant_id' => config('services.prisma.merchant_id'),
            'key_id'      => config('services.prisma.key_id'),
            'secret_key'  => config('services.prisma.secret_key'),
            'base_url'    => config('services.prisma.api_url', 'https://api-staging.plink.co.id/gateway/v2'),
        ];
    }

    private function generateMac(string $rawJson, string $secretKey): string
    {
        return hash_hmac('sha256', $rawJson, $secretKey);
    }

    private function getTimestamp(): string
    {
        return now('Asia/Jakarta')->format('Y-m-d H:i:s.v +0700');
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
        return '+' . $cleaned;
    }

    public function createLink(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1000',
            'payment_method' => 'string|in:VA,QR',
            'bank_code'      => 'string',
        ]);

        $cfg         = $this->getConfig();
        $user        = $request->user();
        $amount      = (int) $request->amount;
        $method      = strtoupper($request->input('payment_method', 'QR'));
        $bankCode    = strtoupper($request->input('bank_code', 'BCA'));
        $refNo       = 'TRX-' . $user->id . '-' . time();
        $externalId  = 'EXT-' . $user->id . '-' . Str::random(6);
        $timestamp   = $this->getTimestamp();
        $appUrl      = config('app.url');

        $bankMap = [
            'BCA'     => '014', 'MANDIRI' => '008', 'BNI'     => '009',
            'BRI'     => '002', 'PERMATA' => '013', 'DANAMON' => '011',
            'CIMB'    => '022',
        ];

        $items = [[
            'item_code'  => '1',
            'item_title' => 'Infaq Aktivasi Akun Muslim App',
            'quantity'   => 1,
            'total'      => (string) $amount,
            'currency'   => 'IDR',
        ]];

        $payload = [
            'merchant_key_id'        => $cfg['key_id'],
            'merchant_id'            => $cfg['merchant_id'],
            'merchant_ref_no'        => $refNo,
            'backend_callback_url'   => env('PLINK_BACKEND_CALLBACK', $appUrl . '/api/prismalink/webhook'),
            'frontend_callback_url'  => env('PLINK_FRONTEND_CALLBACK', $appUrl . '/api/payments/callback'),
            'transaction_date_time'  => $timestamp,
            'transmission_date_time' => $timestamp,
            'transaction_currency'   => 'IDR',
            'transaction_amount'     => $amount,
            'product_details'        => json_encode($items),
            'user_id'                => (string) $user->id,
            'user_name'              => substr($user->username ?? $user->name ?? $user->email, 0, 50),
            'user_email'             => substr($user->email, 0, 50),
            'user_phone_number'      => $this->formatPhone($user->phone ?? null),
            'remarks'                => 'Infaq Aktivasi Akun',
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
            $payload['bank_id'] = $bankMap[$bankCode] ?? '014';
        }

        $rawJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        \Log::info('Plink Payment Request', ['merchant_ref_no' => $refNo, 'mac' => $this->generateMac($rawJson, $cfg['secret_key']), 'payload' => $payload]);
        $mac = $this->generateMac($rawJson, $cfg['secret_key']);

        try {
            $endpoint = $cfg['base_url'] . '/payment/integration/transaction/api/submit-trx';
            $response = Http::timeout(20)->withHeaders([
                'Content-Type' => 'application/json',
                'mac'          => $mac,
            ])->withBody($rawJson, 'application/json')->send('POST', $endpoint);

            $respData = $response->json();
            \Log::info('Plink Payment Response', ['body' => $respData]);

            if (($respData['response_code'] ?? '') === 'PL000' || ($respData['response_code'] ?? '') === '00') {
                $vaNumber = null;
                if (!empty($respData['va_number_list'])) {
                    $vaList = is_string($respData['va_number_list']) ? json_decode($respData['va_number_list'], true) : $respData['va_number_list'];
                    $vaNumber = $vaList[0]['va'] ?? null;
                }
                $qrString = $respData['qris_data'] ?? $respData['qr_string'] ?? null;

                Payment::create([
                    'user_id'     => $user->id,
                    'external_id' => $refNo,
                    'amount'      => $amount,
                    'status'      => 'pending',
                    'payment_method' => $method,
                    'bank_code'   => ($method === 'VA') ? $bankCode : null,
                    'va_number'   => $vaNumber,
                    'qr_string'   => $qrString,
                    'payload'     => json_encode($respData)
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'payment_method' => $method,
                        'bank_code'      => $bankCode,
                        'va_number'      => $vaNumber,
                        'qr_string'      => $qrString,
                        'amount'         => $amount,
                        'ref_no'         => $refNo,
                        'expired_at'     => $respData['expired_date_time'] ?? $respData['expiry_date'] ?? null,
                    ]
                ]);
            }
            
            $errDetail = $respData['response_description'] ?? $respData['response_message'] ?? 'Error';
            return response()->json(['success' => false, 'message' => 'Plink error: ' . $errDetail], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        $user = $request->user();
        $payment = Payment::where('user_id', $user->id)->latest()->first();

        \Log::info('Checking Status - DEBUG INFO', [
            'user_id' => $user->id,
            'user_is_active' => $user->is_active,
            'payment_found' => !is_null($payment),
            'payment_status' => $payment ? $payment->status : 'N/A',
            'payment_ext_id' => $payment ? $payment->external_id : 'N/A'
        ]);

        // Backup: Kalau di DB masih pending, tapi kita klik cek status, 
        // kita paksa nanya ke Plink (Inquiry)
        if ($payment && $payment->status === 'pending') {
            $this->syncPaymentStatus($payment);
            $user->refresh();
            $payment = Payment::find($payment->id); // Refresh payment data from DB
            
            \Log::info('Checking Status - AFTER SYNC', [
                'user_is_active' => $user->is_active,
                'payment_status' => $payment->status
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_active' => $user->is_active,
                'last_payment' => $payment
            ]
        ]);
    }

    private function syncPaymentStatus($payment)
    {
        $cfg = $this->getConfig();
        $timestamp = $this->getTimestamp();
        
        $payload = [
            'merchant_key_id'        => $cfg['key_id'],
            'merchant_id'            => $cfg['merchant_id'],
            'merchant_ref_no'        => $payment->external_id,
            'transaction_date_time'  => $timestamp,
            'transmission_date_time' => $timestamp,
            'action_id'              => '04',
        ];

        $rawJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $mac = $this->generateMac($rawJson, $cfg['secret_key']);

        \Log::info('Plink Inquiry Request', [
            'ref' => $payment->external_id,
            'payload' => $payload,
            'mac' => $mac
        ]);

        try {
            $endpoint = $cfg['base_url'] . '/payment/integration/transaction/api/inquiry-trx';
            $response = Http::timeout(15)->withHeaders([
                'Content-Type' => 'application/json',
                'mac'          => $mac,
            ])->withBody($rawJson, 'application/json')->send('POST', $endpoint);

            $respData = $response->json();
            \Log::info('Plink Inquiry Response RAW', [
                'ref' => $payment->external_id,
                'body' => $respData
            ]);

            $status = $respData['transaction_status'] ?? $respData['payment_status'] ?? $respData['status'] ?? '';
            $respCode = $respData['response_code'] ?? '';

            // Update payload in DB so we can see what Plink said last time
            $payment->update(['payload' => json_encode($respData)]);

            // Plink uses SETLD, SUCCESS, or SETTLED for success
            // Note: Don't use PL000 alone here because it's just the inquiry request status
            if ($status === 'SETTLED' || $status === 'SUCCESS' || $status === 'SETLD') {
                \Log::info('Match Found - Activating User', ['ref' => $payment->external_id]);
                $this->markAsPaid($payment);
            }
        } catch (\Exception $e) {
            \Log::error('Inquiry failed', ['msg' => $e->getMessage()]);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();
        \Log::info('Plink Webhook Received', $payload);

        $refNo = $payload['merchant_ref_no'] ?? null;
        $paymentStatus = $payload['payment_status'] ?? '';

        if ($refNo && ($paymentStatus === 'SETLD' || $paymentStatus === 'SETTLED' || $paymentStatus === 'SUCCESS')) {
            $payment = Payment::where('external_id', $refNo)->where('status', 'pending')->first();
            if ($payment) {
                \Log::info('Webhook Match - Activating User', ['ref' => $refNo]);
                $payment->update(['payload' => json_encode($payload)]); // Update payload with webhook data
                $this->markAsPaid($payment);
            }
        }
        return response()->json(['message' => 'OK']);
    }

    private function markAsPaid($payment)
    {
        DB::transaction(function() use ($payment) {
            // 1. Update status pembayaran
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            
            $u = User::find($payment->user_id);
            if ($u) {
                \Log::info('Activating User ID: ' . $u->id);
                
                // 2. Aktifkan akun user baru
                $u->update(['is_active' => 1]);
                
                // 3. Logic Komisi Referral (10%)
                if ($u->referred_by_id) {
                    $commissionAmount = $payment->amount * 0.1;
                    \Log::info("Giving Commission to Referrer: {$u->referred_by_id}. Amount: {$commissionAmount}");

                    // Catat histori komisi
                    Commission::create([
                        'recipient_id'      => $u->referred_by_id,
                        'referred_user_id'  => $u->id,
                        'payment_id'        => $payment->id,
                        'amount'            => $commissionAmount,
                        'tier'              => 1,
                        'status'            => 'Success' // Langsung sukses biar cair
                    ]);

                    // 4. Update SALDO si pengajak secara REAL
                    $referrer = User::find($u->referred_by_id);
                    if ($referrer) {
                        $referrer->increment('balance', $commissionAmount);
                        \Log::info("User ID {$referrer->id} balance updated. New balance: {$referrer->balance}");
                    }
                }
            }
        });
    }

    public function callback(Request $request)
    {
        // Redirect balik ke App (Deep Link) atau Web Page Sukses
        // Karena ini mobile app, biasanya kita redirect ke URL yang dihandle Flutter
        return "<html><body onload=\"window.location='muslimapp://payment/success'\">
                <h3>Pembayaran Berhasil!</h3>
                <p>Silakan kembali ke aplikasi...</p>
                </body></html>";
    }
}
