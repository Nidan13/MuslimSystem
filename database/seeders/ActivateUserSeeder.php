<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Payment;

class ActivateUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(2);
        if ($user) {
            $user->update(['is_active' => true]);
            
            Payment::create([
                'user_id' => 2,
                'external_id' => 'MANUAL-ACTIVATE-' . uniqid(),
                'amount' => 10000,
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'ADMIN_MANUAL',
                'bank_code' => 'BCA',
                'va_number' => '000000000',
                'payload' => json_encode(['note' => 'Manually activated by AI assistant'])
            ]);
            
            $this->command->info('User ID 2 has been activated and payment record created.');
        } else {
            $this->command->error('User ID 2 not found.');
        }
    }
}
