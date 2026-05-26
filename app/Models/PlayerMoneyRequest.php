<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerMoneyRequest extends Model
{
    protected $fillable = [
        'player_id',
        'agent_id',
        'amount',
        'status',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}