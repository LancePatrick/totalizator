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
        'meron_total',
        'wala_total',
        'draw_total',
        'total_pool',
        'commission_rate',
        'commission_amount',
        'net_pool',
        'meron_odds',
        'wala_odds',
        'draw_odds',
        'winning_side',
        'started_at',
        'closed_at',
        'ended_at',
        'settled_at',
    ];

    protected $casts = [
        'meron_total' => 'decimal:2',
        'wala_total' => 'decimal:2',
        'draw_total' => 'decimal:2',
        'total_pool' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_pool' => 'decimal:2',
        'meron_odds' => 'decimal:2',
        'wala_odds' => 'decimal:2',
        'draw_odds' => 'decimal:2',
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
        'ended_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bets()
    {
        return $this->hasMany(GameBet::class);
    }

    public function logrohanEntry()
    {
        return $this->hasOne(LogrohanEntry::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function recalculateTotalsAndOdds(): void
    {
        $this->meron_total = $this->bets()->where('side', 'meron')->sum('amount');
        $this->wala_total = $this->bets()->where('side', 'wala')->sum('amount');
        $this->draw_total = $this->bets()->where('side', 'draw')->sum('amount');

        $this->total_pool = $this->meron_total + $this->wala_total + $this->draw_total;

        $this->commission_amount = $this->total_pool * ($this->commission_rate / 100);
        $this->net_pool = $this->total_pool - $this->commission_amount;

        $this->meron_odds = $this->meron_total > 0
            ? $this->net_pool / $this->meron_total
            : 0;

        $this->wala_odds = $this->wala_total > 0
            ? $this->net_pool / $this->wala_total
            : 0;

        $this->draw_odds = $this->draw_total > 0
            ? $this->net_pool / $this->draw_total
            : 0;

        $this->save();
    }
}