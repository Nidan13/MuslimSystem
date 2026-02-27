<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Google_Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register new user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|in:Male,Female,male,female',
            'job_class' => 'required|in:Al-Hafizh,Al-Muhsin,Al-Mujahid',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        // Normalize gender to Title Case for DB enum
        $gender = ucfirst(strtolower($validated['gender']));

        // Handle referral logic
        $referrerId = null;
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])->first();
            $referrerId = $referrer?->id;
        }

        // Generate unique referral code
        $referralCode = 'REF-' . strtoupper(uniqid());

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender' => $gender,
            'rank_tier_id' => 1, // Default rank (pastikan ID 1 ada di DB)
            'level' => 1,
            'hp' => 100,
            'max_hp' => 100,
            'current_exp' => 0,
            'overflow_exp' => 0,
            'soul_points' => 0,
            'job_class' => $validated['job_class'],
            'referral_code' => $referralCode,
            'referred_by_id' => $referrerId,
            'is_active' => false,
        ]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Welcome, Hunter!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'level' => $user->level,
                    'soul_points' => $user->soul_points,
                    'job_class' => $user->job_class ?? 'Newbie',
                    'referral_code' => $user->referral_code,
                    'is_active' => (bool)$user->is_active,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Delete old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome back, Hunter!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'level' => $user->level,
                    'current_exp' => $user->current_exp,
                    'soul_points' => $user->soul_points,
                    'job_class' => $user->job_class ?? 'Newbie',
                    'is_active' => (bool)$user->is_active,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout user (revoke token)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully!',
        ]);
    }

    /**
     * Google Login
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); 
            // Note: Set GOOGLE_CLIENT_ID in .env
            
            $payload = $client->verifyIdToken($request->id_token);

            if ($payload) {
                $googleId = $payload['sub'];
                $email = $payload['email'];
                $name = $payload['name'];
                $picture = $payload['picture'];

                // Check if user exists
                $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();
                $isNewUser = false;

                if (!$user) {
                    $isNewUser = true;
                    // Register new user with defaults
                    // Username: remove spaces from name + random number
                    $username = Str::slug($name, '');
                    if (empty($username)) $username = 'hunter';
                    $username .= rand(100, 999);

                    // Re-check username uniqueness
                    while(User::where('username', $username)->exists()) {
                        $username = Str::slug($name, '') . rand(1000, 9999);
                    }

                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'password' => Hash::make(Str::random(16)), // Random password
                        'gender' => 'Male', // Default, will be updated in Complete Profile
                        'rank_tier_id' => 1,
                        'level' => 1,
                        'hp' => 100,
                        'max_hp' => 100,
                        'current_exp' => 0,
                        'overflow_exp' => 0,
                        'soul_points' => 0,
                        'job_class' => 'Al-Hafizh', // Default class
                        'referral_code' => 'REF-' . strtoupper(uniqid()),
                        'google_id' => $googleId,
                        'avatar' => $picture,
                    ]);
                } else {
                    // Update google_id if not set (merging account by email)
                    if (empty($user->google_id)) {
                        $user->update([
                            'google_id' => $googleId,
                            'avatar' => $picture // Optional: update avatar
                        ]);
                    }
                }

                // Create token
                $user->tokens()->delete(); // Optional: Revoke old tokens?
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login Google Berhasil!',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'username' => $user->username,
                            'email' => $user->email,
                            'gender' => $user->gender,
                            'level' => $user->level,
                            'current_exp' => $user->current_exp,
                            'soul_points' => $user->soul_points,
                            'job_class' => $user->job_class ?? 'Newbie',
                            'is_active' => (bool)$user->is_active,
                        ],
                        'is_new_user' => $isNewUser,
                        'token' => $token,
                    ],
                ]);

            } else {
                return response()->json(['success' => false, 'message' => 'Invalid ID Token'], 401);
            }
        } catch (\Exception $e) {
            Log::error("Google Login Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Google Login Failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get authenticated user profile
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Auto-repair rank if it's incorrect for the current level
        $user->updateRankTier();
        $user->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->username),
                    'gender' => $user->gender,
                    'is_menstruating' => (bool)$user->is_menstruating,
                    'rank' => $user->rankTier ? $user->rankTier->name : 'Novice',
                    'level' => $user->level,
                    'xp' => [
                        'current' => $user->current_exp,
                        'max' => $user->next_level_exp,
                        'progress' => $user->next_level_exp > 0 ? round(($user->current_exp / $user->next_level_exp) * 100) : 0,
                    ],
                    'hp' => [
                        'current' => $user->hp,
                        'max' => $user->max_hp,
                        'progress' => $user->max_hp > 0 ? round(($user->hp / $user->max_hp) * 100) : 0,
                    ],
                    'soul_points' => $user->soul_points,
                    'max_sp' => $user->max_sp,
                    'referral_code' => $user->referral_code,
                    'job_class' => $user->job_class ?? 'Newbie',
                    'rank_tier_id' => $user->rank_tier_id,
                    'is_active' => (bool)$user->is_active,
                    'stats' => [ // Basic stats fallback
                        'streak' => $user->streak,
                        'attributes' => [
                            'sholat' => $user->sholat_count,
                            'ilmu' => $user->ilmu_count,
                            'adab' => $user->adab_count,
                            'istiqomah' => $user->streak,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function toggleMenstruation(Request $request)
    {
        $user = $request->user();
        
        if (strtolower($user->gender) !== 'female') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Hunter perempuan yang bisa menggunakan fitur ini!'
            ], 403);
        }

        $user->is_menstruating = !$user->is_menstruating;
        $user->menstruation_started_at = $user->is_menstruating ? now() : null;
        
        // --- NEW: If toggled ON, restore HP (Wok's Request) ---
        if ($user->is_menstruating) {
            $user->hp = $user->max_hp;
            
            // Mark all today's missed prayers as "exempt" by setting is_punished or deleting
            // (We just mark them so they don't trigger penalties)
            \App\Models\PrayerLog::where('user_id', $user->id)
                ->where('is_completed', false)
                ->where('is_punished', false)
                ->update(['is_punished' => true, 'punished_at' => now()]);
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_menstruating 
                ? 'Status: Sedang Berhalangan. Darah dikembalikan (Full HP) & Penalty dinonaktifkan.' 
                : 'Status: Sudah Suci. HP Penalty Sholat diaktifkan kembali.',
            'data' => [
                'is_menstruating' => (bool)$user->is_menstruating,
                'current_hp' => $user->hp
            ]
        ]);
    }
}
