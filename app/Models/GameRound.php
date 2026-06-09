<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    protected $fillable = [
        'created_by',

        'title',
        'round_code',

        'round_name',
        'round_number',
        'video_url',
        'status',
        'winning_side',

        'commission_rate',
        'commission_amount',
        'admin_income',

        'company_commission_rate',
        'agent_commission_rate',
        'company_commission_amount',
        'agent_commission_amount',

        'meron_total',
        'wala_total',
        'draw_total',
        'total_pool',
        'net_pool',

        'meron_odds',
        'wala_odds',
        'draw_odds',

        'payout_total',

        'started_at',
        'closed_at',
        'ended_at',
        'settled_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'admin_income' => 'decimal:2',

        'company_commission_rate' => 'decimal:4',
        'agent_commission_rate' => 'decimal:4',
        'company_commission_amount' => 'decimal:2',
        'agent_commission_amount' => 'decimal:2',

        'meron_total' => 'decimal:2',
        'wala_total' => 'decimal:2',
        'draw_total' => 'decimal:2',
        'total_pool' => 'decimal:2',
        'net_pool' => 'decimal:2',

        'meron_odds' => 'decimal:4',
        'wala_odds' => 'decimal:4',
        'draw_odds' => 'decimal:4',

        'payout_total' => 'decimal:2',

        'started_at' => 'datetime',
        'closed_at' => 'datetime',
        'ended_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function bets()
    {
        return $this->hasMany(GameBet::class, 'game_round_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function normalizeCommissionRate(): float
    {
        $rate = (float) ($this->commission_rate ?? 5);

        return $rate > 1 ? $rate / 100 : $rate;
    }

    public function recalculateTotalsAndOdds(): void
    {
        $meronTotal = (float) $this->bets()
            ->where('status', 'pending')
            ->where('side', 'meron')
            ->sum('amount');

        $walaTotal = (float) $this->bets()
            ->where('status', 'pending')
            ->where('side', 'wala')
            ->sum('amount');

        $drawTotal = (float) $this->bets()
            ->where('status', 'pending')
            ->where('side', 'draw')
            ->sum('amount');

        $totalPool = $meronTotal + $walaTotal + $drawTotal;

        $commissionRate = $this->normalizeCommissionRate();
        $commissionAmount = round($totalPool * $commissionRate, 2);
        $netPool = round($totalPool - $commissionAmount, 2);

        $meronOdds = $meronTotal > 0 ? round($netPool / $meronTotal, 4) : 0;
        $walaOdds = $walaTotal > 0 ? round($netPool / $walaTotal, 4) : 0;
        $drawOdds = $drawTotal > 0 ? round($netPool / $drawTotal, 4) : 0;

        $this->update([
            'meron_total' => round($meronTotal, 2),
            'wala_total' => round($walaTotal, 2),
            'draw_total' => round($drawTotal, 2),
            'total_pool' => round($totalPool, 2),
            'commission_amount' => round($commissionAmount, 2),
            'admin_income' => round($commissionAmount, 2),
            'net_pool' => round($netPool, 2),
            'meron_odds' => round($meronOdds, 4),
            'wala_odds' => round($walaOdds, 4),
            'draw_odds' => round($drawOdds, 4),
        ]);
    }

    public function oddsForSide(string $side): float
    {
        return match ($side) {
            'meron' => (float) $this->meron_odds,
            'wala' => (float) $this->wala_odds,
            'draw' => (float) $this->draw_odds,
            default => 0,
        };
    }

    public function perHundredPrizeForSide(string $side): float
    {
        return round($this->oddsForSide($side) * 100, 2);
    }
}