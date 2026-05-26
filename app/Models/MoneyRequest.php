<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyRequest extends Model
{
    protected $fillable = [
        'user_id',
        'agent_id',
        'admin_id',
        'amount',
        'payment_method',
        'reference_number',
        'proof_image_path',
        'notes',
        'admin_notes',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}