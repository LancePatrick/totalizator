<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogrohanEntry extends Model
{
    protected $fillable = [
        'game_round_id',
        'round_number',
        'result',
    ];

    public function round()
    {
        return $this->belongsTo(GameRound::class, 'game_round_id');
    }
}