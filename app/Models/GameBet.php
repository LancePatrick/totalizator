<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameBet extends Model
{
    protected $fillable = [
        'game_round_id',
        'user_id',
        'side',
        'amount',
        'odds_at_bet',
        'status',
        'payout_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'odds_at_bet' => 'decimal:2',
        'payout_amount' => 'decimal:2',
    ];

    public function round()
    {
        return $this->belongsTo(GameRound::class, 'game_round_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}