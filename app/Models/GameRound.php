<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    protected $fillable = [
        'created_by',
        'round_name',
        'round_number',
        'video_url',
        'status',
        'winning_side',

        'commission_rate',
        'commission_amount',
        'admin_income',

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
        return $this->hasMany(GameBet::class);
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
            ->where('side', 'meron')
            ->sum('amount');

        $walaTotal = (float) $this->bets()
            ->where('side', 'wala')
            ->sum('amount');

        $drawTotal = (float) $this->bets()
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
            'meron_total' => $meronTotal,
            'wala_total' => $walaTotal,
            'draw_total' => $drawTotal,
            'total_pool' => $totalPool,
            'commission_amount' => $commissionAmount,
            'admin_income' => $commissionAmount,
            'net_pool' => $netPool,
            'meron_odds' => $meronOdds,
            'wala_odds' => $walaOdds,
            'draw_odds' => $drawOdds,
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