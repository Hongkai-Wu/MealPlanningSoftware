<?php

namespace App\Services;

use App\Models\UserGoal;

class GoalEvaluator
{
    public function evaluate(array $totals, int $userId): array
    {
        $goals = UserGoal::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        $results = [];

        foreach ($goals as $g) {
            $metric = strtolower($g->goal_type);
            $current = $totals[$metric] ?? null;
            if ($current === null) continue;

            $target = (float) $g->target_value;
            $direction = strtolower($g->direction);
            $unit = $g->unit;

            $status = 'ok';
            $delta = 0;

            if ($direction === 'up') {
                $delta = round($target - $current, 2);
                $status = $current >= $target ? 'ok' : 'deficit';
            } elseif ($direction === 'down') {
                $delta = round($current - $target, 2);
                $status = $current <= $target ? 'ok' : 'excess';
            }

            $results[] = [
                'metric' => $metric,
                'target' => $target,
                'direction' => $direction,
                'current' => round($current, 2),
                'status' => $status,
                'delta' => abs($delta),
                'unit' => $unit,
            ];
        }

        return $results;
    }
}
