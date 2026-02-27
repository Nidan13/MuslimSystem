<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStat;

class LevelingService
{
    /**
     * Add Experience to a user with Overflow handling.
     */
    public function addExp(User $user, int $amount)
    {
        // 1. Check if user is currently in a Rank Trial block
        // For simplicity: Rank E max level 10, Rank D max level 20, etc.
        $levelCap = $this->getRankLevelCap($user->rankTier);

        if ($user->level >= $levelCap) {
            $user->overflow_exp += $amount;
            $user->save();
            return ['status' => 'overflow', 'message' => 'Experience accumulated in Buffer. Complete Rank-Up Trial to unlock.'];
        }

        $user->current_exp += $amount;
        
        // 2. Check for Level Up
        $nextLevelConfig = \App\Models\LevelConfig::where('level', $user->level)->first();
        $expToLevel = $nextLevelConfig ? $nextLevelConfig->xp_required : ($user->level * 1000);
        
        if ($expToLevel <= 0) $expToLevel = 1000; // Safety for unused service
        
        if ($user->current_exp >= $expToLevel) {
            $user->current_exp -= $expToLevel;
            
            // Give Stat Points or Auto-increment
            $user->soul_points += 100;
            $user->save();
            
            return ['status' => 'levelup', 'level' => $user->level, 'message' => 'LEVEL UP! Your power has increased.'];
        }

        $user->save();
        return ['status' => 'success', 'exp' => $user->current_exp];
    }

    /**
     * Increase user statistics based on habit types.
     */
    public function increaseStat(User $user, string $statType, int $points = 1)
    {
        $stats = $user->userStat;
        
        if (!$stats) {
            $stats = UserStat::create(['user_id' => $user->id]);
        }

        switch (strtolower($statType)) {
            case 'physical':
            case 'str':
                $stats->str += $points;
                break;
            case 'learning':
            case 'int':
                $stats->int += $points;
                break;
            case 'reflection':
            case 'wis':
                $stats->wis += $points;
                break;
            case 'health':
            case 'vit':
                $stats->vit += $points;
                break;
        }

        $stats->save();
    }

    private function getRankLevelCap($rank)
    {
        // Rank is now a RankTier model through relationship
        return $rank->min_level ?? 10;
    }
}
