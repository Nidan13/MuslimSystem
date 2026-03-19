<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HunterService
{
    /**
     * Store a new hunter into the system with stats.
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['referral_code'] = $data['referral_code'] ?? strtoupper(Str::random(8));
            
            $user = User::create($data);
            
            // Initialize basic stats
            $user->userStat()->create([
                'strength' => 10,
                'agility' => 10,
                'intelligence' => 10,
                'vitality' => 10,
                'sense' => 10,
                'remaining_points' => 0,
            ]);

            return $user;
        });
    }

    /**
     * Update an existing hunter.
     */
    public function update(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);
            return $user;
        });
    }
}
