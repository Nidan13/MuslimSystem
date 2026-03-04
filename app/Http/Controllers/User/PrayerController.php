<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PrayerLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrayerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date', now()->toDateString());
        
        // --- NEW: Apply punishments before showing list ---
        $this->triggerPunishment($user);

        $masterPrayers = \App\Models\Prayer::all();
        $logs = PrayerLog::where('user_id', $user->id)
            ->where('date', $date)
            ->get()
            ->keyBy('prayer_name');
            
        $data = $masterPrayers->map(function ($prayer) use ($logs) {
            $log = $logs->get($prayer->slug);
            return [
                'name' => $prayer->name,
                'key' => $prayer->slug,
                'is_completed' => $log ? (bool)$log->is_completed : false,
                'completed_at' => $log ? $log->completed_at : null,
                'scheduled_at' => $log ? $log->scheduled_at : null,
                'soul_points' => $prayer->soul_points,
                'icon' => $prayer->icon,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'summary' => [
                'completed_count' => $logs->where('is_completed', true)->count(),
                'total_count' => $masterPrayers->count(),
            ]
        ]);
    }

    public function syncSchedule(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'schedules' => 'required|array', // e.g., ['subuh' => '04:30', ...]
        ]);

        $user = $request->user();
        $date = $request->date;
        $schedules = $request->schedules;

        foreach ($schedules as $prayerName => $time) {
            $scheduledAt = Carbon::parse($date . ' ' . $time);
            
            // Normalize prayer name slug (avoid case sensitivity or spacing issues)
            $slug = strtolower(trim($prayerName));
            
            // Check if log already exists for this prayer/date combo to avoid duplicates
            // We only update scheduled_at, NEVER touch is_completed here
            PrayerLog::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'prayer_name' => $slug,
                    'date' => $date,
                ],
                [
                    'scheduled_at' => $scheduledAt,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal sholat berhasil disinkronkan.'
        ]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'prayer_name' => 'required|string',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $user = $request->user();
        $prayerName = $request->prayer_name;
        $date = $request->date ?? now()->toDateString();
        
        $prayer = \App\Models\Prayer::where('slug', $prayerName)->first();
        if (!$prayer) {
            return response()->json([
                'success' => false,
                'message' => "Sistem tidak mengenali jenis sholat: $prayerName"
            ], 404);
        }

        // --- NEW: Check Reward Eligibility (Within 60 mins of Adzan) ---
        $log = PrayerLog::where('user_id', $user->id)
            ->where('prayer_name', $prayerName)
            ->where('date', $date)
            ->first();

        $now = now();
        $isLate = false;
        $inTimeWindow = false; // Strict: default is not in window

        if ($log && $log->scheduled_at) {
            $scheduledTime = Carbon::parse($log->scheduled_at);
            $deadline = $scheduledTime->copy()->addMinutes(60);
            
            \Log::info("Prayer Completion Check", [
                'prayer' => $prayerName,
                'now' => $now->toDateTimeString(),
                'scheduled' => $scheduledTime->toDateTimeString(),
                'deadline' => $deadline->toDateTimeString(),
            ]);

            if ($now->between($scheduledTime, $deadline)) {
                $inTimeWindow = true;
            } else {
                $isLate = true;
            }
        } else {
            \Log::warning("Prayer Completion - No schedule found for $prayerName. Denying reward.");
        }

        $log = PrayerLog::updateOrCreate(
            [
                'user_id' => $user->id,
                'prayer_name' => $prayerName,
                'date' => $date,
            ],
            [
                'is_completed' => true,
                'completed_at' => $now,
            ]
        );

        $soulPoints = 0;
        $expGained = 0;
        $message = "Alhamdulillah, " . $prayer->name . " selesai!";

        if ($inTimeWindow) {
            $soulPoints = (int) $prayer->soul_points;
            $expGained = $soulPoints; 
            
            DB::transaction(function () use ($user, $soulPoints, $expGained, $prayer) {
                $user->increment('soul_points', $soulPoints);
                $user->gainExp($expGained);

                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'type' => 'sholat_completion',
                    'amount' => $soulPoints,
                    'description' => "Menyelesaikan Sholat " . $prayer->name . " Tepat Waktu"
                ]);
            });
        } else {
            $message = "Berhasil checklist " . $prayer->name . ", tapi kamu telat atau belum sinkron jadwal. Tidak ada hadiah EXP/SP.";
            
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'sholat_late',
                'amount' => 0,
                'description' => "Checklist Sholat " . $prayer->name . " (Terlambat/Tanpa Jadwal)"
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'soul_points_earned' => $soulPoints,
                'exp_earned' => $expGained,
                'is_late' => $isLate,
                'total_soul_points' => $user->soul_points,
            ]
        ]);
    }

    public function triggerPunishment($user)
    {
        if (!$user || $user->is_menstruating) return;

        $now = now();
        \Log::info("Global Punishment Triggered for User {$user->id} at " . $now->toDateTimeString());
        
        // 1. Dapatkan sholat yang terjadwal namun belum selesai dan belum dihukum
        $missedLogs = PrayerLog::where('user_id', $user->id)
            ->where('is_completed', false)
            ->where('is_punished', false)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<', $now)
            ->get();

        if ($missedLogs->isEmpty()) {
            \Log::info("No missed logs found for User {$user->id}");
            return;
        }

        foreach ($missedLogs as $log) {
            $scheduledAt = Carbon::parse($log->scheduled_at);
            $shouldPunish = false;

            // --- Logika Hukuman (Original Values) ---
            
            // 1. Cek sholat berikutnya (jika ada)
            $nextPrayer = PrayerLog::where('user_id', $user->id)
                ->where('scheduled_at', '>', $log->scheduled_at)
                ->orderBy('scheduled_at', 'asc')
                ->first();

            if ($nextPrayer && $now->gt($nextPrayer->scheduled_at)) {
                // Sudah lewat waktu sholat berikutnya, pasti kena hukum.
                $shouldPunish = true;
                \Log::info("Punishing: Next prayer time reached.", ['prayer' => $log->prayer_name, 'next' => $nextPrayer->prayer_name]);
            } 
            // 2. ATAU sudah lewat selisih 120 menit (2 jam) - untuk antisipasi Isya atau jika next belum sinkron
            elseif ($now->diffInMinutes($scheduledAt) >= 120) {
                $shouldPunish = true;
                \Log::info("Punishing: Over 120 minutes grace period.", ['prayer' => $log->prayer_name]);
            }

            if ($shouldPunish) {
                DB::transaction(function () use ($user, $log) {
                    $user->hp = max(0, $user->hp - 20);
                    $user->save();

                    $log->update([
                        'is_punished' => true,
                        'punished_at' => now(),
                    ]);

                    \App\Models\ActivityLog::create([
                        'user_id' => $user->id,
                        'type' => 'sholat_penalty',
                        'amount' => -20,
                        'description' => "Penalti Sholat " . ucfirst($log->prayer_name) . " (-20 HP)"
                    ]);
                });
            } else {
                \Log::info("Skip punishment for {$log->prayer_name}: still within grace period.", [
                    'mins_diff' => $now->diffInMinutes($scheduledAt)
                ]);
            }
        }
    }

    public function uncomplete(Request $request)
    {
        $request->validate([
            'prayer_name' => 'required|string',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $user = $request->user();
        $prayerName = $request->prayer_name;
        $date = $request->date ?? now()->toDateString();
        
        $log = PrayerLog::where('user_id', $user->id)
            ->where('prayer_name', $prayerName)
            ->where('date', $date)
            ->first();

        if ($log) {
            DB::transaction(function () use ($user, $log) {
                $log->delete();
            });
        }

        return response()->json([
            'success' => true,
            'message' => "Status sholat diubah.",
        ]);
    }
}
